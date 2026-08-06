<?php
// Template: index.php — required WP-core fallback template (theme is
// "broken" without it, independent of the template hierarchy). Real
// front-page/single/archive templates land per-page through the rest of
// Phase 4; this stays a bare fallback loop, matching v6's {{BODY}} slot
// with no dedicated design of its own.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
	<div class="wrap sect">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				the_title( '<h1 class="h-page">', '</h1>' );
				the_content();
			endwhile;
		endif;
		?>
	</div>
<?php
get_footer();
