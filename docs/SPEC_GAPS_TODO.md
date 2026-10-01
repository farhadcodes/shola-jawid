# Spec Compliance — Outstanding Work

Generated 2026-10-01 from a full audit of the codebase against
`09_documentation/مشخصات_فنی_وبسایت_شعله_جاوید_v1.0.docx` (the client's
final, approved technical spec). This file is a working to-do list, not a
historical record — check items off (or delete them) as they're done, and
keep `docs/CHANGELOG.md` as the permanent record of what actually shipped,
per the usual project convention.

## How this file is organized

- **Part A — Decide first.** Nine places where the current site intentionally
  does something different from the spec, almost always because of an
  explicit client request made *after* the spec was written. Pick an answer
  for each before touching the related code, or you'll re-break something
  someone already asked for on purpose.
- **Part B — Safe to implement directly.** Clear, unambiguous gaps with no
  known conflicting decision behind them. Ordered roughly by risk, cheapest/
  safest first.
- **Part C — Full traceability table.** Every spec item, both met and
  unmet, for reference — so nothing gets silently dropped later.

Every item below names the actual file(s) involved and flags anything that
touches **shared code** (a function or component used in more than one
place) — those need extra care and a live check in the browser at
desktop/tablet/mobile before shipping, per the project's own standing rule
in `CLAUDE.md` §5/§10.

---

## Part A — Decide first (conflicts between the spec and an existing client decision)

### A1. گزیده vs. ترجمه — are these one unit or two?

The spec (بخش یکم، ج و د) describes **two separate content units**:
- گزیده — same fields as گزارش, including multi-author support.
- ترجمه — its own unit, with original-author, translator, and alias fields.

On 2026-09-27 (this session), the old "گزیده‌ها" postmeta flag
(`shcore_is_selected`) was **renamed in place** to "ترجمه", and the
translation fields (`shcore_translation_original_author`,
`shcore_translation_translator`) were added to that same renamed box. A
separate گزیده unit no longer exists anywhere in the code.

**Decide:** was that rename/merge actually what the client wants long-term
(i.e. the spec's two-unit design is now outdated), or does a real, separate
گزیده unit need to be rebuilt alongside ترجمه? This affects a lot of
downstream work (A1 blocks item A3, and parts of B-items on multi-author
support), so settle this one first.

- Files: `wp-content/plugins/shola-core/includes/class-meta-fields.php`
  (metabox `shcore_selected_field`, function `render_selected_metabox()`),
  `wp-content/themes/shola-jawid/page-selected.php`,
  `wp-content/themes/shola-jawid/front-page.php` (ترجمه homepage section).

### A2. گزارش homepage count: spec says 6, site shows 4

Changed explicitly on 2026-09-18 at the client's own request (see
`docs/CHANGELOG.md`, that date). The spec document predates or wasn't
updated after that conversation.

**Decide:** keep 4 (client's explicit later instruction) or revert to 6 to
match the written spec. Recommend keeping 4 unless the client says
otherwise — a later, explicit verbal decision normally supersedes an
earlier written spec, but confirm rather than assume.

- File: `wp-content/themes/shola-jawid/front-page.php` (`$reports_query`,
  `posts_per_page => 4`, two places — the query args and the hard-cap
  loop counter just below it).

### A3. What belongs in the column under اطلاعیه‌ها on the homepage?

Spec wants a گزیده list (title + date) there. The site currently shows
"پربازدیدترین" (Most Viewed), added 2026-09-15 at the client's own request,
with a code comment noting a future plan to move Most Viewed to the گزارش
section instead.

**Decide:** which content actually belongs in that slot — and this is
downstream of A1 (there's no گزیده unit to pull from right now even if you
wanted to put it back).

- Files: `wp-content/themes/shola-jawid/front-page.php`
  (`$most_viewed_query`, `$has_mostviewed`),
  `wp-content/themes/shola-jawid/template-parts/cards/most-viewed-panel.php`.

### A4. ترجمه homepage card layout: rows or cards?

Spec describes image-on-top vertical cards. The current implementation
(`selected-row.php`) renders horizontal rows — image beside the text, not
above it.

**Decide:** is the current row layout an acceptable interpretation, or does
it need to become vertical cards like the مقالات/گزارش grids? This is a
visible design change, not just a content fix — get a design-level
confirmation, not just a yes/no, before touching it.

- Files: `wp-content/themes/shola-jawid/template-parts/cards/selected-row.php`,
  `wp-content/themes/shola-jawid/assets/css/main.css` (`.selected-row`,
  `.selected-list` rules, search for `selected-row`).

### A5. جهان برای فتح has 4 دوره sub-terms it shouldn't have

The spec says only شعله جاوید has sub-collections (4 دوره); جهان برای فتح
has none. The seed code currently creates all 4 دوره terms under **both**
publications.

**Decide:** delete the 4 دوره terms under جهان برای فتح (a content/taxonomy
change, not a code change — do this via wp-admin, not by editing the seed
function, since the seed only runs once and editing it won't retroactively
fix already-seeded terms). **Before deleting:** check whether any issue is
currently assigned to one of those terms and reassign it first (the
category-move-before-delete flow already exists in admin for this).

- Reference: `wp-content/plugins/shola-core/includes/class-taxonomies.php`
  function `seed_publication_periods()` — fix this too, so a fresh install
  doesn't repeat the mistake, but that alone won't fix the live site.

### A6. کتابخانه "ویراستار" (editor) — one fixed name, or per-book?

Spec wants a per-book editor field. The site currently hard-codes one name
("م. صالح") via `shola_get_managing_editor()`, shown on every book.

**Decide:** make it a real per-book admin field. **Also decide:** since the
spec's security rule says author/translator names shouldn't show publicly
yet, does "ویراستار" count as the same kind of name, and should it also be
hidden, or is it intentionally exempt (it's currently shown)? Don't assume
either way — ask.

- Files: `wp-content/themes/shola-jawid/inc/template-tags.php`
  (`shola_get_managing_editor()`), `wp-content/plugins/shola-core/includes/class-meta-fields.php`
  (`document` post type fields), `wp-content/themes/shola-jawid/single-document.php`.

### A7. تراکت info-panel side: same on both, or mirrored?

Spec wants the info panel on the right for the homepage lightbox and on the
left for the archive gallery. Both currently use the same CSS rule and
render on the right in both places.

**Decide:** confirm this is actually wanted (easy to misjudge "left/right"
from a text spec in an RTL site) before changing it — low risk either way,
but a 5-minute confirmation avoids a wasted CSS change.

- File: `wp-content/themes/shola-jawid/assets/css/main.css`, search for the
  shared leaflet lightbox rule (`row-reverse`, near the leaflet/lightbox
  section, roughly line 5160+ as of this audit — re-check the line number
  before editing since the file has moved since).

### A8. Possible REST API leak of hidden names — verify live before trusting the "hidden" status

All the "must not show publicly" fields (`shcore_byline`,
`shcore_translation_original_author`, `shcore_translation_translator`) are
registered with `show_in_rest => true`. They're confirmed absent from every
theme template, but WordPress's default REST API
(`/wp-json/wp/v2/posts/<id>`) may still expose them to anyone, logged in or
not — a template-level hide doesn't block the REST endpoint.

**Action, not really a decision:** check this live — visit
`https://sholajawid.com/wp-json/wp/v2/posts?per_page=1` (or a specific
post's `/posts/<id>`) in a browser and look for these fields in the `meta`
object of the JSON response. If present, the fix is to set
`show_in_rest => false` on those three `register_post_meta()` calls (or
add an explicit `auth_callback` that denies read access to logged-out
users) in `class-meta-fields.php` — this is a small, low-risk fix once
confirmed, but confirm first rather than changing REST visibility
speculatively.

### A9. کتابخانه auto-scroll direction — confirm, don't just trust the literal translation

Spec says "left to right" for the library shelf's auto-advance. The
current implementation does move that direction in this RTL layout, but
it's worth getting an explicit "yes, that's what I meant" rather than
trusting a possibly-ambiguous translated phrase, given how easy this is to
get backwards in RTL. No code change expected here unless the answer is
"no."

---

## Part B — Safe to implement directly (no known conflicting decision)

Ordered roughly cheapest/lowest-risk first. Each one names the exact file
and flags shared-code impact.

### ✅ B1. Footer copyright year is hard-coded — DONE 2026-10-01

Fixed: `footer.php` now computes the year live via `wp_date('Y')`. See
`docs/CHANGELOG.md` 2026-10-01.

### ✅ B2. Contact page email is hard-coded, not admin-editable — DONE 2026-10-01

Fixed: added `shcore_contact_email` option to `SholaCore\Contact_Settings`
(Settings → موضوعات فرم تماس), `page-contact.php` now reads it via
`get_email()`. See `docs/CHANGELOG.md` 2026-10-01.

### ✅ B3. تراکت: add the missing description field — FIELD DONE 2026-10-01

Fixed: `leaflet` CPT now supports `editor`, so the field exists in admin.
See `docs/CHANGELOG.md` 2026-10-01.

**Still open, deliberately not done yet:** whether/where this
description should actually *display* on the front end (homepage
teaser, archive, both, neither) — a separate decision, not part of this
fix. If display is wanted later, remember the **shared-code note**: the
leaflet teaser markup/lightbox is shared between `front-page.php` and
`page-leaflets.php` — add it once, in the shared template part, not
duplicated in both places.

### ✅ B4. Article subtitle field (عنوان فرعی) missing for `post` — DONE 2026-10-01

Fixed: `shcore_subtitle` extended to `post`, displayed in single.php via
the existing `.article-subtitle` class (not the PDF types' `--doc`
title-size modifier, which was unrelated to this). See
`docs/CHANGELOG.md` 2026-10-01 for full detail, including the new
`.article-hero-visual .article-subtitle` legibility override.

### ✅ B5. Admin field help text: too short / missing on several fields — DONE 2026-10-01, revised same day

Fixed: added previously-missing descriptions (ترجمه name fields, TOC
intro's «بخش» explanation) and restriction lines (PDF format, ترجمه
names not shown publicly yet). **Note:** initially expanded to the
spec's own "15–25 words" wording, but Farhad reviewed it live and
asked for true one-liners instead (~9–14 words) — a deliberate,
approved deviation from his own spec document, not an oversight. Both
passes are in `docs/CHANGELOG.md` 2026-10-01. Longer, pre-existing
multi-option explanations (hero/masthead layout pickers, etc.)
deliberately left alone throughout.

### ✅ B6. نشریات: issue table-of-contents missing its page-number column — DONE 2026-10-01

Fixed: added a `page` field to the `shcore_contents` repeater (admin
column/input, get/sanitize functions) and a "صفحهٔ N" display line on
single-issue.php. Turned out the old "no per-entry page count" code
comment was about not faking a WP permalink for TOC entries, not about
rejecting a plain page-number field — checked the actual project
history before implementing rather than assuming a conflict. See
`docs/CHANGELOG.md` 2026-10-01 for the full reasoning and verification.

### ✅ B7. نشریات: no custom/optional issue title — title gets hard-replaced — DONE 2026-10-01

Fixed: added `shcore_issue_custom_title`, an optional text field on the
issue metabox, plus a new shared helper
`shola_get_issue_display_title()` (inc/template-tags.php) that every
template now calls instead of each keeping its own copy of the
"شمارهٔ N" fallback — single-issue.php's H1, taxonomy-publication.php's
H2/H3 (which disagreed with each other before this fix), and
issue-card.php (for `issue`-type posts only). front-page.php's نشریات
section and the two hero-publication-card helpers were checked and left
alone — they show the publication name, not the issue's title, by
deliberate prior design. See `docs/CHANGELOG.md` 2026-10-01 for the
full per-template reasoning and verification.

### B8. نشریات: no description field, no topic/دوره classification shown as a real taxonomy pick in one place

The `issue` CPT has no `editor` support (so no description), and دوره is
recorded twice — once as a real taxonomy child term, once as a free-text
field (`shcore_volume`) that doesn't actually read from the term.

- Files: `wp-content/plugins/shola-core/includes/class-post-types.php`
  (`issue` CPT `supports` array, add `'editor'`), `class-meta-fields.php`
  (consider whether `shcore_volume` should become a read-only display of
  the actual assigned دوره term instead of a separate free-text field —
  this removes the redundancy the audit flagged, but confirm with Farhad
  whether any existing issues rely on `shcore_volume` having a value that
  doesn't match their actual term before collapsing the two, since that
  could silently change displayed text on existing content).

### B9. کتابخانه: homepage archive page hard-codes 4 collection slugs

`page-library.php` lists 4 specific collection slugs by name — a new
collection added in admin won't show up there automatically.

- File: `wp-content/themes/shola-jawid/page-library.php`.
- Fix shape: replace the hard-coded slug list with a real `get_terms()`
  query against the `collection` taxonomy (same pattern already used for
  نشریات's publication terms in `front-page.php`, or اسناد حزب's group
  listing) — low risk, but **test with the existing 4 collections first**
  to confirm the dynamic query returns them in the same order/grouping
  before relying on it, since collection ordering may currently depend on
  the hard-coded list's order.

### B10. Language field (`shcore_language`) stored but never displayed

Affects both انتشارات حزب and کتابخانه.

- Files: `wp-content/themes/shola-jawid/single-party_publication.php`,
  `single-document.php` — add a simple display row next to the existing
  metadata (file size, date, etc.), matching their existing layout
  pattern. Zero risk, purely additive, no shared code.

### B11. Breadcrumbs missing on several archive pages

Spec requires a breadcrumb on every section that has an archive. Missing
on: `taxonomy-topic.php`, `page-reports.php`, `page-selected.php`,
`page-party-publications.php`/`page-publications.php`,
`archive-announcement.php`, `page-leaflets.php`, `page-topics.php`,
`search.php`.

- **Reuse the exact breadcrumb markup/classes already used** in
  `page-library.php` or `single.php` rather than inventing new markup —
  this is a repetitive, mechanical change across many files, which is
  exactly where copy-paste drift causes inconsistency, so build it once as
  a shared template part (e.g. `template-parts/breadcrumb.php`) if one
  doesn't already exist, and include it from each page, rather than
  pasting the same markup into 8 files separately.
- **Do this after** settling A1–A4 above, since some of these archive
  pages (گزیده/ترجمه, گزارش) may still be in flux.

### B12. Pagination inconsistency across archives + search page gaps

Current per-page values vary (6, 9, 12, 20, unset) with no stated 20-item
threshold enforced; search has no 30-per-page override and only
bottom-side pagination.

- Files: every `page-*.php`/`taxonomy-*.php`/`archive-*.php` archive
  template (see the full list in the audit above), plus
  `wp-content/themes/shola-jawid/search.php`.
- **Decide first (not a conflict, just undecided):** does the spec's "20
  items" mean every archive should be unified to exactly 20, or is the
  20 just a trigger threshold ("don't show more than 20 unpaginated") that
  different sections can still tune differently (e.g. گزارش's current 6
  isn't necessarily wrong, it's just a smaller intentional page size)?
  Recommend treating existing intentional sizes (6 for گزارش section,
  matching its homepage count) as fine, and only fixing the ones with
  **no pagination control at all** (اطلاعیه‌ها archive, search) plus
  adding search's 30-per-page + top-and-bottom pagination explicitly.
- **Search page specifically:**
  1. Confirm live whether an empty `/?s=` query currently lists everything
     (likely, per the audit) — if so, add an early return/empty-state in
     `search.php` when the query string is blank, matching the pattern
     other "show nothing until X" templates already use (check `category.php`
     or similar native templates aren't relied upon elsewhere first — this
     is `search.php` only, shared by nothing else, so this change is safe
     and isolated).
  2. Add `'posts_per_page' => 30` to the search query args.
  3. Duplicate the existing bottom pagination block above the results too
     (same `paginate_links()` call, rendered twice) — this is the same
     "render the same pagination markup twice" pattern already used
     elsewhere in the codebase (e.g. the sidebar components), not a new
     technique.

### B13. دربارهٔ ما: hard-coded H1 and tab navigation

Page title and tab nav don't pull from the actual WP Page content/title.

- File: `wp-content/themes/shola-jawid/page-about.php`.
- Fix shape: use `the_title()` for the H1 instead of a literal string; the
  tab navigation is a bigger question — confirm with Farhad whether the
  tabs themselves need to become admin-editable or just the heading before
  touching that part, since the spec text for this page is fairly minimal
  (title + logo + editable content) and doesn't obviously require
  editable tabs at all.

### B14. دربارهٔ ما: no party-logo slot in the template

- File: `wp-content/themes/shola-jawid/page-about.php`.
- Fix shape: a small, new image field (theme mod or a `shola-core` option,
  consistent with how B2's contact-email setting should be built — ideally
  build both as part of the same small "site settings" admin screen if one
  doesn't exist yet, rather than two unrelated one-off options).

### B15. Contact page: title/description hard-coded; response/privacy note split into two lines instead of one

- File: `wp-content/themes/shola-jawid/page-contact.php`.
- Low risk, cosmetic/content changes only, no shared code.

---

## Part C — Full traceability (every spec item, for reference)

Status key: ✅ done · ⚠️ partial (see Part B/A for the specific gap) ·
❌ missing (see Part B) · 🔶 conflict (see Part A).

| # | Spec item | Status | See |
|---|---|---|---|
| 1 | مقاله core fields | ⚠️ | B4 |
| 2 | موضوع اصلی + breadcrumb | ✅ | — |
| 3 | Subtitle display rule | ❌ | B4 |
| 4 | Author/alias hidden publicly | ✅ (verify REST) | A8 |
| 5 | گزارش multi-author | ❌ | A1 |
| 6 | گزیده multi-author | ❌ | A1 |
| 7 | ترجمه multi-value author/translator + alias | ❌ | A1 |
| 8 | ترجمه fields hidden publicly, exist in admin | ⚠️ (verify REST) | A8 |
| 9 | ترجمه single- vs multi-value | ❌ | A1 |
| 10 | نشریات 2 parents, شعله جاوید has 4 دوره only | ⚠️ | A5 |
| 11 | Issue fields (13) | ⚠️ | B6, B7, B8 |
| 12 | نشریات taxonomy structural rules | ⚠️ | — (fallback-term caveat, low priority) |
| 13 | انتشارات حزب fields | ⚠️ | B10 |
| 14 | کتابخانه fields incl. ویراستار | ⚠️ | A6, B10 |
| 15 | کتابخانه taxonomy/archive | ⚠️ | B9 |
| 16 | اسناد حزب fields | ✅ | — |
| 17 | اسناد حزب groups | ✅ | — |
| 18 | اطلاعیه‌ها fields | ✅ | — |
| 19 | تراکت fields | ❌ | B3 |
| 20 | تراکت display rules | ⚠️ | A7, B12 (archive pagination) |
| 21 | Admin hint text length | ⚠️ | B5 |
| 22 | Breadcrumbs everywhere | ⚠️ | B11 |
| 23 | Pagination threshold | ⚠️ | B12 |
| 24 | Search behavior | ❌ | B12 |
| 25 | Jalali calendar everywhere | ✅ | — |
| 26 | پست ویژه | ✅ | — |
| 27 | پست نشریه | ⚠️ | — (caption-field scope mismatch, low priority) |
| 28 | مقالات section | ⚠️ | — (excerpt ellipsis character, low priority) |
| 29 | اطلاعیه‌ها column | ✅ | — |
| 30 | گزیده under اطلاعیه‌ها | 🔶 | A3 |
| 31 | تراکت section | ✅ | — |
| 32 | گزارش section count | 🔶 | A2 |
| 33 | نشریات section | ⚠️ | B7 (heading/title), download-link target |
| 34 | ترجمه section layout | 🔶 | A4 |
| 35 | انتشارات حزب section | ✅ | — |
| 36 | کتابخانه section | ✅ | — |
| 37 | اسناد حزب section | ✅ | — |
| 38 | همه موضوعات | ✅ | — (nav-menu dependency noted, low priority) |
| 39 | Footer/copyright | ⚠️ | B1 |
| 40 | Header | ✅ | — |
| 41 | دربارهٔ ما | ⚠️ | B13, B14 |
| 42 | ارتباط با ما | ⚠️ | B2, B15 |
| 43 | جست‌وجو | ❌ | B12 |
