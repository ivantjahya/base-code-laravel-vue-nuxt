<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable as Prunable;
use Illuminate\Database\Eloquent\Model;

class MidGoldSiteNetwork extends Model
{
    use Prunable;

    /**
     * Column info:
     *
     * id: integer - autoincrement
     * parent_code: integer
     * child_code: integer
     * type: string(100)
     * comm_network: string
     * comm_network_desc: string(100)
     * site_class: integer
     * created_at: timestamp
     * updated_at: timestamp
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mid_gold_site_networks';

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(1));
    }

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
