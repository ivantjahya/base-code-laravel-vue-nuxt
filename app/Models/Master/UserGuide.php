<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserGuide extends Model
{
    use HasUuids;

    /**
     * Column info:
     *
     * id: uuid
     * menu_id: uuid
     * name: string(100)
     * description: string
     * filename: string
     * filepath: string
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
        'menu_id',
        'name',
        'description',
        'filename',
        'filepath',
        'created_by',
        'updated_by',
    ];
}
