<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasUuids;

    /**
     * Column info:
     *
     * id: uuid
     * site: string(10)
     * code: string(10)
     * initial: string(10)
     * name: string
     * address: string, nullable
     * city: string, nullable
     * region: string, nullable
     * im_auto_flag: integer, default 1
     * dc_flag: integer, default 0
     * code_ebs: string(10), nullable
     * initial_ebs: string(10), nullable
     * name_ebs: string, nullable
     * company_code_ebs: string, nullable
     * company_name_ebs: string, nullable
     * start_date: date
     * end_date: date
     * created_at: timestamp
     * updated_at: timestamp
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site',
        'code',
        'initial',
        'name',
        'address',
        'city',
        'region',
        'im_auto_flag',
        'dc_flag',
        'code_ebs',
        'initial_ebs',
        'name_ebs',
        'company_code_ebs',
        'company_name_ebs',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
