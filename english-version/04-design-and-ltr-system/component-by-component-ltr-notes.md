# Component-by-component LTR notes

This is the actual checklist for Phase 4 of the build
(`05-technical-implementation-plan/phase-by-phase-build-plan.md`). Each
template/component gets visually checked under `dir="ltr"` once real English
content exists, and any exception gets logged here with the fix applied.
This file starts as a checklist, not a finished audit — most of these can
only be properly confirmed by looking at the rendered page, not by reading
CSS in isolation, per the project's own existing "verify live, don't assume"
practice (seen throughout `docs/CHANGELOG.md`).

## Global chrome

- [x] `header.php` masthead — checked live on both languages, both
      masthead layouts (`default` and `two-tier`, the site's actually-
      active one). Found and fixed a real bug this round: the whole
      quick-nav row (Home/Publications/Topics/Library/About/
      Announcements/Contact) was hardcoded Persian HTML, untouched by
      every earlier nav fix — see `docs/CHANGELOG.md` 2026-11-28
      (continued, 6). Persian masthead confirmed byte-identical
      afterward.
- [x] Popup/slide-out menu — the بخش‌ها/بیشتر lists were already correct
      (Phase 1/4); the Topics/Publications sub-lists inside it had the
      same hardcoded-term bug as the quick-nav row, fixed the same round.
- [x] `footer.php` — all columns checked live; same hardcoded-term/
      hardcoded-fallback-menu bug found and fixed in the publications
      list and the `footer_topics`/`footer_site` fallback functions.

## Homepage (`front-page.php`)

- [ ] Whichever hero layout is active (all 7 variants should be checked at
      least once each, even though only one is live at a time, since any of
      them could become active later)
- [ ] Library shelf section
- [ ] Most Viewed panel
- [ ] Editor's Picks panel
- [ ] Translation ("ترجمه") row section
- [ ] Announcement spotlight tiles

## Cards and shared partials

- [x] `template-parts/cards/card.php` — term badge was hardcoded Persian, fixed (Phase 4).
- [ ] `template-parts/cards/issue-card.php`
- [ ] `template-parts/cards/hero-strip-card.php`
- [ ] `template-parts/rows/document-row.php`
- [x] `template-parts/breadcrumb.php` — home-link `/en/` prefix was missing, fixed (Phase 4).
- [ ] `template-parts/search/result.php`

## Single views

- [ ] `single.php` (article) — byline, drop cap (see CSS audit), author
      note box, translation-credit box
- [ ] `single-issue.php` / `single-document.php` / `single-party_publication.php`
      / `single-party_document.php` — PDF preview/download button direction,
      table-of-contents layout
- [ ] `single-announcement.php`

## Archives and listings

- [x] `taxonomy-topic.php` / `taxonomy-publication.php` /
      `taxonomy-collection.php` / `taxonomy-party_document_category.php`
      — all checked live in both languages (Phase 4); term name/link/
      count all fixed to be language-aware.
- [x] `page-library.php` / `page-publications.php` / `page-topics.php` —
      checked live, fixed (Phase 4). `page-party-publications.php` /
      `page-party-documents.php` / `page-reports.php` / `page-selected.php`
      — only HTTP-200 checked, not a full visual pass; `page-party-
      documents.php` was fixed (Phase 4), the other three not yet
      individually confirmed.
- [x] `archive-announcement.php` / `archive-editors_pick.php` — content
      checked live in English, correctly filtered and displayed.
- [x] Pagination controls — arrow characters made direction-aware
      (Phase 3); confirmed live on Persian and English archives.

## Forms and interactive elements

- [ ] `page-contact.php` — CF7 form field alignment, the one existing
      `dir="ltr"` email-address span (already correct, just confirm it
      still reads right next to English body copy)
- [ ] Search form/icon
- [x] Leaflet lightbox viewer — chevron icons needed `scaleX(-1)`
      mirroring beyond the logical-property position flip, fixed
      (Phase 3); demo leaflet confirmed to display correctly in English.

## Login screen

Out of scope for this plan — `login.css` is an admin-facing, Persian-only
screen (the client and staff log in in Persian regardless of which
front-end language a visitor reads). Confirmed not to need an English
variant.
