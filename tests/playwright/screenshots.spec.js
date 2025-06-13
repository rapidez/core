// @ts-check
import { test, expect } from '@playwright/test';

test("homepage", async ({ page }) => {
  await page.goto("/");
  await expect(page).toHaveScreenshot();
});

test("category with simple products", async ({ page }) => {
  await page.goto("/gear/bags.html");
  await expect(page).toHaveScreenshot();
});

test("category with configurable products", async ({ page }) => {
  await page.goto("/women/tops-women.html");
  await expect(page).toHaveScreenshot();
});

test("product simple", async ({ page }) => {
  await page.goto("/joust-duffle-bag.html");
  await expect(page).toHaveScreenshot();
});

test("product configurable", async ({ page }) => {
  await page.goto("/stellar-solar-jacket.html");
  await expect(page).toHaveScreenshot();
});

test("product grouped", async ({ page }) => {
  await page.goto("/set-of-sprite-yoga-straps.html");
  await expect(page).toHaveScreenshot();
});

// TODO: migrate all Dusk tests?
