<?php

namespace App\Models\Middleware;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable as Prunable;
use Illuminate\Database\Eloquent\Model;

class MidVendKontrabonRegional extends Model
{
    use Prunable;

    /**
     * Column info:
     *
     * id: integer - autoincrement
     * site: string(10)
     * regional_code_kontrabon: string(10)
     * regional_init_kontrabon: string(10)
     * regional_name_kontrabon: string
     * status: integer, default 0
     * description: string, nullable
     * created_at: timestamp
     * updated_at: timestamp
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mid_vend_kontrabon_regionals';

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
        'regional_code_kontrabon',
        'regional_init_kontrabon',
        'regional_name_kontrabon',
        'status',
        'description',
    ];
}
