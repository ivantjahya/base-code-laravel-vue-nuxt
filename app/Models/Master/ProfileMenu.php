<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProfileMenu extends Model
{
    use HasUuids;

    /**
     * Column info:
     *
     * profile_id: uuid
     * menu_id: uuid
     * acc_control_id: uuid
     * created_at: timestamp
     * updated_at: timestamp
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_id',
        'menu_id',
        'acc_control_id',
    ];
}
