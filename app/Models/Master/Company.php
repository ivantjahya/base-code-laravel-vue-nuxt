<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasUuids;

    /**
     * Column info:
     *
     * id: uuid
     * code: string(10)
     * name: string
     * address: string, nullable
     * city: string, nullable
     * region: string, nullable
     * status: integer, default 1
     * initial: string(10), nullable
     * created_at: timestamp
     * updated_at: timestamp
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'region',
        'status',
        'initial',
    ];
}
