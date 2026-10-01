<?php
/**
 * Template: page-about.php — دربارهٔ حزب کمونیست (مائوئیست) افغانستان
 * (About page). Converted from
 * 03_UI_Design/shola-jawid-ui/pages/body-about.html (Phase 4.2).
 * Applies to the Page with slug `about`.
 *
 * H1 now pulls from the real WP Page title (spec-audit gap B13,
 * 2026-10-01) — the spec lists "عنوان صفحه" (page title) as one of this
 * page's three admin-editable parts, alongside the logo (B14, separate
 * gap) and the content body below. It was a hardcoded string before
 * this fix (the organization-name rename, Phase D, 2026-08-26, updated
 * that string in place rather than switching to the real title) —
 * `docs/CHANGELOG.md`'s Phase D entry describes that older, now
 * superseded state.
 *
 * Tab nav removed, 2026-10-01, per Farhad's explicit ask after reviewing
 * the B14 logo-centering fix live ("remove the tabs... we do not need
 * them"). The content body's own headings (mission statement, editorial
 * board, submission guidelines, etc.) still live in the Page's own
 * post_content, edited via the block editor, same as always — removing
 * the tab nav didn't touch that, or the #anchor ids those headings may
 * still carry from when the tabs linked to them.
 *
 * لوگوی حزب (spec-audit gap B14, 2026-10-01): the spec lists a party
 * logo as this page's other admin-editable part (alongside title and
 * content), but the approved v6 design (body-about.html) has no logo
 * anywhere on this page — confirmed directly before guessing at a
 * placement. Per Farhad's decision: reuses the same
 * `get_theme_mod( 'custom_logo' )` already shown in the header/footer/
 * masthead, rather than a new, separate upload field — there is only
 * ever one "party logo" for this site, already admin-editable via
 * Appearance → Customize → Site Identity, so a second field would just
 * duplicate it with no way to keep the two in sync. Placed centered
 * above the H1 in the page-header block, per Farhad's explicit pick
 * between that and an inline placement at the top of the prose.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$shola_about_logo_id = get_theme_mod( 'custom_logo' );
?>
	<section class="wrap section-top">

		<header class="page-header page-header--tight">
			<?php if ( $shola_about_logo_id ) : ?>
				<?php
				echo wp_get_attachment_image(
					$shola_about_logo_id,
					'full',
					false,
					array(
						'class'   => 'about-page-logo',
						'loading' => 'eager',
						'alt'     => get_bloginfo( 'name' ),
					)
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() escapes internally.
				?>
			<?php endif; ?>
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php echo esc_html( get_the_title( get_queried_object_id() ) ); ?></h1>
			</div>
		</header>

	</section>

	<section class="wrap-read">
		<div class="prose">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</section>
<?php
get_footer();
