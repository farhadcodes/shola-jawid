<?php
/**
 * Template: single-party_document.php — single اسناد حزب item view (PDF
 * preview + download). Applies to the `party_document` CPT
 * (shola-core\Post_Types), added 2026-09-04 per Farhad relaying a client
 * correction: اسناد حزب is its own independent section, not a shelf
 * inside کتابخانه (see that class's docblock for the full history).
 *
 * Modeled directly on single-party_publication.php (same PDF-download-
 * with-cover anatomy), with two additions that content type doesn't have:
 * a «شمارهٔ سریال» row (shcore_serial_number) and a «دسته» row (the
 * optional, self-managed party_document_category taxonomy — shown only
 * when at least one category has actually been assigned, since the
 * client asked for this to be optional, not a required classification).
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

	$serial_number = get_post_meta( get_the_ID(), 'shcore_serial_number', true );
	$categories    = get_the_terms( get_the_ID(), 'party_document_category' );
	$categories    = ( $categories && ! is_wp_error( $categories ) ) ? $categories : array();

	$related_query = new WP_Query(
		array(
			'post_type'      => 'party_document',
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
			<a class="active" href="<?php echo esc_url( home_url( '/party-documents/' ) ); ?>"><?php esc_html_e( 'اسناد حزب', 'shola-jawid' ); ?></a>
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
				<p class="badge-current"><?php esc_html_e( 'اسناد حزب', 'shola-jawid' ); ?></p>
				<h1 class="article-title mt-sm"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="article-dek mt-sm"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34 ) ); ?></p>
				<?php endif; ?>

				<dl class="issue-meta">
					<dt><?php esc_html_e( 'تاریخ نشر', 'shola-jawid' ); ?></dt>
					<dd><time datetime="<?php echo esc_attr( shola_get_iso_datetime() ); ?>"><?php echo esc_html( get_the_date( '', get_the_ID() ) ); ?></time></dd>

					<?php if ( $serial_number ) : ?>
						<dt><?php esc_html_e( 'شمارهٔ سریال', 'shola-jawid' ); ?></dt>
						<dd><?php echo esc_html( $serial_number ); ?></dd>
					<?php endif; ?>

					<?php if ( $categories ) : ?>
						<dt><?php esc_html_e( 'دسته', 'shola-jawid' ); ?></dt>
						<dd>
							<?php
							echo esc_html(
								implode(
									'، ',
									wp_list_pluck( $categories, 'name' )
								)
							);
							?>
						</dd>
					<?php endif; ?>

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
					<h2 class="h-section"><?php esc_html_e( 'جزئیات سند', 'shola-jawid' ); ?></h2>
				</div>
				<div class="prose">
					<?php the_content(); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $related_query->have_posts() ) : ?>
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h2 class="h-section doc-related-head"><?php esc_html_e( 'سایر اسناد حزب', 'shola-jawid' ); ?></h2>
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
