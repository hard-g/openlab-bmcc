<?php
/**
 * The template for displaying image attachments.
 *
 * @package Academica
 */

get_header();
?>

<div id="content" class="clearfix">

	<?php while ( have_posts() ) : the_post(); ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="column column-title">
				<?php get_template_part( 'breadcrumb' ); ?>
				<?php the_title( '<h1 class="title-header">', '</h1>' ); ?>
			</div><!-- .column-title -->

			<div class="entry-content clearfix">

				<div class="entry-attachment">
					<div class="attachment">
						<?php
							/**
							 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
							 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
							 */
							$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
							foreach ( $attachments as $k => $attachment ) :
								if ( $attachment->ID == $post->ID )
									break;
							endforeach;
							$k++;
							// If there is more than 1 attachment in a gallery
							if ( count( $attachments ) > 1 ) :
								if ( isset( $attachments[ $k ] ) )
									// get the URL of the next image attachment
									$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
								else
									// or get the URL of the first image attachment
									$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
							else :
								// or, if there's only 1 image, get the URL of the image
								$next_attachment_url = wp_get_attachment_url();
							endif;
						?>

						<a href="<?php echo $next_attachment_url; ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php
							$attachment_size = apply_filters( 'academica_attachment_size', array( 1200, 1200 ) ); // Filterable image size.
							echo wp_get_attachment_image( $post->ID, $attachment_size );
						?></a>
					</div><!-- .attachment -->

					<?php if ( ! empty( $post->post_excerpt ) ) : ?>
					<div class="entry-caption">
						<?php the_excerpt(); ?>
					</div><!-- .entry-caption -->
					<?php endif; ?>
				</div><!-- .entry-attachment -->

				<?php the_content(); ?>
				<?php wp_link_pages( array( 'before' => '<p class="pages"><strong>' . __( 'Pages:', 'academica' ) . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number' ) ); ?>

			</div><!-- .entry-content -->

			<div class="entry-meta">
				<?php
					$metadata = wp_get_attachment_metadata();
					printf( __( 'Published <span class="entry-date"><time class="entry-date" datetime="%1$s" pubdate>%2$s</time></span> at <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a> in <a href="%6$s" title="Return to %7$s" rel="gallery">%7$s</a>.', 'academica' ),
						esc_attr( get_the_date( 'c' ) ),
						esc_html( get_the_date() ),
						wp_get_attachment_url(),
						$metadata['width'],
						$metadata['height'],
						get_permalink( $post->post_parent ),
						get_the_title( $post->post_parent )
					); ?>

				<?php
					if ( comments_open() && pings_open() ) : // Comments and trackbacks open
						printf( __( '<a class="comment-link" href="#respond" title="Post a comment">Post a comment</a> or leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'academica' ), get_trackback_url() );
					elseif ( ! comments_open() && pings_open() ) : // Only trackbacks open
						printf( __( 'Comments are closed, but you can leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'academica' ), get_trackback_url() );
					elseif ( comments_open() && ! pings_open() ) : // Only comments open
						_e( 'Trackbacks are closed, but you can <a class="comment-link" href="#respond" title="Post a comment">post a comment</a>.', 'academica' );
					elseif ( ! comments_open() && ! pings_open() ) : // Comments and trackbacks closed
						_e( 'Both comments and trackbacks are currently closed.', 'academica' );
					endif;
					edit_post_link( __( 'Edit', 'academica' ), '<span class="edit-link"> / ', '</span>' ); ?>
			</div><!-- .entry-meta -->

			<div id="image-navigation" class="navigation">
				<span class="previous-image"><?php previous_image_link( false, __( '&larr; Previous', 'academica' ) ); ?></span>
				<span class="next-image"><?php next_image_link( false, __( 'Next &rarr;', 'academica' ) ); ?></span>
			</div><!-- #image-navigation -->
		</div><!-- #post-<?php the_ID(); ?> -->

		<?php comments_template(); ?>
	<?php endwhile; // end of the loop. ?>

</div><!-- #content -->

<?php get_footer(); ?>