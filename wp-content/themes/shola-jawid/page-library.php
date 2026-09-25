<?php
/**
 * Template: page-library.php — کتابخانه (Library listing).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-library.html
 * (Phase 4.2). Applies to the Page with slug `library`.
 *
 * "تازه‌ترین اسناد" (latest documents, flat across all collections)
 * section removed 2026-09-25, per Farhad relaying the client's explicit
 * ask against a live screenshot: this landing page should only show the
 * collection tiles, not a flat document feed underneath. The
 * `latest_documents_heading` label this section used is left registered
 * in Label_Settings (not deleted) — same "don't silently lose an
 * editor's saved override, mark the settings-page description instead"
 * precedent already established there for other now-unused labels.
 *
 * `.article-crumb` breadcrumb added 2026-09-25, per Farhad relaying the
 * client's explicit ask for one "on all levels that require it" across
 * کتابخانه — this landing page had none at all. Two levels only (Home /
 * کتابخانه, matching page-party-documents.php's own equivalent) since
 * there's no deeper context to show here; taxonomy-collection.php (one
 * level down) and single-document.php (already had one) each get their
 * own third level for the collection.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$collection_slugs = array( 'classics', 'international-movement', 'party-documents', 'critique-polemic' );
?>
	<section class="wrap section-top">

		<nav class="article-crumb mt-lg" aria-label="<?php esc_attr_e( 'مسیر', 'shola-jawid' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a class="active" href="<?php echo esc_url( home_url( '/library/' ) ); ?>"><?php esc_html_e( 'کتابخانه', 'shola-jawid' ); ?></a>
		</nav>

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
<?php
get_footer();
