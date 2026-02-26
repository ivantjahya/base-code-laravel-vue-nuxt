<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class UserMerchStruct extends Model
{
    /**
     * Column info:
     *
     * user_id: uuid
     * merch_struct_id: uuid
     * created_at: timestamp
     * updated_at: timestamp, nullable
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
        'merch_struct_id',
    ];
}
