<?php
/**
 * Generalizes category/subcategory management across every hierarchical
 * taxonomy this plugin owns — موضوعات (topic), مجموعه‌ها (collection),
 * دسته‌های اسناد حزب (party_document_category), گزارش (report), and
 * نشریات (publication).
 *
 * Replaces class-term-reassign.php (2026-09-04), which built this exact
 * "move content before delete" safety flow for `publication`/`issue`
 * alone. Farhad asked (2026-09-05, after using that flow) for the same
 * thing everywhere, plus two gaps it didn't cover: a permanent
 * "Uncategorized" landing spot so content is never one bad delete away
 * from vanishing, and a fixed sort-order field on every category list,
 * not just نشریات's دوره. Mirrors WordPress core's own long-standing
 * pattern for its built-in Categories taxonomy (an undeletable
 * "Uncategorized" term, always available as a fallback) rather than
 * inventing a new one.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Category/subcategory management: ordering, Uncategorized fallback,
 * and reassign-before-delete, applied uniformly to every managed taxonomy.
 */
class Category_Manager {

	/**
	 * Query-string page slug for the hidden reassignment/cascade screen.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'shcore-reassign-term';

	/**
	 * Every taxonomy this class manages, mapped to the post type its
	 * terms actually classify. Central list so adding a sixth managed
	 * taxonomy later is a one-line change here, not a hunt through every
	 * method below.
	 *
	 * @var array<string,string>
	 */
	const MANAGED = array(
		'topic'                   => 'post',
		'collection'               => 'document',
		'party_document_category'  => 'party_document',
		'report'                    => 'post',
		'publication'               => 'issue',
	);

	/**
	 * Set by enforce_depth_cap_on_update() when it silently keeps a
	 * term's existing parent instead of applying a requested change that
	 * would exceed the two-level cap — read by append_depth_capped_flag()
	 * right after, in the same request, to add a query-string flag to the
	 * post-save redirect so render_admin_notice() can tell the editor why
	 * their change didn't stick.
	 *
	 * @var bool
	 */
	private static $depth_capped = false;

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'seed_uncategorized_terms' ) );
		add_action( 'admin_menu', array( __CLASS__, 'register_page' ) );
		add_action( 'admin_notices', array( __CLASS__, 'render_admin_notice' ) );
		add_action( 'delete_term', array( __CLASS__, 'recreate_uncategorized_if_deleted' ), 10, 5 );
		add_action( 'delete_term', array( __CLASS__, 'rehome_orphans' ), 20, 5 );

		add_filter( 'pre_insert_term', array( __CLASS__, 'enforce_depth_cap_on_insert' ), 10, 3 );
		add_filter( 'wp_update_term_parent', array( __CLASS__, 'enforce_depth_cap_on_update' ), 10, 5 );
		add_filter( 'redirect_term_location', array( __CLASS__, 'append_depth_capped_flag' ), 10, 2 );
		add_filter( 'taxonomy_parent_dropdown_args', array( __CLASS__, 'filter_parent_dropdown_args' ), 10, 3 );

		foreach ( array_keys( self::MANAGED ) as $taxonomy ) {
			add_filter( "{$taxonomy}_row_actions", array( __CLASS__, 'filter_row_actions' ), 10, 2 );
		}

		/*
		 * ترتیب (manual sort order) term meta — originally built
		 * 2026-09-02 for `publication` only (دوره tiles weren't
		 * rendering in اول/دوم/سوم/چهارم order; WordPress's default term
		 * order for a custom taxonomy isn't creation order or name order
		 * in any guaranteed way). Generalized here 2026-09-05 to every
		 * managed taxonomy: a plain number field on both the "Add New"
		 * and "Edit" term screens, plus a read-only column on the term
		 * list — not made click-to-sort, same reasoning as before (the
		 * front-end sort is what actually matters, not the admin table).
		 */
		add_action( 'init', array( __CLASS__, 'register_term_order_meta' ) );
		foreach ( array_keys( self::MANAGED ) as $taxonomy ) {
			add_action( "{$taxonomy}_add_form_fields", array( __CLASS__, 'render_term_order_add_field' ) );
			add_action( "{$taxonomy}_edit_form_fields", array( __CLASS__, 'render_term_order_edit_field' ) );
			add_action( "created_{$taxonomy}", array( __CLASS__, 'save_term_order' ) );
			add_action( "edited_{$taxonomy}", array( __CLASS__, 'save_term_order' ) );
			add_filter( "manage_edit-{$taxonomy}_columns", array( __CLASS__, 'add_term_order_column' ) );
			add_filter( "manage_{$taxonomy}_custom_column", array( __CLASS__, 'render_term_order_column' ), 10, 3 );
		}
	}

	/* ------------------------------------------------------------------ */
	/* ترتیب — generalized from class-taxonomies.php's publication-only    */
	/* version (2026-09-02). Method bodies are unchanged from that         */
	/* version; only how they're hooked (once per managed taxonomy,        */
	/* instead of hardcoded to `publication`) is new.                      */
	/* ------------------------------------------------------------------ */

	/**
	 * Registers `shcore_term_order` on every managed taxonomy.
	 *
	 * @return void
	 */
	public static function register_term_order_meta() {
		foreach ( array_keys( self::MANAGED ) as $taxonomy ) {
			register_term_meta(
				$taxonomy,
				'shcore_term_order',
				array(
					'type'              => 'integer',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'absint',
					'auth_callback'     => function () {
						return current_user_can( 'manage_categories' );
					},
				)
			);
		}
	}

	/**
	 * ترتیب field on the "افزودن دستهٔ جدید" (Add New Term) side panel —
	 * WP core's own `<div class="form-field">` markup convention for that
	 * screen, matching every core field around it.
	 *
	 * @return void
	 */
	public static function render_term_order_add_field() {
		?>
		<div class="form-field">
			<label for="shcore-term-order"><?php esc_html_e( 'ترتیب', 'shola-core' ); ?></label>
			<input type="number" name="shcore_term_order" id="shcore-term-order" step="1" min="0">
			<p><?php esc_html_e( 'عددی کوچک‌تر زودتر نمایش داده می‌شود. خالی یعنی آخر فهرست.', 'shola-core' ); ?></p>
		</div>
		<?php
	}

	/**
	 * ترتیب field on the term "ویرایش" (Edit) screen — WP core's own
	 * `<tr class="form-field">` markup convention for that screen.
	 *
	 * @param \WP_Term $term Term being edited.
	 * @return void
	 */
	public static function render_term_order_edit_field( $term ) {
		$order = get_term_meta( $term->term_id, 'shcore_term_order', true );
		?>
		<tr class="form-field">
			<th scope="row"><label for="shcore-term-order"><?php esc_html_e( 'ترتیب', 'shola-core' ); ?></label></th>
			<td>
				<input type="number" name="shcore_term_order" id="shcore-term-order" step="1" min="0" value="<?php echo esc_attr( $order ); ?>">
				<p class="description"><?php esc_html_e( 'عددی کوچک‌تر زودتر نمایش داده می‌شود. خالی یعنی آخر فهرست.', 'shola-core' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Saves ترتیب on both create and edit — `created_{$taxonomy}`/
	 * `edited_{$taxonomy}` both fire with the term ID as their first arg,
	 * so one handler covers both hooks for every taxonomy. No nonce check
	 * here deliberately: both hooks only ever fire as a result of
	 * `wp_insert_term()`/`wp_update_term()` being called from WP core's
	 * own admin term-management flows, each of which already verifies its
	 * own nonce before ever reaching that call. Empty input clears the
	 * value (falls back to "last") rather than being coerced to 0, which
	 * would instead sort it *first* — `''` and `'0'` are meaningfully
	 * different here, so this checks for an empty string specifically,
	 * not falsiness.
	 *
	 * @param int $term_id Term ID.
	 * @return void
	 */
	public static function save_term_order( $term_id ) {
		if ( ! current_user_can( 'manage_categories' ) || ! isset( $_POST['shcore_term_order'] ) ) {
			return;
		}

		$raw = sanitize_text_field( wp_unslash( $_POST['shcore_term_order'] ) );
		if ( '' === $raw ) {
			delete_term_meta( $term_id, 'shcore_term_order' );
			return;
		}

		update_term_meta( $term_id, 'shcore_term_order', absint( $raw ) );
	}

	/**
	 * Adds the ترتیب column to a managed taxonomy's term list table,
	 * right after توضیح (description) — a plain read-only display, not
	 * click-to-sort (the front-end sort is what actually matters).
	 *
	 * @param string[] $columns Existing column id => label map.
	 * @return string[]
	 */
	public static function add_term_order_column( $columns ) {
		$new_columns = array();
		foreach ( $columns as $key => $label ) {
			$new_columns[ $key ] = $label;
			if ( 'description' === $key ) {
				$new_columns['shcore_term_order'] = __( 'ترتیب', 'shola-core' );
			}
		}
		return $new_columns;
	}

	/**
	 * Renders the ترتیب column's value for one row.
	 *
	 * @param string $content     Existing column content (always '' for a
	 *                            custom column — WP core never fills this in).
	 * @param string $column_name Column being rendered.
	 * @param int    $term_id     Term ID for this row.
	 * @return string
	 */
	public static function render_term_order_column( $content, $column_name, $term_id ) {
		if ( 'shcore_term_order' !== $column_name ) {
			return $content;
		}
		$order = get_term_meta( $term_id, 'shcore_term_order', true );
		return '' === $order ? '—' : esc_html( $order );
	}

	/* ------------------------------------------------------------------ */
	/* دسته‌بندی‌نشده — a permanent, visible fallback term per taxonomy.    */
	/* ------------------------------------------------------------------ */

	/**
	 * The fixed slug an Uncategorized term uses for a given taxonomy —
	 * one per taxonomy, e.g. `topic-uncategorized`. Namespaced by
	 * taxonomy rather than reusing one bare slug across taxonomies, since
	 * term slugs only need to be unique within their own taxonomy, but a
	 * shared literal string here would still invite confusion reading
	 * the term list.
	 *
	 * @param string $taxonomy Taxonomy slug.
	 * @return string
	 */
	private static function uncategorized_slug( $taxonomy ) {
		return $taxonomy . '-uncategorized';
	}

	/**
	 * Looks up a taxonomy's Uncategorized term ID without creating one —
	 * used anywhere a missing term should just mean "not found yet"
	 * rather than triggering a create (e.g. deciding whether a term
	 * being deleted *is* the Uncategorized one).
	 *
	 * @param string $taxonomy Taxonomy slug.
	 * @return int Term ID, or 0 if it doesn't exist yet.
	 */
	public static function get_uncategorized_term_id( $taxonomy ) {
		$term = get_term_by( 'slug', self::uncategorized_slug( $taxonomy ), $taxonomy );
		return ( $term && ! is_wp_error( $term ) ) ? (int) $term->term_id : 0;
	}

	/**
	 * Creates every managed taxonomy's Uncategorized term once, the same
	 * self-healing admin_init + options-flag pattern already used
	 * elsewhere in this plugin (a code-only zip re-upload doesn't re-fire
	 * activation hooks, so this can't be a one-time activation step).
	 *
	 * @return void
	 */
	public static function seed_uncategorized_terms() {
		if ( get_option( 'shcore_uncategorized_terms_seeded' ) ) {
			return;
		}
		foreach ( array_keys( self::MANAGED ) as $taxonomy ) {
			self::ensure_uncategorized_term( $taxonomy );
		}
		update_option( 'shcore_uncategorized_terms_seeded', true );
	}

	/**
	 * Gets a taxonomy's Uncategorized term ID, creating it first if it
	 * doesn't exist (unlike get_uncategorized_term_id() above) — used
	 * anywhere the term is about to be used as an actual reassignment
	 * target, where "doesn't exist yet" must never mean "silently do
	 * nothing."
	 *
	 * @param string $taxonomy Taxonomy slug.
	 * @return int Term ID, or 0 if creation failed.
	 */
	public static function ensure_uncategorized_term( $taxonomy ) {
		$existing = self::get_uncategorized_term_id( $taxonomy );
		if ( $existing ) {
			return $existing;
		}
		$result = wp_insert_term(
			__( 'دسته‌بندی‌نشده', 'shola-core' ),
			$taxonomy,
			array( 'slug' => self::uncategorized_slug( $taxonomy ) )
		);
		return is_wp_error( $result ) ? 0 : (int) $result['term_id'];
	}

	/**
	 * Removes the "حذف" (Delete) row action for an Uncategorized term
	 * itself — see filter_row_actions() below, which this is called from
	 * for every managed taxonomy's row actions.
	 *
	 * This is a UI-level guard, not a hard block: WordPress has no filter
	 * that can reject wp_delete_term() outright (see rehome_orphans()'s
	 * docblock), so a bulk-select delete or a WP-CLI/REST call can still
	 * remove this term. recreate_uncategorized_if_deleted() below is the
	 * safety net for that — it re-creates the term immediately and moves
	 * back onto it anything that was still attached at the moment of
	 * deletion, so it can be *momentarily* gone but never *stays* gone.
	 *
	 * @param \WP_Term $term Term for this row.
	 * @return bool
	 */
	private static function is_uncategorized_term( $term ) {
		return self::uncategorized_slug( $term->taxonomy ) === $term->slug;
	}

	/**
	 * If a managed taxonomy's Uncategorized term is ever deleted anyway
	 * (bulk delete, WP-CLI, REST — see is_uncategorized_term()'s
	 * docblock), recreates it immediately and moves whatever content was
	 * still attached back onto the fresh term, so the fallback can never
	 * simply disappear.
	 *
	 * @param int      $term_id      Deleted term ID.
	 * @param int      $tt_id        Term taxonomy ID (unused).
	 * @param string   $taxonomy     Taxonomy of the deleted term.
	 * @param \WP_Term $deleted_term Snapshot of the term as it was before deletion.
	 * @param int[]    $object_ids   Post IDs that were related to the term.
	 * @return void
	 */
	public static function recreate_uncategorized_if_deleted( $term_id, $tt_id, $taxonomy, $deleted_term, $object_ids ) {
		if ( ! array_key_exists( $taxonomy, self::MANAGED ) || ! is_object( $deleted_term ) ) {
			return;
		}
		if ( self::uncategorized_slug( $taxonomy ) !== $deleted_term->slug ) {
			return;
		}

		$new_id = self::ensure_uncategorized_term( $taxonomy );
		if ( $new_id && ! empty( $object_ids ) ) {
			foreach ( $object_ids as $object_id ) {
				wp_set_object_terms( (int) $object_id, array( $new_id ), $taxonomy, true );
			}
		}
	}

	/* ------------------------------------------------------------------ */
	/* Reassign-before-delete — generalized from class-term-reassign.php's */
	/* publication-only version.                                           */
	/* ------------------------------------------------------------------ */

	/**
	 * Registers the reassignment/cascade screen as a hidden submenu page —
	 * a `null` parent slug keeps it out of the wp-admin sidebar entirely,
	 * reachable only via the row-action link below.
	 *
	 * @return void
	 */
	public static function register_page() {
		$hook = add_submenu_page(
			null,
			__( 'انتقال محتوای دسته', 'shola-core' ),
			__( 'انتقال محتوای دسته', 'shola-core' ),
			'manage_categories',
			self::PAGE_SLUG,
			array( __CLASS__, 'render_page' )
		);

		if ( $hook ) {
			add_action( 'load-' . $hook, array( __CLASS__, 'handle_submission' ) );
		}
	}

	/**
	 * Replaces the default "حذف" row action, on every managed taxonomy's
	 * term list, for any term that either has content or has
	 * subcategories under it — the default delete link removes the term
	 * relationship immediately with no warning and no way to choose
	 * where the content goes. A term with neither (nothing to lose)
	 * keeps the normal delete link untouched. The Uncategorized term
	 * itself never gets a delete link at all.
	 *
	 * @param string[] $actions Existing row action links, keyed by action id.
	 * @param \WP_Term $term    Term for this row.
	 * @return string[]
	 */
	public static function filter_row_actions( $actions, $term ) {
		if ( ! current_user_can( 'manage_categories' ) ) {
			return $actions;
		}

		if ( self::is_uncategorized_term( $term ) ) {
			unset( $actions['delete'] );
			return $actions;
		}

		$has_children = self::term_has_children( $term->taxonomy, $term->term_id );
		if ( ! $has_children && $term->count < 1 ) {
			return $actions;
		}

		unset( $actions['delete'] );

		$url = add_query_arg(
			array(
				'page'     => self::PAGE_SLUG,
				'taxonomy' => $term->taxonomy,
				'term_id'  => $term->term_id,
			),
			admin_url( 'admin.php' )
		);

		$actions['shcore_reassign'] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( $url ),
			esc_html__( 'انتقال و حذف…', 'shola-core' )
		);

		return $actions;
	}

	/**
	 * Whether a term has any direct child terms.
	 *
	 * @param string $taxonomy Taxonomy slug.
	 * @param int    $term_id  Term ID.
	 * @return bool
	 */
	private static function term_has_children( $taxonomy, $term_id ) {
		$children = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'parent'     => $term_id,
				'hide_empty' => false,
				'fields'     => 'ids',
			)
		);
		return $children && ! is_wp_error( $children ) && count( $children ) > 0;
	}

	/**
	 * Handles both of the reassignment screen's form submissions, hooked
	 * on `load-{$hook}` so it runs before any output — required for
	 * `wp_safe_redirect()` to work. GET requests (the initial row-action
	 * click) fall through to render_page() untouched.
	 *
	 * @return void
	 */
	public static function handle_submission() {
		if ( isset( $_POST['shcore_cascade_submit'] ) ) {
			self::handle_cascade_submission();
			return;
		}
		if ( isset( $_POST['shcore_reassign_submit'] ) ) {
			self::handle_leaf_submission();
			return;
		}
	}

	/**
	 * Handles the leaf-term flow: move this term's content to one chosen
	 * destination (a sibling, the parent, or Uncategorized), then delete it.
	 *
	 * @return void
	 */
	private static function handle_leaf_submission() {
		$term_id  = isset( $_POST['shcore_term_id'] ) ? absint( $_POST['shcore_term_id'] ) : 0;
		$taxonomy = isset( $_POST['shcore_taxonomy'] ) ? sanitize_key( $_POST['shcore_taxonomy'] ) : '';

		check_admin_referer( 'shcore_reassign_term_' . $term_id, 'shcore_reassign_nonce' );

		if ( ! current_user_can( 'manage_categories' ) ) {
			wp_die( esc_html__( 'اجازهٔ لازم برای این کار را ندارید.', 'shola-core' ) );
		}
		if ( ! array_key_exists( $taxonomy, self::MANAGED ) ) {
			wp_die( esc_html__( 'این دسته‌بندی پشتیبانی نمی‌شود.', 'shola-core' ) );
		}

		$term = get_term( $term_id, $taxonomy );
		if ( ! $term || is_wp_error( $term ) ) {
			wp_die( esc_html__( 'این دسته دیگر وجود ندارد.', 'shola-core' ) );
		}

		$target_id = isset( $_POST['shcore_target_term_id'] ) ? absint( $_POST['shcore_target_term_id'] ) : 0;
		$target    = get_term( $target_id, $taxonomy );

		$is_sibling      = $target && ! is_wp_error( $target ) && (int) $target->parent === (int) $term->parent;
		$is_parent       = $target && ! is_wp_error( $target ) && $target_id === (int) $term->parent;
		$is_uncategorized = $target_id === self::get_uncategorized_term_id( $taxonomy );

		if ( ! $target || is_wp_error( $target ) || $target_id === $term_id || ! ( $is_sibling || $is_parent || $is_uncategorized ) ) {
			wp_die( esc_html__( 'دستهٔ مقصد معتبر نیست.', 'shola-core' ) );
		}

		$post_type = self::MANAGED[ $taxonomy ];
		$post_ids  = self::get_post_ids_for_term( $post_type, $taxonomy, $term_id );

		foreach ( $post_ids as $post_id ) {
			wp_set_object_terms( $post_id, array( $target_id ), $taxonomy, true );
			wp_remove_object_terms( $post_id, $term_id, $taxonomy );
		}

		wp_delete_term( $term_id, $taxonomy );

		wp_safe_redirect(
			add_query_arg(
				array(
					'taxonomy'          => $taxonomy,
					'post_type'         => $post_type,
					'shcore_reassigned' => count( $post_ids ),
				),
				admin_url( 'edit-tags.php' )
			)
		);
		exit;
	}

	/**
	 * Handles the parent-with-children flow: move every bit of content
	 * under this branch — the parent's own, and every direct child's —
	 * to Uncategorized in one step, delete every child, then delete the
	 * parent. No destination choice here (unlike the leaf flow):
	 * deleting a whole category on purpose is exactly the "just get this
	 * out of my way, safely" case Uncategorized exists for.
	 *
	 * @return void
	 */
	private static function handle_cascade_submission() {
		$term_id  = isset( $_POST['shcore_term_id'] ) ? absint( $_POST['shcore_term_id'] ) : 0;
		$taxonomy = isset( $_POST['shcore_taxonomy'] ) ? sanitize_key( $_POST['shcore_taxonomy'] ) : '';

		check_admin_referer( 'shcore_cascade_term_' . $term_id, 'shcore_cascade_nonce' );

		if ( ! current_user_can( 'manage_categories' ) ) {
			wp_die( esc_html__( 'اجازهٔ لازم برای این کار را ندارید.', 'shola-core' ) );
		}
		if ( ! array_key_exists( $taxonomy, self::MANAGED ) ) {
			wp_die( esc_html__( 'این دسته‌بندی پشتیبانی نمی‌شود.', 'shola-core' ) );
		}

		$term = get_term( $term_id, $taxonomy );
		if ( ! $term || is_wp_error( $term ) ) {
			wp_die( esc_html__( 'این دسته دیگر وجود ندارد.', 'shola-core' ) );
		}

		$post_type        = self::MANAGED[ $taxonomy ];
		$uncategorized_id = self::ensure_uncategorized_term( $taxonomy );

		$children = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'parent'     => $term_id,
				'hide_empty' => false,
			)
		);
		$children = ( $children && ! is_wp_error( $children ) ) ? $children : array();

		$moved = 0;
		foreach ( array_merge( array( $term ), $children ) as $branch_term ) {
			$post_ids = self::get_post_ids_for_term( $post_type, $taxonomy, $branch_term->term_id );
			foreach ( $post_ids as $post_id ) {
				wp_set_object_terms( $post_id, array( $uncategorized_id ), $taxonomy, true );
				wp_remove_object_terms( $post_id, $branch_term->term_id, $taxonomy );
				++$moved;
			}
		}

		// Children first — by the time the parent is deleted, none of
		// them still exist to be silently re-parented up a level by
		// WordPress's own default term-delete behavior.
		foreach ( $children as $child ) {
			wp_delete_term( $child->term_id, $taxonomy );
		}
		wp_delete_term( $term_id, $taxonomy );

		wp_safe_redirect(
			add_query_arg(
				array(
					'taxonomy'          => $taxonomy,
					'post_type'         => $post_type,
					'shcore_reassigned' => $moved,
				),
				admin_url( 'edit-tags.php' )
			)
		);
		exit;
	}

	/**
	 * Renders the reassignment/cascade screen — the leaf-term dropdown
	 * form if the term has no children, or the cascade confirmation if
	 * it does. If neither term nor taxonomy resolves, shows an error
	 * instead of guessing.
	 *
	 * @return void
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_categories' ) ) {
			wp_die( esc_html__( 'اجازهٔ لازم برای این کار را ندارید.', 'shola-core' ) );
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- non-mutating page load, only reads/displays; the mutating actions are their own nonce-checked forms.
		$term_id  = isset( $_GET['term_id'] ) ? absint( $_GET['term_id'] ) : 0;
		$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_key( $_GET['taxonomy'] ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( ! array_key_exists( $taxonomy, self::MANAGED ) ) {
			echo '<div class="wrap"><p>' . esc_html__( 'این دسته‌بندی پشتیبانی نمی‌شود.', 'shola-core' ) . '</p></div>';
			return;
		}

		$term = get_term( $term_id, $taxonomy );
		if ( ! $term || is_wp_error( $term ) ) {
			echo '<div class="wrap"><p>' . esc_html__( 'این دسته پیدا نشد.', 'shola-core' ) . '</p></div>';
			return;
		}

		$children = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'parent'     => $term_id,
				'hide_empty' => false,
			)
		);
		$children = ( $children && ! is_wp_error( $children ) ) ? $children : array();

		if ( $children ) {
			self::render_cascade_page( $term, $taxonomy, $children );
		} else {
			self::render_leaf_reassign_page( $term, $taxonomy );
		}
	}

	/**
	 * Renders the leaf-term dropdown form: a sibling term, the parent
	 * term, or Uncategorized. If a term somehow has none of those
	 * available (should only ever happen for a top-level term in a
	 * taxonomy with no other top-level terms yet, before Uncategorized
	 * is seeded), the form is replaced with a message.
	 *
	 * @param \WP_Term $term     Term being deleted.
	 * @param string   $taxonomy Its taxonomy.
	 * @return void
	 */
	private static function render_leaf_reassign_page( $term, $taxonomy ) {
		$back_url = add_query_arg(
			array(
				'taxonomy'  => $taxonomy,
				'post_type' => self::MANAGED[ $taxonomy ],
			),
			admin_url( 'edit-tags.php' )
		);

		$uncategorized_id = self::ensure_uncategorized_term( $taxonomy );
		$siblings         = self::get_ordered_siblings( $taxonomy, (int) $term->parent, $term->term_id, $uncategorized_id );
		$parent           = $term->parent ? get_term( $term->parent, $taxonomy ) : false;
		$parent           = ( $parent && ! is_wp_error( $parent ) ) ? $parent : false;

		$has_any_target = $siblings || $parent || ( $uncategorized_id && $uncategorized_id !== (int) $term->term_id );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( sprintf( /* translators: %s: term name being deleted. */ __( 'انتقال محتوای «%s» و حذف آن', 'shola-core' ), $term->name ) ); ?></h1>

			<?php if ( ! $has_any_target ) : ?>
				<p><?php esc_html_e( 'برای این دسته هیچ مقصدی برای انتقال محتوا وجود ندارد.', 'shola-core' ); ?></p>
				<p><a href="<?php echo esc_url( $back_url ); ?>" class="button"><?php esc_html_e( 'بازگشت', 'shola-core' ); ?></a></p>
			<?php else : ?>
				<p>
					<?php
					echo esc_html(
						sprintf(
							/* translators: 1: item count, 2: term name being deleted. */
							_n(
								'%1$d مورد در «%2$s» وجود دارد. پیش از حذف این دسته، مشخص کنید همهٔ آن‌ها به کدام دسته منتقل شوند.',
								'%1$d مورد در «%2$s» وجود دارد. پیش از حذف این دسته، مشخص کنید همهٔ آن‌ها به کدام دسته منتقل شوند.',
								$term->count,
								'shola-core'
							),
							$term->count,
							$term->name
						)
					);
					?>
				</p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) ); ?>">
					<?php wp_nonce_field( 'shcore_reassign_term_' . $term->term_id, 'shcore_reassign_nonce' ); ?>
					<input type="hidden" name="shcore_term_id" value="<?php echo esc_attr( $term->term_id ); ?>">
					<input type="hidden" name="shcore_taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>">
					<table class="form-table">
						<tr>
							<th scope="row"><label for="shcore-target-term"><?php esc_html_e( 'انتقال به', 'shola-core' ); ?></label></th>
							<td>
								<select name="shcore_target_term_id" id="shcore-target-term">
									<?php if ( $parent ) : ?>
										<option value="<?php echo esc_attr( $parent->term_id ); ?>">
											<?php
											/* translators: %s: parent term name. */
											echo esc_html( sprintf( __( '↑ دستهٔ مادر: %s', 'shola-core' ), $parent->name ) );
											?>
										</option>
									<?php endif; ?>
									<?php foreach ( $siblings as $sibling ) : ?>
										<option value="<?php echo esc_attr( $sibling->term_id ); ?>"><?php echo esc_html( $sibling->name ); ?></option>
									<?php endforeach; ?>
									<?php if ( $uncategorized_id && $uncategorized_id !== (int) $term->term_id ) : ?>
										<option value="<?php echo esc_attr( $uncategorized_id ); ?>"><?php esc_html_e( 'دسته‌بندی‌نشده', 'shola-core' ); ?></option>
									<?php endif; ?>
								</select>
							</td>
						</tr>
					</table>
					<p class="submit">
						<button type="submit" name="shcore_reassign_submit" class="button button-primary"><?php esc_html_e( 'انتقال و حذف نهایی', 'shola-core' ); ?></button>
						<a href="<?php echo esc_url( $back_url ); ?>" class="button"><?php esc_html_e( 'انصراف', 'shola-core' ); ?></a>
					</p>
				</form>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Renders the cascade-confirmation screen for a term that has
	 * subcategories under it.
	 *
	 * @param \WP_Term   $term     Parent term being deleted.
	 * @param string     $taxonomy Its taxonomy.
	 * @param \WP_Term[] $children Its direct children.
	 * @return void
	 */
	private static function render_cascade_page( $term, $taxonomy, $children ) {
		$back_url = add_query_arg(
			array(
				'taxonomy'  => $taxonomy,
				'post_type' => self::MANAGED[ $taxonomy ],
			),
			admin_url( 'edit-tags.php' )
		);

		$total = (int) $term->count;
		foreach ( $children as $child ) {
			$total += (int) $child->count;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( sprintf( /* translators: %s: term name being deleted. */ __( 'حذف «%s» و همهٔ زیردسته‌های آن', 'shola-core' ), $term->name ) ); ?></h1>

			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %d: number of subcategories. */
						_n( 'این دسته %d زیردسته دارد:', 'این دسته %d زیردسته دارد:', count( $children ), 'shola-core' ),
						count( $children )
					)
				);
				?>
			</p>
			<ul style="list-style:disc;margin-inline-start:1.5em;">
				<?php foreach ( $children as $child ) : ?>
					<li><?php echo esc_html( $child->name ); ?></li>
				<?php endforeach; ?>
			</ul>
			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %d: total item count across the parent and its subcategories. */
						_n(
							'در مجموع %d مورد در این دسته و زیردسته‌های آن وجود دارد. با ادامه، همهٔ آن‌ها به «دسته‌بندی‌نشده» منتقل می‌شوند و این دسته و همهٔ زیردسته‌هایش برای همیشه حذف خواهند شد.',
							'در مجموع %d مورد در این دسته و زیردسته‌های آن وجود دارد. با ادامه، همهٔ آن‌ها به «دسته‌بندی‌نشده» منتقل می‌شوند و این دسته و همهٔ زیردسته‌هایش برای همیشه حذف خواهند شد.',
							$total,
							'shola-core'
						),
						$total
					)
				);
				?>
			</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) ); ?>">
				<?php wp_nonce_field( 'shcore_cascade_term_' . $term->term_id, 'shcore_cascade_nonce' ); ?>
				<input type="hidden" name="shcore_term_id" value="<?php echo esc_attr( $term->term_id ); ?>">
				<input type="hidden" name="shcore_taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>">
				<p class="submit">
					<button type="submit" name="shcore_cascade_submit" class="button button-primary"><?php esc_html_e( 'انتقال همه به «دسته‌بندی‌نشده» و حذف نهایی', 'shola-core' ); ?></button>
					<a href="<?php echo esc_url( $back_url ); ?>" class="button"><?php esc_html_e( 'انصراف', 'shola-core' ); ?></a>
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Shows a one-time success notice after a manual reassign-and-delete
	 * (leaf or cascade), read off the `shcore_reassigned` query arg both
	 * submission handlers redirect with. Also shows a separate notice
	 * when enforce_depth_cap_on_update() silently kept a term's parent
	 * unchanged instead of applying a requested re-parent that would
	 * have exceeded the two-level cap.
	 *
	 * @return void
	 */
	public static function render_admin_notice() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only display of a redirect result, not a state change.
		if ( isset( $_GET['shcore_reassigned'] ) ) {
			$count = absint( $_GET['shcore_reassigned'] );
			?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: number of items moved. */
							_n( '%d مورد منتقل شد و دسته حذف گردید.', '%d مورد منتقل شد و دسته حذف گردید.', $count, 'shola-core' ),
							$count
						)
					);
					?>
				</p>
			</div>
			<?php
		}

		if ( isset( $_GET['shcore_depth_capped'] ) ) {
			?>
			<div class="notice notice-warning is-dismissible">
				<p><?php esc_html_e( 'این سامانه فقط دو سطح دسته‌بندی را پشتیبانی می‌کند (دسته و زیردسته). دستهٔ مادر این مورد تغییر نکرد.', 'shola-core' ); ?></p>
			</div>
			<?php
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/* ------------------------------------------------------------------ */
	/* Safety net for deletion paths that skip the screen above            */
	/* (bulk delete, WP-CLI, REST API).                                     */
	/* ------------------------------------------------------------------ */

	/**
	 * Safety net for any managed-taxonomy term deletion that didn't go
	 * through the screen above. WordPress core has no filter that can
	 * block wp_delete_term() outright (`pre_delete_term` is an action,
	 * fired for informational purposes only, not a short-circuit
	 * filter), so instead of trying to prevent the delete, this rehomes
	 * whatever WordPress reports as `$object_ids` (objects still related
	 * to the term at the moment of deletion) onto a sibling if one
	 * exists, or Uncategorized otherwise — never left with nowhere to
	 * go, unlike the original `publication`-only version this replaces,
	 * which just gave up if there was no sibling.
	 *
	 * Runs at priority 20, after recreate_uncategorized_if_deleted()
	 * (priority 10): if the deleted term *was* Uncategorized, that
	 * method already re-created it and moved this same content back onto
	 * it, so this method's own `is_uncategorized_term()` check below is
	 * what keeps it from doing that work a second time.
	 *
	 * @param int      $term_id      Deleted term ID.
	 * @param int      $tt_id        Term taxonomy ID (unused).
	 * @param string   $taxonomy     Taxonomy of the deleted term.
	 * @param \WP_Term $deleted_term Snapshot of the term as it was before deletion.
	 * @param int[]    $object_ids   Post IDs that were related to the term.
	 * @return void
	 */
	public static function rehome_orphans( $term_id, $tt_id, $taxonomy, $deleted_term, $object_ids ) {
		if ( ! array_key_exists( $taxonomy, self::MANAGED ) || empty( $object_ids ) || ! is_object( $deleted_term ) ) {
			return;
		}
		if ( self::is_uncategorized_term( $deleted_term ) ) {
			return;
		}

		$uncategorized_id = self::ensure_uncategorized_term( $taxonomy );
		$siblings         = self::get_ordered_siblings( $taxonomy, (int) $deleted_term->parent, $term_id, $uncategorized_id );
		$fallback_id      = $siblings ? (int) $siblings[0]->term_id : $uncategorized_id;

		if ( ! $fallback_id ) {
			return;
		}
		foreach ( $object_ids as $object_id ) {
			wp_set_object_terms( (int) $object_id, array( $fallback_id ), $taxonomy, true );
		}
	}

	/**
	 * Sibling terms under the same parent (0 for top-level), ordered by
	 * the ترتیب term meta the same way the front end sorts them —
	 * excludes the term being deleted and the taxonomy's own
	 * Uncategorized term (offered separately, always, rather than mixed
	 * into this list).
	 *
	 * @param string $taxonomy         Taxonomy slug.
	 * @param int    $parent_id        Parent term ID (0 for top-level).
	 * @param int    $exclude_term_id  Term ID to leave out (the one being deleted).
	 * @param int    $uncategorized_id That taxonomy's Uncategorized term ID, also excluded.
	 * @return \WP_Term[]
	 */
	private static function get_ordered_siblings( $taxonomy, $parent_id, $exclude_term_id, $uncategorized_id ) {
		$exclude = array_unique( array( $exclude_term_id, $uncategorized_id ) );

		$siblings = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'parent'     => $parent_id,
				'exclude'    => $exclude,
				'hide_empty' => false,
			)
		);

		if ( ! $siblings || is_wp_error( $siblings ) ) {
			return array();
		}

		usort(
			$siblings,
			function ( $a, $b ) {
				$order_a = get_term_meta( $a->term_id, 'shcore_term_order', true );
				$order_b = get_term_meta( $b->term_id, 'shcore_term_order', true );
				$order_a = ( '' === $order_a ) ? PHP_INT_MAX : (int) $order_a;
				$order_b = ( '' === $order_b ) ? PHP_INT_MAX : (int) $order_b;
				return $order_a <=> $order_b;
			}
		);

		return $siblings;
	}

	/**
	 * Post IDs of one post type currently related to one term, not
	 * counting descendants — used identically by both submission
	 * handlers and by rehome_orphans() would use if it queried directly
	 * (it uses WordPress's own `$object_ids` param instead, since that's
	 * already exactly this same information for the term being deleted).
	 *
	 * @param string $post_type Post type to query.
	 * @param string $taxonomy  Taxonomy slug.
	 * @param int    $term_id   Term ID.
	 * @return int[]
	 */
	private static function get_post_ids_for_term( $post_type, $taxonomy, $term_id ) {
		return get_posts(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- admin-triggered one-off action, not a recurring front-end query.
					array(
						'taxonomy'         => $taxonomy,
						'field'            => 'term_id',
						'terms'            => $term_id,
						'include_children' => false,
					),
				),
			)
		);
	}

	/* ------------------------------------------------------------------ */
	/* Depth cap — category → subcategory, never a third level.            */
	/* ------------------------------------------------------------------ */

	/**
	 * Rejects creating a new term under a parent that itself already has
	 * a parent (which would put the new term at a third level). Only
	 * applies to this plugin's own managed taxonomies — WordPress core's
	 * `category`/`post_tag` (unused on this site's `post` type, per
	 * remove_core_category_from_post(), but still registered core
	 * taxonomies) are left alone.
	 *
	 * @param string|\WP_Error $term     Term name, or a WP_Error already
	 *                                    set by an earlier filter on this
	 *                                    same hook.
	 * @param string           $taxonomy Taxonomy slug.
	 * @param array            $args     Args passed to wp_insert_term(), including 'parent' if set.
	 * @return string|\WP_Error
	 */
	public static function enforce_depth_cap_on_insert( $term, $taxonomy, $args ) {
		if ( is_wp_error( $term ) || ! array_key_exists( $taxonomy, self::MANAGED ) ) {
			return $term;
		}

		$parent = isset( $args['parent'] ) ? (int) $args['parent'] : 0;
		if ( $parent <= 0 ) {
			return $term;
		}

		$parent_term = get_term( $parent, $taxonomy );
		if ( $parent_term && ! is_wp_error( $parent_term ) && (int) $parent_term->parent > 0 ) {
			return new \WP_Error(
				'shcore_depth_cap',
				__( 'این سامانه فقط دو سطح دسته‌بندی را پشتیبانی می‌کند (دسته و زیردسته) — دستهٔ انتخاب‌شده خودش یک زیردسته است.', 'shola-core' )
			);
		}

		return $term;
	}

	/**
	 * Silently keeps a term's existing parent instead of applying a
	 * requested change that would exceed the two-level cap — either
	 * because the requested new parent itself already has a parent, or
	 * because this term already has children of its own (which would be
	 * pushed to a third level along with it). Can't cleanly reject the
	 * whole save here the way enforce_depth_cap_on_insert() does for a
	 * new term (`wp_update_term_parent` only filters the parent value,
	 * not the overall result) — append_depth_capped_flag() below adds a
	 * query-string flag to the post-save redirect instead, so
	 * render_admin_notice() can tell the editor why their change to this
	 * one field didn't stick, without blocking the rest of the edit.
	 * filter_parent_dropdown_args() (2026-09-05) keeps an editor from
	 * ever picking an invalid parent through the dropdown in the first
	 * place, but this enforcement stays regardless — the dropdown is a
	 * convenience, not the actual guarantee.
	 *
	 * @param int    $parent      Requested parent term ID.
	 * @param int    $term_id     Term being edited.
	 * @param string $taxonomy    Taxonomy slug.
	 * @param array  $parsed_args Parsed update args (unused).
	 * @param array  $args        Raw args passed to wp_update_term() (unused).
	 * @return int
	 */
	public static function enforce_depth_cap_on_update( $parent, $term_id, $taxonomy, $parsed_args, $args ) {
		if ( ! array_key_exists( $taxonomy, self::MANAGED ) ) {
			return $parent;
		}

		$parent  = (int) $parent;
		$current = get_term( (int) $term_id, $taxonomy );
		$current_parent = ( $current && ! is_wp_error( $current ) ) ? (int) $current->parent : 0;

		if ( $parent <= 0 ) {
			return $parent;
		}

		$too_deep = false;

		$parent_term = get_term( $parent, $taxonomy );
		if ( $parent_term && ! is_wp_error( $parent_term ) && (int) $parent_term->parent > 0 ) {
			$too_deep = true;
		}

		if ( ! $too_deep && self::term_has_children( $taxonomy, (int) $term_id ) ) {
			$too_deep = true;
		}

		if ( $too_deep ) {
			self::$depth_capped = true;
			return $current_parent;
		}

		return $parent;
	}

	/**
	 * Adds the `shcore_depth_capped` flag to the term-edit redirect when
	 * enforce_depth_cap_on_update() kept a term's parent unchanged during
	 * this request — see that method's docblock.
	 *
	 * @param string $location Redirect URL WordPress is about to send the browser to.
	 * @param int    $term_id  Term ID (unused).
	 * @return string
	 */
	public static function append_depth_capped_flag( $location, $term_id ) {
		if ( self::$depth_capped ) {
			$location = add_query_arg( 'shcore_depth_capped', 1, $location );
		}
		return $location;
	}

	/**
	 * Removes any term that already has a parent from the "دستهٔ مادر"
	 * (Parent) dropdown on every managed taxonomy's Add/Edit term
	 * screens — added 2026-09-05, after Farhad asked to close the gap
	 * enforce_depth_cap_on_insert()/enforce_depth_cap_on_update() leave
	 * open: those two stop an invalid choice from actually being saved,
	 * but the dropdown itself still *listed* it as pickable, so an
	 * editor could choose it and only find out it was wrong afterwards
	 * (an error on create, a silent no-op plus a notice on update).
	 * Filtering the option list itself means there's nothing invalid to
	 * pick in the first place — the two enforcement methods above stay
	 * as they are, as the actual guarantee (this is a dropdown, not a
	 * lock; nothing stops a request built by hand or a future admin
	 * screen from still trying to submit an excluded ID).
	 *
	 * Hooks `taxonomy_parent_dropdown_args`, which WordPress core fires
	 * for the Parent dropdown on both the "Add New Term" screen
	 * (`$context` 'new') and the "Edit Term" screen ('edit') — the same
	 * exclusion is correct either way, so `$context` doesn't need to be
	 * inspected. Only touches this plugin's own managed taxonomies;
	 * WordPress core's `category`/`post_tag` (unused on this site's
	 * `post` type, but still registered) are left exactly as WordPress
	 * itself renders them.
	 *
	 * @param array  $args     Dropdown args WordPress is about to pass to wp_dropdown_categories().
	 * @param string $taxonomy Taxonomy slug.
	 * @param string $context  'new' or 'edit' (unused — same fix either way).
	 * @return array
	 */
	public static function filter_parent_dropdown_args( $args, $taxonomy, $context ) {
		if ( ! array_key_exists( $taxonomy, self::MANAGED ) ) {
			return $args;
		}

		$all_terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);
		if ( ! $all_terms || is_wp_error( $all_terms ) ) {
			return $args;
		}

		$already_nested_ids = array();
		foreach ( $all_terms as $existing_term ) {
			if ( (int) $existing_term->parent > 0 ) {
				$already_nested_ids[] = (int) $existing_term->term_id;
			}
		}

		if ( ! $already_nested_ids ) {
			return $args;
		}

		$existing_exclude = isset( $args['exclude'] ) ? (array) $args['exclude'] : array();
		$args['exclude']  = array_unique( array_merge( $existing_exclude, $already_nested_ids ) );

		return $args;
	}
}
