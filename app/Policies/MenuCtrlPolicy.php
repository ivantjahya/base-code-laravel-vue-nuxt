<?php

namespace App\Policies;

use App\Traits\PythonMasterDataMicroserviceFunction;

class MenuCtrlPolicy
{
    use PythonMasterDataMicroserviceFunction;

    public function register(): void
    {
        /**
         * List policy of menu control
         */
    }
}
