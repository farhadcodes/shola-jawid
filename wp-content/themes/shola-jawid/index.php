<?php
// Template: index.php — required WP-core fallback template (theme is
// "broken" without it, independent of the template hierarchy). Minimal and
// unstyled on purpose: real markup/design lands per-template in Phase 4.
// Calls wp_head()/wp_footer() directly rather than get_header()/get_footer()
// because header.php/footer.php don't exist until Phase 4.1 — calling them
// now would trigger a _doing_it_wrong notice under WP_DEBUG.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			the_title( '<h1>', '</h1>' );
			the_content();
		endwhile;
	endif;
	?>
	<?php wp_footer(); ?>
</body>
</html>
