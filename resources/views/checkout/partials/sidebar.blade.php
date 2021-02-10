<div v-if="cart" class="p-3 border rounded">
    <table class="mb-3">
        <tr class="py-3" v-for="item in cart.items">
            <td>@{{ item.name }}</td>
            <td class="text-right font-mono text-xs px-4">@{{ item.qty }}</td>
            <td class="text-right font-mono text-xs">@{{ item.price | price }}</td>
        </tr>
        <tr class="py-3" v-for="total_segment in checkout.totals.total_segments" v-if="total_segment.value">
            <td>@{{ total_segment }}</td>
            <td></td>
            <td class="text-right font-mono text-xs">@{{ total_segment.value | price }}</td>
        </tr>
    </table>
</div>
