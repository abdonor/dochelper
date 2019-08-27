<?php

namespace Abdonor\Modules\DocHelper\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Class DocHelper
 * @package Abdonor\Modules\DocHelper\Facade
 * @method static array getParams()
 * @method static array setParamsByInput()
 * @method static array setParamsNoQS()
 * @method static array getRange()
 * @method static array getOrderBy()
 * @method static array search()
 * @method static array getRequest()
 */
class DocHelper extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Abdonor\Modules\DocHelper\DoctrineHelper::class;
    }
}
