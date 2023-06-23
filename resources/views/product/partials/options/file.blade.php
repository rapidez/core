<x-rapidez::label for="option_{{ $option->option_id }}">
    {{ $option->title }} {{ $option->price_label }}
</x-rapidez::label>
<x-rapidez::input
    type="file"
    :label="false"
    :name="false"
    id="option_{{ $option->option_id }}"
    required="{{ $option->is_require }}"
    v-on:change="setCustomOptionFile($event, {{ $option->option_id }})"
    class="px-0"
/>
@if($option->file_extension)
    <p class="text-sm">@lang('Compatible file extensions to upload'): <strong>{{ $option->file_extension }}</strong></p>
@endif
@if($option->image_size_x)
    <p class="text-sm">@lang('Maximum image width'): <strong>{{ $option->image_size_x }}</strong></p>
@endif
@if($option->image_size_y)
    <p class="text-sm">@lang('Maximum image height'): <strong>{{ $option->image_size_y }}</strong></p>
@endif

