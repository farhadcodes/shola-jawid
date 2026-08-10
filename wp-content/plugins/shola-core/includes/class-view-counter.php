<?php
/**
 * Silent, system-written page-view counter backing taxonomy-topic.php's
 * "پرخواننده‌ترین" (most read) tab — see docs/CHANGELOG.md's 2026-08-06
 * entry for why that tab was left an inert placeholder until now: no
 * view-tracking infrastructure existed to sort by. Unlike every other
 * field in class-meta-fields.php, this one is never editor-authored —
 * no metabox, no REST exposure, nothing for a human to type in.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers shcore_view_count post meta, increments it on real front-end
 * singular views (excluding staff, known bots, and rapid repeat views),
 * and backfills the meta row for already-published content so
 * meta_value_num sorting never silently drops never-viewed posts.
 */
class View_Counter {

	const META_KEY        = 'shcore_view_count';
	const POST_TYPES      = array( 'post', 'document', 'issue' );
	const COOKIE_PREFIX   = 'shcore_viewed_';
	const DEDUPE_WINDOW   = DAY_IN_SECONDS;
	const BACKFILL_OPTION = 'shcore_view_counter_backfilled';

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_meta' ) );
		add_action( 'init', array( __CLASS__, 'maybe_backfill' ), 20 );
		add_action( 'template_redirect', array( __CLASS__, 'maybe_count_view' ) );
		add_action( 'transition_post_status', array( __CLASS__, 'seed_on_publish' ), 10, 3 );
		add_filter( 'query_vars', array( __CLASS__, 'register_sort_query_var' ) );
	}

	/**
	 * Registers the meta key on all three post types it applies to.
	 * `announcement` is excluded — it has no single view template. Not
	 * exposed to REST/the block editor's Custom Fields panel and nobody
	 * can write it via the meta API from outside this class
	 * (`auth_callback` always denies) — this is a system counter, not an
	 * editor-facing field.
	 *
	 * @return void
	 */
	public static function register_meta() {
		foreach ( self::POST_TYPES as $post_type ) {
			register_post_meta(
				$post_type,
				self::META_KEY,
				array(
					'type'              => 'integer',
					'single'            => true,
					'show_in_rest'      => false,
					'default'           => 0,
					'sanitize_callback' => 'absint',
					'auth_callback'     => '__return_false',
				)
			);
		}
	}

	/**
	 * Registers `sort`, the query var behind taxonomy-topic.php's
	 * "پرخواننده‌ترین" tab (`?sort=views`), same pattern as search.php's
	 * `result_type` (see class-post-types.php).
	 *
	 * @param string[] $vars Public query vars.
	 * @return string[]
	 */
	public static function register_sort_query_var( $vars ) {
		$vars[] = 'sort';
		return $vars;
	}

	/**
	 * Seeds shcore_view_count to 0 the moment a post of a tracked type is
	 * first published (covers both immediate publish and scheduled-post
	 * cron publish, since transition_post_status fires for both) — so a
	 * never-viewed post already has a real postmeta row and isn't
	 * silently excluded from an `orderby => meta_value_num` query, which
	 * only matches posts that have the meta key at all. Idempotent: a
	 * post transitioning through publish more than once (e.g. edited and
	 * re-saved) just no-ops on the second call since `add_post_meta(...,
	 * true)` refuses to add a duplicate unique row.
	 *
	 * @param string   $new_status New post status.
	 * @param string   $old_status Previous post status.
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function seed_on_publish( $new_status, $old_status, $post ) {
		if ( 'publish' !== $new_status || ! in_array( $post->post_type, self::POST_TYPES, true ) ) {
			return;
		}
		add_post_meta( $post->ID, self::META_KEY, 0, true );
	}

	/**
	 * One-time backfill for posts of the tracked types that were already
	 * published before this feature existed (this project's entire
	 * pre-Phase-E content). Runs at most once — gated by an option flag,
	 * so every subsequent request pays only a single cheap
	 * `get_option()` call, not the backfill query itself. Same
	 * "migration-safe by construction" spirit as D1's `wp_parse_args()`
	 * merge, applied to DB rows instead of an option array.
	 *
	 * @return void
	 */
	public static function maybe_backfill() {
		if ( get_option( self::BACKFILL_OPTION ) ) {
			return;
		}

		$post_ids = get_posts(
			array(
				'post_type'      => self::POST_TYPES,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- one-time backfill, not a per-request query.
					array(
						'key'     => self::META_KEY,
						'compare' => 'NOT EXISTS',
					),
				),
			)
		);

		foreach ( $post_ids as $post_id ) {
			add_post_meta( $post_id, self::META_KEY, 0, true );
		}

		update_option( self::BACKFILL_OPTION, 1 );
	}

	/**
	 * Counts one real front-end view per (post, dedupe window) pair.
	 * Skips: non-singular/non-main-query requests, previews, logged-in
	 * users (this project's only accounts are the four staff roles —
	 * CLAUDE.md's IA doc — never public readers, so a logged-in view is
	 * always staff, not a reader), known-bot user agents, and repeat
	 * views from the same browser within DEDUPE_WINDOW (a cookie, not a
	 * server-side session store — this site has no object cache to keep
	 * one in, and a cookie is sufficient to blunt simple refresh-spam).
	 *
	 * @return void
	 */
	public static function maybe_count_view() {
		if ( ! is_singular( self::POST_TYPES ) || ! is_main_query() ) {
			return;
		}
		if ( is_preview() || is_user_logged_in() ) {
			return;
		}
		if ( self::is_known_bot() ) {
			return;
		}

		$post_id = get_queried_object_id();
		if ( ! $post_id ) {
			return;
		}

		$cookie_name = self::COOKIE_PREFIX . $post_id;
		if ( isset( $_COOKIE[ $cookie_name ] ) ) {
			return;
		}

		self::increment( $post_id );

		if ( ! headers_sent() ) {
			setcookie( $cookie_name, '1', time() + self::DEDUPE_WINDOW, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN );
		}
	}

	/**
	 * Atomically increments the counter with a single `UPDATE ... SET
	 * meta_value = meta_value + 1` — deliberately not
	 * `get_post_meta()` + `update_post_meta()`, which reads a value and
	 * writes it back and can silently lose increments when two requests
	 * for the same popular post land concurrently (both read the same
	 * stale value, both write the same N+1). Falls back to
	 * `add_post_meta()` only for the rare case the row genuinely doesn't
	 * exist yet (pre-Phase-E content the backfill hasn't reached, or a
	 * race with `seed_on_publish()` itself).
	 *
	 * A raw `$wpdb` write like this bypasses WordPress's postmeta object
	 * cache entirely — `update_post_meta()`/`add_post_meta()` normally
	 * clear it for you, but a direct SQL `UPDATE` doesn't. Without the
	 * explicit `wp_cache_delete()` below, a later `get_post_meta()` call
	 * in the same request (or on any future request, if this site ever
	 * gains a persistent object cache such as Redis) would keep
	 * returning the pre-increment value even though the DB row is
	 * correct — found and fixed during this feature's own verification,
	 * not a hypothetical.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	private static function increment( $post_id ) {
		global $wpdb;

		$updated = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->postmeta} SET meta_value = meta_value + 1 WHERE post_id = %d AND meta_key = %s",
				$post_id,
				self::META_KEY
			)
		);

		if ( ! $updated ) {
			add_post_meta( $post_id, self::META_KEY, 1, true );
		} else {
			wp_cache_delete( $post_id, 'post_meta' );
		}
	}

	/**
	 * Lightweight known-bot user-agent check — not a replacement for
	 * Wordfence (CLAUDE.md §3), just enough to keep the most obvious
	 * crawler/scraper noise out of an editorial "most read" signal. A
	 * missing user-agent entirely is treated the same way real browsers
	 * never send an empty one.
	 *
	 * @return bool
	 */
	private static function is_known_bot() {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return true;
		}

		$ua      = strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) );
		$needles = array(
			'bot',
			'crawl',
			'spider',
			'slurp',
			'curl',
			'wget',
			'python-requests',
			'facebookexternalhit',
			'headless',
			'lighthouse',
			'pagespeed',
		);

		foreach ( $needles as $needle ) {
			if ( false !== strpos( $ua, $needle ) ) {
				return true;
			}
		}

		return false;
	}
}
