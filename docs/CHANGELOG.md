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