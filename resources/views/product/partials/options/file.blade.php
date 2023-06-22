<template v-if="option.fileValue">
    <x-rapidez::label ::for="option.uid">
        @{{ option.title }} @{{ option.fileValue | plus_price_type }}
    </x-rapidez::label>
    <x-rapidez::input
        type="file"
        :label="false"
        :name="false"
        ::id="option.uid"
        ::required="option.required"
        v-on:change="setCustomOptionFile($event, option.option_id)"
        class="px-0"
    />
    <p class="text-sm" v-if="option.fileValue.file_extension">
        @lang('Compatible file extensions to upload'): <strong>@{{ option.fileValue.file_extension }}</strong>
    </p>
    <p class="text-sm" v-if="option.fileValue.image_size_x">
        @lang('Maximum image width'): <strong>@{{ option.fileValue.image_size_x }}</strong>
    </p>
    <p class="text-sm" v-if="option.fileValue.image_size_y">
        @lang('Maximum image height'): <strong>@{{ option.fileValue.image_size_y }}</strong>
    </p>
</template>
