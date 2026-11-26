# Open decisions needing client input

Matching this project's existing convention (see `CLAUDE.md` §9's rule on
the IA document's open decisions): these get flagged clearly, built against
a documented working assumption where a decision is needed to keep moving,
and never silently resolved. Nothing here should be decided by guessing.

## 1. ~~Does every content type need an English version, or only some?~~ — RESOLVED 2026-11-26

**Answer: every content type, full stop.** The client wants a complete
structural mirror of the Persian site — see
`complete-mirror-and-empty-state-rule.md` for the full rule and how it's
built (hide a section until it has real English content, show it the
moment it does). This also resolves questions 2 and 3 below.

## 2. ~~Do تراکت (leaflets) need an English version at all?~~ — RESOLVED 2026-11-26

Leaflets get the same full English capability as everything else. Whether
the client ever actually publishes English leaflet content is now
irrelevant to the build — if they don't, the Leaflets nav item simply never
appears on the English site, per the hide-if-empty rule. No separate
decision needed.

## 3. ~~Does the homepage need the same layout/composition in English?~~ — RESOLVED 2026-11-26

Yes, exactly mirrors the Persian homepage's structure (same hero-layout
mechanism, same panel set) — each panel individually hidden/shown per the
same rule.

## 4. ~~Who writes the English content, and on what timeline?~~ — RESOLVED 2026-11-27

**Answer:** the client will write the real English content themselves,
once the site is built. Until then, Claude populates **clearly-marked demo
content** in every content type — enough that the site looks genuinely
full and finished for visual review, not empty/broken — using the
hide-if-empty infrastructure itself to prove it works (a section
appearing the moment *any* English content exists, demo or real). Full
policy, including exactly how demo content is marked so it's trivial to
find and remove later: `demo-content-policy.md` in this same folder.

## 5. ModamPro's Latin character coverage — DEFERRED, Claude flags when it actually matters

**Answer:** decide later, not now. Per Farhad's instruction: Claude will
raise this again right before it actually becomes a blocking decision —
specifically, at the start of Phase 3 (the LTR CSS pass,
`04-design-and-ltr-system/`), where the nav/masthead font choice for
English pages is actually implemented — rather than deciding it
unprompted now or letting it block anything in the meantime. Until then,
English pages simply use the font stack's own existing fallback
(ModamPro → Farhang2 → system sans-serif), which already degrades
gracefully either way.

## 6. ~~Date format for English readers~~ — RESOLVED 2026-11-27

**Answer: Gregorian dates on English pages** — more legible to an
English-speaking, non-Afghan audience, which is the whole point of the
English version existing. Persian pages are completely unaffected and
keep Jalali dates exactly as today. Implementation detail: the existing
`is_rtl()`-gated Jalali-date logic (`inc/template-tags.php`) needs to
additionally check the page's language context (not just `is_rtl()`
alone) once that context exists — see
`url-structure-and-routing.md`.
