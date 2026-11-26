# Bilingual-readiness audit — what's already there, what isn't

This is the honest, file-by-file state of "bilingual-ready" as it actually
exists today, not as a general claim. Every line below was verified by
reading the actual code, 2026-11-26.

## CLAUDE.md's current rule (verbatim — this is what's being changed)

> ## 1. Scope lock — Persian only, bilingual-ready
>
> - **Active content, menus, and site language: Persian (fa) only, RTL.**
>   English (en) is *not* being built, populated, or exposed in this phase.
> - The theme must nonetheless be **bilingual-ready**, meaning: every string
>   wrapped in `__()`/`_e()`; logical CSS properties only, never physical;
>   a `languages/` `.pot` file exists; no Polylang/WPML; a static/inert
>   language-switcher UI element is fine but must not link anywhere real;
>   never hardcode `lang="fa"` in more than one place.
> - Any future English rollout is explicitly out of scope. Do not scaffold
>   Polylang config, `en/` routing, or hreflang tags speculatively.

This rule did its job — it's exactly why the audit below looks as good as
it does. See `05-technical-implementation-plan/claude-md-rule-change.md`
for the proposed replacement.

## ✅ Already solid

- **Text domains**: 349 translation-function calls in the theme, 392 in the
  plugin. No hardcoded UI copy found in any template.
- **Logical CSS properties**: zero hits for `margin-left`, `margin-right`,
  `padding-left`, `padding-right`, bare `left:`/`right:`, `border-left`,
  `border-right`, `text-align: left/right`, or `float: left/right` anywhere
  in `main.css` or `login.css`. This is the single biggest reason the LTR
  flip is realistic rather than a rewrite — see
  `04-design-and-ltr-system/rtl-to-ltr-css-audit.md`.
- **English/Latin font tokens already defined** in `main.css`'s `:root`:
  `--font-en` (Newsreader), `--font-ui` (Inter), `--font-mono` (JetBrains
  Mono) — and the actual font files are already self-hosted in
  `assets/fonts/`. Zero new font licensing or hosting work needed.
- **`shcore_language` meta field** already exists on `document`,
  `party_publication`, `party_document`, `post`.
- **`shcore_translation_id` pairing field** already exists on `post`.
- **Inert hreflang tag pair** already printed by `class-seo.php` on every
  page (currently pointing at itself) — the real wiring is a small change,
  not new code from scratch.
- **`is_rtl()` used correctly** everywhere direction-aware logic exists
  (e.g. the Jalali-calendar code) — never a hardcoded `"fa"` check.

## ⚠️ Scaffolded but incomplete

- **`shcore_language` is missing from `issue`, `leaflet`, `editors_pick`,
  `announcement`, `hero_section`, `masthead_section`** — needs adding to
  whichever of these actually get English content (see
  `03-content-architecture/open-decisions-needing-client-input.md`).
- **`shcore_translation_id` only exists on `post`** — needs generalizing to
  a pairing mechanism every translatable content type can use.
- **`--font-en`/`--font-ui`/`--font-mono` are each used in exactly one CSS
  rule** (`[lang="en"], .latin { font-family: var(--font-en); }` and its
  `.ui` sibling) — a single small Latin "escape hatch" span in the masthead,
  not a document-wide language switch. `--font-mono` has zero consumers at
  all despite being defined and self-hosted.
- **Both `.pot` files are badly out of date** — the theme's has 4 entries
  against 349 real strings; the plugin's has 67 against 392. Neither has
  been regenerated since the content model was still small (Aug 2026),
  before `party_publication`, `party_document`, `leaflet`, `hero_section`,
  `masthead_section`, or `editors_pick` existed.

## ❌ Not built at all

- **No language switcher exists in the live site**, not even as inert
  markup. The v6 static prototype had one (`#lang-toggle` button in the
  masthead, plus a footer `FA · EN` line) — neither was ever ported into
  the actual WordPress templates. This needs building from scratch as part
  of this plan, not just "re-activating" something dormant.
- **No `/en/` routing exists anywhere** — no rewrite rules, no query var,
  nothing. This is new infrastructure, covered in
  `03-content-architecture/url-structure-and-routing.md`.
- **No taxonomy-label translation mechanism** — term names are Persian
  strings stored directly on the term, with no English counterpart field.
- **One real existing `[dir="ltr"]` CSS override**: `.masthead--two-tier`
  at `main.css:849`. Worth noting precisely because it's the only place in
  the whole stylesheet anyone already had to think about LTR — a useful
  worked example for the pattern the rest of the plan follows.
