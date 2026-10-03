<?php
/**
 * Admin settings for the custom login URL (spec-audit: security plan
 * item B6 / L1-L7, docs/SECURITY_PERFORMANCE_PLAN.md) — added 2026-10-03
 * per Farhad's explicit ask, after a design/security discussion in
 * session: a single changeable slug that replaces /wp-login.php as the
 * only working login path, so the site isn't sitting at the default
 * WordPress login URL every automated bot on the internet already
 * scans. Same shape as Loader_Settings/Contact_Settings — one small
 * settings screen via the core WP Settings API, not a CPT: there is
 * exactly one of these, site-wide.
 *
 * The actual request-interception logic lives in Security
 * (class-security.php), the same file that already disables XML-RPC
 * and hides the WP version string — this class only owns the setting
 * itself and its admin UI.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings screen for the custom login URL slug.
 */
class Login_Security_Settings {

	/**
	 * Option name. An empty value means the feature is off and
	 * /wp-login.php behaves exactly like a stock WordPress install —
	 * see Security::route_custom_login()'s own docblock for why an
	 * empty value is deliberately treated as "safe, not broken."
	 */
	const OPTION_SLUG = 'shcore_custom_login_slug';

	/**
	 * Slugs this field must never be set to — either reserved by
	 * WordPress core itself, or already real content on this site.
	 * Checked again live (get_page_by_path()) at save time for anything
	 * not on this fixed list, so a future new page can't accidentally
	 * collide with whatever slug is already saved.
	 */
	const RESERVED_SLUGS = array( 'wp-admin', 'wp-login', 'wp-content', 'wp-includes', 'wp-json', 'feed', 'embed', 'xmlrpc' );

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Register the option and its sanitize callback.
	 *
	 * @return void
	 */
	public static function register_settings() {
		register_setting(
			'shcore_login_security_settings',
			self::OPTION_SLUG,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitize_slug' ),
				'default'           => '',
			)
		);
	}

	/**
	 * Sanitizes the submitted slug: lowercased/hyphenated via
	 * `sanitize_title()` (same transform WordPress applies to post
	 * slugs, so the result behaves predictably in a URL), then checked
	 * against the reserved list and against any real page/post already
	 * using that path. An empty submission is always valid — it's how
	 * the feature is turned off. An invalid non-empty value is rejected
	 * with an admin notice and the previously-saved value is kept,
	 * rather than silently saving something broken.
	 *
	 * @param string $raw Raw submitted value.
	 * @return string
	 */
	public static function sanitize_slug( $raw ) {
		$slug = sanitize_title( (string) $raw );

		if ( '' === $slug ) {
			return '';
		}

		if ( in_array( $slug, self::RESERVED_SLUGS, true ) || get_page_by_path( $slug ) ) {
			add_settings_error(
				self::OPTION_SLUG,
				'shcore-login-slug-invalid',
				__( 'این نشانی قابل استفاده نیست — یا یک نشانی رزروشدهٔ وردپرس است، یا از قبل برای صفحه‌ای دیگر در سایت استفاده می‌شود. نشانی قبلی بدون تغییر باقی ماند.', 'shola-core' )
			);
			return (string) get_option( self::OPTION_SLUG, '' );
		}

		return $slug;
	}

	/**
	 * Register the settings page under Settings → ورود امن.
	 *
	 * @return void
	 */
	public static function add_settings_page() {
		add_options_page(
			__( 'ورود امن', 'shola-core' ),
			__( 'ورود امن', 'shola-core' ),
			'manage_options',
			'shcore-login-security',
			array( __CLASS__, 'render_settings_page' )
		);
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

		$slug         = get_option( self::OPTION_SLUG, '' );
		$current_url  = $slug ? home_url( '/' . $slug . '/' ) : wp_login_url();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'ورود امن', 'shola-core' ); ?></h1>
			<p>
				<?php esc_html_e( 'با تنظیم یک نشانی اختصاصی، صفحهٔ ورود واقعی سایت (wp-login.php) دیگر در دسترس نخواهد بود و فقط نشانی زیر کار می‌کند — همان کاری که ربات‌های خودکار در سراسر اینترنت هر روز روی هر سایت وردپرسی امتحان می‌کنند دیگر به جایی نمی‌رسد.', 'shola-core' ); ?>
			</p>
			<?php settings_errors( self::OPTION_SLUG ); ?>
			<form method="post" action="options.php">
				<?php settings_fields( 'shcore_login_security_settings' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="shcore-login-slug"><?php esc_html_e( 'نشانی ورود', 'shola-core' ); ?></label>
						</th>
						<td>
							<code><?php echo esc_html( home_url( '/' ) ); ?></code>
							<input
								type="text"
								id="shcore-login-slug"
								name="<?php echo esc_attr( self::OPTION_SLUG ); ?>"
								value="<?php echo esc_attr( $slug ); ?>"
								class="regular-text"
								dir="ltr"
								placeholder="<?php esc_attr_e( 'مثلاً: sj-2026-access', 'shola-core' ); ?>"
							/>
							<p class="description">
								<?php
								printf(
									/* translators: %s: the current full login URL. */
									esc_html__( 'نشانی ورود فعلی: %s — در هر زمان که احساس کردید لازم است (مثلاً پس از پایان همکاری یک برنامه‌نویس یا مدیر که این نشانی را می‌دانست)، همین‌جا نشانی را تغییر دهید؛ نشانی قبلی بلافاصله از کار می‌افتد.', 'shola-core' ),
									'<code dir="ltr">' . esc_html( $current_url ) . '</code>'
								);
								?>
							</p>
							<p class="description">
								<?php esc_html_e( 'خالی‌گذاشتن این فیلد، این قابلیت را کاملاً غیرفعال می‌کند و wp-login.php دوباره مثل یک سایت وردپرسی معمولی کار خواهد کرد — راه بازگشت امن در صورت هر مشکلی.', 'shola-core' ); ?>
							</p>
							<p class="description">
								<strong><?php esc_html_e( 'راه نجات در صورت فراموشی نشانی:', 'shola-core' ); ?></strong>
								<?php esc_html_e( 'یک برنامه‌نویس می‌تواند با دستور زیر (از طریق WP-CLI یا مستقیماً در پایگاه‌دادهٔ سایت) این تنظیم را حذف و دسترسی به wp-login.php را بازیابی کند:', 'shola-core' ); ?>
								<code dir="ltr">wp option delete <?php echo esc_html( self::OPTION_SLUG ); ?></code>
							</p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
