@php
    $perPage = max((int) $perPage, 1);
    $currentPageIndex = max((int) request('page', 0), 0);
    $pageUrl = fn (int $page) => $page > 0 ? $url . '?' . http_build_query(['page' => $page]) : $url;
@endphp

@section('canonical', $pageUrl($currentPageIndex))
