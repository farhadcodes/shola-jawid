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

### ⚠️ B8a. نشریات: no description field — EDITOR SUPPORT DONE 2026-10-01, 8b still open

Checked the actual spec text (09_documentation docx) before implementing:
the issue has 13 fields, and «توضیحات» ("space for a description about
this issue") is a genuinely separate field from «چکیده» (excerpt,
already native `post_excerpt`) and «توضیح کارت صفحهٔ اصلی» (the homepage
card blurb, already `shcore_hero_pub_description`) — not a duplicate of
either. Fixed: added `'editor'` to the `issue` CPT's `supports` array
(class-post-types.php), same admin-only pattern as B3's leaflet
`'editor'` support — the spec doesn't name a front-end display spot for
this one, unlike the other two description fields. See
`docs/CHANGELOG.md` 2026-10-01.

### ✅ B8b. نشریات: دوره recorded twice, and the two copies genuinely disagreed on real content — DONE 2026-10-01

Found 5 of 11 issues where `shcore_volume` (free text) actively
disagreed with the issue's real دوره taxonomy term (e.g. issue #26 said
«دورهٔ سوم» while its real term was «دورهٔ اول») — already-live wrong
data on the homepage, not a theoretical risk. Presented both fix
options and the specific issue IDs to Farhad; he chose collapsing to
the real taxonomy term. Removed `shcore_volume` entirely
(class-meta-fields.php — registration, admin field, save whitelist);
the issue metabox now shows the real assigned دوره term read-only
instead, with a note to change it via the "نشریه" taxonomy box.
`front-page.php` now reads the real term directly. Verified all 5
previously-mismatched issues now resolve correctly (including #188,
which has no دوره term — its row correctly disappears rather than
showing stale data), live at desktop/tablet/mobile. See
`docs/CHANGELOG.md` 2026-10-01 for the full per-issue verification.

### ✅ B9. کتابخانه: homepage archive page hard-codes 4 collection slugs — DONE 2026-10-01

Fixed: added `shola_get_library_collections()` (inc/template-tags.php),
a real `get_terms()` query against the `collection` taxonomy, same
shared-helper pattern as `shola_get_party_document_subsections()`.
Replaces the hard-coded slug array in both `page-library.php` (tile
list) and `taxonomy-collection.php` (cross-collection nav strip) — the
second file had its own independent copy of the same hardcoded list,
not mentioned in the original gap description, found while grepping
for every usage before fixing just one.

Testing the existing collections first (per this item's own caution)
surfaced a real, already-live bug: the old list included `party-documents`,
a slug with no matching term (اسناد حزب was split into its own CPT/
taxonomy at some point; this list was never updated). `get_term_by()`
silently returned false for it and the loop skipped it — the public
site was already only ever rendering 3 tiles, not 4. This fix doesn't
change that (still 3 real terms), it just stops silently depending on a
slug that doesn't exist. **Flagged separately to Farhad:** the page's
dek text still says "چهار مجموعه... اسناد رسمی حزب" (four collections,
naming the one that doesn't exist) — a copy fix, not a code fix, left
for his decision on the wording.

### ⏸️ B10. Language field (`shcore_language`) stored but never displayed — DEFERRED 2026-10-01

Checked actual data before implementing: queried every post of all 4
types that carry this field (document, party_publication,
party_document, post) — 30 items total, **100% marked «fa»**, 0 marked
«en». Displaying "زبان: فارسی" would be pure redundant noise on every
single page site-wide right now, not a useful addition — flagged this
to Farhad rather than implementing the literal spec line as-is.
Presented three options (display now anyway / defer until real English
content exists / display only when non-default); Farhad chose to defer.

No code changed — the field itself is untouched and still saved
normally in the admin; this is purely about not adding a front-end
display for it yet. Revisit once bilingual content is actually being
published (per CLAUDE.md §1, that's explicitly out of scope for the
current phase anyway).

- Files, once revisited: `wp-content/themes/shola-jawid/single-party_publication.php`,
  `single-document.php` — add a simple display row next to the existing
  metadata (file size, date, etc.), matching their existing layout
  pattern.

### ✅ B11. Breadcrumbs missing on several archive pages — DONE 2026-10-01

Fixed: built `template-parts/breadcrumb.php`, a shared part (same
`.article-crumb` markup/classes every existing hand-written breadcrumb
already uses), and added it to all 8 pages that had none —
`taxonomy-topic.php` (3-level: Home / موضوعات / {topic}), `page-reports.php`,
`page-selected.php`, `page-party-publications.php`, `page-publications.php`,
`archive-announcement.php`, `page-leaflets.php`, `page-topics.php` (all
2-level: Home / {section}), and `search.php` (Home / جست‌وجو, rendered
as inert text — search has no single fixed canonical URL to self-link
to, unlike every other page here). Pre-existing hand-written breadcrumbs
were deliberately left as-is, not migrated to the new shared part —
kept this fix scoped to the actually-missing ones rather than touching
11+ already-shipped, working templates.

Proceeded despite this item's own note to wait for A1–A4 to settle: a
breadcrumb is just "Home / Section Name" navigation, independent of
whatever those still-open content decisions end up changing about
گزیده/ترجمه/گزارش's layout or terminology.

### ✅ B12. Pagination inconsistency across archives + search page gaps — DONE 2026-10-01

Checked the actual spec text before deciding the "20 items" question:
it's a general rule ("هرگاه مطالب یک صفحه از ۲۰ مورد بیشتر شد، بقیه به
صفحات بعدی تقسیم شوند") — a trigger threshold, not a mandate to unify
every archive to exactly 20. Confirmed every existing explicit per-page
value already sits at or under 20 (6, 9, 12, 20), so those were left
untouched, matching this item's own recommendation.

Also corrected an unsourced number from this gap's own earlier
description: search's page size is now **20**, not 30 — the "30" had
no basis anywhere in the actual spec text; 20 is the one number the
spec actually states.

- **Fixed the two archives with no explicit pagination control**
  (previously silently inheriting Settings → Reading's global default):
  `archive-announcement.php` and `search.php`, both now explicitly
  capped at 20 via two `pre_get_posts` hooks in class-post-types.php
  (`set_announcement_archive_posts_per_page()`, and a `posts_per_page`
  line added to the existing `include_cpts_in_search()`).
- **Confirmed live, then fixed:** an empty `/?s=` query was matching
  WordPress's own search SQL trivially and listing all 41 posts on the
  site, not "no results" — `search.php` now shows "برای دیدن نتیجه،
  عبارتی را در کادر بالا وارد کنید." instead of running the results
  loop when the query string is blank.
  Verified via a real browser search returning 41 results for «ا»:
  capped correctly at 20/page across 3 pages, page 2 confirmed showing
  the next 20. Top-and-bottom pagination confirmed live at desktop,
  tablet, and mobile (same computed link set rendered twice, so the two
  copies can't drift apart), correct RTL, no regressions. Empty-query
  guard and the announcement archive's new 20-cap both confirmed via a
  WP-bootstrap script and a live visit (8 published announcements — too
  few to show pagination either way, but the hook logic itself verified
  directly).

### ✅ B13. دربارهٔ ما: hard-coded H1 — DONE 2026-10-01 (tabs confirmed out of scope)

Checked the actual spec text first to answer this item's own open
question: دربارهٔ ما's spec lists exactly three admin-editable parts —
عنوان صفحه (title), لوگوی حزب (logo, tracked separately as B14), and
the content body. No mention of tabs anywhere, so the tab nav stays
structural/hardcoded chrome, not another instance of this gap — nothing
to confirm with Farhad, the spec already answers it.

Fixed: the H1 (`page-about.php`) now reads the real WP Page title via
`get_the_title( get_queried_object_id() )` instead of a hardcoded
string left over from the Phase D organization-name rename. The real
Page title ("دربارهٔ شعله جاوید") differs from that old hardcoded text
("دربارهٔ حزب کمونیست (مائوئیست) افغانستان") — this is a real, visible
change, not just a code-quality fix; flagged to Farhad rather than
treating it as invisible. The title is now simply whatever he sets on
the Page in wp-admin going forward.
Verified live at desktop, tablet, and mobile — tab nav and content body
(already pulling from `the_content()`, unaffected by this fix) both
render correctly, correct RTL, no regressions.

### ✅ B14. دربارهٔ ما: no party-logo slot in the template — DONE 2026-10-01

Checked the v6 design directly first (body-about.html) — it has no
logo anywhere on this page at all, so there was no existing placement
to copy. Asked Farhad to decide both open questions rather than
guessing: (1) a brand-new upload field vs. reusing the site's existing
Customizer logo, and (2) where to place it. He chose reusing the
existing logo (`get_theme_mod( 'custom_logo' )`, already shown in
header/footer/masthead — a second upload field would just duplicate it
with no way to keep both in sync) and centered placement above the H1.

This also means B14's own suggested "build a consolidated site-settings
screen" approach wasn't needed/used — no new settings page at all,
since the logo already has one (Appearance → Customize → Site
Identity). The several existing small Settings → X screens already in
this plugin (Contact Topics, Social Links, Labels, etc.) are this
project's real established pattern, not a single shared screen; this
fix needed neither.

Fixed: `page-about.php` now displays the logo (falls back to showing
nothing, not a broken image, on a site with no logo set yet — same
fallback convention as the footer's own logo). New `.about-page-logo`
CSS rule (main.css), centered via `.page-header`'s existing
`text-align: center`, no new modifier class needed.
Verified live at desktop, tablet, and mobile — logo renders at a
reasonable size, correctly centered, no layout shift to the H1/tabs
below it, correct RTL, no regressions.

### ✅ B15. Contact page: title/description hard-coded; response/privacy note split into two lines instead of one — DONE 2026-10-01

Checked the spec text first: ارتباط با ما's components are عنوان صفحه
(title), توضیحات مختصر زیر عنوان (short description under the title),
the form (already built), the dynamic email (already built, B2), and
«توضیحات یک‌خطی دربارهٔ زمان پاسخ‌دهی و حریم خصوصی» — a *one-line* note
covering both response time and privacy together, not two separate
paragraphs.

Fixed: `page-contact.php`'s H1 and dek now pull from the real WP Page
(`get_the_title()`/`get_the_excerpt()`), same "title field, description
field" shape as B13's about-page fix — the dek falls back to the old
hardcoded sentence if the Page's excerpt is left empty (it currently
is, so nothing visibly changed yet; the real title already matched the
old hardcoded text too). The response-time and privacy paragraphs were
merged into one line under one combined label ("پاسخ‌دهی و حریم
خصوصی"), with the existing privacy-policy-link logic (Settings →
Privacy) preserved unchanged.
Verified live at desktop, tablet, and mobile — title/dek unchanged in
practice (no surprise this time), combined response/privacy line
renders correctly as one line under one label, correct RTL, no
regressions.

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
