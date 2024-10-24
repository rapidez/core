{{-- When there are no results in the search autocomplete this will be shown. --}}
<div class="font-bold text-neutral text-lg">
    @lang('No results found for :searchterm', ['searchterm' => '<span class="text-primary">"@{{ value }}"</span>'])
</div>
<div class="flex flex-col text-sm pt-7">
    <span class="font-bold">@lang('Have you tried:')</span>
    <ul class="flex flex-col pt-1.5 gap-y-1 *:flex *:gap-x-2 *:items-center">
        <li>
            <x-heroicon-s-check class="size-4"/>
            @lang('Check the spelling of your search term')
        </li>
        <li>
            <x-heroicon-s-check class="size-4"/>
            @lang('Make your search term less specific')
        </li>
        <li>
            <x-heroicon-s-check class="size-4"/>
            @lang('Use other search terms')
        </li>
    </ul>
</div>
