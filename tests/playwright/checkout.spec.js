import { test, expect } from '@playwright/test'
import { BasePage } from './pages/BasePage'
import { ProductPage } from './pages/ProductPage'
import { CheckoutPage, screenshot_login, screenshot_credentials, screenshot_payment, screenshot_success } from './pages/CheckoutPage'
import { AccountPage } from './pages/AccountPage'

const checkoutTypes = ['default', 'onestep']

checkoutTypes.forEach((type) => {
    test(type + ' - as guest', BasePage.tags, async ({ page }) => {
        const productPage = new ProductPage(page)
        const checkoutPage = new CheckoutPage(page, type, [
            screenshot_login,
            screenshot_credentials,
            screenshot_payment,
            screenshot_success,
        ])

        await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
        await checkoutPage.checkout(`wayne+${crypto.randomUUID()}@enterprises.com`, false, false)
    })

    test(type + ' - as user', BasePage.tags, async ({ page }) => {
        const productPage = new ProductPage(page)
        const checkoutPage = new CheckoutPage(page, type, [screenshot_login, screenshot_credentials])
        const accountPage = new AccountPage(page)

        const email = `wayne+${crypto.randomUUID()}@enterprises.com`
        const password = 'IronManSucks.91939'

        // Register
        await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
        await checkoutPage.checkout(email, password, true)

        await accountPage.logout()

        checkoutPage.screenshots = []
        // Login
        await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
        await checkoutPage.checkout(email, password)
    })
})

test('incorrect password login', BasePage.tags, async ({ page }) => {
    const productPage = new ProductPage(page)
    const checkoutPage = new CheckoutPage(page, 'default', [screenshot_credentials])
    const accountPage = new AccountPage(page)

    const email = `wayne+${crypto.randomUUID()}@enterprises.com`
    const password = 'IronManSucks.91939'

    // Register
    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.checkout(email, password, true)

    await accountPage.logout()

    checkoutPage.screenshots = []
    // Login
    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.gotoCheckout()
    await checkoutPage.login(email, password + '!')
    await page.getByTestId('continue').click()
    await page.waitForTimeout(500)
    await page.waitForLoadState('networkidle')
    await expect(page.getByTestId('notifications')).toHaveText(
        'The account sign-in was incorrect or your account is disabled temporarily. Please wait and try again later.',
    )

    await checkoutPage.checkout(email, password)
})
