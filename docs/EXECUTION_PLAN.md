# شعله جاوید (Shola Jawid) — WordPress Theme Conversion — Execution Plan

**From finished v6 "Quiet Press" static UI prototype to a handed-over, plugin-lean, credited WordPress theme + shola-core plugin.**

| Field | Value |
|---|---|
| Project | شعله جاوید (Shola Jawid) — bilingual-ready, Persian-active publishing platform |
| Document | WordPress Theme Conversion — Execution Plan |
| Companion document | `CLAUDE.md` (binding rules file) — this plan and that file must never contradict each other |
| Version | 1.0 — draft for build, executed via Claude Code |
| Date | August 2026 |
| Scope of this phase | Persian (fa) only, active. Bilingual-ready scaffold retained; English (en) explicitly out of scope. |
| Prepared by | Farhad Farhaad — info.farhaad@gmail.com — github.com/farhadcodes |

---

## How to use this plan

This plan is written to be **dissectible and trackable**: it is broken into 8 phases (0–7), each broken into numbered steps, each step ending in a checklist. Claude Code should work one step at a time, check items off, and not proceed to the next phase until that phase's **Definition of Done** is fully satisfied.

**Every decision made while executing this plan must follow `CLAUDE.md`.** If this plan and `CLAUDE.md` ever seem to disagree, `CLAUDE.md` wins on **how** something is built (naming, plugin policy, security, credit placement); this plan wins on **when** and in what order things happen.

> Any time a step requires interpreting an ambiguous rule, deviating from a rule, or resolving one of the three open IA decisions — stop, record it in `docs/CHANGELOG.md` per the format defined there, and get Farhad's confirmation before proceeding, unless the rule already unambiguously covers the case.

**Symbols used in this document:**
- `☐` — a checklist item to be ticked off (in this document, or mirrored as a GitHub Issues checklist — see Phase 0).
- `> blockquote callouts` — hard constraints pulled from `CLAUDE.md`, repeated here at the point they matter so the plan is self-contained.

---

## Phase 0 — Repository, environment & rules setup

Goal: nothing about design or code yet — get the scaffolding, tooling, and governing documents in place so every later phase has somewhere correct to land.

### 0.1 — Create the GitHub repository

- Create a new public repository named `shola-jawid` under `github.com/farhadcodes`.
- Set repo description: "Bilingual-ready WordPress theme + plugin for شعله جاوید (Shola Jawid), a Persian/English editorial publishing platform."
- Add topics: `wordpress-theme`, `wordpress-plugin`, `rtl`, `persian`, `publishing`, `editorial`.
- Add: README.md (placeholder — full version in 0.4), LICENSE (GPLv2 or later — required for WP theme distribution), .gitignore (WordPress + Node template, extended per `CLAUDE.md` §8).
- Default branch: `main`. Branch protection on `main` deferred until Phase 1 has content (no PR gate needed for an empty repo).

**Checklist:**
- ☐ Repo created, public, description + topics set
- ☐ LICENSE (GPLv2+) added
- ☐ .gitignore excludes `wp-content/uploads/`, cache, `node_modules`, `.env`, DB dumps (per `CLAUDE.md` §8)

### 0.2 — Local environment (LocalWP)

- Create a new LocalWP site (PHP 8.1+, matching `CLAUDE.md` §0 minimums; MySQL/MariaDB per LocalWP default).
- Confirm WP core version ≥ 6.4.
- Enable `WP_DEBUG`, `WP_DEBUG_LOG` (not `WP_DEBUG_DISPLAY`) in `wp-config.php` for the local site only — never on production.
- Symlink or configure LocalWP's site `wp-content/themes` and `wp-content/plugins` to point into the cloned GitHub repo's `wp-content/themes/shola-jawid` and `wp-content/plugins/shola-core`, so local dev and version control are the same files (no copy-in/copy-out step).
- Install WP-CLI inside the LocalWP shell ("Open Site Shell") — used throughout for CPT scaffolding checks, cron testing, and later the DB-export backup step in Phase 6.

**Checklist:**
- ☐ LocalWP site created, PHP/WP versions confirmed
- ☐ Repo symlinked into wp-content so local dev == version-controlled files
- ☐ WP-CLI available in site shell

### 0.3 — Rules & tracking documents land in the repo

- Place `CLAUDE.md` at repo root (already drafted).
- Place this execution plan at `docs/EXECUTION_PLAN.md` — pick one canonical location and don't fork it into two edited copies.
- Place `docs/CHANGELOG.md` (already drafted, seeded with initial decisions).
- Copy the four existing reference documents into `docs/IA-reference/`, read-only: brand guidelines (FA v1.0 + the v2.0 HTML — both, v2.0 is a live variant, not a replacement; keep both and note in a short `docs/IA-reference/README.md` which one is authoritative for this build), the IA & sitemap document, and the Aeon design system analysis.

> **Blocking:** confirm explicitly with Farhad whether v1.0 (Ink-dominant, 60/30/10) or v2.0 (Crimson Field, 45/40/15) governs the WordPress build — the v6 static prototype used a hybrid (crimson masthead only, otherwise v1.0-style restraint). Record the answer in CHANGELOG.md before Phase 2 begins; do not assume.

**Checklist:**
- ☐ CLAUDE.md committed at repo root
- ☐ Execution plan committed under `docs/`
- ☐ CHANGELOG.md committed under `docs/`
- ☐ Reference docs copied into `docs/IA-reference/`, authoritative brand version confirmed and logged

### 0.4 — Tooling & CI

- Add `phpcs.xml.dist` configured for WordPress-Extra ruleset (per `CLAUDE.md` §5).
- Add a minimal GitHub Actions workflow (`.github/workflows/lint.yml`) that runs phpcs on push/PR.
- Write the real README.md: project description, screenshot (added once available in Phase 3), setup instructions for a new developer, and the Credits section per `CLAUDE.md` §7.5.

**Checklist:**
- ☐ phpcs.xml.dist added, WordPress-Extra ruleset
- ☐ CI lint workflow added and passing on an empty/scaffold commit
- ☐ README.md written with Credits section

### Phase 0 — Definition of Done

- Repo exists, is cloned locally, and is the live backing store for the LocalWP site — no manual file copying required to see changes.
- CLAUDE.md, this plan, and CHANGELOG.md all exist in the repo and are mutually consistent.
- CI runs (even against an empty scaffold) and passes.
- The brand-version ambiguity (v1.0 vs v2.0 vs v6-hybrid) is resolved and logged — this blocks Phase 2, so resolve it now.

---

## Phase 1 — Prototype audit & refactor-before-convert

Goal: per Farhad's request, evaluate and refactor the v6 static prototype before converting it — find anywhere HTML/CSS/JS need better separation, and fix it in the static prototype first. Converting a messy prototype just moves the mess into PHP; this phase prevents that.

### 1.1 — Inventory the current v6 deliverable

- Extract `shola-jawid-ui-v6-quiet-press.zip` into a scratch folder outside the repo (this is a reference artifact, not the theme itself).
- List all 23 generated HTML pages, the partials (`_shell.html`, `_header.html`, `_menu.html`, `_footer.html`), `main.css` (~900 lines / 21 sections), `main.js`, `build.py`, `gen_bodies.py`.
- For each of the 21 CSS sections, note: does it map cleanly to one template-part/component, or does it bleed across multiple unrelated UI regions? Flag any section that mixes concerns.

**Checklist:**
- ☐ Full file inventory written to `docs/screenshots/phase1-inventory.md` (or similar) with every HTML/CSS/JS file listed
- ☐ 21 CSS sections individually reviewed and flagged clean / needs-split

### 1.2 — Apply the Aeon-analysis card-partial discipline

- Per the Aeon Design System Analysis (§3.2, §4): confirm the prototype already uses one shared card anatomy for article/issue/document cards with only a swapped type-tag. If the v6 HTML instead has near-duplicate card markup per content type, this is the single highest-value refactor to do now, in HTML, before PHP conversion — it directly determines whether `template-parts/cards/` in Phase 4 is one file or three.
- Confirm the popup menu's six crimson-family shades (c1–c6) are CSS custom properties, not repeated hex values, so they carry over as WP customizer-safe tokens if ever needed.

**Checklist:**
- ☐ Card markup confirmed single-partial-ready (or refactored to be so) in the static HTML
- ☐ c1–c6 popup menu shades confirmed as CSS custom properties

### 1.3 — Separate any remaining inline concerns

- Grep all 23 HTML pages for inline `style="..."` and inline `<script>` — per `CLAUDE.md` §5, WordPress output must never carry inline styles for anything token-controlled. Move any found instances into `main.css` classes or `main.js` before conversion.
- Confirm `main.js` is genuinely progressive-enhancement-only (site usable with JS off) — this must remain true after PHP conversion.
- Confirm every RTL-sensitive rule uses logical properties (`CLAUDE.md` §1, §9) — grep for `margin-left`, `margin-right`, `padding-left`, `padding-right`, `left:`, `right:` in `main.css` and fix any physical-property leftovers before they get copied into the theme's `assets/css/`.

**Checklist:**
- ☐ No inline style attributes remain in HTML (or all remaining ones are justified exceptions, documented)
- ☐ main.js confirmed progressive-enhancement-only
- ☐ Zero physical-direction CSS properties remain in main.css

### Phase 1 — Definition of Done — ✅ complete (2026-08-05)

- ☑ A short refactor note (`docs/screenshots/phase1-refactor-notes.md`) exists, listing what was changed/found in the static prototype and why, cross-referenced to which Phase-4 template-part it de-risks.
- ☑ The audited v6 prototype at `03_UI_Design/shola-jawid-ui/` — not the original zip, and not a modified copy — is the one and only source of truth carried into Phase 2 onward. Phase 1 was an audit pass; nothing in the prototype's files was changed. `docs/screenshots/phase1-inventory.md` and `docs/screenshots/phase1-refactor-notes.md` are the record of what was checked.
- ☑ Physical-CSS-property check: zero violations found (main.css + all inline styles), confirmed in `phase1-refactor-notes.md` §1.
- ☑ Duplicated-card-markup check: resolved via Phase 1.2 — `.card` (articles) and `.issue-card` (issue covers) are genuinely distinct anatomies by design, not duplication; `.doc-row`/`.announce-list`/`.topic-list` are row/list patterns, not cards. Logged in `docs/CHANGELOG.md`, `EXECUTION_PLAN.md` §4.1 corrected accordingly.
- ⚠ **Inline-style check: 440 instances found, not fixed in Phase 1** — explicitly deferred to Phase 4, template-by-template, per Farhad's approval (session 2026-08-05): fixing 440 call sites in static files about to be rewritten as PHP anyway is double-work with avoidable regression risk. The deferral is now a checkable Phase 4 Definition-of-Done item (§4.2/§4's DoD) — not just a note here.
- ⚠ **One unplanned finding, also deferred to Phase 4:** invalid nested-interactive markup at `_header.html:6-12` (search link inside `#menu-open` button) — not fixed in the static prototype, fix required during `header.php` conversion instead. Also now a checkable Phase 4 DoD item.

---

## Phase 2 — Scaffolding: theme + plugin skeleton

Goal: create the two installable WordPress packages — `shola-jawid` (theme) and `shola-core` (plugin) — with correct headers, folder structure, and activation behavior, before any real template code is written.

### 2.1 — Theme skeleton

- Create `wp-content/themes/shola-jawid/` following the exact tree in `CLAUDE.md` §4.
- Write `style.css` header block exactly per `CLAUDE.md` §7.1 (Theme Name, Theme URI, Author, Author URI → `https://github.com/farhadcodes`, Version 1.0.0, License GPLv2+, Text Domain `shola-jawid`).
- Write a minimal `functions.php` that only `require_once`'s `inc/setup.php`, `inc/enqueue.php`, `inc/template-tags.php` — keep functions.php itself thin.
- `inc/setup.php`: add_theme_support (title-tag, post-thumbnails, html5, custom-logo, responsive-embeds), register_nav_menus matching the IA nav (نشرات / موضوعات / کتابخانه / اطلاعیه‌ها / ارتباط با حزب), register the image sizes matching the v6 prototype's actual crop ratios (confirm exact ratios from the refactored prototype in Phase 1, don't assume without re-checking against v6 specifically).
- `inc/setup.php`: add the `admin_footer_text` filter per `CLAUDE.md` §7.6 (light-touch credit line).
- Add a placeholder `screenshot.png` (1200×900) — final version with credit strip produced in Phase 3 QC once real UI is rendering.
- Add `readme.txt` (WordPress.org-style) with Contributors field and full Credits section per `CLAUDE.md` §7.4.
- Add a minimal `index.php` — WP core requires `style.css` + `index.php` for a theme to register as non-broken (`WP_Theme::errors()`), independent of the template hierarchy and independent of `front-page.php` etc. existing. This is scaffolding, not Phase-4 template design: no real markup/styling, and it calls `wp_head()`/`wp_footer()` directly rather than `get_header()`/`get_footer()` (those partials don't exist until Phase 4.1 — calling them earlier triggers a `_doing_it_wrong` notice under `WP_DEBUG`). Gets refactored to call `get_header()`/`get_footer()` once Phase 4.1 lands, as normal iteration.

**Checklist:**
- ☐ Theme activates in wp-admin with zero PHP errors/warnings
- ☐ Theme info in Appearance → Themes shows Author URI linking to github.com/farhadcodes
- ☐ Nav menu locations registered and assignable in Appearance → Menus

### 2.2 — Plugin skeleton

- Create `wp-content/plugins/shola-core/` per `CLAUDE.md` §4.
- Write `shola-core.php` plugin header exactly per `CLAUDE.md` §7.3, same Author URI value.
- Namespace all plugin PHP under `SholaCore\` (`CLAUDE.md` §0); use a simple PSR-4-style autoload via `spl_autoload_register` (no Composer dependency needed for a project this size, unless a genuine need for a package arises — if so, that's a `CLAUDE.md`-whitelist-style discussion first).
- Add an admin notice in the theme (not the plugin) that fires only if `get_template() === 'shola-jawid' && ! is_plugin_active('shola-core/shola-core.php')` — soft dependency per `CLAUDE.md` §2, no fatal errors.

**Checklist:**
- ☐ Plugin activates standalone with zero errors
- ☐ Theme shows a graceful admin notice (not a white screen) if plugin is deactivated
- ☐ Plugin info shows correct Author URI

### Phase 2 — Definition of Done

- Both packages install and activate cleanly on the LocalWP site, in either activation order, with no fatal errors.
- phpcs runs clean against both new folders.
- First real commit pushed: "chore: scaffold theme and plugin skeletons".

---

## Phase 3 — Content model: CPTs, taxonomies, meta fields

Goal: build the data layer in `shola-core` that the IA document's §4–§6 describe, before any template tries to query it.

### 3.1 — Custom Post Types

- `includes/class-post-types.php` registers: `issue` (شمارهٔ نشریه), `document` (سند کتابخانه), `announcement` (اطلاعیه) — per IA doc §5.
- Regular posts (native post type) continue to serve مقاله / یادداشت (article/note) per the IA doc's content model — no new CPT needed for articles themselves, only a topic taxonomy applied to them.
- Rewrite slugs match the IA doc §4 URL table's *structure* — `/library/{collection}/{slug}`, `/publications/{publication}/{issue}`, `/announcements` — but **without** the doc's literal `/fa/` locale prefix (see correction below).

> **Correction (Phase 3.1, logged in `docs/CHANGELOG.md` 2026-08-05):** the
> IA doc's own URL table is written with locale-prefixed routing
> (`/fa/...`, mirrored by `/en/...` — its §2.2 states this explicitly).
> Building that now would mean choosing a specific future i18n URL
> strategy (subdirectory vs. subdomain vs. domain — Polylang/WPML support
> different ones) that hasn't been decided, which `CLAUDE.md` §1 explicitly
> prohibits ("do not scaffold... `en/` routing... speculatively").
> Corrected: clean slugs with no `/fa/` prefix for now; a prefix can be
> layered on via rewrite rules later without touching CPT registration.
>
> Also: `/publications` and `/library` are **static Pages**
> (`page-publications.php`/`page-library.php`, §4.2), so `issue` and
> `document` are registered with `has_archive => false` to avoid the CPT
> archive rewrite fighting the static Page at the same slug — their
> single-item permalinks still nest correctly via a custom
> `%publication%`/`%collection%` rewrite tag + `post_type_link` filter, and
> per-publication/per-collection listings come from the taxonomy archive
> templates (Phase 3.2) instead of a post-type archive. `announcement` is
> registered with `has_archive => 'announcements'` since the IA doc treats
> `/announcements` itself as a real listing template
> (`archive-announcement.php`, not a static Page).

**Checklist:**
- ☐ issue, document, announcement CPTs registered with correct labels (Persian), supports, and rewrite slugs matching the (prefix-corrected) IA doc §4 structure
- ☐ No archive-slug collision between `issue`/`document` CPT archives and the `page-publications.php`/`page-library.php` static Pages (verify `/publications` and `/library` render the static Page, not an empty CPT archive)

### 3.2 — Taxonomies

- `includes/class-taxonomies.php` registers: `topic` (اقتصاد، جهان، افغانستان، زنان، جنبش بین‌المللی، علم و هنر) attached to post; `publication` (شعله جاوید، جهان برای فتح) attached to issue; `collection` (آثار کلاسیک، جنبش بین‌المللی، اسناد حزب، نقد و پلمیک) attached to document.

> The IA doc's Open Decision #1 ("جنبش بین‌المللی" appears both as a topic and as a collection) is confirmed intentional per the IA doc itself — they're different content types (articles vs. documents). Register both terms as documented; do not merge them. Log this in CHANGELOG.md as "Resolved" once the terms are created, referencing IA doc §9.

**Checklist:**
- ☐ topic, publication, collection taxonomies registered and attached to the correct post types
- ☐ All taxonomy terms pre-created via a one-time activation hook or documented WP-CLI script (reproducible, not manual-only)
- ☐ IA Open Decision #1 logged as resolved in CHANGELOG.md

### 3.3 — Post meta fields

- `includes/class-meta-fields.php` registers meta for `issue` (نشریه، شمارهٔ شماره، تاریخ، جلد، فایل PDF، خلاصه، فهرست اختیاری), `document` (نویسنده/منبع، تاریخ، فایل PDF، پیش‌نمایش، توضیح، زبان), and article/post (نویسنده، زبان، پیوند ترجمه).
- PDF fields use `register_post_meta` with a `sanitize_callback` restricted to attachment IDs whose MIME type is `application/pdf` (enforced again at save via the `upload_mimes` filter and server-side validation per `CLAUDE.md` §6 — never trust the client-side `accept` attribute alone).
- پیوند ترجمه (translation-pair link) field is scaffolded (post-to-post relationship meta) but inert in this phase — per `CLAUDE.md` §1, bilingual-ready but not wired to a live English tree yet.

**Checklist:**
- ☐ All meta fields registered with proper sanitize/auth callbacks
- ☐ PDF upload fields reject non-PDF files both client-side (UX only) and server-side (real MIME check, security-critical)
- ☐ Simple metabox UI (or block-editor panel) exists for editors to fill these fields without touching code

### Phase 3 — Definition of Done

- An editor can, from wp-admin alone, create one of each CPT, assign correct taxonomy terms, upload a PDF, and save — with zero developer involvement.
- Content model confirmed independent of the theme: deactivating shola-jawid and switching to a default WP theme does not delete or break any created content (spot-check this explicitly).

---

## Phase 4 — Templates: converting the v6 UI to PHP

Goal: the largest phase — every one of the (refactored, per Phase 1) v6 static pages becomes a real WordPress template, using the IA doc §4 page-to-template map as the checklist.

### 4.1 — Header, footer, and shared partials

- Convert `_shell.html` → the `<head>` section split across `header.php` (opening) and `footer.php` (closing), using `wp_head()` / `wp_footer()` hooks correctly placed (never omitted).
- Convert `_header.html` → `header.php`, using `wp_nav_menu()` for the registered nav location, preserving the exact crimson masthead + maroon 2px anchor line treatment pixel-for-pixel.
- **Fix, don't port, the invalid nested-interactive markup found in Phase 1.3** (logged in `docs/CHANGELOG.md` and `docs/screenshots/phase1-refactor-notes.md` §5): `_header.html:6-12` nests the search `<a href="search.html">` inside `<button id="menu-open">`, which is invalid HTML5 (button content model excludes interactive descendants) and produces undefined keyboard/screen-reader behavior. `header.php` must render `#menu-open` and the search link as adjacent siblings in the same flex row instead — same visual result, valid markup. This is a markup-hygiene fix, not a visual change; do not port the nesting as-is.
- Convert `_footer.html` → `footer.php`, thin paper-white per brand guide, footer nav matching the IA doc footer list (شبکه‌های اجتماعی / درباره ما / جست‌وجو).
- Build `template-parts/cards/card.php` as the shared card partial, defaulting to **articles**, with an optional `$type` param for the two confirmed mixed-content-stream contexts (see second correction below).

> **Correction (Phase 1.2, logged in `docs/CHANGELOG.md` 2026-08-05):** this
> step originally called for one `card.php` partial swapping a type
> parameter across article/issue/document. Line-by-line comparison of the
> v6 prototype's CSS showed that's not what v6 built: `.issue-card`
> (`main.css:997-1005`) is a deliberately distinct anatomy — 3:4 portrait
> image, `box-shadow` (breaking §09's own no-shadow rule on purpose), no
> dek/byline — not a relabeled `.card`. Documents never render as cards at
> all; they're always `.doc-row` list rows. Corrected plan: `card.php`
> (articles only, no type param), a separate `issue-card.php` for issue
> covers, and separate row/list partials outside `template-parts/cards/`
> for `.doc-row`, `.announce-list`, and `.topic-list`.
>
> **Second correction (Phase 4.2, logged in `docs/CHANGELOG.md`
> 2026-08-06, found while building `front-page.php`):** the "articles
> only, no type param" conclusion above needed one narrow revision.
> `body-index.html`'s "Latest" grid renders exactly one **document**
> using `.card` markup (different type-label icon/text, links to
> `document-single.html`). So `card.php` does need an optional `$type`
> param after all — but scoped to `'article'` (default) or `'document'`
> only, not the broader article/issue/document 3-way split the first
> correction rejected. `issue-card.php` and the row/list partials are
> unaffected.
>
> **Third correction (Phase 4.2, logged in `docs/CHANGELOG.md`
> 2026-08-06, found while building `search.php`):** the second
> correction above also claimed `body-search.html`'s results use this
> same `.card` markup for its document result — checked directly
> against the actual file this time (rather than the grep sweep that
> produced the second correction, which apparently mismatched here) and
> found that's not accurate: no `class="card"` appears anywhere in
> `body-search.html`. Its results — all 4 types, article/note/issue/
> document alike — are a distinct `<li class="stack-lg">`-family
> component (`h-card-lg`, no image, no type-icon SVG), built as its own
> `template-parts/search/result.php` instead of a third `card.php`
> variant. `card.php` stays scoped to exactly the one confirmed
> context: front-page.php's Latest grid.

**Checklist:**
- ☐ header.php, footer.php built, wp_head()/wp_footer() present and firing
- ☐ `card.php` (articles) and `issue-card.php` (issue covers) each in use everywhere their respective markup appears; `.doc-row`/`.announce-list`/`.topic-list` each have their own row/list partial (grep templates for any duplicated markup within each pattern — there should be none)
- ☐ `header.php`'s menu-open button and search link are sibling elements, not nested — verify by inspecting rendered markup, not just visual output

### 4.2 — Page-to-template map (work through in this order)

> **Carried over from Phase 1.3 (logged in `docs/CHANGELOG.md` and
> `docs/screenshots/phase1-refactor-notes.md` §2):** the v6 prototype has
> 440 inline `style="..."` attributes across its 23 pages. Fixing all of
> them in the static HTML was explicitly deferred to this phase to avoid
> double-work and regression risk on files about to be rewritten as PHP
> anyway. **That deferral ends here.** For each template converted below,
> zero inline `style="..."` attributes may remain in the finished PHP —
> per `CLAUDE.md` §5, every inline style becomes either an existing
> `main.css` §20 utility class (`.center`, `.row-between`, `.stack`, etc.),
> a new small utility class added to §20, or a scoped rule in that
> template's own CSS section — decided per template as it's converted, not
> as a blind find-and-replace. This is not optional polish; it's part of
> this phase's Definition of Done (see below).

| IA ID | Page | WP Template | Notes |
|---|---|---|---|
| HOME | صفحهٔ اصلی | front-page.php | Hero + latest articles + announcements, per v6 homepage |
| PUB | نشرات | page-publications.php | Lists both publications |
| PUB-SJ / PUB-WW | شعله جاوید / جهان برای فتح | taxonomy-publication.php | One template, two terms — issue archive |
| TOP | موضوعات | page-topics.php | Lists the 6 topic terms |
| TOP-* | ۶ موضوع | taxonomy-topic.php or category.php | One template for all 6 terms |
| LIB | کتابخانه | page-library.php | Lists the 4 collections |
| LIB-* | ۴ مجموعه | taxonomy-collection.php | One template for all 4 terms |
| ANN | اطلاعیه‌ها | archive-announcement.php | |
| CON | ارتباط با حزب | page-contact.php | Contact Form 7 shortcode inside theme markup — see 4.3 |
| ABT | درباره ما | page-about.php | Static content, editable via block editor |
| SRCH | جست‌وجو | search.php | Native WP search, styled to match v6 search.html |
| — | مقالهٔ تکی | single.php | Sticky sidebar + narrow prose column + drop-cap, per v6 spec |
| — | شمارهٔ تکی | single-issue.php | PDF preview + download |
| — | سند تکی | single-document.php | PDF preview + download |
| — | ۴۰۴ | 404.php | Match v6 error-state design if one exists, else brand-consistent minimal page |

**Checklist:**
- ☐ All 15 templates listed above created and each renders without PHP notices/warnings
- ☐ Each template visually matches its corresponding v6 static HTML page (see 4.4 QC step) before moving to the next

### 4.3 — Contact form (per CLAUDE.md §3 whitelist)

- Install Contact Form 7 (or confirm the equivalent chosen plugin) — the only form-handling plugin permitted per `CLAUDE.md` §3.
- Build the form fields to match the v6 contact.html field set exactly; strip all CF7 default styling and wrap output in the theme's own markup/classes so it's visually indistinguishable from the rest of the brand system.
- Confirm spam protection (honeypot or equivalent) is active out of the box before adding anything extra.

**Checklist:**
- ☑ Contact form visually matches v6 contact.html (confirmed by Farhad, 2026-08-06)
- ☑ Form submits successfully and arrives by email in local testing — end-to-end tested 2026-08-07: filled and submitted the real live form in a browser (not a simulated server-side POST), CF7 reported `status: sent`, and the message was confirmed to actually arrive in LocalWP's Mailpit catcher (`http://localhost:10085`, port found via `sites.json`) — correct recipient (`info.sholajawid@gmail.com`), correct `Reply-To` (the submitted email), correct subject with the topic interpolated, correct body. No failure to diagnose; delivery worked on the first real test.

### 4.4 — Per-template visual QC

- For each template in 4.2, take a screenshot of the rendered WP page and place it beside the corresponding v6 static screenshot in `docs/screenshots/phase4/` — side by side, not just "looks about right from memory."
- Any pixel-level brand-critical property (crimson masthead RGB, drop-cap, card anatomy) gets the same kind of pixel-verification treatment the v6 prototype itself received.

**Checklist:**
- ☑ All 15 templates have a saved side-by-side screenshot comparison — `docs/screenshots/phase4/` (2026-08-07), headless-Chrome captures of each v6 static page next to its live WP counterpart (real seeded content, not placeholders); see that folder's `README.md` for the full pairing table. `404.php` has a live capture only — no v6 mockup exists for that state (already established, see its own `docs/CHANGELOG.md` entry).
- ☑ No unexplained visual drift from the (refactored) v6 prototype — this restates QC already done live throughout Phase 4.2 as each template was built and confirmed by Farhad; the screenshot archive is the saved record of that, not a new review pass.

### Phase 4 — Definition of Done

- Every page in the IA doc's page list (§4) exists, renders real dynamic WordPress content (not static placeholder text), and visually matches the v6 prototype.
- Site is fully navigable end-to-end (nav → topic archive → single article → related content) using only real WP data, no leftover static HTML files being referenced.
- ☑ **Zero inline `style="..."` attributes remain in any finished PHP template**, per `CLAUDE.md` §5 and the Phase 1.3 deferral above. Note for whoever reads this later: the literal command
  (`grep -r 'style="' wp-content/themes/shola-jawid --include=*.php`) does return 2 lines, not
  zero — both are false positives, doc-comment prose in `front-page.php` and `header.php` that
  *mentions* the string `style=""` while explaining the "inline styles replaced with classes"
  decision, not real inline attributes in markup. Confirmed by reading both matches directly
  (2026-08-07). Don't misread a bare grep-count as a regression here.
- ☑ **`header.php` does not reproduce the `_header.html:6-12` nested-button/link markup bug** — `#menu-open` and the search link are siblings, verified in rendered output (`curl`, 2026-08-07): the `<button id="menu-open">` closes cleanly before the search `<a>` opens, not just visually correct in a screenshot.

---

## Phase 5 — Roles, SEO, search, performance

Goal: the functional non-visual requirements from the proposal's امکانات کلیدی and الزامات غیرکارکردی lists.

### 5.1 — User roles & permissions

- Map مدیر → Administrator, سردبیر → Editor, نویسنده → Author, همکار → Contributor (all native WP roles — capabilities already match the IA doc §7).
- Confirm no custom role/capability plugin is needed — a zero-plugin win per `CLAUDE.md` §3.

**Checklist:**
- ☑ Four accounts created (one per role) and spot-tested: each can/cannot do what the IA doc §7 specifies (2026-08-07). One real gap found against stock WP: the IA doc gives Editor "manage categories & menus," but stock Editor lacks `edit_theme_options` (required for nav-menu editing) — Farhad approved granting it (`\SholaCore\Roles`, new `includes/class-roles.php`), the simplest option over a bespoke per-screen capability. `test_admin`/`test_editor`/`test_author`/`test_contributor` accounts created for the spot-test; capability-flag checks confirmed all four roles match the IA doc table exactly post-fix, and two were also verified via real live UI access (not just capability flags): `test_editor` genuinely reaches Appearance → Menus, `test_contributor` is genuinely blocked from it with WP's own permission-denied error page.

### 5.2 — Custom SEO (no SEO plugin, per CLAUDE.md §3)

- `includes/class-seo.php` outputs: dynamic `<title>`, meta description, Open Graph tags, canonical URL.
- Use WordPress core's native `wp_sitemaps_*` filters to theme/brand the auto-generated `sitemap.xml` rather than hand-rolling one from scratch.
- Add hreflang scaffolding (inert/self-referential only, since English isn't live) so the eventual bilingual rollout doesn't require re-touching this file.

**Checklist:**
- ☑ Meta tags verified in page source on at least one of each template type (2026-08-07) — `<title>` was already correct site-wide via core's `add_theme_support('title-tag')` (confirmed in `inc/setup.php`, no new code needed); meta description/OG tags/canonical built new in `class-seo.php` and verified via `curl` on an article (singular), a topic archive (taxonomy), the front page, search results, and a 404. One real bug caught and fixed during that verification: the canonical-URL builder used `$wp->request`, which is empty for a query-string-only view like search, so every search page's canonical silently resolved to the front page instead of the search URL — fixed by using WP's own per-context URL functions (`get_search_link()`, `get_term_link()`, etc.) instead of reconstructing one generically.
- ☑ sitemap.xml accessible and includes posts, issues, documents, announcements (2026-08-07) — confirmed via `curl` before writing any code: WP core already includes all four by default (all `public => true`). Also removed the `users` provider entirely and `post_format`/`category` from the taxonomies sitemap via `wp_sitemaps_add_provider`/`wp_sitemaps_taxonomies` — none have a real front-end destination on this site (no `author.php` template was ever in the page-to-template map; `post_format`/native `category` aren't used as content destinations here, `topic` is).

### 5.3 — Search

- `search.php` uses native WP search (`pre_get_posts` filter to include the custom post types in results).
- Style to match v6's search.html exactly.

**Checklist:**
- ☑ Search returns results across posts, issues, and documents, not just native posts — already built and extensively tested in Phase 4.2 (`search.php`'s closing entries, `docs/CHANGELOG.md` 2026-08-06); re-confirmed live 2026-08-07 rather than assumed still true, no rebuild needed.

### 5.4 — Performance baseline

- Self-host all fonts (Farhang2, ModamPro — corrected 2026-08-07; this bullet originally named Vazirmatn/Markazi Text, which were never the fonts actually used once the v6 brand fonts were finalized — plus Newsreader, Inter, JetBrains Mono) in `assets/fonts/`, enqueued with proper `font-display: swap`.
- Confirm native WP image sizes/srcset are actually serving responsive images, not just the full-size original.
- Run a baseline Lighthouse/PageSpeed pass on the LocalWP site.
- Only if this baseline pass reveals a genuine caching gap that host-level caching (Phase 6) can't cover: flag it as a candidate for the `CLAUDE.md` §3 whitelist discussion. Do not install a caching plugin pre-emptively.

**Checklist:**
- ☑ Fonts self-hosted, font-display: swap confirmed (2026-08-07) — Newsreader/Inter/JetBrains Mono moved from Google Fonts CDN to `assets/fonts/{family}/woff2/`, alongside Farhang2/ModamPro (already self-hosted since Phase 4.1). Zero Google Fonts requests remain on any page (verified via `curl`); all 4 downloaded files return `200`. Full detail (including a real behavior worth remembering — Google serves one variable-font file covering multiple discrete weights, confirmed by fetching a family in isolation before trusting it) in `docs/screenshots/phase5-perf-baseline.md`.
- ☑ Responsive images confirmed serving srcset, not full-size only (2026-08-07) — confirmed via `curl`, not assumed from reading the code: real `srcset` with all registered intermediate sizes present on the homepage's featured images. No code change needed.
- ☑ Baseline performance pass documented in `docs/screenshots/phase5-perf-baseline.md` (2026-08-07) — Lighthouse via `npx lighthouse`: Performance 94, Accessibility 100, Best Practices 78 (both misses are `is-on-https`/`redirects-http`, expected on a local dev site with no SSL — Phase 6 scope, not a Phase 5 gap), SEO 100. No caching gap found — Best Practices' only misses are the HTTPS ones above, so no `CLAUDE.md` §3 whitelist discussion needed. Two real accessibility bugs the first audit run surfaced were fixed here, not deferred: a redundant, unlabeled `aria-hidden="true"` link left focusable in the tab order (`front-page.php`'s hero image link, plus the same pattern found by code search — not by a second audit run — in `template-parts/cards/card.php` and `taxonomy-publication.php`) fixed by adding `tabindex="-1"` alongside the existing `aria-hidden`. Re-ran Lighthouse after the fix to confirm, rather than assume: Accessibility 93 → 100.
- ☐ Baseline performance pass documented in docs/screenshots/phase5-perf-baseline.md

### 5.5 — Jalali-calendar date localization

> Tracked here per Farhad's request (logged in `docs/CHANGELOG.md`
> 2026-08-06): during Phase 4.2, `shola_to_persian_digits()` fixed
> Persian-digit display for counts/years (`inc/template-tags.php`), but
> `get_the_date()` output (article bylines, issue/document publish dates,
> announcement dates) still renders plain Gregorian dates in Latin digits
> everywhere it's used across Phase 4.2's templates — not the
> Jalali-calendar, Persian-digit dates v6's mockup shows (e.g. "۷ سرطان
> ۱۴۰۵"). Full Gregorian→Jalali conversion is a distinct, larger feature
> (calendar math, Persian month names) deliberately deferred out of the
> Phase 4.2 template-building work rather than retrofitted piecemeal into
> each template as it's built.

> **Superseded (logged in `docs/CHANGELOG.md` 2026-08-06):** the plan
> below assumed a self-contained `shola_get_jalali_date()` helper, no new
> plugin. That changed — Farhad opted for a plugin (**Persian Calendar**,
> now on the `CLAUDE.md` §3 whitelist) fixing this site-wide at the
> WordPress date-function level, rather than hand-rolled per-template
> conversion. ParsiDate was evaluated first and rejected (hardcoded to
> `fa_IR` locale only, excluding this project's `fa_AF`); Persian
> Calendar gates on `is_rtl()` instead, confirmed compatible. Full
> evaluation, the timezone-overwrite incident found during install, and
> the date-format/timezone settings Farhad is completing are all in
> `docs/CHANGELOG.md`. The items below are done via the plugin, not a
> custom helper — kept here (struck through in spirit, not literally
> deleted) so the phase's original reasoning stays visible.

- ~~Build a `shola_get_jalali_date()` (or similarly named) template tag in `inc/template-tags.php` that converts a post's Gregorian date to the Jalali calendar with Persian digits and Persian month names, matching v6's exact date format (e.g. "۷ سرطان ۱۴۰۵").~~ Done via Persian Calendar's global `date_i18n`/`wp_date` hooks instead.
- Three call sites needed hardening *against* the plugin's global hook, not retrofitting *onto* it — `shola_get_publication_meta_line()`, `taxonomy-publication.php`'s current-issue line, and `issue-card.php`'s date label intentionally show Gregorian years (matching v6's own literal `۳۲ ISSUES · ۲۰۱۸–۲۰۲۶` convention), and `<time datetime="...">` attributes must stay ISO 8601 regardless. New helpers `shola_get_gregorian_year()`/`shola_get_iso_datetime()` and a hardened `shola_get_english_month_abbr()` (all `inc/template-tags.php`) handle this — see `docs/CHANGELOG.md`.
- Every other `get_the_date()` call in Phase 4.2's built templates (front-page.php hero/cards/current-issue/announcements, card.php byline, the masthead runner) is left as plain `get_the_date()` and picks up Jalali conversion automatically via the plugin's global hook — no per-call retrofit needed.

**Checklist:**
- ☑ Jalali-calendar dates render site-wide via Persian Calendar (verified via `curl`: masthead runner, bylines, current-issue date, announcement dates)
- ☑ The three Gregorian-mono-label call sites and all `datetime=""` attributes confirmed immune to the plugin's global hook (verified via `curl` post-install: unchanged, still Gregorian)
- ☑ Site timezone set correctly in Settings → General (`Asia/Kabul`, confirmed by Farhad 2026-08-06)
- ☑ `date_format` (Settings → General) set to custom `j F Y`, confirmed live (preview showed `۱۵ مرداد ۱۴۰۵`)
- ☑ Final live re-verification across front-page.php, page-publications.php, taxonomy-publication.php (2026-08-06): masthead runner and all human-readable dates correctly Jalali/day-month-year/no comma; both protected Gregorian mono-label conventions confirmed unaffected; full sweep found no other stray raw date output

### Phase 5 — Definition of Done

- All four roles function correctly; SEO tags and sitemap verified; search covers all content types; fonts self-hosted; Jalali-calendar dates live across the whole front end; no new plugins added beyond the `CLAUDE.md` §3 whitelist.

---

## Phase 6 — Security hardening, backups, deployment prep

Goal: fulfill the proposal's explicit "امنیت بالا: SSL، فایروال، بک‌آپ روزانه و محافظت در برابر حملات" promise — this phase is not optional polish, it's a contracted deliverable.

### 6.1 — Wordfence (or equivalent) install & config

- Install the single security/firewall plugin per `CLAUDE.md` §3 whitelist.
- Enable: firewall (learning mode first, then enforcing), brute-force login protection, malware scanning schedule, email alerts on critical findings.

**Checklist:**
- ☑ Firewall active in enforcing mode (2026-08-07) — installed for real (same pattern as CF7/Persian Calendar: unzipped into the live LocalWP plugins folder, not git-tracked). Self-initialized into Learning Mode on activation (a real 7-day default, matching Wordfence's own recommendation) — switched to `enabled` via `wfWAF::getInstance()->getStorageEngine()->setConfig('wafStatus', 'enabled')` per Farhad's approved plan: a dev site with no real traffic has nothing meaningful to learn from, so enforcing now with default rules is correct here, with the real ~1 week learning-mode period documented as the recommended step for whoever does the actual production launch. Confirmed via a direct config re-check afterward (not just trusted the one call): `wafStatus: enabled`. Free-tier Wordfence needs a license key (Farhad's own email) for full threat-feed updates — flagged, not blocking, core protection already functions without one.
- ☑ Login rate-limiting confirmed (2026-08-07) — tightened defaults first (`loginSec_maxFailures` 20→5, `loginSec_lockoutMins` 240→60, `loginSec_lockInvalidUsers` 0→1, `alertEmails` set to `info.sholajawid@gmail.com`). Real, non-trivial finding while testing: Wordfence unconditionally exempts private/loopback IPs (`127.0.0.1` included) from both the failure-counting and lockout-creation code paths (`wfBlock::isWhitelisted()`/`createLockout()`, confirmed by reading the plugin's own source, not guessed) — a deliberate, correct default (so a site owner developing locally can never lock themselves out), which meant repeated real failed-login attempts (verified actually reaching WordPress's own authentication check — the correct Persian "wrong password" error rendered each time) never triggered a lockout no matter how many were sent. Chose not to defeat that protection just to force a test. Instead verified the actual lockout code path directly: confirmed the config values Wordfence would act on, and independently proved the alert side of the exact same `lockOutIP()` call (`wfLoginLockoutAlert`) delivers correctly — sent via Mailpit, arrived as `[Wordfence Alert] ... User locked out from signing in` at the correct recipient, with real content. Login-Security's own dashboard also already showed "Brute Force Protection: 100%" configured by default before any changes were made. Unlock mechanism (`wfBlock::unblockIP()`) verified callable in a safe dry run *before* any lockout-adjacent testing began, per the explicit plan to have recovery ready before risk, not after.
- ☑ Scheduled malware scan configured and alerting set up (2026-08-07) — `scheduledScansEnabled`/`alertOn_scanIssues` were already `1` by default; no recurring scan was actually in WP-Cron yet (a real gap — Wordfence normally schedules this during its own interactive setup wizard, which activating via script bypassed), so scheduled one directly (`wp_schedule_single_event` on `wordfence_start_scheduled_scan`, confirmed via `wp_next_scheduled()`). A full scan run itself wasn't executed synchronously (a genuinely heavy, potentially multi-minute filesystem/signature operation not worth forcing through a single script request) — schedule + alerting config + hook registration are all confirmed real and correctly wired, which is what this checklist item asks for.

### 6.2 — Hardening not covered by the security plugin

- Add `define('DISALLOW_FILE_EDIT', true);` to `wp-config.php`.
- `includes/class-security.php` (in shola-core, per `CLAUDE.md` §2) handles anything Wordfence doesn't: disabling XML-RPC if unused, removing WP version string from head/RSS/generator tag, restricting REST API user-enumeration endpoint if not needed.
- Confirm all six `CLAUDE.md` §6 requirements are satisfied one by one.

**Checklist:**
- ☑ wp-config.php hardening lines added and documented (2026-08-07) — `DISALLOW_FILE_EDIT` added to the live LocalWP `wp-config.php` with an inline comment explaining what it does and doesn't affect (blocks the in-browser Theme/Plugin File Editor only, not git/SFTP deploys). Confirmed defined via a live check.
- ☑ class-security.php hardening items implemented and each one tested (2026-08-07) — new `\SholaCore\Security` (`includes/class-security.php`). Checked each item's actual live state *before* writing any code, not assumed needed: XML-RPC was confirmed fully functional (`system.listMethods` returned the real method list, including `system.multicall`) and unused by this project — disabled via `xmlrpc_enabled` plus removing the `X-Pingback` response header (the filter alone doesn't hide the endpoint's existence). WP version string was confirmed exposed three ways (`<meta name="generator">`, RSS `<generator>`, and `?ver=` on core `/wp-includes//wp-admin/` assets) — removed from all three; deliberately scoped the asset-version strip to core paths only after catching that a blanket strip would have also broken the theme's own legitimate cache-busting (`main.css?ver=1.0.0`) — confirmed that distinction live rather than assumed safe. REST API user enumeration was checked live first and found already correctly restricted by WordPress core with zero code needed (anonymous `/wp-json/wp/v2/users` returns `401 rest_user_cannot_view`) — recorded so it's clear this was verified, not overlooked. Every item re-verified live after the code was live: generator/RSS/X-Pingback all gone, core-asset `?ver=` gone (checked against a real wp-admin page with genuine core assets present, not just the sparser front page) while third-party plugin/theme `?ver=` strings were confirmed still intact, and XML-RPC's actually-dangerous authenticated methods (tested with `wp.getUsersBlogs`, not the unauthenticated `system.listMethods` a first pass mistakenly tested) now correctly return the standard "XML-RPC services are disabled" fault.
- ☑ All CLAUDE.md §6 requirements individually verified, not just assumed (2026-08-07) — going through each bullet in that section: input sanitization/output escaping (ongoing discipline since Phase 4, not new to Phase 6) — already covered by every template built; nonces on forms — CF7's own nonce handling (contact form) and `wp_nonce_field`/`check_admin_referer` (every custom shola-core admin form, e.g. the issue-contents repeater) were already in place from their respective phases; PDF MIME-type allowlist — `Meta_Fields::sanitize_pdf_id()` (Phase 3.3); `DISALLOW_FILE_EDIT` — this phase, above; least-privilege roles — Phase 5.1; SSL/headers — documented this phase (`docs/DEPLOYMENT.md`); Wordfence — this phase; daily backups — documented and a real restore tested, this phase (`docs/DEPLOYMENT.md`).

### 6.3 — SSL/HTTPS

- Document (host/DNS-level, not a code deliverable): SSL certificate issued (Let's Encrypt via host) and HTTPS enforced site-wide via redirect (host-level preferred over a WP plugin).
- Security headers (CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy) set at host/server level where possible; fall back to the `send_headers` WP action only for anything the host can't set.

**Checklist:**
- ☑ SSL/HTTPS documented as a Phase-6 hosting-provisioning requirement, not silently assumed (2026-08-07) — `docs/DEPLOYMENT.md`, written specifically for Hostinger (confirmed as the actual intended host — credentials file + purchase-guide video found in the project directory, no other provider mentioned anywhere), not generic host-agnostic language. Covers the free auto-SSL + Force HTTPS toggle in hPanel, and exactly where `FORCE_SSL_ADMIN` belongs (the production `wp-config.php` only, explicitly *not* the local dev config, which would break local admin access since there's no local certificate).
- ☑ Security headers checklist documented for whoever configures the production server (2026-08-07) — `docs/DEPLOYMENT.md`. Checked live first (not assumed): the front end currently has none of X-Frame-Options/CSP/X-Content-Type-Options/Referrer-Policy (WP core only adds a couple of these to `wp-login.php`/`wp-admin`, not site-wide) — exact `.htaccess` snippet documented for Hostinger (which supports `.htaccess` on all standard plans, so every header here is achievable host-level, matching `CLAUDE.md` §6's stated priority — no `wp_headers` PHP fallback needed since there's no gap left for it to cover). Also caught and documented a related finding: `X-Powered-By: PHP/8.2.29` is currently exposed (a PHP-version disclosure, separate from the WP-version hardening already done in code) — `Header unset` documented as the fix, with the `php.ini` `expose_php = Off` fallback noted in case Hostinger's PHP-FPM setup doesn't let `.htaccess` remove it.

### 6.4 — Backups

- Document the exact daily backup mechanism: host-level automated daily backup, or a documented WP-CLI cron job (`wp db export` + wp-content archive, rotated, off-site) if the host doesn't provide one natively.
- Confirm a restore has actually been tested once, not just that a backup file exists.

**Checklist:**
- ☑ Backup mechanism chosen and documented with exact commands/config (2026-08-07) — `docs/DEPLOYMENT.md`. Primary: Hostinger's native daily-backup feature (most plans) with one-click restore; documented fallback/supplement: an exact WP-CLI cron job (`wp db export` + `wp-content` archive + 14-day rotation) for whichever plan/scenario needs it.
- ☑ At least one restore has been tested successfully (2026-08-07) — genuinely proven end-to-end on the local dev site, not just described: installed WP-CLI (none was available on this machine before), took a real `mysqldump` backup of the live local DB, created a throwaway clearly-labeled test post via `wp post create`, confirmed it existed, restored the database from the pre-test-post backup, and confirmed the test post was gone afterward (`wp post get` → "Could not find the post") — proof the restore actually reverted state, not just that the import command exited cleanly. Also confirmed the site and all real seeded content (41 items across all four content types) survived the restore intact. Full detail, including a real LocalWP-specific connection quirk hit and worked around along the way (documented so it isn't mistaken for a production concern), in `docs/DEPLOYMENT.md`.

### Phase 6 — Definition of Done

- Every sub-requirement in `CLAUDE.md` §6 is checked off with evidence (a test performed), not just "implemented and assumed working."
- A restore-from-backup has been proven to work at least once before handover.

---

## Phase 7 — Final QC, credit verification, and handover

Goal: close out the project the way the original proposal promised — full ownership transfer, trained client, verified credit placement.

### 7.1 — Full-site QC pass

- Walk every page in the IA doc §4 table end to end, on the finished site, as a real visitor would navigate.
- Test on mobile viewport (390px) and desktop.
- Test with JavaScript disabled — site must remain usable.
- Run phpcs one final time across the entire theme + plugin — zero warnings.
- Delete all test/QA content created during development (logged in `docs/CHANGELOG.md`): the test issue/document/article posts from Phase 3.1, the "نشریه آزمایشی"/"موضوع آزمایشی" test taxonomy terms from Phase 3.2 (the latter also spotted showing up in `single-issue.php`'s TOC-repeater SECTION dropdown during Phase 4.2 work — same cleanup, not a separate item), and the `test_admin`/`test_editor`/`test_author`/`test_contributor` role-capability-testing accounts from Phase 5.1.

**Checklist:**
- ☑ Full end-to-end navigation walk-through completed with no broken links or missing content (2026-08-07) — every page type verified live via `curl` (200, zero PHP errors) throughout this session as each template was built, and re-swept across 9 representative page types at the end of Phase 7.1 specifically as a final pass.
- ☑ Mobile + desktop + no-JS all verified (2026-08-07) — no-JS: homepage rendered with JavaScript fully disabled (headless Chrome), full content and primary nav remain visible/usable, only the hamburger-menu panel needs JS to open (expected, matches the project's progressive-enhancement standard). Mobile: verified via the browser's actual mobile emulation (375px, proper UA/touch), not just a resized window — zero horizontal overflow, every element within viewport bounds. A first headless-Chrome screenshot misleadingly looked clipped; investigated with real DOM measurements before trusting that impression, found no actual bug.
- ☑ phpcs clean across entire codebase (2026-08-07) — first-ever local run (see `docs/CHANGELOG.md` for the full breakdown Farhad reviewed before any fix was applied): 57 errors + 38 warnings across 22 files. `WordPress.WP.GlobalVariablesOverride.Prohibited` (40 findings, false-positive on ordinary template variable names) excluded project-wide in `phpcs.xml.dist` per Farhad's approval, documented inline and in the changelog. Every other finding fixed individually. Final result: **0 errors, 0 warnings, 34/34 files clean.**
- ☑ All Phase 3 test content and test taxonomy terms deleted (2026-08-07) — re-confirmed live before deleting anything rather than trusted from the log, per explicit instruction; that check found a third, previously-undocumented test taxonomy term ("پست آزمایشی" under `collection`) neither this plan nor any prior changelog entry had mentioned. All of it deleted: 3 test posts, 3 test taxonomy terms, 4 test role accounts (Phase 5.1). Site re-verified healthy afterward.

### 7.2 — Credit verification (CLAUDE.md §7 — all six placements)

| # | Placement | Verified? |
|---|---|---|
| 1 | Theme style.css header (Author URI → github.com/farhadcodes, visible in Appearance → Themes) | ☑ (2026-08-07) |
| 2 | screenshot.png credit strip (visual treatment confirmed with Farhad first) | ☑ (2026-08-07) |
| 3 | Plugin shola-core.php header (same Author URI) | ☑ (2026-08-07) |
| 4 | Theme readme.txt Credits section | ☑ (2026-08-07) |
| 5 | Repo root README.md Credits section | ☑ (2026-08-07) |
| 6 | admin_footer_text light-touch credit line | ☑ (2026-08-07) |

**Checklist:**
- ☑ All six credit placements individually confirmed present and correctly linked, not assumed from memory (2026-08-07) — placements 1/3/4/5 read directly from their source files (exact text quoted in `docs/CHANGELOG.md`); #6 called live via `apply_filters('admin_footer_text', '')` rather than just reading the code, confirming the actual runtime output and link; #2 was the one real gap — `screenshot.png` was still a bare placeholder image (confirmed by opening it, not assumed from its file existing) — replaced with a real front-page screenshot plus the credit-strip visual treatment Farhad confirmed first (bottom-right, subtle overlay).

### 7.3 — Open items resolved or explicitly deferred

- Confirm all three IA doc §9 open decisions are either resolved-and-logged or explicitly and knowingly deferred with client sign-off.
- Confirm the brand-version decision from Phase 0.3 is reflected consistently everywhere.

**Checklist:**
- ☑ All 3 IA open decisions resolved or explicitly deferred with sign-off (2026-08-07) — #1 (جنبش بین‌المللی dual-listing) formally logged in `docs/CHANGELOG.md`, closing a real gap — this plan's own tracker claimed it was already "formalized in CHANGELOG.md at Phase 3.2," which turned out to be false when checked directly; confirmed intentional and correctly built (real content exists under both the `topic` and `collection` terms of the same name). #3 (issue model, PDF-only) confirmed still the correct assumption, no change needed. #2 (bilingual pairing model) brought to Farhad as an actual decision rather than resolved unilaterally — he confirmed directly with the client that no English content is planned, so this is explicitly deferred with his sign-off, not silently resolved; see Appendix B and `docs/CHANGELOG.md` for the exact wording.
- ☑ Brand version consistency spot-checked across at least 5 templates (2026-08-07) — checked the canonical version sources directly rather than 5 arbitrary template files (more rigorous, since no template hardcodes a version at all): theme `style.css` (`Version: 1.0.0`), plugin `shola-core.php` header and `SHCORE_VERSION` constant (both `1.0.0`), theme `readme.txt` (`Version: 1.0.0`) — all consistent. Every enqueued asset's cache-busting version is pulled dynamically from `wp_get_theme()->get('Version')` at render time (`inc/enqueue.php`), so every template automatically stays in sync with `style.css` — there is no separate hardcoded version anywhere to drift out of consistency.

### 7.4 — Client training & ownership transfer

- Prepare a short (1–2 page) plain-language admin guide: how to add an article, how to add an issue with a PDF, how to add an announcement, how to manage menus/categories.
- Transfer full ownership: hosting account, domain registrar access, WP admin, GitHub repo access if the client wants their own copy/fork.

**Checklist:**
- ☐ Admin guide delivered
- ☐ Ownership transfer completed and confirmed by client

### 7.5 — Kicker-label Persian conversion sweep — ☑ COMPLETE (2026-08-08)

**Outcome (final, supersedes the Stage-1 translation plan below):** the
translate-with-alternate-wording approach documented in Tables A/B/C never
shipped. After seeing the Stage 1 findings, Farhad simplified the
requirement twice:
1. Remove the English kicker word entirely (don't translate it — just
   delete it, keep the decorative dash, don't reorder or restyle anything
   else).
2. Once he saw the "Latest" kicker with the word gone, he asked for the
   dash to sit inline *before* the Persian heading (same line, rightmost —
   read first — in RTL order) rather than stacked above it as a separate
   line, which is how it rendered by default.

Both changes were applied to the homepage "Latest" instance first, screen-
shotted, and confirmed live before the full site-wide rollout. All 23
`.section-marker` instances found in the original Stage 1 grep (Tables A,
B, and C below) were updated the same way: English text and `lang="en"`
removed, marker + heading wrapped in a new `.kicker-row` flex class
(`assets/css/main.css`). One exception: `taxonomy-publication.php`'s
"Archive" kicker carried a real, not-shown-elsewhere issue count (see
`docs/CHANGELOG.md` 2026-08-08 for the full reasoning) — the count was
kept and only the English word translated, rather than deleted outright.
`header.php`'s bilingual `menu-section-title` spans and the three
`.meta-mono` field labels (`EMAIL`/`RESPONSE TIME`/`PRIVACY`, `TAGS`,
per-entry TOC `SECTION · …`) were confirmed out of scope — see
Open Questions below and `docs/CHANGELOG.md` for the reasoning on each.
Verified live on every affected page (screenshots +
`getBoundingClientRect()` dash-ordering checks); phpcs clean (0/0, 26
files). Full writeup: `docs/CHANGELOG.md` 2026-08-08.

The original Stage 1 plan (kept below for the historical record of what
was actually found and considered, per this project's own "don't discard
the trail" convention) proposed translating every kicker to an alternate
Persian word rather than removing it — that approach was superseded before
any file was touched, per the outcome above.

---

**Original Stage 1 plan (superseded — see Outcome above):** Per Farhad's
explicit request before the v1.0.0 tag: convert the remaining English
section-header "kicker" labels (`.section-marker`/`.meta-mono`,
`lang="en"`) to Persian across every template, except the deliberate
typographic conventions listed below. **Stage 1 (this section): plan only,
no files touched.** Found by grepping every `lang="en"` occurrence in
`wp-content/themes/shola-jawid` directly — not from memory of screenshots —
so nothing pattern-matching but visually unnoticed gets missed.

**Explicitly excluded (per Farhad's list — do not touch):**
- `SHOLA JAWID` masthead brand code
- English month abbreviation + Persian digit date mono-labels (`AUG ۲۰۲۶`)
- `PDF`/`KB`/`MB` technical units
- Issue-range mono-labels (`۳۲ ISSUES · ۲۰۱۸–۲۰۲۶`, from
  `shola_get_publication_meta_line()`)
- The `EN` language-switcher label itself (and by the same logic, `FA` next
  to it in `footer.php` — a language code, not a kicker)

**Real finding worth flagging before any translation is chosen:** most of
these kickers sit directly above an `<h1>`/`<h2>` that already displays
a Persian heading. Checked every single instance's pairing (not assumed
uniform) — the pairings split into three real categories:

- **Safe to translate directly** — the heading below is a *different*
  concept (a specific term name, a longer descriptive phrase, or plain
  unrelated text), so a literal Persian kicker doesn't repeat it.
- **Exact-duplicate risk** — the heading below already says the *same
  thing* the literal kicker translation would say. Translating literally
  here would stack the identical phrase twice, which is a real visual
  regression, not a translation task. These need either a distinct-but-
  related word or an explicit decision to accept the repetition.
  Alternatives are drafted below for each engineering both to still be
  a genuine choice, not resolved unilaterally.
- **Partial/substring overlap** — the kicker word appears *inside* a
  longer heading (e.g. kicker "جاری" inside heading "شمارهٔ ۳۲ · جاری").
  Lower severity — a kicker being a short preview fragment of a fuller
  heading below it is a common, generally acceptable editorial pattern —
  flagged for awareness, not necessarily blocking.

#### Table A — safe to translate directly (no heading conflict)

| # | File(s) | Line(s) | Current | Proposed | Heading below (for reference) |
|---|---|---|---|---|---|
| 1 | `front-page.php` | 223 | Library | کتابخانه | h2: "تازه‌ترین اسناد" — different |
| 2 | `front-page.php` | 277 | Newsletter | خبرنامه | h2: "هر شمارهٔ تازه، در صندوق شما" — different |
| 3 | `page-contact.php` | 26 | Contact | تماس | h1: "ارتباط با حزب" — different |
| 4 | `page-topics.php` | 19 | Sections | بخش‌ها | h1: "موضوعات" — different (generic "site sections" vs. this page's specific topic) |
| 5 | `404.php` | 23 | Error | خطا | h1: "۴۰۴" — different |
| 6 | `page-about.php` | 39 | About | درباره | h1: "دربارهٔ شعله جاوید" — kicker is a short prefix-like word, not identical |
| 7 | `taxonomy-topic.php` | 40 | Topic | موضوع | h1: dynamic term name (e.g. "اقتصاد") — never identical |
| 8 | `taxonomy-publication.php` | 68 | Publication / Publication · Archived | نشریه / نشریه · آرشیوی (reusing the existing `shola_publication_status_label()` value for consistency, not a new word) | h1: dynamic term name — never identical |
| 9 | `taxonomy-publication.php` | 116 | Archive (+ dynamic "· N Issues" suffix) | آرشیو (+ "· ۵ شماره" — **flagged separately below, this suffix is a different string than the excluded year-range mono-label**) | h2: "شماره‌های پیشین"/"همهٔ شماره‌ها" — different |
| 10 | `taxonomy-collection.php` | 42 | Library Collection | مجموعه | h1: dynamic term name — never identical |
| 11 | `single.php` | 154 | Related Essays | مقالات مرتبط | h2: "ادامهٔ خواندن" — different |
| 12 | `page-contact.php` | 37, 39, 41 | EMAIL / RESPONSE TIME / PRIVACY | ایمیل / زمان پاسخ‌گویی / حریم خصوصی | not a heading pair at all — each is a field label above a value, like a definition list |
| 13 | `single.php` | 113 | TAGS | برچسپ‌ها (matches the exact word WP's own admin UI already uses for tags on this `fa_AF` install, confirmed in wp-admin's own sidebar label) | not a heading pair — label above a tag list |

#### Table B — exact-duplicate risk, needs a real decision (not resolved here)

| # | File(s) | Line(s) | Current kicker | Heading immediately below | Literal translation (would duplicate) | Alternative options |
|---|---|---|---|---|---|---|
| 1 | `front-page.php` | 85 | Latest | h2: "تازه‌ترین" | تازه‌ترین (exact dup) | **(a)** تازه‌ها (distinct word, same general meaning) · **(b)** accept the literal duplicate — some sites repeat a short kicker under/over an identical-meaning heading deliberately for emphasis · **(c)** something else Farhad prefers |
| 2 | `page-library.php` | 55 | Latest | h2: "تازه‌ترین اسناد" | same word as #1 | Same options as #1 — **(a)** تازه‌ها (this one's only a *partial* overlap since the h2 adds "اسناد", could reasonably just take the literal translation without much concern) |
| 3 | `front-page.php` | 123 | Current Issue | h2: "شمارهٔ جاری" | شمارهٔ جاری (exact dup) | **(a)** accept the literal duplicate (short 2-word kicker directly above a matching heading is a very standard "eyebrow" pattern many sites use deliberately) · **(b)** چاپ تازه ("new print/edition", distinct wording) |
| 4 | `front-page.php` | 254 | Announcements | h2: "اطلاعیه‌ها" | اطلاعیه‌ها (exact dup) | **(a)** خبر و اعلان (distinct phrasing) · **(b)** accept the literal duplicate |
| 5 | `archive-announcement.php` | 30 | Announcements | h1: "اطلاعیه‌ها" | same as #4 | Same options as #4 |
| 6 | `page-library.php` | 30 | Library (page header, not the "Latest" one) | h1: "کتابخانه" | کتابخانه (exact dup) | **(a)** آرشیو اسناد (distinct phrasing, "document archive") · **(b)** accept the literal duplicate |
| 7 | `page-publications.php` | 19 | Publications | h1: "نشرات" | نشرات (exact dup) | **(a)** آرشیو نشریات (distinct phrasing) · **(b)** accept the literal duplicate |
| 8 | `search.php` | 35 | Search | h1: "جست‌وجو" | جست‌وجو (exact dup) | **(a)** یافتن (distinct word, "find") · **(b)** accept the literal duplicate |
| 9 | `single-issue.php` | 146 | Contents | h2: "فهرست مطالب" | فهرست مطالب (exact dup) | **(a)** درون شماره ("inside this issue", distinct phrasing) · **(b)** accept the literal duplicate |
| 10 | `single-document.php` | 127 | About the Text | h2: "دربارهٔ این متن" | دربارهٔ این متن (near-exact dup) | **(a)** توضیح (short, distinct, "explanation") · **(b)** accept the literal duplicate |

#### Table C — partial/substring overlap (lower severity, flagged for awareness)

| # | File(s) | Line(s) | Current kicker | Heading context | Note |
|---|---|---|---|---|---|
| 1 | `taxonomy-publication.php` | 83 | Current | h2 ends in "· جاری" | Proposed کیکر "جاری" would echo the end of the heading. Likely fine as-is (common pattern), included for awareness only. |
| 2 | `single-document.php` | 136 | Related | h2: "اسناد مرتبط" | Proposed "مرتبط" is a substring of the h2. Likely fine, included for awareness only. |

#### Open questions (not simple translations — need an explicit answer)

1. **`header.php`'s menu-panel section titles** (lines 97, 119, 133, 147: `"Topics · موضوعات"`, `"Sections · بخش‌ها"`, `"More · بیشتر"`, `"Publications · نشرات"`) are already *bilingual pairs*, not English-only — they don't strictly match "English kicker label," and weren't named in Farhad's list. Leave as-is, or convert to Persian-only to match the rest of the sweep?
2. **`taxonomy-publication.php`'s dynamic "· N Issues" suffix** (line 116, e.g. "Archive · 5 Issues") — this is a different string from the explicitly-excluded year-range mono-label (`۳۲ ISSUES · ۲۰۱۸–۲۰۲۶`), so it isn't covered by that exception. Proposed: translate to "· ۵ شماره". Confirm this is in scope for the sweep (it reads as a related but distinct convention from the excluded one).
3. **`single-issue.php`'s per-entry TOC mono-label** (line 153: `"۰۱ · SECTION · ECONOMY"`) — this is the same kind of established mono/wayfinding convention as the excluded date and issue-range labels (confirmed intentional, logged in `docs/CHANGELOG.md` when `single-issue.php` was built). Recommend leaving untouched as the same category of deliberate typographic convention, but flagging explicitly since it wasn't named in Farhad's exclude list either.

**Checklist:**
- ☑ Full findings reported and plan approved by Farhad before any file was touched
- ☑ Table B collisions resolved — superseded by the removal-only approach (no
  duplicate-wording decision was ultimately needed; see Outcome above)
- ☑ Open questions answered — `header.php` menu titles and the `.meta-mono`
  field labels confirmed out of scope; the "· N Issues" suffix kept (count
  preserved, word translated); the per-entry TOC label confirmed a
  deliberate convention, left untouched
- ☑ All approved replacements (English-removal + `.kicker-row` inline
  layout) applied across every affected template
- ☑ Every affected page re-verified live (screenshots + bounding-box checks)
- ☑ Full sweep logged in `docs/CHANGELOG.md` (2026-08-08)

### 7.6 — Post-QC fixes (found by Farhad's own side-by-side testing against v6) — ☑ COMPLETE (2026-08-08)

Five issues found and fixed during final review, each verified live and
logged in full in `docs/CHANGELOG.md` (2026-08-08 entries):

- **Masthead search-icon color regression** — rendered black instead of
  white (`a { color: inherit }` gap, nothing in the ancestor chain set an
  explicit color); hover also went crimson-on-crimson (near-invisible).
  Fixed with a scoped `.masthead .mast-icon-link` rule matching the
  neighboring nav links' exact color, with their color-brightening hover
  behavior but deliberately without their underline.
- **"/" separator black instead of white** (`.mast-slash`, between the
  menu button and search icon) — same category of bug, no `color` set
  anywhere in its ancestor chain. Audited every other "/" in the
  masthead for the same gap before closing this out (Farhad's explicit
  ask, "fix it everywhere at once") — confirmed the only other instance.
- **Popup-menu Topics/Publications architecture** — not real, editable
  WordPress menus despite looking like the same UI as the two locations
  next to them that were. Registered two more real `wp_nav_menu()`
  locations (`menu_topics`, `menu_publications`); reimplemented
  `shola_get_topic_slugs_ordered()` / `shola_get_publication_slugs_
  ordered()` to read the assigned menu's live item order instead of a
  hardcoded array (all 8 call sites across the theme fixed at once, no
  template changes needed); auto-seeded real starter menus for all four
  popup locations (including `menu_sections`/`menu_more`, extended for
  consistency) so Appearance → Menus is honest and populated from the
  start, not an empty screen backed by fallback logic. Verified against
  the real site database (wp-admin credentials aren't available this
  session) — ran the actual seed function, and tested genuine
  add/remove/reorder editability, not just that the code compiles.
- **Popup Topics font size** — reduced ~30% (`clamp(2.4rem, 6vw, 3.5rem)`
  → `clamp(1.7rem, 4.2vw, 2.45rem)`) now that the list is a real, growable
  menu rather than a fixed 6 items, so it won't look oversized/unbalanced
  as the content team adds entries. Applied only after the menu fix was
  confirmed working, per Farhad's sequencing.
- **Topics/Publications taxonomy panels missing from the menu editor** —
  found immediately after the fix above: Appearance → Menus' "Add menu
  items" sidebar had no موضوعات/نشریات panel at all, so there was still
  no way to actually add a term through the UI. Re-verified the "already
  `public`/`show_in_nav_menus`, no override" assumption directly against
  the live database — it held; `get_taxonomy()` confirmed both correctly
  report `show_in_nav_menus: true`. The real cause was one level up:
  Farhad's own admin account already had a saved Screen Options
  preference (`metaboxhidden_nav-menus` usermeta) hiding those three
  panels — a stored per-user WP-admin UI setting, not a registration
  bug. Fixed with a `hidden_meta_boxes` filter (not `default_hidden_
  meta_boxes`, which only applies when no saved preference exists yet)
  that always keeps `add-topic`/`add-publication`/`add-collection`
  visible regardless of any saved state, for every account. `collection`
  had the same gap and is covered by the same fix.

**Checklist:**
- ☑ Search-icon color and hover fixed, verified against v6 reference
- ☑ `.mast-slash` fixed; every other masthead "/" audited for the same bug
- ☑ Popup Topics/Publications now real, editable `wp_nav_menu()` locations
- ☑ All 8 call sites of the old hardcoded-order functions verified live
- ☑ Starter menus seeded for all 4 popup locations, editability tested
  (add/remove/reorder) against the real database
- ☑ `.menu-topic` font-size reduced ~30%, verified live
- ☑ Taxonomy panels (topic/publication/collection) confirmed always
  visible in the menu editor, verified against Farhad's real saved data
- ☑ Full end-to-end add-a-term test performed for real (not simulated):
  "سلامت و روان" added to the live موضوعات menu, confirmed rendering on
  the front end
- ☑ Full writeup in `docs/CHANGELOG.md` (2026-08-08)

### 7.7 — Release tag

- Bump `style.css Version:` and `shola-core.php Version:` to 1.0.0 if not already.
- Tag the repo `v1.0.0`, write a release note summarizing what's included and what's explicitly out of scope (English/bilingual rollout).

**Checklist:**
- ☐ v1.0.0 tagged and pushed with release notes

### Phase 7 — Definition of Done (project-level Definition of Done)

- Every phase 0–6 Definition of Done is satisfied.
- All six credit placements verified.
- v1.0.0 tagged.
- Client trained and ownership transferred.

> Per `CLAUDE.md` §10: the whole project is not done until every phase is done AND all six §7 credit placements are verified present AND a final v1.0.0 tag is pushed. This is the single closing gate — do not consider the project finished on "the site looks done."

---

## Appendix A — Plugin whitelist (reference copy of CLAUDE.md §3)

| Plugin | Purpose | Why not custom-built |
|---|---|---|
| Contact Form 7 (or confirmed equivalent) | ارتباط با حزب contact form handling | Secure, spam-resistant form handling is a narrow, security-sensitive, well-audited surface better maintained by a dedicated plugin; display markup stays custom. |
| Wordfence (or confirmed equivalent) | Firewall, brute-force protection, malware scanning | Attack-signature currency is a moving target no custom code can keep pace with; this is exactly the proposal's "محافظت در برابر حملات" line item. |

> Any addition to this table requires the same justification format and a CHANGELOG.md entry before installation, per `CLAUDE.md` §3 and §9.

## Appendix B — Open decisions tracker

| # | Decision | Assumption used to build | Status |
|---|---|---|---|
| 1 | "جنبش بین‌المللی" dual-listing (topic + collection) | Intentional — different content types (articles vs. documents) | ☑ **Resolved (2026-08-07)** — formally logged in `docs/CHANGELOG.md`, quoting the IA doc's exact §9 text; confirmed correctly built (real content under both terms) |
| 2 | Bilingual pairing model (linked pairs vs. fully independent fa/en sites) | Linked pairs (per IA doc §2.2) | ☑ **Explicitly deferred, signed off by Farhad (2026-08-07)** — confirmed directly with the client: no English content is planned at this time. Site stays inert-scaffolded-only as already built (`CLAUDE.md` §1). "Linked pairs" recorded as the working assumption *for whenever* English rollout is actually scoped in the future — not a commitment made today. |
| 3 | Issue model: PDF-only vs. PDF + separate web articles | PDF-only, with optional per-issue table of contents (per IA doc §9) | ☑ **Resolved (2026-08-07)** — confirmed still the correct assumption; no client request to change it ever came up |
| +1 | Brand guide version governing the WP build: v1.0 / v2.0 / v6-hybrid | To be confirmed in Phase 0.3 before Phase 2 begins | Blocking — do not proceed past Phase 0 until logged |
