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

$collections = shola_get_library_collections();
/*
 * English term label/link/count — added 2026-11-28, English-version
 * rollout, same reasoning/pattern as taxonomy-collection.php's own fix
 * (and every sibling tile-listing Page like this one): $term->name/
 * get_term_link()/$term->count are always the raw, mixed-language
 * Persian values.
 */
$shola_lang = \SholaCore\Language::current();
?>
	<section class="wrap section-top">

		<nav class="article-crumb mt-lg" aria-label="<?php esc_attr_e( 'مسیر', 'shola-jawid' ); ?>">
			<a href="<?php echo esc_url( ( 'en' === $shola_lang ) ? home_url( '/en/' ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a class="active" href="<?php echo esc_url( ( 'en' === $shola_lang ) ? home_url( '/en/library/' ) : home_url( '/library/' ) ); ?>"><?php esc_html_e( 'کتابخانه', 'shola-jawid' ); ?></a>
		</nav>

		<header class="page-header">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'کتابخانه', 'shola-jawid' ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'چهار مجموعه از اسناد PDF: متن‌های پایهٔ نظری، بیانیه‌های جنبش‌های جهانی، اسناد رسمی حزب، و پلمیک‌ها. پیش‌نمایش درون‌مرورگری و دریافت آزاد.', 'shola-jawid' ); ?></p>
		</header>

		<ul class="topic-list">
			<?php foreach ( $collections as $term ) : ?>
				<?php
				$term_link  = shola_build_term_link_for_lang( $term, $shola_lang );
				$term_link  = $term_link ? $term_link : get_term_link( $term );
				// Always per-language — see front-page.php's موضوعات panel for the full reasoning (2026-11-28).
				$term_count = shola_get_term_post_count( $term, $shola_lang, 'document' );
				?>
				<li><a href="<?php echo esc_url( $term_link ); ?>">
					<span class="name"><?php echo esc_html( shola_get_term_label( $term, $shola_lang ) ); ?></span>
					<?php if ( 'en' === $shola_lang ) : ?>
						<?php /* translators: %s: document count. */ ?>
						<span class="count"><?php echo esc_html( sprintf( _n( '%s document', '%s documents', $term_count, 'shola-jawid' ), number_format_i18n( $term_count ) ) ); ?></span></a></li>
					<?php else : ?>
						<?php /* translators: %s: document count. */ ?>
						<span class="count"><?php echo esc_html( sprintf( _n( '%s سند', '%s سند', $term_count, 'shola-jawid' ), shola_to_persian_digits( $term_count ) ) ); ?></span></a></li>
					<?php endif; ?>
			<?php endforeach; ?>
		</ul>

	</section>
<?php
get_footer();
