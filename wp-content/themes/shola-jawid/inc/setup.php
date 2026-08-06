<?php
// inc/setup.php — theme_support, nav menus, image sizes, admin footer credit.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports, nav menu locations, and image sizes.
 *
 * Image sizes are registered against the v6 "Quiet Press" prototype's actual
 * CSS aspect-ratio declarations (confirmed by reading assets/css/main.css
 * directly, per Phase 1's audit — not assumed), so front-end crops line up
 * with object-fit: cover without letterboxing or client-side re-cropping:
 *
 * - shola_card        3:2  — .card-media          (main.css §09)
 * - shola_article_hero 21:9 — .article-hero-media  (main.css §12)
 * - shola_issue_cover  3:4  — .issue-cover          (main.css §14, single issue/document hero)
 * - shola_issue_card   3:4  — .issue-card-media     (main.css §14, issue archive grid thumbnail)
 * - shola_hero_wide         — .hero-media (main.css §10): viewport-height-based,
 *   no fixed CSS aspect-ratio; registered oversized landscape so object-fit:
 *   cover has enough source to crop against at any viewport.
 *
 * .card-mini .card-media (16:10) exists in the CSS but is not used by any of
 * the 23 v6 pages (confirmed by grep in Phase 1.1) — not registered until a
 * template actually needs it, per the no-premature-abstraction rule.
 *
 * @return void
 */
function shola_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'responsive-embeds' );

	/*
	 * The v6 popup menu (main.css §06) has four columns: Topics and
	 * Publications are generated from the `topic`/`publication` taxonomy
	 * terms directly (Phase 3.2), not editor-managed menus — only
	 * Sections and More are real, curated nav lists, so those are the two
	 * locations registered here (Phase 4.1, replacing the single
	 * placeholder `primary` location from Phase 2.1, which didn't match
	 * v6's actual structure). The footer has two further curated lists
	 * (a 3-of-6 topic subset, and a site-links column) that are also
	 * editor-manageable menus, not taxonomy loops — registered separately
	 * since they're visually and structurally distinct locations, not the
	 * same content repeated.
	 */
	register_nav_menus(
		array(
			'menu_sections' => __( 'منو — بخش‌ها', 'shola-jawid' ),
			'menu_more'     => __( 'منو — بیشتر', 'shola-jawid' ),
			'footer_topics' => __( 'پاورقی — موضوعات منتخب', 'shola-jawid' ),
			'footer_site'   => __( 'پاورقی — پیوندهای سایت', 'shola-jawid' ),
		)
	);

	add_image_size( 'shola_card', 800, 533, true );
	add_image_size( 'shola_article_hero', 1600, 686, true );
	add_image_size( 'shola_issue_cover', 600, 800, true );
	add_image_size( 'shola_issue_card', 375, 500, true );
	add_image_size( 'shola_hero_wide', 1920, 1080, true );
}
add_action( 'after_setup_theme', 'shola_setup' );

/**
 * Light-touch admin footer credit line, per CLAUDE.md §7.6.
 *
 * @param string $text Default admin footer text.
 * @return string
 */
function shola_admin_footer_text( $text ) {
	return sprintf(
		/* translators: %s: linked author name, not translatable. */
		__( 'ساخته‌شده با قالب شعله جاوید، توسط %s', 'shola-jawid' ),
		'<a href="https://github.com/farhadcodes" target="_blank" rel="noopener">Farhad Farhaad</a>'
	);
}
add_filter( 'admin_footer_text', 'shola_admin_footer_text' );

/**
 * Soft-dependency admin notice: the theme owns no content-model logic
 * (CPTs/taxonomies/meta live in the shola-core plugin, per CLAUDE.md §2),
 * so it must degrade gracefully — never fatal — if that plugin is
 * inactive. Shows a dismissible notice instead of a white screen.
 *
 * @return void
 */
function shola_maybe_show_missing_plugin_notice() {
	if ( 'shola-jawid' !== get_template() ) {
		return;
	}

	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	if ( is_plugin_active( 'shola-core/shola-core.php' ) ) {
		return;
	}
	?>
	<div class="notice notice-warning is-dismissible">
		<p>
			<?php
			esc_html_e(
				'برای عملکرد کامل نشریه (شماره‌ها، اسناد، اطلاعیه‌ها)، افزونهٔ «Shola Core» باید نصب و فعال باشد.',
				'shola-jawid'
			);
			?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'shola_maybe_show_missing_plugin_notice' );
