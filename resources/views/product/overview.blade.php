@extends('rapidez::layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('description', $product->meta_description)

@section('content')
    <h1 class="font-bold text-4xl mb-5">{{ $product->name }}</h1>

    <div class="flex flex-col sm:flex-row mb-5">
        <div class="sm:w-2/3 sm:mr-5">
            <div class="{{ $product->images->count() == 1 ? '' : 'grid gap-3 grid-cols-2' }}">
                @forelse($product->images as $image)
                    <div class="flex items-center justify-center border rounded p-5 {{ $product->images->count() == 1 ? 'h-96' : 'h-48 sm:h-64 md:h-80 lg:h-96' }}">
                        <img
                            src="/storage/resizes/450/catalog/product{{ $image->value }}"
                            alt="{{ $product->name }}"
                            class="max-h-full max-w-full"
                            loading="lazy"
                        />
                    </div>
                @empty
                    <x-rapidez::no-image class="rounded h-64"/>
                @endforelse
            </div>
        </div>
        <div class="sm:w-1/3">
            <div class="p-3 my-5 sm:mt-0 bg-gray-200 rounded">
                {!! $product->description !!}
            </div>
            @include('rapidez::product.partials.addtocart')
        </div>
    </div>

    <dl class="flex flex-wrap w-full bg-gray-200 rounded p-3">
        <dt class="w-1/2 sm:w-1/4 font-bold">ID</dt>
        <dd class="w-1/2 sm:w-3/4">{{ $product->id }}</dd>
        <dt class="w-1/2 sm:w-1/4 font-bold">SKU</dt>
        <dd class="w-1/2 sm:w-3/4">{{ $product->sku }}</dd>
        @foreach(Rapidez\Core\Models\Attribute::getCachedWhere(fn ($a) => $a['productpage']) as $attribute)
            @if(($value = $product->{$attribute['code']}) && !is_object($value))
                <dt class="w-1/2 sm:w-1/4 font-bold">{{ $attribute['name'] }}</dt>
                <dd class="w-1/2 sm:w-3/4 prose prose-green max-w-none">
                    @php $output = is_array($value) ? implode(', ', $value) : $value @endphp
                    {!! $attribute['html'] ? $output : e($output) !!}
                </dd>
            @endif
        @endforeach
    </dl>
@endsection
