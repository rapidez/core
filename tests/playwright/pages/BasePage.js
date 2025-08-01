import { expect } from '@playwright/test'

export class BasePage {
    constructor(page) {
        this.page = page
    }

    async screenshot(type) {
        let options = {}

        if (['fullpage', 'fullpage-footer'].includes(type)) {
            await this.scrolldown()
            await this.waitForImages()
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
        await this.page.waitForLoadState('networkidle')
    }

    async waitForImages() {
        const images = await this.page.locator('img[loading="lazy"]:visible').evaluateAll((imgs) =>
            // naturalWidth doesn't work with Firefox on svgs
            imgs.filter((img) => !img.src.endsWith('.svg')),
        )

        for (const img of images) {
            await img.scrollIntoViewIfNeeded()
            await expect(img).toHaveJSProperty('complete', true)
            await expect(img).not.toHaveJSProperty('naturalWidth', 0)
        }

        await this.page.evaluate(() => window.scrollTo(0, 0))
    }
}
