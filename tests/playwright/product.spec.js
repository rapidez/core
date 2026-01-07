import { test, expect } from '@playwright/test'
import { BasePage } from './pages/BasePage'

test('product simple', BasePage.tags, async ({ page }) => {
    await page.goto(process.env.PRODUCT_URL_SIMPLE)
    await new BasePage(page).screenshot('fullpage-footer')
})

test('product configurable', BasePage.tags, async ({ page }) => {
    await page.goto(process.env.PRODUCT_URL_CONFIGURABLE)
    await new BasePage(page).screenshot('fullpage-footer')
})

test('product grouped', BasePage.tags, async ({ page }) => {
    await page.goto(process.env.PRODUCT_URL_GROUPED)
    await new BasePage(page).screenshot('fullpage-footer')
})

test('product special price', BasePage.tags, async ({ page }) => {
    await page.goto(process.env.PRODUCT_URL_SPECIAL_PRICE)
    await new BasePage(page).screenshot('fullpage-footer')
})

test('wcag', BasePage.tags, async ({ page }, testInfo) => {
    await page.goto(process.env.PRODUCT_URL_SIMPLE)
    await new BasePage(page).wcag(testInfo)
})

test('lighthouse', BasePage.tags, async ({ page }) => {
    await new BasePage(page).lighthouse(process.env.PRODUCT_URL_SIMPLE)
})
