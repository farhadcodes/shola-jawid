<?php
/**
 * Template: page-party-documents.php — اسناد حزب (Party Documents
 * listing). Applies to the Page with slug `party-documents`. New
 * 2026-09-04 alongside the `party_document` CPT (shola-core\Post_Types)
 * — see that class's docblock for why this is a separate content type/
 * page from both نشریه (page-publications.php) and کتابخانه
 * (page-library.php), including the migration of the 2 documents
 * previously filed under کتابخانه's now-removed "اسناد حزب" shelf.
 *
 * A subsection landing page, not a document listing — `party_document_
 * category` is a self-managed taxonomy (the client wants staff to be
 * able to add categories freely as اسناد حزب accumulates, not a fixed
 * IA-doc vocabulary).
 *
 * Subsection tile block added 2026-09-24, per the client's explicit
 * request to bring اسناد حزب to parity with کتابخانه's own collection
 * tiles (page-library.php): reuses that same `.topic-list` component,
 * fed by shola_get_party_document_subsections() (inc/template-tags.php)
 * — dynamic via get_terms(), not a hardcoded slug array like کتابخانه's
 * own `$collection_slugs` (see that function's docblock for why that
 * wasn't replicated). That helper deliberately excludes this taxonomy's
 * «دسته‌بندی‌نشده» fallback term entirely (Category_Manager,
 * `NO_UNCATEGORIZED_FALLBACK` — see its own docblock for the client's
 * explicit "no such category, front end or back end" correction).
 *
 * Flat document grid removed 2026-09-25 (the very next round, per
 * Farhad relaying the client's explicit correction against a live
 * screenshot): individual اسناد حزب entries were showing both here
 * *and* under their own subsection archive
 * (taxonomy-party_document_category.php) — the client wants documents
 * reachable only through their subsection, not duplicated on this
 * landing page too. This page is now tiles-only, the same "browse by
 * category" landing role page-library.php's own tile block plays
 * (though that page additionally keeps its own flat "latest documents"
 * feed — a deliberate difference Farhad confirmed is intentional for
 * اسناد حزب specifically, not an oversight). One side effect worth
 * knowing: an اسناد حزب entry with no category assigned is no longer
 * reachable from this section of the site at all (no subsection to
 * list it under, and no "uncategorized" bucket per the prior
 * correction) — still reachable by its own permalink/search, just not
 * browsable from here, so staff should assign a category to every new
 * اسناد حزب entry going forward.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$subsections = shola_get_party_document_subsections();
?>
	<section class="wrap section-top">

		<nav class="article-crumb mt-lg" aria-label="<?php esc_attr_e( 'مسیر', 'shola-jawid' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a class="active" href="<?php echo esc_url( home_url( '/party-documents/' ) ); ?>"><?php esc_html_e( 'اسناد حزب', 'shola-jawid' ); ?></a>
		</nav>

		<header class="page-header">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'اسناد حزب', 'shola-jawid' ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'اسناد داخلی حزب — با پیش‌نمایش درون‌مرورگری و دریافت آزاد PDF.', 'shola-jawid' ); ?></p>
		</header>

		<?php if ( $subsections ) : ?>
			<ul class="topic-list">
				<?php foreach ( $subsections as $subsection ) : ?>
					<li><a href="<?php echo esc_url( $subsection['link'] ); ?>">
						<span class="name"><?php echo esc_html( $subsection['name'] ); ?></span>
						<span class="count"><?php echo esc_html( sprintf( /* translators: %s: document count. */ _n( '%s سند', '%s سند', $subsection['count'], 'shola-jawid' ), shola_to_persian_digits( $subsection['count'] ) ) ); ?></span></a></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p class="dek"><?php esc_html_e( 'هنوز دسته‌ای ایجاد نشده است.', 'shola-jawid' ); ?></p>
		<?php endif; ?>

	</section>
<?php
get_footer();
