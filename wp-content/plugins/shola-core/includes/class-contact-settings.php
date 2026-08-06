<?php
/**
 * Editable subject-line options for the contact form's "موضوع پیام"
 * dropdown, stored as a plugin option (not a CPT/taxonomy — this isn't
 * content, it's a small piece of site configuration) so editors can
 * add/remove/reorder options from wp-admin instead of editing Contact
 * Form 7's form-tag syntax directly.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings screen + CF7 dynamic-form-tag bridge for the contact topics list.
 */
class Contact_Settings {

	/**
	 * Option name storing the topic list (array of strings).
	 *
	 * @var string
	 */
	const OPTION_NAME = 'shcore_contact_topics';

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_setting' ) );
		add_filter( 'wpcf7_form_tag_data_option', array( __CLASS__, 'filter_data_option' ), 10, 2 );
	}

	/**
	 * Default topic list — matches body-contact.html's original 5 options,
	 * used both as the option's default value and as a hard fallback if
	 * the option is ever empty.
	 *
	 * @return string[]
	 */
	public static function get_default_topics() {
		return array(
			'پیشنهاد مقاله',
			'پرسش تحریری',
			'همکاری ترجمه',
			'گزارش خطا در سایت',
			'سایر موارد',
		);
	}

	/**
	 * Read the current topic list.
	 *
	 * @return string[]
	 */
	public static function get_topics() {
		$topics = get_option( self::OPTION_NAME, self::get_default_topics() );

		if ( empty( $topics ) || ! is_array( $topics ) ) {
			return self::get_default_topics();
		}

		return $topics;
	}

	/**
	 * CF7's `wpcf7_form_tag_data_option` filter: fires for every form-tag
	 * that has a `data:xxx` pipe (e.g. the your-topic tag's
	 * `"data:contact_topics"`). $options holds the data-option names
	 * present on that tag (["contact_topics"]); only respond when ours is
	 * present, so this doesn't affect any other form/tag on the site.
	 *
	 * @param mixed $value   Current filtered value (null unless another
	 *                       callback already handled this tag).
	 * @param array $options Data-option names present on the form-tag.
	 * @return mixed
	 */
	public static function filter_data_option( $value, $options ) {
		if ( in_array( 'contact_topics', (array) $options, true ) ) {
			return self::get_topics();
		}

		return $value;
	}

	/**
	 * Register the option and its sanitize callback.
	 *
	 * @return void
	 */
	public static function register_setting() {
		register_setting(
			'shcore_contact_settings',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_topics' ),
				'default'           => self::get_default_topics(),
			)
		);
	}

	/**
	 * Sanitize the submitted textarea (one option per line) into a clean
	 * array of non-empty, sanitized strings.
	 *
	 * @param string $raw Raw textarea value from the settings form.
	 * @return string[]
	 */
	public static function sanitize_topics( $raw ) {
		$lines  = explode( "\n", (string) $raw );
		$topics = array();

		foreach ( $lines as $line ) {
			$line = sanitize_text_field( trim( $line ) );
			if ( '' !== $line ) {
				$topics[] = $line;
			}
		}

		return empty( $topics ) ? self::get_default_topics() : $topics;
	}

	/**
	 * Register the settings page under Settings → Contact Topics.
	 *
	 * @return void
	 */
	public static function add_settings_page() {
		add_options_page(
			__( 'موضوعات فرم تماس', 'shola-core' ),
			__( 'موضوعات فرم تماس', 'shola-core' ),
			'manage_options',
			'shcore-contact-topics',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Render the settings page: one textarea, one topic per line.
	 *
	 * @return void
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$topics = self::get_topics();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'موضوعات فرم تماس', 'shola-core' ); ?></h1>
			<p><?php esc_html_e( 'هر سطر یک گزینه برای فهرست کشویی «موضوع پیام» در فرم تماس است. سطرهای خالی نادیده گرفته می‌شوند.', 'shola-core' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'shcore_contact_settings' ); ?>
				<textarea
					name="<?php echo esc_attr( self::OPTION_NAME ); ?>"
					rows="8"
					cols="50"
					class="large-text"
					dir="rtl"
				><?php echo esc_textarea( implode( "\n", $topics ) ); ?></textarea>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
