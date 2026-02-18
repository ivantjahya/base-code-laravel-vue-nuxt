<?php

namespace App\Models\Passport;

use Laravel\Passport\AuthCode;

class PassportAuthCode extends AuthCode
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_auth_codes';
}
