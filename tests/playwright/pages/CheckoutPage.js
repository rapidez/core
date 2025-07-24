import { expect } from '@playwright/test'

export class CheckoutPage {
    constructor(page) {
        this.page = page
    }

    async gotoCheckout() {
        await this.page.goto('/checkout')
    }

    async login(email, password = false, register = false) {
        await this.page.fill('[name=email]', email)
        await this.page.waitForLoadState('networkidle')
    }

    async shippingAddress() {
        await this.page.fill('[name=shipping_firstname]', 'Bruce' + Date.now())
        await this.page.fill('[name=shipping_lastname]', 'Wayne')
        await this.page.fill('[name=shipping_postcode]', '72000')
        await this.page.fill('[name=shipping_housenumber]', '1007')
        await this.page.fill('[name=shipping_street]', 'Mountain Drive')
        await this.page.fill('[name=shipping_city]', 'Gotham')
        await this.page.selectOption('[name=shipping_country]', 'NL')
        await this.page.fill('[name=shipping_telephone]', '530-7972')
        await this.page.locator('input[name=shipping_telephone]').press('Tab')
        await this.page.waitForLoadState('networkidle')
    }

    async shippingMethod() {
        await this.page.getByTestId('shipping-method').first().click()
        await this.page.waitForLoadState('networkidle')
    }

    async paymentMethod() {
        await this.page.getByTestId('payment-method').first().click()
        await this.page.waitForLoadState('networkidle')
        await this.page.waitForTimeout(3000)
    }

    async success() {
        await expect(this.page.getByTestId('checkout-success')).toBeVisible()
    }

    async continue() {
        await this.page.getByTestId('continue').click()
        await this.page.waitForLoadState('networkidle')
    }
}
