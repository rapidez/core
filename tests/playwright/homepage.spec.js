import { test, expect } from '@playwright/test'
import { BasePage } from './pages/BasePage'

test('homepage', async ({ page }) => {
    await page.goto('/')
    await new BasePage(page).screenshot('fullpage-footer')
})
