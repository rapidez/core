import { test, expect } from '@playwright/test'
import { BasePage } from './pages/BasePage'

test('homepage', async ({ page }) => {
    await page.goto('/')
    await new BasePage(page).screenshot('fullpage-footer')
})

test('wcag', async ({ page }, testInfo) => {
    await page.goto('/')
    await new BasePage(page).wcag(testInfo, 'page-has-heading-one')
})

test('lighthouse', async ({ page }) => {
    await new BasePage(page).lighthouse('/')
})
