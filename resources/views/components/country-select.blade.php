<graphql query="{ countries { two_letter_abbreviation full_name_locale } }" cache="countries">
    <div v-if="data" slot-scope="{ data }">
        <x-rapidez::select {{ $attributes }} class="w-full py-2 px-3 border-gray-300 rounded focus:ring-green-500 focus:border-green-500 sm:text-sm">
            <option v-for="country in data.countries" :value="country.two_letter_abbreviation.toUpperCase()">
                @{{ country.full_name_locale }}
            </option>
        </x-rapidez::select>
    </div>
</graphql>
