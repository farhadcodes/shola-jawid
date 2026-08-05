# CLAUDE.md — Shola Jawid WordPress Theme · Binding Rules

**This file is the law for this repository.** Every decision Claude Code makes — file
placement, naming, what gets hardcoded vs. built as a setting, whether a plugin is
allowed — must trace back to a rule in this file. If a situation isn't covered here,
stop and add a rule to this file first (with a CHANGELOG entry), then proceed. Do not
improvise silently.

This file governs the conversion of the finished `shola-jawid-ui-v6-quiet-press`
static prototype into a working WordPress theme + companion plugin. It is coherent
with `EXECUTION_PLAN.md` (or the Word-doc equivalent) — the plan says *when* things
happen in what order; this file says *how* every artifact must be built, always,
regardless of phase.

---

## 0. Project identity

| Key | Value |
|---|---|
| Theme name | Shola Jawid |
| Theme slug / text-domain | `shola-jawid` |
| Plugin name | Shola Core |
| Plugin slug | `shola-core` |
| Author | Farhad Farhaad |
| Author URI | `https://github.com/farhadcodes` |
| Author email | `info.farhaad@gmail.com` |
| Function/hook prefix | `shola_` (theme), `shcore_` (plugin) |
| CSS custom property prefix | `--sj-` (kept from prototype tokens where possible) |
| PHP namespace (plugin only) | `SholaCore\` |
| Minimum WP version | 6.4 |
| Minimum PHP version | 8.1 |
| License | GPLv2 or later (required for any public WP theme) |

Do not rename any of the above mid-project. If a rename is truly required, it is a
rules change: update this table, log it in CHANGELOG.md, and grep the whole repo for
the old value before continuing.

---

## 1. Scope lock — Persian only, bilingual-ready

- **Active content, menus, and site language: Persian (fa) only, RTL.** English (en)
  is *not* being built, populated, or exposed in this phase.
- The theme must nonetheless be **bilingual-ready**, meaning:
  - Every user-facing string in PHP is wrapped in `__()` / `_e()` / `esc_html__()`
    etc. under the `shola-jawid` text-domain (plugin strings under `shola-core`).
    No hardcoded UI copy in template files, ever — not even "temporarily."
  - `dir="rtl"` / `dir="ltr"` and all spacing in CSS uses **logical properties**
    (`margin-inline-start`, `padding-inline-end`, `border-inline-end`, `inset-inline-*`)
    exactly as the v6 prototype already does. Never add a physical-direction property
    (`margin-left`, `padding-right`, etc.) to new CSS — this is a permanent rule, not
    a phase-specific one.
  - The `languages/` folder exists with a `.pot` file kept current, even though only
    a Persian `.po`/`.mo` will actually be produced.
  - Do **not** install Polylang, WPML, or any multilingual plugin in this phase. Do
    not build a language switcher that does anything (a static/inert menu item that
    matches the prototype's visual language toggle is fine — it must not link
    anywhere real or 404).
  - Do not hardcode `lang="fa"` in a way that would require find-and-replace later —
    pull it from `get_locale()` / `is_rtl()` / a theme mod, never a literal string in
    more than one place.
- Any future English rollout is explicitly out of scope. Do not scaffold Polylang
  config, `en/` routing, or hreflang tags speculatively — bilingual-*ready* means
  "won't have to be rewritten," not "half-built now."

---

## 2. Architecture — theme + plugin split (non-negotiable)

Standard, maintainable WordPress practice, and the rule for this project:

- **`shola-core` (must-use or regular plugin, activated standalone) owns the content
  model:** all Custom Post Types (`issue`, `document`, `announcement`), all custom
  taxonomies (`topic`, `publication`, `collection`), all post-meta field
  registration/sanitization, and any content-model-only helper functions. Content
  must survive a theme switch. This is a hard rule, not a suggestion — CPT/taxonomy
  `register_*` calls must never live in the theme's `functions.php` or `inc/`.
- **`shola-jawid` (theme) owns everything visual:** templates, `template-parts/`,
  `assets/css/`, `assets/js/`, `functions.php` (enqueueing, image sizes, menu
  registration, theme supports), and presentation-only helper functions.
- The theme may declare `shola-core` as a soft dependency (admin notice if inactive)
  but must not fatal-error if the plugin is missing — degrade gracefully (e.g. show
  "install Shola Core" instead of a white screen).
- No business logic (content model, meta sanitization, capability checks tied to
  content) in the theme. No template markup, enqueueing, or styling in the plugin.

---

## 3. Plugin dependency policy — fixed whitelist only

Goal: **as plugin-independent as possible.** Anything WordPress core or clean
custom PHP can do, must be built that way — no plugin as a shortcut for something
that's a day of custom code.

**The only third-party plugins this project may ever install are the ones on this
list.** Adding a plugin not on this list is a rules change: it must be added here,
with the same justification format, and logged in CHANGELOG.md, before
`wp plugin install` is run.

| Plugin | Purpose | Why it's on the whitelist (not custom-built) |
|---|---|---|
| **Contact Form 7** *(or equivalent single, well-maintained form plugin — confirm choice before Phase 4)* | The `ارتباط با حزب` contact form (spam-protected submission handling, honeypot/reCAPTCHA hook) | Secure form handling (nonce, spam filtering, mail-injection prevention) is a narrow, security-sensitive surface that's better maintained by a dedicated, widely-audited plugin than reinvented; but the *display* of the form must use the theme's own markup/CSS, not the plugin's default styles. |
| **Wordfence** *(or equivalent single security/firewall plugin — confirm choice before Phase 6)* | Firewall, brute-force login protection, malware scanning, security audit logging | The proposal's "امنیت بالا: SSL، فایروال..." commitment requires a maintained firewall/scanner; this is explicitly the kind of moving-target security surface (new attack signatures) that custom code cannot keep current with. |

Everything else the proposal/IA requires — SEO meta tags + `sitemap.xml`, PDF
library upload/preview/download, multi-role access, daily backups (via host-level
cron + `wp db export`, documented in Phase 6), caching, image optimization at
upload — is built as **custom theme/plugin code**, not a plugin. In particular:

- **No SEO plugin** (Yoast/RankMath). Title tags, meta description, Open Graph tags,
  canonical URLs, and `sitemap.xml` (via WordPress core's built-in
  `wp_sitemaps_*` API, themed to match brand) are custom code in `shola-core`.
- **No page builder** (Elementor, etc.). All templates are hand-written PHP using
  the v6 CSS/HTML as the exact source of truth.
- **No caching plugin** unless Phase 6 performance testing proves object/page
  caching is required beyond host-level caching — if so, that is a rules-change
  discussion with Farhad first, not a default install.
- If, during build, a feature seems to genuinely require a new plugin: **stop, name
  the feature, name the candidate plugin, state why custom code is impractical, and
  wait for confirmation** before adding it to the whitelist above.

---

## 4. Repository structure (target, both repos live in one GitHub repo as a monorepo unless Farhad says otherwise)

```
shola-jawid/                          ← GitHub repo root
├── .github/
│   └── workflows/                    ← lint/build CI (Phase 0)
├── wp-content/
│   ├── themes/
│   │   └── shola-jawid/
│   │       ├── style.css             ← theme header + credit block (§7)
│   │       ├── screenshot.png        ← theme thumbnail (§7)
│   │       ├── functions.php
│   │       ├── inc/
│   │       │   ├── setup.php         ← theme_support, menus, image sizes
│   │       │   ├── enqueue.php        ← css/js registration, font self-hosting
│   │       │   ├── customizer.php    ← if any theme mods are needed
│   │       │   └── template-tags.php ← presentation helper functions
│   │       ├── template-parts/
│   │       │   ├── header/
│   │       │   ├── footer/
│   │       │   ├── cards/            ← ONE shared card partial, per Aeon analysis
│   │       │   └── article/
│   │       ├── assets/
│   │       │   ├── css/              ← ported from v6 main.css, same 21 sections
│   │       │   ├── js/               ← ported from v6 main.js, progressive enhancement only
│   │       │   └── fonts/            ← self-hosted Vazirmatn, Markazi Text, JetBrains Mono
│   │       ├── front-page.php
│   │       ├── page-*.php
│   │       ├── taxonomy-*.php
│   │       ├── single.php / single-issue.php / single-document.php
│   │       ├── archive-announcement.php
│   │       ├── search.php
│   │       ├── 404.php
│   │       ├── header.php / footer.php
│   │       └── languages/
│   │           └── shola-jawid.pot
│   └── plugins/
│       └── shola-core/
│           ├── shola-core.php        ← plugin header + credit block (§7)
│           ├── includes/
│           │   ├── class-post-types.php
│           │   ├── class-taxonomies.php
│           │   ├── class-meta-fields.php
│           │   ├── class-seo.php         ← custom SEO tags/sitemap (§3)
│           │   └── class-security.php    ← hardening not covered by Wordfence
│           └── languages/
│               └── shola-core.pot
├── docs/
│   ├── EXECUTION_PLAN.md (or .docx)
│   ├── CHANGELOG.md
│   ├── IA-reference/                 ← copies of the brand guide, IA doc, Aeon analysis
│   └── screenshots/                  ← QC renders per phase
├── CLAUDE.md                         ← this file
└── README.md                         ← public-facing repo readme (with credit, §7)
```

Rules:
- File and folder names are **kebab-case**, lowercase, no spaces, ASCII only (per the
  IA doc's own slug rule — apply it to code, not just URLs).
- One template file = one URL-template responsibility. Do not cram multiple page
  types into one file with conditionals beyond what the WP template hierarchy
  already implies.
- Every PHP file starts with a one-line comment stating its template-hierarchy role
  or its function group (e.g. `// Template: taxonomy-topic.php — archive for the
  "topic" taxonomy, per IA node TOP-*`).

---

## 5. Coding standards

- **PHP**: WordPress Coding Standards (WPCS) via `phpcs` with
  `WordPress-Extra` ruleset. Tabs for indentation (WP convention), Yoda conditions,
  `snake_case` functions/variables prefixed per §0.
- **CSS**: keep the v6 prototype's existing structure — hand-written, no framework,
  logical properties, the same numbered section-comment convention
  (`/* === 05. Masthead === */`) already used in `main.css`. Do not introduce
  Tailwind, Bootstrap, or any CSS framework into the WP build; the whole point of
  the v6 pivot was hand-authored CSS discipline.
- **JS**: vanilla, progressive enhancement only (site must be usable with JS
  disabled, per the prototype's own quality floor). No framework (no React/Vue) for
  front-end theme output. `wp_enqueue_script` with proper dependencies/versioning,
  never inline `<script>` blocks in templates except small `wp_add_inline_script`
  cases.
- **Every function, class, and non-trivial block gets a docblock** (`@param`,
  `@return`, one-line purpose). This is for Farhad's own future maintenance, not
  decoration — do not skip it to save time.
- **No hardcoded colors, font names, or spacing values in PHP or inline styles.**
  All of that lives in `assets/css/` as CSS custom properties, exactly the eleven
  (plus functional) tokens from the brand guide. If a template needs a color, it
  applies a class; it does not `style="color:#8E1B1B"`.
- **Escape everything on output** (`esc_html`, `esc_attr`, `esc_url`) and **sanitize
  everything on input** (`sanitize_text_field`, etc.) — no exceptions, this is both
  a coding standard and a security requirement (see §6).

---

## 6. Security (hard requirement from the original proposal)

The proposal promises "امنیت بالا: SSL، فایروال، بک‌آپ روزانه و محافظت در برابر
حملات." This is not optional polish — treat every rule below as a blocking
requirement, not a nice-to-have:

- All form input sanitized, all output escaped (see §5).
- Nonces on every form (contact form via CF7's own nonce handling; any custom admin
  forms in `shola-core` use `wp_nonce_field` / `check_admin_referer`).
- File uploads (PDFs for issues/documents) restricted by MIME-type allowlist
  (`upload_mimes` filter — PDF only for those custom fields) and validated
  server-side, not just by extension.
- Disable file editing from wp-admin (`DISALLOW_FILE_EDIT`), documented as a
  required `wp-config.php` addition in `docs/EXECUTION_PLAN.md` Phase 6, not
  something the theme silently assumes.
- Least-privilege roles mapped exactly to the IA doc's four roles (مدیر, سردبیر,
  نویسنده, همکار) using WP's native role/capability system — no custom
  role-management plugin.
- SSL/HTTPS enforcement, security headers (CSP, X-Frame-Options, etc.) documented
  as server/host-level config in Phase 6, with theme-level code (e.g.
  `wp_headers` filter) as the fallback for anything not settable at host level.
- Wordfence (per §3 whitelist) handles firewall/malware-scan/brute-force; do not
  duplicate that logic in custom code.
- Daily backups: document the exact host-level or WP-CLI cron mechanism in Phase 6
  — do not leave this as a vague aspiration in the plan.

---

## 7. Credit block — exact placement (do not deviate)

Farhad's credit must appear in **every one of these places**, using this exact
wording (translate only where the file format requires a translated label, never
the name/URL):

> Designed and developed by Farhad Farhaad
> Email: info.farhaad@gmail.com
> GitHub: https://github.com/farhadcodes

**Placements, each mandatory:**

1. **Theme `style.css` header** — the standard WP theme header block. This is what
   populates the theme's "About" info in wp-admin → Appearance → Themes, so this is
   the primary, standards-compliant place for theme credit:
   ```css
   /*
   Theme Name: Shola Jawid
   Theme URI: https://github.com/farhadcodes/shola-jawid
   Author: Farhad Farhaad
   Author URI: https://github.com/farhadcodes
   Description: ...
   Version: 1.0.0
   License: GNU General Public License v2 or later
   License URI: https://www.gnu.org/licenses/gpl-2.0.html
   Text Domain: shola-jawid
   */
   ```
   `Author URI` is the field WordPress renders as a clickable link under the theme
   name in the theme dashboard grid — this is "the place standard theme developers
   put it," and it must point to `https://github.com/farhadcodes`.

2. **`screenshot.png`** (theme thumbnail shown in the WP dashboard grid) — this
   image itself is visual only (no WP mechanism to put a clickable link inside a
   screenshot), but a small, tasteful credit line may be composited into the
   bottom corner of the screenshot per Phase 3 QC (e.g. a slim mono-font strip:
   "Designed & developed by Farhad Farhaad — github.com/farhadcodes"), styled with
   the brand tokens so it doesn't look bolted-on. Confirm this visual treatment
   with Farhad before finalizing — do not guess at placement/size unilaterally.

3. **Plugin `shola-core.php` header** — same fields as above (`Plugin Name`,
   `Plugin URI`, `Author`, `Author URI`, etc.), same `Author URI` value.

4. **Theme `readme.txt`** (WordPress.org-style readme, even though this isn't being
   submitted to the .org repo — it's the standard file structure and future-proofs
   a submission) — `Contributors:` field and a closing "Credits" section with the
   full three-line block.

5. **Repo root `README.md`** — a "Credits" or "Author" section near the top or
   bottom with the full three-line block and a GitHub profile link.

6. **Admin footer text** (`admin_footer_text` filter, theme's `inc/setup.php`) —
   the standard WP mechanism theme developers use to put a credit line in the
   wp-admin footer ("Thank you for creating with Shola Jawid, by Farhad Farhaad").
   This is optional/light-touch, not a place to be pushy about it — one line, no
   popup, no forced link-back nag screen.

Do not place the credit anywhere that degrades UX for the client (no forced
"powered by" footer link on the public-facing site beyond what's already planned in
the site footer per the IA doc, which does not currently include one — do not add
one without asking).

---

## 8. Version control & GitHub

- New GitHub repo, created in Phase 0 (see execution plan), named `shola-jawid`,
  public (so it also markets Farhad's work per his stated goal), with description
  and topics set (`wordpress-theme`, `rtl`, `persian`, `publishing`).
- `.gitignore` excludes: `wp-content/uploads/`, `wp-content/cache/`, any
  environment-specific LocalWP files, `node_modules/`, `.env`, DB dumps.
  **Never commit real client content, credentials, or DB exports.**
- Conventional commit messages: `feat:`, `fix:`, `refactor:`, `docs:`, `chore:`,
  scoped where useful (`feat(theme): add front-page.php hero loop`).
- One feature/phase-step per branch/PR where practical
  (`feat/cpt-issue-document-announcement`), merged to `main` only after the
  relevant phase's QC step (see execution plan) passes.
- Tag a release (`v1.0.0`) at final handover (Phase 7), matching the `Version:` in
  `style.css`.
- `docs/CHANGELOG.md` is updated **every time a rule in this file is applied in a
  new way, deviated from, or added to** — not just for ordinary feature commits
  (git history covers those). CHANGELOG entries answer "what changed about *how* we
  build," not "what feature got built." Format:

  ```md
  ## 2026-08-12
  - **Changed:** SEO sitemap now uses core `wp_sitemaps` filters instead of a
    fully custom rewrite, after discovering core's API covers 90% of the need.
    Reason: less custom code to maintain, same "no SEO plugin" outcome. Approved
    by Farhad in session on 2026-08-12.
  ```

---

## 9. What Claude Code must NOT do, ever, without stopping to ask

- Add a plugin not on the §3 whitelist.
- Introduce a CSS or JS framework.
- Hardcode Persian strings outside the text-domain/`__()` pattern.
- Use physical CSS properties (`left`/`right`/`margin-left` etc.) instead of
  logical properties.
- Put CPT/taxonomy/meta registration in the theme instead of `shola-core`.
- Change the color tokens, font stack, or the 60/30/10 crimson-as-accent
  discipline from the brand guide — the WP conversion is a **faithful port** of
  the v6 static prototype, not a redesign. Any visual deviation from the v6
  screenshots is a bug, not a design decision, unless Farhad explicitly asks for
  a change.
- Skip the credit block in any of the six §7 placements.
- Commit real content, secrets, or DB dumps.
- Silently resolve an "open IA decision" from the IA doc §9 (the جنبش بین‌المللی
  dual-listing, the pairing model, the issue-vs-web-article model) — these are
  still open. Build against the documented *assumption* for each, flag clearly in
  code comments (`// ASSUMES: linked-pair bilingual model per IA §9, pending
  confirmation`), and surface them again at the relevant phase gate in the
  execution plan.

---

## 10. Definition of done (per phase and overall)

A phase is not done until:
1. Code matches this file's rules (self-check against §§1–9).
2. Visual output matches the v6 prototype's rendered pages (screenshot diff or
   side-by-side, saved to `docs/screenshots/`).
3. `phpcs` runs clean against WPCS.
4. The relevant `docs/CHANGELOG.md` entries (if any rule was newly applied/
   deviated) are written.
5. Work is committed and pushed with a conventional commit message.
6. The execution plan's phase checklist (in `EXECUTION_PLAN.md`) is checked off.

The whole project is not done until every phase above is done **and** the six §7
credit placements are verified present, **and** a final `v1.0.0` tag is pushed.
