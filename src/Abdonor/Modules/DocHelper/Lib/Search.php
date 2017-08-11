<?php

namespace Abdonor\Modules\DocHelper\Lib;

class Search
{
    protected $orderBy;
    protected $ascOrDesc;
    protected $params;
    protected $range;

    /**
     * @return mixed
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param mixed $orderBy
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAscOrDesc()
    {
        return $this->ascOrDesc;
    }

    /**
     * @param mixed $ascOrDesc
     */
    public function setAscOrDesc($ascOrDesc)
    {
        $this->ascOrDesc = $ascOrDesc;

        return $this;
    }
 
    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * @param mixed $range
     */
    public function setRange($range)
    {
        $this->range = $range;
    }
}