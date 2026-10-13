<?php
/**
 * Database housekeeping — P-5, docs/SECURITY_PERFORMANCE_PLAN.md
 * (2026-10-13). Covers the two real cleanup gaps WordPress core does
 * NOT already handle on its own:
 *
 * - **Post revisions**: core keeps every revision of every post
 *   forever unless `WP_POST_REVISIONS` is defined — not set anywhere
 *   in this project, confirmed by grep before writing this class, so
 *   the revisions table grows unbounded on an actively-edited site.
 * - **Expired transients**: core only deletes a transient's row when
 *   something actually *requests* that specific key after it expired
 *   (`get_transient()`'s own lazy-delete) — a transient nothing ever
 *   reads again just sits in `wp_options` forever.
 *
 * Deliberately does NOT duplicate what core already does on its own:
 * `wp_scheduled_delete` (a core cron event, active by default, no
 * override found anywhere in this project) already empties trashed
 * posts/comments after `EMPTY_TRASH_DAYS` (30) and spam comments after
 * 15 days — re-implementing that here would just be redundant, racing
 * code.
 *
 * Uses WP-Cron (`wp_schedule_event`), not a server crontab entry —
 * deliberately, so this runs with zero host-level setup required
 * (matches this project's "no production host access assumed" Track 1
 * scope). Documented caveat: WP-Cron only fires on an actual site
 * visit, so on a very low-traffic site a run could be delayed past its
 * exact scheduled time — acceptable here since this is routine
 * housekeeping, not anything time-critical.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Scheduled cleanup of old post revisions and expired transients.
 */
class Db_Housekeeping {

	/**
	 * Cron hook name.
	 */
	const CRON_HOOK = 'shcore_daily_db_housekeeping';

	/**
	 * Revisions older than this many days are deleted. 90 days keeps a
	 * generous real editing-history window (far beyond what anyone
	 * realistically needs to roll back to) while still bounding growth
	 * on an actively-edited site.
	 */
	const REVISION_MAX_AGE_DAYS = 90;

	/**
	 * Upper bound on how many revisions are deleted in a single cron
	 * run — keeps one run fast and avoids a timeout/memory spike on a
	 * low-end shared-hosting PHP worker if a large backlog exists the
	 * first time this ever runs.
	 */
	const REVISIONS_PER_RUN = 300;

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( self::CRON_HOOK, array( __CLASS__, 'run' ) );
		// Also scheduled here, not just on the plugin's activation hook —
		// an already-active install (e.g. production, after this ships
		// in a normal update) never re-fires activation, so without
		// this the cron event would simply never get scheduled there.
		// `wp_next_scheduled()` is a cheap single indexed lookup, safe
		// to check on every load.
		self::schedule();
	}

	/**
	 * Schedules the daily cron event if it isn't already — called from
	 * the plugin's activation hook, same pattern as the rewrite-rule
	 * flush already there.
	 *
	 * @return void
	 */
	public static function schedule() {
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time(), 'daily', self::CRON_HOOK );
		}
	}

	/**
	 * Unschedules the cron event — called from the plugin's
	 * deactivation hook, so nothing lingers after the plugin is gone.
	 *
	 * @return void
	 */
	public static function unschedule() {
		$timestamp = wp_next_scheduled( self::CRON_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::CRON_HOOK );
		}
	}

	/**
	 * The actual cleanup pass. Public (not private) so it can also be
	 * triggered manually via `wp eval '\SholaCore\Db_Housekeeping::run();'`
	 * for an on-demand run without waiting for the next cron tick.
	 *
	 * @return void
	 */
	public static function run() {
		self::delete_old_revisions();
		self::delete_expired_transients();
	}

	/**
	 * Deletes post revisions older than REVISION_MAX_AGE_DAYS, via
	 * `wp_delete_post_revision()` (not a raw DB delete) so WordPress's
	 * own related-meta cleanup runs for each one too, not just the
	 * `wp_posts` row.
	 *
	 * @return void
	 */
	private static function delete_old_revisions() {
		$cutoff = gmdate( 'Y-m-d H:i:s', time() - ( self::REVISION_MAX_AGE_DAYS * DAY_IN_SECONDS ) );

		$revision_ids = get_posts(
			array(
				'post_type'      => 'revision',
				'post_status'    => 'inherit',
				'date_query'     => array(
					array(
						'column' => 'post_date_gmt',
						'before' => $cutoff,
					),
				),
				'fields'         => 'ids',
				'posts_per_page' => self::REVISIONS_PER_RUN,
				'orderby'        => 'post_date',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);

		foreach ( $revision_ids as $revision_id ) {
			wp_delete_post_revision( $revision_id );
		}
	}

	/**
	 * Deletes expired transients (and site-transients, harmless no-op
	 * check on a non-multisite install) directly via `$wpdb` — there is
	 * no core API for "find every expired transient regardless of
	 * whether anything has requested it," only the lazy per-key
	 * deletion `get_transient()` does on access. Matches the same
	 * query shape WP-CLI's own `wp transient delete-expired` uses.
	 *
	 * @return void
	 */
	private static function delete_expired_transients() {
		global $wpdb;

		$now = time();

		// Site-wide transients (`_transient_*`): find expired timeout
		// rows, then delete both the timeout row and its paired value
		// row for each one.
		$expired_timeouts = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value < %d",
				$wpdb->esc_like( '_transient_timeout_' ) . '%',
				$now
			)
		);

		foreach ( $expired_timeouts as $timeout_option ) {
			$key = str_replace( '_transient_timeout_', '', $timeout_option );
			delete_option( '_transient_timeout_' . $key );
			delete_option( '_transient_' . $key );
		}

		// Network-wide site-transients (`_site_transient_*`), stored as
		// regular options on a single-site install (which this is) —
		// same shape, included for completeness/future-proofing if this
		// project is ever networked.
		$expired_site_timeouts = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value < %d",
				$wpdb->esc_like( '_site_transient_timeout_' ) . '%',
				$now
			)
		);

		foreach ( $expired_site_timeouts as $timeout_option ) {
			$key = str_replace( '_site_transient_timeout_', '', $timeout_option );
			delete_option( '_site_transient_timeout_' . $key );
			delete_option( '_site_transient_' . $key );
		}
	}
}
