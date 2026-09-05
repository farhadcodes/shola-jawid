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

	$pub_terms   = get_the_terms( get_the_ID(), 'publication' );
	$pub         = ( $pub_terms && ! is_wp_error( $pub_terms ) ) ? reset( $pub_terms ) : false;
	// 2026-09-03: since the دوره migration, $pub is a دوره (period) child
	// term, not the top-level publication — Farhad flagged the breadcrumb
	// was missing that grandparent level (صفحهٔ اصلی / نشرات / دورهٔ اول
	// instead of .../ شعله جاوید / دورهٔ اول). Resolved the same way
	// taxonomy-publication.php's leaf view already does: walk up to
	// $pub->parent for the grandparent crumb, and use *that* term's slug
	// (not $pub's own — a دوره slug like `shola-jawid-dowre-1` was never
	// what "is this the actively-publishing publication" was actually
	// asking about) for $is_current.
	$pub_parent  = ( $pub && $pub->parent ) ? get_term( $pub->parent, 'publication' ) : false;
	$root_slug   = ( $pub_parent && ! is_wp_error( $pub_parent ) ) ? $pub_parent->slug : ( $pub ? $pub->slug : '' );
	$is_current  = 'shola-jawid' === $root_slug;
	$number      = get_post_meta( get_the_ID(), 'shcore_issue_number', true );

	$pdf_id   = (int) get_post_meta( get_the_ID(), 'shcore_pdf_id', true );
	$pdf_url  = $pdf_id ? wp_get_attachment_url( $pdf_id ) : '';
	$pdf_size = shola_get_pdf_size( $pdf_id );

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
			<?php if ( $pub_parent ) : ?>
				<span aria-hidden="true"> / </span>
				<a href="<?php echo esc_url( get_term_link( $pub_parent ) ); ?>"><?php echo esc_html( $pub_parent->name ); ?></a>
			<?php endif; ?>
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

			<?php
			/*
			 * 2026-09-03: this used $pub (a دوره/period child term since
			 * the migration, e.g. `shola-jawid-dowre-1`) where it actually
			 * means the top-level publication (شعله جاوید/جهان برای فتح) —
			 * shola_publication_status_label() always returned «آرشیوی»
			 * here regardless of which publication, since a دوره slug
			 * never equals 'shola-jawid'. Now uses $root_slug (the
			 * resolved grandparent's slug, falling back to $pub's own if
			 * there's somehow no parent — see where it's computed above).
			 */
			?>
			<div>
				<p class="<?php echo $is_current ? 'badge-current' : 'badge-archive'; ?>">
					<?php
					echo esc_html(
						$number
							? sprintf( /* translators: 1: issue number, 2: current/archived status label. */ __( 'شمارهٔ %1$s · %2$s', 'shola-jawid' ), shola_to_persian_digits( $number ), shola_publication_status_label( $root_slug ) )
							: shola_publication_status_label( $root_slug )
					);
					?>
				</p>
				<h1 class="article-title mt-sm">
					<?php
					/*
					 * Publication name ("شعله جاوید ·") dropped from the H1,
					 * 2026-09-05, per Farhad: the breadcrumb and the badge
					 * pill directly above already say which publication this
					 * is (and this page only ever renders reached via that
					 * publication's own archive), so repeating the name here
					 * was redundant.
					 */
					echo esc_html(
						$number
							? sprintf( /* translators: %s: issue number. */ __( 'شمارهٔ %s', 'shola-jawid' ), shola_to_persian_digits( $number ) )
							: get_the_title()
					);
					?>
				</h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="article-dek mt-sm"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34 ) ); ?></p>
				<?php endif; ?>

				<dl class="issue-meta">
					<dt><?php esc_html_e( 'تاریخ نشر', 'shola-jawid' ); ?></dt>
					<dd><time datetime="<?php echo esc_attr( shola_get_iso_datetime() ); ?>"><?php echo esc_html( shola_get_jalali_month_year_label() ); ?></time></dd>

					<?php if ( $contents_summary ) : ?>
						<dt><?php esc_html_e( 'شمار مطالب', 'shola-jawid' ); ?></dt>
						<dd><?php echo esc_html( $contents_summary ); ?></dd>
					<?php endif; ?>

					<?php if ( $pdf_size ) : ?>
						<dt><?php esc_html_e( 'حجم فایل', 'shola-jawid' ); ?></dt>
						<dd lang="en"><?php echo esc_html( $pdf_size ); ?> · PDF</dd>
					<?php endif; ?>

					<?php
					/*
					 * سردبیر مسئول (managing editor) row removed from this
					 * page's display, 2026-09-05, per Farhad: not wanted
					 * here. shola_get_managing_editor() and its CMS/label
					 * setting are untouched — only this template's dl row
					 * is gone, not the underlying data or setting.
					 */
					?>
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
