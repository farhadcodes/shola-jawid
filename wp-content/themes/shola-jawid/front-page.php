<?php
/**
 * Template: front-page.php — homepage.
 *
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-index.html
 * (Phase 4.2): hero, latest grid (articles + the confirmed document-in-
 * card exception, see docs/CHANGELOG.md 2026-08-06), current issue,
 * topics table, latest documents, announcements, newsletter band.
 * Zero inline style="" attributes — all replaced with classes already in
 * assets/css/main.css or added during this conversion (see the "WP
 * conversion (Phase 4.2)" comments in that file).
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Latest across articles + documents (the confirmed mixed-stream case),
// newest first. First result is the hero if it's an article (v6 only
// ever shows an article in hero position); the rest fill the grid.
$latest_query = new WP_Query(
	array(
		'post_type'      => array( 'post', 'document' ),
		'posts_per_page' => 7,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
$latest_posts = $latest_query->posts;

$hero = null;
foreach ( $latest_posts as $i => $p ) {
	if ( 'post' === $p->post_type ) {
		$hero = $p;
		unset( $latest_posts[ $i ] );
		break;
	}
}
$latest_posts = array_slice( array_values( $latest_posts ), 0, 6 );
?>

<?php if ( $hero ) : ?>
	<section class="hero-lead" aria-label="<?php esc_attr_e( 'مقالهٔ سرخط', 'shola-jawid' ); ?>">
		<a href="<?php echo esc_url( get_permalink( $hero ) ); ?>" class="hero-media" aria-hidden="true">
			<?php if ( has_post_thumbnail( $hero ) ) : ?>
				<?php echo get_the_post_thumbnail( $hero, 'shola_hero_wide', array( 'loading' => 'eager' ) ); ?>
			<?php endif; ?>
		</a>
		<div class="wrap">
			<div class="hero-body">
				<?php
				$hero_terms = get_the_terms( $hero, 'topic' );
				$hero_term  = ( $hero_terms && ! is_wp_error( $hero_terms ) ) ? array_shift( $hero_terms ) : false;
				?>
				<p class="type-label">
					<svg class="glyph" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M2 2h9a3 3 0 0 1 3 3v9H5a3 3 0 0 1-3-3V2Zm1 1v8a2 2 0 0 0 2 2h8V5a2 2 0 0 0-2-2H3Z"/></svg>
					<span><?php echo has_post_format( 'aside', $hero ) ? esc_html__( 'یادداشت', 'shola-jawid' ) : esc_html__( 'مقاله', 'shola-jawid' ); ?></span>
					<?php if ( $hero_term ) : ?>
						<span class="divider">/</span>
						<a href="<?php echo esc_url( get_term_link( $hero_term ) ); ?>"><?php echo esc_html( $hero_term->name ); ?></a>
					<?php endif; ?>
				</p>
				<h1 class="h-display">
					<a href="<?php echo esc_url( get_permalink( $hero ) ); ?>"><?php echo esc_html( get_the_title( $hero ) ); ?></a>
				</h1>
				<p class="dek"><?php echo esc_html( wp_trim_words( get_the_excerpt( $hero ), 34 ) ); ?></p>
				<?php
				$hero_byline_meta = get_post_meta( $hero->ID, 'shcore_byline', true );
				$hero_byline      = $hero_byline_meta ? $hero_byline_meta : get_the_author_meta( 'display_name', $hero->post_author );
				?>
				<p class="card-byline"><?php echo esc_html( $hero_byline ); ?> · <time datetime="<?php echo esc_attr( get_the_date( 'c', $hero ) ); ?>"><?php echo esc_html( get_the_date( '', $hero ) ); ?></time></p>
			</div>
		</div>
	</section>

	<hr class="rule wrap">
<?php endif; ?>

<?php if ( $latest_posts ) : ?>
	<section class="wrap sect" aria-label="<?php esc_attr_e( 'تازه‌ترین مقالات', 'shola-jawid' ); ?>">
		<div class="section-head row-between">
			<div>
				<p class="section-marker" lang="en">Latest</p>
				<h2 class="h-section"><?php esc_html_e( 'تازه‌ترین', 'shola-jawid' ); ?></h2>
			</div>
			<a class="link-more" href="<?php echo esc_url( home_url( '/topics/' ) ); ?>"><?php esc_html_e( 'همهٔ موضوعات', 'shola-jawid' ); ?> <span class="arr">←</span></a>
		</div>
		<div class="grid-cards">
			<?php
			foreach ( $latest_posts as $p ) {
				get_template_part(
					'template-parts/cards/card',
					null,
					array(
						'post' => $p,
						'type' => 'document' === $p->post_type ? 'document' : 'article',
					)
				);
			}
			?>
		</div>
	</section>
<?php endif; ?>

<?php
$current_issue_query = new WP_Query(
	array(
		'post_type'      => 'issue',
		'posts_per_page' => 1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
$current_issue = $current_issue_query->have_posts() ? $current_issue_query->posts[0] : null;
?>
<?php if ( $current_issue ) : ?>
	<section class="sect-cream sect" aria-label="<?php esc_attr_e( 'شمارهٔ جاری و کتابخانه', 'shola-jawid' ); ?>">
		<div class="wrap">
			<div class="section-head row-between">
				<div>
					<p class="section-marker" lang="en">Current Issue</p>
					<h2 class="h-section"><?php esc_html_e( 'شمارهٔ جاری', 'shola-jawid' ); ?></h2>
				</div>
				<?php
				$issue_pub_terms = get_the_terms( $current_issue, 'publication' );
				$issue_pub_term  = ( $issue_pub_terms && ! is_wp_error( $issue_pub_terms ) ) ? array_shift( $issue_pub_terms ) : false;
				?>
				<?php if ( $issue_pub_term ) : ?>
					<a class="link-more" href="<?php echo esc_url( get_term_link( $issue_pub_term ) ); ?>"><?php esc_html_e( 'آرشیو شماره‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
				<?php endif; ?>
			</div>

			<div class="issue-lead">
				<div class="issue-hero issue-hero--embedded">

					<a href="<?php echo esc_url( get_permalink( $current_issue ) ); ?>" class="issue-cover reveal">
						<?php if ( has_post_thumbnail( $current_issue ) ) : ?>
							<?php echo get_the_post_thumbnail( $current_issue, 'shola_issue_cover', array( 'loading' => 'lazy' ) ); ?>
						<?php endif; ?>
					</a>

					<div class="reveal">
						<?php $issue_number = get_post_meta( $current_issue->ID, 'shcore_issue_number', true ); ?>
						<p class="badge-current">
							<?php
							if ( $issue_number ) {
								/* translators: %s: issue number. */
								printf( esc_html__( 'شمارهٔ %s · جاری', 'shola-jawid' ), esc_html( $issue_number ) );
							} else {
								esc_html_e( 'جاری', 'shola-jawid' );
							}
							?>
						</p>
						<h3 class="h-page mt-sm"><a href="<?php echo esc_url( get_permalink( $current_issue ) ); ?>" class="link-quiet"><?php echo esc_html( ( $issue_pub_term ? $issue_pub_term->name . ' · ' : '' ) . ( $issue_number ? sprintf( __( 'شمارهٔ %s', 'shola-jawid' ), $issue_number ) : get_the_title( $current_issue ) ) ); ?></a></h3>
						<p class="dek mt-sm"><?php echo esc_html( wp_trim_words( get_the_excerpt( $current_issue ), 30 ) ); ?></p>
						<dl class="issue-meta">
							<dt><?php esc_html_e( 'تاریخ نشر', 'shola-jawid' ); ?></dt>
							<dd><time datetime="<?php echo esc_attr( get_the_date( 'c', $current_issue ) ); ?>"><?php echo esc_html( get_the_date( '', $current_issue ) ); ?></time></dd>
							<?php $volume = get_post_meta( $current_issue->ID, 'shcore_volume', true ); ?>
							<?php if ( $volume ) : ?>
								<dt><?php esc_html_e( 'دوره / جلد', 'shola-jawid' ); ?></dt>
								<dd><?php echo esc_html( $volume ); ?></dd>
							<?php endif; ?>
							<?php
							$pdf_id = (int) get_post_meta( $current_issue->ID, 'shcore_pdf_id', true );
							if ( $pdf_id ) :
								$pdf_file = get_attached_file( $pdf_id );
								$pdf_size = $pdf_file && file_exists( $pdf_file ) ? size_format( filesize( $pdf_file ) ) : '';
								?>
								<dt><?php esc_html_e( 'فایل PDF', 'shola-jawid' ); ?></dt>
								<dd lang="en"><?php echo esc_html( $pdf_size ? $pdf_size : 'PDF' ); ?></dd>
							<?php endif; ?>
						</dl>
						<div class="row mt-sm">
							<a class="btn btn-primary" href="<?php echo esc_url( get_permalink( $current_issue ) ); ?>"><?php esc_html_e( 'دریافت شماره', 'shola-jawid' ); ?></a>
							<?php if ( $issue_pub_term ) : ?>
								<a class="btn btn-ghost" href="<?php echo esc_url( get_term_link( $issue_pub_term ) ); ?>"><?php esc_html_e( 'آرشیو شماره‌ها', 'shola-jawid' ); ?></a>
							<?php endif; ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="wrap sect" aria-label="<?php esc_attr_e( 'موضوعات', 'shola-jawid' ); ?>">
	<div class="section-head center">
		<p class="section-marker" lang="en">Topics</p>
		<h2 class="h-section"><?php esc_html_e( 'همهٔ موضوعات', 'shola-jawid' ); ?></h2>
	</div>
	<ul class="topic-list">
		<?php foreach ( shola_get_topic_slugs_ordered() as $slug ) : ?>
			<?php
			$term = get_term_by( 'slug', $slug, 'topic' );
			if ( ! $term ) {
				continue;
			}
			?>
			<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
				<span class="name"><?php echo esc_html( $term->name ); ?></span>
				<?php /* translators: %s: number of articles. */ ?>
				<span class="count"><?php echo esc_html( sprintf( _n( '%s مقاله', '%s مقاله', $term->count, 'shola-jawid' ), number_format_i18n( $term->count ) ) ); ?></span></a></li>
		<?php endforeach; ?>
	</ul>
</section>

<?php
$documents_query = new WP_Query(
	array(
		'post_type'      => 'document',
		'posts_per_page' => 4,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<?php if ( $documents_query->have_posts() ) : ?>
	<section class="wrap sect" aria-label="<?php esc_attr_e( 'کتابخانه', 'shola-jawid' ); ?>">
		<div class="section-head row-between">
			<div>
				<p class="section-marker" lang="en">Library</p>
				<h2 class="h-section"><?php esc_html_e( 'تازه‌ترین اسناد', 'shola-jawid' ); ?></h2>
			</div>
			<a class="link-more" href="<?php echo esc_url( home_url( '/library/' ) ); ?>"><?php esc_html_e( 'همهٔ مجموعه‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
		</div>
		<ul>
			<?php
			while ( $documents_query->have_posts() ) :
				$documents_query->the_post();
				$doc         = get_post();
				$doc_terms   = get_the_terms( $doc, 'collection' );
				$doc_term    = ( $doc_terms && ! is_wp_error( $doc_terms ) ) ? array_shift( $doc_terms ) : false;
				$doc_pdf_id  = (int) get_post_meta( $doc->ID, 'shcore_pdf_id', true );
				$doc_pdf_sz  = '';
				if ( $doc_pdf_id ) {
					$doc_pdf_file = get_attached_file( $doc_pdf_id );
					$doc_pdf_sz   = $doc_pdf_file && file_exists( $doc_pdf_file ) ? size_format( filesize( $doc_pdf_file ) ) : '';
				}
				?>
				<li class="doc-row reveal">
					<div class="doc-body">
						<a href="<?php the_permalink(); ?>" class="link-quiet"><?php the_title(); ?></a>
						<p class="doc-meta">
							<?php echo $doc_term ? esc_html( $doc_term->name ) : ''; ?>
							<?php if ( $doc_pdf_sz ) : ?>
								· <span class="meta-mono" lang="en">PDF · <?php echo esc_html( $doc_pdf_sz ); ?></span>
							<?php endif; ?>
						</p>
					</div>
					<a class="btn btn-ghost btn-sm" href="<?php the_permalink(); ?>"><?php esc_html_e( 'دریافت', 'shola-jawid' ); ?></a>
				</li>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</ul>
	</section>
<?php endif; ?>

<?php
$announcements_query = new WP_Query(
	array(
		'post_type'      => 'announcement',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<?php if ( $announcements_query->have_posts() ) : ?>
	<section class="wrap sect" aria-label="<?php esc_attr_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?>">
		<div class="section-head row-between">
			<div>
				<p class="section-marker" lang="en">Announcements</p>
				<h2 class="h-section"><?php esc_html_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?></h2>
			</div>
			<a class="link-more" href="<?php echo esc_url( home_url( '/announcements/' ) ); ?>"><?php esc_html_e( 'همهٔ اطلاعیه‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
		</div>
		<ul class="announce-list">
			<?php
			while ( $announcements_query->have_posts() ) :
				$announcements_query->the_post();
				?>
				<li>
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</li>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</ul>
	</section>
<?php endif; ?>

<section class="newsletter" aria-label="<?php esc_attr_e( 'خبرنامه', 'shola-jawid' ); ?>">
	<div class="wrap newsletter-inner">
		<div>
			<p class="section-marker" lang="en">Newsletter</p>
			<h2 class="h-section"><?php esc_html_e( 'هر شمارهٔ تازه، در صندوق شما', 'shola-jawid' ); ?></h2>
			<p class="dek"><?php esc_html_e( 'اعلان انتشار شماره‌های تازه و مقالات برگزیده؛ ماهی یک بار، بدون هرزنامه؛ لغو اشتراک با یک کلیک.', 'shola-jawid' ); ?></p>
		</div>
		<form action="#" method="post" aria-label="<?php esc_attr_e( 'عضویت در خبرنامه', 'shola-jawid' ); ?>">
			<label class="label" for="nl-email"><?php esc_html_e( 'نشانی ایمیل', 'shola-jawid' ); ?></label>
			<div class="newsletter-form">
				<input class="field" type="email" id="nl-email" name="email" dir="ltr" placeholder="you@example.com" autocomplete="email" required>
				<button class="btn btn-primary" type="submit"><?php esc_html_e( 'عضویت', 'shola-jawid' ); ?></button>
			</div>
		</form>
	</div>
</section>

<?php get_footer(); ?>
