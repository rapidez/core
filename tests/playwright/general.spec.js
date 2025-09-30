import { test, expect } from '@playwright/test'
import { BasePage } from './pages/BasePage'

test('cookie', async ({ page }) => {
    const acceptCookiesButton = page.getByTestId('accept-cookies')

    await page.goto('/?show-cookie-notice')
    await new BasePage(page).screenshot()
    await acceptCookiesButton.click()
    await expect(acceptCookiesButton).not.toBeVisible()

    const cookieNotice = await page.evaluate(() => {
        return window.localStorage.getItem('cookie-notice')
    })

    await expect(cookieNotice).not.toBeNull()
})

test('newsletter', async ({ page }) => {
    const email = `wayne+${Date.now()}@enterprises.com`

    await page.goto('/')
    await new BasePage(page).scrolldown()

    await page.getByTestId('newsletter-email').fill(email)
    await page.getByTestId('newsletter-submit').click()
    await expect(page.getByTestId('newsletter-success')).toBeVisible()

    await page.getByTestId('newsletter-email').fill(email)
    await page.getByTestId('newsletter-submit').click()
    await expect(page.getByTestId('newsletter-error')).toBeVisible()
})
