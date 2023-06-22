<template v-if="option.areaValue">
    <x-rapidez::label ::for="option.uid">
        @{{ option.title }} @{{ option.areaValue | plus_price_type }}
    </x-rapidez::label>
    <x-rapidez::textarea
        :label="false"
        :name="false"
        ::id="option.uid"
        ::required="option.required"
        ::maxlength="option.areaValue.max_characters || false"
        v-model="customOptions[option.option_id]"
    />
</template>
