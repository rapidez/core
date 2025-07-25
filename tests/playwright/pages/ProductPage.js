export class ProductPage {
    constructor(page) {
        this.page = page
    }

    async goto(url) {
        await this.page.goto(url)
        return await this.page.evaluate(() => window.config.product)
    }

    async addToCart(url, qty = 1) {
        const product = await this.goto(url)

        if (qty > 1) {
            await this.page.getByTestId('qty').fill(qty.toString())
        }

        await this.page.getByTestId('add-to-cart').click()
        await this.page.waitForLoadState('networkidle')

        return product
    }
}
