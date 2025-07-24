import { test, expect } from '@playwright/test'

test('product simple', async ({ page }) => {
    await page.goto(process.env.PRODUCT_URL_SIMPLE)
    await expect(page).toHaveScreenshot({ fullPage: true })
})

test('product configurable', async ({ page }) => {
    await page.goto(process.env.PRODUCT_URL_CONFIGURABLE)
    await expect(page).toHaveScreenshot({ fullPage: true })
})

test('product grouped', async ({ page }) => {
    await page.goto(process.env.PRODUCT_URL_GROUPED)
    await expect(page).toHaveScreenshot({ fullPage: true })
})
