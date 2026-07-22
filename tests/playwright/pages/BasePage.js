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
        const masks = options['mask'] || []
        masks.push(this.page.getByTestId('masked'))
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

        options['mask'] = [...masks, this.page.locator('[data-attribute=id]')]

        if (type.startsWith('fullpage')) {
            await this.loadLazy()
            options['fullPage'] = true
        }

        if (type.startsWith('fullpage-footer')) {
            await expect(this.page.getByTestId('newsletter-email')).toBeVisible()
        }

        await this.waitForImages()
        await expect(this.page).toHaveScreenshot(options)
    }

    async waitForImages() {
        // Make all images eager loaded
        await this.page.evaluate(() => window.document.querySelectorAll('img[loading="lazy"]').forEach((elem) => (elem.loading = 'eager')))

        // Check all images for completed state
        for (const img of await this.page.locator('img').all()) {
            await expect(img).toHaveJSProperty('complete', true)
            await expect(img).not.toHaveJSProperty('naturalWidth', 0)
        }
    }

    async waitUntilIdle() {
        // Webkit does not support requestIdleCallback, so we just wait for a bit.
        if (this.page.context().browser().browserType().name() === 'webkit') {
            await this.page.waitForTimeout(200)
            await this.page.waitForLoadState('networkidle')
            return
        }

        await expect(
            await this.page.evaluate(async () => {
                return (
                    (await new Promise((resolve, reject) => {
                        let counter = 0
                        let intervalTime = 0.05
                        let idleForTime = 0.5
                        let interval = setInterval(async function () {
                            if (window.app?.config === undefined) {
                                return
                            }
                            let result = await new Promise((resolve, reject) =>
                                window.requestIdleCallback((deadline) => resolve(!deadline.didTimeout), { timeout: 5 }),
                            )
                            counter = result ? counter + 1 : 0
                            if (counter >= idleForTime / intervalTime) {
                                clearInterval(interval)
                                resolve(true)
                            }
                        }, intervalTime * 1000)
                        setTimeout(() => resolve(false), 60 * 1000)
                    })) === true
                )
            }),
            'Page should become idle within a minute',
        ).toBeTruthy()
        await this.page.waitForLoadState('networkidle')
    }

    async loadLazy() {
        await this.page.waitForLoadState('networkidle')
        await this.page.evaluate(() => window.$emit('load-lazy'))
        await this.page.waitForTimeout(250)
        await this.page.waitForLoadState('networkidle')
        await this.page.waitForTimeout(250)
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
