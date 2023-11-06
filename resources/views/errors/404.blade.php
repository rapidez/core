@extends('rapidez::layouts.app')
@php
    $page = config('rapidez.models.page')::firstWhere('identifier', 'no-route');
@endphp

@section('title', $page->meta_title ?: $page->title)
@section('description', $page->meta_description)

@section('content')
    <div class="container">
        <h1 class="mb-5 text-4xl font-bold">{{ $page->content_heading }}</h1>
        <div class="prose prose-green mb-5">
            {!! $page->content !!}
        </div>
    </div>
@endsection
