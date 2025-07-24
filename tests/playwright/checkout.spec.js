import { test, expect } from '@playwright/test'
import { ProductPage } from './pages/ProductPage'
import { CheckoutPage } from './pages/CheckoutPage'

test('as guest', async ({ page }) => {
    const product = await new ProductPage(page).addToCart(process.env.PRODUCT_URL_SIMPLE)
    const checkoutPage = new CheckoutPage(page)

    await checkoutPage.gotoCheckout()
    await expect(page).toHaveScreenshot({ fullPage: true })
    await checkoutPage.login(`wayne+${ Date.now() }@enterprises.com`)
    await checkoutPage.continue()
    await expect(page).toHaveScreenshot({ fullPage: true })
    await checkoutPage.shippingAddress()
    await checkoutPage.shippingMethod()
    await checkoutPage.continue()
    await expect(page).toHaveScreenshot({ fullPage: true })
    await checkoutPage.paymentMethod()
    await checkoutPage.continue()
    await checkoutPage.success()
    await expect(page).toHaveScreenshot({ fullPage: true })
})

// TODO:
// as user
// with onestep
