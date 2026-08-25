<?php
/**
 * Template: page-about.php — دربارهٔ حزب کمونیست (مائوئیست) افغانستان
 * (About page). Heading text updated, Phase D (2026-08-26,
 * docs/CHANGELOG.md) — organization-name rename; see that entry for
 * scope (this heading only — the About WP Page's own title/body
 * content is out of scope, edited by Farhad directly in wp-admin).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-about.html
 * (Phase 4.2). Applies to the Page with slug `about`.
 *
 * The tab nav is structural chrome (fragment links into the sections
 * below) kept in the template, but the actual prose — mission
 * statement, editorial board, submission guidelines, etc. — lives in
 * the Page's own post_content, edited via the block editor (Heading
 * blocks with an Anchor set to match each tab's #fragment), not
 * hardcoded here. Per Farhad's confirmation (2026-08-06): this is
 * substantive editorial content the client should be able to edit
 * without a code change, unlike the short structural labels elsewhere
 * in this phase.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

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
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'دربارهٔ حزب کمونیست (مائوئیست) افغانستان', 'shola-jawid' ); ?></h1>
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
