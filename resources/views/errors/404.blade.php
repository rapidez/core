@extends('rapidez::layouts.app')
@php
    $page = config('rapidez.models.page')::firstWhere('identifier', 'no-route');
@endphp

@section('title', $page->meta_title ?: $page->title)
@section('description', $page->meta_description)

@push('head')
    <script>
        document.addEventListener('turbo:load', function() {
            window.Turbo.session.drive = false;
        });
    </script>
@endpush

@section('content')
    <div class="container">
        <x-rapidez::title tag="h1" class="mb-5 text-4xl">{{ $page->content_heading }}</x-rapidez::title>
        <div class="prose prose-green mb-5">
            {!! $page->content !!}
        </div>
    </div>
@endsection
