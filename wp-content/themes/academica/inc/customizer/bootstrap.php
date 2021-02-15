<?php
/**
 * @package Academica
 */

require_once get_template_directory() . '/inc/customizer/header.php';

/**
 * Load Customizer files.
 */
function academica_customizer_init() {
    require_once get_template_directory() . '/inc/customizer/logo.php';
}
add_action( 'after_setup_theme', 'academica_customizer_init' );

/**
 * Add sections and controls to the customizer.
 */
function academica_customizer_add_sections_and_options( $wp_customize ) {
    $wp_customize->add_section( 'academica_logo', array(
        'title'    => __( 'Logo', 'academica' ),
        'priority' => 20,
    ) );

    $wp_customize->add_setting( 'logo', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );

    $wp_customize->add_control( new WP_Customize_Image_Control(
        $wp_customize,
        'logo',
        array(
            'label'    => __( 'Logo', 'academica' ),
            'context'  => 'academica_logo',
            'section'  => 'academica_logo',
            'settings' => 'logo',
        )
    ) );

    $wp_customize->add_setting( 'logo-retina-ready', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ) );

    $wp_customize->add_control( 'logo-retina-ready', array(
        'type'     => 'checkbox',
        'label'    => __( 'Retina Ready?', 'academica' ),
        'section'  => 'academica_logo',
        'settings' => 'logo-retina-ready',
    ) );

    $wp_customize->add_setting( 'logo-position', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ) );

    $wp_customize->add_control( 'logo-position', array(
        'type'     => 'radio',
        'choices'  => array(
            __( 'Left', 'academica' ),
            __( 'Center', 'academica' ),
            __( 'Right', 'academica' ),
        ),
        'label'    => __( 'Logo Position', 'academica' ),
        'section'  => 'academica_logo',
        'settings' => 'logo-position',
    ) );

    $wp_customize->add_setting( 'header-background-color', array(
        'default' => '#0a5794',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control(
        $wp_customize,
        'header-background-color',
        array(
            'label' => __( 'Header Background Color', 'academica' ),
            'priority' => 0,
            'section' => 'colors',
            'settings' => 'header-background-color'
        )
    ) );
}
add_action( 'customize_register', 'academica_customizer_add_sections_and_options' );

/**
 * Print customizer css.
 */
function acedemica_customizer_display_css() {
    $styles = array();

    if ( '#0a5794' != ( $header_background_color = get_theme_mod( 'header-background-color', '#0a5794' ) ) ) {
        $styles[] = array(
            'selectors' => '#header, .navbar-nav ul',
            'declarations' => array(
                'background-color' => $header_background_color
            )
        );
    }

    if ( empty( $styles ) ) return;
    ?>

    <style type="text/css">

        <?php
        foreach ( $styles as $rule ) {
            echo $rule['selectors'] . ' {';

            foreach ( $rule['declarations'] as $property => $value ) {
                echo "{$property}:{$value};\n";
            }

            echo '}';
        }
        ?>

    </style>

    <?php
}
add_action( 'wp_head', 'acedemica_customizer_display_css', 20 );
