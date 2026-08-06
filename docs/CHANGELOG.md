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
