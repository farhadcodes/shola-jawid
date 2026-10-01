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
 * Contact email is admin-editable (Settings → موضوعات فرم تماس) via
 * SholaCore\Contact_Settings::get_email() — added 2026-10-01, spec-audit
 * gap B2. See docs/CHANGELOG.md 2026-08-06 for the original placeholder
 * decision this replaces.
 *
 * H1 and dek now pull from the real WP Page (spec-audit gap B15,
 * 2026-10-01) — the spec lists عنوان صفحه and توضیحات مختصر زیر عنوان as
 * this page's first two admin-editable parts, same "title field,
 * description field" shape page-about.php's own B13 fix already
 * established. The dek falls back to the same hardcoded sentence this
 * field used to have if the Page's excerpt is left empty, same
 * "un-configured site keeps working" pattern as B2's email default.
 *
 * The response-time + privacy note is now one combined line under one
 * label instead of two separate labeled paragraphs — the spec calls for
 * «توضیحات یک‌خطی دربارهٔ زمان پاسخ‌دهی و حریم خصوصی» (a *one-line* note
 * covering both), not two full paragraphs under two headings.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$shola_contact_page_id = get_queried_object_id();
$shola_contact_dek     = get_the_excerpt( $shola_contact_page_id );
if ( ! $shola_contact_dek ) {
	$shola_contact_dek = __( 'پیشنهاد مقاله، پرسش‌های تحریری، همکاری ترجمه یا نکته‌ای دربارهٔ سایت — از هر مسیر که برایتان راحت‌تر است.', 'shola-jawid' );
}
?>
	<section class="wrap section-top">

		<header class="page-header page-header--narrow">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php echo esc_html( get_the_title( $shola_contact_page_id ) ); ?></h1>
			</div>
			<p class="dek"><?php echo esc_html( $shola_contact_dek ); ?></p>
		</header>

		<div class="contact-grid">
			<div class="contact-inner">

				<?php echo do_shortcode( '[contact-form-7 id="71"]' ); ?>

				<?php
				// shola-core is inactive — degrade to the same real address
				// this field used to hardcode, instead of a dead mailto link.
				$contact_email = class_exists( '\SholaCore\Contact_Settings' )
					? \SholaCore\Contact_Settings::get_email()
					: 'info.sholajawid@gmail.com';
				?>
				<aside class="contact-aside">
					<p class="meta-mono"><?php esc_html_e( 'ایمیل', 'shola-jawid' ); ?></p>
					<p class="contact-aside-value"><a class="link" href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>" dir="ltr"><?php echo esc_html( $contact_email ); ?></a></p>
					<p class="meta-mono"><?php esc_html_e( 'پاسخ‌دهی و حریم خصوصی', 'shola-jawid' ); ?></p>
					<p>
						<?php
						/*
						 * Combined into one line under one label, 2026-10-01
						 * (spec-audit gap B15) — the spec calls for «توضیحات
						 * یک‌خطی دربارهٔ زمان پاسخ‌دهی و حریم خصوصی» (a
						 * *one-line* note covering both topics together), not
						 * two separate full paragraphs each under its own
						 * heading, which is what this used to be.
						 *
						 * Privacy-policy link logic unchanged from the
						 * 2026-09-11 fix: wired to WordPress core's own
						 * Privacy Policy page mechanism (Settings → Privacy)
						 * rather than a placeholder "#" link; if no policy
						 * page is set, the sentence simply doesn't promise one.
						 */
						$privacy_policy_url = get_privacy_policy_url();
						if ( $privacy_policy_url ) {
							echo wp_kses_post(
								sprintf(
									/* translators: %s: link to the site's Privacy Policy page. */
									__( 'پیام‌ها معمولاً ظرف یک هفته پاسخ داده می‌شوند؛ ایمیلتان فقط برای پاسخگویی استفاده می‌شود، نه بازاریابی — جزئیات در %s.', 'shola-jawid' ),
									'<a class="link" href="' . esc_url( $privacy_policy_url ) . '">' . esc_html__( 'سیاست حریم خصوصی', 'shola-jawid' ) . '</a>'
								)
							);
						} else {
							esc_html_e( 'پیام‌ها معمولاً ظرف یک هفته پاسخ داده می‌شوند؛ ایمیلتان فقط برای پاسخگویی استفاده می‌شود، نه بازاریابی.', 'shola-jawid' );
						}
						?>
					</p>
				</aside>

			</div>
		</div>

	</section>
<?php
get_footer();
