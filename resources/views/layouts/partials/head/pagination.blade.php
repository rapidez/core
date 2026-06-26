@php
    $perPage = max((int) $perPage, 1);
    $currentPageIndex = max((int) request('page', 0), 0);
    $lastPageIndex = max((int) ceil($total / $perPage) - 1, 0);
    $pageUrl = fn (int $page) => $page > 0 ? $url . '?' . http_build_query(['page' => $page]) : $url;
@endphp

@section('canonical', $pageUrl($currentPageIndex))

@push('head')
    @if ($currentPageIndex > 0)
        <link rel="prev" href="{{ $pageUrl($currentPageIndex - 1) }}" />
    @endif

    @if ($currentPageIndex < $lastPageIndex)
        <link rel="next" href="{{ $pageUrl($currentPageIndex + 1) }}" />
    @endif
@endpush
