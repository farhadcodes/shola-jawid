<?php
/**
 * Template: page-contact.php — ارتباط با حزب (Contact page).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-contact.html
 * (Phase 4.2/4.3). Applies to the Page with slug `contact`.
 *
 * Form submission handled by Contact Form 7 (CLAUDE.md §3 whitelist,
 * form #71) rendered with the theme's own markup/CSS (wpcf7_load_css
 * filtered off in inc/enqueue.php), not CF7's default stylesheet.
 *
 * Contact email is a placeholder pending the real address — see
 * docs/CHANGELOG.md 2026-08-06 for the decision record.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
	<section class="wrap section-top">

		<header class="page-header page-header--narrow">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'ارتباط با حزب', 'shola-jawid' ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'پیشنهاد مقاله، پرسش‌های تحریری، همکاری ترجمه یا نکته‌ای دربارهٔ سایت — از هر مسیر که برایتان راحت‌تر است.', 'shola-jawid' ); ?></p>
		</header>

		<div class="contact-grid">
			<div class="contact-inner">

				<?php echo do_shortcode( '[contact-form-7 id="71"]' ); ?>

				<aside class="contact-aside">
					<p class="meta-mono"><?php esc_html_e( 'ایمیل', 'shola-jawid' ); ?></p>
					<p class="contact-aside-value"><a class="link" href="mailto:info.sholajawid@gmail.com" dir="ltr">info.sholajawid@gmail.com</a></p>
					<p class="meta-mono"><?php esc_html_e( 'زمان پاسخ‌دهی', 'shola-jawid' ); ?></p>
					<p class="contact-aside-value"><?php esc_html_e( 'پیام‌ها معمولاً ظرف یک هفته پاسخ داده می‌شوند.', 'shola-jawid' ); ?></p>
					<p class="meta-mono"><?php esc_html_e( 'حریم خصوصی', 'shola-jawid' ); ?></p>
					<p>
						<?php
						/*
						 * Fixed 2026-09-11: this used to link to a literal "#"
						 * (a placeholder from the 2026-08-06 build, see
						 * docs/CHANGELOG.md — v6's own prototype had no real
						 * privacy page either). Wired to WordPress core's own
						 * Privacy Policy page mechanism instead of inventing
						 * one: if a page is set under Settings → Privacy, link
						 * to it; if not, drop the dangling promise rather than
						 * point at a dead link.
						 */
						$privacy_policy_url = get_privacy_policy_url();
						if ( $privacy_policy_url ) {
							echo wp_kses_post(
								sprintf(
									/* translators: %s: link to the site's Privacy Policy page. */
									__( 'نشانی ایمیل شما فقط برای پاسخگویی استفاده می‌شود؛ در پایگاه داده‌ای برای بازاریابی نگهداری نمی‌شود. جزئیات در %s.', 'shola-jawid' ),
									'<a class="link" href="' . esc_url( $privacy_policy_url ) . '">' . esc_html__( 'سیاست حریم خصوصی', 'shola-jawid' ) . '</a>'
								)
							);
						} else {
							esc_html_e( 'نشانی ایمیل شما فقط برای پاسخگویی استفاده می‌شود؛ در پایگاه داده‌ای برای بازاریابی نگهداری نمی‌شود.', 'shola-jawid' );
						}
						?>
					</p>
				</aside>

			</div>
		</div>

	</section>
<?php
get_footer();
