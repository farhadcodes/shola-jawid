<?php
/**
 * Guards deletion of a `publication` term (typically a دوره) that still
 * has issues assigned to it. Added 2026-09-04 after Farhad relayed the
 * client's concern: the client had already uploaded real PDF issues into
 * some دوره sub-terms, and deleting a WordPress term never deletes the
 * posts/PDFs themselves — it only removes that term's relationship to
 * them, which silently makes those issues unreachable from
 * taxonomy-publication.php's دوره tiles. From the client's point of view
 * that reads as "the content got deleted," so this makes reassignment
 * mandatory rather than letting it happen by accident:
 *
 * - The normal "حذف" row action on edit-tags.php is replaced, for any
 *   `publication` term that still has posts, with a link to a small
 *   dedicated screen where a target sibling term is chosen; only after
 *   that choice are the issues moved and the original term deleted.
 * - As a safety net for any deletion path that doesn't go through that
 *   screen (bulk delete, WP-CLI, REST API — none of which this class can
 *   cleanly intercept before the fact, since WordPress core has no
 *   filter that can block wp_delete_term() outright), a `delete_term`
 *   handler automatically moves any issues WordPress already reports as
 *   affected onto the deleted term's best-ordered sibling, so a دوره can
 *   never be deleted with its issues simply left to vanish from
 *   navigation.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reassign-before-delete flow for `publication` terms with content.
 */
class Term_Reassign {

	/**
	 * Query-string page slug for the hidden reassignment screen.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'shcore-reassign-term';

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_page' ) );
		add_filter( 'publication_row_actions', array( __CLASS__, 'filter_row_actions' ), 10, 2 );
		add_action( 'delete_term', array( __CLASS__, 'rehome_orphans' ), 10, 5 );
		add_action( 'admin_notices', array( __CLASS__, 'render_admin_notice' ) );
	}

	/**
	 * Registers the reassignment screen as a hidden submenu page — a
	 * `null` parent slug keeps it out of the wp-admin sidebar entirely
	 * (there is no other precedent for this in the plugin, which
	 * otherwise only ever adds visible Settings-API subpages; this one is
	 * deliberately reachable only via the row-action link below, never
	 * browsed to directly). The `load-{$hook}` action fires early enough,
	 * before any admin-page markup is sent, to allow handle_submission()
	 * to `wp_safe_redirect()` after processing the form.
	 *
	 * @return void
	 */
	public static function register_page() {
		$hook = add_submenu_page(
			null,
			__( 'انتقال محتوای دوره', 'shola-core' ),
			__( 'انتقال محتوای دوره', 'shola-core' ),
			'manage_categories',
			self::PAGE_SLUG,
			array( __CLASS__, 'render_page' )
		);

		if ( $hook ) {
			add_action( 'load-' . $hook, array( __CLASS__, 'handle_submission' ) );
		}
	}

	/**
	 * Replaces the default "حذف" row action with a link into the
	 * reassignment screen for any `publication` term that still has
	 * issues assigned. The default delete link removes the term
	 * relationship immediately with no warning and no way to choose where
	 * the content goes — exactly what the client asked this to prevent.
	 * Terms with zero issues keep the normal delete link untouched.
	 *
	 * @param string[] $actions Existing row action links, keyed by action id.
	 * @param \WP_Term $term    Term for this row.
	 * @return string[]
	 */
	public static function filter_row_actions( $actions, $term ) {
		if ( $term->count < 1 || ! current_user_can( 'manage_categories' ) ) {
			return $actions;
		}

		unset( $actions['delete'] );

		$url = add_query_arg(
			array(
				'page'    => self::PAGE_SLUG,
				'term_id' => $term->term_id,
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
	 * Handles the reassignment form's POST, hooked on `load-{$hook}` so it
	 * runs before any output — required for `wp_safe_redirect()` to work.
	 * GET requests (the initial row-action click) fall through to
	 * render_page() untouched.
	 *
	 * @return void
	 */
	public static function handle_submission() {
		if ( ! isset( $_POST['shcore_reassign_submit'] ) ) {
			return;
		}

		$term_id = isset( $_POST['shcore_term_id'] ) ? absint( $_POST['shcore_term_id'] ) : 0;

		check_admin_referer( 'shcore_reassign_term_' . $term_id, 'shcore_reassign_nonce' );

		if ( ! current_user_can( 'manage_categories' ) ) {
			wp_die( esc_html__( 'اجازهٔ لازم برای این کار را ندارید.', 'shola-core' ) );
		}

		$term = get_term( $term_id, 'publication' );
		if ( ! $term || is_wp_error( $term ) ) {
			wp_die( esc_html__( 'این دوره دیگر وجود ندارد.', 'shola-core' ) );
		}

		$target_id = isset( $_POST['shcore_target_term_id'] ) ? absint( $_POST['shcore_target_term_id'] ) : 0;
		$target    = get_term( $target_id, 'publication' );
		if ( ! $target || is_wp_error( $target ) || (int) $target->parent !== (int) $term->parent || $target_id === $term_id ) {
			wp_die( esc_html__( 'دورهٔ مقصد معتبر نیست.', 'shola-core' ) );
		}

		$issue_ids = get_posts(
			array(
				'post_type'      => 'issue',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small, fixed-vocabulary taxonomy, admin-triggered one-off action, not a recurring query.
					array(
						'taxonomy'         => 'publication',
						'field'            => 'term_id',
						'terms'            => $term_id,
						'include_children' => false,
					),
				),
			)
		);

		foreach ( $issue_ids as $issue_id ) {
			wp_set_object_terms( $issue_id, array( $target_id ), 'publication', true );
			// Remove the source relationship immediately, not just leave it
			// for wp_delete_term() to clean up — rehome_orphans() below
			// decides whether to act by checking wp_delete_term()'s
			// $object_ids param (objects still related at the moment of
			// deletion), so an issue already moved here must not still be
			// showing as related to $term_id when that fires, or it would
			// get a second, redundant term appended by the safety net.
			wp_remove_object_terms( $issue_id, $term_id, 'publication' );
		}

		wp_delete_term( $term_id, 'publication' );

		wp_safe_redirect(
			add_query_arg(
				array(
					'taxonomy'          => 'publication',
					'post_type'         => 'issue',
					'shcore_reassigned' => count( $issue_ids ),
				),
				admin_url( 'edit-tags.php' )
			)
		);
		exit;
	}

	/**
	 * Renders the reassignment screen: term name, how many issues will
	 * move, a dropdown of sibling terms (same parent, i.e. other دوره
	 * under the same نشریه — or other top-level نشریه if this term itself
	 * has no parent), and a submit button. If no sibling exists yet, the
	 * form is replaced with a message rather than letting the admin
	 * submit with nowhere valid to send the content.
	 *
	 * @return void
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_categories' ) ) {
			wp_die( esc_html__( 'اجازهٔ لازم برای این کار را ندارید.', 'shola-core' ) );
		}

		$term_id = isset( $_GET['term_id'] ) ? absint( $_GET['term_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- non-mutating page load, only reads/displays; the mutating action below is its own nonce-checked form.
		$term    = get_term( $term_id, 'publication' );

		if ( ! $term || is_wp_error( $term ) ) {
			echo '<div class="wrap"><p>' . esc_html__( 'این دوره پیدا نشد.', 'shola-core' ) . '</p></div>';
			return;
		}

		$siblings = self::get_ordered_siblings( (int) $term->parent, $term_id );
		$back_url = add_query_arg(
			array(
				'taxonomy'  => 'publication',
				'post_type' => 'issue',
			),
			admin_url( 'edit-tags.php' )
		);

		?>
		<div class="wrap">
			<h1><?php echo esc_html( sprintf( /* translators: %s: term name being deleted. */ __( 'انتقال محتوای «%s» و حذف آن', 'shola-core' ), $term->name ) ); ?></h1>

			<?php if ( ! $siblings ) : ?>
				<p>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: term name. */
							__( 'برای «%1$s» هیچ دورهٔ دیگری برای انتقال محتوا وجود ندارد. ابتدا یک دورهٔ دیگر بسازید، سپس دوباره تلاش کنید.', 'shola-core' ),
							$term->name
						)
					);
					?>
				</p>
				<p><a href="<?php echo esc_url( $back_url ); ?>" class="button"><?php esc_html_e( 'بازگشت به فهرست نشریات', 'shola-core' ); ?></a></p>
			<?php else : ?>
				<p>
					<?php
					echo esc_html(
						sprintf(
							/* translators: 1: number of issues, 2: term name being deleted. */
							_n(
								'%1$d شماره در «%2$s» وجود دارد. پیش از حذف این دوره، مشخص کنید همهٔ آن‌ها به کدام دوره منتقل شوند.',
								'%1$d شماره در «%2$s» وجود دارد. پیش از حذف این دوره، مشخص کنید همهٔ آن‌ها به کدام دوره منتقل شوند.',
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
					<?php wp_nonce_field( 'shcore_reassign_term_' . $term_id, 'shcore_reassign_nonce' ); ?>
					<input type="hidden" name="shcore_term_id" value="<?php echo esc_attr( $term_id ); ?>">
					<table class="form-table">
						<tr>
							<th scope="row"><label for="shcore-target-term"><?php esc_html_e( 'انتقال به', 'shola-core' ); ?></label></th>
							<td>
								<select name="shcore_target_term_id" id="shcore-target-term">
									<?php foreach ( $siblings as $sibling ) : ?>
										<option value="<?php echo esc_attr( $sibling->term_id ); ?>"><?php echo esc_html( $sibling->name ); ?></option>
									<?php endforeach; ?>
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
	 * Shows a one-time success notice after a manual reassign-and-delete,
	 * read off the `shcore_reassigned` query arg set by handle_submission()'s
	 * redirect.
	 *
	 * @return void
	 */
	public static function render_admin_notice() {
		if ( ! isset( $_GET['shcore_reassigned'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only display of a redirect result, not a state change.
			return;
		}

		$count = absint( $_GET['shcore_reassigned'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- see above.
		?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %d: number of issues moved. */
						_n( '%d شماره منتقل شد و دوره حذف گردید.', '%d شماره منتقل شد و دوره حذف گردید.', $count, 'shola-core' ),
						$count
					)
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Safety net for any `publication` term deletion that didn't go
	 * through handle_submission() above — bulk delete, WP-CLI, REST API.
	 * WordPress core has no filter that can block wp_delete_term()
	 * outright (`pre_delete_term` is an action, fired for informational
	 * purposes only, not a short-circuit filter), so instead of trying to
	 * prevent the delete, this rehomes whatever WordPress reports as
	 * `$object_ids` (objects that were still related to the term at the
	 * moment of deletion — a native param since WP 4.5) onto that term's
	 * best-ordered sibling, so issues can never simply fall out of
	 * navigation. A no-op when handle_submission() already moved
	 * everything off the term first (then `$object_ids` is empty).
	 *
	 * @param int      $term_id      Deleted term ID.
	 * @param int      $tt_id        Term taxonomy ID (unused).
	 * @param string   $taxonomy     Taxonomy of the deleted term.
	 * @param \WP_Term $deleted_term Snapshot of the term as it was before deletion.
	 * @param int[]    $object_ids   Post IDs that were related to the term.
	 * @return void
	 */
	public static function rehome_orphans( $term_id, $tt_id, $taxonomy, $deleted_term, $object_ids ) {
		if ( 'publication' !== $taxonomy || empty( $object_ids ) || ! is_object( $deleted_term ) ) {
			return;
		}

		$parent_id = (int) $deleted_term->parent;
		$siblings  = self::get_ordered_siblings( $parent_id, $term_id );
		if ( ! $siblings ) {
			return;
		}

		$fallback_id = (int) $siblings[0]->term_id;
		foreach ( $object_ids as $object_id ) {
			wp_set_object_terms( (int) $object_id, array( $fallback_id ), 'publication', true );
		}
	}

	/**
	 * Sibling `publication` terms under the same parent (other دوره under
	 * the same نشریه, or other top-level نشریه when $parent_id is 0),
	 * ordered by the ترتیب term meta the same way taxonomy-publication.php
	 * sorts دوره tiles on the front end — so both the dropdown here and
	 * the automatic fallback in rehome_orphans() pick the same "first"
	 * sibling a person would expect.
	 *
	 * @param int $parent_id       Parent term ID (0 for top-level).
	 * @param int $exclude_term_id Term ID to leave out of the results.
	 * @return \WP_Term[]
	 */
	private static function get_ordered_siblings( $parent_id, $exclude_term_id ) {
		$siblings = get_terms(
			array(
				'taxonomy'   => 'publication',
				'parent'     => $parent_id,
				'exclude'    => array( $exclude_term_id ),
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
}
