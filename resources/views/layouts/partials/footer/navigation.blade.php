<nav class="flex flex-wrap md:flex-nowrap justify-between w-full md:gap-8">
    @foreach(config('rapidez.footer_navigation') as $item)
        <ul role="list" class="w-1/2 md:w-1/4 mt-4 md:mt-0 space-y-4">
            <p class="text-base font-medium text-gray-900">{{ $item['title'] }}</p>
            @foreach($item['children'] as $child)
                <li>
                    <a href="{{ $child['url'] }}" {{ $child['target_blank'] ? 'target="_blank"' : '' }} class="text-base text-gray-500 hover:text-gray-900">
                        {{ $child['title'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endforeach
</nav>
