<?php
// inc/customizer.php — Customizer control tweaks (Site Identity → Logo,
// footer tagline).

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
 * Also adds a new `shola_footer_tagline` setting/control to the same
 * native Site Identity section — 2026-10-03, per Farhad's ask: the party
 * description shown under the footer logo (`.footer-tagline`,
 * footer.php) was a hardcoded string, so changing it meant editing code
 * and re-deploying. A `textarea` theme_mod control puts it in
 * Appearance → Customize → Site Identity, right alongside the logo and
 * native Site Title/Tagline fields it sits next to visually in the
 * footer — no new admin page needed for one field. `sanitize_textarea_field`
 * (plain text, no HTML/links) matches what this field actually is; the
 * default is the original hardcoded sentence, so nothing changes for the
 * client until they actually edit it.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 * @return void
 */
function shola_customize_register( $wp_customize ) {
	$logo_control = $wp_customize->get_control( 'custom_logo' );
	if ( $logo_control ) {
		$logo_control->description = __( 'برای بهترین کیفیت، تصویر PNG یا WebP با پس‌زمینهٔ شفاف و حداقل ۴۰۰ در ۴۰۰ پیکسل بارگذاری کنید.', 'shola-jawid' );
	}

	$wp_customize->add_setting(
		'shola_footer_tagline',
		array(
			'default'           => shola_get_default_footer_tagline(),
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'shola_footer_tagline',
		array(
			'type'        => 'textarea',
			'section'     => 'title_tagline',
			'label'       => __( 'توضیح پانویس (زیر نشان حزب)', 'shola-jawid' ),
			'description' => __( 'متنی که زیر نشان و نام سایت، در پانویس هر صفحه نمایش داده می‌شود.', 'shola-jawid' ),
		)
	);
}
add_action( 'customize_register', 'shola_customize_register' );

/**
 * Default/fallback value for `shola_footer_tagline` above — pulled into
 * its own function so footer.php's `get_theme_mod()` call and the
 * Customizer setting's own `default` always agree on the exact same
 * string, with no risk of the two drifting apart if either is edited
 * later.
 *
 * @return string
 */
function shola_get_default_footer_tagline() {
	return __( 'پلتفرم نشر دوزبانه برای مقالات، یادداشت‌ها و اسناد؛ با آرشیو کامل نشرات «شعله جاوید» و «جهان برای فتح».', 'shola-jawid' );
}
