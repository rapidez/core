import { test, expect } from '@playwright/test'

test('homepage', async ({ page }) => {
    await page.goto('/')
    await expect(page).toHaveScreenshot({ fullPage: true })
})
