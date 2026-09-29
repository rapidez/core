import { expect } from '@playwright/test'
import { BasePage } from './BasePage'

export class AccountPage {
    constructor(page) {
        this.page = page
    }

    async logout() {
        await this.page.goto('/')
        await new BasePage(this.page).waitUntilIdle()
        await this.page.getByTestId('account-menu').click()
        await this.page.getByTestId('logout').click()
        await this.page.waitForLoadState('networkidle')
        await expect(this.page.getByTestId('logout')).toBeHidden()
        await new BasePage(this.page).waitUntilIdle()
    }
}
