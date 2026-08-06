<?php
// inc/template-tags.php — presentation-only helper functions used by templates.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The six topic terms cycle through six fixed crimson-family shades in the
 * v6 popup menu (main.css §06, .menu-topic--c1..c6). v6 hardcodes each
 * topic to a specific shade in a fixed order (economy=c1 ... science-and-art
 * =c6); mapped here by slug rather than by term_id/position so it doesn't
 * depend on get_terms() ordering.
 *
 * @param string $slug Topic term slug.
 * @return string Modifier class, e.g. "menu-topic--c1".
 */
function shola_topic_color_class( $slug ) {
	$map = array(
		'economy'                => 'menu-topic--c1',
		'world'                  => 'menu-topic--c2',
		'afghanistan'            => 'menu-topic--c3',
		'women'                  => 'menu-topic--c4',
		'international-movement' => 'menu-topic--c5',
		'science-and-art'        => 'menu-topic--c6',
	);
	return isset( $map[ $slug ] ) ? $map[ $slug ] : 'menu-topic--c1';
}

/**
 * Fixed v6 display order for the 6 topic terms (economy=c1 ...
 * science-and-art=c6). get_terms()'s default orderby is alphabetical by
 * name, which wouldn't match — this is the actual, deliberate order.
 *
 * @return string[] Topic slugs, in display order.
 */
function shola_get_topic_slugs_ordered() {
	return array( 'economy', 'world', 'afghanistan', 'women', 'international-movement', 'science-and-art' );
}

/**
 * Fixed v6 display order for the 2 publication terms (current first).
 *
 * @return string[] Publication slugs, in display order.
 */
function shola_get_publication_slugs_ordered() {
	return array( 'shola-jawid', 'a-world-to-win' );
}

/**
 * Which of the two publication terms is "current" vs. "former" — a fixed
 * editorial fact from the IA doc (PUB-SJ current, PUB-WW former), not
 * something derivable from taxonomy data alone.
 *
 * @param string $slug Publication term slug.
 * @return string
 */
function shola_publication_status_label( $slug ) {
	return 'shola-jawid' === $slug ? __( 'جاری', 'shola-jawid' ) : __( 'آرشیوی', 'shola-jawid' );
}

/**
 * Masthead runner text ("SHOLA JAWID · شماره ۳۲ · سرطان ۱۴۰۵" in v6) —
 * built from the latest published issue's number + date if one exists,
 * falling back to just the site name so the masthead never shows stale
 * placeholder data once real content exists.
 *
 * @return string
 */
function shola_get_masthead_runner() {
	$site_name = get_bloginfo( 'name' );

	$latest = get_posts(
		array(
			'post_type'      => 'issue',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	if ( ! $latest ) {
		return $site_name;
	}

	$number = get_post_meta( $latest[0]->ID, 'shcore_issue_number', true );
	if ( ! $number ) {
		return $site_name;
	}

	return sprintf(
		/* translators: 1: site name, 2: issue number, 3: issue date. */
		__( '%1$s · شماره %2$s · %3$s', 'shola-jawid' ),
		$site_name,
		$number,
		get_the_date( '', $latest[0] )
	);
}

/**
 * Fallback for the `menu_sections` nav location, matching v6's
 * hardcoded defaults (_menu.html) — used until an admin builds a real
 * menu under Appearance → Menus for pages that don't exist yet (Phase
 * 4.2). Same visual result, real <nav>-compatible markup.
 *
 * @return void
 */
function shola_fallback_menu_sections() {
	?>
	<ul class="menu-list">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/publications/' ) ); ?>"><?php esc_html_e( 'نشرات', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/library/' ) ); ?>"><?php esc_html_e( 'کتابخانه', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/topics/' ) ); ?>"><?php esc_html_e( 'همهٔ موضوعات', 'shola-jawid' ); ?></a></li>
	</ul>
	<?php
}

/**
 * Fallback for the `menu_more` nav location, matching v6's defaults.
 *
 * @return void
 */
function shola_fallback_menu_more() {
	?>
	<ul class="menu-list menu-list-small">
		<li><a href="<?php echo esc_url( home_url( '/announcements/' ) ); ?>"><?php esc_html_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'دربارهٔ ما', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'ارتباط با حزب', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/search/' ) ); ?>"><?php esc_html_e( 'جست‌وجو در آرشیو', 'shola-jawid' ); ?></a></li>
	</ul>
	<?php
}

/**
 * Fallback for the `footer_topics` nav location — v6's footer shows a
 * curated 3-of-6 topic subset (economy, afghanistan, women), not all six,
 * plus an "all topics" link. Real term links once the taxonomy exists
 * (Phase 3.2, already live), same curated subset as v6 until an editor
 * chooses to customize it via Appearance → Menus.
 *
 * @return void
 */
function shola_fallback_footer_topics() {
	$slugs = array( 'economy', 'afghanistan', 'women' );
	echo '<ul>';
	foreach ( $slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, 'topic' );
		if ( $term ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( get_term_link( $term ) ), esc_html( $term->name ) );
		}
	}
	printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/topics/' ) ), esc_html__( 'همهٔ موضوعات', 'shola-jawid' ) );
	echo '</ul>';
}

/**
 * Fallback for the `footer_site` nav location, matching v6's defaults.
 *
 * @return void
 */
function shola_fallback_footer_site() {
	?>
	<ul>
		<li><a href="<?php echo esc_url( home_url( '/library/' ) ); ?>"><?php esc_html_e( 'کتابخانه', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/announcements/' ) ); ?>"><?php esc_html_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'دربارهٔ ما', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'ارتباط با حزب', 'shola-jawid' ); ?></a></li>
	</ul>
	<?php
}
