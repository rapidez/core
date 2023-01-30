<div v-if="cart" class="p-3 border rounded">
    <div class="flex flex-col w-full">
        <div class="flex gap-x-1 py-3 border-b" v-for="item in cart.items">
            <div class="w-7/12">@{{ item.name }}</div>
            <div class="w-2/12 text-right font-mono px-4">@{{ item.qty }}</div>
            <div class="w-3/12 text-right font-mono">@{{ item.price | price }}</div>
        </div>
        <div class="flex gap-x-1 py-3 border-b last:font-bold last:border-b-0" v-for="total_segment in checkout.totals.total_segments" v-if="total_segment.value">
            <div class="w-7/12">@{{ total_segment.title }}</div>
            <div class="w-2/12 px-4"></div>
            <div class="w-3/12 text-right font-mono">@{{ total_segment.value | price }}</div>
        </div>
    </div>
</div>
