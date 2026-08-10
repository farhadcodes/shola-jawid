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
	$post_tags   = get_the_tags();
	$post_tags   = ( $post_tags && ! is_wp_error( $post_tags ) ) ? $post_tags : array();
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
						<a href="<?php echo esc_url( home_url( '/topics/' ) ); ?>"><?php echo esc_html( shola_get_label( 'breadcrumb_topics_label' ) ); ?></a>
						<?php if ( $topic ) : ?>
							<span aria-hidden="true"> / </span>
							<a class="active" href="<?php echo esc_url( get_term_link( $topic ) ); ?>"><?php echo esc_html( $topic->name ); ?></a>
						<?php endif; ?>
					</nav>

					<h1 class="article-title"><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?>
						<p class="article-dek"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34 ) ); ?></p>
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
				<?php if ( $post_tags ) : ?>
					<div>
						<p class="meta-mono" lang="en">TAGS</p>
						<ul class="tag-list">
							<?php foreach ( $post_tags as $post_tag ) : ?>
								<li><a class="tag-outline" href="<?php echo esc_url( get_term_link( $post_tag ) ); ?>"><?php echo esc_html( $post_tag->name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<div class="row">
					<?php
					$share_id    = 'share-menu-' . get_the_ID();
					$share_url   = get_permalink();
					$share_title = get_the_title();
					$share_links = array(
						'facebook' => array(
							'label' => __( 'فیس‌بوک', 'shola-jawid' ),
							'href'  => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $share_url ),
							'icon'  => '<path d="M22 12a10 10 0 1 0-11.5 9.95v-7.04H7.9V12h2.6V9.8c0-2.6 1.55-4 3.9-4 1.1 0 2.3.2 2.3.2v2.5h-1.3c-1.3 0-1.7.8-1.7 1.6V12h2.9l-.5 2.9h-2.4v7.04A10 10 0 0 0 22 12Z"/>',
						),
						'telegram' => array(
							'label' => __( 'تلگرام', 'shola-jawid' ),
							'href'  => 'https://t.me/share/url?url=' . rawurlencode( $share_url ) . '&text=' . rawurlencode( $share_title ),
							'icon'  => '<path d="M21.9 4.6 18.8 19c-.2 1-.8 1.2-1.7.8l-4.6-3.4-2.2 2.1c-.3.3-.5.5-.9.5l.3-4.6L18 6.9c.4-.3-.1-.5-.5-.2L7 13.2l-4.4-1.4c-1-.3-1-1 .2-1.4L20.6 3.2c.8-.3 1.5.2 1.3 1.4Z"/>',
						),
						'whatsapp' => array(
							'label' => __( 'واتساپ', 'shola-jawid' ),
							'href'  => 'https://api.whatsapp.com/send?text=' . rawurlencode( $share_title . ' ' . $share_url ),
							'icon'  => '<path d="M12 2a10 10 0 0 0-8.5 15.2L2 22l4.9-1.3A10 10 0 1 0 12 2Zm5.8 14.2c-.2.6-1.3 1.2-1.8 1.3-.5.1-1 .1-1.7-.1-.4-.1-.9-.3-1.6-.6-2.8-1.2-4.6-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 1.9c.1.2.1.3 0 .5l-.3.5-.4.4c-.1.1-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.4 2.4 1.5.3.1.4.1.6-.1l.7-.8c.2-.3.4-.2.7-.1l1.7.8c.2.1.4.2.5.3.1.2.1.9-.1 1.5Z"/>',
						),
						'x'        => array(
							'label' => __( 'ایکس', 'shola-jawid' ),
							'href'  => 'https://twitter.com/intent/tweet?url=' . rawurlencode( $share_url ) . '&text=' . rawurlencode( $share_title ),
							'icon'  => '<path d="M17.8 3h3l-6.7 7.7L22 21h-6.2l-4.8-6.3L5.4 21h-3l7.2-8.2L2 3h6.3l4.4 5.8L17.8 3Zm-1.1 16.2h1.7L7.3 4.7H5.5l11.2 14.5Z"/>',
						),
					);
					?>
					<div class="share-menu">
						<button type="button" class="link-more share-trigger" aria-haspopup="true" aria-expanded="false" aria-controls="<?php echo esc_attr( $share_id ); ?>">
							<?php esc_html_e( 'اشتراک', 'shola-jawid' ); ?> <span class="arr">↗</span>
						</button>
						<div class="share-dropdown" id="<?php echo esc_attr( $share_id ); ?>">
							<?php foreach ( $share_links as $key => $link ) : ?>
								<a
									class="share-option"
									href="<?php echo esc_url( $link['href'] ); ?>"
									target="_blank"
									rel="noopener"
									aria-label="<?php echo esc_attr( sprintf( /* translators: %s: platform name (Facebook, Telegram, etc.). */ __( 'اشتراک‌گذاری در %s', 'shola-jawid' ), $link['label'] ) ); ?>"
								>
									<svg class="glyph" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><?php echo $link['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, trusted inline SVG defined above, not user input. ?></svg>
									<span><?php echo esc_html( $link['label'] ); ?></span>
								</a>
							<?php endforeach; ?>
							<button type="button" class="share-option share-copy" data-url="<?php echo esc_url( $share_url ); ?>" data-copied-label="<?php esc_attr_e( 'لینک کپی شد', 'shola-jawid' ); ?>">
								<svg class="glyph" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.9 3.5a5 5 0 0 1 7.07 7.07l-2.5 2.5a5 5 0 0 1-7.07 0 1 1 0 1 1 1.41-1.41 3 3 0 0 0 4.25 0l2.5-2.5a3 3 0 0 0-4.25-4.25l-1.2 1.2a1 1 0 1 1-1.41-1.42l1.2-1.19Zm-1.8 17a5 5 0 0 1-7.07-7.07l2.5-2.5a5 5 0 0 1 7.07 0 1 1 0 1 1-1.41 1.41 3 3 0 0 0-4.25 0l-2.5 2.5a3 3 0 0 0 4.25 4.25l1.2-1.2a1 1 0 1 1 1.41 1.42l-1.2 1.19Z"/></svg>
								<span class="share-copy-label"><?php esc_html_e( 'کپی لینک', 'shola-jawid' ); ?></span>
							</button>
						</div>
					</div>
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

				<div class="section-head kicker-row">
					<p class="section-marker"></p>
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
