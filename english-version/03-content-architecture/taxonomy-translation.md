# Taxonomy translation — one set of categories, two labels

## The decision

Every taxonomy that matters here (`topic`: 9 terms, `publication`: 2 terms
+ 8 دوره children, `collection`: 4 terms) has a small, fixed vocabulary that
barely ever changes (`create_default_terms()` in `class-taxonomies.php`
seeds them once, by design). Because of that:

**One shared term per category, with an English label stored as term-meta —
not a second, parallel English taxonomy tree.**

Example: the term "جهان" (World) keeps its one `term_id`. A new term-meta
field, `shcore_term_name_en`, holds `"World"`. A Persian article and an
English article tagged with the same real-world topic share the exact same
`term_id` — the only thing that changes between languages is which label
gets printed, and (for the URL) which **term slug** gets used.

## Why not a second taxonomy tree

- Doubles admin maintenance for zero real benefit — these vocabularies are
  fixed and rarely touched; a second tree to keep in sync forever is pure
  risk for no gain.
- Breaks the pairing logic in `language-field-and-pairing.md` for free: an
  English article and its Persian counterpart naturally share the same
  `term_id`, so "what topic is this English article about" and "what topic
  is the Persian original about" is automatically always the same answer.
- `wp_term_relationships` (which post has which term) doesn't care what
  language the post is in — this is already how WordPress works, the plan
  just uses that instead of fighting it.

## Term slugs for English URLs

Each term also needs an English **slug** (not just a label) for English
URLs to read naturally: `en/topics/afghanistan` rather than
`en/topics/%d8%a7%d9%81%d8%ba%d8%a7%d9%86%d8%b3%d8%aa%d8%a7%d9%86`. Plan:
a second term-meta field, `shcore_term_slug_en`, holding a plain ASCII slug
per term (e.g. `afghanistan`, `world`, `labor`, `women`). Set once per term,
same effort as naming the term in the first place.

## What gets built

- A small admin field (two fields really: English name + English slug) on
  every term's edit screen, for `topic`, `publication`, `collection`, and
  `party_document_category` — same pattern this project already uses for
  other admin-field additions (`class-category-manager.php` territory).
- A small helper function (`shola_get_term_label( $term, $lang )`) that
  every template calls instead of `$term->name` directly, so switching
  language is a one-line change per template, not a rewrite.
- The English URL rewrite rule (see `url-structure-and-routing.md`) reads
  `shcore_term_slug_en` to build/match the English archive URL.

## Content this does NOT apply to

`party_document_category` and `editors_pick_category` grow over time (not
fixed vocabularies) — the same term-meta mechanism still applies, it's just
that whoever adds a new category needs to remember to fill in the English
fields too. Worth a one-line reminder in the admin UI (a field description),
not a blocking requirement — an English label can always be added later
without breaking anything.
