<?php
/**
 * The /en/ URL prefix, which language the current request is in, and
 * the shared helper every content query uses to show only that
 * language's content. See english-version/03-content-architecture/
 * url-structure-and-routing.md for the full design.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * English/Persian URL routing and query filtering.
 */
class Language {

	/**
	 * The query var that marks a request as being in the English URL
	 * space. Present and `en` = English page; absent = Persian page.
	 */
	const QUERY_VAR = 'shola_lang';

	/**
	 * This request's language, resolved once per request by
	 * set_language_context() and read by current() everywhere else —
	 * one source of truth instead of every piece of code re-deriving it
	 * from the URL on its own.
	 *
	 * @var string|null
	 */
	private static $current = null;

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'query_vars', array( __CLASS__, 'register_query_var' ) );
		add_filter( 'rewrite_rules_array', array( __CLASS__, 'add_english_prefixed_rules' ) );
		add_action( 'init', array( __CLASS__, 'add_homepage_rule' ) );
		add_action( 'parse_request', array( __CLASS__, 'set_language_context' ) );
		add_action( 'admin_init', array( __CLASS__, 'backfill_legacy_language_meta' ) );

		/*
		 * These three templates run WordPress's own main query directly
		 * (have_posts()/the_post(), no `new WP_Query()` of their own), so
		 * pre_get_posts actually reaches them — same pattern already used
		 * by Post_Types::include_cpts_in_search() and its two per-page
		 * siblings. Every other listing template (taxonomy-*.php,
		 * page-*.php, front-page.php's panels) builds its own query and
		 * needs maybe_add_language_meta_query() called directly on its
		 * own $args instead — see that method's docblock.
		 */
		add_action( 'pre_get_posts', array( __CLASS__, 'filter_main_query_by_language' ) );
	}

	/**
	 * Makes `shola_lang` a recognized query var, so it survives
	 * WordPress's own request parsing instead of being silently dropped.
	 *
	 * @param string[] $vars Existing public query vars.
	 * @return string[]
	 */
	public static function register_query_var( $vars ) {
		$vars[] = self::QUERY_VAR;
		return $vars;
	}

	/**
	 * Mirrors every existing rewrite rule under an `en/` prefix, each one
	 * additionally setting `shola_lang=en` — so any URL that already
	 * works today (an article, a taxonomy archive, a PDF permalink...)
	 * also works with `en/` in front, with no per-URL-pattern list to
	 * hand-maintain. English-prefixed rules go first, so they're matched
	 * before their un-prefixed Persian equivalents.
	 *
	 * @param string[] $rules Existing rewrite rules (regex => query string).
	 * @return string[]
	 */
	public static function add_english_prefixed_rules( $rules ) {
		// Rule types with no real "English version" to speak of — mirroring
		// these would just double the rewrite table for nothing.
		// `rest_route=`: the REST API isn't a page with a language. `sitemap`:
		// handled deliberately in Phase 5, not by blind prefixing here.
		// `feed=`/`embed=true`: out of scope for now, not asked for.
		$skip_if_contains = array( 'rest_route=', 'sitemap', 'feed=', 'embed=true' );

		$english_rules = array();
		foreach ( $rules as $regex => $query ) {
			foreach ( $skip_if_contains as $needle ) {
				if ( false !== strpos( $query, $needle ) ) {
					continue 2;
				}
			}

			// Every existing regex starts with `^` (an anchor, not a
			// literal character) — putting "en/" in front of the whole
			// string would bury that anchor in the middle of the regex
			// instead of at the start, breaking every single one of
			// these silently. Strip it and put it back at the very
			// front, now in front of "en/" instead of behind it.
			$en_regex                   = '^en/' . ltrim( $regex, '^' );
			$english_rules[ $en_regex ] = $query . '&' . self::QUERY_VAR . '=en';
		}
		return $english_rules + $rules;
	}

	/**
	 * The homepage itself (`/en/`) has no regular rewrite rule to mirror
	 * above — WordPress serves the front page directly, not through a
	 * regex match — so it needs its own explicit rule.
	 *
	 * @return void
	 */
	public static function add_homepage_rule() {
		add_rewrite_rule( '^en/?$', 'index.php?' . self::QUERY_VAR . '=en', 'top' );
	}

	/**
	 * Resolves and caches this request's language from the parsed query
	 * vars — called once, early, so current() below never has to guess.
	 *
	 * @param \WP $wp The main WP request object.
	 * @return void
	 */
	public static function set_language_context( $wp ) {
		self::$current = ( isset( $wp->query_vars[ self::QUERY_VAR ] ) && 'en' === $wp->query_vars[ self::QUERY_VAR ] ) ? 'en' : 'fa';
	}

	/**
	 * This request's language — `fa` or `en`. Safe to call from anywhere
	 * (templates, other plugin classes, the language switcher); falls
	 * back to `fa` if asked before parse_request has run (e.g. a WP-CLI
	 * or cron context with no real request).
	 *
	 * @return string
	 */
	public static function current() {
		return self::$current ? self::$current : 'fa';
	}

	/**
	 * Adds a `shcore_language` condition to a query's existing
	 * meta_query, merging rather than replacing anything already there —
	 * the one shared function every content-listing template calls, so
	 * the actual language condition lives in one place, not copy-pasted
	 * into a dozen templates.
	 *
	 * Call this on `$query_args` before passing it to `new WP_Query()` or
	 * `get_posts()` for any template that builds its own query (most of
	 * this site's listing pages do — see the class docblock). For a
	 * template that instead uses WordPress's own main query
	 * (have_posts()/the_post()), filter_main_query_by_language() below
	 * already does this automatically.
	 *
	 * @param array $query_args Query args being built for `new WP_Query()`/`get_posts()`.
	 * @return array Same args, with the language condition merged in.
	 */
	public static function add_language_filter( $query_args ) {
		$condition = array(
			'key'   => 'shcore_language',
			'value' => self::current(),
		);

		if ( empty( $query_args['meta_query'] ) ) {
			$query_args['meta_query'] = array( $condition ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- small per-type post counts, same as every other meta_query already in this codebase.
			return $query_args;
		}

		// Something else already set a meta_query (e.g. a sort-by-views
		// query) — nest both under a shared 'AND' relation rather than
		// overwriting it.
		$query_args['meta_query'] = array(
			'relation' => 'AND',
			$condition,
			$query_args['meta_query'],
		);

		return $query_args;
	}

	/**
	 * Same condition as add_language_filter() above, applied to
	 * WordPress's own main query — for the handful of templates
	 * (search.php, archive-announcement.php, archive-editors_pick.php)
	 * that use have_posts()/the_post() directly instead of building
	 * their own WP_Query. Single posts/pages are left alone — a single
	 * item resolves by its own unique slug regardless of language, same
	 * as every other post type already works.
	 *
	 * @param \WP_Query $query The query being filtered.
	 * @return void
	 */
	public static function filter_main_query_by_language( $query ) {
		if ( is_admin() || ! $query->is_main_query() || $query->is_singular() ) {
			return;
		}

		$existing = $query->get( 'meta_query' );
		$args     = self::add_language_filter( array( 'meta_query' => $existing ) );
		$query->set( 'meta_query', $args['meta_query'] ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- see add_language_filter() above.
	}

	/**
	 * One-time fix for a real bug caught live, not assumed: every piece of
	 * content that existed before this field did has NO actual
	 * `shcore_language` postmeta row at all — `register_post_meta()`'s
	 * `default` only affects what `get_post_meta()` *returns* for a post
	 * with no row, it never writes one. A meta_query exact-match (which is
	 * what every language filter in this class does) only matches a real
	 * stored value, so every pre-existing post was silently excluded from
	 * BOTH the Persian and English queries — confirmed live: the Persian
	 * اطلاعیه‌ها archive dropped from 5 real announcements to 0 the moment
	 * filter_main_query_by_language() went live, not just the English one.
	 *
	 * Fixes it the same way every other one-time content migration in this
	 * plugin does (see Taxonomies::migrate_legacy_party_documents() for the
	 * original pattern this follows): find every post with no row yet, set
	 * it to `fa` explicitly (every pre-existing post actually is Persian),
	 * hooked on admin_init with a persisted flag rather than plugin
	 * activation (a code-only redeploy to an already-active plugin never
	 * re-fires activation).
	 *
	 * @return void
	 */
	public static function backfill_legacy_language_meta() {
		if ( get_option( 'shcore_language_meta_backfilled' ) ) {
			return;
		}

		$post_types = array( 'post', 'issue', 'document', 'party_publication', 'party_document', 'leaflet', 'editors_pick', 'announcement' );

		foreach ( $post_types as $post_type ) {
			$missing_ids = get_posts(
				array(
					'post_type'      => $post_type,
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- one-time migration, not a recurring query.
						array(
							'key'     => 'shcore_language',
							'compare' => 'NOT EXISTS',
						),
					),
				)
			);

			foreach ( $missing_ids as $post_id ) {
				update_post_meta( $post_id, 'shcore_language', 'fa' );
			}
		}

		update_option( 'shcore_language_meta_backfilled', 1 );
	}
}