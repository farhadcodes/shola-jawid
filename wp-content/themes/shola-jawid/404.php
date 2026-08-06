<?php
/**
 * Template: 404.php — page-not-found error state.
 *
 * No v6 mockup exists for this state (checked: no 404/error file
 * anywhere in 03_UI_Design/shola-jawid-ui/pages or the top-level page
 * list). Per EXECUTION_PLAN.md's own instruction for this row ("Match
 * v6 error-state design if one exists, else brand-consistent minimal
 * page"), this is assembled from existing components/tokens only — no
 * new visual design invented.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
	<section class="wrap section-top error-404">

		<p class="section-marker" lang="en">Error</p>
		<h1 class="h-display">۴۰۴</h1>
		<p class="dek"><?php esc_html_e( 'صفحه‌ای که به دنبال آن هستید یافت نشد. ممکن است نشانی اشتباه باشد یا این صفحه جابه‌جا یا حذف شده باشد.', 'shola-jawid' ); ?></p>

		<div class="error-404-actions">
			<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'بازگشت به صفحهٔ اصلی', 'shola-jawid' ); ?></a>
			<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>"><?php esc_html_e( 'جست‌وجو در سایت', 'shola-jawid' ); ?></a>
		</div>

	</section>
<?php
get_footer();
