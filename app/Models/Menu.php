<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasUuids;

    /**
     * Column info:
     *
     * id: uuid
     * name: string(100)
     * url: string(100)
     * icon: string(100), nullable
     * parent_id: uuid, nullable
     * code: integer, nullable
     * created_at: timestamp
     * updated_at: timestamp
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'url',
        'icon',
        'parent_id',
        'code',
    ];
}
