<?php
// Template: header.php — <head> open + masthead + popup menu.
// Converted from 03_UI_Design/shola-jawid-ui/pages/_shell.html + _header.html
// + _menu.html (Phase 4.1). Pixel-faithful port — see docs/CHANGELOG.md
// 2026-08-06 for the two deliberate deviations from the static HTML:
// (1) the invalid nested <a> inside <button> at _header.html:6-12 is fixed
// here (siblings instead), (2) all inline style="" attributes are replaced
// with classes added to assets/css/main.css (same computed values).

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php
	/*
	 * All fonts are now self-hosted via @font-face in main.css
	 * (Farhang2/ModamPro since Phase 4.1; Newsreader/Inter/JetBrains
	 * Mono added Phase 5.4) — no Google Fonts CDN request on this page
	 * at all anymore.
	 */
	?>

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<script>document.documentElement.classList.add("js")</script>
<a class="skip-link" href="#main"><?php esc_html_e( 'پرش به محتوای اصلی', 'shola-jawid' ); ?></a>

<header class="masthead">
	<div class="wrap masthead-inner">

		<div class="masthead-left">
			<button type="button" id="menu-open" class="mast-btn" aria-expanded="false" aria-controls="menu-panel" aria-label="<?php esc_attr_e( 'باز کردن منو', 'shola-jawid' ); ?>">
				<svg width="22" height="14" viewBox="0 0 16 10" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M0 1h16M0 5h16M0 9h16"/></svg>
				<span><?php esc_html_e( 'منو', 'shola-jawid' ); ?></span>
			</button>
			<span aria-hidden="true" class="mast-slash">/</span>
			<a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="link-quiet mast-icon-link" aria-label="<?php esc_attr_e( 'جست‌وجو', 'shola-jawid' ); ?>">
				<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
			</a>

			<span class="mast-sister mast-sister-desktop hide-mobile" aria-label="<?php esc_attr_e( 'نشریات', 'shola-jawid' ); ?>">
				<?php
				$sister_slugs = shola_get_publication_slugs_ordered();
				foreach ( $sister_slugs as $i => $slug ) :
					$term = get_term_by( 'slug', $slug, 'publication' );
					if ( ! $term ) {
						continue;
					}
					if ( $i > 0 ) :
						?>
						<span class="sep" aria-hidden="true">/</span>
					<?php endif; ?>
					<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
					<?php
				endforeach;
				?>
			</span>
		</div>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — ' . __( 'صفحهٔ اصلی', 'shola-jawid' ) ); ?>" class="mast-brand">
			<span class="mast-nameplate"><?php bloginfo( 'name' ); ?></span>
			<span class="mast-runner" lang="en"><?php echo esc_html( shola_get_masthead_runner() ); ?></span>
		</a>

		<div class="masthead-right">
			<a href="<?php echo esc_url( home_url( '/announcements/' ) ); ?>" class="mast-btn hide-mobile"><?php esc_html_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?></a>
			<span aria-hidden="true" class="hide-mobile mast-slash-light">/</span>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="mast-btn hide-mobile"><?php esc_html_e( 'تماس', 'shola-jawid' ); ?></a>
		</div>

	</div>
</header>

<div id="menu-panel" class="menu-panel" data-open="false" aria-hidden="true" role="dialog" aria-label="<?php esc_attr_e( 'منوی اصلی', 'shola-jawid' ); ?>">

	<div class="wrap menu-topbar">
		<button type="button" id="menu-close" class="menu-close" aria-label="<?php esc_attr_e( 'بستن منو', 'shola-jawid' ); ?>">
			<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M6 18L18 6"/></svg>
			<span><?php esc_html_e( 'بستن', 'shola-jawid' ); ?></span>
		</button>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mast-nameplate mast-nameplate--menu" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — ' . __( 'صفحهٔ اصلی', 'shola-jawid' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
	</div>

	<div class="menu-grid">

		<nav aria-label="<?php esc_attr_e( 'موضوعات', 'shola-jawid' ); ?>">
			<p class="menu-section-title"><?php esc_html_e( 'موضوعات', 'shola-jawid' ); ?></p>
			<ul class="menu-topics">
				<?php foreach ( shola_get_topic_slugs_ordered() as $slug ) : ?>
					<?php
					$term = get_term_by( 'slug', $slug, 'topic' );
					if ( ! $term ) {
						continue;
					}
					?>
					<li><a class="menu-topic <?php echo esc_attr( shola_topic_color_class( $slug ) ); ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<div class="menu-side">
			<div class="menu-social" aria-label="<?php esc_attr_e( 'شبکه‌های اجتماعی', 'shola-jawid' ); ?>">
				<?php foreach ( shola_get_social_links() as $social ) : ?>
					<a href="<?php echo esc_url( $social['url'] ); ?>" aria-label="<?php echo esc_attr( $social['label'] ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><?php echo $social['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, trusted inline SVG defined in shola_get_social_links(), not user input. ?></svg></a>
				<?php endforeach; ?>
			</div>

			<nav aria-label="<?php esc_attr_e( 'بخش‌های سایت', 'shola-jawid' ); ?>">
				<p class="menu-section-title"><?php esc_html_e( 'بخش‌ها', 'shola-jawid' ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu_sections',
						'container'      => false,
						'items_wrap'     => '<ul class="menu-list">%3$s</ul>',
						'fallback_cb'    => 'shola_fallback_menu_sections',
					)
				);
				?>
			</nav>

			<nav aria-label="<?php esc_attr_e( 'پیوندهای کمکی', 'shola-jawid' ); ?>">
				<p class="menu-section-title"><?php esc_html_e( 'بیشتر', 'shola-jawid' ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu_more',
						'container'      => false,
						'items_wrap'     => '<ul class="menu-list menu-list-small">%3$s</ul>',
						'fallback_cb'    => 'shola_fallback_menu_more',
					)
				);
				?>
			</nav>

			<div class="menu-publications">
				<p class="menu-section-title"><?php esc_html_e( 'نشرات', 'shola-jawid' ); ?></p>
				<ul class="menu-list menu-list-small">
					<?php foreach ( shola_get_publication_slugs_ordered() as $slug ) : ?>
						<?php
						$term = get_term_by( 'slug', $slug, 'publication' );
						if ( ! $term ) {
							continue;
						}
						?>
						<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?> <span class="meta-mono ms-sm"><?php echo esc_html( shola_publication_status_label( $slug ) ); ?></span></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>

</div>

<main id="main">
