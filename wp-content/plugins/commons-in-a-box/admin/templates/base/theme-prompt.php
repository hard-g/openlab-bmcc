
	<?php if ( cbox_get_theme_prop( 'screenshot_url' ) ) : ?>
		<a class="thickbox" title="<?php esc_html_e( 'Screenshot of theme', 'cbox' ); ?>" href="<?php echo esc_url( cbox_get_theme_prop( 'screenshot_url' ) ); ?>" style="float:right; margin-left:2em;"><img width="200" src="<?php echo esc_url( cbox_get_theme_prop( 'screenshot_url' ) ); ?>" alt="" /></a>
	<?php endif; ?>

	<p><?php esc_html_e( 'One last step!', 'cbox' ); ?></p>
	
	<p><?php printf( __( 'The %1$s Theme is the final piece of the Commons In A Box %2$s experience.  It ties together all functionality offered by the %2$s box in a beautiful package.', 'cbox' ), cbox_get_theme_prop( 'name' ), cbox_get_package_prop( 'name' ) ); ?></p>
	
	<p><?php printf( esc_html__( 'Please note: clicking on "%s" will change your current theme.  If you would rather keep your existing theme, click on "Skip".', 'cbox' ), cbox_get_theme( cbox_get_theme_prop( 'directory_name' ) )->exists() ? __( 'Activate Theme', 'cbox' ) : __( 'Install Theme', 'cbox' ) ); ?></p>
