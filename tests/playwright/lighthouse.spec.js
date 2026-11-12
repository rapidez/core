import { test } from '@playwright/test'
import { BasePage } from './pages/BasePage'

test('homepage', BasePage.tags, async ({ page }) => {
    await new BasePage(page).lighthouse('/')
})

test('product', BasePage.tags, async ({ page }) => {
    await new BasePage(page).lighthouse(process.env.PRODUCT_URL_SIMPLE)
})
