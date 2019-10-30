<?php

namespace Abdonor\Modules\DocHelper\Lib;

use function GuzzleHttp\Psr7\parse_query;

class DoctrineHelperLib
{
    /** @var int Quantity of arguments, internal variable */
    private $qtdArgs = 0;
    /** @var Search $search */
    private $search;

    const OP_IN = 'IN';
    const OP_OR = 'OR';
    const OP_AND = 'AND';
    const OP_LIKE = 'LIKE';
    const OP_EQUAL = '=';
    const OP_BETWEEN = 'BETWEEN';
    const OP_NULL = 'NULL';

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

    public static function getOrderBy()
    {
        $queryArr = self::getArrayQuery();
        $data['order_by'] = isset($queryArr['order_by']) ? $queryArr['order_by'] : null;
        $data['order_ad'] = isset($queryArr['order_ad']) && $queryArr['order_ad'] == 'DESC' ? 'DESC' : 'ASC';

        return $data;
    }

    public function addOrderBy($column, $order)
    {
        if ($column) {
            $this->query->orderBy($column, $order);
        }
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
        if (isset($_SERVER['QUERY_STRING'])) {
            return parse_query($_SERVER['QUERY_STRING']);
        } else {
            return [];
        }
    }

    public static function getParams($arrAllowed)
    {
        $params = [];
        $queryArr = self::getArrayQuery();

        return self::getParamsInput($arrAllowed, $queryArr);
    }

    public static function getParamsInput($arrAllowed, $queryArr = [])
    {
        $params = [];
        $queryArr = array_merge(self::getArrayQuery(), $queryArr);
        foreach ($queryArr as $key => $value) {
            if (isset($arrAllowed[$key])) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    public static function getParamsNoQS($arrAllowed, $queryArr = [])
    {
        $params = [];
        if (is_array($queryArr)) {
            foreach ($queryArr as $key => $value) {
                if (isset($arrAllowed[$key])) {
                    $params[$key] = $value;
                }
            }
        }

        return $params;
    }

    protected function fieldExists($params, $nameVar)
    {
        try {
            $a = $params[$nameVar];
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function eq($params, $nameVar, $columnName, $operator)
    {
        if ($this->fieldExists($params, $nameVar)) {
            $var = $params[$nameVar];
            if ($operator == self::OP_AND) {
                $opX = $this->query->expr()->andX();
                $op = self::OP_AND;
            } elseif ($operator == self::OP_OR) {
                $opX = $this->query->expr()->orX();
                $op = self::OP_OR;
            }
            $i = $this->qtdArgs;
            if (is_array($var)) {
                foreach ($var as $item) {
                    if ($item === null) {
                        $dql = $this->columnNull($columnName, $op, true);
                        $opX->add($dql);
                    } else {
                        $dql = $this->columnArray($columnName, $op, $i, self::OP_EQUAL);
                        $opX->add($dql);
                        $this->query->setParameter($i, $item);
                    }
                    $i++;
                }
                $this->query->andWhere($opX);
            } else {
                if ($var === null) {
                    $dql = $this->columnNull($columnName, $op, true);
                    $this->query->andWhere($dql);
                } else {
                    $dql = $this->columnArray($columnName, $op, $i, self::OP_EQUAL);
                    $this->query->andWhere($dql)->setParameter($i, $var);
                }
                $i++;
            }
            $this->qtdArgs = $i;
        }
    }

    protected function like($params, $nameVar, $columnName, $operator)
    {
        if ($this->fieldExists($params, $nameVar)) {
            $var = $params[$nameVar];
            if ($operator == self::OP_AND) {
                $opX = $this->query->expr()->andX();
                $op = self::OP_AND;
            } elseif ($operator == self::OP_OR) {
                $opX = $this->query->expr()->orX();
                $op = self::OP_OR;
            }

            $i = $this->qtdArgs;

            if (is_array($var) && $var) {
                foreach ($var as $item) {
                    if ($item === null) {
                        $dql = $this->columnNull($columnName, $op, true);
                        $opX->add($dql);
                    } else {
                        $dql = $this->columnArray($columnName, $op, $i, self::OP_LIKE);
                        $opX->add($dql);
                        $this->query->setParameter($i, '%' . $item . '%');
                    }
                    $i++;
                }
                $this->query->andWhere($opX);
            } else {
                if ($var === null) {
                    $dql = $this->columnNull($columnName, $op, true);
                    $this->query->andWhere($dql);
                } else {
                    $dql = $this->columnArray($columnName, $op, $i, self::OP_LIKE);
                    $this->query->andWhere($dql)->setParameter($i, '%' . $var . '%');
                }
                $i++;
            }
            $this->qtdArgs = $i;
        }

        return $this->query;
    }

    protected function null($params, $nameVar, $columnName, $operator, $isNull = true)
    {
        if (isset($params[$nameVar]) && $params[$nameVar]) {
            $var = $params[$nameVar];
            if ($operator == self::OP_AND) {
                $opX = $this->query->expr()->andX();
                $op = self::OP_AND;
            } elseif ($operator == self::OP_OR) {
                $opX = $this->query->expr()->orX();
                $op = self::OP_OR;
            }

            $i = $this->qtdArgs;

            if (is_array($var) && $var) {
                foreach ($var as $item) {
                    $dql = $this->columnNull($columnName, $op, $isNull);
                    $opX->add($dql);
                    $i++;
                }
                $this->query->andWhere($opX);
            } else {
                $dql = $this->columnNull($columnName, $op, $isNull);
                $this->query->andWhere($dql);
                $i++;
            }
            $this->qtdArgs = $i;
        }

        return $this->query;
    }

    protected function startWith($params, $nameVar, $columnName, $operator)
    {
        if (isset($params[$nameVar]) && $params[$nameVar]) {
            $var = $params[$nameVar];
            if ($operator == self::OP_AND) {
                $opX = $this->query->expr()->andX();
                $op = self::OP_AND;
            } elseif ($operator == self::OP_OR) {
                $opX = $this->query->expr()->orX();
                $op = self::OP_OR;
            }

            $i = $this->qtdArgs;

            if (is_array($var) && $var) {
                foreach ($var as $item) {
                    $dql = $this->columnArray($columnName, $op, $i, self::OP_LIKE);
                    $opX->add($dql);
                    $this->query->setParameter($i, $item.'%');
                    $i++;
                }
                $this->query->andWhere($opX);
            } else {
                $dql = $this->columnArray($columnName, $op, $i, self::OP_LIKE);
                $this->query->andWhere($dql)->setParameter($i, $var.'%');
                $i++;
            }
            $this->qtdArgs = $i;
        }

        return $this->query;
    }

    protected function between($params, $nameVar1, $nameVar2, $columnName)
    {
        if (isset($params[$nameVar1]) && isset($params[$nameVar2]) && $params[$nameVar1] && $params[$nameVar2]) {
            $var1 = $params[$nameVar1];
            $var2 = $params[$nameVar2];

            $i = $this->qtdArgs;

            if (!is_array($var1) && !is_array($var2)) {
                $i2 = $i + 1;
                $dql = $this->columnBetween($columnName, $i, $i2);
                $this->query->andWhere($dql);
                $this->query->setParameter($i, $var1);
                $this->query->setParameter($i2, $var2);
                $i = $i +2;
            }
            $this->qtdArgs = $i;
        }

        return $this->query;
    }

    protected function in($params, $nameVar, $columnName, $operator)
    {
        if (isset($params[$nameVar]) && $params[$nameVar]) {
            $var = $params[$nameVar];
            if ($operator == self::OP_AND) {
                $opX = $this->query->expr()->andX();
                $op = self::OP_AND;
            } elseif ($operator == self::OP_OR) {
                $opX = $this->query->expr()->orX();
                $op = self::OP_OR;
            }
            $i = $this->qtdArgs;
            {
                $dql = " $columnName IN ( ?$i ) ";
                $this->query->andWhere($dql)->setParameter($i, $var);
                $i++;
            }
            $this->qtdArgs = $i;
        }
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

    protected function columnNull($columnName, $op, $isNull = true)
    {
        $dql = '';
        $null = ($isNull)? 'IS NULL' : 'IS NOT NULL';
        if (is_array($columnName)) {
            $op1 = '';
            foreach ($columnName as $nameColumnItem) {
                $dql .= " $op1 $nameColumnItem $null ";
                $op1 = $op;
            }
        } else {
            $dql = " $columnName $null ";
        }

        return $dql;
    }

    protected function columnOperator($params, $nameVar, $columnName, $operator)
    {
        if ($this->validateParamVar($params, $nameVar)) {
            $var = $this->valueVar($params, $nameVar);
            $i = $this->qtdArgs;
            {
                $dql = " $columnName $operator  ?$i ";
                $this->query->andWhere($dql)->setParameter($i, $var);
                $i++;
            }
            $this->qtdArgs = $i;
        }

        return $this->query;
    }

    private function validateParamVar($params, $nameVar)
    {
        return (isset($params[$nameVar]) && $params[$nameVar]);
    }

    private function valueVar($params, $nameVar)
    {
        return $params[$nameVar];
    }
    
    protected function columnBetween($columnName, $i, $i2)
    {
        $comparator = self::OP_BETWEEN;
        return " $columnName $comparator ?$i AND ?$i2 ";
    }

    protected function search($array = [])
    {
        if (!$this->search instanceof Search) {
            $this->search = new Search();
            $o = self::getOrderBy();
            $orderBy = $o['order_by'];
            $orderAscOrDesc =  $o['order_ad'];
            if ($orderBy) {
                if (array_key_exists($orderBy, $array)) {
                    $orderBy = $array[$orderBy];
                }
            }
            $this->search->setOrderBy($orderBy);
            $this->search->setAscOrDesc($orderAscOrDesc);
            $this->search->setRange(self::getRange());
        }

        return $this->search;
    }
}
