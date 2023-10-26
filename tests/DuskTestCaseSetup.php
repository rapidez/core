<?php

namespace Rapidez\Core\Tests;

use Closure;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Assert;
use Rapidez\Core\Models\Product;

trait DuskTestCaseSetup
{
    public string $flat;

    public Product $testProduct;

    protected function setUp(): void
    {
        parent::setUp();

        Browser::macro('waitUntilTrueForDuration', function (string $script = 'true', $timeout = 120, $for = 0.5) {
            // Waits until the script is truthy for x seconds, supports await.
            $interval = 0.05;

            /** @var Browser $this */
            $this->waitUntil('await new Promise((resolve, reject) => {
                let counter = 0;
                let interval = setInterval(async function () {
                    let result = ' . $script . ';
                    counter = result ? counter + 1 : 0;
                    if (counter >= ' . ($for / $interval) . ') {
                        clearInterval(interval)
                        resolve(true)
                    }
                }, ' . ($interval * 1000) . ');
                setTimeout(() => resolve(false), ' . ($timeout * 1000) . ')
            }) === true', $timeout);

            return $this;
        });

        Browser::macro('waitUntilIdle', function ($timeout = 120) {
            /** @var Browser $this */
            $this->waitUntilTrueForDuration('window.app?.$data?.loading !== true && await new Promise((resolve, reject) => window.requestIdleCallback((deadline) => resolve(!deadline.didTimeout), {timeout: 5}))', $timeout);

            return $this;
        });

        Browser::macro('assertFormValid', function ($selector) {
            /** @var Browser $this */
            $fullSelector = $this->resolver->format($selector);
            Assert::assertEquals(
                true,
                $this->driver->executeScript("return document.querySelector('{$fullSelector}').reportValidity();"),
                'Form is not valid: ' . PHP_EOL . $this->driver->executeScript("return Array.from(document.querySelector('{$fullSelector}').elements).filter(el => !el.validity.valid).map(el => el.name + ': ' + el.validationMessage).join('\\n');")
            );

            return $this;
        });

        Browser::macro('assertSeePrice', function ($price) {
            /** @var Browser $this */
            $this->assertSee(preg_replace("/\s+/u", ' ', price($price)));

            return $this;
        });

        $this->flat = (new Product)->getTable();

        $this->testProduct = Product::selectAttributes([
            'name',
            'price',
            'special_price',
            'url_key',
        ])->firstWhere($this->flat . '.sku', env('TEST_PRODUCT', '24-WB02'));

        // We're running with the database from the
        // michielgerritsen/magento-project-community-edition
        // image and the tax is not configured yet.
        if (DB::table('admin_user')->where('email', 'user@example.com')->exists()
            && DB::table('tax_calculation_rate')->where('code', 'Default Tax Rate NL')->doesntExist()) {
            $this->configureTax();
        }
    }

    protected function configureTax()
    {
        $taxRateDefaultId = DB::table('tax_calculation_rate')->insertGetId([
            'tax_country_id' => 'NL',
            'tax_region_id'  => 0,
            'tax_postcode'   => '*',
            'code'           => 'Default Tax Rate NL',
            'rate'           => 21,
        ]);

        $taxRateNoneId = DB::table('tax_calculation_rate')->insertGetId([
            'tax_country_id' => 'NL',
            'tax_region_id'  => 0,
            'tax_postcode'   => '*',
            'code'           => 'No Tax Rate NL',
            'rate'           => 0,
        ]);

        $taxRuleDefaultId = DB::table('tax_calculation_rule')->insertGetId([
            'code'               => 'Default Tax Rate NL',
            'priority'           => 0,
            'position'           => 0,
            'calculate_subtotal' => 0,
        ]);

        $taxRuleNoneId = DB::table('tax_calculation_rule')->insertGetId([
            'code'               => 'No Tax Rate NL',
            'priority'           => 0,
            'position'           => 0,
            'calculate_subtotal' => 0,
        ]);

        $retailCustomerClassId = DB::table('tax_class')->where('class_name', 'Retail Customer')->pluck('class_id')->first();

        $noTaxClassId = DB::table('tax_class')->insertGetId([
            'class_name' => 'No Tax',
            'class_type' => 'CUSTOMER',
        ]);

        $productTaxClassId = DB::table('tax_class')->where('class_name', 'Taxable Goods')->pluck('class_id')->first();

        DB::table('tax_calculation')->insert([
            [
                'tax_calculation_rate_id' => $taxRateDefaultId,
                'tax_calculation_rule_id' => $taxRuleDefaultId,
                'customer_tax_class_id'   => $retailCustomerClassId,
                'product_tax_class_id'    => $productTaxClassId,
            ],
            [
                'tax_calculation_rate_id' => $taxRateNoneId,
                'tax_calculation_rule_id' => $taxRuleNoneId,
                'customer_tax_class_id'   => $noTaxClassId,
                'product_tax_class_id'    => $productTaxClassId,
            ],
        ]);

        DB::table('core_config_data')->upsert(
            [
                [
                    'scope'    => 'default',
                    'scope_id' => 0,
                    'path'     => 'tax/defaults/country',
                    'value'    => 'NL',
                ],
                [
                    'scope'    => 'default',
                    'scope_id' => 0,
                    'path'     => 'tax/display/type',
                    'value'    => 2,
                ],
                [
                    'scope'    => 'default',
                    'scope_id' => 0,
                    'path'     => 'tax/display/shipping',
                    'value'    => 2,
                ],
            ],
            ['scope', 'scope_id', 'path'],
            ['value']
        );

        DB::table('customer_group')->where('customer_group_code', 'General')->update(['tax_class_id' => $noTaxClassId]);
    }

    public function browseWithConfigs(array $configs, Closure $callback)
    {
        $configModel = config('rapidez.models.config');

        try {
            // First we get the current value and store the new value.
            foreach ($configs as $path => $value) {
                $previousValues[$path] = $configModel::where(compact('path'))->first();
                $configModel::query()->updateOrInsert(compact('path'), compact('value'));
            }

            $this->browse($callback);
        } finally {
            // Afterwards we update the previous values or remove new ones.
            foreach ($configs as $path => $value) {
                if ($previousValues[$path]) {
                    $previousValues[$path]->query()->update(['value' => $previousValues[$path]->value]);
                } else {
                    $configModel::query()->where(compact('path'))->delete();
                }
            }
        }
    }
}
