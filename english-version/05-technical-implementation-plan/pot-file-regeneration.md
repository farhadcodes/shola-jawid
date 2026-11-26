# POT file regeneration

## The current state

| File | Current entries | Real string count in code | Last generated |
|---|---|---|---|
| `wp-content/themes/shola-jawid/languages/shola-jawid.pot` | 4 | 349 | 2026-08-05 |
| `wp-content/plugins/shola-core/languages/shola-core.pot` | 67 | 392 | 2026-08-06 |

Both files were generated once, near the very start of the project, and
never regenerated since — predating `party_publication`, `party_document`,
`leaflet`, `hero_section`, `masthead_section`, and `editors_pick` (all added
2026-09-02 through 2026-10-01) entirely. This is independent housekeeping
debt, not something this English-version project caused — but it has to be
fixed as part of this work, since `CLAUDE.md` §1 requires these files to
stay current, and a stale `.pot` would now also block generating a real
Persian `.po`/`.mo` translation file accurately.

## The fix

Regenerate both via WP-CLI's built-in i18n command (standard WordPress
tooling, not a new dependency):

```
wp i18n make-pot wp-content/themes/shola-jawid wp-content/themes/shola-jawid/languages/shola-jawid.pot
wp i18n make-pot wp-content/plugins/shola-core wp-content/plugins/shola-core/languages/shola-core.pot
```

## Why this matters for the English rollout specifically

Once `/en/` pages exist, there are now two real audiences for this file:

1. **Translators producing the actual Persian `.po`/`.mo`** (the original,
   still-pending task `CLAUDE.md` §1 always called for) — need a complete,
   current list of every string.
2. **Anyone auditing "is every visible string actually translatable"** —
   this plan's whole premise (`bilingual-readiness-audit.md`) rests on the
   claim that 100% of UI copy already goes through `__()`/`_e()`. A current
   `.pot` file is the direct, checkable proof of that claim, not just a
   grep result quoted in a planning document.

## When this happens in the build order

Placed as Phase 6 in `phase-by-phase-build-plan.md` — after English content
exists, so the regenerated `.pot` reflects any new strings the English
rollout itself introduces (new language-switcher label text, any new admin
field labels for the term-translation UI, etc.), not just today's Persian
strings.
