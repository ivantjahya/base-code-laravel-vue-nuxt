<?php

namespace App\Models\Master;

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
     * min_value: bigInteger
     * max_value: bigInteger
     * start_date: date
     * end_date: date
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
        'created_by',
        'updated_by',
    ];
}
