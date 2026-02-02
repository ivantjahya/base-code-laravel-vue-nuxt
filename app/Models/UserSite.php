<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserSite extends Model
{
    use HasUuids;

    /**
     * Column info:
     *
     * user_id: uuid
     * site_id: uuid
     * created_at: timestamp
     * updated_at: timestamp, nullable
     * created_by: uuid, nullable
     * updated_by: uuid, nullable
     */

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false; // No auto-increment id

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = null; // No primary key column named 'id'

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'site_id',
        'created_by',
        'updated_by',
    ];
}
