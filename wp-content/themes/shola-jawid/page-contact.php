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
			<p class="section-marker" lang="en">Contact</p>
			<h1 class="h-page"><?php esc_html_e( 'ارتباط با حزب', 'shola-jawid' ); ?></h1>
			<p class="dek"><?php esc_html_e( 'پیشنهاد مقاله، پرسش‌های تحریری، همکاری ترجمه یا نکته‌ای دربارهٔ سایت — از هر مسیر که برایتان راحت‌تر است.', 'shola-jawid' ); ?></p>
		</header>

		<div class="contact-grid">
			<div class="contact-inner">

				<?php echo do_shortcode( '[contact-form-7 id="71"]' ); ?>

				<aside class="contact-aside">
					<p class="meta-mono" lang="en">EMAIL</p>
					<p class="contact-aside-value"><a class="link" href="mailto:info.sholajawid@gmail.com" dir="ltr">info.sholajawid@gmail.com</a></p>
					<p class="meta-mono" lang="en">RESPONSE TIME</p>
					<p class="contact-aside-value"><?php esc_html_e( 'پیام‌ها معمولاً ظرف یک هفته پاسخ داده می‌شوند.', 'shola-jawid' ); ?></p>
					<p class="meta-mono" lang="en">PRIVACY</p>
					<p>
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: %s: privacy policy link (placeholder, not yet a real destination). */
								__( 'نشانی ایمیل شما فقط برای پاسخگویی استفاده می‌شود؛ در پایگاه داده‌ای برای بازاریابی نگهداری نمی‌شود. جزئیات در %s.', 'shola-jawid' ),
								'<a class="link" href="#">' . esc_html__( 'سیاست حریم خصوصی', 'shola-jawid' ) . '</a>'
							)
						);
						?>
					</p>
				</aside>

			</div>
		</div>

	</section>
<?php
get_footer();
