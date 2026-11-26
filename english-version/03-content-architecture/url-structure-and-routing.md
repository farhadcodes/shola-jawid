# URL structure and routing

## The rule

**Persian URLs never change.** Every URL that works today keeps working
exactly as it does today — no `/fa/` prefix gets added, nothing about
existing permalinks moves. English gets a `/en/` prefix added in front of
the exact same URL shape.

| Persian (unchanged) | English (new) |
|---|---|
| `sholajawid.com/` | `sholajawid.com/en/` |
| `sholajawid.com/topics/afghanistan/` | `sholajawid.com/en/topics/afghanistan/` |
| `sholajawid.com/publications/shola-jawid/` | `sholajawid.com/en/publications/shola-jawid/` |
| `sholajawid.com/library/classics/` | `sholajawid.com/en/library/classics/` |
| `sholajawid.com/some-article-slug/` | `sholajawid.com/en/some-article-english-slug/` |
| `sholajawid.com/about/` | `sholajawid.com/en/about/` |

(Taxonomy archive URLs above use the English term slug from
`taxonomy-translation.md`, not the Persian one — that's the whole point of
that field.)

## How this gets built technically

1. **A custom rewrite rule** added early (`init` hook, same place
   `class-post-types.php` already adds its own custom rewrite tags for
   issue/document permalinks), matching anything starting with `en/` and
   internally routing it to the exact same query WordPress would already
   build for the non-prefixed version — plus one new query var,
   `shola_lang`, set to `en`.
2. **One early hook** (something like `shola_set_language_context()`, on
   `parse_request` or similar) reads that query var and sets a single,
   site-wide "current language" value for the rest of the request — every
   other piece of code (templates, the language switcher, SEO tags) asks
   this one place, rather than each re-deriving it from the URL
   independently.
3. **Every archive/taxonomy/search query gets one additional filter**
   (`pre_get_posts`, the standard WordPress hook for this) adding
   `meta_query: shcore_language = [current language]` — this is the one
   piece of logic that actually splits Persian content from English content
   in every listing anywhere on the site.
4. **Single posts/pages don't need the filter** — a single post's language
   is just read directly from its own `shcore_language` meta once the URL
   resolves to it; WordPress's own URL-to-post resolution already works
   fine with the `en/` prefix stripped by step 1.

## Canonical URLs and the SEO class

`class-seo.php`'s `get_canonical_url()` needs one addition: when
`shola_lang` is `en`, prefix the canonical URL with `/en/`. Its existing
hreflang code (currently printing a self-referential inert pair) gets
rewired to use the real pairing field from `language-field-and-pairing.md`
— full detail in `05-technical-implementation-plan/seo-and-sitemap-updates.md`.

## What does NOT get an English URL

`hero_section` and `masthead_section` are already `public => false`,
admin-only config types with no front-end URL at all today — nothing
changes there. `leaflet` is flagged as an open decision
(`open-decisions-needing-client-input.md`) on whether it needs an English
mirror at all before building its routing.
