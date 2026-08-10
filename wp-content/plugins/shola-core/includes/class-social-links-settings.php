<?php
/**
 * Editable social-media URLs for the theme's footer and popup-menu icon
 * rows, stored as a plugin option so editors can update them from
 * wp-admin instead of a developer editing template files. RSS is
 * deliberately not part of this — it's the site's own feed, always
 * correct via get_feed_link(), not something an editor should ever need
 * to type in. Same shape as Contact_Settings (the "موضوعات فرم تماس"
 * screen) — small option, one settings page, one sanitize callback.
 *
 * Expanded 2026-08-08 (Farhad) from the original 2 platforms (Telegram,
 * X) to a fixed list of 11 — see PLATFORMS below. Reading is migration-
 * safe by construction: get_links() merges stored data over the current
 * defaults via wp_parse_args(), so a site that only ever saved the
 * original 2 keys keeps those values and simply gets empty strings (=
 * "not shown", per the omit-when-empty rule) for the 9 new ones, rather
 * than losing anything.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings screen for the site's social-media link URLs.
 */
class Social_Links_Settings {

	/**
	 * Option name storing the social links (associative array).
	 *
	 * @var string
	 */
	const OPTION_NAME = 'shcore_social_links';

	/**
	 * Fixed list of supported platforms: key => Persian label. Order here
	 * is the order fields appear on the settings screen and (via
	 * shola_get_social_links()) the order icons render on the front end.
	 *
	 * @return array<string, string>
	 */
	public static function get_platforms() {
		return array(
			'telegram'  => __( 'تلگرام', 'shola-core' ),
			'x'         => __( 'ایکس (توییتر)', 'shola-core' ),
			'facebook'  => __( 'فیس‌بوک', 'shola-core' ),
			'instagram' => __( 'اینستاگرام', 'shola-core' ),
			'whatsapp'  => __( 'واتساپ', 'shola-core' ),
			'youtube'   => __( 'یوتیوب', 'shola-core' ),
			'linkedin'  => __( 'لینکدین', 'shola-core' ),
			'tiktok'    => __( 'تیک‌تاک', 'shola-core' ),
			'threads'   => __( 'تردز', 'shola-core' ),
			'signal'    => __( 'سیگنال', 'shola-core' ),
			'mastodon'  => __( 'ماستودون', 'shola-core' ),
		);
	}

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
	 * Empty-string default for every platform key.
	 *
	 * @return array<string, string>
	 */
	private static function get_defaults() {
		return array_fill_keys( array_keys( self::get_platforms() ), '' );
	}

	/**
	 * Read the current social links. Migration-safe: wp_parse_args()
	 * keeps any previously-saved value for a key and fills in '' for
	 * any key that didn't exist yet when the option was last saved.
	 *
	 * @return array<string, string>
	 */
	public static function get_links() {
		$defaults = self::get_defaults();
		$links    = get_option( self::OPTION_NAME, $defaults );

		if ( ! is_array( $links ) ) {
			return $defaults;
		}

		return wp_parse_args( $links, $defaults );
	}

	/**
	 * Register the option and its sanitize callback.
	 *
	 * @return void
	 */
	public static function register_setting() {
		register_setting(
			'shcore_social_links_settings',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_links' ),
				'default'           => self::get_defaults(),
			)
		);
	}

	/**
	 * Sanitize the submitted URL fields — one per known platform key;
	 * anything else in $raw (there shouldn't be, but defensively) is
	 * dropped rather than stored.
	 *
	 * @param array $raw Raw submitted values.
	 * @return array<string, string>
	 */
	public static function sanitize_links( $raw ) {
		$raw    = is_array( $raw ) ? $raw : array();
		$result = array();

		foreach ( array_keys( self::get_platforms() ) as $key ) {
			$result[ $key ] = isset( $raw[ $key ] ) ? esc_url_raw( trim( $raw[ $key ] ) ) : '';
		}

		return $result;
	}

	/**
	 * Register the settings page under Settings → شبکه‌های اجتماعی.
	 *
	 * @return void
	 */
	public static function add_settings_page() {
		add_options_page(
			__( 'شبکه‌های اجتماعی', 'shola-core' ),
			__( 'شبکه‌های اجتماعی', 'shola-core' ),
			'manage_options',
			'shcore-social-links',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Render the settings page: one URL field per platform. RSS is not
	 * included — it's not editable content, see the class docblock.
	 *
	 * @return void
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$links = self::get_links();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'شبکه‌های اجتماعی', 'shola-core' ); ?></h1>
			<p><?php esc_html_e( 'نشانی صفحات شبکه‌های اجتماعی که در پاورقی و منوی سایت نمایش داده می‌شوند. هر شبکه‌ای که نشانی نداشته باشد، آیکونش در سایت اصلاً نمایش داده نمی‌شود — نیازی نیست همهٔ فیلدها پر شوند.', 'shola-core' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'shcore_social_links_settings' ); ?>
				<table class="form-table" role="presentation">
					<?php foreach ( self::get_platforms() as $key => $label ) : ?>
						<tr>
							<th scope="row">
								<label for="shcore-social-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
							</th>
							<td>
								<input
									type="url"
									id="shcore-social-<?php echo esc_attr( $key ); ?>"
									name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $key ); ?>]"
									value="<?php echo esc_attr( $links[ $key ] ); ?>"
									class="regular-text"
									dir="ltr"
									placeholder="https://..."
								/>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
