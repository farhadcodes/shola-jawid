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
					<li><a href="#" aria-label="<?php esc_attr_e( 'تلگرام', 'shola-jawid' ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.9 4.6 18.8 19c-.2 1-.8 1.2-1.7.8l-4.6-3.4-2.2 2.1c-.3.3-.5.5-.9.5l.3-4.6L18 6.9c.4-.3-.1-.5-.5-.2L7 13.2l-4.4-1.4c-1-.3-1-1 .2-1.4L20.6 3.2c.8-.3 1.5.2 1.3 1.4Z"/></svg></a></li>
					<li><a href="#" aria-label="<?php esc_attr_e( 'ایکس', 'shola-jawid' ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.8 3h3l-6.7 7.7L22 21h-6.2l-4.8-6.3L5.4 21h-3l7.2-8.2L2 3h6.3l4.4 5.8L17.8 3Zm-1.1 16.2h1.7L7.3 4.7H5.5l11.2 14.5Z"/></svg></a></li>
					<li><a href="#" aria-label="<?php esc_attr_e( 'خوراک RSS', 'shola-jawid' ); ?>"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 4a16 16 0 0 1 16 16h-3A13 13 0 0 0 4 7V4Zm0 6a10 10 0 0 1 10 10h-3a7 7 0 0 0-7-7v-3Zm2 6a2 2 0 1 1 0 4 2 2 0 0 1 0-4Z"/></svg></a></li>
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

			<nav class="footer-col" aria-label="<?php esc_attr_e( 'موضوعات', 'shola-jawid' ); ?>">
				<h3><?php esc_html_e( 'موضوعات', 'shola-jawid' ); ?></h3>
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
			<p><?php echo esc_html( sprintf( __( '© ۱۴۰۵ · %s · بازنشر با ذکر منبع آزاد است', 'shola-jawid' ), get_bloginfo( 'name' ) ) ); ?></p>
			<p lang="en">FA · <a href="#" class="link-quiet">EN</a></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
