<?php
/**
 * Renders a hierarchical taxonomy checklist (the same tree WordPress core's
 * `post_categories_meta_box()` draws for a hierarchical taxonomy) with radio
 * inputs instead of checkboxes, so picking one term visually replaces
 * whichever was picked before rather than adding to it. Used only for the
 * `publication` taxonomy on `issue` posts — see class-taxonomies.php's
 * `use_single_select_publication_metabox()` for why.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Walker_Category_Checklist' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-walker-category-checklist.php';
}

/**
 * Single-select variant of WordPress core's category-checklist walker.
 */
class Term_Radio_Walker extends \Walker_Category_Checklist {

	/**
	 * Identical to the parent class's `start_el()` (WP 6.4,
	 * wp-admin/includes/class-walker-category-checklist.php) except the
	 * rendered `<input>` is `type="radio"` instead of `type="checkbox"`.
	 * The field name is left as `tax_input[{$taxonomy}][]` — the trailing
	 * `[]` looks like it should still allow multiple values, but a group
	 * of radio inputs that all share one `name` only ever submits the one
	 * that's checked, so WordPress's own hierarchical tax_input handling
	 * in wp-admin/includes/post.php (which expects — and gets — a plain
	 * array of term IDs either way) needs no changes at all to end up
	 * saving exactly one term.
	 *
	 * @param string       $output   Passed by reference, appended to.
	 * @param \WP_Term     $category Term being rendered (core's own param name).
	 * @param int          $depth    Tree depth (unused directly, kept for signature match).
	 * @param array        $args     Walker args (expects 'taxonomy', 'selected_cats', 'disabled').
	 * @param int          $id       Unused, kept for signature match with the parent class.
	 * @return void
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		$taxonomy = empty( $args['taxonomy'] ) ? 'category' : $args['taxonomy'];

		$args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'>" .
			'<label class="selectit"><input value="' . (int) $category->term_id . '" type="radio" name="tax_input[' . esc_attr( $taxonomy ) . '][]" id="in-' . esc_attr( $taxonomy ) . '-' . (int) $category->term_id . '"' .
			checked( in_array( $category->term_id, $args['selected_cats'], true ), true, false ) .
			disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
			esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
	}
}
