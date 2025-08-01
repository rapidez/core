import { test, expect } from '@playwright/test'
import { BasePage } from './pages/BasePage'

test('category with simple products', async ({ page }) => {
    await page.goto(process.env.CATEGORY_URL_SIMPLE)
    await new BasePage(page).screenshot('fullpage-footer-images')
})

test('category with configurable products', async ({ page }) => {
    await page.goto(process.env.CATEGORY_URL_CONFIGURABLE)
    await new BasePage(page).screenshot('fullpage-footer-images')
})

test('category pagination', async ({ page }) => {
    await page.goto(process.env.CATEGORY_URL_SIMPLE)
    await expect(page.getByTestId('listing-item')).toHaveCount(12)
    const firstProductPage1 = await page.getByTestId('listing-item').first().textContent()
    await page.getByTestId('pagination').getByRole('button', { name: '2' }).click()
    await page.waitForLoadState('networkidle')
    const firstProductPage2 = await page.getByTestId('listing-item').first().textContent()
    expect(firstProductPage1).not.toBe(firstProductPage2)
    await new BasePage(page).screenshot('fullpage-footer-images')
})

test('category filter', async ({ page }) => {
    await page.goto(process.env.CATEGORY_URL_SIMPLE)
    await expect(page.getByTestId('listing-item')).toHaveCount(12)

    // On mobile you first need to open the filter slideover.
    const filtersToggle = page.getByTestId('listing-filters-toggle')
    if (await filtersToggle.isVisible()) {
        await filtersToggle.click()
    }

    const filter = page.getByTestId('listing-filters').locator('label', { hasText: process.env.CATEGORY_FILTER_LABEL })
    const countText = await filter.getByTestId('listing-filter-count').textContent()
    const count = parseInt(countText.replace(/\D/g, ''))

    await filter.click()
    await expect(page.getByTestId('listing-item')).toHaveCount(count)
    await new BasePage(page).screenshot('fullpage-footer-images')
})

test('wcag', async ({ page }, testInfo) => {
    await page.goto(process.env.CATEGORY_URL_SIMPLE)
    await new BasePage(page).wcag(testInfo)
})
