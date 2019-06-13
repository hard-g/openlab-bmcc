<?php
/**
 * @package Academica
*/

get_header();
?>

<div id="content" class="clearfix">

	<div class="column column-title">

		<?php get_template_part( 'breadcrumb' ); ?>


	</div><!-- end .column-title -->

	<div class="column column-narrow">
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</div><!-- end .column-narrow -->

	<div id="column-content" class="column column-content">

		<h1 class="title-header"><?php _e( 'Oops! That page can&rsquo;t be found.', 'academica' ); ?></h1>

		<div id="post-0" class="post error404 no-results not-found">
			<div class="entry-content">
				<p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'academica' ); ?></p>

				<?php the_widget( 'WP_Widget_Search' ); ?>

				<div class="widget">
					<h2 class="widgettitle"><?php _e( 'Most Used Categories', 'academica' ); ?></h2>
					<ul>
					<?php wp_list_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'show_count' => 1, 'title_li' => '', 'number' => 10 ) ); ?>
					</ul>
				</div><!-- .widget -->

			</div><!-- .entry-content -->
		</div><!-- #post-0 -->

 	</div><!-- end .column-content -->

 	<div class="column column-narrow column-last">
 		<?php dynamic_sidebar( 'sidebar-2' ); ?>
 	</div><!-- end .column-narrow -->

</div><!-- end #content -->

<?php get_footer(); ?>