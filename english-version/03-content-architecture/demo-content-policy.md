# Demo content policy

Resolved 2026-11-27: the client writes the real English content
themselves, once the site is built. Until then, Claude adds **clearly
marked placeholder content** to every content type during the build, so
the English site looks genuinely full and finished when reviewed — not
empty sections, not "0 results," not obviously unfinished — while making
it completely unambiguous which content is real and which is a stand-in
to be replaced.

## How demo content is marked

Every piece of demo content gets a dedicated flag —
**`shcore_is_demo_content`** (boolean post-meta, same registration pattern
as every other field in `class-meta-fields.php`) — set `true`. This is a
real, queryable, bulk-manageable marker, not just a note in a title:

- **Admin list column**: every content type's wp-admin list table gets a
  "نمونه" (Demo) column/badge next to any post with this flag set — same
  pattern already used for the `hero_section`/`masthead_section` active-
  status column, so Farhad can see at a glance, across the entire site,
  exactly what's real and what's a placeholder.
- **Admin notice on the edit screen**: opening a demo post shows a plain,
  impossible-to-miss notice at the top — "This is placeholder demo
  content — replace it with the real English version."
- **Bulk find/replace is trivial**: `get_posts( array( 'meta_key' =>
  'shcore_is_demo_content', 'meta_value' => '1' ) )` across any content
  type finds every piece of demo content on the site in one query, for a
  bulk cleanup pass once real content exists.
- **Title convention, as a second, human-visible signal** (belt-and-
  braces, not a substitute for the real flag above): every demo post's
  English title is prefixed `[نمونه] ` — e.g. `[نمونه] World Food Prices
  Hit a Decade High` — so it's unmistakable even in a quick list glance,
  an email, or a screenshot, not just in a query.

## What demo content is for for — and isn't

- **Is for:** proving the hide-if-empty infrastructure actually works
  (a nav item/homepage section appears the instant it has content — demo
  content is exactly as real as any other content from that mechanism's
  point of view), and giving the client something concrete to look at
  rather than an empty shell when they review the English version.
- **Is NOT:** real editorial content, real translations, anything that
  should stay on the live site once the client starts writing for real.
  Demo content is deliberately generic/placeholder in substance (a real
  but unremarkable sentence or two — not lorem ipsum, which would look
  obviously broken, but not real journalism either), never copied or
  adapted from real Persian articles (that would misrepresent translated
  content as already done).

## Removal, once real content exists

Not an automatic/scheduled thing — Farhad (or the client) deletes demo
posts manually from wp-admin as real replacements go up, using the admin
column/notice above to know exactly what's left. No code is needed to
"turn off" demo content — it behaves exactly like any other English post
in every system (hide-if-empty, the language switcher, search, sitemaps)
precisely because it *is* an ordinary post, just flagged for visibility.
