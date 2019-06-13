<?php
/**
 * Template Name: Full Width
 *
 * @package Academica
 */

get_header(); ?>

<div id="content" class="clearfix">

	<?php while ( have_posts() ) : the_post(); ?>

	<div class="column column-title">
		<?php get_template_part( 'breadcrumb' ); ?>
 	</div><!-- end .column-title -->

	<div class="column column-content column-last single">

		<?php get_template_part( 'content', 'page' );
		comments_template(); ?>

	</div><!-- end .column-content -->

	<?php endwhile; ?>

</div><!-- end #content -->

<?php get_footer(); ?>