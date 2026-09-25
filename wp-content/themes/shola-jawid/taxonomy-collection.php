<?php
/**
 * Template: taxonomy-collection.php — document archive for the
 * `collection` taxonomy (one template for all 4 terms). Converted from
 * 03_UI_Design/shola-jawid-ui/pages/body-library-classics.html (and its
 * 3 structurally identical siblings) — Phase 4.2.
 *
 * Cover-thumbnail grid added 2026-09-25, per Farhad relaying the
 * client's explicit ask against a live screenshot: the plain text-row
 * list (document-row.php — title + a small download button, no cover)
 * "does not have their thumbnail of the cover page" and "should not
 * look like a list." Switched to the exact `.issue-grid` +
 * issue-card.php pairing already used for the same "paginated grid of
 * PDF covers" job on page-party-publications.php — issue-card.php was
 * already generalized to work with the `document` post type (its own
 * docblock says so), so no new card template was needed. `posts_per_page`
 * raised 6 -> 20 to match the client's "paginate past ~20" ask.
 *
 * Column count: briefly given its own `.issue-grid--library` 3-column
 * override the same day, then reverted a round later per Farhad's
 * follow-up design-head assessment against a live screenshot — 3
 * columns made each cover far too large for comfortable archive
 * browsing at desktop width. Plain `.issue-grid` (no modifier) is used
 * instead, matching انتشارات حزب/اسناد حزب's own paginated grids and the
 * homepage teasers exactly: 3 columns on mobile, fluid auto-fit on
 * tablet, a flat 6-column row on desktop.
 *
 * `.article-crumb` breadcrumb added 2026-09-25, per Farhad relaying the
 * client's explicit ask for one "on all levels that require it" across
 * کتابخانه — this page had none. Three levels (Home / کتابخانه /
 * {collection}), the same pattern taxonomy-party_document_category.php
 * and single-document.php's own (already-correct) breadcrumb both use.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term  = get_queried_object();
$paged = max( 1, get_query_var( 'paged' ) );

$collection_slugs = array( 'classics', 'international-movement', 'party-documents', 'critique-polemic' );

$archive_query = new WP_Query(
	array(
		'post_type'      => 'document',
		'posts_per_page' => 20,
		'paged'          => $paged,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'collection',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
		),
	)
);
?>
	<section class="wrap section-top">

		<nav class="article-crumb mt-lg" aria-label="<?php esc_attr_e( 'مسیر', 'shola-jawid' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a href="<?php echo esc_url( home_url( '/library/' ) ); ?>"><?php esc_html_e( 'کتابخانه', 'shola-jawid' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a class="active" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
		</nav>

		<header class="page-header page-header--narrow page-header--tight">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php echo esc_html( $term->name ); ?></h1>
			</div>
			<?php if ( $term->description ) : ?>
				<p class="dek"><?php echo esc_html( $term->description ); ?></p>
			<?php endif; ?>
		</header>

		<nav class="topic-nav" aria-label="<?php esc_attr_e( 'پیمایش مجموعه‌ها', 'shola-jawid' ); ?>">
			<?php foreach ( $collection_slugs as $slug ) : ?>
				<?php
				$nav_term = get_term_by( 'slug', $slug, 'collection' );
				if ( ! $nav_term ) {
					continue;
				}
				?>
				<a<?php echo ( $nav_term->term_id === $term->term_id ) ? ' class="active"' : ''; ?> href="<?php echo esc_url( get_term_link( $nav_term ) ); ?>"><?php echo esc_html( $nav_term->name ); ?></a>
			<?php endforeach; ?>
		</nav>

		<?php if ( $archive_query->have_posts() ) : ?>
			<div class="issue-grid">
				<?php
				while ( $archive_query->have_posts() ) :
					$archive_query->the_post();
					get_template_part( 'template-parts/cards/issue-card', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<?php if ( $archive_query->max_num_pages > 1 ) : ?>
				<div class="pagination" aria-label="<?php esc_attr_e( 'صفحه‌بندی', 'shola-jawid' ); ?>">
					<?php
					$links = paginate_links(
						array(
							'total'     => $archive_query->max_num_pages,
							'current'   => $paged,
							'type'      => 'array',
							'prev_text' => '→',
							'next_text' => '←',
						)
					);
					if ( $links ) {
						foreach ( $links as $link ) {
							$link = shola_persian_digits_pagination_link( $link );
							$link = str_replace( 'page-numbers', 'page-num', $link );
							echo wp_kses_post( $link );
						}
					}
					?>
				</div>
			<?php endif; ?>
		<?php endif; ?>

	</section>
<?php
get_footer();
