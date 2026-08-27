<?php
/**
 * Private, admin-only list of Farhad's unlisted YouTube tutorial videos
 * for Farhad (how to use the dashboard, publish content, etc.). Two
 * entry points, same underlying data and rendering, both gated to
 * manage_options only:
 *   - wp-admin: Settings → راهنمای ویدیویی (add_options_page(), same
 *     shape as Label_Settings/Social_Links_Settings/Contact_Settings —
 *     one option, one settings page, one sanitize callback, Settings
 *     API for the nonce/capability/save plumbing).
 *   - Front end: /video-guide (2026-08-28, docs/CHANGELOG.md) — a
 *     bookmarkable URL for the same thumbnail grid, registered as a
 *     custom rewrite rule + query var + template_redirect gate (see
 *     register_rewrite()/maybe_render_front_end_page() below), not a
 *     real WP Page/post. Real capability check on every request
 *     (is_user_logged_in() && current_user_can('manage_options')),
 *     not an unguessable-URL approach — anyone else is redirected to
 *     wp-login.php with redirect_to pointing back at this URL, no page
 *     content rendered first. Deliberately does not inherit the
 *     public theme (no get_header()/get_footer()) — a minimal
 *     standalone shell reusing the same admin CSS/JS, plus
 *     noindex/nofollow as defense in depth. Not linked from any menu,
 *     sitemap, or public navigation anywhere.
 * Neither entry point is public-facing beyond that one deliberate
 * front-end URL — no shortcode, no theme template, no sitemap/search-
 * index exposure otherwise.
 *
 * Bulk-edited as plain text (one entry per line, "عنوان | آدرس یوتیوب"),
 * not a repeatable-field UI — this is an internal tool Farhad edits
 * himself a few lines at a time, not client-facing content authoring.
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
	 * Gate for the /video-guide front-end route. Anything other than a
	 * logged-in Administrator (manage_options) is redirected to the
	 * login screen with redirect_to pointing back at this exact URL —
	 * no page content, no "access denied" message, nothing rendered
	 * before the redirect for a non-admin. A real capability check,
	 * not an unguessable-URL/security-through-obscurity approach — the
	 * URL itself is expected to be bookmarked/shared in chat.
	 *
	 * @return void
	 */
	public static function maybe_render_front_end_page() {
		if ( ! get_query_var( 'shcore_video_guide' ) ) {
			return;
		}

		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			$current_url = home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- read-only URL reconstruction, passed only to wp_login_url()/wp_safe_redirect(), which handle escaping internally; never echoed directly.
			wp_safe_redirect( wp_login_url( $current_url ) );
			exit;
		}

		self::render_front_end_page();
		exit;
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
			<style>
				body { font-family: -apple-system, "Segoe UI", Tahoma, sans-serif; max-width: 1100px; margin: 0 auto; padding: 2rem 1.5rem 4rem; color: #1d2327; }
				h1 { margin-bottom: 0.25rem; }
			</style>
		</head>
		<body>
			<h1><?php esc_html_e( 'راهنمای ویدیویی', 'shola-core' ); ?></h1>
			<p><?php esc_html_e( 'فهرست ویدیوهای آموزشی داخلی — فقط برای مدیران سایت.', 'shola-core' ); ?></p>
			<?php self::render_grid( self::get_entries() ); ?>
			<script src="<?php echo esc_url( $js_url ); ?>"></script>
		</body>
		</html>
		<?php
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
		wp_enqueue_style(
			'shcore-video-guide',
			SHCORE_URL . 'admin/css/video-guide.css',
			array(),
			SHCORE_VERSION
		);
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
	 * Register the option and its sanitize callback.
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
	}

	/**
	 * Parses the submitted bulk-edit textarea into the stored entry list.
	 * Line format:
	 *   - "## بخش" — starts a new section; every entry line after it is
	 *     grouped under that label until the next "##" line (or end of
	 *     text). No "##" line yet seen means section '' (flat list —
	 *     the expected starting state per the initial rollout).
	 *   - "عنوان | آدرس" — one entry: title (sanitize_text_field) and
	 *     URL (esc_url_raw). Lines missing the "|" separator, or with an
	 *     empty title/URL after sanitizing, are silently dropped rather
	 *     than stored malformed — this is a bulk text field, not a
	 *     validated multi-field form, so a typo should not fatal or
	 *     leave a broken row, just be omitted (Farhad can re-add it).
	 *   - Blank lines are ignored.
	 *
	 * @param mixed $raw Raw submitted value — the textarea's string
	 *                    content (Settings API passes the POSTed field
	 *                    value as-is, not pre-split into lines).
	 * @return array<int, array{section: string, title: string, url: string}>
	 */
	public static function sanitize_entries( $raw ) {
		$text = is_string( $raw ) ? $raw : self::entries_to_text( is_array( $raw ) ? $raw : array() );

		$result  = array();
		$section = '';

		foreach ( preg_split( '/\r\n|\r|\n/', $text ) as $line ) {
			$line = trim( $line );

			if ( '' === $line ) {
				continue;
			}

			if ( 0 === strpos( $line, '##' ) ) {
				$section = sanitize_text_field( trim( substr( $line, 2 ) ) );
				continue;
			}

			$parts = explode( '|', $line, 2 );
			if ( count( $parts ) < 2 ) {
				continue;
			}

			$title = sanitize_text_field( trim( $parts[0] ) );
			$url   = esc_url_raw( trim( $parts[1] ) );

			if ( '' === $title || '' === $url ) {
				continue;
			}

			$result[] = array(
				'section' => $section,
				'title'   => $title,
				'url'     => $url,
			);
		}

		return $result;
	}

	/**
	 * Inverse of sanitize_entries() — renders the stored list back into
	 * the same line-based text format, so the bulk-edit textarea shows
	 * the current entries pre-filled (to append to) rather than a blank
	 * box Farhad would have to retype from scratch every time.
	 *
	 * @param array<int, array{section?: string, title?: string, url?: string}> $entries Stored entries.
	 * @return string
	 */
	private static function entries_to_text( $entries ) {
		$lines           = array();
		$current_section = null;

		foreach ( $entries as $entry ) {
			$section = isset( $entry['section'] ) ? $entry['section'] : '';
			$title   = isset( $entry['title'] ) ? $entry['title'] : '';
			$url     = isset( $entry['url'] ) ? $entry['url'] : '';

			if ( '' === $title || '' === $url ) {
				continue;
			}

			if ( $section !== $current_section && '' !== $section ) {
				$lines[]         = '## ' . $section;
				$current_section = $section;
			}

			$lines[] = $title . ' | ' . $url;
		}

		return implode( "\n", $lines );
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
			<p><em><?php esc_html_e( 'هنوز ویدیویی افزوده نشده است.', 'shola-core' ); ?></em></p>
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
					</div>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
		<?php
	}

	/**
	 * Renders the wp-admin settings screen: intro text, the shared
	 * thumbnail grid (render_grid()), then the bulk-edit textarea,
	 * pre-filled with the current list.
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

			<?php self::render_grid( $entries ); ?>

			<hr />

			<h2><?php esc_html_e( 'افزودن/ویرایش گروهی', 'shola-core' ); ?></h2>
			<p>
				<?php
				echo wp_kses(
					__( 'هر سطر یک ویدیو: <code>عنوان ویدیو | آدرس یوتیوب</code>. برای شروع یک بخش جدید، سطری با <code>## نام بخش</code> بنویسید — همهٔ ویدیوهای بعد از آن تا بخش بعدی زیر همان عنوان نمایش داده می‌شوند.', 'shola-core' ),
					array( 'code' => array() )
				);
				?>
			</p>
			<form method="post" action="options.php">
				<?php settings_fields( 'shcore_video_guide_settings' ); ?>
				<textarea
					name="<?php echo esc_attr( self::OPTION_NAME ); ?>"
					rows="12"
					class="large-text code"
					dir="ltr"
					placeholder="## پنل مدیریت&#10;آشنایی با داشبورد | https://youtube.com/..."
				><?php echo esc_textarea( self::entries_to_text( $entries ) ); ?></textarea>
				<?php submit_button( __( 'ذخیره فهرست', 'shola-core' ) ); ?>
			</form>
		</div>
		<?php
	}
}
