# English Version of Shola Jawid — Master Plan

**Status:** Planning only. Nothing in this folder has been built yet. No code
has changed because of this plan.
**Written:** 2026-11-26
**Why this folder exists:** Farhad confirmed the client wants a full English
(LTR) version of the site, reachable by clicking an "EN" button from any
Persian page and landing on the matching English page. This folder is the
complete, standalone plan for that project — kept separate from `docs/`
(which covers the Persian site that's already live) so nothing gets mixed up.

No prior plan for this existed anywhere in the project (confirmed by reading
every file in `docs/`) — this is being written from scratch, grounded in a
full audit of the real, current Persian codebase (see `01-current-site-audit/`).

## How to read this folder

Read in this order the first time:

1. **`01-current-site-audit/`** — what the Persian site actually is today:
   every template, every content type, and exactly how "bilingual-ready" it
   really is right now (more ready than you might expect — see below).
2. **`02-architecture-decision/`** — the single biggest decision: how the
   English version technically plugs into the existing site. One approach
   is recommended, with the alternatives and why they were rejected.
3. **`03-content-architecture/`** — once the approach is picked, how content,
   categories, and URLs actually work for two languages.
4. **`04-design-and-ltr-system/`** — the visual/CSS side: what already works
   for free, what needs a deliberate fix.
5. **`05-technical-implementation-plan/`** — the actual phased build plan,
   written in the same style as `docs/EXECUTION_PLAN.md`.
6. **`06-editorial-workflow/`** — how Farhad/staff will actually use this
   day-to-day once it's built.
7. **`07-qa-and-launch-checklist/`** — how we'll know it's actually done.
8. **`08-effort-and-risk/`** — rough sizing and the things most likely to go
   wrong.

## The single most important finding

The Persian site was already built with real bilingual discipline — this
isn't a from-scratch effort:

- Every user-facing string already goes through `__()`/`_e()` (349 calls in
  the theme, 392 in the plugin) — no hardcoded copy to hunt down.
- CSS is **100% logical properties** already — zero `margin-left`,
  `padding-right`, hardcoded `left:`/`right:` anywhere in `main.css` or
  `login.css`. Under `dir="ltr"`, most of the layout should just flip
  correctly with no CSS changes at all.
- English/Latin font tokens (`--font-en`, `--font-ui`, `--font-mono`) already
  exist and are already self-hosted (Inter, Newsreader, JetBrains Mono) —
  just not applied document-wide yet.
- A `shcore_language` meta field (`fa`/`en`) already exists on four content
  types, and a `shcore_translation_id` pairing field already exists on
  articles — both currently inactive, both exactly what this project needs,
  just not finished or extended to everything.
- The SEO code already prints an hreflang tag pair — currently pointing at
  itself (inert placeholder) — ready to become real once English pages exist.

In short: this was planned for from day one. The work ahead is finishing
what's already scaffolded, not inventing a new system.

## One governance step this plan depends on

`CLAUDE.md` §1 currently says, in writing: *"Any future English rollout is
explicitly out of scope... do not scaffold... speculatively."* That rule was
correct when it was written and needs to be formally replaced, not quietly
ignored, before a single line of this plan gets built. The exact proposed
replacement text is in `05-technical-implementation-plan/claude-md-rule-change.md`
— that file is literally step one of the build.

## Code comments going forward (per Farhad's instruction)

Once this plan moves into actual code: **comments stay to 1–2 lines, plain
language, no jargon.** A future developer with no context should be able to
read a comment and understand what a piece of code does and how it connects
to the rest of the site — not have to decode a technical explanation. Full
reasoning/history (the "why," the discarded alternatives, the debugging
story) belongs in `docs/CHANGELOG.md`, the same convention this project
already uses for the Persian build. This plan's own documents are written in
full detail on purpose — code comments won't be.
