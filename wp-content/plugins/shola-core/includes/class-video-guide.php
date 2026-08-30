<?php
/**
 * Admin-managed list of Farhad's unlisted YouTube tutorial videos
 * (how to use the dashboard, publish content, etc.). Two entry points,
 * same underlying data and rendering, different access rules:
 *   - wp-admin: Settings → راهنمای ویدیویی (add_options_page(), same
 *     shape as Label_Settings/Social_Links_Settings/Contact_Settings —
 *     one option, one settings page, one sanitize callback, Settings
 *     API for the nonce/capability/save plumbing) — the "adding/
 *     editing" area: a paired title/URL repeater form
 *     (video-guide-admin.js), not a thumbnail preview (see
 *     render_settings_page()'s docblock for why that preview was
 *     removed, 2026-08-30). Still strictly manage_options-only,
 *     unaffected by the front-end change below.
 *   - Front end: /video-guide (2026-08-28, docs/CHANGELOG.md) — the
 *     "watching" area: a bookmarkable URL for the click-to-play
 *     thumbnail grid, registered as a custom rewrite rule + query var
 *     + template_redirect gate (see register_rewrite()/
 *     maybe_render_front_end_page() below), not a real WP Page/post.
 *     A logged-in Administrator always gets straight through. Anyone
 *     else sees a password form (2026-08-30, Farhad's explicit
 *     request, docs/CHANGELOG.md) rather than being redirected to
 *     wp-login.php — a deliberate, documented reversal of this
 *     route's original "real capability check, not an
 *     unguessable-URL approach" design, specifically so the page can
 *     be shared with people who don't have a WordPress account here.
 *     See maybe_render_front_end_page()'s docblock for the full
 *     mechanics (shared password option, HMAC-signed unlock cookie).
 *     Deliberately does not inherit the public theme (no
 *     get_header()/get_footer()) — a minimal standalone shell reusing
 *     the same admin CSS/JS, plus noindex/nofollow as defense in
 *     depth (still meaningful even with the password gate — the page
 *     still isn't discoverable by search/browsing). Not linked from
 *     any menu, sitemap, or public navigation anywhere. A "مشاهدهٔ
 *     ویدیوها" button on the wp-admin screen (render_settings_page())
 *     is the one-click bridge between the two areas.
 * The wp-admin screen is not public-facing at all; the front-end route
 * is reachable by anyone with both the URL and the current password —
 * no shortcode, no theme template, no sitemap/search-index exposure
 * either way.
 *
 * Entries are added/edited as paired title+URL fields (a real
 * repeater — "افزودن ویدیوی دیگر" clones a row, "حذف" removes one),
 * not the free-text "عنوان | آدرس یوتیوب" bulk-edit textarea this
 * screen originally shipped with (2026-08-27) — replaced 2026-08-30
 * per Farhad's explicit report that the line-based text format was
 * hard to hand-edit correctly (see sanitize_entries()'s docblock).
 * Rendered as a click-to-play thumbnail grid (2026-08-27,
 * docs/CHANGELOG.md), not a plain link list — thumbnails come from
 * YouTube's own static-image CDN (no stored thumbnail field, no API
 * key) and clicking one swaps it in place for a live iframe embed
 * (video-guide.js), rather than navigating to youtube.com. Requires
 * the videos to be YouTube-"Unlisted," not "Private": a Private
 * video's thumbnail/embed only work for Google accounts individually
 * authorized on that exact video, which has nothing to do with this
 * site's own login — confirmed with Farhad before building this,
 * see docs/CHANGELOG.md.
 *
 * Menu placement deliberately matches every other shola-core settings
 * screen: add_options_page() under Settings, not a top-level
 * add_menu_page(). A top-level page was considered for the dashicon
 * requested alongside this feature, but Settings-submenu items don't
 * support icons in wp-admin at all (only top-level menu items do) —
 * this class picks matching this plugin's existing all-Settings-
 * submenu footprint over getting an icon on one single screen, per
 * the task's own "your call" discretion on placement — flagged to
 * Farhad for override rather than assumed final, see docs/CHANGELOG.md.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings screen for the internal video-guide link list.
 */
class Video_Guide {

	/**
	 * Option name storing the parsed entries (indexed array of
	 * {section, title, url} associative arrays).
	 *
	 * @var string
	 */
	const OPTION_NAME = 'shcore_video_guide_entries';

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_setting' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_action( 'init', array( __CLASS__, 'register_rewrite' ) );
		add_filter( 'query_vars', array( __CLASS__, 'register_query_var' ) );
		add_action( 'template_redirect', array( __CLASS__, 'maybe_render_front_end_page' ) );
	}

	/**
	 * Adds the /video-guide front-end route — same add_rewrite_rule()
	 * mechanism Taxonomies::register_topic_rewrite() already uses for
	 * /topics/{topic}/{slug}, applied here to a fixed path with no
	 * dynamic segment. Requires a one-time rewrite-rule flush to take
	 * effect, which shcore_activate() (shola-core.php) now includes —
	 * this survives a fresh deploy/reactivation, not just a manual
	 * one-time flush (see the class docblock for why a flush on every
	 * page load was deliberately not used instead).
	 *
	 * @return void
	 */
	public static function register_rewrite() {
		add_rewrite_rule( '^video-guide/?$', 'index.php?shcore_video_guide=1', 'top' );
	}

	/**
	 * Whitelists the shcore_video_guide query var so get_query_var()
	 * can see it — WordPress only exposes query vars added via a
	 * rewrite rule's target if they're also registered here, same
	 * pattern View_Counter::register_sort_query_var() already uses.
	 *
	 * @param string[] $vars Public query vars.
	 * @return string[]
	 */
	public static function register_query_var( $vars ) {
		$vars[] = 'shcore_video_guide';
		return $vars;
	}

	/**
	 * Option name storing the shared password for non-admin access to
	 * /video-guide (see class docblock's 2026-08-30 "public sharing"
	 * note). Plain text, same convention WP core itself uses for
	 * `post_password` on password-protected posts/pages — this is a
	 * shared secret for casual sharing, not a real user account, so
	 * that's an appropriate (not a weakened) standard to hold it to.
	 *
	 * @var string
	 */
	const PASSWORD_OPTION_NAME = 'shcore_video_guide_password';

	/**
	 * Gate for the /video-guide front-end route. A logged-in
	 * Administrator (manage_options) always gets straight through, as
	 * before. Anyone else — logged out, or logged in without that
	 * capability — sees a small password form instead of being
	 * redirected to wp-login.php; a correct password sets a signed
	 * unlock cookie (has_valid_unlock_cookie()/set_unlock_cookie()) so
	 * they don't have to re-enter it on every visit. No password
	 * configured (get_password() returns '') means nobody gets in
	 * this way — only admins, same as before this feature existed.
	 *
	 * Deliberate, explicit policy change from this page's original
	 * "real capability check, not an unguessable-URL approach" design
	 * — added 2026-08-30 at Farhad's explicit request, specifically so
	 * this can be shared with people who don't have a WordPress
	 * account on this site. See docs/CHANGELOG.md for the full
	 * decision record, including the accepted limitation that failed-
	 * password attempts on this route aren't separately rate-limited
	 * (Wordfence's brute-force protection is scoped to wp-login.php,
	 * not custom routes like this one) — acceptable for a shared
	 * secret meant for short-term casual sharing, not a real account
	 * credential.
	 *
	 * @return void
	 */
	public static function maybe_render_front_end_page() {
		if ( ! get_query_var( 'shcore_video_guide' ) ) {
			return;
		}

		if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
			self::render_front_end_page();
			exit;
		}

		if ( isset( $_POST['shcore_vg_password'] ) ) {
			$submitted = sanitize_text_field( wp_unslash( $_POST['shcore_vg_password'] ) );
			if ( self::password_matches( $submitted ) ) {
				self::set_unlock_cookie();
				self::render_front_end_page();
				exit;
			}
			self::render_password_gate( true );
			exit;
		}

		if ( self::has_valid_unlock_cookie() ) {
			self::render_front_end_page();
			exit;
		}

		self::render_password_gate( false );
		exit;
	}

	/**
	 * Reads the configured shared password. Empty string (the default
	 * — no password ever set) means the password gate always fails,
	 * so a fresh install stays admin-only exactly as before this
	 * feature was added; nobody accidentally ships with an open door.
	 *
	 * @return string
	 */
	public static function get_password() {
		return (string) get_option( self::PASSWORD_OPTION_NAME, '' );
	}

	/**
	 * Timing-safe comparison (hash_equals(), not ===) against the
	 * configured password. Empty stored password always fails, even
	 * against an empty submission — an unset password must never be
	 * satisfiable by submitting nothing.
	 *
	 * @param string $submitted Raw submitted password, already sanitize_text_field()'d.
	 * @return bool
	 */
	private static function password_matches( $submitted ) {
		$stored = self::get_password();
		if ( '' === $stored ) {
			return false;
		}
		return hash_equals( $stored, $submitted );
	}

	/**
	 * The unlock cookie's name, namespaced with WordPress's own
	 * COOKIEHASH (derived from the site URL) — same convention core
	 * uses for its own auth cookies, avoids collisions if this site
	 * ever shares a cookie domain with another WP install.
	 *
	 * @return string
	 */
	private static function unlock_cookie_name() {
		return 'shcore_vg_unlock_' . COOKIEHASH;
	}

	/**
	 * The unlock cookie's expected value: an HMAC of the current
	 * password, keyed with one of WordPress's own secret salts —
	 * deliberately not the password itself, so the cookie value never
	 * discloses the password even if intercepted. A useful side
	 * effect of deriving it from the password rather than storing a
	 * separate token: changing the password immediately invalidates
	 * every previously-issued cookie (the HMAC no longer matches),
	 * with no separate revocation list to maintain.
	 *
	 * @return string
	 */
	private static function expected_unlock_token() {
		return hash_hmac( 'sha256', self::get_password(), wp_salt( 'auth' ) );
	}

	/**
	 * Whether the current request's cookie proves a correct password
	 * was submitted previously (for the *current* password — see
	 * expected_unlock_token()'s docblock on why a password change
	 * invalidates old cookies automatically).
	 *
	 * @return bool
	 */
	private static function has_valid_unlock_cookie() {
		if ( '' === self::get_password() ) {
			return false;
		}
		if ( empty( $_COOKIE[ self::unlock_cookie_name() ] ) ) {
			return false;
		}
		return hash_equals( self::expected_unlock_token(), sanitize_text_field( wp_unslash( $_COOKIE[ self::unlock_cookie_name() ] ) ) );
	}

	/**
	 * Sets the unlock cookie after a correct password submission — 30
	 * days, httponly (never needed from JS), matching the scheme
	 * (`is_ssl()`) and path/domain conventions WordPress's own auth
	 * cookies use. Must run before any output — called from
	 * maybe_render_front_end_page() before render_front_end_page()
	 * echoes anything, same header-timing constraint the earlier
	 * wp_safe_redirect() call already had to respect.
	 *
	 * @return void
	 */
	private static function set_unlock_cookie() {
		setcookie(
			self::unlock_cookie_name(),
			self::expected_unlock_token(),
			time() + 30 * DAY_IN_SECONDS,
			COOKIEPATH,
			COOKIE_DOMAIN,
			is_ssl(),
			true
		);
	}

	/**
	 * The password-entry page shown to anyone who isn't an already-
	 * logged-in Administrator and doesn't have a valid unlock cookie
	 * yet. Same noindex/nofollow + standalone-shell treatment as
	 * render_front_end_page() — still never linked from anywhere
	 * crawlable, the password is defense in depth on top of that, not
	 * a replacement for it.
	 *
	 * @param bool $show_error Whether the previous submission was wrong.
	 * @return void
	 */
	private static function render_password_gate( $show_error ) {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="robots" content="noindex, nofollow">
			<title><?php esc_html_e( 'راهنمای ویدیویی', 'shola-core' ); ?></title>
			<style>
				body { font-family: "Tahoma", sans-serif; background: #FAF8F3; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
				.shcore-vg-gate { background: #fff; border: 1px solid #E6E5E1; border-radius: 6px; padding: 2rem; max-width: 320px; width: 90%; text-align: center; }
				.shcore-vg-gate h1 { font-size: 1.15rem; margin: 0 0 1rem; color: #0F0F0F; }
				.shcore-vg-gate input[type="password"] { width: 100%; padding: 0.6rem; margin-block-end: 1rem; border: 1px solid #E6E5E1; border-radius: 4px; font-size: 1rem; box-sizing: border-box; }
				.shcore-vg-gate button { width: 100%; padding: 0.6rem; background: #8E1B1B; color: #fff; border: 0; border-radius: 4px; font-size: 1rem; cursor: pointer; }
				.shcore-vg-gate .error { color: #8E1B1B; font-size: 0.85rem; margin-block-end: 1rem; }
			</style>
		</head>
		<body>
			<form class="shcore-vg-gate" method="post">
				<h1><?php esc_html_e( 'راهنمای ویدیویی', 'shola-core' ); ?></h1>
				<?php if ( $show_error ) : ?>
					<p class="error"><?php esc_html_e( 'رمز عبور نادرست است.', 'shola-core' ); ?></p>
				<?php endif; ?>
				<input type="password" name="shcore_vg_password" placeholder="<?php esc_attr_e( 'رمز عبور', 'shola-core' ); ?>" autofocus />
				<button type="submit"><?php esc_html_e( 'ورود', 'shola-core' ); ?></button>
			</form>
		</body>
		</html>
		<?php
	}

	/**
	 * The /video-guide standalone page shell — deliberately does not
	 * call get_header()/get_footer() or otherwise go through the public
	 * theme: this is an internal tool, not editorial content, and
	 * inheriting the theme's public chrome risks it reading as a public
	 * page. Reuses the same admin CSS/JS as the wp-admin settings
	 * screen (plain CSS/vanilla JS, nothing wp-admin-specific in
	 * either) rather than duplicating them. noindex/nofollow is
	 * defense in depth on top of the capability gate above — this page
	 * was never going to be linked from anywhere crawlable, but costs
	 * nothing to add.
	 *
	 * @return void
	 */
	private static function render_front_end_page() {
		$css_url = add_query_arg( 'ver', SHCORE_VERSION, SHCORE_URL . 'admin/css/video-guide.css' );
		$js_url  = add_query_arg( 'ver', SHCORE_VERSION, SHCORE_URL . 'admin/js/video-guide.js' );
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="robots" content="noindex, nofollow">
			<title><?php esc_html_e( 'راهنمای ویدیویی', 'shola-core' ); ?></title>
			<link rel="stylesheet" href="<?php echo esc_url( $css_url ); ?>">
			<style><?php echo self::get_font_face_css(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static string built entirely from get_theme_file_uri()/esc_url(), no user input. ?></style>
		</head>
		<body>
			<div class="shcore-vg">
				<header class="shcore-vg-header">
					<div class="shcore-vg-header-inner">
						<h1 class="shcore-vg-title"><?php esc_html_e( 'راهنمای ویدیویی', 'shola-core' ); ?></h1>
						<p class="shcore-vg-subtitle"><?php esc_html_e( 'فهرست ویدیوهای آموزشی داخلی — فقط برای مدیران سایت.', 'shola-core' ); ?></p>
					</div>
				</header>
				<div class="shcore-vg-body">
					<?php self::render_grid( self::get_entries() ); ?>
				</div>
			</div>
			<script src="<?php echo esc_url( $js_url ); ?>"></script>
		</body>
		</html>
		<?php
	}

	/**
	 * The Farhang2 @font-face rules this page needs (400/600/800 —
	 * exactly the weights the restyle spec calls for), built with real
	 * absolute URLs via get_theme_file_uri() rather than a hand-written
	 * relative path in the static CSS file — a relative url() from
	 * wp-content/plugins/shola-core/admin/css/ across into
	 * wp-content/themes/shola-jawid/assets/fonts/ is exactly the kind
	 * of thing that's easy to get subtly wrong (an earlier draft of
	 * this file did, by one directory level) and hard to verify without
	 * live-loading the page. Same font files main.css already loads for
	 * the public theme — not duplicated assets, just referenced from
	 * a second place. Injected as inline CSS (wp_add_inline_style() for
	 * the admin screen, a <style> tag for the front-end route) rather
	 * than shipped in video-guide.css itself.
	 *
	 * @return string
	 */
	public static function get_font_face_css() {
		$weights = array(
			400 => 'Farhang2-Regular',
			600 => 'Farhang2-DemiBold',
			800 => 'Farhang2-ExtraBold',
		);

		$css = '';
		foreach ( $weights as $weight => $file ) {
			$url  = esc_url( get_theme_file_uri( 'assets/fonts/farhang2/woff2/' . $file . '.woff2' ) );
			$css .= sprintf(
				'@font-face{font-family:"Farhang2";font-style:normal;font-weight:%1$d;font-display:swap;src:url("%2$s") format("woff2");}',
				(int) $weight,
				$url
			);
		}

		return $css;
	}

	/**
	 * Extracts an 11-character YouTube video ID from any of the common
	 * URL shapes (watch?v=, youtu.be/, /embed/, /shorts/, /live/) —
	 * whatever domain/subdomain/scheme, and regardless of extra query
	 * params before or after. Not exhaustive: playlist-only URLs, or a
	 * URL where the ID has been mistyped to something other than 11
	 * characters, won't match. Returns '' rather than guessing when it
	 * can't find one — callers render a "no preview" fallback instead
	 * of a broken thumbnail/embed.
	 *
	 * @param string $url Stored video URL (already esc_url_raw()'d at save time).
	 * @return string 11-character video ID, or '' if not recognized.
	 */
	public static function get_video_id( $url ) {
		if ( preg_match( '/[?&]v=([A-Za-z0-9_-]{11})/', $url, $matches ) ) {
			return $matches[1];
		}

		if ( preg_match( '#(?:youtu\.be/|/(?:embed|shorts|live)/)([A-Za-z0-9_-]{11})#', $url, $matches ) ) {
			return $matches[1];
		}

		return '';
	}

	/**
	 * YouTube's standard static-thumbnail CDN URL — no API key, no
	 * stored thumbnail field to keep in sync with the URL. hqdefault
	 * (480×360) rather than maxresdefault: the latter doesn't exist for
	 * every video (older/lower-resolution uploads), and a missing
	 * maxresdefault serves YouTube's generic broken-image icon instead
	 * of falling back — hqdefault is generated for effectively every
	 * video, unlisted included.
	 *
	 * @param string $video_id 11-character YouTube video ID.
	 * @return string
	 */
	public static function get_thumbnail_url( $video_id ) {
		return 'https://img.youtube.com/vi/' . rawurlencode( $video_id ) . '/hqdefault.jpg';
	}

	/**
	 * Enqueue the thumbnail-grid click-to-play script/style, only on
	 * this settings screen — $hook is the exact suffix add_settings_page()
	 * returns and registers, same gating pattern as
	 * Meta_Fields::enqueue_admin_assets().
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public static function enqueue_admin_assets( $hook ) {
		if ( 'settings_page_shcore-video-guide' !== $hook ) {
			return;
		}

		wp_enqueue_script(
			'shcore-video-guide',
			SHCORE_URL . 'admin/js/video-guide.js',
			array(),
			SHCORE_VERSION,
			true
		);
		wp_enqueue_script(
			'shcore-video-guide-admin',
			SHCORE_URL . 'admin/js/video-guide-admin.js',
			array(),
			SHCORE_VERSION,
			true
		);
		wp_enqueue_style(
			'shcore-video-guide',
			SHCORE_URL . 'admin/css/video-guide.css',
			array(),
			SHCORE_VERSION
		);
		wp_add_inline_style( 'shcore-video-guide', self::get_font_face_css() );
	}

	/**
	 * Read the current entries. Never returns anything but a well-formed
	 * list of {section, title, url} arrays — sanitize_entries() is the
	 * only place that writes this option, so a fresh/never-saved site
	 * simply gets an empty list, not a malformed one.
	 *
	 * @return array<int, array{section: string, title: string, url: string}>
	 */
	public static function get_entries() {
		$entries = get_option( self::OPTION_NAME, array() );

		return is_array( $entries ) ? $entries : array();
	}

	/**
	 * Register the entries option and the shared front-end password —
	 * both in the same settings group so one form/submit saves both
	 * (see render_settings_page()). The password field is what lets
	 * Farhad change/set it himself from wp-admin without needing
	 * WP-CLI/SSH access to production — added 2026-08-30 after
	 * discovering the option had no way to be set on a site he can't
	 * shell into (see docs/CHANGELOG.md for the full story).
	 *
	 * @return void
	 */
	public static function register_setting() {
		register_setting(
			'shcore_video_guide_settings',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_entries' ),
				'default'           => array(),
			)
		);
		register_setting(
			'shcore_video_guide_settings',
			self::PASSWORD_OPTION_NAME,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitize_password' ),
				'default'           => '',
			)
		);
	}

	/**
	 * Plain sanitize_text_field() + trim() — this is a shared secret
	 * for casual sharing (see PASSWORD_OPTION_NAME's docblock), not a
	 * real user-account password needing hashing on this end.
	 *
	 * @param mixed $raw Raw submitted value.
	 * @return string
	 */
	public static function sanitize_password( $raw ) {
		return sanitize_text_field( trim( (string) $raw ) );
	}

	/**
	 * Parses the submitted repeater rows into the stored entry list.
	 * Expects $raw as an indexed array of {title, url} sub-arrays — the
	 * shape the Settings API hands this callback from
	 * shcore_video_guide_entries[N][title]/[url] fields (see
	 * render_settings_page()/video-guide-admin.js). Each row's title
	 * (sanitize_text_field) and URL (esc_url_raw) are sanitized
	 * independently; a row missing either after sanitizing (e.g. an
	 * empty row left over from clicking "افزودن ویدیوی دیگر" without
	 * filling it in) is silently dropped rather than stored broken —
	 * Farhad can just re-add it, this isn't a validated form that needs
	 * to error.
	 *
	 * Replaced the original line-based "عنوان | آدرس" bulk-textarea
	 * format (2026-08-27) with this paired-field repeater UI
	 * (2026-08-30, docs/CHANGELOG.md) per Farhad's explicit request —
	 * the free-text format was hard to hand-edit correctly. The
	 * `##`-prefixed section-header line that format supported has no
	 * equivalent in this UI; every entry saved through it gets
	 * `section => ''` (flat list). render_grid() still groups by
	 * `section` when the value is non-empty, so a `section` set some
	 * other way (e.g. directly in the database) still displays
	 * correctly — this UI just doesn't offer a way to set one anymore.
	 *
	 * @param mixed $raw Raw submitted value.
	 * @return array<int, array{section: string, title: string, url: string}>
	 */
	public static function sanitize_entries( $raw ) {
		$raw = is_array( $raw ) ? $raw : array();

		$result = array();
		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$title = isset( $row['title'] ) ? sanitize_text_field( trim( $row['title'] ) ) : '';
			$url   = isset( $row['url'] ) ? esc_url_raw( trim( $row['url'] ) ) : '';

			if ( '' === $title || '' === $url ) {
				continue;
			}

			$result[] = array(
				'section' => '',
				'title'   => $title,
				'url'     => $url,
			);
		}

		return $result;
	}

	/**
	 * Registers the settings page under Settings → راهنمای ویدیویی.
	 *
	 * @return void
	 */
	public static function add_settings_page() {
		add_options_page(
			__( 'راهنمای ویدیویی', 'shola-core' ),
			__( 'راهنمای ویدیویی', 'shola-core' ),
			'manage_options',
			'shcore-video-guide',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Renders the thumbnail grid markup only (grouped under their
	 * section heading where one is set, otherwise flat) — no page
	 * chrome, no form. Shared by both render_settings_page() (the
	 * wp-admin screen) and the front-end route's
	 * render_front_end_page(), so a future styling or video-ID-parsing
	 * change only has to happen once. An entry whose URL doesn't yield
	 * a recognized video ID (get_video_id()) falls back to a plain
	 * "open in YouTube" link instead of a broken thumbnail.
	 *
	 * @param array<int, array{section: string, title: string, url: string}> $entries As returned by get_entries().
	 * @return void
	 */
	public static function render_grid( $entries ) {
		if ( ! $entries ) {
			?>
			<p class="shcore-vg-empty"><?php esc_html_e( 'هنوز ویدیویی افزوده نشده است.', 'shola-core' ); ?></p>
			<?php
			return;
		}

		$grouped = array();
		foreach ( $entries as $entry ) {
			$grouped[ $entry['section'] ][] = $entry;
		}
		?>
		<?php foreach ( $grouped as $section => $section_entries ) : ?>
			<?php if ( '' !== $section ) : ?>
				<h2 class="shcore-video-section-heading"><?php echo esc_html( $section ); ?></h2>
			<?php endif; ?>
			<div class="shcore-video-grid">
				<?php foreach ( $section_entries as $entry ) : ?>
					<?php $video_id = self::get_video_id( $entry['url'] ); ?>
					<div class="shcore-video-card">
						<?php if ( $video_id ) : ?>
							<button
								type="button"
								class="shcore-video-thumb"
								data-video-id="<?php echo esc_attr( $video_id ); ?>"
								data-video-title="<?php echo esc_attr( $entry['title'] ); ?>"
								aria-label="<?php echo esc_attr( $entry['title'] ); ?>"
							>
								<img src="<?php echo esc_url( self::get_thumbnail_url( $video_id ) ); ?>" alt="" loading="lazy" />
							</button>
						<?php else : ?>
							<p class="shcore-video-noid">
								<?php esc_html_e( 'شناسهٔ ویدیو از این آدرس قابل تشخیص نیست.', 'shola-core' ); ?>
								<a href="<?php echo esc_url( $entry['url'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'باز کردن در یوتیوب', 'shola-core' ); ?></a>
							</p>
						<?php endif; ?>
						<span class="shcore-video-title"><?php echo esc_html( $entry['title'] ); ?></span>
						<?php /* Reserved for a future duration/date line — deliberately empty, no placeholder text (:empty { display: none; } in video-guide.css hides it until real data exists). */ ?>
						<span class="shcore-video-meta"></span>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
		<?php
	}

	/**
	 * Renders the wp-admin settings screen: intro text, the "مشاهدهٔ
	 * ویدیوها" link out to /video-guide (the actual watching
	 * experience — this screen no longer also renders the thumbnail
	 * grid itself; Farhad found duplicating it here, alongside broken/
	 * mismatched thumbnail previews in some browser states, more
	 * confusing than useful, 2026-08-30, docs/CHANGELOG.md), then the
	 * paired title/URL repeater form (also 2026-08-30 — replaced the
	 * original bulk-edit textarea, per Farhad's explicit request that
	 * the free-text "عنوان | آدرس" line format was hard to hand-edit
	 * correctly).
	 *
	 * @return void
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$entries = self::get_entries();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'راهنمای ویدیویی', 'shola-core' ); ?></h1>
			<p><?php esc_html_e( 'فهرست ویدیوهای آموزشی داخلی (یوتیوب، خصوصی/فهرست‌نشده) — فقط برای مدیران سایت، در هیچ‌کجای سایت عمومی نمایش داده نمی‌شود.', 'shola-core' ); ?></p>

			<?php
			/*
			 * "مشاهدهٔ ویدیوها" button — a one-click way to reach the
			 * dedicated /video-guide watching page (the front-end route,
			 * clean grid only, no edit form) from here, per Farhad's
			 * 2026-08-30 request: this Settings screen is the "adding/
			 * editing" area, /video-guide is the "watching" area, and
			 * this button is the bridge between them. target="_blank" —
			 * deliberately opens in a new tab so the editor keeps this
			 * screen open too.
			 */
			?>
			<p>
				<a href="<?php echo esc_url( home_url( '/video-guide/' ) ); ?>" target="_blank" rel="noopener" class="button button-primary">
					<?php esc_html_e( 'مشاهدهٔ ویدیوها', 'shola-core' ); ?> ↗
				</a>
			</p>

			<hr />

			<h2><?php esc_html_e( 'افزودن/ویرایش ویدیوها', 'shola-core' ); ?></h2>
			<p><?php esc_html_e( 'برای هر ویدیو، عنوان و آدرس یوتیوب آن را وارد کنید.', 'shola-core' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'shcore_video_guide_settings' ); ?>
				<div id="shcore-vg-repeater">
					<?php foreach ( array_values( $entries ) as $index => $entry ) : ?>
						<div class="shcore-vg-row" data-index="<?php echo esc_attr( $index ); ?>">
							<input
								type="text"
								name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $index ); ?>][title]"
								value="<?php echo esc_attr( $entry['title'] ); ?>"
								placeholder="<?php esc_attr_e( 'عنوان ویدیو', 'shola-core' ); ?>"
							/>
							<input
								type="url"
								name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $index ); ?>][url]"
								value="<?php echo esc_attr( $entry['url'] ); ?>"
								placeholder="https://youtu.be/..."
								dir="ltr"
							/>
							<button type="button" class="button shcore-vg-remove-row"><?php esc_html_e( 'حذف', 'shola-core' ); ?></button>
						</div>
					<?php endforeach; ?>
				</div>
				<p>
					<button type="button" id="shcore-vg-add-row" class="button"><?php esc_html_e( 'افزودن ویدیوی دیگر', 'shola-core' ); ?></button>
				</p>

				<hr />

				<h2><?php esc_html_e( 'رمز عبور صفحهٔ عمومی', 'shola-core' ); ?></h2>
				<p><?php esc_html_e( 'برای دسترسی به /video-guide بدون ورود به مدیریت سایت، بازدیدکننده باید این رمز عبور را وارد کند. هر زمان که این رمز را تغییر دهید و ذخیره کنید، دسترسی‌های قبلی باطل شده و افراد باید رمز جدید را وارد کنند.', 'shola-core' ); ?></p>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="shcore-vg-password"><?php esc_html_e( 'رمز عبور', 'shola-core' ); ?></label>
						</th>
						<td>
							<input
								type="text"
								id="shcore-vg-password"
								name="<?php echo esc_attr( self::PASSWORD_OPTION_NAME ); ?>"
								value="<?php echo esc_attr( self::get_password() ); ?>"
								class="regular-text"
								dir="ltr"
								autocomplete="off"
							/>
						</td>
					</tr>
				</table>

				<?php submit_button( __( 'ذخیره', 'shola-core' ) ); ?>
			</form>

			<?php
			/*
			 * Row template video-guide-admin.js clones on "افزودن
			 * ویدیوی دیگر" — a real <template> element, never rendered
			 * or submitted itself, just a source of markup to clone.
			 * __INDEX__ is replaced with a running counter in JS.
			 */
			?>
			<template id="shcore-vg-row-template">
				<div class="shcore-vg-row" data-index="__INDEX__">
					<input
						type="text"
						name="<?php echo esc_attr( self::OPTION_NAME ); ?>[__INDEX__][title]"
						placeholder="<?php esc_attr_e( 'عنوان ویدیو', 'shola-core' ); ?>"
					/>
					<input
						type="url"
						name="<?php echo esc_attr( self::OPTION_NAME ); ?>[__INDEX__][url]"
						placeholder="https://youtu.be/..."
						dir="ltr"
					/>
					<button type="button" class="button shcore-vg-remove-row"><?php esc_html_e( 'حذف', 'shola-core' ); ?></button>
				</div>
			</template>
		</div>
		<?php
	}
}
