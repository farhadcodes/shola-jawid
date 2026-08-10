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
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php echo esc_html( shola_get_label( 'topics_page_title' ) ); ?></h1>
			</div>
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
					<span class="count"><?php echo esc_html( sprintf( /* translators: %s: article count. */ _n( '%s مقاله', '%s مقاله', $term->count, 'shola-jawid' ), shola_to_persian_digits( $term->count ) ) ); ?></span></a></li>
			<?php endforeach; ?>
		</ul>

	</section>
<?php
get_footer();
