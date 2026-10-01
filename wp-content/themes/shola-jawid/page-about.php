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
 * The tab nav is structural chrome (fragment links into the sections
 * below) kept in the template, not pulled from admin — checked the
 * spec text directly for this gap: دربارهٔ ما's three listed parts are
 * title, logo, and the content body only, no mention of tabs, so this
 * remains intentional, not another instance of the same gap. The
 * actual prose — mission statement, editorial board, submission
 * guidelines, etc. — lives in the Page's own post_content, edited via
 * the block editor (Heading blocks with an Anchor set to match each
 * tab's #fragment), not hardcoded here. Per Farhad's confirmation
 * (2026-08-06): this is substantive editorial content the client
 * should be able to edit without a code change, unlike the short
 * structural labels elsewhere in this phase.
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

$tabs = array(
	'about'      => __( 'دربارهٔ ما', 'shola-jawid' ),
	'team'       => __( 'هیئت تحریریه', 'shola-jawid' ),
	'contact'    => __( 'تماس', 'shola-jawid' ),
	'guidelines' => __( 'راهنمای همکاری', 'shola-jawid' ),
	'republish'  => __( 'بازنشر', 'shola-jawid' ),
	'support'    => __( 'حمایت مالی', 'shola-jawid' ),
	'write'      => __( 'نوشتن برای ما', 'shola-jawid' ),
);
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

		<nav class="about-tabs" aria-label="<?php esc_attr_e( 'بخش‌های دربارهٔ ما', 'shola-jawid' ); ?>">
			<?php
			$first = true;
			foreach ( $tabs as $anchor => $label ) :
				?>
				<a<?php echo $first ? ' class="active"' : ''; ?> href="#<?php echo esc_attr( $anchor ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php
				$first = false;
			endforeach;
			?>
		</nav>

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
