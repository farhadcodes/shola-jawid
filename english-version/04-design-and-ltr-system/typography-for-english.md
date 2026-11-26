# Typography for English

## The decision (2026-11-28): same fonts as Persian, no font switch at all

Resolved by Farhad: Farhang2 (body/display) and ModamPro (nav/brand
wordmark) are used for English pages exactly as they already are for
Persian — purchased and confirmed as full Persian+English typefaces.
Verified directly before accepting the claim: rendered plain Latin text
in each font with no fallback in the stack, live in the browser — both
produced real, distinct, fully legible Latin letterforms and numerals,
no missing glyphs, no silent substitution.

**This means Phase 3 needs zero font-family changes.** No
`html[lang="en"] body { font-family: ... }` override, no separate
English font stack — the site's one existing default font stack already
applies correctly regardless of a page's language, since the same fonts
are correct for both.

## What this replaces

An earlier draft of this plan assumed English pages would switch to the
already-self-hosted `--font-en` (Newsreader)/`--font-ui` (Inter) tokens,
based on the (reasonable, but since overridden) assumption that Farhang2/
ModamPro were Persian-script-only display fonts. That assumption was
checked directly rather than left as a guess once Farhad gave the actual
font direction — confirmed wrong. Inter/Newsreader/JetBrains Mono remain
self-hosted and are untouched by this — they're still used exactly where
they already were (the one existing `[lang="en"]`/`.latin` escape-hatch
selector on the masthead's brand-runner `<span>`, a separate, pre-existing
detail unrelated to this project), just not adopted more broadly as part
of the English rollout.

## What actually needs checking in Phase 3

Not font-family — spacing/rhythm. The existing type scale
(`--t-micro` through `--t-display`, 9 steps) is unitless/rem-based and
already language-agnostic, so no scale changes are expected. Worth
confirming during the QA pass (`07-qa-and-launch-checklist/`), not
assumed either way: Farhang2 at the same point sizes/line-heights tuned
for Persian script may read slightly tighter or looser for Latin text
specifically (different scripts commonly need slightly different
line-height for comfortable reading) — a visual check against real
English content once some exists, not a structural concern to solve
speculatively now.
