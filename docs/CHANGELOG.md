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

## 2026-09-05 (Phase 5 of the Build Readiness Plan)
- **Changed:** every homepage shelf (`front-page.php`) now has an
  explicit, deliberate item cap, requested after Phase 4 shipped so the
  layout can never overflow or look uneven as content grows. Checked
  every section's actual `posts_per_page` against what was asked rather
  than assuming:
  - تازه‌ها (6 shown), مقالات (6), اسناد حزب (4), گزارش (6), نشریات (1
    per publication) were all already exactly what was requested — no
    change.
  - **انتشارات حزب** was `posts_per_page => 10` — genuinely over the
    requested 5-item limit. Reduced to 5.
  - **کتابخانه** was `posts_per_page => 4` — already under the requested
    5-item limit, but raised to exactly 5 rather than left at a number
    that happened to be under the cap, matching انتشارات حزب's identical
    shelf treatment right above it.
  None of this touches the full archives at `/party-publications/` or
  `/library/` — both still show everything, paginated; only the
  homepage teasers are capped.
  Verified locally: confirmed via a direct DOM count that every section
  renders exactly the expected number of cards, and specifically
  confirmed کتابخانه — which has 28 real documents available locally —
  correctly shows only 5, not all of them.
  Theme version bumped 1.3.0 → 1.3.1.
  Approved by: Farhad, in this session (2026-09-05) — Phase 5 of the
  Technical Scoping Plan.

## 2026-09-05 (Phase 7 of the Build Readiness Plan)
- **Changed:** removed اسناد حزب from the masthead's slim top bar
  (`header.php`, `.mast-pub-nav`) — added there alongside
  نشریات/موضوعات/کتابخانه when Phase 1 shipped, removed the next day per
  Farhad: that row was getting crowded, and اسناد حزب only needs to be
  one click away via the full popup menu, not present in this
  always-visible row on every screen size (desktop included — this
  wasn't a mobile-only ask).
  No change to reachability: اسناد حزب was already, and remains, in the
  popup menu's «بخش‌ها» list (`shola_maybe_add_party_documents_menu_item()`,
  `inc/setup.php`, added the same day as the top-bar entry) — only the
  top-bar duplicate is gone.
  Verified locally: confirmed via the browser that only
  نشریات/موضوعات/کتابخانه remain in the top bar, and that اسناد حزب
  still resolves correctly from the popup menu.
  Theme version bumped 1.3.1 → 1.3.2.
  Approved by: Farhad, in this session (2026-09-05) — Phase 7 of the
  Technical Scoping Plan.

## 2026-09-05 (later still — date-calendar consistency fix)
- **Fixed:** every remaining Gregorian-date display on the site now
  renders Jalali (Dari), matching the rest of the site's dates. Farhad
  flagged this with screenshots — اسناد حزب's homepage shelf, its
  archive and single-document pages, انتشارات حزب's homepage shelf,
  کتابخانه's homepage shelf and single-document pages, and نشریات's
  issue-count year range were all still showing "MAR ۲۰۲۶"-style
  Gregorian dates while every article/announcement date elsewhere on
  the site was already correctly Jalali.
  Root cause: this was not a bug in the Persian Calendar plugin
  integration — it was two deliberate, differing date conventions baked
  into the v6 static prototype and faithfully ported in Phase 4.2
  (2026-08-06): article/announcement dates always used `get_the_date()`
  (filtered to Jalali by Persian Calendar + `shola_convert_jalali_months_to_dari()`),
  while `issue`/`document`/`party_document`/`party_publication` "card"
  and "meta" date labels deliberately used `shola_get_english_month_abbr()`
  + `shola_get_gregorian_year()` — two helpers built specifically to stay
  *immune* to the Jalali-conversion hooks, because v6's own source used
  a literal "MAR ۲۰۲۶" Gregorian convention for those labels. That was
  the correct choice at the time (a faithful port per CLAUDE.md §9), but
  Farhad has now explicitly asked for full calendar consistency instead,
  superseding that earlier v6-fidelity decision for this one element.
  Removed `shola_get_english_month_abbr()`/`shola_get_gregorian_year()`
  entirely (no remaining callers) and replaced them with
  `shola_get_jalali_month_year_label()`/`shola_get_jalali_year()`
  (`inc/template-tags.php`) — both use `wp_date()`, the same call already
  proven to render correctly (masthead runner date,
  `shola_get_masthead_runner()`). Updated every call site:
  `template-parts/cards/issue-card.php` (also dropped the now-incorrect
  `lang="en"` on `.issue-card-date`, since it's Dari text now, not
  English), `single-document.php`, `single-issue.php`,
  `taxonomy-publication.php` (latest-issue meta line),
  `template-parts/search/result.php` (also dropped `lang="en"` on the
  issue search-result byline), and the year-range in
  `shola_get_publication_meta_line()`.
  Verified locally across every affected page (homepage's اسناد
  حزب/انتشارات حزب/کتابخانه shelves, `/party-documents/` archive, a
  single party-document page, a single library-document page, and
  `/publications/` — both the publication-level year range and a
  دوره leaf's latest-issue line and past-issue list) — all Gregorian
  dates are gone, all now show Jalali/Dari (e.g. "اسد ۱۴۰۵",
  "۱۴۰۳–۱۴۰۵"). No plugin change required.
  Theme version bumped 1.3.2 → 1.3.3.
  Approved by: Farhad, in this session (2026-09-05).

## 2026-09-05 (later still — تازه‌ها's redundant "view all" link)
- **Changed:** removed the "همهٔ موضوعات ←" (link to `/topics/`) button
  from تازه‌ها's homepage section header (`front-page.php`). Farhad
  flagged it via screenshot: تازه‌ها is the mixed "everything new" feed
  (articles + reports + documents + issues), not topic-scoped content,
  so a link labeled "all topics" there was a mismatch; it also just
  duplicated the موضوعات section further down the same homepage, which
  already lists every topic directly with no "view all" of its own
  needed. Removed rather than repointed, since تازه‌ها has no single
  dedicated archive of its own to link to (it spans multiple post
  types/CPTs) — every other homepage section's "view all" link points to
  that section's real archive page; تازه‌ها is the one exception with no
  such page, so no link is the correct fix here, not a placeholder one.
  The `home_topics_link_more` label (`inc/template-tags.php`) is left
  defined, now unused, in case a real destination is added later.
  Verified locally: confirmed via the browser that تازه‌ها's header now
  shows only the heading, with no stray link, and no console/debug.log
  errors.
  Theme version bumped 1.3.3 → 1.3.4.
  Approved by: Farhad, in this session (2026-09-05).

## 2026-09-05 (later still — نشریات meta order)
- **Changed:** reordered the homepage نشریات section's issue-meta list
  (`front-page.php`) — دوره (period) now comes before تاریخ نشر
  (publish date), matching Farhad's requested sequence: شماره, دوره,
  تاریخ نشر, فایل PDF. Purely a markup reorder (moved the `$volume`
  block ahead of the date `<dt>`/`<dd>` pair) — no field added, removed,
  or restyled.
  Verified locally via the DOM (`.issue-meta`'s child order) on both
  publication cards (شعله جاوید, جهان برای فتح) on the homepage.
  Theme version bumped 1.3.4 → 1.3.5.
  Approved by: Farhad, in this session (2026-09-05).

## 2026-09-05 (later still — single-issue.php cleanup + site-wide PDF-size digits)
- **Changed:** three issues on the single-issue.php page (flagged by
  Farhad via screenshot, "شعله جاوید · شمارهٔ ۲۰۲۶"):
  1. Dropped the publication name from the H1 — it now shows just
     "شمارهٔ ۲۰۲۶" instead of "شعله جاوید · شمارهٔ ۲۰۲۶". Reason:
     redundant — the breadcrumb and the badge pill directly above the
     H1 already name the publication, and this page is only ever
     reached via that publication's own archive. The now-unused
     `$pub_display` variable (and the docblock explaining it) were
     removed along with it, rather than left as dead code.
  2. Removed the "سردبیر مسئول" (managing editor) row from this page's
     `dl.issue-meta` — Farhad: not wanted here. Per his explicit
     instruction, `shola_get_managing_editor()` and its CMS setting are
     untouched; only this one template's display row is gone.
- **Fixed:** PDF file-size labels site-wide (`509 کیلوبایت`) were still
  in Latin digits — the one remaining inconsistency Farhad hadn't yet
  flagged elsewhere, caught here on the same page ("حجم فایل"). Root
  cause: `size_format()` (like `number_format_i18n()`) doesn't localize
  digits on this fa_AF install, same gap `shola_to_persian_digits()`
  already exists to cover — but the `get_attached_file()`/
  `file_exists()`/`size_format()` pattern was copy-pasted across 7 call
  sites with no digit conversion at any of them. Added
  `shola_get_pdf_size( $attachment_id )` (`inc/template-tags.php`) — one
  place that does the file lookup + `size_format()` + digit conversion —
  and replaced all 7 call sites with it: `single-issue.php`,
  `single-document.php`, `single-party_document.php`,
  `single-party_publication.php`, `front-page.php` (نشریات homepage
  cards), `template-parts/search/result.php` (both its document and
  party_publication branches), and `template-parts/rows/document-row.php`.
  Verified locally: single-issue.php's title/سردبیر/PDF-size all
  confirmed via the rendered HTML (title is now just "شمارهٔ ۲۰۲۶", no
  سردبیر مسئول row, "۵۰۹ کیلوبایت" in Persian digits), and the homepage
  نشریات cards (same helper, via front-page.php) also confirmed showing
  "۵۰۹" — no remaining Latin "509" anywhere it was checked.
  Theme version bumped 1.3.5 → 1.3.6.
  Approved by: Farhad, in this session (2026-09-05).

## 2026-09-05 (later still — publication picker checked_ontop bug)
- **Fixed:** `render_publication_radio_metabox()`'s `wp_terms_checklist()`
  call (`class-taxonomies.php`) didn't pass `checked_ontop`, so it used
  WP core's own default of `true` — this bubbles a checked term (and a
  duplicate of its ancestor branch) to the top of the list, separated
  from its real parent in the tree below. Farhad reported (screenshot,
  2026-09-05) that reopening an issue for edit showed its دوره sitting
  alone at the very top of the نشریات picker with no visible link to
  which نشریه it belonged to — exactly this behavior, and a real
  ambiguity problem for a taxonomy whose entire point is a fixed
  نشریه → دوره hierarchy (added specifically so this couldn't be
  mistaken, Phase 3-era work — see earlier entries). Set
  `'checked_ontop' => false` so the picker always renders in the
  taxonomy's real registered hierarchy, regardless of which دوره is
  currently selected.
  Verified locally: logged in via a temporary auth-cookie script (per
  the established LocalWP testing pattern, deleted after use), opened
  the same issue post from Farhad's screenshot, and confirmed via the
  DOM that the checked دورهٔ اول term now renders correctly nested
  inside its real parent's `<ul class="children">` — no more duplicate,
  floating instance above the list.
  Plugin version bumped 1.1.2 → 1.1.3.
  Approved by: Farhad, in this session (2026-09-05). Farhad asked to be
  checked back with about whether similar `wp_terms_checklist()`/
  `checked_ontop` issues exist elsewhere on the site before closing
  this out — see the next entry once that's done.

## 2026-09-05 (later still — checked_ontop audit across every taxonomy)
- **Changed:** audited every hierarchical taxonomy this plugin registers
  (`topic`, `collection`, `party_document_category`, `report` — `publication`
  already fixed in the entry above) for the same "checked term jumps to
  the top of the list" behavior, per Farhad's follow-up ask. Added
  `disable_checked_ontop_for_own_taxonomies()` (`class-taxonomies.php`),
  hooked on the `wp_terms_checklist_args` filter, forcing
  `checked_ontop => false` for all four — this is the same underlying
  WordPress mechanism as `publication`'s fix, so it covers every place
  on the site that still renders one of these taxonomies through WP
  core's classic checklist markup (confirmed this includes each
  taxonomy's Quick Edit checkbox tree on its post-list screen, since
  that's populated from the same `wp_terms_checklist()` output).
- **Found, not fixed (flagging for a decision):** `post`/`document`/
  `party_document` are all block-editor (Gutenberg) post types, and
  their taxonomy panels in the editor sidebar (موضوعات on a post,
  مجموعه‌ها on a document, دسته‌های اسناد حزب on a party_document) are
  rendered by WordPress core's own React hierarchical-term-selector
  component, not by `wp_terms_checklist()` at all — confirmed by
  testing live (post #175, موضوعات panel: opening it with «اقتصاد» and
  «جنبش کمونیستی بین‌المللی» both checked shows them bumped above
  «افغانستان», which is alphabetically first). That reordering is
  hardcoded into WordPress core's block-editor component itself; no
  server-side filter can turn it off, unlike the classic-meta-box case
  `publication` and this entry's fix both cover.
  Checked via `wp term list` whether this actually creates the
  parent/child ambiguity Farhad originally reported: it doesn't, today —
  `topic` (9 terms), `collection` (3 terms), and `report` (1 term) have
  no parent terms at all (`parent` is `0` for every one), and
  `party_document_category` currently has zero terms. `publication`
  (نشریه → دوره) is the only taxonomy on the site with a real two-level
  structure, and that one is already fixed. Since `party_document_category`
  is explicitly self-managed by staff (see register_taxonomies()), this
  is the one place the same ambiguity could appear later if a category
  is ever given a parent — flagged to Farhad rather than pre-built, since
  a fix here means writing a custom block-editor sidebar panel (materially
  more work than either fix above), not a small filter.
  Plugin version bumped 1.1.3 → 1.1.4.
  Approved by: Farhad, in this session (2026-09-05).

## 2026-09-05 (later still — party_document_category fixed-order panel)
- **Changed:** Farhad asked for the pre-emptive fix flagged in the entry
  above rather than waiting. Added `use_fixed_order_party_document_category_metabox()`
  (`class-taxonomies.php`, hooked on `add_meta_boxes`), which removes
  `party_document_category`'s default block-editor box
  (`party_document_categorydiv` — the ID WordPress's own block-editor
  screen auto-removes in favor of its React panel for any REST-enabled
  taxonomy) and re-adds it under a fresh ID
  (`shcore-party-document-category-fixed-order`) using WordPress core's
  own `post_categories_meta_box()` function directly — not a
  reimplementation, so the "+ افزودن دسته" quick-add form, its nonce,
  and its AJAX handling all keep working exactly as WordPress itself
  built them. Since `post_categories_meta_box()` calls
  `wp_terms_checklist()` internally, it already inherits
  `checked_ontop => false` from the filter added in the previous entry —
  no further change needed there. Same technique already proven for
  `publication`/`issue` (`use_single_select_publication_metabox()`).
  Verified locally: seeded two temporary test terms (a parent and a
  checked child) via a scratch script, confirmed via the DOM that the
  checklist now renders as a classic box (not the React panel — checked
  there was exactly one "دسته‌های اسناد حزب" panel on the screen, not a
  duplicate) with the checked child correctly nested under its parent in
  `<ul class="children">`, no reordering. Deleted the test terms and
  scratch scripts afterward, confirmed via `wp term list` that none
  remain.
  Plugin version bumped 1.1.4 → 1.1.5.
  Approved by: Farhad, in this session (2026-09-05).

## 2026-09-05 (later still — Phase 8: category management, generalized)
- **Added:** generalized category/subcategory management across every
  hierarchical taxonomy this plugin owns (موضوعات, مجموعه‌ها, دسته‌های
  اسناد حزب, گزارش, نشریات) — see the Technical Scoping Plan's Phase 8
  for the full requirements history. Replaced `class-term-reassign.php`
  (2026-09-04's `publication`-only reassign-before-delete flow) with a
  new `class-category-manager.php`, generalized to all five taxonomies
  via one `taxonomy => post_type` map. Four pieces:
  1. **ترتیب (sort order) field**, previously `publication`-only, now on
     every managed taxonomy's Add/Edit term screens and term-list
     column. Method bodies unchanged from the original — only how
     they're hooked (once per taxonomy, not hardcoded to one) is new.
  2. **A permanent "دسته‌بندی‌نشده" (Uncategorized) term per taxonomy** —
     self-healing-seeded (admin_init + options flag, the same pattern
     used elsewhere in this plugin for a code-only zip redeploy that
     doesn't re-fire activation hooks), visible and directly selectable
     by staff (matches WordPress's own native Categories behavior,
     confirmed with Farhad before building). Its "حذف" row action is
     removed so it can't be deleted through the normal UI; since
     WordPress has no filter that can block `wp_delete_term()` outright
     (`pre_delete_term` is an action, not a short-circuit filter — same
     limitation the original `publication`-only version's docblock
     already documented), a `delete_term` safety net immediately
     re-creates it and moves any content still attached back onto the
     fresh term if it's ever deleted anyway via bulk-select, WP-CLI, or
     REST.
  3. **Reassign-before-delete, generalized**, with two flows depending
     on what's being deleted:
     - A **leaf term** (no subcategories) with content: a dropdown of a
       sibling term, the parent term, or Uncategorized — same as the
       original `publication`-only flow, minus the "no destination
       available" dead end it had before (now falls through to
       Uncategorized instead of blocking).
     - A **term with subcategories**: a single confirmation moving
       *everything* under that branch — the term's own content and
       every subcategory's — to Uncategorized in one step, deleting the
       subcategories first and the term itself last (new; the original
       version never had to handle a parent-with-children case).
     The same bulk-delete/WP-CLI/REST safety net as before (now
     generalized to all five taxonomies) falls back to Uncategorized
     instead of doing nothing when there's no sibling.
  4. **A two-level depth cap** (category → subcategory, never a third
     level) — new. Creating a term under a parent that already has a
     parent is rejected outright with a clear error (`pre_insert_term`
     filter, which can cleanly reject with a message). Re-parenting an
     *existing* term into that same situation can't be cleanly rejected
     the same way (`wp_update_term_parent` only filters the parent
     value, it can't stop the rest of the save) — silently keeps the
     term's current parent instead, and flags the post-save redirect so
     an admin notice tells the editor why their change to that one
     field didn't stick. Known, accepted gap: the Parent dropdown itself
     isn't pre-filtered to hide invalid choices, so a level-2 term can
     still be *picked* as a new parent — only rejected/clamped at save
     time. Left as-is rather than also rebuilding the dropdown, since
     the save-time enforcement already prevents any actual bad state.
  Checked, before writing anything, that this couldn't surface
  Uncategorized (or a newly-added category) as unexpected homepage/hub-
  page clutter: every one of those pages (front-page.php's موضوعات/
  نشریات sections, page-library.php's shelf list, page-party-
  documents.php, page-reports.php) reads from either a nav-menu-driven
  ordered list or a hardcoded slug array — never a blind "list every
  term" call — so a new term is invisible on the front end by default,
  exactly like it already was for موضوعات/کتابخانه before this change.
  Verified locally end-to-end via the real admin UI (not just
  WP-CLI): created a temporary parent+child category with a temporary
  test document, ran both the leaf-reassign flow and the cascade flow
  through the actual reassignment screen, confirmed the content landed
  on Uncategorized both times and the terms were gone; separately
  confirmed the depth cap rejects a 3rd-level `wp term create` with the
  expected error, and that re-parenting an existing term with children
  is silently clamped with the expected admin notice. Deleted all test
  terms/posts/scratch scripts afterward, confirmed via `wp term list`
  that only real content remains in every taxonomy touched. No console
  or debug.log errors from any of this — one pre-existing, unrelated
  PHP 8.1 deprecation notice from WordPress core's own
  wp-admin/includes/template.php (`strip_tags(): Passing null`) appeared
  on the hidden reassignment screen, same as it already does elsewhere
  in wp-admin on this PHP version; not something this change introduced
  or something in scope to fix here.
  Plugin version bumped 1.1.5 → 1.2.0 (minor bump, not a patch — this is
  a real capability added across every taxonomy, not a fix).
  Approved by: Farhad, in this session (2026-09-05) — Phase 8 of the
  Technical Scoping Plan.

## 2026-09-05 (later still — Phase 8 follow-up: filter the Parent dropdown)
- **Changed:** the "دستهٔ مادر" (Parent) dropdown on every managed
  taxonomy's Add/Edit term screens (موضوعات, مجموعه‌ها, دسته‌های اسناد
  حزب, گزارش, نشریات) no longer lists a category that already has a
  parent as a choice. Closes a gap Farhad flagged after the previous
  entry shipped: enforce_depth_cap_on_insert()/enforce_depth_cap_on_update()
  already stopped an invalid 3rd-level assignment from actually saving,
  but the dropdown itself still *offered* an already-nested category as
  a pickable option — an editor could choose it and only discover it
  was wrong afterward (an error on create, a silent no-op plus a notice
  on update). Added `filter_parent_dropdown_args()`
  (`class-category-manager.php`), hooked on WordPress core's own
  `taxonomy_parent_dropdown_args` filter (fires for the Parent dropdown
  on both the "Add New Term" and "Edit Term" screens), which excludes
  every term with a non-zero parent from the dropdown's option list.
  The two enforcement methods from the previous entry are unchanged and
  still the actual guarantee — this dropdown filter is a convenience on
  top of them, not a replacement.
  Verified locally: created a temporary top-level test category with a
  child under it and a separate unrelated top-level test category,
  confirmed via the dropdown's own rendered options that the child was
  excluded while the unrelated top-level one still appeared — first on
  دسته‌های اسناد حزب, then separately confirmed on نشریات's real
  production data (شعله جاوید/جهان برای فتح's real دوره children are
  all correctly excluded, only the two نشریه themselves and
  دسته‌بندی‌نشده appear). Noticed partway through that Farhad had
  already been testing this same feature live on the real site
  (real اسناد داخلی/صورت‌جلسات/گزارش مالی categories, matching the
  walkthrough example given in conversation) — left that real content
  untouched and only cleaned up my own temporary test terms afterward.
  No console or debug.log errors from any of this.
  Plugin version bumped 1.2.0 → 1.2.1.
  Approved by: Farhad, in this session (2026-09-05) — Phase 8 of the
  Technical Scoping Plan.

## 2026-09-05 (later still — Phase 8 fix: real content undercounted)
- **Fixed:** the "انتقال و حذف…" safety link was missing from
  دورهٔ دوم/سوم/چهارم on the live site even though Farhad had real,
  in-progress content (unpublished issues) sitting in them — reported
  live via screenshots after uploading v1.2.1. Root cause:
  `filter_row_actions()` decided whether to show the safety link using
  `$term->count`, WordPress core's own cached term count — which only
  reflects **published** posts. Several people actively draft content
  on this site before it's published, so a term holding only drafts or
  pending-review posts showed `count` 0 and fell through to the plain,
  unprotected "حذف" link, exactly as if it were genuinely empty.
  `get_post_ids_for_term()` (used by both submission handlers to
  actually move content) had the same gap: `get_posts()`'s own default
  `post_status` is `publish` only, so even going through the reassign
  screen would have silently left any draft/pending posts behind,
  un-moved, when the term was deleted.
  Added a `REAL_POST_STATUSES` constant (`publish`, `future`, `draft`,
  `pending`, `private` — every non-trashed status) and a new
  `has_any_content_for_term()` helper; both `filter_row_actions()`'s
  decision and `get_post_ids_for_term()`'s actual query now use it
  instead of `$term->count`. Also fixed the item-count numbers shown on
  both the leaf-reassign and cascade-confirmation screens themselves
  (previously `$term->count`/`$child->count`, same undercount) to use a
  real, all-status count so the number displayed matches what will
  actually move. `rehome_orphans()` (the bulk-delete/WP-CLI/REST safety
  net) needed no change — it already reads WordPress's own raw
  `$object_ids` param off `delete_term`, which was never status-filtered
  in the first place.
  Verified locally: created a temporary draft issue under a real,
  previously-empty دوره (دورهٔ دوم), confirmed the row action correctly
  switched to "انتقال و حذف…", ran it through to Uncategorized,
  confirmed the draft (still in draft status, not accidentally
  published) actually moved. Cleaned up the test post afterward, then
  restored دورهٔ دوم itself (deleted as part of the same test — a real,
  meaningful category, not test data) with its original ترتیب value.
  No console or debug.log errors.
  Plugin version bumped 1.2.1 → 1.2.2.
  Approved by: Farhad, in this session (2026-09-05) — Phase 8 of the
  Technical Scoping Plan.

## 2026-09-05 (later still — removed the generic Custom Fields panel)
- **Changed:** removed WordPress core's own generic "زمینه‌های دلخواه"
  (Custom Fields) meta box from every content type's edit screen —
  `issue`, `document`, `party_publication`, `party_document`, and
  `announcement` — by dropping `'custom-fields'` from each one's
  `supports` array in `register_post_type()`
  (`class-post-types.php`). Farhad flagged it via screenshot: this
  panel exposes every `shcore_*` meta field by its raw internal
  programming name (`shcore_issue_number`, `shcore_pdf_id`, ...) with
  no label, no validation, and a plain "به‌روزرسانی"/"حذف" — entirely
  redundant with the proper, labeled fields already on the same screen
  (شمارهٔ شماره, the PDF picker button, etc.), and a real risk:
  `shcore_pdf_id` specifically expects a numeric attachment ID, so
  hand-editing it here could silently break a document's PDF link with
  no warning.
  This only removes the UI panel — `'custom-fields'` support has no
  effect on whether post meta actually saves/reads (that's
  `update_post_meta()`/`get_post_meta()`, unrelated to this flag) or on
  REST API exposure (controlled separately by each
  `register_post_meta()` call's own `show_in_rest`, already set
  per-field in `class-meta-fields.php`). Confirmed no other code
  anywhere in the plugin checks `post_type_supports( ..., 'custom-fields' )`
  before this change.
  Verified locally: opened an `issue` edit screen and a
  `party_document` edit screen, confirmed "زمینه‌های دلخواه" no longer
  appears on either, and confirmed the real, purpose-built meta boxes
  (اطلاعات شماره's شمارهٔ شماره/دوره/PDF picker, اسناد حزب's شمارهٔ
  سریال) still render and still show their real, saved values
  correctly. No console or debug.log errors.
  Plugin version bumped 1.2.2 → 1.2.3.
  Approved by: Farhad, in this session (2026-09-05).

## 2026-09-05 (later still — plugin whitelist addition: FileBird)
- **Added:** `FileBird` to the plugin whitelist (CLAUDE.md §3) for Media
  Library folder organization — Farhad's own idea, raised after seeing
  the Media Library as one large, growing flat pile with no way to
  group PDFs/images/videos. Presented two options first: fully custom
  (a taxonomy-based folder system, reusing this same session's
  `Category_Manager` mechanism, but List-view-only — no drag-and-drop in
  the modern Grid view, since that requires extending WordPress core's
  own internal Backbone.js media JS, an ongoing-maintenance surface, not
  a stable public API) versus a dedicated plugin. Farhad initially
  wanted only a genuinely free/open-source option, not a paid one.
  Verified directly on WordPress.org before recommending it (not from
  memory): 200,000+ active installs, 4.7★ (1,121 reviews), last updated
  August 2026 — actively maintained; confirmed the *free* version
  includes unlimited folders/subfolders and full drag-and-drop (not a
  crippled "lite" tier) — the paid tier only adds unrelated features
  (posts/pages folders, page-builder integrations, ZIP export).
  Downloaded FileBird v6.5.8 directly from WordPress.org, installed and
  activated on the local test site: confirmed folder creation works
  (including a Persian-named test folder, no encoding issues), and that
  the sidebar/UI renders correctly alongside the existing media grid.
  File-to-folder drag-and-drop specifically wasn't confirmed end-to-end
  in this session — automated browser testing can't reliably simulate
  native HTML5 drag-and-drop, and this was cut short by the session
  ending before an alternative (a context-menu/dropdown-based "move to
  folder" action, likely present) could be checked. Not a concern given
  FileBird's own install base and reviews already vouch for this exact
  feature working; Farhad can verify it directly once installed.
  FileBird is a public WordPress.org plugin (unlike this project's own
  `shola-core`), so it installs the standard way — **افزونه‌ها → افزودن
  افزونه** → search "FileBird" → Install Now → Activate — not a manual
  zip upload, and it will receive its own update notifications through
  WordPress core going forward like any other normal plugin.
  Approved by: Farhad, in this session (2026-09-05).

## 2026-09-06 (Phase 9 of the Technical Scoping Plan — تازه‌ها narrowed to articles only)
- **Changed:** `front-page.php`'s تازه‌ها section no longer includes
  کتابخانه (`document`) — it queries `post_type => 'post'` only now,
  same as it did before 2026-09-02's decision to widen it into a mixed
  "everything new" feed (articles + documents). **This reverses that
  earlier, explicitly-confirmed decision** — flagging it as a reversal,
  not a bug fix, since the 2026-09-02 choice is still logged further up
  this file. Reported by the client after testing on the live domain:
  uploading a library book made it appear in تازه‌ها ("recent"), which
  read as wrong in practice — کتابخانه already has its own dedicated
  homepage shelf, and content should only ever surface there.
  Audited the rest of the homepage first, before changing anything:
  confirmed شماره‌های نشریه (`issue`), انتشارات حزب
  (`party_publication`), and اسناد حزب (`party_document`) were never
  wired into تازه‌ها's query in the first place — only `document` was —
  so there was nothing to cut for those three.
  Simplified the hero-selection logic alongside the query change: with
  only one post type possible now, the "is this one an article"
  type-check that used to pick the hero out of a mixed list is dead
  code — replaced with a plain `array_shift()`. Same simplification for
  the card-type branch in the grid loop (always `'article'` now, not a
  ternary). Updated three stale comments referencing the old
  mixed-feed reasoning (the query's own docblock, the now-removed
  "همهٔ موضوعات" link's removal note, and مقالات's own comment about
  "duplication with تازه‌ها being expected") so the file doesn't
  contradict itself for the next person reading it.
  Verified locally: published a temporary library item dated "right
  now" and confirmed — by checking which homepage section actually
  contained it — that it appeared only in کتابخانه's shelf, not in
  تازه‌ها. Deleted the test item afterward. No console or debug.log
  errors.
  Theme version bumped 1.3.6 → 1.3.7.
  Approved by: Farhad, in this session (2026-09-06) — Phase 9 of the
  Technical Scoping Plan.

## 2026-09-07 — Phase 10 (homepage: تازه‌ها merged into مقالات)
- **Changed:** `front-page.php`'s standalone تازه‌ها "recent" grid section
  (hero + 6 more article cards) removed entirely, after Farhad relayed
  the client's conclusion — following the Phase 9 discussion above —
  that تازه‌ها and مقالات had become the same thing (تازه‌ها being
  مقاله-only since Phase 9) with no real distinction left between them.
  Rather than keep two sections showing near-identical content, the
  homepage now shows one: the hero (still a single latest مقاله, same
  markup/featured-image handling as before, just simplified from a
  7-post query + `array_slice` down to a plain 1-post query since the
  grid it used to sit above is gone), immediately followed by مقالات —
  renamed **تازه‌ترین مقالات** (heading and aria-label both) to make its
  now-dual "recent + articles" role explicit — sitting directly under
  the hero instead of below اسناد حزب/گزارش... previously did. No change
  to مقالات's own query (still 6 posts, still excludes `report`-taxonomy
  posts, still no exclusion against the hero — same accepted-duplication
  reasoning already in place since 2026-09-02).
  Every other homepage section (گزارش, اسناد حزب, نشریات, انتشارات حزب,
  کتابخانه, موضوعات) is untouched — same query, same position relative
  to each other, only shifted up by the removed تازه‌ها block. Checked
  background-band alternation after the removal: cream (تازه‌ترین
  مقالات) → paper (گزارش) → tint (اسناد حزب) → paper (نشریات) → tint
  (انتشارات حزب) → cream (کتابخانه) → paper (موضوعات) — no two adjacent
  sections share a background, unaffected by the reorder.
  `home_articles_section_aria` and `home_latest_heading`
  (`class-label-settings.php`) — the client-editable admin labels تازه‌ها
  used to render from — are now unused on the homepage. Left registered
  rather than deleted, so a value an editor already saved isn't silently
  lost, but their settings-page descriptions are now prefixed
  "(غیرفعال — بخش «تازه‌ها» از صفحهٔ اصلی حذف شد)" so no editor is left
  editing a field with no visible effect and no explanation why.
  Verified locally: homepage loads with zero PHP/console errors; hero
  renders once at the top, تازه‌ترین مقالات renders directly below it
  with 6 cards (including the same post as the hero, as expected), every
  section below it in the original order, nothing duplicated or missing.
  Theme version bumped 1.3.7 → 1.3.8. Plugin version bumped 1.2.3 →
  1.2.4 (label-settings description text only).
  Approved by: Farhad, in this session (2026-09-07) — Phase 10 of the
  Technical Scoping Plan.

- **Fixed:** Farhad reported (screenshot) a visible blank gap between the
  hero and تازه‌ترین مقالات on the live-equivalent local build, right
  after the Phase 10 merge above shipped. Root cause: `.hero-lead`'s
  `margin-block: 0 5rem` (80px) was tuned back when the section
  immediately below it was تازه‌ها's own plain-white `.wrap.sect` — the
  80px gap was there the whole time, just invisible on a white-on-white
  background. Once مقالات (renamed تازه‌ترین مقالات) took that position
  with its `.sect-cream` background, the same 80px rendered as an
  obvious dead white band before the cream started. Not a new bug
  introduced by the merge — a pre-existing gap the merge made visible.
  Reduced to `margin-block: 0 2rem` (32px) — confirmed via
  `getBoundingClientRect()` in a live local session: 81px gap before,
  33px after; `main.css` reload confirmed picking up the new
  `ver=1.3.8` query string, not a stale cache. `.hero-lead` is only used
  on the homepage (grepped the theme first to confirm), so this change
  has no effect anywhere else.
  Theme version bumped 1.3.8 → 1.3.9.
  Approved by: Farhad, in this session (2026-09-07) — Phase 10 of the
  Technical Scoping Plan.

- **Changed:** Farhad asked for the gap above to be removed completely,
  not just shrunk — `.hero-lead`'s bottom margin reduced 2rem → 0.
  Verified live: `gapHeroToSect` (`getBoundingClientRect()` difference
  between the hero's bottom edge and تازه‌ترین مقالات's top edge) is now
  1px — exactly the `<hr class="rule wrap">` divider's own height, i.e.
  no gap left at all beyond the intentional dividing line. `main.css`
  reload confirmed serving the new rule (`marginBottom: "0px"` via
  computed style).
  Theme version bumped 1.3.9 → 1.3.10.
  Approved by: Farhad, in this session (2026-09-07) — Phase 10 of the
  Technical Scoping Plan.

## 2026-09-07 (later same day) — Phase 11 (اطلاعیه spotlight tile)
- **Added:** the latest اطلاعیه now gets its own accent-colored tile
  inside تازه‌ترین مقالات's card grid, per Farhad's screenshot marking
  exactly where — the grid's own visually-leftmost slot, spanning both
  rows on desktop, replacing the 2 article cards that used to sit there.
  Evaluated with Farhad before building (per his own request): confirmed
  as a standard, well-understood editorial-grid pattern (a "spotlight"
  tile inside a card grid, the same trick used for call-out blocks on
  other news sites), not a redesign risk, since the hero stays untouched
  and the change is scoped entirely to one grid's internal layout.
  New: `template-parts/cards/announcement-spotlight.php` — renders the
  single latest `announcement` post (title, excerpt, date — `announcement`
  has no featured image, so no `.card-media` here, unlike card.php) with
  its own "همهٔ اطلاعیه‌ها" link to the existing `/announcements/` archive.
  Deliberately does not share تازه‌ترین مقالات's own "همهٔ مقالات" link —
  two different content types, two different ways out, per Farhad's own
  instinct that this needed "its own section."
  `front-page.php`: added a 1-post `announcement` query; when it has a
  result, the article count for تازه‌ترین مقالات drops 6 → 4 so the grid
  reads as a clean 2×2 of articles beside the tile instead of an uneven
  leftover column; falls back to 6 articles (no tile) on the (currently
  hypothetical — 8 announcements exist live) chance there are none, so
  the grid is never short a row for no visible reason.
  `main.css`: new `.card-spotlight` — the first fully-saturated
  background this design system uses anywhere (every other section is
  paper/cream/tint). Uses `--maroon`, not `--crimson` — darker/more
  restrained than the accent crimson used for hover states/kickers
  elsewhere, so an official اطلاعیه reads as its own register rather than
  "another crimson thing." No new color token added, per CLAUDE.md §5/§9.
  Responsive, checked at all three breakpoints (not just assumed from the
  CSS): **desktop** (≥1000px, 3-col grid) — tile explicitly placed in
  column 3 (the visually-left column under this site's `dir="rtl"`,
  confirmed via `getBoundingClientRect()`: tile at grid-relative `left:0`,
  spanning `top:10` to `984`px, exactly the height of 2 stacked cards +
  gap), 4 article cards auto-fill the remaining 2×2 via
  `grid-auto-flow: dense` regardless of DOM order. **Tablet** (720–999px,
  2-col grid) — no room for a tall side panel next to a sensible 2×2, so
  the tile spans the full row width instead (`grid-column: 1 / -1`) and
  switches to a horizontal (`flex-direction: row`) layout, sitting above
  the article grid — confirmed live at 820px: 741px-wide banner, 210px
  tall. **Mobile** (<720px, 1-col) — tile stacks like any other card,
  full width, column layout — confirmed live at 375px: 335px square,
  first in the stack, zero console errors, zero layout overflow.
  Verified zero console/PHP errors at every breakpoint tested; confirmed
  the tile's "همهٔ اطلاعیه‌ها" link resolves to the real, already-existing
  `/announcements/` archive (not a placeholder).
  Theme version bumped 1.3.10 → 1.4.0 (new template part + new grid
  behavior — feature-level bump, not a patch).
  Approved by: Farhad, in this session (2026-09-07) — Phase 11 of the
  Technical Scoping Plan.

- **Changed:** Farhad flagged from a live homepage screenshot, right
  after Phase 11 shipped, that the spotlight tile — spanning both grid
  rows to show 1 اطلاعیه — left most of its own height empty, and made
  the whole homepage read as unusually long/thin on content. Reworked
  same day: tile now spans **1** grid row (half its previous height —
  confirmed live: 974px → 467px at desktop, matching a single article
  card exactly, zero content overflow at all 3 breakpoints tested), and
  shows **3** اطلاعیه‌ها instead of 1 — the newest one prominent (title +
  excerpt + date, unchanged from before), the other 2 as a compact list
  below it (`.card-spotlight-more`: title + date only, thin dividers, no
  excerpt) — so it's still obvious which is newest, but the tile is
  naturally full rather than empty.
  `front-page.php`: `$announcement_query` now pulls 3 posts instead of 1
  (`template-parts/cards/announcement-spotlight.php`'s `$args` changed
  from a single `post` to a `posts` array accordingly); article count
  for تازه‌ترین مقالات adjusted 4 → 5 to match the tile now occupying 1 of
  the grid's 6 cells instead of 2 — confirmed live at desktop: all 6
  cells filled, no gap, no overlap.
  Approved by: Farhad, in this session (2026-09-07) — Phase 11 of the
  Technical Scoping Plan.

- **Changed:** two more fixes from Farhad's live-screenshot review of
  the spotlight tile. (1) Tile background changed `--maroon` →
  `--winston-red`, matching the masthead's own background exactly
  (confirmed live: both compute to `rgb(204, 0, 0)`) — a deliberate
  widening of Winston Red's original scope (Phase A, 2026-08-24:
  documented at the time as "scoped only to the masthead background")
  to a second, explicitly client-approved use, not silent scope creep.
  (2) تازه‌ترین مقالات's own "همهٔ مقالات" link — via `.section-head`'s
  plain space-between — landed at the grid's true left edge, which is
  where the spotlight tile sits, not the article columns the link
  actually points to; Farhad flagged this as reading "irrelevant" from
  a screenshot circling exactly that mismatch. Fixed with a new
  `.section-head--with-spotlight` modifier (added only when a spotlight
  renders): pads the header row's end side by exactly one grid column +
  gap at desktop, so the link's edge lands at the boundary between the
  spotlight and article columns instead of the container's true edge —
  confirmed live: spotlight tile spans 0–357px, link's own left edge
  sits at 389px (357 + the 32px column gap), i.e. now aligned with
  where the article cards actually start. Confirmed the fix is scoped
  correctly: at tablet width the new modifier's padding computes to
  `0px` (media query doesn't apply there), so the tile's own
  full-width-banner layout at that breakpoint is unaffected.
  Approved by: Farhad, in this session (2026-09-07) — Phase 11 of the
  Technical Scoping Plan.

- **Changed:** Farhad asked (as a design review, not a bug report) to
  check the tile for legibility/contrast/hierarchy issues, since it felt
  "a little off" and hard to read — and asked specifically for a way to
  tell the 1st/2nd/3rd اطلاعیه apart "without being puzzled." Checked
  the actual numbers rather than going by feel: the compact list's dates
  (55% white) and a few other secondary-text colors, computed against
  `--winston-red` (`#CC0000`) using the real WCAG relative-luminance
  formula, blended for alpha rather than assumed, came out around
  **3.0:1** — below the 4.5:1 floor WCAG AA requires for text this size.
  Root cause: hierarchy was being conveyed entirely through opacity,
  which directly fights legibility (lower opacity = lower contrast) —
  worse on `--winston-red` than it was on the darker `--maroon` this
  tile started with.
  Fixed two ways together, not just one: (1) added explicit numbered
  index badges (۱/۲/۳, `.card-spotlight-index`,
  `template-parts/cards/announcement-spotlight.php`) so order is stated
  outright instead of left for a visitor to infer from size/opacity —
  directly answers Farhad's "1st/2nd/3rd" ask; (2) raised every
  secondary text color that was below-threshold, then **verified live**
  with `getComputedStyle()` + the actual contrast formula (not
  re-eyeballed) — every text element in the tile now measures ≥4.7:1
  against `--winston-red` except the eyebrow label at 4.71:1, all
  clearing 4.5:1 with room to spare. Divider line opacity also raised
  (0.2/0.12 → 0.3/0.2) so the separation between the prominent item and
  the compact list is actually visible, not just implied.
  Confirmed no regressions from the added badges: tile height unchanged
  at all 3 breakpoints (467px desktop / 342px tablet / 425px mobile,
  `scrollHeight === clientHeight` at each — zero overflow), zero
  console errors.
  Approved by: Farhad, in this session (2026-09-07) — Phase 11 of the
  Technical Scoping Plan.

- **Fixed:** Farhad asked what happens if an اطلاعیه title runs long —
  whether the tile has a real ceiling or could grow unbounded. Checked
  rather than assumed: the featured item's title (`.h-card`, inside
  `.card-spotlight-item--featured`) had no length cap anywhere — unlike
  `.card-dek` just below it (already clamped to 2 lines) — so an
  unusually long title had nothing stopping it from growing past the
  tile's normal height. Since this tile shares a CSS Grid row with 2
  article cards (`align-items: stretch` is the grid default), that
  extra height would have stretched the whole row, undoing today's
  earlier "half the height" fix. The compact list's 2 smaller items
  were already safe — `.card-spotlight-more a` already truncates to a
  single line via `white-space: nowrap` + ellipsis.
  Fixed by capping the featured title at 2 lines
  (`-webkit-line-clamp: 2`), the same device already used on
  `.card-dek` and elsewhere sitewide. Verified with a real test post,
  not just reasoning about the CSS: temporarily created a live
  `announcement` (WP-CLI, DB_HOST switched to `127.0.0.1:10090` and
  back per the usual local-testing procedure) with a deliberately
  excessive ~130-character title, confirmed live via
  `scrollHeight`/`clientHeight` that the title actually clamps
  (87px of content in a 58px box) while the tile itself shows zero
  overflow, then deleted the test post and confirmed the homepage
  reverted to its real content.
  Scope note: regular article-card titles (`template-parts/cards/card.php`'s
  `.h-card`) still have no clamp anywhere on the site — pre-existing,
  unrelated to this tile, and out of scope for a fix scoped to the
  اطلاعیه spotlight; flagging it here rather than silently changing a
  site-wide pattern beyond what was asked.
  Approved by: Farhad, in this session (2026-09-07) — Phase 11 of the
  Technical Scoping Plan.

## 2026-09-07 (later same day) — Phase 12 (full homepage design review)
- **Changed:** Farhad asked for a full UX/UI review of the homepage —
  spacing, sizing, contrast, sharpness — comparing it against
  international editorial-site standards, describing the overall feel
  as "shallow" with spacing that read as too wide. Reviewed with a
  written checklist shared and confirmed with Farhad first (vertical
  rhythm, sparse/low-item sections, typography scale, contrast
  site-wide, icon/card treatment, alignment consistency, topics list,
  footer, mobile/tablet), then went through it methodically rather than
  restyling by feel — every finding below was measured live, not
  eyeballed.
  **1. Vertical rhythm tightened** (site-wide, since these are shared
  utility classes used well beyond the homepage): `.sect` padding-block
  4rem → 3rem; `.section-head` margin-bottom 2.5rem → 2rem; `.grid-cards`
  gap 2.5rem/2rem → 2rem/1.5rem; `.issue-grid` gap 3rem/2rem →
  2rem/1.5rem. `.section-head--with-spotlight`'s alignment formula
  (added earlier today, Phase 11) updated to match the new 1.5rem grid
  gap — re-verified live afterward: still lands exactly at the leftmost
  article card's edge (387px at desktop), not just assumed to still
  work after the gap changed.
  **2. Sparse-grid fix** — اسناد حزب and انتشارات حزب specifically read
  as empty in Farhad's screenshot: both are `.sect-tint` sections with
  only 2 seeded items, and `.issue-grid`'s old fixed `repeat(5, 1fr)`
  desktop layout squeezed those 2 items into 2 of 5 equal columns,
  leaving ~60% of the row as dead space. Changed to
  `repeat(auto-fit, minmax(160px, 210px))` with `justify-content: start`
  (≥640px only — mobile's `repeat(2, 1fr)` already filled correctly with
  2 items and wasn't part of the complaint): columns now collapse to
  match actual item count instead of reserving unused width. Deliberately
  capped at 210px rather than left as `minmax(160px, 1fr)` — uncapped,
  2 items would each grow to ~550px wide, and at this card's 3:4 aspect
  ratio that's a ~730px-tall thumbnail, far more dominant than a shelf
  card should be. Verified live: both sections' cards now render at a
  clean 210px (desktop) / 210px (tablet) / 156px (mobile), not stretched,
  not squeezed.
  **3. Contrast — found via a systematic sweep, not spot-checking**: ran
  a script over every text node on the live homepage
  (`getComputedStyle` + the real WCAG contrast formula, effective
  background resolved per element) rather than checking colors by eye.
  Found `.issue-card-date` (`--stone`, `#6E6E6A`) on `.sect-tint`'s
  `--crimson-tint` background at **3.94:1** — below the 4.5:1 AA floor —
  affecting exactly اسناد حزب and انتشارات حزب, the same two sections
  flagged as visually "off." Fixed with `.sect-tint .issue-card-date {
  color: var(--ink-soft); }` — verified live afterward at **11.04:1**.
  Every other flagged node from the sweep was a false positive from the
  script not accounting for the hero's background *image* (defaults to
  assuming white), not a real issue — confirmed by re-checking those
  against the actual photo+scrim treatment already verified earlier
  this session.
  Verified zero console errors and zero layout regressions (including
  the earlier spotlight tile) at desktop (1440px), tablet (820px), and
  mobile (375px) — Farhad specifically asked mobile/tablet not be
  skipped in this review.
  Theme version bumped 1.4.4 → 1.5.0 (site-wide spacing-scale change
  across shared utility classes, not a homepage-only patch).
  Approved by: Farhad, in this session (2026-09-07) — Phase 12 of the
  Technical Scoping Plan.

- **Changed:** Farhad reviewed the shipped tile live and flagged the
  small uppercase "اطلاعیه" eyebrow label as buried — easy to miss at a
  glance despite the tile's own bold red background, asking for
  something that "attracts attention pretty much in an instance,"
  offering background-color, icon size, or general sizing as options.
  Diagnosed why raising its own opacity further (like the earlier
  contrast fixes) wouldn't help here: a same-color label can't get any
  louder against a background that's already fully saturated — the fix
  is flipping polarity instead. Reworked into a solid white flag/badge
  (`background: var(--paper)`, `color: var(--winston-red)`, bold,
  letterspaced, padded) that reads instantly against the red tile,
  rather than a new one-off shape: same solid-background + colored-text
  + uppercase treatment `.badge-current` already uses elsewhere on the
  site, just inverted for this darker background, and still no
  border-radius anywhere (this design has no rounded/pill elements —
  see .card's "no borders, whitespace only" principle). Icon enlarged
  12px → 16px and given the same red-on-white treatment.
  Verified live: badge renders at 84×38px, contrast 5.89:1 (comfortably
  above WCAG AA), zero tile overflow at desktop (1440px), tablet
  (820px), and mobile (375px, screenshot-confirmed).
  Approved by: Farhad, in this session (2026-09-07) — Phase 11 of the
  Technical Scoping Plan.

- **Fixed:** Farhad reported from a real-device screenshot (iPhone 16
  Pro Max, 440px wide — still inside the <720px single-column layout)
  that the spotlight tile rendered directly under تازه‌ترین مقالات's own
  heading, above every article card, at that width. Correct on desktop,
  where the tile is a deliberate side-by-side column — wrong on a
  single-column mobile stack, where top-to-bottom position reads as
  "read this first," and an اطلاعیه isn't meant to outrank the section's
  own articles there. Explicit instruction: mobile only, articles first,
  tile after — desktop untouched.
  Fixed with `order: 1` on `.card-spotlight` (mobile-only, i.e. below
  the existing 720px breakpoint), reset back to `order: 0` inside the
  `≥720px` media query that already exists for the tile's tablet/desktop
  layout — so tablet and desktop pick up zero change. The tile's DOM
  position is untouched (still first in markup — screen readers and any
  no-CSS fallback still meet it first, unaffected by a purely visual
  `order` reorder).
  Verified live at all 3 breakpoints, not just the one that changed:
  **375px** (and Farhad's own 440px) — tile now renders last, after all
  5 article cards, confirmed via each grid item's own `getBoundingClientRect()`
  top position, not just visually. **820px** (tablet) — tile still first,
  pixel-identical top position to before this fix. **1440px** (desktop)
  — tile still spans the grid's third column across both rows,
  pixel-identical left/top coordinates to before this fix.
  Approved by: Farhad, in this session (2026-09-07) — Phase 11 of the
  Technical Scoping Plan.

## 2026-09-07 (later same day) — Phase 13 (sticky/shrink masthead)
- **Added:** sticky, shrinking masthead across the whole site, per
  Farhad's request for the pattern common on international editorial
  sites — full-size at page top, then a narrower, compact bar once the
  page scrolls, on desktop and mobile alike. Farhad's explicit
  constraints, gathered before building: only the date line under the
  title (`.mast-runner`) disappears — every other element (title,
  نشریات/موضوعات/کتابخانه, دربارهٔ ما/اطلاعیه‌ها/تماس, search, the menu
  button) stays, scaling down together in proportion, with better
  spacing, and the title must stay easily readable at the smaller size.
  **Mechanism**: `.masthead` changed from `position: relative` to
  `position: sticky; top: 0` — plain CSS, so it stays sticky even with
  JavaScript disabled, per this project's progressive-enhancement rule.
  Offset below the WP admin toolbar when logged in
  (`body.admin-bar .masthead`), using WP core's own two admin-bar
  heights (32px desktop, 46px below its own 782px breakpoint) — not a
  value invented for this theme.
  `main.js`: a new `IntersectionObserver` watches `#mast-sentinel`
  (`header.php` — an invisible 1×1px marker fixed 80px from the top of
  the page, positioned via `position:absolute` with no positioned
  ancestor so it holds that document coordinate while the page scrolls
  under it). Once it scrolls out of view, `.masthead` gets
  `.is-scrolled`. Same IntersectionObserver technique already used for
  this theme's scroll-reveal animations — cheaper than a raw
  scroll-event listener recalculating every frame. Deliberately does
  NOT also refresh `--masthead-h` (the existing JS-computed variable
  `.hero-media` sizes itself against) on this toggle — tried that first,
  reverted after live testing showed it makes `.hero-media` grow taller
  exactly when the masthead compacts (a smaller `--masthead-h` means
  `calc(100dvh - masthead-h)` computes larger), shifting page content
  under the user mid-scroll. `--masthead-h` only ever needs the
  full-size masthead height for the hero's one-time initial-viewport
  fit, never the current scroll-time height.
  **Scaling**: one custom property, `--mast-scale` (1 normally, 0.68
  once `.is-scrolled`), multiplies every sized value in the masthead —
  padding, gaps, font-sizes, icon dimensions — via `calc()`, so the bar
  shrinks as one proportional unit instead of several independently-
  tuned values that could drift out of ratio with each other. A `max()`
  floor (0.75rem) was added to `.mast-btn`'s font-size after live
  testing showed the bare `calc()` alone would land the already-smaller
  utility row (اطلاعیه‌ها/تماس/دربارهٔ ما, 0.85rem base) at ~9.25px —
  too small to read comfortably, letterspaced uppercase besides; the
  floor keeps every nav label at a legible ~12px minimum while padding/
  gaps/icons above it still shrink fully, honoring Farhad's explicit
  "readable" requirement rather than shrinking indiscriminately.
  `.mast-runner` collapses via `max-height`/`opacity`/`margin-top`
  transitions (not `display: none`, which can't animate), so it fades
  out smoothly rather than snapping away.
  All transitions guarded under `prefers-reduced-motion: reduce`
  (matches this theme's existing `.card-media img` pattern) — compact
  toggle happens instantly, no animation, for visitors who've opted out
  of motion.
  Verified live at both breakpoints (not assumed from the CSS): **desktop
  (1440px)** — masthead height 113px → 69px, title 30.72px → 20.89px
  (comfortably readable), نشریات/موضوعات/کتابخانه and
  دربارهٔ‌ما/اطلاعیه‌ها/تماس both floor at 12px, sticky `top: 32px` under
  the logged-in admin bar, confirmed via `getComputedStyle` after a real
  scroll. **Mobile (375px)** — masthead height 115px → 58px (~50%
  reduction), title 23.04px → 15.67px, sticky `top: 46px` (WP's mobile
  admin-bar height), date line fully hidden
  (`opacity:0; max-height:0px`). Confirmed the hamburger menu still opens
  correctly while the masthead is in its compact state. Zero console
  errors at either breakpoint.
  Approved by: Farhad, in this session (2026-09-07) — Phase 13 of the
  Technical Scoping Plan.

## 2026-09-07 (later same day) — Phase 14 (metabox field descriptions)
- **Added:** a short (10-15 word) Persian description under every custom
  metabox field across the site, per Farhad's request to help whoever
  does data entry know exactly what belongs in each field. Covers all 5
  metaboxes registered in `class-meta-fields.php` — اطلاعات شماره
  (`issue`: شمارهٔ شماره, دوره/جلد), اطلاعات سند (`document`: نویسنده/
  منبع), اطلاعات اثر (`party_publication`), اطلاعات سند
  (`party_document`: شمارهٔ سریال), and اطلاعات مقاله (`post`: نام
  مستعار نویسنده, توضیح همکاری, شناسهٔ نوشتهٔ ترجمه) — plus the shared
  فایل PDF and زبان fields reused across several of those metaboxes.
  Also cleaned up the article metabox's 3 fields that previously crammed
  their explanation into the field label itself as a long parenthetical
  (e.g. "نام مستعار نویسنده (اختیاری — در صورت خالی بودن، نام کاربر
  وردپرس نمایش داده می‌شود)") — split back into a short label plus a
  proper description underneath, matching every other field on the site
  and WP admin's own convention.
  `shcore_term_order`'s ترتیب field (`class-category-manager.php`,
  the term add/edit screens) already had a description from earlier
  work this project — confirmed live, left untouched, not duplicated.
  Fixed an HTML-validity issue caught while writing this: nesting
  `<p class="description">` directly inside the `<p>` that already wraps
  each field's `<label>`/`<input>` is invalid (a `<p>` cannot contain
  another block-level `<p>`) — every description was placed as its own
  sibling `<p>` immediately after the field's own `<p>` closes instead,
  matching the pattern the term-order field (`<td><input><p
  class="description">`) already used correctly.
  Verified live in wp-admin, not just read from the code: opened the
  "add new" screen for all 5 post types (شماره, سند [کتابخانه], اثر,
  سند [حزب], نوشته) while logged in as a real admin user (temporary
  `shola-test-login.php`, per the usual local-testing procedure —
  deleted immediately after), confirmed every field shows its label
  followed directly by its new description, `php -l` clean.
  Plugin version bumped 1.2.4 → 1.3.0 (admin-UI-only, no data-model or
  behavior change — a feature-level bump for the amount of surface
  covered, not a patch).
  Approved by: Farhad, in this session (2026-09-07) — Phase 14 of the
  Technical Scoping Plan.

## 2026-09-08 — Phase 15 (live-only bugs surfaced by real content)
- **Fixed:** Farhad reported two live-only bugs after uploading v1.6.0 to
  sholajawid.com with real content — neither reproduced with this
  session's local test data, so both were diagnosed directly against
  the live site (`fetch()`-verified the live `main.css` matched what was
  shipped, byte for byte in the relevant rules — ruling out a caching/
  upload problem before looking for a code bug) rather than guessed at.
  **1. اطلاعیه tile and the article cards beside it rendering visibly
  bigger than local.** Confirmed live via `getComputedStyle`:
  `.grid-cards--with-spotlight`'s 3 columns were NOT equal
  (321.75px / 321.77px / 444.48px) despite `grid-template-columns:
  repeat(3, 1fr)`. Root cause: grid *and* flex items default to
  `min-width: auto`, not `0` — `.card-spotlight-more a` has
  `white-space: nowrap` (intentional, for its ellipsis truncation) and
  `flex: 1`, so a genuinely long اطلاعیه title (the real one live —
  "اعلامیه مشترک احزاب و سازمانهای مارکسیست‑لنینیست‑ مائوئیست") refused
  to shrink below its own text width, forcing `.card-spotlight`'s own
  grid track wider than an equal 1fr share and stealing width from the
  article-card columns beside it. This session's local test titles were
  all short enough by coincidence to never hit this. Fixed with
  `min-width: 0` on `.card-spotlight` itself and on both nested flex
  containers that could independently hit the same floor
  (`.card-spotlight-item--featured` + its inner `<div>`,
  `.card-spotlight-more a`) — `overflow: hidden`/`text-overflow:
  ellipsis` were already correct, they just couldn't take effect while
  the min-content floor was still in force.
  Verified by reproducing the exact live conditions locally (WP-CLI,
  DB_HOST switched per the usual procedure): created a temporary
  اطلاعیه with that same real title, confirmed the 3 columns computed
  to 362.656px / 362.672px / 362.656px — equal within sub-pixel
  rounding — then deleted the test post.
  **2. A 5th انتشارات حزب card wrapping to its own row** instead of all
  5 sitting in one line, once the client uploaded a real 5th item.
  Confirmed live: `.issue-grid`'s content width is 1136px
  (`--wrap-wide` 1200px minus 2rem padding per side); the `auto-fit`
  fix from Phase 12 capped each card at `minmax(160px, 210px)`, and 5
  cards at that 210px cap plus 4×1.5rem gaps need 1146px — 10px more
  than the row actually has, so the 5th had nowhere to go. This
  session's earlier local verification only ever had 2 real items in
  this section (the client hadn't uploaded the rest yet), so the
  "does 5 actually fit" case was never tested, only the "few items"
  case the fix was originally built for. Corrected the cap to 200px
  (5×200 + 4×24 = 1096px, ~40px to spare instead of a cap a few pixels
  too tight for this exact container). Verified by creating 3 temporary
  test party_publication posts locally (bringing the real total to 5,
  matching live) — confirmed all 5 render in one row at 200px each,
  zero wrap — then deleted the test posts.
  **3. Also fixed while investigating** (Farhad's third, smaller ask,
  same live report): `.issue-card-title` — used on every `.issue-grid`
  shelf (انتشارات حزب, اسناد حزب, کتابخانه) — never set its own
  `line-height`, so a wrapped 2-line title inherited `body`'s
  `line-height: 1.95`, tuned for long-form paragraph text, not a small
  14px card title. Every other heading style on the site already sets
  its own tighter line-height; this was the one left out. Set to `1.4`,
  matching that convention — confirmed live: 19.6px computed line-height
  at the 14px font-size, down from ~27.3px.
  Confirmed no regressions at tablet width (820px): the 5-card row
  naturally wraps to 3+2 there, which is expected at that narrower
  width and was never part of the complaint — not re-capped to force
  one row where the viewport genuinely has no room for it.
  Theme version bumped 1.6.0 → 1.6.1.
  Approved by: Farhad, in this session (2026-09-08) — Phase 15 of the
  Technical Scoping Plan.

## 2026-09-08 (later same day) — Phase 16 (اسناد حزب reorder)
- **Changed:** homepage section order — اسناد حزب moved from right after
  گزارش down to directly above موضوعات, at the very bottom of the
  page's content sections, per Farhad relaying the client's decision to
  lower this section's prominence relative to نشریات/انتشارات حزب/
  کتابخانه (all of which move up one position as a result). Shared the
  reordering outcome with Farhad and got explicit confirmation before
  touching any code, per his request. New order: headline article,
  تازه‌ترین مقالات, گزارش, نشریات (شمارهٔ جاری), انتشارات حزب, کتابخانه,
  اسناد حزب, موضوعات. Query/label/link/count for اسناد حزب itself
  unchanged — only its position in the page moved.
  **Two background-band issues found and fixed while doing this**, not
  assumed safe just because the move itself was simple:
  (1) Removing اسناد حزب from between گزارش and نشریات made those two
  — both a plain/paper background — directly adjacent whenever گزارش
  actually has content, which would have broken the site's long-standing
  "no two adjacent sections share a background" rule. Made نشریات's
  background dynamic instead of guessing one value: a new `$has_reports`
  boolean (captured once, before گزارش's own loop consumes
  `have_posts()`) drives it — cream when گزارش is visible (plain) above
  it, plain when گزارش is empty and hides (in which case تازه‌ترین
  مقالات's cream band becomes the real neighbor instead). Verified both
  branches live, not just one: temporarily tagged a test post into the
  `report` taxonomy to force گزارش visible, confirmed نشریات switched to
  cream with no clash; deleted the test post and reconfirmed the
  zero-reports state (نشریات plain) also has no clash against تازه‌ترین
  مقالات.
  (2) اسناد حزب's own `.sect-tint` still doesn't clash in its new spot
  (کتابخانه's cream above, موضوعات's plain below) — confirmed, no change
  needed there.
  Verified with a live per-section background-color sweep (not
  eyeballed) at all 3 breakpoints — desktop (1440px), tablet (820px),
  mobile (375px) — confirming both the new section order and zero
  adjacent-background clashes at each; zero console errors at any of
  them.
  Theme version bumped 1.6.1 → 1.7.0 (homepage structural reorder, not a
  patch).
  Approved by: Farhad, in this session (2026-09-08) — Phase 16 of the
  Technical Scoping Plan.

## 2026-09-09 — Phase 17 (hero_section CPT — switchable hero variants)
- **Added:** a new `hero_section` custom post type in `shola-core`, per the
  client's request (relayed by Farhad) for a second hero layout (headline
  article + a side column showing a publication's latest issue), combined
  with Farhad's own proposal for the underlying mechanism: rather than
  hardcode one hero design and rebuild it from scratch every time the
  client changes their mind, editors save any number of `hero_section`
  entries and flip a single "active" flag between them — nothing already
  built is ever deleted to try a new design.
  **Data model** (see class-post-types.php's hero_section docblock for
  the full rationale): each entry holds only (1) `shcore_hero_active`
  (boolean, singleton-enforced), (2) `shcore_hero_layout` (`single` or
  `lead_rail`), and (3) `shcore_hero_rail_publication` (`shola-jawid` or
  `a-world-to-win`, only meaningful for `lead_rail`). The headline article
  itself is deliberately never stored per-entry — it stays "latest
  published post," computed live, regardless of which hero variant is
  active, per Farhad's explicit instruction.
  **Singleton enforcement**: `Meta_Fields::deactivate_other_hero_sections()`
  runs from `save_meta_boxes()` whenever an entry is saved active,
  clearing the flag on every other `hero_section` (any status). Verified
  in both directions with real saves through the wp-admin edit screen
  (not just code review): activating one entry via its own metabox
  checkbox correctly deactivated the other; using the list-table row
  action (below) on the other correctly reversed it.
  **Admin UX**: a "وضعیت" (status) column on the `hero_section` list table
  (فعال/غیرفعال at a glance) and a one-click "تنظیم به‌عنوان فعال" row
  action (`admin.php?action=shcore_set_active_hero`, nonce-verified,
  capability-checked) so switching the live variant never requires
  opening an entry and finding a checkbox.
  **Bug caught and fixed while building the metabox, before shipping**:
  the rail-publication dropdown's first version queried
  `get_terms( 'publication', parent => 0 )`, which also surfaced the
  taxonomy's auto-created "دسته‌بندی‌نشده" (Uncategorized) top-level term
  as a selectable option — meaningless here. Fixed by looking up the two
  real publication slugs (`shola-jawid`, `a-world-to-win`) by name
  directly, matching `sanitize_hero_rail_publication()`'s own fixed
  two-slug vocabulary instead of trusting a broader taxonomy query.
  **Seeding**: one default, active, `single`-layout entry ("هدر پیش‌فرض
  (تک‌ستونی)") is seeded on `admin_init` (idempotent, same option-flag
  pattern as `Taxonomies::migrate_legacy_party_documents()`), so the
  admin list isn't empty and the mechanism is testable immediately.
  **Deliberately out of scope for this phase, per Farhad's explicit
  instruction**: front-page.php's actual hero markup is untouched — this
  post type has no effect on the live site yet. Wiring the homepage hero
  to read from the active `hero_section` entry, building the `lead_rail`
  template (the new side-column layout), and migrating today's hero into
  this system as its `single` entry are follow-up work, tested
  layout-by-layout as its own step.
  Also fixed in passing: `SHCORE_VERSION` (the cache-busting constant in
  `shola-core.php`) had drifted to `1.1.2` while the plugin header already
  read `1.3.0` — brought back in sync as part of this version bump.
  Plugin version bumped 1.3.0 → 1.4.0.
  Approved by: Farhad, in this session (2026-09-09) — Phase 17 of the
  Technical Scoping Plan.

## 2026-09-10 — Phase 17 continued (front-page.php wired to hero_section)
- **Changed:** front-page.php's hero now looks up the active
  `hero_section` entry (added the previous session) to decide which
  layout to render, instead of always rendering the one hardcoded
  design. The headline article query itself is completely unchanged —
  per Farhad's explicit instruction, the hero_section CPT only ever
  controls layout/rail, never which article leads.
  No active entry (a theoretical edge case, not something that can
  currently happen since one is always seeded) falls back to `single`
  — today's only layout — so the homepage can never render broken or
  blank because of this.
  `lead_rail` layout additionally looks up the latest `issue` for
  whichever publication the active entry names; if that publication
  has no issues yet, silently falls back to `single` rather than
  showing a rail with nothing in it.
  **Verified this is a genuinely zero-visual-change migration, not just
  assumed safe**: loaded the homepage with the seeded default entry
  (`single`) active — pixel-identical to before this change, zero
  console errors. Then switched the entry to `lead_rail` directly via
  WP-CLI and reloaded: confirmed the new `hero-lead--with-rail` class
  is correctly added with no visual regression (no CSS targets that
  class yet — this session's next step is designing and building that
  layout's actual markup/CSS, presented to Farhad before implementing
  since it's a real visual design decision, not just data wiring).
  Reverted the test entry back to `single` before finishing.
  Theme version bumped 1.7.0 → 1.7.1.
  Approved by: Farhad, in this session (2026-09-10) — Phase 17 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 17 continued (lead_rail hero layout built)
- **Added:** the actual "لید + ستون نشریه" (Lead + Rail) hero layout,
  designed and confirmed with Farhad before any CSS was written (a real
  visual-design decision, not just data wiring): the headline hero stays
  at its full existing size/photo/overlay (~70% width), a new rail
  column (~30%, `--paper` background, dark text — deliberate light/dark
  contrast against the photo, distinct from the small inset-tile style
  already used for the اطلاعیه spotlight elsewhere) sits beside it
  showing the active hero_section entry's configured publication's
  latest issue (cover, title, short dek, "دریافت شماره" button).
  **RTL handled with zero hardcoded left/right**: the hero markup
  (`.hero-main`) comes first in the DOM, the rail (`.hero-rail`) second
  — CSS's normal logical flex ordering already puts the rail on the
  visual left in this RTL layout with no special-casing, matching the
  client's own description of the request ("another column on the
  left").
  **Refactor to keep the two layouts from drifting apart**: the hero's
  kicker/title/dek/date markup (identical between `single` and
  `lead_rail`) was pulled into one shared `shola_render_hero_body()`
  helper in `inc/template-tags.php`, called from both branches in
  front-page.php, rather than duplicated inline in each.
  **CSS approach**: `.hero-lead`'s existing `::before`/`::after` (photo
  scrim + gradient) and `> .wrap` positioning are written for a
  full-bleed single-column hero, so `.hero-lead--with-rail` disables
  them and re-applies the identical rules scoped to `.hero-main`
  instead — otherwise the scrim would stretch across the rail column
  too. Below 900px the two columns stack (rail becomes a full-width
  card below the unchanged hero); below 720px, `.hero-main > .wrap`
  gets the same lower-band text-position treatment `.hero-lead > .wrap`
  already had for the `single` layout at that width — the existing
  rule's selector doesn't reach the new nested `.wrap`, so it needed an
  explicit twin.
  If the active entry's configured publication has no issues yet,
  front-page.php silently falls back to `single` rather than showing a
  rail with nothing in it (existing fallback from the previous session's
  wiring, exercised for the first time here).
  **Verified live at all 3 breakpoints** by temporarily switching the
  seeded hero_section entry to `lead_rail` via WP-CLI: desktop (1280px)
  — two columns, rail correctly on the visual left, zero console errors;
  tablet (768px) — stacks to one column, rail's own rect confirmed
  (DOM measurement, not just eyeballed) directly below the hero at full
  width; mobile (375px) — hero identical to the `single` layout's own
  mobile treatment, rail confirmed present and fully readable below it
  via `innerText`. (Two screenshots at tablet width came back
  blank/garbled after a scroll — the same known sticky-masthead
  scroll+screenshot tool artifact seen earlier in this project, not a
  real bug: cross-checked and confirmed correct via
  `getBoundingClientRect()`/`innerText` instead of trusting those
  particular screenshots.) Reverted the test entry back to `single` and
  reconfirmed the homepage is pixel-identical to before this whole
  feature — zero regression to the existing design.
  Theme version bumped 1.7.1 → 1.8.0 (new hero layout, not a patch).
  Approved by: Farhad, in this session (2026-09-10) — Phase 17 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 17 continued (lead_rail width capped)
- **Fixed:** the `lead_rail` hero layout shipped a few minutes earlier
  was full-bleed edge-to-edge like the `single` layout it's built
  alongside — Farhad caught this live on his own machine (screenshot):
  on a wide monitor the rail column drifted all the way to the far edge
  of the browser window, disconnected from where the rest of the
  homepage's content actually sits. Farhad's own stated rule for the
  fix: the one-column hero stays full width always; the two-column
  hero should always be a centered, capped band matching the rest of
  the page — never wider than that regardless of how wide the browser
  gets.
  `.hero-lead--with-rail` now carries the exact same box model as every
  other section's `.wrap` (`max-width: var(--wrap-wide)` [1200px],
  `margin-inline: auto`, `padding-inline: var(--gap-pad-x)`) — the
  `single` layout's own `.hero-lead` rule is completely untouched, this
  cap only applies to the two-column variant.
  Verified live at a genuinely wide viewport (1920px, since the bug
  only shows above ~1264px): `getBoundingClientRect()` confirms the
  hero and the very next section's `.wrap` now share identical `left`/
  `right`/`width` values (1200px, 352.4px–1552.4px) — pixel-exact
  alignment with the rest of the page, not just "looks close." Also
  reconfirmed at tablet width (768px, below the cap) that nothing
  regressed there — still stacks to one column, full width as before,
  zero console errors at either width.
  Theme version bumped 1.8.0 → 1.8.1.
  Approved by: Farhad, in this session (2026-09-10) — Phase 17 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 17 continued (lead_rail: height + rail color)
- **Changed:** two more rounds of visual feedback on the `lead_rail`
  hero from Farhad, testing it live on his own machine (screenshot):
  (1) the hero was still using the `single` layout's full-viewport-
  height treatment, which reads as disproportionate/empty for a
  two-column module — a tall, narrow rail with a small text block just
  vertically centered in a huge column looks unfinished, not like a
  real editorial "Lead + Rail" pattern (NYT/WaPo-style top-story
  modules use a short, wide banner band, not a cinematic full-screen
  slide); (2) the rail's `var(--paper)` (white) background visually
  merged into the page's own white background, reading as an unstyled
  leftover rather than a deliberate card.
  **Fix 1 — compact height, desktop only**: `.hero-lead--with-rail
  .hero-media`/`.hero-main` now use `height: clamp(420px, 42vw, 560px)`
  above 901px, instead of the `single` layout's `calc(100dvh -
  masthead)`. Scoped to `min-width: 901px` deliberately — below the
  900px stack breakpoint the two columns already collapse to one on
  top of the other, where the `single` layout's own full-height mobile
  treatment is correct and untouched.
  **Fix 2 — solid crimson rail**: `--crimson` instead of `--paper`,
  with `--paper` text. Not a new color — this is the site's one
  recurring accent (masthead bar, buttons, tags) used at a larger,
  deliberate scale for one card, not an unrelated addition. The rail's
  own "دریافت شماره" button is inverted (paper background, `--crimson-
  deep` text) since `.btn-primary`'s default crimson-on-paper would
  otherwise vanish against the rail's own now-crimson background.
  Verified live: at 1920px, `.hero-main`/`.hero-rail` both measure
  560px tall (the clamp's own maximum, confirmed via
  `getBoundingClientRect()`, not eyeballed) and computed
  `background-color`/`color` on the rail and its button match the
  intended tokens exactly; re-confirmed the `single` layout is still
  byte-for-byte unaffected (switched back to it locally, pixel-
  identical screenshot to before any of today's hero work); tablet
  (768px) and mobile (375px) both still stack correctly with the rail's
  new crimson color applied, zero console errors at any width.
  Theme version bumped 1.8.1 → 1.8.2.
  Approved by: Farhad, in this session (2026-09-10) — Phase 17 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 17 continued (third hero layout: overlay)
- **Added:** a third hero_section layout, `overlay` ("مقالهٔ سرخط با
  کارت شناور روی تصویر") — per a client idea relayed by Farhad with a
  sketch: the exact same full-bleed photo/headline as `single`
  (untouched, no CSS changes to that layout), with a white publication
  card floating over the photo's lower corner instead of a full side
  column, on desktop only. On tablet and mobile the card drops below
  the photo as a plain full-width block instead of overlaying it —
  Farhad's own recommendation, confirmed before building, once we
  discussed that overlaying it on a small screen would collide with the
  hero's own headline text.
  Plugin: `sanitize_hero_layout()` now accepts `overlay` as a third
  value; the hero_section metabox's "نوع چیدمان" dropdown gained the
  third option and its description was reworded to cover all three
  layouts; "نشریهٔ ستون کناری" label/description generalized to "نشریهٔ
  کارت/ستون نشریه" since the field is now shared by two layouts, not
  one. Plugin version bumped 1.4.0 → 1.5.0.
  Theme: `shola_render_hero_publication_card()` (new, `inc/template-
  tags.php`) is now the single source of the "latest issue" card's
  content (kicker, cover, title, dek, button), called by both
  `lead_rail`'s `<aside class="hero-rail">` and `overlay`'s new
  `<div class="hero-pub-card">` — extracted so the two layouts'
  identical card content can't drift apart, same reasoning as
  `shola_render_hero_body()` from earlier in this phase. Renamed the
  card's inner CSS classes from `hero-rail-*` to `hero-pub-card-*`
  accordingly (no color set on the shared classes themselves — each
  parent, `.hero-rail` (crimson) or `.hero-pub-card` (paper), sets its
  own text colors).
  `.hero-pub-card` is deliberately NOT nested inside `.wrap` (which is
  `position:absolute`) — as a static sibling of it inside `.hero-lead`
  (`position:relative`), it naturally renders directly below the photo
  in normal document flow at narrow widths with zero extra markup;
  above the stack breakpoint, CSS switches it to `position:absolute`
  and floats it instead. Positioned with the same "stay centered like
  the rest of the page even though the photo is full-bleed" math
  Farhad asked for on `lead_rail` earlier this session: `inset-inline-
  end: calc(max(0, (100% - 1200px) / 2) + gap-pad-x)`, RTL-safe with no
  hardcoded left/right.
  **Two bugs caught and fixed live before shipping, not assumed
  correct:**
  (1) the card's title/dek initially used the site's default (much
  larger) heading size, since the compact sizing rule only targeted
  `.hero-rail .h-page`/`.dek`, not the new `.hero-pub-card` context —
  ballooned the card to 547px tall (a 96px 2-line title alone) instead
  of the intended ~415px; fixed by sharing that sizing rule across both
  contexts.
  (2) the card's own breakpoint (721px) was too low: at tablet width
  (768px) the hero's headline text is still in its desktop bottom-
  anchored position (its own mobile repositioning only starts below
  720px), so overlaying the card there too made them visually collide
  — caught live testing tablet width, not assumed safe. Raised the
  card's stack breakpoint to 901px, matching `lead_rail`'s own stack
  breakpoint, so both multi-element hero layouts agree on where
  "enough room for two things at once" starts.
  Verified live at all 3 breakpoints after both fixes: desktop
  (1920px) — card's left edge measured via `getBoundingClientRect()`
  at 384.5px, an exact pixel match with the very next section's own
  content-column edge; tablet (768px) — card confirmed `position:
  static`, full width, stacked cleanly below the photo with zero
  overlap; mobile (375px) — same static full-width stacking, hero photo
  itself pixel-identical to the `single` layout's own mobile view.
  Zero console errors at any width. Re-confirmed both `single` and
  `lead_rail` layouts are completely unaffected by this refactor.
  Theme version bumped 1.8.2 → 1.9.0.
  Approved by: Farhad, in this session (2026-09-10) — Phase 17 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 18 (Winston Red replaces crimson site-wide)
- **Changed — rules change, not just a feature commit (CLAUDE.md §8)**:
  `--winston-red` (#CC0000, the masthead's own color) is now this
  site's one red, used everywhere `--crimson`/`--crimson-deep`/
  `--crimson-tint` used to be — buttons, links, hover states, kickers,
  badges, form focus rings, pagination, footer, breadcrumbs, share
  buttons, contact-form validation, everywhere. Farhad flagged the
  inconsistency directly from three screenshots: the "دریافت شماره"
  button and several hover states were still the older, more muted
  crimson (#8E1B1B), visibly different from the brighter red already
  used in the masthead and the اطلاعیه spotlight tile, reading as two
  unrelated reds on the same page.
  **This supersedes two earlier, deliberately-scoped decisions**, both
  already on record: Phase A (2026-08-24) explicitly scoped Winston Red
  to the masthead background only ("crimson untouched everywhere
  else"); Phase 11 (2026-09-07) widened that to the اطلاعیه tile too,
  explicitly noting "still exactly 2 uses ... not scope creep." Farhad's
  instruction this session removes that boundary entirely — recorded
  here per CLAUDE.md §8 rather than left implicit in a diff.
  **Two new derived tokens added**, `--winston-red-deep` (#990000) and
  `--winston-red-tint` (#FCF0F0), mirroring how `--crimson-deep`/
  `--crimson-tint` were originally derived from `--crimson` (a ~25%-
  darker shade for hover/active states, a light tint for backgrounds/
  selection) — not literally re-scaled from crimson's own ratios,
  recalculated against Winston Red's own hue.
  **Contrast checked before shipping, not assumed safe** (same
  discipline as the existing winston-red accessibility comment this
  file already had): a straight 90%-white-mix tint (matching
  `--crimson-tint`'s own ratio) put `--stone` body text on `.sect-tint`
  backgrounds at ~4.45:1 — just under the 4.5:1 AA floor that section's
  real paragraph text needs. Mixed lighter (~94% white,
  `--winston-red-tint`'s final value) instead, measuring ~4.8:1. White
  text on the base `--winston-red` (buttons, badges) measures ~5.9:1 —
  lower than old crimson's ~9:1 headroom, but still comfortably AA-
  compliant.
  **Every functional `var(--crimson*)` declaration in `main.css`**
  (~50 occurrences — buttons, links/hovers, badges, `::selection`,
  `:focus-visible`, prose links, share buttons, form validation,
  pagination, topic nav, footer) was repointed to the matching
  `var(--winston-red*)`. The original `--crimson`/`--crimson-deep`/
  `--crimson-tint`/`--maroon` token *definitions* are kept (not
  deleted) — they're still part of the original locked brand-guide
  table, just no longer referenced by any rule in this file. Stale
  comments describing the old "scoped to masthead only" rule were
  updated so they read as history, not a standing constraint.
  Scope: this theme's own `main.css` only — `shola-core`'s admin-only
  `video-guide.css` (wp-admin editor tooling, not a public page) and
  the archived brand-guideline reference docs in `docs/IA-reference/`
  were deliberately left untouched, since "throughout the website"
  means the public-facing site, not internal admin chrome or historical
  reference documents.
  Verified live across multiple templates, not just the homepage: the
  hero's button/kicker (both hero layouts), pagination's current-page
  background, and a topic archive's active-nav-item color all measured
  via `getComputedStyle()` as exactly `rgb(204, 0, 0)` /
  `rgb(153, 0, 0)` (Winston Red / its new deep variant) — an exact
  match with the masthead's own background. Zero console errors on any
  page checked.
  Theme version bumped 1.9.0 → 1.10.0.
  Approved by: Farhad, in this session (2026-09-10) — Phase 18 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 18 continued (headings still off-brand)
- **Fixed:** Farhad caught, from live screenshots, that several
  prominent headings/labels still read as "the old crimson" even after
  the Phase 18 swap above — the hero overlay card's title, the
  homepage's "تازه‌ترین مقالات" section heading, and a single article
  page's "مطالب دیگر" heading. Root cause: Phase 18's swap mechanically
  mapped every old `--crimson-deep` declaration to `--winston-red-deep`
  — technically consistent, but `--winston-red-deep` (#990000) is
  darker/more muted than the masthead's own exact `--winston-red`
  (#CC0000), so a *static* heading in that deep shade still visually
  reads as "a different, muted red" next to the masthead — the same
  complaint that started Phase 18 in the first place, just one shade
  removed from it.
  **Fix**: reserved `--winston-red-deep` for genuine interactive
  feedback only (`:hover`/`:active` states, where a color shift is
  expected UX, not a mismatch) and switched every *resting*, visibly-
  prominent heading/label to the exact masthead `--winston-red`
  instead: `.h-display`, `.h-page`, `.h-section` (all three, plus
  `.article-title` and `.pull-line`, which weren't caught by the first
  pass since they weren't visible in the original three screenshots),
  `.btn-ghost`'s resting text color, `.hero-rail .btn-primary`'s resting
  text color, `.badge-current`, `.topic-nav .active` (a static
  "currently selected" state, not a hover), and `.h-card-lg mark`.
  Genuine hover states (`.btn-primary:hover`, `.link:hover`,
  `.prose a:hover`, `.topic-nav a:hover`, etc.) and `::selection` were
  deliberately left on `--winston-red-deep` — a hover state is supposed
  to look different from the resting color, that's expected feedback,
  not the "two unrelated reds" problem Farhad flagged.
  Contrast re-verified for the two resting-state repoints onto tint
  backgrounds (`--winston-red` on `--winston-red-tint`, used by
  `.badge-current`/`.h-card-lg mark`): ~5.3:1, still comfortably AA.
  Verified live: the hero overlay card's title, the homepage's
  "تازه‌ترین مقالات" heading, and the single article page's "مطالب
  دیگر" heading all measured via `getComputedStyle()` as exactly
  `rgb(204, 0, 0)` — an exact match with the masthead background.
  Zero console errors.
  Theme version bumped 1.10.0 → 1.10.1.
  Approved by: Farhad, in this session (2026-09-10) — Phase 18 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 17 continued (hero cover proportions)
- **Fixed:** the hero publication card's cover image (both `lead_rail`'s
  rail and `overlay`'s floating card, since they share
  `shola_render_hero_publication_card()`) was cropped to a wide 3:2
  landscape shape — Farhad flagged this from a live screenshot as
  inconsistent with real issue covers, which are book/magazine-style
  portrait shapes (matching `.issue-cover`'s own 3:4 ratio used
  elsewhere, e.g. the نشریات section). Confirmed understanding with
  Farhad before changing anything, per his request.
  `.hero-pub-card-cover`'s `aspect-ratio` changed from `3/2` to `3/4`.
  `object-fit: cover` (already in place) continues to guarantee any
  uploaded cover image — whatever its real dimensions — fills this
  frame exactly, cropped as needed, never stretched or overflowing;
  Farhad asked for this explicitly and it was already the existing
  behavior, just now applied to the correct shape.
  **Real bug found and fixed while verifying, not shipped blind**: a
  full-width (256px) 3/4 cover needs ~341px of height on its own —
  more than `lead_rail`'s compact clamped hero height (420-560px,
  tuned earlier this session for a different "too tall" complaint) has
  room for once the rest of the card's content (kicker/title/dek/
  button/padding, ~269px) is added. Since `.hero-rail` is stretched to
  match `.hero-main`'s explicit height, the overflow forced the whole
  row taller than the photo — a ~50px gap opened up below the photo,
  caught via `getBoundingClientRect()` at the layout's realistic worst
  case (viewports around 901-999px, where the height clamp sits at its
  420px floor), not just eyeballed at one comfortable width.
  Fixed by capping the cover to 120×160px (still the same 3:4 shape,
  just sized to reliably fit) inside `.hero-rail` specifically, rather
  than shrinking the whole rail or its text — `.hero-pub-card` (the
  `overlay` layout's card) keeps the original full-width 256×341 cover
  since it isn't height-constrained the same way (it floats freely
  over a photo tall enough to hold it, not stretched to match a fixed-
  height sibling).
  Verified at the exact 420px clamp floor (950px viewport): hero photo
  and rail now measure identically (420px each, zero gap). Re-verified
  desktop (1920px, 560px each, zero gap) and tablet stacking (768px,
  cover still 120×160, full-width rail) all correct. `overlay` layout
  re-confirmed unaffected (still the full 256×341 cover, comfortably
  within its own taller photo). Zero console errors at any width.
  Theme version bumped 1.10.1 → 1.10.2.
  Approved by: Farhad, in this session (2026-09-10) — Phase 17 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 17 continued (cover-size rebalance)
- **Fixed:** the previous cover-size fix (120×160, scoped to `.hero-rail`
  only) overcorrected — Farhad's next round of live screenshots showed
  two opposite problems at once: `overlay`'s floating card (still using
  the full 256×341 cover, untouched by that fix since it wasn't the one
  with the overflow bug) now read as "very large in height", its top
  edge reaching well up the photo past where Farhad marked a line it
  should stay below; `lead_rail`'s 120×160 cover, by contrast, looked
  "very small... shrunk down".
  Resolved both at once with one coherent approach instead of two more
  one-off patches: `.hero-pub-card-cover` now defaults to
  `width: min(220px, 100%)` — a generous size used whenever the card is
  full-width (mobile/tablet stacking, both layouts, <901px, addressing
  Farhad's explicit "mobile version... should not be crumbled
  together") — and narrows to 160px specifically for the two desktop
  contexts that have a real space constraint: `.hero-rail` (stretched
  to match the hero's own height) and `.hero-pub-card` (kept compact so
  it stays a corner card, not a near-full-height column). Also raised
  `lead_rail`'s compact-height clamp floor from 420px to 480px, since a
  portrait 3/4 cover needs more height per unit width than the old
  landscape 3/2 one — a small, deliberate trade-off for a visibly
  bigger cover, still nowhere near the original full-viewport-height
  design two fixes ago.
  Verified live, not assumed: at 950px (the layout's real worst case,
  where the height clamp sits at its new 480px floor), `lead_rail`'s
  photo and rail measure identically (480px each, zero overflow) with
  a visibly larger 160×213 cover. The `overlay` card now measures
  458px tall total (down from ~585-600px) and its top edge sits at
  ~42% down the photo — comfortably in the lower half, not near the
  top. Mobile (375px) confirmed both layouts now show the larger
  220×293 cover when stacked, addressing the "not crumbled" request.
  Zero console errors at any width or layout.
  Theme version bumped 1.10.2 → 1.10.3.
  Approved by: Farhad, in this session (2026-09-10) — Phase 17 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 17 continued (overlay card: desktop-only)
- **Changed:** the `overlay` layout's floating publication card no
  longer appears at all on mobile/tablet (<901px) — Farhad's
  instruction, after seeing it stacked full-width below the photo
  there: the same "latest issue" information already appears in the
  نشریات section further down the same homepage, so repeating it a
  second time in the hero on a small screen is redundant clutter, not
  worth keeping at a smaller size. `.hero-pub-card` is now `display:
  none` below 901px and `display: block` (its existing absolute-
  positioned floating treatment, unchanged) above it — mobile/tablet
  now renders an `overlay` hero identically to the `single` layout's
  hero (full-bleed photo + headline only), and the card only appears
  once there's room for it to float without competing for space.
  Verified live: mobile (375px) and tablet (768px) both confirmed
  `display: none` via `getComputedStyle()`; desktop (1600px) confirmed
  still `display: block` / `position: absolute`, unaffected. Zero
  console errors at any width. `lead_rail`'s rail (a different, still-
  stacking-on-mobile UI element — a full column, not a "floating
  card") was not touched; only the `overlay` layout's card, which is
  what Farhad's screenshot and instruction were both about.
  Theme version bumped 1.10.3 → 1.10.4.
  Approved by: Farhad, in this session (2026-09-10) — Phase 17 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 19 (masthead flicker + tablet spotlight order)
- **Fixed — sticky masthead flicker, site-wide (Phase 13's own bug,
  caught live on tablet).** Farhad reported the sticky/shrinking
  masthead visibly "flickering"/"buzzing" while scrolling — worst on
  tablet, reported from Chrome's iPad emulation, where touch-style
  momentum scrolling is jumpier than a mouse wheel. Root cause:
  `#mast-sentinel` (the invisible marker `main.js`'s
  IntersectionObserver watches to know when the page has scrolled past
  80px) was only 1px tall — a razor-thin, single-point threshold where
  any tiny back-and-forth scroll jitter right at that exact pixel
  (routine with touch/momentum scrolling, and even ordinary sub-pixel
  scroll rounding) flips `isIntersecting` rapidly. Every flip restarts
  the shrink/expand CSS transition (padding, font-size, gap, icon
  sizes — the whole `--mast-scale` system) in the opposite direction,
  which is exactly what reads as buzzing.
  Fixed with a hysteresis buffer, not a logic change: `#mast-sentinel`
  height raised 1px → 48px, so the observer only reports "not
  intersecting" once the whole 48px band has scrolled past — small
  jitter within that band no longer flips the class back and forth.
  The masthead still starts shrinking at the same ~80px scroll
  position as before; it just no longer flickers doing it. This is
  the one shared sentinel/observer for the whole site (mobile, tablet,
  desktop all use it) — tablet's touch-style scrolling just made the
  pre-existing jitter far more visible there, so the fix applies
  everywhere, per Farhad's explicit ask.
  Verified live: real scroll (not `window.scrollTo()`, confirmed
  unreliable for triggering this observer in this environment) past
  the threshold correctly sets `.is-scrolled`; scrolling back to the
  top correctly removes it. Zero console errors.
- **Changed — اطلاعیه spotlight tile order, tablet only.** Previously
  the tile sat first (above the article grid) at every width ≥720px
  (tablet and desktop alike) — Farhad reviewed the tablet width
  specifically and asked for the same "after the articles" placement
  the `<720px` mobile layout already had, since a tablet's 2-column
  grid reads top-to-bottom similarly to mobile's single column. The
  `order: 0` reset (tile-first) that used to apply at `≥720px` now
  only applies at `≥1000px` (true desktop, where the tile is a
  deliberate side-by-side column via `grid-column: 3`) — tablet
  (720-999px) now falls through to the same `order: 1` mobile already
  had. Desktop's own `grid-column: 3` side-placement is untouched.
  Verified live at all three breakpoints via computed `order` and
  actual rendered position (`getBoundingClientRect()`, not just the
  CSS property): tablet (768px) — `order: 1`, tile confirmed
  positioned below every article card; desktop (1440px) — `order: 0`,
  `grid-column: 3`, unchanged; mobile (375px) — `order: 1`, unchanged.
  Theme version bumped 1.10.4 → 1.11.0.
  Approved by: Farhad, in this session (2026-09-10) — Phase 19 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 17 continued (overlay card: geometric polish)
- **Changed:** final polish round on the `overlay` layout's floating
  card, desktop only — Farhad flagged from a live screenshot that the
  card's fixed 320px width didn't line up with تازه‌ترین مقالات's own
  3-column article grid further down the same page (its outer/left
  edge already did, from an earlier fix, but its inner/right edge
  didn't), and asked for the title to get more breathing room.
  **Width**: `.hero-pub-card`'s width is no longer a flat 320px — now
  `calc((var(--wrap-wide) - (var(--gap-pad-x) * 2) - (1.5rem * 2)) / 3)`,
  the exact same formula `.grid-cards`' `repeat(3, 1fr)` uses to size
  each of its own columns inside the same capped/centered content
  area. Reusing the real formula (not a second hand-picked pixel
  value) means both of the card's edges land exactly on that grid's
  real column boundaries, and stay correct if `--wrap-wide`/
  `--gap-pad-x`/the grid's own gap are ever retuned together.
  **Title spacing**: `.hero-pub-card .h-page` (the card's title only —
  `.hero-rail`'s own title, still visible on mobile for the `lead_rail`
  layout, is untouched) now uses a slightly smaller 0.95rem (was the
  shared 1.05rem) with its own `margin-block: 0.65rem`, giving it real
  space from the cover above and the dek below instead of sitting
  flush against both.
  Verified live via `getBoundingClientRect()`, not eyeballed: the
  card's left edge (144.5px) exactly matches تازه‌ترین مقالات's grid's
  own left edge, and the card's right edge (507.156px) exactly matches
  that grid's leftmost column's own right edge — a pixel-exact match,
  not an approximation. Re-confirmed the card still fits comfortably
  within the photo's height (455px card vs. ~900px+ photo at normal
  desktop heights) despite the extra width. Mobile (375px) reconfirmed
  untouched — `.hero-pub-card` still `display: none` there, and
  `lead_rail`'s own title (still visible on mobile) reconfirmed at its
  original, unchanged 1.05rem size. Zero console errors.
  Theme version bumped 1.11.0 → 1.11.1.
  Approved by: Farhad, in this session (2026-09-10) — Phase 17 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 20 (fourth hero layout: rail_full)
- **Added:** a fourth hero_section layout, `rail_full` ("مقالهٔ سرخط +
  ستون نشریهٔ تمام‌عرض") — per a client sketch: visually the same idea
  as `lead_rail` (a solid Winston Red panel with the publication card
  beside the full-size headline photo), but the panel's outer edge
  bleeds all the way to the true browser edge instead of stopping at
  the centered 1200px content column `lead_rail` deliberately uses.
  Confirmed the exact visual (a client-drawn mockup, not just a verbal
  description) before writing any CSS, per Farhad's own request to
  review understanding first.
  Deliberately reuses `.hero-main`/`.hero-rail` as-is for the shared
  inner styling (photo scrim/text overlay, crimson background,
  inverted button, publication-card content) rather than duplicating
  it — only what's genuinely new gets its own rules: no width cap
  (`.hero-lead--rail-full`, not `.hero-lead--with-rail`), no compact-
  height clamp (keeps the full viewport height `single`/`overlay`
  already use, matching the client's sketch — a tall panel, not
  `lead_rail`'s short banner), and a bigger cover (`min(240px, 100%)`
  vs. `lead_rail`'s 160px) since the extra height leaves real room for
  one without the overflow risk `lead_rail` had to be fixed for
  earlier.
  Desktop AND tablet, not mobile: the rail is hidden below 721px
  (falling back to the same full-bleed photo + headline `single`/
  `overlay` already show there), per Farhad's explicit "computer, not
  mobile" instruction. Tablet (721-900px) was tested live, not assumed
  either way — the side-by-side treatment (rail floored at a 320px
  minimum width via `min-width`, main flexing to fill the rest) reads
  cleanly at 768px, no cramping, so it was kept rather than falling
  back to a stacked treatment there.
  **Real bug found and fixed while testing, not shipped blind**: at
  tablet width, `lead_rail`'s own stacking media query
  (`@media (max-width: 900px) { .hero-main {...} .hero-rail {...} }`)
  turned out to target the bare `.hero-main`/`.hero-rail` class names,
  not scoped to `.hero-lead--with-rail` — since `rail_full` reuses
  those same two class names, that rule silently applied to it too,
  forcing both children to `width: 100%` inside a row-direction
  container with no `flex-direction: column` to make sense of it. The
  rail was pushed entirely off-screen (`x: -320`, caught via
  `getBoundingClientRect()`, not eyeballed) at tablet width. Fixed at
  the root — scoped that rule to `.hero-lead--with-rail .hero-main`/
  `.hero-lead--with-rail .hero-rail` explicitly, which is what it was
  always meant to mean; re-verified `lead_rail`'s own tablet stacking
  is unaffected by the fix.
  Verified live at all three breakpoints: desktop (1600px) — rail's
  left edge at `x: 0`, main's right edge at the true viewport edge
  (accounting for the scrollbar), both confirmed via
  `getBoundingClientRect()`; tablet (768px) — side-by-side, no
  overflow, cover still a legible 220×293; mobile (375px) — rail
  `display: none`, `.hero-main` naturally expands to fill 100% of the
  row, identical to `single`'s own mobile hero. Zero console errors at
  any width. `single`, `lead_rail`, and `overlay` all reconfirmed
  unaffected.
  Plugin: `sanitize_hero_layout()` now accepts `rail_full` as a fourth
  value; the hero_section metabox's "نوع چیدمان" dropdown gained the
  fourth option, description updated to cover all four layouts.
  Plugin version bumped 1.5.0 → 1.6.0. Theme version bumped
  1.11.1 → 1.12.0.
  Approved by: Farhad, in this session (2026-09-10) — Phase 20 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 20 continued (button + dek length)
- **Fixed — publication card button stretched full-width.** Farhad
  flagged this live on `rail_full` (screenshot, marked with a green
  circle): the "دریافت شماره" button spanned the rail's entire width
  instead of being a normal, compact button. Root cause: both
  `.hero-rail` and `.hero-pub-card` are `display: flex; flex-
  direction: column`, and a flex column's default `align-items` is
  `stretch` — with nothing overriding it, the button (an inline-flex
  element, but still a direct flex-column child) stretched to fill the
  full cross-axis width. This was actually present in every layout
  using this shared card (`lead_rail`'s rail, `overlay`'s card too) —
  just far less visually obvious at their narrower widths than at
  `rail_full`'s ~34%-of-viewport rail, which is what made it visible
  enough to flag. Fixed the shared root cause once
  (`.hero-rail .btn, .hero-pub-card .btn { align-self: flex-start; }`)
  rather than patching `rail_full` alone, so all three layouts get a
  normal, content-sized, reading-start-aligned (visual right, in this
  RTL site) button. Verified live: button width dropped from spanning
  the full rail/card width to ~93px (its real content size) in all
  three layouts, confirmed via `getBoundingClientRect()`.
- **Changed — publication card dek trimmed 18 → 12 words.** Farhad
  flagged the excerpt reading as "extending all the way to the left"
  on `rail_full`'s wide rail, and asked for roughly 10-12 words,
  matching what's "standard" elsewhere on the site. 12 sits inside his
  own suggested range and below every other compact-card dek already
  on the site (`.card-spotlight`'s own dek uses 16) — reasonable, since
  this card is consistently the narrowest/most compact context a dek
  appears in, across all four hero layouts.
  Zero console errors in either fix, at any of the three affected
  layouts.
  Theme version bumped 1.12.0 → 1.12.1.
  Approved by: Farhad, in this session (2026-09-10) — Phase 20 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 21 (site-wide article cards + spotlight ellipsis)
- **Changed — article card title/dek sizing, site-wide.** Farhad
  flagged from live screenshots (both تازه‌ترین مقالات and a topic
  archive — `template-parts/cards/card.php` is shared by both, so the
  fix applies everywhere it's used) that the excerpt read as too
  short/thin next to the title. Two changes together: `.h-card`
  (title) 1px smaller (`var(--t-h4)` 20px → 19px, scoped to `.card
  .h-card` rather than changing the shared `--t-h4` token other
  headings also use); `.card-dek` (excerpt) a full step up the
  existing type scale (`var(--t-small)` 14px → `var(--t-body)` 17px).
  Paired with doubling the word count fed into `wp_trim_words()` in
  card.php (24 → 48, per Farhad's explicit ask) — verified live this
  was actually most of the "short" complaint: at 24 words the excerpt
  often ran out of text before filling `.card-dek`'s existing 3-line
  clamp, leaving visible empty space below a 1-2 line excerpt next to
  a full-height title. Confirmed live (not assumed) that 48 words
  reliably fills all 3 lines now — `scrollHeight` (149px) vs.
  `clientHeight` (89px, the clamped 3-line box) on a real card.
- **Fixed — اطلاعیه spotlight title's "…" rendered dark instead of
  white.** Farhad caught this live on the production site
  (sholajawid.com, a real long اطلاعیه title that actually truncates).
  Root cause, confirmed via `getComputedStyle()` on the live site
  before touching any code: `.card-spotlight-item--featured .h-card`
  is the element webkit's line-clamp truncates and generates the "…"
  for, but only its child `<a>` was ever styled white elsewhere in
  this file — the `.h-card` block itself was left at `.h-card`'s own
  default dark `--ink` color. The visible title text still looked
  fully white (inherited from the anchor's own override), so this only
  ever surfaced once a title was long enough to actually truncate,
  which is why it went unnoticed until now. Added `color: var(--paper)`
  directly to the truncating rule. Re-verified on shola-jawid.local
  (not just production) after the fix.
  Zero console errors across all three fixes, checked on the
  homepage's تازه‌ترین مقالات grid and a topic archive page.
  Theme version bumped 1.12.1 → 1.13.0.
  Approved by: Farhad, in this session (2026-09-10) — Phase 21 of the
  Technical Scoping Plan.

## 2026-09-10 (later same day) — Phase 22 (search "RESULTS FOR" + site-wide pagination 404s)

- **Fixed — hardcoded English strings surfaced by Farhad after a full
  site walkthrough.** He specifically flagged the search results page
  showing `"" RESULTS FOR ۳۸` in Latin script next to Persian digits.
  Grepped the whole theme for the pattern this represents (ALL-CAPS
  literals, `lang="en"` attributes, English inside otherwise-Persian
  strings) rather than fixing only the reported instance, per §1's "no
  hardcoded UI copy in template files, ever" rule. Found and fixed
  five distinct cases: `search.php`'s "RESULTS FOR" (now
  `'%1$s نتیجه برای «%2$s»'`, translators-commented, `esc_html__()`
  under the `shola-jawid` text-domain); `single.php`'s hardcoded
  "TAGS" label (`'برچسب‌ها'`); `single-issue.php`'s hardcoded "SECTION"
  word plus a second, subtler bug in the same line — it was printing
  the raw `topic` taxonomy **slug** instead of the term's real Persian
  name (fixed via `get_term_by( 'slug', ..., 'topic' )`); and
  `shola_get_publication_meta_line()`'s (`inc/template-tags.php`)
  hardcoded "ISSUE"/"ISSUES" (now plain `'%1$s شماره · %2$s'` — no
  singular/plural split needed in Persian, so switched `_n()` to a
  plain `__()`; kept it un-escaped since every call site already wraps
  the return value in `esc_html()`, to avoid double-escaping). Also
  removed two now-stale `lang="en"` attributes
  (`taxonomy-publication.php`, `page-publications.php`) left over from
  before that meta-line bug was fixed at the source.
  Deliberately left untouched, flagged for Farhad's decision rather
  than guessed at: the masthead's Latin "SHOLA JAWID" code
  (`shola_get_masthead_code()`) and `taxonomy-publication.php`'s
  dynamically-built "SJ-32"-style Latin catalogue code
  (`strtoupper($root_slug) . '-' . shola_to_persian_digits($number)`)
  — both read as intentional v6-prototype brand/stylistic marks (same
  category as a book's ISBN-style spine code), not accidental English
  vocabulary, so translating them without confirmation would risk an
  unrequested visual deviation under §9.

- **Fixed — pagination page 2/3 showing a real 404,** the second bug
  Farhad reported (with screenshots of `/page/۲/?s=...` hitting WP's
  own "برگه پیدا نشد" page). Root-caused in two separate, unrelated
  layers, both live on every paginated listing in the theme:
  1. **Digit corruption in pagination hrefs.** `shola_to_persian_digits()`
     — a blanket ASCII→Persian digit `strtr()` — was being applied to
     the *entire* `<a href="...">` string `paginate_links()` returns,
     corrupting both URL-encoded search queries (`%D8%A7` →
     `%D۸%A۷`) and the `/page/2/` path segment itself (`2` → `۲`),
     which WordPress's own rewrite rule (`page/([0-9]+)`, ASCII-only)
     no longer matches. Fixed by adding
     `shola_persian_digits_pagination_link()` (`inc/template-tags.php`)
     — extracts the `href="..."` attribute behind a placeholder,
     Persian-izes the rest of the markup, restores the untouched href
     — and swapping it in at all 8 call sites that build pagination
     (`search.php`, `taxonomy-publication.php`, `taxonomy-topic.php`,
     `taxonomy-collection.php`, `archive-announcement.php`,
     `page-reports.php`, `page-party-publications.php`,
     `page-party-documents.php`).
  2. **Rewrite-rule slug collisions.** Independently, several custom
     post types are registered with a rewrite slug that shares a URL
     prefix with a sibling static Page or taxonomy archive:
     `party_publication`/`party_document` use the exact same slug as
     their own listing Page (`party-publications`, `party-documents`);
     `issue`'s slug (`publications/%publication%`) shares the
     `publication` taxonomy's own `publications` archive prefix; and
     `document`'s slug (`library/%collection%`) shares the
     `collection` taxonomy's `library` prefix. WordPress checks each
     CPT's auto-generated single-post rewrite rule before the
     page/taxonomy's own pagination rule, so `.../page/2/` was being
     misparsed as a request for a single post literally named "page"
     — a genuine "no such post" 404, raised at URL-parsing time, before
     the template or even WP's `pre_handle_404` filter hook ever runs.
     Confirmed via `wp rewrite list` (showing the exact colliding
     rule) and `read_network_requests` (showing `?paged=2` redirecting
     to the pretty `/page/2/` URL that then actually 404s). Two earlier
     fix attempts were tried and confirmed ineffective before finding
     this: syncing a template's secondary `WP_Query::max_num_pages`
     onto the main query (too late — runs after WordPress's 404
     decision is already made) and filtering `redirect_canonical`
     (never fires for this case, since WP never computes a redirect
     URL here — it's a direct 404). Fixed at the actual layer WordPress
     resolves URLs: added four explicit, `'top'`-priority
     `add_rewrite_rule()` calls (`class-post-types.php`,
     `register_pagination_collision_fixes()`) that match
     `party-publications/page/N/`, `party-documents/page/N/`,
     `publications/{term}/page/N/`, and `library/{term}/page/N/`
     directly to the correct page/taxonomy query — checked before the
     colliding CPT rule gets a chance to misparse them — followed by
     `wp rewrite flush`. This pairs with the already-existing
     `pre_handle_404` filter in `inc/setup.php`
     (`shola_skip_404_for_secondary_query_pagination()`, added earlier
     this session): that filter handles a *different*, narrower
     problem — WordPress's own "does this static Page have that many
     `<!--nextpage-->` splits" check rejecting a template's secondary
     `WP_Query` pagination even once the URL parses correctly — and
     stays necessary for `party-publications`/`party-documents`/
     `reports` even after this rewrite-rule fix.
     Verified live on all previously-404ing URLs, confirming real
     content (not the 404 template) and non-`is404` page titles:
     `/publications/shola-jawid-dowre-1/page/2/`,
     `/party-publications/page/2/`, `/party-documents/page/2/`,
     `/library/classics/page/2/` (no real page 2 content yet — 6 of 6
     posts fit on page 1 — but confirmed it no longer hard-404s, same
     as `reports`/`topics` did before this session). Also re-verified
     `search.php` end-to-end (Persian digits, real page 2 content,
     `is404` false) and spot-checked `/announcements/page/2/`, which
     correctly 404s — that one's a genuine out-of-range request (8
     announcements, 10 per page, no real page 2 to show), not a bug.
  Theme version bumped 1.13.0 → 1.14.0. Plugin (shola-core) version
  bumped 1.6.0 → 1.7.0 (new rewrite rules live in
  `class-post-types.php`, per §2's content-model/rewrite-rule
  ownership rule).
  Approved by: Farhad, in this session (2026-09-10) — Phase 22 of the
  Technical Scoping Plan.

## 2026-09-11 — Phase 23 (full-site QA audit + privacy-link fix)

- **Audited:** Farhad asked for a full designer+developer pass over
  the live production site (sholajawid.com) after the pagination/
  English-text fixes shipped, to catch anything else — content
  population, design, or language. Checked the homepage, several
  single-post types (article, issue, party-publication, library
  document), taxonomy archives, About, and Contact, on desktop and
  mobile, for console errors, failed network requests, SEO tags,
  responsive layout, and content correctness. Two apparent bugs
  chased down during this pass turned out to be false positives from
  the review tooling itself, not the site (recorded so they aren't
  re-investigated later): a homepage section that photographed as a
  blank white screenshot was confirmed live and correct via the raw
  page text/DOM — a screenshot-capture timing glitch, not a rendering
  bug; a console 404 on an article page traced back to the auditor's
  own mistyped test URL from a prior navigation, not a real request
  the page made.
  Confirmed clean: zero real console/network errors, all SEO meta
  tags (title/description/canonical/OG) present and correctly
  localized, `lang="fa-IR"`/`dir="rtl"` correct, self-hosted fonts
  loading, mobile layout reflows with no horizontal overflow, footer
  nav links all resolve, social icons carry proper Persian
  `aria-label`s, and the §7 credit-block policy is correctly followed
  (no public-facing footer credit, which is intentional per that
  section's own explicit rule against a forced "powered by" link —
  not a gap).
  Found and fixed (this entry): the Contact page's privacy-policy
  link. Found, flagged for Farhad, not yet fixed (content/data,
  outside a developer's authority to silently resolve):
  1. Two posts under انتشارات حزب share the exact same title
     (`رويزيونيزم پسا ماركسيستي- لنينيستي- مائوئيستيدر حزب كمونيست
     (مائوئيست) افغانستان جاي ندارد`, at two different permalinks),
     using Arabic ي/ك letterforms instead of Persian ی/ک throughout,
     and missing a space between "مائوئيستي" and "در" — needs
     distinguishing titles (likely different volumes/parts) and a
     letterform/spacing fix in wp-admin.
  2. "منتخب آثار مائوتسه دون – جلد دوم" is filed under the
     جنبش بین‌المللی collection in کتابخانه while volumes ۱، ۳، and ۴
     of the same work are filed under آثار کلاسیک — looks like a
     mis-tag on that one post's `collection` term.
  3. **More significant:** re-reading this file's own 2026-08-06
     entries while tracing the dead privacy link surfaced that the
     Contact page's public email (`info.sholajawid@gmail.com`) was
     explicitly decided as a *placeholder* back then, pending
     Farhad's real address, and was never followed up — it's still
     the live recipient for both the CF7 contact-form submissions and
     the `mailto:` links on Contact and About. Flagged directly to
     Farhad as the top-priority item, since a developer substituting
     a guessed real address without being told it would be worse than
     leaving the documented placeholder in place.
  4. About page's editorial-board/funding/subscriber copy reads like
     placeholder-style boilerplate; flagged for Farhad to confirm it's
     the real, final copy (plausible as-is, e.g. staff using initials
     for safety, given the organization) rather than silently assumed
     either way.
  **Fixed:** the Contact page's "جزئیات در سیاست حریم خصوصی" line
  linked to a literal `href="#"` — a placeholder from the original
  2026-08-06 build (matching v6's own prototype, which never linked
  to a real privacy page either — see that date's entry). Rather than
  invent privacy-policy content or a page, wired it to WordPress
  core's own Privacy Policy mechanism: `get_privacy_policy_url()`
  (`page-contact.php`) — if a page is set under Settings → Privacy it
  links there, otherwise the sentence's second clause is dropped
  instead of pointing at a dead link. No plugin needed, no content
  invented on Farhad's behalf. Verified live on shola-jawid.local
  (no Privacy Policy page currently set, matching production): renders
  the fallback sentence cleanly, no console errors, no dangling link;
  will start linking automatically the moment a real Privacy Policy
  page is set, no further code change required.
  Theme version bumped 1.14.0 → 1.14.1.
  Approved by: Farhad, in this session (2026-09-11).

## 2026-09-13 — Phase 25 (fifth hero layout: filmstrip)

- **Added:** a fifth `hero_section` layout, `filmstrip` — the same
  full-bleed headline hero as `single`, with a horizontally scrolling
  strip of the site's other recent articles added directly below it.
  Farhad's request came with a reference screenshot of a modern
  SaaS-style hero (photo background, headline, a "Get Started" pill
  button, a star-rating line, and a card carousel underneath); before
  building anything, restated the request back to him in plain terms
  and got two explicit confirmations first: (1) the CTA button and the
  star-rating do not fit this site and are excluded entirely — only
  the headline/photo and the card strip are adapted; (2) the layout is
  mirrored for RTL rather than copied at the reference's LTR
  positions, and restyled to this site's own square-bordered, no-
  rounded-corners visual language (see `.card-spotlight`'s existing
  "no rounded/pill elements anywhere" principle) instead of the
  reference's circular arrow buttons and pill CTA shape.
  Implementation: `class-meta-fields.php`'s `sanitize_hero_layout()`
  and the hero_section admin layout picker gained the new `filmstrip`
  option (no rail-publication field needed — this layout isn't tied
  to one publication); `front-page.php` queries the site's 10 latest
  posts, excluding the headline article itself (Farhad's explicit
  instruction — "it will be duplicated" — unlike تازه‌ترین مقالات
  further down the page, which deliberately does NOT exclude the
  hero, a separate, older 2026-09-02 decision for that unrelated
  section); a new `shola_render_hero_filmstrip()` helper
  (`inc/template-tags.php`) and `template-parts/cards/hero-strip-
  card.php` (a new, deliberately minimal image+title+date thumbnail —
  too narrow at ~200px for either `card.php`'s or `issue-card.php`'s
  full anatomy, not a variant of either) render the strip and its two
  arrow buttons.
  Scroll mechanism, main.css §10.5 + main.js: `.hero-filmstrip-track`
  is a native `overflow-x: auto` scroll container — works with zero
  JS (touch, trackpad, keyboard), satisfying CLAUDE.md §5's "usable
  with JS disabled" floor on its own. main.js layers two enhancements
  on top: the arrow buttons call `scrollBy({behavior:'smooth'})`, and
  a slow, continuous auto-drift reverses direction at each end,
  pausing while a visitor is actually interacting with the strip.
  **Two real bugs caught and fixed via live testing, not assumed
  correct from reading the code:**
  1. A direct `track.scrollLeft = x` assignment was silently having no
     effect at all in this specific environment, while `scrollBy()`
     reliably moved it — confirmed by instrumenting the actual running
     script, not guessed. Rewrote the auto-drift to always move via
     `scrollBy()` (the same method the arrow buttons already used),
     removing an entire layer of "track our own intended position and
     resync against outside scrolls" bookkeeping the direct-assignment
     approach had needed — reading `scrollLeft` fresh each frame is
     safe once `scrollBy()` is the only write path, since nothing
     else's write can go stale against it.
  2. `scroll-behavior: smooth` in CSS on `.hero-filmstrip-track` was
     intercepting the auto-drift's own tiny per-frame `scrollBy()`
     calls, each restarting a new smooth-scroll animation before the
     last one finished, netting zero visible movement over time even
     though the underlying math was accumulating correctly. Removed
     that CSS property from the track (the arrow buttons already pass
     `behavior:'smooth'` explicitly per-call, so this cost them
     nothing) and drive the auto-drift with explicit `behavior:'auto'`
     (instant) per-frame nudges instead — the standard, animation-
     timing-independent technique, and also why a 0.4px/frame speed
     needed its own JS-side sub-pixel accumulator (`pendingFraction`)
     rather than passing that fractional amount to `scrollBy()`
     directly, since a sub-pixel delta rounds away to a 0px move on
     its own.
  Verified: RTL scroll-sign detection, arrow-button clicks, and the
  auto-drift's accumulation math all confirmed correct via direct
  live instrumentation of the actual shipped code (not a rewritten
  stand-in) on shola-jawid.local — a test hero_section entry (ID 225)
  created via WP-CLI with `shcore_hero_layout=filmstrip` and set
  active. Mobile layout re-verified separately (375px viewport):
  cards shrink to 148px, both arrows stay on-screen, no page-level
  horizontal overflow. Zero console errors throughout. One tooling
  caveat worth recording: this session's automated browser tool does
  not service `requestAnimationFrame` at all in its preview tab
  (confirmed directly — 0 callback firings measured over several
  seconds of real time, independent of the tab's visibility state),
  so the live continuous auto-drift animation itself could not be
  visually screen-recorded end-to-end in this session — verified
  instead by substituting `setInterval` for `requestAnimationFrame`
  around the exact same shipped tick logic and confirming it moves
  the strip correctly, plus a full manual code review of the
  accumulation math. `requestAnimationFrame` is a universally-
  serviced API in any real visitor's foregrounded browser tab; asked
  Farhad to do one live visual confirmation of the drift's smoothness
  on his own machine as the final check this session's tooling
  couldn't complete on its own.
  Theme version bumped 1.14.1 → 1.15.0. Plugin (shola-core) version
  bumped 1.7.0 → 1.8.0 (new `filmstrip` layout option lives in
  `class-meta-fields.php`, per §2's content-model ownership rule).
  Approved by: Farhad, in this session (2026-09-13).

- **Fixed — filmstrip layout redesigned same day, second pass.** Farhad
  compared the first version directly against his reference screenshot
  and flagged the real gap: it sat in its own centered, padded band
  below the photo, reading as a separate new section rather than part
  of the hero — not what the reference showed. Restated the specific
  differences back to him before touching code (per his own explicit
  "show me your understanding first" preference from earlier this
  session) and got confirmation before implementing:
  1. **Overlap, not a gap.** `shola_render_hero_filmstrip()`
     (`inc/template-tags.php`) no longer wraps the strip in `.wrap`;
     `.hero-filmstrip` (main.css §10.5) now bleeds full-width like
     `.hero-media` itself, with a new `margin-top:
     calc(-1 * var(--filmstrip-overlap))` pulling it up to visually
     overlap the photo's bottom edge. Overlap amount deliberately
     modest (64px desktop / 36px mobile), not matched pixel-for-pixel
     to the reference: this site's hero headline is bottom-anchored
     (`.hero-lead > .wrap`, `padding-block-end: 5rem`) — a different
     convention from the reference's top-anchored headline, which
     left its own photo's bottom edge free — so overlapping too far
     would collide with text every other hero layout already relies
     on. Tuned to sit clear of that reserved band, verified live at
     both desktop and 375px mobile widths.
  2. **Floating cards, not flat thumbnails.** `.hero-strip-card`
     (`template-parts/cards/hero-strip-card.php`) gained a box-shadow
     and 4px radius, reusing the exact shadow recipe `.hero-pub-card`
     already established for "a white card floating over the hero
     photo" in the `overlay` layout (2026-09-10) — the one place this
     design already breaks its own no-radius rule, deliberately, for
     this exact kind of floating element, so this isn't a new
     exception, it's applying an existing one consistently.
  3. **Image-forward cards, not text-below-image.** Title/date moved
     from a plain caption below the image to overlaid directly on it,
     behind a dark gradient scrim reusing `.hero-lead::after`'s own
     recipe scaled down — each card now reads as a small self-
     contained "mini-hero" tile (closer to the reference's clean,
     image-forward cards) instead of a photo-plus-caption list item
     that visually fought with the hero photo it now overlaps.
  Re-verified end-to-end on the same test hero_section entry (ID 225):
  overlap sits correctly below the headline's reserved bottom band at
  both desktop and mobile widths, no page-level horizontal overflow,
  card shadows and scrim text legible against both the photo behind
  them and each other, arrow buttons stay legible (opaque paper
  background + their own shadow) regardless of what's behind them.
  Theme version bumped 1.15.0 → 1.15.1 (plugin unchanged — this pass
  was theme-only markup/CSS).
  Approved by: Farhad, in this session (2026-09-13).

- **Fixed — filmstrip layout, third pass, same day.** Farhad tested
  the second pass live and sent back a marked-up screenshot of his own
  homepage with three concrete, specific problems, plus one separate
  request:
  1. Auto-drift "moving extremely fast left and right" — cut
     `assets/js/main.js`'s auto-drift `speed` from 0.4 to 0.15
     px/frame (roughly a 60% reduction).
  2. Headline title/dek sitting "very low," crowded by the overlapping
     strip right below it.
  3. The strip itself sitting "deep down" — not "completely visible"
     without scrolling on his actual screen.
  4. (Separate ask) per-card title/date text removed entirely —
     "clean, without any extra busy information."
  Root cause of #2/#3 together: the second pass's overlap amount and
  the shared hero's existing `padding-block-end: 5rem` (used by every
  other hero layout, tuned for those layouts' needs, not this one)
  left too little vertical room between the bottom-anchored headline
  text and the overlapping strip, and too little total headroom within
  one viewport for the strip to clear the fold on a real screen —
  this session's own testing had only checked that the *overlap itself*
  landed correctly, not the composition's total height against a real
  viewport, which is exactly the gap Farhad's live test caught.
  Fixed with a new `.hero-lead--filmstrip` modifier (main.css §10.5,
  added to the `<section>` in front-page.php's filmstrip branch,
  scoped to this layout only so `single`/`lead_rail`/`overlay`/
  `rail_full`'s shared rules are untouched): shortens `.hero-media` by
  an extra fixed amount (140px desktop / 100px mobile, on top of the
  usual masthead subtraction) so there's real headroom for the strip
  within one screen, and increases the headline `.wrap`'s
  `padding-block-end` from the shared 5rem to 8rem (6.5rem mobile) so
  the text sits higher, clear of the overlap zone. Card caption (title
  + date + its dark gradient scrim) removed from `hero-strip-card.php`
  entirely per point 4 — the title survives as the link's `aria-label`
  so a screen-reader user isn't left with an unlabeled link, only the
  visible text came off.
  Verified this time against the actual failure mode, not just the
  overlap's own position: measured `.hero-filmstrip`'s real bounding
  rect against `window.innerHeight` directly (not eyeballed) at both a
  1024×768 desktop viewport (strip bottom 741px, comfortably inside
  768px, ~27px margin) and a 375×812 mobile viewport (strip bottom
  798px, inside 812px) — both fit with zero scrolling required. Text-
  to-strip separation confirmed at ~110px on both sizes, plenty clear
  of the reserved band. Zero console errors.
  Theme version bumped 1.15.1 → 1.15.2 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-13).

- **Changed — filmstrip layout, fourth pass, same day.** Farhad sent
  another marked-up comparison against his reference screenshot with
  three more specific asks:
  1. The strip should no longer span the full photo width — only
     about four cards visible at once, "coming through from the
     right" (this site's inline-start/reading-start side) and fading
     out toward the left instead of a hard clip.
  2. Both arrow buttons should sit together in one cluster at that
     faded edge, not one flanking each end of the row.
  3. The card row should sit low enough that only part of it shows on
     the first screen (matching how his own reference screenshot is
     itself cropped right at the card row) — a direct reversal of the
     third pass's "fit entirely within one viewport" fix. Asked him
     directly via a multiple-choice check before touching this one,
     since it read as contradicting his own prior "thumbnails should
     be completely visible" request — confirmed he meant push the row
     lower / reduce the overlap, not increase it.
  Implementation: `shola_render_hero_filmstrip()` restructured so the
  track comes first and both arrow buttons move into a new
  `.hero-filmstrip-controls` wrapper after it (`inc/template-tags.php`).
  `.hero-filmstrip` (main.css §10.5) is no longer full-width — sized
  to `fit-content` with `margin-inline-end: auto` so it hugs the
  inline-start (right) edge with the same gutter as the headline text,
  instead of bleeding edge-to-edge. `.hero-filmstrip-track` capped to
  `860px` (roughly four 190px cards + gaps) with the remaining posts
  reachable via scroll/the arrows, and a `mask-image: linear-gradient
  (to left, black 78%, transparent 100%)` fading its far edge instead
  of a hard clip — a fixed physical-direction gradient, matching how
  `.hero-lead::after`'s own scrim already uses `to top` similarly for
  a decorative (not logical-property) purpose. `.hero-lead--filmstrip
  .hero-media`'s custom shortened height (added in the third pass)
  removed entirely — the photo is back to the same full-viewport
  height every other hero layout uses; only `.wrap`'s increased
  `padding-block-end` (still 8rem/6.5rem, keeping the headline clear
  of the strip) stays from that pass. `--filmstrip-overlap` reduced
  64px → 44px (36px unchanged on mobile) to land the reference's
  "partially cropped" look.
  Verified by measuring the actual visible fraction of a card against
  `window.innerHeight`, not eyeballed: ~28% visible at a 1024×768
  desktop viewport, ~34% at 375×812 mobile — both close to the
  requested "30% visible," with no page-level horizontal overflow at
  either size and headline-to-strip separation still ~126px. Zero
  console errors.
  Theme version bumped 1.15.2 → 1.15.3 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-13).

- **Changed — filmstrip layout, fifth pass, same day.** Farhad sent a
  third marked-up comparison with a correction and one more, more
  important request. Before touching code, restated all four points
  back to him in plain language (per his own explicit "ask me first"
  instruction this round) — including asking directly whether the
  "fade should be on the right, not left" point was its own request or
  actually describing the same problem the centering fix below would
  independently resolve — and got confirmation before implementing:
  1. **Visibility ratio was backwards.** He wanted ~70% of the card
     visible / ~30% cropped, not the fourth pass's ~30%/70% split —
     `--filmstrip-overlap` increased 44px → 100px desktop (36px → 72px
     mobile), pulling the strip up further so more of it shows above
     the fold.
  2. **Headline nudged higher again** — `.hero-lead--filmstrip >
     .wrap`'s `padding-block-end` 8rem → 9rem desktop (6.5rem → 7.5rem
     mobile), a further small adjustment on top of the third pass's
     fix, per his "a little upward" note.
  3. **Fade-direction question resolved by the fix below**, per his
     own confirmation — not changed separately.
  4. **The important one: bounded to the site's normal content
     column.** `.hero-filmstrip` was anchored to the raw viewport edge
     with `margin-inline-end: auto` (fourth pass) — correct up to
     1200px wide, but on any wider monitor it could stretch further
     toward the true browser edge than every other homepage section
     does. Changed to `max-width: var(--wrap-wide); margin-inline:
     auto` — the exact same centered-column treatment `.wrap` (main
     .css §02) already gives تازه‌ترین مقالات's grid and every other
     section — so past 1200px the cluster stays framed in the middle
     with open photo on both sides, instead of hugging one edge. The
     cluster still sits flush to that column's own inline-start
     (right) edge via the track+controls' ordinary flex-start
     placement, keeping the "enters from the right" reading order from
     the fourth pass — just bounded within the centered column now,
     not the full viewport.
  Verified: measured the actual visible card fraction again — 66% at
  a 1024×768 viewport, 68% at 375×812 mobile, both close to the
  requested 70%. Measured the centering fix directly at a 1920×1000
  viewport: strip spans exactly 1200px, margins of 352px/368px on
  either side (equal within rounding) — confirmed it does NOT reach
  the true browser edge on a wide monitor, which is exactly the
  regression this pass fixes. Text-to-strip separation still positive
  (~86px) at standard desktop width. Zero console errors, zero
  horizontal overflow at any tested width.
  Theme version bumped 1.15.3 → 1.15.4 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-13).

- **Changed — filmstrip layout, sixth pass, same day.** Farhad, happy
  with the overall direction, asked for two more fine-tuning passes:
  a further small upward nudge on the cards ("polish it... without
  harassing the eye," a design-quality ask rather than a new specific
  problem), and the auto-drift still reading as too fast even after
  the third pass's cut — "just bothering me... make sure it's very
  smooth and just a little movement."
  - `--filmstrip-overlap`: 100px → 118px desktop, 72px → 85px mobile —
    a modest further pull-up (not a big layout change), landing ~78%
    of the card visible at desktop / ~80% at mobile (up from ~66-68%),
    while re-confirming text-to-strip separation stays positive at
    both sizes (no collision reintroduced).
  - Auto-drift `speed` (assets/js/main.js): 0.15 → 0.05 px/frame — a
    third cut (0.4 → 0.15 → 0.05, roughly a 90% reduction from the
    original), since the first cut alone wasn't enough per this same
    live-feedback loop.
  Verified: visible-card-fraction and text-gap measurements re-run at
  both 1024×768 and 375×812 (78%/68px and 80%/77px respectively, no
  overflow either width), zero console errors. The drift speed itself
  still can't be visually screen-recorded in this session's tooling
  (see the second-pass entry's own note on requestAnimationFrame not
  firing in this environment) — the reduction is a direct, requested
  numeric change to the one variable controlling it, not a guess.
  Theme version bumped 1.15.4 → 1.15.5 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-13).

- **Changed — filmstrip layout, seventh pass, same day.** Farhad
  reported the auto-drift still read as too fast even after the
  sixth pass's cut, described what he wants as "a little soft
  movement... lively," and separately asked for a hover effect: each
  card should "get a little taller" when the pointer is over it.
  - `speed` (assets/js/main.js): 0.05 → 0.008 — a much larger cut
    (~6x) than the previous two increments, deliberately, rather than
    another small step: two prior reductions (0.4→0.15, 0.15→0.05)
    each still came back as "too fast," so this pass assumes the
    earlier steps were too timid rather than trying a third similar-
    sized nudge.
  - Hover "grow taller" (main.css §10.5): moved the existing hover
    effect from just the image (`.hero-strip-card-media img { transform:
    scale(1.04) }`) to the whole card (`.hero-strip-card:hover {
    transform: scale(1.07) }`) — the shadow now grows along with the
    photo, reading as the card itself lifting/enlarging rather than a
    zoomed-in photo inside a static frame. `transform-origin: center
    bottom`, not the default center: this card floats low over the
    hero photo (the layout's own negative-margin overlap), so growing
    from the true center would push its top edge further up into the
    photo/headline area above it — anchoring growth to the bottom
    keeps that upward creep from happening.
  Verified the hover rule is correctly present in the live stylesheet
  (`.hero-strip-card:hover { transform: scale(1.07); }`, confirmed via
  the page's own CSSOM, not just read from the source file) — a
  hover-triggered `transform:scale` is well-supported, low-risk CSS
  that doesn't need further live interaction testing to trust. The
  drift speed, as with the sixth pass, still can't be visually
  screen-recorded in this session's tooling; the cut is a direct,
  requested, and this time deliberately large numeric change to the
  one variable controlling it.
  Theme version bumped 1.15.5 → 1.15.6 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-13).

- **Fixed — filmstrip hover effect, same day.** Farhad's first live
  look at the seventh pass's hover effect showed a real problem with
  a marked-up screenshot: `scale(1.07)` grows both axes, so the
  hovered card visibly got *bigger* — wider too, crowding into its
  neighbor in the row — not "taller" as he'd actually asked for.
  Changed `.hero-strip-card:hover` from `transform: scale(1.07)` to
  `transform: scaleY(1.14)` (main.css §10.5) — scales only the
  vertical axis, so a card's width (and so its horizontal spacing
  against its neighbors) never changes on hover, only its height,
  matching "taller" literally instead of "bigger." `transform-origin:
  center bottom` (unchanged from the seventh pass) still anchors the
  growth to the bottom, so it's the top edge that extends upward, not
  a shift into the photo/headline the card overlaps below it.
  Verified the corrected rule is present in the live stylesheet via
  the page's own CSSOM (not just the source file). Zero console
  errors.
  Theme version bumped 1.15.6 → 1.15.7 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-13).

- **Fixed — filmstrip hover effect, second correction same day.**
  `scaleY()` did keep the width fixed as intended, but a *transform*
  scales rendered pixels — so the photo inside each card visibly
  stretched/warped vertically on hover, which Farhad's next screenshot
  correctly described as "only the image inside the frame is getting
  taller" (distorted), not the clean "frame gets taller" effect he
  wanted.
  Replaced the transform entirely with a real box-size change:
  `.hero-strip-card-media` now has an explicit `height: 152px`
  (106px mobile) that transitions to `173px` (121px mobile) on
  `.hero-strip-card:hover` — plain `height`, not `aspect-ratio`
  (not reliably animatable across browsers yet). Since
  `.hero-strip-card` itself has no explicit height of its own, it
  simply grows to fit its taller child, carrying its shadow along —
  the actual frame gets taller, and `object-fit: cover` on the image
  reveals more of the same undistorted photo as the box grows, rather
  than stretching pixels.
  This needed one supporting change: `.hero-filmstrip-track` gained
  `align-items: flex-end` (was the flex default, `stretch`) — without
  it, every card in the row would have been forced to match whichever
  one is tallest at any moment, so hovering one card would have
  visibly grown all of its neighbors too. `flex-end` also keeps every
  card's bottom edge on a shared baseline, so the hovered card visibly
  grows upward from that line, matching the intended effect.
  Verified the new rules live via the page's own CSSOM (`height: 152px`
  base / `height: 173px` on hover, `align-items: flex-end` on the
  track) rather than just reading the source file. Zero console
  errors.
  Theme version bumped 1.15.7 → 1.15.8 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-13).

- **Fixed — filmstrip hover effect, third correction same day.** The
  height-change approach avoided pixel distortion, but changing the
  media box's aspect ratio necessarily changes how much of the photo
  `object-fit: cover` shows — Farhad's final live look correctly read
  that as the image itself changing/"zooming" on hover, and asked for
  the photo to stay completely inert: only the frame should move.
  `.hero-strip-card-media` is now a single fixed height always (152px
  desktop / 106px mobile) with no hover rule touching it at all — the
  image genuinely cannot change under any circumstance. The card
  itself grows instead, via `padding-block-end` transitioning
  0 → 21px (0 → 15px mobile): since the media sits inside the same
  `overflow: hidden`, rounded, shadowed `.hero-strip-card`, the extra
  padding visibly extends the frame's own box (and its shadow, which
  follows the box) below the now-static photo, so the card reads as
  physically taller while the image above it never moves, re-crops,
  or scales. `.hero-filmstrip-track`'s `align-items: flex-end` (added
  the previous pass) stays required for the same reason as before —
  the card's total height still changes on hover, just via padding
  now instead of the media's own height, so siblings still need to
  size independently rather than being stretched to match.
  Verified the new rules live via the page's own CSSOM — confirmed
  `.hero-strip-card-media`'s `height` has no hover-scoped rule
  anywhere in the stylesheet (the image is provably static), and
  `.hero-strip-card:hover { padding-block-end: 21px }` is the only
  thing that changes. Zero console errors.
  Theme version bumped 1.15.8 → 1.15.9 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-13).

- **Changed — filmstrip hover, final version, same day.** Frozen-
  image + padding (previous entry) read as "not getting taller" and
  a "jump" to Farhad live. Asked him directly which trade-off he
  preferred — width-locked (no photo growth) vs. a uniform scale
  (photo grows too, tiny width increase) — he confirmed the uniform
  scale. `.hero-strip-card:hover` is now `transform: scale(1.06)`,
  `transform-origin: center bottom` so it grows upward from a fixed
  bottom edge, not a jump. Simplified in-code comments to short,
  one-line notes per his explicit request. Verified live via the
  page's CSSOM. Theme 1.15.9 → 1.15.10 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-13).

## 2026-09-14 — Phase 26 (site header/masthead CMS mechanism)

- **Added:** a new `masthead_section` CPT (shola-core), giving editors
  a CMS mechanism for the sitewide header (`<header class="masthead">`
  in header.php — logo/nameplate, top nav, search, hamburger menu,
  shown on every page) — Farhad's request, so a future header redesign
  (e.g. once the client sends a real logo) can be switched to via
  wp-admin instead of a code deploy being the only path.
  Deliberately the same mechanism as `hero_section` (multiple saved
  versions, one "active" flag with singleton enforcement, a layout
  picker inside each version, a list-table status column + one-click
  "set active" row action, an idempotent admin-seeded default entry)
  — see that CPT's own docblock for the original reasoning, not
  duplicated here. Deliberately a *separate* CPT from `hero_section`,
  not a shared one: one controls the homepage's own lead-article hero,
  this controls the sitewide header above it (and every other page) —
  different template, different scope, and needed clearly distinct
  Persian admin-menu labels ("هدر و ناوبری سایت" vs. hero_section's
  "هدر صفحهٔ اصلی") so the two aren't confused for the same setting.
  This is Phase 1 only, matching how hero_section itself was rolled
  out: the mechanism ships with exactly one layout, `default`,
  reproducing today's existing header markup byte-for-byte (verified
  live — zero visual change, zero console errors) — `header.php` now
  branches on `shola_get_active_masthead_layout()`
  (`inc/template-tags.php`, same active-flag lookup pattern as
  front-page.php's hero query) wrapped around the untouched existing
  markup as the `default` case. A real second layout (with the
  client's actual logo) is a separate, later step once Farhad provides
  it, added as a new `elseif` branch the same way hero_section's four
  extra layouts were each added one at a time.
  New files/functions: `class-post-types.php` (CPT registration),
  `class-meta-fields.php` (`shcore_masthead_active`/
  `shcore_masthead_layout` post meta, `render_masthead_metabox()`,
  `deactivate_other_masthead_sections()`, status column, row action,
  `seed_default_masthead_section()` — all mirroring their hero_section
  counterparts exactly).
  Verified via WP-CLI (not just read as correct-looking code): CPT
  registers (`wp post-type list` shows `masthead_section`), the seed
  function creates one active, `default`-layout entry when none
  exists, and the live homepage's rendered header is pixel-identical
  to before this change with zero console errors.
  Theme version bumped 1.15.10 → 1.16.0. Plugin (shola-core) version
  bumped 1.8.0 → 1.9.0 (new CPT/meta fields live in the plugin, per
  §2's content-model ownership rule).
  Approved by: Farhad, in this session (2026-09-14).

- **Added:** second masthead_section layout, `logo` — replaces the
  text nameplate with the client's flag logo, per Farhad's request
  (his own file: `Shola Jawid Header Logo.webp`, 503×482px,
  transparent background, pole intentionally included — kept exactly
  as supplied, not cropped). Restated the request back to him as a
  plan before building anything (his own "ask me first" preference,
  used consistently this session) and got confirmation.
  Implementation:
  - Logo image managed through WordPress's own native Customizer
    (Appearance → Customize → Site Identity), not a separate custom
    field — `add_theme_support('custom-logo')` (already present in
    `inc/setup.php`, unused until now) given real `width`/`height`
    (240×240, a quality baseline, not the render size) and
    `flex-width`/`flex-height` so the crop tool doesn't force the
    logo's near-square-but-not-exact ratio into a fixed square.
  - New `inc/customizer.php`: hooks `customize_register` to add a
    short helper sentence under that Logo control — recommended
    size/format for good quality — per Farhad's explicit ask, so
    whoever uploads a replacement logo later isn't guessing.
  - `header.php`'s single shared masthead markup (not duplicated —
    the nav/menu is identical either way, only the brand block
    differs) now swaps `.mast-nameplate` for
    `wp_get_attachment_image( get_theme_mod( 'custom_logo' ), ... )`
    when the `logo` layout is active, wrapped in the *same* `.mast-
    brand` link, not `the_custom_logo()`'s own output — that function
    wraps the image in its own `<a>`, which would nest inside the
    existing brand link (invalid HTML). Falls back to the plain
    nameplate if no logo is set, so an empty Customizer field can't
    leave the header looking broken. The date line (`.mast-runner`)
    is unchanged and untouched — stays exactly where it was, next to
    the logo instead of the name.
  - `main.css` §05: new `.mast-logo` — sized by `height`
    (`clamp(40px, 7vw, 52px) * var(--mast-scale)`), not a fixed
    width, since the logo is a flag graphic (near-square), not a wide
    horizontal wordmark — height-based sizing keeps it from looking
    stretched or squashed at any screen width, and the same `clamp()`
    + `--mast-scale` formula `.mast-nameplate` already uses means it
    shrinks with the sticky/scrolled masthead exactly like the text
    version did.
  - `class-meta-fields.php`: `sanitize_masthead_layout()` and the
    admin layout picker gained the `logo` option, labeled "چیدمان با
    لوگو".
  Shipped as a real, live change, not just the mechanism: uploaded
  the actual logo file to the media library, set it as the site's
  Customizer logo, and created + activated a new masthead_section
  entry with `shcore_masthead_layout = logo` (all via WP-CLI).
  Verified live: logo renders centered, correctly proportioned,
  transparent background blending cleanly into the red masthead, at
  52px tall on desktop (clamp ceiling) shrinking to ~35px on scroll
  (0.68× `--mast-scale`, matching the nameplate's own shrink ratio),
  and correctly sized on a 375px mobile viewport — zero console
  errors at any size, zero visual regression to the `default` layout.
  Theme version bumped 1.16.0 → 1.17.0. Plugin (shola-core) version
  bumped 1.9.0 → 1.10.0.
  Approved by: Farhad, in this session (2026-09-14).

- **Fixed — `logo` masthead layout, same day.** Farhad's live look at
  the first version (with a marked-up screenshot) found the logo
  stacked with the date under it looked small and awkward, with the
  center column reading visibly taller than the rest of the bar.
  Measured the actual cause rather than guessing: `.mast-brand`'s
  rendered height was 95.55px — driven by the stacked logo+date
  content — against `.masthead-left`/`.masthead-right`'s own ~42-47px,
  and CSS Grid sizes the whole row to its tallest column.
  Fix: the date moves out of the center column entirely, into
  `.masthead-left` next to the menu button (new `.mast-runner--inline`
  modifier, ~2px smaller — 13px → 11px — per Farhad's ask, `hide-
  mobile` since that row is already tight on a phone screen), so
  `.mast-brand` holds only the logo. With the date gone, `.mast-logo`
  could grow substantially larger while the *total* masthead height
  stayed the same as before this whole feature — confirmed by
  measuring `.masthead`'s real rendered height with the original
  `default` layout active (144.68px) and matching `.mast-logo`'s clamp
  ceiling to it empirically (56px → 139.55px total, still under
  target; 88px → 151.55px, over; 81px landed at 144.55px, within
  0.13px of the 144.68px target) rather than guessing a number.
  Verified live: logo now 81px tall at rest (up from 52px), masthead
  total height unchanged from before the `logo` layout existed at all,
  date legible in its new spot and still correctly disappearing on
  scroll (same collapse behavior as before, just relocated), checked
  at desktop, scrolled/compact, and 375px mobile — zero console
  errors, zero horizontal overflow.
  Theme version bumped 1.17.0 → 1.17.1 (plugin unchanged — this pass
  was theme-only markup/CSS).
  Approved by: Farhad, in this session (2026-09-14).

- **Fixed — date placement, `logo` layout, same day.** Farhad's next
  screenshot flagged the date's new spot as wrong: placed first in
  `.masthead-left` (before منو), it rendered as the outermost/first
  element in RTL reading order — ahead of the menu button, which read
  as unprofessional (a date outranking primary navigation).
  Moved to the opposite side instead: the outer edge of
  `.masthead-right`, after the search icon — now the true leftmost
  element in the bar, with منو correctly first in its own cluster.
  `header.php` swap only; `.mast-runner--inline`'s styling (11px, no
  block stacking) from the previous pass is unchanged, just relocated.
  Verified: masthead height unchanged (144.55px, same as the previous
  pass's own measured target), scroll-collapse behavior intact, no
  console errors, no horizontal overflow at mobile width.
  Theme version bumped 1.17.1 → 1.17.2 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-14).

- **Changed — masthead height, `logo` layout, same day.** Farhad's
  next look, with the bar's top/bottom edges marked directly on a
  screenshot, asked for it more compact vertically — reversing the
  earlier pass's own goal of matching the `default` layout's height
  exactly. With his own explicit correction as the newer, more
  specific instruction, tightened the padding instead of preserving
  parity with `default`.
  New `.masthead--logo` class, added to `<header>` only when this
  layout is active (`header.php`), scopes `.masthead-inner`'s
  `padding-block` down from the shared 1.5rem (0.75rem here — 24px →
  12px each side) without touching `default`'s own spacing at all.
  Same tightening at the ≤720px breakpoint (1rem → 0.6rem).
  Verified: masthead height dropped from 144.55px → 120.55px at
  desktop (375px mobile: 90.75px), the logo (still 81px tall — not
  reduced, only the surrounding padding was) fits with an even 12px
  margin top and bottom, confirmed via `getBoundingClientRect()`
  rather than eyeballed. Scroll-collapse and mobile layout re-checked,
  zero console errors, zero overflow. `default` layout's own height
  confirmed unaffected (no `.masthead--logo` class present there).
  Theme version bumped 1.17.2 → 1.17.3 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-14).

- **Changed — masthead height, `logo` layout, same day, one more
  pass.** Farhad's next look, after the previous compacting pass, said
  it still needed to be "more compact" and asked to shorten it "by
  15%" from where it stood. Read that as 15% off the previous pass's
  own measured 120.55px total, targeting ≈102.47px.
  `.masthead--logo .masthead-inner`'s `padding-block` reduced again
  (0.75rem → 0.185rem — the same 24px-per-side spacing that started
  this whole thread down to roughly 6px each side at rest scale),
  same ≤720px breakpoint override reduced proportionally by the same
  ratio (0.6rem → 0.15rem). Only the padding moved; `.mast-logo`'s own
  height (81px) is untouched, same as every prior pass in this thread.
  Verified via `getBoundingClientRect()`: masthead height landed at
  102.45px (target 102.47px, off by 0.02px), a 15.0% reduction from
  the prior 120.55px. Mobile re-checked at 375px width: 75.92px,
  no horizontal overflow, logo/search/menu row reads cleanly. Zero
  console errors. Scroll-collapse (`.is-scrolled` / `--mast-scale`)
  logic itself is unchanged — only the rest-state padding value did —
  so it continues to compound the same way it already did before this
  pass.
  Theme version bumped 1.17.3 → 1.17.4 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-14).

- **Fixed — date vanishes on the sticky/shrunk masthead, `logo`
  layout.** Farhad's live look at the sticky header once scrolled
  showed the date's spot empty. Root cause: `.mast-runner--inline`
  (the relocated date, `header.php`) keeps the base `.mast-runner`
  class for its shared typography, and a pre-existing rule —
  `.masthead.is-scrolled .mast-runner { max-height: 0; opacity: 0; }`,
  written for the `default` layout's stacked date so it disappears
  once the bar compacts — matched it too, hiding Farhad's relocated
  date the instant the masthead hit `.is-scrolled`. Scoped that rule
  to `.mast-runner:not(.mast-runner--inline)` so it only ever touches
  the `default` layout's own date; `.mast-runner--inline` now stays
  visible through the scrolled state, as intended (it's already a
  single compact row item, not something that needs to collapse).
  Verified: forced `.is-scrolled` and read the date's computed style
  (`opacity: 1`, `max-height: 24px`, non-zero rect) plus a screenshot
  showing it rendered at the bar's outer-left edge; `default` layout's
  own hide-on-scroll rule unaffected (selector only adds an exclusion,
  doesn't remove the original match for elements without the modifier
  class). Zero console errors.
  Theme version bumped 1.17.4 → 1.17.5 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-14).

- **Changed — mobile-only regrouping of the logo and date, `logo`
  layout.** Farhad's phone-size look (DevTools device toolbar, iPhone
  16 emulation) found the logo floating alone in the middle column
  with nothing anchoring it, no date anywhere (its span was
  `hide-mobile` in header.php, desktop/tablet only up to this point),
  and asked for it fixed on mobile only — tablet/desktop keep the
  centered-logo design from the earlier rounds, untouched.
  `header.php`: removed `hide-mobile` from `.mast-runner--inline` (the
  date span) — its preceding separator slash stays desktop/tablet-only,
  nothing to separate from on mobile. `main.css`, inside the existing
  `@media (max-width: 720px)` block, scoped to `.masthead--logo` only:
  a 4th grid column turns the row into menu cluster (col 1, unchanged,
  right) — flexible spacer (col 2) — date (col 3) — logo (col 4,
  leftmost), via explicit `grid-column` per item (auto-placement can't
  be told which of 4 columns to leave empty with only 3 items).
  Also needed `order: 1/2/3` on the three items: grid's auto-placement
  cursor only advances forward through columns in source order, and
  `mast-brand` (col 4) sits before `.masthead-right` (col 3) in the
  markup — without reordering, the cursor reached column 4 first and
  couldn't go back for column 3, pushing the date onto its own
  implicit second row instead of sitting beside the logo (found live —
  the date rendered as a second thin bar under the masthead). Giving
  `.masthead-right` a lower `order` than `.mast-brand` fixed it.
  Verified via `getBoundingClientRect()` at 375px width: single grid
  row (was two), masthead height 76.35px, logo flush to the left edge,
  date immediately to its right, menu/search cluster unmoved on the
  right. `.is-scrolled` re-checked (date stays visible, no regression
  from the previous round's fix). No horizontal overflow. Tablet
  (768px) and desktop re-checked and confirmed byte-for-byte unchanged
  (still 102.45px, centered logo) — the mobile rules are inside the
  ≤720px query and scoped to `.masthead--logo`, so they can't leak
  upward. Zero console errors.
  Theme version bumped 1.17.5 → 1.17.6 (plugin unchanged).
  Approved by: Farhad, in this session (2026-09-14).

- **Added: third masthead layout, `logo-light` — same structure as
  `logo`, reversed colors.** Farhad's explicit ask: duplicate the
  `logo` layout exactly, white background, red text/icons, logo image
  itself untouched ("without the flag" — the flag graphic keeps its
  own colors, only the surrounding chrome inverts). Now three options
  in the masthead_section layout picker: `default` (text nameplate),
  `logo` (red background), `logo-light` (white background).
  `shola-core` (`class-meta-fields.php`): `sanitize_masthead_layout()`
  allow-list extended to `array('default','logo','logo-light')`; the
  admin `<select>` gets a third `<option>`, and the existing two got
  "(پس‌زمینهٔ قرمز)"/"(پس‌زمینهٔ سفید)" suffixes added to their labels so
  the picker itself makes the red/white distinction clear (previously
  just "چیدمان با لوگو" — fine when there was only one logo layout, no
  longer disambiguating with two). Plugin version bumped 1.10.0 →
  1.11.0.
  `shola-jawid` (`header.php`): `logo-light` is deliberately NOT a
  parallel branch of `logo` — it reuses every `'logo' === layout`
  structural check (the logo image, the date's position, the mobile
  regrouping) via a new `$shola_is_logo_layout` boolean, since Farhad
  was explicit that the structure is identical and only color inverts.
  A second, narrower check (`'logo-light' === layout`) adds one extra
  class, `masthead--logo-light`, purely for the color override.
  Duplicating the whole branch instead would have meant every future
  structural tweak (like the two mobile-layout fixes earlier today)
  needing to be made and verified twice.
  `main.css`: new `.masthead--logo-light` block overrides exactly the
  properties that use `var(--paper)` (or a color-mix of it) elsewhere
  in the masthead — background, `.mast-btn`, `.mast-runner`,
  `.mast-slash`/`.mast-slash-light`, `.mast-icon-link`, focus outline —
  swapped to `var(--winston-red)`/`var(--winston-red-deep)`. No new
  grid/spacing/responsive rules: the mobile regrouping and sticky-
  shrink behavior added earlier today apply to `logo-light` automatically
  since it shares the `masthead--logo` structural class.
  One real bug caught before shipping: `.masthead .mast-icon-link` (the
  existing red-layout search-icon color rule, further down this file)
  has the exact same specificity as `.masthead--logo-light
  .mast-icon-link` (two classes each) and comes later in source order,
  so it was silently winning the cascade and leaving the search icon
  white on the white background. Fixed by repeating `.masthead` in the
  `logo-light` selector (`.masthead.masthead--logo-light
  .mast-icon-link`), which out-specifies it regardless of order.
  Verified live (temporarily set the active masthead_section entry to
  `logo-light` via WP-CLI, screenshotted, then reverted to `logo`):
  white background, red menu/nav/date/search-icon text at both desktop
  and 375px mobile widths, correct through `.is-scrolled`, flag logo
  unrecolored, zero console errors. Reverted the live site's active
  layout back to `logo` afterward — this round only adds the option,
  Farhad didn't ask to switch the live site to it.
  Theme version bumped 1.17.6 → 1.18.0 (minor bump: new layout option,
  not a fix).
  Approved by: Farhad, in this session (2026-09-14).

- **Fixed — asymmetric gap under the logo, both `logo`/`logo-light`
  layouts; also made the logo bigger.** Farhad's live look at the
  white variant (which he'd switched to himself in wp-admin) found the
  gap under the flag noticeably wider than the gap above it, and asked
  for the flag a little bigger too.
  Root cause, confirmed via `getBoundingClientRect()`: `.mast-logo` is
  `display: inline-block`, which sits on its line box's text baseline
  by default — that reserves descender space below the image that
  isn't there above it, measured at 2.95px above vs. 16.9px below for
  the same `padding-block` on both sides of `.masthead-inner`. Fixed
  with `vertical-align: middle` on `.mast-logo`, which centers the
  image on the line instead of baseline-aligning it — not a layout
  restructure, a one-line property most image-in-a-line-box gaps like
  this come down to.
  Also bumped the logo's own size: `.mast-logo`'s clamp ceiling
  81px → 92px (~13.6% larger), per Farhad's "make the flag a little
  bigger" — the clamp's other two arguments (`56px` floor, `9.5vw`
  preferred) are untouched, so responsive scaling between them is
  unchanged, only the desktop ceiling grew.
  Both changes live in the shared `.mast-logo` rule, not a layout-
  scoped one, so they apply identically to `logo` (red) and
  `logo-light` (white) — verified on both live.
  Verified via `getBoundingClientRect()`: gap above/below now both
  2.95px (was 2.95px/16.9px) — perfectly symmetric. Masthead height
  actually dropped slightly, 102.45px → 99.5px, despite the bigger
  logo: removing the ~14px phantom baseline gap outweighed the ~11px
  the taller logo added. Mobile re-checked (375px: masthead 62.4px, no
  horizontal overflow), zero console errors on both widths.
  Theme version bumped 1.18.0 → 1.18.1.
  Approved by: Farhad, in this session (2026-09-14).

- **Added: fourth masthead layout, `logo-radial` — the red layout,
  plus a radial gradient behind the flag.** Farhad's ask, previewed
  first as a set of static mockups (dot grid / diagonal stripes / star
  pattern / single watermark for a background-texture question, then a
  white→red radial-gradient concept specifically) before approval:
  duplicate the red `logo` layout exactly ("keep the weight as is"),
  the only change being the background — white at the center, blending
  out to the same red used everywhere else, so the flag gets a soft
  halo without the header's overall red identity changing.
  Fourth option in the masthead_section layout picker alongside
  `default` / `logo` / `logo-light`. `shola-core`:
  `sanitize_masthead_layout()` allow-list extended to include
  `logo-radial`, admin `<select>` gets a fourth `<option>`. Plugin
  version bumped 1.11.0 → 1.12.0.
  `header.php`: refactored the class-building from chained ternaries
  to a small `$shola_masthead_modifier` variable, now that there are
  three modifier classes instead of one — `logo-radial` reuses
  `$shola_is_logo_layout` (extended to include it) for every
  structural check, same as `logo-light` did, so none of today's
  earlier structural fixes (mobile regrouping, date-on-scroll, gap-
  under-logo) needed to be redone for a third layout.
  `main.css`: new `.masthead--logo-radial` rule — unlike `logo-light`,
  this one recolors nothing (text/icons/border-bottom stay exactly as
  the red layout), it only layers a `radial-gradient()`
  `background-image` on top of the existing red `background-color`.
  Used an explicit fixed-radius circle (`circle calc(100px *
  var(--mast-scale)) at center`), not a percentage-based gradient
  (which defaults to `farthest-corner` and would stretch the white
  bloom out toward the nav text on a wide desktop window) — verified
  live that it fades to solid red well before reaching "دربارهٔ ما"/
  "منو" on either side. `* var(--mast-scale)` shrinks the halo with
  the logo in the sticky-compact state, matching every other sized
  value in this layout.
  Two things caught before shipping, not after:
  (1) `background-repeat`'s default is `repeat` — a fixed-size circle
  smaller than the box tiles into a grid of red/white circles across
  the whole bar without an explicit `background-repeat: no-repeat`.
  (2) The mobile-only rules from earlier today move the logo flush to
  the left edge (`.masthead--logo .mast-brand { grid-column: 4 }`),
  but the gradient's `at center` still meant "center of the whole bar"
  — on mobile that's nowhere near where the logo actually sits anymore,
  so the halo appeared centered on empty space while the flag sat
  unlit to the side. Fixed with a `≤720px`-scoped override
  repositioning the gradient (`at 49px center`, matching the logo's
  measured on-screen center) and using a smaller radius to match the
  shorter mobile bar.
  Verified live on both breakpoints and through `.is-scrolled`: the
  halo tracks the logo correctly at both desktop-centered and mobile-
  flush-left positions, shrinks proportionally when the masthead
  compacts, no overflow, zero console errors. Reverted the live site's
  active layout back to `logo` afterward, same as the `logo-light`
  round — this only adds the option.
  Theme version bumped 1.18.1 → 1.19.0 (minor bump: new layout option).
  Approved by: Farhad, in this session (2026-09-14).

- **Changed — `logo-radial` made much bolder, per a reference image.**
  Farhad's first-pass version (small fixed-radius white circle) read as
  too subtle; he sent a reference image of the concept applied far more
  boldly — a wide gradient bleed covering most of the bar's width, a
  noticeably bigger flag, and the flagpole visibly cropped off at the
  top and bottom rather than fully contained in the bar — and asked to
  match it exactly.
  Gradient: switched from a `circle` with a fixed pixel radius to an
  `ellipse` sized as percentages of the bar itself (horizontal radius a
  fraction of width, vertical radius spanning the full height), which
  is what produces the reference's wide-short bleed shape. Also added a
  solid-white plateau before the fade to red (`white 0%, white 18%, red
  100%` instead of a straight `white 0%, red 100%`) — the straight
  version mixed to red too quickly to read as a visible glow once the
  bigger flag was covering most of the gradient's original narrow
  width; first tried at the old narrow width, confirmed via screenshot
  it was nearly invisible, before widening it.
  Cropped pole: `.masthead--logo-radial .masthead-inner` gets a
  *definite* height (not the shared padding-driven auto height every
  other layout uses) plus `overflow: hidden`; `.mast-logo` is sized
  taller than that (180px desktop, up from 92px). CSS Grid still sizes
  its implicit row to the tallest item's natural content height
  regardless of the container's own specified height, so the oversized
  logo pushes the grid's content past the container's box, which
  `overflow: hidden` clips top and bottom symmetrically (centered by
  the existing `align-items: center`). Deliberately sizing+clipping
  rather than `position: absolute` on `.mast-brand` (which would have
  been a simpler way to decouple it from row-height) — absolute
  positioning would also un-place it from the `grid-column` assignment
  the mobile-only rules give it, undoing the flush-left mobile position
  from earlier today. This way the crop works at both the desktop-
  centered and mobile-flush-left position without touching placement.
  One real bug caught before shipping, not a design choice: the mobile
  override for this layout's gradient/sizing was originally placed
  inside the existing `@media (max-width: 720px)` block alongside the
  other `.masthead--logo` mobile rules — physically *before* the
  unconditional `.masthead--logo-radial` rule further down the file.
  Same selector, same specificity (one class each); a media query
  changes *when* a rule applies, not its specificity, so with both
  rules matching at ≤720px the tie went to whichever came later in the
  file — the unconditional one. Found live: at 375px width the
  gradient and crop sizing stayed at full desktop values, centered on
  the whole bar, while the actual (flush-left) logo sat unlit off to
  the side. Fixed by moving the mobile override into its own `@media`
  block placed *after* the unconditional rule, so it now correctly
  wins the specificity tie at mobile widths.
  Verified live on both breakpoints and through `.is-scrolled`: wide
  visible white bleed roughly matching the reference's proportions,
  flagpole cropped top/bottom, mobile gradient/crop now correctly
  tracks the flush-left logo, no horizontal overflow, zero console
  errors. Reverted the live site's active layout back to `logo`
  afterward, same as every other layout-option round today.
  Theme version bumped 1.19.0 → 1.19.1.
  Approved by: Farhad, in this session (2026-09-14).

- **Fixed — `logo-radial`'s crop was silently bottom-only, dragging
  the nav row down with it.** Farhad's screenshot from his own (real
  Chrome, not this session's automated browser pane, which had
  rendered the same CSS looking plausibly correct) browser showed the
  flagpole's top fully intact, only the bottom cut off, and the whole
  nav row sitting near the bottom edge of the bar instead of its
  vertical middle — "nothing like" the reference.
  Root cause: `align-items: center` (already on `.masthead-inner`,
  shared by every layout) only centers an item *within its own row
  track* — it says nothing about where an oversized row sits inside a
  container whose specified height is smaller than that row's content.
  Without `align-content`, Grid places a too-tall row flush to the
  container's block-start (top) by default, so the ~80px difference
  between the 180px logo and the 100px container overflowed entirely
  at the bottom. Since the nav items share that same row and are also
  just `align-items: center`-ed within it, they landed at the row's
  own vertical middle — which, in a row anchored to the container's
  top and taller than the container itself, sits below the container's
  visible center, near its bottom edge.
  Fixed with one property: `align-content: center` on
  `.masthead--logo-radial .masthead-inner`, which centers the row
  itself (not just items within it) inside the container when the two
  heights differ. Applies automatically on mobile too — the mobile
  override rule doesn't set `align-content`, so it inherits this fix
  from the base rule without needing its own copy.
  Verified via `getBoundingClientRect()`: the logo now overflows
  exactly 40px above and 40px below the 100px container (symmetric),
  and the nav row's vertical center lands at y=50 — dead center.
  Re-checked visually and via console on both desktop and mobile,
  resting and `.is-scrolled` states; zero errors. Reverted the live
  site's active layout back to `logo` afterward.
  Theme version bumped 1.19.1 → 1.19.2.
  Approved by: Farhad, in this session (2026-09-14).

- **Changed — reverted `logo-radial`'s crop, narrowed the gradient.**
  Farhad's next live look said this "is not close to the prototype" on
  two specific points: the white gradient was much wider than the
  reference, and the flag being cropped top/bottom (added two rounds
  ago) was wrong — the reference's flag is fully visible; that read
  came from a tightly-exported reference image, not an intentional
  clipped-pole effect.
  Removed the entire crop mechanism: `.masthead--logo-radial
  .masthead-inner`'s definite `height`/`overflow: hidden`/
  `align-content: center` and `.mast-logo`'s 180px override are gone.
  `.mast-logo` and `.masthead-inner` are back to the exact same shared
  rules the plain `logo` layout uses — confirmed masthead height is
  now 99.5px, identical to `logo`/`logo-light`.
  Narrowed the gradient: ellipse horizontal radius 45%/55%
  (desktop/mobile) down to 16%/26%, and the solid-white plateau before
  the fade to red shortened from 18% to 8% — red now dominates most of
  the bar, a narrow bright band sits behind the flag, matching the
  reference's proportions far more closely than the previous pass.
  Verified live on desktop and mobile, resting and `.is-scrolled`
  states: flag fully visible (no clipping), narrow visible white band,
  masthead height matches the other logo layouts exactly, no
  horizontal overflow, zero console errors. Reverted the live site's
  active layout back to `logo` afterward.
  Theme version bumped 1.19.2 → 1.19.3.
  Approved by: Farhad, in this session (2026-09-14).

- **Changed — mobile masthead date moved from flush-left to absolute
  center, all three `logo`-based layouts.** Farhad's live look at the
  phone size (real Chrome device toolbar, iPhone 16 Pro Max) said the
  date sitting at the left (the flush-left grouping added earlier
  today) should be dead center instead.
  Scoped to the shared `.masthead--logo` mobile rules (`main.css`, the
  existing `≤720px` block) — applies identically to `logo`,
  `logo-light`, and `logo-radial`, no per-layout duplication needed.
  True centering of the `[date, logo]` pair, independent of the menu
  button's own width rather than just centered in whatever space
  happened to be left after it: `.masthead-left` (the menu) comes out
  of the grid's column flow entirely via `position: absolute`, so it
  no longer consumes a track or biases where "center" falls; the
  remaining two items get `grid-template-columns: 1fr auto auto 1fr`,
  two matched flexible spacers centering the pair in the full row.
  Two mistakes caught and fixed before shipping, not after:
  (1) first tried `inset-inline-end: 0` to keep the menu at its usual
  right-side spot — wrong physical side. In this RTL page inline flow
  runs right-to-left, so "start" is the physical right; `-end` is the
  physical left, and it put the menu on the wrong side of the bar
  entirely, caught immediately via screenshot.
  (2) after correcting to `inset-inline-start`, the menu rendered
  flush against the true viewport edge with no margin. An absolutely
  positioned element's containing block is its ancestor's padding
  *edge* — the boundary between border and padding, not inset by that
  padding — so `inset-inline-start: 0` ignored `.masthead-inner`'s own
  `.wrap`-derived `padding-inline` entirely. Fixed by offsetting with
  `var(--gap-pad-x)`, the same token `.wrap` uses for that padding.
  Also removed `logo-radial`'s mobile-specific gradient-position
  override (previously hardcoded to the old flush-left offset): now
  that mobile centers the same way desktop does, the unconditional
  `at center` rule already lines up correctly at every width without
  a special case.
  Verified live on all three logo-based layouts at 375px width and
  through `.is-scrolled`: `[date, logo]` pair centered at the row's
  true midpoint (confirmed via `getBoundingClientRect()`, cluster
  center within 0.1px of half the row width), menu inset correctly
  from the edge, `logo-radial`'s white bloom correctly follows the now-
  centered flag, no horizontal overflow, zero console errors. Desktop/
  tablet re-checked unaffected (rules are inside the existing ≤720px
  block only). Reverted the live site's active layout back to `logo`
  afterward.
  Theme version bumped 1.19.3 → 1.19.4.
  Approved by: Farhad, in this session (2026-09-14).

- **Changed — `logo-radial`'s mobile arrangement diverges from
  `logo`/`logo-light`: date back at the left edge, flag independently
  centered.** Farhad's next live look, at this specific layout only,
  asked for it to look different from the other two on mobile — date
  on its own at the left, flag centered on the white gradient bleed
  "so it looks beautiful" — with an explicit instruction not to touch
  `logo`/`logo-light` (which keep the `[date, logo]`-centered-as-a-pair
  arrangement from the previous round) or desktop/tablet.
  Added a `logo-radial`-specific mobile override, layered on top of
  the shared `.masthead--logo` mobile rules via equal-specificity/
  later-source-order (same technique as the earlier `logo-radial`
  mobile overrides): `.masthead-right` (date) becomes a second
  `position: absolute` element pinned to the opposite edge from the
  menu, with `grid-column: auto` clearing the column it would
  otherwise inherit from the shared rule — needed because an
  out-of-flow grid item still uses an explicitly assigned grid AREA as
  its containing block instead of the whole container, which would
  have confined its `inset-inline-end` to one narrow column instead of
  the full row. With the date now out of flow alongside the menu,
  `.mast-brand` (the flag) is the only in-flow grid item left, so a
  plain `1fr auto 1fr` centers it alone in the full row — lining up
  exactly with the gradient's existing `at center`.
  Verified live: flag's measured horizontal center lands within 0.1px
  of exactly half the row's width, date sits at the far-left edge, no
  horizontal overflow, correct through `.is-scrolled`, zero console
  errors. Re-verified `logo` unaffected on mobile (still the centered
  pair) and desktop/tablet unaffected on all three layouts. Reverted
  the live site's active layout back to `logo` afterward.
  Theme version bumped 1.19.4 → 1.19.5.
  Approved by: Farhad, in this session (2026-09-14).

- **Added: logo in the footer, replacing the plain text site name.**
  Farhad's explicit ask, independent of the masthead work above: the
  footer's `.footer-name` ("شعله جاوید" in text) replaced with the same
  logo set at Appearance → Customize → Site Identity, sized reasonably
  and not distorted, good on mobile too.
  `footer.php`: reads the same `get_theme_mod( 'custom_logo' )` the
  header's `logo`/`logo-light`/`logo-radial` layouts already use —
  still exactly one place to manage the logo — independent of which
  masthead layout is active, including `default` (the plain-text one).
  Falls back to the plain text nameplate if no logo has been uploaded,
  same fallback pattern as the header, so an empty Customizer field
  never leaves the footer looking broken.
  `main.css`: new `.footer-logo` rule, height-based sizing (44px, width
  auto) so the flag's aspect ratio is preserved and it can't stretch
  out of shape — comfortably smaller than the masthead's own logo
  (81-92px) so it doesn't compete with the header, and reads at
  roughly the same visual weight as the text it replaces. No baseline-
  alignment gap fix needed here (unlike `.mast-logo` in the masthead):
  this replaces a standalone block-level `<p>`, not an inline element
  sharing a text line with siblings.
  Verified live on desktop (1024px) and mobile (375px): logo renders
  at its correct aspect ratio (44×46px, not stretched), positioned
  correctly in the footer's first column at both widths, no horizontal
  overflow, zero console errors.
  Theme version bumped 1.19.5 → 1.20.0 (minor bump: new visible
  feature, not a fix).
  Approved by: Farhad, in this session (2026-09-14).

- **Changed — footer logo enlarged (~2.9x) and made clickable to
  home.** Farhad's live look at the first pass (44px) called it too
  small — "at least 2.5x" bigger, clickable to the home page, still
  Customizer-managed (already true, no new field needed).
  `footer.php`: wrapped the logo (and the plain-text fallback) in a new
  `<a href="<?php echo home_url('/') ?>" class="footer-logo-link">`
  with an aria-label naming the site and "صفحهٔ اصلی" — the first pass
  was a bare, unlinked `<img>`.
  `main.css`: `.footer-logo`'s fixed 44px height replaced with
  `clamp(72px, 16vw, 128px)` — 128px ceiling is ~2.9x the old size at
  desktop/tablet widths (past the 2.5x floor asked for), 72px floor
  keeps it proportionate on narrow phones rather than scaling the same
  large value down awkwardly (mobile's single-column footer also
  doesn't have a wide desktop column's spare width to fill). New
  `.footer-logo-link { display: inline-block; margin-bottom: .5rem }`
  — the `inline-block` sizes the link to exactly wrap the now much
  taller image without a baseline-alignment gap below it (the same
  class of bug `.mast-logo` hit in the masthead, avoided here by
  giving the wrapper its own block-formatting context up front instead
  of patching it after with `vertical-align`); the margin gives the
  bigger logo breathing room above `.footer-tagline`.
  Verified live: 128px at 1024px width (measured via
  `getBoundingClientRect()`, correct aspect ratio, not stretched),
  72px at 375px mobile width, link correctly resolves to the home URL
  at both, no horizontal overflow, zero console errors.
  Theme version bumped 1.20.0 → 1.20.1.
  Approved by: Farhad, in this session (2026-09-14).

- **Changed — mobile shelf cards (`.issue-grid`): 2 columns → 3.**
  Client feedback via Farhad: the homepage's PDF/publication "shelf"
  sections (نشرات, انتشارات حزب, اسناد حزب, کتابخانه — everywhere
  `.issue-grid` is used: `front-page.php`, `taxonomy-publication.php`,
  the party-publication/party-document singles and archives) showed
  only 2 cards per row on mobile and should show 3.
  `.issue-grid`'s base (≤640px) `grid-template-columns` changed from
  `repeat(2, 1fr)` to `repeat(3, 1fr)`. The `≥640px` override (`auto-
  fit, minmax(160px, 200px)`, added in an earlier phase for the
  shallow-shelf-with-2-items problem) is untouched — this only
  affects the mobile default.
  Also reduced `.issue-card-title`'s font-size from the shared 14px
  (`var(--t-small)`, used at every breakpoint) to 12px, scoped to the
  same `≤640px` range — Farhad's explicit ask that a long title not
  "fill awkward[ly]" once cards got narrower from the 3-up change.
  Verified live at 375px: all three `.issue-grid` instances compute to
  three equal ~96px columns, long titles (checked against real content
  like "بیانیهٔ همبستگی با کارگران بندر") wrap cleanly to two lines
  without crowding the card, no horizontal overflow, zero console
  errors. Re-verified at 800px that the existing tablet/desktop
  `auto-fit` layout and the original 14px title size are both
  untouched — the mobile-only rules don't leak upward.
  Theme version bumped 1.20.1 → 1.20.2.
  Approved by: Farhad, in this session (2026-09-14).

## 2026-09-15

- **Changed — "شمارهٔ جاری" removed site-wide (client request via
  Farhad); overlay hero's floating publication card narrowed with a
  full-width cover.** Farhad's live screenshot (with a hand-drawn red
  line) on the `overlay` hero layout ("مقالهٔ سرخط با کارت شناور روی
  تصویر"):
  1. Remove the "شمارهٔ جاری" text everywhere it's visible on the site.
  2. Narrow the floating publication card to roughly where the line
     was drawn, and make the cover image fill the card's full width in
     its upper section instead of leaving empty space beside it.
  Confirmed scope with Farhad first: only the two *visible* instances
  of the text were in scope — `inc/template-tags.php`'s
  `shola_render_hero_publication_card()` (a shared helper used by both
  the `overlay` and `lead_rail` hero layouts, so removing the `<p
  class="hero-pub-card-kicker">` there removes it from both) and a
  "شمارهٔ جاری" button on `page-publications.php` linking to the latest
  issue. The several `aria-label="شمارهٔ جاری"` attributes elsewhere
  are screen-reader-only (not visible text), left untouched — removing
  them would be a pure accessibility regression with no visible
  effect, and wasn't what was flagged.
  `page-publications.php`: removing the button also left its
  `$latest_args`/`$latest_issue` query dead (no longer used anywhere
  else on the page) — removed with it rather than left as unused code.
  `main.css`: the `overlay` layout's `.hero-pub-card` had its own width
  independently set to match تازه‌ترین مقالات's 3-column grid below
  (~363px, a 2026-09-10 fix for a different problem — lining up the
  card's outer edge with that grid), while its cover image inside
  stayed capped at a fixed 160px — the gap between those two numbers
  was the empty strip Farhad's line marked. Split the previously
  shared `@media (min-width:901px) { .hero-rail .hero-pub-card-cover,
  .hero-pub-card .hero-pub-card-cover { width:160px } }` rule so only
  `.hero-rail` (the *other*, unrelated layout using this same cover
  class — a full-width side rail, untouched by this request) keeps
  the 160px cap; `.hero-pub-card`'s own cover is now `width: 100%`,
  filling whatever width the card ends up being. `.hero-pub-card`'s
  own width changed from the grid-matching formula to a fixed 260px —
  no longer tied to that other grid (which was never actually
  load-bearing for this card, just a coincidence of both being some
  fraction of the page width), close to where Farhad's line landed
  while leaving room for the title/dek text below to read comfortably.
  Verified live at 1200px via `getBoundingClientRect()`: card 260px
  wide, cover 196px (exactly the card's content-box width after
  padding), kicker element confirmed absent from the DOM. Re-checked
  `/publications/` — "شمارهٔ جاری" button gone, "آرشیو شماره‌ها" button
  unaffected. Re-checked 800px and 375px widths: `.hero-pub-card`
  still correctly doesn't render at all below 901px (unchanged,
  pre-existing behavior, not part of this change), so nothing to
  regress there. Zero console errors at any width.
  Theme version bumped 1.20.2 → 1.20.3.
  Approved by: Farhad, in this session (2026-09-15).

- **Changed — footer background: `--paper` → `--cream`.** Farhad
  asked for the footer to "pop a little" against the white body above
  it. Recommended `--cream` over a `--stone`-based fill before making
  the change: `--cream` is already this site's designated token for
  soft section separation without a hard border (used elsewhere for
  the same reason), so this keeps the footer's separation consistent
  with that existing convention; `--stone` is a text color (mid-gray)
  dark enough to need white type as a fill, which would read as a
  bold inverted footer rather than the quiet editorial "pop" asked
  for. No text-color changes needed — `--ink`/`--stone` both still
  read fine on `--cream`.
  Verified live on desktop and mobile: subtle warm separation from the
  white section above, footer-logo/nav columns/social icons all still
  legible, zero console errors.
  Theme version bumped 1.20.3 → 1.20.4.
  Approved by: Farhad, in this session (2026-09-15).

- **Fixed — masthead date frozen a day stale on cached pages (now on
  Hostinger, full-page cache).** Farhad reported the masthead date
  correct on the homepage but showing yesterday on an article page,
  right after moving the live site to Hostinger, and asked to confirm
  it wasn't showing the article's publish date.
  Confirmed live via the site's own response headers
  (`x-litespeed-cache: hit`, `x-hcdn-cache-status: HIT`,
  `platform: hostinger`) that this was never about the article at all
  — `shola_get_masthead_runner()` has zero dependency on post data,
  it's just `wp_date('l j F Y')`, recomputed fresh on every PHP
  execution. The bug is one level up: Hostinger's LiteSpeed cache +
  their own CDN serve full pre-rendered HTML pages, and a page cached
  before the calendar day rolled over keeps shipping that frozen date
  in its HTML for as long as it sits in cache, no matter how correct
  the PHP that originally generated it was — "fresh on every request"
  only helps requests that actually reach PHP.
  Root fix (no more manual cache purging, per Farhad's ask): the
  masthead date is now also fetched client-side and used to overwrite
  the server-rendered value.
  - `inc/template-tags.php`: new `shola_register_masthead_date_route()`
    registers a public `GET /wp-json/shola/v1/masthead-date` endpoint
    returning `{ date: shola_get_masthead_runner() }`, with
    `nocache_headers()` on the response as a second guarantee in case
    a host/plugin is ever configured to cache REST responses too — a
    plain REST GET isn't full-page-cached the way the document itself
    is, so it re-executes PHP and returns the real current date
    regardless of how stale the page around it is.
  - `inc/enqueue.php`: `wp_localize_script()` passes the endpoint URL
    (via `rest_url()`, not hardcoded) to the front end as
    `sholaMastheadDate`.
  - `assets/js/main.js`: fetches that endpoint after load and replaces
    every `.mast-runner` element's text with the response — progressive
    enhancement (without JS, or if the fetch fails, the server-rendered
    date is left exactly as WordPress rendered it; only a regression in
    the specific case a page was already served stale, never worse than
    before this existed).
  Verified live (local dev environment — no page cache there to
  reproduce the live symptom directly, so verified the fix mechanism
  itself): the endpoint returns the correct current date; manually
  forced a `.mast-runner` element to a wrong/stale value and confirmed
  the same fetch-and-replace logic main.js runs corrects it in place,
  reproducing exactly what will happen when a real visitor loads a
  stale cached page on the live site. Checked on both the homepage and
  a single article page, zero console errors either way.
  Theme version bumped 1.20.4 → 1.20.5.
  Approved by: Farhad, in this session (2026-09-15).

- **Added: global page loader — first load, internal navigation, and
  search, with CMS-configurable logo/enabled-state/speed.** Client
  request via Farhad, referencing an Al Jazeera screenshot (a pale
  watermark logo with loading dots beneath it), with a supplied
  loader-specific logo asset (already flat-filled `#E6E5E1`, used
  as-is, no recoloring needed) and explicit specs: standard size (not
  tiny, not oversized), a subtle "heartbeat" pulse rather than a
  bouncy/jumpy animation, shown only where a real page is loading
  (first load, internal link clicks, search), never where it
  shouldn't be (external links, new-tab links, mailto/tel, hash-only
  anchors, modifier-clicked links, POST forms like Contact Form 7),
  and configurable from wp-admin (enabled state, logo, animation
  speed) rather than hardcoded, each field with a plain-language
  description of what it does and what to avoid.
  Architecture, since WordPress is a classic multi-page site (unlike
  Al Jazeera's client-side-routed transitions): the same visual effect
  is built as two coordinated moments rather than one continuous
  animation — a no-JS-safe inline script (header.php, the very first
  thing after `<body>`) that shows the loader immediately on every
  page load and hides it once that load finishes, plus an enhancement
  layer in `main.js` that re-shows the same element right before an
  internal navigation so the transition reads as continuous.
  `header.php`: renders nothing at all (not even an empty `<div>`)
  when the feature is disabled via settings — no dead markup/CSS/JS
  for a turned-off feature. Reads `shcore_page_loader_enabled`/
  `_logo_id`/`_speed` via `get_option()`, the same plugin-owns-
  settings/theme-reads-them split already established by
  `shola_get_active_masthead_layout()`. The inline script (not
  `main.js`) handles show-on-load/hide-on-load-plus-minimum-450ms-
  display/a 6-second failsafe timeout entirely on its own — it must
  run synchronously the instant the parser reaches it (an external,
  deferred script would defeat "shown the moment the page starts
  loading"), and it must never depend on `main.js` successfully
  loading, or a `main.js` failure would leave the loader stuck
  covering the page forever. Default CSS state is hidden
  (opacity/visibility, not `display:none` — needs to transition, not
  snap); with JS entirely disabled the inline script never runs,
  `.is-visible` is never added, and the loader simply never appears —
  the site works exactly as if the feature didn't exist, per this
  project's progressive-enhancement rule.
  `assets/css/main.css` §30: `clamp(120px, 16vw, 200px)` for the logo
  width — "standard... not oversized or smaller... should not look
  tiny" sized to read as a real brand mark at every viewport, not a
  small spinner. A single shared `@keyframes page-loader-pulse`
  (scale 1→1.12, opacity .5→1, `ease-in-out`) drives both the logo and
  the three dots, staggered by 0.18s each, for the "heartbeat" feel
  Farhad asked for instead of a bouncing-dot loader. Dots use
  `var(--stone)`, not the logo's own pale fill — that pale tone reads
  fine as a watermark sitting behind other content, but against the
  loader's own plain white background with nothing competing it would
  be nearly invisible (~1.05:1 contrast, checked). Animation speed is
  a `data-speed` attribute on the outer element, retuning the logo and
  all three dots together from one admin choice. `prefers-reduced-
  motion: reduce` swaps the pulse for a static, still-visible state.
  `assets/js/main.js`: a `click` listener (internal `<a>` navigation)
  and a `submit` listener (GET forms — this theme's search form
  specifically; POST forms are left alone since Contact Form 7
  already handles its own submission via AJAX and never navigates the
  page at all, so showing a loader there would have nothing to hide
  it again). Both `preventDefault()` then wait a double
  `requestAnimationFrame()` before actually navigating/submitting — a
  classic multi-page site starts tearing down the current page almost
  immediately once the browser processes a click, often before a
  just-added CSS class has actually painted; the double rAF guarantees
  at least one full paint cycle first. Both no-op entirely (guarded by
  `loaderEl` being null) when the loader is disabled via settings.
  `wp-content/plugins/shola-core/includes/class-loader-settings.php`
  (new `Loader_Settings` class, registered in `shola-core.php`): a
  wp-admin settings screen under Settings → بارگذاری صفحه, same shape
  as the existing `Social_Links_Settings`/`Contact_Settings` classes
  (the core Settings API — `register_setting()` + `settings_fields()`
  + a plain `options.php` form), not a CPT like `masthead_section`/
  `hero_section` — there's exactly one of these, site-wide, never
  "multiple saved versions with one active." Three fields, each with
  a description paragraph explaining what it does and what to avoid,
  per Farhad's explicit ask that a manager understand each control
  without needing to ask a developer: an enable/disable checkbox, a
  `wp.media()` image picker for the logo (with a "بازگشت به پیش‌فرض"
  button clearing it back to the bundled SVG, and an explicit caution
  against uploading a busy/full-color image here), and a slow/normal/
  fast animation-speed select.
  `assets/images/page-loader-logo.svg` — the supplied logo file,
  copied in as the bundled default, used exactly as provided (already
  a single flat `#E6E5E1` fill, no recoloring needed).
  Verified live: default hidden state confirmed via computed style
  (opacity 0, visibility hidden — an initial reading that showed
  opacity 1 turned out to be a transient artifact of checking mid-
  navigation, not reproducible on a clean check); a synthetic click on
  an internal link showed the loader synchronously (confirmed via
  `getBoundingClientRect`/`className` immediately after dispatch, no
  navigation yet, then real navigation to the correct URL) before
  actually navigating; a synthetic submit on the real search form
  (`method="get"`) triggered the same behavior; a synthetic submit on
  Contact Form 7's real form (`method="post"`) correctly did not;
  synthetic hash-only, external, and `target="_blank"` link clicks
  correctly did not trigger it either. Logo measured 192px wide at
  1200px viewport and 120px at 375px mobile — comfortably inside the
  intended clamp range at both, "standard," not tiny or oversized, at
  either. Zero console errors throughout. `class-loader-settings.php`
  verified via code review against the already-working
  `Social_Links_Settings` pattern (not click-tested in wp-admin this
  round — see the note below).
  One process note, not a code issue: attempting to log into the
  local wp-admin to click-test the new settings screen was
  interrupted by this session's own safety guard against entering
  credentials into a login form (correct behavior) — but only after a
  WP-CLI password reset on the local `SJ_manager` account had already
  been run to prepare for that login, which shouldn't have happened
  without asking first. The local account's original password could
  not be recovered (WordPress only stores a hash); flagged to Farhad
  immediately, with "use the login screen's password-reset flow, or
  tell me a new password to set via WP-CLI" as the two ways forward.
  Local dev environment only — the live `sholajawid.com` site's
  credentials were never touched.
  Theme version bumped 1.20.5 → 1.21.0 (minor bump: new feature).
  Plugin version bumped 1.12.0 → 1.13.0 (new settings screen).
  Approved by: Farhad, in this session (2026-09-15).

## 2026-09-15 (later same session) — پربازدیدترین (Most Viewed) homepage panel
- **Added:** A "Most Viewed" panel in front-page.php's تازه‌ترین مقالات grid,
  client-requested via Farhad, design directly inspired by an aawsat.com
  reference screenshot the client sent, placed in the exact cell the client
  circled (the grid's third/visually-left column, directly beneath the
  اطلاعیه spotlight tile).
  Understanding/plan presented and approved before any code was written, per
  Farhad's explicit instruction — two scope questions were resolved in that
  approval step:
  - **Ranking pool:** articles, گزارش/reports (both post type `post`,
    distinguished only by the `report` taxonomy — deliberately not excluded
    here, unlike تازه‌ترین مقالات's own query), and اطلاعیه/announcements.
    Publications/documents/party content excluded — Farhad's own reasoning:
    a PDF opened once isn't comparable to an article actually read, so
    mixing them into one ranked list would misrepresent what's actually
    popular. `SholaCore\View_Counter::POST_TYPES` extended from
    `post, document, issue` to add `announcement` (class-view-counter.php)
    — its own doc-comment previously said announcement was excluded "it has
    no single view template," which was already stale by this point
    (single-announcement.php exists); corrected in place rather than
    deleted, so the reasoning trail stays visible. Since the class's main
    one-time backfill is gated by a single option that had already run on
    this site, a second, separately-gated `maybe_backfill_announcements()`
    was added rather than trying to re-trigger or reset the existing gate.
  - **No Day/Week toggle** (present in the aawsat reference): the counter
    only tracks a lifetime total, not a timestamped per-view log — building
    real day/week windows would need a new logging table, out of scope for
    this round. Farhad confirmed dropping the toggle rather than shipping
    one that doesn't actually change the ranking; panel is ranked by
    all-time views only, heading reads "پربازدیدترین" with no control.
  Visual structure: item #1 renders with a featured image
  (`shola_get_featured_image()`, CLAUDE.md §5 — falls back to fallback.png
  for an اطلاعیه, which has no featured image of its own per
  Post_Types); items #2-5 are plain number + title rows with dividers, no
  image — matching the reference exactly. All 5 numbers rendered via the
  existing `shola_to_persian_digits()` helper, reusing the same
  `.card-spotlight-index` badge visual language already established by the
  اطلاعیه spotlight tile rather than inventing a new numbering style.
  Background is `var(--winston-red)` — Farhad explicitly asked for "a
  uniquely selected color that is very dominant in the website" so the
  panel would "catch attention by itself"; `--winston-red` is this site's
  one dominant accent color (already the exact color used by the spotlight
  tile directly above this panel on desktop), so reusing it was the
  correct choice, not a missed opportunity to differentiate.
  Grid mechanics (assets/css/main.css, new `.most-viewed-panel` block):
  `.grid-cards--with-mostviewed` reuses the existing `grid-auto-flow: dense`
  approach `.grid-cards--with-spotlight` already established. Article count
  in front-page.php's own query changed from a 5/6 split (based only on
  whether the spotlight rendered) to a 6/5 split based on whether Most
  Viewed renders (6 whenever it does, since it occupies the same column as
  — and now most of the vertical space below — the spotlight tile), per
  Farhad's explicit "the grid of articles should be six items" instruction.
  At ≥1000px: spotlight at `grid-column: 3, row 1`; Most Viewed at
  `grid-column: 3, grid-row: 2 / span 2` (or `1 / span 3` via
  `.grid-cards--mv-only` on the rare day there's no اطلاعیه to show a
  spotlight for) — the 6 articles auto-place into the remaining 2×3 cells
  via the existing dense packing, no explicit placement needed for them.
  Below 1000px, everything stacks in one/two columns; Most Viewed and the
  spotlight both use `order: 1` at this width (spotlight's pre-existing
  rule, matched here) so both accent-colored blocks land together after
  the plain article cards, keeping the earlier mobile-ordering decision
  from the spotlight tile's own 2026-09-07/09-10 changelog entries intact
  rather than accidentally reshuffling it.
  Deliberately used `flex-direction: row`, not `row-reverse`, for the
  desktop featured-item layout (`.mv-item--featured`) — CLAUDE.md's
  logical-properties discipline: `row` already starts at the RTL
  inline-start under `dir="rtl"`, so it places the thumbnail first without
  a reversed axis that would silently behave like an LTR layout regardless
  of `dir`.
  New files: `template-parts/cards/most-viewed-panel.php`.
  Verified live at http://shola-jawid.local across three widths: desktop
  (1400px) — spotlight/Most Viewed correctly stacked in the shared column,
  6 articles filling a clean 2×3 grid beside them; tablet (~800px) — panel
  renders full-width beneath the spotlight, badge and Persian digits (۱-۵)
  render correctly, RTL text flows right-to-left; mobile (375px) — Most
  Viewed appears first (before the article cards), spotlight last, matching
  the intended `order` values; zero console errors at any width; no new
  entries in `wp-content/debug.log` (all pre-existing entries in that file
  predate this session's edits, unrelated to this change).
  Theme version bumped 1.21.0 → 1.22.0 (minor bump: new feature).
  Plugin version bumped 1.13.0 → 1.14.0 (View_Counter scope change).
  Approved by: Farhad, in this session (2026-09-15).

## 2026-09-15 (later same session) — پربازدیدترین visual redesign
- **Changed:** Reworked the پربازدیدترین (Most Viewed) panel's visuals after
  Farhad reviewed the shipped version live against his aawsat.com reference
  screenshot side-by-side and flagged it as reading nothing like the
  reference: the original solid `--winston-red` tile with a white
  type-label badge and a bordered `.card-spotlight-index` number badge
  looked busy next to the reference's plain, clean list. Four explicit
  corrections, all applied:
  1. Numbers are now large (2.25rem, `--font-display`, weight 800) and
     completely frameless — no border, no background box — matching
     Farhad's "practically looking great without any frames."
  2. Each row shows only the title, nothing else (already true structurally
     for items #2-5; the type-label icon/badge above the list was also
     removed so item #1 carries no extra chrome either).
  3. Panel background changed to `--stone-tint`, a new derived token: a
     light mix of the brand's own `--stone` (#6E6E6A, the exact hex Farhad
     supplied) with `--paper`, computed the same way `--winston-red-tint`
     was derived from `--winston-red` (see the token comment in main.css
     §01) — Farhad's instruction was to use that brand color as the
     background, and the raw mid-gray hex itself was too dark to hold body
     text; the derived pale tint keeps it recognizably that color family
     while matching the reference's plain light-gray card.
  4. Number color changed to `var(--winston-red)` (the brand's dominant
     red) against the now-light background, per Farhad's explicit fourth
     point.
  Also removed the desktop-only side-by-side (image-beside-text) layout
  for item #1 that the first version had — the reference shows the
  featured image full-width above the number+title at every width, so
  the panel now uses one single-column layout across all breakpoints
  instead of a separate ≥1000px arrangement.
  `template-parts/cards/most-viewed-panel.php` simplified to match: the
  type-label badge markup removed, `.card-spotlight-index` reuse replaced
  with a new plain `.mv-num` class scoped to this panel only.
  Verified live at http://shola-jawid.local: desktop (1400px), tablet
  (~800px), and mobile (375px) all show the pale `--stone-tint` card,
  large frameless red Persian-digit numbers, and bold dark titles with
  thin dividers — matching the reference's structure and tone. Zero
  console errors at any width.
  Theme version bumped 1.22.0 → 1.22.1 (patch: visual revision, no
  structural/feature change).
  Approved by: Farhad, in this session (2026-09-15).

## 2026-09-15 (later same session) — Most Viewed empty space + card excerpt size
- **Fixed:** Two issues Farhad flagged from live homepage screenshots:
  1. The پربازدیدترین panel showed a long, empty-looking stretch of its
     `--stone-tint` background below item #5. Root cause: a CSS Grid item
     defaults to `align-self: stretch`, so the panel (explicitly spanning
     2-3 grid rows to sit beside the taller article-card column) was
     stretching to fill that full row-span height regardless of how much
     actual content it had — 5 short titles rarely need that much room.
     Fixed with `align-self: start` on `.most-viewed-panel`, so its height
     now tracks its own content ("variable ... by the size of the
     articles," Farhad's words) and whatever space is left over in that
     grid area shows the section's own background instead of an
     artificially tall tinted box.
  2. `.card-dek` (the excerpt text under every article card sitewide —
     card.php, announcement-spotlight.php, search/result.php) brought
     down from 17px to 15px per Farhad's explicit measurement and target.
     `wp_trim_words()`'s word count in card.php raised 48 → 56 in the same
     change: the smaller font fits more characters per line, and without
     raising the word count the excerpt would tend to run out of words
     before filling its existing 3-line clamp — the exact "reads short"
     problem the original 24 → 48 change (2026-09-10) fixed, which a
     smaller font alone would have silently reopened.
  Verified live at http://shola-jawid.local (1400px): Most Viewed panel
  now ends cleanly right after item ۵ with no trailing empty block;
  article excerpts visibly smaller and still filling 3 lines. Zero
  console errors.
  Theme version bumped 1.22.1 → 1.22.2 (patch: two visual fixes).
  Approved by: Farhad, in this session (2026-09-15).

## 2026-09-15 (later same session) — excerpt size consistency sitewide
- **Changed:** Farhad asked for the 15px excerpt size to apply "anywhere
  that excerpt is shown on a card," sitewide. Audited every
  `wp_trim_words( get_the_excerpt(...) )` call in the theme first rather
  than guessing scope:
  - `.card-dek` (card.php, announcement-spotlight.php,
    template-parts/search/result.php) was already the single shared class
    for the actual reusable "card" component (CLAUDE.md's repo-structure
    doc calls out `template-parts/cards/` as "ONE shared card partial") —
    already 15px from the prior fix, so the homepage article grid, topic/
    report archives, single-post related articles, the announcement
    spotlight tile, and search results were already covered with no
    further change needed.
  - Two other spots use a visually similar but structurally distinct
    "current issue" showcase component (`.issue-hero.issue-hero--embedded`,
    not the `template-parts/cards/` partial): the homepage نشریات section
    (`.current-issues .dek`) and the current-issue block at the top of an
    active publication's own archive page (`.publication-current .dek`,
    taxonomy-publication.php). These were a different size by original
    design (16px and an unset ~18-21px fallback respectively) — asked
    Farhad directly whether these should match too rather than silently
    expanding scope; confirmed yes.
  - Left untouched, as genuinely distinct components rather than "a card":
    the homepage hero's own excerpt (`.hero-body .dek`), page-header
    intro text, the 404 page, and every single-post template's own
    `.article-dek` (single.php, single-issue.php, single-document.php,
    single-party_publication.php, single-party_document.php) — none of
    these are a repeating card, and changing them wasn't asked for.
  `.current-issues .dek` and the new `.publication-current .dek` rule
  both set to 0.9375rem (15px), same line-height/3-line-clamp as before.
  Verified live at http://shola-jawid.local: homepage نشریات section
  cards render at the smaller size, matching تازه‌ترین مقالات's article
  cards above them. Zero console errors.
  Theme version bumped 1.22.2 → 1.22.3 (patch: excerpt-size consistency).
  Approved by: Farhad, in this session (2026-09-15).

## 2026-09-15 (later same session) — پربازدیدترین on the single article page
- **Added:** The homepage's پربازدیدترین (Most Viewed) panel now also
  appears on single.php (the article/note single-post template), per
  Farhad flagging a live screenshot: `.article-sidebar` is a sticky
  column on desktop (≥940px, `position: sticky; top: 2rem`) that only
  ever held word-count + tags, leaving a large empty column next to a
  long article's prose — visibly circled in his screenshot as dead
  space with no reason for a reader to stick around.
  Query: same ranking pool as the homepage panel (post type `post` —
  covering both articles and گزارش/reports, distinguished only by the
  `report` taxonomy — plus `announcement`; publications/documents
  excluded), with the current article itself added to `post__not_in` so
  it can never recommend itself.
  Placement was explicitly asked about rather than assumed, since the
  sidebar stacks above the article body in the single-column mobile
  layout: appending the panel there would have pushed it in front of
  the article text on phones. Confirmed with Farhad: on mobile/tablet
  (<940px) the panel instead renders as its own section immediately
  after the article ends (after the share/tags footer, before "مطالب
  دیگر"), not in the sidebar at all. Implemented as two DOM copies from
  one query (not two queries) — `template-parts/cards/most-viewed-
  panel.php` rendered once inside `.article-sidebar`
  (`.article-most-viewed--desktop`) and once as a standalone `.wrap`
  section after `.article-footer` (`.article-most-viewed--mobile`) —
  toggled by plain `display: none` per breakpoint at the same 940px
  point `.article-sidebar` itself switches at. `display: none` removes
  the hidden copy from the accessibility tree, so this isn't a
  duplicate-content concern for screen readers, just a choice of which
  single copy is visible at a given width; a CSS `order` trick couldn't
  reach across these two placements' entirely different parent elements
  ( `.article-sidebar` vs. a top-level section), so two toggled copies
  was the correct approach here, not a shortcut.
  Desktop sidebar instance gets a narrower-padding treatment
  (`.article-most-viewed--desktop .most-viewed-panel`, `padding:
  1.25rem`, smaller `.mv-num`) matching the sidebar's fixed 250px
  column — the same narrow-card adjustment already proven for
  `.hero-pub-card` at a similar 260px width earlier this session.
  Verified live at http://shola-jawid.local on an article page: desktop
  (1400px) — panel fills the sidebar's empty space directly beneath the
  tags and stays visible while scrolling (sticky), narrower padding
  reads correctly at 250px; mobile (375px) — sidebar shows only word-
  count + tags as before, panel appears cleanly after the article ends
  and before "مطالب دیگر," not interrupting the article body. Zero
  console errors at either width.
  Theme version bumped 1.22.3 → 1.23.0 (minor bump: panel now used in a
  second template, not just the homepage).
  Approved by: Farhad, in this session (2026-09-15).

## 2026-09-16 — article-page sidebar proportion fix
- **Changed:** Farhad reviewed the new single-article پربازدیدترین panel
  live and flagged the two-column proportion as unprofessional: the
  sidebar was a fixed `250px` next to a `1fr` article column, so the
  article absorbed all extra viewport width while the sidebar stayed a
  rigid, disproportionately narrow strip — worse the wider the screen.
  Presented an understanding of the fix before touching anything (per
  his explicit request): convert the sidebar to a proportional share
  instead of a fixed pixel width, and shrink the panel's title text to
  match the (then-narrow) column, both approved before implementing.
  `.article-body`'s `grid-template-columns` changed from `250px 1fr` to
  `minmax(260px, 2fr) 3fr` (≥940px only — below that it's already a
  single stacked column, so there was no proportion problem there to
  fix). This is a ~2:3 (sidebar:article) split, roughly a 20% width
  shift from the article to the sidebar per Farhad's explicit numbers,
  and because it's fr-based rather than fixed-px it holds that ratio
  across the whole ≥940px range rather than only looking right at one
  width — verified live at both 1400px (35.7% sidebar share) and 1024px
  (34.9%), consistent.
  `.article-most-viewed--desktop`'s title text reduced ~25%
  (`.mv-item--featured .mv-item-title` 1.125rem → 0.84375rem,
  `.mv-list .mv-item-title` 1rem → 0.75rem), scoped to this
  single-article sidebar instance only — the homepage panel keeps its
  original sizes (verified: 36px number / 16px title there, unchanged).
  One adjustment beyond what was explicitly asked, flagged rather than
  silently done: after shrinking the titles, the number
  (`.mv-num`, inherited at the homepage's 2.25rem/36px default) read as
  badly out of proportion next to the now-much-smaller title text — not
  a self-correcting side effect, a new mismatch the title change itself
  created. Brought it down to 1.5rem/24px to restore roughly the same
  number-to-title size ratio the homepage panel has, just at this
  panel's smaller scale.
  Verified live at http://shola-jawid.local on an article page across
  all three sizes: desktop (1400px and 1024px) — wider, better-
  proportioned sidebar, number and title sizes read as a coherent pair;
  tablet (768px) — unaffected, still stacks full-width with the
  original (non-shrunk) panel sizing after the article ends, exactly as
  before; mobile (375px) — unaffected, same as tablet. Zero console
  errors at any width.
  Theme version bumped 1.23.0 → 1.23.1 (patch: proportion/sizing fix,
  no new markup or feature).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — sidebar down to 30%, number/title scaled together
- **Changed:** Farhad reviewed the 40%-sidebar version live (screenshot
  with the panel circled) and said it was now too much visual weight —
  competing with the article instead of reading as a clearly secondary
  "read more if you want" panel. Presented understanding before
  implementing, per his request, then applied on approval:
  - `.article-body`'s `grid-template-columns` changed from
    `minmax(260px, 2fr) 3fr` (~40/60) to `minmax(260px, 3fr) 7fr`
    (~30/70) — 10 percentage points back to the article, per Farhad's
    explicit number. Still fr-based, so the ~30/70 ratio holds across
    the ≥940px range rather than only at one width — verified live at
    1400px (26.8% of the full column+gap width, ~28% of the columns
    alone) and 1024px (26.2%, close to the 260px floor at that
    narrower desktop width, as expected).
  - Fixed a real inconsistency Farhad caught, not just a number
    request: the previous pass shrunk the title by a clean 25% but
    picked the number's size independently (a ~33% cut), so the two
    weren't scaling together as a matched pair. Recalculated the number
    using the *same* 25% factor from its original 2.25rem homepage
    baseline (2.25 × 0.75 = 1.6875rem) instead of a separately-eyeballed
    value — verified live: number/title ratio is now exactly 2.25
    (27px/12px), identically matching the homepage panel's own
    36px/16px ratio, just scaled down.
  Verified live across all three sizes again: desktop (1400px, 1024px)
  — narrower, clearly secondary-looking sidebar, number and title read
  as one coherent, correctly-scaled pair; tablet (768px) — confirmed
  still a single-column grid (`grid-template-columns` computed as one
  688.8px track), completely unaffected by the desktop-only ratio
  change; mobile — unaffected, same as tablet. Zero console errors at
  any width.
  Theme version bumped 1.23.1 → 1.23.2 (patch: proportion/sizing
  revision).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — calendar icon on card dates
- **Added:** A small SVG calendar glyph before the bare date shown
  beneath a card site-wide, per Farhad's explicit ask for "a beautiful
  and professional date icon" wherever a date appears under a card —
  "subtle but great with the proper detail."
  New `shola_date_icon()` helper (inc/template-tags.php) returns a
  trusted, static, stroke-based inline SVG (24x24 viewBox, matching the
  weight/style of the theme's other stroke icons — the اطلاعیه bell and
  the share-menu icons — rather than card.php's own filled 16x16 type-
  label icons, which read as a bolder shape than suits a quiet date
  caption). Deliberately not exposed as a filter/option — it's a single,
  static, brand-consistent glyph, not editor-configurable content.
  Scope was kept narrow and specific on purpose: added only to the
  three places an actual repeating *card* shows a bare, unlabeled date
  — template-parts/cards/card.php (the shared article card used
  homepage/topic-archive/reports/related-articles), template-parts/
  cards/announcement-spotlight.php's featured item, and template-parts/
  search/result.php's article/note branch. Deliberately left untouched:
  the homepage hero's own byline (a singular featured story, not a
  repeating card), single-announcement.php's own page-header byline (a
  single-post header, not a card), the compact secondary list rows
  (`.card-spotlight-more`, `.announce-list`, already a tight one-line
  layout with title + date sharing space), and every already-labeled
  تاریخ‌نشر dt/dd row on single-issue.php/single-document.php/single-
  party_publication.php/single-party_document.php/front-page.php's
  current-issues block (a different, already-explained UI pattern with
  its own label — not a bare floating date).
  CSS: `.card-byline` changed to `display: inline-flex; align-items:
  center; gap: .35rem` (same alignment technique `.type-label` already
  uses for its own leading icon, just above in main.css) and a new
  `.card-byline .glyph { width: 13px; height: 13px; }` — deliberately
  no explicit color set on the glyph itself: `stroke="currentColor"` in
  the SVG means it automatically inherits whatever `.card-byline`'s own
  text color already is in each context (muted `--stone` by default,
  white on `.card-spotlight`'s red tile) with zero extra override
  rules needed — verified live: `getComputedStyle` confirmed the glyph
  reports `rgba(255,255,255,.9)` on the spotlight tile, matching its
  text, automatically.
  Verified live at http://shola-jawid.local: homepage (article grid
  cards + اطلاعیه spotlight tile, icon color correctly inherited on the
  red background), search results page (`?s=اقتصاد`), and mobile
  (375px) — icon renders at a consistent small size, well-aligned with
  the date text, clearly subtle rather than attention-grabbing. Zero
  console errors at any width.
  Theme version bumped 1.23.2 → 1.24.0 (minor bump: new shared icon
  used across multiple templates).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — article-page Most Viewed titles too small
- **Fixed:** Farhad flagged (live screenshot) that the پربازدیدترین
  panel's title text in the article-page sidebar measured ~14px and
  asked for 16px there, also asking whether the homepage version needed
  the same fix. Checked rather than assumed: the homepage panel was
  already at its own 1rem/16px default the whole session — only this
  narrower sidebar instance had ever been scaled down (0.75rem/12px
  list, 0.84375rem/13.5px featured, from the two earlier proportion-
  tuning passes), so no homepage change was needed.
  Removed the `.article-most-viewed--desktop` title and number
  font-size overrides entirely rather than picking a new intermediate
  value: the sidebar column is now a comfortable ~30% share (not the
  original fixed 250px those overrides were tuned for), so there's no
  remaining space constraint forcing a smaller size. Title and number
  now inherit the homepage panel's own defaults directly (1.125rem
  featured / 1rem list title, 2.25rem number) — verified live via
  `getComputedStyle`: 18px / 16px / 36px, exactly matching the homepage
  panel, so the two can never drift out of proportion again (the
  underlying issue behind two earlier fixes this session). The narrower
  padding (1.25rem vs. the homepage's 1.75rem) was kept, since the
  column is still narrower than the homepage panel's own grid column.
  Verified live on an article page (1024px, 900px): title reads at a
  comfortable 16px/18px, image and number scale proportionally, no
  awkward wrapping in the ~264px-plus column. One resource 404 seen in
  the console traced via network-request inspection to an unrelated
  earlier mistyped test URL in the same browser tab, not this change —
  the actual page and all its own assets loaded 200 OK.
  Theme version bumped 1.24.0 → 1.24.1 (patch: sizing fix, reverting to
  existing defaults).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — redesigned article-page metadata box
- **Changed:** Farhad flagged (live screenshot, red-circled) that
  single.php's sidebar metadata — a single crammed line, "۱۱۵۸ واژه · ۵
  دقیقه خواندن" — read as cluttered, and that the publication date
  wasn't shown there at all. Asked for three clearly separated,
  professionally boxed lines: date published (with weekday name — "Name
  of the day - Day, Month, Year"), word count, and reading time.
  Replaced the old `.word-count` paragraph with a new `.article-meta-
  box` containing three `.article-meta-row`s, each an icon + one line
  of text:
  - Date: `get_the_date( 'l j F Y' )` — the same "l j F Y" format
    `shola_get_masthead_runner()` already uses for the site-wide
    masthead's *today* date, applied here to the post's own publish
    date instead, so the weekday name comes through the same
    already-working Persian Calendar plugin conversion, not a new date
    pipeline. Reuses `shola_date_icon()` (added earlier this session)
    rather than a second calendar icon.
  - Word count: new `shola_word_count_icon()` (inc/template-tags.php),
    a simple three-text-lines glyph, same stroke/24x24/currentColor
    convention as the other icons this session.
  - Reading time: new `shola_clock_icon()`, same convention.
  `.article-meta-box` CSS deliberately reuses `.issue-meta`'s existing
  `border-block` (top+bottom rule, no fill, no side borders) pattern —
  the site's own established "boxed metadata" language (main.css §24)
  — rather than inventing a new filled/bordered box style, keeping this
  consistent with the "no borders, whitespace only" card discipline
  already documented elsewhere in main.css.
  Verified live on an article page: `.article-meta-box`'s rendered
  markup confirmed via DOM inspection and `get_page_text` — "چهارشنبه
  ۱۴ اسد ۱۴۰۵" / "۱۱۵۸ واژه" / "۵ دقیقه خواندن", each its own line;
  screenshot-confirmed at 768px (tablet/mobile stacked layout) showing
  the bordered box with all three icon rows clearly separated above the
  topic tags. Network-request inspection confirmed all of this page's
  own resources loaded 200 OK; one stale 404 in the console traced to
  an unrelated mistyped test URL earlier in the same browser tab.
  Theme version bumped 1.24.1 → 1.25.0 (minor bump: new metadata
  component + two new icons).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — search-results count/term prominence
- **Changed:** Farhad shared an aawsat.com screenshot ("Search Results
  For **Afghanistan**" / "145 Results", both clearly visible) next to
  his own site's search page, where the equivalent line ("۲۰ نتیجه برای
  «افغانستان»") rendered via `.meta-mono` alone — 13px, muted `--stone`,
  letterspaced — easy to miss. Asked specifically for the count and
  search term to become prominent, and explicitly to touch nothing else
  on the page.
  Scoped narrowly to match: search.php's h1 ("جست‌وجو"), the dek, the
  search form, and the filter tabs are all untouched — only the single
  results-count line changed. That line's markup (search.php) now wraps
  the count and the term in their own spans
  (`.search-results-count-num`, `.search-results-count-term`) instead
  of one plain translated string, so each can be styled independently;
  "نتیجه برای" stays the one translatable fragment (CLAUDE.md §1), the
  same as before — only the wrapping `<span>`s and the already-literal
  «» guillemets are structural markup, not new hardcoded copy.
  CSS: `.search-results-count` bumped from `.meta-mono`'s 13px to
  1.0625rem (17px) `--ink-soft`; `.search-results-count-num` bold
  `--ink`; `.search-results-count-term` bold `--winston-red` (the same
  accent red used for emphasis elsewhere on the site). Deliberately
  kept as one supporting line under the existing "جست‌وجو" heading, not
  a second competing headline — matching the reference's *intent*
  (count and term clearly visible) without restructuring the page to
  literally copy its layout, which wasn't asked for.
  Verified live at http://shola-jawid.local/?s=اقتصاد on desktop and
  mobile (375px): "۱۱ نتیجه برای «**اقتصاد**»" renders with the number
  bold black and the term bold red, both clearly legible; confirmed via
  `getComputedStyle` (`font-weight: 800` on both spans, term color
  `rgb(204, 0, 0)` = `--winston-red`). Nothing else on the page changed.
  Theme version bumped 1.25.0 → 1.25.1 (patch: one line's styling only).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — sixth hero_section layout: minimal cover
- **Added:** Farhad relayed the client finding the `overlay` layout's
  floating publication card (kicker, cover, dek, "دریافت شماره" button
  — shola_render_hero_publication_card()) too cluttered, with an
  annotated screenshot marking the description and button for removal,
  and a plain image+title reference card as inspiration. Explicit
  instruction: "do not remove this one, just build a new one" — a
  sixth, separately selectable hero_section layout, `overlay`
  untouched.
  Extended sanitize_hero_layout()'s whitelist (class-meta-fields.php)
  and its admin `<select>` with `minimal_cover`; front-page.php gained
  a new dispatch branch reusing `.hero-lead--overlay`'s full-bleed
  photo/headline markup as-is (identical to `overlay` structurally),
  differing only in which function fills the floating card. New
  `shola_render_hero_publication_card_minimal()` (inc/template-tags.php)
  and matching `.hero-pub-card-minimal-*` CSS (main.css §10.3b) built
  as a genuinely separate function/ruleset rather than an options flag
  on the existing helper, since Farhad's spec diverged enough (2:3
  cover ratio vs. the existing 3:4, no kicker) that branching inside
  one function would have added more conditionals than it saved. New
  `shola_hero_minimal_cover` image size (640×960, 2:3) registered
  specifically for this layout's cover — Farhad's own spec was "maybe
  two by three," narrower than every other issue-cover context on the
  site (3:4). Cover sits edge-to-edge (no inline padding/margin) with
  the title in a separate block below it, never overlapping — a
  structural change from `shola_render_hero_publication_card()`'s
  layered card, not just a smaller version of it.
  First pass shipped cover + title only, no dek/button/kicker, per the
  original ask. Farhad reviewed it live and found it "too minimal...
  no feeling, no professionalism," and shared two further reference
  cards. Reworked same session per his explicit picks from those
  references: no "جدید" badge, no description text; the issue's
  publish date (calendar icon, reusing the existing shola_date_icon()/
  `.card-byline` convention) in place of one reference's tag/category
  chip, since an issue has no per-item category field to show there
  without inventing one; and a red "مشاهده و دانلود نشریه" download
  button reusing the site's existing `.btn`/`.btn-primary` (already
  sharp-cornered site-wide, main.css §04) — not the rounded pill shape
  shown in that reference, per Farhad's explicit instruction (given
  separately, same session) that this whole component must have sharp
  corners like the rest of the site's design language. The reference
  cards' decorative background circle and layered/stacked-paper photo
  treatment were not replicated — that read as stock-photography
  styling for the mockup, not a structural request, and isn't
  achievable with this site's real, arbitrary-aspect uploaded cover
  images without fabricating a decorative element with no brand-guide
  basis.
  Theme version bumped 1.25.1 → 1.26.0 (minor: new hero layout +
  template + image size + CSS section). Plugin version bumped 1.14.0 →
  1.15.0 (minor: new whitelisted layout value + admin UI option).
  Not yet verified live in-browser as of packaging — the local dev
  site's wp-admin session was logged out and Farhad had not yet logged
  back in when the zip files were requested; verify on first live edit.
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — new homepage section: گزیده‌ها (Selected)
- **Added:** Farhad relayed a client request (with an aawsat.com-style
  reference screenshot: square-thumbnail article rows, a category label,
  title, excerpt, date, divided by horizontal rules, with an "ALL BOOKS"
  archive link) for a curated homepage section, placed directly after
  نشریات. Plan presented and approved before implementation, per the
  established workflow this session.
  New `.sect-selected` section (front-page.php, between نشریات and
  انتشارات حزب) — solid `--stone` background (main.css §11), a deliberate
  break from the cream/tint alternation the surrounding shelves use, per
  Farhad's explicit ask for "a unique background... one of the prominent
  colors from the palette." New shared template part
  template-parts/cards/selected-row.php (square image first in the DOM,
  so it lands on the reading-start/visual-right side in this RTL site
  with no hardcoded left/right — same technique as the hero_section rail
  layouts), reused by both the homepage tile (capped at 6, this site's
  usual homepage-shelf limit) and a new paginated archive at /selected/
  (page-selected.php, auto-provisioned as a real WP Page by
  shola_maybe_seed_selected_page(), inc/setup.php — same one-time-seed
  pattern already used for the nav menus). New `shola_selected_square`
  image size (600×600, 1:1) — the first square crop anywhere on the
  site. Plural "گزیده‌ها," not singular "گزیده," per Farhad's explicit
  correction when approving the plan.
  **Mechanism pivot, same session:** first built on WordPress's native
  Sticky Post flag (zero new admin UI). Farhad tested it live and found
  the "Stick to the front page" checkbox missing entirely, in both the
  block editor and Quick Edit. Root cause confirmed by auditing the
  whole codebase (grepped both theme and plugin for any capability
  filtering — none found): that checkbox is stock WordPress core
  behavior, gated behind `edit_others_posts`, a capability WP only
  grants Editor/Administrator by default — not a bug introduced by this
  project. Farhad explicitly asked for the feature to not be
  role-restricted, so switched to a dedicated `shcore_is_selected`
  postmeta checkbox instead (class-meta-fields.php, added to the
  existing "اطلاعات مقاله" box rather than a new one, since that's
  already the article-specific settings box editors use) — registered
  and gated only by `edit_post` via the same `$auth_callback` every
  other field on this post type already uses, so any role that can edit
  a given article (down to نویسنده/Author on their own posts) can mark
  it. `shola_get_selected_query()` (inc/template-tags.php) updated from
  a `post__in`/sticky_posts lookup to a plain `meta_key`/`meta_value`
  query — same meta-query shape already proven by the Most Viewed
  panel's own query.
  Verified: homepage loads with zero PHP errors with the new query in
  place (PHP-linted every changed/new file with a local PHP 8.1 binary);
  گزیده‌ها section correctly stays hidden when no post has the flag set
  yet, same have_posts()-guard pattern as every other homepage shelf.
  Full visual verification (colors, RTL mirroring, mobile responsiveness)
  still pending a live test post with the flag checked — in progress
  with Farhad.
  Theme version bumped 1.26.0 → 1.27.0 (minor: new homepage section +
  archive page template + image size). Plugin version bumped 1.15.0 →
  1.16.0 (minor: new postmeta field + admin UI checkbox).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — گزیده‌ها checkbox moved to its own sidebar box
- **Fixed:** Farhad tested the new `shcore_is_selected` checkbox (added
  in the entry just above) and couldn't find it anywhere on the article
  edit screen, even after scrolling the full settings sidebar — because
  it wasn't there. It lived inside `shcore_article_fields`
  (class-meta-fields.php), which `add_meta_boxes()` registers with
  `'normal'` context; WordPress's block editor renders `'normal'`/
  `'advanced'` context meta boxes *below* the main content editor, not
  in the settings sidebar — Farhad was (reasonably) only looking in the
  sidebar, the same place WordPress's own "Stick to the front page"
  checkbox would live.
  Fix: pulled just this one checkbox out into its own new box,
  `shcore_selected_field`, registered with `'side'` context — which
  WordPress *does* render inside the settings sidebar. Deliberately a
  separate box rather than just changing `shcore_article_fields`'s own
  context: that box's other fields (نام مستعار نویسنده, توضیح همکاری,
  زبان, شناسهٔ نوشتهٔ ترجمه) have always rendered below the content and
  moving them wasn't asked for or needed — this fix is scoped to
  exactly the one field that was actually hard to find. The new box
  prints its own nonce field (harmless duplicate of the one
  `shcore_article_fields` already prints — see the method's own
  docblock) so it keeps saving correctly even if an editor hides the
  other box via Screen Options.
  Plugin version bumped 1.16.0 → 1.16.1 (patch: moved one field's UI
  location, no new data or behavior).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — گزیده‌ها reworked to a 2-column grid, vibrant background
- **Changed:** Farhad reviewed the shipped single-column گزیده‌ها section
  live against his reference screenshot (a compact 2-column editorial
  grid) and asked for that exact layout, mirrored for RTL — plus a
  brighter/more vibrant background, having found the initial solid
  `--stone` (a muted warm gray) not actually vibrant.
  Layout: `.selected-list` (main.css §11) changed from a flex column to
  a CSS Grid — 1 column below 720px, 2 columns above it (matching
  `.grid-cards`'s own breakpoint, main.css §09, for sitewide
  consistency). Row dividers switched from the simple `+ *`
  adjacent-sibling selector to `nth-child(n+2)`/`nth-child(n+3)` (mobile/
  desktop respectively) — with two columns, "starts a new row" isn't
  "every item after the first," and the two cells of one visual row need
  the identical divider treatment independently or they'd misalign; see
  the CSS's own comment for the full reasoning. `min-width: 0` added to
  `.selected-row` itself (a grid item now, same long-content overflow
  fix already documented at `.card-spotlight`).
  Background: `.sect-selected` changed from solid `--stone` to solid
  `--winston-red` — this brand's one genuinely saturated color, already
  proven as a full-bleed section background elsewhere (`.hero-rail`,
  `.card-spotlight`, the masthead). Every text/link/badge color override
  under `.sect-selected` had already been built against a saturated,
  non-paper background in the first place (matching `.card-spotlight`'s
  own conventions) — none needed changing for this swap, only the one
  background declaration.
  Theme version bumped 1.27.0 → 1.27.1 (patch: layout/color rework of an
  already-shipped, unreleased section — no new markup, fields, or
  behavior).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — گزیده‌ها changed from 2 to 3 columns, compacted
- **Changed:** Farhad reviewed the 2-column grid live and found the
  6-item shelf reading too tall/narrow for its width ("shallow"), and
  asked for 3 columns / 2 rows instead, more compact overall.
  `.selected-list` (main.css §11) grid breakpoints changed to 1 column
  below 720px, 2 columns 720-999px, 3 columns at 1000px+ — the same
  three breakpoints `.grid-cards` already uses (main.css §09), for
  sitewide consistency, rather than jumping straight from 1 to 3.
  Divider `nth-child` selectors extended with a third rule for the new
  3-column breakpoint (row two now starts at item 4, not item 3) — see
  the CSS block's own updated comment for the full reasoning, unchanged
  from the 2-column version.
  Compacting: column/row gaps tightened (2.5rem → 2rem column gap, 2rem
  → 1.5rem row spacing), cover image `clamp()` reduced from
  84-140px to 72-112px (a narrower 3-column cell has less room for both
  cover and text), and the excerpt clamp tightened from 3 to 2 lines
  (`.card-dek`, scoped to this component only) with its word-trim count
  lowered 32 → 20 (selected-row.php) so the shorter excerpt still
  reliably fills, rather than overruns, the tighter 2-line clamp — same
  word-count-tuned-to-clamp reasoning already used for `.card-dek`
  elsewhere on the site.
  Verified live at 1024px (3 columns, 293.6px each, confirmed via
  `getComputedStyle`, `document.body.scrollWidth` === `clientWidth`, no
  horizontal overflow) and at 375px (drops to 1 column, same overflow
  check passed).
  Theme version bumped 1.27.1 → 1.27.2 (patch: layout/color-rework of an
  already-shipped, unreleased section).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — گزیده‌ها type scale, spacing, and category label
- **Changed:** Farhad asked for three more refinements after reviewing
  the 3-column grid live: smaller title (20px → 16px) and excerpt
  (15px → 12px, using the existing `--t-caption` token rather than a new
  magic number), tighter/cleaner spacing throughout, and the category
  label's white badge removed entirely — plain text only, matching his
  reference image, recolored for contrast since plain crimson text
  directly on the section's own crimson background would be invisible.
  `.selected-row-body .h-card`/`.card-dek` (main.css §11) now carry their
  own scoped font-size overrides — every other `.h-card`/`.card-dek` on
  the site is untouched. Cover thumbnail reduced again (72-112px →
  64-96px) to stay visually balanced against the now-smaller text block.
  Spacing tightened: row gap 1rem → .85rem, explicit small margins added
  between title/excerpt/date (none existed before — this component
  relied on default flow spacing, which read as uneven/"hashed" once the
  type got smaller).
  Category label recolored to `--cream` (not `--paper`, the title's own
  color) — still one of this site's eleven brand tokens, contrast
  against `--winston-red` calculated at ~5.9:1 (comfortably AA, same
  ballpark as the title's own ~6.3:1 in white), but warm enough to read
  as a deliberately distinct label color rather than a dimmer echo of
  the title above it.
  Verified live: `getComputedStyle` confirmed 16px/12px sizes, cream
  label color with a fully transparent background, on both desktop
  (3-column) and mobile (375px, single column, no overflow).
  Theme version bumped 1.27.2 → 1.27.3 (patch).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — گزیده‌ها category label reverted to cream + tag icon
- **Changed:** The `--ok` green label from the previous round (this
  session's own contrast concern — ~1.2:1 against the crimson
  background) was tried live by Farhad and, as flagged, didn't read
  well; he asked to revert to the earlier --cream color and add a small
  icon instead so the label still reads as distinct from the title,
  without an off-brand/low-contrast color. That intermediate green
  change was never committed, so this entry replaces it outright rather
  than reading as two separate color changes in history.
  New `shola_tag_icon()` (inc/template-tags.php) — a filled 16×16
  `currentColor` glyph matching `.type-label`'s own existing icon family
  (card.php's `$type_icon`), not the stroke-based 24×24 family used for
  date/word-count/clock elsewhere, since this icon lives inside a
  `.type-label`, the same class those filled icons already target.
  Rendered in template-parts/cards/selected-row.php, before the term
  link, inside the existing `.type-label` (already `inline-flex` with a
  gap — no layout changes needed to fit it in).
  `.sect-selected .type-label`/`.type-label a` back to `color: var(--cream)`
  (font-size stays the literal 10px from two rounds ago — that was a
  separate, unretracted ask). New `.sect-selected .type-label .glyph`
  rule recolors the icon to match — `.type-label .glyph`'s site-wide
  default (main.css §07) hardcodes `--winston-red` rather than
  inheriting `currentColor`, so an explicit override was needed, same as
  every other per-context icon recolor already on this site
  (`.card-byline .glyph`, etc.).
  Verified live: `getComputedStyle` confirmed both the label text and
  the icon report `rgb(250, 248, 243)` (--cream), label still 10px.
  Theme version bumped 1.27.3 → 1.27.4 (patch).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — گزیده‌ها cover reworked to fill its full row height
- **Changed:** Farhad compared the shipped row layout against his
  reference image again and asked for the cover to span the *entire*
  height of its row — top-aligned with the category label, bottom-
  aligned with the byline/date, as one visually bonded unit with the
  text beside it — rather than a small fixed-size thumbnail sitting at
  the top of a taller text column. Presented understanding first per
  this session's established workflow; approved with three explicit
  additions: it must never look small even on short-text rows, its box
  size must never depend on the uploaded photo's own dimensions (only on
  the layout), and to do a fuller design pass on the rest of the
  component while at it.
  `.selected-row` (main.css §11) switched from `display: flex` to a
  2-column CSS Grid (`grid-template-columns`, `align-items: stretch`) —
  required, not just an `align-items` swap, because a CSS Grid row's
  auto-height calculation explicitly excludes percentage-sized grid
  items, so the image's `height: 100%` cleanly resolves against the
  text column's own natural content height with no circular dependency;
  the flex equivalent doesn't have that guarantee once an aspect-ratio
  is involved. `min-height: 180px` added as a floor so short-text rows
  still get a substantial cover, at the cost of exact bottom-alignment
  on exactly those rows (accepted trade-off, per Farhad's explicit
  "never small" priority).
  First pass gave the image column `auto` width with `aspect-ratio: 1/1`
  (width following the stretched height) — caught live immediately:
  in the 3-column desktop grid each row is only ~294px wide, so a tall
  row (a full 2-line title + 2-line excerpt) produced a ~274px-wide
  square image, squeezing the text into a ~20px sliver. Fixed with a
  literal fixed 130px image column instead — width never changes
  (satisfying Farhad's "it should be fixed" literally, more so than a
  square ratio ever did), only height grows with the text; `object-fit:
  cover` on the `<img>` still fills that box completely regardless of
  the source photo's own proportions.
  Design pass (Farhad's explicit ask to review "spacing, sizing, color
  contrast, any fundamental design concept that doesn't fit"): title
  capped at 2 lines (`-webkit-line-clamp`, same protective reasoning
  already documented at `.card-spotlight-item--featured .h-card` — one
  long headline could otherwise inflate every cover sharing its grid
  row); byline/date font-size matched to the category label's 10px
  (was 14px) so the two small "meta" elements read as one consistent
  scale; row-divider spacing widened 1.5rem → 2rem to stay proportionate
  next to the now much taller (180px+) rows.
  Verified live via `getBoundingClientRect()`: image and text report
  identical top/bottom coordinates on every row checked, at desktop
  (3-column, 130px fixed image width, no overflow), tablet (820px,
  2-column, confirmed via `document.body.scrollWidth`/`clientWidth`
  parity), and mobile (375px, 1-column, same parity check) — and
  visually confirmed on page-selected.php's archive (plain background,
  same component, unaffected by the `.sect-selected`-scoped color
  rules).
  Theme version bumped 1.27.4 → 1.28.0 (minor: layout-mechanism change,
  not just a value tweak).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — گزیده‌ها cover switched to landscape (article, not publication)
- **Changed:** Farhad asked, as a design question first, whether
  switching to 2 columns / 3 rows would fix an ambiguity he'd noticed:
  the previous round's tall, narrow cover shape matched this site's own
  established *publication*-cover convention (shola_issue_cover,
  shola_hero_minimal_cover — both portrait) rather than its *article*-
  cover convention (.card-media, 3:2 landscape) — confusing for a
  section that exclusively curates articles/reports, never
  publications. Answered as a design consult before touching anything
  (per this session's established workflow): column count alone doesn't
  fix the ratio (the row's height is driven by text content regardless
  of column count) — the real fix is the aspect ratio itself, but a
  *landscape* cover sized big enough not to look thin does need more
  width than the 3-column ~294px row leaves once text is accounted for,
  so 2 columns turned out to be genuinely necessary here, not just
  preference. Farhad confirmed: landscape, matching the regular article
  cards, not the publication-cover look.
  `.selected-row-media` (main.css §11) reworked from a fixed-width/
  text-height-tracking box (last round's mechanism, built specifically
  to bookend the text column's full height) to a fixed `180px` width ×
  `aspect-ratio: 3/2` box — the same ratio `.card-media` already uses
  site-wide for articles. This is mechanically incompatible with last
  round's "grows to match the text" behavior, so that's gone; matching
  this site's own article-image language was the explicit priority this
  round. `.selected-row` dropped the 1000px 3-column breakpoint (back to
  1-column mobile / 2-column tablet-up, matching the layout's second
  round) and `align-items: stretch` → `start` (the cover no longer needs
  to track the text column's height).
  Image size: `shola_selected_square` (1:1, added specifically for this
  component two rounds ago) removed from inc/setup.php and
  selected-row.php switched to reuse the existing `shola_card` (3:2) —
  the same size the regular homepage/archive article cards already use,
  rather than maintaining a second, now-pointless near-duplicate size
  registration.
  Verified live: `getComputedStyle`/`getBoundingClientRect` confirmed
  180×120px (3:2) covers and 2 real grid columns (456.4px each) at
  desktop, and no horizontal overflow (`document.body.scrollWidth` ===
  `clientWidth`) at mobile (375px).
  Theme version bumped 1.28.0 → 1.28.1 (patch).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — گزیده‌ها row/column spacing tightened
- **Changed:** Farhad's live screenshot (annotated, marking both the
  column gutter and the row divider gap) flagged the spacing between
  rows and columns as reading too wide/"very white" now that the cover
  is a compact 180×120px landscape box — the 2rem values in both places
  were tuned for the previous round's much taller, text-height-tracking
  covers and were never revisited once that height dropped back down.
  `.selected-row:nth-child(n+2)`/`(n+3)` divider margin/padding
  (main.css §11): 2rem → 1.25rem, at both the mobile and 720px+ tiers.
  `.selected-list`'s `column-gap` (720px+): 2rem → 1.5rem.
  Verified no horizontal overflow at any breakpoint after tightening
  (`document.body.scrollWidth` === `clientWidth` at 375px mobile, 820px
  tablet, and desktop).
  Theme version bumped 1.28.1 → 1.28.2 (patch).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-16 (later same session) — گزیده‌ها hard-capped at 6 in the render loop
- **Fixed:** Farhad confirmed live that flagging a 7th article as
  گزیده‌ها rendered all 7 on the homepage, despite
  `shola_get_selected_query( array( 'posts_per_page' => 6 ) )`
  (front-page.php) already passing a hard `posts_per_page` limit to
  WP_Query. Root cause not reproducible in this session's local dev
  environment — a plain WP_Query with `posts_per_page` applies a SQL
  `LIMIT` server-side, which has no code path that returns more rows
  than that limit, and the only `pre_get_posts` hook in this codebase
  (`Post_Types::include_cpts_in_search()`) is guarded to the main
  search query only, confirmed not touching this secondary query.
  Rather than leave the homepage dependent on diagnosing an
  unreproducible environment-specific cause (a hosting-level cache or
  similar), added a second, independent enforcement layer directly in
  the render loop: an explicit counter that stops rendering after the
  6th row regardless of how many the query itself returns — the display
  cap no longer trusts WP_Query's result count alone.
  Theme version bumped 1.28.2 → 1.28.3 (patch).
  Approved by: Farhad, in this session (2026-09-16).

## 2026-09-17 — New feature: تراکت (Leaflets) — Part 1, CPT/archive/homepage teaser
- **Added:** Farhad relayed a client request to track propaganda/campaign
  visual materials (leaflets, banners, posters for demonstrations/
  protests) as a new dedicated, flat, reverse-chronological section —
  non-technical staff upload directly through wp-admin. Full written
  plan (CPT fields, file list, visual treatment, pagination mechanism,
  RTL approach, breakpoint test plan, open questions) presented and
  approved before any code was written, per this project's standing
  discipline.
  New `leaflet` CPT (`class-post-types.php`) — modeled directly on
  `party_publication` (no taxonomy, `has_archive => false`, flat
  rewrite slug `/leaflets/%postname%/`) — audited this project's other
  flat, uncategorized CPTs rather than inventing a new pattern.
  `supports => array( 'title', 'thumbnail' )` only — no excerpt, no
  editor; the featured image is the content, the optional title is a
  short caption, and this keeps the wp-admin edit screen down to just a
  title field + featured-image box for non-technical uploaders, with no
  custom meta box needed. Ordering is purely native `post_date`
  descending — no counter/numbering field; staff can backdate an
  entry's `post_date` via the standard Publish box for a leaflet
  uploaded well after the protest it was made for (confirmed with
  Farhad).
  New `shola_get_leaflets_query()` (inc/template-tags.php), shared by
  the archive and the homepage teaser so they can't drift on what counts
  as a real, displayable entry — excludes any leaflet with no featured
  image via a `_thumbnail_id EXISTS` meta_query (Farhad's explicit
  call: an entry with no image isn't a real leaflet yet, so it's
  excluded outright rather than falling back to
  shola_get_featured_image()'s generic placeholder).
  New `page-leaflets.php` (modeled on `page-library.php`'s static-Page-
  + WP_Query pattern) and `template-parts/leaflets/leaflet-item.php` —
  deliberately outside `template-parts/cards/`, since this isn't a card
  anatomy: no boxed container, no dek/byline block, just an image at its
  own real aspect ratio (no fixed crop — leaflets/banners/posters vary
  too widely in shape to force into one box) plus a caption below, per
  the Aeon "whitespace and typography, not containers" principle. The
  page's one distinguishing rhythm: captions alternate reading-start/
  reading-end position via CSS `:nth-child`, ≥1000px only (main.css §31)
  — built entirely with logical properties (`margin-inline-start/-end`),
  verified by temporarily forcing `dir="ltr"` on the page to confirm the
  alternation still mirrors correctly.
  Pagination: a single "بارگذاری بیشتر" next-page link (reusing the
  existing `.btn-ghost` component), not a numbered pager and not true
  fetch-based infinite scroll — a plain server-rendered next-page link
  works with JavaScript fully disabled, matching this site's existing
  progressive-enhancement floor, and avoids introducing this project's
  first AJAX-pagination pattern for a single-feature archive. Auto-
  provisioned as a real WP Page via `shola_maybe_seed_leaflets_page()`
  (inc/setup.php), same one-time-seed pattern as `/selected/`.
  Deliberately **not** added to any nav menu location yet — Farhad's
  explicit instruction: reachable only via the homepage teaser link for
  now, pending a separate confirmation with the client about whether it
  belongs in the main navigation; the page has no dependency on being in
  a menu, so one can be added later with zero rework.
  Homepage teaser (front-page.php): single latest leaflet only (`posts_
  per_page => 1`), placed directly after the تازه‌ترین مقالات section
  (which پرخواننده‌ترین/Most Viewed is embedded inside, not a separate
  top-level section — confirmed by reading the actual markup before
  picking this insertion point). Solid `--ink` background, not
  `--winston-red` — Farhad's explicit call: گزیده‌ها, immediately above
  this section on the homepage, already spends this site's one
  deliberately-rationed crimson accent on a solid background; a second
  one here would spend that same "loud, meaningful accent" signal twice
  on one page. `--ink` (one of the same eleven locked tokens) gives this
  section its own strong, distinct weight without competing with
  گزیده‌ها.
  Audit findings surfaced during planning, not assumed: `docs/
  decisions-and-learnings.md` (referenced in the task brief) does not
  exist anywhere in this repo — the `has_archive => false` pattern was
  instead verified directly against the real `issue`/`document`/
  `party_publication`/`party_document` registrations. `render_grid()`
  exists in this codebase but is a private method on the unrelated
  `Video_Guide` class, not a general homepage-module helper — every
  actual homepage module (Most Viewed, اطلاعیه spotlight) is a plain
  inline `WP_Query` + `get_template_part()` in front-page.php, which is
  the real pattern this feature follows instead.
  Verified: homepage and the archive page load with zero PHP errors
  (checked via network request status and rendered output) with no
  leaflets yet published — the teaser correctly stays hidden via the
  same `have_posts()` guard every other homepage section already uses.
  Full breakpoint/alternating-caption/RTL-mirroring verification is
  pending at least one real تراکت entry with a featured image — noted as
  outstanding rather than assumed passing from code review alone.
  Theme version bumped 1.28.3 → 1.29.0 (minor: new CPT-backed page +
  homepage section). Plugin version bumped alongside it (new `leaflet`
  CPT registration) — see the Part 2 entry just below for the combined
  plugin version number, since both parts landed in the same review
  pass.
  Approved by: Farhad, in this session (2026-09-17).

## 2026-09-17 (same session) — تراکت Part 2: upload-time image optimization
- **Added:** Per the same approved plan, automatic server-side
  optimization for تراکت featured-image uploads, scoped to the `leaflet`
  CPT only (Farhad's explicit scope decision — other content types'
  uploads, with their own already-tuned image sizes and more experienced
  uploaders, are untouched). New `\SholaCore\Image_Optimizer`
  (`includes/class-image-optimizer.php`), built entirely on
  `WP_Image_Editor` — WordPress's own image-library abstraction, which
  auto-selects Imagick when available and falls back to GD transparently
  — so this code never needs to know or check which library is actually
  active on the host.
  **Hook point refined during implementation, not as originally
  written in the plan:** the plan's candidate hooks were
  `wp_handle_upload` and/or `intermediate_image_sizes_advanced`/
  `image_editor_output_format`. Building it surfaced a real problem with
  both: `wp_generate_attachment_metadata`-family filters fire *after*
  WordPress has already generated every intermediate size from the
  original file, too late to influence subsize generation; and
  `add_attachment` (fires early enough, with a reliable `post_parent` in
  *some* upload flows) turned out not to reliably carry `post_parent` in
  the one flow non-technical staff actually use — the block editor's
  "Set featured image" panel typically uploads as an unattached
  attachment first, then links it via `set_post_thumbnail()` as a
  separate step. Hooking `added_post_meta`/`updated_post_meta` on
  `_thumbnail_id` instead triggers off "this is now the leaflet's
  image," not off upload timing, so it covers every upload flow
  uniformly. If the file needs changing, `wp_generate_attachment_
  metadata()` is explicitly re-run afterward so any subsizes already
  generated from the pre-optimization original are correctly
  regenerated from the optimized file (same deterministic filenames —
  nothing left orphaned on disk).
  Values, all as proposed and confirmed: max long edge 2000px (checked
  against this theme's own `--wrap-wide`, 1200px, and the existing
  `shola_hero_wide` full-bleed precedent, 1920px — 2000px covers a
  full-bleed leaflet image at ~1.67x pixel density without keeping a
  phone-camera original at its full size); JPEG quality 82 (matches
  WordPress core's own default since 5.3 — not a deviation, just made
  explicit and documented rather than relying on an undocumented
  default); skip-if-under 300KB; PNG→JPEG conversion for non-alpha PNGs
  only.
  PNG alpha-channel detection reads the PNG file's IHDR color-type byte
  directly from the file header — a format-level check, not a library-
  specific API, so it behaves identically under Imagick or GD.
  Deliberately conservative: only color types 0 (grayscale) and 2
  (truecolor) — the two types that can never carry transparency — are
  converted; palette PNGs (type 3, which *can* carry a `tRNS`
  transparency chunk) are left alone rather than risk silently
  flattening a genuinely-transparent image to an opaque JPEG.
  Retroactive-safety: this hook only ever fires when a `leaflet` post's
  featured image is actually set, which only happens going forward —
  structurally incapable of touching already-uploaded media, not just
  by intention.
  Risks checked per the approved plan: no existing image-related filter
  anywhere in this codebase (confirmed via repo-wide grep) — no
  collision risk; `shola_get_featured_image()` calls `get_the_post_
  thumbnail()` unchanged, which already transparently serves whichever
  intermediate sizes exist — nothing about its contract changes.
  Site Health → Info → Media Handling's "Active editor" value (Imagick
  vs. GD) was not confirmed directly this session — Farhad to report it
  separately; the implementation above does not depend on the answer
  either way.
  Plugin version bumped 1.16.1 → 1.18.0 (minor: covers both this
  session's plugin-side additions — the `leaflet` CPT registration from
  the entry above, and this new class + hook registration).
  Approved by: Farhad, in this session (2026-09-17).

## 2026-09-17 (same session) — تراکت fullscreen lightbox
- **Added:** Farhad flagged that clicking a تراکت on the archive page
  opened the raw image file with no site chrome — traced to the
  homepage teaser's link falling through WordPress's template hierarchy
  to `single.php` (built for articles, expecting content/excerpt a
  leaflet doesn't have), since no `single-leaflet.php` exists; the
  archive item's own `<img>` had no link at all. Full written plan
  (technical approach, RTL arrow-direction audit, color tokens, caption
  layout per breakpoint, conditional-caption mechanism, lazy-loading
  approach, accessibility specifics) presented and approved before any
  code was written.
  Built on a native `<dialog>` (not a hand-built overlay `<div>` or a
  third-party library) for the real modal top-layer element and
  implicit dialog role it provides for free. Every thumbnail (archive +
  homepage teaser) now wraps in `<a href="{full-size image URL}">` —
  the complete no-JS fallback: with JavaScript disabled this is just a
  plain link to the image file; `main.js` intercepts it when JS is
  available.
  Two real bugs found and fixed live during implementation, not just
  assumed working from the written plan:
  1. **Navigation race with the existing page-loader click handler**
     (main.js): that handler treats any same-origin `<a href>` as an
     ordinary internal link, calls its own `preventDefault()`, and
     schedules its own `window.location.href` navigation — running
     *before* the lightbox's own (later-registered) click handler ever
     got a chance to intercept the click. The lightbox opened for an
     instant, then the page navigated to the raw image anyway once that
     scheduled navigation fired. Fixed by adding one exclusion line to
     that handler's existing skip-list (`data-leaflet-trigger`), the
     same pattern it already uses for wpadminbar/download/mailto links.
  2. **`<dialog>`'s native "close" event and native Escape-to-close did
     not fire/work** in the browser used to test this feature — verified
     directly, not assumed: a real, trusted Escape keypress (via the
     browser automation tool's genuine OS-level key action, not a
     synthetic `KeyboardEvent`, which wouldn't be a fair test of a
     native default action) left the dialog open; a direct
     `dialog.close()` method call didn't fire a `close` event even after
     a 100ms wait. Rather than trust each browser's own level of
     `<dialog>` support, both Escape-to-close and the Tab focus trap are
     now implemented explicitly in `main.js` (a single `leafletClose()`
     function every close path calls directly, and a manual
     first/last-focusable-element wrap on Tab/Shift+Tab), so the feature
     works identically regardless of native support quality.
   RTL arrow-direction confirmed against `page-selected.php`'s own real
  pagination (`'prev_text' => '→', 'next_text' => '←'`) rather than
  assumed: "next" (chronologically older) is left-pointing at the
  reading-end side, "previous" (newer) is right-pointing at the
  reading-start side — `inset-inline-start/-end`, mirrors correctly
  under `dir="ltr"` with zero changes (verified live by forcing it).
  Keyboard arrows and swipe direction both read the page's actual
  computed `direction` at runtime rather than hardcoding RTL, and were
  verified under both directions: ArrowRight under forced `dir="ltr"`
  correctly moved to the older item, ArrowLeft correctly no-opped at
  the boundary (already at the newest item).
  Conditional caption: `page-leaflets.php` builds one JSON array from
  the already-fetched query results (no duplicate query); the `caption`
  key is only ever added when `trim( get_the_title() )` is non-empty.
  In `main.js`, the title/caption `<p>` is only ever created via
  `document.createElement` when `item.caption` is truthy — never
  created-then-hidden. Verified with an isolated test harness running
  the exact same rendering code against a with-caption and a
  without-caption item (both real test تراکت entries happened to have
  titles, so this validated the empty-caption path directly rather than
  leaving it unverified): confirmed 1 title element created for the
  with-caption case, 0 for the without-caption case — not hidden, not
  present in the DOM at all. The caption container itself always has at
  least the date paragraph (native `post_date` is never empty), so it's
  never left as an empty box either way.
  Homepage teaser: single-image mode confirmed live — its trigger link
  carries no `data-leaflet-index`, so `main.js` reads its own
  `data-leaflet-*` attributes directly and hides both nav arrows,
  matching Part 1's "single latest entry, not a mini-gallery" spec.
  Prev/next in the archive lightbox is capped to the current page's own
  loaded batch, never reaching across a "بارگذاری بیشتر" page boundary,
  per Farhad's explicit confirmation.
  Design: sharp corners throughout (no circular buttons — this site's
  own locked "no rounded/pill elements anywhere" discipline, corrected
  from a rounded-button idea floated in the plan draft before
  implementation); overlay scrim and caption gradient built from
  `--ink`/`--paper` only, no new colors; 44×44px touch targets for
  close/prev/next at every breakpoint, not just mobile. No open/close/
  transition animation was added — a plain instant show/hide already
  trivially satisfies `prefers-reduced-motion` (nothing to reduce),
  consistent with this project's general restraint on decorative motion.
  Verified: no horizontal overflow at 375px (mobile), 820px (tablet),
  and 1400px (desktop) — `document.body.scrollWidth` === `clientWidth`
  at each; focus moves to the close button on open and returns to the
  exact triggering link on close; Tab/Shift+Tab cycling stays within the
  dialog's three controls (confirmed via real Tab keypresses, not
  synthetic events).
  New files: `template-parts/leaflets/lightbox.php`. Modified:
  `template-parts/leaflets/leaflet-item.php`, `front-page.php`,
  `page-leaflets.php`, `assets/js/main.js`, `assets/css/main.css` (new
  §32).
  Theme version bumped for this feature — see style.css.
  Approved by: Farhad, in this session (2026-09-17).
