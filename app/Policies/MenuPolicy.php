<?php

namespace App\Policies;

use App\Traits\PythonMasterDataMicroserviceFunction;

class MenuPolicy
{
    use PythonMasterDataMicroserviceFunction;

    public function register(): void
    {
        /**
         * List policy of parent menu
         */
    }
}
