<?php
/**
 * Template: single-party_publication.php — single انتشارات حزب item view
 * (PDF preview + download). Applies to the `party_publication` CPT
 * (shola-core\Post_Types), added 2026-09-02 — see that class's docblock
 * for why this is a genuinely separate content type from both `issue`
 * (single-issue.php) and `document` (single-document.php), not a variant
 * of either.
 *
 * Modeled on single-document.php (same PDF-download-with-cover anatomy),
 * with the differences that content type's own registration implies:
 * no «نویسنده» row (no shcore_author_source field — these are the
 * party's own works, not cited from an external author), no «مجموعه»
 * row (no taxonomy for this type), and no «ویراستار» row (managing-
 * editor byline removed site-wide 2026-09-02 per the client's no-public-
 * author-display instruction — this template never had it in the first
 * place, rather than needing it removed).
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$pdf_id   = (int) get_post_meta( get_the_ID(), 'shcore_pdf_id', true );
	$pdf_url  = $pdf_id ? wp_get_attachment_url( $pdf_id ) : '';
	$pdf_size = shola_get_pdf_size( $pdf_id );

	$related_query = new WP_Query(
		array(
			'post_type'      => 'party_publication',
			'posts_per_page' => 3,
			'post__not_in'   => array( get_the_ID() ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	?>

	<section class="wrap">

		<nav class="article-crumb mt-lg" aria-label="<?php esc_attr_e( 'مسیر', 'shola-jawid' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a class="active" href="<?php echo esc_url( home_url( '/party-publications/' ) ); ?>"><?php esc_html_e( 'انتشارات حزب', 'shola-jawid' ); ?></a>
		</nav>

		<div class="issue-hero">

			<a
				<?php
				if ( $pdf_url ) :
					?>
					href="<?php echo esc_url( $pdf_url ); ?>" download
					<?php
else :
	?>
					href="#"<?php endif; ?>
				class="issue-cover"
				aria-label="<?php esc_attr_e( 'دریافت PDF', 'shola-jawid' ); ?>"
			>
				<?php echo shola_get_featured_image( get_post(), 'shola_issue_cover', array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
			</a>

			<div>
				<p class="badge-current"><?php esc_html_e( 'انتشارات حزب', 'shola-jawid' ); ?></p>
				<h1 class="article-title mt-sm"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="article-dek mt-sm"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34 ) ); ?></p>
				<?php endif; ?>

				<dl class="issue-meta">
					<dt><?php esc_html_e( 'تاریخ نشر', 'shola-jawid' ); ?></dt>
					<dd><time datetime="<?php echo esc_attr( shola_get_iso_datetime() ); ?>"><?php echo esc_html( get_the_date( '', get_the_ID() ) ); ?></time></dd>

					<?php if ( $pdf_size ) : ?>
						<dt><?php esc_html_e( 'حجم فایل', 'shola-jawid' ); ?></dt>
						<dd lang="en"><?php echo esc_html( $pdf_size ); ?> · PDF</dd>
					<?php endif; ?>
				</dl>

				<div class="row mt-sm">
					<?php if ( $pdf_url ) : ?>
						<a class="btn btn-primary" href="<?php echo esc_url( $pdf_url ); ?>" download><?php esc_html_e( 'دریافت PDF', 'shola-jawid' ); ?></a>
						<a class="btn btn-ghost" href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'پیش‌نمایش درون‌مرورگری', 'shola-jawid' ); ?></a>
					<?php else : ?>
						<a class="btn btn-primary" href="#"><?php esc_html_e( 'دریافت PDF', 'shola-jawid' ); ?></a>
						<a class="btn btn-ghost" href="#"><?php esc_html_e( 'پیش‌نمایش درون‌مرورگری', 'shola-jawid' ); ?></a>
					<?php endif; ?>
				</div>
			</div>

		</div>

		<?php if ( get_the_content() ) : ?>
			<div class="wrap-read doc-about">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section"><?php esc_html_e( 'دربارهٔ این اثر', 'shola-jawid' ); ?></h2>
				</div>
				<div class="prose">
					<?php the_content(); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $related_query->have_posts() ) : ?>
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h2 class="h-section doc-related-head"><?php esc_html_e( 'سایر آثار حزب', 'shola-jawid' ); ?></h2>
			</div>
			<div class="issue-grid">
				<?php
				while ( $related_query->have_posts() ) :
					$related_query->the_post();
					get_template_part( 'template-parts/cards/issue-card', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>

	</section>
	<?php
endwhile;

get_footer();
