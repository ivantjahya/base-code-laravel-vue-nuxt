<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Limit extends Model
{
    use HasUuids;

    /**
     * Column info:
     *
     * id: uuid
     * code: string(50)
     * min_value: integer
     * max_value: integer
     * start_date: date
     * end_date: date
     * status: integer
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
        'code',
        'min_value',
        'max_value',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'updated_by',
    ];
}
