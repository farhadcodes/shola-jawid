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
- Rewrite slugs match the IA doc §4 URL table exactly: `/library/{collection}/{slug}`, `/publications/{publication}/{issue}`, etc.

**Checklist:**
- ☐ issue, document, announcement CPTs registered with correct labels (Persian), supports, and rewrite slugs matching IA doc §4

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
- Build `template-parts/cards/card.php` as the shared card partial for **articles only** (no `type` param — see correction below).

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
- ☐ Contact form visually matches v6 contact.html
- ☐ Form submits successfully and arrives by email in local testing (use LocalWP's built-in mail catcher)

### 4.4 — Per-template visual QC

- For each template in 4.2, take a screenshot of the rendered WP page and place it beside the corresponding v6 static screenshot in `docs/screenshots/phase4/` — side by side, not just "looks about right from memory."
- Any pixel-level brand-critical property (crimson masthead RGB, drop-cap, card anatomy) gets the same kind of pixel-verification treatment the v6 prototype itself received.

**Checklist:**
- ☐ All 15 templates have a saved side-by-side screenshot comparison
- ☐ No unexplained visual drift from the (refactored) v6 prototype

### Phase 4 — Definition of Done

- Every page in the IA doc's page list (§4) exists, renders real dynamic WordPress content (not static placeholder text), and visually matches the v6 prototype.
- Site is fully navigable end-to-end (nav → topic archive → single article → related content) using only real WP data, no leftover static HTML files being referenced.
- ☐ **Zero inline `style="..."` attributes remain in any finished PHP template** (`grep -r 'style="' wp-content/themes/shola-jawid --include=*.php` returns nothing), per `CLAUDE.md` §5 and the Phase 1.3 deferral above.
- ☐ **`header.php` does not reproduce the `_header.html:6-12` nested-button/link markup bug** — `#menu-open` and the search link are siblings, verified in rendered output, not just visually.

---

## Phase 5 — Roles, SEO, search, performance

Goal: the functional non-visual requirements from the proposal's امکانات کلیدی and الزامات غیرکارکردی lists.

### 5.1 — User roles & permissions

- Map مدیر → Administrator, سردبیر → Editor, نویسنده → Author, همکار → Contributor (all native WP roles — capabilities already match the IA doc §7).
- Confirm no custom role/capability plugin is needed — a zero-plugin win per `CLAUDE.md` §3.

**Checklist:**
- ☐ Four accounts created (one per role) and spot-tested: each can/cannot do what the IA doc §7 specifies

### 5.2 — Custom SEO (no SEO plugin, per CLAUDE.md §3)

- `includes/class-seo.php` outputs: dynamic `<title>`, meta description, Open Graph tags, canonical URL.
- Use WordPress core's native `wp_sitemaps_*` filters to theme/brand the auto-generated `sitemap.xml` rather than hand-rolling one from scratch.
- Add hreflang scaffolding (inert/self-referential only, since English isn't live) so the eventual bilingual rollout doesn't require re-touching this file.

**Checklist:**
- ☐ Meta tags verified in page source on at least one of each template type
- ☐ sitemap.xml accessible and includes posts, issues, documents, announcements

### 5.3 — Search

- `search.php` uses native WP search (`pre_get_posts` filter to include the custom post types in results).
- Style to match v6's search.html exactly.

**Checklist:**
- ☐ Search returns results across posts, issues, and documents, not just native posts

### 5.4 — Performance baseline

- Self-host all fonts (Vazirmatn, Markazi Text, Newsreader if/when English is live, JetBrains Mono) in `assets/fonts/`, enqueued with proper `font-display: swap`.
- Confirm native WP image sizes/srcset are actually serving responsive images, not just the full-size original.
- Run a baseline Lighthouse/PageSpeed pass on the LocalWP site.
- Only if this baseline pass reveals a genuine caching gap that host-level caching (Phase 6) can't cover: flag it as a candidate for the `CLAUDE.md` §3 whitelist discussion. Do not install a caching plugin pre-emptively.

**Checklist:**
- ☐ Fonts self-hosted, font-display: swap confirmed
- ☐ Responsive images confirmed serving srcset, not full-size only
- ☐ Baseline performance pass documented in docs/screenshots/phase5-perf-baseline.md

### Phase 5 — Definition of Done

- All four roles function correctly; SEO tags and sitemap verified; search covers all content types; fonts self-hosted; no new plugins added beyond the `CLAUDE.md` §3 whitelist.

---

## Phase 6 — Security hardening, backups, deployment prep

Goal: fulfill the proposal's explicit "امنیت بالا: SSL، فایروال، بک‌آپ روزانه و محافظت در برابر حملات" promise — this phase is not optional polish, it's a contracted deliverable.

### 6.1 — Wordfence (or equivalent) install & config

- Install the single security/firewall plugin per `CLAUDE.md` §3 whitelist.
- Enable: firewall (learning mode first, then enforcing), brute-force login protection, malware scanning schedule, email alerts on critical findings.

**Checklist:**
- ☐ Firewall active in enforcing mode
- ☐ Login rate-limiting confirmed (test with deliberate repeated failed logins)
- ☐ Scheduled malware scan configured and alerting set up

### 6.2 — Hardening not covered by the security plugin

- Add `define('DISALLOW_FILE_EDIT', true);` to `wp-config.php`.
- `includes/class-security.php` (in shola-core, per `CLAUDE.md` §2) handles anything Wordfence doesn't: disabling XML-RPC if unused, removing WP version string from head/RSS/generator tag, restricting REST API user-enumeration endpoint if not needed.
- Confirm all six `CLAUDE.md` §6 requirements are satisfied one by one.

**Checklist:**
- ☐ wp-config.php hardening lines added and documented
- ☐ class-security.php hardening items implemented and each one tested
- ☐ All six CLAUDE.md §6 requirements individually verified, not just assumed

### 6.3 — SSL/HTTPS

- Document (host/DNS-level, not a code deliverable): SSL certificate issued (Let's Encrypt via host) and HTTPS enforced site-wide via redirect (host-level preferred over a WP plugin).
- Security headers (CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy) set at host/server level where possible; fall back to the `send_headers` WP action only for anything the host can't set.

**Checklist:**
- ☐ SSL/HTTPS documented as a Phase-6 hosting-provisioning requirement, not silently assumed
- ☐ Security headers checklist documented for whoever configures the production server

### 6.4 — Backups

- Document the exact daily backup mechanism: host-level automated daily backup, or a documented WP-CLI cron job (`wp db export` + wp-content archive, rotated, off-site) if the host doesn't provide one natively.
- Confirm a restore has actually been tested once, not just that a backup file exists.

**Checklist:**
- ☐ Backup mechanism chosen and documented with exact commands/config
- ☐ At least one restore has been tested successfully

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

**Checklist:**
- ☐ Full end-to-end navigation walk-through completed with no broken links or missing content
- ☐ Mobile + desktop + no-JS all verified
- ☐ phpcs clean across entire codebase

### 7.2 — Credit verification (CLAUDE.md §7 — all six placements)

| # | Placement | Verified? |
|---|---|---|
| 1 | Theme style.css header (Author URI → github.com/farhadcodes, visible in Appearance → Themes) | ☐ |
| 2 | screenshot.png credit strip (visual treatment confirmed with Farhad first) | ☐ |
| 3 | Plugin shola-core.php header (same Author URI) | ☐ |
| 4 | Theme readme.txt Credits section | ☐ |
| 5 | Repo root README.md Credits section | ☐ |
| 6 | admin_footer_text light-touch credit line | ☐ |

**Checklist:**
- ☐ All six credit placements individually confirmed present and correctly linked, not assumed from memory

### 7.3 — Open items resolved or explicitly deferred

- Confirm all three IA doc §9 open decisions are either resolved-and-logged or explicitly and knowingly deferred with client sign-off.
- Confirm the brand-version decision from Phase 0.3 is reflected consistently everywhere.

**Checklist:**
- ☐ All 3 IA open decisions resolved or explicitly deferred with sign-off
- ☐ Brand version consistency spot-checked across at least 5 templates

### 7.4 — Client training & ownership transfer

- Prepare a short (1–2 page) plain-language admin guide: how to add an article, how to add an issue with a PDF, how to add an announcement, how to manage menus/categories.
- Transfer full ownership: hosting account, domain registrar access, WP admin, GitHub repo access if the client wants their own copy/fork.

**Checklist:**
- ☐ Admin guide delivered
- ☐ Ownership transfer completed and confirmed by client

### 7.5 — Release tag

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
| 1 | "جنبش بین‌المللی" dual-listing (topic + collection) | Intentional — different content types (articles vs. documents) | Confirmed in IA doc itself; formalize in CHANGELOG.md at Phase 3.2 |
| 2 | Bilingual pairing model (linked pairs vs. fully independent fa/en sites) | Linked pairs (per IA doc §2.2) | Inert/scaffolded only in this phase per CLAUDE.md §1 — not truly resolved until English rollout is scoped |
| 3 | Issue model: PDF-only vs. PDF + separate web articles | PDF-only, with optional per-issue table of contents (per IA doc §9) | Build against this assumption; revisit only if client requests otherwise |
| +1 | Brand guide version governing the WP build: v1.0 / v2.0 / v6-hybrid | To be confirmed in Phase 0.3 before Phase 2 begins | Blocking — do not proceed past Phase 0 until logged |
