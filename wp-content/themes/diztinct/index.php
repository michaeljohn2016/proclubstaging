<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Pro Clubs Theme
 */

get_header(); ?>
<?php
if ( is_page( 'condition-guide' ) ) :
    $title = get_field('title');
    $subtitle = get_field('banner_subtitle1');
    $mobile_image = get_field('image_for_mobile');
?>
    <div class="page-banner">
        <div class="page-banner__content">
            <?php if ( $title ) : ?>
                <h1 class="page-banner__title"><?php echo esc_html($title); ?></h1>
            <?php endif; ?>
            <?php if ( $subtitle ) : ?>
                <p class="page-banner__description"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
        </div>

        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail('full', ['alt' => get_the_title()]); ?>
        <?php endif; ?>

        <?php if ( is_array($mobile_image) && isset($mobile_image['url']) ) : ?>
            <div class="page-banner__image mobile-only">
                <img src="<?php echo esc_url($mobile_image['url']); ?>" alt="<?php echo esc_attr($mobile_image['alt']); ?>" />
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>


	<main id="main" class="container site-main ">
		

		<?php
		if ( have_posts() ) :
			if ( is_home() && ! is_front_page() ) :
				?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>

				<?php
			endif;

			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/**
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_format() );

			endwhile;

			wds_proclubs_display_numeric_pagination();

		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>

	</main><!-- #main -->

<?php get_footer(); ?>
