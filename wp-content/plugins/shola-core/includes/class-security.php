<?php
/**
 * Hardening not covered by Wordfence — CLAUDE.md §6 /
 * EXECUTION_PLAN.md Phase 6.2. Wordfence (firewall, brute-force,
 * malware scanning) handles the broad-spectrum threats; this class
 * only covers the narrower, WordPress-specific items the plan calls
 * out separately, each checked against this site's actual state
 * before building (not assumed needed):
 *
 * - XML-RPC: confirmed live (`system.listMethods` responded with the
 *   full method list, including `system.multicall`) that it was fully
 *   functional and unused by this project — no remote-publishing app,
 *   no pingbacks/trackbacks relevant to this site. Disabled.
 * - WP version string: confirmed live (`<meta name="generator"
 *   content="WordPress 7.0.3">`) that the exact core version was
 *   exposed in page source, RSS feeds, and script/style query strings.
 *   Removed from all three.
 * - REST API user enumeration: checked live first
 *   (`/wp-json/wp/v2/users` as an anonymous request) and found it
 *   already returns `401 rest_user_cannot_view` by default — no code
 *   needed, WordPress core already restricts this without
 *   authentication. Recorded here so it's clear this was verified, not
 *   overlooked.
 * - Author name in RSS/Atom feeds (2026-09-02): every theme template
 *   that displayed a byline/author was edited directly (see
 *   docs/CHANGELOG.md), but WordPress core's own default feed templates
 *   (`wp-includes/feed-rss2.php`/`feed-atom.php`) independently call
 *   `the_author()` for `<dc:creator>`/`<author><name>` — a separate
 *   code path those template edits can't reach. Blanked here instead,
 *   scoped to `! is_admin()` so wp-admin's own author column/dropdowns
 *   (a legitimate internal CMS-management view, not public-facing) are
 *   unaffected.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * XML-RPC disable + WP version string removal.
 */
class Security {

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'xmlrpc_enabled', '__return_false' );
		add_filter( 'wp_headers', array( __CLASS__, 'remove_xmlrpc_header' ) );

		remove_action( 'wp_head', 'wp_generator' );
		add_filter( 'the_generator', '__return_empty_string' );
		add_filter( 'the_author', array( __CLASS__, 'remove_public_author_name' ), 20 );
		add_filter( 'style_loader_src', array( __CLASS__, 'remove_version_query_arg' ), 9999 );
		add_filter( 'script_loader_src', array( __CLASS__, 'remove_version_query_arg' ), 9999 );

		add_action( 'plugins_loaded', array( __CLASS__, 'block_real_login_urls' ) );
		add_action( 'template_redirect', array( __CLASS__, 'serve_custom_login' ) );
		add_filter( 'login_url', array( __CLASS__, 'rewrite_login_related_url' ) );
		add_filter( 'logout_url', array( __CLASS__, 'rewrite_login_related_url' ) );
		add_filter( 'lostpassword_url', array( __CLASS__, 'rewrite_login_related_url' ) );
		add_filter( 'register_url', array( __CLASS__, 'rewrite_login_related_url' ) );
		add_filter( 'wp_auth_check_html', array( __CLASS__, 'rewrite_login_related_url' ) );
		add_filter( 'site_url', array( __CLASS__, 'rewrite_login_related_url' ) );
		add_filter( 'network_site_url', array( __CLASS__, 'rewrite_login_related_url' ) );
	}

	/**
	 * Custom login URL (Login_Security_Settings, spec-audit security plan
	 * item B6/L1-L7) — added 2026-10-03, split into two hooks after a
	 * live fatal error on the first version (single `plugins_loaded`
	 * handler that `require`d wp-login.php in place): wp-login.php's own
	 * script output references constants (e.g. `AUTOSAVE_INTERVAL`) that
	 * WordPress's bootstrap (wp-settings.php) only defines *after*
	 * `plugins_loaded` finishes — fine when wp-login.php runs as its own
	 * fresh top-level script (which does its own full bootstrap first),
	 * but requiring it mid-`plugins_loaded`, nested inside a request
	 * that's still bootstrapping, hit those constants before they
	 * existed. Fixed by moving the "serve the form" half to
	 * `template_redirect` — late enough that bootstrap has fully
	 * finished, but still early enough that nothing has been output yet.
	 *
	 * `block_real_login_urls()` (`plugins_loaded`) blocks the real,
	 * un-rewritten `wp-login.php` and `wp-admin` paths — needed this
	 * early because both are direct, non-`index.php` entry points WP
	 * itself loads outside the normal front-end bootstrap, and
	 * `template_redirect` (a front-end-only hook) never fires for either
	 * of them at all.
	 *
	 * `serve_custom_login()` (`template_redirect`) handles the opposite
	 * case: a normal front-end request to the custom slug itself (e.g.
	 * `/sj-2026-access/`), which core would otherwise just 404 since
	 * nothing really lives at that path — `require`s the real
	 * wp-login.php in place, now safely after full bootstrap, so the same
	 * URL stays in the browser's address bar with no redirect.
	 *
	 * If the option is empty (feature off), both functions return
	 * immediately — `wp-login.php`/`wp-admin` behave exactly like a stock
	 * WordPress install, the documented, safe "something broke, turn it
	 * off" fallback (see Login_Security_Settings's own settings-page
	 * copy).
	 *
	 * @return void
	 */
	public static function block_real_login_urls() {
		$slug = trim( (string) get_option( Login_Security_Settings::OPTION_SLUG, '' ) );
		if ( '' === $slug ) {
			return;
		}

		$script       = isset( $_SERVER['SCRIPT_NAME'] ) ? wp_unslash( $_SERVER['SCRIPT_NAME'] ) : '';
		$is_login_php = ( false !== strpos( $script, 'wp-login.php' ) );
		$is_wp_admin  = ( false !== strpos( $script, '/wp-admin/' ) ) && ! self::is_exempt_admin_script( $script );

		if ( $is_login_php ) {
			self::force_404();
		}

		/*
		 * Without this, WordPress core's own `auth_redirect()` would send
		 * a logged-out `/wp-admin/` visitor to `wp_login_url()` — which,
		 * once the filter below rewrites it to the custom slug, would
		 * leak that slug to anyone who simply types `/wp-admin`. Blocked
		 * here, before that redirect ever has a chance to fire.
		 */
		if ( $is_wp_admin && ! is_user_logged_in() ) {
			self::force_404();
		}
	}

	/**
	 * See block_real_login_urls()'s docblock above for the full design —
	 * this is the `template_redirect` half.
	 *
	 * @return void
	 */
	public static function serve_custom_login() {
		$slug = trim( (string) get_option( Login_Security_Settings::OPTION_SLUG, '' ) );
		if ( '' === $slug ) {
			return;
		}

		$path = trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ), '/' );
		if ( $path !== $slug ) {
			return;
		}

		require ABSPATH . 'wp-login.php';
		exit;
	}

	/**
	 * `admin-ajax.php`/`admin-post.php`/`async-upload.php` each do their
	 * own per-action auth handling (many registered actions are
	 * deliberately public, e.g. a front-end form submission) and never
	 * call `auth_redirect()` the way a real admin page does — blocking
	 * them for logged-out visitors would break legitimate front-end
	 * functionality, not just close a login-page loophole.
	 *
	 * @param string $script `$_SERVER['SCRIPT_NAME']`.
	 * @return bool
	 */
	private static function is_exempt_admin_script( $script ) {
		foreach ( array( 'admin-ajax.php', 'admin-post.php', 'async-upload.php' ) as $exempt ) {
			if ( false !== strpos( $script, $exempt ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Sends a real, full 404 through WordPress's own normal front-end
	 * pipeline — redirecting to a never-real path rather than trying to
	 * hand-render a 404 from inside `wp-login.php`'s/wp-admin's limited
	 * bootstrap context (no parsed main query exists there yet), so the
	 * page title, headers, and everything else about it are exactly what
	 * a genuine 404 on this site looks like, not an approximation.
	 *
	 * @return void
	 */
	private static function force_404() {
		wp_safe_redirect( home_url( '/' . wp_generate_password( 10, false ) . '/' ) );
		exit;
	}

	/**
	 * Rewrites `wp-login.php` to the custom slug in every core-generated
	 * login-related URL (login, logout, lost-password, register) once a
	 * slug is set — so logout links, password-reset emails, and
	 * WordPress's own "please log in" redirects all correctly point at
	 * the real, working URL instead of the now-blocked default one.
	 *
	 * Also hooked to `wp_auth_check_html` (plain string-replace works the
	 * same on that filter's full HTML blob as it does on a bare URL) —
	 * found live, 2026-10-03: wp-admin's own session-expiry check embeds
	 * a hidden iframe pointing straight at `wp-login.php`, built
	 * independently of the four URL functions above, so it was still
	 * hitting the blocked real path even after those were all rewritten.
	 *
	 * Also hooked to `site_url` and `network_site_url` — the real fix for
	 * the actual reproducible bug (logging in through the custom slug
	 * redirected to a 404 instead of completing): wp-login.php's own
	 * login/register/lost-password/reset-password *form* `action`
	 * attributes are built with raw `site_url( 'wp-login.php', ... )` /
	 * `network_site_url( 'wp-login.php?...', ... )` calls deep in core,
	 * never through `wp_login_url()` — so even with the GET page correctly
	 * served at the custom slug, every form on it still posted straight
	 * to the real, blocked `/wp-login.php`, failing silently before
	 * authentication ever ran. Confirmed via `curl` (which let me pick the
	 * POST target manually and masked the bug) vs. an actual browser
	 * submitting the form's real `action` attribute (which reproduced it
	 * every time) — scoping both filters here, since `str_replace` on a
	 * URL that never contains "wp-login.php" is always a harmless no-op,
	 * closes every one of these form-action cases in one place.
	 *
	 * @param string $url Core-generated URL, or (via `wp_auth_check_html`) a full HTML string containing one.
	 * @return string
	 */
	public static function rewrite_login_related_url( $url ) {
		$slug = trim( (string) get_option( Login_Security_Settings::OPTION_SLUG, '' ) );
		if ( '' === $slug ) {
			return $url;
		}
		return str_replace( 'wp-login.php', $slug, $url );
	}

	/**
	 * `xmlrpc_enabled` (filtered above) stops XML-RPC *methods* from
	 * working, but WordPress core still advertises the endpoint via an
	 * `X-Pingback` response header and a `<link rel="pingback">` tag
	 * unless removed separately — both are otherwise a giveaway that
	 * xmlrpc.php exists and is worth probing.
	 *
	 * @param array $headers Response headers.
	 * @return array
	 */
	public static function remove_xmlrpc_header( $headers ) {
		unset( $headers['X-Pingback'] );
		return $headers;
	}

	/**
	 * Blanks `the_author()`/`get_the_author()` everywhere on the public
	 * site (RSS/Atom feeds' `<dc:creator>`/`<author><name>` being the one
	 * remaining case not already handled by direct template edits — see
	 * the class docblock). `! is_admin()` — not `is_feed()` — deliberately,
	 * so this also catches any other front-end call to this same core
	 * template tag, present or future, not just the currently-known feed
	 * templates.
	 *
	 * @param string $name Author display name.
	 * @return string
	 */
	public static function remove_public_author_name( $name ) {
		return is_admin() ? $name : '';
	}

	/**
	 * Strips the `?ver=X.Y.Z` query argument from core (`/wp-includes/`,
	 * `/wp-admin/`) asset URLs only — that version string is the same
	 * WP-core-version disclosure `wp_generator()` exposes, just via a
	 * second path. Deliberately scoped to core paths only: the theme's
	 * own assets (`shola_enqueue_assets()`, `main.css`/`main.js`) and
	 * this plugin's own enqueued assets pass an explicit `$version` for
	 * real cache-busting after a deploy, not version disclosure — a
	 * blanket strip across every asset would silently break that
	 * (confirmed the distinction before writing this filter, not
	 * assumed it was safe to apply everywhere).
	 *
	 * @param string $src Asset URL.
	 * @return string
	 */
	public static function remove_version_query_arg( $src ) {
		if ( strpos( $src, 'ver=' ) === false ) {
			return $src;
		}

		if ( strpos( $src, '/wp-includes/' ) === false && strpos( $src, '/wp-admin/' ) === false ) {
			return $src;
		}

		return remove_query_arg( 'ver', $src );
	}
}
