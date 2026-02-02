<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SiteNetwork extends Model
{
    use HasUuids;

    /**
     * Column info:
     *
     * id: uuid
     * parent_code: integer
     * child_code: integer
     * type: string
     * comm_network: string
     * comm_network_desc: string
     * site_class: integer
     * created_at: timestamp
     * updated_at: timestamp
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_code',
        'child_code',
        'type',
        'comm_network',
        'comm_network_desc',
        'site_class',
    ];
}
