<reactive-component component-id="category" v-if="config.category" :show-filter="false">
    <div slot-scope="{ setQuery }">
        <category-filter :set-query="setQuery"></category-filter>
    </div>
</reactive-component>
