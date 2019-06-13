<?php
/**
 * @package Academica
 */
?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?>>

	<?php the_title( '<h2><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

	<p class="entry-meta">
		<?php
		if ( 'post' == get_post_type() && ! is_sticky() ) :
			printf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
				esc_url( get_permalink() ),
				esc_attr( sprintf( __( 'Permanent Link to %s', 'academica' ), the_title_attribute( 'echo=0' ) ) ),
				esc_html( get_the_date( get_option( 'date_format' ) . ' ' ) )
			);
			if ( ! post_password_required() && ( comments_open() || 0 != get_comments_number() ) ) :
				echo ' <span class="sep">/ </span> ';
				comments_popup_link( __( 'Leave a comment', 'academica' ) );
			endif;
		endif;
		edit_post_link( __( 'Edit', 'academica' ), '<span class="edit-link"><span class="sep"> / </span>', '</span>' ); ?>
	</p><!-- end .entry-meta -->
	<div class="entry-summary"><?php the_content(); ?></div>

</div><!-- end #post-## -->