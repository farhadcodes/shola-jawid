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
<?php
/*
 * Global page loader (2026-09-15, client-requested via Farhad, with an
 * Al Jazeera screenshot as the reference — same idea: a pale watermark
 * logo with a subtle "still working" pulse beneath it, shown on first
 * load and every internal navigation). CMS-configurable — enabled
 * state, the logo image, and animation speed all come from
 * shola-core's "بارگذاری صفحه" admin settings page
 * (includes/class-loader-settings.php), not hardcoded, per Farhad's
 * explicit ask that a manager be able to change these without a code
 * change.
 * Placed as the very first thing after <body>, before even the skip
 * link, so it's the first thing the browser paints — no point in a
 * "first load" loader that itself waits on other markup above it.
 * Deliberately renders nothing (not even the empty `<div>`) when
 * disabled via settings — no dead markup/CSS/JS for a feature a site
 * owner turned off, and no JS below has anything to guard against.
 */
$shola_loader_enabled = get_option( 'shcore_page_loader_enabled', '1' );
if ( $shola_loader_enabled ) :
	$shola_loader_logo_id = get_option( 'shcore_page_loader_logo_id' );
	$shola_loader_logo_url = $shola_loader_logo_id
		? wp_get_attachment_image_url( $shola_loader_logo_id, 'full' )
		: get_theme_file_uri( 'assets/images/page-loader-logo.svg' );
	if ( ! $shola_loader_logo_url ) {
		// Attachment was deleted from the media library after being
		// selected — fall back to the bundled default rather than an
		// empty <img src="">.
		$shola_loader_logo_url = get_theme_file_uri( 'assets/images/page-loader-logo.svg' );
	}
	$shola_loader_speed = get_option( 'shcore_page_loader_speed', 'normal' );
	if ( ! in_array( $shola_loader_speed, array( 'slow', 'normal', 'fast' ), true ) ) {
		$shola_loader_speed = 'normal';
	}
	?>
	<div id="page-loader" class="page-loader" data-speed="<?php echo esc_attr( $shola_loader_speed ); ?>" role="status" aria-live="polite" aria-label="<?php esc_attr_e( 'در حال بارگذاری…', 'shola-jawid' ); ?>">
		<div class="page-loader-inner">
			<img src="<?php echo esc_url( $shola_loader_logo_url ); ?>" alt="" class="page-loader-logo">
			<div class="page-loader-dots" aria-hidden="true">
				<span></span><span></span><span></span>
			</div>
		</div>
	</div>
	<script>
	/*
	 * Inline, not in main.js: this must run synchronously, the instant
	 * the parser reaches it — main.js is a deferred external file, and
	 * waiting for it to download would defeat the point of a "shown the
	 * moment the page starts loading" loader. Also self-contained on
	 * the hide side (window `load` + an absolute failsafe timeout) so
	 * the loader can never get stuck covering the page even if main.js
	 * fails to load/parse for some reason — the show-on-navigation
	 * behavior in main.js is an enhancement on top of this, not
	 * something this depends on.
	 * CSS default is hidden (opacity 0/visibility hidden) — .is-visible
	 * is what actually shows it, added here. That default matters for
	 * the no-JS case too: with JS disabled entirely, this script never
	 * runs, .is-visible is never added, and the loader simply never
	 * appears — the site works exactly as if this feature didn't
	 * exist, per this project's progressive-enhancement rule.
	 */
	(function () {
		var el = document.getElementById("page-loader");
		if (!el) return;
		el.classList.add("is-visible");
		var shownAt = Date.now();
		var minDisplay = 450;
		var hidden = false;
		function hide() {
			if (hidden) return;
			hidden = true;
			var wait = Math.max(0, minDisplay - (Date.now() - shownAt));
			setTimeout(function () {
				el.classList.remove("is-visible");
			}, wait);
		}
		window.addEventListener("load", hide);
		setTimeout(hide, 6000);
	})();
	</script>
<?php endif; ?>
<script>document.documentElement.classList.add("js")</script>
<a class="skip-link" href="#main"><?php esc_html_e( 'پرش به محتوای اصلی', 'shola-jawid' ); ?></a>

<?php
/*
 * Sticky-shrink masthead sentinel (Phase 13, 2026-09-07, client-
 * requested). Positioned at a fixed 80px from the top of the actual
 * page content (not the viewport — position: absolute with no
 * positioned ancestor places it relative to the initial containing
 * block, so it stays put at that document coordinate while the page
 * scrolls under it). main.js observes it with IntersectionObserver:
 * once it scrolls out of view — i.e. the user has scrolled past 80px —
 * .masthead gets `.is-scrolled`, which every compact-mode rule in
 * main.css §05 keys off. This threshold-via-sentinel technique avoids a
 * raw scroll-event listener (no per-frame recalculation), same
 * IntersectionObserver approach already used for this theme's scroll-
 * reveal animations. Empty and aria-hidden — carries no content, exists
 * only to be observed.
 */
?>
<span id="mast-sentinel" aria-hidden="true"></span>

<?php
/*
 * Masthead layout switch (2026-09-14, extended same day with `logo`,
 * `logo-light`, and `logo-radial`): reads the active masthead_section
 * entry, same mechanism front-page.php uses for hero_section. The
 * nav/menu markup below is identical for every layout — only the
 * brand block (.mast-brand, further down) and the color scheme
 * actually differ, so those are branched here rather than
 * duplicating this whole template.
 * `logo-light` and `logo-radial` are both structurally identical to
 * `logo` (same logo image, same date placement, same responsive
 * grid) — `logo-light` was Farhad's explicit "same structure,
 * reversed colors" ask, and `logo-radial` his follow-up "duplicate
 * the red one, just add a radial-gradient background" ask — so both
 * share the `masthead--logo` structural class and every `logo`-layout
 * check below, and only add their own modifier class on top for the
 * background/color override (main.css §05). The logo image itself is
 * untouched by any of the three (per Farhad, from the `logo-light`
 * round — "without the flag" meaning the flag graphic keeps its own
 * colors).
 */
$shola_masthead_layout    = shola_get_active_masthead_layout();
$shola_is_logo_layout     = in_array( $shola_masthead_layout, array( 'logo', 'logo-light', 'logo-radial' ), true );
$shola_is_two_tier_layout = 'two-tier' === $shola_masthead_layout;
$shola_masthead_modifier  = '';
if ( 'logo-light' === $shola_masthead_layout ) {
	$shola_masthead_modifier = 'masthead--logo-light';
} elseif ( 'logo-radial' === $shola_masthead_layout ) {
	$shola_masthead_modifier = 'masthead--logo-radial';
} elseif ( $shola_is_two_tier_layout ) {
	$shola_masthead_modifier = 'masthead--two-tier';
}
?>
<header class="masthead <?php echo $shola_is_logo_layout ? 'masthead--logo' : ''; ?> <?php echo esc_attr( $shola_masthead_modifier ); ?>">
<?php if ( $shola_is_two_tier_layout ) : ?>
	<?php
	/*
	 * `two-tier` layout (2026-09-18): a dark utility bar + red-gradient
	 * identity bar, structurally different from every other layout's
	 * shared single-row `.masthead-inner` grid below — pulled into its
	 * own template part rather than branched inline here, unlike
	 * `logo`/`logo-light`/`logo-radial` (which only swap small content
	 * chunks within that same shared row). This keeps every other
	 * layout's own markup in this file completely untouched — see
	 * template-parts/masthead/two-tier.php's own docblock for what it
	 * reuses and from where.
	 */
	get_template_part( 'template-parts/masthead/two-tier' );
	?>
<?php else : ?>
	<div class="wrap masthead-inner">

		<div class="masthead-left">
			<button type="button" id="menu-open" class="mast-btn" aria-expanded="false" aria-controls="menu-panel" aria-label="<?php esc_attr_e( 'باز کردن منو', 'shola-jawid' ); ?>">
				<svg width="22" height="14" viewBox="0 0 16 10" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M0 1h16M0 5h16M0 9h16"/></svg>
				<span><?php esc_html_e( 'منو', 'shola-jawid' ); ?></span>
			</button>
			<span aria-hidden="true" class="mast-slash">/</span>
			<?php
			/*
			 * 2026-09-02: this search icon is now mobile-only (`hide-desktop`) —
			 * on desktop it's replaced by the larger one at the far end of
			 * .masthead-right (see below), per Farhad: easier to find/read.
			 * Two markup instances, CSS-toggled by breakpoint, matches the
			 * existing hide-mobile/hide-desktop pattern already used for the
			 * desktop-only nav row — no JS move across the two grid cells.
			 */
			?>
			<a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="link-quiet mast-icon-link hide-desktop" aria-label="<?php esc_attr_e( 'جست‌وجو', 'shola-jawid' ); ?>">
				<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
			</a>

			<?php
			/*
			 * اسناد حزب briefly lived here too (added alongside the
			 * party_document CPT, 2026-09-04) — removed 2026-09-05 per
			 * Farhad: this top bar was getting crowded, and اسناد حزب only
			 * needs to be one click away via the full popup menu's «بخش‌ها»
			 * list (see shola_maybe_add_party_documents_menu_item(),
			 * inc/setup.php), not present in this always-visible row on
			 * every screen size.
			 */
			?>
			<nav class="mast-pub-nav" aria-label="<?php esc_attr_e( 'پیوندهای اصلی', 'shola-jawid' ); ?>">
				<a href="<?php echo esc_url( home_url( '/publications/' ) ); ?>" class="mast-btn"><?php esc_html_e( 'نشریات', 'shola-jawid' ); ?></a>
				<span aria-hidden="true" class="mast-slash-light">/</span>
				<a href="<?php echo esc_url( home_url( '/topics/' ) ); ?>" class="mast-btn"><?php esc_html_e( 'موضوعات', 'shola-jawid' ); ?></a>
				<span aria-hidden="true" class="mast-slash-light">/</span>
				<a href="<?php echo esc_url( home_url( '/library/' ) ); ?>" class="mast-btn"><?php esc_html_e( 'کتابخانه', 'shola-jawid' ); ?></a>
			</nav>
		</div>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — ' . __( 'صفحهٔ اصلی', 'shola-jawid' ) ); ?>" class="mast-brand">
			<?php
			/*
			 * `logo` layout (2026-09-14): the site name text is replaced
			 * with the logo set at Appearance → Customize → Site Identity
			 * (native WP feature, add_theme_support('custom-logo') in
			 * inc/setup.php) — not a separate upload field of its own, so
			 * there's exactly one place to manage the logo image. Falls
			 * back to the plain nameplate if no logo has been set yet,
			 * same as `default`, so an empty Customizer field never leaves
			 * the header looking broken.
			 */
			$shola_logo_id = $shola_is_logo_layout ? get_theme_mod( 'custom_logo' ) : 0;
			if ( $shola_logo_id ) {
				echo wp_get_attachment_image(
					$shola_logo_id,
					'full',
					false,
					array(
						'class'   => 'mast-logo',
						'loading' => 'eager',
						'alt'     => get_bloginfo( 'name' ),
					)
				);
			} else {
				?>
				<span class="mast-nameplate"><?php bloginfo( 'name' ); ?></span>
				<?php
			}
			// `default` layout only — `logo`/`logo-light` show the date at
			// the outer edge of .masthead-right instead (see below).
			if ( ! $shola_is_logo_layout ) {
				?>
				<span class="mast-runner" lang="en"><?php echo esc_html( shola_get_masthead_runner() ); ?></span>
				<?php
			}
			?>
		</a>

		<div class="masthead-right">
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="mast-btn hide-mobile"><?php esc_html_e( 'دربارهٔ ما', 'shola-jawid' ); ?></a>
			<span aria-hidden="true" class="hide-mobile mast-slash-light">/</span>
			<a href="<?php echo esc_url( home_url( '/announcements/' ) ); ?>" class="mast-btn hide-mobile"><?php esc_html_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?></a>
			<span aria-hidden="true" class="hide-mobile mast-slash-light">/</span>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="mast-btn hide-mobile"><?php esc_html_e( 'تماس', 'shola-jawid' ); ?></a>
			<span aria-hidden="true" class="hide-mobile mast-slash-light">/</span>
			<a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="link-quiet mast-icon-link mast-icon-link--lg hide-mobile" aria-label="<?php esc_attr_e( 'جست‌وجو', 'shola-jawid' ); ?>">
				<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
			</a>
			<?php if ( $shola_is_logo_layout ) : ?>
				<?php
				/*
				 * `logo`/`logo-light` layout: date at the outer edge of
				 * .masthead-right on desktop/tablet (2026-09-14, corrected same day —
				 * Farhad's second live look found it placed before منو on
				 * the other side, which read as outranking the menu
				 * button; moved to the far outer edge, after the search
				 * icon, instead). The separating slash stays desktop/
				 * tablet-only (nothing else in .masthead-right on mobile
				 * to separate it from), but the date span itself is not
				 * `hide-mobile`: at ≤720px it's regrouped next to the
				 * logo instead (main.css §05 mobile block), per Farhad's
				 * mobile-specific request — not hidden there.
				 */
				?>
				<span aria-hidden="true" class="hide-mobile mast-slash-light">/</span>
				<span class="mast-runner mast-runner--inline" lang="en"><?php echo esc_html( shola_get_masthead_runner() ); ?></span>
			<?php endif; ?>
		</div>

	</div>
<?php endif; ?>
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

		<nav aria-label="<?php echo esc_attr( shola_get_label( 'nav_topics_label' ) ); ?>">
			<p class="menu-section-title"><?php echo esc_html( shola_get_label( 'nav_topics_label' ) ); ?></p>
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
				<p class="menu-section-title"><?php echo esc_html( shola_get_label( 'nav_more_label' ) ); ?></p>
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
