@php
    $currentPage = max((int) request('page', 0), 0);
    $lastPage = max((int) ceil($total / max((int) $perPage, 1)) - 1, 0);
    $pageUrl = fn (int $page) => $page > 0 ? $url . '?' . http_build_query(['page' => $page]) : $url;
@endphp

@section('canonical', $pageUrl($currentPage))

@push('head')
    @if ($currentPage > 0)
        <link rel="prev" href="{{ $pageUrl($currentPage - 1) }}" />
    @endif

    @if ($currentPage < $lastPage)
        <link rel="next" href="{{ $pageUrl($currentPage + 1) }}" />
    @endif
@endpush
