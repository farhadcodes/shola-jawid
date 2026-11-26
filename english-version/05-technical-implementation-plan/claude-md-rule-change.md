# CLAUDE.md rule change — this is Phase 1, step one

`CLAUDE.md` is binding law for this repository (its own opening line says
so). It currently, explicitly, in writing, forbids this entire project:

> Any future English rollout is explicitly out of scope. Do not scaffold
> Polylang config, `en/` routing, or hreflang tags speculatively —
> bilingual-*ready* means "won't have to be rewritten," not "half-built
> now."

Per `CLAUDE.md`'s own rule ("If a situation isn't covered here, stop and add
a rule to this file first... then proceed. Do not improvise silently"),
**this file must be formally edited and the change logged in
`docs/CHANGELOG.md` before any code in this plan is written** — not quietly
ignored, not worked around. This is not a formality; it's the same
discipline this project has applied to every other scope change so far
(see the plugin-whitelist additions in `CLAUDE.md` §3, each with its own
dated justification).

## Proposed replacement text for §1

```markdown
## 1. Scope lock — Persian (RTL) primary, English (LTR) secondary

- **Primary content, menus, and site language: Persian (fa), RTL.** This
  remains the default language for every new visitor and the language
  staff write in first.
- **English (en) is a second, real, reachable version of the site**, not a
  placeholder. English pages live under a `/en/` URL prefix; Persian URLs
  are completely unaffected — no `/fa/` prefix, no change to any existing
  Persian permalink.
- Content is paired, not duplicated as a separate install: a post's English
  counterpart (if one exists) is linked via a generalized
  `shcore_translation_id`-style meta field, and its language is marked via
  a generalized `shcore_language` meta field (`fa`/`en`). Not every piece
  of Persian content needs an English counterpart — pairing is set per
  post, as translations are actually written.
- Fixed-vocabulary taxonomies (`topic`, `publication`, `collection`, etc.)
  are NOT duplicated into parallel English taxonomies — one shared term
  per real-world category, with an English label and English slug stored
  as term-meta. See `english-version/03-content-architecture/taxonomy-translation.md`.
- `dir="rtl"`/`dir="ltr"` and all spacing in CSS continues to use **logical
  properties only** — this was already the rule and is now load-bearing:
  the English version depends on logical properties flipping correctly
  under `dir="ltr"` with minimal explicit overrides. Any new physical
  direction property (`margin-left`, etc.) is a bug, not a style choice,
  in either language.
- The language switcher is now a real, working control — not inert. It
  resolves to the current page's actual paired/equivalent URL in the other
  language, with a defined, safe fallback when no direct pairing exists
  (never a 404, never a silent no-op). See
  `english-version/03-content-architecture/language-field-and-pairing.md`.
- Both `languages/*.pot` files are kept current going forward (regenerated
  as part of this rollout — they were badly stale even for Persian-only
  content) — no multilingual plugin (Polylang, WPML) is used for this or
  anything else; `CLAUDE.md` §3's plugin-whitelist policy is unchanged.
- `get_locale()`/`is_rtl()` remain the only correct way to branch on
  language/direction in PHP — never a hardcoded `"fa"`/`"en"` string
  outside the one central language-context function described in
  `english-version/03-content-architecture/url-structure-and-routing.md`.
```

## What this change does NOT do

It does not reopen or relitigate any other `CLAUDE.md` rule — the
plugin whitelist (§3), the theme/plugin architecture split (§2), the
credit-block placements (§7), the git workflow (§8) are all unaffected and
apply to this work exactly as they do to everything else on this project.

## The CHANGELOG entry this requires

Once approved, `docs/CHANGELOG.md` gets a dated entry recording: what
changed, why (client requested a real English version, confirmed by Farhad
2026-11-26), and a pointer to this `english-version/` folder as the full
reasoning/plan — matching the exact format every other rules-change entry
in that file already uses.
