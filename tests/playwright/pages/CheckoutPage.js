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

        if (password && !register) {
            await this.page.fill('[name=password]', password)
        }

        if (password && register) {
            await this.page.getByTestId('create-account').click()
            await this.page.fill('[name=password]', password)
            await this.page.fill('[name=password_repeat]', password)
            await this.page.fill('[name=firstname]', 'Bruce')
            await this.page.fill('[name=lastname]', 'Wayne')
        }
    }

    async shippingAddress() {
        // TODO: Remove this timeout!
        await this.page.waitForTimeout(500)

        const addressSelect = this.page.getByTestId('shipping-address-select')
        if (await addressSelect.isVisible()) {
            const addressCount = await addressSelect.locator('option').count()
            console.log(addressCount)

            if (addressCount > 1) {
                await addressSelect.selectOption({ index: 0 })
                return
            }
        }

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
        // TODO: Remove this timeout!
        await this.page.waitForTimeout(500)
    }

    async shippingMethod() {
        await this.page.getByTestId('shipping-method').first().click()
        await this.page.waitForLoadState('networkidle')
        // TODO: Remove this timeout!
        await this.page.waitForTimeout(500)
    }

    async paymentMethod() {
        await this.page.getByTestId('payment-method').first().click()
        await this.page.waitForLoadState('networkidle')
        // TODO: Remove this timeout!
        await this.page.waitForTimeout(500)
    }

    async success() {
        await expect(this.page.getByTestId('checkout-success')).toBeVisible()
    }

    async continue(expectedStep) {
        await this.page.getByTestId('continue').click()
        await this.page.waitForTimeout(100)
        await this.page.waitForLoadState('networkidle')
        await this.page.waitForURL('**/' + expectedStep)
        if (expectedStep != 'success') {
            await this.continueButtonVisible()
        }
    }

    async continueButtonVisible() {
        await this.page.getByTestId('continue').waitFor({ state: 'visible' })
    }

    async checkout(email, password = false, register = false, screenshots = []) {
        await this.gotoCheckout()

        if (screenshots.includes('login')) {
            await expect(this.page).toHaveScreenshot({ fullPage: true })
        }

        await this.login(email, password, register)
        await this.continue('credentials')

        if (screenshots.includes('credentials')) {
            await expect(this.page).toHaveScreenshot({ fullPage: true })
        }

        await this.shippingAddress()
        await this.shippingMethod()
        await this.continue('payment')

        if (screenshots.includes('payment')) {
            await expect(this.page).toHaveScreenshot({ fullPage: true })
        }

        await this.paymentMethod()
        await this.continue('success')
        await this.success()

        if (screenshots.includes('success')) {
            await expect(this.page).toHaveScreenshot({ fullPage: true })
        }
    }
}
