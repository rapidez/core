@extends('rapidez::layouts.app')

@section('title', $page->meta_title ?: $page->title)
@section('description', $page->meta_description)

@section('content')
    <div class="container">
        @if ($page->identifier != 'home' && $page->content_heading)
            <h1 class="mb-5 text-4xl font-bold">{{ $page->content_heading }}</h1>
        @endif
        @includeIf('pages.' . $page->identifier)
        <div class="hidden lg:block">
            @widget('content', 'pages', ($page->identifier == 'home' ? 'cms' : $page->identifier) . '_index_index')
        </div>
        @if ($page->content)
            <div class="prose prose-green mb-5">
                @content($page->content)
            </div>
        @endif
    </div>
@endsection
