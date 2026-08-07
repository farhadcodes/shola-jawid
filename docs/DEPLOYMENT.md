# Deployment — SSL/HTTPS, security headers, and backups

Phase 6.3/6.4 (`EXECUTION_PLAN.md`). These four items are explicitly
host/DNS-level requirements, not a code deliverable — this file is the
"documented, not silently assumed" record `CLAUDE.md` §6 requires. Written
specifically for **Hostinger** (confirmed as the actual intended host —
`00_received/Hostinger Credentials.txt` and `04_Sent/Hostinger Purchase
Guide.mp4` — rather than generic "ask your host" language), not a generic
host-agnostic checklist.

## 1. SSL/HTTPS

- **Certificate**: Hostinger issues a free Let's Encrypt SSL certificate
  automatically for the domain once DNS points at Hostinger — hPanel →
  Websites → [site] → **SSL** → confirm status is "Installed" (usually
  active within a few hours of DNS propagating; can be triggered manually
  from that screen if it hasn't auto-issued).
- **Force HTTPS site-wide**: hPanel's SSL section has a **Force HTTPS**
  toggle — enable it. That's the host-level redirect; no `.htaccess` edit
  needed for this specific step on Hostinger.
- **`wp-config.php`**: once SSL is confirmed active on the live domain,
  add `define( 'FORCE_SSL_ADMIN', true );` to the **production**
  `wp-config.php` (forces `/wp-admin/` and `/wp-login.php` over HTTPS
  specifically, on top of the site-wide redirect). **Do not add this to
  the local dev `wp-config.php`** — there is no certificate on
  `shola-jawid.local`, and this constant would lock the local admin out
  entirely (redirect loop to a non-existent HTTPS listener). Left
  undefined in the git-tracked/local config for that reason; add it only
  in the live host's own `wp-config.php`.

## 2. Security headers

Checked live against the running local site before writing this (not
assumed): `curl -I` on the front page currently returns none of
X-Frame-Options/CSP/X-Content-Type-Options/Referrer-Policy — WordPress
core only adds a couple of these automatically to `wp-login.php`/
`wp-admin`, not site-wide. Per `CLAUDE.md` §6's stated priority — host/
server-level first, `wp_headers` PHP fallback only for what the host
can't do — and Hostinger supports `.htaccess` (Apache) on all standard
plans, so every header on the proposal's list is achievable at the host
level. No PHP-level fallback code was added; there's nothing left for it
to cover.

Add to the site's `.htaccess` (root, above the WordPress block WP itself
manages):

```apache
<IfModule mod_headers.c>
	Header always set X-Frame-Options "SAMEORIGIN"
	Header always set X-Content-Type-Options "nosniff"
	Header always set Referrer-Policy "strict-origin-when-cross-origin"
	Header always set Content-Security-Policy "frame-ancestors 'self';"
	Header unset X-Powered-By
	Header always unset X-Powered-By
</IfModule>
```

- `X-Frame-Options`/CSP `frame-ancestors`: prevents the site being
  embedded in a third-party `<iframe>` (clickjacking protection). Matches
  what Wordfence already sets on `wp-login.php` by default — this extends
  the same protection site-wide.
- `X-Content-Type-Options: nosniff`: stops browsers guessing/overriding a
  served file's MIME type.
- `Referrer-Policy`: `strict-origin-when-cross-origin` is a reasonable
  default — sends the full URL as referrer for same-origin requests, only
  the origin (no path) cross-origin, nothing on a downgrade to HTTP.
- `X-Powered-By` removal: found live (`curl -I` showed
  `X-Powered-By: PHP/8.2.29`) — a real PHP-version disclosure, separate
  from the WP-version hardening already handled in code
  (`SholaCore\Security`). `Header unset` only removes it if Apache's own
  `Header` directive can see it before it reaches the client; if
  Hostinger's PHP is running as PHP-FPM and the header persists after
  this, the correct fix is `expose_php = Off` in the account's `php.ini`
  override (Hostinger's hPanel → Advanced → PHP Configuration).

## 3. Backups

**Primary mechanism: Hostinger's native daily backups.** Hostinger's
Business/Premium/Cloud hosting plans include automated daily backups
(hPanel → Files → Backups) with a one-click restore UI — this should be
the primary mechanism actually relied on in production, not the WP-CLI
approach below. Confirm the plan purchased includes this (check hPanel →
Backups; if the plan is a lower tier without automated backups,
Hostinger's own weekly-backup add-on or the WP-CLI cron fallback below
becomes the primary mechanism instead of a supplement).

**Documented fallback/supplement: WP-CLI cron job**, per
`EXECUTION_PLAN.md`'s own fallback instruction. Exact commands (adapt the
path to the real host's WP root):

```bash
# Daily cron entry (crontab -e, or Hostinger hPanel -> Advanced -> Cron Jobs):
0 3 * * * cd /home/USER/domains/DOMAIN/public_html && wp db export /home/USER/backups/db-$(date +\%Y-\%m-\%d).sql && tar -czf /home/USER/backups/wp-content-$(date +\%Y-\%m-\%d).tar.gz wp-content --exclude=wp-content/cache

# Rotation (delete anything older than 14 days), same crontab, run after the above:
0 4 * * * find /home/USER/backups -type f -mtime +14 -delete
```

Off-site copy: point the backup directory at Hostinger's own off-site
storage if the plan includes it, or sync `/home/USER/backups` to an
external destination (S3, Google Drive via `rclone`, etc.) as a separate
cron line — not detailed further here since it depends on which off-site
target Farhad chooses; the local export/rotation above is host-agnostic
and works regardless of where the off-site copy ultimately lands.

**Restore** (from either a Hostinger one-click restore or a WP-CLI
export):

```bash
wp db import /home/USER/backups/db-2026-08-07.sql
# plus restoring wp-content/ from the matching tar.gz if files (uploads,
# an updated plugin, etc.) also need to roll back, not just the DB.
```

### Restore actually tested (not just described)

Per the checklist's explicit "at least one restore has been tested
successfully" requirement — proven end-to-end on the local dev site
2026-08-07, not just documented in theory:

1. Installed WP-CLI (`wp-cli.phar`, none was available on this machine
   before) plus located LocalWP's per-site MySQL binaries and the
   site's actual (non-default) DB port — found via `sites.json` in
   LocalWP's own app data, since this site's MySQL runs on a
   LocalWP-assigned port (`10090`), not the standard `3306` mysqldump/
   mysql assume by default. This local-environment port quirk is
   LocalWP-specific and won't exist on the real host — Hostinger's own
   `wp db export`/`wp db import` work with zero connection overrides,
   the exact commands above are what production actually needs, this
   detour was purely to get the same commands working against this
   particular local dev setup.
2. `mysqldump` (real DB export, ~1.2MB, all real seeded content).
3. Created a throwaway, clearly-labeled test post (`wp post create`,
   ID 80, title "BACKUP RESTORE TEST — should not survive restore").
4. Confirmed it existed (`wp post get 80`).
5. Restored the database from the step-2 backup (`mysql ... < backup.sql`).
6. Confirmed the test post was gone (`wp post get 80` → "Could not find
   the post with ID 80") — proof the restore actually reverted state,
   not just that the import command exited without an error.
7. Confirmed the site and all real content survived intact: front page
   and an article both `200`, zero inline styles, `wp post list` across
   all four content types returned the expected count (41) — the
   restore didn't corrupt or drop real data.
8. Backup files and WP-CLI's temp state cleaned up afterward (kept
   entirely outside the git-tracked repo throughout — never at risk of
   being committed).

**Checklist:**
- ☑ Backup mechanism chosen and documented with exact commands/config
- ☑ At least one restore has been tested successfully
