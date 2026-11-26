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

## 4. Who writes the English content, and on what timeline?

This plan covers making English content *possible* to add and *correctly
displayed* once added — it does not cover the translation work itself
(who translates articles, how quickly, whether every new Persian article
also gets an English version going forward or only selected ones). That's
an editorial/staffing decision for Farhad and the client, covered at a
process level in `06-editorial-workflow/`, not a technical one.

## 5. ModamPro's Latin character coverage

`--font-nav` (the stylized font used for brand/nav wordmarks) falls back to
`"ModamPro", "Farhang2", ui-sans-serif, sans-serif` — **needs verification**
whether ModamPro's font file actually includes Latin glyphs at all, or
whether it's Persian-script-only (common for decorative branded fonts).
If it has no Latin coverage, the browser already falls through to the next
font in that stack automatically (no bug), but it's worth confirming on
purpose rather than discovering it by accident — a 10-minute check, not a
blocking decision.

## 6. Date format for English readers

The Persian site shows dates in the Jalali (Persian) calendar via the
Persian Calendar plugin (`CLAUDE.md` §3 whitelist). **Open question:**
should English pages show Gregorian dates instead (more legible to an
English-speaking, non-Afghan audience) or keep Jalali dates for
consistency/authenticity? Either is a reasonable editorial choice — this
needs the client's preference, not an assumption either way.
