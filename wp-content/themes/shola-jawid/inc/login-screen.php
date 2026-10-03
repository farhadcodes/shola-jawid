<?php
/**
 * inc/login-screen.php — custom branding for wp-login.php, 2026-10-03,
 * per Farhad's explicit ask after sharing several reference screenshots:
 * the default WordPress login screen "feels generic," and he wants a
 * two-column, on-brand page instead, without touching anything about
 * how login/password-reset/2FA actually work.
 *
 * Deliberately does NOT re-implement or replace #loginform, the error/
 * message markup, the lost-password/register links, or the language
 * switcher — all of that is still 100% core WordPress output (so
 * Wordfence's own 2FA field, which hooks into this same #loginform,
 * keeps working untouched). This file only:
 *   1. Enqueues a dedicated stylesheet (assets/css/login.css) that
 *      re-skins the existing markup with this site's own brand tokens.
 *   2. Injects one new sibling element (the brand panel) via the
 *      `login_header` action — the earliest hook core provides inside
 *      `#login`.
 *   3. Points the (now visually hidden, but still present for screen
 *      readers) default logo link at the home page with the site name.
 *
 * The two-column layout itself is pure CSS (`display: grid` on `#login`,
 * see login.css) — every existing child of `#login` (the default h1,
 * any error/message markup, #loginform, the nav/backtoblog links, the
 * language switcher) is explicitly placed into the "form" grid column
 * regardless of DOM order, so core is free to output messages/errors
 * wherever it always has without needing a second pass of wrapper HTML
 * around them.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues the login screen's own stylesheet — scoped to wp-login.php
 * only via the `login_enqueue_scripts` hook, never loaded anywhere else.
 *
 * @return void
 */
function shola_login_enqueue_assets() {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'shola-jawid-login', get_theme_file_uri( 'assets/css/login.css' ), array( 'login' ), $version );
}
add_action( 'login_enqueue_scripts', 'shola_login_enqueue_assets' );

/**
 * `login_headerurl`/`login_headertext` — the default h1 logo link is
 * hidden visually (login.css), but core still renders it (and a
 * password manager or screen reader may still use its link text/href),
 * so it's pointed at the real home page/site name rather than left at
 * WordPress core's own default (wordpress.org / "Powered by WordPress").
 */
add_filter( 'login_headerurl', function () {
	return home_url( '/' );
} );
add_filter( 'login_headertext', function () {
	return get_bloginfo( 'name' );
} );

/**
 * Injects the brand panel markup right after `#login`'s own default h1
 * (the earliest point the `login_header` action fires). CSS grid
 * placement (login.css) is what actually positions this as a full
 * two-column side panel regardless of where in the DOM it physically
 * sits relative to the form/messages/links that follow it.
 *
 * Reuses the exact same `get_theme_mod( 'custom_logo' )` +
 * `wp_get_attachment_image()` + plain-text-nameplate-fallback pattern
 * already used by footer.php's own logo — same Customizer field, same
 * fallback behavior, no new place to manage the logo.
 *
 * @return void
 */
function shola_login_render_brand_panel() {
	$logo_id = get_theme_mod( 'custom_logo' );
	?>
	<div class="shola-login-brand" aria-hidden="true">
		<div class="shola-login-brand-inner">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="shola-login-brand-logo" tabindex="-1">
				<?php
				if ( $logo_id ) {
					echo wp_get_attachment_image(
						$logo_id,
						'full',
						false,
						array( 'loading' => 'eager' )
					);
				} else {
					bloginfo( 'name' );
				}
				?>
			</a>
			<h2 class="shola-login-brand-name"><?php bloginfo( 'name' ); ?></h2>
			<p class="shola-login-brand-tagline">
				<?php esc_html_e( 'ورود ویژهٔ اعضای هیئت تحریریه و مدیریت سایت.', 'shola-jawid' ); ?>
			</p>
		</div>
	</div>
	<?php
}
add_action( 'login_header', 'shola_login_render_brand_panel' );
