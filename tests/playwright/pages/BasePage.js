import { expect } from '@playwright/test'

export class BasePage {
    constructor(page) {
        this.page = page
    }

    async screenshot(type) {
        let options = {}

        if (['fullpage', 'fullpage-footer'].includes(type)) {
            await this.scrolldown()
            options = { fullPage: true }
        }

        if (type == 'fullpage-footer') {
            await expect(this.page.getByTestId('newsletter-email')).toBeVisible()
        }

        await expect(this.page).toHaveScreenshot(options)
    }

    async scrolldown() {
        await this.page.evaluate(() => window.scrollTo(0, document.body.scrollHeight))
        await this.page.waitForLoadState('networkidle')
        await this.page.evaluate(() => window.scrollTo(0, 0))
    }
}
