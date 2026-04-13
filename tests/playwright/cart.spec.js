import { test, expect } from '@playwright/test'
import { ProductPage } from './pages/ProductPage'
import { CartPage } from './pages/CartPage'
import { BasePage } from './pages/BasePage'

test('add product simple', BasePage.tags, async ({ page }) => {
    const product = await new ProductPage(page).addToCart(process.env.PRODUCT_URL_SIMPLE)
    await new CartPage(page).firstItemIs(product)
    await new BasePage(page).screenshot('fullpage-footer')
})

test('add product simple twice', BasePage.tags, async ({ page }) => {
    await new ProductPage(page).addToCart(process.env.PRODUCT_URL_SIMPLE, 2)
    await new CartPage(page).firstItemQtyIs(2)
})

test('change quantity', BasePage.tags, async ({ page }) => {
    const product = await new ProductPage(page).addToCart(process.env.PRODUCT_URL_SIMPLE)
    // TODO: Extract to CartPage?
    await page.goto('/cart')
    await page.getByTestId('qty').fill('5')
    await page.getByTestId('qty').press('Tab')
    await expect(page.getByTestId('cart-item')).toContainText((product.price * 5).toString())
})

test('remove product', BasePage.tags, async ({ page }) => {
    const product = await new ProductPage(page).addToCart(process.env.PRODUCT_URL_SIMPLE)
    // TODO: Extract to CartPage?
    await page.goto('/cart')
    await page.getByTestId('cart-item-remove').click()
    await page.waitForLoadState('networkidle')
    await expect(page.getByTestId('cart-item')).toHaveCount(0)
})

test('empty cart', BasePage.tags, async ({ page }) => {
    await page.goto('/cart')
    await new BasePage(page).screenshot('fullpage-footer')
})

test('wcag', BasePage.tags, async ({ page }, testInfo) => {
    const product = await new ProductPage(page).addToCart(process.env.PRODUCT_URL_SIMPLE)
    await new CartPage(page).gotoCart()
    await new BasePage(page).wcag(testInfo)
})
