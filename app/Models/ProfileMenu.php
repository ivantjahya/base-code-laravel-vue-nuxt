<?php

namespace App\Models;

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
     * created_at: timestamp
     * updated_at: timestamp
     * created_by: uuid
     * updated_by: uuid
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_id',
        'menu_id',
        'created_by',
        'updated_by',
    ];
}
