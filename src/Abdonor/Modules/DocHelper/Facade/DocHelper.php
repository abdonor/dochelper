<?php

namespace Abdonor\Modules\DocHelper\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Class DocHelper
 * @package Abdonor\Modules\DocHelper\Facade
 * @method static addRange($range)
 * @method static array setRange()
 * @method static array getRange()
 * @method static array getParams($arrAllowed)
 * @method static array getParamsNoQS($arrAllowed, $queryArr = [])
 * @method static array getOrderBy()
 * @method static array getParamsInput($arrAllowed, $queryArr = [])
 * @method static andLikeOr($params, $nameVar, $nameColumn)
 * @method static andStartWithOr($params, $nameVar, $nameColumn)
 * @method static andEndWithAnd($params, $nameVar, $nameColumn)
 * @method static andLikeAnd($params, $nameVar, $nameColumn)
 * @method static andEqOr($params, $nameVar, $nameColumn)
 * @method static andNull($params, $nameVar, $nameColumn)
 * @method static andNotNull($params, $nameVar, $nameColumn)
 * @method static andBetween($params, $nameVar1, $nameVar2, $nameColumn)
 * @method static andIn($params, $nameVar1, $nameColumn)
 * @method static Search search($array = [])
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
