<reactive-component component-id="searchterm">
    <div slot-scope="{ setQuery }">
        <search-term-filter :set-query="setQuery" term="{{ request('q') }}"></search-term-filter>
    </div>
</reactive-component>
