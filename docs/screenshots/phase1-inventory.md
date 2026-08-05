# Phase 1.1 — v6 prototype file inventory

Audit only. No files in `03_UI_Design/` were modified while producing this
inventory. Source: `03_UI_Design/shola-jawid-ui/` (outside this repo, per
`CLAUDE.md` — treated as a reference artifact, not the theme itself).

## 1. Top-level generated HTML pages (23)

`about.html`, `announcements.html`, `article-single.html`, `contact.html`,
`document-single.html`, `index.html`, `issue-single.html`,
`library-classics.html`, `library-critique-polemic.html`,
`library-international-movement.html`, `library-party-documents.html`,
`library.html`, `publication-a-world-to-win.html`,
`publication-shola-jawid.html`, `publications.html`, `search.html`,
`topic-afghanistan.html`, `topic-economy.html`,
`topic-international-movement.html`, `topic-science-and-art.html`,
`topic-women.html`, `topic-world.html`, `topics.html`

Matches the IA page-to-template map in the prototype's own README and in
`docs/EXECUTION_PLAN.md` §4.2 — count and names confirmed independently, not
assumed from either document.

## 2. Partials (`pages/_*.html`)

`_shell.html`, `_header.html`, `_menu.html`, `_footer.html` — 4 files, as
documented.

## 3. Body source files (`pages/body-*.html`)

23 files, one per top-level page (the generator's source-of-truth content;
`build.py` assembles `_shell` + `_header` + `_menu` + `body-*` + `_footer`
into the 23 top-level pages). Spot-checked sizes: `body-index.html` 296
lines, `body-article-single.html` 207 lines, `body-topics.html` 32 lines —
sizes vary a lot by page complexity, nothing anomalous.

## 4. Build scripts

- `build.py` — assembles partials + bodies into the 23 final HTML files.
- `pages/gen_bodies.py` — generates the 9 archive-style body pages
  (topic/library variants) from shared templates.

## 5. CSS — `assets/css/main.css`

**1,289 lines**, not the ~900 the prototype's own README claims — the
README is stale on this figure. Not a code problem, just noting the
inventory doesn't match the doc; no action needed.

All **21 numbered sections** present and accounted for. Section-by-section
scope review (concern-mixing check, part of the 1.1 checklist):

| # | Section | Lines | Scope assessment |
|---|---|---|---|
| 01 | Design tokens | 7–204 | Clean. Single concern: `:root` custom properties only. |
| 02 | Reset & base | 205–234 | Clean. |
| 03 | Skip link (a11y) | 235–246 | Clean, single component. |
| 04 | Layout containers | 247–253 | Clean, small. |
| 05 | Masthead | 254–332 | Clean. Single component (`header.php` target). Not dead code — the crimson masthead is the live v6 header treatment, "historical anchor" is a design-intent label, not a deprecation note. |
| 06 | Popup menu | 333–419 | Clean. Single component (the v6 signature move), including the six `--menu-topic--c1..c6` shades — **confirmed already CSS custom-property-driven**, satisfies Phase 1.2's second checklist item ahead of time. |
| 07 | Typography components | 420–499 | Shared typographic primitives (`.h-display`, `.h-card`, `.dek`, `.meta`, `.type-label`, `.section-marker`) used across nearly every template. This is expected to be cross-cutting — it's a type-system layer, not a page region — so "bleeding across templates" here is by design, not a flag. |
| 08 | Links & buttons | 500–588 | Shared component primitives (`.link`, `.btn`, `.tag`, `.badge-*`). Same as above — intentionally cross-cutting, not a concern-mixing issue. |
| 09 | Cards | 589–653 | Clean, and directly relevant to Phase 1.2: single `.card` anatomy with a `.card-mini` variant and two grid wrappers (`.grid-cards`, `.grid-cards-4`). No duplicated per-type card rules found in this section. |
| 10 | Homepage hero | 654–714 | Clean, single-page scope (front-page.php only). |
| 11 | Sections & rules | 715–725 | Clean, tiny utility section. |
| 12 | Article page | 726–931 | Large (206 lines) but all sub-parts (`.article-hero`, `.article-header`, `.article-body`/sidebar split, `.prose`, `.pull-line`, `.article-footer`, `.related-rail`) belong to the single-article template only — one template's concerns, not multiple regions. Not flagged. |
| 13 | Reading progress bar | 932–943 | Clean, single-purpose, correctly scoped to article-single only per its own comment. |
| 14 | Issue / Document single views | 944–1010 | Clean — but see card-partial note below: `.issue-card` (grid variant) is a **separate** card anatomy from `.card`, not a variant of it. Flagged for Phase 1.2 investigation, not fixed here. |
| 15 | Topic index | 1011–1073 | Clean, single template scope. |
| 16 | Announcements list | 1074–1105 | Clean — `.announce-list` is a row/list pattern, not card-based; reasonable that it doesn't reuse `.card`. |
| 17 | Forms | 1106–1150 | Clean, shared form primitives (`.field`, `.label`, `.pagination`). Cross-cutting by design. |
| 18 | Newsletter band | 1151–1177 | Clean, single component. |
| 19 | Footer | 1178–1234 | Clean, single component (`footer.php` target). |
| 20 | Utility helpers | 1235–1262 | Clean, intentionally small per its own comment. |
| 21 | About page tabs | 1263–end | Clean, single-page scope. |

**No sections need splitting.** The two sections flagged above (07, 08) are
intentionally cross-cutting component layers, not evidence of mixed
concerns — this will be re-examined in 1.2/1.3 only if it turns out to
affect the card-partial question.

## 6. JS — `assets/js/main.js`

**85 lines.** Four self-contained behaviors, each gated on the presence of
its target element (`if (!menu) return;` etc.), each independently a no-op
if its markup isn't present on a given page:
1. Popup menu open/close + `Escape` handling + focus return.
2. Language toggle (visual only, per its own comment — "در وردپرس: به آیتم
   متناظر en/ پیوند داده می‌شود").
3. Scroll-reveal via `IntersectionObserver`, degrades to "show everything"
   if unsupported or `prefers-reduced-motion` is set.
4. Reading-progress bar, scoped via `[data-progress-scope]` — only active
   on article-single.

No inline `<script>` blocks referenced by this file; wrapped in an IIFE;
no global pollution observed. Consistent with the README's
progressive-enhancement claim — full verification (site usable with JS off)
is 1.3 scope, not done here.

## 7. Fonts — `assets/fonts/`

Two families, **already self-hosted** in both `.woff` and `.woff2`:
`farhang2` (9 weights) and `modampro` (8 weights). Note: the prototype
README's "Fonts" section describes Vazirmatn/Markazi Text/Newsreader/Inter
as the intended stack, but the CSS tokens (`--font-fa`, `--font-display`,
`--font-nav`) actually reference `"Farhang2"` and `"ModamPro"`, matching
what's physically present in `assets/fonts/`. The README is stale here too;
the CSS + font files are the actual, consistent source of truth and that's
what will carry into the theme. No action needed now — flagging so Phase
5.4 (font self-hosting) enqueues the fonts that actually exist, not the
README's list.

## 8. Images — `assets/images/`

8 placeholder JPGs (protest/market/ruins/archive themed), matches the
prototype README's note that these are Pexels placeholders to be replaced
with real media in the WordPress media library.

## 9. Preliminary card-markup observation (fuller check is Phase 1.2)

Grepped `class="card` across all 23 `body-*.html` source files as a
sanity check ahead of 1.2:

- Uses `.card`/`.card-mini`: `body-index.html` (31), `body-topic-*.html`
  (30 each, ×6), `body-article-single.html` (15, related-rail), `body-
  search.html` (8).
- Zero `.card` uses in `body-library*.html`, `body-publication*.html`,
  `body-announcements.html`, `body-about.html`, `body-topics.html`,
  `body-document-single.html`, `body-issue-single.html` — these appear to
  use other list/row patterns instead (`.issue-card`, `.doc-row`,
  `.announce-list`, `.topic-list`), per the CSS section review above.

This raises the real Phase 1.2 question: is `.issue-card` (CSS §14) a
justified separate anatomy (issues are cover-image-driven, portrait
aspect-ratio, shadowed — genuinely different from the borderless article
card), or should it collapse into the shared `.card` partial with a
modifier? Not answered here — this is exactly what 1.2 is for.
