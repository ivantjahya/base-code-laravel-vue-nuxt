<?php

namespace App\Models;

use Laravel\Passport\RefreshToken;

class PassportRefreshToken extends RefreshToken
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_refresh_tokens';
}
