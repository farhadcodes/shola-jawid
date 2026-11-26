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
- Build the `/en/` rewrite rule, the `shola_lang` query var, and the central
  language-context function (`url-structure-and-routing.md`).
- Add the `pre_get_posts` language filter to every archive/search query.
- Build `shola_lang_has_content()` — the one shared "does this section have
  real English content yet" helper, per
  `03-content-architecture/complete-mirror-and-empty-state-rule.md`. Every
  later hide-if-empty check (nav, homepage panels, taxonomy term listings)
  calls this one function, not a one-off query each.
- **Nothing is user-visible yet at the end of this phase** — this is pure
  plumbing, verified via WP-CLI/direct query checks, not a visual QA pass.

## Phase 2 — Language switcher

- Build `shola_get_language_switch_url()` (the fallback logic from
  `language-field-and-pairing.md`) and wire it into `header.php`'s masthead,
  replacing nothing (this UI never existed in the live build — see the
  bilingual-readiness audit) but adding it fresh, in the same visual spot
  the v6 prototype had it.
- Verify the switcher resolves correctly on every page type listed in
  `04-design-and-ltr-system/component-by-component-ltr-notes.md`, even
  before any real English content exists (every path should correctly fall
  back to an English archive/homepage, never 404).
- Wire `shola_lang_has_content()` into the primary nav menu builder and
  every `front-page.php` panel, so the hide-if-empty rule is live before
  any real English content exists — verify by checking the English site
  looks clean/complete with zero English content published yet (an empty
  nav except About/Contact once those exist, an empty-but-not-broken
  homepage), not just after content starts appearing.

## Phase 3 — LTR CSS pass

- **Decision checkpoint, raised here, not before:** ModamPro's Latin
  glyph coverage (open decision #5) gets decided right at the start of
  this phase, since this is the first point it actually matters (the
  nav/masthead font choice for English pages is implemented here) —
  Claude raises it again at that point rather than deciding it earlier
  or leaving it unresolved past the point it's needed.
- Add the `html[lang="en"] body`/`.ui` font rules from
  `typography-for-english.md`.
- Work through `component-by-component-ltr-notes.md` top to bottom, fixing
  each flagged exception (icon mirroring, drop-cap float, number
  formatting) as found — this requires Phase 1's language context to exist,
  since it needs real `lang="en"`/`dir="ltr"` pages to check against.
- Wire the Gregorian-vs-Jalali date decision (open decision #6, resolved —
  Gregorian for English) into the existing `is_rtl()`-gated date logic in
  `inc/template-tags.php`, now checking the language context instead of
  `is_rtl()` alone.

## Phase 4 — Demo content, then real English content population

- **Demo content first** (resolved 2026-11-27 —
  `03-content-architecture/demo-content-policy.md`): Claude populates
  clearly-marked placeholder English content across every content type,
  so the site looks genuininely full for visual review and so the
  hide-if-empty infrastructure from Phase 1-2 can actually be seen
  working, not just verified empty. Every demo post is flagged
  (`shcore_is_demo_content`) and titled with a `[نمونه]` prefix — see
  that file for the full marking convention and how it gets cleaned up
  later.
- Once the client is ready, they write the real English content
  themselves (this is their own ongoing editorial process, covered in
  `06-editorial-workflow/`) — About and Contact pages first (lowest
  complexity, highest visibility), then articles/issues, pairing each to
  its Persian original, replacing demo posts as real ones go up.
- Add English term labels/slugs to every taxonomy term in scope (this part
  Claude also does now, alongside the demo content, since it's structural
  setup, not editorial writing).
- This phase has no fixed end date past the demo-content step — real
  content population is an ongoing editorial process.

## Phase 5 — SEO and sitemap

- Rewire `class-seo.php`'s hreflang tags from the current inert
  self-referential pair to the real pairing-based logic
  (`seo-and-sitemap-updates.md`).
- Update canonical URL logic for the `/en/` prefix.
- Confirm `wp_sitemaps_*` correctly includes English URLs (check, don't
  assume — WordPress core's sitemap API may need one small filter addition,
  detailed in that file).

## Phase 6 — POT file regeneration

- Regenerate both `.pot` files via WP-CLI (`pot-file-regeneration.md`) —
  both are currently stale by roughly two orders of magnitude even for the
  existing Persian-only string count, independent of this whole project;
  doing this now is overdue housekeeping as much as it is part of this
  rollout.

## Phase 7 — QA

- Full checklist in `07-qa-and-launch-checklist/definition-of-done.md` —
  screenshot diff every template type in both languages, verify every
  language-switcher path, confirm zero physical CSS properties crept in
  during Phase 3, `phpcs` clean.

## Phase 8 — Launch

- Client review and sign-off.
- Tag the release in git, same convention as `CLAUDE.md` §8's existing
  `v1.0.0` tag practice.
