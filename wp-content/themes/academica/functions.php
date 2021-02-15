<?php
/**
 * @package Academica
 */

if ( ! isset( $content_width ) )
	$content_width = 600; // pixels

/* Customizer */
require get_template_directory() . '/inc/customizer/bootstrap.php';

/**
 * Theme Setup
 */
function academica_setup() {

	// This theme styles the visual editor to resemble the theme style.
	add_editor_style( array( 'css/editor-style.css' ) );

	// Custom Background
	add_theme_support( 'custom-background' );

	// Custom Menus
	register_nav_menus( array(
		'primary' => __( 'Top Menu', 'academica' ),
		'footer'  => __( 'Footer Menu', 'academica' ),
	) );

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );

	// Featured Image
	add_theme_support( 'post-thumbnails' );
   	add_image_size( 'academica-featured-posts-widget', 75, 50, true );

	// Title Tag
	add_theme_support( 'title-tag' );

	// Feed Links
	add_theme_support( 'automatic-feed-links' );

	add_theme_support( 'infinite-scroll', array(
		'footer_widgets' => 'sidebar-3',
		'container'      => 'column-content',
		'wrapper'        => false,
		'footer'         => 'footer',
	) );

	load_theme_textdomain( 'academica', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'academica_setup' );

/**
 * Enqueues scripts and styles
 */
function academica_enqueue_scripts() {
	wp_enqueue_style( 'academica-style', get_stylesheet_uri() );

	wp_enqueue_style( 'academica-style-mobile', get_template_directory_uri() . '/media-queries.css', array( 'academica-style' ), '1.0' );

	wp_enqueue_style( 'academica-google-font-default', '//fonts.googleapis.com/css?family=Open+Sans:400,700|Roboto+Condensed:400,700&subset=latin,cyrillic-ext,greek-ext&display=swap' );

	wp_enqueue_style( 'dashicons' );

 	wp_enqueue_script( 'mmenu', get_template_directory_uri() . '/js/jquery.mmenu.min.all.js', array( 'jquery' ), '20150325', true );

 	wp_enqueue_script( 'superfish', get_template_directory_uri() . '/js/superfish.min.js', array( 'jquery' ), '20150325', true );

 	wp_enqueue_script( 'academica-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20150325', true );


	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'academica_enqueue_scripts' );

/**
 * Initializes Widgetized Areas (Sidebars)
 */
function academica_widgets_init() {

	register_sidebar( array(
		'name'          => __( 'Sidebar: Left', 'academica' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="heading">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Sidebar: Right', 'academica' ),
		'id'            => 'sidebar-2',
		'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="heading">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer', 'academica' ),
		'id'            => 'sidebar-3',
		'before_widget' => '<div id="%1$s" class="widget clearfix %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="heading">',
		'after_title'   => '</h3>',
	) );

	// Custom Theme Widget
	require_once get_template_directory() . '/inc/widgets.php';
    require_once get_template_directory() . '/inc/wpzoom-widgets.php';

}
add_action( 'widgets_init', 'academica_widgets_init' );


/**
 * Add some useful default widgets to the Academica sidebar
 */
function academica_default_widgets( $theme ) {
	if ( 'Academica' == $theme ) return;

	$sidebars = get_option( 'sidebars_widgets' );

	if ( empty( $sidebars['sidebar-1'] ) ) {
		$pages = get_option( 'widget_pages', array( '_multiwidget' => 1 ) );
		$pages[2] = array( 'title' => __( 'Start here', 'academica' ) );
		update_option( 'widget_pages', $pages );
		$sidebars['sidebar-1'] = array( 0 => 'pages-2' );
	}

	$sidebars['wp_inactive_widgets'] = array();
	$sidebars['array_version'] = 3;

	update_option( 'sidebars_widgets', $sidebars );
}
add_action( 'after_switch_theme', 'academica_default_widgets' );

/*
 * Count number of widgets in a sidebar
 */
function academica_widgets_count_class( $index = 1 ) {
    global $wp_registered_sidebars, $wp_registered_widgets;

    if ( is_int($index) ) {
        $index = "sidebar-$index";
    } else {
        $index = sanitize_title($index);
        foreach ( (array) $wp_registered_sidebars as $key => $value ) {
            if ( sanitize_title($value['name']) == $index ) {
                $index = $key;
                break;
            }
        }
    }

    $sidebars_widgets = wp_get_sidebars_widgets();

    if ( empty( $wp_registered_sidebars[ $index ] ) || empty( $sidebars_widgets[ $index ] ) || ! is_array( $sidebars_widgets[ $index ] ) ) {
        return '';
    }

    $count = 0;

    foreach ( (array) $sidebars_widgets[ $index ] as $id ) {
        if ( ! isset( $wp_registered_widgets[ $id ] ) ) continue;

        $count++;
    }

    return ' widgets-' . $count;
}

/**
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string Filtered title.
 */
function academica_wp_title( $title, $sep ) {

	if ( is_feed() )
		return $title;

	global $paged, $page;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'academica' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'academica_wp_title', 10, 2 );

/**
 * Get wp_page_menu() to show a home link.
 *
 * @param array $args
 * @return array
 */
function academica_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'academica_page_menu_args' );

/**
 * Sets a custom comment form title
 *
 * @param array $args
 * @return array
 */
function academica_comment_form_defaults( $args ) {
	$args['title_reply'] = __( 'Leave a comment', 'academica' );
	return $args;
}
add_filter( 'comment_form_defaults', 'academica_comment_form_defaults' );

if ( ! function_exists( 'academica_continue_reading_link' ) ) :
/**
 * Returns a "Continue Reading" link for excerpts
*/
function academica_continue_reading_link() {
	return ' <a href="'. esc_url( get_permalink() ) . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'academica' ) . '</a>';
}
endif; // academica_continue_reading_link

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an
 * ellipsis and academica_continue_reading_link().
 */
function academica_auto_excerpt_more( $more ) {
	return ' &hellip;' . academica_continue_reading_link();
}
add_filter( 'excerpt_more', 'academica_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 */
function academica_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= academica_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'academica_custom_excerpt_more' );

/**
 * Adds layout classes to the <body> element
 *
 * @param array $classes
 * @return array
 */
function academica_body_class( $classes ) {
	return array_merge( $classes, academica_sidebar_classes() );
}
add_filter( 'body_class', 'academica_body_class' );

/**
 * Adjusts content width if there are less than two sidebars
 */
function academica_set_content_width() {
	global $content_width;

	$classes = academica_sidebar_classes();

	if ( in_array( 'column-double', $classes ) || is_page_template( 'template-sidebar-left.php' ) || is_page_template( 'template-sidebar-right.php' ) )
		$content_width = 880;

	elseif ( in_array( 'column-full', $classes ) || is_page_template( 'template-full-width.php' ) )
	$content_width = 1170;
}
add_action( 'template_redirect', 'academica_set_content_width' );

/**
 * Displays a horizontal rule before the comment form, when there are no
 * comments yet
 */
function academica_comment_form_before() {
	if ( ! have_comments() )
		echo '<hr />';
}
add_action( 'comment_form_before', 'academica_comment_form_before' );

/**
 * Returns an array with classes, based on the number of active sidebars and the
 * page that is being viewed
 *
 * @return array
 */
function academica_sidebar_classes() {
	$classes = array();
	$sidebar_left = $sidebar_right = '';

	$sidebar_left = 'sidebar-1';
	$sidebar_right = 'sidebar-2';

	if ( // Not an archive and no active sidebars

		// Single post view and post set to full-width
		( is_singular( 'post' ) && 'column-full' == get_post_meta( get_the_ID(), '_academica_post_layout', true ) ) )
		$classes[] = 'column-full';

	elseif (
		// Just one sidebar active
		! is_active_sidebar( $sidebar_left ) || ! is_active_sidebar( $sidebar_right )

		// Single post view and post set to only left sidebar
		|| ( is_singular( 'post' ) && 'column-right' == get_post_meta( get_the_ID(), '_academica_post_layout', true ) ) )
		$classes[] = 'column-double';

	if (

		// Single post view and post set to only left sidebar
		( is_singular( 'post' ) && 'column-right' == get_post_meta( get_the_ID(), '_academica_post_layout', true ) ) )
		$classes[] = 'column-right';

	return $classes;
}

if ( ! function_exists( 'academica_entry_meta' ) ) :
/**
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
*/
function academica_entry_meta() {
	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'academica' ) );

	$date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a>',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'academica' ), get_the_author() ) ),
		get_the_author()
	);

	// Translators: 1 is the author's name, 2 is category, and 3 is the date.
	if ( $categories_list ) {
		$utility_text = __( '<span class="by-author">By %1$s </span>in <span class="category">%2$s</span> on <span class="datetime">%3$s</span>.', 'academica' );
	} else {
		$utility_text = __( '<span class="by-author">By %1$s </span>on <span class="datetime">%3$s</span>.', 'academica' );
	}

	printf(
		$utility_text,
		$author,
		$categories_list,
		$date
	);
}

endif;

if ( ! function_exists( 'academica_content_nav' ) ) :
/**
 * Displays navigation to next/previous pages when applicable.
*/
function academica_content_nav() {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>

<div class="navigation clearfix">
	<h3 class="assistive-text">
		<?php _e( 'Post navigation', 'academica' ); ?>
	</h3>
	<span class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'academica' ) ); ?>
	</span> <span class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'academica' ) ); ?>
	</span>
</div>
<!-- end .navigation -->
<?php endif;
}
endif;

/**
 * Adds the post layout meta box
 */
function academica_add_meta_box() {
	add_meta_box( 'academica_post_layout', __( 'Post Layout', 'academica' ), 'academica_meta_box_post_layout', 'post', 'side', 'low' );
}
add_action( 'add_meta_boxes', 'academica_add_meta_box' );

/**
 * Displays the post layout dropdown field
 */
function academica_meta_box_post_layout() {
	global $post;

	$selected = get_post_meta( $post->ID, '_academica_post_layout', true );
	wp_nonce_field( 'academica_post_layout', 'academica_post_layout_nonce' ); ?>

<p>
	<label for="academica_post_layout"><?php _e( 'Choose layout for this post:', 'academica' ); ?>
	</label><br /> <select name="academica_post_layout"
		id="academica_post_layout">
		<option value="">
			<?php _e( 'Default', 'academica' ); ?>
		</option>
		<option value="column-right"
		<?php selected( $selected, 'column-right' ); ?>>
			<?php _e( 'Only Left Sidebar', 'academica' ); ?>
		</option>
		<option value="column-full"
		<?php selected( $selected, 'column-full' ); ?>>
			<?php _e( 'Full Width (no sidebars)', 'academica' ); ?>
		</option>
	</select>
</p>
<?php
}

/**
 * Updates or deletes post meta with the post layout selection
 *
 * @param int $post_id
 * @return int
 */
function academica_save_post( $post_id ){
	if ( ( ! defined( 'DOING_AUTOSAVE' ) || ! DOING_AUTOSAVE )
		&& current_user_can( 'edit_post', $post_id )
		&& isset( $_POST['academica_post_layout'] )
		&& check_admin_referer( 'academica_post_layout', 'academica_post_layout_nonce' ) ) {

		if ( in_array( $_POST['academica_post_layout'], array( 'column-right', 'column-full' ) ) )
			update_post_meta( $post_id, '_academica_post_layout', $_POST['academica_post_layout'] );
		else
			delete_post_meta( $post_id, '_academica_post_layout' );
	}
	return $post_id;
}
add_action( 'save_post', 'academica_save_post' );


if ( ! function_exists( 'academica_breadcrumbs' ) ) :
/**
 * Displays a breadcrumb navigation
*/
function academica_breadcrumbs() {

	if ( !is_home() && !is_front_page() ) {

		$sep = ' &raquo; ';
		$before = '<span class="current">';
		$after = '</span>';

		echo '<a href="' . esc_url( home_url() ) . '">' . __( 'Home', 'academica' ) . '</a>' . $sep;

		if ( is_category() ) {
			global $wp_query;

			$cat = get_category( $wp_query->get_queried_object()->term_id );

			if ( $cat->parent ) {
				$cat_parents = get_category_parents( get_category( $cat->parent ), true, $sep );

				if ( ! is_wp_error( $cat_parents ) && 0 < $cat->parent ) {
					echo $cat_parents;
				}
			}

			echo $before . single_cat_title() . $after;

		} elseif ( is_day() ) {
			echo '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a>' . $sep;
			echo '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '">' . get_the_time( 'F' ) . '</a>' . $sep;
			echo $before . get_the_time( 'd' ) . $after;

		} elseif ( is_month() ) {
			echo '<a href="' . get_year_link( get_the_time('Y' ) ) . '">' . get_the_time( 'Y' ) . '</a>' . $sep;
			echo $before . get_the_time( 'F' ) . $after;

		} elseif ( is_year() ) {
			echo $before . get_the_time( 'Y' ) . $after;

		} elseif ( is_single() ) {
			if ( is_attachment() ) {
				global $post;
				echo '<a href="' . get_permalink( $post->post_parent ) . '">' . get_the_title( $post->post_parent ) . '</a>' . $sep . $before . get_the_title() . $after;
			} else {
				$cat = get_the_category();
				$cat = $cat[0];
				$cat_parents = get_category_parents( $cat, true, $sep );

				if ( ! is_wp_error( $cat_parents ) )
					echo $cat_parents . $before . get_the_title() . $after;
			}

		} elseif ( is_page() ) {
			global $post;
			if ( $post->post_parent ) {
				$parent_id  = $post->post_parent;
				$parent_links = array();
				while ( $parent_id ) {
					$page = get_page( $parent_id );
					$parent_links[] = '<a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a>';
					$parent_id  = $page->post_parent;
				}
				echo implode( $sep, array_reverse( $parent_links ) ) . $sep;
			}
			echo $before . get_the_title() . $after;

		} elseif ( is_search() ) {
			echo $before . sprintf( __( 'Search results for &#39;%s&#39;', 'academica' ), get_search_query() ) . $after;

		} elseif ( is_tag() ) {
			echo $before . sprintf( __( 'Posts tagged &#39;%s&#39;', 'academica' ), single_tag_title( '', false ) ) . $after;

		} elseif ( is_author() ) {
			global $author;
			echo $before . sprintf( __( 'Articles posted by %s', 'academica' ), get_userdata( $author )->display_name ) . $after;

		} elseif ( is_404() ) {
			echo $before . __( 'Error 404', 'academica' ) . $after;
		}

		if ( get_query_var( 'paged' ) ) {
			echo ' (' . sprintf( __( 'Page %s', 'academica' ), get_query_var( 'paged' ) ) . ')';
		}
	}
}
endif;

/**
 * Outputs footer text
 */
function academica_the_footer_text( $date_fmt = 'Y', $echo = true ) {

	$text = sprintf( __( 'Powered by <a href="%s">WordPress</a> / Academica WordPress Theme by <a href="%s" rel="nofollow">WPZOOM</a>', 'academica' ), 'http://wordpress.org', 'http://www.wpzoom.com' );

	$html = apply_filters( 'academica_the_footer_text', $text ) ;
	$html = apply_filters( 'academica_the_footer_text', $html, $text );

	if ( $echo ) {
		echo $html;
	}
	return $html;
}



/**
 * Include the TGM_Plugin_Activation class.
 */
require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'academica_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function academica_register_required_plugins() {
    /*
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(

        // This is an example of how to include a plugin from the WordPress Plugin Repository.
        array(
            'name'      => 'Social Icons Widget by WPZOOM',
            'slug'      => 'social-icons-widget-by-wpzoom',
            'required'  => false,
        ),

    );

    /*
     * Array of configuration settings. Amend each line as needed.
     *
     * TGMPA will start providing localized text strings soon. If you already have translations of our standard
     * strings available, please help us make TGMPA even better by giving us access to these translations or by
     * sending in a pull-request with .po file(s) with the translations.
     *
     * Only uncomment the strings in the config array if you want to customize the strings.
     */
    $config = array(
        'id'           => 'academica',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.

    );

    tgmpa( $plugins, $config );
}




/*
 * Fetch Theme Data & Options for About Page
 */

$academica_data = wp_get_theme('academica');
$academica_version = $academica_data['Version'];
$academica_options = get_option('academica_options');

if (!function_exists('academica_admin_scripts')) {
    function academica_admin_scripts($hook) {
        if ('appearance_page_academica' === $hook || 'widgets.php' === $hook) {
            wp_enqueue_style('academica-admin', get_template_directory_uri() . '/inc/admin/admin.css');
        }

        // Styles
        wp_enqueue_style(
            'academica-admin-review-notice',
            get_template_directory_uri() . '/inc/admin/admin-review-notice.css'
        );

    }
}
add_action('admin_enqueue_scripts', 'academica_admin_scripts');


if (is_admin()) {
    require_once('inc/admin/admin.php');

    if (current_user_can( 'manage_options' ) ) {
        require_once(get_template_directory() . '/inc/admin/academica-notices.php');
        require_once(get_template_directory() . '/inc/admin/notice-review.php');

    }

}