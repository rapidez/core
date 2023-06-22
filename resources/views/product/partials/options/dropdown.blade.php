<template v-if="option.dropdownValue">
    <x-rapidez::label ::for="option.uid">
        @{{ option.title }}
    </x-rapidez::label>
    <x-rapidez::select
        :label="false"
        ::id="option.uid"
        ::required="option.required"
        v-model="customOptions[option.option_id]"
    >
        <option v-for="dropdownValue in option.dropdownValue" :value="dropdownValue.option_type_id">
            @{{ dropdownValue.title }} @{{ dropdownValue | plus_price_type }}
        </option>
    </x-rapidez::select>
</template>
