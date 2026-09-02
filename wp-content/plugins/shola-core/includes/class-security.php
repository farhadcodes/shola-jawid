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
