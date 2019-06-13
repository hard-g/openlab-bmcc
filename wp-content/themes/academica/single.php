<?php
/**
 * @package Academica
 */

$post_layout = get_post_meta( get_the_ID(), '_academica_post_layout', true );

get_header(); ?>

<div id="content" class="clearfix">

	<?php while ( have_posts() ) : the_post(); ?>

	<div class="column column-title">
		<?php get_template_part( 'breadcrumb' ); ?>
	</div><!-- end .column-title -->

	<?php if ( 'column-full' != $post_layout ) : ?>
	<div class="column column-narrow">
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</div><!-- end .column-narrow -->
	<?php endif; ?>

	<div class="column column-content single">

		<?php get_template_part( 'content', 'single' ); ?>

		<div class="navigation clearfix">
			<?php previous_post_link( '<span class="alignleft">%link</span>', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'academica' ) . '</span> %title' ); ?>
			<?php next_post_link( '<span class="alignright">%link</span>', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'academica' ) . '</span>' ); ?>
		</div><!-- end .navigation -->

		<?php comments_template(); ?>

	</div><!-- end .column-content -->

	<?php if ( 'column-full' != $post_layout && 'column-right' != $post_layout ) : ?>
	<div class="column column-narrow column-last">
		<?php dynamic_sidebar( 'sidebar-2' ); ?>
	</div><!-- end .column-narrow -->
	<?php endif; ?>

	<?php endwhile; ?>

</div><!-- end #content -->

<?php get_footer(); ?>