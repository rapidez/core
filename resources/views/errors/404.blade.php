@extends('rapidez::layouts.app')
@php
$page = config('rapidez.models.page')::firstWhere('identifier', 'no-route')
@endphp

@section('title', $page->meta_title ?: $page->title)
@section('description', $page->meta_description)

@section('content')
    <div class="container">
        <h1 class="font-bold text-4xl mb-5">{{ $page->content_heading }}</h1>
        <div class="mb-5 prose prose-green">
            {!! $page->content !!}
        </div>
    </div>
@endsection
