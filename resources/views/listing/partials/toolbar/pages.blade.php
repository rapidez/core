<ais-hits-per-page :items="hitsPerPage">
    <template v-slot="{ items, refine }">
        <select
            v-bind:value="items.find(item => item.isRefined)?.value"
            v-on:change="event => refine(event.target.value)"
            class="py-0 pr-5 border-none text text-sm focus:outline-none focus:ring-0 focus:ring-offset-0 custom-select"
            aria-label="{{ __('Items per page') }}"
        >
            <option
                v-for="item in items"
                v-bind:key="item.value"
                v-bind:value="item.value"
            >
                @{{ item.label }}
            </option>
        </select>
    </template>
</ais-hits-per-page>
