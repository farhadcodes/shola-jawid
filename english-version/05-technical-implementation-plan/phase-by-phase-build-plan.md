# Phase-by-phase build plan

Written in the same phase style as `docs/EXECUTION_PLAN.md` — this file
says *when* things happen and in what order; the other files in this folder
say *how*. Each phase gets its own git branch per `CLAUDE.md` §8's standing
workflow, squash-merged to `main` when done, with a `docs/CHANGELOG.md`
entry if any rule was newly applied.

## Phase 0 — Governance (do this first, nothing else starts before it)

- Get the client's/Farhad's explicit sign-off on the architecture decision
  in `02-architecture-decision/approach-and-alternatives.md`.
- Resolve (or explicitly defer with a documented assumption) the open
  decisions in `03-content-architecture/open-decisions-needing-client-input.md`.
- Apply the `CLAUDE.md` §1 rule change exactly as specified in
  `claude-md-rule-change.md`, with its `docs/CHANGELOG.md` entry.

## Phase 1 — Infrastructure (no visible change to either language yet)

- ✅ Extend `shcore_language` to every content type decided on in Phase 0.
- ✅ Generalize `shcore_translation_id` into a shared pairing field across
  those content types, with its admin metabox UI.
- ✅ Add `shcore_term_name_en`/`shcore_term_slug_en` term-meta fields and their
  admin UI to `topic`, `publication`, `collection`, `party_document_category`
  (and `editors_pick_category`, `report`) — done 2026-11-26, generalized
  across `Category_Manager::MANAGED` the same way ترتیب already was.
- ✅ Build the `/en/` rewrite rule, the `shola_lang` query var, and the
  central language-context function (`url-structure-and-routing.md`) —
  done 2026-11-27, `class-language.php`.
- ✅ Add the language filter to every archive/search/homepage-panel query
  — done 2026-11-27. `pre_get_posts` covers the 3 templates using
  WordPress's own main query (search, announcement/editors_pick
  archives); every other listing template (14 files: every
  `taxonomy-*.php`, `page-reports.php`, `page-party-publications.php`,
  all 12 of `front-page.php`'s panel queries, plus the two shared query
  functions `shola_get_selected_query()`/`shola_get_leaflets_query()`)
  now calls `Language::add_language_filter()` directly on its own query
  args. Verified live: Persian homepage unchanged byte-for-byte in
  substance; English homepage correctly shows every content panel empty
  (no English content exists yet) with zero Persian content leaking
  through anywhere.
- ✅ Build `shola_lang_has_content()` — done 2026-11-27, `inc/template-
  tags.php`. Every hide-if-empty check (nav, homepage panels) calls this
  one function, not a one-off query each. A sibling,
  `shola_page_has_english_version( $page_id )`, handles the different
  "has this *specific* Page been translated" check static pages need.
- ✅ Nav hide-if-empty — done 2026-11-27, `shola_hide_empty_language_nav_items()`
  in `inc/setup.php`, filtering `wp_nav_menu_objects` for the popup
  menu's two real locations. `page` post type got `shcore_language`/
  `shcore_translation_id` added too (needed for the About/Contact
  pairing check) — required fixing the backfill migration from earlier
  to track completion per post type rather than one global flag, since
  the original flag was already marked done before `page` existed.
  Homepage panel hide-if-empty turned out to already work for free —
  every panel was already wrapped in `if ($query->have_posts())`, so
  Phase 1's query-language-filtering alone already made them disappear
  correctly once filtered to zero results; confirmed live, no extra code
  needed there.
- **This phase is now fully done and is genuinely user-visible** (the
  nav itself changes) — verified live at the actual URLs, not just via
  function calls: `/en/`'s popup menu correctly shows only Home/Topics/
  Search; Persian nav shows every item unchanged.

## Phase 2 — Language switcher ✅ done 2026-11-27

- ✅ Built `shola_get_language_switch_url()` (`inc/template-tags.php`) —
  covers every page type: homepage, structural listing Pages (fixed-path
  mirror), genuine editorial Pages/single posts (pairing-based, via
  `shola_get_paired_url_or_fallback()`), taxonomy archives (slug-swap via
  `shola_build_term_link_for_lang()`), CPT archives, and search — every
  branch falls back to the target language's homepage rather than a dead
  link. Wired into both real masthead layouts (`header.php`'s shared row,
  `template-parts/masthead/two-tier.php`) via one shared renderer,
  `shola_render_language_switch_link()`.
- ✅ Verified live against the real site, not just read from the code —
  and two real bugs were caught doing that, not assumed fixed:
  1. `is_page()`'s structural-vs-editorial detection used
     `get_page_template_slug()`, which only reflects a template chosen
     from the Page Attributes dropdown — every `page-*.php` file in this
     theme is matched by WordPress's automatic `page-{slug}.php` naming
     convention instead, so that function returned empty for all of
     them, silently misrouting نشرات/کتابخانه/etc. through the pairing
     branch instead of the fixed-path one. Fixed with `get_page_template()`
     (the actually-resolved file), confirmed live on `/publications/`.
  2. The pairing branch returned a paired post's bare `get_permalink()`,
     with no `/en/` prefix — meaning a reader sent to an English post
     this way would land in the *Persian* language context (no
     `shola_lang=en`), making that same page's own switcher then offer
     the wrong direction. Fixed by rebuilding the URL with the correct
     prefix explicitly, same as every other branch. Verified end-to-end
     with real throwaway paired posts: FA→EN and EN→FA both landed on
     the correct post, in the correct language context, confirmed by
     re-reading the switcher's own link on the destination page.
  3. (Caught during visual QA, not function testing) the new masthead
     link overflowed off-screen entirely on real, common desktop widths
     (confirmed via `getBoundingClientRect()`, not a guess) — and the
     site's actually-active masthead layout turned out to be `two-tier`,
     not the `default` layout assumed at first glance, confirmed via
     `document.querySelector('header.masthead').className` rather than
     assumed. Fixed with a CSS rule (`main.css`) hiding the in-masthead
     link below 1400px, plus a second, always-visible copy added to the
     popup menu (`#menu-panel`) so the switcher is reachable at every
     screen width regardless.
- Hide-if-empty for nav and homepage panels was already done in Phase 1
  (confirmed there, not repeated here).

## Phase 3 — LTR CSS pass

- ✅ **Font decision** (open decision #5) — resolved 2026-11-28: same
  fonts as Persian (Farhang2/ModamPro) for English too, confirmed by
  Farhad and verified directly (real Latin text rendered in both fonts
  with no fallback, live in the browser — full, legible letterforms and
  numerals, no missing glyphs). **No font-family CSS work needed at
  all** — see `typography-for-english.md`'s rewrite. This replaces the
  originally-planned `html[lang="en"] body`/`.ui` font-switch rules,
  which are no longer part of this plan.
- ✅ **Foundational fix, done 2026-11-28, found before anything else in
  this phase could matter:** `is_rtl()` (which `language_attributes()`'s
  `<html dir="...">` output, Persian Calendar's Jalali-date/Persian-digit
  conversion, and potentially other code all branch on) is a global,
  whole-site-locale check — not request-aware. Without a fix, every
  English page would still render `dir="rtl"` and still show Jalali
  dates. Fixed in `class-language.php` with WordPress's native
  `switch_to_locale('en_US')` for `/en/`-prefixed requests. Real bug
  caught live, not assumed fixed: a single switch at `plugins_loaded` was
  silently undone by a WordPress-core locale reload between
  `setup_theme` and `after_setup_theme` (traced precisely with a
  temporary debug probe) — fixed by reapplying the switch a second time
  at `after_setup_theme` priority 999. Verified at every bootstrap hook
  from `plugins_loaded` through `wp_loaded`; confirmed `date_i18n()`
  returns real Gregorian English dates on `/en/` and real Jalali dates
  unchanged on Persian pages; wp-admin unaffected. See
  `04-design-and-ltr-system/rtl-to-ltr-css-audit.md` for the full story,
  including one separate finding this doesn't reach (the masthead's
  REST-polled "live date" widget) flagged as its own follow-up.
- ✅ This same fix **resolved two of the three remaining
  `component-by-component-ltr-notes.md` items for free**: number
  formatting (Persian-digit conversion is gated on the same `is_rtl()`
  check) and drop-caps (the original audit had mis-attributed this to
  `main.css` — it's actually WordPress core's own already-RTL-aware
  block-library CSS, now reached correctly once `is_rtl()` is correct).
- ✅ Icon mirroring — done 2026-11-28: the hero filmstrip's prev/next SVG
  arrows, plus one other real finding along the way — an existing
  `[dir="ltr"] .masthead--two-tier` rule from an earlier session turned
  out to have been dead code the whole time (WordPress core never prints
  `dir="ltr"` explicitly, only omits `dir` for LTR), fixed to
  `html:not([dir="rtl"])`, the selector that actually reaches English
  pages — same fix applied to the new filmstrip-arrow rule.
- ✅ Pagination arrow characters — done 2026-11-28: added
  `shola_pagination_prev_text()`/`shola_pagination_next_text()`
  (`inc/template-tags.php`), both direction-aware via `is_rtl()`, and
  replaced the hardcoded `'prev_text' => '→'`/`'next_text' => '←'` pair
  in all 9 templates that call `paginate_links()`
  (archive-announcement.php, archive-editors_pick.php,
  page-party-publications.php, page-reports.php, page-selected.php,
  search.php, taxonomy-collection.php,
  taxonomy-party_document_category.php, taxonomy-publication.php,
  taxonomy-topic.php — 10 files, one had two call sites). Verified live
  with a temporary debug probe (not assumed from code alone): Persian
  pages still render `prev=→ next=←` unchanged; `/en/` renders
  `prev=← next=→`, correctly swapped. A second, related bug was caught
  reviewing this: the leaflet lightbox's prev/next buttons
  (`template-parts/leaflets/lightbox.php`) position correctly via
  `inset-inline-start`/`-end` already, but their chevron SVGs are a
  static path, not direction-aware on their own — same class of bug as
  the filmstrip arrows. Fixed with the same `scaleX(-1)` mirroring
  technique, scoped to `html:not([dir="rtl"])`, in `main.css`.
- ✅ Gregorian-vs-Jalali date decision (open decision #6, resolved —
  Gregorian for English) — **resolved by the foundational fix above, not
  a separate task.** Persian Calendar's own date conversion is gated on
  the exact same `is_rtl()` check that fix already corrects; no
  additional code needed beyond it.

## Phase 4 — Demo content, then real English content population

- ✅ **Demo-content infrastructure** — done 2026-11-28:
  `shcore_is_demo_content` registered on every `Language::
  MANAGED_POST_TYPES` type plus `page`, with a generalized "نمونه"
  admin-list column and edit-screen notice (`class-meta-fields.php`).
  Verified with a direct bootstrap script simulating the admin list-
  table columns and both notice states, not assumed from the code alone.
- ✅ **Demo content populated** — done 2026-11-28: one English post per
  managed content type (`post`, `issue`, `document`,
  `party_publication`, `party_document`, `leaflet`, `editors_pick`,
  `announcement`), each paired bidirectionally to a real Persian post,
  flagged, `[نمونه]`-titled, generic placeholder prose (never adapted
  from the real Persian original — see demo-content-policy.md's own
  rule on this). Plus two new English Pages (About, Contact), paired to
  the real `دربارهٔ شعله جاوید`/`ارتباط با حزب` Pages. Full story,
  including the four real bugs this surfaced and fixed, in
  `docs/CHANGELOG.md` 2026-11-28 (continued, 3).
- ✅ **English term labels/slugs for every taxonomy term in scope** —
  done 2026-11-28: all 24 real terms across `topic`, `publication`,
  `collection`, `party_document_category`, `report` now have
  `shcore_term_name_en`/`shcore_term_slug_en` set (`editors_pick_
  category` has zero terms yet in either language — nothing to
  translate). **This alone wasn't enough** — verifying it live
  surfaced that no template had ever actually been wired to *use*
  these fields (Phase 1 built the mechanism, not its consumers) and
  that per-language term post-counts weren't being filtered on
  *either* language once content started sharing terms across them —
  both fixed across every taxonomy archive, the homepage موضوعات panel,
  the shared card, and every tile-listing Page; see the CHANGELOG entry
  above for the full list of files and the live verification on both
  languages.
- ✅ **Nav menu items now resolve to the correct language's URL, not
  just hide/show correctly** — done 2026-11-28, found while verifying
  the above: every popup-menu item is a plain WP menu-item post with a
  hardcoded Persian URL; `shola_hide_empty_language_nav_items()` (Phase
  1) only ever removed empty ones, never rewrote the URL of one that
  stayed. This was the single most user-visible bug found this phase —
  every nav click from an English page would have silently bounced the
  reader back to Persian. Fixed in the same function; see CHANGELOG.
- Once the client is ready, they write the real English content
  themselves (this is their own ongoing editorial process, covered in
  `06-editorial-workflow/`) — About and Contact pages first (lowest
  complexity, highest visibility), then articles/issues, pairing each to
  its Persian original, replacing demo posts as real ones go up.
- This phase has no fixed end date past the demo-content step — real
  content population is an ongoing editorial process.

## Phase 5 — SEO and sitemap ✅ done 2026-11-28

- ✅ **A real, previously-undiscovered routing bug found and fixed
  first, before any of the SEO work below could mean anything:** every
  URL on the site was reachable at BOTH its bare path and its
  `/en/`-prefixed mirror, regardless of which language that content
  actually is — the `/en/` rewrite rules only ever *add* a prefixed
  mirror of each existing rule, never remove the original. Confirmed
  live: `/en/about/` rendered the *Persian* About page with
  `lang="en-US"`/no `dir="rtl"`; `/about-en/` (no prefix) rendered the
  *English* About page with `dir="rtl"`/`lang="fa-IR"`. Fixed with
  `Language::redirect_mismatched_singular_language()` — a 301 to the
  one correct URL whenever a singular post/Page's own
  `shcore_language` disagrees with the URL's language, added to
  `class-language.php`. Deliberately excludes the structural listing
  Pages (نشرات, کتابخانه, ...) via a new `Language::
  STRUCTURAL_PAGE_SLUGS` list — those render the *same* single Page row
  at both URLs by design, not a mismatch to correct, found live the
  moment the first version of this fix wrongly redirected `/en/leaflets/`
  back to `/leaflets/`.
- ✅ Rewired `class-seo.php`'s hreflang tags from inert self-referential
  scaffolding to real pairing-based logic: singular content gets a
  second `hreflang` tag only when a real, published
  `shcore_translation_id` pairing exists (never a guessed/fallback
  URL); every archive/search/front-page context always gets both
  (`/en/` structurally mirrors all of them). `x-default` stays on the
  Persian URL when one exists. Verified live across every context:
  paired singular (`/en/about-en/` ↔ `/about/`), unpaired singular
  (one real tag only), taxonomy archive, post-type archive, search,
  front page.
- ✅ Canonical URLs now carry the `/en/` prefix: a new
  `get_canonical_url` filter (`filter_singular_canonical_url()`) fixes
  core's own `rel_canonical()` for singular content (which otherwise
  built its URL from the never-prefixed `get_permalink()`), and
  `SEO::get_canonical_url()`'s own non-singular branches (search,
  taxonomy, post-type archive, front page) each got the same prefix
  logic. Verified live, not assumed.
- ✅ `og:locale` needed no change at all, confirmed live exactly as this
  file predicted: `fa_AF` on Persian pages, `en_US` on English ones.
- ✅ Sitemap — **checked live, not assumed, per this file's own
  instruction**: core's `wp_sitemaps_*` does **not** auto-include the
  `/en/` prefix (every post-type sitemap listed English content's bare,
  never-prefixed URL — the exact URL the new redirect fix above now
  301s away from). Fixed with one `wp_sitemaps_posts_entry` filter,
  re-verified by curling every one of the 9 managed post types' sitemap
  XML directly. **Deliberately deferred, not a silent gap:**
  taxonomy-archive sitemap entries still list only the Persian URL per
  term — adding a second English entry per term means injecting new
  sitemap rows, not correcting existing ones, a larger change left for
  a later round if Farhad wants it (English taxonomy archives are still
  fully reachable via nav/search either way, just not sitemap-listed
  directly).

## Phase 6 — POT file regeneration ✅ done 2026-11-28

- ✅ Regenerated both `.pot` files via WP-CLI's `wp i18n make-pot`
  (`pot-file-regeneration.md`), run against the real local install
  (WP-CLI phar + the exact matching PHP binary/php.ini, same technique
  used throughout this project for live verification). Theme:
  4 → 197 entries. Plugin: 67 → 346 entries — both now current through
  every post type/taxonomy/field added since the original 2026-08-05/06
  generation (`party_publication`, `party_document`, `leaflet`,
  `hero_section`, `masthead_section`, `editors_pick`, and every string
  this English-version rollout itself added: the language switcher, the
  admin demo-content notice, the admin language switcher, …).
- ✅ Fixed one real header regression WP-CLI's default introduced:
  `Report-Msgid-Bugs-To` defaulted to a `wordpress.org` support-forum
  URL — wrong, since this project isn't published there — re-generated
  with `--headers` to keep the original GitHub issues URL both files
  already had.
- Three benign WP-CLI warnings during generation (same msgid, different
  `translators:` comment, e.g. "%s مقاله" commented once as "number of
  articles" and once as "article count") — not an error, nothing to fix;
  gettext just can't attach two different comments to one string.
- Verified live, not assumed: grepped both regenerated files for a
  cross-section of recent strings (`تغییر زبان`, `گزیده‌ها`, the demo-
  content admin notice, `اسناد حزب`) to confirm they're actually present,
  not just that the file got bigger.

## Phase 7 — QA

- Full checklist in `07-qa-and-launch-checklist/definition-of-done.md` —
  screenshot diff every template type in both languages, verify every
  language-switcher path, confirm zero physical CSS properties crept in
  during Phase 3, `phpcs` clean.

## Phase 8 — Launch

- Client review and sign-off.
- Tag the release in git, same convention as `CLAUDE.md` §8's existing
  `v1.0.0` tag practice.
