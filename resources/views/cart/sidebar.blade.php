<dl class="mb-5 flex w-full flex-col rounded-lg border [&>*]:flex [&>*]:flex-wrap [&>*]:justify-between [&>*]:p-3 [&>*]:border-b [&>*:last-child]:border-none">
    <div v-for="segment in cart.sidebarSegments.value">
        <dt>
            @{{ segment.title }}
        </dt>
        <dd>
            <template v-if="segment.value">
                @{{ segment.value | price }}
            </template>
            <template v-else>
                @{{ segment.display_tax ? segment.value_including_tax : segment.value_excluding_tax | price }}
            </template>
        </dd>
        <small v-if="segment.subtitle">
            @{{ segment.subtitle }}
        </small>
    </div>
</dl>

<x-rapidez::button href="{{ route('checkout') }}" dusk="checkout" class="w-full text-center">
    @lang('Checkout')
</x-rapidez::button>
