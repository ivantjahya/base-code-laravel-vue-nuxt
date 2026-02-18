<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ApprovalPoFlow extends Model
{
    use HasUuids;

    /**
     * Column info:
     *
     * id: uuid
     * code: string(50)
     * name: string
     * merch_struct_id: uuid
     * limit_id: uuid
     * po_status_id: uuid
     * next_profile_id: uuid
     * next_po_status_id: uuid
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
        'merch_struct_id',
        'limit_id',
        'po_status_id',
        'next_profile_id',
        'next_po_status_id',
        'created_by',
        'updated_by',
    ];
}
