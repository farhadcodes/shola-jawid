<?php
/**
 * Admin settings for the site-wide page loader (header.php renders it,
 * assets/css/main.css §30 styles it, assets/js/main.js drives its
 * show-on-navigation behavior) — added 2026-09-15 per the client's
 * explicit ask (relayed by Farhad): the loader's enabled state, its
 * logo image, and its animation speed must all be changeable from
 * wp-admin, not hardcoded, so a site manager can adjust or turn off the
 * effect without a developer touching code.
 * Same shape as Social_Links_Settings/Contact_Settings — one small
 * settings screen, the core WP Settings API (register_setting() +
 * settings_fields() + a plain options.php form), not a CPT: there's
 * exactly one of these, site-wide, never "multiple saved versions with
 * one active" the way masthead_section/hero_section need to be.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings screen for the global page loader.
 */
class Loader_Settings {

	/**
	 * Option names. Three separate options, not one array option like
	 * Social_Links_Settings — header.php already reads each of these
	 * independently via get_option() with its own default and its own
	 * fallback logic (e.g. the logo ID falling back to the bundled SVG
	 * when empty), so keeping them separate here matches how they're
	 * actually consumed instead of adding an array-unpacking step
	 * neither side needs.
	 */
	const OPTION_ENABLED = 'shcore_page_loader_enabled';
	const OPTION_LOGO_ID = 'shcore_page_loader_logo_id';
	const OPTION_SPEED   = 'shcore_page_loader_speed';

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_media_uploader' ) );
	}

	/**
	 * Register the three options and their sanitize callbacks.
	 *
	 * @return void
	 */
	public static function register_settings() {
		register_setting(
			'shcore_page_loader_settings',
			self::OPTION_ENABLED,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => array( __CLASS__, 'sanitize_enabled' ),
				'default'           => '1',
			)
		);
		register_setting(
			'shcore_page_loader_settings',
			self::OPTION_LOGO_ID,
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);
		register_setting(
			'shcore_page_loader_settings',
			self::OPTION_SPEED,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitize_speed' ),
				'default'           => 'normal',
			)
		);
	}

	/**
	 * Checkbox sanitize: present in $_POST (any truthy value) => '1',
	 * absent (an unchecked checkbox sends nothing at all) => ''.
	 *
	 * @param mixed $raw Raw submitted value.
	 * @return string
	 */
	public static function sanitize_enabled( $raw ) {
		return $raw ? '1' : '';
	}

	/**
	 * Restrict to the three allowed speed values — anything else
	 * (a stale value, a tampered request) falls back to 'normal'.
	 *
	 * @param string $raw Raw submitted value.
	 * @return string
	 */
	public static function sanitize_speed( $raw ) {
		return in_array( $raw, array( 'slow', 'normal', 'fast' ), true ) ? $raw : 'normal';
	}

	/**
	 * Register the settings page under Settings → بارگذاری صفحه.
	 *
	 * @return void
	 */
	public static function add_settings_page() {
		add_options_page(
			__( 'بارگذاری صفحه', 'shola-core' ),
			__( 'بارگذاری صفحه', 'shola-core' ),
			'manage_options',
			'shcore-page-loader',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Load wp-admin's built-in media uploader only on this settings
	 * page — no reason to ship it site-wide in wp-admin for a picker
	 * used on exactly one screen.
	 *
	 * @param string $hook_suffix Current admin page hook, passed by WP.
	 * @return void
	 */
	public static function enqueue_media_uploader( $hook_suffix ) {
		if ( 'settings_page_shcore-page-loader' !== $hook_suffix ) {
			return;
		}
		wp_enqueue_media();
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$logo_id  = (int) get_option( self::OPTION_LOGO_ID, 0 );
		$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
		$speed    = get_option( self::OPTION_SPEED, 'normal' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'بارگذاری صفحه', 'shola-core' ); ?></h1>
			<p><?php esc_html_e( 'صفحه‌ای که هنگام بار شدن اولیهٔ سایت، جابه‌جایی بین صفحات، و جست‌وجو نمایش داده می‌شود — لوگوی سایت با یک ضربان ملایم، به‌همراه سه نقطهٔ زیر آن.', 'shola-core' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'shcore_page_loader_settings' ); ?>
				<table class="form-table" role="presentation">

					<tr>
						<th scope="row">
							<label for="shcore-loader-enabled"><?php esc_html_e( 'فعال‌سازی', 'shola-core' ); ?></label>
						</th>
						<td>
							<label>
								<input
									type="checkbox"
									id="shcore-loader-enabled"
									name="<?php echo esc_attr( self::OPTION_ENABLED ); ?>"
									value="1"
									<?php checked( get_option( self::OPTION_ENABLED, '1' ), '1' ); ?>
								/>
								<?php esc_html_e( 'نمایش صفحهٔ بارگذاری در سراسر سایت', 'shola-core' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'در صورت خاموش بودن، هیچ صفحهٔ بارگذاری‌ای نمایش داده نمی‌شود و سایت دقیقاً مانند حالت عادی مرورگر — بدون هیچ افکت انتقالی — کار می‌کند. برای خاموش‌کردن کامل و آزمایشی این جلوه، همین گزینه کافی است؛ نیازی به حذف کد نیست.', 'shola-core' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="shcore-loader-logo-id"><?php esc_html_e( 'لوگوی صفحهٔ بارگذاری', 'shola-core' ); ?></label>
						</th>
						<td>
							<div id="shcore-loader-logo-preview" style="margin-bottom:10px;">
								<?php if ( $logo_url ) : ?>
									<img src="<?php echo esc_url( $logo_url ); ?>" alt="" style="max-width:160px; height:auto; display:block;" />
								<?php else : ?>
									<p style="color:#666; font-style:italic; margin:0;"><?php esc_html_e( 'در حال استفاده از تصویر پیش‌فرض طراحی‌شده برای این بخش.', 'shola-core' ); ?></p>
								<?php endif; ?>
							</div>
							<input type="hidden" id="shcore-loader-logo-id" name="<?php echo esc_attr( self::OPTION_LOGO_ID ); ?>" value="<?php echo esc_attr( $logo_id ); ?>" />
							<button type="button" class="button" id="shcore-loader-logo-select"><?php esc_html_e( 'انتخاب تصویر', 'shola-core' ); ?></button>
							<button type="button" class="button" id="shcore-loader-logo-remove" <?php echo $logo_id ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'بازگشت به پیش‌فرض', 'shola-core' ); ?></button>
							<p class="description">
								<?php esc_html_e( 'برای بهترین نتیجه، تصویری تک‌رنگ و کم‌رنگ بارگذاری کنید — دقیقاً مانند تصویر پیش‌فرضِ این بخش، نه لوگوی کامل‌رنگِ ماستهد سایت. این تصویر در پس‌زمینه‌ای سفید و به‌صورت کم‌رنگ دیده می‌شود؛ یک تصویر پررنگ، شلوغ، یا چندرنگ در این‌جا سنگین و نامناسب به نظر می‌رسد.', 'shola-core' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="shcore-loader-speed"><?php esc_html_e( 'سرعت انیمیشن', 'shola-core' ); ?></label>
						</th>
						<td>
							<select id="shcore-loader-speed" name="<?php echo esc_attr( self::OPTION_SPEED ); ?>">
								<option value="slow" <?php selected( $speed, 'slow' ); ?>><?php esc_html_e( 'آرام', 'shola-core' ); ?></option>
								<option value="normal" <?php selected( $speed, 'normal' ); ?>><?php esc_html_e( 'متوسط (پیشنهادی)', 'shola-core' ); ?></option>
								<option value="fast" <?php selected( $speed, 'fast' ); ?>><?php esc_html_e( 'سریع', 'shola-core' ); ?></option>
							</select>
							<p class="description">
								<?php esc_html_e( 'سرعت ضربان‌زدن لوگو و سه نقطهٔ زیر آن را تنظیم می‌کند. «متوسط» برای بیشتر سایت‌ها مناسب است؛ فقط در صورتی تغییر دهید که در بازدید واقعی سایت احساس کردید حرکت خیلی سریع یا خیلی کند به‌نظر می‌رسد — تغییر این گزینه چیز دیگری را در سایت دگرگون نمی‌کند.', 'shola-core' ); ?>
							</p>
						</td>
					</tr>

				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<script>
		(function () {
			var frame;
			var selectBtn = document.getElementById( 'shcore-loader-logo-select' );
			var removeBtn = document.getElementById( 'shcore-loader-logo-remove' );
			var input     = document.getElementById( 'shcore-loader-logo-id' );
			var preview   = document.getElementById( 'shcore-loader-logo-preview' );

			selectBtn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				if ( frame ) {
					frame.open();
					return;
				}
				frame = wp.media( {
					title: <?php echo wp_json_encode( __( 'انتخاب لوگوی صفحهٔ بارگذاری', 'shola-core' ) ); ?>,
					button: { text: <?php echo wp_json_encode( __( 'استفاده از این تصویر', 'shola-core' ) ); ?> },
					multiple: false
				} );
				frame.on( 'select', function () {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					input.value = attachment.id;
					var previewUrl = ( attachment.sizes && attachment.sizes.medium ) ? attachment.sizes.medium.url : attachment.url;
					preview.innerHTML = '<img src="' + previewUrl + '" alt="" style="max-width:160px; height:auto; display:block;" />';
					removeBtn.style.display = '';
				} );
				frame.open();
			} );

			removeBtn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				input.value = '0';
				preview.innerHTML = <?php echo wp_json_encode( '<p style="color:#666; font-style:italic; margin:0;">' . esc_html__( 'در حال استفاده از تصویر پیش‌فرض طراحی‌شده برای این بخش.', 'shola-core' ) . '</p>' ); ?>;
				removeBtn.style.display = 'none';
			} );
		})();
		</script>
		<?php
	}
}
