<?php

namespace Abdonor\Modules\DocHelper\Lib;

use function GuzzleHttp\Psr7\parse_query;

class DoctrineHelperLib
{
    /** @var int Quantity of arguments, internal variable */
    private $qtdArgs = 0;

    const OR = 'OR';
    const AND = 'AND';
    const LIKE = 'LIKE';
    const EQUAL = '=';

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

    public function addOrderBy($column, $order)
    {
        $this->query->orderBy($column, $order);
    }

    /**
     * This var is mandatory
     * @param $query - your repository QueryBuilder */
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

    protected function eq($params, $nameVar, $columnName, $operator)
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

    protected function like($params, $nameVar, $columnName, $operator)
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

    protected function columnArray($columnName, $op, $i, $comparator)
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