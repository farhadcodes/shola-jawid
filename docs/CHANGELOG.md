# Changelog — Shola Jawid WordPress Theme

This file tracks **decisions**, not routine commits. Git history already shows
every file change; this file exists so that if something goes wrong, is
inconsistent, or needs re-evaluating months from now, there's a short, readable
trail of *why* the build deviated from — or newly applied — a rule in
`CLAUDE.md`, and who approved it.

## What belongs here

- Any time a rule in `CLAUDE.md` was ambiguous and had to be interpreted for a
  specific case.
- Any time a rule was deviated from, with reasoning and approval.
- Any addition to the plugin whitelist (§3), or any other rules-file edit.
- Resolution of one of the three open IA decisions (IA doc §9) once Farhad
  confirms it — record which option was chosen and when.
- Any phase that shipped with a known gap or a deferred item, and why.

## What does NOT belong here

- Ordinary feature work ("built front-page.php hero loop") — that's a normal git
  commit message, not a changelog entry.
- Visual/content tweaks with no rule implication.

## Format

```md
## YYYY-MM-DD
- **[Changed / Added / Resolved / Deferred]:** One-line summary.
  Reason: why.
  Approved by: Farhad (session date / method), or "self-resolved per CLAUDE.md §X"
  if the rules file already covered it unambiguously.
```

---

## 2026-08-05
- **Added:** Initial `CLAUDE.md` rules file and this changelog created ahead of
  Phase 0. Key decisions locked in this session:
  - Bilingual-ready scaffold, Persian-only active content (§1).
  - `shola-core` kept as a separate plugin from the theme, per standard WP
    maintainability practice (§2).
  - Plugin dependency policy set to fixed whitelist: Contact Form 7 (or
    equivalent) + Wordfence (or equivalent) only; everything else custom-built
    (§3).
  - New GitHub repo `shola-jawid` to be created in Phase 0 (§8).
  - Local dev environment: LocalWP (Local by Flywheel).
  - Theme slug confirmed as `shola-jawid`.
  Approved by: Farhad, in this session (2026-08-05).

- **Changed:** Phase 0.2's LocalWP↔repo symlinking step was implemented with
  Windows directory junctions (`New-Item -ItemType Junction`) instead of real
  symlinks (`New-Item -ItemType SymbolicLink`) for
  `wp-content/themes/shola-jawid` and `wp-content/plugins/shola-core`.
  Reason: creating true symlinks on this machine requires Administrator
  privilege, which this session/account doesn't have; directory junctions
  achieve the same practical outcome (local dev files and version-controlled
  repo files are literally the same files on disk, verified live by editing
  through the repo path and reading back through the LocalWP path) without
  elevation. No other behavioral difference expected for this project's
  purposes.
  Approved by: Farhad, in this session (2026-08-05).

- **Deferred/Noted:** Two unrelated items surfaced while verifying Phase 0.2 —
  logged for awareness only, no action taken or needed:
  - A stale, unrelated folder exists at
    `D:\18 - Freelance\03 - Fekri Website\shola-jawid-wp-theme` (different
    project structure — `RULES.md`, `package.json`, `node_modules` — not this
    project's `CLAUDE.md`/`EXECUTION_PLAN.md` architecture). Not touched.
  - Farhad's initial Phase 0.2 status report (PHP/WP versions, LocalWP site
    path) contained unfilled template placeholders (e.g. literal
    `YOUR_WINDOWS_USERNAME`) rather than verified values; the real values were
    independently confirmed against the filesystem and Local's own site config
    before proceeding (see [[feedback_verify_dont_assume]] memory).
  Approved by: Farhad, in this session (2026-08-05).

## 2026-08-05 (continued)
- **Resolved:** Phase 0.3's blocking brand-version question (v1.0 vs v2.0 vs
  v6-hybrid) is closed. There is no choice to make between v1.0 and v2.0 for
  this build: the finished `shola-jawid-ui` v6 "Quiet Press" prototype in
  `03_UI_Design/` is the sole design source, and its own README documents
  that v6 already abandoned v2.0's 45/40/15 field rule and returned to
  v1.0's 60/30/10 discipline (Aeon-reinterpreted), after v5 (which followed
  v2.0) was live-reviewed with the client and read as "corporate." All
  eleven brand tokens are preserved; only their application changed. Per
  `CLAUDE.md` §9, the WP build is a faithful port of v6 exactly as built —
  neither v1.0 nor v2.0 independently governs any visual decision.
  Full explanation logged in `docs/IA-reference/README.md`.
  Reason: Farhad reconfirmed the project is a pixel-accurate port with zero
  visual-improvement exceptions, and pointed to `03_UI_Design` as the single
  design source — this made clear the "brand version" checklist item was
  already answered by the v6 prototype itself, not an open decision to make.
  Approved by: Farhad, in this session (2026-08-05).

- **Added:** `docs/IA-reference/` (brand guide v1.0 EN/FA, IA & sitemap doc,
  Aeon design-system analysis) and `docs/IA-reference/README.md` committed,
  per `CLAUDE.md` §4 / Execution Plan 0.3.
  Approved by: Farhad, in this session (2026-08-05).

- **Added:** Phase 0 scaffolding/tooling closed out: root `LICENSE` (GPLv2,
  fetched verbatim from `gnu.org/licenses/old-licenses/gpl-2.0.txt` rather
  than reproduced from memory), `.gitignore`, `phpcs.xml.dist`
  (WordPress-Extra + PHPCompatibilityWP, PHP 8.1+, WP 6.4+), root
  `README.md` with Credits section, and `.github/workflows/lint.yml`
  (installs WPCS via Composer, runs phpcs on push/PR to `main`).
  Approved by: Farhad, in this session (2026-08-05).

## 2026-08-05 (continued)
- **Changed:** `docs/EXECUTION_PLAN.md` §4.1's card-partial description was
  wrong and has been corrected (see matching note added directly in that
  section). The plan originally called for one shared
  `template-parts/cards/card.php` partial "accepting a type parameter
  (`article` | `issue` | `document`) to swap only the tag/label." Phase 1.2's
  markup/CSS comparison across the v6 prototype's five card/list patterns
  (`.card`, `.issue-card`, `.doc-row`, `.announce-list`, `.topic-list`)
  showed this doesn't match what v6 actually built:
  - `.issue-card` (`assets/css/main.css:997-1005` in
    `03_UI_Design/shola-jawid-ui/`) uses a 3:4 portrait image and, critically,
    `box-shadow: 0 10px 25px -12px rgba(15,15,15,.25)` at line 1002 —
    deliberately breaking CSS §09's own "no borders, no shadow,
    whitespace-only" rule that governs `.card`. It also carries no
    type-label, dek, or byline field. This is a second, intentionally
    distinct card anatomy (issue covers read as physical shelved objects),
    not a type-swapped variant of `.card`.
  - `.doc-row`, `.announce-list`, and `.topic-list` have no image field at
    all and are flex/list rows, not cards under any definition — documents
    render exclusively as `.doc-row` list rows in v6; there is no
    document-type card anywhere in the prototype.
  Corrected plan (per Farhad's approval): `template-parts/cards/card.php`
  serves articles only, with no `type` param (nothing else uses this
  anatomy); a separate `template-parts/cards/issue-card.php` serves issue
  covers; `.doc-row`, `.announce-list`, and `.topic-list` each get their own
  row/list partial outside `template-parts/cards/`, since they are not
  cards.
  Reason: the plan's "single shared card partial" wording was written
  against the Aeon design-system analysis's general card-discipline
  principle before the actual v6 CSS/HTML was line-by-line compared; the
  build must follow what v6 actually shipped (per `CLAUDE.md` §9 faithful-
  port rule), not the plan's a-priori assumption. This is a build-structure
  correction only — no visual output changes.
  Approved by: Farhad, in this session (2026-08-05).

## 2026-08-05 (continued)
- **Deferred:** Phase 1.3's two hygiene findings — 440 inline `style="..."`
  attributes across the v6 prototype's 23 pages, and invalid
  nested-interactive markup at `_header.html:6-12` (search `<a>` nested
  inside `#menu-open` `<button>`) — are not fixed in the static prototype.
  Both are explicitly deferred to Phase 4, to be resolved template-by-
  template as each PHP template is converted, rather than patched in the
  static HTML now. `docs/EXECUTION_PLAN.md` §4.1/§4.2 and Phase 4's
  Definition of Done were amended with concrete, checkable items for both
  (not just a note in Phase 1's writeup): zero inline styles may remain in
  any finished PHP template (`CLAUDE.md` §5), and `header.php` must render
  `#menu-open` and the search link as siblings, not nested.
  Reason: fixing 440 call sites and one markup bug in files about to be
  entirely rewritten as PHP is double-work with avoidable visual-regression
  risk; doing it once, correctly, during the real conversion is safer and
  faster than a static-HTML patch pass that gets thrown away at Phase 4
  anyway.
  Approved by: Farhad, in this session (2026-08-05).

- **Resolved:** Phase 1 (Prototype audit & refactor-before-convert) is
  complete. `docs/EXECUTION_PLAN.md`'s Phase 1 Definition of Done updated
  to reflect actual findings (audit-only, no prototype files modified;
  physical-CSS and card-duplication checks clean; inline-styles and
  header markup findings explicitly deferred to Phase 4 per above, not
  silently dropped). The audited v6 prototype at
  `03_UI_Design/shola-jawid-ui/` — unmodified, per the audit-only scope
  Farhad set for this phase — is confirmed as the carried-forward source
  of truth into Phase 2 onward.
  Approved by: Farhad, in this session (2026-08-05).

## 2026-08-05 (continued)
- **Changed:** Added a minimal `index.php` to Phase 2.1's theme-skeleton
  scope (`docs/EXECUTION_PLAN.md` §2.1). It was missing: Phase 2.1's file
  list, ported from `CLAUDE.md` §4's tree, didn't include it, and
  attempting to activate the theme in wp-admin surfaced WordPress core's
  "Broken Theme — Template is missing" error, since `index.php` (with
  `style.css`) is one of the two files WP core requires for a theme to be
  non-broken (`WP_Theme::errors()`), independent of the template hierarchy
  and independent of `front-page.php` etc. existing later in Phase 4.
  Considered two options before deciding: (1) add a minimal `index.php`
  now, or (2) accept activation can't be verified until Phase 4 and reword
  Phase 2's Definition of Done to check "no PHP errors" via WP-CLI/debug
  log instead of literal wp-admin activation. Rejected option 2 on
  inspection — it doesn't actually work, since a theme WP considers
  "broken" can't be activated via WP-CLI either (`wp theme activate` fails
  the same structural check `switch_theme()` does); there's no route to
  verifying "no PHP errors on activation" without first satisfying WP's
  baseline file requirement. Chose option 1: `index.php` is standard,
  required WP theme scaffolding — not Phase-4 template/design work — so
  adding it doesn't blur the phase boundary. Kept deliberately minimal
  (bare loop, inline `wp_head()`/`wp_footer()`, no styling) and does not
  call `get_header()`/`get_footer()` since those partials don't exist
  until Phase 4.1 (calling them earlier triggers a `_doing_it_wrong`
  notice under `WP_DEBUG`, which would itself violate the "zero PHP
  errors/warnings" checklist item this is meant to satisfy). Will be
  refactored to call `get_header()`/`get_footer()` once Phase 4.1 lands.
  Reason: this is a standard, well-known WordPress theme-development
  sequencing question (skeleton-before-templates), not a project-specific
  judgment call — WP's own two-required-files rule settles it.
  Approved by: Farhad, in this session (2026-08-05).

## 2026-08-05 (continued)
- **Changed:** `docs/EXECUTION_PLAN.md` §3.1's rewrite-slug guidance
  amended to drop the `/fa/` locale prefix that the IA doc's §4 URL table
  uses literally (confirmed by extracting the doc's actual text, not
  relying on memory/summary). The IA doc's own §2.2 states the `/fa/` /
  `/en/` prefixes are part of a locale-routing strategy; building the
  `/fa/` half now, with no `/en/` counterpart going live this phase, would
  mean committing to a specific future i18n URL scheme (subdirectory vs.
  subdomain vs. domain — Polylang and WPML default to different ones)
  before that decision has been made — exactly what `CLAUDE.md` §1
  prohibits ("do not scaffold... `en/` routing... speculatively").
  Corrected: clean slugs now (`/publications/...`, `/library/...`,
  `/announcements`); a locale prefix can be layered on via rewrite rules
  whenever the i18n strategy is actually chosen, without touching CPT
  registration.
  Reason: rewrite slugs are painful to change once URLs are indexed or
  bookmarked, so this was worth stopping on rather than guessing — flagged
  to Farhad before any code was written with the prefix baked in.
  Approved by: Farhad, in this session (2026-08-05).

- **Changed:** `issue` and `document` CPTs will be registered with
  `has_archive => false`, not the default post-type archive. Both
  `/publications` and `/library` are static Pages per the IA doc §4
  (`page-publications.php`/`page-library.php`), so enabling a CPT archive
  at either of those same slugs would create a rewrite-rule collision with
  the static Page. Single-item permalinks for both CPTs still nest under
  their parent term (`/publications/{publication}/{issue}`,
  `/library/{collection}/{slug}`) via a custom `%publication%`/
  `%collection%` rewrite tag + `post_type_link` filter, not via
  `has_archive`; the "listing" views come from the taxonomy archive
  templates in Phase 3.2 instead. `announcement` keeps
  `has_archive => 'announcements'` since the IA doc treats `/announcements`
  as a real listing template (`archive-announcement.php`), not a static
  Page — no collision there.
  Reason: same as above — a structural permalink decision that's costly to
  reverse later, caught before implementation rather than after.
  Approved by: Farhad, in this session (2026-08-05).

## 2026-08-06
- **Added:** Phase 3.2 (taxonomies) implemented native `post` single
  permalinks as `/topics/{topic}/{slug}` (a custom rewrite rule + tag +
  `post_type_link` filter in `class-taxonomies.php`, the same technique
  already approved for `issue`/`document` in Phase 3.1), rather than
  leaving posts on the site's default `/%postname%/` structure. Proceeded
  without a separate stop-and-confirm, unlike the `/fa/` prefix and
  `has_archive` calls above, because this one isn't ambiguous: the IA doc
  §4 single-view table states `/topics/{topic}/{slug}` explicitly, and the
  mechanism is identical to the pattern already reviewed and approved for
  issue/document — applying it to `topic`/`post` is direct execution of an
  already-settled decision, not a new judgment call. Noting it here anyway
  since it does change how all posts permalink, for traceability.
  Posts with no `topic` term assigned fall back to a `بدون-موضوع`
  placeholder slug, mirroring the `بدون-نشریه`/`بدون-مجموعه` fallback
  pattern from Phase 3.1 (verified working as intended, not a bug, in
  Farhad's 3.1 testing).
  Approved by: Farhad, in this session (2026-08-06).

- **Deferred/Noted:** Test/QA content created while verifying Phases 3.1–3.2
  needs cleanup before handover (Phase 7 QC) — logged here so it isn't
  forgotten, no action needed yet:
  - One test post of each type (issue, document, article/post), created
    while verifying Phase 3.1's CPT registration.
  - Two extra taxonomy terms added while verifying Phase 3.2's seeding:
    "نشریه آزمایشی" under Publications and "موضوع آزمایشی" under Topics.
  Add a Phase 7.1 checklist item to delete all of the above before
  final QC / handover.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** `topic`, `publication`, and `collection` were registered with
  `hierarchical => false` in Phase 3.2 — wrong. Caught by Farhad testing:
  the topic panel in the post editor rendered as a free-text Tags-style box
  instead of a checkbox list of the 6 fixed terms. `hierarchical` governs
  the editor UI/capability model, not just nesting: `false` lets any
  `edit_posts` user mint an arbitrary new term on the fly (duplicate risk,
  e.g. "اقتصاد" vs "اقتصادی"); `true` gives a checkbox list against
  existing terms only, requiring `manage_categories` to add a new one. All
  three are fixed, IA-doc-specified vocabularies, not open tagging, so
  `true` is correct despite none of them nesting. Confirmed with Farhad
  before changing (`CLAUDE.md` §9). Fixed in `class-taxonomies.php`.
  Reason: serves `CLAUDE.md`'s "self-manageable" requirement — a
  controlled vocabulary that lets editors free-type new terms isn't
  actually controlled.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** native `post` (article) permalinks were showing
  `/uncategorized/{postname}/` on the live front end instead of the IA
  doc's `/topics/{topic}/{slug}` — a more serious bug than the
  `hierarchical` UI issue above, found by Farhad checking the actual
  front-end URL. Two independent causes, both fixed:
  1. `Taxonomies::filter_post_permalink()` was hooked to `post_type_link`,
     which only fires for custom post types via `get_post_permalink()`
     (why `issue`/`document` worked correctly) — native `post` permalinks
     go through `get_permalink()`'s own tag-replacement logic, filtered
     through `post_link` instead. The filter was silently never running
     for articles. Rehooked to `post_link` in `class-taxonomies.php`.
  2. The site's `permalink_structure` option was `/%category%/%postname%/`
     — not `/%postname%/` as it appeared during Phase 3.1 testing. Since
     this project doesn't use WP core's built-in Category taxonomy at all
     (articles use the custom `topic` taxonomy instead), every post was
     defaulting to the "Uncategorized" category and leaking that slug into
     the URL. Corrected via a one-time `update_option()` +
     `flush_rewrite_rules()` (run through a throwaway diagnostic script,
     deleted immediately after, per security practice — never leave a
     debug/option-writing script reachable over HTTP) to
     `/%postname%/`; the `post_link` filter above fully overrides the
     final URL regardless, but leaving the site option itself wrong would
     have been misleading in Settings → Permalinks.
  Verified end-to-end: the existing test article (already tagged "economy"
  from earlier testing) now permalinks as `/topics/economy/...`, matching
  the IA doc exactly.
  Reason: Farhad explicitly asked for the underlying URL structure fixed,
  not just the editor-panel symptom hidden — confirmed both root causes
  before fixing, per `CLAUDE.md` §9.
  Approved by: Farhad, in this session (2026-08-06).

- **Changed:** removed WP core's built-in Category taxonomy from the
  `post` type (`Taxonomies::remove_core_category_from_post()`, hooked on
  `init` priority 20, after core's own registration). `topic` is the
  actual content-model taxonomy for articles (IA doc §6); leaving
  Categories attached alongside it would show editors a redundant, unused
  panel and silently default posts to "Uncategorized" for no reason. Uses
  both `remove_post_type_support('post', 'category')` (classic-editor
  metabox/`post_type_supports()` checks) and
  `unregister_taxonomy_for_object_type('category', 'post')` (controls
  whether the block editor's REST-driven taxonomy panel appears at all —
  Gutenberg reads the taxonomy's object_type association, not
  `post_type_supports()`).
  Verified via a throwaway diagnostic script (deleted immediately after,
  confirmed unreachable): `get_object_taxonomies('post')` no longer
  includes `category`; `wp_insert_post()` still succeeds with no error;
  the topic-based permalink is unaffected
  (`/topics/economy/...` still correct); an untouched default post with no
  topic term still falls back to `/topics/بدون-موضوع/...` rather than
  erroring.
  Reason: known-confusing, known-redundant UI left in deliberately rather
  than by oversight would contradict `CLAUDE.md`'s "self-manageable, no
  developer needed" requirement — better removed now than carried into
  Phase 4 templates that would need to account for two competing
  categorization systems.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** Phase 3.3 (post meta fields) — `class-meta-fields.php` registers
  all IA doc §5 meta fields not already covered by native WP fields or
  Phase 3.2's taxonomies (تاریخ → post_date, خلاصه/توضیح → post_excerpt,
  جلد/پیش‌نمایش → featured image, نشریه/مجموعه → taxonomy terms — no meta
  key needed for those): `shcore_issue_number`/`shcore_volume`/
  `shcore_pdf_id`/`shcore_contents` on `issue`;
  `shcore_author_source`/`shcore_pdf_id`/`shcore_language` on `document`;
  `shcore_byline`/`shcore_language`/`shcore_translation_id` on `post`
  (پیوند ترجمه scaffolded per `CLAUDE.md` §1 — field exists, nothing acts
  on it yet). All registered via `register_post_meta()` with real
  `sanitize_callback`/`auth_callback` pairs (not just present for show),
  `show_in_rest => true`.
  PDF fields use `sanitize_pdf_id()` — rejects anything that isn't a real
  attachment ID whose *stored* MIME type (from WP core's own finfo-based
  check at original upload time, not the file extension) is
  `application/pdf`. Verified directly, not just read for correctness, via
  a throwaway diagnostic (deleted immediately after): a real image
  attachment ID is correctly rejected (returns 0), as are an invalid ID
  and a non-numeric string. This is the actual server-side enforcement per
  `CLAUDE.md` §6; the media-picker restriction in the new
  `admin/js/meta-boxes.js` is client-side UX only, and `ensure_pdf_mime_allowed()`
  keeps `application/pdf` in the site's allowed upload mimes defensively.
  Classic metaboxes (nonce-protected, `edit_post`-capability-checked) give
  editors a no-code UI for all of the above; `update_post_meta()` calls in
  the save handler route through the same sanitize callbacks REST would
  use, so there's one validation path, not two.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** the PDF media picker (`admin/js/meta-boxes.js`) let editors
  select and save non-PDF files with no visible error — found by Farhad
  clicking through the real UI, exactly the kind of check a diagnostic
  script can't substitute for. Root-caused both halves separately, per
  Farhad's instruction not to assume which one was broken:
  1. **Client-side (the actual bug):** the `select` handler accepted
     whatever `attachment.id` came back with zero validation — the
     `library: { type: 'application/pdf' }` frame option restricts the
     browse grid but doesn't stop a file reaching `select` via other paths
     (search, the "Upload files" tab, etc.), and nothing re-checked the
     final choice. Fixed by validating `attachment.mime` in the `select`
     callback and alerting + refusing the selection if it isn't
     `application/pdf`. (No live browser devtools access in this
     environment to pin down exactly why the grid-level filter itself
     wasn't visually restricting — the fix doesn't depend on that working,
     it validates the actual outcome regardless.)
  2. **Server-side (re-verified, not just re-read):** re-ran the real
     `update_post_meta()` call chain (not the isolated `sanitize_pdf_id()`
     function call from the original Phase 3.3 verification) against a
     throwaway temp post, saving a real image attachment's ID —
     confirmed rejected (`shcore_pdf_id` stayed empty), same as the
     isolated-function test. Server-side enforcement was never actually
     broken.
  **Diagnostic-process error, corrected:** the first version of this
  re-test ran against Farhad's real test issue (not a throwaway post) and
  its cleanup step blindly deleted `shcore_pdf_id` afterward — which
  turned out to hold a legitimate, correctly-saved reference to a real PDF
  (`50-writing-tools.pdf`, attachment #14) from Farhad's own earlier
  testing, not test debris. Caught immediately by checking what the
  deleted value actually was before assuming it was disposable; restored
  to `14` right away. Subsequent diagnostics create and fully delete their
  own throwaway post instead of touching real content.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** Phase 4.1 (header, footer, shared partials). Ported v6's
  `assets/css/main.css` (1,289 lines) and `assets/js/main.js` verbatim
  into the theme, plus the already-self-hosted Farhang2/ModamPro font
  files (relative `@font-face` paths in main.css needed no changes — same
  directory nesting preserved). Newsreader/Inter/JetBrains Mono still load
  from Google Fonts in `header.php`, matching what v6 currently does
  (self-hosting those is explicitly Phase 5.4 scope, not done early).
  `header.php` converts `_shell.html` + `_header.html` + `_menu.html`;
  `footer.php` converts the closing half of `_shell.html` + `_footer.html`.
  Both deferred fixes from Phase 1.3 are applied, not ported as-is: the
  nested `<a>` inside `<button id="menu-open">` is now sibling markup, and
  all inline `style="..."` attributes from these three source files are
  replaced with classes added to `main.css` (`.mast-slash`,
  `.mast-slash-light`, `.mast-icon-link`, `.mast-brand`, `.menu-topbar`,
  `.mast-nameplate--menu`, `.menu-publications`, `.ms-sm`) — same computed
  values, verified via `curl`: zero `style="` attributes anywhere in the
  rendered output.
  `inc/setup.php`'s nav menu registration expanded from the single
  placeholder `primary` location (Phase 2.1) to `menu_sections`/
  `menu_more`/`footer_topics`/`footer_site` — matching what the popup menu
  and footer actually need. The Topics and Publications columns in both
  are generated directly from the `topic`/`publication` taxonomy terms
  (fixed v6 display order, not `get_terms()`'s alphabetical default — see
  `shola_get_topic_slugs_ordered()`/`shola_get_publication_slugs_ordered()`
  in `inc/template-tags.php`), not menus, since they aren't editor-curated
  lists. Each of the four new locations has a `fallback_cb` matching v6's
  hardcoded defaults, so the popup/footer render correctly today even
  before an admin builds real menus under Appearance → Menus.
  `index.php` refactored to call `get_header()`/`get_footer()`, as noted
  as a follow-up when it was first scaffolded in Phase 2.
  Verified via `curl` (no live browser access in this environment): `200`,
  zero PHP errors/warnings/notices in output or `debug.log`, zero inline
  styles, the nested-button fix holds, and all taxonomy-driven content
  (6 topics, 2 publications, correct colors/order/permalinks) renders
  correctly.
  **Two real gaps found, both WP admin settings, not code bugs:**
  Settings → General → Site Language is still English (`lang="en-US"`, no
  `dir="rtl"` — `language_attributes()` is working correctly, it's
  reporting the actual site config) and Site Title is still the LocalWP
  default `shola-jawid` instead of `شعله جاوید` (used correctly via
  `get_bloginfo('name')` throughout, per WP convention, rather than
  hardcoded — the fix is the admin setting, not the code). Flagged to
  Farhad rather than silently working around them.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** Phase 4.1 confirmed complete via visual comparison
  (screenshots, both settings from the prior entry corrected). Masthead
  colors, nav structure, RTL direction, and footer columns all match the
  v6 prototype. The homepage body currently falls through to `index.php`'s
  generic fallback loop rather than the real hero/card-grid/current-issue
  layout — confirmed expected, not a regression: that layout is
  `front-page.php`, Phase 4.2 scope, correctly not built yet.
  Approved by: Farhad, in this session (2026-08-06).

- **Changed:** revised Phase 1.2's "card.php is articles-only, no type
  param" conclusion — found while actually building `front-page.php`
  (Phase 4.2), not a Phase 1.2 oversight; the mixed-content pattern only
  becomes visible when you build the template that consumes it.
  `body-index.html`'s "Latest" grid and `body-search.html`'s results each
  render exactly one **document** using `.card` markup (distinct
  type-label icon/text, links to `document-single.html`), confirmed via
  `grep -l '>سند<' pages/body-*.html` to appear in only those two files —
  topic archives stay articles-only, library archives still use
  `.doc-row`/`.issue-card` exclusively, nowhere else does this happen.
  `card.php` gets an optional `$type` param (`'article'` default,
  `'document'` for these two confirmed contexts only) swapping just the
  type-label icon/text and link target — same anatomy otherwise. This is
  narrower than the original 3-way article/issue/document split Phase
  1.2 rejected, which stays rejected: `issue` still never uses `.card`
  anywhere, `issue-card.php` and the row/list partials are unaffected.
  `docs/EXECUTION_PLAN.md` §4.1 amended with a second correction note,
  appended after the Phase 1.2 one rather than overwriting it, so the
  history of how this was actually figured out (two passes, not one)
  stays visible.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** Phase 4.2 — `front-page.php` (converted from `body-index.html`):
  hero (latest article), "Latest" grid (articles + the confirmed
  document-in-card mix via `card.php`'s new `$type` param), "Current
  Issue" module (latest published issue), topics table (real per-term
  post counts, not placeholder numbers), latest documents, announcements,
  newsletter band. `template-parts/cards/card.php` built per the revised
  Phase 1.2/4.2 decision above. `issue-card.php` deliberately not built
  this session — front-page.php's Current Issue module uses the
  single-item `.issue-hero`/`.issue-cover` markup, not the grid
  `.issue-card` component; that partial will be built when
  `taxonomy-publication.php` (which actually needs it) is converted, per
  Farhad's "seed content alongside each template as it's built" approach
  — building unconsumed partials ahead of need was avoided.
  Also added `add_theme_support('post-formats', ['aside'])` — the IA
  doc's مقاله/یادداشت (article/note) distinction had no field of its own
  from Phase 3.3; mapped onto WP's native `aside` format rather than a new
  meta field, since that's exactly what it's for.
  Three inline `style="..."` attributes from `body-index.html` turned out
  to be fully redundant with CSS rules that already existed in
  `main.css` (`.section-head .h-section`'s `margin-top:.5rem` already
  matched 6 of them; `.rule` already matched the `<hr>`'s inline style) —
  dropped outright rather than replaced. The remaining few got small
  scoped classes (`.issue-lead`, `.issue-hero--embedded`,
  `.newsletter .dek`) or reused the existing `.mt-sm` utility, following
  the same per-template approach as Phase 4.1.
  Seeded realistic sample content (5 articles across 5 different topics,
  one with the `aside` format; 1 issue with a real PDF, cover image, and
  publication term; 4 documents across 3 collections; 3 announcements) via
  a throwaway dev-only script, deleted immediately after running — same
  discipline as the diagnostic scripts used throughout Phase 3, not a
  permanent seeding feature shipped in the theme/plugin.
  Verified via `curl`: `200`, zero PHP errors, zero inline styles, all
  seven homepage sections present, hero/grid/current-issue/topics all
  showing real dynamic data (accurate per-topic post counts, real PDF file
  size computed from the actual uploaded file, correct term links).
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** the "Latest" grid's sample content lacked visual variety —
  found by Farhad comparing screenshots: 4 of 6 cards showed the same
  featured image. Root cause: the three "different" attachment IDs the
  original seed script cycled through (Phase 4.2 above) were the same
  photo uploaded three times (`Children_Food_Aid`), not three distinct
  images — the media library simply didn't have enough real variety to
  seed from. Fixed by uploading the v6 prototype's own 8 placeholder
  photos (`03_UI_Design/shola-jawid-ui/assets/images/`) into the media
  library and reassigning distinct featured images across the seeded
  articles/issue/documents, via another throwaway script (deleted
  immediately after running). Verified via `curl`: the grid now shows
  multiple genuinely distinct images.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** site-wide featured-image fallback — permanent behavior, not a
  one-off fix, per Farhad's request. Empty featured-image containers
  (blank space in card grids, hero, etc. for posts without one) read as
  unpolished. `assets/images/fallback.png` (Farhad-provided, copied from
  `03_UI_Design/brand assets/shola-jawid-fallback-image.png`) plus a new
  `shola_get_featured_image()` helper in `inc/template-tags.php` — same
  signature/return type as core's `get_the_post_thumbnail()`, so it's a
  drop-in replacement, falling back to the bundled image instead of
  rendering nothing. `card.php` and `front-page.php` (hero, issue-cover)
  updated to call it instead of their ad-hoc `has_post_thumbnail()`
  checks; confirmed (via grep) no other direct calls to
  `get_the_post_thumbnail()`/`has_post_thumbnail()` remain anywhere in the
  theme. Applies to article, issue, and document — announcements don't
  show a thumbnail anywhere in v6, nothing to update there.
  **Logged as a permanent rule in `CLAUDE.md` §5** (not just here): every
  template built from Phase 4.2 onward must call
  `shola_get_featured_image()`, never the core functions directly.
  Verified via `curl`: the fallback renders correctly (correct `<img>`
  markup, `200` on the file itself) for existing test posts that have no
  featured image, while seeded posts with real thumbnails are unaffected.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** Phase 4.2 — `page-publications.php` (converted from
  `body-publications.html`): lists both publication terms with real,
  computed data — `shola_get_publication_meta_line()` (new in
  `inc/template-tags.php`) counts actual published `issue` posts per term
  and their real year range, not fabricated numbers; term `description`
  (WP-native taxonomy field, editable via Edit Term with no code) supplies
  the per-publication blurb.
  Added a reusable `.page-header` component to `main.css` (§22) after
  confirming — by actually comparing all six relevant source files, not
  assuming — that the centered section-marker+h1+dek block is byte-for-
  byte identical across `body-topics/library/announcements/about/
  contact/search.html`; will be reused as those pages are built rather
  than re-solved each time. `.publications-list`/`.publication-item` (§23)
  cover this page's specific list markup.
  Seeded the `publications` static Page (WP Page, slug `publications`,
  required for this template to apply at all), set both publication
  terms' descriptions, and added a second issue under the archived
  a-world-to-win publication so its list state isn't empty for QC.
  Verified via `curl`: `200`, zero PHP errors, zero inline styles, correct
  current/archived button logic (archived gets one button, no "current
  issue" link, matching v6 exactly).
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** issue-count/year-range meta line on `page-publications.php`
  (and, found in the same pass, the topic-count line on `front-page.php`)
  rendered in Latin numerals instead of Persian — found by Farhad
  comparing screenshots. Verified the actual cause empirically rather
  than guessing: `number_format_i18n()` does **not** convert digits to
  Persian on this install (site locale `fa_AF`) — confirmed via a
  diagnostic script showing `number_format_i18n(2026)` returns `2,026`
  (Latin digits, and an incorrect thousands separator for a bare year).
  Re-checked v6's own source to confirm the real convention rather than
  assume: counts and years use Persian digits everywhere, including
  inside `.meta-mono`/`lang="en"` elements (`body-publications.html`:
  `۳۲ ISSUES · ۲۰۱۸–۲۰۲۶`) — only English month abbreviations and
  technical units (file sizes via `size_format()`) stay Latin.
  Added `shola_to_persian_digits()` to `inc/template-tags.php` (plain
  digit substitution, no thousands-separator grouping, matching what v6
  actually does) and replaced both `number_format_i18n()` call sites —
  confirmed via `grep` these were the only two in the theme. File sizes
  (`size_format()` output on the current-issue/document PDF fields)
  intentionally left as Latin — matches v6's own convention, not an
  oversight.
  Verified via `curl`: both pages now show correct Persian digits (`۱
  ISSUE · ۲۰۲۶`, `۲ ISSUES · ۲۰۰۶–۲۰۲۶`, `۲ مقاله`/`۱ مقاله`/`۰ مقاله`).
  **Known, separate gap not addressed here:** `get_the_date()` output
  (byline/issue publish dates) still shows Gregorian dates with Latin
  digits, not the Jalali-calendar Persian-digit dates v6's mockup shows
  (e.g. "۷ سرطان ۱۴۰۵") — full Gregorian-to-Jalali conversion is a
  distinct, much larger feature not requested in this pass; flagging so
  it isn't mistaken for already-solved.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** Jalali-calendar date localization tracked as a real
  checklist item — `docs/EXECUTION_PLAN.md` §5.5 (new) — not left as a
  changelog-only note. Placed in Phase 5 (functional non-visual
  requirements) rather than Phase 7.1 QC, since building the actual
  `shola_get_jalali_date()` conversion function is dev work, not
  verification; Phase 7.1 QC should find zero remaining Gregorian dates
  by the time it runs, which only works if the feature is built before
  QC, not during it. Phase 5's Definition of Done updated to include it.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** Phase 4.2 — `taxonomy-publication.php` (one template, both
  publication terms, converted from `body-publication-shola-jawid.html`
  and `body-publication-a-world-to-win.html`) plus
  `template-parts/cards/issue-card.php`. Current publication shows a
  highlighted "Current" issue block (excluded from the archive grid below
  it) and "شماره‌های پیشین"; the archived publication skips the highlight
  entirely and shows "همهٔ شماره‌ها" — matching v6's actual per-term
  difference, not a simplification. Real WP pagination via
  `paginate_links()`, page numbers converted to Persian digits and
  `page-numbers` classes remapped to the theme's `.page-num`.
  Two things fixed during this build, not shipped broken:
  - Two inline `style="..."` attributes were initially bypassed with
    `phpcs:ignore` comments instead of being fixed properly — caught on
    review before committing, replaced with real classes
    (`.page-header--muted`, `.row-tight`) and a new `.publication-current`
    class (`main.css` §24), consistent with every other template in this
    project.
  - `get_the_date('M')` turned out to be locale-aware and returns
    translated Persian month names on this site (`fa_AF`), not the
    English abbreviations ("MAR", "DEC") v6's `.issue-card-date`
    convention actually wants — found live while verifying, not assumed.
    Added `shola_get_english_month_abbr()` (`inc/template-tags.php`),
    using `mysql2date()`'s `$translate = false` argument to bypass i18n
    translation, which `get_the_date()`/`date_i18n()` don't offer.
  `issue-card.php`'s month+year date label (always shown, even for the
  archived publication's issues) is a deliberate minor simplification
  from v6's year-only archived-issue display — logged as such, since our
  data model always has a precise date either way, unlike v6's mockup
  which fakes reduced precision for older issues.
  Seeded 6 additional issues for شعله جاوید so the archive grid has real
  content (previously only the 1 "current" issue existed, which is
  excluded from the grid). Verified via `curl` on both publication terms:
  `200`, zero PHP errors, zero inline styles, correct current/archived
  branching, correct English month abbreviations with Persian year
  digits.
  Approved by: Farhad, in this session (2026-08-06).

- **Added (whitelist exception):** installed **Persian Calendar**
  (`persian-calendar`) — a deliberate, Farhad-approved exception to
  `CLAUDE.md` §3's fixed plugin whitelist (now added there too, not just
  here), to fix the Jalali-calendar date gap tracked as Phase 5.5. Farhad
  wanted this fixed site-wide at the source rather than continuing to
  hand-roll per-template conversions.
  Evaluated two candidates by reading their actual source, not just their
  WordPress.org descriptions:
  - **ParsiDate** (100,000+ installs, most-installed candidate) — installed
    first, activated cleanly, but its date-conversion hooks turned out to
    be hardcoded to `get_locale() === 'fa_IR'` only (`inc/App/Core/
    FixDates.php`, with the plugin's own `@TODO: locale non-farsi is a
    problem` comment). This project's site locale is `fa_AF` (Afghanistan
    Dari), not `fa_IR` — the hook silently never fired, no dates
    converted, no error. Farhad explicitly declined both a locale switch
    to `fa_IR` (a real site-identity decision — this is an
    Afghanistan-focused publication, not something to trade for plugin
    convenience) and a theme-side workaround filter. Uninstalled.
  - **Persian Calendar** — audited the same way: gates its date-conversion
    hooks on `is_rtl()` (`includes/class-persca-plugin.php`), not a
    hardcoded locale string, so it works under `fa_AF`. `enable_jalali`
    defaults to `true`. Installed and activated in its place.
  **Incident during install, logged permanently since it could recur on a
  plugin update:** Persian Calendar's `regional_settings` option defaults
  to `true`, and its `maybe_set_tehran_timezone()` method does a direct
  `update_option('timezone_string', 'Asia/Tehran')` on init — it had
  already silently overwritten the site's timezone before this was
  caught. The prior value was not recorded anywhere and could not be
  restored. Disabled `regional_settings` immediately (a factual error for
  an Afghanistan-focused site, not a preference — fixed without asking
  first, per the standing "fix known bugs immediately" pattern in this
  project). Farhad is setting the correct timezone (likely `Asia/Kabul`)
  manually in Settings → General, since there's no record to restore from
  and the exact value is his call. **Action for whoever does the Phase 6
  security/config hardening pass: re-check `regional_settings` is still
  off after any future Persian Calendar update**, since plugin updates
  can silently re-enable options changed post-install.
  Also flagged, Farhad's call, not fixed in code: `date_format` (Settings
  → General) is still WP's English default `F j, Y` ("Month Day, Year"),
  so Jalali dates render in that token order (`مرداد ۱۵, ۱۴۰۵`) rather
  than v6's `j F Y` convention (`۱۵ مرداد ۱۴۰۵`, day-month-year, no
  comma). Farhad is updating this setting directly.
  Verified via `curl` after install: `200`, zero PHP errors on front-page,
  publications, and taxonomy-publication; masthead runner and all
  human-readable dates (bylines, current-issue date, announcements) now
  render as Jalali with Persian digits; the Gregorian mono-label dates
  hardened in the same pass (`shola_get_gregorian_year()`,
  `shola_get_iso_datetime()`, hardened `shola_get_english_month_abbr()` —
  see below) are confirmed unaffected by the plugin's global hook.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** hardened three date-related template-tag helpers against
  ParsiDate/Persian Calendar's global date-function hooks, added
  *before* installing either plugin, per Farhad's explicit request to
  audit for risk first rather than find breakage after the fact:
  - `shola_get_english_month_abbr()` (existing, Phase 4.2) — switched
    from `mysql2date($format, $date, false)` to raw `gmdate()` on the
    post's timestamp, since `mysql2date()` with `$translate = false`
    wasn't guaranteed immune to a plugin hooking at a lower level than
    `get_the_date()`.
  - `shola_get_gregorian_year()` (new) — same rationale, for the
    issue-count/year-range mono-label convention
    (`shola_get_publication_meta_line()`, `taxonomy-publication.php`,
    `issue-card.php`), which is intentionally Gregorian (matching v6's
    own literal source, `۳۲ ISSUES · ۲۰۱۸–۲۰۲۶`), not Jalali.
  - `shola_get_iso_datetime()` (new) — uses `get_post_datetime()` (WP
    core, 5.3+) for `<time datetime="...">` machine-readable attributes
    (`front-page.php` ×3, `card.php`), which must stay ISO 8601/Gregorian
    for accessibility/microformat correctness regardless of what
    human-readable text is shown next to them.
  Confirmed via `grep` these were the only risk sites across
  `front-page.php`, `page-publications.php`, `taxonomy-publication.php`,
  `card.php`, and `issue-card.php` before installing anything.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** Phase 5.5 (Jalali-calendar date localization) confirmed
  complete via live re-verification across all three affected pages,
  after Farhad set both remaining settings (timezone → `Asia/Kabul`,
  `date_format` → custom `j F Y`). Verified via `curl`:
  - Masthead runner now `۱۵ مرداد ۱۴۰۵` — Jalali, Persian digits,
    day-month-year order, no comma — matching v6 exactly.
  - All human-readable dates (bylines, current-issue "تاریخ نشر",
    announcements) across `front-page.php` correctly Jalali.
  - Both protected Gregorian mono-label conventions (`page-publications.php`
    issue-count/year-range, `taxonomy-publication.php` current-issue line
    and issue-card grid dates) confirmed still unaffected by the
    `date_format` change — they bypass WP's date-formatting option
    entirely via the hardened helpers, as designed.
  - Full-page sweep of all three pages for any other stray raw Gregorian
    output (beyond `datetime=""` attributes, upload paths, and the
    already-verified mono-label contexts) found none.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** Phase 4.2 — `page-topics.php` (converted from
  `body-topics.html`). Straightforward reuse of components already built
  and verified in earlier Phase 4.2 templates — `.page-header
  page-header--narrow` (front-page.php's topics section /
  page-publications.php) and the `.topic-list` loop with real per-term
  post counts (already correct, Persian digits via
  `shola_to_persian_digits()`). No new CSS needed. Seeded the `topics`
  static Page. Verified via `curl`: `200`, zero PHP errors, zero inline
  styles, real counts matching the homepage topics table exactly.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** masthead runner (`shola_get_masthead_runner()`) was missing
  v6's fixed Latin brand-code prefix ("SHOLA JAWID") — found by Farhad
  comparing `page-topics.php` and `page-publications.php` against v6 (a
  shared-component bug, visible on every page, not template-specific).
  Root cause: the function used `get_bloginfo('name')` (the Persian site
  title) for the first segment, when v6's source (`_header.html:24`) uses
  a fixed Latin brand code instead — `SHOLA JAWID · شماره ۳۲ · سرطان
  ۱۴۰۵`, not a translation of the Persian nameplate (which is already
  shown separately). Added `shola_get_masthead_code()` (filterable,
  intentionally not wrapped in `__()` — a fixed ASCII brand mark, same
  category as the `SJ-32`-style codes already correctly used in
  `taxonomy-publication.php`, not translatable UI copy).
  Verified via `curl` on both affected pages: `شعله جاوید · شماره ۳۲ ·
  ۱۵ مرداد ۱۴۰۵` (Persian title, wrong) → `SHOLA JAWID · شماره ۳۲ ·
  ۱۵ مرداد ۱۴۰۵` (correct, matches v6). Fixed once at the shared
  `inc/template-tags.php` level, applies everywhere automatically — no
  per-template changes needed.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed (in `card.php`, found while building `taxonomy-topic.php`):**
  the article card's topic label was always rendered as a link. Checked
  v6's source before building the consuming template: on a topic's own
  archive page (`body-topic-economy.html`), the card's type-label shows
  the term as **plain text**, not a link (avoiding a redundant self-link)
  — while the homepage's equivalent card does link it. Fixed with
  `is_tax( $term->taxonomy, $term->term_id )` in `card.php`: suppresses
  the link only when the card is rendered on that same term's own
  archive, links it everywhere else (homepage, search, other topic
  archives). Verified via `curl`: `اقتصاد` renders as plain text on
  `/topics/economy/`, still links correctly on the homepage.
- **Added:** Phase 4.2 — `taxonomy-topic.php` (one template, all 6 topic
  terms, converted from `body-topic-economy.html` and its 5 structurally
  identical siblings). Horizontal `.topic-nav` with active-state
  highlighting, `.filter-tabs` ("تازه‌ترین" real/active via the date-order
  query; "پرخواننده‌ترین" kept as an inert placeholder matching v6's own
  `href="#"` — no view-count tracking exists in this project to back a
  real "most read" sort, so faking one wasn't an option), real
  `WP_Query` article grid via `card.php`, WP pagination matching the
  `taxonomy-publication.php` pattern (Persian digits, `.page-num`
  classes). Seeded real per-term descriptions (exact text pulled from
  all 6 `body-topic-*.html` source files, not paraphrased) and 6
  additional articles spread across topics for archive-grid variety.
  Verified via `curl`: `200`, zero PHP errors, zero inline styles.
  Approved by: Farhad, in this session (2026-08-06).

- **Investigated three items from Farhad's `taxonomy-topic.php` review,
  found via direct source inspection rather than assumption:**
  1. **Type-tag fill color (مقاله vs یادداشت) — not a missed rule; no
     such rule exists in v6.** Checked `main.css`'s `.type-label .glyph`
     rule: one uniform declaration (`color: var(--crimson)`), no
     type-based modifier. Checked the HTML: on `body-index.html`, the
     یادداشت card uses the identical SVG path to every مقاله card there.
     The only place a visually different icon appears is
     `body-topic-economy.html`, where *every* card — both types,
     uniformly — uses a shorter, simplified SVG path than the homepage's
     version. That's a page-to-page inconsistency within v6's own
     prototype (likely a `gen_bodies.py` generator artifact), not a
     مقاله-vs-یادداشت design rule. `card.php`'s single consistent icon
     (matching the homepage's version) is left as-is — flagged rather
     than "fixed" toward a distinction that doesn't exist in the source.
  2. **Duplicate images — confirmed and fixed, not just seed-data
     noise.** Root cause: not a duplicate-file issue (already fixed
     earlier in Phase 4.2) — multiple independent seed scripts across
     this session each cycled the same 8-image array from index 0, so
     posts from different batches landing in the same query result
     repeated images. `/topics/economy/` showed 4 of its 5 visible cards
     sharing images. Reassigned via a throwaway script so all 5
     `economy`-tagged posts use distinct images; verified via `curl`
     (checking `src=` only, not `srcset`, to avoid double-counting) that
     all 5 are now unique. Deleted immediately after running.
  3. **Pagination — confirmed correct, not a template gap.** Verified
     with a throwaway diagnostic reusing `taxonomy-topic.php`'s exact
     query/`paginate_links()` logic at an artificially small
     `posts_per_page` (without touching the live template or adding real
     content, per Farhad's instruction): Persian digits, `.page-num`
     classes, `aria-current` state, and prev/next arrows all render
     correctly once `max_num_pages > 1` — the same pattern already
     proven live on `taxonomy-publication.php`. Nothing to fix; will
     render correctly once any topic accumulates more than 6 articles.
  Approved by: Farhad, in this session (2026-08-06).

- **Corrected:** the type-tag icon finding above was wrong — reversed by
  Farhad with a direct screenshot comparison against v6, not just source
  diffing. The two SVG path variants I found earlier (`main.css`/HTML
  source review) are real and do render differently: v6's homepage
  (`body-index.html`) uses a two-subpath path (two nested,
  opposite-wound shapes — with `fill="currentColor"` and nonzero winding,
  this cuts a hole, rendering **hollow/outlined**); every one of v6's
  6 topic-archive files (`body-topic-*.html`) uses a one-subpath path,
  rendering **solid filled** — confirmed uniform across all 6, not just
  economy. My first pass concluded "no rule exists" because I only
  compared source *code*, not what each path actually renders as — a
  wrong conclusion, corrected by Farhad's screenshot evidence.
  Fixed properly in `card.php`: the icon variant is now selected by the
  same `is_tax( $term->taxonomy, $term->term_id )` condition already used
  to suppress the term self-link (v6 toggles both together — solid icon
  + plain-text term on a topic's own archive; hollow icon + linked term
  everywhere else). Verified via `curl` on both contexts: `/topics/
  economy/` renders the one-subpath (solid) icon, the homepage renders
  the two-subpath (hollow) icon — matching v6 exactly in both places.
  Full regression check across all five built pages: `200`, zero PHP
  errors.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** solid-vs-hollow type-tag icon fix confirmed via actual
  browser rendering by Farhad, not just the `curl`/regression check —
  deliberately re-verified visually given the prior round's conclusion
  on this exact component turned out to be wrong. `/topics/economy/`
  shows solid-filled squares uniformly (مقاله and یادداشت alike);
  homepage shows hollow/outlined squares uniformly. Matches v6.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** Phase 4.2 — `page-library.php` (converted from
  `body-library.html`): collections listing (real per-collection
  document counts, `.topic-list` pattern already verified) + "تازه‌ترین
  اسناد" latest-documents list.
  **Fixed a real gap while building this, not just seed volume:**
  extracted `template-parts/rows/document-row.php` (per the Phase 1.2
  decision that `.doc-row` needed its own partial outside
  `template-parts/cards/`) rather than duplicating the doc-row markup a
  third time — and in doing so found `front-page.php`'s existing inline
  version omitted the author/source field entirely, even though v6's own
  example (`body-library.html`: "آثار کلاسیک · لنین · PDF · 2.8 MB")
  includes it and `shola-core` already tracks it (`shcore_author_source`,
  Phase 3.3). `front-page.php` switched to the new shared partial in the
  same commit — fixed retroactively there too, verified via `curl` on
  both pages.
  Also checked rather than assumed: `body-library.html`'s "همهٔ …" link
  text/target for the latest-documents section differs from
  `front-page.php`'s equivalent link ("همهٔ آثار کلاسیک" → the classics
  collection, not "همهٔ مجموعه‌ها" → `/library/`) — makes sense on the
  library page itself (a generic "all collections" link would be
  redundant right below the collections list already shown), matched
  exactly rather than assumed identical to the homepage's version.
  "ص" (page count) omitted from `doc-meta`, same as the current-issue
  module — no such field exists in the content model, not fabricated.
  Seeded the `library` static Page, backfilled real author data on 3
  existing documents, and added 2 more documents for list variety.
  Verified via `curl` on both pages: `200`, zero PHP errors, zero inline
  styles.
  Approved by: Farhad, in this session (2026-08-06).

- **Verified (not assumed):** two follow-up questions on
  `document-row.php`'s PDF metadata, raised by Farhad after the
  `page-library.php` review.
  1. **File size is genuinely dynamic, not stale/hardcoded.** All 5
     seeded documents showing identical `509 KB` is explained entirely by
     them sharing the same placeholder PDF attachment (`shcore_pdf_id =
     14`) — not a bug. Proved this empirically rather than by code
     review alone: uploaded a synthetic 2,267-byte test PDF, temporarily
     swapped one document's `shcore_pdf_id` to it via a throwaway
     script, confirmed `document-row.php`'s exact size-computation logic
     now rendered `2 KB` instead of `509 KB`, then restored the original
     value and deleted the test attachment. Confirmed live afterward:
     the document shows `509 KB` again, no PHP errors. (The
     `get_page_by_title()` deprecation notices that briefly appeared in
     `debug.log` came from the throwaway test script itself, which is
     already deleted — confirmed via `grep` that function is not used
     anywhere in the actual theme/plugin code.)
  2. **Page count ("ص") is confirmed not tracked anywhere** — `grep`
     across `class-meta-fields.php` found no page-count field registered
     in the Phase 3.3 content model. This is a genuine content-model gap,
     not a display bug; flagging for a decision on whether to add a
     `shcore_page_count` meta field (Farhad's call — not added
     speculatively here).
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** page-count field (raised in the previous entry) confirmed
  out of scope by Farhad — a deliberate non-requirement, not in the
  plan. No `shcore_page_count` meta field added. Not carried forward as
  a pending/deferred item elsewhere (unlike the earlier Jalali-calendar
  and test-content-cleanup items, which are real deferred work) — this
  one is closed.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed (in `document-row.php`, found before building the consuming
  template, not after):** checked `body-library-classics.html`'s exact
  source before building `taxonomy-collection.php` and found the same
  class of self-reference bug already fixed once for `card.php`'s topic
  link — v6 omits the collection name from `doc-meta` entirely on that
  collection's own archive (`لنین · ترجمه ۱۴۰۵ · ...`, no "آثار کلاسیک"
  prefix), but `document-row.php` always included it. Fixed with the
  same `is_tax( $doc_term->taxonomy, $doc_term->term_id )` pattern
  already proven for `card.php`, before this bug could ship. "ترجمه
  ۱۴۰۵" (translation year) also appears in some v6 rows but has no
  content-model field — omitted, not fabricated, same as page count.
- **Added:** Phase 4.2 — `taxonomy-collection.php` (one template, all 4
  collection terms, converted from `body-library-classics.html` and its
  3 structurally identical siblings). Horizontal `.topic-nav` with
  active-state highlighting (same pattern as `taxonomy-topic.php`),
  `document-row.php` list, WP pagination. Seeded real per-term
  descriptions (exact text from all 4 `body-library-*.html` source
  files) and 4 more classics documents with real authors for archive
  variety. Verified via `curl`: `200`, zero PHP errors, zero inline
  styles, collection-name suppression confirmed working on
  `/library/classics/`.
  Approved by: Farhad, in this session (2026-08-06).

- **Deferred:** document CPT translation-year field — identified while
  reviewing `taxonomy-collection.php` against `body-library-classics.html`
  (v6's reference: `لنین · ترجمهٔ ۱۴۰۵ · ۱۲۰ ص`, live only shows `لنین`).
  Confirmed via `class-meta-fields.php` this is genuinely untracked, not
  a wiring bug: the only translation-related field that exists
  (`shcore_translation_id`) is scoped to the `post` CPT only, not
  `document`, and is a different concept entirely (a bilingual
  post-pairing ID, inert scaffolding per Phase 3.3) — not a year value,
  wouldn't fit even if wired to documents.
  Farhad confirmed this is out of scope for this build — unlike page
  count (closed outright, not deferred, per the earlier entry), this one
  is logged as a deliberate deferral: a `shcore_translation_year` (or
  similar) meta field on the `document` CPT, rendering as "ترجمهٔ
  [year]" in `document-row.php`'s meta line, if this ever gets revisited.
  Not added now — no field registered, no template changes made.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** `taxonomy-collection.php` fully confirmed clean by
  Farhad against `body-library-classics.html` — structure, header,
  term-tab active state, and the collection-name self-suppression fix
  all match v6 exactly. Pagination correctly absent (fewer seeded
  documents than v6's mockup — expected, not a bug, same as every prior
  archive template).
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** Phase 4.2 — `archive-announcement.php` (converted from
  `body-announcements.html`): real `have_posts()` loop, Jalali dates via
  the site-wide plugin, WP pagination matching the established pattern.
  One deliberate deviation from v6, not a visual one: v6's announcement
  titles link to `href="#"` (inert placeholder — no single-announcement
  view exists anywhere in the IA doc's page-to-template map). Linked to
  the real `get_permalink()` instead, which resolves correctly through
  WP's default template hierarchy (verified via `curl`: `200`, not a
  404) even with no custom single-announcement template — a real
  destination is more correct than a dead link, and doesn't require
  building new scope (no template file needed, WP handles the
  fallback).
  One inline `style="max-width:820px;margin-inline:auto"` from
  `body-announcements.html`'s list wrapper replaced with a new
  `.announce-list--page` class — caught and fixed before committing,
  not shipped.
  Seeded 4 more announcements (exact titles/content from v6's remaining
  examples) for archive variety. Verified via `curl`: `200`, zero PHP
  errors, zero inline styles.
  Approved by: Farhad, in this session (2026-08-06).

- **Reverted:** `archive-announcement.php`'s title links back to v6's
  inert `href="#"`. My earlier reasoning ("a real destination is more
  correct than a dead link") was wrong — Farhad caught that a real
  permalink pointing at WP's bare default template hierarchy (no
  single-announcement template exists or is planned) is a *worse*
  experience than an inert link: a user clicking through lands on
  something unstyled and inconsistent with the rest of the site.
  Re-checked the plan's page-to-template map directly before deciding:
  exactly three single-view templates are listed (article, issue,
  document) — no single-announcement anywhere, and the `ANN` row's own
  Notes column is empty, no forward-reference to a future detail view.
  Confirmed: announcements are list-only by design, permanently, not a
  "missing template" gap to fill later. Verified via `curl`: `200`, zero
  PHP errors, titles correctly back to inert `href="#"`.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** `archive-announcement.php` fully closed — inert-link
  revert confirmed working, everything else from the original build
  (real Jalali dates, WP pagination, zero inline styles) unaffected.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** 3 of the 7 seeded announcements still showed generic
  placeholder content ("این یک اطلاعیهٔ نمونه است.") instead of their
  real excerpt text from `body-announcements.html` — leftovers from the
  earliest seed batch (front-page.php's original announcement seeding),
  before the later batch started pulling exact v6 text. Farhad reported
  4 affected; checked precisely before fixing rather than trusting the
  count blindly — confirmed via `curl` only 3 actually had the
  placeholder (`تحویل چاپی شمارهٔ ۳۱` already had correct content).
  Backfilled the real text for all 3 via a throwaway script (deleted
  immediately after running): "فراخوان ارسال مقاله برای شمارهٔ ۳۳...",
  "نسخهٔ انگلیسی سایت به‌زودی راه‌اندازی می‌شود", and "آرشیو کامل «جهان
  برای فتح» دیجیتال شد". Verified via `curl`: `200`, zero PHP errors,
  zero remaining placeholder instances, all 7 v6-matched announcements
  now show their correct excerpt text.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** `archive-announcement.php` fully confirmed clean by
  Farhad against `body-announcements.html` — structure, header, list
  layout, inert titles, Jalali dates, and (now) all real excerpt text
  match v6 exactly, same 7 titles in the same order. Pagination
  correctly absent (fewer seeded items than v6's mockup, same pattern as
  every prior archive template).
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** Contact Form 7 installed (§3 whitelist, pre-approved — no
  exception process needed) for `page-contact.php`'s form per
  `EXECUTION_PLAN.md` §4.3. Downloaded the official plugin zip from
  `downloads.wordpress.org` and extracted directly into the live LocalWP
  `wp-content/plugins/` directory (same precedent as Persian Calendar —
  not git-tracked, since third-party plugin binaries live outside the
  junction-linked theme/plugin paths). Activated and configured via a
  throwaway script (deleted immediately after running) using CF7's own
  `WPCF7_ContactForm` API rather than hand-writing post meta. Created
  form #71 with the 4 fields from `body-contact.html` (name, email,
  topic select, message) and CF7's own mail-recipient/subject/body/
  Reply-To settings. Disabled CF7's default stylesheet entirely via
  `add_filter( 'wpcf7_load_css', '__return_false' )`
  (`inc/enqueue.php`), per CLAUDE.md §3's requirement that the form use
  the theme's own markup/CSS, not the plugin's defaults — verified via
  `curl` that no `contact-form-7*.css` request appears in the page.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** First-pass CF7 form config had no `<label>` elements at all
  (only placeholder-less `<input>`s), unlike v6's source which has a
  visible Persian `<label class="label">` for every field. Missed on the
  first form-template string; caught during live verification of the
  rendered HTML rather than assumed correct from the setup script alone.
  Reconfigured form #71 via a second throwaway script (deleted after
  running) to wrap each field with its matching label
  (نام / نشانی ایمیل / موضوع پیام / متن پیام), and set the form's post
  title to "فرم تماس" (was the CF7 default English "Contact form",
  which was leaking into the rendered `<form aria-label>`).
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** CF7 renders its form wrapper with `dir="ltr"` on this
  install — it doesn't recognize the site's `fa_AF` locale as
  RTL (same class of locale gap as the ParsiDate/`fa_IR` issue found
  earlier with the Jalali calendar plugin). Rather than patch CF7's
  locale table, added `.wpcf7 { direction: rtl; }` in `main.css` to
  force the actual rendered direction; the `#c-email` field keeps its
  own `direction: ltr` override underneath, matching v6's
  `dir="ltr"` on the email input specifically (an ASCII address field,
  correctly LTR even inside an RTL form).
  Approved by: Farhad, in this session (2026-08-06).

- **Decided:** Contact page's public email is a placeholder,
  `info.sholajawid@gmail.com`, not the real address. v6's own source
  hardcodes `info.farhaad@gmail.com` — the theme developer's personal
  email (CLAUDE.md §0) — as the public contact address; copying that
  onto client-facing content without asking would have been wrong, so
  it was flagged instead of ported verbatim. Used for both the displayed
  `mailto:` link and CF7 form #71's mail-recipient setting, so both
  paths agree. Documented here explicitly (not left as a silent
  substitution) so it's replaced with the real address once Farhad
  provides it — nothing else about the contact flow needs to change
  when that happens, since both places read from the one placeholder
  value.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** `page-contact.php` built and closed. Converted from
  `body-contact.html` (Phase 4.2/4.3): centered narrow page header,
  CF7 form #71 rendered via `[contact-form-7 id="71"]` inside
  `.contact-inner`, static aside (email / response-time / privacy text)
  with the placeholder email above and v6's own inert `href="#"`
  privacy-policy link (v6 doesn't link to a real privacy page either,
  so this isn't a regression — matched exactly). New CSS section §25
  added to `main.css` (`.contact-grid`, `.contact-inner`, `.wpcf7`,
  `.wpcf7-form`, `#c-email`, `.contact-aside`, `.contact-aside-value`)
  replacing every inline `style=""` from the source HTML, including one
  found and removed after the fact (`.page-header .section-marker`'s
  existing centered rule already covered it, and `.page-header` is
  centered by default so the source's redundant `class="center"` wrapper
  was dropped rather than ported). Seeded the `contact` WP Page (id 72)
  via a throwaway script, deleted after running. Verified live via
  `curl`: `200`, zero PHP errors/warnings/notices, zero inline styles,
  zero CF7 default CSS requests, labels and RTL direction both correct
  after the two fixes above.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** Contact form's "موضوع پیام" subject dropdown was hardcoded
  as 5 literal pipe-values inside CF7 form #71's own stored form
  markup — meaning changing an option meant editing raw CF7 tag syntax,
  not something to hand to an editorial team. Fixed by adding
  `shcore_contact_topics`, a `shola-core`-owned option (array of
  strings, default = the original 5 v6 values), following the §2 split:
  this is a piece of site configuration, not content (no CPT/taxonomy
  involved), so it lives in the plugin rather than the theme, and
  survives a theme switch like everything else the plugin owns. New
  file `includes/class-contact-settings.php`
  (`SholaCore\Contact_Settings`), wired into `shola-core.php` alongside
  the other `::init()` calls. Adds one settings screen (Settings →
  موضوعات فرم تماس), a single RTL textarea, one topic per line,
  sanitized per-line via `sanitize_text_field` on save
  (`register_setting`'s callback) — no CPT/taxonomy/custom capability
  involved, just a plain option and the Settings API.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** First implementation attempt used CF7's `"dynamic:name"`
  tag syntax and a `wpcf7_form_tag_data_option_{name}`-suffixed filter —
  both invented from a wrong assumption about CF7's actual API, not
  verified against this install's source first. Live-verified the
  result before trusting it: the dropdown rendered the literal string
  `dynamic:contact_topics` as its only option, proving the mechanism
  didn't exist. Read CF7's actual `modules/select.php` and
  `includes/form-tag.php` in the live plugin install to find the real
  mechanism: an unquoted `data:xxx` option token (quoted strings are
  select *values*, not options — a second wrong assumption caught the
  same way, by testing rather than trusting the syntax) resolved via
  the single generic `wpcf7_form_tag_data_option` filter, which
  receives the tag's data-option names as its second argument. Fixed
  `Contact_Settings::filter_data_option()` to hook that filter and
  check `in_array( 'contact_topics', $options, true )` before
  responding, so it can't affect any other form/tag. Updated form #71's
  `your-topic` tag to `[select your-topic class:field id:c-topic
  data:contact_topics]` (unquoted, no other options list) via a
  throwaway script, deleted after running.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** End-to-end loop verified live, not just read as
  correct-looking code: confirmed the dropdown renders the same 5
  default options with the option unset (falls back to
  `get_default_topics()`), then wrote a throwaway script that called
  `update_option( 'shcore_contact_topics', [...2 test values...] )` and
  re-fetched `/contact/` via `curl` — dropdown changed to exactly those
  2 values. Restored the default 5 afterward via another throwaway
  script; both scripts deleted and confirmed unreachable (`404`) after
  running. `debug.log` checked for any `shola-core`/`Contact_Settings`
  errors — none found. `page-contact.php` is now fully closed: form,
  labels, RTL direction, and the editable subject list all verified.
  Approved by: Farhad, in this session (2026-08-06).

- **Decided:** `page-about.php`'s long-form prose (mission statement,
  editorial board, submission guidelines, republishing policy, support
  model, writer's-guide text — 7 sections from `body-about.html`) is
  stored as the `about` WP Page's own `post_content`, edited via the
  block editor, output through `the_content()` — not hardcoded as
  `__()` PHP strings like every other Phase 4.2 template's short
  structural labels. Flagged as a genuine fork before building rather
  than defaulting to the established pattern: this content is
  substantive editorial copy a client should be able to edit without a
  code change, unlike a page-header dek or a nav label. Each section is
  a Heading block with its Anchor field set to match the existing tab
  nav's fragment targets (#about, #team, #contact, #guidelines,
  #republish, #support, #write), so the tabs keep working without any
  JS (v6 has no scroll-spy JS for this component either — confirmed by
  grepping its `main.js`, pure CSS/anchor-link behavior). The tab nav
  itself stays in the template as structural chrome, not page content,
  since it's tightly coupled to the fixed section structure.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** `page-about.php` built and closed. Tab nav and page
  header ported from `body-about.html` (using the existing
  `.page-header--tight` variant, matching v6's `margin-bottom:1rem`
  exactly). Seeded the `about` WP Page (id 73) with the 7 sections as
  proper Gutenberg heading+paragraph blocks (anchors set per the
  decision above) via a throwaway script, deleted after running. Same
  placeholder-email substitution as `page-contact.php`
  (`info.sholajawid@gmail.com`, not v6's hardcoded personal address) in
  the تماس section. Verified live via `curl`: `200`, zero PHP
  errors/warnings/notices, zero inline styles, all 7 `id=` anchors
  present and matching the tab nav's `href="#..."` targets, correct
  placeholder email.
  Approved by: Farhad, in this session (2026-08-06).

- **Corrected:** `EXECUTION_PLAN.md`'s Phase 4.1 "second correction"
  (logged 2026-08-06) claimed `body-search.html`'s results render its
  one document using `.card` markup, alongside `body-index.html`'s
  "Latest" grid. Checked directly against the actual file while
  building `search.php` rather than trusting the plan's prior note: no
  `class="card"` appears anywhere in `body-search.html`. Search results
  are a genuinely distinct anatomy — a plain `<li>` in `<ul
  class="stack-lg">`, `h-card-lg` not `h-card`, no image, no type-icon
  SVG — that happens to reuse the `card-dek`/`card-byline` class names
  for visual-family consistency, not the same component. Built a new
  `template-parts/search/result.php` instead of extending `card.php`'s
  `$type` param a second time. `card.php`'s own docblock (which
  repeated the same inaccurate claim) corrected in the same commit.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** `search.php` needs 4 result types mixed in one query
  (article, note, issue, document) plus working filter tabs — v6's
  mockup renders the tabs as inert `href="#"` placeholders, but unlike
  `archive-announcement.php`'s announcement-detail case, a real
  destination for each tab already exists (this same template,
  filtered), so building them as live links is the "real destination
  beats a dead link" case, not the reverted one. Implemented in
  `shola-core\Post_Types`: `include_cpts_in_search()` (hooked to
  `pre_get_posts`) now both (a) defaults native search to `post` +
  `issue` + `document` together — `announcement` deliberately excluded,
  it never appears in v6's search results or filter-tab list — and (b)
  reads a new `result_type` public query var (registered via
  `query_vars`) to back the filter tabs: `article` (post, excluding the
  aside post format via `tax_query`), `note` (post, `post_format =
  aside`), `issue`, `document`. Query engineering placed in `shola-core`
  rather than the theme, consistent with the existing permalink filters
  living there (§2 split — this determines which content a native WP
  feature surfaces, not presentation).
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** `shola_highlight_search_term()` (`inc/template-tags.php`)
  wraps query matches in `<mark>`, matching v6's highlighted result
  titles/deks. Multibyte-safe (`/iu` regex flags) for Persian text.
  Takes already-`esc_html()`'d input and returns HTML with `<mark>`
  intact, output via `wp_kses( $text, array( 'mark' => array() ) )` at
  the call site rather than raw `echo`, so nothing except the `<mark>`
  tag itself is ever unescaped.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** the masthead search icon (`header.php`, built Phase 4.1)
  has linked to `home_url( '/search/' )` since it was written — a
  static path with no Page and no rewrite rule behind it, confirmed via
  `curl`: `404`. Pre-existing bug, not something this session
  introduced; caught now because `search.php` finally gives it a real,
  correct destination to point to. Fixed to `home_url( '/?s=' )` — WP's
  `is_search()` is true whenever the `s` query var is present at all,
  even empty, confirmed via `curl` (`200`, renders the search template
  with an empty query rather than 404ing or falling through to the
  front page). Footer checked for the same bug — no search link exists
  there at all (`shola_fallback_footer_site()`), so nothing to fix.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** `search.php` built and closed. Verified live via
  `curl` against real seeded content (query "تورم", present in both an
  article and an issue): `200`, zero PHP errors/warnings/notices, zero
  inline styles, correct Persian-digit result count, `<mark>`
  highlighting working on both title and dek, filter tabs verified to
  actually filter (`result_type=issue` returned only the شماره result),
  and the empty-results state tested with a nonsense query (renders the
  "نتیجه‌ای یافت نشد" message, not a blank page or an error).
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** real bug on `search.php`, found by Farhad from a rendered
  screenshot — the masthead/crimson header bar was visibly narrower
  than every other template, boxed instead of full-width. Root cause
  had nothing to do with the masthead or header.php (confirmed
  byte-identical header markup/CSS between search.php and a working
  template via `curl`): WordPress's own `body_class()` auto-adds a
  literal `search-results` class to `<body>` on this template (WP
  core, not theme code), which collided with this session's own
  `.search-results { max-width: 820px; margin-inline: auto; }` rule
  (added while building `search.php`, meant only for the results
  `<div>`, same selector text as the reserved body class) — shrinking
  and centering the entire `<body>`, masthead included, to 820px.
  Confirmed live via the browser's DOM inspector (not curl, which
  can't reveal a computed-CSS bug like this): `<body>` computed width
  was exactly `820px`, matching the rule's constant. Renamed the
  wrapper class to `.search-results-wrap` (`search.php`, `main.css`) —
  a distinct string, can't collide with any of WP core's own
  auto-added body classes. Re-verified live: `.masthead` now spans the
  full body width again, `.search-results-wrap` still correctly
  constrained to 820px.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** `:target` highlight-flash on `page-about.php`'s
  anchor-jump sections, per Farhad's UX request — clicking a tab
  (دربارهٔ ما / هیئت تحریریه / etc.) already jumps via the browser's
  native anchor scroll, but gave no visual confirmation of where the
  page landed. Checked `v6`'s source first per instructions before
  inventing anything: no `:target` or `@keyframes` rule exists anywhere
  in its CSS, so this is a new addition, not a ported one. Reuses
  `--crimson-tint` — the same token the search-result `<mark>`
  highlight already uses for "highlighted" meaning, rather than a new
  color — animating `background-color` from `--crimson-tint` to
  transparent over `1.4s`, scoped to `.prose h2:target` (`main.css`,
  new `@keyframes shola-target-flash`) so it only ever fires on the
  headings `page-about.php`'s tab nav actually targets. CSS-only, no
  JS. Verified live via the browser: navigating to `/about/#team`
  confirms `#team` matches `:target`, the animation is applied with
  the correct name/duration, and its starting computed background
  color is `rgb(245, 220, 220)` (`--crimson-tint` exactly).
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** Farhad reviewed the flash live and found two real gaps:
  too quick to actually register, and scoped to only the `<h2>` title
  rather than the section a reader jumped to. The heading-only scope
  was a real ceiling of the original approach — `h2:target` has no
  pure-CSS way to reach "every sibling until the next heading," so the
  fix restructures rather than just tunes: the `about` Page's
  `post_content` (id 73) now wraps each of the 7 sections in a Group
  block (`{"anchor":"...","className":"about-section",...}`) so the
  anchor — and therefore `:target` — lands on a real container around
  the heading *and* its paragraphs, not the heading alone. CSS updated
  to match: `.prose .about-section:target` instead of `.prose
  h2:target`. Timing changed from a straight 1.4s fade to a
  `0%, 35% { …tint }, 100% { …transparent }` keyframe at 2.2s — holds
  at full tint for the first third before fading, giving it time to
  actually be seen rather than fading immediately. Re-verified live via
  the browser: `#team` now resolves to the wrapping `<div>` (confirmed
  fresh, not a stale cached DOM — an earlier check briefly and
  incorrectly suggested the id had landed on the `<h2>` again, caught
  and re-checked against a forced fresh navigation before trusting it),
  its bounding-box height covers the full section (~190px, heading +
  both paragraphs) not just the heading line, and
  `animation-duration: 2.2s` is applied.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved (deliberate deviation from v6, reviewed and approved):**
  `search.php`'s filter tabs (همه/مقاله/یادداشت/شمارهٔ نشریه/سند
  کتابخانه) are real, functioning links — `result_type`-driven, built
  and logged earlier in this session — rather than v6's inert `href="#"`
  placeholders. Recording this explicitly as an intentional
  improvement, not an unreviewed scope addition, the same way this
  session's other deliberate deviations from the static prototype are
  on record: the contact form's subject dropdown made editable
  (`shcore_contact_topics`, rather than v6's hardcoded list),
  `archive-announcement.php`'s titles tried as real permalinks and then
  reverted back to v6's inert links once the plan confirmed no
  single-announcement template exists. This case lands on the opposite
  side of that same judgment call from the announcement one: a real
  destination for each tab already exists (this same template,
  filtered), so building them live is "real destination beats a dead
  link," not the reverted case. `search.php` is now fully closed:
  masthead-width fix confirmed, functional filter tabs confirmed and
  approved by Farhad.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** `single.php` (article/note view) built, converted from
  `body-article-single.html`. Reuses existing Phase 4.1 CSS
  (`.article-hero`, `.article-sidebar`, `.related-rail`, etc. — all
  already ported, no new sections needed) and the existing
  `shola_article_hero` image size. Two new pieces of content model in
  `shola-core`: `shcore_author_note` (optional, the sidebar's
  "کاری از میز اقتصاد؛ در همکاری با..." collaboration line — genuinely
  per-article, unlike the fields below) and, in the theme,
  `shola_get_managing_editor()` (`inc/template-tags.php`) — "سردبیر
  مسئول" is the same fixed masthead-level role named on
  `page-about.php`'s هیئت تحریریه section, not a per-post value, so
  it's a filterable constant like `shola_get_masthead_code()` rather
  than a new meta field on every article. Word count + reading time are
  computed from `post_content` (`shola_get_reading_stats()`), not
  stored, so they can never go stale after an edit — 250 words/minute
  is a standard editorial baseline, the same kind of stated
  simplification as `issue-card.php`'s month+year date. Hero caption
  reuses the featured image's native WP attachment caption
  (`wp_get_attachment_caption()`) instead of a new meta field. Related
  essays reuse `template-parts/cards/card.php` directly (its one
  confirmed context beyond front-page.php's Latest grid) via a
  `tax_query` on the post's primary topic, excluding the current post.
  Save/share links kept as v6's inert `href="#"` — no real destination
  exists for either (no bookmarking/accounts, no chosen share target),
  same reasoning as the announcement-detail precedent.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed:** real bug found during live verification, not visible from
  source review — `array_shift( $terms )` (used to pull the primary
  topic for the breadcrumb) mutates the array in place, silently
  removing that term from `$terms` before the sidebar/footer tag lists
  render from the same variable. Invisible with 2+ topics (the list
  just looked one item short); with exactly one topic — the common
  case — `$terms` became an empty array, which is falsy, so the entire
  tag-list block silently vanished. Caught by testing against a real
  seeded article rather than trusting the code: curl showed zero
  `tag-outline` matches on a single-topic article, breadcrumb otherwise
  correct. Fixed by normalizing `$terms` to a plain array once up top
  and reading the primary topic via `reset( $terms )` (non-mutating)
  instead of `array_shift()`. Re-verified live: both sidebar and footer
  tag lists render correctly on a single-topic article.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** `single.php` verified live against two real seeded
  articles: `200`, zero PHP errors/warnings/notices, zero inline
  styles, correct breadcrumb/title/dek, correct word count/reading
  time, correct tag lists (sidebar + footer, after the fix above),
  hero caption correctly absent when the featured image has none,
  progress-bar markup present (behavior already handled by the
  existing `main.js`, ported Phase 4.1), and 3 related-essay cards
  rendering via `card.php`.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed, real bug found via live editor testing:** `single.php`'s
  bottom "TAGS" section (before the related-essays rail) was reading
  from the same `topic` taxonomy `$terms` as the sidebar, not
  WordPress's native tags (`post_tag`/برچسپ‌ها) — editing topic
  checkboxes changed it, editing the actual تگ field did nothing.
  Checked before assuming either fix direction: no IA/content-model doc
  (`Bilingual_Publishing_Site_IA_Sitemap_v1.0.docx`,
  `EXECUTION_PLAN.md`, the Persian proposal doc) defines a tag layer
  separate from Topic/Publication/Collection — the only doc that says
  "tags" at all is the Aeon design-analysis doc, and only as informal
  UI copy describing the topic-taxonomy display, not a spec for a
  distinct WP feature. First proposed removing `post_tag` support
  entirely on that basis (nothing implemented toward it) — **reversed
  by Farhad**: keep `post_tag` active, and use it for real. Sidebar
  keeps showing topics (unchanged); the bottom "TAGS" section now reads
  from `get_the_tags()` instead of the topic `$terms`.
  Approved by: Farhad, in this session (2026-08-06).

- **Added (deliberate content-model expansion beyond the IA docs'
  current spec — reviewed and approved, not quietly wired in):**
  WordPress's native `post_tag` taxonomy is now a real, functioning
  part of the article content model — a fourth classification layer
  alongside Topic/Publication/Collection, which is more than
  `Bilingual_Publishing_Site_IA_Sitemap_v1.0.docx` §6 currently
  specifies. Recording this explicitly, the same way this session's
  other deliberate deviations are on record (Persian Calendar plugin
  addition, contact-form dynamic subjects, search.php's functional
  filter tabs): this expands the IA doc's taxonomy table, it isn't
  something the doc already implied. Verified end-to-end: seeded two
  real tags (بازار غیررسمی، کار و مزد) on one test article via
  `wp_set_post_tags()` (throwaway script, deleted after running) — live
  `curl` confirmed the sidebar still shows only "اقتصاد" (the topic,
  untouched) while the bottom TAGS section now shows the two real tags
  linking to their native `/tag/...` archives. A second, untagged
  article confirmed the section renders as fully absent (no empty box)
  rather than as a bug, per Farhad's note that existing seeded content
  has no real tags yet and that's expected, not something to fix.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** `single-issue.php` built, converted from
  `body-issue-single.html`. Reuses `.issue-hero`/`.issue-cover`/
  `.badge-current`/`.issue-meta` CSS already ported in Phase 4.1 for
  `taxonomy-publication.php`'s embedded current-issue preview, and
  `shola_get_managing_editor()` from `single.php`'s build for "سردبیر
  مسئول". "شمار صفحات" (page count) stays omitted from `dl.issue-meta`,
  same already-ruled-out-of-scope decision as before (2026-08-06) — not
  re-litigated. Cover image, "دریافت PDF", and "پیش‌نمایش درون‌مرورگری"
  all point at the real PDF attachment URL
  (`wp_get_attachment_url( shcore_pdf_id )`) — download vs.
  `target="_blank"` respectively, since most browsers render a PDF
  inline in a new tab without one, giving both v6 buttons a real,
  distinct, functioning destination instead of two links to the same
  place. Falls back to inert `href="#"` on both when no PDF is
  attached yet, matching the site's established convention for "no
  real destination exists."
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** `شمار مطالب` ("۱۲ مقاله + ۲ ترجمه" in v6) is derived by
  counting `shcore_contents` lines, not a new field — v6's own count
  is table-of-contents-derived, and `shcore_contents` already exists
  for exactly that content, so counting it directly avoids a second
  field the editor would have to keep in sync by hand. A line's
  `SECTION` value of `TRANSLATION` counts toward "ترجمه" instead of
  "مقاله"; empty/absent `shcore_contents` omits the whole
  `dt`/`dd` pair rather than showing "۰ مقاله".
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** `shcore_contents`'s free-text format is now a documented
  convention, not fully unstructured — one line per table-of-contents
  entry, `SECTION|Title|Byline` (pipe-delimited; SECTION and Byline are
  optional, only Title is required). New
  `\SholaCore\Meta_Fields::get_issue_contents( $post_id )` parses it
  (malformed/empty lines skipped, not errored — this is editor-typed
  free text, not a strict form). The metabox textarea
  (`class-meta-fields.php`) now has an inline description explaining
  the format plus a real placeholder example, and switched to a
  monospace (`code`) textarea so the `|` delimiters are easy to line
  up. Table-of-contents entries are rendered as plain (unlinked) text —
  not `href="#"` like the announcement-detail precedent — since a TOC
  line isn't a WP entity with a page identity at all (per the
  PDF-only, `EXECUTION_PLAN.md` Phase 0.3 resolved assumption), so an
  inert link would misleadingly suggest one exists; v6's own
  `article-single.html` links here are a static-prototype artifact of
  reusing one demo page, not evidence a real per-entry destination was
  ever intended. Called from the theme via `class_exists()`-guarded
  static call (`single-issue.php`), degrading to an empty TOC if
  `shola-core` is ever inactive, per CLAUDE.md §2's no-fatal-if-plugin-
  missing rule — the first template this session to call a plugin
  class directly from the theme rather than only via `get_post_meta()`
  or a rewrite/permalink filter, so this guard is a new, deliberate
  precedent, not copied from an existing pattern.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** `single-issue.php` verified live against 3 real issues
  (شعله جاوید #32, current publication; two جهان برای فتح issues,
  archived publication): `200`, zero PHP errors/warnings/notices, zero
  inline styles. Confirmed: breadcrumb/title/badge exact match against
  `body-issue-single.html`, `badge-current` vs. `badge-archive`
  correctly follows publication status, real PDF download/preview
  links work, `شمار مطالب`/`حجم فایل` correctly computed from real
  data, `سردبیر مسئول` correct, TOC renders exactly as seeded
  (including `TRANSLATION`-tagged entries counting correctly toward
  "۱ ترجمه") on the issue with `shcore_contents` set (seeded via a
  throwaway script, deleted after running), and — on issues with no
  PDF/no TOC — every dependent piece (buttons, `حجم فایل` row, the
  whole TOC section) degrades gracefully to absent rather than showing
  empty/broken markup.
  Approved by: Farhad, in this session (2026-08-06).

- **Changed (real schema change, editorial-workflow-facing, reviewed
  before building — not bolted on after):** `shcore_contents` replaces
  the pipe-delimited-text editing UI with a real repeater: SECTION
  (dropdown), Title (text), Byline (text) per row, add/remove-row
  controls, extending the existing PDF-picker admin-JS pattern
  (`admin/js/meta-boxes.js`) rather than a new dependency. Plan
  reviewed and approved before implementation:
  - **SECTION dropdown is dynamic**, not hardcoded — pulled live from
    `get_terms( 'topic' )` plus a fixed `TRANSLATION` pseudo-option, so
    a new topic term doesn't need a code change to appear here. (A
    stray pre-existing test term, "موضوع آزمایشی", was noticed showing
    up in this dropdown while verifying — unrelated leftover data in
    the `topic` taxonomy, not introduced by this change; flagged here,
    not cleaned up as part of this task.)
  - **Storage**: `shcore_contents` keeps its single meta key, but its
    *contents* change from pipe-delimited free text to a JSON-encoded
    array of `{section, title, byline}` objects — simpler atomic
    save/read than WP's repeated-meta-row pattern for this small,
    bounded row count. `get_issue_contents()`'s return shape is
    unchanged, so `single-issue.php` needed zero changes.
  - **Every field is genuinely optional at every layer, by design, not
    just tolerated**: `sanitize_issue_contents()` keeps any row with at
    least one non-empty field (title-only, section-only, byline-only,
    any combination) and only drops rows that are fully empty across
    all three — that was the explicit choice made, stated here per
    Farhad's instruction to say which of "save blank vs. drop blank
    rows" was chosen. A completely empty repeater (zero rows) saves as
    `''` and reads back as an empty array, matching how absent
    `shcore_contents` already behaved before this change.
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed, real bug found and fixed before it could ship:** the first
  migration attempt (converting issue #32's existing pipe-delimited
  text to the new JSON format) silently corrupted every Persian
  character in the stored data — `wp_json_encode()` without
  `JSON_UNESCAPED_UNICODE` escapes non-ASCII text as `\uXXXX`
  sequences, and something in WordPress's own post-meta save pipeline
  (isolated and confirmed empirically via a throwaway scratch-post
  test, not just assumed: a plain `update_post_meta()` call with no
  custom code involved reproduced the exact same corruption)
  applies `stripslashes()`-style unslashing on the way to the
  database — which strips the backslash off every `\uXXXX` escape,
  turning correctly-encoded Persian text into literal garbage
  (`u0627` instead of what it should decode to). Caught by checking
  the actual re-parsed output after the first migration attempt rather
  than assuming success from "no PHP error was thrown" — there wasn't
  one; the corruption was silent. Fixed by encoding with
  `JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES` everywhere
  `shcore_contents` is written (`sanitize_issue_contents()`,
  `save_meta_boxes()`), which avoids the problem entirely by never
  producing a backslash-escape for Persian text (or `/`) in the first
  place, so there's nothing for the unslashing step to corrupt.
  Re-ran the migration afterward — verified below.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** Full verification pass, per Farhad's explicit request
  to verify each layer rather than trust the code:
  1. **Migration**: issue #32's 6 original rows re-migrated after the
     `JSON_UNESCAPED_UNICODE` fix — raw stored JSON confirmed to
     contain real UTF-8 Persian text (not escapes), all 6 rows re-parse
     with content byte-identical to the original v6 source text. No
     data loss.
  2. **Add/remove-row UI**: wp-admin itself needs a login this session
     doesn't have credentials for, so tested differently but for-real:
     built a standalone HTML harness that loads the actual
     `admin/js/meta-boxes.js` file and the actual server-rendered
     repeater markup (`render_issue_metabox()`'s real output, not a
     mock) — the same PHP and the same JS the real editor screen would
     load, just outside the wp-admin auth wall. Confirmed: initial
     load shows the real 6 rows; "+ افزودن ردیف" appends a correctly-
     indexed new row (`[6]`); removing a row correctly removes only
     that row; adding again after a mid-list removal correctly skips
     the now-missing index rather than colliding with it (`[7]`, not a
     reused `[2]`) — the JS's collision-avoidance loop verified doing
     its job, not just present in the code.
  3. **Fresh issue, no existing TOC**: `render_issue_metabox()` called
     directly on a scratch draft post with empty `shcore_contents` —
     renders the repeater table with zero rows and the add-row button/
     row-template intact, no PHP warnings in the captured output.
  4. **Front-end unchanged after migration**: `single-issue.php` on
     issue #32 re-verified via `curl` post-migration — `200`, zero
     inline styles, zero PHP errors, identical TOC output (numbering,
     section labels, "۵ مقاله + ۱ ترجمه" summary) to before the schema
     change.
  5. **Optional-fields behavior, tested explicitly at both layers**:
     a title-only row, a section-only row, and a fully empty repeater
     each round-tripped through `sanitize_issue_contents()` →
     `get_issue_contents()` correctly (confirmed via `var_dump`, no
     undefined-index warnings — every returned row always has all
     three keys, empty string where absent). Then re-tested live on
     the real front end: temporarily added a title-only row and a
     section-only row to issue #32's real TOC, `curl`-verified `200`
     and zero PHP warnings, confirmed each rendered exactly as the
     template's existing conditionals intend (title-only: bare
     `<span>`, no meta-mono line, no byline line; section-only:
     meta-mono line present, empty `<span class="link-quiet"></span>`,
     no byline line) — not broken, just minimally rendered, then
     restored the original 6 clean rows afterward.
  All throwaway scripts (migration, isolation test, verification test,
  harness) deleted after running and confirmed unreachable (`404`).
  `debug.log` checked for any `shcore_contents`/`Meta_Fields`-related
  entries from this work — none found (one unrelated pre-existing
  warning noticed in `class-contact-settings.php:118`, "Array to
  string conversion" — from an earlier feature, not touched, flagged
  here for awareness only).
  Approved by: Farhad, in this session (2026-08-06).

- **Fixed, real bug, found only after actually loading the real
  wp-admin edit screen:** Farhad reported the repeater's horizontal
  overflow and missing remove buttons were both still broken after the
  prior verification pass — correctly, since that pass relied on a
  standalone harness that never modeled wp-admin's own metabox layout,
  so it couldn't have caught either. Investigated this time in the
  real admin context: generated a real authenticated session
  server-side (`wp_set_auth_cookie()` on this local dev install, via a
  throwaway script, deleted immediately after use — no credentials
  were available or needed otherwise) and navigated the actual browser
  to the real `post.php?post=26&action=edit` screen. Found the real
  cause via computed-style inspection, not guessing: the repeater
  `<table>` used the browser default `table-layout: auto`, and a
  native `<select>` with no explicit width auto-sizes to fit its
  widest `<option>` text — one topic term happens to have a very long
  slug (the same stray "موضوع آزمایشی" test term flagged, not fixed,
  in the previous entry), ballooning the SECTION column to ~800px
  alone inside a ~761px metabox `.inside`. The table rendered at
  ~1593px total — the remove-row buttons were never hidden or absent,
  they were being pushed to negative X coordinates by the overflow (this
  admin is RTL, so excess width pushes left, off-screen) — confirmed
  directly via `getBoundingClientRect()` on the real page before any
  fix (`left: -512.6`) and after (`left: 369.8`, on-screen). Fixed with
  a new `admin/css/meta-boxes.css` (enqueued alongside the existing
  admin JS): `table-layout: fixed` + `width: 100%` on the table with
  percentage column widths, and `width: 100%` on the `<select>`/
  `<input>` elements so they fill their cell instead of dictating it.
  Re-verified live, same authenticated session, same real edit screen
  (not the harness): table width now `737.2px` inside the `761.2px`
  container (no overflow), all 6 remove buttons at positive on-screen
  coordinates, clicking one live actually removed a row (6→5),
  clicking "+ افزودن ردیف" live actually added one back with a
  correctly-indexed, visible remove button, and `document.body.scrollWidth`
  no longer exceeds `window.innerWidth`. No changes were saved to the
  real issue during this session (form never submitted) — logged out
  cleanly afterward. Front end re-checked via `curl` after the CSS
  addition: `200`, zero inline styles, unaffected (this CSS only loads
  on the `issue`/`document` post-edit screens).
  Approved by: Farhad, in this session (2026-08-06).

- **Changed:** `document` post type gains `'editor'` support
  (`class-post-types.php`), so `single-document.php`'s "دربارهٔ این متن"
  section can be real, per-document editorial prose written via the
  block editor. Same reasoning already applied to `page-about.php`:
  v6's 2-paragraph "About the Text" is substantive content that varies
  per document, not a one-line dek (that's `post_excerpt`, still used
  separately in the hero) — an editor should be able to write it
  without a code change. Documents seeded before this change simply
  show the section as absent until filled in; no content-model field
  was fabricated to backfill it.
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** `single-document.php` built, converted from
  `body-document-single.html`. Reuses `.issue-hero`/`.issue-meta`
  wholesale from `single-issue.php` (same anatomy: cover, badge, title/
  dek, meta list, real PDF download/preview buttons) and
  `template-parts/rows/document-row.php` for the "اسناد مرتبط" related
  list (3 documents sharing the current one's collection, excluding
  itself) — no new partial needed for either. "شمار صفحات" (page
  count) stays omitted from `dl.issue-meta`, same out-of-scope ruling
  as `single-issue.php`, not re-litigated. "ویراستار" reuses
  `shola_get_managing_editor()` (same person/value as `single-issue.php`'s
  "سردبیر مسئول" — v6 just uses a different label word for the same
  role in this context, matched exactly rather than assumed identical).
  Verified live via `curl` against a real seeded document ("دولت و
  انقلاب"): `200`, zero PHP errors/warnings/notices, zero inline
  styles, breadcrumb/badge/title/dek/meta/buttons all correct against
  real data, 3 related documents rendering, "About the Text" correctly
  absent before content was seeded and correctly present after (seeded
  via a throwaway script, deleted after running).
  Approved by: Farhad, in this session (2026-08-06).

- **Added:** `404.php` built. No v6 mockup exists for an error state —
  checked directly (no 404/error-named file anywhere in
  `03_UI_Design/shola-jawid-ui`) rather than assumed — so per
  `EXECUTION_PLAN.md`'s own instruction for this row ("Match v6
  error-state design if one exists, else brand-consistent minimal
  page"), this is assembled entirely from existing components/tokens
  (`.h-display`, `.dek`, `.btn-primary`/`.btn-ghost`) rather than any
  new visual design. "بازگشت به صفحهٔ اصلی" (home) and "جست‌وجو در سایت"
  (pre-filled to `/?s=`, reusing `search.php` rather than inventing a
  second inline search box) give a genuinely useful next step instead
  of a dead end. Verified live via `curl` against a nonexistent URL,
  `?p=99999`, and a nonexistent topic archive slug: all three
  correctly return `404` (not a silent `200` or a redirect), zero PHP
  errors/warnings/notices, zero inline styles.
  Approved by: Farhad, in this session (2026-08-06).

- **Resolved:** Every template in `EXECUTION_PLAN.md`'s page-to-
  template map (§4.2) is now built. Phase 4 (templates) is
  functionally complete; remaining work moves to whatever Phase 5+
  covers per the execution plan (fonts self-hosting, security
  hardening, final QC, etc.) — not tracked further in this log entry,
  since that's ordinary phase progression, not a rule
  application/deviation.
  Approved by: Farhad, in this session (2026-08-06).

## 2026-08-07

- **Resolved:** §4.3's second checklist item (CF7 form submission
  actually arrives by email in local testing) — never actually tested;
  the earlier `page-contact.php` closure covered visual/markup
  correctness only. Found LocalWP's mail catcher for this site via
  `%AppData%\Roaming\Local\sites.json` (each LocalWP site has its own
  Mailpit instance — this one's at `http://localhost:10085`, not a
  fixed/shared port). Submitted the real, live contact form in a
  browser (typed field values, clicked the actual submit button — not
  a simulated server-side POST, so CF7's own JS/AJAX/nonce path was
  exercised exactly as a real visitor's would be): CF7 reported
  `status: sent`, and Mailpit's API confirmed the message actually
  arrived — correct recipient (`info.sholajawid@gmail.com`), correct
  `Reply-To` (the submitted address), correct interpolated subject,
  clean body with all 4 fields present and no leftover `[tag]`
  placeholders. No failure found to diagnose — delivery worked
  correctly on the first real test.
  Approved by: Farhad, in this session (2026-08-07).

- **Added:** `docs/screenshots/phase4/` — the saved side-by-side
  screenshot archive §4.4 asks for. Farhad had already done this QC
  live in the browser throughout Phase 4.2 as each template was built;
  this creates the saved record, not a new review pass. Captured with
  headless Chrome (`chrome.exe --headless=new --window-size=1280,2400
  --screenshot=...`) rather than the interactive browser tool, since
  the latter's read/screenshot actions are gated behind a per-site
  approval this session can't grant for `shola-jawid.local`; headless
  Chrome sidesteps that entirely and needed no approval. 14 v6-vs-live
  pairs (one per template with a v6 counterpart) plus a live-only
  capture for `404.php` (no v6 mockup exists for that state — already
  established, see this file's own earlier 2026-08-06 entry) — 29
  files total, real seeded content throughout, not placeholder text.
  Full pairing table in that folder's own `README.md`.

  Real tooling bug hit and fixed while building the capture script,
  worth recording since it could bite again: this session's Bash tool
  has a quirk where a literal backslash immediately before `${var}`
  (e.g. `"$OUT\\${name}_v6.png"`) silently prevents the variable from
  expanding — the output filename contained the literal text `${name}`
  instead of its value, and the leading backslash was consumed too.
  Not standard POSIX/bash behavior; reproduced in isolation
  (`echo "\\${name}"` → literal `${name}`, not the variable's value)
  before concluding it was a real platform quirk and not user error.
  First batch silently overwrote the same two wrongly-named files 5
  times in a row rather than erroring, so the failure wasn't obvious
  until the directory was checked directly. Fixed by using forward
  slashes throughout the path construction instead (Windows binaries,
  including `chrome.exe`, accept `D:/foo/bar.png` just as well as
  backslash paths) — avoids the bug entirely rather than working around
  it. Re-ran the full batch after the fix; verified all 29 files
  written with correct, distinct names and plausible (non-blank)
  file sizes before trusting the batch was complete.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** `EXECUTION_PLAN.md` updated — §4.3 and §4.4 checklists
  marked complete (see entries above for what was actually verified),
  Phase 4's Definition of Done fully checked off: the inline-`style=""`
  grep item marked complete with an explicit note that the literal grep
  command still returns 2 lines (both confirmed false positives —
  doc-comment prose mentioning the string, not real inline attributes
  — so a future reader doesn't misread that as a regression), and the
  `header.php` nested-button-bug item confirmed via `curl` (not just
  visually) and checked off.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** §5.5 (Jalali-calendar localization) re-audited per
  Farhad's explicit instruction not to assume the existing checkmarks
  still hold — that section was last verified against only
  `front-page.php`/`page-publications.php`/`taxonomy-publication.php`
  (2026-08-06), before `single.php`, `single-issue.php`,
  `single-document.php`, `search.php`, `page-contact.php`,
  `page-about.php`, and `404.php` were built. Audited every
  `get_the_date()`/`shola_get_gregorian_year()`/
  `shola_get_english_month_abbr()` call added in those templates:
  every one correctly follows the already-established dual convention
  — plain `get_the_date()` for human-readable content dates (auto-
  converts to Jalali via the Persian Calendar plugin's global hook)
  vs. the deliberate Gregorian-mono-label helpers for issue/publication
  meta contexts (`single-issue.php`/`single-document.php`'s "تاریخ
  نشر", `template-parts/search/result.php`'s issue byline) — no case
  where the two were mixed up. `single.php`, `page-contact.php`,
  `page-about.php`, and `404.php` have no date output at all (matches
  v6 — `body-article-single.html` doesn't show a publish date either).
  Verified live, not just by code review: `curl`'d a real search
  results page and confirmed an article byline renders a genuine
  Jalali date (`۱۵ مرداد ۱۴۰۵`, Persian digits and month name) while
  the issue byline on the same results page correctly stays the
  Gregorian mono-label (`AUG ۲۰۲۶`). §5.5's checkmarks remain accurate;
  no update needed there.
  Approved by: Farhad, in this session (2026-08-07).

- **Added:** §5.1 (roles & permissions). Read the IA doc's §7 role table
  directly rather than assuming stock WP already matches it: three of
  four roles (Administrator/Author/Contributor) map cleanly, but
  Editor's spec — "manage categories & menus" — doesn't, since nav-menu
  editing needs `edit_theme_options`, a capability stock WP only grants
  Administrator. No narrower "menus only" capability exists in core,
  so satisfying the doc's letter means Editor also gains
  Customizer/widget access as a side effect. Presented this as a real
  fork before touching anything (grant the broader stock capability vs.
  leave menus admin-only vs. build a bespoke per-screen capability
  check) — Farhad chose granting `edit_theme_options`, the simplest
  option, over hand-rolling permissions complexity for a narrow gain.
  New `wp-content/plugins/shola-core/includes/class-roles.php`
  (`\SholaCore\Roles`), gated on `admin_init` so it's cheap and
  self-healing if a role reset ever clears the capability, rather than
  only on plugin activation.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** Created `test_admin`/`test_editor`/`test_author`/
  `test_contributor` accounts (one per role, random generated
  passwords not recorded anywhere — these are local capability-testing
  fixtures, not real accounts) and spot-tested all four against the
  IA doc §7 table via capability flags
  (`manage_options`/`edit_users`/`edit_theme_options`/
  `manage_categories`/`publish_posts`/`edit_others_posts`/
  `delete_others_posts`/`upload_files`/`edit_posts`) — all four match
  the table exactly post-fix. Went further than flag-checking for two
  of them, per the standing pattern this session of verifying real UI
  behavior rather than trusting a capability check alone: generated a
  real authenticated session for `test_editor` (same
  `wp_set_auth_cookie()` technique used earlier for the TOC-repeater
  admin verification) and confirmed it genuinely reaches Appearance →
  Menus (page title, not a redirect); did the same for
  `test_contributor` and confirmed it's genuinely blocked with WP's own
  "› خطا" permission-denied page, not just that the capability flag
  reads false. Logged out cleanly afterward both times.
  Approved by: Farhad, in this session (2026-08-07).

- **Added:** §5.2 (custom SEO, no plugin per `CLAUDE.md` §3). New
  `wp-content/plugins/shola-core/includes/class-seo.php`
  (`\SholaCore\SEO`) — meta description (post excerpt for singular,
  term description for taxonomy archives, site tagline fallback
  everywhere else), Open Graph tags (title/description/url/type/
  site_name/locale/image), and the canonical link for the non-singular
  contexts core doesn't already cover (`<title>` and singular
  `rel_canonical` were already correct via core — confirmed live before
  writing anything, not assumed). Inert, self-referential `hreflang`
  scaffolding (`fa` + `x-default`, both pointing at the current page)
  per `CLAUDE.md` §1 — English isn't live this phase, so this avoids
  re-touching the file later without claiming a second language exists
  now.

  Sitemap: confirmed via `curl` before writing any customization code
  that `issue`/`document`/`announcement` already appear in
  `wp-sitemap.xml` by default (all `public => true`) — the checklist's
  "includes posts, issues, documents, announcements" requirement needed
  zero new code. Did trim the default sitemap via
  `wp_sitemaps_add_provider` (drops the `users` provider — no
  `author.php` template was ever in the page-to-template map, so
  publishing author-archive URLs would point nowhere real) and
  `wp_sitemaps_taxonomies` (drops `post_format` and native `category` —
  neither is a real content destination on this site; `topic` is the
  actual classification taxonomy).
  Approved by: Farhad, in this session (2026-08-07).

- **Fixed:** real bug caught during live verification, not from reading
  the code: the first version of `class-seo.php`'s canonical-URL
  builder used `global $wp; home_url( add_query_arg( array(), $wp->
  request ) . '/' )` — `$wp->request` holds only the matched rewrite
  *path*, which is empty for a query-string-only view like search
  (`?s=...`), so every search page's canonical silently resolved to the
  front page instead of the actual search URL. `curl` showed
  `<link rel="canonical" href=".../"/>` on a search-results page before
  this was caught. Fixed by using WP's own per-context URL functions
  instead of reconstructing one generically: `get_search_link()` for
  search, `get_term_link()` for taxonomy archives,
  `get_post_type_archive_link()` for post-type archives, `home_url('/')`
  for the front page, and the raw request URI only as the true fallback
  (404 and anything else uncovered — there's no "real" canonical target
  to guess for a genuine 404). Re-verified live across search, a
  taxonomy archive, an `is_post_type_archive()` case
  (`archive-announcement.php`), the front page, and a 404 — every one
  now correct.
  Approved by: Farhad, in this session (2026-08-07).

- **Added:** the site's WordPress tagline (`blogdescription` option)
  was empty, which meant `class-seo.php`'s fallback description
  (`get_bloginfo('description')`) silently rendered `content=""` on the
  front page, search, and 404. Rather than duplicate the footer's
  existing tagline text (`footer.php`) as a second hardcoded string in
  the plugin, fixed it at the actual right layer: set the real WP
  tagline to that same text via `update_option('blogdescription', ...)`
  — the tagline is precisely what that option is *for*, so this isn't a
  workaround, it's filling in a genuine site-configuration gap. Verified
  the expected, correct side effect this has on the front page's
  `<title>` tag too (core's `wp_get_document_title()` appends the
  tagline to the site name specifically on the front page) — a more
  descriptive, SEO-appropriate title than the site name alone, not a
  regression.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** §5.3 (search) — already built and thoroughly tested in
  Phase 4.2 (`search.php`'s own closing entries above). Re-confirmed
  live via `curl` that results still span articles/notes, issues, and
  documents rather than trusting the old entry to still hold — no
  rebuild needed, checklist marked complete.
  Approved by: Farhad, in this session (2026-08-07).

- **Added:** §5.4 fonts — Newsreader, Inter, and JetBrains Mono
  (previously Google Fonts CDN, per `header.php`'s own Phase-5.4-
  deferral comment) are now self-hosted in
  `assets/fonts/{inter,jetbrains-mono,newsreader}/woff2/`, matching the
  existing Farhang2/ModamPro pattern from Phase 4.1. Fetched each
  family's actual CSS from Google's `css2` endpoint with a modern
  Chrome User-Agent (to get `woff2`, not legacy formats), filtered to
  just the `latin` unicode-range subset (this site never renders these
  three fonts for anything but Latin text — mono-labels, the brand
  code; Persian always uses Farhang2/ModamPro).

  Real finding worth remembering, confirmed empirically rather than
  assumed: requesting 3 discrete weights for Inter (400/500/600) and 2
  for JetBrains Mono (400/500) returned the *same* file URL for every
  weight in each case — re-fetched Inter alone, in isolation, to
  confirm this wasn't a combined-request artifact before trusting it.
  This is correct, expected behavior for Google's variable-font
  serving scoped to a requested weight range, not a fetch bug — the
  browser renders the intended weight from each rule's `font-weight`
  descriptor via the file's own variable axis despite the identical
  bytes. Declared as a weight range (`font-weight: 400 600` etc.) in
  one `@font-face` rule per family/style rather than three redundant
  blocks pointing at the same file. Only 4 distinct files needed as a
  result, not 6. No `.woff` legacy fallback for these three (unlike
  Farhang2/ModamPro's woff2+woff pairs) — Google's response didn't
  include one for this request and woff2 support is universal in any
  browser this project targets; not worth a second round-trip for
  near-zero real reach.

  `header.php`'s Google Fonts `<link>`/`<preconnect>` tags removed
  entirely. Verified live: zero `fonts.googleapis`/`fonts.gstatic`
  requests remain on any page (`curl`), all 4 new font files return
  `200`. The interactive browser tool's JS-eval capability became
  unresponsive mid-session (a tooling-level issue, not caused by this
  change — even a trivial `'ping'` script timed out on a fresh tab) —
  fell back to the same headless-Chrome screenshot technique already
  proven reliable earlier this session rather than assuming the fonts
  loaded correctly: visually confirmed clean, correctly-styled
  typography (mono labels, no fallback/tofu glyphs, no layout
  breakage).
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** §5.4 responsive images — confirmed via `curl`, not
  assumed from reading `shola_get_featured_image()`'s delegation to
  core: real `srcset` with all registered intermediate sizes
  (300w/768w/800w/1024w/1536w/1920w) present on the homepage's featured
  images. No code change needed.
  Approved by: Farhad, in this session (2026-08-07).

- **Added:** §5.4 performance baseline — ran Lighthouse (`npx
  lighthouse`, headless Chrome) against the homepage. First run:
  Performance 96, Accessibility 93, Best Practices 78, SEO 100. Full
  metrics and reasoning in `docs/screenshots/phase5-perf-baseline.md`
  (along with the raw `.report.html`/`.report.json`). Best Practices'
  only misses (`is-on-https`, `redirects-http`) are expected on a
  local dev site with no SSL certificate — Phase 6 scope, not a Phase 5
  gap. No caching gap found, so no `CLAUDE.md` §3 whitelist discussion
  needed.
  Approved by: Farhad, in this session (2026-08-07).

- **Fixed:** two real accessibility bugs the Lighthouse run itself
  surfaced — acted on them rather than just recording the score, since
  a baseline audit that finds real bugs and doesn't fix them isn't
  doing its job. `aria-hidden-focus`: `front-page.php`'s hero-image
  link (`<a class="hero-media" aria-hidden="true">`) is a deliberate
  duplicate of the properly-labeled link on the article title right
  below it, but `aria-hidden="true"` alone doesn't remove a focusable
  `<a>` from the keyboard tab order — screen-reader/keyboard users hit
  an "invisible," unlabeled stop. `link-name`: the same root cause on
  `template-parts/cards/card.php`'s `.card-media` link, flagged by the
  audit on a document card with no distinguishing image alt text.
  Searched the codebase for the same structural pattern rather than
  waiting for a second audit run to catch it template-by-template:
  found and fixed the identical issue in
  `taxonomy-publication.php`'s embedded current-issue `.issue-cover`
  link too. All three fixed by adding `tabindex="-1"` alongside the
  existing `aria-hidden="true"`, fully excluding the redundant link
  from assistive-tech interaction instead of leaving it half-hidden.
  Checked the *other* `.issue-cover` links
  (`single-issue.php`/`single-document.php`) too before assuming they
  needed the same fix — they don't: those aren't duplicates of another
  link, they're the only way to reach the PDF, and already carry a real
  `aria-label`. Re-ran Lighthouse after the fix to confirm rather than
  assume it worked: Accessibility 93 → 100, both audits now pass
  (Performance's 96 → 94 shift between runs is normal local-audit
  variance — the change touched only accessibility-tree attributes,
  nothing render- or load-affecting). Verified live via `curl` on three
  affected templates that nothing rendered differently and no PHP
  errors/warnings appeared.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** Phase 5 (Roles, SEO, search, performance) is
  functionally complete — every §5.1–§5.4 checklist item in
  `EXECUTION_PLAN.md` verified and checked off, one stale reference
  corrected along the way (§5.4's own text still named Vazirmatn/
  Markazi Text as the fonts to self-host — leftover from before the v6
  brand fonts were finalized as Farhang2/ModamPro; corrected in place
  rather than left to confuse a future reader). §5.5 (Jalali calendar)
  re-audited above and confirmed still accurate. Next per the plan:
  Phase 6 — security hardening, backups, deployment prep.
  Approved by: Farhad, in this session (2026-08-07).

- **Added:** Phase 6.1 — Wordfence installed for real (same unzip-into-
  live-plugins pattern as CF7/Persian Calendar, not git-tracked). Before
  touching config, presented the plan (firewall mode for a dev site,
  alert-email destination, and confirmed the deliberate-lockout test
  wouldn't be able to touch the real `SJ_manager` account) and got
  sign-off first, per Farhad's explicit request for anything security-
  sensitive.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** Firewall switched from its self-initialized Learning
  Mode (a real 7-day default) to `enabled` — a dev site with no real
  traffic has nothing meaningful to learn from, so enforcing now with
  Wordfence's default ruleset is correct here; documented (not just
  silently decided) that the real ~1-week learning period is still the
  right move for whoever does the actual production launch. Set via
  `wfWAF::getInstance()->getStorageEngine()->setConfig('wafStatus',
  'enabled')` after reading Wordfence's own source to find the correct
  API — the regular `wfConfig` settings store (used for everything else)
  turned out not to be where WAF status lives, a separate storage
  engine is. Re-confirmed via a fresh config read afterward rather than
  trusting the one call: `wafStatus: enabled`.
  Approved by: Farhad, in this session (2026-08-07).

- **Fixed/Resolved:** Login rate-limiting. Tightened the (quite
  generous) defaults — `loginSec_maxFailures` 20→5,
  `loginSec_lockoutMins` 240→60, `loginSec_lockInvalidUsers` 0→1 (also
  count attempts against nonexistent usernames, not just wrong
  passwords for real ones), `alertEmails` set to the placeholder
  address already used site-wide. Deliberately used the throwaway
  `test_contributor` account (created in Phase 5.1, password never
  recorded) for the actual failed-login test, never the real admin —
  per the plan agreed before starting.

  Real, non-trivial finding while running that test: repeated genuine
  failed logins (confirmed actually reaching WordPress's own auth
  check — the correct Persian "wrong password" error rendered every
  time, both via raw `curl` POSTs and later via real browser form
  submissions once a `wordpress_test_cookie` requirement was diagnosed
  and fixed) never triggered a lockout, no matter how many were sent.
  Traced it to the actual cause by reading Wordfence's own source
  rather than guessing: `wfBlock::isWhitelisted()` and
  `wfBlock::createLockout()` both unconditionally exempt private/
  loopback IPs (`127.0.0.1` included) — a deliberate Wordfence default
  specifically so a site owner developing locally can never lock
  themselves out, confirmed live (`isWhitelisted('127.0.0.1') ===
  true`). Chose not to defeat that protection just to force a positive
  test result — it's correct, desirable behavior, and won't exist on
  the real production site (real attackers don't connect from
  `127.0.0.1`).

  Verified what's actually verifiable from here instead: confirmed
  every real config value the lockout logic would act on, and
  separately proved the *alert* side of the exact same `lockOutIP()`
  call Wordfence would make on a real lockout — instantiated
  `wfLoginLockoutAlert` directly and called `->send()`, confirmed via
  Mailpit that `[Wordfence Alert] ... User locked out from signing in`
  arrived at the correct recipient with real, correctly-formatted
  content. (Noticed in passing: `lockOutIP()`'s alert step isn't itself
  whitelist-gated, only its block-creation sub-call is — so this test
  and an earlier direct `lockOutIP()` call both sent a real test alert,
  2 harmless duplicates, both caught locally.) Unlock mechanism
  (`wfBlock::unblockIP()`) was verified callable in a safe dry run
  *before* any of this testing began, per the explicit plan to have
  recovery confirmed working before taking any risk, not after.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** Malware scan schedule + alerting. `scheduledScansEnabled`/
  `alertOn_scanIssues` were already `1` by Wordfence's own default, but
  no scan was actually in WP-Cron yet — a real gap, since Wordfence
  normally schedules this during its own interactive setup wizard,
  which activating the plugin via script bypassed. Scheduled one
  directly (`wp_schedule_single_event` on
  `wordfence_start_scheduled_scan`), confirmed via `wp_next_scheduled()`.
  Did not force a full synchronous scan run through a single script
  request — a genuinely heavy, potentially multi-minute filesystem/
  signature operation not worth forcing through that path; schedule +
  alerting + hook registration are all confirmed real and correctly
  wired, which is what "configured" actually requires here.
  Approved by: Farhad, in this session (2026-08-07).

- **Added:** Phase 6.2 hardening. `DISALLOW_FILE_EDIT` added to the live
  `wp-config.php` with an inline comment on scope (blocks the in-browser
  Theme/Plugin File Editor only, not git/SFTP deploys). New
  `wp-content/plugins/shola-core/includes/class-security.php`
  (`\SholaCore\Security`) — each item checked live first, nothing
  assumed needed: XML-RPC confirmed fully functional
  (`system.listMethods` returned the real method list including
  `system.multicall`) and unused by this project, disabled via
  `xmlrpc_enabled` plus removing the `X-Pingback` header (the filter
  alone doesn't hide the endpoint's existence, confirmed by checking
  both separately); WP version string confirmed exposed three ways
  (`<meta name="generator">`, RSS `<generator>`, core-asset `?ver=`
  query strings) and removed from all three; REST API user-enumeration
  checked live first and found already correctly restricted by
  WordPress core (`401 rest_user_cannot_view` for anonymous requests) —
  zero code needed, recorded so it's clear this was verified, not
  overlooked.
  Approved by: Farhad, in this session (2026-08-07).

- **Fixed:** real bug caught before it shipped, not after — the first
  version of the core-asset version-string filter stripped `?ver=` from
  *every* enqueued script/style indiscriminately, which would have also
  broken the theme's own legitimate cache-busting
  (`main.css?ver=1.0.0`, `shola_enqueue_assets()`) the moment a real
  deploy needed browsers to fetch a fresh file. Caught by re-reading the
  filter's own effect before verifying it live, not after a report came
  in. Fixed by scoping the strip to `/wp-includes/`/`/wp-admin/` paths
  only. Verified live afterward on a real admin page with genuine core
  assets present (the sparser front page didn't have any core `?ver=`
  URLs to test against, confirmed and switched test pages rather than
  reporting an inconclusive result as a pass): all core `?ver=` strings
  gone, all 17 real third-party plugin/theme `?ver=` strings on that
  same page still intact. XML-RPC's actual protection was also
  re-verified correctly, after a first test mistakenly used the
  unauthenticated `system.listMethods` (not gated by the disable filter
  — pure introspection, no login involved) and wrongly looked like the
  filter wasn't working; re-tested with `wp.getUsersBlogs`, the real
  authenticated method attackers target, which now correctly returns
  the standard "XML-RPC services are disabled on this site" fault.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** All `CLAUDE.md` §6 requirements individually verified
  against the actual current state, not assumed from memory of when
  each was originally built: input sanitization/output escaping
  (ongoing since Phase 4), nonces on forms (CF7's own handling plus
  `wp_nonce_field`/`check_admin_referer` on every custom admin form,
  e.g. the issue-contents repeater), the PDF MIME-type allowlist
  (`Meta_Fields::sanitize_pdf_id()`, Phase 3.3), `DISALLOW_FILE_EDIT`
  (above), least-privilege roles (Phase 5.1), SSL/headers (documented
  this phase), Wordfence (this phase), daily backups (documented and a
  real restore tested, this phase).
  Approved by: Farhad, in this session (2026-08-07).

- **Added:** Phase 6.3/6.4 — new `docs/DEPLOYMENT.md`, written
  specifically for Hostinger rather than generic host-agnostic
  language. Confirmed Hostinger as the actual intended host by
  searching the project directory rather than assuming — a credentials
  file and a purchase-guide video both specifically named "Hostinger"
  in `00_received`/`04_Sent`, no other provider named anywhere. Covers:
  free auto-SSL + Force HTTPS toggle in hPanel; exactly where
  `FORCE_SSL_ADMIN` belongs (the production `wp-config.php` only —
  explicitly *not* the local dev config, which has no certificate and
  would lock out local admin access with a redirect loop); security
  headers via `.htaccess` (checked live first that the front end
  currently has none of X-Frame-Options/CSP/X-Content-Type-Options/
  Referrer-Policy — WP core only adds a couple of these to
  `wp-login.php`/`wp-admin`, not site-wide) — no `wp_headers` PHP
  fallback added, since Hostinger supports `.htaccess` on all standard
  plans and covers every header on the list, matching `CLAUDE.md` §6's
  stated host-level-first priority; also caught and documented in
  passing that `X-Powered-By: PHP/8.2.29` is currently exposed (found
  via the same header check), a separate disclosure from the WP-version
  hardening already done in code.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** Backup mechanism documented (Hostinger's native daily
  backups as primary, an exact WP-CLI cron job as the documented
  fallback/supplement, per `EXECUTION_PLAN.md`'s own instruction) *and*
  a real restore proven end-to-end, not just described in theory —
  installed WP-CLI (none was available on this machine before),
  real-`mysqldump`-backed up the live local DB (~1.2MB, all real
  content), created a throwaway clearly-labeled test post
  (`wp post create`, ID 80), confirmed it existed, restored the
  database from the pre-test-post backup, and confirmed the test post
  was gone afterward — proof the restore actually reverted state, not
  just that the import command exited without an error. Also confirmed
  the site and all real seeded content (41 items across all four
  content types) survived the restore fully intact. Hit and worked
  through a real LocalWP-specific connection quirk along the way
  (this site's MySQL runs on a non-default port, `10090`, found via
  LocalWP's own `sites.json` rather than guessed) — documented clearly
  in `docs/DEPLOYMENT.md` as a local-environment detail, not a
  production concern, since Hostinger's own `wp db export`/
  `wp db import` need no such override. All backup files and WP-CLI
  temp state were kept entirely outside the git-tracked repo throughout
  and cleaned up afterward.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** `EXECUTION_PLAN.md` updated — every Phase 6 checklist
  item (§6.1–§6.4) checked off with the evidence above, not just marked
  done. Also added the `test_admin`/`test_editor`/`test_author`/
  `test_contributor` accounts (Phase 5.1) to Phase 7.1's existing
  test-content cleanup list, so they aren't forgotten before handover —
  noticed they weren't already on that list while reviewing it. Phase 6
  (security hardening, backups, deployment prep) is functionally
  complete. Next per the plan: Phase 7 — final QC, credit verification,
  and handover.
  Approved by: Farhad, in this session (2026-08-07).

- **Added:** first-ever local phpcs run against this codebase. It had
  only ever run via CI (`phpcs.xml.dist`/`.github/workflows/lint.yml`,
  WordPress-Extra); never locally, since neither `phpcs` nor a
  `vendor/` directory existed on this machine before now. Installed the
  exact same tool versions CI uses
  (`squizlabs/php_codesniffer:^3.9`, `wp-coding-standards/wpcs:^3.1`,
  `phpcompatibility/phpcompatibility-wp:^2.1`) via a global Composer
  install, so the local result matches what CI would actually report.
  First run: 57 errors + 38 warnings across 22 of 34 scanned files.
  Reported the actual composition to Farhad before touching anything,
  per his request to see the real scope first — 43 were pure
  cosmetic/alignment (auto-fixable), 8 were missing `translators:`
  comments, 1 matched-but-flagged Yoda-condition/output-escaping/naming
  issues each, and 40 were `WordPress.WP.GlobalVariablesOverride.
  Prohibited` flagging completely ordinary template variables
  (`$term`, `$paged`) that happen to share a name with a WordPress
  global — ordinary, expected WP template-hierarchy code, not a bug.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved (deliberate ruleset decision, not a silent suppression):**
  excluded `WordPress.WP.GlobalVariablesOverride.Prohibited`
  project-wide in `phpcs.xml.dist`, with an inline comment explaining
  why, per Farhad's explicit approval of that option over renaming
  ~20-30 variables across many already-closed-out templates for a
  purely cosmetic naming collision. All 40 of that sniff's findings
  were confirmed to be this exact false-positive pattern before
  excluding it, not assumed.
  Approved by: Farhad, in this session (2026-08-07).

- **Fixed:** the remaining real findings, each individually — 8 missing
  `/* translators: */` comments added (footer.php, front-page.php,
  page-library.php, page-topics.php, single-issue.php ×3,
  taxonomy-publication.php), one genuine Yoda-condition violation
  fixed (`inc/template-tags.php`), one `shola_to_persian_digits()`
  output wrapped in `esc_html()` in `taxonomy-publication.php` (the
  value is always digit-safe by construction, but matches this
  project's own "escape everything, no exceptions" rule rather than
  arguing the technicality), and two naming nits resolved (the
  `admin_footer_text` callback's required-but-unused `$text` parameter
  documented with a `phpcs:ignore` explaining why it can't be removed;
  the plugin autoloader's `$class` parameter renamed to `$class_name`,
  a trivial zero-risk rename). Ran `phpcbf` for the 43 auto-fixable
  cosmetic violations; manually cleaned up two spots afterward where
  its automatic reformatting of alternative-syntax control structures
  (`single-issue.php`'s conditional `href`, `card.php`'s term-link
  conditional) produced functionally-correct but inconsistently-indented
  output — simplified the first to a plain ternary expression instead
  of fighting the formatter, hand-fixed the second's indentation.
  Re-ran phpcs after every fix: **0 errors, 0 warnings, all 34 files
  clean.** `php -l` re-run on every touched file, and the live site
  re-verified across 9 page types (front page, all archives, search,
  a topic page) plus the specific manually-rewritten PDF-link markup —
  all `200`, zero PHP errors, real PDF URL/`download` attribute intact.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** Phase 7.1's test-content cleanup, re-confirmed live
  before deleting anything rather than trusted from the CHANGELOG log,
  per Farhad's explicit instruction. That live check found a real
  discrepancy: a **third** test taxonomy term
  ("پست آزمایشی" under `collection`, term_id 15) existed that neither
  `EXECUTION_PLAN.md` nor any prior CHANGELOG entry had ever mentioned
  — only the topic and publication test terms were documented. Found
  by listing every term across all three taxonomies directly instead
  of searching only for the terms already expected. Deleted all of it:
  3 test posts (مقاله آزمایشی/شماره ای آزمایشی/سند آزمایشی, IDs 11/10/8),
  3 test taxonomy terms (موضوع آزمایشی/نشریه ازمایشی/پست آزمایشی —
  confirmed `count: 0` on all three before deleting, none attached to
  real content), and the 4 test role accounts from Phase 5.1
  (`test_admin`/`test_editor`/`test_author`/`test_contributor`).
  Verified live afterward: site healthy across 7 page types, zero PHP
  errors, real seeded content unaffected.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** Phase 7.1's mobile/desktop/no-JS check. No-JS: homepage
  rendered via headless Chrome with JavaScript disabled — full content
  and primary navigation (site nav, search link, تماس/اطلاعیه‌ها links)
  remain visible and functional; only the hamburger-menu panel itself
  needs JS to open, an acceptable, expected degradation matching this
  project's stated "progressive enhancement, not JS-required" standard
  (`CLAUDE.md` §5). Mobile: a first headless-Chrome screenshot at
  390px appeared to show clipped/cut-off content (masthead icons,
  hero title text) — investigated properly rather than reported as a
  bug on a visual impression alone: real DOM measurements via the
  interactive browser's actual mobile emulation (375px, proper UA/touch
  emulation) showed **zero** horizontal overflow
  (`body.scrollWidth === window.innerWidth` exactly) and every
  element's `getBoundingClientRect()` fully within viewport bounds,
  hero title text fully present and un-truncated. The initial headless-
  Chrome screenshot was a misleading rendering artifact of that
  specific capture method, not a real bug — confirmed by cross-checking
  with a more reliable measurement method before reporting anything,
  the same discipline applied all session to visual findings.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** decision #1 from `EXECUTION_PLAN.md` Appendix B's open-
  decisions tracker ("جنبش بین‌المللی" dual-listing) formally logged
  here, closing a real gap: the plan's own tracker said this was
  "Confirmed in IA doc itself; formalize in CHANGELOG.md at Phase 3.2"
  — that formalization never actually happened, confirmed by searching
  this file directly rather than trusting the tracker's "Confirmed"
  status at face value. The IA doc's exact §9 text: *""Int'l movement /
  جنبش بین‌المللی" appears twice — once under Topics (articles) and
  once under Library (documents). Confirm this is intentional; they
  hold different content types."* — posed as a question to confirm,
  not a settled fact, in the source doc. Confirmed intentional and
  already correctly built: `topic` term "جنبش بین‌المللی" (term_id 7)
  holds real article content, `collection` term "جنبش بین‌المللی"
  (term_id 12) holds real document content — genuinely different
  content types under the same name, exactly as the IA doc's own
  reasoning describes, verified via real seeded content counts (both
  non-zero), not asserted from the label alone.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** decision #3 from the same tracker (issue model:
  PDF-only vs. PDF + separate web articles) — already built against
  this assumption throughout Phase 4 (`single-issue.php`,
  `EXECUTION_PLAN.md`'s own Phase 0.3 resolved-assumption note) and
  never revisited, since no client request to change it ever came up.
  Confirmed still the correct assumption; no code or content-model
  change needed.
  Approved by: Farhad, in this session (2026-08-07).

- **Deferred (explicit sign-off, not resolved unilaterally):** decision
  #2 from `EXECUTION_PLAN.md` Appendix B (bilingual pairing model —
  linked fa/en translation pairs vs. two fully independent sites).
  Brought to Farhad as an actual decision to make ("resolve now" vs.
  "explicitly defer with sign-off"), not a recommendation to rubber-
  stamp, per his explicit instruction when this item was flagged.
  Farhad confirmed directly with the client: no English content is
  planned at this time. Site stays exactly as already built —
  bilingual-*ready*, Persian-active only, no Polylang/WPML, no English
  content or routing (`CLAUDE.md` §1). The "linked pairs" model (per
  IA doc §2.2) is recorded as the working assumption for *whenever*
  English rollout is eventually scoped — not a commitment made today,
  and not something this build has validated against real English
  content, since none exists. `EXECUTION_PLAN.md` Appendix B updated
  to reflect this closure. All 3 of the IA doc's open decisions are
  now either resolved or explicitly deferred with sign-off — Phase
  7.3's checklist item for this is complete.
  Approved by: Farhad, in this session (2026-08-07).

- **Resolved:** Phase 7.2 — all six `CLAUDE.md` §7 credit placements
  individually re-verified, not assumed from memory as already done
  earlier in the project: theme `style.css`, plugin `shola-core.php`,
  `readme.txt`, and root `README.md` all read directly from their
  actual current file contents (exact `Author`/`Author URI` lines and
  Credits sections all correct); `admin_footer_text` verified live via
  `apply_filters('admin_footer_text', '')` rather than just reading
  the callback's code, confirming the actual rendered output and link.
  Five of six were already correct from earlier phases.
  Approved by: Farhad, in this session (2026-08-07).

- **Fixed:** the sixth placement, `screenshot.png`, was the one real
  gap — opened the actual file rather than trusting that its existence
  meant it was finished, and found it was still exactly the Phase 3.1
  placeholder (bare white background, "Shola Jawid — placeholder
  screenshot" text, 5.7KB). Per `CLAUDE.md` §7's own instruction not to
  guess at the credit-strip visual treatment unilaterally, presented
  three concrete options to Farhad before building anything — he chose
  a subtle bottom-right overlay. Captured a real screenshot of the
  finished front page (headless Chrome, 1200×900, the same technique
  proven throughout this session), then composited the credit strip
  via an HTML+CSS overlay (dark semi-transparent strip, brand
  `--ink`/`--paper` tokens, monospace text: "Designed & developed by
  Farhad Farhaad" / "github.com/farhadcodes") rendered through the
  same headless-Chrome screenshot technique rather than needing an
  image-editing tool. Replaced `screenshot.png` (now 918KB, real
  content). Verified live via `curl` that the new file is actually
  served at the theme's real screenshot URL, not just present on disk.

## 2026-08-08

- **Resolved:** Phase 7.5 — site-wide kicker-label sweep, closed after a
  three-stage process that changed shape twice as Farhad narrowed the
  actual requirement:
  1. **Stage 1 (plan only):** grepped every `lang="en"` occurrence
     across the theme (not relying on memory of screenshots), found 23
     `.section-marker` "kicker" instances (small mono eyebrow labels
     above a heading, e.g. "LATEST", "CURRENT ISSUE"), and drafted
     literal Persian translations for each. Surfaced a real design
     problem before touching any file: several literal translations
     (e.g. "Latest" → "تازه‌ترین") would exactly duplicate the Persian
     heading directly beside them, which is a visual regression, not a
     translation task. Findings written into `EXECUTION_PLAN.md` §7.5
     for review, per Farhad's explicit "plan first, implement second"
     instruction — no template touched at this stage.
  2. **Simplified requirement:** Farhad disregarded the
     translate-with-alternate-wording approach entirely and asked
     instead for the English kicker word to be **removed outright**,
     leaving only the decorative dash. Confirmed via `main.css` that
     the dash is a `.section-marker::before` pseudo-element attached to
     the label element itself, not separate markup — so emptying the
     `<p class="section-marker">` of text (and dropping its now-moot
     `lang="en"`) removes the English word while the dash keeps
     rendering unchanged. Applied to the homepage "Latest" instance
     first per Farhad's request, screenshotted live, and confirmed
     before any further rollout.
  3. **Layout correction:** from a screenshot Farhad circled, the dash
     needed to sit inline before the Persian heading (same line,
     rightmost — i.e. read first — in RTL order) rather than stacked
     on its own line above it, which is how `.section-marker` and its
     sibling heading rendered by default (each a separate block).
     Added a new `.kicker-row` utility class (`main.css`, `assets/css`)
     — `display: inline-flex; align-items: center; gap: .75rem;`,
     wrapping the marker `<p>` and its heading (`h1`/`h2`) in one flex
     row — scoped narrowly so it wouldn't affect any kicker not yet
     rolled out. Applied to the homepage "Latest" instance, verified
     live (screenshot + `getBoundingClientRect()` check confirming the
     dash sits to the right of, i.e. before, the heading text), and
     confirmed by Farhad against his circled reference before full
     rollout.
  Approved by: Farhad, in this session (2026-08-08), at each of the
  three stages above.

- **Applied site-wide** once the `.kicker-row` pattern was confirmed:
  all 23 `.section-marker` instances updated — text and `lang="en"`
  removed, each wrapped in `.kicker-row` alongside its heading. Files
  touched: `404.php`, `page-about.php`, `page-contact.php`,
  `page-library.php` (×2), `page-publications.php`, `page-topics.php`,
  `search.php`, `archive-announcement.php`, `taxonomy-collection.php`,
  `taxonomy-topic.php`, `taxonomy-publication.php` (×3),
  `single-document.php` (×2), `single.php`, `single-issue.php`,
  `front-page.php` (×6). `header.php`'s four `menu-section-title`
  spans (e.g. "Topics · موضوعات") were confirmed out of scope — they
  are already bilingual pairs, not English-only labels, and Farhad
  explicitly excluded them when approving the site-wide rollout.
  Meta-mono field labels found during the original Stage 1 grep
  (`EMAIL`, `RESPONSE TIME`, `PRIVACY` on `page-contact.php`; `TAGS` on
  `single.php`; the per-entry `SECTION · …` TOC label on
  `single-issue.php`) were left untouched: these are value labels, not
  headline eyebrows, have no adjacent dash/heading pair, and blanking
  them would delete real information rather than fix a layout/
  translation issue — a structurally different case from the "kicker"
  pattern this sweep targeted, so left for a separate decision if
  Farhad wants them addressed.

- **Exception, flagged rather than silently resolved:**
  `taxonomy-publication.php`'s "Archive" kicker included a dynamic
  issue count (e.g. "Archive · 5 Issues") that appears nowhere else on
  the page — unlike the other two `.section-marker` instances on that
  same template (`Publication · Archived` and `Current`), whose
  information is already duplicated elsewhere (the `badge-current`/
  `badge-archive` status pill, and the "· جاری" suffix already present
  in the current-issue heading, respectively) so blanking those two
  loses nothing. Deleting the count outright would have been real
  information loss, not a cosmetic fix, so instead of blanking it the
  count was kept and only the English word translated:
  "Archive · 5 Issues" → "۵ شماره" (kept as marker text, inside the
  same `.kicker-row`, dash + count + heading all inline). Noted here
  explicitly since it is the one instance in this sweep that isn't a
  pure removal.

- **Verified:** phpcs clean (0 errors, 0 warnings, 26 files) after the
  full sweep. Every affected template checked live — front page (all
  six kickers), `page-library.php`, `page-topics.php`,
  `page-publications.php`, `search.php` (with a live query), `404.php`,
  `archive-announcement.php`, `page-about.php`, `page-contact.php`,
  `taxonomy-topic.php`, `taxonomy-collection.php`,
  `taxonomy-publication.php` (all three kickers, including the
  count-preserving exception), `single.php`, `single-issue.php`
  (including the untouched per-entry TOC labels), `single-document.php`
  — via a mix of headless-Chrome screenshots and, for the exact
  dash-before-heading ordering, `getBoundingClientRect()` checks
  confirming the marker's bounding box sits to the right of (i.e.
  before, in RTL reading order) every paired heading's bounding box, on
  every page checked. No leftover English text found in any
  `.section-marker` element anywhere in the theme (grepped after the
  fact to confirm, not just checked the files touched).
  Approved by: Farhad, in this session (2026-08-08).

## 2026-08-08 (continued)

- **Fixed:** two masthead search-icon bugs, reported by Farhad with
  side-by-side screenshots against the v6 prototype (`header.php:45-47`,
  the `<a class="link-quiet mast-icon-link">` wrapping the search SVG).
  1. **Color regression:** the icon rendered dark/black instead of the
     white used by every other masthead element. Root cause: `a { color:
     inherit }` (`main.css` §01) — nothing in the icon link's ancestor
     chain (`.masthead` → `.masthead-left` → the `<a>` itself) sets an
     explicit `color`, so it fell through to the page's default ink
     color instead of the masthead's white scheme. The SVG uses
     `stroke="currentColor"`, so it inherited the same wrong color.
  2. **Hover treatment:** `.link-quiet:hover` (the icon's other class)
     sets `color: var(--crimson)` — crimson text on the crimson masthead
     background, effectively invisible on hover. Farhad asked for the
     same hover behavior as the neighboring "شعله جاوید"/"جهان برای فتح"
     links (`.mast-sister a`) — a color brightening — but explicitly
     without the underline those links get (`.mast-sister a`'s
     `border-bottom`).
  Fix: added `.masthead .mast-icon-link` / `.masthead .mast-icon-link:
  hover` rules (`assets/css/main.css`, §05 Masthead) using the exact
  same color values as `.mast-sister a` (`color-mix(in oklab, var(--paper)
  85%, transparent)` default → `var(--paper)` on hover), without adopting
  `.mast-sister a`'s `border-bottom` — so the icon brightens on hover with
  no underline. Used a `.masthead` prefix for specificity so this
  reliably wins over `.link-quiet:hover` regardless of stylesheet order.
  Verified live: default computed color matches `.mast-sister a`'s
  computed color exactly (confirmed via `getComputedStyle` — both
  `oklab(... / 0.85)`); hover state verified with a real synthesized
  pointer event via the Chrome DevTools Protocol (`Input.dispatchMouseEvent`
  — genuine `:hover` cannot be forced through a dispatched `mouseover`
  event, and this session's browser tooling required per-action approval
  not available for the local dev host), confirming the computed color
  transitions to solid white (`rgb(255, 255, 255)`) with
  `textDecorationLine: none` and `borderBottomStyle: none` throughout.
  Screenshots taken of both states. No PHP touched, CSS-only fix.
  Approved by: Farhad, in this session (2026-08-08).

- **Fixed:** same color-regression bug found in one more masthead
  element by Farhad — the "/" separator between the menu button and the
  search icon (`.mast-slash`, `header.php:44`) rendered black instead of
  white. Different mechanism than the search-icon bug (not the `a {
  color: inherit }` gap, since this is a plain `<span>`, not an anchor)
  but the same underlying category: `.mast-slash` (`main.css` §05,
  originally `opacity: .5; margin-inline: .35rem;` only) never had an
  explicit `color` at all, and neither did any of its ancestors
  (`.masthead-left`, `.masthead-inner`, `.masthead`), so it fell through
  to the page's default ink color. The file's own conversion-era comment
  (§05, "replaces inline style="" attributes that were on _header.html's
  menu/search separator, search icon link...") confirms this was the
  same incomplete Phase 4.1 port as the search-icon bug, just not caught
  at the time. Fixed by adding `color: var(--paper);` to `.mast-slash`,
  keeping its existing `opacity: .5`.
  Audited every other "/" separator in the masthead/nav for the same gap
  before calling this done, per Farhad's request to fix it everywhere at
  once rather than instance-by-instance: `.mast-slash-light` (the two
  separators in `.masthead-right`, between "اطلاعیه‌ها"/"تماس"/"EN")
  already had an explicit `color: var(--paper)` — not buggy.
  `.mast-sister .sep` (the "/" between publication names, e.g. "شعله
  جاوید / جهان برای فتح") has no color of its own but correctly inherits
  from `.mast-sister`'s own explicit `color-mix(...)` rule — not buggy,
  a plain `<span>` inherits color normally (only `<a>` needed the
  explicit `color: inherit` reset). `.mast-slash` was the only actual
  instance of this bug.
  Verified live: `getComputedStyle` on `.mast-slash` now returns `rgb(255,
  255, 255)` (at the existing 0.5 opacity); confirmed visually via a
  3x-scaled headless-Chrome screenshot of the masthead.
  Approved by: Farhad, in this session (2026-08-08).

## 2026-08-08 (continued)

- **Fixed:** real architectural gap found and confirmed by Farhad through
  his own testing — the popup menu's "Topics · موضوعات" column (and, by
  the same mechanism, "Publications · نشرات") was not backed by a real,
  editor-manageable WordPress menu. Confirmed the exact mechanism:
  `shola_get_topic_slugs_ordered()` / `shola_get_publication_slugs_
  ordered()` (`inc/template-tags.php`) returned a hardcoded 6/2-slug PHP
  array — a deliberate Phase 3.2/4.1 decision at the time (documented in
  the original `inc/setup.php` comment: "Topics and Publications are
  generated from the taxonomy terms directly... not editor-managed
  menus"), but one that directly violates the IA doc's "editable by staff
  without a developer" requirement, exactly as Farhad's test showed
  (deleting a hardcoded term made `get_term_by()` fail silently and drop
  the item; adding a new term did nothing, since it was never in the
  fixed array to begin with). Investigated and confirmed the *adjacent*
  "Sections · بخش‌ها" and "More · بیشتر" popup columns were, by contrast,
  already real `wp_nav_menu()` locations (`menu_sections`/`menu_more`)
  with a `fallback_cb` for the empty-menu case — Appearance → Menus
  looking completely empty was because *no* menu had ever been created
  for *any* of the (then two, now four) registered locations, not because
  those two locations were fake.

  **Plan reported and approved by Farhad before implementation** (his
  explicit instruction, consistent with how every other architectural
  decision this session was handled): fix centrally rather than touching
  all 8 call sites (`header.php` popup ×2, `front-page.php`, `footer.php`,
  `page-topics.php`, `page-publications.php`, `taxonomy-topic.php`,
  the masthead sister-links) individually — reimplement the two ordering
  functions themselves to read a real menu's item order, with the old
  hardcoded array kept only as a last-resort fallback. Farhad's one
  change to the plan: seed real, pre-populated starter menus (not a
  silent fallback-only approach) so Appearance → Menus honestly reflects
  what's driving the site — reasoning: the confusion he'd just hit
  (menus looking empty while the site still worked) shouldn't just move
  from "4 of 4 empty" to "2 of 4 empty." Also applied the same real-menu
  treatment to `menu_sections`/`menu_more` for consistency across all
  four locations, per his invitation to make that call — flagging it
  here as the deliberate choice it was, not an oversight.

  **Implementation:**
  1. `inc/setup.php`: registered two more locations, `menu_topics`
     ("منو — موضوعات") and `menu_publications` ("منو — نشریات"). Both
     taxonomies already have `public => true` with no `show_ui`/
     `show_in_nav_menus` override (`class-taxonomies.php`), so WordPress
     automatically shows a taxonomy-term picker for them in the
     Appearance → Menus editor — no plugin change needed.
  2. `inc/setup.php`: added `shola_maybe_seed_nav_menus()`, hooked to
     `admin_init`, guarded by a persisted `shola_seeded_nav_menus` option
     so each of the four locations is seeded exactly once — not re-run
     every admin page load, and not re-created if an editor later
     deliberately empties/unassigns a menu. Creates and assigns a real
     menu per location, pre-populated with today's v6 default content
     (the same 6 topics / 2 publications / 4+4 curated links already
     live), via `wp_create_nav_menu()` + `wp_update_nav_menu_item()`. If
     creation fails for a location, the flag is left unset so it's
     retried next time rather than silently marked done.
  3. `inc/template-tags.php`: `shola_get_topic_slugs_ordered()` /
     `shola_get_publication_slugs_ordered()` now call a shared helper,
     `shola_get_ordered_term_slugs_from_menu()`, which resolves the
     assigned menu at the location, walks `wp_get_nav_menu_items()`
     (already returned in menu order), and extracts the slug of each
     taxonomy-type item matching that taxonomy. Falls back to the
     original hardcoded array only if no menu is assigned. No template
     changes needed anywhere — all 8 call sites keep working unmodified,
     since they only ever consumed whatever these two functions return.

  **Verified**, since wp-admin login credentials aren't available in this
  session (same constraint noted earlier in this file for the issue-TOC
  repeater work) — used LocalWP's actual MySQL instance directly instead
  of the browser admin. Found the site's real DB port (10090, from
  `~/AppData/Roaming/Local/sites.json`, since `DB_HOST` in `wp-config.php`
  is just `localhost` and only resolves correctly under LocalWP's own
  PHP-FPM); wrote a CLI bootstrap that pre-defines `DB_HOST` before
  `wp-load.php` runs (constants don't get overwritten once set, so this
  reliably wins) to get a real, full WordPress bootstrap from ordinary
  PHP CLI. This runs the actual theme code, not a reimplementation —
  genuine verification, not a mock.
  - Ran `shola_maybe_seed_nav_menus()` directly: all four menus created
    correctly, correct items, correct order, confirmed idempotent (a
    second run makes no changes, same menu IDs).
  - **Editability, tested for real** (add/remove/reorder), matching
    Farhad's explicit ask to confirm this rather than assume it: created
    a new topic term and added it to the menu the same way the taxonomy
    picker in Appearance → Menus would — appeared correctly in
    `shola_get_topic_slugs_ordered()`'s output. Removed a seeded item —
    disappeared correctly. Reordered an item to the end — moved
    correctly. (First reorder attempt failed by only patching
    `menu-item-position` without resending the item's type/object/
    object-id, which `wp_update_nav_menu_item()` requires on every call —
    a bug in the test script, not the theme code; wp-admin's real "Save
    Menu" always resubmits full item data, so a real editor dragging a
    row would never hit this. Fixed the test, reran, confirmed correct.)
    All test data cleaned up afterward, seeded menus restored to their
    exact original state.
  - All 8 call sites checked live against the real seeded data: popup
    menu (screenshotted, both columns), `page-topics.php`,
    `page-publications.php`, `taxonomy-topic.php` (sister-links),
    homepage topics table, footer — all correct.
  - phpcs clean on both changed files (`inc/setup.php`,
    `inc/template-tags.php`).

  **Found during verification, not touched:** a pre-existing topic term,
  "سلامت و روان" (`health`, term_id 26, 0 posts) — almost certainly the
  exact term Farhad created himself while confirming the original bug
  ("adding a new one does NOT appear"). Left as-is since it's his data,
  not a test artifact of this fix; it won't appear in the popup nav until
  someone deliberately adds it to the "موضوعات" menu at Appearance →
  Menus, which is now possible for the first time.
  Approved by: Farhad, in this session (2026-08-08).

- **Changed:** `.menu-topic` font-size reduced ~30%, from
  `clamp(2.4rem, 6vw, 3.5rem)` to `clamp(1.7rem, 4.2vw, 2.45rem)`
  (confirmed via computed style: 56px → 39.2px at 1280px viewport, an
  exact 30% reduction). Reason: the popup Topics list was designed around
  a fixed 6 items; now that it's a real, growable menu (per the fix
  above), the large size would look unbalanced/oversized as the content
  team adds more entries. Applied only after the real-menu fix was
  implemented and verified, per Farhad's explicit sequencing instruction.
  Verified live via a headless-Chrome screenshot of the open popup menu.
  Approved by: Farhad, in this session (2026-08-08).

- **Fixed:** the Topics/Publications taxonomy panels didn't appear at all
  in Appearance → Menus' "Add menu items" sidebar, confirmed by Farhad's
  live testing (screenshot: only Pages, Post types, Custom links, and
  دسته‌ها visible) and consistent with his other finding in the same
  report — adding "سلامت و روان" as a new topic still didn't show up in
  the live popup menu even after the real-menu fix above, since with no
  taxonomy panel there was no way to add it to the menu in the first
  place. Re-verified the earlier "already `public => true`, no `show_ui`/
  `show_in_nav_menus` override" assumption directly against the live
  site's database rather than trusting the registration code alone —
  confirmed it was in fact correct: `get_taxonomy('topic')` and
  `get_taxonomy('publication')` both report `show_in_nav_menus: true`,
  and `get_taxonomies( array( 'show_in_nav_menus' => true ) )` correctly
  lists both. No filter on `register_taxonomy_args` was suppressing
  anything either. So the taxonomy registration itself was never the
  problem — the assumption held, but something else downstream of it
  didn't.
  Found the real cause querying `wp_usermeta` directly: Farhad's own
  admin account (`SJ_manager`, user ID 1) already had a saved
  `metaboxhidden_nav-menus` Screen Options preference that explicitly
  listed `add-topic`, `add-publication`, *and* `add-collection` as
  hidden — a standard WordPress admin-UI mechanism (every "Add menu
  items" panel can be individually hidden per-user via the screen's
  "Screen Options" tab), unrelated to taxonomy registration. Read WP
  core's actual `get_hidden_meta_boxes()` (`wp-admin/includes/screen.php`)
  to find the correct fix: `default_hidden_meta_boxes` only applies when
  a user has *no* saved preference yet for that screen — since his
  account already had one, that filter would never reach him. Used
  `hidden_meta_boxes` instead (fires unconditionally, regardless of any
  saved per-user state), added as `shola_always_show_taxonomy_nav_menu_
  panels()` in `inc/setup.php`, scoped to the `nav-menus` screen only,
  stripping just `add-topic`/`add-publication`/`add-collection` from
  whatever hidden list comes through — every other hidden item (his
  existing post-type/tag/format panel preferences) is left untouched.
  `collection` had the identical gap and is fixed by the same filter,
  per Farhad's ask to check for it.
  Trade-off flagged, not silently decided: this makes the three panels
  permanently un-hideable via Screen Options, for anyone. Judged correct
  given these taxonomies are core to the "editable without a developer"
  requirement the whole fix exists for — but it's a real, deliberate
  choice worth knowing about, not an incidental side effect.
  **Verified against Farhad's exact real saved data**, not just in the
  abstract: pulled his actual `metaboxhidden_nav-menus` row from the
  database, ran it through the new filter function directly, confirmed
  all three of his hidden panels are removed from the result while his
  five other hidden items (post-type/tag/format panels) pass through
  unchanged, and confirmed a different admin screen (`post`) is
  completely unaffected by the filter.
  **Full end-to-end test performed for real**, exactly as asked, not
  simulated: added the "سلامت و روان" topic term (the one Farhad had
  created himself while testing the original bug) to the real `موضوعات`
  menu via `wp_update_nav_menu_item()` — the same underlying WordPress
  call the now-visible panel's "Add to Menu" button makes — then loaded
  the live popup menu and confirmed it now renders as the 7th topic,
  and separately confirmed it also flows through correctly to
  `page-topics.php`'s topic-count table. **This was left live** (not
  reverted, unlike the earlier throwaway QA test) since Farhad's
  instruction explicitly named this exact term/menu/outcome as the test
  to perform — flagging clearly here rather than leaving it as a silent
  side effect: "سلامت و روان" is now a real, publicly visible 7th topic
  on the site. Remove/reorder it via Appearance → Menus (now genuinely
  possible) if that wasn't the intended outcome.
  phpcs clean on `inc/setup.php`.
  Approved by: Farhad, in this session (2026-08-08).

## 2026-08-08 — Phase A: Afghan Dari Jalali month names (site-wide correctness fix)

- **Fixed:** Farhad's full manual walkthrough of the live site and
  wp-admin (first amendment round after Phase 7 close-out) surfaced a
  site-wide correctness bug: every Jalali date rendered the Iranian Solar
  Hijri month names (فروردین, اردیبهشت, خرداد, ...) via the Persian
  Calendar plugin, when this site needs the Afghan Dari names (حمل,
  ثور, جوزا, ...) instead, matching the project's `fa_AF` locale
  identity (the same reasoning that ruled out ParsiDate back in Phase
  5.5 — see that entry for the full history).
  **Investigated before implementing, per Farhad's request:** read
  Persian Calendar's actual source rather than assuming. Confirmed it
  has **no** built-in Afghan/Dari variant — three independent checks,
  all negative: (1) the 12 Iranian names live in a hardcoded,
  non-`apply_filters()`-wrapped `private $months_fa` property on its
  date-converter class; (2) its complete settings list
  (`class-persca-admin.php::get_default_settings()`) has no locale/
  dialect/variant field; (3) its only locale-related gate anywhere in
  the plugin is `is_rtl()` — it never calls `get_locale()`, so it can't
  distinguish `fa_AF` from `fa_IR` (consistent with why it was chosen
  over ParsiDate in the first place). Also confirmed its actual
  rendering mechanism: it hooks six WordPress-core filters —
  `date_i18n`, `wp_date`, `get_comment_date`, `get_comment_time`,
  `get_the_modified_date`, `get_the_modified_time` — all at priority 10,
  doing its own internal Gregorian→Jalali conversion and building the
  final string itself (it does not touch `$wp_locale->month`), so those
  six filters are the only stable interception points available without
  forking the plugin.
  **Approach (approved by Farhad before implementation):** added
  `shola_convert_jalali_months_to_dari()` (`inc/template-tags.php`),
  registered on the same six filters at **priority 20** (after the
  plugin's conversion), doing a single-pass `strtr()` swap of all 12
  Iranian month names to their Dari equivalents on whatever string comes
  back. Deliberately anchored to the plugin's *public* contract — that
  it filters these six hooks and outputs literal Persian month-name
  strings — rather than any private property/method, since changing
  that public behavior would break Persian Calendar for its whole
  non-Afghan user base too, not just this site. This is exactly what
  makes the fix survive plugin updates instead of reverting with them,
  per Farhad's explicit requirement (and exactly the risk category the
  ParsiDate/regional_settings incidents in Phase 5.5 already burned this
  project on once).
  **Surface area, found by grep and independently re-verified:** despite
  sounding like it could touch dozens of files, only 7 call sites across
  5 files actually render a Jalali month name — `archive-announcement.php`,
  `front-page.php` (×3: hero byline, current-issue date, announcements
  list), `inc/template-tags.php`'s `shola_get_masthead_runner()` (site-
  wide, every page header), `template-parts/search/result.php`, and
  `template-parts/cards/card.php` (shared by the homepage grid,
  `taxonomy-topic.php`, and `single.php`'s related-essays rail). Every
  other date-rendering spot in the theme (`single-issue.php`,
  `single-document.php`, `taxonomy-publication.php`,
  `issue-card.php`, and the `shola_get_english_month_abbr()`/
  `shola_get_gregorian_year()` helpers) was already hardened back on
  2026-08-06 to bypass Jalali conversion entirely (Gregorian mono-label
  dates) — confirmed still out of scope, not re-touched.
  **Verified, not assumed:** direct PHP-level unit check of the filter
  function against all 12 month pairs (all correct); confirmed via the
  real site database that Persian Calendar's callback is registered at
  priority 10 and this theme's at priority 20 on `date_i18n` (correct
  run order); a real `get_the_date()` call against a live post returned
  the Dari month. Live-checked all 7 call sites, including — per
  Farhad's specific ask — `card.php` on **three different consuming
  pages** (homepage grid, `taxonomy-topic.php`'s topic archive, and
  `single.php`'s related-essays rail), not just wherever it was first
  tested, confirming both "اسد" (مرداد) and "سرطان" (تیر) render
  correctly across different months, not just one repeated case. Ran a
  site-wide sweep (fetching `/`, `/announcements/`, a topic archive, a
  search results page, and a publication archive, checking each page's
  raw HTML for all 12 Iranian month names as whole words) and confirmed
  zero leftover instances anywhere sampled. phpcs clean.
  Approved by: Farhad, in this session (2026-08-08).

## 2026-08-08 — Phase B: genuine bugs from Farhad's manual walkthrough

- **Fixed (B1):** Contact Form 7's validation/error messages
  ("Please fill out this field.", "One or more fields have an error...")
  rendered in English on `/contact/`, found by Farhad. Investigated
  rather than assumed: confirmed the site's CF7 *translation files
  are correctly installed and load correctly* — `wp-content/languages/
  plugins/contact-form-7-fa_AF.mo` exists, `get_locale()`/
  `determine_locale()` both correctly resolve to `fa_AF`, and a direct
  `__( 'Please fill out this field.', 'contact-form-7' )` call returns
  the correct Persian string. The bug wasn't a translation-loading
  problem at all: `WPCF7_ContactForm::message( $status )` reads
  directly from the *form's own stored* `messages` property
  (`$this->prop('messages')[$status]`) with no fallback to the
  plugin's live, translatable `wpcf7_messages()` defaults — and the
  live contact form (post ID 71, "ارتباط با حزب") had all 23 of its
  message strings stored as literal English text, baked in at
  form-creation time and never updated since. Confirmed via the same
  REST endpoint the live form actually calls
  (`POST /wp-json/contact-form-7/v1/contact-forms/71/feedback` with
  empty fields) both before (English) and after (Persian) the fix.
  **Fix:** updated the form's stored `messages` property to the
  plugin's own correct Persian defaults (read live from
  `wpcf7_messages()`, all 23 keys — not just the 2 Farhad happened to
  notice, since all 23 were English) via `WPCF7_ContactForm::
  set_properties()` + `save()`, CF7's own supported save API — not raw
  SQL. This is form *content*, stored the same way the CF7 admin
  "Messages" tab stores an editor's own overrides — completely
  unaffected by future plugin code updates, satisfying "must not break
  on plugin updates" even more directly than a code-level fix would,
  since it doesn't depend on any plugin hook/internal at all.
  **Related finding, investigated and confirmed harmless, no fix
  needed:** CF7's own `wpcf7_is_rtl()` (`includes/l10n.php`) has a
  hardcoded RTL-locale list containing `fa_IR` but not `fa_AF` — the
  same locale-completeness gap already seen twice this project
  (ParsiDate, Persian Calendar) — causing the `.wpcf7` wrapper's `dir`
  attribute to render `ltr`. Checked its actual effect via
  `getComputedStyle()`: the theme's own CSS already sets an explicit
  `direction: rtl` on form elements, which wins over the HTML `dir`
  attribute, so this has zero real visual/functional impact on this
  site — confirmed empirically, not assumed, so left alone rather than
  "fixing" something that isn't actually broken.
  Noted, not touched: a second, unused CF7 form exists (post ID 70,
  "Contact form 1" — CF7's default sample form from plugin activation,
  never referenced by any template; `page-contact.php` only renders
  form 71). Left as-is since it's never displayed; flagged here in
  case Farhad wants it deleted as housekeeping.
  Verified live in-browser (real form submission, not just the REST
  call) — both the response-output summary and the per-field
  `wpcf7-not-valid-tip` inline messages render correct Persian.
  Approved by: Farhad, in this session (2026-08-08).

- **Fixed (B3):** mobile footer text (`.footer-tagline`, `.footer-col a`,
  `.footer-col h3`) read too small on mobile, found by Farhad. These
  used the same fixed sizes (13-14px) at every viewport — no CSS bug
  causing an actual desktop/mobile size *difference*, just the same
  absolute size reading too small against normal mobile-UX readability
  norms (~16px floor for link/body text) on an actual phone screen.
  Added a `@media (max-width: 720px)` override in `main.css` §19
  raising `.footer-tagline`/`.footer-col a` to `var(--t-body)` (17px)
  and `.footer-col h3` to `var(--t-small)` (14px, kept smaller than
  body text since it's an uppercase category label, not primary
  reading content) — reusing existing design tokens rather than
  inventing new arbitrary values. Desktop rules above the media query
  are untouched.
  Verified via computed style at both viewports: mobile (375px) now
  17px/17px/14px; desktop (1280px) unchanged at 14px/14px/13px.
  Confirmed visually via a mobile screenshot.
  Approved by: Farhad, in this session (2026-08-08).

- **Fixed (B4):** homepage mobile hero — the featured image filled the
  screen and the headline required a scroll to see, found by Farhad.
  Diagnosed as the well-documented mobile-browser "`100vh` doesn't
  account for the collapsing address bar" issue: `.hero-media` used
  `height: calc(100vh - 128px)` (`main.css` §10) with the title
  (`.hero-lead > .wrap`) absolutely bottom-anchored inside it — `100vh`
  on mobile Safari/Chrome resolves to the *largest possible* viewport
  (as if the address bar were already hidden), which is taller than
  what's actually visible at page load (address bar still showing), so
  the bottom-anchored title lands below the actually-visible area until
  the user scrolls (which is what triggers the toolbar to collapse).
  Fixed with `100dvh` (dynamic viewport height — tracks the *current*
  visible area, not the nominal maximum) as a cascade-layered addition
  after the existing `100vh` line, so older browsers without `dvh`
  support keep the untouched `vh` fallback and nothing regresses; kept
  the same treatment for the short-landscape media query variant.
  Desktop is unaffected (`dvh`≈`vh` there — no toolbar-collapse
  behavior on desktop browsers).
  **Verification limitation, noted rather than glossed over:** the
  actual bug (toolbar eating extra space at load) can't be reproduced
  in this session's headless/emulated mobile testing environment — no
  real collapsing address bar to simulate — so I could not screenshot
  the literal "before" broken state or literally prove the "after" fix
  visually. Confirmed instead: `CSS.supports('height', '100dvh')` is
  true and the property is live in computed style (cascade applying
  correctly, no parse error), the page renders with no regression at
  a standard mobile viewport, and `dvh` is exactly the standard,
  widely-documented fix for this exact category of bug. Recommend
  Farhad re-confirm on an actual phone during his walkthrough.
  Approved by: Farhad, in this session (2026-08-08).

- **Fixed (B5):** article excerpt/dek text had no length ceiling, found
  by Farhad — an editor-set `post_excerpt` has no built-in length limit
  in WordPress, so a very long one could overflow its container.
  Two layers, since the existing `wp_trim_words()` calls at most call
  sites (word-count trimming) don't by themselves guarantee no
  layout overflow — a container narrower than expected, or the excerpt
  supplied through a path with no word-count guard at all, could still
  break:
  1. Found three call sites with no truncation whatsoever — raw
     `get_the_excerpt()` in `single.php:56`, `single-issue.php:110`,
     `single-document.php:86` — added `wp_trim_words( ..., 34 )` to
     all three, matching the word-count pattern already used
     everywhere else in the theme.
  2. Added a CSS line-clamp safety net (`main.css`) to `.dek`,
     `.card-dek`, and `.article-dek` — `display: -webkit-box;
     -webkit-box-orient: vertical; -webkit-line-clamp: N; overflow:
     hidden;` (3 lines for the tighter card-grid context, 4 elsewhere)
     — a hard ceiling independent of whatever text reaches the DOM,
     which is the actual "can never break the layout regardless of
     length" guarantee. `.dek` is shared by several *static*,
     developer-authored descriptions (page headers, 404, newsletter)
     as well as dynamic excerpts — harmless to those, since a clamp is
     a ceiling that only engages once content exceeds it; short fixed
     copy renders identically to before.
  **Verified with a real stress test, not just short sample text**:
  temporarily set a live post's excerpt to an intentionally extreme
  1,760-character block, screenshotted it rendering correctly (clamped,
  no overflow) in three different contexts at once — the homepage hero
  dek, and the same post's own `.article-dek` on its single-article
  page — then reverted the post's excerpt back to its original text
  immediately after.
  phpcs clean on all three changed PHP files.
  Approved by: Farhad, in this session (2026-08-08).

- **Fixed (B6):** clicking a tag at the bottom of an article
  (`/tag/{slug}/`) led to a broken-looking layout — blank space on the
  left, content pushed right — found by Farhad. Investigated the
  suspected cause first (per his instruction) rather than assuming: the
  theme has no `tag.php`/`archive.php`/`category.php`, so tag archives
  fall through the WP template hierarchy to the theme's `index.php` —
  but that file already correctly calls `get_header()`/`get_footer()`
  and wraps content in the standard `.wrap sect` container, so it's
  *not* a bare/unstyled WP default missing the site's RTL treatment,
  contrary to the natural first guess.
  Found the real cause by inspecting computed styles directly:
  `<body>`'s own computed `width` was constrained to ~1127px (of a
  1280px viewport) with `display: inline-block` — matching exactly
  what an `inline-block` `<body>` does in RTL (shrinks to content
  width, right-aligns, leaving blank space on the left). Traced this
  to `main.css`: WordPress's `body_class()` outputs a literal `tag`
  class on every tag-archive page, and the theme separately had its
  own `.tag { display: inline-block; ... }` rule (a "tag pill"
  component, `main.css` ~line 665) — an accidental selector collision
  between a generic single-word theme class and one of WP core's
  reserved-ish auto-generated body classes, applying `inline-block` to
  the entire `<body>` on every tag archive.
  Confirmed `.tag`/`.tag:hover` were themselves orphaned/unused in
  current markup before touching anything — grepped every template and
  found actual tag links use `.tag-outline`/`.tag-list` (`single.php`),
  not bare `.tag`; the old rule was leftover from an earlier rename
  that never got cleaned up. Deleted both orphaned rules entirely
  rather than just scoping them, since nothing in the theme depends on
  them — removes the collision at its root with zero risk to any
  current styling.
  **Not done, flagged as a separate, optional follow-up**: the tag
  archive still renders via `index.php`'s bare fallback (post title +
  full content, no card grid, no archive polish) now that the layout
  bug itself is fixed — a plain page, not a broken one. Building a
  dedicated `tag.php` matching `taxonomy-topic.php`'s card-grid
  treatment would be a real quality improvement but is a feature
  addition, not the reported bug; left for Farhad to decide whether
  it's worth doing as its own item.
  Verified live: `/tag/بازار-غیررسمی/` renders full-width, masthead and
  footer correctly flush edge-to-edge, matching every other page.
  Approved by: Farhad, in this session (2026-08-08).

- **Removed (B7):** drop-cap styling, site-wide — Farhad's call that it
  works for Latin typography but breaks/obscures the first word in
  Persian (Arabic-script letters take different joined/initial/medial/
  final forms depending on position in a word; isolating and enlarging
  just the first character via CSS `::first-letter` grabs the wrong
  glyph shape and visually detaches it from the rest of the word).
  Grepped the whole theme rather than assuming it was only on
  `single-document.php`: the mechanism was a single shared rule,
  `.prose > p:first-child::first-letter` (+ its `[dir="ltr"]`
  mirror-flip variant) in `main.css`, applying to every template using
  the shared `.prose` wrapper — three consuming templates, not one:
  `single-document.php` ("About the Text", the one Farhad noticed),
  `single.php` (main article body), and `page-about.php` (درباره
  tabs content). Deleted both rules entirely; `.prose`'s normal
  paragraph styling (font size, line-height, spacing) is untouched.
  Note for context, not a code change: `docs/screenshots/
  phase1-refactor-notes.md` documents this drop-cap's `float` as "the
  only directional CSS in the stylesheet" from the original Phase 1
  RTL audit — that note is now stale/historical since the feature no
  longer exists; left the historical doc as-is (a snapshot of that
  phase, not a live reference) rather than editing old phase notes,
  per this file's own established practice.
  Verified live on all three consuming pages — `page-about.php`,
  `single.php`, and `single-document.php` — first word of the first
  paragraph in each now renders as normal, undistorted Persian prose.
  Approved by: Farhad, in this session (2026-08-08).

- **Closed (B2):** the non-functional ذخیره/اشتراک buttons on
  `single.php`. Farhad's decisions on the reported plan:
  - **ذخیره (save) — removed entirely, not built.** A real save/
    bookmark feature needs an account system to be meaningful (sync
    across devices, survive a browser data clear); this site has no
    reader-account system by design (WP login is editorial-staff
    only). Rather than ship a fragile localStorage-only approximation,
    the button was deleted from `single.php` outright — no replacement
    markup. Verified live that the remaining `.row` (now a single
    child) has no leftover gap/misalignment where the second button
    used to sit.
  - **اشتراک (share) — built for real**, with explicit platform icons
    rather than relying only on the Web Share API (better desktop
    coverage, matches the site's existing icon-based social-link
    pattern): Facebook, Telegram, WhatsApp, X, and Copy Link, in a
    dropdown triggered by the اشتراک button. Each platform link is a
    real share-intent URL (`facebook.com/sharer/sharer.php`,
    `t.me/share/url`, `api.whatsapp.com/send`, `twitter.com/intent/
    tweet`) built server-side from `get_permalink()`/`get_the_title()`,
    `rawurlencode()`'d per parameter and `esc_url()`'d on output.
    Copy Link uses the async Clipboard API with a
    `document.execCommand('copy')` fallback (added after live testing
    surfaced that `navigator.clipboard` is unavailable on this local
    dev site specifically — it requires a secure context/HTTPS, which
    this HTTP-only LocalWP environment doesn't have; production will
    be HTTPS per the Phase 6 SSL commitment, but the fallback makes it
    robust regardless), showing "لینک کپی شد" for 2 seconds before
    reverting.
    Icon glyphs reuse the same 24×24 `currentColor` SVG-path style and
    hover-to-crimson treatment as the existing `.footer-social`/
    `.menu-social` icons for visual consistency (Telegram/X paths
    literally reused from `header.php`'s existing icons; Facebook/
    WhatsApp/copy-link are new simplified glyphs in the same minimal
    single-path style the codebase already uses for Telegram/X/RSS —
    not official brand SVGs, matching the codebase's existing
    approach).
    **Progressive enhancement, not overlooked**: the dropdown does
    *not* rely on the HTML `hidden` attribute (which would make it
    permanently unreachable with JS disabled) — it renders as a plain,
    always-visible list of links by default, and is only collapsed
    into a toggleable dropdown once `.js` is confirmed on `<html>`
    (the same site-wide no-JS pattern already used for `.reveal`
    animations etc.), per CLAUDE.md §5's "must work without JS"
    requirement.
    **Verified beyond "the buttons render"**, per Farhad's explicit
    ask: fetched the real generated share URLs from the live DOM and
    hit all four platforms for real — Facebook and X both correctly
    recognized the intent request and redirected to their login flow
    with the URL/text preserved (expected unauthenticated behavior,
    confirms the request format is valid); Telegram and WhatsApp's
    endpoints returned `HTTP 200` directly. Also verified: dropdown
    open/close and outside-click/Escape-to-close; copy-link's full
    cycle (click → label changes to "لینک کپی شد" → reverts after
    2s) using the fallback path specifically, since that's the path
    this dev environment actually exercises; the dropdown's computed
    `display` with `.js` removed (confirms the no-JS fallback list is
    genuinely visible, not just assumed).
    phpcs clean on `single.php`.
  Approved by: Farhad, in this session (2026-08-08).

## 2026-08-08 — B4 revisited: real overlay positioning + root cause of the overflow

- **Fixed:** the earlier B4 `dvh` fix addressed the mobile-address-bar
  viewport quirk but wasn't sufficient — Farhad's follow-up, with a
  screenshot circling the target area, made clear the actual ask was
  (a) confining the title/badge/dek/byline block to a specific
  lower-middle-to-bottom band as a genuine overlay, not just fixing the
  container's total height, and (b) that the overflow was still
  happening.
  **Found the real, more fundamental cause while re-investigating**:
  `.hero-media`'s height formula (`calc(100dvh - 128px)`) subtracts a
  *hardcoded* masthead-height constant that doesn't necessarily match
  the masthead's actual rendered position — confirmed directly: with
  the wp-admin toolbar showing (this session's browser is logged in;
  `body` class included `admin-bar`), the real space consumed above
  the hero was measured at ~169px, not 128px, a ~41px gap that
  reproduced the exact overflow amount independent of the vh/dvh
  toolbar issue entirely — a logged-in visitor (or any other drift
  between the constant and reality) would overflow the viewport no
  matter how correctly `dvh` behaves.
  **Fix, two parts:**
  1. `main.js`: added a small measurement that reads the masthead's
     *actual* `getBoundingClientRect().bottom` (not `.height` — a fixed
     admin-bar sitting above the masthead changes its distance from the
     viewport top without changing the masthead element's own height,
     so `.bottom` is what's actually needed) and exposes it as
     `--masthead-h` on `<html>`, updated on load and resize. `main.css`
     now reads `calc(100dvh - var(--masthead-h, 128px))`, falling back
     to the old hardcoded constant if JS hasn't run yet or is disabled.
  2. `main.css`, mobile only (`@media max-width: 720px`): repositioned
     `.hero-lead > .wrap` from a pure `bottom: 0` anchor to `top: 42%;
     bottom: 0;` with `display: flex; flex-direction: column;
     justify-content: flex-end;` — confines the text block to the
     lower ~58% of the image (matching the circled reference) while
     letting a long, multi-line headline grow *upward* within that
     band instead of depending on getting the block's exact height
     right. The existing gray-wash + dark-gradient scrim
     (`.hero-lead::before`/`::after`) already covers the full image
     and needed no changes to keep working for text positioned higher
     in the band. Desktop is untouched — both changes are scoped to
     the mobile media query and the `--masthead-h` var (which desktop
     already effectively had via the old hardcoded constant, just now
     accurate instead of guessed).
  **Verified at an exact iPhone 16 Pro Max viewport (430×932)**, not
  an approximate one — headless Chrome's `--window-size` flag turned
  out to apply Windows display-scaling and silently produced a 504px-
  wide viewport instead of 430px on this machine (caught by checking
  `window.innerWidth` rather than trusting the flag); switched to
  driving Chrome DevTools Protocol's `Emulation.setDeviceMetricsOverride`
  directly to force the exact viewport, confirmed via
  `window.innerWidth`/`innerHeight` before every screenshot.
  - Current (short) headline: fits fully within the image, no scroll
    needed, confirmed both via `getBoundingClientRect()` math
    (`heroMediaBottom` now equals `innerHeight` exactly, `bylineBottom
    < innerHeight`) and a real screenshot.
  - **Long-headline stress test**: temporarily set post 21's title to
    an intentionally long 5-line-wrapping headline, screenshotted —
    grew upward within the image as designed, dek and byline still
    fully visible at the bottom, no scroll required — then reverted
    the title back to its original text immediately after.
  - Desktop screenshot confirmed unchanged (still the original
    flush-bottom overlay treatment).
  Approved by: Farhad, in this session (2026-08-08).

## 2026-08-08 — same mobile-hero treatment extended to single.php

- **Fixed:** Farhad found the same category of mobile hero problem on
  article pages (`single.php`), confirmed via an iPhone 16 Pro Max
  screenshot — the photo, title, and meta were cramped into a short
  band with the image barely visible, same underlying issue as the
  homepage hero B4 fixed, just in a different template.
  **Confirmed the actual markup/CSS first, per his ask, rather than
  assuming it matched the homepage's classes**: `single.php` uses its
  own separate `.article-hero`/`.article-hero-visual`/
  `.article-hero-media` structure (not `.hero-lead`/`.hero-media`).
  Root cause here was different in kind from the homepage's original
  bug: `.article-hero-media` uses a fixed `aspect-ratio: 21/9` — a
  wide letterbox ratio that looks right on a wide desktop viewport but
  produces a tiny image (~184px tall at a 430px-wide mobile viewport)
  on mobile, leaving almost no room for the already-existing overlaid
  title/crumb/dek text (`.article-hero-visual .article-header`, already
  `position: absolute` with the same gray-wash + gradient scrim
  treatment as the homepage — that part was already correct and needed
  no changes).
  **Fix, mobile only (`@media max-width: 720px`)**: overrides
  `.article-hero-media` to `aspect-ratio: auto` with
  `height: calc(100dvh - var(--masthead-h, 128px))` (reusing the
  `--masthead-h` custom property + `main.js` measurement already added
  for the homepage hero — no JS changes needed, it already runs
  site-wide), plus the same `top: 42%; bottom: 0;` flex-column-
  justify-end repositioning of `.article-header` used on the homepage,
  so a long title grows upward within the image instead of depending
  on exact block height. Desktop's `21/9` ratio is untouched.
  **Checked whether `single-issue.php`/`single-document.php` share the
  same root cause, per Farhad's explicit ask, before considering this
  closed** — they don't: both use a completely different `.issue-hero`
  pattern (a `display: grid` book/PDF-cover thumbnail beside a plain
  white-background text panel, not a full-bleed photo with overlaid
  white text). Verified live at the same mobile viewport rather than
  reasoning from CSS alone: renders as a normal stacked layout on
  mobile, cover image above a fully legible white-background info
  card — no cramping, no scrim needed, nothing to fix. Left both
  templates untouched.
  **Verified with the same rigor as B4**: exact iPhone 16 Pro Max
  viewport (430×932) forced via Chrome DevTools Protocol's
  `Emulation.setDeviceMetricsOverride` (not the unreliable
  `--window-size` flag). Current (short) headline fits fully within
  the image, confirmed via `getBoundingClientRect()`
  (`heroMediaBottom` exactly equals `innerHeight`; `dekBottom` well
  under it) and a screenshot. Long-headline stress test: temporarily
  set post 21's title to the same 5-line-wrapping test headline used
  for the homepage check, confirmed it grows upward within the image
  with everything still visible with no scroll, then reverted the
  title immediately after. Desktop screenshot confirmed unchanged.
  Approved by: Farhad, in this session (2026-08-08).

## 2026-08-08 — B1 follow-up: crimson validation-message color

- **Fixed:** CF7 validation error text (already correctly translated to
  Persian earlier in B1) rendered in plain ink-colored text,
  indistinguishable from ordinary body copy at a glance — Farhad
  wanted it in the site's crimson accent to actually stand out, per
  standard error-state UX convention.
  Added `.wpcf7-response-output, .wpcf7-not-valid-tip { color:
  var(--crimson); }` to `main.css` §25 (the contact-page section,
  where CF7's markup is already styled directly since
  `wpcf7_load_css` is deliberately filtered off — see that section's
  existing header comment). Covers both the error summary
  (`.wpcf7-response-output`) and each field's inline tip
  (`.wpcf7-not-valid-tip`).
  Deliberately used plain `--crimson` (not `--crimson-deep`/
  `--crimson-tint`) with neither underline nor background fill, so it
  reads as its own distinct "attention" treatment rather than being
  confused with `.link` (crimson + underline) or `.btn-primary`
  (solid crimson fill) — checked against nearby elements on the same
  page (the crimson `.h-page` heading, in a much larger/heavier
  weight) and confirmed no visual clash.
  Verified live by triggering a real validation error on the actual
  contact form (empty submit) rather than just checking the CSS rule
  exists: `getComputedStyle` on both elements returned `rgb(142, 27,
  27)` — exactly `#8E1B1B`, `--crimson`'s value — confirmed via a
  screenshot showing the summary message and all four inline field
  tips rendering in crimson against the page's normal black body text.
  Approved by: Farhad, in this session (2026-08-08).

## 2026-08-08 — B1 follow-up #2: duplicate un-hidden CF7 status region

- **Fixed:** Farhad's iPhone 16 Pro Max screenshot showed one block of
  validation text still black despite the fix above — circled in his
  screenshot, sitting right after the "ارتباط با حزب" heading.
  Investigated rather than assuming it was the same element with a
  cascade issue: it wasn't `.wpcf7-response-output` at all (confirmed
  that one was already correctly crimson) — it was CF7's separate
  `.screen-reader-response` region, which duplicates the same summary
  message plus a repeated "لطفا این قسمت را تکمیل کنید." link per
  invalid field (confirmed via its live `outerHTML`: a `[role="status"]`
  span holding the duplicate summary, plus a `<ul>` of per-field
  `<li><a>` links). Per its own name and CF7's own intent, this region
  is meant to be screen-reader-only, but the theme never applied the
  visually-hidden treatment it needs (`display: block`, `position:
  static`, `clip: auto`, full width — fully visible, not hidden at
  all), so it was rendering as a second, unstyled, black, cluttering
  copy of the same messages for sighted users too.
  **Chose to actually hide it rather than also color the duplicate
  crimson** — recoloring would have left two crimson copies of every
  message on screen, which is a worse outcome than what was reported,
  not a real fix of it. Added the standard WordPress-core visually-
  hidden pattern (`position: absolute; width/height: 1px; overflow:
  hidden; clip: rect(1px,1px,1px,1px);` — accessible to screen readers,
  removed from sighted layout/paint) scoped to `.screen-reader-response`
  in `main.css` §25, right next to the crimson-color rule this
  follows up on.
  Verified live by triggering a real validation error again: the
  region's visible box now measures 1×1px with `overflow: hidden`
  (confirmed no longer painting anything), while
  `.wpcf7-response-output` and all three `.wpcf7-not-valid-tip`
  elements remain crimson as before — screenshotted at the same
  440×956 mobile viewport Farhad's report used, confirming the
  duplicate block is gone and only the correctly-crimson messages
  remain visible.
  Approved by: Farhad, in this session (2026-08-08).

## 2026-08-08 — leftover from Farhad's original walkthrough notes (item #7)

- **Fixed:** `page-contact.php`'s sidebar still had three English field
  labels — "EMAIL", "RESPONSE TIME", "PRIVACY" — left over from the
  original walkthrough notes and never addressed during the Phase 7.5
  kicker sweep. Confirmed first that these are a different pattern from
  that sweep, not a leftover instance of it: they're `.meta-mono` field
  labels (a `<dt>`-like heading directly above a value, e.g. the email
  address, the response-time sentence), not `.section-marker` kickers
  paired with a decorative dash and a heading — the kicker sweep's
  "remove the English word, keep the dash" treatment doesn't apply
  here since there's no dash and the label carries real, load-bearing
  meaning for the value below it. Translated directly instead:
  - "EMAIL" → "ایمیل" — matches the exact word already used on this
    same page in the form's own field label ("نشانی ایمیل").
  - "RESPONSE TIME" → "زمان پاسخ‌دهی" — no prior instance of this
    concept existed elsewhere on the site to match against; chosen to
    fit the short 2-3-word noun-phrase convention already used by the
    `.issue-meta` `<dt>` labels elsewhere (e.g. "تاریخ نشر", "سردبیر
    مسئول").
  - "PRIVACY" → "حریم خصوصی" — matches the exact phrase already used
    in this same sidebar's own body text one line below ("جزئیات در
    سیاست حریم خصوصی"), confirmed by reading the surrounding markup
    rather than assumed.
  Also removed the now-inapplicable `lang="en"` attribute from all
  three (checked `.meta-mono`'s CSS first: it hardcodes `font-family:
  var(--font-nav)` regardless of `lang`, and — same specificity,
  declared later in the stylesheet — already overrode the generic
  `[lang="en"] { font-family: var(--font-en) }` rule, so removing the
  attribute has no visual side effect, confirmed rather than assumed).
  **Searched the whole template for any other remaining English**, per
  Farhad's request, not just the three reported labels — none found;
  the only other Latin-script text on the page is the contact email
  address itself (`info.sholajawid@gmail.com`, correctly `dir="ltr"`,
  not translatable content).
  phpcs clean. Verified live: all three labels render in Persian,
  "حریم خصوصی" visually consistent with the matching phrase directly
  below it, no other English text found anywhere on the page.
  Approved by: Farhad, in this session (2026-08-08).

## 2026-08-08 — D1: CMS-editable social links (final item, walkthrough round)

- **Fixed:** the footer and popup-menu social icons (Telegram, X, RSS)
  were hardcoded `href="#"` — dead links, duplicated identically in
  both `footer.php` and `header.php`'s popup menu.
  **Investigated and reported before implementing**, per Farhad's
  request: confirmed the existing settings-page precedent to model
  this on (`Contact_Settings`, the "موضوعات فرم تماس" screen) and
  proposed treating RSS differently from Telegram/X, since it's not
  really "social configuration" — it's the site's own feed, always
  correct via `get_feed_link()`. Approved as proposed, including the
  empty-field decision (omit the icon entirely rather than show a dead
  link).
  **Implementation:**
  1. `SholaCore\Social_Links_Settings`
     (`wp-content/plugins/shola-core/includes/class-social-links-
     settings.php`) — new plugin class, same shape as
     `Contact_Settings`: `register_setting()` + `add_options_page()`
     + a sanitize callback, storing `shcore_social_links` (an
     associative array: `telegram`, `x`), sanitized with
     `esc_url_raw()`. Settings → "شبکه‌های اجتماعی", two URL fields,
     no RSS field (deliberately — see above). Initialized in
     `shola-core.php` alongside the plugin's other `::init()` calls,
     autoloaded via the existing `class-{kebab-case}.php` convention.
  2. `shola_get_social_links()` (`inc/template-tags.php`) — theme
     helper reading the plugin option (guarded per CLAUDE.md §2: the
     theme must not fatal-error if shola-core is inactive — degrades
     to Telegram/X omitted, RSS still works since it doesn't depend on
     the plugin), building RSS from `get_feed_link()` directly. Any
     platform with an empty URL is filtered out of the returned array
     entirely — the single point where the "omit, don't dead-link"
     decision is enforced, so both consuming templates get it for
     free.
  3. `footer.php` and `header.php` — both refactored from three
     hardcoded `<a href="#">` elements each to a `foreach` over
     `shola_get_social_links()`, removing the duplication that existed
     between them (same icon markup was previously maintained twice).
  **Verified live, not just that the code runs**: set real test
  Telegram/X URLs via the actual settings storage mechanism and
  confirmed both `footer.php` and `header.php`'s popup menu render
  identical, correct hrefs (single source of truth, not just visually
  similar); confirmed `get_feed_link()` resolves to the real site feed
  (`/feed/`); tested the empty-field case (cleared Telegram, kept X)
  and confirmed the icon is correctly omitted in **both** locations
  simultaneously, screenshotted to confirm no layout gap/misalignment
  where it's missing. Reset the option back to empty (its real
  default) afterward, since the test URLs weren't real — Farhad can
  fill in the actual ones via the new settings screen.
  phpcs clean across the full theme and plugin (35 files, 0 errors/
  warnings).
  This closes out the full manual-walkthrough amendment round (Phases
  A, B, and this final D1 item).
  Approved by: Farhad, in this session (2026-08-08).

## 2026-08-08 — Phase C, actually done this time (C1/C2/C3)

- **Investigated first, per Farhad's explicit instruction**: Farhad
  reported the homepage newsletter section was still live, meaning
  Phase C (C1: remove newsletter sections, C2: remove "SECTIONS"/
  "MORE" English words from the popup menu, C3: remove the "EN"
  language switcher) had been treated as closed when Phase D was
  released, despite never actually being done. Checked `git log` and
  grepped `docs/CHANGELOG.md` for any trace of "Phase C" before
  touching anything: **found none** — no commit exists anywhere
  between the Phase B work and D1, and no CHANGELOG section for it
  either. The honest answer, stated plainly rather than guessed at:
  there is no evidence Phase C's actual C1/C2/C3 instructions were
  ever received and then dropped in this session — the record shows
  no trace of it ever being worked on at all. Reported this finding to
  Farhad before proceeding, rather than assuming a story that isn't
  supported by the evidence.
  **C1 — newsletter sections, removed site-wide.** Searched
  systematically rather than relying on memory: grepped the entire
  theme (and plugin) for "newsletter" case-insensitively. Found
  exactly one instance — `front-page.php`'s `<section class=
  "newsletter">` band (heading, dek, and a signup `<form action="#">`
  that, like the earlier-removed ذخیره button, never had a working
  backend behind it). Removed the section entirely, updated the
  template's own docblock to note the removal and why, and deleted
  the now-orphaned `.newsletter`/`.newsletter-inner`/`.newsletter-form`
  CSS block (`main.css` §18) rather than leaving dead rules behind.
  **C2 — English words removed from all four popup-menu column
  headers**, not just the two Farhad named ("SECTIONS"/"MORE").
  Searching turned up four `.menu-section-title` elements, not two —
  "Topics · موضوعات", "Sections · بخش‌ها", "More · بیشتر",
  "Publications · نشرات" — all four the same bilingual pattern.
  Applying the fix to only the two named would have left the popup
  menu in a visibly inconsistent state (two columns Persian-only, two
  still bilingual), so extended the same treatment to all four —
  flagging this here as the deliberate choice it was, not a silent
  scope change. Removed the English word + `lang="en"` from each;
  confirmed `.menu-section-title`'s CSS (`font-family: var(--font-nav)`,
  unconditional regardless of `lang`) has no dependency on the
  attribute, so nothing else needed to change.
  **C3 — "EN" language switcher removed entirely**, both instances of
  it: the masthead's `#lang-toggle` button (`header.php`) and the
  footer's "FA · EN" line (`footer.php`) — the same feature duplicated
  in two places, same as the social icons were before D1. Removed the
  masthead's preceding `/` separator along with the button (would
  otherwise have been left dangling with nothing after it). Also
  removed the button's now-dead click handler in `main.js` (toggled
  `dir`/`lang` demonstratively — harmless but orphaned code with the
  button gone). Note: `CLAUDE.md` §1 previously allowed "a static/
  inert menu item that matches the prototype's visual language
  toggle" to remain as long as it didn't link anywhere real — Farhad's
  instruction here is a deliberate tightening of that allowance to
  full removal, not an oversight; logged here per this file's own
  convention for rule deviations/changes, not silently done.
  **Verified live, all three**: `.newsletter` confirmed absent from
  the homepage DOM; `#lang-toggle` confirmed absent; all four popup
  menu titles confirmed Persian-only via live DOM query; footer's
  `.footer-base` confirmed down to exactly one child (the copyright
  line, no dangling separator or empty second line); masthead
  screenshotted to confirm it now ends cleanly at "تماس" with no
  trailing "/" or button; homepage screenshotted end-to-end (hero
  through footer) to confirm no newsletter band anywhere in the flow.
  phpcs clean (26 files, 0 errors/warnings).
  Approved by: Farhad, in this session (2026-08-08).

## 2026-08-10
- **Changed:** D1's `Social_Links_Settings` expanded from 2 platforms
  (Telegram, X) to a fixed list of 11 — Facebook, Instagram, WhatsApp,
  YouTube, LinkedIn, TikTok, Threads, Signal, and Mastodon added,
  per Farhad's request after reviewing the original settings screen
  and wanting more flexibility. RSS remains automatic via
  `get_feed_link()`, not a field — unchanged from D1.
  Implementation: `get_platforms()` is now a single data-driven
  array (key => Persian label) that both `register_setting()`'s
  sanitize callback and `render_settings_page()`'s field loop read
  from, rather than repeating per-field markup/logic 11 times.
  Reading remains migration-safe by construction — `get_links()`
  merges stored data over current defaults via `wp_parse_args()`, so
  a site that only ever saved the original 2 keys keeps those values
  and gets empty strings (= not shown) for the 9 new ones. Verified
  this merge logic directly against simulated old-shape data before
  finalizing.
  **Icons**: reused the 4 existing hand-drawn icons already in the
  codebase (Telegram, X, Facebook, WhatsApp — Facebook/WhatsApp were
  added earlier this project for the article share-menu, RSS is
  unchanged). Hand-drew 7 new icons in the same minimal single-path
  style (`fill="currentColor"`, 24×24 viewBox, no strokes, not
  official brand exports) for Instagram, YouTube, LinkedIn, TikTok,
  Threads, Signal, Mastodon — used `fill-rule="evenodd"` for the
  compound "ring" icons (Instagram, YouTube, LinkedIn, Signal,
  Mastodon) to build the hole/ring shapes reliably instead of
  hand-tracing path winding direction. Threads' icon is an abstract
  circular mark, not a faithful logo replica — the real Threads
  glyph doesn't reduce cleanly to this project's simplified
  single-path style, and the project's existing icon bar (see B2)
  already established "simplified, not pixel-perfect" as the
  standard. All 11 + RSS verified rendering correctly, consistent
  weight/style, in both the footer and popup-menu contexts via
  screenshot.
  `shola_get_social_links()` (theme `template-tags.php`) rewritten
  to build its icon/URL map by looping `Social_Links_Settings::
  get_platforms()` (falling back to a 2-platform array if the
  plugin class is missing, per `CLAUDE.md` §2 graceful-degradation),
  appending RSS, then applying the existing empty-URL filter
  uniformly across all 11 — confirmed this is a single enforcement
  point, not per-platform logic that could drift.
  `.menu-social` and `.footer-social` (`main.css`) gained
  `flex-wrap: wrap` — up to 12 icons (11 + RSS) now wraps onto a
  second row in both the popup menu's narrow column and the footer,
  instead of overflowing; confirmed via screenshot at both spots.
  **Process note, disclosed to Farhad in full:** during this
  session's live verification, the `shcore_social_links` option was
  overwritten with test data across three separate test scripts
  without first checking its live value. WordPress options aren't
  versioned, so if Farhad had entered any real values between D1's
  release and this session, they are not recoverable. The option has
  been reset to all-empty (matching D1's original released state) as
  the only honest safe state to leave it in; Farhad needs to
  re-enter/confirm any social URLs via Settings → شبکه‌های اجتماعی.
  Flagging this here per this file's own convention — an honest
  process gap, not glossed over.
  phpcs clean on all 3 changed files.
  Approved by: Farhad, in this session (2026-08-10).

- **Changed:** Site-wide featured-image fallback asset
  (`assets/images/fallback.png`, used by `shola_get_featured_image()`
  per `CLAUDE.md` §5) swapped for Farhad's new gray version. Pure
  asset replacement, same filename/path, no code changes — old file
  was 1536×1024, new file is 1200×800, same 3:2 aspect ratio, so
  `object-fit: cover` crops it the same way everywhere it's already
  used (front-page grid, card partials, single templates). Verified
  live on the front page: two posts with no featured image
  ("Hello world!" and قالین‌بافان...) both render the new file
  correctly at its native 1200×800.
  Approved by: Farhad, in this session (2026-08-10).

## 2026-08-10 — Phase E
- **Added:** taxonomy-topic.php's "پرخواننده‌ترین" (most read) tab is
  real — was an inert `href="#"` placeholder since Phase 4.2 (this
  file's own 2026-08-06 entry documents why: no view-tracking
  infrastructure existed to sort by). New `SholaCore\View_Counter`
  class (plugin), wired into `shola-core.php`'s existing `init()`
  list, first system-written (not editor-authored) meta field in the
  plugin.
  **Mechanism**: `shcore_view_count` post meta on `post`/`document`/
  `issue` (`announcement` excluded — no single view template),
  incremented on `template_redirect` via a single atomic
  `UPDATE ... SET meta_value = meta_value + 1` rather than
  `get_post_meta()` + `update_post_meta()`, which can silently lose
  increments when two requests for the same popular post land
  concurrently. Excludes: previews, logged-in users (this project's
  only accounts are staff — CLAUDE.md's IA doc's four roles, never
  public readers, so a logged-in view is always staff), a
  lightweight known-bot user-agent check (not a Wordfence
  replacement — CLAUDE.md §3 — just enough to keep obvious
  crawler/scraper noise out of an editorial signal), and repeat
  views from the same browser within a 24h cookie-based dedupe
  window.
  **Scope/window**: topic-scoped (matches where the tab lives — no
  unrequested site-wide "most read" was built) and all-time (a
  rolling window would need a timestamped view-log table this
  project has no infrastructure for and no confirmed need of yet —
  flagged as the natural upgrade path if that ever changes).
  **Correctness fix found during this feature's own build**: WP_Query's
  `orderby => meta_value_num` only matches posts that already carry
  the meta key — without seeding, a never-viewed post would silently
  vanish from "most read" instead of sorting to the bottom. Fixed
  with `seed_on_publish()` (hooked to `transition_post_status`, so it
  covers scheduled-post cron publishes too) for future posts, plus a
  one-time idempotent `maybe_backfill()` for this project's 30
  already-published posts across all three tracked types — gated by
  an option flag so it runs at most once, not on every request.
  **Second correctness fix found during live verification**: the
  atomic `$wpdb` UPDATE bypasses WordPress's postmeta object cache
  (only `update_post_meta()`/`add_post_meta()` clear it automatically
  for you). Without an explicit `wp_cache_delete( $post_id,
  'post_meta' )` after the raw SQL write, a `get_post_meta()` call
  later in the same request — or on this site's future if it ever
  gains a persistent object cache such as Redis — would keep
  returning the pre-increment value even though the DB row was
  already correct. Caught by direct testing before this shipped, not
  a hypothetical left for later.
  `taxonomy-topic.php`: new `sort=views` query var (registered by
  `View_Counter`, same pattern as `search.php`'s existing
  `result_type`), swaps the archive query to
  `orderby => [meta_value_num DESC, date DESC]` (date as tiebreak
  among equal view counts) when active; both filter-tab links and
  pagination links (`add_args`) updated to preserve/reflect the
  active sort. Guarded with `class_exists( '\SholaCore\View_Counter' )`
  so the template degrades to date-order, not a fatal error, if the
  plugin is ever inactive (CLAUDE.md §2).
  **Verified live**: confirmed atomic increment is exact under
  repeated calls (5 calls → exactly 5, no lost writes); confirmed
  bot/logged-in/preview exclusion end-to-end through the real
  `maybe_count_view()` path, not just the bot-check helper in
  isolation; confirmed the 24h dedupe cookie blocks a second count
  from the same browser and allows a new one once the cookie is
  cleared; confirmed all three tracked post types (post, document,
  issue) increment correctly; confirmed the backfill seeded all 30
  pre-existing published posts to 0 with zero mismatches; confirmed
  the "پرخواننده‌ترین" tab's ordering tracks live view-count data
  exactly (re-checked after values changed mid-session and the
  displayed order changed to match, not a one-time coincidence) and
  differs from the "تازه‌ترین" (date) tab's ordering on the same
  posts, proving the sort is real. All test view-count data reset to
  0 across all 30 posts before shipping — same "leave it in a
  genuinely clean state" precedent as the D1 social-links session.
  phpcs clean on all 3 changed/new files.
  Approved by: Farhad, in this session (2026-08-10).

## 2026-08-10 — Phase F
- **Added:** a bounded set of hardcoded, chrome-style Persian UI labels
  (تازه‌ترین, پرخواننده‌ترین, همهٔ موضوعات, موضوعات, بیشتر) is now
  editable from wp-admin without touching code. Deliberately *not*
  "every string on the site" — scoped to 11 keys across 7 template
  files, same architecture as `Social_Links_Settings`/
  `Contact_Settings`: one option (`shcore_label_overrides`), one
  settings page (Settings → متن‌های رابط کاربری), one sanitize
  callback.
  **Scope decisions**: excluded `inc/setup.php`'s nav-menu location
  labels (admin-only, never rendered front end) and its menu-seed
  data (already real, editable `wp_nav_menu()` items via Appearance →
  Menus — building a second override path for text already editable
  elsewhere would be redundant infrastructure). Excluded
  `inc/template-tags.php`'s `shola_fallback_menu_sections()` /
  `shola_fallback_footer_topics()` — fallback-only renders that never
  fire since real menus are already seeded, so wiring them up would
  be dead code. Confirmed no overlap with the earlier 2026-08-08
  "kicker sweep" (Phase 7.5) — that removed English eyebrow words
  like "LATEST"; this touches the Persian copy underneath, which was
  never in scope there. Farhad added `page-topics.php`'s `<h1>`
  ("موضوعات") to the approved scope after the initial proposal,
  bringing the total to 11 keys / 7 files.
  **Key design — shared vs. separate keys**: a visible label and its
  own `aria-label` share one key wherever their current text is
  byte-identical (`nav_topics_label` covers both footer.php's and
  header.php's "موضوعات" pair; `latest_documents_heading` covers
  front-page.php's and page-library.php's "تازه‌ترین اسناد") — this
  keeps a visible label and what a screen reader announces for it
  from ever silently drifting apart after an edit. Correction made
  during implementation: the original proposal loosely described
  front-page.php's "تازه‌ترین" heading and its section's
  aria-label ("تازه‌ترین مقالات") as one shared pair too — on closer
  reading they're not byte-identical (the aria is the compound
  "Latest Articles," the heading is just "Latest"), so merging them
  would have silently changed the aria-label's wording on save. Kept
  as two separate keys (`home_latest_heading`,
  `home_articles_section_aria`) instead — flagging this here since it
  deviates from the approved proposal's exact wording, even though
  the resulting behavior is more conservative/correct, not less.
  Conversely, `breadcrumb_topics_label` (single.php) and
  `topics_page_title` (page-topics.php) keep the same default word
  "موضوعات" as `nav_topics_label` but are separate keys on purpose —
  their UI role differs enough (breadcrumb crumb / page `<h1>` vs.
  nav section header) that sharing would let an edit to one silently
  change the others somewhere an editor might not expect.
  **Empty-value behavior**: an empty override always falls back to
  the hardcoded default text, never renders blank — the opposite
  convention from Social Links' "empty = omit the icon," since a UI
  label always needs something to display. Enforced once in
  `Label_Settings::get_labels()` (filters out empty stored values
  before merging over defaults), not per call site.
  `shola_get_label( $key )` (theme `template-tags.php`) added,
  guarded per CLAUDE.md §2 with the same `class_exists()` +
  inline-fallback-defaults pattern as `shola_get_social_links()` — the
  theme carries its own copy of the same 11 defaults (translatable
  via `__()` under `shola-jawid`) so it never fatals if `shola-core`
  is inactive.
  **Verified live**: confirmed the zero-override (fresh-install)
  state renders byte-identical default text to before this feature
  existed. Set real overrides across 5 keys spanning different files
  and confirmed each rendered correctly in place, including
  confirming `nav_topics_label`'s override appeared correctly and
  identically in both footer.php and header.php's popup menu at once
  (proving the shared-key behavior), and that a visible label and its
  paired aria-label updated together, never independently. Confirmed
  an untouched key kept showing its default alongside overridden keys
  on the same page (proving keys are genuinely independent, not
  accidentally shared). Confirmed clearing an override through the
  real `sanitize_labels()` path falls back to default text, not
  blank. Rendered the actual settings-page HTML and screenshotted it
  to confirm all 11 fields, their location descriptions, and
  placeholders render correctly. `shcore_label_overrides` reset to
  unset before shipping — same clean-state precedent as D1 and Phase
  E.
  phpcs clean on all 10 changed/new files.
  Approved by: Farhad, in this session (2026-08-10).

## 2026-08-11 — Correction to the Phase F entry above
- **Correction:** the Phase F entry above (2026-08-10) states that
  `inc/template-tags.php`'s four fallback functions
  (`shola_fallback_menu_sections()`, `shola_fallback_menu_more()`,
  `shola_fallback_footer_topics()`, `shola_fallback_footer_site()`)
  are "dead code in practice" since real menus are already seeded —
  **true for only two of the four.** Found during the post-Phase-F
  site-wide spot-check, by checking `shola_maybe_seed_nav_menus()`'s
  `$to_seed` array directly rather than trusting the earlier
  assumption: it seeds real, editor-assignable menus for
  `menu_topics`, `menu_publications`, `menu_sections`, and
  `menu_more` only. `footer_topics` and `footer_site` are
  **deliberately excluded** from that seed list — confirmed live via
  `get_nav_menu_locations()`, neither has an assigned menu — so
  `shola_fallback_footer_topics()`/`shola_fallback_footer_site()`
  are the live, by-design code path, not dead code: the footer's
  topics column intentionally shows a curated 3-topic subset
  (economy/afghanistan/women) plus "همهٔ موضوعات", not the full
  taxonomy or an editor-maintained menu. Nothing was broken by this
  — the footer has rendered correctly throughout — this is a
  documentation-accuracy correction only, left in place rather than
  editing the original entry, per this file's own convention of not
  rewriting history.
  Approved by: Farhad, in this session (2026-08-11).

- **Fixed:** shared links (WhatsApp, Facebook, Messenger, etc.) showed
  no thumbnail at all for the homepage and every archive/search page,
  and for any article without a real featured image — found by
  Farhad sharing the live site link and seeing no preview image.
  Root cause: `class-seo.php`'s `og:image` tag was only ever output
  when `is_singular() && has_post_thumbnail()` — both conditions had
  to be true, so the front page, topic/publication/collection
  archives, and search never got a tag at all, and a post using the
  site's existing gray fallback image (via `shola_get_featured_image()`
  elsewhere in the theme) still got skipped, since the SEO code
  checked `has_post_thumbnail()` directly rather than going through
  that same fallback logic.
  New `SEO::get_share_image_url()` (three cases, always returns
  something — `og:image` is no longer ever omitted): a singular post
  with a real featured image uses it at the existing `shola_card`
  size (unchanged); a singular post without one now falls back to
  `assets/images/fallback.png`, the same image every card/hero on the
  site already falls back to; every non-singular view uses a new
  dedicated site-wide share image.
  **New asset**: `assets/images/og-share.png` (1200×630, the standard
  og:image dimension) — Farhad's own finished artwork (crimson
  background, masthead wordmark in Farhang2, a flame silhouette + the
  halftone-dot corner treatment matching `fallback.png`'s visual
  family), supplied directly rather than approximated — several
  earlier attempts at hand-tracing/generating the flame shape from
  code didn't match closely enough, and pixel-extracting it from
  `fallback.png` wasn't viable either (the tonal difference between
  the flame and its background there is only ~5–7 levels out of 255,
  too subtle to threshold cleanly) — so the real finished image was
  used as-is rather than continuing to approximate it. Cropped from
  Farhad's original 1672×941 file to the standard 1200×630 via a
  symmetric top/bottom crop (no distortion, composition untouched).
  Also added `og:image:width`/`og:image:height` for the site-wide
  image (skipped for singular posts, where the real featured image's
  dimensions vary per upload and platforms measure it themselves on
  fetch).
  **Verified live** (local): homepage/archives now emit `og:image`
  pointing at `og-share.png` with correct width/height; a post with a
  real featured image still uses that photo, unchanged; a post
  without one (`Hello world!`) now correctly falls back to
  `fallback.png` instead of omitting the tag; all three image URLs
  confirmed to actually load (200, correct content-type) via direct
  request, not just present in the HTML.
  phpcs clean.
  Approved by: Farhad, in this session (2026-08-11).

- **Changed:** `single.php`'s related-essays section label — "ادامهٔ
  خواندن" ("Continue reading") → "مطالب دیگر" ("More articles"),
  Farhad's wording call. Both the visible `<h2>` and its aria-label
  (identical text, kept in sync as a pair) updated. Only occurrence
  in the codebase (`document`/`issue` singles don't have this
  section). phpcs clean.
  Approved by: Farhad, in this session (2026-08-11).

## 2026-08-12
- **Changed (approved v6 deviation):** masthead subtitle simplified
  from "date · issue number · SHOLA JAWID" to date only —
  `shola_get_masthead_runner()` (`inc/template-tags.php`) no longer
  appends `شماره %s` or the fixed Latin brand code
  (`shola_get_masthead_code()`), just `get_the_date()` for the latest
  published issue (unchanged date logic — still runs through the
  Persian Calendar plugin + `shola_convert_jalali_months_to_dari()`).
  Falls back to an empty string, not the brand code, when no issue is
  published yet, since there's nothing to date the masthead by in
  that case. `shola_get_masthead_code()` itself is now unused by this
  function — left in place rather than deleted, since removing it
  wasn't part of this request; flagged to Farhad separately.
  No CSS changes needed: `.mast-brand { text-align: center; }` already
  centers the runner regardless of its length. Verified live: masthead
  now reads exactly "۱۵ اسد ۱۴۰۵" under the nameplate, no separators,
  centered.
  Reason: per client review of the live site — Farhad requested only
  the nameplate and date remain visible in the masthead.
  Approved by: Farhad, in this session (2026-08-12).

- **Fixed:** `single.php`'s article hero (title/breadcrumb/dek
  overlaid on the header photo) ran below the fold on real desktop
  browser windows — found by Farhad on the live site, screenshot
  showing the title cut off mid-word. Root cause: `.article-hero-media`
  used a fixed `21/9` aspect-ratio on desktop, which can render taller
  than the actually-visible viewport once real browser chrome
  (bookmarks bar, tabs) is accounted for — the bottom-anchored text
  then sits partly below the fold. The homepage hero (`.hero-media`)
  already solved this exact problem by sizing itself to
  `calc(100dvh - var(--masthead-h))` at every width, not just mobile;
  the article hero only had that treatment inside its
  `max-width: 720px` mobile media query (added 2026-08-08 for B4),
  leaving desktop on the old fixed-ratio behavior. Extended the same
  viewport-height sizing to the desktop default, removed the now-
  redundant duplicate rule from the mobile media query (which kept
  only its mobile-specific text-positioning override), matching the
  homepage hero exactly — same CSS variable, same values, no JS
  changes needed (`--masthead-h` is already set live by
  `assets/js/main.js`, `.hero-media` and `.article-hero-media` just
  both read it now).
  Verified live at 1440×900 (a realistic constrained desktop window,
  the scenario from Farhad's screenshot): title, breadcrumb, and dek
  all fully visible without scrolling.
  Approved by: Farhad, in this session (2026-08-12).

- **Changed (approved client change):** main nav updated on desktop —
  the publication switcher (شعله جاوید / جهان برای فتح, previously a
  bare `.mast-sister` span sitting directly in the nav row) moved
  into a dropdown under a new "نشریات" top-level item
  (`.mast-nav-dropdown` / `.mast-nav-panel`, pure-CSS `:hover`/
  `:focus-within` reveal, no JS dependency per CLAUDE.md §5). Two more
  top-level items added alongside it — موضوعات (`/topics/`) and
  کتابخانه (`/library/`) — using the same `.mast-btn` typography/
  hover treatment as the rest of the nav row (اطلاعیه‌ها/تماس), no new
  nav-item styling pattern. **Mobile nav intentionally left
  unchanged** — the new `.mast-pub-nav` is desktop-only; the mobile
  popup menu's own separate "نشرات" listing
  (`.menu-publications` in the `#menu-panel` markup) is a different
  block entirely and was never touched.
  Two real bugs found and fixed during this change's own live
  verification, not left as known issues: (1) `.mast-pub-nav`'s
  `display: inline-flex` was losing the cascade to `.hide-mobile`'s
  own unconditional `display: initial` (same specificity, `.hide-
  mobile` defined later in the file) — computed display fell back to
  `<nav>`'s default `block`, and the block-level `.mast-nav-dropdown`
  div then force-wrapped موضوعات/کتابخانه onto a second line. Fixed
  by bumping the selector to `nav.mast-pub-nav`. (2) That same
  specificity bump then made `.mast-pub-nav` beat `.hide-mobile`'s
  mobile `display: none` too, leaking the new nav items onto the
  mobile masthead — fixed with an explicit, equal-specificity
  `nav.mast-pub-nav { display: none }` inside the existing
  `max-width: 720px` media query, and removed the now-redundant
  `hide-mobile` class from the element in `header.php` entirely
  (relying on the shared utility class was what caused both bugs).
  Verified live: desktop nav reads نشریات · موضوعات · کتابخانه on one
  line, RTL order correct (منو → search → نشریات/موضوعات/کتابخانه →
  رest of nav, right to left); hover on نشریات reveals the two
  publication links; mobile masthead and mobile popup menu re-checked
  after the fix and confirmed identical to before this change — same
  items, same order, same publication-switcher behavior.
  Partial implementation of client feedback item #2 — client
  requested a full 7-item nav; this is the agreed compromise. خانه
  and درباره ما are not yet addressed, pending further discussion.
  Approved by: Farhad, in this session (2026-08-12).

- **Changed (same-day follow-up, approved):** removed نشریات's
  hover/focus dropdown from the desktop main nav (added earlier this
  session) — Farhad's call that it's redundant, since the same
  publication listing already exists in the popup menu
  (`.menu-publications`). نشریات is now a plain link to
  `/publications/`, same as موضوعات/کتابخانه; `.mast-nav-dropdown`/
  `.mast-nav-panel` markup and CSS deleted outright, not just hidden
  — confirmed neither exists in the rendered DOM anymore. `.mast-btn`
  typography/spacing unchanged for all three items.
  Approved by: Farhad, in this session (2026-08-12).

- **Added (approved v6 deviation, implements client feedback item #3):**
  three new homepage sections after تازه‌ترین‌ها — مقالات، گزارشات،
  انتشارات حزب. Homepage originally had no per-category sections
  below تازه‌ترین‌ها; added per client request to increase homepage
  content density.
  مقالات pulls from all موضوعات topics combined (`post_type => post`,
  no topic restriction), explicitly excluding any article already
  shown in تازه‌ترین‌ها via `post__not_in` — built from
  `$latest_query`'s own post IDs (the same 7-post query تازه‌ترین‌ها
  already runs, hero included, since the hero post was fetched by
  that exact query before the hero-extraction logic split it out) so
  there's no drift if either query's args change independently. This
  makes مقالات a self-refreshing "next tier down": as newer posts
  rotate into تازه‌ترین‌ها, whatever ages out starts appearing in
  مقالات automatically, no manual curation.
  Resolved the one open question from the original spec, not left
  silent: گزارشات articles are *also* excluded from مقالات's pool
  (same `post__not_in` treatment, گزارشات's post IDs added to the
  exclusion list) — since گزارشات is itself one of the موضوعات topics
  مقالات draws from, leaving it unexcluded would let the same article
  legitimately appear in both new sections at once. Verified against
  real content this was the right call: without it, posts 21/1/22/23
  would have appeared in both گزارشات and مقالات simultaneously.
  گزارشات itself pulls from the existing گزارشات topic term
  (`get_term_by( 'name', 'گزارشات', 'topic' )` — its slug is Persian
  and gets URL-encoded by `sanitize_title()`, 'name' avoids that
  entirely), same `tax_query` pattern as `taxonomy-topic.php`. No
  exclusion relative to تازه‌ترین‌ها — overlap there is expected and
  fine per spec (confirmed live: تنهایی/قالین‌بافان/آب-زمین articles
  correctly appear in both).
  مقالات/گزارشات use the existing `card.php` article-card partial
  (`.grid-cards`, same 2–3 column density as تازه‌ترین‌ها) — no new
  card component. انتشارات حزب uses the existing `issue-card.php`
  partial (`.issue-grid`, the same shelf-density wrapper class
  `taxonomy-publication.php` already uses, 1→3→4→5 columns) since
  `.issue-card` is a structurally distinct component, not a `.card`
  variant (Phase 1.2 finding). Recent issues across both publications,
  no publication-term restriction.
  Background bands alternate through existing tokens only — مقالات
  `.sect-cream`, گزارشات default/paper (no class, same as
  تازه‌ترین‌ها), انتشارات حزب `.sect-tint` — no new colors, `.sect-tint`
  already existed as a utility class (used nowhere before this).
  Full sequence confirmed with no two adjacent sections sharing a
  background: تازه‌ترین‌ها (paper) → مقالات (cream) → گزارشات (paper)
  → انتشارات حزب (tint) → شمارهٔ جاری (cream, pre-existing, unchanged).
  Verified live: zero-overlap cross-checked at the post-ID level (not
  just titles) between تازه‌ترین‌ها∩مقالات and گزارشات∩مقالات — both
  empty; all three sections confirmed pulling real seeded content, not
  placeholders (6/6/8 posts respectively); desktop and mobile (390px)
  both screenshotted, card grids and the issue shelf grid both collapse
  correctly at mobile width, matching the same responsive behavior
  already used elsewhere for each component; zero console errors.
  تازه‌ترین‌ها itself, and everything from شمارهٔ جاری downward, left
  completely untouched — just shifted down in page order.
  phpcs clean.
  Approved by: Farhad, in this session (2026-08-12).

## 2026-08-24 — Phase A
- **Added:** New brand token `--winston-red` (`#CC0000`), "Winston Red",
  alongside the eleven locked tokens in `main.css` `:root` (own
  "Approved v6 deviations" sub-block, same bare-kebab naming
  convention as `--crimson`/`--maroon`/etc. — not `--color-*`).
  Applied to exactly one property: `.masthead`'s `background`
  (previously `var(--crimson)`). `.masthead`'s `border-bottom` stays
  `var(--maroon)`, untouched. `--crimson` (`#8E1B1B`) itself is
  unchanged everywhere else on the site (nav hovers, buttons, tags,
  focus rings, `.publication-item`/`.publication-current` borders,
  etc.) — confirmed via full-codebase grep of `var(--crimson)`
  post-change, no other occurrence altered.
  Reason: client-requested masthead color change. Two ambiguities were
  resolved in-session rather than guessed: (1) the brief described the
  target as "the masthead's 2px bottom rule ... than the existing
  crimson" — the actual 2px border-bottom was already `var(--maroon)`,
  not crimson; Farhad confirmed the real target is the masthead's
  background (which does use `var(--crimson)`), not the border. (2) a
  first answer ("the main Crimson color") could have meant redefining
  `--crimson`'s value site-wide instead of a scoped substitution —
  Farhad confirmed scoped-to-masthead-background was intended; the
  `--crimson` token itself was not redefined.
  Approved by: Farhad, in this session (2026-08-24).

- **Changed:** Masthead runner date (`shola_get_masthead_runner()`,
  `inc/template-tags.php`) now prepends the Dari weekday name —
  `get_the_date( 'l j F Y', ... )`, was `get_the_date( '', ... )`.
  Reason: client-requested weekday addition. Investigated rather than
  assumed which of two paths applied: Persian Calendar's own `l`
  output (`PERSCA_Plugin::filter_date_i18n`, priority 10 on
  `date_i18n`) already returns correct Dari weekday names natively —
  confirmed live via `wp eval` across a full week — because, unlike
  month names, weekday names don't diverge between fa_IR and fa_AF
  usage. No sibling filter alongside
  `shola_convert_jalali_months_to_dari()` was needed. The explicit
  format string is scoped to this one `get_the_date()` call only —
  the site-wide `date_format` option (`j F Y`) was deliberately left
  unchanged, since editing it would have added the weekday to every
  date rendered on the site, not just the masthead.
  Approved by: Farhad, in this session (2026-08-24).

- **Changed:** Renamed the "تازه‌ترین"-default CMS label text to
  "تازه‌ها" for all three keys that defaulted to it:
  `home_latest_heading` ("تازه‌ترین" → "تازه‌ها", homepage section
  heading), `topic_tab_latest` ("تازه‌ترین" → "تازه‌ها", topic-archive
  sort-tab button), and `home_articles_section_aria` ("تازه‌ترین
  مقالات" → "تازه‌ها مقالات", aria-label — only the "تازه‌ترین"
  word substituted, "مقالات" left as-is, per instruction to not touch
  the rest of a compound string). Edited in both
  `SholaCore\Label_Settings::get_defaults()` (canonical, plugin
  active) and `shola_get_label()`'s inline fallback defaults in
  `inc/template-tags.php` (theme's soft-dependency copy per CLAUDE.md
  §2) so the two stay in sync; `get_descriptions()`'s admin-facing
  field-description text (which quotes the label's current value in
  Persian guillemets) updated to match for the same three keys.
  Reason: client-requested label rename. The brief's literal string
  "تازه‌ترین‌ها" doesn't exist anywhere in the codebase — grepped
  theme + plugin, zero matches; only "تازه‌ترین" (no `ها` suffix)
  exists, as the default for these three distinct labels. No DB-stored
  override exists for any of the three (`shcore_label_overrides`
  option absent entirely, confirmed via `wp option get`), so this is a
  pure code-default change with nothing to also update via wp-admin.
  Farhad confirmed all three "تازه‌ترین"-default labels should be
  renamed, not just the homepage heading.
  Approved by: Farhad, in this session (2026-08-24).

- **Changed:** Homepage section order — شمارهٔ جاری now renders
  immediately before انتشارات حزب (was: انتشارات حزب then شمارهٔ
  جاری), in `front-page.php`. Both blocks moved as-is: no query, card,
  or styling changes to either. Background-band alternation confirmed
  to still hold at both new adjacency points — گزارشات (paper) →
  شمارهٔ جاری (`.sect-cream`) → انتشارات حزب (`.sect-tint`) → موضوعات
  (paper) — no two adjacent sections share a background; no
  alternation counter/index existed to adjust (classes are hardcoded
  per section, not computed), so the palette itself needed no changes.
  Reason: client-requested reorder. Sections before گزارشات (hero,
  تازه‌ها/`home_latest_heading`, مقالات) and after موضوعات (کتابخانه,
  اطلاعیه‌ها) are unaffected, left in their existing positions.
  Approved by: Farhad, in this session (2026-08-24).

## 2026-08-24 — Phase C (topic taxonomy migration)
- **Changed:** `topic` taxonomy migrated from six terms to the client's
  nine-term list, in this exact order: جهان، افغانستان، کارگری، زنان،
  سیاست، اقتصاد، علم و هنر، جنبش کمونیستی بین‌المللی، جنبش چپ
  افغانستان. Full read-only audit performed first (post counts, every
  call site querying `topic` by slug/term-ID, confirmation `collection`
  term_id 12 — journeying the same name, جنبش بین‌المللی, for the
  library — is a fully distinct term record from the old `topic`
  term_id 7 of the same name); DB backup taken via direct `mysqldump`
  invocation (`wp db export` silently defaulted to the wrong MySQL
  port for this LocalWP site — the site's actual port only exists in
  `php.ini`, not something the standalone `mysqldump`/`mysql` binaries
  know) before any write.
  - Deleted `سلامت و روان` (term_id 26, 0 posts, confirmed empty
    immediately before deletion).
  - `گزارشات` (term_id 42) was a placeholder term, never part of the
    client's real taxonomy. Its 6 posts (all of which already carried
    a real topic term alongside گزارشات) were each given one
    additional term from the new nine-term set, round-robin, before
    گزارشات was deleted — two of the six (posts 1 and 55) coincided
    with a term they already had (افغانستان, اقتصاد respectively), a
    harmless no-op given "no need to match by content/topic" was the
    explicit instruction.
  - Created the five missing terms (کارگری/`labor`,
    سیاست/`politics`, علم و هنر/`science-and-art` — this one also
    resolves a pre-existing code/DB mismatch flagged in the audit: the
    slug was hardcoded in three files but the term didn't exist in the
    DB, جنبش کمونیستی بین‌المللی/`international-communist-movement`,
    جنبش چپ افغانستان/`afghanistan-left-movement`). Slugs weren't
    specified by the client; chosen to match the existing English-slug
    convention, flagged for review.
  - جنبش بین‌المللی (`topic`, term_id 7) migrated to جنبش کمونیستی
    بین‌المللی (new term_id 46): post 25 reassigned, old term then
    deleted. `collection` term_id 12 (same name, library-side)
    confirmed untouched throughout — verified both before and after
    the topic-side deletion.
  - Term display order (`shola_get_topic_slugs_ordered()` reads the
    `menu_topics` nav menu, not a DB term-order field — found the menu
    was already out of sync with code, missing 2 of the original 6
    items) rebuilt from scratch with all 9 terms in the client's exact
    order.
  - `wp rewrite flush` run after term creation — new terms 404'd on
    their `/topics/{slug}/` archive until rewrite rules regenerated;
    not a data change, standard post-taxonomy-change maintenance.
  - Three guarded/idempotent hardcoded fallback arrays updated to the
    new 9-slug set (`class-taxonomies.php`'s `create_default_terms()`,
    `inc/setup.php`'s `shola_maybe_seed_nav_menus()`, `inc/
    template-tags.php`'s `shola_get_topic_slugs_ordered()` fallback) —
    code hygiene only, none of the three re-fire on this live site
    (all guarded by an already-set option/existing terms), so this is
    not a functional change to the live site.
  All 11 call sites from the audit re-verified live post-migration
  (homepage sections, taxonomy archives — both populated and 0-post
  terms — single.php breadcrumb/badge, search results, footer's
  curated fallback subset, `filter_post_permalink()`'s generated
  URLs, admin metabox term dropdown already self-updating by design).
  Approved by: Farhad, in this session (2026-08-24).

- **Removed:** The گزارشات homepage section (`front-page.php`) removed
  entirely — heading, view-all link, card grid markup, the
  `$reports_term`/`$reports_query` block including the
  `get_term_by('name', 'گزارشات', 'topic')` call that the term
  deletion above left permanently returning false, and its
  contribution to مقالات's post-exclusion pool. No commented-out code
  left behind. Confirmed no other file (header.php's popup menu,
  `menu_sections`, etc.) referenced this section.
  Background-band alternation broke as a direct result (مقالات
  `.sect-cream` became adjacent to شمارهٔ جاری `.sect-cream`, previously
  separated by گزارشات's paper band) — fixed by reverting شمارهٔ جاری
  from `.sect-cream` to plain/paper, since its cream banding was
  originally justified specifically by گزارشات's now-gone paper band
  (see the Phase A entry above). New sequence, live-verified via DOM:
  تازه‌ها (paper) → مقالات (cream) → شمارهٔ جاری (paper) → انتشارات حزب
  (tint) → موضوعات (paper) — no adjacent repeats at either touched
  boundary; palette itself unchanged, only which existing class
  applies to شمارهٔ جاری.
  Approved by: Farhad, in this session (2026-08-24).

- **Added:** New brand token `--cinder-red` (`#330A0A`), "Cinder Red",
  same `:root` sub-block as Winston Red. New utility class
  `.menu-topic--c7` (`color: var(--cinder-red)`) added alongside the
  existing hardcoded `.menu-topic--c1..c6` — this one uses the CSS
  custom property rather than a bare hex, matching Winston Red's
  precedent rather than the older classes' pattern.
  Reason: the Phase C topic migration grew `topic` from 6 to 9 terms,
  but only 6 crimson-family shades existed for
  `shola_topic_color_class()` — 4 of the 9 slugs (`labor`, `politics`,
  `international-communist-movement`, `afghanistan-left-movement`)
  were silently collapsing onto the same fallback shade (`c1`).
  Originally proposed 3 new shades for review; Farhad approved a
  cheaper 2-shade path instead — reassign the orphaned `c5` (its
  original owner, `international-movement`, was deleted in this same
  migration, leaving the shade unused) rather than minting a shade for
  every gap, and add only one genuinely new shade. One of the three
  originally-proposed shades ("Vanguard Red", `#B23535`) was dropped
  entirely on Farhad's explicit call, specifically because it was
  flagged as risking a drift toward pink — no reason to accept that
  risk once a cheaper path existed.
  Final mapping — `economy` → c1, `world` → c2, `afghanistan` → c3,
  `women` → c4, `politics` → c5 (reassigned from the orphaned slot),
  `science-and-art` → c6, `international-communist-movement` → c7
  (Cinder Red, new), `labor` → c1 (deliberate reuse, shared with
  economy — the other "material" topic, as opposed to the
  movement/politics topics on the newer/reassigned shades; color here
  is a secondary accent, topic-name text is the primary identifier),
  `afghanistan-left-movement` → unmapped/falls back to c1 (0 posts,
  left for later per the original proposal). Live DOM-verified in the
  rendered popup menu: all 9 topics show a real, correctly-resolved
  color (Cinder Red's `var(--cinder-red)` computed to `rgb(51,10,10)`
  = `#330A0A` exactly).
  Approved by: Farhad, in this session (2026-08-24).

## 2026-08-25 — Phase B (homepage restructure)
- **Changed:** `front-page.php` restructured to the client-specified
  section order: تازه‌ها → مقالات → گزارش → اسناد حزب → شمارهٔ جاری →
  انتشارات حزب → موضوعات. اطلاعیه‌ها removed from the homepage
  entirely (inline markup deleted, no shared partial); the
  `announcement` CPT, `archive-announcement.php`, and the masthead nav
  link to `/announcements/` are untouched — a homepage-section removal
  only, not a content-type removal, confirmed both by grep and live.
  Background-band alternation re-verified at every step of the
  restructure (paper → cream → paper → cream → paper → tint → paper,
  zero adjacent repeats) — one existing section's background class
  changed (شمارهٔ جاری: `.sect-cream` → plain) to keep it intact, no
  new palette colors.
  Investigated rather than assumed the "شمارهٔ جاری و کتابخانه"
  combined-section question raised going into this phase: the two were
  already independent `<section>` elements in the template — the
  combined aria-label was stale copy, not a structural coupling. No
  split was needed; corrected the label to just "شمارهٔ جاری."
  Approved by: Farhad, across this session (2026-08-24–25).

- **Changed:** `issue-card.php` generalized to accept both `issue` and
  `document` CPTs (previously issue-only in its docblock, though every
  field it actually rendered — featured image, title, date — was
  already post-type-agnostic, so no conditional-by-type logic was
  needed, only the docblock/variable naming). انتشارات حزب's own
  rendering re-verified byte-for-byte unaffected: identical markup,
  same 8 cards, same `.issue-grid` wrapper, same responsive column
  count as اسناد حزب at 375px — confirmed live, desktop and mobile.

- **Changed:** اسناد حزب (was "تازه‌ترین اسناد") restyled from the
  `document-row.php` list partial onto the generalized `issue-card.php`
  + `.issue-grid`, matching انتشارات حزب's anatomy. Query unchanged
  (`post_type => document`, `posts_per_page => 4`, no collection
  restriction) — rendering only.
  Label split: `latest_documents_heading` is a key shared with
  `page-library.php`'s own heading, which covers the whole library
  (آثار کلاسیک/اسناد حزب/نقد و پلمیک/جنبش بین‌المللی), not just party
  documents — renaming the shared key to "اسناد حزب" would have
  silently mis-labeled that page too. Split into a new
  `home_latest_documents_heading` key (homepage only, = "اسناد حزب")
  while `latest_documents_heading` stays untouched for
  `page-library.php` (= "تازه‌ترین اسناد", unchanged). No DB-stored
  override existed for the original key (confirmed via `wp option get`
  before changing anything, same discipline as Phase A's A3).

- **Added:** گزارش homepage section, `card.php` anatomy (full article
  cards, dek/byline — same as مقالات, since these are normal posts).
  Hidden entirely when empty (no heading, no empty grid) — the same
  `have_posts()` guard every other homepage section already uses, not
  a new pattern.
  **Architectural finding, `category` vs. `post_tag`:** originally
  built on WP core's `category` taxonomy per the initial brief. Found
  that `post` had `category` deliberately disconnected from it in an
  earlier phase (`SholaCore\Class_Taxonomies::remove_core_category_from_post()`,
  predating this phase) specifically to avoid a redundant
  "Uncategorized" editor panel — creating a `category` term for
  `post`-type content re-triggered exactly that conflict: no admin UI
  to assign it (block editor's taxonomy panel and the classic metabox
  both stay hidden regardless of the term existing), and WordPress's
  default term-count updater (`_update_post_term_count()`) silently
  excludes `post` from `category` counts, so the term's `count` read 0
  permanently even with a real, verified post assignment (proven via
  `wp_set_object_terms()` + `WP_Query`, which bypass the object-type
  check that WP-CLI's own `post term` commands enforce more strictly).
  Reversing the disconnect would have restored the *entire* category
  UI for every article editor, not just گزارش — a bigger change than
  this one section warranted without sign-off, so it wasn't done
  silently.
  Rebuilt on `post_tag` instead: confirmed via `is_object_in_taxonomy()`
  it's still fully registered for `post` (unmodified, no other code
  disables it), already actively used and rendered as visible tag
  chips on `single.php`, normal Add-New-Tag editor UI, and term counts
  track correctly (verified 0 → 1 → 0 across a real create/delete
  cycle). The original `category` term (term_id 48) was deleted; a
  `post_tag` term گزارش (slug `reports`, matching the project's
  clean-English-slug convention rather than an auto-generated
  Persian-encoded one) was created and seeded idempotently in
  `create_default_terms()` — deliberately added there rather than left
  as a one-off `wp-cli` term, to avoid repeating the exact
  `science-and-art` gap Phase C found (a slug referenced in code but
  never actually seeded in the DB).
  Verified end-to-end with a temporary post (WP-CLI-created, tagged via
  `--tags_input`, not clicked through the real wp-admin UI — a
  browser-based auth-cookie injection to prove the click-through was
  attempted and blocked by this environment's own permission
  guardrails on credential/session actions; Farhad opted to skip that
  specific proof rather than share real admin credentials, given the
  underlying mechanism — tag registration, query, term counting — was
  already independently confirmed): homepage picked it up correctly in
  the right position with the right background, the tag chip rendered
  correctly on the post's own single view, and the term count tracked
  0 → 1 → 0 correctly across creation and deletion. No test or
  verification content left in the database afterward.
  Approved by: Farhad, across this session (2026-08-24–25).

## 2026-08-26 — Phase D, Step 3 (organization rename: شعله جاوید → حزب کمونیست (مائوئیست) افغانستان)
- **Changed:** `publication` term_id 9 renamed شعله جاوید → حزب کمونیست
  (مائوئیست) افغانستان (`wp term update publication 9 --name=...`).
  **Slug deliberately left unchanged** (`shola-jawid`) — a considered
  decision, not an oversight: this is a live public site
  (`sholajawid.com`) with 7 issues already published under
  `/publications/shola-jawid/...` URLs that may already be
  indexed/shared; changing the slug would break all 7 without a
  redirect layer this step doesn't build. URL stability was chosen
  over cosmetic slug/name matching. Live-verified after rename, not
  assumed: `shola_publication_status_label()`
  (`inc/template-tags.php:341-343`) keys off the *slug*
  (`'shola-jawid' === $slug`), not the name — since the slug is
  unchanged, both the renamed publication's "جاری" (current) badge and
  جهان برای فتح's "آرشیوی" (archived) badge continued to render
  correctly with zero code change needed, confirmed on نشرات, the
  نشریات popup-menu switcher, and single-issue.php. Also confirmed
  live: 2 of the 7 issue permalinks resolve unchanged at their
  original `/publications/shola-jawid/...` paths — no broken links, no
  redirects needed. No name-based term lookup exists anywhere for
  `publication` (confirmed in Step 2's audit, re-confirmed here) — a
  plain rename propagated correctly everywhere without the گزارشات-
  style fragility Phase C hit.
  Deliberately out of scope, confirmed untouched: the 7 issue posts'
  own titles (still prefixed "شعله جاوید · شمارهٔ …" — Farhad's to
  edit directly in wp-admin if wanted), the About WP Page's own title
  (post ID 73) and its two body paragraphs (same), theme/plugin/repo
  internal identity ("Shola Jawid" in `style.css`/`shola-core.php`/
  folder names/git repo, permanently locked per CLAUDE.md §0), and the
  domain (`sholajawid.com`).
  Approved by: Farhad, across this session (2026-08-25–26).

- **Changed:** Two hardcoded visitor-facing strings updated (found in
  Phase D Step 1's audit — neither pulls from `bloginfo()` or the
  renamed term, both literal strings in template files):
  `page-about.php`'s `<h1>` ("دربارهٔ شعله جاوید" → "دربارهٔ حزب
  کمونیست (مائوئیست) افغانستان") and `page-publications.php`'s dek
  paragraph (same substitution within the existing sentence
  structure, جهان برای فتح reference and sentence shape unchanged).
  Approved by: Farhad, across this session (2026-08-25–26).

- **Changed:** `blogname` option updated directly via
  `wp option update blogname` (already in-session, WP-CLI access
  established) to حزب کمونیست (مائوئیست) افغانستان — per Phase D Step
  1's finding, this alone propagates the new name to the masthead,
  browser `<title>` (`wp_get_document_title()`), all `og:*`/meta tags
  (`class-seo.php`), and the footer, with zero code change, confirmed
  live across desktop and mobile.
  `blogdescription` (the tagline, used only as the meta-description/
  `og:description` fallback per Phase D Step 1's finding — not
  rendered as visible on-page UI anywhere) still references شعله
  جاوید as of this entry. Proposed replacement text (mirroring the
  same substitution pattern approved above for
  page-publications.php's sentence) flagged for Farhad's approval
  before being applied, rather than guessed and shipped — see the
  session report for the exact proposed wording.

## 2026-08-26 — Phase D, Step 4 (blogdescription applied)
- **Changed:** The `blogdescription` replacement text proposed in the
  entry above ("Phase D, Step 3") was approved by Farhad and applied
  via `wp option update blogdescription`. DB-only option change, no
  file diff, nothing to commit — this entry is the record of it, per
  this project's existing convention of logging `wp_options` changes
  inline in CHANGELOG.md rather than treating them as somehow outside
  its scope (see the `blogname` entry above, and the earlier
  `shcore_label_overrides` DB-state notes elsewhere in this file).
  Live-verified across all three surfaces Step 1 identified for this
  option — browser `<title>` tag and the meta-description/
  `og:description` fallback on the front page, search results, and
  404 — desktop and mobile, both old-name-free, zero console errors.
  Approved by: Farhad, in this session (2026-08-26).

## 2026-08-27 — Popup-menu topic label size fix; version bump
- **Changed:** `.menu-topic` font-size reduced a further ~40%
  (`clamp(1.7rem, 4.2vw, 2.45rem)` → `clamp(1rem, 2.5vw, 1.45rem)`),
  same rule, `assets/css/main.css`. The 2026-08-08 reduction had
  anticipated the popup Topics list growing past a fixed 6 items, but
  by 9 items (post-Phase-C) the labels were still oversized and hard
  to scan on the live site — confirmed from Farhad's own screenshot.
  Live-verified both breakpoints: 23.2px desktop (was 39.2px, ~41%
  reduction — within the requested 30-50% range), 16px mobile (375px).
  Approved by: Farhad, in this session (2026-08-27).

- **Changed:** Theme (`style.css`) and plugin (`shola-core.php`,
  `SHCORE_VERSION`) version bumped 1.0.0 → 1.0.1 — both had sat at
  1.0.0 (the git v1.0.0 tag's version) through all of Phases A-D
  despite real CSS/JS changes shipping in each. `wp_enqueue_style()`/
  `wp_enqueue_script()` use this version as the asset URL's cache-
  busting query string (`main.css?ver=1.0.0`); with it never changing,
  browsers and the production host's server-side cache had no signal
  to fetch a fresh copy after deploy — confirmed as the cause of a
  live-site report ("colors not updating") that was resolved by
  manually purging Hostinger's cache. Bumping the version fixes this
  going forward without requiring a manual cache purge on every
  future deploy.
  Approved by: Farhad, in this session (2026-08-27).

## 2026-08-27 — Video-guide internal admin tool
- **Added:** `SholaCore\Video_Guide` (`includes/class-video-guide.php`)
  — a private, admin-only list of Farhad's unlisted YouTube tutorial
  videos for the client (dashboard walkthrough, publishing, etc.),
  registered under Settings → راهنمای ویدیویی. wp-admin only: no
  shortcode, no front-end template, no public menu/sitemap/search
  exposure of any kind.
  Same shape as `Label_Settings`/`Social_Links_Settings`/
  `Contact_Settings` — one option (`shcore_video_guide_entries`,
  native PHP array, not the JSON string originally suggested — kept
  consistent with how every other option in this plugin is stored),
  one settings page, one `sanitize_callback`, registered via the
  Settings API (`register_setting()`/`settings_fields()`), which
  already supplies the nonce + capability + sanitize hygiene a
  hand-rolled POST handler would have had to reimplement — chosen
  over the hand-rolled form the initial brief described, per its own
  "match existing project conventions" instruction.
  `manage_options` capability throughout (page registration and the
  render-time `current_user_can()` guard) — confirmed live via
  `wp eval` that Editor and Author both lack this capability while
  Administrator has it, not assumed from memory.
  Bulk-edited as plain text, one entry per line
  (`عنوان ویدیو | آدرس یوتیوب`), with an optional `## بخش` line to
  start a labeled section for everything after it until the next
  `##` or end of text — unlabeled entries (the expected starting
  state, zero entries today) render as a flat list. Malformed lines
  (no `|` separator, or an empty title/URL after sanitizing) are
  silently dropped rather than stored broken. Verified end-to-end via
  `wp eval` against the real `sanitize_entries()`/`get_entries()`
  methods (not just read from code): valid multi-section input parsed
  and grouped correctly, two deliberately-malformed lines correctly
  dropped, saved option read back correctly, and the bulk-edit
  textarea's reverse formatting (`entries_to_text()`) reconstructed
  the original input correctly — a real save/reload round-trip, not
  assumed.
  **Menu placement flagged, not assumed final:** every existing
  shola-core settings screen uses `add_options_page()` (Settings
  submenu) — matched that here for consistency, per the task's own
  "your call" discretion. This directly conflicts with the same
  task's separate ask for a dashicon (`dashicons-video-alt3`):
  Settings-submenu items cannot show an icon in wp-admin at all, only
  top-level `add_menu_page()` entries can. No icon was added. Farhad
  to confirm this placement is fine, or that a top-level menu (with
  icon) is worth breaking from the plugin's otherwise-uniform
  Settings-submenu pattern for.
  **Not verified via direct wp-admin click-through** — `add_options_page()`
  doesn't register correctly when simulated through `wp eval`/CLI
  context (confirmed this is a WP-CLI limitation, not a bug in this
  class specifically, by running the identical test against the
  already-live `Label_Settings::add_settings_page()` and getting the
  same non-registration result) — and, per the precedent set earlier
  this session (Phase B's گزارش tag verification), a browser-based
  wp-admin auth-cookie injection is blocked by this environment's own
  permission guardrails on credential/session actions. The underlying
  save/render logic was independently verified instead (above); the
  actual wp-admin page render/click-through is Farhad's to confirm.
  Approved by: Farhad, in this session (2026-08-27).

## 2026-08-27 — Video-guide thumbnail grid + inline player
- **Changed:** The راهنمای ویدیویی settings page's plain link list
  replaced with a click-to-play thumbnail grid. Data entry unchanged —
  still the same `عنوان ویدیو | آدرس` bulk-edit textarea from the
  initial rollout; only the rendering changed.
  New `Video_Guide::get_video_id()` extracts the 11-character YouTube
  ID from a stored URL (watch?v=, youtu.be/, /embed/, /shorts/,
  /live/, any domain/scheme, extra query params before or after) —
  live-tested via `wp eval` against 14 cases including malformed/
  non-YouTube/playlist-only URLs, all passing, before relying on it
  for rendering. Not exhaustive by design: a URL `get_video_id()`
  can't parse falls back to a plain "باز کردن در یوتیوب" link instead
  of a broken thumbnail — flagged in the docblock rather than trying
  to silently handle every conceivable URL shape.
  Thumbnails come from YouTube's own static-image CDN
  (`img.youtube.com/vi/{id}/hqdefault.jpg`) derived from the ID at
  render time — no stored thumbnail field, nothing to fall out of
  sync with the URL, no API key.
  Clicking a thumbnail (`admin/js/video-guide.js`, vanilla JS, no
  jQuery dependency — a single delegated click handler didn't
  warrant one) swaps it in place for a `youtube-nocookie.com` iframe
  embed (privacy-enhanced mode — no tracking cookies set until the
  viewer actually presses play), autoplaying on the same user
  gesture that triggered the swap. New admin-only assets
  (`admin/css/video-guide.css`, `admin/js/video-guide.js`), enqueued
  only on this settings screen (`admin_enqueue_scripts`, gated on the
  exact hook suffix `settings_page_shcore-video-guide` — same gating
  pattern `Meta_Fields::enqueue_admin_assets()` already uses for its
  own screen, though this exact hook-suffix value is standard
  WordPress behavior for `add_options_page()`, not independently
  runtime-verified this session — flagged, not assumed silently).
  **Requires the videos to be YouTube-"Unlisted," not "Private"** —
  confirmed with Farhad before building: YouTube's thumbnail CDN and
  embed player only work for a Private video for the specific Google
  accounts individually authorized on that exact video, which has no
  relationship to this site's own login system. Farhad to switch
  existing videos' visibility in YouTube Studio before this goes
  live; nothing on the WordPress side can substitute for that.
  Full render output verified end-to-end via `wp eval`
  (`wp_set_current_user()` + direct `render_settings_page()` call,
  output-buffered and checked) — not just read from code — confirming
  the thumbnail `<img>`/`data-video-id` markup, the no-ID fallback
  link, section headings, and the grid wrapper class all render
  correctly from real saved entries.
  Approved by: Farhad, in this session (2026-08-27).

## 2026-08-28 — Bookmarkable /video-guide front-end route
- **Added:** `/video-guide` — a front-end URL for the same thumbnail
  grid as Settings → راهنمای ویدیویی, so Farhad can bookmark/share a
  direct link instead of navigating through wp-admin every time.
  Registered as a custom rewrite rule + query var + `template_redirect`
  gate (`Video_Guide::register_rewrite()`/`register_query_var()`/
  `maybe_render_front_end_page()`), not a real WP Page/post — combining
  two mechanisms that already existed independently elsewhere in this
  plugin rather than introducing a new pattern:
  `add_rewrite_rule()` (the same mechanism
  `Taxonomies::register_topic_rewrite()` already uses for
  `/topics/{topic}/{slug}`) and the `template_redirect` +
  `query_vars`-filter combination `View_Counter` already uses for its
  own front-end interception. `shcore_activate()` (`shola-core.php`)
  now also calls `Video_Guide::register_rewrite()` before its existing
  `flush_rewrite_rules()` call, so the route survives a fresh
  deploy/reactivation rather than depending on a one-time manual flush
  — this local site's already-active install still needed one manual
  `wp rewrite flush` to pick up the new rule immediately (same
  situation Phase C hit creating new taxonomy terms), which is
  expected and doesn't affect a fresh production activation.
  Refactored the thumbnail-grid markup out of `render_settings_page()`
  into a new shared `render_grid( $entries )` method, called by both
  the wp-admin screen and the new front-end route, so a future
  styling/parsing change only has to happen once.
  **Real capability check, not an unguessable-URL approach** — every
  request checks `is_user_logged_in() && current_user_can('manage_options')`;
  anyone else gets `wp_safe_redirect( wp_login_url( $current_url ) )`
  and an immediate `exit`, with zero page content rendered first.
  Live-verified via raw HTTP requests (not assumed from reading the
  code): a logged-out request to `/video-guide/` returns a 302 to
  `/wp-login.php?redirect_to=...%2Fvideo-guide%2F` with a **0-byte**
  response body; a temporary Editor-role test account (created and
  deleted for this check) gets the identical 302/0-byte result; the
  real Administrator account gets a 200 with the correct standalone
  page — confirmed `dir="rtl"`/`lang="fa-IR"` (from `language_attributes()`),
  the `noindex, nofollow` meta tag, correctly cache-busted CSS/JS
  asset URLs, and — with a real entry temporarily saved and removed
  afterward — the exact same thumbnail/`data-video-id` markup the
  admin-page grid produces, confirming the shared `render_grid()`
  method is genuinely shared, not diverged copies.
  Deliberately does not call `get_header()`/`get_footer()` or
  otherwise route through the public theme — a minimal standalone
  HTML shell reusing the existing admin CSS/JS as-is (plain CSS,
  dependency-free vanilla JS, nothing wp-admin-specific in either).
  Not linked from any menu, sitemap, or public navigation.
  Approved by: Farhad, in this session (2026-08-28).

## 2026-08-28 — Video-guide feature: manual verification complete, ready to deploy
- **Confirmed:** Farhad manually verified the full video-guide feature
  end-to-end through an actual browser session — the wp-admin
  thumbnail grid, the `/video-guide` front-end route, and the full
  login/redirect flow — closing the one open gap noted in the
  2026-08-27 "Video-guide internal admin tool" entry above (this
  session's own tooling couldn't drive a real wp-admin click-through;
  Farhad's manual pass covers exactly that). The Settings-submenu
  placement question raised in that same entry (vs. a top-level menu
  with a dashicon) is resolved as-is — no change requested.
  Approved by: Farhad, in this session (2026-08-28).

## 2026-08-30 — Video-guide restyle: brand colors, RTL, YouTube-style cards
- **Changed:** Full visual restyle of both video-guide surfaces
  (wp-admin Settings → راهنمای ویدیویی and the /video-guide front-end
  route — both call the same render_grid() and now load the same
  video-guide.css, so they are styled identically by construction, not
  by copy-pasting the same values twice). Markup/CSS only — none of
  sanitize_entries(), get_video_id(), get_thumbnail_url(), or
  entries_to_text() were touched.

  Crimson value confirmed from source, not assumed: grepped main.css's
  :root block directly. --crimson is #8E1B1B, --crimson-deep #6B1414,
  --crimson-tint #F5DCDC — all three match the documented brand guide
  (docs/IA-reference/04_Shola_Jawid_Brand_Guidelines_v1.0.html)
  exactly, zero drift. The #CC0000 Farhad reported seeing is a real,
  different, deliberately separate token (--winston-red, Phase A,
  scoped only to the masthead background) — not a drifted --crimson.
  Used the confirmed real --crimson (#8E1B1B) throughout this restyle,
  not #CC0000. --maroon (#4A0E0E) doesn't appear in either brand-guide
  document at all — not "drifted" (there's no documented value to
  drift from), just an implementation-only token from the original
  theme build, flagged separately as instructed rather than silently
  treated as an error.

  Typeface substitution, flagged rather than silently made: the brief
  specified "Vazirmatn," which doesn't exist anywhere in this project
  — no font files, no @font-face, zero references anywhere in the
  codebase (confirmed by grep, not assumed) — almost certainly a
  holdover from CLAUDE.md's original, superseded font plan (the theme
  shipped on Farhang2/ModamPro instead, per an existing in-code
  comment and an earlier "correct font list in v1.0.0 release notes"
  fix). Used the theme's real self-hosted Persian typeface, Farhang2,
  which already has every weight the spec calls for (800 ExtraBold,
  600 DemiBold i.e. semibold, 400 Regular) — same font files main.css
  already loads for the public theme, referenced via
  Video_Guide::get_font_face_css() rather than duplicated. That method
  builds the @font-face rules with get_theme_file_uri() absolute URLs
  rather than a hand-written relative path in the static CSS file — a
  relative url() from wp-content/plugins/shola-core/admin/css/ across
  into wp-content/themes/shola-jawid/assets/fonts/ is exactly the kind
  of thing that's easy to get subtly wrong (an early draft of this had
  precisely that off-by-one-directory-level bug, caught before it
  shipped) and hard to verify without live-loading the page. Injected
  as inline CSS: wp_add_inline_style() on the admin screen, a <style>
  tag on the front-end route.

  Header bar applied to the front-end route only, not the wp-admin
  screen — a deliberate scope decision, not an oversight: the new
  crimson header bar with the maroon 2px accent line (the same pattern
  .masthead's own border-bottom: 2px solid var(--maroon) in main.css
  uses) replaces the plain intro on /video-guide, which has no other
  page chrome at all. The wp-admin settings screen keeps WordPress's
  own native h1/intro styling, matching every other shola-core
  settings screen's convention of fitting into wp-admin's UI rather
  than fighting it with custom-colored chrome — this is scoped to the
  header/chrome only; the grid/card styling below it is 100% identical
  on both surfaces via the shared CSS file, confirmed no extra effort
  was needed to keep those in sync.

  RTL verified empirically, not just via the dir attribute: html
  dir="rtl" lang="fa-IR" (from language_attributes()) is at the real
  document root on the front-end route — confirmed via a real HTTP
  request. Card grid uses no direction/order override, relying on CSS
  Grid's own RTL-aware auto-placement; verified in a live browser test
  (a self-contained local copy of the real CSS, built to sidestep both
  this environment's blocked live-session auth and a
  file://-to-http:// cross-origin limitation hit along the way) that
  the first card in DOM order is genuinely the rightmost card on
  screen at desktop width (four cards' x-positions strictly
  decreasing left-to-right: 947 -> 653 -> 359 -> 159px), and that real
  Tab-key keyboard navigation visits elements in that same
  right-to-left order — not assumed from the presence of dir="rtl"
  alone. The play-button triangle icon is mirrored to point start-ward
  (left under RTL) rather than reused unmirrored from an LTR set.

  Card structure: 16:9 thumbnail (padding-block-start: 56.25%,
  object-fit: cover), crimson-tinted play-button overlay (not
  YouTube's default red) visible by default under
  (hover: none), (pointer: coarse) and fading in on :hover/
  :focus-visible otherwise — confirmed both states live, including
  that touch-emulation correctly triggers always-visible mode. Title
  is a 2-line -webkit-line-clamp with a fixed min-height so a long
  title (tested with a genuinely long one) doesn't change card
  height. A .shcore-video-meta slot exists in the markup for a future
  duration/date line — deliberately empty
  (:empty { display: none; }), no placeholder text, per instruction.

  Grid/responsive: repeat(auto-fill, minmax(240px, 1fr)), not a fixed
  column count, per the brief's explicit ask (main.css's own
  .issue-grid/.grid-cards elsewhere on the site use fixed breakpoint
  column counts instead — a deliberate difference here, not an
  inconsistency). Live-tested, real browser, not just read from the
  CSS: 1280px -> 4 columns, 768px (tablet) -> 2 columns, 480px and
  below (both 375px and 390px tested) -> forced single column via a
  max-width media query, full card width, zero horizontal overflow at
  any width tested. Inline-player click tested at 375px: the
  thumbnail's aspect-ratio box reserves identical space whether it
  holds the <img> or the swapped-in <iframe> — confirmed before/after
  heights byte-identical (175.15px) and page scroll height unchanged
  (1299px), zero layout jump.

  Focus/hover, one gap flagged rather than overclaimed: the
  element-level :focus-visible effect (2px crimson outline) is
  confirmed working via a real Tab keypress in a live browser — the
  outline color read back as rgb(142, 27, 27) after tabbing, and
  :focus-visible matching was independently confirmed via both
  .matches() and a live querySelector against the actual stylesheet
  rule. The play-button-appears-on-focus half of the same interaction
  (.shcore-video-thumb:focus-visible::after) is present as a standard,
  correctly-specified CSS rule (verified in the live CSSOM, correct
  selector and specificity, identical technique to the
  already-confirmed :hover::after variant) but this automation
  environment's getComputedStyle() did not reflect the opacity change
  on the ::after pseudo-element specifically when queried under
  simulated keyboard focus — flagged as an unresolved verification gap
  in this environment rather than either claimed as fully confirmed or
  hidden. Worth Farhad's own quick real-browser check (Tab to a
  thumbnail, confirm the play button appears) before treating this one
  specific interaction as done.

  Empty state: already existed from earlier work ("هنوز ویدیویی
  افزوده نشده است.") — checked before assuming it needed adding, per
  instruction; only its CSS class changed (.shcore-vg-empty), the
  Persian copy itself is unchanged.
  Approved by: Farhad, in this session (2026-08-30).

- **Changed:** Replaced the wp-admin settings screen's data-entry
  mechanism entirely, per Farhad's direct feedback after trying the
  restyled page live: the free-text bulk-edit textarea
  ("عنوان ویدیو | آدرس یوتیوب" per line) was hard to hand-edit
  correctly, and the thumbnail-grid preview duplicated on this screen
  (on top of the dedicated /video-guide watching page) was showing
  mismatched/broken-looking thumbnails in some browser states and
  added confusion rather than value.
  Removed the render_grid() preview from render_settings_page()
  entirely — this screen is now purely the "adding/editing" area
  (paired fields + the "مشاهدهٔ ویدیوها" link out to /video-guide, the
  "watching" area); render_grid() itself is untouched and still used
  by /video-guide.
  Replaced the textarea with a real repeater: one row per video, a
  plain-text "عنوان ویدیو" field and a `url`-type field side by side,
  a "حذف" button per row, and an "افزودن ویدیوی دیگر" button
  (video-guide-admin.js, admin-only — the front-end route has no form
  so it doesn't enqueue this file) that clones a `<template>` row with
  a fresh numeric index. Rows don't need to stay contiguous after a
  removal; sanitize_entries() (rewritten to accept the array-of-pairs
  shape the Settings API now hands it, instead of parsing a textarea
  string) iterates whatever indices are present. The old format's
  `##`-prefixed section-header line has no equivalent in this UI —
  every entry saved through it now gets `section => ''`
  (render_grid() still groups by `section` when non-empty, so a
  section set some other way, e.g. directly in the database, would
  still display correctly; there's just no UI to set one through
  anymore). entries_to_text() (the old textarea pre-fill helper) was
  removed as dead code rather than left orphaned.
  Verified via `wp eval` against the real methods, not just read from
  code: sanitize_entries() correctly parses a realistic submission
  (two valid rows plus one deliberately empty row from clicking "Add"
  without filling it in, whitespace included) — the empty row is
  dropped, the two valid ones saved and trimmed correctly;
  render_settings_page()'s output confirmed to contain zero grid/
  thumbnail markup and correctly pre-fill both saved rows' fields;
  render_grid() on /video-guide (a real HTTP request, not eval)
  confirmed still renders both entries with correct thumbnails,
  proving the two screens' data flow is genuinely decoupled at the
  UI level while still sharing the same underlying option/entries.
  Approved by: Farhad, in this session (2026-08-30).

- **Changed:** Plugin version bumped 1.0.1 → 1.0.2 (`shola-core.php`,
  `SHCORE_VERSION`). Same root cause as the earlier theme/plugin
  1.0.0 → 1.0.1 bump (2026-08-27): `SHCORE_VERSION` is the cache-
  busting query string on `video-guide.css`/`video-guide.js`/
  `video-guide-admin.js`, and it stayed at 1.0.1 through this entire
  session's restyle and repeater-UI work — so after deploying to
  production, Hostinger's server-side cache kept serving the *old,
  unstyled* `video-guide.css` from before any of today's changes
  (`?ver=1.0.1` never changed, so nothing signaled a re-fetch), even
  though the new file was genuinely present on the server. Farhad
  confirmed exactly this symptom live: styling worked on local but
  "the whole style of the page" was missing after deploying the same
  files to production. This bump — and remembering to do it on every
  deploy that touches CSS/JS going forward — is the actual fix; a
  manual Hostinger cache purge (as done for the earlier occurrence)
  is the one-time unblock for whichever version is already stuck
  cached right now.
  Approved by: Farhad, in this session (2026-08-30).

- **Changed:** `/video-guide` — a deliberate, explicit reversal of
  this route's original access-control design, per Farhad's direct
  request so he can share the page with people who don't have a
  WordPress account on this site. Previously: any non-Administrator
  (logged out or not) was redirected to `wp-login.php`, no exceptions
  — "a real capability check, not an unguessable-URL approach." Now:
  a logged-in Administrator still gets straight through unchanged;
  anyone else sees a small password form instead of a redirect.
  Mechanism: shared password stored in a new option
  (`shcore_video_guide_password`, plain text — same convention
  WordPress core itself uses for password-protected posts'
  `post_password` column, an appropriate standard for a casual shared
  secret, not a real user account). A correct submission sets an
  unlock cookie whose value is an HMAC of the *current* password
  keyed with one of WordPress's own secret salts
  (`wp_salt('auth')`) — deliberately not the password itself, so the
  cookie never discloses it even if intercepted, and changing the
  password automatically invalidates every previously-issued cookie
  with no separate revocation list needed. `hash_equals()` used for
  both the password check and the cookie check (timing-safe
  comparison, not `===`). No password configured
  (`get_password()` returns `''`) means the gate always fails — a
  fresh install/state stays admin-only exactly as this route
  originally shipped, nobody accidentally ships with an open door.
  **Accepted limitation, not overlooked:** failed password attempts
  on this route aren't separately rate-limited — Wordfence's
  brute-force protection (per CLAUDE.md §3/§6) is scoped to
  `wp-login.php`, not custom routes like this one. Acceptable for a
  shared secret meant for short-term casual sharing, not a real
  account credential; worth reconsidering if this access model is
  ever made permanent rather than the temporary arrangement it's
  intended as today.
  Verified end-to-end via real HTTP requests with a cookie jar (not
  assumed from reading the code): a logged-out visitor with no
  cookie sees the password form (`200`, not a redirect); a wrong
  password re-shows the form with an error and sets no cookie; a
  correct password shows the real content and sets the unlock
  cookie; a subsequent visit with that cookie shows content directly
  with no form; changing the stored password correctly invalidates
  the old cookie (form reappears); and a logged-in Administrator
  still bypasses all of this exactly as before, with no password
  cookie at all. wp-admin's Settings → راهنمای ویدیویی screen is
  completely unaffected — still strictly `manage_options`, unchanged.
  Approved by: Farhad, in this session (2026-08-30).

- **Added:** "مشاهدهٔ ویدیوها" button on the wp-admin settings screen,
  linking to /video-guide (target="_blank"). Per Farhad's request:
  the Settings screen is the "adding/editing" area (grid preview +
  bulk-edit textarea), /video-guide is the "watching" area (clean
  grid, no edit form) — this button is the one-click bridge between
  them, rather than building a second, redundant watching page when
  /video-guide already served exactly that purpose.

## 2026-08-30 (later same day)
- **Fixed / Added:** The `/video-guide` password Farhad set locally
  (`guide@2026`, via `wp option update`) never took effect on
  production because deploying the plugin zip only copies code
  files — it never touches production's database, so the
  `shcore_video_guide_password` option simply didn't exist there.
  Farhad has no WP-CLI/SSH access to production to set it directly.
  Fixed by adding a plain, visible (not masked) text field — "رمز
  عبور صفحهٔ عمومی" — to the bottom of the same Settings →
  راهنمای ویدیویی screen, wired to the same `shcore_video_guide_settings`
  option group and saved through the same `options.php` form/nonce
  the video repeater already uses, so it needed no separate form,
  handler, or capability check. Deliberately `type="text"`, not
  `type="password"`: per Farhad's stated reason ("difficult to
  remember"), the whole point is that he can see and copy the
  current value, not have it hidden from himself. `sanitize_password()`
  (added to `register_setting()`) is `sanitize_text_field( trim(...) )`
  — same treatment every other plain settings field in this plugin
  gets. Saving a new password here immediately invalidates every
  previously-issued unlock cookie (per the existing HMAC-of-password
  design, unchanged) — expected and desirable.
  **Action required on Farhad's side after this deploys:** the
  production database still doesn't have this option set to
  anything — deploying the new field only adds the *ability* to set
  it from wp-admin. He must open Settings → راهنمای ویدیویی on the
  live site once, type the password into the new field, and click
  ذخیره — only then does /video-guide's password gate start working
  on production. The local LocalWP site's `guide@2026` value (set
  via WP-CLI, for local testing only) is a separate database and is
  not carried over by this or any future code deploy.
  Approved by: Farhad, in this session (2026-08-30) — explicit
  request: "make it an option in the video guide tab ... so that I
  can change the password of this specific page from there."
  Approved by: Farhad, in this session (2026-08-30).

## 2026-08-31
- **Fixed:** wp-admin's date pickers (classic-editor "انتشار" metabox
  and the block editor's schedule panel/sidebar) were showing Iranian
  Jalali month names (شهریور, فروردین, ...) while the public front-end
  already showed the correct Afghan Dari names (سنبله, حمل, ...) —
  Farhad flagged the mismatch directly (screenshots of both surfaces
  showing different month names for the same date). Root cause: the
  front-end fix, shola_convert_jalali_months_to_dari()
  (inc/template-tags.php, added 2026-08-08), is a PHP output filter on
  date_i18n()/wp_date()/etc.; wp-admin's date pickers are Persian
  Calendar's own client-rendered JavaScript widgets
  (assets/js/persian-calendar.js's PersianCalendar class, plus
  assets/js/gutenberg.js for the block editor), which never call those
  PHP functions, so the existing filter couldn't reach them.
  Investigated Persian Calendar's JS source directly (same "read the
  actual plugin, don't guess" approach already used for the PHP fix):
  found its 12 month-name strings live in one array, exposed globally
  as `window.PersianDateConverter.PERSIAN_MONTHS` — and confirmed
  every read site (the date-picker widget's own `<option>`/`<span>`
  rendering, gutenberg.js's schedule-button and inline-hint text)
  reads that exact array *by reference*, at the moment it renders, not
  a cached copy taken at load time.
  Added inc/admin-jalali-months.php + assets/js/admin-jalali-months.js
  (theme, alongside the existing front-end fix, for the same reason
  that one lives in the theme rather than shola-core — CLAUDE.md §2
  reserves shola-core for the content model, not
  presentation/integration glue): a small script, enqueued as an
  explicit dependency of Persian Calendar's own 'persian-calendar-main'
  handle (on both `admin_enqueue_scripts` and
  `enqueue_block_editor_assets`, since Persian Calendar itself splits
  classic-editor vs. block-editor loading across those two hooks, each
  guarded by `wp_script_is( 'persian-calendar-main', 'enqueued' )` so
  it only ever loads on a screen where Persian Calendar's own script
  already loads), that `splice()`s the Dari month names into that same
  array object in place. Because it mutates Persian Calendar's own
  array rather than reading page text or touching a plugin file, this
  is not a DOM-scraping/MutationObserver hack and — per Farhad's
  explicit requirement — **survives a Persian Calendar plugin update**:
  it depends only on the plugin's existing, functionally-required
  `window.PersianDateConverter.PERSIAN_MONTHS` global (gutenberg.js
  itself already depends on that same global for its own rendering, so
  an update can't drop it without breaking the plugin's own Gutenberg
  integration first) and never inspects or assumes anything about the
  Iranian strings themselves — no plugin file was edited.
  Verified two ways: (1) real request — fetched a logged-in wp-admin
  block-editor screen via curl with WP-CLI-generated auth cookies and
  confirmed `shola-admin-jalali-months-js` is correctly enqueued
  immediately after `persian-calendar-main-js` (and after
  `persian-calendar-gutenberg-js`) in the actual page's script list;
  (2) logic — ran the real, unmodified persian-calendar.js followed by
  the new override script under Node, confirmed
  `window.PersianDateConverter.PERSIAN_MONTHS` changes from the
  Iranian to the Dari list, and confirmed a `PERSIAN_MONTHS[jm - 1]`
  read (gutenberg.js's own access pattern) returns the Dari name
  afterward.
  Theme version bumped 1.0.1 → 1.0.2 (style.css) for cache-busting on
  the next deploy, per the "bump version on every deploy touching
  CSS/JS" lesson from the video-guide restyle work.
  Approved by: Farhad, in this session (2026-08-31).

- **Fixed:** after confirming the above fix worked, Farhad found a
  second, unrelated issue on the posts list's "ویرایش سریع" (Quick
  Edit) date field: it wasn't showing the wrong month-name variant, it
  wasn't showing Jalali at all — WordPress core's native Gregorian
  date fields were untouched (with Persian-language Gregorian month
  names, e.g. "آگوست" for August; confirmed with a screenshot).
  Investigated live (not guessed): Persian Calendar's admin-timewrap.js
  binds a `.editinline` click handler that does
  `jQuery(this).closest('td')` to find the clicked row's stored date
  (year/month/day/hour/minute, read from the row's hidden
  `#inline_<id>` div), then builds the Jalali replacement fields from
  that. Read the actual rendered DOM of a real Quick Edit row and found
  the posts list's title column is a `<th scope="row">` in this
  WordPress version, not a `<td>` — so `.closest('td')` always resolves
  to an empty jQuery set, `year > 1700` silently evaluates false against
  the resulting `''`, and the whole Jalali-fields step no-ops with no
  console error, leaving WordPress core's native fields showing as-is.
  A plugin bug, not a WordPress-version-specific outcome the plugin
  ever handled — and, per Farhad's requirement, not something to patch
  inside the plugin file itself (would revert on the plugin's next
  update).
  Added assets/js/admin-quickedit-jalali.js (enqueued from the same
  inc/admin-jalali-months.php, gated on
  `wp_script_is( 'persian-calendar-admin-timewrap', 'enqueued' )` so it
  only ever runs where Persian Calendar's own Quick Edit script already
  runs): a fully independent `.editinline` click handler using
  `.closest('tr')` instead of the plugin's `.closest('td')`, reading the
  same hidden per-row date data WordPress core itself always renders
  (not a Persian Calendar implementation detail, so this doesn't depend
  on anything WordPress-core-version-specific either) and
  `window.PersianDateConverter` (the same already-Dari-patched global
  from the month-names fix above) to build the Jalali fields. The
  injected fields deliberately reuse Persian Calendar's own field ids
  (`jja`/`mma`/`aaa`/`hha`/`mna`) and `.jalali` class, in the same
  `.inline-edit-date legend` position — so admin-timewrap.js's *other*
  handlers (the ones that convert an edited Jalali date back to
  Gregorian and write it into WordPress core's real `name="mm"/"jj"/
  "aa"` fields before Save/Update) keep working unmodified, since those
  are bound generically to `#timestampdiv,.inline-edit-date` rather
  than to the broken `.editinline` handler and were never actually
  broken. This means the fix only reimplements the *broken* half of
  Quick Edit's Jalali support, not the whole feature — and if Persian
  Calendar ever fixes its own `closest('td')` bug, this script simply
  becomes redundant (both would render identical correct fields, not
  conflict).
  Verified live end-to-end via a real logged-in browser session
  (WP-CLI-generated auth cookies delivered through a temporary local
  redirect script, removed again immediately after): opened Quick Edit
  on a post published 1405/05/15 and confirmed the injected fields read
  day 15, month اسد (5, Dari — not مرداد), year 1405 (Jalali, not
  2026) — then, to confirm the write-back path specifically, changed
  the Jalali day field from 15 to 16 and confirmed WordPress core's
  native Gregorian `jj` field updated from 06 to 07 accordingly (with
  `mm`/`aa` unchanged, as expected for a same-month day change) — all
  without ever clicking Update, so no test data was saved.
  Theme version bumped 1.0.2 → 1.0.3 (style.css) for cache-busting.
  Approved by: Farhad, in this session (2026-08-31).

## 2026-09-02
- **Changed:** Homepage مقالات section (`front-page.php`) no longer excludes
  posts already shown in تازه‌ها. Previously it used `post__not_in` against
  `$latest_query`'s post IDs so the same article never appeared twice on the
  homepage (documented 2026-08-25 as a deliberate "next tier down, no
  duplicates" design). Farhad reported a newly published article only showed
  in تازه‌ها (as the hero) and not in مقالات, and relayed the client's actual
  requirement: تازه‌ها is the "everything new" feed (articles, reports,
  documents, issues combined), مقالات is the "articles only" feed — the two
  are independent, and it's fine/expected for the same article to appear in
  both at once. `$articles_query` is now simply "latest 6 posts of type
  `post`, any topic," with no cross-exclusion.
  Reason: reverses the 2026-08-25 anti-duplication design per explicit
  client instruction relayed by Farhad — duplication across these two
  specific sections is intentional, not a bug.
  Approved by: Farhad, in this session (2026-09-02), citing direct client
  instruction.

## 2026-09-02 (later same day)
- **Added, then corrected same session:** the breadcrumb/card term shown on
  the front end (`array_shift( get_the_terms( ... ) )` in `card.php`,
  `single.php`, and `Taxonomies::filter_post_permalink()`) picked whichever
  selected `topic` term WordPress's default term ordering (alphabetical by
  name) happened to sort first — not necessarily the one the editor meant
  as primary. Farhad flagged this as unprofessional after seeing a post
  with two topics checked show the "wrong" one in its breadcrumb.
  First attempt restricted `post` to exactly one `topic` term outright
  (single-select radio panel replacing the checkbox tree, plus a
  server-side `set_object_terms` backstop). Farhad corrected this
  immediately: multi-select `topic` assignment is the standard/wanted
  behavior — only the *display* pick needed fixing, not the assignment
  model. Reverted the single-select restriction; `topic` is back to its
  original Categories-style multi-select panel, untouched.
  Shipped instead: a **primary topic** concept, additive to `topic`
  rather than replacing it —
  - `shcore_primary_topic` (new post meta, `class-meta-fields.php`) stores
    which of a post's assigned `topic` terms is primary.
  - `SholaCore\Taxonomies::get_primary_topic()` resolves it: the stored
    primary if it's still one of the post's actual terms, else the same
    array_shift() fallback as before (never "no topic shown" just because
    nothing was explicitly picked).
  - `admin/js/primary-topic.js` + `Taxonomies::enqueue_primary_topic_assets()`
    add a **second**, separate block-editor panel — «موضوع اصلی» — a radio
    list built only from the topics currently checked in the (unmodified)
    default panel, writing to `shcore_primary_topic`.
  - `shola_get_primary_topic()` (new theme wrapper, `template-tags.php`,
    same plugin-inactive-degrades-gracefully pattern as
    `shola_get_label()`) is the one call site every breadcrumb/card/hero
    term now goes through — `card.php`, `single.php` (breadcrumb + related-
    posts query only; the full multi-topic tag list on that page is
    untouched, still all of `$terms`), `front-page.php`'s hero.
  Known gap, unchanged from the first attempt: Quick Edit's taxonomy
  checklist on the Posts list screen isn't touched by either panel — not a
  correctness issue now that multi-select is allowed again, just means
  Quick Edit has no primary-topic control of its own.
  Plugin version bumped 1.0.2 → 1.0.3 (shola-core.php) for the new
  enqueued script.
  Approved by: Farhad, in this session (2026-09-02).

## 2026-09-02 (later same day)
- **Changed:** Masthead (`header.php`, `assets/css/main.css`), per Farhad's
  direct feedback on the live site: the hero/site-title text read too large
  against the nav row (`font-size` reduced ~20%, `.masthead .mast-brand`);
  the utility row (اطلاعیه‌ها / تماس) was competing visually with the
  publication nav row (نشریات / موضوعات / کتابخانه), fixed by sizing it
  down ~15% (`.masthead-right .mast-btn`, scoped to that container only,
  not a change to `.mast-btn` itself); a new "دربارهٔ ما" link was added to
  that same utility row (`/about/`, existing page, no new template); and
  the search icon — previously a single small (19px) icon in the left
  cluster, which Farhad found hard to notice — is now mobile-only there,
  with a larger (26px), easier-to-hit desktop-only version added to the far
  end of the utility row instead (two markup instances, CSS `hide-mobile`/
  `hide-desktop`-toggled by breakpoint, matching the existing pattern
  already used for the desktop-only nav row — no JS required to move it
  between grid cells at different widths).
  Reason: all four are direct visual/UX corrections from Farhad reviewing
  the live masthead, not new design decisions — §9's "faithful port, not a
  redesign" rule allows exactly this (deviation from the v6 screenshots
  only when Farhad explicitly asks).
  Approved by: Farhad, in this session (2026-09-02).

## 2026-09-02 (later same day)
- **Fixed:** the block-editor sidebar's panel order on the `post` screen —
  Farhad wanted موضوعات (topic, built-in taxonomy panel), موضوع اصلی
  (primary topic, the new custom `PluginDocumentSettingPanel` from the
  entry above), برچسب‌ها (post_tag, built-in), in exactly that order; it was
  rendering as موضوع اصلی, برچسب‌ها, موضوعات. Inspected the actual live DOM
  (not assumed) and found there's no supported ordering API for this:
  built-in taxonomy panels and custom `PluginDocumentSettingPanel`s render
  as plain sibling `.components-panel__body` elements, in whatever order
  the editor's internal panel registry produced, with neither mechanism
  exposing a `position`/`order` prop.
  Added `admin/js/panel-order.js` (enqueued from
  `Taxonomies::enqueue_primary_topic_assets()`, same screen/conditions as
  `primary-topic.js`): reorders those three sibling nodes in place by
  matching each panel's visible title text against the wanted order.
  Deliberately conservative — it only acts when *every* panel currently in
  that sibling group matches a name in its known list, so if a currently-
  unused core panel (Featured image, Excerpt, Discussion) is ever enabled,
  or another custom panel is added later, this leaves the whole group
  alone rather than guessing where the newcomer belongs, instead of
  silently mis-ordering it.
  A `MutationObserver` (not a one-time reorder at load) is required
  because the editor re-renders this sibling group on state changes (e.g.
  checking a topic checkbox re-renders «موضوع اصلی»'s content, which
  re-renders its siblings too) — scoped to the sidebar panel container
  itself once it first appears, specifically so it never reacts to
  unrelated DOM churn elsewhere on the screen (the post content area
  updates on every keystroke; observing document-wide would run this
  reorder check on every keystroke too).
  Verified live via a real logged-in editor session (WP-CLI-generated auth
  cookies through a temporary local redirect script, removed immediately
  after): confirmed the corrected order renders on load, survives
  expanding/collapsing a panel, and survives toggling a topic checkbox
  (which re-renders «موضوع اصلی»'s content) without reverting or flickering
  back to the old order.
  Approved by: Farhad, in this session (2026-09-02).

## 2026-09-02 (later same day)
- **Changed:** Removed the author/username (byline) from every public-
  facing surface, per an explicit client instruction relayed by Farhad —
  dates and every other label stay, only the person's name/username goes.
  Went through the theme file-by-file (not a blanket search-and-hope) and
  removed:
  - `single.php`: the whole «نویسنده:» (byline + optional author-note) and
    «سردبیر مسئول:» (managing editor) lines from the article sidebar —
    Farhad confirmed both should go, not just the byline.
  - `card.php` (the shared card partial), `front-page.php`'s hero, and
    `template-parts/search/result.php`'s article/note branch: each had a
    single `.card-byline` line combining the byline with the publish
    date (`{byline} · {date}`) — the byline half and its `·` separator
    were removed, the `<time>` element and its formatting are untouched.
  - Dropped the now-dead `$byline`/`$byline_meta` variable computation
    (`get_the_author_meta( 'display_name', ... )` calls) alongside each
    removed line, rather than leaving unused assignments behind.
  - `assets/css/main.css`: removed the now-unused `.article-sidebar
    .author`/`.editor` rules; `.word-count` (now the sidebar's first
    child) had its `margin-top` dropped so removing the lines above it
    doesn't leave a stray gap.
  - `class-security.php` (new): WordPress core's own default RSS/Atom
    feed templates independently call `the_author()` for
    `<dc:creator>`/`<author><name>` — a code path none of the template
    edits above could reach. Added a `the_author` filter, scoped to
    `! is_admin()`, blanking it there while leaving wp-admin's own author
    column/dropdowns (an internal CMS-management view, not public-facing)
    untouched. Verified live: `/feed/` now renders `<dc:creator><![CDATA[]]></dc:creator>`.
  **Deliberately left alone**, after confirming with Farhad each is a
  different kind of "author" than a CMS username:
  - `shcore_author_source` (`single-document.php`'s «نویسنده» field,
    and the same meta in `template-parts/search/result.php`'s document
    branch): who originally wrote the archived historical text (e.g.
    Lenin, Mao) — bibliographic/citation info about the document itself,
    not a WordPress user. Confirmed live: search results for "دولت و
    انقلاب" still show "لنین" correctly.
  - `single-issue.php`'s per-row table-of-contents «byline» (admin-typed
    free text crediting a piece inside a printed issue) — same reasoning,
    never tied to a WordPress account.
  - wp-admin's own "نویسنده" column (Posts list) and any other CMS-
    internal author display — this is a public-front-end-only change,
    not a data-model or admin-UX change; verified live, untouched.
  No CMS data was deleted — `shcore_byline`/`shcore_author_note` post
  meta, `shola_get_managing_editor()`, and the `post_author` field itself
  are all unchanged and still stored; they're simply no longer rendered
  on the public site. Reversible without any data loss if this is ever
  revisited.
  Approved by: Farhad, in this session (2026-09-02).

## 2026-09-02 (later same day)
- **Changed:** Homepage «شمارهٔ جاری» now shows one card per publication
  (شعله جاوید, جهان برای فتح), side by side, instead of a single card for
  whichever publication happened to publish most recently. Client
  clarification relayed by Farhad: these are two distinct, both-still-
  active publications from the same organization, and the homepage needs
  to represent both, not just the newest one.
  - `front-page.php`: replaced the single `$current_issue_query` (no
    publication filter) with a loop over every `publication` term,
    querying each one's own latest `issue` (`tax_query` scoped to that
    term). A publication with no issues yet is simply skipped, not shown
    as an empty/broken card — the whole «شمارهٔ جاری» section only
    renders if at least one publication has a current issue.
  - Per Farhad's explicit layout spec: each card's title is now just the
    publication name (شعله جاوید / جهان برای فتح) — previously "{pub
    name} · شمارهٔ {number}" — with the issue number relocated from a
    `.badge-current` pill above the title into the `.issue-meta` list as
    its own «شماره» row, now the *first* row (above تاریخ نشر). «دوره /
    جلد» renamed to «دوره» (the "/ جلد" was redundant per Farhad — دوره
    already means volume here). The description (excerpt) is unchanged.
    «آرشیو شماره‌ها» — previously a single link in the section header,
    pointing at only one publication's archive — is now a button in
    *each* card instead, pointing at that card's own publication archive;
    the now-redundant single header link was removed.
  - `assets/css/main.css`: added `.current-issues` (1 column below
    700px, 2 columns at 700px+ — matches `.wrap`'s 1200px max-width
    leaving ~570px per column comfortably) and overrode
    `.issue-hero--embedded`'s cover-beside-text layout (inherited from
    the single-card `.issue-hero`, meant for a full 1200px-wide card) to
    stay stacked (cover on top, details below) at every width once
    nested in `.current-issues` — a fixed 380px cover no longer fits
    next to text in a ~570px-wide column. Capped `.issue-cover`'s width
    to 200px in this context too (a full-column-width 3:4 cover would
    render ~700px tall, more poster than thumbnail). Removed the now-
    fully-unused `.issue-lead` rule (was only ever used by the single-
    card version this replaces).
  Verified via direct DOM inspection (not a visual screenshot — this
  particular page hit an unrelated screenshot-capture-tool glitch on
  scroll in this session, confirmed unrelated to this change since it
  reproduced identically scrolling to *any* section on the page, forced-
  visible `.reveal` elements included): confirmed live at desktop width,
  two 548px-wide columns, correct publication name in each title, «شماره»
  present as the first `.issue-meta` row (value matches each
  publication's real current issue number), «دوره» (not «دوره / جلد»)
  where a volume is set, each card's «آرشیو شماره‌ها» and issue-title
  links pointing at that card's own publication (`/publications/shola-
  jawid/` vs. `/publications/a-world-to-win/` respectively, not both at
  the same one) — and at mobile width (375px), a single stacked column.
  Approved by: Farhad, in this session (2026-09-02).

## 2026-09-02 (later same day)
- **Fixed:** Farhad reviewed the two-column «شمارهٔ جاری» above and sent
  back two corrections, applied together in `assets/css/main.css` and
  `front-page.php`: (1) it read too tall/spaced-out for two columns to
  fit comfortably on screen; (2) each card had been restructured to
  stack the cover above the details — Farhad wanted the *original*
  `.issue-hero` anatomy kept (cover beside the details, same side as
  before, i.e. right-hand column under this site's RTL), just scaled
  down to fit a half-width column, not redesigned into a different
  layout.
  - `.current-issues .issue-hero--embedded` now uses
    `grid-template-columns: 140px 1fr` (was `1fr`, i.e. stacked) —
    restores the side-by-side anatomy at roughly a third of the
    standalone `.issue-hero`'s 380px cover width.
  - Every internal spacing/type value scaled down to match, not just the
    cover: `.h-page` here 1.375rem (was the shared class's
    `clamp(2rem, 4vw, …)`), `.dek` here 0.9375rem/3-line-clamp (was
    `clamp(1.125rem, …, 1.3125rem)`/4-line-clamp), `.issue-meta`'s
    padding-block/margin-block cut from 1.5rem/2rem to 0.75rem/0.75rem,
    and both buttons switched to the existing `.btn-sm` modifier
    (already used elsewhere in this design system for exactly this —
    no new button-size CSS invented). `.current-issues`'s own
    column gap trimmed 2.5rem → 2rem.
  Net effect, confirmed live via DOM measurement: each card's height
  dropped from 724px to 272px (desktop, real current data) — matching
  the original single, non-split card's general proportions rather than
  reading like a stretched-out redesign.
  Approved by: Farhad, in this session (2026-09-02).

## 2026-09-02 (later same day)
- **Fixed:** a second round of feedback on the same «شمارهٔ جاری» cards
  (annotated screenshot, both columns marked): the fixed-size cover
  (140px, `align-items: start`) only ever aligned with the *top* of the
  taller details column beside it, leaving a visible gap underneath —
  not the "top and bottom aligned" look Farhad asked for — and the
  overall scale/spacing read as cramped rather than deliberately
  compact. Also asked explicitly for the mobile version to be covered.
  - `.current-issues .issue-hero--embedded` now only switches to the
    side-by-side (cover-beside-details) anatomy at 700px+; below that it
    stacks (cover on top, full width, details below) — matching this
    same site's own established convention for this card type (the
    standalone `.issue-hero` page also stacks below its own 800px
    breakpoint), rather than forcing two narrow side-by-side halves onto
    a phone screen.
  - At 700px+: cover width upped 140px → 170px ("a little bigger," per
    Farhad), and — the actual alignment fix — `align-items: stretch` on
    the grid plus `aspect-ratio: auto` on `.issue-cover` (was a fixed
    3:4) so the cover's height now matches whatever height the details
    column actually needs, edge to edge, instead of a fixed height
    computed from a fixed aspect ratio. `.issue-cover img`'s existing
    `object-fit: cover` crops cleanly to that dynamic height — no new
    image-handling code needed.
  - Eased the previous pass's type/spacing scale back open slightly:
    `.h-page` 1.375rem → 1.5rem, `.dek` .9375rem → 1rem, `.issue-meta`'s
    padding-block/margin-block .75rem → 1rem, its row gap .35rem →
    .5rem, the column gap between the two cards 2rem → 2.5rem — still
    well short of the original single-card scale that read too tall two
    rounds ago, but no longer reading as squeezed.
  Verified via DOM measurement (this page's screenshot capture in this
  environment stayed unreliable on scroll — a tool-side issue confirmed
  unrelated to this change, see the previous entry): at 1280px, cover
  height for both cards now exactly equals the details column's height
  (300.25px both, top/bottom offsets identical) — genuinely edge-aligned,
  not just visually close. At 375px (mobile), confirmed both cards
  collapse to the single stacked column (full-width 335×447 cover, 3:4
  aspect ratio restored since the 700px+ override no longer applies).
  Approved by: Farhad, in this session (2026-09-02).

## 2026-09-02 (later same day)
- **Changed:** third round on the same «شمارهٔ جاری» cards — Farhad found
  the 170px cover still too narrow at desktop width. Widened to 230px
  (`.current-issues .issue-hero--embedded`'s fixed grid column, and
  `.issue-cover`'s matching `max-width`, both in `assets/css/main.css`);
  `align-items: stretch` already in place from the previous round keeps
  it exactly height-matched to the details column beside it regardless
  of the width change (confirmed live: 230×336px cover, details column
  also 336px tall, at 1280px viewport width). Farhad also flagged «جهان
  برای فتح»'s current issue not showing a «دوره» row, then withdrew that
  before any change was made ("the information is there") — so left
  untouched; the row's own conditional display (`if ( $volume )`,
  `front-page.php`) is unchanged from the previous entry.
  Approved by: Farhad, in this session (2026-09-02).

## 2026-09-02 (later same day)
- **Fixed:** «شمارهٔ جاری»'s two columns rendered جهان برای فتح before
  شعله جاوید — `get_terms()`'s default alphabetical order happens to
  sort ج ("جهان...") ahead of ش ("شعله...") — which Farhad flagged as
  backwards: شعله جاوید is this organization's flagship publication and
  needs to lead. `front-page.php` now stable-sorts the `shola-jawid`
  term to the front after fetching (`usort`, PHP's sort has been stable
  since 8.0 — this project already requires PHP 8.1 per `CLAUDE.md` §0,
  so no polyfill needed) rather than hardcoding a fixed two-item slug
  list, so a future third publication term still appears (after these
  two, in whatever order `get_terms()` gave it) instead of silently
  disappearing from this section. Verified live: DOM order (and so,
  under this site's `dir="rtl"`, visual right-to-left reading order) is
  now شعله جاوید, جهان برای فتح.
  Approved by: Farhad, in this session (2026-09-02).

## 2026-09-02 (later same day)
- **Added:** a genuine third content type, `party_publication`
  (انتشارات حزب — the party's own books/booklets), fully independent
  from both `issue` (نشریه — periodical شعله جاوید/جهان برای فتح
  numbers) and `document` (کتابخانه — the general library of *other*
  theorists'/authors' works). Farhad relayed a client correction: a
  homepage section already existed under this exact Persian heading
  (added 2026-08-12), but it was actually querying `issue` — the client
  identified all three (نشریه, کتابخانه, انتشارات حزب) as needing to be
  completely independent, each with its own wp-admin tab, own front-end
  URL, and own archive page. Before building anything, grepped the whole
  repo — `docs/IA-reference/`, `EXECUTION_PLAN.md`, `CLAUDE.md`'s §9 open-
  decision list — for any prior mention of this split: none exists, this
  is a new content type, not a fix to something previously specified.
  - `shola-core\Post_Types`: registered `party_publication`
    (`has_archive => false`, matching `issue`/`document`'s "static Page
    instead of a CPT archive" pattern; `rewrite.slug =>
    'party-publications'`; no taxonomy — client didn't ask for sub-
    categorization here, easy to add later if that changes rather than
    guessing at categories now). Added to
    `include_cpts_in_search()`'s default post-type list and its
    `result_type` switch.
  - `shola-core\Meta_Fields`: `shcore_pdf_id` (reusing the same meta key
    `issue`/`document` already use — `register_post_meta()` scopes by
    post type/object-subtype, so this is the established pattern, not a
    collision) and `shcore_language`, plus a metabox
    (`render_party_publication_metabox()`) with just those two fields —
    deliberately no author-source field like `document` has: these are
    the party's own works, nothing to attribute per item to an external
    writer.
  - Theme: new `single-party_publication.php` (modeled on
    single-document.php's PDF-preview-with-cover anatomy, minus the
    نویسنده/مجموعه/ویراستار rows that don't apply — the last also
    per the site-wide no-public-author-display policy from earlier
    today) and new `page-party-publications.php` (the Page-slug-matched
    archive template, paginated `WP_Query` since — unlike
    page-library.php's fixed "latest 5" — a real archive shouldn't have
    an arbitrary cutoff; issue-card.php reused as-is for the grid, since
    it was already post-type-agnostic).
  - `front-page.php`'s «انتشارات حزب» section: swapped its query from
    `post_type => 'issue'` to `post_type => 'party_publication'` (the
    actual bug this whole change fixes) and its "همهٔ نشرات" link from
    `/publications/` to `/party-publications/`.
  - `search.php` + `template-parts/search/result.php`: added the fourth
    filter tab and result-rendering branch (PDF size shown, no
    author/term line, matching the new type's actual field shape).
  Verified live end-to-end (not just individually): flushed rewrite
  rules, created a real test Page at slug `party-publications` and a
  real test post, confirmed (1) its permalink correctly uses
  `/party-publications/...`, not `/publications/...` or `/library/...`;
  (2) the single-item template renders with no نویسنده/ویراستار line;
  (3) the archive page lists it; (4) the homepage's «انتشارات حزب»
  section now shows it instead of the old (wrong) issue data; (5) it's
  findable via site search with the correct filter-tab label; (6) the
  نشریه section (`/publications/`) and its homepage «شمارهٔ جاری» cards
  are completely unaffected — still real `issue` data, confirming the
  fix didn't regress the type it was wrongly borrowing from; (7)
  wp-admin's sidebar now shows all three — «شماره‌های نشریه», «کتابخانه»,
  «انتشارات حزب» — as fully separate top-level tabs.
  **Deployment note, not yet done as of this entry:** a live/production
  deploy of this change needs (a) a rewrite-rules flush (visit Settings
  → Permalinks and click Save, or deactivate/reactivate the shola-core
  plugin) before `/party-publications/...` URLs resolve, and (b) an
  actual WordPress Page created with slug `party-publications` for the
  archive template to attach to — the same manual step already required
  for `/library/` and `/publications/` when those were first built.
  Also found, but out of scope for this entry and deliberately not
  touched here: single-document.php's «ویراستار» row still calls
  `shola_get_managing_editor()`, missed by the earlier "remove public
  author/username" pass above since it wasn't matched by that pass's
  byline/author search terms — flagged as a separate follow-up task.
  Approved by: Farhad, in this session (2026-09-02).

## 2026-09-02 (later same day)
- **Added:** each `publication` term (شعله جاوید, جهان برای فتح) is now
  split into 4 دوره (period) sub-terms — «دورهٔ اول» through «دورهٔ
  چهارم» — client request relayed by Farhad. Presented pseudocode for
  the whole change first and got explicit approval, including two
  specific decisions: existing issues (all tagged only with the parent
  term until now) get bucketed into «دورهٔ اول» as a starting point
  rather than left unbrowsable, and issue URLs stay flat
  (`/publications/shola-jawid-dowre-1/{issue}/`) rather than building
  new nested-rewrite code for a "prettier" URL — both per Farhad's
  explicit choice.
  - `publication` was already registered `hierarchical => true` (an
    existing, unused capability — confirmed by reading the registration
    before assuming anything needed to change there), so no taxonomy-
    registration change was needed; only term seeding and template
    logic.
  - `SholaCore\Taxonomies::maybe_insert_term()` gained an optional
    `$parent` param (default 0, so every existing call site is
    unaffected) and now returns the term ID instead of void.
  - New `SholaCore\Taxonomies::seed_publication_periods()`: creates the
    8 دوره terms (slugs `{pub-slug}-dowre-{1-4}`, distinct per
    publication rather than reused, avoiding any slug-uniqueness
    ambiguity) and migrates any issue still tagged with only a parent
    publication term into that publication's «دورهٔ اول». Hooked on
    `admin_init`, not plugin activation — activation hooks don't re-fire
    on a code-only zip re-upload to an already-active plugin, so this
    follows the same self-healing `admin_init`-idempotent pattern
    already established by `Roles::maybe_grant_editor_menu_access()`.
    Both halves are naturally idempotent by construction (no separate
    "done" flag needed) — see the method's own docblock for exactly why,
    including why this also self-heals a *future* issue that
    accidentally ends up tagged with only a parent term.
  - `taxonomy-publication.php` now branches on whether the term being
    viewed has children: a top-level term renders a `.topic-list` grid
    of its 4 دوره tiles (name + issue count) — the same component
    page-topics.php and page-library.php's collection list already use,
    not a new one — instead of the flat issue list it used to show
    directly; a دوره (leaf) term renders that same flat-issue-list-plus-
    current-issue-hero behavior, unchanged except `$is_current` now
    resolves from the term's *parent* slug (شعله جاوید vs. جهان برای
    فتح), since a دوره term's own slug is publication-agnostic.
  - `page-publications.php`'s «آرشیو شماره‌ها»/«آرشیو کامل» buttons
    needed no change — they already link to the parent term's own
    archive, which is exactly where the new tile grid now lives.
  - `shola_get_publication_meta_line()` (inc/template-tags.php) needed
    no change either — already purely `term_id`-based, so it correctly
    computes a دوره-scoped issue count/year-range when called with a
    دوره term instead of a top-level one; only its docblock was updated
    to say so explicitly.
  Found and fixed a data-hygiene issue while testing, not part of the
  planned change: two terms already existed under شعله جاوید — «دوره
  یک»/«دوره دو» (slugs `period-one`/`period-two`), differently named
  and slugged than the 4 seeded here, both empty (0 issues) — evidently
  created exploring the taxonomy screen before this feature existed.
  Confirmed with Farhad and deleted from the local database; if the
  same two terms exist on production, Farhad will remove them there too
  (I have no direct production DB access).
  Verified live end-to-end: ran the migration function directly
  (`wp eval`) and confirmed via `wp term list` that all 8 existing
  شعله جاوید issues and the 1 existing جهان برای فتح issue moved onto
  their publication's «دورهٔ اول» with the correct counts; loaded both
  publications' top-level archive and confirmed each shows exactly its
  4 دوره tiles with correct per-دوره issue counts; loaded «دورهٔ
  اول» for both publications and confirmed the existing hero/grid
  behavior renders correctly, with «جاری»/«شماره‌های پیشین» wording
  showing only for شعله جاوید's and «آرشیوی»/«همهٔ شماره‌ها» only for
  جهان برای فتح's, confirming the parent-slug-based `$is_current` fix
  actually works, not just compiles; confirmed page-publications.php's
  three buttons (آرشیو شماره‌ها، آرشیو کامل، شمارهٔ جاری) all still
  resolve to the correct URLs.
  **Deployment note:** on an already-active production site, this
  migration needs one wp-admin page load after the plugin update is
  live (any admin screen — `admin_init` fires on all of them) before
  the دوره terms/migration take effect; no Settings → Permalinks flush
  needed this time, since no new rewrite rule was added (the existing
  `%publication%` rewrite tag already resolves whatever slug ends up on
  the issue's `publication` term, دوره or not).
  Approved by: Farhad, in this session (2026-09-02).

## 2026-09-03
- **Added:** a «ترتیب» (manual sort order) field for `publication` terms.
  Farhad reported the new دوره tiles (previous entry) weren't rendering
  in اول/دوم/سوم/چهارم order — WordPress's default term order for a
  custom taxonomy isn't creation order or name order in any guaranteed
  way — and asked for a CMS field to control it himself, for these and
  any دوره he adds later.
  - `shcore_term_order` registered as term meta on `publication`
    (`SholaCore\Taxonomies::register_publication_order_meta()`), with a
    plain number `<input>` added to both the "Add New Term" panel and
    the "Edit Term" screen (WP core's own `form-field`
    div/tr markup conventions, not a custom UI), saved via
    `created_publication`/`edited_publication`, and shown as a read-only
    column on the term list table so current values are visible at a
    glance. Not made click-to-sort in the admin table — that needs
    additional `terms_clauses` filtering that wasn't asked for; the
    front-end sort is what actually matters here.
  - The 4 دوره terms `seed_publication_periods()` creates now also get
    `shcore_term_order` set to 1–4 at creation time (only if unset, so
    it never overwrites a value Farhad has since changed) — so اول
    through چهارم are correctly ordered by default without him having to
    set anything, and only terms he adds by hand (or the already-
    existing «دوره پنجم» found while testing, unset) need it filled in.
  - `taxonomy-publication.php`'s دوره-tile query sorts the results in
    PHP with `usort()` rather than via `get_terms()`'s own
    `orderby => meta_value_num` — that option performs an inner join
    against term meta and would silently *drop* any term with no
    `shcore_term_order` value at all (i.e. every دوره Farhad adds before
    he's set an order for it) instead of just sorting it last. A term
    with no value set sorts after every term that has one, matching the
    field's own "خالی یعنی آخر فهرست" description.
  Verified live: confirmed the field renders (with the correct existing
  value) on a real Edit Term screen; confirmed via `wp term meta get`
  that seeding correctly backfilled 1–4 on the 8 existing دوره terms
  without touching any value already set; submitted a real form POST
  (replicating an actual "به‌روزرسانی" click, cookies from a real
  logged-in session) to set an order on the previously-unordered «دوره
  پنجم» term and confirmed via `wp term meta get` that it actually
  persisted through that real save path, not just a direct DB write;
  confirmed the front-end tile order changed to match afterward
  (اول, دوم, سوم, چهارم, پنجم).
  **Incident during this verification, self-caused and self-fixed, not
  left for Farhad to find:** the curl command used to replicate that
  form POST passed «دوره پنجم» through bash's own shell/UTF-8 handling,
  which corrupted the term's stored *name* (not the order field being
  tested) into literal `????`. Caught immediately by re-reading the term
  back, fixed with a direct `wp_update_term()` call restoring the exact
  original name (bypassing the same shell encoding path this time), and
  reconfirmed correct. No other term's data was touched by this.
  Approved by: Farhad, in this session (2026-09-03).

## 2026-09-03 (later same day)
- **Added:** a `.article-crumb` breadcrumb to `taxonomy-publication.php`,
  both branches (the top-level tile-grid view and the دوره leaf view).
  Farhad reported that once browsing a publication meant a دوره tile
  click first, there was no way back up from either page besides the
  browser's own back button — this template had never had a breadcrumb
  at all, unlike every single-*.php view.
  Same "صفحهٔ اصلی / نشرات / ..." pattern already used by
  single-issue.php/single-document.php (not a new component), extended
  one level for the leaf view specifically so the parent publication's
  own tile-grid page is a real link, not just implied:
  - Tile-grid view: «صفحهٔ اصلی / نشرات / {publication}»
  - دوره view: «صفحهٔ اصلی / نشرات / {publication} / {دوره}»
  Verified live: both pages render the correct trail, and confirmed via
  the actual rendered `<a>` hrefs (not just the visible text) that every
  link resolves correctly — critically, that شعله جاوید's crumb on the
  دوره page really does link back to `/publications/shola-jawid/` (the
  tile grid), the specific "no way back" gap Farhad reported.
  Approved by: Farhad, in this session (2026-09-03).

## 2026-09-03 (later same day)
- **Fixed:** `single-issue.php`'s breadcrumb was still missing the
  publication level — «صفحهٔ اصلی / نشرات / دورهٔ اول» instead of
  «.../ شعله جاوید / دورهٔ اول» — the same grandparent-level gap already
  fixed on `taxonomy-publication.php`'s two archive views, but on the
  single-issue page itself. Root cause: since the دوره migration,
  `get_the_terms($post, 'publication')` on an issue now returns the
  دوره (child) term, not the top-level publication — `$pub` was never
  updated to also resolve `$pub->parent` for the grandparent crumb.
  Walks up to `$pub->parent` the same way `taxonomy-publication.php`'s
  leaf view already does, and — checking rather than assuming this was
  the *only* place `$pub` meant "دوره, not publication" — found and
  fixed two more bugs from the exact same root cause on this same page,
  neither of which Farhad had reported yet: `shola_publication_status_label()`
  was being passed the دوره's own slug (e.g. `shola-jawid-dowre-1`,
  which never equals `'shola-jawid'`), so every شعله جاوید issue's page
  showed «آرشیوی» (archived) instead of «جاری» (current), regardless of
  which publication; and the `<h1>` showed "دورهٔ اول · شمارهٔ ۳۲" instead
  of "شعله جاوید · شمارهٔ ۳۲". Both switched to the resolved
  publication-level slug/term (`$root_slug`/`$pub_display`).
  Verified live at the exact URL Farhad reported
  (`/publications/a-world-to-win-dowre-1/جهان-برای-فتح-·-شمارهٔ-۲۱/`):
  breadcrumb now reads «صفحهٔ اصلی / نشرات / جهان برای فتح / دورهٔ اول»,
  status correctly reads «آرشیوی» (this one genuinely is archived —
  جهان برای فتح), and the H1 correctly reads «جهان برای فتح · شمارهٔ ۲۱».
  Also spot-checked a شعله جاوید issue to confirm its status now
  correctly flips to «جاری».
  Found while re-verifying, out of scope for this entry and left
  untouched: this same page still shows «سردبیر مسئول» (managing
  editor) — the same public-author-display category already removed
  from single.php, card.php, front-page.php, and search results
  (2026-09-02), and already flagged once before as missed on
  single-document.php. Flagged again as a follow-up task rather than
  fixed here, to keep this entry scoped to the breadcrumb bug Farhad
  was actively waiting on.
  Approved by: Farhad, in this session (2026-09-03).

## 2026-09-03 (later same day)
- **Fixed:** the homepage's «شمارهٔ جاری» section was showing 4-5 cards
  instead of 2 — one card per publication *and* one per دوره that had
  an issue. Root cause: `front-page.php`'s `get_terms()` call for that
  section (added 2026-09-02, before the دوره sub-terms existed) fetched
  every `publication` term with no `parent` filter — harmless while the
  taxonomy only had its 2 top-level terms, but once
  `seed_publication_periods()` added the 8 دوره children the next day,
  it silently started looping over those too. Added `'parent' => 0`
  so it only ever fetches the top-level publications, same as the
  section always intended.
  Checked every other `get_terms()`/`tax_query` call against
  `publication` in the theme for the same unscoped-since-دوره-existed
  bug rather than assuming this was the only one: `page-publications.php`
  and `shola_get_publication_meta_line()` (inc/template-tags.php) both
  already scope by a specific known `$term`/slug, and `inc/setup.php`'s
  nav-menu registration uses a hardcoded two-slug list — none of those
  had the same problem.
  Verified live: homepage now shows exactly 2 cards (شعله جاوید, جهان
  برای فتح), each still correctly showing that publication's own
  overall latest issue across all its دوره (unaffected — the per-card
  issue lookup itself was never scoped to a single دوره, only the list
  of *cards to render* was over-broad).
  Approved by: Farhad, in this session (2026-09-03).

## 2026-09-03 (later same day)
- **Changed:** the masthead date (`shola_get_masthead_runner()`,
  `inc/template-tags.php`) now shows today's real date via `wp_date()`,
  unconditionally. Farhad reported it looked "stuck" on production and
  asked why before anything was changed — investigated and explained
  first, not assumed to be a bug: this text has never been a live
  clock. Since 2026-08-12 it deliberately showed the *latest published
  issue's* date (a print-newspaper "edition date" convention,
  client-approved at the time), which only ever advances when a new
  شماره is published. On local it looked live purely by coincidence —
  test issues kept getting created with today's date during this
  session's own work — while on production, where the last real issue
  predated today by several days, it correctly stayed frozen at that
  real publish date. Once this was explained, Farhad chose to change
  the behavior going forward rather than keep the original design.
  No longer queries `issue` posts at all — `wp_date( 'l j F Y' )`
  (today, site timezone) unconditionally, so it also no longer needs
  the old "empty string when no issue exists yet" fallback. Confirmed
  `wp_date`/`get_the_date` are both already covered by the existing
  `shola_convert_jalali_months_to_dari()` filter list (re-checked
  before relying on it, not assumed) — no change needed there.
  Verified live: `wp eval`'s `current_time('mysql')` read
  `2026-09-03 10:45:57`; the masthead correctly showed «پنجشنبه ۱۲
  سنبله ۱۴۰۵» (the Jalali/Dari equivalent) immediately, with no
  dependency on any issue's publish date.
  Approved by: Farhad, in this session (2026-09-03).

## 2026-09-04
- **Fixed:** `SholaCore\Taxonomies::seed_publication_periods()` was
  recreating any manually deleted دوره term on the very next wp-admin
  page load. Farhad reported that after the client confirmed جهان
  برای فتح only ever had one real دوره, he tried deleting دورهٔ
  دوم/سوم/چهارم from wp-admin — the delete action said it succeeded,
  but the terms reappeared on refresh, both in the admin list and back
  on the front-end tile grid. Root cause: the function ran
  unconditionally on every `admin_init` (needed originally because
  activation hooks don't re-fire on a code-only zip re-upload — see
  the 2026-09-02 entry) and used `maybe_insert_term()`'s
  `term_exists()` check to decide whether to (re)create each دوره —
  which can't distinguish "never created yet" from "created, then
  deliberately deleted." Fixed by recording, per publication, that
  seeding has run at all (`shcore_periods_seeded` option, persisted in
  the database so it still survives a plugin-zip redeploy) — once a
  publication is marked seeded, `seed_publication_periods()` never
  touches it again, so any دوره term Farhad deletes afterward now
  stays deleted, including on the front end.
  Also removed `period-five` (a leftover test term this session
  accidentally left behind while diagnosing the ترتیب save path via a
  raw curl POST, per the 2026-09-02 entry's UTF-8 corruption incident —
  the corrupted name was fixed there, but the test term itself was
  never cleaned up).
  Verified locally: ran the fixed function once (sets the seeded flag
  for both publications with no changes, since all terms already
  existed), deleted a-world-to-win's دورهٔ دوم/سوم/چهارم via `wp term
  delete`, ran the function again to simulate the next admin page
  load, and confirmed via `wp term list` and the live front-end
  (`/publications/a-world-to-win/`) that only دورهٔ اول remains.
  Plugin version bumped 1.0.7 → 1.0.8 (`shola-core.php`).
  Approved by: Farhad, in this session (2026-09-04).

## 2026-09-04 (later same day)
- **Added:** reassign-before-delete guard for `publication` terms with
  content (`includes/class-term-reassign.php`, new `SholaCore\
  Term_Reassign` class). Farhad reported the client had already
  uploaded real PDF issues into some دوره sub-terms, and asked that
  deleting a دوره first require choosing another دوره to move its
  content onto — deleting a WordPress term never deletes the underlying
  issue posts/PDFs, but it does silently remove them from
  taxonomy-publication.php's دوره tiles, which reads as "the content
  disappeared" from the client's side.
  Two parts: (1) the "حذف" row action on Nشریات → edit-tags.php is
  replaced, only for a `publication` term that still has issues
  attached, with an "انتقال و حذف…" link to a small hidden admin
  screen (`admin.php?page=shcore-reassign-term`, registered via
  `add_submenu_page( null, … )` so it never appears in the sidebar —
  no prior precedent for a hidden page in this plugin, which otherwise
  only ever adds visible Settings-API subpages) where a sibling دوره
  is chosen before the issues are moved (`wp_set_object_terms()` +
  `wp_remove_object_terms()`) and the original term is deleted.
  (2) A `delete_term` safety net (`rehome_orphans()`) automatically
  moves any issues WordPress reports as still related
  (`$object_ids`, a native `delete_term` action param since WP 4.5)
  onto the deleted term's best-ordered sibling for any deletion path
  that doesn't go through that screen — bulk delete, WP-CLI, REST API
  — none of which this class can cleanly intercept beforehand, since
  WordPress core has no filter capable of blocking `wp_delete_term()`
  outright (`pre_delete_term` is an action, fired for informational
  purposes only, not a short-circuit filter). This is deliberately a
  silent auto-rehome for that edge case rather than a hard block,
  since the alternative — returning a `WP_Error` from a would-be
  blocking filter — is unreliable here anyway: the AJAX single-delete
  handler (`wp_ajax_delete_tag()`) treats any truthy return, including
  a `WP_Error` object, as success, which would have reintroduced
  exactly the "looks deleted, isn't" confusion from the earlier
  2026-09-04 entry above, just for a different reason.
  Both paths share one ordering helper (`get_ordered_siblings()`),
  sorting by the same `shcore_term_order` term meta
  taxonomy-publication.php already uses for its front-end دوره tiles,
  so "the sibling a person would expect" is consistent everywhere.
  Verified locally end-to-end: created a test issue, tagged it onto
  شعله جاوید's دورهٔ دوم, used the new admin screen to reassign it to
  دورهٔ سوم and confirm the term deleted cleanly with the issue landing
  on exactly one دوره (no duplicate assignment — an initial version of
  this fix used `wp_set_object_terms(..., true)` without also calling
  `wp_remove_object_terms()` first, which left the old relationship in
  place long enough for the delete_term safety net to *also* fire and
  append a second, redundant sibling term; caught by inspecting `wp
  post term list` after the first test run, fixed before shipping).
  Separately confirmed the safety net alone: deleted a دوره directly
  via `wp term delete` (bypassing the admin screen entirely, standing
  in for a bulk-delete/CLI/REST path) and confirmed the test issue was
  automatically rehomed onto دورهٔ اول rather than left orphaned.
  Plugin version bumped 1.0.8 → 1.0.9 (`shola-core.php`).
  Approved by: Farhad, in this session (2026-09-04).

## 2026-09-04 (later still)
- **Fixed:** the `publication` metabox on the `issue` edit screen let an
  editor tick more than one نشریه/دوره at once — Farhad reported
  (screenshot) checking, e.g., both جهان برای فتح and one of شعله
  جاوید's دوره simultaneously, when an issue must always belong to
  exactly one. WordPress core has no built-in single-select mode for a
  hierarchical taxonomy's admin checklist (`post_categories_meta_box()`
  always renders checkboxes), so this swaps in a new
  `SholaCore\Term_Radio_Walker` (`includes/class-term-radio-walker.php`)
  — a `Walker_Category_Checklist` subclass whose only change is
  rendering `type="radio"` instead of `type="checkbox"` — for the
  `publication` taxonomy on `issue` posts specifically
  (`Taxonomies::use_single_select_publication_metabox()`, hooked on
  `add_meta_boxes`). `topic`/`post` and `collection`/`document` are
  untouched and keep their normal multi-select checkbox trees; only
  `issue` ↔ `publication` needed this.
  The field name is left exactly as core's own
  `tax_input[publication][]` — a radio *group* sharing one `name` only
  ever submits the single checked value even with the trailing `[]`, so
  WordPress's existing tax_input-saving code
  (wp-admin/includes/post.php's `edit_post()`) needed no changes and no
  extra nonce: it already expects (and gets) a plain array of term IDs,
  now always exactly one element.
  Also added a `set_object_terms` safety net
  (`Taxonomies::enforce_single_publication_term()`) that trims an issue
  back down to one `publication` term if anything else — Quick Edit,
  the REST API, an import — ever assigns more than one, since none of
  those paths render through the radio metabox above and so aren't
  limited to one choice by the markup alone; keeps whichever term was
  part of the write that just happened, guarded against re-entrancy
  since it calls `wp_set_object_terms()` itself.
  Verified locally: confirmed via the browser's DOM that all terms
  across both نشریه trees share one native radio group (checking
  جهان برای فتح correctly unchecked a previously-checked شعله جاوید
  دوره), saved a test issue and confirmed via `wp post term list` it
  kept exactly one term, then called `wp_set_object_terms()` directly
  with two term IDs to simulate a non-UI write and confirmed the
  safety net collapsed it back to one automatically.
  Plugin version bumped 1.0.9 → 1.0.10 (`shola-core.php`).
  Approved by: Farhad, in this session (2026-09-04).

## 2026-09-04 (Phase 1 of the Build Readiness Plan)
- **Added:** اسناد حزب (Party Documents) as a fully independent content
  type, per the client's explicit correction (relayed by Farhad) that it
  is not a کتابخانه/Library shelf — it needs its own admin tab, its own
  fields, and a self-managed category system, distinct from both
  `document` (other authors' works) and `party_publication` (the party's
  finished books).
  New `party_document` CPT (`class-post-types.php`) — same flat
  `party-documents/%postname%/` URL shape as `party_publication` (no
  category in the URL; the client's description of "add categories if
  they need them" describes an optional admin-side grouping, not a URL
  taxonomy structure), title/thumbnail/excerpt/editor/custom-fields
  support, `dashicons-portfolio` icon (distinct from the other three
  content types' icons).
  New `party_document_category` taxonomy (`class-taxonomies.php`) —
  hierarchical (matches the site's existing Categories-style admin UI
  convention) but deliberately seeded with zero default terms, unlike
  topic/publication/collection's fixed vocabularies: this one is meant to
  start empty and grow as staff need it.
  New fields (`class-meta-fields.php`): شمارهٔ سریال
  (`shcore_serial_number`, new), فایل PDF (`shcore_pdf_id`, same
  validated-attachment pattern as every other PDF field on the site —
  confirmed required by Farhad after being flagged as a possible gap),
  زبان (`shcore_language`, same fa/en pattern as `document`/
  `party_publication`). Name/detail/date all reuse native fields (title,
  block-editor content, publish date) rather than adding redundant meta.
  New front-end templates: `single-party_document.php` (modeled on
  `single-party_publication.php`, plus a شمارهٔ سریال row and a دسته row
  shown only when a category has actually been assigned) and
  `page-party-documents.php` (a single paginated grid, same shape as
  `page-party-publications.php` — not split by category up front, since
  the category list starts empty and may never grow into something
  worth a dedicated layout).
  Migration (`Taxonomies::migrate_legacy_party_documents()`, admin_init +
  options-flag, same self-healing pattern as `seed_publication_periods()`):
  the 2 real documents previously filed under کتابخانه's "اسناد حزب"
  shelf are moved onto the new post type by changing their `post_type`
  directly (title/excerpt/content/featured image/PDF/language all carry
  over untouched, including the post ID), then that now-empty `collection`
  term is deleted — Library's shelf count drops from 4 to 3 as a direct,
  intended result, not a side effect. Verified locally: both documents
  (`قطعنامهٔ پایانی نشست سراسری`, `اساسنامهٔ حزب`) moved with their PDFs
  intact, `collection` term list now reads فقط آثار کلاسیک/جنبش
  بین‌المللی/نقد و پلمیک.
  Navigation: added to the masthead's top bar (`header.php`) alongside
  نشریات/موضوعات/کتابخانه, and self-heals an اسناد حزب entry into the
  already-seeded «بخش‌ها» popup menu (`shola_maybe_add_party_documents_
  menu_item()`, `inc/setup.php`) — a separate, narrower check from
  `shola_maybe_seed_nav_menus()`, since that function only ever seeds a
  menu location once and can't retroactively add an item to a menu that
  already existed on every site live before this change.
  Verified locally end-to-end: admin list/add-new/categories screens all
  render correctly (block editor active, matching `party_publication`'s
  existing behavior since both share the same `supports` array), the
  taxonomy screen shows an empty list ready for staff to add their own
  categories, both migrated documents render correctly on their single
  pages and the new archive page, and both the top-bar and popup-menu
  links resolve to the new archive.
  Plugin version bumped 1.0.10 → 1.1.0, theme version bumped 1.1.6 → 1.2.0
  (new CPT/taxonomy/templates/nav entries — feature-level bump, not a
  patch).
  Approved by: Farhad, in this session (2026-09-04) — Phase 1 of the
  Technical Scoping Plan.

## 2026-09-04 (Phase 2 of the Build Readiness Plan)
- **Added:** a single-page view for اطلاعیه‌ها (Announcements) —
  `single-announcement.php`, new. This deliberately reverses an earlier
  decision (archive-announcement.php's own docblock, 2026-08-06:
  "list-only by design, permanently") per an explicit client request
  relayed by Farhad — clicking an announcement should open its full text
  on its own page. Worth being clear this isn't a repeat of the 2026-08-06
  mistake it reverses: that attempt linked titles to WP's bare unstyled
  default template hierarchy, since no detail template existed yet, which
  was worse than the inert `href="#"` it replaced; this time the
  destination is an actual designed template, matching the rest of the
  site (breadcrumb, title, date, full body text, and a "سایر اطلاعیه‌ها"
  related list so a reader is never dead-ended).
  No fields changed — `announcement` still only has title and body text;
  the only thing that was ever actually missing was the page itself.
  `archive-announcement.php`'s title links, previously `href="#"`,
  updated to `the_permalink()` now that there's somewhere real for them
  to go.
  Verified locally: opened multiple real announcements end to end,
  including following the "سایر اطلاعیه‌ها" related links from one
  announcement into another and back into the archive.
  Theme version bumped 1.2.0 → 1.2.1 (`style.css`).
  Approved by: Farhad, in this session (2026-09-04) — Phase 2 of the
  Technical Scoping Plan.

## 2026-09-04 (Phase 3 of the Build Readiness Plan)
- **Added:** a full archive page for گزارش (Reports) —
  `page-reports.php`, new, applies to a static Page at slug `reports`.
  گزارش has existed since Phase B (2026-08-25) as a `post_tag` shown on
  the homepage, but had no page of its own — only ever the newest 6 on
  the homepage, with no "see all" link because there was nowhere for one
  to point.
  Uses a static Page rather than WordPress's native tag-archive URL
  (`/tag/reports/`), matching نشریات/کتابخانه/انتشارات حزب/اسناد حزب's
  existing pattern — a clean `/reports/` address consistent with the
  rest of the site, no rewrite-rule plumbing needed. Same grid/pagination
  shape as taxonomy-topic.php (Reports are regular `post`-type articles,
  same card.php partial as every other article listing), minus that
  template's topic-switcher and most-read sort tab, neither of which
  apply here.
  `front-page.php`'s homepage گزارش section gets its «همهٔ گزارش‌ها» link
  now that `/reports/` exists — the one homepage section that didn't
  already have a "see all" link, for the same reason it had no archive.
  No field or data changes at all — گزارش is still just a tag on
  Articles/Notes.
  Verified locally: temporarily tagged 2 existing articles as گزارش,
  confirmed both the new archive page and the homepage's new "see all"
  link render them correctly, then removed the test tag again
  (`wp post term remove`) to leave real content exactly as it was.
  Theme version bumped 1.2.1 → 1.2.2 (`style.css`).
  Approved by: Farhad, in this session (2026-09-04) — Phase 3 of the
  Technical Scoping Plan.

## 2026-09-05 (Phase 3b of the Build Readiness Plan)
- **Changed:** گزارش (Reports) moved from a `post_tag` term to a
  dedicated `report` taxonomy (`class-taxonomies.php`) — found while
  testing Phase 3: Farhad relayed that the client couldn't find any way
  to mark a post as a report at all. Root cause, confirmed by checking
  the block editor directly rather than assumed: a WordPress "tag" is a
  type-to-add free-text field with no visible, clickable option, unlike
  موضوعات's Categories-style checkbox list — the mechanism worked exactly
  as designed, it just wasn't discoverable for a non-technical editor
  expecting the same checkbox pattern every other classification on this
  site uses.
  `report` is `hierarchical => true` for the same reason
  topic/publication/collection/party_document_category all are (the
  Categories-style checkbox UI, not Tags-style free text), registered
  directly on `post` — confirmed this doesn't hit the same conflict WP's
  built-in `category` has here (removed from `post` in an earlier phase
  specifically because it *is* `category`): `topic` has worked as a
  custom taxonomy on `post` since Phase 3.2 with no such issue.
  `Taxonomies::migrate_legacy_reports_tag()` (admin_init + options-flag,
  same self-healing pattern as seed_publication_periods()/
  migrate_legacy_party_documents()) moves any post already marked under
  the old `post_tag` term onto the new taxonomy's term and removes the
  old tag. Also seeds the new "گزارش" term itself directly inside the
  migration, rather than assuming create_default_terms() already ran —
  caught this exact gap live during testing: create_default_terms() only
  fires on plugin activation, which doesn't re-fire on a code-only
  redeploy, so the very first test run silently found no term to migrate
  onto and marked itself done anyway. Fixed before shipping.
  `front-page.php`'s homepage section and `page-reports.php`'s archive
  both updated from `'tag' => 'reports'` to a `tax_query` against
  `report` — no visible change, same posts, same layout, same "see all"
  link, just reading from the new source. Sidebar panel order
  (`admin/js/panel-order.js`) extended to place گزارش between موضوع اصلی
  and برچسب‌ها.
  Verified locally: simulated a pre-existing legacy-tagged post, ran the
  migration, confirmed the post landed on the new taxonomy with the old
  tag term deleted, confirmed the new گزارش checkbox renders and behaves
  identically to موضوعات in the block editor, and confirmed both the
  homepage section and the `/reports/` archive still show the migrated
  post correctly. Removed the test tag afterward.
  Plugin version bumped 1.1.0 → 1.1.1, theme version bumped 1.2.2 →
  1.2.3.
  Approved by: Farhad, in this session (2026-09-05) — Phase 3b of the
  Technical Scoping Plan.

## 2026-09-05 (Phase 4 of the Build Readiness Plan — final phase)
- **Changed:** homepage (`front-page.php`) reordered and reworked to the
  order locked earlier this session: headline article, تازه‌ها, مقالات,
  اسناد حزب, گزارش, نشریات (شمارهٔ جاری), انتشارات حزب, کتابخانه,
  موضوعات. Five changes, all in this one page:
  1. **مقالات excludes گزارش** — a `tax_query` `NOT IN` against the
     `report` taxonomy, so a report no longer shows in both places; it
     now only ever appears in تازه‌ها (still "everything new," unchanged)
     and its own گزارش section/archive.
  2. **اسناد حزب's homepage section now genuinely queries
     `party_document`** — this position previously queried
     `post_type=document` with no collection filter (a mismatch left
     over from before اسناد حزب became its own real content type,
     Phase 1) under the same "اسناد حزب" label; now the query and the
     label finally agree. Links to `/party-documents/`.
  3. **شمارهٔ جاری retitled to نشریات** — text only, exactly per
     Farhad's "همین‌طور که هست باقی بماند" for everything else about
     this section (query, layout, position relative to انتشارات حزب).
  4. **New کتابخانه section**, after انتشارات حزب — the homepage had no
     Library presence at all before this. Reuses the general-library
     query (`post_type=document`, no collection filter, latest 4) that
     used to live under the old اسناد حزب position, now correctly
     labeled and in its own new spot. New `home_library_heading` label
     key added (`class-label-settings.php`) — `home_latest_documents_
     heading` stays put, now accurately describing the اسناد حزب
     section it always shared a homepage with.
  5. **Dead code removed**: a second, unused `$documents_query` sitting
     near the bottom of the file (confirmed nothing below it ever
     rendered from it) — cleaned up while reworking this page's other
     document queries, not left as leftover clutter.
  Background bands rechecked for alternation: مقالات (cream) → اسناد
  حزب (tint, new) → گزارش (plain) → نشریات (plain) → انتشارات حزب
  (tint) → کتابخانه (cream) → موضوعات (plain) — no two identically-toned
  bands sit adjacent except the one plain/plain pair (گزارش، نشریات)
  that's an unavoidable consequence of both being fixed points Farhad
  asked to leave untouched; a plain-plain repeat is far less visually
  jarring than two matching colored bands would be.
  Verified locally end-to-end: full homepage render confirmed all 9
  sections in the locked order with real content in each; separately
  tagged a test post as گزارش and confirmed it disappeared from مقالات
  and appeared correctly in گزارش's own section, then removed the test
  tag.
  Plugin version bumped 1.1.1 → 1.1.2, theme version bumped 1.2.3 →
  1.3.0 (feature-level bump — this closes out the full Technical
  Scoping Plan, not a small fix).
  Approved by: Farhad, in this session (2026-09-05) — Phase 4 of the
  Technical Scoping Plan. All four phases (plus 3b) are now complete.
