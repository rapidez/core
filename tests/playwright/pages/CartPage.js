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

    async setFirstItemQty(qty) {
        const cartItemQty = this.cartItemQty()

        await expect(cartItemQty).toBeVisible()
        await cartItemQty.fill(qty + '')
        await cartItemQty.press('Tab')
        await this.page.waitForLoadState('networkidle')
    }

    async firstItemQtyIs(qty) {
        await this.gotoCart()
        await expect(this.cartItemQty()).toHaveValue(qty.toString())
    }

    async removeFirstItem() {
        const removeButton = this.cartItem().getByTestId('cart-item-remove')

        await expect(removeButton).toBeVisible()
        await removeButton.click()
        await this.page.waitForLoadState('networkidle')
    }
}
