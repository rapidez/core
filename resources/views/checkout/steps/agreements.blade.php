@php($checkoutAgreements ??= \Rapidez\Core\Models\CheckoutAgreement::all())
@if (!$checkoutAgreements->count())
    @return
@endif

@foreach ($checkoutAgreements as $agreement)
    <x-rapidez::slideover position="right" id="{{ $agreement->agreement_id }}_agreement">
        <x-slot name="title">
            {{ $agreement->name }}
        </x-slot>

        @if ($agreement->is_html)
            <div class="p-3">
                {!! $agreement->content !!}
            </div>
        @else
            <div class="whitespace-pre-line p-3">
                {{ $agreement->content }}
            </div>
        @endif
    </x-rapidez::slideover>

    @if ($agreement->mode == 'AUTO')
        <label class="cursor-pointer underline hover:no-underline" for="{{ $agreement->agreement_id }}_agreement">
            {{ $agreement->checkbox_text }}
        </label>
    @else
        <div>
            <x-rapidez::checkbox
                name="agreement_ids[]"
                :value="$agreement->agreement_id"
                dusk="agreements"
                required
            >
                <label class="cursor-pointer underline hover:no-underline" for="{{ $agreement->agreement_id }}_agreement">
                    {{ $agreement->checkbox_text }}
                </label>
            </x-rapidez::checkbox>
        </div>
    @endif
@endforeach
