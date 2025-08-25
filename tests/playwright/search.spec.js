import { test, expect } from '@playwright/test'
import { ProductPage } from './pages/ProductPage'

test('search page', async ({ page }) => {
    const product = await new ProductPage(page).goto(process.env.PRODUCT_URL_SIMPLE)
    await page.goto('/search?q=' + product.name)
    await page.waitForLoadState('networkidle')
    await expect(page.getByTestId('listing-item').first()).toContainText(product.name)
    await expect(page).toHaveScreenshot()
})

test('autocomplete', async ({ page }) => {
    const product = await new ProductPage(page).goto(process.env.PRODUCT_URL_SIMPLE)
    await page.goto('/')
    await page.getByTestId('autocomplete-input').click()
    await page.waitForLoadState('networkidle')
    await page.getByTestId('autocomplete-input').fill(product.name)
    await page.waitForLoadState('networkidle')
    await expect(page.getByTestId('autocomplete-item').first()).toContainText(product.name)
    await expect(page).toHaveScreenshot()
})
