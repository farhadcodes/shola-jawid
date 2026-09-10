<?php
/**
 * Registers post meta for issue, document, party_publication,
 * party_document, and article/post, per the IA doc §5 content-model table
 * (party_publication added 2026-09-02, party_document added 2026-09-04,
 * both outside that original table — see class-post-types.php's docblock
 * on each), plus the metabox UI editors use to fill them in from
 * wp-admin without touching code.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post meta registration, metabox UI, and PDF MIME validation.
 */
class Meta_Fields {

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_meta' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta_boxes' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_filter( 'upload_mimes', array( __CLASS__, 'ensure_pdf_mime_allowed' ) );

		/*
		 * hero_section admin-list UX (2026-09-09): a status column so an
		 * editor can see which variant is live without opening each one,
		 * and a one-click "Set as active" row action instead of requiring
		 * them to open an entry and find the checkbox — see
		 * class-post-types.php's hero_section docblock for the feature.
		 */
		add_filter( 'manage_hero_section_posts_columns', array( __CLASS__, 'add_hero_status_column' ) );
		add_action( 'manage_hero_section_posts_custom_column', array( __CLASS__, 'render_hero_status_column' ), 10, 2 );
		add_filter( 'post_row_actions', array( __CLASS__, 'add_hero_set_active_row_action' ), 10, 2 );
		add_action( 'admin_action_shcore_set_active_hero', array( __CLASS__, 'handle_set_active_hero' ) );

		/*
		 * Seed one default, active hero_section entry (تک‌ستونی — today's
		 * existing hero design) so the admin list isn't empty and the
		 * active-flag mechanism is immediately testable, even before
		 * front-page.php is wired to read from this post type. Same
		 * idempotent admin_init + option-flag pattern as
		 * Taxonomies::migrate_legacy_party_documents() — safe to run on
		 * every admin page load, only ever inserts once.
		 */
		add_action( 'admin_init', array( __CLASS__, 'seed_default_hero_section' ) );
	}

	/**
	 * Register all post meta fields with real sanitize/auth callbacks and
	 * REST exposure (so both the classic metabox below and the block
	 * editor's Custom Fields panel work). Native WP fields already cover
	 * part of the IA doc §5 table and get no meta key here: تاریخ →
	 * post_date, خلاصه/توضیح → post_excerpt, جلد کتاب/پیش‌نمایش → featured
	 * image (thumbnail support, added in Phase 3.1), نشریه/مجموعه →
	 * publication/collection taxonomies (Phase 3.2).
	 *
	 * @return void
	 */
	public static function register_meta() {
		$auth_callback = array( __CLASS__, 'auth_edit_post' );

		// issue.
		register_post_meta(
			'issue',
			'shcore_issue_number',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'issue',
			'shcore_volume',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'issue',
			'shcore_pdf_id',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( __CLASS__, 'sanitize_pdf_id' ),
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'issue',
			'shcore_contents',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( __CLASS__, 'sanitize_issue_contents' ),
				'auth_callback'     => $auth_callback,
			)
		);

		// document.
		register_post_meta(
			'document',
			'shcore_author_source',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'document',
			'shcore_pdf_id',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( __CLASS__, 'sanitize_pdf_id' ),
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'document',
			'shcore_language',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'fa',
				'sanitize_callback' => array( __CLASS__, 'sanitize_language' ),
				'auth_callback'     => $auth_callback,
			)
		);

		// party_publication (2026-09-02) — deliberately no author-source
		// field like `document` has: these are the party's own
		// books/booklets, not works being cited from an external
		// theorist/author, so there's no one to attribute per-item.
		register_post_meta(
			'party_publication',
			'shcore_pdf_id',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( __CLASS__, 'sanitize_pdf_id' ),
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'party_publication',
			'shcore_language',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'fa',
				'sanitize_callback' => array( __CLASS__, 'sanitize_language' ),
				'auth_callback'     => $auth_callback,
			)
		);

		// party_document (2026-09-04) — the party's own archive of
		// internal documents (اسناد حزب), distinct from `document`
		// (کتابخانه, other authors' works) and `party_publication`
		// (انتشارات حزب, the party's finished books/booklets). Detail/
		// description uses native `editor` support (post_content), same
		// as `document`'s "about this text" — no separate meta needed for
		// that field. Date and title are likewise native (post_date,
		// post_title). Only what has no native equivalent gets a meta key
		// here: the serial number.
		register_post_meta(
			'party_document',
			'shcore_serial_number',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'party_document',
			'shcore_pdf_id',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( __CLASS__, 'sanitize_pdf_id' ),
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'party_document',
			'shcore_language',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'fa',
				'sanitize_callback' => array( __CLASS__, 'sanitize_language' ),
				'auth_callback'     => $auth_callback,
			)
		);

		/*
		 * hero_section (2026-09-09) — see class-post-types.php's docblock
		 * on this CPT for the full rationale. `shcore_hero_active` is a
		 * singleton in practice (enforced in save_meta_boxes(), not here —
		 * register_post_meta() has no cross-post constraint mechanism), so
		 * exactly one hero_section (or none, before an editor has picked
		 * one) is ever active at a time.
		 */
		register_post_meta(
			'hero_section',
			'shcore_hero_active',
			array(
				'type'              => 'boolean',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'hero_section',
			'shcore_hero_layout',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'single',
				'sanitize_callback' => array( __CLASS__, 'sanitize_hero_layout' ),
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'hero_section',
			'shcore_hero_rail_publication',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'shola-jawid',
				'sanitize_callback' => array( __CLASS__, 'sanitize_hero_rail_publication' ),
				'auth_callback'     => $auth_callback,
			)
		);

		// post (article/note).
		register_post_meta(
			'post',
			'shcore_byline',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'post',
			'shcore_author_note',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'post',
			'shcore_language',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'fa',
				'sanitize_callback' => array( __CLASS__, 'sanitize_language' ),
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'post',
			'shcore_translation_id',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => $auth_callback,
			)
		);
		/*
		 * Primary topic (2026-09-02): a post can carry several `topic`
		 * terms, but the breadcrumb/card display needs exactly one. This
		 * stores which of the assigned terms the editor picked as primary
		 * — see SholaCore\Taxonomies::get_primary_topic() for the
		 * resolution logic (falls back to the old array_shift() pick if
		 * this is unset or no longer one of the post's actual terms) and
		 * admin/js/primary-topic.js for the picker UI. Sanitized
		 * with absint like shcore_translation_id above; a term_id of 0
		 * (nothing picked) is a valid "no primary set" state, not an error.
		 */
		register_post_meta(
			'post',
			'shcore_primary_topic',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => $auth_callback,
			)
		);
	}

	/**
	 * Reject anything that isn't a real attachment ID whose stored MIME
	 * type (validated by WP core's own finfo-based sniffing at original
	 * upload time, not just the file extension) is application/pdf.
	 * Runs on every update_post_meta()/REST write for this key, per
	 * CLAUDE.md §6 — the client-side picker restriction (meta-boxes.js) is
	 * UX only; this is the real, non-spoofable check.
	 *
	 * @param mixed $value Raw meta value.
	 * @return int
	 */
	public static function sanitize_pdf_id( $value ) {
		$id = absint( $value );
		if ( ! $id || 'application/pdf' !== get_post_mime_type( $id ) ) {
			return 0;
		}
		return $id;
	}

	/**
	 * Restrict to the four hero layouts this feature ships with (see
	 * class-post-types.php's hero_section docblock) — `single` (today's
	 * full-bleed hero), `lead_rail` (headline + a separate, centered
	 * rail column), `overlay` (2026-09-10, third layout per Farhad
	 * relaying a client idea: same full-bleed hero as `single`, with a
	 * white publication card floating over the photo's lower corner
	 * instead of a full side column), and `rail_full` (2026-09-10,
	 * fourth layout per a client sketch: visually the same idea as
	 * `lead_rail` — a solid-color panel with the publication card
	 * beside the full-size headline photo — but edge-to-edge full-bleed
	 * on the outer side instead of staying inside the centered content
	 * column). An unrecognized value (e.g. a future layout type removed
	 * later) falls back to the safest option — single, the current
	 * site's existing hero design.
	 *
	 * @param mixed $value Raw meta value.
	 * @return string
	 */
	public static function sanitize_hero_layout( $value ) {
		return in_array( $value, array( 'single', 'lead_rail', 'overlay', 'rail_full' ), true ) ? $value : 'single';
	}

	/**
	 * Restrict to the two fixed `publication` term slugs (شعله جاوید /
	 * جهان برای فتح) a hero_section's rail can source its latest issue
	 * from. Hardcoded like sanitize_language() above rather than checked
	 * against get_terms(), since this is the same kind of small, fixed
	 * vocabulary — not something editors can add to.
	 *
	 * @param mixed $value Raw meta value.
	 * @return string
	 */
	public static function sanitize_hero_rail_publication( $value ) {
		return in_array( $value, array( 'shola-jawid', 'a-world-to-win' ), true ) ? $value : 'shola-jawid';
	}

	/**
	 * Restrict to the two locales this project is bilingual-ready for.
	 * Only `fa` is active per CLAUDE.md §1; `en` is scaffolded, not wired
	 * to anything live.
	 *
	 * @param mixed $value Raw meta value.
	 * @return string
	 */
	public static function sanitize_language( $value ) {
		return in_array( $value, array( 'fa', 'en' ), true ) ? $value : 'fa';
	}

	/**
	 * Standard per-post edit capability check for all fields registered
	 * above.
	 *
	 * @param bool   $allowed Whether the value is allowed.
	 * @param string $meta_key Meta key.
	 * @param int    $post_id Post ID.
	 * @return bool
	 */
	public static function auth_edit_post( $allowed, $meta_key, $post_id ) {
		return current_user_can( 'edit_post', $post_id );
	}

	/**
	 * Ensure PDF stays in the allowed upload MIME list even if it's
	 * restricted elsewhere later (e.g. Phase 6 hardening) — belt-and-
	 * braces per CLAUDE.md §6, since PDF is already a WP core default but
	 * this makes the requirement explicit in code rather than implicit.
	 *
	 * @param array $mimes Allowed mime => extension map.
	 * @return array
	 */
	public static function ensure_pdf_mime_allowed( $mimes ) {
		$mimes['pdf'] = 'application/pdf';
		return $mimes;
	}

	/**
	 * Register the classic metaboxes editors use to fill these fields in
	 * without touching code (renders inside the block editor too, below
	 * the content area, same as any classic metabox).
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box( 'shcore_issue_fields', __( 'اطلاعات شماره', 'shola-core' ), array( __CLASS__, 'render_issue_metabox' ), 'issue', 'normal', 'high' );
		add_meta_box( 'shcore_document_fields', __( 'اطلاعات سند', 'shola-core' ), array( __CLASS__, 'render_document_metabox' ), 'document', 'normal', 'high' );
		add_meta_box( 'shcore_party_publication_fields', __( 'اطلاعات اثر', 'shola-core' ), array( __CLASS__, 'render_party_publication_metabox' ), 'party_publication', 'normal', 'high' );
		add_meta_box( 'shcore_party_document_fields', __( 'اطلاعات سند', 'shola-core' ), array( __CLASS__, 'render_party_document_metabox' ), 'party_document', 'normal', 'high' );
		add_meta_box( 'shcore_article_fields', __( 'اطلاعات مقاله', 'shola-core' ), array( __CLASS__, 'render_article_metabox' ), 'post', 'normal', 'high' );
		add_meta_box( 'shcore_hero_fields', __( 'تنظیمات هدر', 'shola-core' ), array( __CLASS__, 'render_hero_metabox' ), 'hero_section', 'normal', 'high' );
	}

	/**
	 * Render the issue metabox fields.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_issue_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$number = get_post_meta( $post->ID, 'shcore_issue_number', true );
		$volume = get_post_meta( $post->ID, 'shcore_volume', true );
		$rows   = self::get_issue_contents( $post->ID );
		?>
		<p>
			<label for="shcore_issue_number"><strong><?php esc_html_e( 'شمارهٔ شماره', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_issue_number" name="shcore_issue_number" class="regular-text" value="<?php echo esc_attr( $number ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'شمارهٔ پیاپی این شماره از نشریه را وارد کنید؛ مثلاً ۳۱.', 'shola-core' ); ?></p>
		<p>
			<label for="shcore_volume"><strong><?php esc_html_e( 'دوره / جلد', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_volume" name="shcore_volume" class="regular-text" value="<?php echo esc_attr( $volume ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'دورهٔ یا جلد این شماره را بنویسید؛ اگر ندارد، خالی بگذارید.', 'shola-core' ); ?></p>
		<?php self::render_pdf_field( $post->ID, 'shcore_pdf_id' ); ?>

		<p><strong><?php esc_html_e( 'فهرست مطالب (اختیاری)', 'shola-core' ); ?></strong></p>
		<p class="description">
			<?php esc_html_e( 'نوشته‌های شماره فقط در PDF چاپ می‌شوند و در سایت صفحهٔ مستقل ندارند — این فهرست فقط توضیحی است. هر سه فیلد اختیاری‌اند؛ ردیف‌های کاملاً خالی هنگام ذخیره نادیده گرفته می‌شوند.', 'shola-core' ); ?>
		</p>
		<div class="shcore-toc-repeater-wrap">
			<table class="widefat shcore-toc-repeater">
				<thead>
					<tr>
						<th><?php esc_html_e( 'بخش', 'shola-core' ); ?></th>
						<th><?php esc_html_e( 'عنوان', 'shola-core' ); ?></th>
						<th><?php esc_html_e( 'نویسنده', 'shola-core' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rows as $i => $row ) : ?>
						<?php self::render_toc_row( $i, $row ); ?>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p><button type="button" class="button shcore-toc-add-row"><?php esc_html_e( '+ افزودن ردیف', 'shola-core' ); ?></button></p>
			<script type="text/html" class="shcore-toc-row-template">
				<?php self::render_toc_row( '__INDEX__', array() ); ?>
			</script>
		</div>
		<?php
	}

	/**
	 * Renders one <tr> of the table-of-contents repeater — shared between
	 * existing rows (real index, real values) and the JS row template
	 * (literal `__INDEX__` placeholder, empty values, string-replaced on
	 * clone in admin/js/meta-boxes.js). SECTION options come live from the
	 * `topic` taxonomy plus a fixed TRANSLATION pseudo-option, rather than
	 * a hardcoded slug list, so a new topic term doesn't need a code
	 * change to show up here.
	 *
	 * @param int|string                                  $index Row index (or '__INDEX__' for the template).
	 * @param array{section?: string, title?: string, byline?: string} $row Row values.
	 * @return void
	 */
	private static function render_toc_row( $index, $row ) {
		$section = isset( $row['section'] ) ? $row['section'] : '';
		$title   = isset( $row['title'] ) ? $row['title'] : '';
		$byline  = isset( $row['byline'] ) ? $row['byline'] : '';
		$topics  = get_terms(
			array(
				'taxonomy'   => 'topic',
				'hide_empty' => false,
			)
		); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small, fixed-vocabulary taxonomy, admin-only.
		if ( is_wp_error( $topics ) ) {
			$topics = array();
		}
		?>
		<tr>
			<td>
				<select name="shcore_contents[<?php echo esc_attr( $index ); ?>][section]">
					<option value=""><?php esc_html_e( '— بدون بخش —', 'shola-core' ); ?></option>
					<?php foreach ( $topics as $topic ) : ?>
						<option value="<?php echo esc_attr( $topic->slug ); ?>" <?php selected( $section, $topic->slug ); ?>>
							<?php echo esc_html( $topic->name . ' (' . strtoupper( $topic->slug ) . ')' ); ?>
						</option>
					<?php endforeach; ?>
					<option value="TRANSLATION" <?php selected( $section, 'TRANSLATION' ); ?>><?php esc_html_e( 'ترجمه (TRANSLATION)', 'shola-core' ); ?></option>
				</select>
			</td>
			<td><input type="text" class="regular-text" name="shcore_contents[<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $title ); ?>"></td>
			<td><input type="text" class="regular-text" name="shcore_contents[<?php echo esc_attr( $index ); ?>][byline]" value="<?php echo esc_attr( $byline ); ?>"></td>
			<td><button type="button" class="button-link shcore-toc-remove-row" aria-label="<?php esc_attr_e( 'حذف ردیف', 'shola-core' ); ?>">✕</button></td>
		</tr>
		<?php
	}

	/**
	 * Render the document metabox fields.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_document_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$author_source = get_post_meta( $post->ID, 'shcore_author_source', true );
		$language      = get_post_meta( $post->ID, 'shcore_language', true );
		?>
		<p>
			<label for="shcore_author_source"><strong><?php esc_html_e( 'نویسنده / منبع', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_author_source" name="shcore_author_source" class="regular-text" value="<?php echo esc_attr( $author_source ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'نام نویسندهٔ اصلی متن یا منبعی که این سند از آن گرفته‌شده را بنویسید.', 'shola-core' ); ?></p>
		<?php self::render_pdf_field( $post->ID, 'shcore_pdf_id' ); ?>
		<?php self::render_language_field( $language ); ?>
		<?php
	}

	/**
	 * Render the party_publication metabox fields — a PDF and a language
	 * picker only, no author/source field (see register_meta()'s comment
	 * on why: these are the party's own works, not attributed to an
	 * external theorist per item like `document` is).
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_party_publication_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$language = get_post_meta( $post->ID, 'shcore_language', true );
		?>
		<?php self::render_pdf_field( $post->ID, 'shcore_pdf_id' ); ?>
		<?php self::render_language_field( $language ); ?>
		<?php
	}

	/**
	 * Render the party_document metabox fields — a serial number, a PDF,
	 * and a language picker. Name/title is the native post title, detail/
	 * description is the native block-editor content area (same pattern
	 * as `document`'s "about this text"), and date is the native
	 * publish/modified date — none of those need a field here.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_party_document_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$serial_number = get_post_meta( $post->ID, 'shcore_serial_number', true );
		$language      = get_post_meta( $post->ID, 'shcore_language', true );
		?>
		<p>
			<label for="shcore_serial_number"><strong><?php esc_html_e( 'شمارهٔ سریال', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_serial_number" name="shcore_serial_number" class="regular-text" value="<?php echo esc_attr( $serial_number ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'شمارهٔ ثبت یا سریال داخلی این سند حزبی را در آرشیو وارد کنید.', 'shola-core' ); ?></p>
		<?php self::render_pdf_field( $post->ID, 'shcore_pdf_id' ); ?>
		<?php self::render_language_field( $language ); ?>
		<?php
	}

	/**
	 * Render the article/post metabox fields.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_article_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$byline         = get_post_meta( $post->ID, 'shcore_byline', true );
		$author_note    = get_post_meta( $post->ID, 'shcore_author_note', true );
		$language       = get_post_meta( $post->ID, 'shcore_language', true );
		$translation_id = get_post_meta( $post->ID, 'shcore_translation_id', true );
		?>
		<p>
			<label for="shcore_byline"><strong><?php esc_html_e( 'نام مستعار نویسنده', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_byline" name="shcore_byline" class="regular-text" value="<?php echo esc_attr( $byline ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'در صورت نیاز به نام مستعار به‌جای نام کاربری وردپرس، اینجا وارد کنید؛ اختیاری است.', 'shola-core' ); ?></p>
		<p>
			<label for="shcore_author_note"><strong><?php esc_html_e( 'توضیح همکاری', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_author_note" name="shcore_author_note" class="large-text" value="<?php echo esc_attr( $author_note ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'توضیح کوتاه دربارهٔ نحوهٔ همکاری در تولید این نوشته؛ مثلاً «کاری از میز اقتصاد».', 'shola-core' ); ?></p>
		<?php self::render_language_field( $language ); ?>
		<p>
			<label for="shcore_translation_id"><strong><?php esc_html_e( 'شناسهٔ نوشتهٔ ترجمه', 'shola-core' ); ?></strong></label><br>
			<input type="number" id="shcore_translation_id" name="shcore_translation_id" class="small-text" value="<?php echo esc_attr( $translation_id ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'این فیلد هنوز فعال نیست و در حال حاضر نیازی به تکمیل آن نیست.', 'shola-core' ); ?></p>
		<?php
	}

	/**
	 * Render the hero_section metabox fields: the active-flag checkbox,
	 * the layout picker, and (only meaningful for the "lead_rail" layout)
	 * the rail-publication picker. The headline article itself is
	 * deliberately never a field here — see class-post-types.php's
	 * hero_section docblock.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_hero_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$is_active         = (bool) get_post_meta( $post->ID, 'shcore_hero_active', true );
		$layout            = get_post_meta( $post->ID, 'shcore_hero_layout', true );
		$rail_publication  = get_post_meta( $post->ID, 'shcore_hero_rail_publication', true );
		$layout           = $layout ? $layout : 'single';
		$rail_publication = $rail_publication ? $rail_publication : 'shola-jawid';

		/*
		 * Restricted to exactly the two real publication slugs
		 * sanitize_hero_rail_publication() accepts — a plain
		 * get_terms( parent => 0 ) query also picks up the taxonomy's
		 * auto-created "دسته‌بندی‌نشده" (Uncategorized) top-level term,
		 * which would show as a selectable but meaningless option here
		 * (caught live while testing this screen, 2026-09-09).
		 */
		$publication_terms = array();
		foreach ( array( 'shola-jawid', 'a-world-to-win' ) as $pub_slug ) {
			$term = get_term_by( 'slug', $pub_slug, 'publication' );
			if ( $term && ! is_wp_error( $term ) ) {
				$publication_terms[] = $term;
			}
		}
		?>
		<p>
			<label>
				<input type="checkbox" id="shcore_hero_active" name="shcore_hero_active" value="1" <?php checked( $is_active ); ?>>
				<strong><?php esc_html_e( 'این نسخه هم‌اکنون فعال است', 'shola-core' ); ?></strong>
			</label>
		</p>
		<p class="description"><?php esc_html_e( 'فعال‌سازی این نسخه، آن را در صفحهٔ اصلی نمایش می‌دهد و فعال بودن سایر نسخه‌های هدر را خودکار غیرفعال می‌کند.', 'shola-core' ); ?></p>
		<p>
			<label for="shcore_hero_layout"><strong><?php esc_html_e( 'نوع چیدمان', 'shola-core' ); ?></strong></label><br>
			<select id="shcore_hero_layout" name="shcore_hero_layout">
				<option value="single" <?php selected( $layout, 'single' ); ?>><?php esc_html_e( 'تک‌ستونی (مقالهٔ سرخط)', 'shola-core' ); ?></option>
				<option value="lead_rail" <?php selected( $layout, 'lead_rail' ); ?>><?php esc_html_e( 'مقالهٔ سرخط + ستون نشریه', 'shola-core' ); ?></option>
				<option value="overlay" <?php selected( $layout, 'overlay' ); ?>><?php esc_html_e( 'مقالهٔ سرخط با کارت شناور روی تصویر', 'shola-core' ); ?></option>
				<option value="rail_full" <?php selected( $layout, 'rail_full' ); ?>><?php esc_html_e( 'مقالهٔ سرخط + ستون نشریهٔ تمام‌عرض', 'shola-core' ); ?></option>
			</select>
		</p>
		<p class="description"><?php esc_html_e( 'تک‌ستونی: طرح فعلی سایت. دوستونی: ستونی جدا برای آخرین شمارهٔ یک نشریه، کنار مقالهٔ سرخط. کارت شناور: همان تصویر تمام‌عرض تک‌ستونی، با کارتی سفید از آخرین شماره روی گوشهٔ تصویر. ستون تمام‌عرض: مانند دوستونی، با این تفاوت که ستون نشریه تا لبهٔ مرورگر ادامه می‌یابد، نه فقط داخل بخش مرکزی صفحه. فقط در نمایشگرهای بزرگ‌تر (رایانه) دیده می‌شود.', 'shola-core' ); ?></p>
		<p>
			<label for="shcore_hero_rail_publication"><strong><?php esc_html_e( 'نشریهٔ کارت/ستون نشریه', 'shola-core' ); ?></strong></label><br>
			<select id="shcore_hero_rail_publication" name="shcore_hero_rail_publication">
				<?php foreach ( $publication_terms as $term ) : ?>
					<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $rail_publication, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p class="description"><?php esc_html_e( 'در چیدمان دوستونی و کارت شناور استفاده می‌شود؛ آخرین شمارهٔ این نشریه نمایش داده خواهد شد. در چیدمان تک‌ستونی نادیده گرفته می‌شود.', 'shola-core' ); ?></p>
		<?php
	}

	/**
	 * Shared fa/en select, since both post and document carry a language
	 * field.
	 *
	 * @param string $language Current value.
	 * @return void
	 */
	private static function render_language_field( $language ) {
		if ( ! $language ) {
			$language = 'fa';
		}
		?>
		<p>
			<label for="shcore_language"><strong><?php esc_html_e( 'زبان', 'shola-core' ); ?></strong></label><br>
			<select id="shcore_language" name="shcore_language">
				<option value="fa" <?php selected( $language, 'fa' ); ?>><?php esc_html_e( 'فارسی', 'shola-core' ); ?></option>
				<option value="en" <?php selected( $language, 'en' ); ?>>English</option>
			</select>
		</p>
		<p class="description"><?php esc_html_e( 'زبان اصلی این محتوا را مشخص کنید؛ اکنون فقط فارسی فعال است.', 'shola-core' ); ?></p>
		<?php
	}

	/**
	 * Shared PDF picker field: a hidden attachment-ID input, a filename
	 * display, and a button that opens the media library restricted to
	 * PDFs (client-side UX only — meta-boxes.js). The real enforcement is
	 * sanitize_pdf_id() above, which runs regardless of how the ID got
	 * into the field.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $meta_key Meta key holding the attachment ID.
	 * @return void
	 */
	private static function render_pdf_field( $post_id, $meta_key ) {
		$pdf_id       = (int) get_post_meta( $post_id, $meta_key, true );
		$current_name = $pdf_id ? basename( get_attached_file( $pdf_id ) ) : '';
		?>
		<p class="shcore-pdf-field">
			<label><strong><?php esc_html_e( 'فایل PDF', 'shola-core' ); ?></strong></label><br>
			<input type="hidden" class="shcore-pdf-id" name="<?php echo esc_attr( $meta_key ); ?>" value="<?php echo esc_attr( $pdf_id ); ?>">
			<span class="shcore-pdf-filename"><?php echo esc_html( $current_name ); ?></span>
			<button type="button" class="button shcore-pdf-select"><?php esc_html_e( 'انتخاب فایل PDF', 'shola-core' ); ?></button>
			<button type="button" class="button shcore-pdf-remove" <?php echo $pdf_id ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'حذف', 'shola-core' ); ?></button>
		</p>
		<p class="description"><?php esc_html_e( 'فایل نهایی PDF این محتوا را از کتابخانهٔ رسانه انتخاب یا بارگذاری کنید.', 'shola-core' ); ?></p>
		<?php
	}

	/**
	 * Save all metabox fields for the current post type. update_post_meta()
	 * routes through the sanitize_callback registered in register_meta()
	 * above, so validation (including the real PDF MIME check) applies
	 * here exactly the same as it would via REST.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function save_meta_boxes( $post_id, $post ) {
		if ( ! isset( $_POST['shcore_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['shcore_meta_nonce'] ), 'shcore_save_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields_by_type = array(
			'issue'             => array( 'shcore_issue_number', 'shcore_volume', 'shcore_pdf_id', 'shcore_contents' ),
			'document'          => array( 'shcore_author_source', 'shcore_pdf_id', 'shcore_language' ),
			'party_publication' => array( 'shcore_pdf_id', 'shcore_language' ),
			'party_document'    => array( 'shcore_serial_number', 'shcore_pdf_id', 'shcore_language' ),
			'post'              => array( 'shcore_byline', 'shcore_author_note', 'shcore_language', 'shcore_translation_id' ),
			'hero_section'      => array( 'shcore_hero_active', 'shcore_hero_layout', 'shcore_hero_rail_publication' ),
		);

		if ( ! isset( $fields_by_type[ $post->post_type ] ) ) {
			return;
		}

		foreach ( $fields_by_type[ $post->post_type ] as $field ) {
			if ( 'shcore_hero_active' === $field ) {
				// A checkbox is absent from $_POST entirely when unchecked
				// — unlike every other field here, "not present" is a real,
				// meaningful value (inactive), not "leave the old value
				// alone", so this can't use the generic isset() branch
				// below.
				$is_active = isset( $_POST['shcore_hero_active'] );
				update_post_meta( $post_id, 'shcore_hero_active', $is_active );
				if ( $is_active ) {
					self::deactivate_other_hero_sections( $post_id );
				}
				continue;
			}
			if ( 'shcore_contents' === $field ) {
				// Repeater rows arrive as shcore_contents[N][section|title|byline].
				// Absent entirely (JS never touched, or every row removed) is a
				// valid, expected state — save as an empty TOC, not an error.
				$rows = isset( $_POST['shcore_contents'] ) && is_array( $_POST['shcore_contents'] )
					? array_values( wp_unslash( $_POST['shcore_contents'] ) )
					: array();
				update_post_meta( $post_id, 'shcore_contents', wp_json_encode( $rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
				continue;
			}
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, $field, wp_unslash( $_POST[ $field ] ) );
			}
		}
	}

	/**
	 * Enforces the hero_section active-flag singleton: whenever one entry
	 * is saved as active, every other entry (any status — a stray active
	 * flag left on a draft would be just as wrong as one on a published
	 * entry) is explicitly set inactive. Runs from save_meta_boxes() only,
	 * never from register_meta()'s sanitize_callback, since that callback
	 * has no way to see or modify *other* posts.
	 *
	 * @param int $active_id Post ID of the hero_section just marked active.
	 * @return void
	 */
	private static function deactivate_other_hero_sections( $active_id ) {
		$others = get_posts(
			array(
				'post_type'      => 'hero_section',
				'posts_per_page' => -1,
				'post__not_in'   => array( $active_id ),
				'post_status'    => 'any',
				'fields'         => 'ids',
			)
		);

		foreach ( $others as $other_id ) {
			update_post_meta( $other_id, 'shcore_hero_active', false );
		}
	}

	/**
	 * Add a "وضعیت" (status) column to the hero_section list table, right
	 * after the title, so an editor can see which variant is live without
	 * opening each one.
	 *
	 * @param array<string, string> $columns Default columns.
	 * @return array<string, string>
	 */
	public static function add_hero_status_column( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['shcore_hero_status'] = __( 'وضعیت', 'shola-core' );
			}
		}
		return $new;
	}

	/**
	 * Render the "وضعیت" column's value for one row.
	 *
	 * @param string $column Column key being rendered.
	 * @param int    $post_id Post ID for this row.
	 * @return void
	 */
	public static function render_hero_status_column( $column, $post_id ) {
		if ( 'shcore_hero_status' !== $column ) {
			return;
		}
		$is_active = (bool) get_post_meta( $post_id, 'shcore_hero_active', true );
		if ( $is_active ) {
			echo '<strong style="color:#0a7d2c">' . esc_html__( 'فعال', 'shola-core' ) . '</strong>';
		} else {
			echo '<span style="color:#777">' . esc_html__( 'غیرفعال', 'shola-core' ) . '</span>';
		}
	}

	/**
	 * Add a one-click "تنظیم به‌عنوان فعال" row action on inactive
	 * hero_section entries, so switching the live variant doesn't require
	 * opening it and finding the checkbox. Already-active entries get no
	 * such link — there's nothing useful to do from here once it's live.
	 *
	 * @param array<string, string> $actions Existing row actions.
	 * @param \WP_Post              $post Post object for this row.
	 * @return array<string, string>
	 */
	public static function add_hero_set_active_row_action( $actions, $post ) {
		if ( 'hero_section' !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}
		if ( (bool) get_post_meta( $post->ID, 'shcore_hero_active', true ) ) {
			return $actions;
		}

		$url = wp_nonce_url(
			admin_url( 'admin.php?action=shcore_set_active_hero&post=' . $post->ID ),
			'shcore_set_active_hero_' . $post->ID
		);

		$actions['shcore_set_active_hero'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'تنظیم به‌عنوان فعال', 'shola-core' ) . '</a>';
		return $actions;
	}

	/**
	 * Handles the "Set as active" row-action link: verifies the nonce and
	 * edit capability, marks the requested hero_section active, deactivates
	 * every other one (same helper save_meta_boxes() uses), then redirects
	 * back to the list table.
	 *
	 * @return void
	 */
	public static function handle_set_active_hero() {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

		if ( ! $post_id
			|| ! isset( $_GET['_wpnonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'shcore_set_active_hero_' . $post_id )
			|| 'hero_section' !== get_post_type( $post_id )
			|| ! current_user_can( 'edit_post', $post_id )
		) {
			wp_die( esc_html__( 'درخواست نامعتبر است.', 'shola-core' ) );
		}

		update_post_meta( $post_id, 'shcore_hero_active', true );
		self::deactivate_other_hero_sections( $post_id );

		wp_safe_redirect( admin_url( 'edit.php?post_type=hero_section' ) );
		exit;
	}

	/**
	 * Seed one default, active "تک‌ستونی" hero_section entry so the admin
	 * list isn't empty and the active-flag mechanism is testable
	 * immediately — see init()'s comment on why this runs on admin_init
	 * rather than only on plugin activation.
	 *
	 * @return void
	 */
	public static function seed_default_hero_section() {
		if ( get_option( 'shcore_default_hero_seeded' ) ) {
			return;
		}

		$existing = get_posts(
			array(
				'post_type'      => 'hero_section',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		if ( ! $existing ) {
			$post_id = wp_insert_post(
				array(
					'post_type'   => 'hero_section',
					'post_status' => 'publish',
					'post_title'  => __( 'هدر پیش‌فرض (تک‌ستونی)', 'shola-core' ),
				)
			);

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, 'shcore_hero_active', true );
				update_post_meta( $post_id, 'shcore_hero_layout', 'single' );
				update_post_meta( $post_id, 'shcore_hero_rail_publication', 'shola-jawid' );
			}
		}

		update_option( 'shcore_default_hero_seeded', true );
	}

	/**
	 * Parses `shcore_contents` (an issue's optional table of contents)
	 * into structured entries for single-issue.php and the metabox
	 * repeater. Stored as a JSON-encoded array of
	 * `{section, title, byline}` objects (schema changed 2026-08-06 from
	 * a pipe-delimited free-text format to this repeater-backed one — see
	 * docs/CHANGELOG.md for the migration record). All three fields are
	 * genuinely optional: a row with only a title, only a section, or any
	 * other partial combination is valid and rendered as-is; only fully
	 * empty rows are dropped, and that happens on save
	 * (sanitize_issue_contents()), not here. Malformed/non-JSON stored
	 * data (e.g. pre-migration content that was never converted) decodes
	 * to an empty array rather than erroring.
	 *
	 * Deliberately no per-entry page count or link to a real article: per
	 * docs/EXECUTION_PLAN.md's Phase 0.3 resolved assumption, issues are
	 * PDF-only — a table-of-contents entry describes what's in the PDF,
	 * it isn't a real WP post with its own permalink.
	 *
	 * @param int $post_id Issue post ID.
	 * @return array<int, array{section: string, title: string, byline: string}>
	 */
	public static function get_issue_contents( $post_id ) {
		$raw = get_post_meta( $post_id, 'shcore_contents', true );
		if ( ! $raw ) {
			return array();
		}

		$decoded = json_decode( $raw, true );
		if ( ! is_array( $decoded ) ) {
			return array();
		}

		$entries = array();
		foreach ( $decoded as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$entries[] = array(
				'section' => isset( $row['section'] ) ? (string) $row['section'] : '',
				'title'   => isset( $row['title'] ) ? (string) $row['title'] : '',
				'byline'  => isset( $row['byline'] ) ? (string) $row['byline'] : '',
			);
		}

		return $entries;
	}

	/**
	 * Sanitize callback for `shcore_contents`. Accepts a JSON string
	 * (from save_meta_boxes()'s own `wp_json_encode()` of the repeater's
	 * `$_POST` rows, or from REST), sanitizes every field with
	 * `sanitize_text_field()`, and drops rows that are fully empty across
	 * all three fields — a partially-filled row (e.g. title only) is kept
	 * as-is, since every field is independently optional by design, not
	 * just tolerated.
	 *
	 * Real bug found and fixed while building this: `wp_json_encode()`
	 * without `JSON_UNESCAPED_UNICODE` escapes every non-ASCII (i.e.
	 * every Persian) character as `\uXXXX`, and something in WP's own
	 * post-meta save path (empirically confirmed via an isolated
	 * `update_post_meta()` test, not just assumed) applies
	 * `stripslashes()`-style unslashing to the value on the way to the
	 * DB — which silently eats the backslash off every `\uXXXX`
	 * sequence, turning it into literal garbage text (`u0627` instead of
	 * the character it decodes to). `JSON_UNESCAPED_UNICODE` avoids the
	 * problem entirely by never producing a backslash-escape for Persian
	 * text in the first place. `JSON_UNESCAPED_SLASHES` alongside it for
	 * the same reason, applied preventatively (no `/` in real data hit
	 * this yet, but the failure mode would be identical).
	 *
	 * @param string $raw JSON-encoded array of {section, title, byline} rows.
	 * @return string JSON-encoded, sanitized array — or '' if nothing valid remains.
	 */
	public static function sanitize_issue_contents( $raw ) {
		$decoded = json_decode( (string) $raw, true );
		if ( ! is_array( $decoded ) ) {
			return '';
		}

		$clean = array();
		foreach ( $decoded as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$section = isset( $row['section'] ) ? sanitize_text_field( $row['section'] ) : '';
			$title   = isset( $row['title'] ) ? sanitize_text_field( $row['title'] ) : '';
			$byline  = isset( $row['byline'] ) ? sanitize_text_field( $row['byline'] ) : '';

			if ( '' === $section && '' === $title && '' === $byline ) {
				continue; // Fully empty row — nothing to keep.
			}

			$clean[] = array(
				'section' => $section,
				'title'   => $title,
				'byline'  => $byline,
			);
		}

		return $clean ? wp_json_encode( $clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : '';
	}

	/**
	 * Enqueue the PDF-picker admin script, only on the edit screens for
	 * our three post types.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public static function enqueue_admin_assets( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->post_type, array( 'issue', 'document', 'party_publication', 'party_document' ), true ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script(
			'shcore-admin-meta',
			SHCORE_URL . 'admin/js/meta-boxes.js',
			array( 'jquery' ),
			SHCORE_VERSION,
			true
		);
		wp_enqueue_style(
			'shcore-admin-meta',
			SHCORE_URL . 'admin/css/meta-boxes.css',
			array(),
			SHCORE_VERSION
		);
	}
}
