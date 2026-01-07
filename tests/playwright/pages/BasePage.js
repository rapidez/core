import { test, expect } from '@playwright/test'
import AxeBuilder from '@axe-core/playwright'
import { playAudit } from 'playwright-lighthouse'
import playwright from 'playwright'
import lighthouseMobileConfig from 'lighthouse/core/config/lr-mobile-config.js'

export class BasePage {
    static tags = { tag: process.env.MAGENTO_VERSION ? '@' + process.env.MAGENTO_VERSION : null }

    constructor(page) {
        this.page = page
    }

    async screenshot(type = '', options = {}) {
        const masks = [this.page.getByTestId('masked')]
        const emailFields = this.page.locator('[name=email]')

        // Only mask filled email fields
        const emailFieldsCount = await emailFields.count()
        if (emailFieldsCount) {
            for (let i = 0; i < emailFieldsCount; i++) {
                let emailField = emailFields.nth(i)
                if (await emailField.inputValue()) {
                    masks.push(emailField)
                }
            }
        }

        options['mask'] = masks

        if (type.startsWith('fullpage')) {
            await this.scrolldown()
            options['fullPage'] = true
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

    async lighthouse(url) {
        test.skip(test.info().project.name !== 'chromium', 'Chromium only')

        const browser = await playwright['chromium'].launch({
            args: ['--remote-debugging-port=9222'],
        })
        const page = await browser.newPage()
        const reportName = `lighthouse-${new Date().getTime()}`

        await page.goto(url)

        try {
            await playAudit({
                page: page,
                port: 9222,
                thresholds: {
                    performance: 90,
                    accessibility: 100,
                    'best-practices': 100,
                    seo: 100,
                },
                reports: {
                    formats: {
                        html: true,
                    },
                    name: reportName,
                },
                config: {
                    ...lighthouseMobileConfig,
                    settings: {
                        ...lighthouseMobileConfig.settings,
                        skipAudits: [
                            ...lighthouseMobileConfig.settings.skipAudits,
                            // Skip everything that's not fixed within CI tests.
                            'meta-description',
                            'is-on-https',
                            'redirects-http',
                            'uses-long-cache-ttl',
                            'uses-optimized-images',
                            'cache-insight',
                            'image-delivery-insight',
                        ],
                    },
                },
            })
        } catch (error) {
            await test.info().attach(reportName, {
                path: 'lighthouse/' + reportName + '.html',
                contentType: 'text/html',
            })

            throw error
        } finally {
            await browser.close()
        }
    }
}
