<?php

namespace Rapidez\Core\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Rapidez\Core\Models\Scopes\IsActiveScope;

/**
 * Class SearchQuery
 *
 * @property int $query_id
 * @property string|null $query_text
 * @property int $num_results
 * @property int $popularity
 * @property string|null $redirect
 * @property int $store_id
 * @property int $display_in_terms
 * @property int|null $is_active
 * @property int|null $is_processed
 * @property Carbon $updated_at
 * @property Store $store
 * @property Collection|CatalogsearchRecommendation[] $catalogsearch_recommendations
 */
class SearchQuery extends Model
{
    protected $table = 'search_query';

    protected $primaryKey = 'query_id';

    public $timestamps = false;

    protected $casts = [
        'num_results' => 'int',
        'popularity' => 'int',
        'store_id' => 'int',
        'display_in_terms' => 'int',
        'is_active' => 'int',
        'is_processed' => 'int',
    ];

    protected $fillable = [
        'query_text',
        'num_results',
        'popularity',
        'redirect',
        'store_id',
        'display_in_terms',
        'is_active',
        'is_processed',
    ];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new IsActiveScope());
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function catalogsearch_recommendations()
    {
        return $this->hasMany(CatalogsearchRecommendation::class, 'relation_id');
    }
}
