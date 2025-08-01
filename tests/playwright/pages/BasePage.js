import { expect } from '@playwright/test'
import AxeBuilder from '@axe-core/playwright'

export class BasePage {
    constructor(page) {
        this.page = page
    }

    async screenshot(type) {
        let options = {}

        if (type.startsWith('fullpage')) {
            await this.scrolldown()
            options = { fullPage: true }
        }

        if (type.startsWith('fullpage-footer')) {
            await expect(this.page.getByTestId('newsletter-email')).toBeVisible()
        }

        if (type == 'fullpage-footer-images') {
            await this.waitForImages()
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
        for (const img of await this.page.locator('img[loading="lazy"]:visible').all()) {
            await img.scrollIntoViewIfNeeded()
            await expect(img).toHaveJSProperty('complete', true)
            await expect(img).not.toHaveJSProperty('naturalWidth', 0)
        }

        await this.page.evaluate(() => window.scrollTo(0, 0))
    }

    async wcag(testInfo, disabledRules = []) {
        const page = this.page
        const accessibilityScanResults = await new AxeBuilder({ page }).disableRules(disabledRules).analyze()

        await testInfo.attach('accessibility-scan-results', {
            body: JSON.stringify(accessibilityScanResults, null, 2),
            contentType: 'application/json',
        })

        expect(accessibilityScanResults.violations).toEqual([])
    }
}
