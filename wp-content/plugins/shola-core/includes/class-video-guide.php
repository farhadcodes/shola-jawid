<?php
/**
 * Private, admin-only list of Farhad's unlisted YouTube tutorial videos
 * for Farhad (how to use the dashboard, publish content, etc.) — wp-admin
 * only, never public-facing (no shortcode, no front-end template, no
 * sitemap/search-index exposure of any kind). Same shape as
 * Label_Settings/Social_Links_Settings/Contact_Settings: one option, one
 * settings page, one sanitize callback, Settings API for the nonce/
 * capability/save plumbing rather than a hand-rolled form handler.
 *
 * Bulk-edited as plain text (one entry per line, "عنوان | آدرس یوتیوب"),
 * not a repeatable-field UI — this is an internal tool Farhad edits
 * himself a few lines at a time, not client-facing content authoring.
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
	 * Renders the list of video links (grouped under their section
	 * heading where one is set, otherwise flat), then the bulk-edit
	 * textarea below it, pre-filled with the current list.
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

			<?php if ( $entries ) : ?>
				<?php
				$grouped = array();
				foreach ( $entries as $entry ) {
					$grouped[ $entry['section'] ][] = $entry;
				}
				?>
				<?php foreach ( $grouped as $section => $section_entries ) : ?>
					<?php if ( '' !== $section ) : ?>
						<h2><?php echo esc_html( $section ); ?></h2>
					<?php endif; ?>
					<ul>
						<?php foreach ( $section_entries as $entry ) : ?>
							<li>
								<a href="<?php echo esc_url( $entry['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $entry['title'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endforeach; ?>
			<?php else : ?>
				<p><em><?php esc_html_e( 'هنوز ویدیویی افزوده نشده است.', 'shola-core' ); ?></em></p>
			<?php endif; ?>

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
