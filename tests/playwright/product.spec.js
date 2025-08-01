import { test, expect } from '@playwright/test'
import { BasePage } from './pages/BasePage'

test('product simple', async ({ page }) => {
    await page.goto(process.env.PRODUCT_URL_SIMPLE)
    await new BasePage(page).screenshot('fullpage-footer')
})

test('product configurable', async ({ page }) => {
    await page.goto(process.env.PRODUCT_URL_CONFIGURABLE)
    await new BasePage(page).screenshot('fullpage-footer')
})

test('product grouped', async ({ page }) => {
    await page.goto(process.env.PRODUCT_URL_GROUPED)
    await new BasePage(page).screenshot('fullpage-footer')
})
