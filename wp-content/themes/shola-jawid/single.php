<?php
/**
 * Template: single.php — single article/note view.
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-article-single.html
 * (Phase 4.2). Applies to the native `post` type (articles/notes) — issue
 * and document have their own single-issue.php/single-document.php.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$terms       = get_the_terms( get_the_ID(), 'topic' );
	$terms       = ( $terms && ! is_wp_error( $terms ) ) ? $terms : array();
	$topic       = $terms ? reset( $terms ) : false;
	$byline_meta = get_post_meta( get_the_ID(), 'shcore_byline', true );
	$byline      = $byline_meta ? $byline_meta : get_the_author_meta( 'display_name' );
	$author_note = get_post_meta( get_the_ID(), 'shcore_author_note', true );
	$stats       = shola_get_reading_stats();
	$thumb_id    = get_post_thumbnail_id();
	$caption     = $thumb_id ? wp_get_attachment_caption( $thumb_id ) : '';
	?>

	<div class="progress-track" aria-hidden="true"><div class="progress-bar"></div></div>

	<article data-progress-scope>

		<div class="article-hero">
			<div class="article-hero-visual">
				<div class="article-hero-media">
					<?php echo shola_get_featured_image( get_post(), 'shola_article_hero', array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
				</div>

				<header class="wrap article-header">
					<nav class="article-crumb" aria-label="<?php esc_attr_e( 'مسیر', 'shola-jawid' ); ?>">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a>
						<span aria-hidden="true"> / </span>
						<a href="<?php echo esc_url( home_url( '/topics/' ) ); ?>"><?php esc_html_e( 'موضوعات', 'shola-jawid' ); ?></a>
						<?php if ( $topic ) : ?>
							<span aria-hidden="true"> / </span>
							<a class="active" href="<?php echo esc_url( get_term_link( $topic ) ); ?>"><?php echo esc_html( $topic->name ); ?></a>
						<?php endif; ?>
					</nav>

					<h1 class="article-title"><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?>
						<p class="article-dek"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
				</header>
			</div>

			<?php if ( $caption ) : ?>
				<p class="article-hero-caption"><?php echo esc_html( $caption ); ?></p>
			<?php endif; ?>
		</div>

		<div class="wrap article-body">

			<aside class="article-sidebar" aria-label="<?php esc_attr_e( 'اطلاعات مقاله', 'shola-jawid' ); ?>">
				<p class="author">
					<?php esc_html_e( 'نویسنده:', 'shola-jawid' ); ?> <strong><a href="#"><?php echo esc_html( $byline ); ?></a></strong>
					<?php if ( $author_note ) : ?>
						<?php echo esc_html( $author_note ); ?>
					<?php endif; ?>
				</p>
				<p class="editor">
					<?php
					printf(
						/* translators: %s: managing editor link. */
						esc_html__( 'سردبیر مسئول: %s', 'shola-jawid' ),
						'<a href="#">' . esc_html( shola_get_managing_editor() ) . '</a>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inline above.
					);
					?>
				</p>
				<p class="word-count">
					<?php
					printf(
						/* translators: 1: word count, 2: reading time in minutes. */
						esc_html__( '%1$s واژه · %2$s دقیقه خواندن', 'shola-jawid' ),
						esc_html( shola_to_persian_digits( $stats['words'] ) ),
						esc_html( shola_to_persian_digits( $stats['minutes'] ) )
					);
					?>
				</p>
				<?php if ( $terms ) : ?>
					<ul class="tag-list">
						<?php foreach ( $terms as $term ) : ?>
							<li><a class="tag-outline" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</aside>

			<div class="prose">
				<?php the_content(); ?>
			</div>

		</div>

		<div class="wrap">
			<div class="article-footer">
				<?php if ( $terms ) : ?>
					<div>
						<p class="meta-mono" lang="en">TAGS</p>
						<ul class="tag-list">
							<?php foreach ( $terms as $term ) : ?>
								<li><a class="tag-outline" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<div class="row">
					<a class="link-more" href="#" aria-label="<?php esc_attr_e( 'ذخیره برای بعد', 'shola-jawid' ); ?>"><?php esc_html_e( 'ذخیره', 'shola-jawid' ); ?> <span class="arr">↓</span></a>
					<a class="link-more" href="#" aria-label="<?php esc_attr_e( 'اشتراک‌گذاری', 'shola-jawid' ); ?>"><?php esc_html_e( 'اشتراک', 'shola-jawid' ); ?> <span class="arr">↗</span></a>
				</div>
			</div>
		</div>

		<?php
		$related_query = false;
		if ( $topic ) {
			$related_query = new WP_Query(
				array(
					'post_type'      => 'post',
					'posts_per_page' => 3,
					'post__not_in'   => array( get_the_ID() ),
					'orderby'        => 'date',
					'order'          => 'DESC',
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small, fixed-vocabulary taxonomy, not a scale concern.
						array(
							'taxonomy' => 'topic',
							'field'    => 'term_id',
							'terms'    => $topic->term_id,
						),
					),
				)
			);
		}
		?>
		<?php if ( $related_query && $related_query->have_posts() ) : ?>
			<section class="wrap related-rail" aria-label="<?php esc_attr_e( 'ادامهٔ خواندن', 'shola-jawid' ); ?>">

				<div class="section-head">
					<p class="section-marker" lang="en">Related Essays</p>
					<h2 class="h-section"><?php esc_html_e( 'ادامهٔ خواندن', 'shola-jawid' ); ?></h2>
				</div>

				<div class="grid-cards">
					<?php
					while ( $related_query->have_posts() ) :
						$related_query->the_post();
						get_template_part( 'template-parts/cards/card', null, array( 'post' => get_post() ) );
					endwhile;
					wp_reset_postdata();
					?>
				</div>

			</section>
		<?php endif; ?>

	</article>

	<?php
endwhile;

get_footer();
