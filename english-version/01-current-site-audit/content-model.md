# Content model — the Persian site's real data shape

Source: direct read of `class-post-types.php`, `class-taxonomies.php`,
`class-meta-fields.php` in `wp-content/plugins/shola-core/includes/`,
2026-11-26. This is the exact shape the English-version plan has to fit
into — not a redesign of it.

## Custom post types

| CPT slug | Has an image/excerpt? | Has a real archive page? | Belongs to which taxonomy |
|---|---|---|---|
| `issue` (نشریه) | image + excerpt | no (lives under `publications/%publication%`) | `publication` |
| `document` (سند کتابخانه) | image + excerpt | no (lives under `library/%collection%`) | `collection` |
| `party_publication` | image + excerpt | no (plain `party-publications`) | none |
| `party_document` | image + excerpt | no (lives under `party-documents/%party_document_category%`) | `party_document_category` |
| `leaflet` (تراکت) | image only | no (plain `leaflets`) | none |
| `hero_section` | no | admin-only, not public | none |
| `masthead_section` | no | admin-only, not public | none |
| `announcement` (اطلاعیه) | no | yes, real archive | none |
| `editors_pick` (گزیده) | image + excerpt | yes, real archive | `editors_pick_category` |

Plain articles/notes use WordPress's native `post` type (no CPT), tagged with
the `topic` taxonomy.

## Custom taxonomies

| Taxonomy | Applies to | Fixed vocabulary size |
|---|---|---|
| `topic` | `post` | 9 terms |
| `publication` | `issue` | 2 terms, each with 4 "دوره" (period) child terms |
| `collection` | `document` | 4 terms |
| `party_document_category` | `party_document` | starts with 2, grows over time |
| `editors_pick_category` | `editors_pick` | grows over time |
| `report` | `post` | 1 term, used internally (not public-facing) |

All the small, fixed-size vocabularies (`topic`, `publication`, `collection`)
are exactly the kind of thing that should get an English **label**, not a
whole separate English taxonomy tree — see
`03-content-architecture/taxonomy-translation.md`.

## Meta fields already in place

The two fields that matter most for this plan already exist:

- **`shcore_language`** — already on `document`, `party_publication`,
  `party_document`, and `post`. Values `fa`/`en`, defaults to `fa`. The `en`
  option is scaffolded but nothing in the site currently does anything with
  it.
- **`shcore_translation_id`** — already on `post` only. Described in its own
  code comment as "inactive field for linking a bilingual pair." This is
  precisely the pairing mechanism the English version needs — currently
  built for articles only, needs generalizing to every content type that
  gets an English counterpart.

Every other meta field (issue number, PDF attachment ID, subtitle, byline,
etc.) is language-neutral — it doesn't need to change for English, it just
needs to hold English values when the post it belongs to is the English
version of something.
