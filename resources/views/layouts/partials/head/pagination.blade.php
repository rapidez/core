@php
    $currentPage = max((int) request('page', 1), 1);
    $lastPage = max(ceil($total / $perPage), 1);
@endphp

@section('canonical', $currentPage === 1 ? $url : $url . '?page=' . $currentPage)

@push('head')
    @if ($currentPage > 1)
        <link rel="prev" href="{{ $url . '?page=' . ($currentPage - 1) }}" />
    @endif

    @if ($currentPage < $lastPage)
        <link rel="next" href="{{ $url . '?page=' . ($currentPage + 1) }}" />
    @endif
@endpush
