# Shola Jawid — Security & Performance Hardening Plan

**Prepared by:** Farhad Farhaad
**Date:** 2026-10-03
**Scope:** `sholajawid.com` (production WordPress site)
**Status:** Draft for client review — no item below has been implemented yet
unless explicitly marked **[ALREADY DONE]**.

---

## 0. Purpose and how to use this document

This document is the single, traceable list of every security and
performance action recommended for the site, given its nature as a
politically active publishing platform. Every action has:

- a unique **ID** (e.g. `S1`, `P1`, `L1`) for reference in commits, the
  CHANGELOG, and future conversations — "do S4" is unambiguous;
- a **priority** (see legend below);
- an **effort** estimate;
- a **status** column to be updated as items are completed.

Nothing in this document should be actioned without the client's
(Farhad's) confirmation where a decision or tradeoff is involved — items
marked **Decision needed** must not be built silently.

### Priority legend

| Priority | Meaning |
|---|---|
| **P0 — Critical** | Directly prevents account takeover, data loss, or site compromise. Do these first, regardless of anything else. |
| **P1 — High** | Significantly reduces attack surface or materially improves performance. Do shortly after P0. |
| **P2 — Medium** | Worthwhile hardening/optimization with lower urgency or smaller impact. |

---

## 1. Section A — Security: Critical (P0)

| ID | Action | Priority | Effort | Status |
|---|---|---|---|---|
| **A1** | Verify production `wp-config.php` has `DISALLOW_FILE_EDIT` set to `true`. Confirmed present on the local dev copy; must be independently confirmed on `sholajawid.com` itself. | P0 | 5 min | Not started |
| **A2** | Verify production has `WP_DEBUG` set to `false` and `WP_DEBUG_DISPLAY` off. Debug mode must never be on in production — it can leak file paths and internal errors to visitors. | P0 | 5 min | Not started |
| **A3** | Enable **two-factor authentication (2FA)** for every admin and editor account (مدیر, سردبیر), using Wordfence's built-in 2FA (already installed, free). This is the single most effective defense against account takeover for a politically targeted site. | P0 | 30 min | Not started |
| **A4** | Confirm Wordfence's **brute-force / login-attempt limiting** module is actually switched on and configured (installed does not mean configured). | P0 | 15 min | Not started |
| **A5** | Confirm **daily automated backups** are running on the hosting account, stored in a location separate from the live server (off-server/off-site), with at least one successful **restore test** performed once. A backup that has never been test-restored is not a verified backup. | P0 | Varies by host | Not started |
| **A6** | Audit all existing wp-admin user accounts: remove any unused/stale accounts, and confirm every account's role matches the intended four-role model (مدیر, سردبیر, نویسنده, همکار) with no excess capability. | P0 | 30–60 min | Not started |

---

## 2. Section B — Security: High priority (P1)

| ID | Action | Priority | Effort | Status |
|---|---|---|---|---|
| **B1** | Audit and configure **security response headers** at the server/host level: Content-Security-Policy (CSP), X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy. Theme already has a `wp_headers` filter fallback point (`class-security.php`) for anything not settable at host level. | P1 | 2–4 hrs | Not started |
| **B2** | Audit SSL/TLS configuration: confirm HSTS is enabled, no weak/deprecated cipher suites, and certificate auto-renewal is actually functioning (not just "has a cert today"). | P1 | 1 hr | Not started |
| **B3** | Put the site behind **Cloudflare** (or equivalent) in front of the origin server — free tier is sufficient. Provides DDoS absorption, hides the real server IP, and allows bot/geo challenge rules without touching WordPress itself. High value for a politically exposed site specifically because server-level DDoS cannot be stopped by WordPress hardening alone. | P1 | 2–3 hrs | **Decision needed** — confirm with Farhad before proceeding (changes DNS) |
| **B4** | Disable **application-password REST authentication** (`application_passwords_enabled` filter) if the REST API is not used by any external application — removes an unused credential-based attack surface. | P1 | 30 min | Not started |
| **B5** | Confirm Wordfence **file-integrity monitoring** alerts (core/theme/plugin file changes) are actually reaching an inbox/phone, not just sitting unread in wp-admin. | P1 | 15 min | Not started |
| **B6** | Build the **custom login URL** feature — full specification in Section 4 below. | P1 | ~1 day | ✅ Done 2026-10-03 — theme v1.49.20 / plugin v1.25.3, see docs/CHANGELOG.md. Also includes a full branded login-page redesign beyond the original L1-L7 scope, per Farhad's follow-up ask. |

---

## 3. Section C — Performance

| ID | Action | Priority | Effort | Status |
|---|---|---|---|---|
| **P-1** | Run a real **Lighthouse / PageSpeed Insights audit** against the live production site to get actual measured numbers, instead of planning further performance work from guesses. | P1 | 30 min | Not started |
| **P-2** | Audit whether uploaded **images and PDFs are compressed/resized on upload**, or stored exactly as-uploaded. On a content-heavy publishing site with many photos and PDFs, this is typically the single largest real-world speed lever. Per CLAUDE.md §3, this is built as custom code, not a plugin. | P1 | Audit: 1 hr · Fix: 1 day | Not started |
| **P-3** | Add an **edge/reverse-proxy cache** (e.g. Cloudflare cache rules, or host-level caching if the hosting provider offers it) rather than a WordPress caching plugin — keeps the project's existing "no caching plugin by default" policy (CLAUDE.md §3) while still gaining the performance benefit. | P2 | 2–3 hrs | **Decision needed** — rules-change discussion per CLAUDE.md §3 |
| **P-4** | Confirm self-hosted fonts (Vazirmatn, Markazi Text, JetBrains Mono — already self-hosted per CLAUDE.md §4) are served with `font-display: swap` so text is not invisible while fonts load. | P2 | 30 min | Not started |
| **P-5** | **Database housekeeping**: clean up accumulated post revisions, spam/trashed comments, and expired transients via a scheduled WP-CLI cron job (no plugin needed). Reduces query overhead that grows over time on an actively updated site. | P2 | 2–3 hrs | Not started |

---

## 4. Section D — Custom login URL feature (full specification)

**Goal:** Remove the site from the constant, automated pool of bots that
scan every WordPress site's default `/wp-login.php` and `/wp-admin` around
the clock, by allowing a changeable, admin-controlled custom login path.
This is a layer added **on top of** real authentication security (2FA,
rate limiting) — not a replacement for it.

| ID | Component | Detail |
|---|---|---|
| **L1** | Admin settings field | A field in wp-admin (new settings screen, e.g. "شخصی‌سازی ورود") where مدیر can type a custom login slug (e.g. `sj-access-7391`). Stored in `wp_options`, changeable at any time, effective immediately — no cache delay. |
| **L2** | Request interception | Visiting the real `/wp-login.php` or `/wp-admin` while logged out returns a plain **404** — not a redirect, and not a page that hints a working login exists elsewhere. A redirect would simply tell an automated scanner "a real login exists, go find it." |
| **L3** | Slug validation | The custom slug is sanitized and validated on save: no slashes, no collision with any existing real page or rewrite rule. |
| **L4** | Lockout safety net | A documented WP-CLI/database fallback (e.g. deleting a single option row) to restore the default `/wp-login.php` if the custom slug is ever forgotten or misconfigured. **This is mandatory, not optional** — without it, a bug in this feature could lock the client out of their own admin panel entirely. |
| **L5** | Scope exclusions | WP-Cron, REST authentication, and the already-disabled XML-RPC endpoint are explicitly exempted from the interception, so background site functionality is never silently broken. |
| **L6** | Compatibility with Wordfence | Built independently of Wordfence's own login-page protections — Wordfence continues to rate-limit/2FA-protect whichever URL ends up being the real one. |
| **L7** | Implementation location | Per CLAUDE.md §2 (business logic belongs in the plugin, not the theme): new class in `wp-content/plugins/shola-core/includes/`, e.g. `class-custom-login.php`. Not a third-party plugin — this is exactly the kind of narrow, well-understood feature the project's plugin-whitelist policy (§3) says should be custom code. |

**Honest caveat to communicate to the client:** a custom login URL is
*security by obscurity*. It meaningfully reduces automated/bot-driven
attack volume, but it does not replace 2FA (A3) or brute-force protection
(A4) — both of those remain necessary regardless of this feature.

---

## 5. Recommended order of execution

1. **Section A (A1–A6)** — all P0, do first, same week. These are cheap
   and close the most dangerous gaps.
2. **Section B (B1, B2, B4, B5)** — P1 items with no open decision, do
   next.
3. **B3 and P-3** — require a short decision conversation with Farhad
   (DNS change for Cloudflare; caching policy), schedule that
   conversation, then proceed.
4. **L1–L7 (custom login URL)** — Farhad has confirmed he wants this
   built; schedule as its own task/branch per the project's standing git
   workflow (CLAUDE.md §8).
5. **P-1 (real audit)** — run early, in parallel with Section A, since it
   costs nothing and tells us whether P-2/P-3/P-5 are actually worth
   prioritizing or not.

---

## 6. Sign-off

| Field | Value |
|---|---|
| Plan prepared | 2026-10-03 |
| Discussed with client | 2026-10-03 (verbal, this session) |
| Approved items | Custom login URL (L1–L7) — confirmed wanted |
| Items pending decision | B3 (Cloudflare/DNS), P-3 (caching policy) |
| Next review | After Section A is complete |
