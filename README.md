# شعله جاوید (Shola Jawid) — WordPress Theme

A WordPress theme + companion plugin for شعله جاوید (Shola Jawid), a
Persian-language editorial publishing platform. Bilingual-ready scaffold;
Persian (fa, RTL) is the only active language in this phase.

This is a **faithful, pixel-accurate conversion** of the finished
`shola-jawid-ui` v6 "Quiet Press" static prototype into a working WordPress
theme (`shola-jawid`) + content-model plugin (`shola-core`). Nothing about
the design is being changed, improved, or reinterpreted during conversion —
see `CLAUDE.md` for the full binding rules governing this build.

## Repository structure

```
wp-content/
├── themes/shola-jawid/    ← templates, assets/css, assets/js — all visual code
└── plugins/shola-core/    ← CPTs, taxonomies, meta fields — content model only
docs/
├── EXECUTION_PLAN.md      ← phased build plan
├── CHANGELOG.md           ← decisions log (not routine commits)
└── IA-reference/          ← read-only copies of brand guide, IA doc, Aeon analysis
CLAUDE.md                  ← binding rules for this repo (read this first)
```

## Setup for a new developer

1. Install [LocalWP](https://localwp.com/) (or any local WP env, PHP 8.1+, WP 6.4+).
2. Clone this repo.
3. Point your local site's `wp-content/themes/shola-jawid` and
   `wp-content/plugins/shola-core` at the matching folders in this repo
   (symlink or directory junction on Windows — see `docs/CHANGELOG.md` for
   the exact mechanism used in this project).
4. Activate the `shola-core` plugin, then the `shola-jawid` theme.
5. Run `phpcs --standard=phpcs.xml.dist` before committing (requires WPCS —
   see `.github/workflows/lint.yml` for the exact toolchain).

## Credits

Designed and developed by Farhad Farhaad
Email: info.farhaad@gmail.com
GitHub: https://github.com/farhadcodes

## License

GPLv2 or later — see [LICENSE](LICENSE).
