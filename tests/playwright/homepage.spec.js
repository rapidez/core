import { test, expect } from '@playwright/test'
import { BasePage } from './pages/BasePage'

test('homepage', BasePage.tags, async ({ page }, testInfo) => {
    await page.goto('/')
    await new BasePage(page).screenshot('fullpage-footer')
})

test('wcag', BasePage.tags, async ({ page }, testInfo) => {
    await page.goto('/')
    await new BasePage(page).wcag(testInfo, 'page-has-heading-one')
})

test('lighthouse', BasePage.tags, async ({ page }) => {
    await new BasePage(page).lighthouse('/')
})
