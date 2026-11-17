---
name: rapidez-core-override-comments
description: Check, add and correct the "Overwritten to …" comments on Rapidez Blade and GraphQL view overrides in resources/views/vendor, and report incorrect comments and overrides that have fallen behind vendor. Use when documenting or auditing overrides in resources/views/vendor, checking vendor-hash comments, or preparing a Rapidez upgrade.
---

# Rapidez override comments

Every real override in `resources/views/vendor` or a Rapidez theme (`<theme>/vendor`, from `rapidez.frontend.theme` and `rapidez.stores.*.frontend.theme`) gets one short comment saying what it changes compared to the vendor file, so whoever upgrades the project can see what has to be carried over. Most overrides will already have one: checking those is the main job.

## 1. Map the overrides

Use the Rapidez MCP tools from Laravel Boost (they're registered when `laravel/boost` is installed, restart the MCP server after updating Rapidez):

- `rapidez-list-view-overrides` lists every Blade and GraphQL file in the vendor folders, optionally filtered by `kind`.
- `rapidez-view-override-diff` returns the diff of a single override (`file`, as listed).

They read the view namespaces Laravel actually resolves (plus `mail` for the framework's mail components) and the configured theme folders, and need `git` and `diff`. Each file is classified:

- **override**: has a vendor counterpart. Gets a comment.
- **include-only**: the body is only `@include`s. No comment.
- **project-only**: no vendor file, so not an override. No comment.

The list also gives `has_comment`, `hash` (vendor-hash `none`, `match` or `mismatch`), `diff_lines` against the installed vendor file and `shadows`: a theme file that also exists in `resources/views/vendor` replaces that project override on that theme, not just the vendor file. `notes` lists what needs a manual look: hashes without a vendor file (vendor removed it), overrides identical to vendor, paths that several packages provide (it compares with the one Laravel resolves first), and, from the diff tool, overrides that have fallen behind vendor.

**Base version.** An override without a vendor-hash, or with a hash that doesn't match the installed file, was made against an older vendor file. Diffing it against today's vendor would show vendor's later additions as if the override removed them. So for those files the diff tool finds the vendor version the override was based on: for every commit that touched the override, it reads `composer.lock` at that commit and fetches the vendor file at that locked reference from GitHub (cached in `storage/framework/cache/rapidez-view-overrides`). A vendor-hash picks the exact version; without one, the closest version wins. Then:

- `diff` is **base → override**: only the override's own changes. Write the comment from this.
- `vendor_since_base_diff` is **base → installed vendor**: what vendor changed since. Not part of the comment.
- `base` (version), `base_date`, `base_source` (`hash` or `closest`) and `vendor_changed_since_base` (changed lines) are set.

Without a base, `diff` is installed vendor → override. Packages that aren't on GitHub (private repositories) or have no locked source reference can't be fetched, so those fall back to the installed vendor file. Treat their diffs with the same caution.

## 2. Check and write the comments

With 100+ overrides, split them into ~4 batches balanced by `diff_lines` and hand each batch to a subagent with these rules and the file paths; it fetches each diff with `rapidez-view-override-diff`. Ask each for `path — comment` for new comments, `path — old → new — reason` for incorrect ones, fallen-behind findings, and oddities.

Per file, get the diff and work out what the override changes **functionally**. Then:

- **Comment exists** (existing ones may say "Override because …", "Overwrite …"): keep it only if the diff shows it is correct and complete. It's incorrect when it describes something the override no longer does, misses a functional change, names the wrong field/prop/store, or describes something vendor now does too. Amend it in place and note old, new and reason.
- **No comment**: write one.
- **Comment on an include-only or project-only file**: leave the file, report it.

For files with a `base`, judge the comment against `diff` (base → override) only. Then read `vendor_since_base_diff` and work out what the override misses: fields, props, fixes or features vendor added since, and code vendor rewrote that the override still carries in its old form. Never describe those as the override's changes (vendor adding a field is not "Remove field"). Report them under fallen behind.

When the diff tool couldn't find a base but there's no hash (or the hash mismatches), use `git log` on the override and the vendor package's history to tell override changes from later vendor changes before trusting the diff.

When a change matches what has been done in the vendor file itself there is no need to document it.

**Format**

- Blade comment, also in `.graphql` files (they render as Blade, so `#` would end up in the query).
- Directly below `{{-- vendor-hash:… --}}`, or on line 1 if there is none.
- One change: `{{-- Overwritten to … --}}`. Several: up to ~4 bullets:
    ```blade
    {{-- Overwritten to:
        - Add X
        - Remove Y
    --}}
    ```

**Content**

- Follow ASD-STE100.
- Short. Name concrete fields, props, features, store conditions (e.g. "hide prices behind login on the B2B store").
- Styling-only: just "change styling".
- Largely rewritten: one line on what the override does.
- Keep existing TODO/removal notes (e.g. "Remove when PR #202 is released").

Change nothing but the comment, including the blank lines around it. Don't commit.

## 3. Verify

```bash
git diff -U0 | grep '^-[^-]'   # only old comment lines may be removed
git diff -U0 | grep -c '^-$'   # must be 0
```

Run `rapidez-list-view-overrides` again: every override must have `has_comment` `true`, and only override files may appear in `git diff --stat`. No `&lt;`/`&gt;`/`&amp;` may have been written.

## 4. Report back

1. Every incorrect comment: `path — old → new — reason`. This matters most.
2. Counts: checked and correct, amended, added, skipped per category.
3. Fallen behind vendor, per file: `path — based on <version> (<date>) — what vendor changed since that the override misses`. Call out notable discrepancies specifically: missing fields or props that other code relies on, vendor bug fixes, and comments that were wrong because they described vendor's later changes as the override's.
4. Oddities: overrides identical to vendor or formatting-only (removable), styling-only, unused-looking, hash without vendor file, comments on non-overrides, and overrides still using code vendor has since deprecated, replaced or removed.
