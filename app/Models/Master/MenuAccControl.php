<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class MenuAccControl extends Model
{
    /**
     * Column info:
     *
     * menu_id: uuid
     * acc_control_id: uuid
     * created_at: timestamp
     * updated_at: timestamp
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
        'menu_id',
        'acc_control_id',
    ];
}
