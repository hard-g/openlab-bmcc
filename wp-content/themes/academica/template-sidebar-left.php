<?php
/**
 * Template Name: Sidebar Left
 *
 * @package Academica
 */

get_header(); ?>

<div id="content" class="clearfix">

	<?php while ( have_posts() ) : the_post(); ?>

	<div class="column column-title">
		<?php get_template_part( 'breadcrumb' ); ?>
 	</div><!-- end .column-title -->

	<div class="column column-narrow">
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</div><!-- end .column-narrow -->

	<div class="column column-double column-last column-content single">

		<?php get_template_part( 'content', 'page' );
		comments_template(); ?>

	</div><!-- end .column-content -->

	<?php endwhile; ?>

</div><!-- end #content -->

<?php get_footer(); ?>