---
name: rapidez-core-upgrade-overwritten-templates
description: Carry vendor changes into Rapidez Blade and GraphQL view overrides after a Composer update, using the changed vendor-hash comments, keep their "Overwritten to …" comments correct and propose overrides that can be removed or simplified. Use when upgrading Rapidez or other packages with published views, after composer update, or when vendor-hash lines changed in resources/views/vendor or a theme.
---

# Upgrade overwritten templates

After `composer update`, `rapidez/composer-template-diff-plugin` rewrites the `{{-- vendor-hash:… --}}` line in every Blade override to the md5 of the **new** vendor file. A changed hash line means vendor changed that file in this update. This skill brings those overrides up to date with vendor without losing what they change, and finds overrides vendor made unnecessary.

It uses two Laravel Boost MCP tools from `rapidez/core`: `rapidez-changed-overrides` and `rapidez-override-upgrade-diff`. If they're missing, Laravel Boost isn't installed or is outdated: suggest `composer require laravel/boost --dev` and `php artisan boost:install`.

Don't use the `rapidez-core-override-comments` script on these files: the rewritten hash now matches the new vendor file, so it would diff against the new vendor and show vendor's additions as if the override removed them. Its comment rules still apply, see step 2a.

## 1. Find the changed overrides

If the hashes haven't been updated yet, run `composer update-vendor-hashes` first. Then call `rapidez-changed-overrides`.

What it compares:

- **Changed or staged vendor-hash lines in the working tree**: the working tree against `HEAD`.
- **Otherwise**: the latest commit against the one before (`HEAD~1` → `HEAD`).
- **`from`**: a git ref to compare the working tree with, for example when the update spans several commits.

It returns the packages that changed between both `composer.lock` files, and one row per override with the old and new version, `own_lines`, `vendor_lines` and the number of merge `conflicts`. It also returns overrides without a hash (`.graphql` files, or Blade files the plugin skipped) whose vendor file changed.

The old vendor file comes from GitHub at the reference in the old `composer.lock`. Check these fields:

- `old_source: missing`: it couldn't be fetched (a path repository, a private repo without a Composer GitHub token, or no `composer.lock` at that ref). Ask the user for a vendor folder from before the update, for example a copy of `vendor/` or a `git worktree` of the old commit with `composer install`, and pass it to both tools as `old_vendor`.
- `verified: false`: the fetched file doesn't match the old hash, so the override was already behind before this update. Treat its diffs with care, and use `git log` on the override to see which changes are its own.
- `shadows` / `shadowed_by`: a theme override replaces the project override with the same path on that theme. Keep that in mind when porting changes and especially before removing either file.

## 2. Per override

With more than ~15 overrides, split them into batches balanced by `own_lines + vendor_lines` and hand each batch to a subagent with these rules. Ask each for the report items from step 5 and the cleanups from step 2c.

Call `rapidez-override-upgrade-diff` with the `file` (and the same `from` / `old_vendor`). It returns four sections: **override changes** (old vendor → override), **vendor changes** (old vendor → new vendor), the **merge** (override with the vendor changes applied, conflicts between diff3 markers) and **override vs new vendor**.

### a. Check the comment

Apply the rules of the `rapidez-core-override-comments` skill (_Check and write the comments_, _Format_ and _Content_) to the **override changes** section. It tells you what the override changes and why, and that is what you need for the next steps. Fix or add the comment.

### b. Port the vendor changes

Go through the **vendor changes** hunk by hunk. Apply a hunk to the override only when **both** are true:

- the hunk merges cleanly (it's outside the conflict markers in the merge);
- it doesn't go against what the override changes or why. Vendor re-adding a field the override removes on purpose, restyling markup the override replaced, or changing a prop the override deliberately sets differently all go against it, even when they merge cleanly.

Edit those hunks in, taking their text from the merge section. Don't paste the whole merged file, and never leave conflict markers behind. Leave the `vendor-hash` line as it is.

- **Conflicts**: when the hunk is a bug fix or an addition the override's change can be kept next to (a new prop, attribute, event, translation, slot), combine both and say so in the report. Otherwise don't apply it: report it.
- **Whitespace and formatting-only hunks**: skip them.

If porting changed what the override does, update the comment again.

### c. Collect cleanups

Collect these, but don't apply them yet. Use **override vs new vendor** together with the vendor changes:

- **Obsolete**: the only differences left are comments, formatting, or changes vendor now makes itself.
- **Simplifiable**: vendor added a slot, prop, config option, `@include` point, event or CSS hook that does what the override changes, so the override can be removed in favour of that, or reduced to an include-only file or a smaller change.
- **Partly upstreamed**: some of the override's changes are now in vendor and can be dropped from it.

Per cleanup, note `path — what — why (the vendor change that allows it)`.

## 3. Ask the user

Show the collected cleanups grouped as obsolete, simplifiable and partly upstreamed. Ask with AskUserQuestion which ones to do: multiSelect, at most 4 options per question, so use several questions for more. Then apply only what was chosen:

- **Obsolete**: delete the override. If it `shadows` or is `shadowed_by` another override, say what that changes for those stores. If the replacement is a config option, prop or slot, put that in place.
- **Simplifiable / partly upstreamed**: reduce the override, keep the `vendor-hash` line and update the comment.

## 4. Verify

```bash
grep -rnE '^(<<<<<<<|\|\|\|\|\|\|\||=======|>>>>>>>)( |$)' resources/views resources/themes   # must be empty
php artisan view:cache && php artisan view:clear   # every view must compile
git diff --stat   # only override files (and replaced config, if a cleanup needed that)
```

Calling `rapidez-changed-overrides` again should list the same overrides, with the new `own_lines` showing what was ported. Don't commit.

## 5. Report back

1. **Ported** per file: the vendor changes applied, with conflicts that were combined called out.
2. **Not ported** per file: `path — vendor change — why` (conflicts with the override's change or reason, or unclear). This matters most: the user has to decide on these.
3. **Comments**: `path — old → new — reason` for amended ones, and the ones that were added.
4. **Cleanups**: applied and declined.
5. **Unverified or missing** old vendor files, and overrides vendor has since removed (`vendor` is empty).
