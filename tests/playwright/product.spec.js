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

test('product special price', async ({ page }) => {
    await page.goto(process.env.PRODUCT_URL_SPECIAL_PRICE)
    await new BasePage(page).screenshot('fullpage-footer')
})

test('product tier priece', async ({ page }) => {
    await page.goto(process.env.PRODUCT_URL_TIER_PRICE)
    await page.waitForTimeout(100)
    await new BasePage(page).screenshot('fullpage-footer')
    await page.getByTestId('qty').fill('')
    await expect(page.getByTestId('price')).toHaveText('€58.00')
    await page.getByTestId('qty').fill('')
    await page.getByTestId('qty').pressSequentially('2')
    await expect(page.getByTestId('price')).toHaveText('€55.00')
    await page.getByTestId('qty').fill('')
    await page.getByTestId('qty').pressSequentially('4')
    await expect(page.getByTestId('price')).toHaveText('€41.30')
    await page.getByTestId('qty').fill('')
    await page.getByTestId('qty').pressSequentially('6')
    await expect(page.getByTestId('price')).toHaveText('€41.30')
})

test('wcag', async ({ page }, testInfo) => {
    await page.goto(process.env.PRODUCT_URL_SIMPLE)
    await new BasePage(page).wcag(testInfo)
})
