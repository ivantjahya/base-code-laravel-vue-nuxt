<?php

namespace App\Models\Passport;

use Laravel\Passport\Token;

class PassportToken extends Token
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_access_tokens';
}
