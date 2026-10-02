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

### ✅ A1. گزیده vs. ترجمه — are these one unit or two? — DONE 2026-10-02

**گزیده half (2026-10-01):** confirmed two genuinely separate things —
the spec's original two-unit design, not the 2026-09-27 rename/merge.
گزیده (Editor's Picks) got its own independent CPT (`editors_pick`) with
its own real taxonomy, "same as the articles but independent." Built and
shipped — see `docs/CHANGELOG.md` 2026-10-01.

**ترجمه half (2026-10-02):** Farhad reviewed the ترجمه section live and
confirmed it's "completely fine, we do not need any changes" — the
existing `shcore_is_selected`/ترجمه mechanism (`shola_get_selected_query()`,
page-selected.php, front-page.php's ترجمه homepage section,
`shcore_translation_original_author`/`shcore_translation_translator`)
stays exactly as it is; no restructuring needed. No code changed.

### ✅ A2. گزارش homepage count: spec says 6, site shows 4 — DONE 2026-10-02

Lowered to 4 on 2026-09-18 at the client's own request. Raised back to 6
on 2026-10-02, alongside turning this section into a carousel (same
round, client-requested) — the client explicitly asked for "at least
six" cards in the rotation, matching the original written spec's number.
The earlier "stay at 4" instruction no longer applies: it was motivated
by the section being a static grid where a hard cap kept it from growing
taller than its neighbors, which stopped being a concern once extra
items scroll in a carousel instead of wrapping into new grid rows.

- File: `wp-content/themes/shola-jawid/front-page.php` (`$reports_query`,
  `posts_per_page => 6`, two places — the query args and the hard-cap
  loop counter just below it).

### ✅ A3. What belongs in the column under اطلاعیه‌ها on the homepage? — DONE 2026-10-01

**Decided and shipped:** گزیده (Editor's Picks), per Farhad's explicit
request — replacing "پربازدیدترین" (Most Viewed) in this one slot only.
Most Viewed itself (SholaCore\View_Counter, shcore_view_count) is fully
intact and untouched everywhere else: single.php's article-sidebar
panel and taxonomy-topic.php's پرخواننده‌ترین sort tab both still work
exactly as before — Farhad was explicit about that constraint.

- Files changed: `wp-content/themes/shola-jawid/front-page.php`
  (`$editors_picks_query`/`$has_editorspicks`, replacing
  `$most_viewed_query`/`$has_mostviewed`),
  `wp-content/themes/shola-jawid/template-parts/cards/editors-picks-panel.php`
  (new — a visual copy of most-viewed-panel.php's exact anatomy, not a
  shared/renamed file; that file is untouched and still used by its two
  remaining call sites), `wp-content/plugins/shola-core/includes/class-post-types.php`
  (new `editors_pick` CPT), `class-taxonomies.php` (new
  `editors_pick_category` taxonomy), `single.php` (reused for گزیده‌ها's
  single view; breadcrumb made post-type-aware), new
  `archive-editors_pick.php`. Full verification in
  `docs/CHANGELOG.md` 2026-10-01.

### ✅ A4. ترجمه homepage card layout: rows or cards? — DONE 2026-10-02

Resolved: Farhad reviewed the ترجمه section live and confirmed it's
"completely fine, we do not need any changes" — the current horizontal
row layout (`selected-row.php`) is accepted as-is; the spec's image-on-
top vertical card description is not being pursued. No code change.

### ✅ A5. جهان برای فتح has 4 دوره sub-terms — DONE 2026-10-02

Resolved as a content decision, not a code issue: Farhad confirmed the
دوره (period) sub-term functionality is deliberately built to work for
both publications — this was a client request for the underlying
capability, not a spec gap. Whether a given publication actually uses it
is managed per-publication via wp-admin (removing/keeping the seeded
terms there), not by restricting the feature in code. جهان برای فتح's
دوره terms have already been removed on the live site by the client's
own choice; the seed code in
`wp-content/plugins/shola-core/includes/class-taxonomies.php`
(`seed_publication_periods()`) is correct as-is and does not need to
change.

### ✅ A6. کتابخانه "ویراستار" (editor) — one fixed name, or per-book? — DONE 2026-10-02

Resolved: Farhad originally requested the per-book field, but confirmed
2026-10-02 it's no longer needed — the single fixed name
("م. صالح" via `shola_get_managing_editor()`, shown on every book) stays
as-is. No per-book admin field will be built; the spec's "per-book"
description is superseded by this explicit decision.

- Files (unchanged, reference only): `wp-content/themes/shola-jawid/inc/template-tags.php`
  (`shola_get_managing_editor()`), `wp-content/themes/shola-jawid/single-document.php`.

### ✅ A7. تراکت info-panel side: same on both — DONE 2026-10-02

Resolved: Farhad confirmed "same on both" is correct — panel on the
right, image on the left, in both the homepage lightbox and the full
archive gallery, matching this RTL site's reading flow throughout. No
CSS change was needed; the existing shared rule
(`.leaflet-lightbox-body`, `row-reverse`, main.css) was already correct.

Same round: added the leaflet's own description text (its post content
— an existing `leaflet` CPT field, previously entered in wp-admin but
never shown anywhere) to the lightbox panel, below the title. Files:
`wp-content/themes/shola-jawid/page-leaflets.php` (archive gallery JSON
data), `wp-content/themes/shola-jawid/front-page.php` (homepage single-
item trigger), `wp-content/themes/shola-jawid/assets/js/main.js`
(`leafletRender()` and the single-trigger open path), `assets/css/main.css`
(`.leaflet-lightbox-description`).

### ✅ A8. REST API leak of hidden names — confirmed live, FIXED 2026-10-02

Checked live against production (`https://sholajawid.com/wp-json/wp/v2/posts`)
before touching anything, per this item's own instruction: the leak was
real, not speculative. `auth_callback` on `register_post_meta()` only
gates REST *writes* (edit_post_meta capability checks) — it does nothing
for reads. With `show_in_rest => true`, all three fields were exposed in
the public `meta` object of every post, logged in or not. Confirmed
actual names leaking, not just empty keys — e.g. post 1292's
`shcore_translation_original_author` publicly returned "حزب کمونیست
انقلابی کانادا" and `shcore_translation_translator` returned "هیت تحریر
شعله جاوید", despite both being correctly absent from every theme
template.

**Fix shipped:** `show_in_rest => false` on all three
`register_post_meta()` calls (`shcore_byline`,
`shcore_translation_original_author`, `shcore_translation_translator`)
in `wp-content/plugins/shola-core/includes/class-meta-fields.php`.
Confirmed locally post-fix: all three keys are now completely absent
from the REST `meta` object. Safe with no admin-side side effects —
these fields are saved via a classic meta box + `save_post` hook, never
through the REST API, so disabling REST exposure doesn't touch editing
at all.

### ✅ A9. کتابخانه auto-scroll direction — DONE 2026-10-02

Resolved: Farhad confirmed this section (the homepage کتابخانه shelf's
existing continuous auto-scroll, `.library-shelf-track` / `main.js`) is
accepted as-is — not a request for a new carousel/manual-nav feature,
which he explicitly doesn't want here (that pattern was built separately
for گزارش, per Farhad's own distinction). No code change made, per this
item's own original scope ("no code change expected unless the answer is
'no'").

This closes out every item in Part A (A1–A9).

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

### ✅ B8a. نشریات: no description field — DONE 2026-10-01

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

Status key: ✅ done/decided · ⚠️ genuine open gap, never actioned ·
❌ feature not built (decision no longer blocking it, but nobody has
built it) · 🔶 conflict (see Part A).

Refreshed 2026-10-02 once every Part A/B item was resolved — most rows
flip to ✅ accordingly, but a few don't: resolving the *decision* behind
an item isn't the same as building the *feature*, and a couple of rows
were never picked up by any A/B item at all. Both kinds are called out
below rather than silently marked done.

| # | Spec item | Status | See |
|---|---|---|---|
| 1 | مقاله core fields | ✅ | B4 |
| 2 | موضوع اصلی + breadcrumb | ✅ | — |
| 3 | Subtitle display rule | ✅ | B4 |
| 4 | Author/alias hidden publicly | ✅ | A8 (confirmed live + fixed) |
| 5 | گزارش multi-author | ✅ decided, won't-build | Farhad confirmed 2026-10-03 — not needed |
| 6 | گزیده multi-author | ✅ decided, won't-build | same as #5 |
| 7 | ترجمه multi-value author/translator + alias | ✅ decided, won't-build | Farhad reviewed ترجمه live 2026-10-02 and confirmed no changes needed — single-value stays as-is |
| 8 | ترجمه fields hidden publicly, exist in admin | ✅ | A8 (confirmed live + fixed) |
| 9 | ترجمه single- vs multi-value | ✅ decided, won't-build | same 2026-10-02 ترجمه confirmation as #7 |
| 10 | نشریات 2 parents, شعله جاوید has 4 دوره only | ✅ decided | A5 — both publications intentionally support دوره; which one uses it is a content choice |
| 11 | Issue fields (13) | ✅ | B6, B7, B8a, B8b |
| 12 | نشریات taxonomy structural rules | ✅ | Fixed 2026-10-02 — `publication` added to `Category_Manager::NO_UNCATEGORIZED_FALLBACK`, same precedent as `party_document_category`; the already-seeded empty «دسته‌بندی‌نشده» term auto-cleaned on next admin_init |
| 13 | انتشارات حزب fields | ⚠️ still open | B10 (deferred by explicit prior decision, not forgotten) |
| 14 | کتابخانه fields incl. ویراستار | ✅ ویراستار · ⚠️ language field | A6 (decided, fixed name stays) · B10 (deferred) |
| 15 | کتابخانه taxonomy/archive | ✅ | B9 |
| 16 | اسناد حزب fields | ✅ | — |
| 17 | اسناد حزب groups | ✅ | — |
| 18 | اطلاعیه‌ها fields | ✅ | — |
| 19 | تراکت fields | ✅ | B3 |
| 20 | تراکت display rules | ✅ | A7, B12 |
| 21 | Admin hint text length | ✅ | B5 |
| 22 | Breadcrumbs everywhere | ✅ | B11 |
| 23 | Pagination threshold | ✅ | B12 |
| 24 | Search behavior | ✅ | B12 |
| 25 | Jalali calendar everywhere | ✅ | — |
| 26 | پست ویژه | ✅ | — |
| 27 | پست نشریه | ✅ | Fixed 2026-10-02 — `single-issue.php` now reads/shows the cover image's media caption, same `wp_get_attachment_caption()` pattern single.php already used for articles |
| 28 | مقالات section | ✅ | Fixed 2026-10-02 — new `shola_trim_excerpt()` helper (inc/template-tags.php) always passes a real "…" to `wp_trim_words()`; every one of the ~15 excerpt-trimming call sites site-wide switched to it, replacing the broken literal "&hellip;" `esc_html()` was producing |
| 29 | اطلاعیه‌ها column | ✅ | — |
| 30 | گزیده under اطلاعیه‌ها | ✅ | A3 |
| 31 | تراکت section | ✅ | — |
| 32 | گزارش section count | ✅ | A2 |
| 33 | نشریات section | ✅ | B7 (heading/title) · download-link fixed 2026-10-02 — homepage "دریافت شماره" buttons (front-page.php, `shola_render_hero_publication_card()`) now link straight to the real PDF with `download`, instead of duplicating the cover/title's own link to the issue permalink |
| 34 | ترجمه section layout | ✅ decided | A4 — current row layout confirmed fine as-is, 2026-10-02 |
| 35 | انتشارات حزب section | ✅ | — |
| 36 | کتابخانه section | ✅ | — |
| 37 | اسناد حزب section | ✅ | — |
| 38 | همه موضوعات | ✅ | Fixed 2026-10-02 — added `shola_maybe_seed_topics_page()` (inc/setup.php), same self-healing pattern as the existing leaflets/selected page seeders. The page already existed live (confirmed before changing anything), so this is a safety net against future deletion, not a live-bug fix |
| 39 | Footer/copyright | ✅ | B1 |
| 40 | Header | ✅ | — |
| 41 | دربارهٔ ما | ✅ | B13, B14 |
| 42 | ارتباط با ما | ✅ | B2, B15 |
| 43 | جست‌وجو | ✅ | B12 |

**Nothing left open.** #12/#27/#28/#33/#38 were fixed 2026-10-02; #5/#6
(گزارش/گزیده multi-author) was the last open item — Farhad decided
2026-10-03 it's not needed, no multi-author mechanism will be built.
Part A, Part B, and Part C are now fully resolved except B10 (intentionally
deferred by prior explicit client decision).
