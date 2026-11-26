# Language field and pairing — how one post knows its English (or Persian) twin

## The field itself

`shcore_language` already exists (`class-meta-fields.php`) on `document`,
`party_publication`, `party_document`, `post`. Plan:

- **Extend it to every content type that will carry English content**
  (which ones exactly is an open decision — see
  `open-decisions-needing-client-input.md` — but at minimum `issue`,
  `editors_pick`, `announcement`).
- **Default stays `fa`** for every existing post (nothing already published
  silently becomes "unlabeled") — this field only needs a value going
  forward, existing content doesn't need a bulk migration.
- Stored exactly as today: a plain string meta value, `fa` or `en`.

## The pairing field

`shcore_translation_id` already exists but only on `post`. Plan: generalize
it into a field every translatable content type gets, storing the **post ID
of this post's counterpart in the other language**. Both directions get
written at once (if post A points to post B, post B is also updated to
point back to post A) — a one-step admin action, not two.

**Where this gets set:** a single field in the post-edit screen (metabox),
same pattern as every other custom field in this project
(`class-meta-fields.php`'s existing style) — a dropdown/search field to pick
"this post's translation," pre-filtered to posts of the same type in the
opposite language. No new admin screen needed.

## What happens when you click "EN" (or "FA")

This is the actual behavior the language-switcher button executes, on every
single page type:

1. **On a single post/page that has a pairing set:** go straight to the
   paired post's URL, in the other language's URL space.
2. **On a single post/page with no pairing set yet:** go to the equivalent
   *archive* page in the other language if one exists (e.g. an
   untranslated article falls back to `/en/` homepage or the nearest parent
   archive, not a dead link) — never a 404, never silently do nothing.
3. **On an archive/taxonomy page:** go to the same archive in the other
   language (e.g. Persian `topics/afghanistan` → English
   `en/topics/afghanistan`) — these pages always exist in both languages
   once the taxonomy-translation work (`taxonomy-translation.md`) is done,
   since they're structural, not individually authored content.
4. **On the homepage:** go to the other language's homepage. Always works.

This logic lives in one shared function (something like
`shola_get_language_switch_url()`), called from one place in `header.php` —
not duplicated per template.

## Content types that do NOT get this field

`hero_section` and `masthead_section` are admin-only configuration rows
(they control which homepage layout is active), not content a reader ever
sees as "a Persian page" or "an English page" by itself. These likely don't
need `shcore_language` at all — what they configure (which hero layout is
active, which publication the hero rail pulls from) can simply be read the
same way regardless of language, with the *content it points to* (the
actual issue, the actual picks) being what's language-filtered. Flagged as
an open decision in case the client wants a genuinely different homepage
composition for English — see `open-decisions-needing-client-input.md`.
