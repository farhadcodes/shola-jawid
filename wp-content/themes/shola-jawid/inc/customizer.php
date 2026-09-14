<?php
// inc/customizer.php — Customizer control tweaks (Site Identity → Logo).

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a short help sentence under the native Site Identity → Logo
 * upload control (2026-09-14, `logo` masthead_section layout) — Farhad's
 * explicit ask: whoever uploads a replacement logo should see the
 * recommended size/format right there, not have to guess. Modifies the
 * existing `custom_logo` control WP core already registers (from
 * add_theme_support('custom-logo') in inc/setup.php) rather than adding
 * a second, separate logo field — one logo, one native place to set it.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 * @return void
 */
function shola_customize_register( $wp_customize ) {
	$logo_control = $wp_customize->get_control( 'custom_logo' );
	if ( $logo_control ) {
		$logo_control->description = __( 'برای بهترین کیفیت، تصویر PNG یا WebP با پس‌زمینهٔ شفاف و حداقل ۴۰۰ در ۴۰۰ پیکسل بارگذاری کنید.', 'shola-jawid' );
	}
}
add_action( 'customize_register', 'shola_customize_register' );
