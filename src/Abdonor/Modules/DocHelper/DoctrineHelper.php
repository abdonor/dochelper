<?php

namespace Abdonor\Modules\DocHelper;

use Doctrine\ORM\QueryBuilder;
use function GuzzleHttp\Psr7\parse_query;

class DoctrineHelper
{
    /**
     * @info You need to instantiate this var in your code with the Doctrine\ORM\QueryBuilder
     * @var QueryBuilder */
    private $query;
    /** @var int Quantity of arguments, internal variable */
    private $qtdArgs = 0;

    const OR = 'OR';
    const AND = 'AND';
    const LIKE = 'LIKE';
    const EQUAL = '=';

    /**
     * Example of $range: $range = ['limit' => 100, 'offset' => 20];
     * @param $range
     */
    public function addRange($range)
    {
        if ($range) {
            if (isset($range['limit'])) {
                $this->query->setMaxResults($range['limit']);
            }
            if (isset($range['offset'])) {
                $this->query->setFirstResult($range['offset']);
            }
        }
    }

    /**
     * @param $column
     * @param string $order
     */
    public function addOrderBy($column, $order = 'ASC')
    {
        $this->query->orderBy($column, $order);
    }

    /**
     * This var is mandatory
     * @param $query - your repository QueryBuilder
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * It will get your query string from the call
     * @return array
     */
    public static function getArrayQuery()
    {
        return parse_query($_SERVER['QUERY_STRING']);
    }

    /**
     * @param $arrAllowed Are the params allowed to be searched in your query
     * An example of "$arrAllowed": $allowedParams = ['name' => 'name', 'id' => 'id',];
     * @return array
     */
    public static function getParams($arrAllowed)
    {
        $params = [];
        $queryArr = self::getArrayQuery();
        foreach ($queryArr as $key => $value) {
            if (isset($arrAllowed[$key])) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    /**
     * It allows you to get the range from your Query String
     * @return null|['limit' => 100, 'offset' => 20]
     */
    public static function getRange()
    {
        $queryArr = self::getArrayQuery();
        $data['limit'] = (isset($queryArr['limit']) && $queryArr['limit'] > 0)? (int) $queryArr['limit'] : null;
        $data['offset'] = (isset($queryArr['offset']) && $queryArr['offset'] > -1)? (int) $queryArr['offset'] : 0;
        if ($data['limit'] == null && $data['offset'] == 0) {
            $data = null;
        }

        return $data;
    }

    public function andLikeOr($params, $nameVar, $nameColumn)
    {
        return $this->like($params, $nameVar, $nameColumn, self::OR);
    }

    public function andLikeAnd($params, $nameVar, $nameColumn)
    {
        return $this->like($params, $nameVar, $nameColumn, self::AND);
    }

    public function andEqOr($params, $nameVar, $nameColumn)
    {
        return $this->eq($params, $nameVar, $nameColumn, self::OR);
    }

    public function eq($params, $nameVar, $columnName, $operator)
    {
        if (isset($params[$nameVar])) {
            $var = $params[$nameVar];
            if ($operator == self::AND) {
                $opX = $this->query->expr()->andX();
                $op = self::AND;
            } elseif ($operator == self::OR) {
                $opX = $this->query->expr()->orX();
                $op = self::OR;
            }
            $i = $this->qtdArgs;
            if (is_array($var)) {
                foreach ($var as $item) {
                    $dql = $this->columnArray($columnName, $op, $i, self::EQUAL);
                    $opX->add($dql);
                    $this->query->setParameter($i, $item);
                    $i++;
                }
                $this->query->andWhere($opX);
            } else {
                $dql = $this->columnArray($columnName, $op, $i, self::EQUAL);
                $this->query->andWhere($dql)->setParameter($i, $var);
                $i++;
            }
            $this->qtdArgs = $i;
        }
    }

    private function like($params, $nameVar, $columnName, $operator)
    {
        if (isset($params[$nameVar])) {
            $var = $params[$nameVar];
            if ($operator == self::AND) {
                $opX = $this->query->expr()->andX();
                $op = self::AND;
            } elseif ($operator == self::OR) {
                $opX = $this->query->expr()->orX();
                $op = self::OR;
            }

            $i = $this->qtdArgs;

            if (is_array($var)) {
                foreach ($var as $item) {
                    $dql = $this->columnArray($columnName, $op, $i, self::LIKE);
                    $opX->add($dql);
                    $this->query->setParameter($i, '%'.$item.'%');
                    $i++;
                }
                $this->query->andWhere($opX);
            } else {
                $dql = $this->columnArray($columnName, $op, $i, self::LIKE);
                $this->query->andWhere($dql)->setParameter($i, '%'.$var.'%');
                $i++;
            }
            $this->qtdArgs = $i;
        }

        return $this->query;
    }

    private function columnArray($columnName, $op, $i, $comparator)
    {
        $dql = '';
        if (is_array($columnName)) {
            $op1 = '';
            foreach ($columnName as $nameColumnItem) {
                $dql .= " $op1 $nameColumnItem $comparator ?$i ";
                $op1 = $op;
            }
        } else {
            $dql = " $columnName $comparator ?$i ";
        }

        return $dql;
    }
}
