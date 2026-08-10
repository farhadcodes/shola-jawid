<?php
/**
 * Editable overrides for a bounded set of short, chrome-style Persian UI
 * labels (nav/tab/heading microcopy — تازه‌ترین, پرخواننده‌ترین, موضوعات,
 * etc.), stored as a single plugin option so an editor can retype one from
 * wp-admin instead of a developer editing template files. Deliberately not
 * "every string on the site" — see docs/CHANGELOG.md's Phase F entry for
 * the scoping rationale. Same shape as Social_Links_Settings/
 * Contact_Settings: small option, one settings page, one sanitize
 * callback.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and reads shcore_label_overrides — a key => override-string
 * map. An empty/missing override always falls back to the hardcoded
 * default text (never renders blank) — deliberately different from
 * Social_Links_Settings, where an empty value means "omit the icon
 * entirely." A label always needs something to display.
 */
class Label_Settings {

	const OPTION_NAME = 'shcore_label_overrides';

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
	 * The fixed, bounded set of editable labels and their current
	 * hardcoded default text — the exact strings taxonomy-topic.php,
	 * front-page.php, page-library.php, page-topics.php, footer.php,
	 * header.php, and single.php render today. Kept translatable via
	 * `__()` under the `shola-core` text-domain so a site with zero
	 * overrides saved (fresh install) is still fully bilingual-ready per
	 * CLAUDE.md §1 — only a value an editor actually types into the
	 * settings screen bypasses `.pot` scanning, same as any other
	 * user-entered content already does.
	 *
	 * Some keys are deliberately shared across more than one call site
	 * (e.g. `latest_documents_heading` covers both front-page.php's and
	 * page-library.php's "تازه‌ترین اسناد" heading, `nav_topics_label`
	 * covers both footer.php's and header.php's "موضوعات" nav label) —
	 * always where the current rendered text is byte-identical, so one
	 * edit keeps every instance of that exact concept in sync rather
	 * than letting them drift into two different-sounding labels for the
	 * same idea. Elsewhere (`breadcrumb_topics_label`,
	 * `topics_page_title`), the word is the same today but the UI role
	 * differs enough (breadcrumb crumb vs. nav section header vs. page
	 * `<h1>`) that a shared key would let editing one silently change the
	 * others somewhere an editor might not expect — kept as separate
	 * keys instead.
	 *
	 * @return array<string, string>
	 */
	public static function get_defaults() {
		return array(
			'home_articles_section_aria'  => __( 'تازه‌ترین مقالات', 'shola-core' ),
			'home_latest_heading'         => __( 'تازه‌ترین', 'shola-core' ),
			'home_topics_link_more'       => __( 'همهٔ موضوعات', 'shola-core' ),
			'home_topics_section_heading' => __( 'همهٔ موضوعات', 'shola-core' ),
			'latest_documents_heading'    => __( 'تازه‌ترین اسناد', 'shola-core' ),
			'nav_topics_label'            => __( 'موضوعات', 'shola-core' ),
			'nav_more_label'              => __( 'بیشتر', 'shola-core' ),
			'breadcrumb_topics_label'     => __( 'موضوعات', 'shola-core' ),
			'topic_tab_latest'            => __( 'تازه‌ترین', 'shola-core' ),
			'topic_tab_most_read'         => __( 'پرخواننده‌ترین', 'shola-core' ),
			'topics_page_title'           => __( 'موضوعات', 'shola-core' ),
		);
	}

	/**
	 * Where each key renders — shown next to its field on the settings
	 * page so an editor can tell what they're changing without opening
	 * the site in another tab.
	 *
	 * @return array<string, string>
	 */
	public static function get_descriptions() {
		return array(
			'home_articles_section_aria'  => __( 'صفحهٔ اصلی — برچسب دسترس‌پذیری بخش «تازه‌ترین»', 'shola-core' ),
			'home_latest_heading'         => __( 'صفحهٔ اصلی — عنوان بخش «تازه‌ترین»', 'shola-core' ),
			'home_topics_link_more'       => __( 'صفحهٔ اصلی — پیوند «همهٔ موضوعات» کنار بخش تازه‌ترین', 'shola-core' ),
			'home_topics_section_heading' => __( 'صفحهٔ اصلی — عنوان بخش موضوعات', 'shola-core' ),
			'latest_documents_heading'    => __( 'صفحهٔ اصلی و کتابخانه — عنوان بخش «تازه‌ترین اسناد»', 'shola-core' ),
			'nav_topics_label'            => __( 'پاورقی و منوی بازشو — برچسب «موضوعات»', 'shola-core' ),
			'nav_more_label'              => __( 'منوی بازشو — عنوان بخش «بیشتر»', 'shola-core' ),
			'breadcrumb_topics_label'     => __( 'صفحهٔ مقاله — پیوند «موضوعات» در مسیر ناوبری', 'shola-core' ),
			'topic_tab_latest'            => __( 'آرشیو موضوع — دکمهٔ مرتب‌سازی «تازه‌ترین»', 'shola-core' ),
			'topic_tab_most_read'         => __( 'آرشیو موضوع — دکمهٔ مرتب‌سازی «پرخواننده‌ترین»', 'shola-core' ),
			'topics_page_title'           => __( 'صفحهٔ «موضوعات» — عنوان اصلی', 'shola-core' ),
		);
	}

	/**
	 * Resolved labels: saved overrides layered over the defaults. A
	 * saved-but-empty override is treated the same as no override at all
	 * (falls back to the default), not as "show nothing" — filtered out
	 * before the merge so this is the one place that rule lives, rather
	 * than every call site having to check for an empty string itself.
	 *
	 * @return array<string, string>
	 */
	public static function get_labels() {
		$defaults = self::get_defaults();
		$stored   = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $stored ) ) {
			return $defaults;
		}

		$stored = array_filter(
			$stored,
			static function ( $value ) {
				return '' !== trim( (string) $value );
			}
		);

		return wp_parse_args( $stored, $defaults );
	}

	/**
	 * Single-key convenience reader for the theme's shola_get_label().
	 *
	 * @param string $key One of the keys from get_defaults().
	 * @return string
	 */
	public static function get_label( $key ) {
		$labels = self::get_labels();
		return isset( $labels[ $key ] ) ? $labels[ $key ] : '';
	}

	/**
	 * Registers the setting with the Settings API.
	 *
	 * @return void
	 */
	public static function register_setting() {
		register_setting(
			'shcore_label_settings',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_labels' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * Keeps only known keys, sanitizes each as plain text. Empty values
	 * are kept in the stored option as empty strings (not dropped) —
	 * get_labels() is the single place that decides an empty stored
	 * value means "use the default," not here.
	 *
	 * @param mixed $raw Raw submitted value.
	 * @return array<string, string>
	 */
	public static function sanitize_labels( $raw ) {
		$raw    = is_array( $raw ) ? $raw : array();
		$result = array();

		foreach ( array_keys( self::get_defaults() ) as $key ) {
			$result[ $key ] = isset( $raw[ $key ] ) ? sanitize_text_field( trim( $raw[ $key ] ) ) : '';
		}

		return $result;
	}

	/**
	 * Adds the settings page under Settings.
	 *
	 * @return void
	 */
	public static function add_settings_page() {
		add_options_page(
			__( 'متن‌های رابط کاربری', 'shola-core' ),
			__( 'متن‌های رابط کاربری', 'shola-core' ),
			'manage_options',
			'shcore-label-settings',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Renders the settings page: one row per key, its location
	 * description, and a text field pre-filled with the saved override
	 * (blank if none) — the field's placeholder shows the current
	 * default so an editor can see at a glance what an empty field falls
	 * back to.
	 *
	 * @return void
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$defaults     = self::get_defaults();
		$descriptions = self::get_descriptions();
		$stored       = get_option( self::OPTION_NAME, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'متن‌های رابط کاربری', 'shola-core' ); ?></h1>
			<p><?php esc_html_e( 'ویرایش برچسب‌های کوتاه رابط کاربری سایت (مانند «تازه‌ترین»، «موضوعات») بدون نیاز به تغییر کد. هر فیلدی که خالی بماند، متن پیش‌فرض همان‌جا نمایش داده می‌شود.', 'shola-core' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'shcore_label_settings' ); ?>
				<table class="form-table" role="presentation">
					<?php foreach ( $defaults as $key => $default_text ) : ?>
						<tr>
							<th scope="row">
								<label for="shcore-label-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( isset( $descriptions[ $key ] ) ? $descriptions[ $key ] : $key ); ?></label>
							</th>
							<td>
								<input
									type="text"
									id="shcore-label-<?php echo esc_attr( $key ); ?>"
									name="<?php echo esc_attr( self::OPTION_NAME ); ?>[<?php echo esc_attr( $key ); ?>]"
									value="<?php echo esc_attr( isset( $stored[ $key ] ) ? $stored[ $key ] : '' ); ?>"
									class="regular-text"
									placeholder="<?php echo esc_attr( $default_text ); ?>"
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
