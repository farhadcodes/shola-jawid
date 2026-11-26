<?php
/**
 * Template: page-topics.php — موضوعات (Topics listing).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-topics.html
 * (Phase 4.2). Applies to the Page with slug `topics`.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
// Language-aware term name/link/count, not the raw Persian-only values.
$shola_lang = \SholaCore\Language::current();
?>
	<section class="wrap section-top">

		<?php
		get_template_part(
			'template-parts/breadcrumb',
			null,
			array(
				'items' => array(
					array(
						'label' => shola_get_label( 'breadcrumb_topics_label' ),
						'url'   => ( 'en' === $shola_lang ) ? home_url( '/en/topics/' ) : home_url( '/topics/' ),
					),
				),
			)
		);
		?>

		<header class="page-header page-header--narrow">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php echo esc_html( shola_get_label( 'topics_page_title' ) ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'شش موضوع ثابت؛ چهارچوبی که نشریه پیرامون آن مقاله سفارش می‌دهد و مقاله می‌پذیرد.', 'shola-jawid' ); ?></p>
		</header>

		<ul class="topic-list">
			<?php foreach ( shola_get_topic_slugs_ordered() as $slug ) : ?>
				<?php
				$term = get_term_by( 'slug', $slug, 'topic' );
				if ( ! $term ) {
					continue;
				}
				$term_link  = shola_build_term_link_for_lang( $term, $shola_lang );
				$term_link  = $term_link ? $term_link : get_term_link( $term );
				$term_count = shola_get_term_post_count( $term, $shola_lang );
				?>
				<li><a href="<?php echo esc_url( $term_link ); ?>">
					<span class="name"><?php echo esc_html( shola_get_term_label( $term, $shola_lang ) ); ?></span>
					<?php if ( 'en' === $shola_lang ) : ?>
						<?php /* translators: %s: article count. */ ?>
						<span class="count"><?php echo esc_html( sprintf( _n( '%s article', '%s articles', $term_count, 'shola-jawid' ), number_format_i18n( $term_count ) ) ); ?></span></a></li>
					<?php else : ?>
						<?php /* translators: %s: article count. */ ?>
						<span class="count"><?php echo esc_html( sprintf( _n( '%s مقاله', '%s مقاله', $term_count, 'shola-jawid' ), shola_to_persian_digits( $term_count ) ) ); ?></span></a></li>
					<?php endif; ?>
			<?php endforeach; ?>
		</ul>

	</section>
<?php
get_footer();
