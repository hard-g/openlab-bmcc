<?php

/**
 * Replace parent theme admin page with child theme page to use appropriate theme labelling
 */
function liquido_replace_admin_page() {
	remove_action( 'admin_menu', 'fluida_add_page_fn' );
} // liquido_replace_admin_page()
add_action( 'init', 'liquido_replace_admin_page', 11 );

function liquido_add_page_fn() {
	global $fluida_page;
	$fluida_page = add_theme_page( __( 'Liquido Theme', 'liquido' ), __( 'Liquido Theme', 'liquido' ), 'edit_theme_options', 'about-liquido-theme', 'fluida_page_fn' );
	add_action( 'admin_enqueue_scripts', 'fluida_admin_scripts' );
} // liquido_add_page_fn()
add_action( 'admin_menu', 'liquido_add_page_fn' );

/**
 * Add child theme version to admin page
 */
function liquido_admin_version_output( $parent ) {
	$theme = wp_get_theme();
	$name = $theme->get( 'Name' );
	$version = $theme->get( 'Version' );

	return sprintf( __( '<em>%1$s v%2$s</em> &ndash; a child theme of %3$s', 'liquido' ), $theme, $version, $parent );
} // liquido_admin_version_output()
// this filter is applied in liquido_setup()

/**
 * Extend description to reference the use of the child theme
 */
function liquido_custom_description( $description ) {
	// Child theme
	$theme = wp_get_theme();
	$template = esc_attr( $theme->get( 'Template' ) );
	$name = esc_attr( $theme->get( 'Name' ) );

	// Parent theme
	$template_theme = wp_get_theme( $template );
	$template_desc = $template_theme->get( 'Description');

	$output = '<h3>' . sprintf( esc_attr__( '%1$s is a child theme of %2$s', 'liquido' ), $name,  ucfirst( $template ) ) . '</h3>';

	return  $output . $description . '<br><br><hr><br><em>' . $template_desc . '</em>';
} // liquido_custom_description()
// this filter is added in liquido_setup()


// FIN
