@if (Rapidez::checkCompadreVersion('0.0.1'))
    <x-rapidez::label for="option_{{ $option->option_id }}">
        {{ $option->title }} {{ $option->price_label }}
    </x-rapidez::label>
    <x-rapidez::input
        type="file"
        :label="false"
        :name="false"
        id="option_{{ $option->option_id }}"
        :required="$option->is_require"
        v-on:change="addToCart.setCustomOptionFile($event, {{ $option->option_id }})"
        class="px-0"
    />
    @if ($option->file_extension)
        <p class="text-sm">@lang('Compatible file extensions to upload'): <strong>{{ $option->file_extension }}</strong></p>
    @endif
    @if ($option->image_size_x)
        <p class="text-sm">@lang('Maximum image width'): <strong>{{ $option->image_size_x }}</strong></p>
    @endif
    @if ($option->image_size_y)
        <p class="text-sm">@lang('Maximum image height'): <strong>{{ $option->image_size_y }}</strong></p>
    @endif
@else
    @php(Log::error('File upload customizable option is being used, for now you will need to install rapidez/magento2-compadre'))
@endif
