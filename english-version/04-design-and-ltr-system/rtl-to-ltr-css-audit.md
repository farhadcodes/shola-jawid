# RTL → LTR CSS audit

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
   `[dir="ltr"]`, or a direction-aware icon variant, applied per-icon as
   they're found.
2. **Drop caps.** `main.css` already has RTL-specific drop-cap handling
   (`.has-drop-cap:not(:focus):first-letter { float: right; ... }` with a
   separate `body.rtl` rule) — this is a real physical `float` today,
   specifically because drop caps are one of the few CSS features with no
   logical equivalent. Needs an explicit LTR counterpart
   (`float: left` under `[dir="ltr"]`), not a logical-property fix, since
   none exists for this property.
3. **Number and digit formatting.** Persian content uses Persian-script
   digits (۱۲۳) via the Persian Calendar plugin and `wp_date()`/
   `date_i18n()`. English pages need standard Arabic-numeral digits (123) —
   this is a `is_rtl()`/language-context check at the PHP level, not CSS,
   already using the correct pattern (`is_rtl()`, never `get_locale()`) per
   the existing code comment in `inc/template-tags.php:338` — the English
   version just needs that same logic extended to check the new language
   context from `url-structure-and-routing.md`, not `is_rtl()` alone.
4. **Quote marks / typographic punctuation**, if any template uses
   direction-specific quote characters (needs checking per-template, not
   assumed either way).

## What does NOT need to change

- Brand colors, spacing tokens, type scale — completely language-agnostic,
  zero changes.
- The flag/logo artwork's -25° tilt — purely decorative, unrelated to
  reading direction, left exactly as-is.
- Every template's actual HTML structure — no template needs restructuring,
  only `dir`/`lang` attribute wiring and the handful of exceptions above.
