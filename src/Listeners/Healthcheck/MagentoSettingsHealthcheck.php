<?php

namespace Rapidez\Core\Listeners\Healthcheck;

use Illuminate\Support\Facades\DB;
use PDOException;
use Rapidez\Core\Facades\Rapidez;

class MagentoSettingsHealthcheck extends Base
{
    public function handle()
    {
        $response = [
            'healthy'  => true,
            'messages' => [],
        ];

        try {
            DB::connection()->getPDO();
            DB::connection()->getDatabaseName();
        } catch (PDOException $e) {
            $response['healthy'] = false;
            $response['messages'][] = ['type' => 'error', 'value' => __('Database connection could not be established! :message', ['message' => PHP_EOL . $e->getMessage()])];

            return $response;
        }

        $response = $this->checkCustomerConfiguration($response);

        return $response;
    }

    public function checkCustomerConfiguration(array $response): array
    {
        if (Rapidez::config('webapi/jwtauth/customer_expiration', 60) <= 60) {
            $response['messages'][] = ['type' => 'warn', 'value' => __('Customer JWT token expiration is set to 60 minutes, customers will be logged out every hour! See: https://docs.rapidez.io/5.x/configuration.html#customer-token-lifetime')];
        }

        if (Rapidez::config('customer/online_customers/section_data_lifetime', 60) <= 60) {
            $response['messages'][] = ['type' => 'warn', 'value' => __('Customer Data Lifetime is set to 60 minutes, this could result in customers losing their cart! See: https://docs.rapidez.io/5.x/configuration.html#customer-data-lifetime')];
        }

        return $response;
    }
}
