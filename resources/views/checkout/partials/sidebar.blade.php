<div v-if="cart" class="p-3 border rounded">
    <table class="w-full my-3">
        <tr class="py-3" v-for="item in cart.items">
            <td>@{{ item.name }}</td>
            <td class="text-right font-mono px-4">@{{ item.qty }}</td>
            <td class="text-right font-mono">@{{ item.price | price }}</td>
        </tr>
        <tr class="py-3" v-for="total_segment in checkout.totals.total_segments" v-if="total_segment.value">
            <td>@{{ total_segment.title }}</td>
            <td></td>
            <td class="text-right font-mono">@{{ total_segment.value | price }}</td>
        </tr>
    </table>
</div>
