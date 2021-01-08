@extends('rapidez::layouts.app')

@section('title', $page->meta_title ?: $page->title)
@section('description', $page->meta_description)

@section('content')
    <div class="container mx-auto">
        <h1 class="font-bold text-4xl">{{ $page->content_heading }}</h1>
        <div class="mb-5">
            @widget('content', 'pages', ($page->identifier == 'home' ? 'cms' : $page->identifier).'_index_index')
            {!! $page->content !!}
        </div>
    </div>
@endsection
