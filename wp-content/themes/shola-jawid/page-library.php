<?php
/**
 * Template: page-library.php — کتابخانه (Library listing).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-library.html
 * (Phase 4.2). Applies to the Page with slug `library`.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$collection_slugs = array( 'classics', 'international-movement', 'party-documents', 'critique-polemic' );

$documents_query = new WP_Query(
	array(
		'post_type'      => 'document',
		'posts_per_page' => 5,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
	<section class="wrap section-top">

		<header class="page-header">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'کتابخانه', 'shola-jawid' ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'چهار مجموعه از اسناد PDF: متن‌های پایهٔ نظری، بیانیه‌های جنبش‌های جهانی، اسناد رسمی حزب، و پلمیک‌ها. پیش‌نمایش درون‌مرورگری و دریافت آزاد.', 'shola-jawid' ); ?></p>
		</header>

		<ul class="topic-list">
			<?php foreach ( $collection_slugs as $slug ) : ?>
				<?php
				$term = get_term_by( 'slug', $slug, 'collection' );
				if ( ! $term ) {
					continue;
				}
				?>
				<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
					<span class="name"><?php echo esc_html( $term->name ); ?></span>
					<span class="count"><?php echo esc_html( sprintf( /* translators: %s: document count. */ _n( '%s سند', '%s سند', $term->count, 'shola-jawid' ), shola_to_persian_digits( $term->count ) ) ); ?></span></a></li>
			<?php endforeach; ?>
		</ul>

	</section>

	<?php if ( $documents_query->have_posts() ) : ?>
		<section class="wrap sect" aria-label="<?php esc_attr_e( 'تازه‌ترین اسناد', 'shola-jawid' ); ?>">
			<div class="section-head row-between">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section"><?php esc_html_e( 'تازه‌ترین اسناد', 'shola-jawid' ); ?></h2>
				</div>
				<?php
				// v6's own text/target here differs from front-page.php's equivalent
				// link ("همهٔ مجموعه‌ها" -> /library/) -- body-library.html specifically
				// says "همهٔ آثار کلاسیک" linking to the classics collection, not a
				// generic "all collections" link (which would be redundant on this
				// page, since the collections list is already right above). Matched
				// exactly rather than assumed to be the same component.
				$classics_term = get_term_by( 'slug', 'classics', 'collection' );
				?>
				<?php if ( $classics_term ) : ?>
					<a class="link-more" href="<?php echo esc_url( get_term_link( $classics_term ) ); ?>"><?php esc_html_e( 'همهٔ آثار کلاسیک', 'shola-jawid' ); ?> <span class="arr">←</span></a>
				<?php endif; ?>
			</div>

			<ul>
				<?php
				while ( $documents_query->have_posts() ) :
					$documents_query->the_post();
					get_template_part( 'template-parts/rows/document-row', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</ul>
		</section>
	<?php endif; ?>
<?php
get_footer();
