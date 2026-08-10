<?php
// Template: footer.php — footer + </main> + <head> close.
// Converted from 03_UI_Design/shola-jawid-ui/pages/_footer.html + the
// closing half of _shell.html (Phase 4.1). See header.php's docblock for
// the faithful-port notes shared by both.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<footer class="footer">
	<div class="wrap">
		<div class="footer-grid">

			<div>
				<p class="footer-name"><?php bloginfo( 'name' ); ?></p>
				<p class="footer-tagline">
					<?php esc_html_e( 'پلتفرم نشر دوزبانه برای مقالات، یادداشت‌ها و اسناد؛ با آرشیو کامل نشرات «شعله جاوید» و «جهان برای فتح».', 'shola-jawid' ); ?>
				</p>
				<ul class="footer-social" aria-label="<?php esc_attr_e( 'شبکه‌های اجتماعی', 'shola-jawid' ); ?>">
					<?php foreach ( shola_get_social_links() as $social ) : ?>
						<li><a href="<?php echo esc_url( $social['url'] ); ?>" aria-label="<?php echo esc_attr( $social['label'] ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><?php echo $social['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, trusted inline SVG defined in shola_get_social_links(), not user input. ?></svg></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<nav class="footer-col" aria-label="<?php esc_attr_e( 'نشرات', 'shola-jawid' ); ?>">
				<h3><?php esc_html_e( 'نشرات', 'shola-jawid' ); ?></h3>
				<ul>
					<?php foreach ( shola_get_publication_slugs_ordered() as $slug ) : ?>
						<?php
						$term = get_term_by( 'slug', $slug, 'publication' );
						if ( ! $term ) {
							continue;
						}
						?>
						<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
					<?php endforeach; ?>
					<li><a href="<?php echo esc_url( home_url( '/publications/' ) ); ?>"><?php esc_html_e( 'همهٔ شماره‌ها', 'shola-jawid' ); ?></a></li>
				</ul>
			</nav>

			<nav class="footer-col" aria-label="<?php echo esc_attr( shola_get_label( 'nav_topics_label' ) ); ?>">
				<h3><?php echo esc_html( shola_get_label( 'nav_topics_label' ) ); ?></h3>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer_topics',
						'container'      => false,
						'items_wrap'     => '<ul>%3$s</ul>',
						'fallback_cb'    => 'shola_fallback_footer_topics',
					)
				);
				?>
			</nav>

			<nav class="footer-col" aria-label="<?php esc_attr_e( 'پیوندهای سایت', 'shola-jawid' ); ?>">
				<h3><?php esc_html_e( 'سایت', 'shola-jawid' ); ?></h3>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer_site',
						'container'      => false,
						'items_wrap'     => '<ul>%3$s</ul>',
						'fallback_cb'    => 'shola_fallback_footer_site',
					)
				);
				?>
			</nav>

		</div>

		<div class="footer-base">
			<p><?php echo esc_html( sprintf( /* translators: %s: site name. */ __( '© ۱۴۰۵ · %s · بازنشر با ذکر منبع آزاد است', 'shola-jawid' ), get_bloginfo( 'name' ) ) ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
