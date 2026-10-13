# P-1 — Production performance baseline (sholajawid.com)

**Date:** 2026-10-13
**Target:** `https://sholajawid.com/` (live production homepage)

## Why this isn't a formal Lighthouse/PSI report

Two independent attempts at an automated audit both failed, for
different reasons — recorded here instead of silently falling back to
guesses:

1. **`npx lighthouse` (local headless Chrome) → HTTP 403.** A plain
   `curl` against the same URL returns `200` every time; only the
   automated/headless Chrome request gets blocked. This points at
   Wordfence's or LiteSpeed's own bot/automation detection on
   production — a genuinely good security sign (exactly what A4/B5 in
   this plan want configured and firing), but it means Lighthouse's
   own Chrome can't get in either.
2. **Google PageSpeed Insights API → HTTP 429**, `quota_limit_value:
   "0"` for the anonymous/no-API-key tier — Google now requires a
   billed Cloud project + API key for any PSI API usage, which isn't
   something to set up silently.

## What was actually measured instead

Loaded the real production homepage in a genuine interactive browser
(not headless/automated — confirmed it loads fine there, consistent
with the bot-detection theory above) and pulled real values via the
Navigation Timing / Resource Timing APIs directly against the live
page. These are real measured numbers, not estimates:

| Metric | Value |
|---|---|
| TTFB | 290ms |
| DOMContentLoaded | 2.72s |
| `load` event | 6.89s |
| Total transfer (all requests) | 1,109 KB / 24 requests |

### Biggest real finding: font loading

**12 separate font files load on first paint of the homepage, totaling
486 KB** — more than any other asset category, including images:

| File | Size | Load time |
|---|---|---|
| Farhang2-Regular.woff2 | 27 KB | 3.13s |
| Farhang2-DemiBold/Bold/ExtraBold/Black | ~29 KB each | 1.0–1.7s each |
| ModamWeb-Regular/SemiBold/Bold/ExtraBold/Black | ~33 KB each | 1.3–3.5s each |
| Newsreader-Variable.woff2 | 129 KB | 4.30s |
| Inter-Variable.woff2 | 47 KB | 386ms |

Every one of these already has `font-display: swap` (confirmed, P-4),
so text is never invisible — but the sheer number of weights loading
unconditionally on the homepage (5 Farhang2 weights + 5 ModamWeb
weights + 2 variable fonts) is a real, measurable cost. Likely
candidate for a future pass: audit which weights the homepage actually
renders above the fold and whether every one of those 10 static
weights needs to load there, vs. only where used further down the
page.

### Second finding: at least one uncompressed image

`Herat-women-protest-1.png` — **292 KB**, the single largest file on
the page, as an unoptimized PNG. Directly confirms the P-2 gap this
plan already flagged: `Image_Optimizer` (shola-core) currently only
runs on the `leaflet` CPT; this is a regular article featured image,
untouched by it.

## Recommendation

Feeds directly into **P-2** (already on Track 1, next up): extending
the existing upload-time image optimizer beyond the `leaflet` CPT is
the single highest-value fix visible in this data. A font-loading
audit (which weights are actually needed above the fold) is a
reasonable P2-priority follow-up beyond the current plan's scope —
noted here, not added as a new plan item without Farhad's sign-off.
