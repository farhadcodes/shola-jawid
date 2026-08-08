<?php
/**
 * Template: single-issue.php — single issue view (PDF preview + download).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-issue-single.html
 * (Phase 4.2). Issues are PDF-only per EXECUTION_PLAN.md's Phase 0.3
 * resolved assumption — no separate web-article content, and the table
 * of contents below is descriptive text, not a set of linked WP posts.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$pub_terms  = get_the_terms( get_the_ID(), 'publication' );
	$pub        = ( $pub_terms && ! is_wp_error( $pub_terms ) ) ? reset( $pub_terms ) : false;
	$is_current = $pub && 'shola-jawid' === $pub->slug;
	$number     = get_post_meta( get_the_ID(), 'shcore_issue_number', true );

	$pdf_id   = (int) get_post_meta( get_the_ID(), 'shcore_pdf_id', true );
	$pdf_url  = $pdf_id ? wp_get_attachment_url( $pdf_id ) : '';
	$pdf_size = '';
	if ( $pdf_id ) {
		$pdf_file = get_attached_file( $pdf_id );
		$pdf_size = $pdf_file && file_exists( $pdf_file ) ? size_format( filesize( $pdf_file ) ) : '';
	}

	// Guarded per CLAUDE.md §2: the theme must not fatal-error if
	// shola-core is inactive — degrade to an empty TOC instead.
	$toc = class_exists( '\SholaCore\Meta_Fields' ) ? \SholaCore\Meta_Fields::get_issue_contents( get_the_ID() ) : array();

	$article_count     = 0;
	$translation_count = 0;
	foreach ( $toc as $entry ) {
		if ( 'TRANSLATION' === strtoupper( $entry['section'] ) ) {
			++$translation_count;
		} else {
			++$article_count;
		}
	}
	$contents_summary = '';
	if ( $article_count || $translation_count ) {
		$parts = array();
		if ( $article_count ) {
			$parts[] = sprintf(
				/* translators: %s: article count. */
				_n( '%s مقاله', '%s مقاله', $article_count, 'shola-jawid' ),
				shola_to_persian_digits( $article_count )
			);
		}
		if ( $translation_count ) {
			$parts[] = sprintf(
				/* translators: %s: translation count. */
				_n( '%s ترجمه', '%s ترجمه', $translation_count, 'shola-jawid' ),
				shola_to_persian_digits( $translation_count )
			);
		}
		$contents_summary = implode( ' + ', $parts );
	}
	?>

	<section class="wrap">

		<nav class="article-crumb mt-lg" aria-label="<?php esc_attr_e( 'مسیر', 'shola-jawid' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a href="<?php echo esc_url( home_url( '/publications/' ) ); ?>"><?php esc_html_e( 'نشرات', 'shola-jawid' ); ?></a>
			<?php if ( $pub ) : ?>
				<span aria-hidden="true"> / </span>
				<a class="active" href="<?php echo esc_url( get_term_link( $pub ) ); ?>"><?php echo esc_html( $pub->name ); ?></a>
			<?php endif; ?>
		</nav>

		<div class="issue-hero">

			<a
				<?php echo $pdf_url ? 'href="' . esc_url( $pdf_url ) . '" download' : 'href="#"'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url() already applied; the surrounding markup is a static string, not user input. ?>
				class="issue-cover"
				aria-label="<?php echo esc_attr( $number ? sprintf( /* translators: %s: issue number. */ __( 'دریافت PDF شمارهٔ %s', 'shola-jawid' ), shola_to_persian_digits( $number ) ) : __( 'دریافت PDF', 'shola-jawid' ) ); ?>"
			>
				<?php echo shola_get_featured_image( get_post(), 'shola_issue_cover', array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
			</a>

			<div>
				<p class="<?php echo $is_current ? 'badge-current' : 'badge-archive'; ?>">
					<?php
					echo esc_html(
						$number
							? sprintf( /* translators: 1: issue number, 2: current/archived status label. */ __( 'شمارهٔ %1$s · %2$s', 'shola-jawid' ), shola_to_persian_digits( $number ), shola_publication_status_label( $pub ? $pub->slug : '' ) )
							: shola_publication_status_label( $pub ? $pub->slug : '' )
					);
					?>
				</p>
				<h1 class="article-title mt-sm">
					<?php
					echo esc_html(
						$number && $pub
							? sprintf( '%1$s · %2$s', $pub->name, sprintf( /* translators: %s: issue number. */ __( 'شمارهٔ %s', 'shola-jawid' ), shola_to_persian_digits( $number ) ) )
							: get_the_title()
					);
					?>
				</h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="article-dek mt-sm"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<dl class="issue-meta">
					<dt><?php esc_html_e( 'تاریخ نشر', 'shola-jawid' ); ?></dt>
					<dd><time datetime="<?php echo esc_attr( shola_get_iso_datetime() ); ?>"><?php echo esc_html( shola_get_english_month_abbr() . ' ' . shola_to_persian_digits( shola_get_gregorian_year() ) ); ?></time></dd>

					<?php if ( $contents_summary ) : ?>
						<dt><?php esc_html_e( 'شمار مطالب', 'shola-jawid' ); ?></dt>
						<dd><?php echo esc_html( $contents_summary ); ?></dd>
					<?php endif; ?>

					<?php if ( $pdf_size ) : ?>
						<dt><?php esc_html_e( 'حجم فایل', 'shola-jawid' ); ?></dt>
						<dd lang="en"><?php echo esc_html( $pdf_size ); ?> · PDF</dd>
					<?php endif; ?>

					<dt><?php esc_html_e( 'سردبیر مسئول', 'shola-jawid' ); ?></dt>
					<dd><?php echo esc_html( shola_get_managing_editor() ); ?></dd>
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

		<?php if ( $toc ) : ?>
			<div class="issue-toc-head">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section mt-sm mb-lg"><?php esc_html_e( 'فهرست مطالب', 'shola-jawid' ); ?></h2>
				</div>

				<ul class="issue-toc">
					<?php foreach ( $toc as $i => $entry ) : ?>
						<li>
							<?php if ( $entry['section'] ) : ?>
								<p class="meta-mono" lang="en"><?php echo esc_html( shola_to_persian_digits( sprintf( '%02d', $i + 1 ) ) . ' · SECTION · ' . strtoupper( $entry['section'] ) ); ?></p>
							<?php endif; ?>
							<span class="link-quiet"><?php echo esc_html( $entry['title'] ); ?></span>
							<?php if ( $entry['byline'] ) : ?>
								<p class="meta mt-sm"><?php echo esc_html( $entry['byline'] ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

	</section>
	<?php
endwhile;

get_footer();
