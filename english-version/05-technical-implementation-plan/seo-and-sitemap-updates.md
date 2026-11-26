# SEO and sitemap updates

Source: `wp-content/plugins/shola-core/includes/class-seo.php`, read in
full. This file already does almost everything needed — the changes below
are small and targeted, not a rewrite.

## hreflang tags — from inert to real

Today (`output_meta_tags()`, lines ~162-203):

```php
// both of these currently point at the SAME $canonical URL
<link rel="alternate" hreflang="fa" href="$canonical">
<link rel="alternate" hreflang="x-default" href="$canonical">
```

The class's own docblock already calls this "inert self-referential hreflang
scaffolding" — it was built knowing it would need exactly this fix one day.

**The change:** when the current page has a real paired URL in the other
language (via `language-field-and-pairing.md`'s pairing field/fallback
logic), print a real second `hreflang` tag pointing at it:

```php
<link rel="alternate" hreflang="fa" href="[persian URL]">
<link rel="alternate" hreflang="en" href="[english URL]">
<link rel="alternate" hreflang="x-default" href="[persian URL]">
```

`x-default` stays pointing at the Persian version — Persian is the site's
primary/default language per the (updated) `CLAUDE.md` §1.

**If no pairing exists for the current page** (an untranslated article, for
example): print only the one real `hreflang` tag for the language that
actually exists, no second tag — never point hreflang at a fallback/archive
URL, since that's not what hreflang means (it's specifically "this exact
page, translated," not "related content in another language").

## `og:locale`

Already correct and already language-aware:
`esc_attr( is_rtl() ? 'fa_AF' : get_locale() )` — once a request is in the
English language context, `is_rtl()` naturally returns `false` for that
request (since `dir="ltr"` pages aren't RTL), so this line needs **no
change at all**. Worth confirming live during QA rather than assumed.

## Canonical URLs

`get_canonical_url()` needs one addition: when the current request is in
the English language context, prefix the canonical path with `/en/`. This
is a small, local change — the function's existing branches (search,
taxonomy, post-type archive, front page) all stay structurally the same,
they just need the prefix applied once, at the point the final URL string
is assembled.

## Sitemap (`wp_sitemaps_*`)

The existing code only touches *which* providers/taxonomies appear in the
sitemap (removing `users`, `post_format`, native `category`) — it has no
language-specific logic at all today. **To check, not assume:** does
WordPress core's sitemap API automatically include English URLs once their
posts exist and the rewrite rules from `url-structure-and-routing.md` are
in place, or does it need an explicit filter to walk the `/en/` URL space
too? This needs a real check against a live WP-CLI/sitemap XML output once
Phase 1 infrastructure exists — not guessed at from documentation alone,
consistent with how every other technical claim in this plan was verified
against the actual code rather than assumed.
