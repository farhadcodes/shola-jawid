# Open decisions needing client input

Matching this project's existing convention (see `CLAUDE.md` §9's rule on
the IA document's open decisions): these get flagged clearly, built against
a documented working assumption where a decision is needed to keep moving,
and never silently resolved. Nothing here should be decided by guessing.

## 1. Does every content type need an English version, or only some?

**Working assumption, pending confirmation:** articles (`post`), issues
(`issue`), and the About/Contact pages are the real priority — these are
what a first-time English-speaking visitor actually reads. Library
documents, party publications, and party documents are large archives of
often decades-old material; translating all of it is a much bigger editorial
job than building the technical capability for it.

**Recommendation:** build the *capability* for every content type (the
`shcore_language` field, the pairing field, the routing) everywhere it
plausibly matters, but don't treat "every single existing Persian document
needs an English translation before launch" as a requirement — content gets
added to the English site incrementally, same as the Persian site's own
content already grows incrementally.

## 2. Do تراکت (leaflets) need an English version at all?

Leaflets are street-level agitation posters/flyers — image-only content,
often tied to a specific local event or audience. **Open question for the
client specifically:** is there any English-language leaflet material, or
is this section Persian-only by nature? If Persian-only, the English site
simply doesn't show a Leaflets link in its navigation — not a missing
feature, a deliberate scope choice.

## 3. Does the homepage need the same layout/composition in English?

The Persian homepage's hero section is one of 7 configurable layouts
(`shcore_hero_layout`), chosen via an admin singleton. **Working
assumption:** English homepage mirrors whichever layout is active in
Persian, pulling from English-language content instead. **Open question:**
does the client want the English homepage to potentially look different
structurally (e.g. feature different content, a different hero layout) from
day one, or should it always mirror the Persian homepage's current choice
automatically?

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
