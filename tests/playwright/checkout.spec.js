import { test, expect } from '@playwright/test'
import { ProductPage } from './pages/ProductPage'
import { CheckoutPage, screenshot_login, screenshot_credentials, screenshot_payment, screenshot_success } from './pages/CheckoutPage'
import { AccountPage } from './pages/AccountPage'
import { BasePage } from './pages/BasePage'

const checkoutTypes = ['default', 'onestep']

checkoutTypes.forEach((type) => {
    test(type + ' - as guest', async ({ page }) => {
        const productPage = new ProductPage(page)
        const checkoutPage = new CheckoutPage(page, type, [
            screenshot_login,
            screenshot_credentials,
            screenshot_payment,
            screenshot_success,
        ])

        await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
        await checkoutPage.checkout(`${crypto.randomUUID()}@enterprises.com`, false, false)
    })

    test(type + ' - as user', async ({ page }) => {
        const productPage = new ProductPage(page)
        const checkoutPage = new CheckoutPage(page, type, [screenshot_login, screenshot_credentials])
        const accountPage = new AccountPage(page)

        const email = `${crypto.randomUUID()}@enterprises.com`
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

test('different billing address', async ({ page }) => {
    const productPage = new ProductPage(page)
    const checkoutPage = new CheckoutPage(page)

    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.gotoCheckout()
    await checkoutPage.login(`${crypto.randomUUID()}@enterprises.com`)
    await checkoutPage.continue('credentials')
    await checkoutPage.shippingAddress()

    // Only the country differs, it should still be a separate billing address.
    // Luxembourg has no regions in Magento, so no region is required.
    await page.getByTestId('same-as-shipping').uncheck()
    await new BasePage(page).waitUntilIdle()
    await page.selectOption('[name=billing_country]', 'LU')
    await page.getByTestId('continue').focus()
    await new BasePage(page).waitUntilIdle()

    await page.reload()
    await new BasePage(page).waitUntilIdle()
    await expect(page.getByTestId('same-as-shipping')).not.toBeChecked()
    await expect(page.locator('[name=billing_country]')).toHaveValue('LU')

    // Changing the shipping address should not overwrite the billing address.
    await page.fill('[name=shipping_city]', 'Metropolis')
    await page.getByTestId('continue').focus()
    await new BasePage(page).waitUntilIdle()

    await page.reload()
    await new BasePage(page).waitUntilIdle()
    await expect(page.locator('[name=shipping_city]')).toHaveValue('Metropolis')
    await expect(page.getByTestId('same-as-shipping')).not.toBeChecked()
    await expect(page.locator('[name=billing_country]')).toHaveValue('LU')

    await checkoutPage.shippingMethod()
    await checkoutPage.continue('payment')
    await checkoutPage.paymentMethod()
    await checkoutPage.continue('success')
    await checkoutPage.success()
    await expect(page.getByTestId('checkout-success')).toContainText('Gotham - LU')
    await expect(page.getByTestId('checkout-success')).toContainText('Metropolis - NL')
})

test('same billing address after unchecking', async ({ page }) => {
    const productPage = new ProductPage(page)
    const checkoutPage = new CheckoutPage(page)

    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.gotoCheckout()
    await checkoutPage.login(`${crypto.randomUUID()}@enterprises.com`)
    await checkoutPage.continue('credentials')
    await checkoutPage.shippingAddress()

    await page.getByTestId('same-as-shipping').uncheck()
    await new BasePage(page).waitUntilIdle()
    await page.selectOption('[name=billing_country]', 'LU')
    await page.getByTestId('continue').focus()
    await new BasePage(page).waitUntilIdle()

    await page.getByTestId('same-as-shipping').check()
    await new BasePage(page).waitUntilIdle()

    await page.reload()
    await new BasePage(page).waitUntilIdle()
    await expect(page.getByTestId('same-as-shipping')).toBeChecked()
})

test('same billing address after shipping method change', async ({ page }) => {
    const productPage = new ProductPage(page)
    const checkoutPage = new CheckoutPage(page)

    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.gotoCheckout()
    await checkoutPage.login(`${crypto.randomUUID()}@enterprises.com`)
    await checkoutPage.continue('credentials')
    await checkoutPage.shippingAddress()

    // Changing the shipping method saves the shipping address again,
    // which resets same_as_billing within Magento.
    await page.getByTestId('shipping-method').last().click()
    await new BasePage(page).waitUntilIdle()

    await page.reload()
    await new BasePage(page).waitUntilIdle()
    await expect(page.getByTestId('same-as-shipping')).toBeChecked()
})

test('new address as user', async ({ page }) => {
    const productPage = new ProductPage(page)
    const checkoutPage = new CheckoutPage(page)
    const accountPage = new AccountPage(page)

    const email = `${crypto.randomUUID()}@enterprises.com`
    const password = 'IronManSucks.91939'

    // Register, this saves the address in the address book
    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.checkout(email, password, true)

    await accountPage.logout()

    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.gotoCheckout()
    await checkoutPage.login(email, password)
    await checkoutPage.continue('credentials')

    // Select the address book address first, so the new address starts with its data.
    await checkoutPage.shippingAddress()

    // Only the country differs from the address book address, it should stay a new address.
    const addressSelect = page.getByTestId('shipping-address-select')
    await addressSelect.selectOption({ index: (await addressSelect.locator('option').count()) - 1 })
    await new BasePage(page).waitUntilIdle()
    await expect(page.locator('[name=shipping_city]')).toHaveValue('Gotham')
    await page.selectOption('[name=shipping_country]', 'LU')
    await page.getByTestId('continue').focus()
    await new BasePage(page).waitUntilIdle()

    await page.reload()
    await new BasePage(page).waitUntilIdle()
    await expect(page.locator('[name=shipping_country]')).toHaveValue('LU')

    await checkoutPage.shippingMethod()
    await checkoutPage.continue('payment')
    await checkoutPage.paymentMethod()
    await checkoutPage.continue('success')
    await checkoutPage.success()
    await expect(page.getByTestId('checkout-success')).toContainText('Gotham - LU')
})

test('same default shipping and billing address as user', async ({ page }) => {
    const productPage = new ProductPage(page)
    const checkoutPage = new CheckoutPage(page)
    const accountPage = new AccountPage(page)

    const email = `${crypto.randomUUID()}@enterprises.com`
    const password = 'IronManSucks.91939'

    // Register, this saves the address in the address book
    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.checkout(email, password, true)

    // Make it the default shipping and billing address,
    // these are set on the cart when it's linked to the customer.
    await page.evaluate(async () => {
        const response = await window.magentoGraphQL('{ customer { addresses { id } } }')
        const id = response.data.customer.addresses[0].id
        await window.magentoGraphQL(
            `mutation { updateCustomerAddress(id: ${id}, input: { default_shipping: true, default_billing: true }) { id } }`,
        )
    })

    await accountPage.logout()

    await productPage.addToCart(process.env.PRODUCT_URL_SIMPLE)
    await checkoutPage.gotoCheckout()
    await checkoutPage.login(email, password)
    await checkoutPage.continue('credentials')

    await page.reload()
    await new BasePage(page).waitUntilIdle()
    await expect(page.getByTestId('same-as-shipping')).toBeChecked()
})

test('incorrect password login', async ({ page }) => {
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
    await expect(page.getByTestId('notifications')).toContainText(
        'The account sign-in was incorrect or your account is disabled temporarily. Please wait and try again later.',
    )

    await checkoutPage.checkout(email, password)
})
