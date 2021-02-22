<div class="flex {{ $class ?? 'justify-between'}}">
    <button
        class="btn btn-primary mt-5 mr-5"
        :disabled="$root.loading"
        dusk="back"
        v-on:click.prevent="goToStep(checkout.step - 1)"
    >
        @lang('Back')
    </button>
    <button
    type="submit"
    class="btn btn-primary mt-5"
    :disabled="$root.loading"
    dusk="continue"
    >
        @lang($buttonText ?? 'Continue')
    </button>
</div>
