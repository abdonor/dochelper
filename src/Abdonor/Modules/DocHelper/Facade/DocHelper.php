<?php

namespace Abdonor\Modules\DocHelper\Facade;

use Illuminate\Support\Facades\Facade;

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