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
	 * The IA doc's content model treats مقاله/یادداشت (article/note) as one
	 * native `post` type with no distinguishing field of its own (Phase
	 * 3.3 didn't define one). v6's card component displays "یادداشت"
	 * instead of "مقاله" for shorter/lighter dispatch-style posts
	 * (main.css §09 .type-label) — mapped onto WP's native `aside` post
	 * format rather than a new custom meta field, since that's exactly
	 * what this format is for and avoids adding content-model surface for
	 * a single display-label distinction.
	 */
	add_theme_support( 'post-formats', array( 'aside' ) );

	/*
	 * The v6 popup menu (main.css §06) has four columns. Topics and
	 * Publications were originally generated straight from the
	 * `topic`/`publication` taxonomies via a hardcoded slug order
	 * (Phase 3.2) — found by Farhad (2026-08-08) to fail the IA doc's
	 * "editable by staff without a developer" requirement: a newly added
	 * topic term never appeared, since the order was a fixed PHP array,
	 * not menu data. Fixed by registering two more real locations here so
	 * all four popup columns are genuine `wp_nav_menu()` locations;
	 * `shola_get_topic_slugs_ordered()` / `shola_get_publication_slugs_
	 * ordered()` (inc/template-tags.php) now read the assigned menu's
	 * item order instead of a hardcoded array, with the old hardcoded
	 * array kept only as a last-resort fallback. See docs/CHANGELOG.md
	 * 2026-08-08. The footer has two further curated lists (a 3-of-6
	 * topic subset, and a site-links column) that are also editor-
	 * manageable menus — registered separately since they're visually and
	 * structurally distinct locations, not the same content repeated.
	 */
	register_nav_menus(
		array(
			'menu_topics'       => __( 'منو — موضوعات', 'shola-jawid' ),
			'menu_publications' => __( 'منو — نشریات', 'shola-jawid' ),
			'menu_sections'     => __( 'منو — بخش‌ها', 'shola-jawid' ),
			'menu_more'         => __( 'منو — بیشتر', 'shola-jawid' ),
			'footer_topics'     => __( 'پاورقی — موضوعات منتخب', 'shola-jawid' ),
			'footer_site'       => __( 'پاورقی — پیوندهای سایت', 'shola-jawid' ),
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
 * One-time seed for the four popup-menu locations (menu_topics,
 * menu_publications, menu_sections, menu_more): creates and assigns a real
 * `wp_nav_menu()` menu for each, pre-populated with today's v6 default
 * order/content, so Appearance → Menus shows honest, editable menus from
 * the start instead of an empty screen backed by invisible fallback logic.
 * Farhad's explicit call (2026-08-08) over a fallback-only approach — see
 * docs/CHANGELOG.md.
 *
 * Guarded by a persisted per-location flag (`shola_seeded_nav_menus`
 * option) so each location is seeded exactly once — not re-run on every
 * admin page load, and not re-created if an editor later deliberately
 * unassigns/empties the menu at that location. If menu creation fails for
 * a location (e.g. a name collision), the flag is left unset so it's
 * retried on the next admin_init rather than silently marked done.
 *
 * @return void
 */
function shola_maybe_seed_nav_menus() {
	$seeded = get_option( 'shola_seeded_nav_menus', array() );

	$to_seed = array(
		'menu_topics'       => array(
			'name'  => __( 'موضوعات', 'shola-jawid' ),
			'items' => array_map(
				static function ( $slug ) {
					return array(
						'type'     => 'taxonomy',
						'taxonomy' => 'topic',
						'slug'     => $slug,
					);
				},
				// Phase C topic migration (2026-08-24, docs/CHANGELOG.md): nine
				// terms, this exact client-specified order — was six
				// (economy, world, afghanistan, women,
				// international-movement, science-and-art).
				array( 'world', 'afghanistan', 'labor', 'women', 'politics', 'economy', 'science-and-art', 'international-communist-movement', 'afghanistan-left-movement' )
			),
		),
		'menu_publications' => array(
			'name'  => __( 'نشریات', 'shola-jawid' ),
			'items' => array_map(
				static function ( $slug ) {
					return array(
						'type'     => 'taxonomy',
						'taxonomy' => 'publication',
						'slug'     => $slug,
					);
				},
				array( 'shola-jawid', 'a-world-to-win' )
			),
		),
		'menu_sections'     => array(
			'name'  => __( 'بخش‌ها', 'shola-jawid' ),
			'items' => array(
				array(
					'type'  => 'custom',
					'url'   => home_url( '/' ),
					'title' => __( 'صفحهٔ اصلی', 'shola-jawid' ),
				),
				array(
					'type'  => 'custom',
					'url'   => home_url( '/publications/' ),
					'title' => __( 'نشرات', 'shola-jawid' ),
				),
				array(
					'type'  => 'custom',
					'url'   => home_url( '/library/' ),
					'title' => __( 'کتابخانه', 'shola-jawid' ),
				),
				array(
					'type'  => 'custom',
					'url'   => home_url( '/topics/' ),
					'title' => __( 'همهٔ موضوعات', 'shola-jawid' ),
				),
			),
		),
		'menu_more'         => array(
			'name'  => __( 'بیشتر', 'shola-jawid' ),
			'items' => array(
				array(
					'type'  => 'custom',
					'url'   => home_url( '/announcements/' ),
					'title' => __( 'اطلاعیه‌ها', 'shola-jawid' ),
				),
				array(
					'type'  => 'custom',
					'url'   => home_url( '/about/' ),
					'title' => __( 'دربارهٔ ما', 'shola-jawid' ),
				),
				array(
					'type'  => 'custom',
					'url'   => home_url( '/contact/' ),
					'title' => __( 'ارتباط با حزب', 'shola-jawid' ),
				),
				array(
					'type'  => 'custom',
					'url'   => home_url( '/search/' ),
					'title' => __( 'جست‌وجو در آرشیو', 'shola-jawid' ),
				),
			),
		),
	);

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$changed   = false;

	foreach ( $to_seed as $location => $config ) {
		if ( ! empty( $seeded[ $location ] ) ) {
			continue;
		}

		$menu_id = wp_create_nav_menu( $config['name'] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}

		$position = 1;
		foreach ( $config['items'] as $item ) {
			$item_args = array(
				'menu-item-status'   => 'publish',
				'menu-item-position' => $position,
			);

			if ( 'taxonomy' === $item['type'] ) {
				$term = get_term_by( 'slug', $item['slug'], $item['taxonomy'] );
				if ( ! $term ) {
					continue;
				}
				$item_args['menu-item-type']      = 'taxonomy';
				$item_args['menu-item-object']    = $item['taxonomy'];
				$item_args['menu-item-object-id'] = $term->term_id;
				$item_args['menu-item-title']     = $term->name;
			} else {
				$item_args['menu-item-type']  = 'custom';
				$item_args['menu-item-title'] = $item['title'];
				$item_args['menu-item-url']   = $item['url'];
			}

			wp_update_nav_menu_item( $menu_id, 0, $item_args );
			++$position;
		}

		$locations[ $location ] = $menu_id;
		$seeded[ $location ]    = true;
		$changed                = true;
	}

	if ( $changed ) {
		set_theme_mod( 'nav_menu_locations', $locations );
		update_option( 'shola_seeded_nav_menus', $seeded );
	}
}
add_action( 'admin_init', 'shola_maybe_seed_nav_menus' );

/**
 * Keeps the Topics/Publications/Collections taxonomy panels always
 * available in the Appearance → Menus "Add menu items" sidebar.
 *
 * Found by Farhad (2026-08-08): the panels didn't appear at all, which
 * looked like a taxonomy-registration bug — but `get_taxonomy('topic')`
 * confirmed `show_in_nav_menus` was already correctly `true` (verified
 * directly against the site's database; see docs/CHANGELOG.md for the
 * full investigation). The actual cause was a saved per-user Screen
 * Options preference: his account's `metaboxhidden_nav-menus` user meta
 * already listed `add-topic`/`add-publication`/`add-collection` as
 * hidden. WP core's `default_hidden_meta_boxes` filter only runs when a
 * user has *no* saved preference for the screen yet (see
 * `get_hidden_meta_boxes()` in wp-admin/includes/screen.php) — since his
 * account already had one, that filter would never have reached him.
 * `hidden_meta_boxes` runs unconditionally regardless of any saved
 * preference, so it's used here instead, guaranteeing these three panels
 * stay visible for every current and future admin/editor account without
 * needing to touch anyone's stored usermeta directly.
 *
 * Trade-off worth knowing: this makes the three panels permanently
 * un-hideable via Screen Options for anyone. Considered correct for
 * taxonomies this central to the site's editorial workflow (topics and
 * publications must be addable to a menu without a developer, per the IA
 * doc), rather than something to leave as a fragile per-user setting.
 *
 * @param string[]   $hidden Meta box IDs currently hidden on this screen.
 * @param \WP_Screen $screen Current screen object.
 * @return string[]
 */
function shola_always_show_taxonomy_nav_menu_panels( $hidden, $screen ) {
	if ( 'nav-menus' !== $screen->id ) {
		return $hidden;
	}
	return array_diff( $hidden, array( 'add-topic', 'add-publication', 'add-collection' ) );
}
add_filter( 'hidden_meta_boxes', 'shola_always_show_taxonomy_nav_menu_panels', 10, 2 );

/**
 * Light-touch admin footer credit line, per CLAUDE.md §7.6.
 *
 * @param string $text Default admin footer text.
 * @return string
 */
function shola_admin_footer_text( $text ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- required by the admin_footer_text filter signature; this callback replaces the text entirely rather than modifying it.
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
