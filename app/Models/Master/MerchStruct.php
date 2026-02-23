<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MerchStruct extends Model
{
    use HasUuids;

    /**
     * Column info:
     *
     * id: uuid
     * code: string(50)
     * name: string(255)
     * parent_id: uuid (nullable)
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
        'parent_id',
    ];
}
