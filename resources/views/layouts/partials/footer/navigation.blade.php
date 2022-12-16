@php
    $items = [
        [
            'title' => 'Catalog',
            'children' => [
                [
                    'title' => 'What\'s New',
                    'url' => '/what-is-new.html',
                    'target_blank' => false
                ],
                [
                    'title' => 'Women',
                    'url' => '/women',
                    'target_blank' => false
                ],
                [
                    'title' => 'Men',
                    'url' => '/men',
                    'target_blank' => false
                ],
                [
                    'title' => 'Gear',
                    'url' => '/gear.html',
                    'target_blank' => false
                ],
                [
                    'title' => 'Training',
                    'url' => '/training.html',
                    'target_blank' => false
                ],
                [
                    'title' => 'Sale',
                    'url' => '/sale.html',
                    'target_blank' => false
                ]
            ]
        ],
        [
            'title' => 'Support',
            'children' => [
                [
                    'title' => 'Customer service',
                    'url' => '/customer-service',
                    'target_blank' => false
                ],
                [
                    'title' => 'Documentation',
                    'url' => 'https://docs.rapidez.io/',
                    'target_blank' => true
                ],
                [
                    'title' => 'FAQ',
                    'url' => 'https://rapidez.io/#faq',
                    'target_blank' => true
                ]
            ]
        ],
        [
            'title' => 'Legal',
            'children' => [
                [
                    'title' => 'Privacy',
                    'url' => '/privacy-policy-cookie-restriction-mode',
                    'target_blank' => false
                ],
                [
                    'title' => 'Cookies',
                    'url' => '/enable-cookies',
                    'target_blank' => false
                ]
            ]
        ],
        [
            'title' => 'Rapidez',
            'children' => [
                [
                    'title' => 'Website',
                    'url' => 'https://rapidez.io/',
                    'target_blank' => true
                ],
                [
                    'title' => 'GitHub',
                    'url' => 'https://github.com/rapidez',
                    'target_blank' => true
                ]
            ]
        ]
    ];
@endphp
<nav class="flex flex-wrap md:flex-nowrap justify-between w-full md:gap-8">
    @foreach($items as $item)
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
