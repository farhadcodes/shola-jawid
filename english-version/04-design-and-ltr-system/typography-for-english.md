# Typography for English

## The tokens already exist

`main.css`'s `:root` block already defines everything needed:

```
--font-en:      "Newsreader", Georgia, serif;   /* body/reading text */
--font-ui:      "Inter", "Farhang2", ui-sans-serif, sans-serif;  /* UI chrome */
--font-mono:    "JetBrains Mono", ui-monospace, monospace;       /* code/mono, if ever needed */
```

All three font families are already self-hosted in `assets/fonts/` — **zero
new licensing, hosting, or `@font-face` work required.** This was clearly
set up in advance for exactly this project.

## What's missing: applying them document-wide

Today, `--font-en` and `--font-ui` are each used in exactly one CSS rule —
a small `[lang="en"]`/`.latin` escape-hatch selector that only applies to
one specific masthead `<span>` (the brand's English-letter runner text).
`--font-mono` has zero consumers anywhere despite being defined and loaded.

**The fix:** once a page's root `<html>` element carries `lang="en"` and
`dir="ltr"` (which happens automatically via WordPress's own
`language_attributes()` call already in `header.php:27` — no change needed
there, it already pulls from the real site language, not a hardcoded
string), add document-wide rules:

```
html[lang="en"] body { font-family: var(--font-en); }
html[lang="en"] .ui, html[lang="en"] nav { font-family: var(--font-ui); }
```

(Exact selector list to be finalized once the component-by-component pass
in `component-by-component-ltr-notes.md` identifies every place `--font-fa`/
`--font-display`/`--font-nav` currently apply, so each has a clear English
counterpart — not guessed at a glance.)

## Farhang2 and ModamPro — Persian-only fonts, correctly scoped

Farhang2 (primary Persian body font) and ModamPro (nav/brand wordmark font)
are both Persian-script display fonts — they stay exactly as they are for
Persian pages and simply don't apply to English pages once the rules above
exist. No change needed to either font's own `@font-face` definitions.

One verification task (not a build task): confirm whether ModamPro's font
file has any Latin glyph coverage at all — flagged in
`03-content-architecture/open-decisions-needing-client-input.md` — since
its fallback chain already handles the "no Latin coverage" case gracefully
either way.

## Type scale, line-height, measure (line length)

The existing type scale (`--t-micro` through `--t-display`, 9 steps) is
unitless/rem-based and language-agnostic — no changes needed. One thing
worth checking during QA, not assuming: Newsreader (an editorial serif) at
the same point sizes as Farhang2 may read slightly differently for line
length and line-height comfort — a visual QA pass item
(`07-qa-and-launch-checklist/`), not a structural concern.
