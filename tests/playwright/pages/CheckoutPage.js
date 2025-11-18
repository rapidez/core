import { expect } from '@playwright/test'
import { BasePage } from './BasePage'

export class CheckoutPage {
    constructor(page, type = 'default') {
        this.page = page
        this.type = type
    }

    async gotoCheckout() {
        const url = this.type === 'onestep' ? '/checkout?checkout=onestep' : '/checkout'

        await this.page.goto(url)
    }

    async login(email, password = false, register = false) {
        await this.page.fill('[name=email]', email)
        await this.page.locator('[name=email]').dispatchEvent('change')
        await this.page.waitForLoadState('networkidle')

        if (password && !register) {
            await new BasePage(this.page).screenshot('fullpage')
            await this.page.fill('[name=password]', password)
        }

        if (password && register) {
            await this.page.getByTestId('create-account').click()
            await new BasePage(this.page).screenshot('fullpage')
            await this.page.fill('[name=password]', password)
            await this.page.fill('[name=password_repeat]', password)
            await this.page.fill('[name=firstname]', 'Bruce')
            await this.page.fill('[name=lastname]', 'Wayne')
        }
    }

    async shippingAddress() {
        await this.page.waitForLoadState('networkidle')

        const addressSelect = this.page.getByTestId('shipping-address-select')
        if (await addressSelect.isVisible()) {
            const addressCount = await addressSelect.locator('option').count()

            if (addressCount > 1) {
                await addressSelect.selectOption({ index: 0 })
                await addressSelect.dispatchEvent('change')
                await this.page.waitForTimeout(200)
                await this.page.waitForLoadState('networkidle')
                return
            }
        }

        await this.page.fill('[name=shipping_firstname]', 'Bruce')
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
        await this.page.waitForTimeout(200)
        await this.page.waitForLoadState('networkidle')
    }

    async paymentMethod() {
        await this.page.getByTestId('payment-method').first().click()
        await this.page.waitForTimeout(200)
        await this.page.waitForLoadState('networkidle')
    }

    async success() {
        await expect(this.page.getByTestId('checkout-success')).toBeVisible()
    }

    async continue(expectedStep) {
        await this.page.getByTestId('continue').click()
        await this.page.waitForTimeout(500)
        await this.page.waitForLoadState('networkidle')
        await this.page.waitForURL('**/' + expectedStep)
        if (expectedStep != 'success') {
            await this.continueButtonVisible()
        }
        await this.page.waitForTimeout(500)
    }

    async continueButtonVisible() {
        await this.page.getByTestId('continue').waitFor({ state: 'visible' })
    }

    async checkout(email, password = false, register = false, screenshots = []) {
        const method = this.type === 'onestep' ? 'checkoutOnestep' : 'checkoutDefault'

        await this[method](email, password, register, screenshots)
    }

    async checkoutDefault(email = false, password = false, register = false, screenshots = []) {
        await this.gotoCheckout()

        if (screenshots.includes('login')) {
            await new BasePage(this.page).screenshot('fullpage')
        }

        if (email) {
            await this.login(email, password, register)
            await this.continue('credentials')
        }

        if (screenshots.includes('credentials')) {
            await new BasePage(this.page).screenshot('fullpage')
        }

        await this.shippingAddress()
        await this.shippingMethod()
        await this.continue('payment')

        if (screenshots.includes('payment')) {
            await new BasePage(this.page).screenshot('fullpage')
        }

        await this.paymentMethod()
        await this.continue('success')
        await this.success()

        if (screenshots.includes('success')) {
            await new BasePage(this.page).screenshot('fullpage-footer')
        }
    }

    async checkoutOnestep(email = false, password = false, register = false, screenshots = []) {
        await this.gotoCheckout()

        if (screenshots.includes('login')) {
            await new BasePage(this.page).screenshot('fullpage')
        }

        if (email) {
            await this.login(email, password, register)
            if(password)
            await this.page.getByTestId('login').click()
            await this.page.waitForTimeout(500)
            await this.page.waitForLoadState('networkidle')
        }

        await this.shippingAddress()
        await this.shippingMethod()
        await this.paymentMethod()

        if (screenshots.includes('payment')) {
            await new BasePage(this.page).screenshot('fullpage')
        }

        await this.continue('success')
        await this.success()

        if (screenshots.includes('success')) {
            await new BasePage(this.page).screenshot('fullpage-footer')
        }
    }
}
