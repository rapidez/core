<template v-if="option.fieldValue">
    <x-rapidez::label ::for="option.uid">
        @{{ option.title }} @{{ option.fieldValue | plus_price_type }}
    </x-rapidez::label>
    <x-rapidez::input
        :label="false"
        :name="false"
        ::id="option.uid"
        ::required="option.required"
        ::maxlength="option.fieldValue.max_characters || false"
        v-model="customOptions[option.option_id]"
    />
</template>
