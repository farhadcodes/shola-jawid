# Definition of done

Mirrors `CLAUDE.md` §10's existing phase-completion criteria, adapted for
this specific rollout. Nothing in this project is "done" until every item
below is true — not just the code being written.

## Governance

- [x] `CLAUDE.md` §1 formally updated per `claude-md-rule-change.md` —
      confirmed present (the "Changed 2026-11-26" scope-lock section).
- [x] Corresponding `docs/CHANGELOG.md` entry written — every phase of
      this rollout has its own dated entry.
- [x] All open decisions in `03-content-architecture/open-decisions-needing-client-input.md`
      resolved — confirmed live: all 6 marked RESOLVED with a dated
      answer, none left open.

## Infrastructure

- [x] `shcore_language` present and working on every content type decided
      on in Phase 0 — confirmed live via direct bootstrap script, all 9
      managed types plus `page`.
- [x] Pairing field working both directions (linking A→B also links B→A)
      — confirmed live through the real admin save path
      (`Meta_Fields::save_meta_boxes()`), not just direct script writes.
- [x] `/en/` URLs resolve correctly for every template type listed in
      `01-current-site-audit/template-inventory.md` — every one curled
      directly, all 200 (or correct 404 for a genuine 404).
- [x] Every archive/search query correctly filtered by language —
      confirmed live on topics, library, party-documents, announcements,
      editors-picks, publications, and search specifically (an English
      search for a Persian-only term returns zero results and vice versa).

## Language switcher

- [x] Verified working (correct destination, never a 404/dead link) on
      every single page type — singular (paired and unpaired), every
      taxonomy archive, every structural listing Page, the two new
      paired Pages (About/Contact), front page, search.
- [x] Verified correct fallback behavior specifically on at least one
      untranslated post — confirmed: falls back to the target language's
      homepage, never a dead link.

## Visual/LTR

- [ ] Every item in `component-by-component-ltr-notes.md` checked and
      ticked off against a real rendered page, screenshot-compared against
      its Persian equivalent (same practice `docs/screenshots/phase4/`
      already used for the original Persian build)
- [x] Re-grep of `main.css`/`login.css` for physical CSS properties —
      zero new violations; the one `left`/`right` match is a pre-existing
      code comment, not a rule.
- [x] Font rules confirmed live — resolved 2026-11-28 (decision #5):
      English pages use the same fonts as Persian (Farhang2/ModamPro,
      confirmed full Latin coverage), not Newsreader/Inter as originally
      planned. No font-switch CSS exists or is needed; nothing to check
      beyond confirming the default stack still applies on `/en/` pages.

## SEO

- [x] hreflang tags confirmed real/paired on a live page via `curl` —
      paired singular, unpaired singular (one tag only), taxonomy
      archive, post-type archive, search, front page.
- [x] Canonical URLs confirmed correct on English pages — both core's
      own singular canonical (via the new filter) and the custom
      non-singular branches.
- [x] Sitemap confirmed to include English URLs, checked live — all 9
      managed post types curled directly from their own sitemap XML.

## Code quality

- [ ] `phpcs` runs clean (same WPCS standard already used project-wide)
      — **not verified**: no local `phpcs`/Composer install available
      this round. Manual review + `php -l` on every touched file instead;
      a real `phpcs` run against `phpcs.xml.dist` is still outstanding.
- [x] All code comments follow Farhad's stated rule: 1-2 lines, plain
      language, no jargon — every long narrative comment added across
      this entire rollout (Phases 1-7) reviewed and condensed; full
      history kept only in `docs/CHANGELOG.md`.
- [x] Both `.pot` files regenerated and current — Phase 6.

## Sign-off

- [ ] Client has reviewed the live English version end-to-end
- [ ] Release tagged in git per `CLAUDE.md` §8's existing convention
