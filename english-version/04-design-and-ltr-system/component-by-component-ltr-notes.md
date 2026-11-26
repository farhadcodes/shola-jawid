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

- [ ] `header.php` masthead — logo position, search icon, menu icon,
      masthead-runner text, date display
- [ ] Popup/slide-out menu
- [ ] `footer.php` — all columns, credit line, any social icons with
      directional meaning

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

- [ ] `template-parts/cards/card.php`
- [ ] `template-parts/cards/issue-card.php`
- [ ] `template-parts/cards/hero-strip-card.php`
- [ ] `template-parts/rows/document-row.php`
- [ ] `template-parts/breadcrumb.php` — separator character/icon direction
- [ ] `template-parts/search/result.php`

## Single views

- [ ] `single.php` (article) — byline, drop cap (see CSS audit), author
      note box, translation-credit box
- [ ] `single-issue.php` / `single-document.php` / `single-party_publication.php`
      / `single-party_document.php` — PDF preview/download button direction,
      table-of-contents layout
- [ ] `single-announcement.php`

## Archives and listings

- [ ] `taxonomy-topic.php` / `taxonomy-publication.php` /
      `taxonomy-collection.php` / `taxonomy-party_document_category.php`
- [ ] `page-library.php` / `page-publications.php` /
      `page-party-publications.php` / `page-party-documents.php` /
      `page-reports.php` / `page-selected.php` / `page-topics.php`
- [ ] `archive-announcement.php` / `archive-editors_pick.php`
- [ ] Pagination controls — arrow direction (flagged generally in the CSS
      audit; confirm per-listing here)

## Forms and interactive elements

- [ ] `page-contact.php` — CF7 form field alignment, the one existing
      `dir="ltr"` email-address span (already correct, just confirm it
      still reads right next to English body copy)
- [ ] Search form/icon
- [ ] Leaflet lightbox viewer (if leaflets get an English version — see
      open decisions)

## Login screen

Out of scope for this plan — `login.css` is an admin-facing, Persian-only
screen (the client and staff log in in Persian regardless of which
front-end language a visitor reads). Confirmed not to need an English
variant.
