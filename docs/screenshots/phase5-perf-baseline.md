# Phase 5.4 — Performance baseline

Lighthouse 13.4.1, run via `npx lighthouse` against headless Chrome, targeting
the LocalWP homepage (`http://shola-jawid.local/`) with real seeded content.
Run 2026-08-07 (Kabul time), after self-hosting the remaining fonts and
fixing two accessibility bugs the audit itself surfaced (below). Raw reports:
`phase5-perf-baseline.report.html` / `.report.json` in this same folder.

## Scores

| Category | Score |
|---|---|
| Performance | 94 / 100 |
| Accessibility | 100 / 100 |
| Best Practices | 78 / 100 |
| SEO | 100 / 100 |

## Core metrics

- First Contentful Paint: 1.3 s
- Largest Contentful Paint: 3.0 s
- Speed Index: 1.7 s
- Total Blocking Time: 0 ms
- Cumulative Layout Shift: 0.009
- Time to Interactive: 3.0 s
- Total page weight: 1,901 KiB

## Best Practices — 78/100, both misses expected on local dev

The only two failing audits are `is-on-https` and `redirects-http` — this
LocalWP site has no SSL certificate, by design (a local dev environment).
SSL/HTTPS is explicitly Phase 6 scope (`EXECUTION_PLAN.md` §6.3); this score
is expected to reach 100 once the real host has a certificate and HTTP→HTTPS
redirect configured. Not a Phase 5 gap.

## Real bugs the audit surfaced and fixed here, not deferred

Two genuine accessibility bugs were caught by this run — not local-dev
artifacts — and fixed as part of Phase 5.4, since the whole point of running
a baseline audit is to act on what it finds, not just record the number:

- **`aria-hidden-focus`**: `front-page.php`'s hero image link
  (`<a class="hero-media" aria-hidden="true">`) is a deliberate duplicate of
  the real, properly-labeled link on the article title right below it — but
  `aria-hidden="true"` alone doesn't remove a focusable `<a>` from the
  keyboard tab order, so screen-reader/keyboard users landed on an
  "invisible" stop with no way to know what it was. The same pattern existed
  in `template-parts/cards/card.php`'s `.card-media` link (not yet caught by
  this audit, since Lighthouse only scanned the homepage — found by
  searching the codebase for the same structure) and
  `taxonomy-publication.php`'s embedded current-issue `.issue-cover` link.
  Fixed all three by adding `tabindex="-1"` alongside the existing
  `aria-hidden="true"`, so the redundant link is fully excluded from
  assistive-tech interaction instead of half-hidden. The two other
  `.issue-cover` links (`single-issue.php`/`single-document.php`) were
  checked too and are fine as-is — those aren't duplicates of another link,
  they're the only way to reach the PDF, and already carry a real
  `aria-label`.
- **`link-name`**: the same `.card-media` link (a document card with no
  distinguishing alt text on its image) had no discernible accessible name
  at all. Fixed by the same `aria-hidden`+`tabindex="-1"` change above —
  once it's excluded from the accessibility tree entirely, the missing name
  stops mattering, since the real link (the card's title) already provides
  it.

Re-ran Lighthouse after the fix: Accessibility went from 93 → 100, both
audits now pass. Verified live via `curl` on three affected templates
(front page, `taxonomy-publication.php`, a topic archive) that nothing
rendered differently and no PHP errors/warnings appeared — this change is
accessibility-tree-only, no visible or functional difference for a mouse
user.

## Fonts (Phase 5.4 self-hosting)

Newsreader, Inter, and JetBrains Mono (previously loaded from Google Fonts
CDN in `header.php`) are now self-hosted in `assets/fonts/{family}/woff2/`,
alongside Farhang2/ModamPro (already self-hosted since Phase 4.1) — zero
Google Fonts requests remain on any page. Only 4 files needed rather than 6:
fetching each family's CSS confirmed Google serves the same variable-font
file for multiple discrete weights when they fall within one variable
instance's range (confirmed by fetching Inter alone in isolation, not
assumed from the combined request) — declared as a weight range
(`font-weight: 400 600`) in one `@font-face` rule per family/style rather
than duplicating the same `src` across three redundant blocks.

## Responsive images

Confirmed via `curl` (not assumed from reading the code): featured images
already output a full `srcset` with all registered intermediate sizes
(300w/768w/800w/1024w/1536w/1920w observed on the homepage) — no code change
needed, `shola_get_featured_image()` already delegates to
`get_the_post_thumbnail()`, which handles this natively.
