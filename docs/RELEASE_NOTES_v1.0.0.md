# Shola Jawid — v1.0.0 Release Notes

**Release date:** 2026-08-11
**Scope:** Theme `shola-jawid` + companion plugin `shola-core`, both at version 1.0.0.

This is the first tagged release: a complete conversion of the `shola-jawid-ui-v6-quiet-press` static prototype into a working WordPress theme + content-model plugin, per `CLAUDE.md`'s binding rules, built through Phases 0–7 plus a final amendment round (Phases A–F below).

---

## What's included

**Content model** (`shola-core`)
- Custom post types: `issue` (PDF publication issues), `document` (library PDFs), `announcement`.
- Custom taxonomies: `topic` (6 fixed terms), `publication`, `collection` — all hierarchical, controlled vocabularies (not open tagging).
- Native WordPress `post` used for articles/notes, with a fourth classification layer via native `post_tag`.
- Full post-meta model per content type (issue number/volume/PDF/contents, document author/source/language, article byline/author-note/language/translation-id), all with real sanitize/auth callbacks and metabox UI.
- PDF upload restricted to a real MIME-type check (not just extension), enforced on every meta write.

**Templates** (`shola-jawid`)
- Every page type from the v6 prototype ported as a pixel-accurate PHP template: front page, single article/document/issue, all taxonomy archives, publications/library/topics listing pages, announcements archive, search, about, contact, 404.
- One shared card partial (`template-parts/cards/card.php`) plus issue-card/document-row variants, matching v6's actual component boundaries (not the originally-assumed single-partial design — corrected early, Phase 1).
- Custom permalink structure: articles under `/topics/{topic}/{slug}`, issues/documents nested under their parent term.

**Roles, SEO, search, performance**
- Least-privilege WordPress roles mapped to the IA doc's four roles (مدیر, سردبیر, نویسنده, همکار) — native capabilities, no role-management plugin.
- Custom SEO: title tags, meta description, Open Graph tags, canonical URLs, and `sitemap.xml` via WordPress core's `wp_sitemaps_*` API — no SEO plugin.
- Native search extended to include articles, notes, issues, and documents together, with real filter tabs (`result_type` query var).
- Self-hosted fonts (Farhang2, ModamPro, Inter, JetBrains Mono, Newsreader); Lighthouse performance baseline passed with no caching gap found.

**Security hardening**
- Wordfence (firewall/malware-scan/brute-force) installed per the `CLAUDE.md` §3 whitelist.
- `DISALLOW_FILE_EDIT`, security headers, and documented SSL/HTTPS enforcement.
- Documented daily-backup mechanism (host-level cron + `wp db export`).

**CMS-editable configuration (no developer needed for any of these)**
- Contact form subject dropdown (`موضوعات فرم تماس`) — plugin-owned option, replaces hardcoded CF7 form-tag values.
- Social media links (`شبکه‌های اجتماعی`) — 11 platforms (Telegram, X, Facebook, Instagram, WhatsApp, YouTube, LinkedIn, TikTok, Threads, Signal, Mastodon) plus automatic RSS, each optional; an empty field omits its icon entirely rather than showing a dead link.
- UI label text (`متن‌های رابط کاربری`) — ~11 short chrome/nav-style Persian strings (تازه‌ترین, موضوعات, بیشتر, etc.) editable from wp-admin, falling back to the original hardcoded text when unset.
- Real "most read" (پرخواننده‌ترین) sorting on topic archives — atomic, race-condition-safe view counting, excluding bots, logged-in staff, and duplicate views from the same browser within 24h.
- Real nav-menu architecture under Appearance → Menus for the four editor-curated locations (topics, publications, sections, more), each degrading gracefully to a hardcoded fallback if unassigned.

**Localization / bilingual readiness**
- Persian (fa, RTL) is the only active language, per `CLAUDE.md` §1 — no English content, no language-switcher plugin.
- Every user-facing string wrapped for translation (`__()`/`_e()`/`esc_html__()` etc.); `.pot` files current for both theme and plugin.
- 100% logical CSS properties (no physical `left`/`right`/`margin-left` anywhere) — the codebase is bilingual-ready without requiring a rewrite for a future English rollout, which remains explicitly out of scope for this release.
- Afghan Dari Jalali calendar month names (not the Iranian variant) site-wide, via the whitelisted Persian Calendar plugin, hardened against its own global date-function hooks.

**Credits**
All six `CLAUDE.md` §7 placements verified present: theme `style.css` header, `screenshot.png`, plugin header, theme `readme.txt`, repo root `README.md`, and the wp-admin footer text.

---

## Explicitly out of scope for this release

- **English / bilingual content rollout.** The codebase is bilingual-*ready* (translatable strings, logical CSS, locale-driven `dir`/`lang`), but no English content, Polylang/WPML, or `/en/` routing has been built. This was a deliberate scope boundary throughout (`CLAUDE.md` §1), not an oversight.
- **A dedicated `/tag/` archive template.** Tag archives currently render via the theme's bare `index.php` fallback (full content, no card grid) — a known, previously-reported layout bug in this fallback was fixed (Phase B6), but building a `taxonomy-topic.php`-style card-grid template for tags was identified as a genuine future enhancement, not a defect, and left for a future decision.
- **A rolling-window "most read"** (e.g. "most read this month"). Current implementation is all-time view counts — simpler, and matches this site's real traffic scale; a rolling window would need a timestamped view-log table this project has no infrastructure for yet.
- **A site-wide "most read"** view (only topic-scoped, matching where the feature's UI actually lives).
- **In-context/inline label editing.** UI label overrides are a single wp-admin settings page, not a front-end overlay editor — deliberately the simpler of two designs considered.

---

## Notable fixes and corrections along the way

(Full detail in `docs/CHANGELOG.md` — this is a representative sample, not exhaustive.)

- Corrected an early wrong assumption about the card-partial architecture (one shared partial with a type switch) after comparing all five of v6's actual card/list patterns.
- Two independent root causes found and fixed for native `post` permalinks not respecting the `/topics/{topic}/` structure (wrong hook, wrong site option).
- `topic`/`publication`/`collection` taxonomies corrected from `hierarchical => false` to `true` after Farhad found the post-editor UI allowed free-typing arbitrary new terms.
- A CF7 dynamic-select mechanism was implemented, found not to work as first assumed, and re-implemented against CF7's actual source rather than assumed API.
- A silent `array_shift()` mutation bug that could make an article's tag list vanish entirely for single-topic articles — found via live testing, not visible from source review alone.
- Mobile hero overflow, drop-cap removal (Persian typography doesn't support it), unclamped excerpts, and a `/tag/` archive CSS body-class collision — all found via Farhad's manual walkthrough, fixed and verified live.
- Phase C (newsletter section removal, popup-menu English-word removal, language-switcher removal) was discovered to have never actually been done despite being treated as closed — investigated honestly, found to have no trace in git history, then completed properly.
- Two real bugs caught during Phase E's ("most read") own verification: a `meta_value_num` query silently excluding never-viewed posts, and a raw `$wpdb` write bypassing WordPress's postmeta object cache — both fixed and verified before shipping, not left as known issues.
- A documentation-accuracy correction (2026-08-11) to the Phase F CHANGELOG entry, clarifying which nav-menu fallback functions are genuinely dead code versus live by-design behavior.

---

## Credits

Designed and developed by Farhad Farhaad
Email: info.farhaad@gmail.com
GitHub: https://github.com/farhadcodes
