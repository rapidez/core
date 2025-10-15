import { expect } from '@playwright/test'

export class CartPage {
    constructor(page) {
        this.page = page
    }

    async gotoCart() {
        await this.page.goto('/cart')
        await this.page.waitForLoadState('networkidle')
    }

    cartItem() {
        return this.page.getByTestId('cart-item')
    }

    cartItemQty() {
        return this.cartItem().getByTestId('qty')
    }

    async firstItemIs(product) {
        await this.gotoCart()
        await expect(this.cartItem()).toContainText(product.name)
    }

    async firstItemQtyIs(qty) {
        await this.gotoCart()
        await expect(this.cartItemQty()).toHaveValue(qty.toString())
    }
}
