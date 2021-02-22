<login v-slot="{ email, password, go, loginInputChange, emailAvailable }">
    <div class="flex justify-center">
        <form class="p-8 border rounded w-400px" v-on:submit.prevent="go()">
            <h1 class="font-bold text-4xl text-center mb-5">@lang('Checkout')</h1>

            <x-rapidez::input
                :label="false"
                name="email"
                type="email"
                v-bind:value="email"
                v-on:input="loginInputChange"
                required
            />
            <x-rapidez::input
                v-if="!emailAvailable"
                :label="false"
                class="mt-3"
                name="password"
                type="password"
                ref="password"
                v-on:input="loginInputChange"
                required
            />
            @include('rapidez::checkout.partials.buttons')
        </form>
    </div>
</login>
