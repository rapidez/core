<?php

namespace Rapidez\Core\Tests\Feature\View;

use Rapidez\Core\Tests\TestCase;

class ButtonTest extends TestCase
{
    /**
     * @test
     */
    public function button_component_tag_is_a_button()
    {
        $this->blade('<x-rapidez::button>Test</x-rapidez::button>')->assertSee('<button', escape: false);
    }

    /**
     * @test
     */
    public function button_component_tag_is_a_anchor_with_hrefs()
    {
        $this->blade('<x-rapidez::button href="/link">Test</x-rapidez::button>')->assertSee('<a', escape: false);
        $this->blade('<x-rapidez::button ::href="link">Test</x-rapidez::button>')->assertSee('<a', escape: false);
        $this->blade('<x-rapidez::button v-bind:href="link">Test</x-rapidez::button>')->assertSee('<a', escape: false);
    }

    /**
     * @test
     */
    public function button_component_tag_is_a_label_with_for()
    {
        $this->blade('<x-rapidez::button for="something">Test</x-rapidez::button>')->assertSee('<label', escape: false);
    }
}
