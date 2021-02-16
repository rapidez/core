@extends('rapidez::layouts.app')

@section('title', $page->meta_title ?: $page->title)
@section('description', $page->meta_description)

@section('content')
    <div class="container mx-auto">
        <h1 class="font-bold text-4xl mb-5">{{ $page->content_heading }}</h1>
        @widget('content', 'pages', ($page->identifier == 'home' ? 'cms' : $page->identifier).'_index_index')
        @includeIf('pages.'.$page->identifier)
        @if($page->content)
            <div class="mb-5 prose prose-green">
                {!! $page->content !!}
            </div>
        @endif
    </div>
@endsection
