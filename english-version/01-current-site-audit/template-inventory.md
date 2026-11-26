# Template inventory — the Persian site as it exists today

Every template file is listed here with its purpose, so the implementation
plan can say exactly which files get touched for the English version and
which don't. Source: direct read of `wp-content/themes/shola-jawid/`,
2026-11-26.

## Theme root templates

| File | Purpose |
|---|---|
| `header.php` | `<head>` open + masthead + popup menu |
| `footer.php` | Footer markup + closing tags |
| `front-page.php` | Homepage |
| `index.php` | Required WP fallback (bare loop, no dedicated design — should never actually render) |
| `single.php` | Single article/note (native `post`); also reused for `editors_pick` |
| `single-issue.php` | Single issue (PDF-only) |
| `single-document.php` | Single library document (PDF preview + download) |
| `single-party_publication.php` | Single party-publication item (PDF, no author/editor row) |
| `single-party_document.php` | Single party-document item (PDF + serial number + category) |
| `single-announcement.php` | Single announcement detail |
| `archive-announcement.php` | Announcements archive |
| `archive-editors_pick.php` | Editor's Picks archive |
| `taxonomy-topic.php` | Article archive for the `topic` taxonomy (shared by all 9 terms) |
| `taxonomy-publication.php` | Archive for the `publication` taxonomy (branches on parent vs. دوره child term) |
| `taxonomy-collection.php` | Document archive for the `collection` taxonomy (shared by all 4 terms) |
| `taxonomy-party_document_category.php` | Archive for party-document categories |
| `page-about.php` | About page (slug `about`) |
| `page-contact.php` | Contact page (slug `contact`, CF7 form) |
| `page-library.php` | Library listing (slug `library`) |
| `page-publications.php` | Publications listing (slug `publications`) |
| `page-party-publications.php` | Party Publications listing (slug `party-publications`) |
| `page-party-documents.php` | Party Documents listing (slug `party-documents`) |
| `page-leaflets.php` | Leaflets full archive |
| `page-reports.php` | Reports full archive (slug `reports`) |
| `page-selected.php` | Translation ("ترجمه") full archive (slug `selected`) |
| `page-topics.php` | Topics listing (slug `topics`) |
| `search.php` | Search results, across all content types |
| `404.php` | Not-found page |

## `template-parts/`

| File | Purpose |
|---|---|
| `breadcrumb.php` | Shared breadcrumb nav |
| `cards/card.php` | Shared borderless card, default article display |
| `cards/issue-card.php` | Portrait-cover grid thumbnail |
| `cards/library-shelf-card.php` | Cover-only shelf card, homepage library section |
| `cards/most-viewed-panel.php` | "Most Viewed" panel |
| `cards/editors-picks-panel.php` | Editor's Picks panel |
| `cards/selected-row.php` | One row of the Translation section |
| `cards/announcement-spotlight.php` | Accent-colored announcement tile |
| `cards/hero-strip-card.php` | Compact thumbnail for the filmstrip hero layout |
| `leaflets/leaflet-item.php` | One entry in the leaflets stream |
| `leaflets/lightbox.php` | Fullscreen leaflet viewer |
| `masthead/two-tier.php` | One masthead layout variant |
| `rows/document-row.php` | Shared document list row |
| `search/result.php` | One search-result item |

## What this means for the English version

Every one of these files renders Persian strings only through `__()`/`_e()`
calls — meaning **none of these files need to be duplicated or forked for
English.** The plan in `05-technical-implementation-plan/` is built around
making these same files language-aware (what content they pull, which
labels they print), not writing a second copy of each one. A second,
parallel template tree would be the wrong approach — see
`02-architecture-decision/approach-and-alternatives.md`.
