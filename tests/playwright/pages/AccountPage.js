import { expect } from '@playwright/test'

export class AccountPage {
    constructor(page) {
        this.page = page
    }

    async logout() {
        await this.page.goto('/')
        await this.page.getByTestId('account-menu').click()
        await this.page.getByTestId('logout').click()
        await this.page.waitForLoadState('networkidle')
        await expect(this.page.getByTestId('logout')).toBeHidden()
    }
}
