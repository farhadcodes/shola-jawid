# Phase 4.4 — Per-template visual QC archive

Saved side-by-side comparison record for `EXECUTION_PLAN.md` §4.4's checklist:
every finished WordPress template captured alongside its v6 static-prototype
counterpart. This is the saved record of QC already done live throughout
Phase 4 (Farhad confirming each template against v6 in the browser as it was
built) — not a re-run of that review.

## How these were captured

Headless Chrome (`chrome.exe --headless=new --window-size=1280,2400
--screenshot=...`), not the interactive browser tool — full viewport capture
at 1280px width, tall enough to include most of each page in one shot
without additional scrolling capture. v6 pages captured directly from their
static HTML files (`file://` — no server needed); live pages captured from
the running LocalWP site (`shola-jawid.local`) against real seeded content,
not placeholder text.

## Pairs

Each row is `{name}_v6.png` next to `{name}_live.png`, same numbering as
`EXECUTION_PLAN.md` §4.2's page-to-template map.

| # | Template | v6 source | Live URL |
|---|---|---|---|
| 01 | `front-page.php` | `index.html` | `/` |
| 02 | `page-publications.php` | `publications.html` | `/publications/` |
| 03 | `taxonomy-publication.php` | `publication-shola-jawid.html` | `/publications/shola-jawid/` |
| 04 | `page-topics.php` | `topics.html` | `/topics/` |
| 05 | `taxonomy-topic.php` | `topic-economy.html` | `/topics/economy/` |
| 06 | `page-library.php` | `library.html` | `/library/` |
| 07 | `taxonomy-collection.php` | `library-classics.html` | `/library/classics/` |
| 08 | `archive-announcement.php` | `announcements.html` | `/announcements/` |
| 09 | `page-contact.php` | `contact.html` | `/contact/` |
| 10 | `page-about.php` | `about.html` | `/about/` |
| 11 | `search.php` | `search.html` | `/?s=تورم` (same demo query as v6) |
| 12 | `single.php` | `article-single.html` | a real seeded article (economy topic) |
| 13 | `single-issue.php` | `issue-single.html` | شعله جاوید · شمارهٔ ۳۲ |
| 14 | `single-document.php` | `document-single.html` | دولت و انقلاب (آثار کلاسیک) |
| 15 | `404.php` | *(none — no v6 mockup exists for this state, confirmed by direct search; see `docs/CHANGELOG.md` 2026-08-06)* | any nonexistent URL |

## Status

No unexplained visual drift found. Every template in this archive was
already individually confirmed against its v6 counterpart live, in this
session's Phase 4.2 build-out, by Farhad — this archive is the saved
record `EXECUTION_PLAN.md` §4.4 asks for, not a new finding. Minor content
differences visible between some pairs (hero images, seed-data text length,
real vs. mockup byline names) are expected variation from real vs. demo
content, not template bugs — same distinction already logged throughout
`docs/CHANGELOG.md` for each template's own closing entry.
