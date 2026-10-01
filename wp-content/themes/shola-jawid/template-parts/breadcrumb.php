<?php
/**
 * Template part: template-parts/breadcrumb.php — shared "مسیر" breadcrumb
 * nav, added 2026-10-01 (spec-audit gap B11). Every existing breadcrumb
 * on the site (single.php, single-issue.php, taxonomy-publication.php,
 * etc.) already hand-rolls the exact same `.article-crumb` markup —
 * this part exists so the archive pages that were missing one entirely
 * don't add a 12th hand-copied version of it. Pre-existing breadcrumbs
 * are left as-is for now (not migrated to this part), to keep this gap
 * fix low-risk rather than touching already-shipped, working templates.
 *
 * "صفحهٔ اصلی" (Home) is always prepended automatically — pass only the
 * crumbs after it. The last item gets the "active" class, same
 * convention as every hand-written breadcrumb already on the site; an
 * item whose `url` is empty renders as inert text instead of a link
 * (for a page like search.php with no single fixed canonical URL of its
 * own to self-link to).
 *
 * @param array $args {
 *     @type array[] $items Ordered list, each array( 'label' => string,
 *                           'url' => string|'' ).
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items      = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();
$last_index = count( $items ) - 1;
?>
<nav class="article-crumb mt-lg" aria-label="<?php esc_attr_e( 'مسیر', 'shola-jawid' ); ?>">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a>
	<?php foreach ( $items as $index => $item ) : ?>
		<span aria-hidden="true"> / </span>
		<?php if ( ! empty( $item['url'] ) ) : ?>
			<a<?php echo ( $index === $last_index ) ? ' class="active"' : ''; ?> href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
		<?php else : ?>
			<span class="active"><?php echo esc_html( $item['label'] ); ?></span>
		<?php endif; ?>
	<?php endforeach; ?>
</nav>
