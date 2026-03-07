<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Management Configurations
    |--------------------------------------------------------------------------
    | This section contains configurations related to user management,
    | including default passwords and password reset settings.
    |
    */

    'reset_password' => env('RESET_PASSWORD_DATA', 'login'),

    'default_password_user' => env('DEFAULT_PASSWORD_USER', 'login'),

    'default_password_supplier' => env('DEFAULT_PASSWORD_SUPPLIER', 'login'),

];
