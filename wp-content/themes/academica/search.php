<?php
/**
 * @package Academica
 */

get_header(); ?>

<div id="content" class="clearfix">

	<div class="column column-title">
		<?php get_template_part( 'breadcrumb' ); ?>
		<h1 class="title-header"><?php printf( __( 'Search Results for: %s', 'academica' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
	</div><!-- end .column-title -->

	<div class="column column-narrow">
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</div><!-- end .column-narrow -->

	<div class="column column-content posts">

		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'content', 'search' );
			endwhile;
			academica_content_nav();
		else : ?>

		<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'academica' ); ?></p>
		<?php get_search_form(); ?>

		<?php endif; ?>

	</div><!-- end .column-content -->

	<div class="column column-narrow column-last">
		<?php dynamic_sidebar( 'sidebar-2' ); ?>
	</div><!-- end .column-narrow -->

</div><!-- end #content -->

<?php get_footer(); ?>