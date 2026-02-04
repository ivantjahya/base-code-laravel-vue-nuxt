<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable as Prunable;
use Illuminate\Database\Eloquent\Model;

class MidGoldSite extends Model
{
    use Prunable;

    /**
     * Column info:
     *
     * id: integer - autoincrement
     * site: string(10)
     * code: string(10)
     * initial: string(10)
     * name: string
     * address: string, nullable
     * city: string, nullable
     * region: string, nullable
     * im_auto_flag: integer, default 1
     * dc_flag: integer, default 0
     * created_at: timestamp
     * updated_at: timestamp
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mid_gold_sites';

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
        'site',
        'code',
        'initial',
        'name',
        'address',
        'city',
        'region',
        'im_auto_flag',
        'dc_flag',
    ];
}
