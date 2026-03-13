import { test, expect } from '@playwright/test'
import { BasePage } from './pages/BasePage'
import { ProductPage } from './pages/ProductPage'
import { CheckoutPage } from './pages/CheckoutPage'
import { AccountPage } from './pages/AccountPage'

const checkoutTypes = ['default', 'onestep']

checkoutTypes.forEach((type) => {
    test(type + ' - as guest', BasePage.tags, async ({ page }) => {
        const productPage = new ProductPage(page)
        const checkoutPage = new CheckoutPage(page, type)

        await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
        await checkoutPage.checkout(`wayne+${crypto.randomUUID()}@enterprises.com`, false, false, [
            'login',
            'credentials',
            'payment',
            'success',
        ])
    })

    test(type + ' - as user', BasePage.tags, async ({ page }) => {
        const productPage = new ProductPage(page)
        const checkoutPage = new CheckoutPage(page, type)
        const accountPage = new AccountPage(page)

        const email = `wayne+${crypto.randomUUID()}@enterprises.com`
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

test('incorrect password login', BasePage.tags, async ({ page }) => {
    const productPage = new ProductPage(page)
    const checkoutPage = new CheckoutPage(page, 'default')
    const accountPage = new AccountPage(page)

    const email = `wayne+${crypto.randomUUID()}@enterprises.com`
    const password = 'IronManSucks.91939'

    // Register
    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.checkout(email, password, true, ['credentials'])

    await accountPage.logout()

    // Login
    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.gotoCheckout()
    await checkoutPage.login(email, password + '!')
    await this.page.getByTestId('continue').click()
    await this.page.waitForTimeout(500)
    await this.page.waitForLoadState('networkidle')
    await expect(page.getByTestId('notifications')).toHaveText(
        'The account sign-in was incorrect or your account is disabled temporarily. Please wait and try again later.',
    )

    await checkoutPage.checkout(email, password)
})
