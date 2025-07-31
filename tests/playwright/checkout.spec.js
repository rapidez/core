import { test, expect } from '@playwright/test'
import { ProductPage } from './pages/ProductPage'
import { CheckoutPage } from './pages/CheckoutPage'
import { AccountPage } from './pages/AccountPage'

const checkoutTypes = ['default', 'onestep']

checkoutTypes.forEach((type) => {
    test(type + '- as guest', async ({ page }) => {
        const productPage = new ProductPage(page)
        const checkoutPage = new CheckoutPage(page, type)

        await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
        await checkoutPage.checkout(`wayne+${Date.now() + Math.random()}@enterprises.com`, false, false, [
            'login',
            'credentials',
            'payment',
            'success',
        ])
    })

    test(type + ' - as user', async ({ page }) => {
        const productPage = new ProductPage(page)
        const checkoutPage = new CheckoutPage(page, type)
        const accountPage = new AccountPage(page)

        const email = `wayne+${Date.now() + Math.random()}@enterprises.com`
        const password = 'IronManSucks.91939'

        // Register
        await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
        await checkoutPage.checkout(email, password, true, ['credentials'])

        await accountPage.logout()

        // Login
        await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
        await checkoutPage.checkout(email, password)
    })
})
