<?php

namespace Rapidez\Core\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Config;
use Rapidez\Core\Tests\TestCase;

class ConfigTest extends TestCase
{
    #[Test]
    public function config_can_be_intentionally_null()
    {
        $test = Config::create([
            'path'  => 'rapidez/test/value_null',
            'value' => null,
        ]);

        $this->assertNull(Rapidez::config('rapidez/test/value_null', 'default'));
        $this->assertEquals('default', Rapidez::config('rapidez/test/nonexistent_value', 'default'));

        $test->delete();
    }
}
