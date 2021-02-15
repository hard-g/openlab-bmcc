<?php
/**
 * @package Academica
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

    <?php
    wp_body_open();
    ?>

	<?php $header_image = get_header_image(); ?>

	<div id="header" class="clearfix" <?php if ( ! empty( $header_image ) ) : ?> style="background-image: url('<?php echo $header_image; ?>');" <?php endif; ?>>

		<?php if ( ! empty( $header_image ) ) : ?><div class="header_overlay"><?php endif; ?>

			<div class="inner-wrap">

				<div id="logo" class="<?php academica_logo_position(); ?>">
					<?php if ( ! academica_has_logo() ) : ?> <h1 id="site-title"> <?php endif; ?>

						<a href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> - <?php bloginfo( 'description' ); ?>" rel="home">

							<?php
							if ( ! academica_has_logo() ) :
								bloginfo( 'name' );
							else:
								academica_logo();
							endif;
							?>

						</a>

					<?php if ( ! academica_has_logo() ) : ?> </h1> <?php endif; ?>

					<p id="site-description"><?php bloginfo( 'description' ); ?></p>
				</div><!-- end #logo -->

			</div><!-- end .inner-wrap -->

			<div id="main-nav">

				<div class="inner-wrap">

					<nav class="main-navbar" role="navigation">

	                    <div class="navbar-header">
	                        <?php if (has_nav_menu( 'primary' )) { ?>

	                           <a class="navbar-toggle" href="#menu-main-slide">
	                               <span class="icon-bar"></span>
	                               <span class="icon-bar"></span>
	                               <span class="icon-bar"></span>
	                           </a>


	                           <?php wp_nav_menu( array(
	                               'container_id'   => 'menu-main-slide',
	                               'theme_location' => 'primary'
	                           ) );
	                       }  ?>

	                    </div>


	                    <div id="navbar-main">

	                        <?php if (has_nav_menu( 'primary' )) {
	                            wp_nav_menu( array(
	                                'menu_class'     => 'nav navbar-nav dropdown sf-menu',
	                                'theme_location' => 'primary'
	                            ) );
	                        } ?>


	                    </div><!-- #navbar-main -->

	                </nav><!-- .navbar -->

					<div id="search">
						<?php get_search_form(); ?>
					</div><!-- end #search -->

				</div><!-- end .inner-wrap -->

			</div><!-- end #main-nav -->

		<?php if ( ! empty( $header_image ) ) : ?></div><!-- end .header_overlay --><?php endif; ?>

	</div><!-- end #header -->

	<div id="wrap">