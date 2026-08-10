<?php
/**
 * Editable social-media URLs (Telegram, X) for the theme's footer and
 * popup-menu icon rows, stored as a plugin option so editors can update
 * them from wp-admin instead of a developer editing template files.
 * RSS is deliberately not part of this — it's the site's own feed, always
 * correct via get_feed_link(), not something an editor should ever need
 * to type in. Same shape as Contact_Settings (the "موضوعات فرم تماس"
 * screen) — small option, one settings page, one sanitize callback.
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
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_setting' ) );
	}

	/**
	 * Read the current social links.
	 *
	 * @return array{telegram: string, x: string}
	 */
	public static function get_links() {
		$defaults = array(
			'telegram' => '',
			'x'        => '',
		);
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
				'default'           => array(
					'telegram' => '',
					'x'        => '',
				),
			)
		);
	}

	/**
	 * Sanitize the submitted URL fields.
	 *
	 * @param array $raw Raw submitted values.
	 * @return array{telegram: string, x: string}
	 */
	public static function sanitize_links( $raw ) {
		$raw = is_array( $raw ) ? $raw : array();

		return array(
			'telegram' => isset( $raw['telegram'] ) ? esc_url_raw( trim( $raw['telegram'] ) ) : '',
			'x'        => isset( $raw['x'] ) ? esc_url_raw( trim( $raw['x'] ) ) : '',
		);
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
			<p><?php esc_html_e( 'نشانی صفحات شبکه‌های اجتماعی که در پاورقی و منوی سایت نمایش داده می‌شوند. اگر خالی بماند، آیکون آن شبکه اصلاً نمایش داده نمی‌شود.', 'shola-core' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'shcore_social_links_settings' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="shcore-social-telegram"><?php esc_html_e( 'تلگرام', 'shola-core' ); ?></label>
						</th>
						<td>
							<input
								type="url"
								id="shcore-social-telegram"
								name="<?php echo esc_attr( self::OPTION_NAME ); ?>[telegram]"
								value="<?php echo esc_attr( $links['telegram'] ); ?>"
								class="regular-text"
								dir="ltr"
								placeholder="https://t.me/..."
							/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="shcore-social-x"><?php esc_html_e( 'ایکس (توییتر)', 'shola-core' ); ?></label>
						</th>
						<td>
							<input
								type="url"
								id="shcore-social-x"
								name="<?php echo esc_attr( self::OPTION_NAME ); ?>[x]"
								value="<?php echo esc_attr( $links['x'] ); ?>"
								class="regular-text"
								dir="ltr"
								placeholder="https://x.com/..."
							/>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
