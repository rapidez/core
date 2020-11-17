<div class="flex w-1/2 sm:w-1/3 lg:w-1/4 px-1 my-1" slot="renderItem" slot-scope="{ item }">
    <div class="w-full bg-white rounded hover:border hover:shadow group relative" :key="item.id">
        <a :href="item.url">
            <img v-if="item.thumbnail" :src="'/storage/resizes/200/catalog/product' + item.thumbnail" class="object-contain rounded-t h-48 w-full mb-3" />
            <x-rapidez::no-image v-else class="rounded-t h-48 mb-3"/>
            <div class="px-2">
                <div class="hyphens">@{{ item.name }}</div>
                <div class="font-semibold">@{{ item.formatted_price }}</div>
            </div>
        </a>
        <product-compare-checkbox
            class="absolute right-0 top-0 p-1 mt-3 hidden group-hover:flex items-center flex-row-reverse bg-gray-100 text-gray-500 rounded-l lowercase"
            :key="item.id"
            class-checkbox="ml-1"
        />
    </div>
</div>
