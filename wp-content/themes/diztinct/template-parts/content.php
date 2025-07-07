<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Pro Clubs Theme
 */

if ( is_page( 'condition-guide' ) ) : ?>
   <div class="condition-section">
        <h2 class="condition-section__title">To help determine the cash value of your clubs, ProClubs has created a condition guide with 3 distinct condition categories:</h2>
        <div class="condition-blocks">	
            <?php if( have_rows('blocks') ): ?>
                <?php while( have_rows('blocks') ): the_row(); ?>
                    <div class="condition-block">
                        <h3 class="condition-block__title"><?php the_sub_field('title'); ?></h3>
                        <p class="condition-block__info"><?php the_sub_field('info'); ?></p>
                        <h4 class="condition-block__subtitle">Key Features:</h4>
                        <div class="condition-block__text">
                            <?php the_sub_field('text'); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
		<div class="condition-texts">
			<?php if( have_rows('texts') ): ?>
                <?php while( have_rows('texts') ): the_row(); ?>
                    <p class="condition-text"><?php the_sub_field('text'); ?></p>
                <?php endwhile; ?>
            <?php endif; ?>
		</div>				
   </div>
	
<?php else : ?>
	<article <?php post_class( 'post-container' ); ?>>

		<header class="entry-header">
			<?php
			if ( is_single() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			endif;

			if ( 'post' === get_post_type() ) :
				?>
				<div class="entry-meta">
					<?php wds_proclubs_posted_on(); ?>
				</div><!-- .entry-meta -->
			<?php endif; ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php
				the_content(
					sprintf(
						wp_kses(
							/* translators: %s: Name of current post. */
							esc_html__( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'proclubs' ),
							[
								'span' => [
									'class' => [],
								],
							]
						),
						the_title( '<span class="screen-reader-text">"', '"</span>', false )
					)
				);

				wp_link_pages(
					[
						'before' => '<div class="page-links">' . esc_attr__( 'Pages:', 'proclubs' ),
						'after'  => '</div>',
					]
				);
				?>
		</div><!-- .entry-content -->

		<footer class="entry-footer">
			<?php wds_proclubs_entry_footer(); ?>
		</footer><!-- .entry-footer -->

	</article><!-- #post-## -->
<?php endif; ?>
