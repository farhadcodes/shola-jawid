<?php
/**
 * Template: taxonomy-collection.php — document archive for the
 * `collection` taxonomy (one template for all 4 terms). Converted from
 * 03_UI_Design/shola-jawid-ui/pages/body-library-classics.html (and its
 * 3 structurally identical siblings) — Phase 4.2.
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
		'posts_per_page' => 6,
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

		<header class="page-header page-header--narrow page-header--tight">
			<p class="section-marker" lang="en">Library Collection</p>
			<h1 class="h-page"><?php echo esc_html( $term->name ); ?></h1>
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
			<ul>
				<?php
				while ( $archive_query->have_posts() ) :
					$archive_query->the_post();
					get_template_part( 'template-parts/rows/document-row', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</ul>

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
							$link = shola_to_persian_digits( $link );
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
