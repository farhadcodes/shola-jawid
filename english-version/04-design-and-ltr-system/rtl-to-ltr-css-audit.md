# RTL → LTR CSS audit

## Foundational fix, found before any of this could matter (2026-11-28)

Before anything below was relevant, a much more basic problem surfaced:
`is_rtl()` — which WordPress core's own `language_attributes()` (the
`<html dir="...">` attribute itself), the Persian Calendar plugin's
Jalali-date conversion, and potentially other code all branch on — is a
**global, whole-site-locale check**, not aware of which request is in
the `/en/` URL space. Without a fix, every English page would still
render `dir="rtl"` and still show Jalali dates, making the rest of this
phase meaningless.

Fixed in `class-language.php` using WordPress's own native
`switch_to_locale( 'en_US' )` for any `/en/`-prefixed request. A real bug
was found and fixed getting this right, not assumed correct from reading
the code: traced live with a temporary debug probe logging `is_rtl()` at
every bootstrap hook — a single switch at `plugins_loaded` worked in the
moment but was silently reset by WordPress core's own locale reload
between the `setup_theme` and `after_setup_theme` actions. Fixed by
reapplying the switch a second time at `after_setup_theme` priority
999 — late enough to survive that reset, still before Persian Calendar's
own `init` (priority 10) check.

Verified directly: `/en/` pages render `<html lang="en-US">` with no
`dir="rtl"`/`rtl` body class; `date_i18n()` returns real Gregorian
English dates there; Persian pages are completely unaffected; wp-admin
loads clean (explicitly excluded from the switch). One separate finding,
deliberately not folded into this fix: the masthead's "live" date widget
is fetched via its own REST endpoint
(`/wp-json/shola/v1/masthead-date`) — a different request with no `/en/`
in its own URL, so it still shows Jalali regardless of page language
until separately made language-aware. Flagged as its own follow-up.

## The headline finding

`assets/css/main.css` and `assets/css/login.css` were both grepped in full
for every physical-direction CSS property: `margin-left`, `margin-right`,
`padding-left`, `padding-right`, bare `left:`, `right:`, `border-left`,
`border-right`, `text-align: left`, `text-align: right`, `float: left`,
`float: right`. **Zero matches in either file.** The one `left`/`right`
string that does appear (`main.css:5407`) is inside a code comment
explaining why `row-reverse` was deliberately used instead of a hardcoded
direction — i.e. even the comments already think in logical terms.

**What this means practically:** under `dir="ltr"`, logical properties like
`margin-inline-start`/`padding-inline-end`/`inset-inline-start` automatically
flip their real-world meaning (inline-start becomes left instead of right).
The vast majority of the site's layout, spacing, and alignment should
**render correctly in LTR with no CSS changes at all.** This is the single
biggest reason this project is realistic in scope rather than a rebuild.

## The one existing precedent

`main.css:849` — `[dir="ltr"] .masthead--two-tier { ... }` — already exists,
for a masthead layout variant that apparently needed one explicit tweak
beyond what logical properties handle automatically. This is useful as a
worked example of the pattern: **default styles stay direction-agnostic;
only the specific exceptions below get an explicit `[dir="ltr"]` (or
`html[lang="en"]`) override, nothing more.**

## What logical properties do NOT handle automatically — the real LTR work

These need deliberate attention during the template-by-template pass
(`component-by-component-ltr-notes.md`):

1. **Directional icons/arrows.** Any SVG or icon that visually points a
   direction to mean "next," "forward," "back," or similar (chevrons,
   arrows in pagination, breadcrumb separators) needs mirroring under LTR —
   logical CSS properties handle spacing and position, not which way an
   arrow glyph points. Fix: `transform: scaleX(-1)` on the icon under
   `html:not([dir="rtl"])`, or a direction-aware icon variant, applied
   per-icon as they're found. **Done, 2026-11-28, for every instance found
   so far:** the hero filmstrip arrows, and the leaflet lightbox's
   prev/next chevrons (`.leaflet-lightbox-prev`/`-next svg`) — the latter
   already positioned correctly via `inset-inline-start`/`-end`, but its
   SVG path needed the same mirroring, same root cause as the filmstrip.
   Pagination's `prev_text`/`next_text` are plain text arrow *characters*,
   not SVGs, so they got their own fix instead — see item below.
2. **Pagination arrow characters.** `paginate_links()`'s `prev_text`/
   `next_text` were hardcoded `'→'`/`'←'` in 9 templates (10 call sites) —
   a plain-text glyph, not an SVG, so `scaleX(-1)` doesn't apply. **Done,
   2026-11-28:** two new direction-aware helpers,
   `shola_pagination_prev_text()`/`shola_pagination_next_text()`
   (`inc/template-tags.php`, both a one-line `is_rtl()` ternary), used
   everywhere instead of the literal characters. Verified live with a
   temporary debug probe: Persian unchanged (`→`/`←`), `/en/` correctly
   swapped (`←`/`→`).
3. ~~**Drop caps.**~~ **Corrected 2026-11-28 — not this theme's concern at
   all.** The original audit mis-attributed this to `main.css`; a direct
   grep found zero matches there. The RTL-aware `.has-drop-cap` rule
   actually lives in **WordPress core's own** injected block-library CSS,
   which already ships a correct `body.rtl` override. Now that `is_rtl()`
   is correctly request-aware (the foundational fix above), core's own
   existing CSS already handles this correctly for English pages with
   zero theme changes needed.
4. ~~**Number and digit formatting.**~~ **Resolved by the foundational fix
   above**, not a separate task — Persian Calendar's Persian-digit
   conversion is gated on the same `is_rtl()` check the date-conversion
   fix already corrects, so English pages now get standard Arabic-numeral
   digits automatically, with no additional code needed.
5. **Quote marks / typographic punctuation**, if any template uses
   direction-specific quote characters (needs checking per-template, not
   assumed either way).

## What does NOT need to change

- Brand colors, spacing tokens, type scale — completely language-agnostic,
  zero changes.
- The flag/logo artwork's -25° tilt — purely decorative, unrelated to
  reading direction, left exactly as-is.
- Every template's actual HTML structure — no template needs restructuring,
  only `dir`/`lang` attribute wiring and the handful of exceptions above.
