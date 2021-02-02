@extends('rapidez::layouts.app')
@php
$page = \Rapidez\Core\Models\Page::firstWhere('identifier', 'no-route')
@endphp

@section('title', $page->meta_title ?: $page->title)
@section('description', $page->meta_description)

@section('content')
    <div class="container mx-auto">
        <h1 class="font-bold text-4xl mb-5">{{ $page->content_heading }}</h1>
        <div class="mb-5 prose prose-green">
            {!! $page->content !!}
        </div>
    </div>
@endsection
