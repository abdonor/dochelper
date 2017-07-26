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

    private $qtdArgs = 0;

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

    public function addOrderBy($column, $order = 'ASC')
    {
        $this->query->orderBy($column, $order);
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public static function getArrayQuery()
    {
        return parse_query($_SERVER['QUERY_STRING']);
    }

    /**
     * @param $arrAllowed Are the params allowed to be searched in your query
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
        if(isset($params[$nameVar])) {
            $var = $params[$nameVar];
            $orX = $this->query->expr()->orX();
            $i = $this->qtdArgs;
            if (is_array($var)) {
                foreach ($var as $item) {
                    $dql = " $nameColumn LIKE ?$i ";
                    $orX->add($dql);
                    $this->query->setParameter($i, '%'.$item.'%');
                    $i++;
                }
                $this->query->andWhere($orX);
            } else {
                $this->query->andWhere("$nameColumn LIKE ?$i ")->setParameter($i, '%'.$var.'%');
                $i++;
            }
            $this->qtdArgs = $i;
        }
    }

    public function andEqOr($params, $nameVar, $nameColumn)
    {
        if(isset($params[$nameVar])) {
            $var = $params[$nameVar];
            $orX = $this->query->expr()->orX();
            $i = $this->qtdArgs;
            if (is_array($var)) {
                foreach ($var as $item) {
                    $dql = " $nameColumn = ?$i ";
                    $orX->add($dql);
                    $this->query->setParameter($i, $item);
                    $i++;
                }

                $this->query->andWhere($orX);
            } else {
                $this->query->andWhere("$nameColumn = ?$i")->setParameter($i, $var);
                $i++;
            }
            $this->qtdArgs = $i;
        }
    }
}
