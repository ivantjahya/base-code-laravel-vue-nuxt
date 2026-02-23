<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class FuncProfile extends Model
{
    use HasUuids;

    /**
     * Column info:
     *
     * id: uuid
     * code: string(50)
     * name: string
     * profile_id: uuid
     * merch_struct_id: uuid
     * limit_code: string
     * status: integer (1: Active, 0: Inactive)
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
        'name',
        'profile_id',
        'merch_struct_id',
        'limit_code',
        'status',
        'created_by',
        'updated_by',
    ];
}
