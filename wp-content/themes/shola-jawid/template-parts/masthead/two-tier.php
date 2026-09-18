<?php
/**
 * Template part: template-parts/masthead/two-tier.php — the `two-tier`
 * masthead_section layout, rebuilt a second time 2026-09-18 after live
 * review found the previous single-row pass still didn't match the
 * reference: the identity cluster is a *vertical* stack (title, then
 * date beneath it, then a small flag beneath both) on the right, not a
 * horizontal title-beside-flag pair, and it sits on the right — the
 * utility nav is on the left, running menu-icon first through to the
 * search icon at the far outer edge. Single linear-gradient
 * (main.css, .masthead--two-tier) still spans the header's full width:
 * red (--winston-red/-deep) behind the identity stack on the right,
 * dark (--ink) behind the utility nav on the left.
 *
 * Every individual element here is the same markup/function the other
 * masthead layouts already use: shola_get_masthead_runner() for the
 * date, get_theme_mod( 'custom_logo' ) for the flag/logo image,
 * #menu-open/#menu-panel wiring (main.js keys off the ID, not DOM
 * position), and the same six utility/pub-nav links + search-icon
 * markup header.php's default row already renders.
 *
 * Flag treatment corrected 2026-09-18 (third pass): per Farhad's live
 * review, the flag is a large, tilted background graphic sitting
 * behind the title/date (not a small third stacked line beneath them)
 * — sized via main.css (.mast-two-tier-flag), absolutely positioned
 * and rotated, clipped to the header's own box (overflow:hidden on
 * .masthead--two-tier) so the oversized/rotated graphic never spills
 * onto the page below. It renders first in the markup, before the
 * brand link, so the brand's own normal-flow paint order sits visually
 * on top of it with no z-index needed. No separate background/wrapper
 * behind the flag itself — the source file is genuinely transparent
 * (verified live via canvas pixel-alpha reads, all four corners
 * alpha:0), so it needs nothing behind it but this file's single
 * shared gradient.
 *
 * .mast-two-tier-flag-glow (2026-09-18, corrected same round): a
 * radial-gradient "pop" halo sitting *behind the flag* — Farhad's
 * explicit correction after an earlier pass mistakenly put a dark
 * scrim behind the text instead. Same technique the `logo-radial`
 * masthead layout already uses behind its own flag, repositioned to
 * this flag's off-center, tilted placement. aria-hidden: purely
 * decorative, carries no content of its own.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shola_two_tier_logo_id = get_theme_mod( 'custom_logo' );
?>
<div class="wrap mast-two-tier-inner">

	<div class="mast-two-tier-identity">
		<?php if ( $shola_two_tier_logo_id ) : ?>
			<?php
			echo wp_get_attachment_image(
				$shola_two_tier_logo_id,
				'full',
				false,
				array(
					'class'   => 'mast-logo mast-two-tier-flag',
					'loading' => 'eager',
					'alt'     => '',
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() escapes internally.
			?>
		<?php endif; ?>
		<span class="mast-two-tier-flag-glow" aria-hidden="true"></span>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — ' . __( 'صفحهٔ اصلی', 'shola-jawid' ) ); ?>" class="mast-two-tier-brand">
			<span class="mast-nameplate"><?php bloginfo( 'name' ); ?></span>
			<span class="mast-runner" lang="en"><?php echo esc_html( shola_get_masthead_runner() ); ?></span>
		</a>
	</div>

	<div class="mast-two-tier-utility">
		<button type="button" id="menu-open" class="mast-btn" aria-expanded="false" aria-controls="menu-panel" aria-label="<?php esc_attr_e( 'باز کردن منو', 'shola-jawid' ); ?>">
			<svg width="22" height="14" viewBox="0 0 16 10" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M0 1h16M0 5h16M0 9h16"/></svg>
			<span><?php esc_html_e( 'منو', 'shola-jawid' ); ?></span>
		</button>
		<nav class="mast-two-tier-util-nav" aria-label="<?php esc_attr_e( 'پیوندهای کمکی', 'shola-jawid' ); ?>">
			<a href="<?php echo esc_url( home_url( '/publications/' ) ); ?>" class="mast-btn"><?php esc_html_e( 'نشریات', 'shola-jawid' ); ?></a>
			<span aria-hidden="true" class="mast-slash-light">/</span>
			<a href="<?php echo esc_url( home_url( '/topics/' ) ); ?>" class="mast-btn"><?php esc_html_e( 'موضوعات', 'shola-jawid' ); ?></a>
			<span aria-hidden="true" class="mast-slash-light">/</span>
			<a href="<?php echo esc_url( home_url( '/library/' ) ); ?>" class="mast-btn"><?php esc_html_e( 'کتابخانه', 'shola-jawid' ); ?></a>
			<span aria-hidden="true" class="mast-slash-light">/</span>
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="mast-btn"><?php esc_html_e( 'دربارهٔ ما', 'shola-jawid' ); ?></a>
			<span aria-hidden="true" class="mast-slash-light">/</span>
			<a href="<?php echo esc_url( home_url( '/announcements/' ) ); ?>" class="mast-btn"><?php esc_html_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?></a>
			<span aria-hidden="true" class="mast-slash-light">/</span>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="mast-btn"><?php esc_html_e( 'تماس', 'shola-jawid' ); ?></a>
		</nav>
		<?php
		/*
		 * Redesigned proportions 2026-09-18: a bigger lens circle (r=8, was
		 * 7) with a shorter handle (was a long ~6-unit stroke, now ~4.2) —
		 * Farhad's explicit ask for a more "professional" magnifier shape,
		 * not just a scaled-up copy of the same proportions used
		 * elsewhere. Actual rendered size is set in main.css
		 * (.masthead--two-tier .mast-icon-link svg), not the width/height
		 * attributes here — those are only the SVG's own internal
		 * coordinate space.
		 *
		 * pathLength="1" on both shapes (added same day, hover-effect
		 * round): normalizes the circle and the short handle path to the
		 * same 0-1 dash unit regardless of their actual geometric length,
		 * so main.css's stroke-dasharray/dashoffset line-draw hover
		 * animation doesn't need hand-computed pixel lengths for either.
		 */
		?>
		<a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="link-quiet mast-icon-link" aria-label="<?php esc_attr_e( 'جست‌وجو', 'shola-jawid' ); ?>">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" aria-hidden="true"><circle cx="9.5" cy="9.5" r="8" pathLength="1"/><path d="m16 16 3 3" pathLength="1"/></svg>
		</a>
	</div>

</div>
