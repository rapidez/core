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
    const cartPage = new CartPage(page)

    await cartPage.gotoCart()
    await cartPage.setFirstItemQty(5)
    await expect(cartPage.cartItem()).toContainText((product.price * 5).toString())
})

test('remove product', BasePage.tags, async ({ page }) => {
    const product = await new ProductPage(page).addToCart(process.env.PRODUCT_URL_SIMPLE)
    const cartPage = new CartPage(page)

    await cartPage.gotoCart()
    await cartPage.removeFirstItem()
    await expect(cartPage.cartItem()).toHaveCount(0)
})

test('wcag', BasePage.tags, async ({ page }, testInfo) => {
    const product = await new ProductPage(page).addToCart(process.env.PRODUCT_URL_SIMPLE)
    await new CartPage(page).gotoCart()
    await new BasePage(page).wcag(testInfo)
})
