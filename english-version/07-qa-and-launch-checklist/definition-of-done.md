# Definition of done

Mirrors `CLAUDE.md` §10's existing phase-completion criteria, adapted for
this specific rollout. Nothing in this project is "done" until every item
below is true — not just the code being written.

## Governance

- [ ] `CLAUDE.md` §1 formally updated per `claude-md-rule-change.md`
- [ ] Corresponding `docs/CHANGELOG.md` entry written
- [ ] All open decisions in `03-content-architecture/open-decisions-needing-client-input.md`
      resolved or explicitly deferred with a documented working assumption

## Infrastructure

- [ ] `shcore_language` present and working on every content type decided
      on in Phase 0
- [ ] Pairing field working both directions (linking A→B also links B→A)
- [ ] `/en/` URLs resolve correctly for every template type listed in
      `01-current-site-audit/template-inventory.md`
- [ ] Every archive/search query correctly filtered by language — confirmed
      live (an English listing never shows a Persian-only post and vice
      versa), not assumed from reading the filter code

## Language switcher

- [ ] Verified working (correct destination, never a 404/dead link) on
      every single page type in `04-design-and-ltr-system/component-by-component-ltr-notes.md`
- [ ] Verified correct fallback behavior specifically on at least one
      untranslated post

## Visual/LTR

- [ ] Every item in `component-by-component-ltr-notes.md` checked and
      ticked off against a real rendered page, screenshot-compared against
      its Persian equivalent (same practice `docs/screenshots/phase4/`
      already used for the original Persian build)
- [ ] Re-grep of `main.css`/`login.css` for physical CSS properties —
      confirms zero new violations were introduced during the LTR pass
- [ ] Font rules confirmed live (English pages actually render in
      Newsreader/Inter, not falling back silently to Farhang2)

## SEO

- [ ] hreflang tags confirmed real/paired on a live page (not the old inert
      self-referential pair) via `curl`/page-source check
- [ ] Canonical URLs confirmed correct on English pages
- [ ] Sitemap confirmed to include English URLs, checked live

## Code quality

- [ ] `phpcs` runs clean (same WPCS standard already used project-wide)
- [ ] All code comments follow Farhad's stated rule: 1-2 lines, plain
      language, no jargon — reviewed specifically for this, not assumed
      from habit
- [ ] Both `.pot` files regenerated and current

## Sign-off

- [ ] Client has reviewed the live English version end-to-end
- [ ] Release tagged in git per `CLAUDE.md` §8's existing convention
