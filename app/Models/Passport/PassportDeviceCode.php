<?php

namespace App\Models\Passport;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Passport\DeviceCode;

class PassportDeviceCode extends DeviceCode
{
    use HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_device_codes';
}
