# The complete-mirror rule (decided 2026-11-26, overrides earlier "pick and choose" assumption)

This replaces the "maybe only some content types need English" framing that
was in the original open-decisions list. Farhad relayed the client's actual
position, and it's clearer and stronger than the original working
assumption:

> The English version must be a **complete mirror** of the Persian site —
> every section, every content type, every piece of navigation that exists
> in Persian structurally exists in English too. Nothing should ever be
> something the client can later point at and say "you didn't build this."

## The rule, precisely

**Every content type, every taxonomy, every homepage section, and every
nav item gets full English support, with no exceptions carved out in
advance.** But a section only *displays* once it actually has at least one
English item in it:

- If an English nav item would point at a content type with **zero**
  published English content right now, that nav item is **hidden**, not
  shown-but-empty.
- The moment someone publishes the first English item of that type in the
  dashboard, the nav item (and every other place that content type appears
  — homepage panels, sitemaps, search) **appears automatically.** No
  redeploy, no code change, no flipping a switch — it's a live query, not a
  hardcoded list.
- Nothing is ever visibly "missing" or "broken" — there is no empty grid,
  no "0 results" panel on the homepage, no dead nav link. A section either
  shows real content or doesn't exist on the page at all.

This is a stronger, better answer than the original plan's "maybe skip
leaflets, maybe skip library documents" framing — it resolves three of the
six original open questions at once:

- **Open question 1** (does every content type need English): **yes,
  every one** — the technical capability is never optional per content
  type, only the display is conditional on content existing.
- **Open question 2** (do leaflets need English): **the question
  dissolves** — leaflets get full English capability like everything else;
  if the client never adds English leaflet content, the Leaflets nav item
  simply never appears on the English site, with zero extra work and zero
  risk of looking incomplete.
- **Open question 3** (should the English homepage mirror the Persian
  one): **yes, exactly** — same hero-layout mechanism, same panel set,
  each panel individually subject to the same hide-if-empty rule.

## Where this gets built (one shared mechanism, not six separate checks)

Per this project's own established pattern (one shared helper function
reused everywhere, like `shola_get_featured_image()` or
`shola_render_hero_body()`, rather than duplicating logic per template):
one function, something like `shola_lang_has_content( $args )`, wraps a
`WP_Query`/`get_posts()` existence check (post type + taxonomy term +
`shcore_language = en`, `posts_per_page => 1`, just checking "is there at
least one"). Every place that needs a hide-if-empty decision calls this one
function:

| Where | What it checks |
|---|---|
| Primary nav menu builder | Does this nav item's content type have ≥1 English item? |
| `front-page.php` homepage panels | Library shelf, Most Viewed, Editor's Picks, Translation row, Announcement spotlight — each independently |
| Topics/Publications/Collections listing pages | Does this specific term have ≥1 English-tagged item? (hides the term link, not the whole listing page) |
| Static pages (About/Contact) | Has the English version of *this specific page* been published? (a direct pairing check, not a content-type count) |

## What this does NOT mean

- It does **not** mean every piece of existing Persian content needs an
  English translation before launch — the site launches with whatever
  English content exists at the time, and grows from there. "Complete
  mirror" describes the site's *structure and capability*, not a
  requirement that the entire back-catalog be translated on day one.
- It does **not** mean building a visibly "under construction" or "coming
  soon" placeholder anywhere — the rule is specifically to hide rather than
  show an empty/apologetic state, so the English site always looks
  finished for exactly what content currently exists in it.

## What's still genuinely open

Questions 4, 5, and 6 in the original open-decisions list are unaffected by
this and remain open (editorial timeline/staffing, ModamPro's Latin glyph
coverage, Gregorian vs. Jalali dates for English readers).
