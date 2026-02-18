<?php

namespace App\Models\Master;

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
     * regional_code_kontrabon: string(10), nullable
     * regional_init_kontrabon: string(10), nullable
     * regional_name_kontrabon: string, nullable
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
        'regional_code_kontrabon',
        'regional_init_kontrabon',
        'regional_name_kontrabon',
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
