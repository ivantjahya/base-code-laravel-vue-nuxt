<?php

namespace App\Interfaces;

use App\Traits\CommonFunction;

class InterfaceClass implements CacheKeyConst, DefaultValueConst, MenuConst, MenuCtrlConst
{
    use CommonFunction;

    public function __construct()
    {
        //
    }
}
