<?php

namespace Rapidez\Core\Models;

class CustomerGroup extends Model
{
    protected $table = 'customer_group';
    protected $primaryKey = 'customer_group_id';

    public $timestamps = false;

    protected $guarded = [];

    public function taxCalculations()
    {
        return $this->hasMany(TaxCalculation::class, 'customer_tax_class_id', 'tax_class_id');
    }
}
