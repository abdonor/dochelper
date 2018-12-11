<?php

namespace Abdonor\Modules\DocHelper;

use Abdonor\Modules\DocHelper\Lib\DoctrineHelperLib;
use Doctrine\ORM\QueryBuilder;

class DoctrineHelper extends DoctrineHelperLib
{
    /**
     * @info You need to instantiate this var in your code with the Doctrine\ORM\QueryBuilder
     * Use the method setQuery to pass your QueryBuilder.
     * @var QueryBuilder */
    protected $query;

    /**
     * Example of $range: $range = ['limit' => 100, 'offset' => 20];
     * @param $range
     */
    public function addRange($range)
    {
        return parent::addRange($range);
    }

    /**
     * It allows you to get the range from your Query String
     * @return null|['limit' => 100, 'offset' => 20]
     */
    public static function getRange()
    {
        return parent:: getRange();
    }

    /**
     * @param $column
     * @param string $order
     */
    public function addOrderBy($column, $order = 'ASC')
    {
        parent::addOrderBy($column, $order);
    }

    /**
     * @param $arrAllowed Are the params allowed to be searched in your query
     * An example of "$arrAllowed": $allowedParams = ['name' => 'name', 'id' => 'id',];
     * @return array
     */
    public static function getParams($arrAllowed)
    {
        return parent::getParams($arrAllowed);
    }

    public static function getOrderBy()
    {
        return parent::getOrderBy();
    }

    /**
     * @param $params - key value of your params to be searched.
     * @param $nameVar - Name of the var from your query string
     * @param $nameColumn - name of your column from your entity
     * @return result from database
     */
    public function andLikeOr($params, $nameVar, $nameColumn)
    {
        return $this->like($params, $nameVar, $nameColumn, DoctrineHelperLib::OP_OR);
    }

    /**
     * @param $params - key value of your params to be searched.
     * @param $nameVar - Name of the var from your query string
     * @param $nameColumn - name of your column from your entity
     * @return result from database
     */
    public function andStartWithOr($params, $nameVar, $nameColumn)
    {
        return $this->startWith($params, $nameVar, $nameColumn, DoctrineHelperLib::OP_OR);
    }

    /**
     * @param $params - key value of your params to be searched.
     * @param $nameVar - Name of the var from your query string
     * @param $nameColumn - name of your column from your entity
     * @return result from database
     */
    public function andEndWithAnd($params, $nameVar, $nameColumn)
    {
        return $this->startWith($params, $nameVar, $nameColumn, DoctrineHelperLib::OP_AND);
    }
    
    /**
     * @param $params - key value of your params to be searched.
     * @param $nameVar - Name of the var from your query string
     * @param $nameColumn - name of your column from your entity
     * @return result from database
     */
    public function andLikeAnd($params, $nameVar, $nameColumn)
    {
        return $this->like($params, $nameVar, $nameColumn, DoctrineHelperLib::OP_AND);
    }

    /**
     * @param $params - key value of your params to be searched.
     * @param $nameVar - Name of the var from your query string
     * @param $nameColumn - name of your column from your entity
     * @return result from database
     */
    public function andEqOr($params, $nameVar, $nameColumn)
    {
        return $this->eq($params, $nameVar, $nameColumn, DoctrineHelperLib::OP_OR);
    }

    /**
     * This between, doesnt allow multiple search.
     * @param $params - key value of your params to be searched.
     * @param $nameVar1 - Name 1 of the var from your query string
     * @param $nameVar2 - Name 2 of the var from your query string
     * @param $nameColumn - name of your column from your entity
     * @return result from database
     */
    public function andBetween($params, $nameVar1, $nameVar2, $nameColumn)
    {
        return $this->between($params, $nameVar1, $nameVar2, $nameColumn);
    }

    public function andIn($params, $nameVar1, $nameColumn)
    {
        return $this->in($params, $nameVar1, $nameColumn, DoctrineHelperLib::OP_OR);
    }

    /** @return Lib\Search */
    public function search()
    {
        return parent::search();
    }
}
