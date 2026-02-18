<?php

namespace App\Models\Middleware;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable as Prunable;
use Illuminate\Database\Eloquent\Model;

class MidEbsSite extends Model
{
    /**
     * Column info:
     *
     * id: integer - autoincrement
     * code: string(10)
     * initial: string(10)
     * name: string
     * company_code: string
     * company_name: string
     * created_at: timestamp
     * updated_at: timestamp
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mid_ebs_sites';

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
        'code',
        'initial',
        'name',
        'company_code',
        'company_name',
    ];
}
