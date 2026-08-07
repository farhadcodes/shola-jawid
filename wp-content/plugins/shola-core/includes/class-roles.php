<?php
/**
 * Maps the IA doc §7 role table onto WordPress's native roles. Three of
 * the four (مدیر/نویسنده/همکار → Administrator/Author/Contributor) match
 * WP's stock capability sets exactly, no changes needed. One gap: the IA
 * doc gives سردبیر (Editor) "manage categories & menus," but stock WP's
 * Editor role can manage categories, not menus — nav-menu editing needs
 * `edit_theme_options`, which WP only grants Administrator by default.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Role/capability adjustments.
 */
class Roles {

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'maybe_grant_editor_menu_access' ) );
	}

	/**
	 * Grants `edit_theme_options` to Editor so it can manage nav menus,
	 * per the IA doc. No narrower "menus only" capability exists in WP
	 * core — this also unlocks Customizer/widgets for Editor, a broader
	 * reach than the doc's "menus" phrasing alone implies; approved by
	 * Farhad as the simplest option over a bespoke per-screen capability
	 * (2026-08-07, see docs/CHANGELOG.md).
	 *
	 * `WP_Role::add_cap()` only writes to the DB when the capability
	 * isn't already set to the target value, so gating this behind
	 * `admin_init` (checked on every admin request rather than only on
	 * plugin activation) stays cheap and also self-heals if a role reset
	 * ever clears it, at the cost of one `in_array()`/`has_cap()` check
	 * per admin page load — an acceptable, standard trade-off for a
	 * four-role site.
	 *
	 * @return void
	 */
	public static function maybe_grant_editor_menu_access() {
		$editor = get_role( 'editor' );

		if ( $editor && ! $editor->has_cap( 'edit_theme_options' ) ) {
			$editor->add_cap( 'edit_theme_options' );
		}
	}
}
