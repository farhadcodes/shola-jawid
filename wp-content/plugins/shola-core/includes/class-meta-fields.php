<?php
/**
 * Registers post meta for issue, document, and article/post, per the IA
 * doc §5 content-model table, plus the metabox UI editors use to fill
 * them in from wp-admin without touching code.
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
		add_meta_box( 'shcore_article_fields', __( 'اطلاعات مقاله', 'shola-core' ), array( __CLASS__, 'render_article_metabox' ), 'post', 'normal', 'high' );
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
		<p>
			<label for="shcore_volume"><strong><?php esc_html_e( 'دوره / جلد', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_volume" name="shcore_volume" class="regular-text" value="<?php echo esc_attr( $volume ); ?>">
		</p>
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
			<label for="shcore_byline"><strong><?php esc_html_e( 'نام مستعار نویسنده (اختیاری — در صورت خالی بودن، نام کاربر وردپرس نمایش داده می‌شود)', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_byline" name="shcore_byline" class="regular-text" value="<?php echo esc_attr( $byline ); ?>">
		</p>
		<p>
			<label for="shcore_author_note"><strong><?php esc_html_e( 'توضیح همکاری (اختیاری — مثلاً «کاری از میز اقتصاد؛ در همکاری با...»)', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_author_note" name="shcore_author_note" class="large-text" value="<?php echo esc_attr( $author_note ); ?>">
		</p>
		<?php self::render_language_field( $language ); ?>
		<p>
			<label for="shcore_translation_id"><strong><?php esc_html_e( 'شناسهٔ نوشتهٔ ترجمه (غیرفعال در این مرحله — فقط زیرساخت دوزبانه)', 'shola-core' ); ?></strong></label><br>
			<input type="number" id="shcore_translation_id" name="shcore_translation_id" class="small-text" value="<?php echo esc_attr( $translation_id ); ?>">
		</p>
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
			'issue'    => array( 'shcore_issue_number', 'shcore_volume', 'shcore_pdf_id', 'shcore_contents' ),
			'document' => array( 'shcore_author_source', 'shcore_pdf_id', 'shcore_language' ),
			'post'     => array( 'shcore_byline', 'shcore_author_note', 'shcore_language', 'shcore_translation_id' ),
		);

		if ( ! isset( $fields_by_type[ $post->post_type ] ) ) {
			return;
		}

		foreach ( $fields_by_type[ $post->post_type ] as $field ) {
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
		if ( ! $screen || ! in_array( $screen->post_type, array( 'issue', 'document' ), true ) ) {
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
