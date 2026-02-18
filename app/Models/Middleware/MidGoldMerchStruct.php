<?php

namespace App\Models\Middleware;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable as Prunable;
use Illuminate\Database\Eloquent\Model;

class MidGoldMerchStruct extends Model
{
    /**
     * Column info:
     *
     * id: integer - autoincrement
     * code: string(50)
     * description: string
     * parent_code: string(50), nullable
     * parent_description: string, nullable
     * created_at: timestamp
     * updated_at: timestamp
     */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mid_gold_merch_structs';

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
        'description',
        'parent_code',
        'parent_description',
    ];
}
