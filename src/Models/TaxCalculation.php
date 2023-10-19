<?php

namespace Rapidez\Core\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TaxCalculation extends Model
{
	protected $table = 'tax_calculation';
	protected $primaryKey = 'tax_calculation_id';
	public $timestamps = false;

	protected $guarded = [];

	protected static function boot()
	{
        parent::boot();

		static::addGlobalScope(
            'withRates',
            fn (Builder $builder) => $builder
                ->join('tax_calculation_rate', 'tax_calculation.tax_calculation_rate_id', '=', 'tax_calculation_rate.tax_calculation_rate_id')
        );
	}

    public function customerGroups()
    {
        return $this->hasOne(CustomerGroup::class, 'tax_class_id', 'customer_tax_class_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'tax_class_id', 'product_tax_class_id');
    }
}
