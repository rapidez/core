import { test, expect } from '@playwright/test'
import { ProductPage } from './pages/ProductPage'
import { CheckoutPage } from './pages/CheckoutPage'
import { AccountPage } from './pages/AccountPage'

test('as guest', async ({ page }) => {
    const productPage = new ProductPage(page)
    const checkoutPage = new CheckoutPage(page)

    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.checkout(`wayne+${Date.now()}@enterprises.com`, false, false, ['login', 'credentials', 'payment', 'success'])
})

test('as user', async ({ page }) => {
    const productPage = new ProductPage(page)
    const checkoutPage = new CheckoutPage(page)
    const accountPage = new AccountPage(page)

    const email = `wayne+${Date.now()}@enterprises.com`
    const password = 'IronManSucks.91939'

    // Register
    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.checkout(email, password, true, ['credentials'])

    await accountPage.logout()

    // Login
    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.checkout(email, password)
})
