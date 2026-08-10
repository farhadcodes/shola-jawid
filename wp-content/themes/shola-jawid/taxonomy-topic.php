<?php
/**
 * Template: taxonomy-topic.php — article archive for the `topic`
 * taxonomy (one template for all 6 terms). Converted from
 * 03_UI_Design/shola-jawid-ui/pages/body-topic-economy.html (and its 5
 * sibling files, structurally identical) — Phase 4.2.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term  = get_queried_object();
$paged = max( 1, get_query_var( 'paged' ) );
$sort  = sanitize_key( get_query_var( 'sort' ) );

$query_args = array(
	'post_type'      => 'post',
	'posts_per_page' => 6,
	'paged'          => $paged,
	'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'taxonomy' => 'topic',
			'field'    => 'term_id',
			'terms'    => $term->term_id,
		),
	),
);

if ( 'views' === $sort && class_exists( '\SholaCore\View_Counter' ) ) {
	// meta_value_num ordering only matches posts that already carry the
	// shcore_view_count meta key — View_Counter seeds every post with 0
	// on publish (and backfills pre-existing ones), so a never-viewed
	// post still sorts in, tied at the bottom via the date DESC tiebreak.
	$query_args['orderby']  = array(
		'meta_value_num' => 'DESC',
		'date'           => 'DESC',
	);
	$query_args['meta_key'] = \SholaCore\View_Counter::META_KEY; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
} else {
	$query_args['orderby'] = 'date';
	$query_args['order']   = 'DESC';
}

$archive_query = new WP_Query( $query_args );
?>
	<section class="wrap section-top">

		<header class="page-header page-header--narrow page-header--tight">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php echo esc_html( $term->name ); ?></h1>
			</div>
			<?php if ( $term->description ) : ?>
				<p class="dek"><?php echo esc_html( $term->description ); ?></p>
			<?php endif; ?>
		</header>

		<nav class="topic-nav" aria-label="<?php esc_attr_e( 'پیمایش موضوعات', 'shola-jawid' ); ?>">
			<?php foreach ( shola_get_topic_slugs_ordered() as $slug ) : ?>
				<?php
				$nav_term = get_term_by( 'slug', $slug, 'topic' );
				if ( ! $nav_term ) {
					continue;
				}
				?>
				<a<?php echo ( $nav_term->term_id === $term->term_id ) ? ' class="active"' : ''; ?> href="<?php echo esc_url( get_term_link( $nav_term ) ); ?>"><?php echo esc_html( $nav_term->name ); ?></a>
			<?php endforeach; ?>
		</nav>

		<div class="filter-tabs">
			<a class="<?php echo ( 'views' !== $sort ) ? 'active' : ''; ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( shola_get_label( 'topic_tab_latest' ) ); ?></a>
			<a class="<?php echo ( 'views' === $sort ) ? 'active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'sort', 'views', get_term_link( $term ) ) ); ?>"><?php echo esc_html( shola_get_label( 'topic_tab_most_read' ) ); ?></a>
		</div>

		<?php if ( $archive_query->have_posts() ) : ?>
			<div class="grid-cards">
				<?php
				while ( $archive_query->have_posts() ) :
					$archive_query->the_post();
					get_template_part( 'template-parts/cards/card', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<?php if ( $archive_query->max_num_pages > 1 ) : ?>
				<div class="pagination mt-lg" aria-label="<?php esc_attr_e( 'صفحه‌بندی', 'shola-jawid' ); ?>">
					<?php
					$links = paginate_links(
						array(
							'total'     => $archive_query->max_num_pages,
							'current'   => $paged,
							'type'      => 'array',
							'prev_text' => '→',
							'next_text' => '←',
							'add_args'  => $sort ? array( 'sort' => $sort ) : array(),
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
