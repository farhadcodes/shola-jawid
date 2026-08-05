# Phase 1.3 — inline concerns, RTL logical-property audit, JS-off verification

Audit only. Nothing in `03_UI_Design/` was modified while producing this.
Source: `03_UI_Design/shola-jawid-ui/` (23 top-level pages, `pages/_*.html`
partials, `pages/body-*.html` sources).

## 1. Physical CSS properties (RTL logical-property compliance)

**Clean — zero violations.** Grepped `main.css` and every inline `style="…"`
attribute across all 23 pages for `margin-left`/`-right`,
`padding-left`/`-right`, `border-left`/`-right`, and bare `left:`/`right:`
declarations. None found anywhere.

The only directional CSS in the stylesheet is the drop-cap's `float`
(`main.css:858` `float: right` for RTL, `main.css:864`
`[dir="ltr"] .prose > p:first-child::first-letter { float: left; … }`) —
this is the single documented exception the prototype's own README calls
out ("تنها استثنای جهت‌دار"), and it correctly flips per `[dir]`, so it's
not a bug. No action needed — CLAUDE.md §1/§9's logical-properties rule is
already fully satisfied by the source prototype.

## 2. Inline `style="..."` attributes

**Not clean — 440 instances across the 23 built pages**, ~90 distinct
value combinations (counts via `grep -c 'style="'`). None carry hardcoded
colors or physical-direction properties (spot-checked the full unique-value
list — everything is spacing/typography/flex layout, and colors that do
appear are all `var(--token)` references, never hex). Representative
high-frequency examples:

| Inline style | Count | Notes |
|---|---|---|
| `margin-inline-start:.5rem` | 46 | Small inline-badge spacing |
| `color:var(--paper);opacity:.4` | 46 | Masthead separator slashes |
| `margin-top:1rem` | 25 | Generic spacing after headers |
| `text-align:center` | 24 | A `.center` utility class already exists (main.css §20) but isn't used here |
| `padding-top:1.5rem;border-top:1px solid var(--line)` | 23 | Repeated section-divider pattern |
| `display:flex;justify-content:space-between;align-items:center` | 23 | Repeated row layout |

This is a real finding: `CLAUDE.md` §5 ("No hardcoded... spacing values in
PHP or inline styles... it applies a class; it does not `style=...`")
applies directly. Several of the most common inline patterns already have
matching utility classes sitting unused in `main.css` §20
(`.center`, `.row-between`, `.stack`) or are one step away from one (e.g.
`margin-top:1rem` needs a `.mt-sm`-equivalent at 1rem — the existing scale
only has `.mt-sm` at .75rem, `.mt-md` at 1.5rem, `.mt-lg` at 3rem, so 1rem
falls in a gap).

**Not fixed in this pass** — per your instruction, this is audit-only, and
fixing 440 call sites is real work with a visual-regression risk if any
value is transcribed wrong. Recommend addressing this at Phase 4 template
conversion time: each inline style becomes either an existing utility
class, a new small utility class added to `main.css` §20, or (for
patterns only used once) is left as a scoped rule in the relevant
component's CSS section — decided template-by-template as PHP conversion
happens, not as a blind find-replace now.

## 3. Inline `<script>` blocks

One single-line inline script repeated on all 23 top-level pages:

```html
<script>document.documentElement.classList.add("js")</script>
```

This is a standard, intentional progressive-enhancement pattern — it must
run synchronously and early (it's in `_shell.html`'s `<head>`, before
`main.js` loads via `defer`) so that CSS can gate the scroll-reveal
animation on `.js .reveal` (main.css:1265) rather than on `.reveal` alone.
If JS is disabled, this line never runs, no `.js` class is added, and
`.reveal` elements fall back to their un-gated default (visible, no
transform) — confirmed by reading the CSS rule directly. This is exactly
the kind of "small `wp_add_inline_script` case" CLAUDE.md §5 already
carves out as acceptable; it's necessary for the no-JS fallback to work,
not a violation. No other inline `<script>` blocks exist — every other
`<script>` tag is `<script src="assets/js/main.js" defer>`.

## 4. JS progressive-enhancement verification

Confirmed usable with JS off, by tracing each of `main.js`'s four
behaviors against their CSS fallback state:

1. **Popup menu** — `.menu-panel` defaults to `display:none`,
   `[data-open="true"]` (JS-only) reveals it. Without JS, `#menu-open`
   does nothing and the panel never opens. **This is not a lockout**: the
   footer partial (`_footer.html`) independently duplicates full site
   navigation as plain always-visible links — publications, all topics,
   library, announcements, about, contact — so every destination reachable
   from the popup menu is also reachable without JS via the footer. Verified
   by reading `_footer.html` directly, not assumed.
2. **Language toggle** — visual-only per its own code comment; inert
   without JS, degrades to simply not being interactive. Acceptable per
   `CLAUDE.md` §1 (a static/inert toggle "must not link anywhere real or
   404" — this one already does nothing destructive without JS).
3. **Scroll-reveal** — confirmed above: degrades to fully visible content
   with no animation, not hidden content.
4. **Reading-progress bar** — `.progress-bar` starts at `width:0` and is
   purely decorative; without JS it just never animates. No content
   depends on it.

**Conclusion: the site is genuinely usable with JS disabled** — nothing
found here needed fixing.

## 5. Additional finding outside the original 1.3 checklist: invalid nested-interactive markup

Not one of the four things Phase 1.3 asked me to check, but surfaced while
reading `_header.html` for the JS-off pass, and worth flagging rather than
silently fixing per your standing instruction:

`_header.html:6-12` nests an `<a href="search.html">` (the search icon
link) **inside** `<button id="menu-open">`:

```html
<button type="button" id="menu-open" ... >
  <svg ...></svg>
  <span>منو</span>
  <span aria-hidden="true" style="opacity:.5;margin-inline:.35rem">/</span>
  <a href="search.html" class="link-quiet" aria-label="جست‌وجو" style="display:inline-flex">
    <svg ...></svg>
  </a>
</button>
```

This is invalid HTML5 — a `<button>`'s content model excludes interactive
descendants, and an `<a>` is interactive content. Browsers won't reject it
outright, but it produces undefined/inconsistent behavior for keyboard and
screen-reader users (activating the nested link may also fire the parent
button's handler depending on event propagation; assistive tech announces
a confusing button-containing-a-link structure). This is a markup-hygiene
bug, not a visual one — fixing it (making menu-open and the search link
adjacent siblings instead of nested, same flex row, same visual result)
would not change how the page looks. **Not fixed here** — flagging per
your "ask, don't just change it" instruction. Recommend fixing during
Phase 4's `header.php` conversion (trivial there — restructure the markup
once, in PHP, rather than patching the static HTML now and having the
fix be silently overwritten by regenerating from `pages/_header.html` +
`build.py`).

## Phase 1 — status

- ☐ No inline-style attributes remain — **not satisfied**; 440 found,
  documented above, deferred to Phase 4 per the reasoning given (real work,
  regression risk, better done template-by-template).
- ☑ main.js confirmed progressive-enhancement-only, verified usable with
  JS off end-to-end (not just asserted).
- ☑ Zero physical-direction CSS properties remain (the one directional
  rule, the drop-cap float, is a documented, correctly-flipped exception).
- Additional, unplanned finding: invalid nested-interactive markup in
  `_header.html`, flagged for a Phase 4 fix, not touched now.

Given the inline-style item is not satisfied as a blanket "fix now"
condition, recommend treating Phase 1's Definition of Done as **met with
one explicitly deferred item** (inline styles → Phase 4, template-by-
template) rather than blocking Phase 2 on a mechanical pre-pass — this
mirrors how `CLAUDE.md` §9's "IA open decisions" are handled (documented
assumption, revisited at the right phase gate), and avoids a
higher-risk blind find-and-replace across 23 static files that are about
to be rewritten as PHP anyway.
