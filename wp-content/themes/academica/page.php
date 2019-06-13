<?php
/**
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

	<div class="column column-content single">

		<?php get_template_part( 'content', 'page' );
		comments_template(); ?>

	</div><!-- end .column-content -->

	<div class="column column-narrow column-last">
		<?php dynamic_sidebar( 'sidebar-2' ); ?>
	</div><!-- end .column-narrow -->

	<?php endwhile; ?>

</div><!-- end #content -->

<?php get_footer(); ?>