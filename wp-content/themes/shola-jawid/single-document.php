<?php
/**
 * Template: single-document.php — single library document view (PDF
 * preview + download). Converted from
 * 03_UI_Design/shola-jawid-ui/pages/body-document-single.html (Phase
 * 4.2).
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$col_terms  = get_the_terms( get_the_ID(), 'collection' );
	$collection = ( $col_terms && ! is_wp_error( $col_terms ) ) ? reset( $col_terms ) : false;
	$author     = get_post_meta( get_the_ID(), 'shcore_author_source', true );

	$pdf_id   = (int) get_post_meta( get_the_ID(), 'shcore_pdf_id', true );
	$pdf_url  = $pdf_id ? wp_get_attachment_url( $pdf_id ) : '';
	$pdf_size = '';
	if ( $pdf_id ) {
		$pdf_file = get_attached_file( $pdf_id );
		$pdf_size = $pdf_file && file_exists( $pdf_file ) ? size_format( filesize( $pdf_file ) ) : '';
	}

	$related_query = false;
	if ( $collection ) {
		$related_query = new WP_Query(
			array(
				'post_type'      => 'document',
				'posts_per_page' => 3,
				'post__not_in'   => array( get_the_ID() ),
				'orderby'        => 'date',
				'order'          => 'DESC',
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small, fixed-vocabulary taxonomy, not a scale concern.
					array(
						'taxonomy' => 'collection',
						'field'    => 'term_id',
						'terms'    => $collection->term_id,
					),
				),
			)
		);
	}
	?>

	<section class="wrap">

		<nav class="article-crumb mt-lg" aria-label="<?php esc_attr_e( 'مسیر', 'shola-jawid' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a href="<?php echo esc_url( home_url( '/library/' ) ); ?>"><?php esc_html_e( 'کتابخانه', 'shola-jawid' ); ?></a>
			<?php if ( $collection ) : ?>
				<span aria-hidden="true"> / </span>
				<a class="active" href="<?php echo esc_url( get_term_link( $collection ) ); ?>"><?php echo esc_html( $collection->name ); ?></a>
			<?php endif; ?>
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
				<p class="badge-current"><?php esc_html_e( 'در کتابخانه', 'shola-jawid' ); ?></p>
				<h1 class="article-title mt-sm"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="article-dek mt-sm"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<dl class="issue-meta">
					<?php if ( $author ) : ?>
						<dt><?php esc_html_e( 'نویسنده', 'shola-jawid' ); ?></dt>
						<dd><?php echo esc_html( $author ); ?></dd>
					<?php endif; ?>

					<?php if ( $collection ) : ?>
						<dt><?php esc_html_e( 'مجموعه', 'shola-jawid' ); ?></dt>
						<dd><?php echo esc_html( $collection->name ); ?></dd>
					<?php endif; ?>

					<?php if ( $pdf_size ) : ?>
						<dt><?php esc_html_e( 'حجم فایل', 'shola-jawid' ); ?></dt>
						<dd lang="en"><?php echo esc_html( $pdf_size ); ?> · PDF</dd>
					<?php endif; ?>

					<dt><?php esc_html_e( 'ویراستار', 'shola-jawid' ); ?></dt>
					<dd><?php echo esc_html( shola_get_managing_editor() ); ?></dd>

					<dt><?php esc_html_e( 'تاریخ نشر', 'shola-jawid' ); ?></dt>
					<dd><time datetime="<?php echo esc_attr( shola_get_iso_datetime() ); ?>"><?php echo esc_html( shola_get_english_month_abbr() . ' ' . shola_to_persian_digits( shola_get_gregorian_year() ) ); ?></time></dd>
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
				<p class="section-marker" lang="en">About the Text</p>
				<h2 class="h-section"><?php esc_html_e( 'دربارهٔ این متن', 'shola-jawid' ); ?></h2>
				<div class="prose">
					<?php the_content(); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $related_query && $related_query->have_posts() ) : ?>
			<p class="section-marker" lang="en">Related</p>
			<h2 class="h-section doc-related-head"><?php esc_html_e( 'اسناد مرتبط', 'shola-jawid' ); ?></h2>
			<ul class="doc-related-list">
				<?php
				while ( $related_query->have_posts() ) :
					$related_query->the_post();
					get_template_part( 'template-parts/rows/document-row', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</ul>
		<?php endif; ?>

	</section>
	<?php
endwhile;

get_footer();
