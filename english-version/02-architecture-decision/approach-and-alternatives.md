# The architecture decision

This is the one decision everything else in this plan depends on. It needs
Farhad's and the client's sign-off before Phase 1 of the build starts (see
`05-technical-implementation-plan/phase-by-phase-build-plan.md`).

## The requirement, in plain terms

"When people click the English button, they should go there and see
everything in here" — meaning: a real English mirror of the site, reachable
page-for-page (click EN on an article, land on that article's English
version if it exists), same visual design, mirrored for LTR reading.

## Recommended approach: one WordPress install, URL-prefixed, paired content

**One site, one database, one admin login — the same install running right
now.** English content lives in the exact same post types and taxonomies as
Persian content already does, distinguished by the `shcore_language` meta
field (already exists, just needs extending to every content type). Every
post that has an English counterpart gets paired to it via a generalized
version of the existing `shcore_translation_id` field. English pages live
under a `/en/` URL prefix (`sholajawid.com/en/topics/afghanistan/`); Persian
URLs don't change at all — no `/fa/` prefix, exactly as today.

**Why this is the right call:**

- It's a straight continuation of work already started. `shcore_language`
  and `shcore_translation_id` exist today for exactly this purpose — they
  were clearly planned for, just never finished. Building anything else
  would mean abandoning real existing scaffolding.
- It's the only approach that satisfies "click EN, land on the matching
  page" cleanly — the pairing field *is* that link.
- Zero risk to the Persian site's URLs, SEO, or backlinks — nothing about
  how Persian content is stored or addressed changes.
- One admin, one set of user accounts, one database to back up — matches
  `CLAUDE.md`'s existing "as plugin/complexity-independent as possible"
  philosophy, and Farhad doesn't have to maintain two separate WordPress
  installs going forward.
- No multilingual plugin (Polylang, WPML) needed — stays inside
  `CLAUDE.md` §3's existing "fixed whitelist only" policy, same as every
  other decision made on this project so far. This is custom code, built
  the same way the rest of this theme/plugin already is.

## Alternatives considered and rejected

### A. Install Polylang or WPML

Rejected outright — `CLAUDE.md` §3 is explicit that only a fixed,
documented whitelist of plugins may ever be installed on this project, and
neither is on it. Adding either would also be the first real departure from
this project's whole "plugin-independent as possible" philosophy, which has
held for every other feature built so far (custom SEO, custom security
hardening, custom login protection, custom image optimization — all
hand-built rather than reached for a plugin). Also: both plugins are built
around rewriting WordPress's routing and admin UI in ways that would fight
with this project's already-extensive custom rewrite logic (issue
permalinks, custom login URLs, the whole `class-post-types.php` rewrite
layer) — a real, nontrivial integration risk, not just a policy objection.

### B. WordPress Multisite (a second, networked site for English)

Rejected. Multisite would mean a second, separate set of database tables
for all content, requiring the pairing link to cross a site boundary
(technically possible, but meaningfully more complex than a same-site meta
field), a hosting-plan check with Hostinger (Multisite isn't automatically
available on every plan/tier), and network-activating the plugin across
both sites. It solves a problem this project doesn't have (needing fully
independent sites with different owners/branding) while adding real
infrastructure overhead for a problem it does have (two languages of the
same site). The one-install approach gets the same outcome with none of
this overhead.

### C. A second, fully separate WordPress installation (e.g. a subdomain)

Rejected for the same core reason as B, more acutely: two separate admin
logins, two separate databases to keep in sync for anything shared (the
hero/masthead singleton configuration, brand assets, security hardening,
future feature work — all of it would need to be built and maintained
twice). It also doesn't actually satisfy the "click EN, land on the
matching page" requirement any better than the single-install approach —
it would still need a pairing mechanism between two separate installs,
which is strictly harder to build and keep correct than a pairing field
within one.

## What "one install, URL-prefixed" means concretely

- `sholajawid.com/topics/afghanistan/` → Persian archive (unchanged).
- `sholajawid.com/en/topics/afghanistan/` → English archive, same
  taxonomy term, same template file (`taxonomy-topic.php`), just querying
  for `shcore_language = en` posts and printing the term's English label
  instead of its Persian one.
- A single article that has both a Persian and an English version: the
  Persian post and the English post are two separate `post` rows, paired to
  each other via the (generalized) `shcore_translation_id` field. Clicking
  EN on the Persian version takes the reader straight to the English post's
  URL. If no English version exists yet for a given piece of content,
  clicking EN falls back to the English homepage (never a 404, never a
  broken link) — exact fallback behavior is specified in
  `03-content-architecture/language-field-and-pairing.md`.

Full routing mechanics (the actual rewrite rule, the query var, canonical
URL handling) are in
`03-content-architecture/url-structure-and-routing.md`.
