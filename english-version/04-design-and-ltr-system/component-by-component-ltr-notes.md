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

- [x] Hero — all 7 layouts share one function
      (`shola_render_hero_body()`) for the topic badge; fixed once,
      applies to all. The two publication-card renderers 4 of the 7
      layouts also use were fixed the same round. Not individually
      screenshotted per layout (only `feature_card`, the currently-active
      one, was visually confirmed), but the fix is structural, not
      per-layout markup.
- [x] Library shelf section — checked live, correct.
- [x] Most Viewed panel — N/A on the homepage itself (that panel lives on
      `single.php`, see below); homepage has no separate instance.
- [ ] Editor's Picks panel
- [ ] Translation ("ترجمه") row section
- [x] Announcement spotlight tiles — checked live, correct (demo
      announcement shows correctly, card.php's own fix applies).
- [x] «نشریات» current-issue panel (not in the original checklist, found
      during this pass) — `$pub_term->name`/`get_term_link()` was
      hardcoded Persian; fixed, see `docs/CHANGELOG.md` 2026-11-28
      (continued, 7).

## Cards and shared partials

- [x] `template-parts/cards/card.php` — term badge was hardcoded Persian, fixed (Phase 4).
- [ ] `template-parts/cards/issue-card.php`
- [ ] `template-parts/cards/hero-strip-card.php`
- [x] `template-parts/rows/document-row.php` — collection-name label was
      hardcoded Persian, fixed.
- [x] `template-parts/breadcrumb.php` — home-link `/en/` prefix was missing, fixed (Phase 4).
- [x] `template-parts/search/result.php` — term name/link was hardcoded
      Persian, fixed; confirmed live with a real English search result.

## Single views

- [x] `single.php` (article) — breadcrumb, related-topics tag list, and
      the "Most Viewed"/"مطالب دیگر" related-content queries were all
      either hardcoded Persian or completely unfiltered by language
      (showing Persian content under an English article); all fixed and
      verified live. Native `post_tag` (`برچسب‌ها`) deliberately left
      unfixed — no bilingual mechanism exists for native WP tags, a
      separate, larger decision. Drop cap/author-note-box/translation-
      credit-box not individually re-checked this round (no demo content
      currently exercises those specific blocks).
- [x] `single-issue.php` / `single-document.php` / `single-party_publication.php`
      / `single-party_document.php` — breadcrumbs and "related items"
      queries all had the same two bugs, all fixed and verified live.
      PDF button direction/TOC layout not separately re-checked (no LTR-
      specific issue found or expected there — logical properties already
      handle it per the Phase 3 CSS audit).
- [x] `single-announcement.php` — same two bugs (breadcrumb, unfiltered
      related-announcements query), fixed and verified live.

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
