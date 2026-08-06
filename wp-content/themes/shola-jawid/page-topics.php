<?php
/**
 * Template: page-topics.php — موضوعات (Topics listing).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-topics.html
 * (Phase 4.2). Applies to the Page with slug `topics`.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
	<section class="wrap section-top">

		<header class="page-header page-header--narrow">
			<p class="section-marker" lang="en">Sections</p>
			<h1 class="h-page"><?php esc_html_e( 'موضوعات', 'shola-jawid' ); ?></h1>
			<p class="dek"><?php esc_html_e( 'شش موضوع ثابت؛ چهارچوبی که نشریه پیرامون آن مقاله سفارش می‌دهد و مقاله می‌پذیرد.', 'shola-jawid' ); ?></p>
		</header>

		<ul class="topic-list">
			<?php foreach ( shola_get_topic_slugs_ordered() as $slug ) : ?>
				<?php
				$term = get_term_by( 'slug', $slug, 'topic' );
				if ( ! $term ) {
					continue;
				}
				?>
				<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
					<span class="name"><?php echo esc_html( $term->name ); ?></span>
					<span class="count"><?php echo esc_html( sprintf( _n( '%s مقاله', '%s مقاله', $term->count, 'shola-jawid' ), shola_to_persian_digits( $term->count ) ) ); ?></span></a></li>
			<?php endforeach; ?>
		</ul>

	</section>
<?php
get_footer();
