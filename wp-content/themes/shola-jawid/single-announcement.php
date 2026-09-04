<?php
/**
 * Template: single-announcement.php — single اطلاعیه detail view.
 * Applies to the `announcement` CPT (shola-core\Post_Types). Added
 * 2026-09-04, reversing the earlier "list-only, permanently" decision
 * (see archive-announcement.php's own docblock for that history) per
 * Farhad relaying an explicit client request: clicking an announcement
 * should open its full text on its own page, not just read it inside the
 * list.
 *
 * `announcement` supports only `title`/`editor` (no thumbnail, no
 * excerpt) — deliberately the simplest single-view template on the site:
 * a breadcrumb, the title and date, the full body text, and a short list
 * of other announcements so this never dead-ends a visitor. No fields
 * changed to add this; only the template was missing.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$related_query = new WP_Query(
		array(
			'post_type'      => 'announcement',
			'posts_per_page' => 3,
			'post__not_in'   => array( get_the_ID() ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	?>

	<section class="wrap section-top">

		<nav class="article-crumb" aria-label="<?php esc_attr_e( 'مسیر', 'shola-jawid' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a class="active" href="<?php echo esc_url( home_url( '/announcements/' ) ); ?>"><?php esc_html_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?></a>
		</nav>

		<header class="page-header page-header--narrow mt-sm">
			<h1 class="article-title"><?php the_title(); ?></h1>
			<p class="card-byline mt-sm"><time datetime="<?php echo esc_attr( shola_get_iso_datetime() ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
		</header>

		<div class="wrap-read prose">
			<?php the_content(); ?>
		</div>

		<?php if ( $related_query->have_posts() ) : ?>
			<div class="kicker-row mt-lg">
				<p class="section-marker"></p>
				<h2 class="h-section"><?php esc_html_e( 'سایر اطلاعیه‌ها', 'shola-jawid' ); ?></h2>
			</div>
			<ul class="announce-list announce-list--page">
				<?php
				while ( $related_query->have_posts() ) :
					$related_query->the_post();
					?>
					<li>
						<time datetime="<?php echo esc_attr( shola_get_iso_datetime() ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						<div>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<p class="meta mt-sm"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>

	</section>
	<?php
endwhile;

get_footer();
