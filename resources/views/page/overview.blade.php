@extends('rapidez::layouts.app')

@section('title', $page->meta_title ?: $page->title)
@section('description', $page->meta_description)

@section('content')
    <div class="container">
        @if ($page->identifier != 'home' && $page->content_heading)
            <x-rapidez::title tag="h1" class="mb-5 text-4xl">{{ $page->content_heading }}</x-rapidez::title>
        @endif
        @includeIf('pages.' . $page->identifier)
        @if ($page->content)
            <div class="prose prose-green mb-5">
                @content($page->content)
            </div>
        @endif
    </div>
@endsection
