<?php

if (!defined('ABSPATH')) {
	exit;
}

/*
 * Welcome Notice after Theme Activation
 */

if (!function_exists('academica_admin_notice')) {
	function academica_admin_notice() {
		global $pagenow, $academica_version;
		if (current_user_can('edit_theme_options') && 'index.php' === $pagenow && !get_option('academica_notice_welcome') || current_user_can('edit_theme_options') && 'themes.php' === $pagenow && isset($_GET['activated']) && !get_option('academica_notice_welcome')) {
			wp_enqueue_style('academica-admin-notice', get_template_directory_uri() . '/inc/admin/admin-notice.css', array(), $academica_version);
			academica_welcome_notice();
		}
	}
}
add_action('admin_notices', 'academica_admin_notice');


/*
 * Hide Welcome Notice in WordPress Dashboard ***
 */

if (!function_exists('academica_hide_notice')) {
	function academica_hide_notice() {
		if (isset($_GET['academica-hide-notice']) && isset($_GET['_academica_notice_nonce'])) {
			if (!wp_verify_nonce($_GET['_academica_notice_nonce'], 'academica_hide_notices_nonce')) {
				wp_die(esc_html__('Action failed. Please refresh the page and retry.', 'academica'));
			}
			if (!current_user_can('edit_theme_options')) {
				wp_die(esc_html__('You do not have the necessary permission to perform this action.', 'academica'));
			}
			$hide_notice = sanitize_text_field($_GET['academica-hide-notice']);
			update_option('academica_notice_' . $hide_notice, 1);
		}
	}
}
add_action('wp_loaded', 'academica_hide_notice');

/*
 * Content of Welcome Notice in WordPress Dashboard
 */

if (!function_exists('academica_welcome_notice')) {
	function academica_welcome_notice() {
		global $academica_data; ?>
		<div class="notice notice-success wpz-welcome-notice">
			<a class="notice-dismiss wpz-welcome-notice-hide" href="<?php echo esc_url(wp_nonce_url(remove_query_arg(array('activated'), add_query_arg('academica-hide-notice', 'welcome')), 'academica_hide_notices_nonce', '_academica_notice_nonce')); ?>">
				<span class="screen-reader-text">
					<?php echo esc_html__('Dismiss this notice.', 'academica'); ?>
				</span>
			</a>
			<p><?php printf(esc_html__('Thanks for using %1$s! To get started please make sure you visit our %2$swelcome page%3$s.', 'academica'), $academica_data['Name'], '<a href="' . esc_url(admin_url('themes.php?page=academica')) . '">', '</a>'); ?></p>
			<p class="wpz-welcome-notice-button">
				<a class="button-secondary" href="<?php echo esc_url(admin_url('themes.php?page=academica')); ?>">
					<?php printf(esc_html__('Get Started with %s', 'academica'), $academica_data['Name']); ?>
				</a>
				<a class="button-primary" href="<?php echo esc_url(__('https://www.wpzoom.com/themes/academica-pro-3/', 'academica')); ?>" target="_blank">
					<?php esc_html_e('Upgrade to Academica PRO', 'academica'); ?>
				</a>
			</p>
		</div><?php
	}
}

/*
 * About Theme Page
 */

if (!function_exists('academica_theme_info_page')) {
	function academica_theme_info_page() {
		add_theme_page(esc_html__('Welcome to Academica Theme', 'academica'), esc_html__('About Academica', 'academica'), 'edit_theme_options', 'academica', 'academica_display_theme_page');
	}
}
add_action('admin_menu', 'academica_theme_info_page');

if (!function_exists('academica_display_theme_page')) {
	function academica_display_theme_page() {
		global $academica_data; ?>
		<div class="theme-info-wrap">

			<div class="wpz-row theme-intro wpz-clearfix">


				<div class="wpz-col-1-4">
					<img class="theme-screenshot"
					     src="<?php echo esc_url( get_template_directory_uri() . '/screenshot.png' ); ?>"
					     alt="<?php esc_attr_e( 'Theme Screenshot', 'academica' ); ?>"/>
				</div>
				<div class="wpz-col-3-4 theme-description">

                    <h1>
                        <?php printf(esc_html__('Welcome to %1$1s %2$2s', 'academica'), $academica_data['Name'], $academica_data['Version']); ?>
                    </h1>


                    <?php esc_html_e('Academica is an education- and school-oriented CMS theme with a three-column layout and modern design. It is easy to customize and comes with a custom widget, three templates for Posts and Pages.', 'academica'); ?>

                    <div class="theme-links wpz-clearfix">
                        <p>
                            <a href="<?php echo esc_url(__('https://www.wpzoom.com/themes/academica/', 'academica')); ?>" class="button button-primary" target="_blank">
                                <?php esc_html_e('About Academica Theme', 'academica'); ?>
                            </a>
                            <a href="<?php echo esc_url(__('https://www.wpzoom.com/documentation/academica/','academica')); ?>" target="_blank">
                                <?php esc_html_e('Documentation', 'academica'); ?></a>
                            <a href="<?php echo esc_url(__('https://www.wpzoom.com/showcase/theme/academica-pro-3/', 'academica')); ?>" target="_blank">
                                <?php esc_html_e('Academica PRO Showcase', 'academica'); ?>
                            </a>
                        </p>
                    </div>

                </div>

			</div>
			<hr>
			<div id="getting-started">
				<h3>
					<?php printf(esc_html__('Get Started with %s', 'academica'), $academica_data['Name']); ?>
				</h3>
				<div class="wpz-row wpz-clearfix">
					<div class="wpz-col-1-2">
						<div class="section">
							<h4>
								<span class="dashicons dashicons-editor-help"></span>
								<?php esc_html_e('Theme Documentation', 'academica'); ?>
							</h4>
							<p class="about">
								<?php printf(esc_html__('Need help configuring %s? In the documentation you can find all theme related information that is needed to get your site up and running in no time.', 'academica'), $academica_data['Name']); ?>
							</p>
							<p>
								<a href="<?php echo esc_url(__('https://www.wpzoom.com/documentation/academica/', 'academica')); ?>" target="_blank" class="button button-primary">
									<?php esc_html_e('Theme Documentation', 'academica'); ?>
								</a>
								<a href="<?php echo esc_url(__('https://wordpress.org/support/theme/academica', 'academica')); ?>" target="_blank" class="button button-secondary">
                                    <?php esc_html_e('Support Forum', 'academica'); ?>
 								</a>
							</p>
						</div>

                        <hr /><br/>
						<div class="section">
							<h4>
								<span class="dashicons dashicons-admin-plugins"></span>
								<?php esc_html_e('Recommended Plugins', 'academica'); ?>
							</h4>
							<p class="about">
                                <?php esc_html_e('In order to enable all theme features, it\'s necessary to install a few recommended plugins.', 'academica'); ?>
							</p>
							<p>
								<a href="<?php echo esc_url(admin_url('themes.php?page=tgmpa-install-plugins')); ?>" class="button button-secondary">
                                    <?php esc_html_e('Recommended Plugins', 'academica'); ?>
 								</a>
							</p>
						</div>
					</div>
					<div class="wpz-col-1-2">
						<div class="section">
							<h4>
								<span class="dashicons dashicons-cart"></span>
								<?php esc_html_e('Academica Pro', 'academica'); ?>
							</h4>
							<p class="about">
								<?php esc_html_e('If you like the free version of this theme, you will LOVE the full version of Academica which includes unique custom widgets, additional features and more useful options to customize your website.', 'academica'); ?>
							</p>
							<p>
								<a href="<?php echo esc_url(__('https://www.wpzoom.com/themes/academica-pro-3/', 'academica')); ?>" target="_blank" class="button button-primary">
									<?php esc_html_e('Upgrade to academica PRO', 'academica'); ?>
								</a>
							</p>
						</div><hr /><br/>
						<div class="section">
							<h4>
								<span class="dashicons dashicons-star-filled"></span>
								<?php esc_html_e('Why Upgrade?', 'academica'); ?>
							</h4>
							<p class="about">
								<?php esc_html_e('Upgrading to Academica PRO you will unlock a dozen of unique features that will take your website to the next level. See in the table below just a few of the features included in the PRO version.', 'academica'); ?>
							</p>
							<p>
								<a href="<?php echo esc_url(__('http://demo.wpzoom.com/?theme=academica-pro-3', 'academica')); ?>" target="_blank" class="button button-primary">
									<?php esc_html_e('View Academica PRO Demo', 'academica'); ?>
								</a>
							</p>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<div class="theme-comparison">
				<h3 class="theme-comparison-intro">
					<?php esc_html_e('Academica Free vs. Academica PRO', 'academica'); ?>
				</h3>
				<table>
					<thead class="theme-comparison-header">
						<tr>
							<th class="table-feature-title"><h3><?php esc_html_e('Features', 'academica'); ?></h3></th>
							<th><h3><?php esc_html_e('Academica Free', 'academica'); ?></h3></th>
							<th><h3><?php esc_html_e('Academica PRO', 'academica'); ?></h3></th>
						</tr>
					</thead>
					<tbody>
                        <tr>
                            <td><h3><?php esc_html_e('Custom Widgets', 'academica'); ?></h3></td>
                            <td><?php esc_html_e('2', 'academica'); ?></td>
                            <td><?php esc_html_e('6 (Custom Menu, Image Box, Testimonials, Recent Posts, etc.)', 'academica'); ?></td>
                        </tr>
                        <tr>
                            <td><h3><?php esc_html_e('Widget Areas', 'academica'); ?></h3></td>
                            <td><?php esc_html_e('3', 'academica'); ?></td>
                            <td><?php esc_html_e('11 (4 on Homepage)', 'academica'); ?></td>
                        </tr>
                        <tr>
                            <td><h3><?php esc_html_e('Header Menus', 'academica'); ?></h3></td>
                            <td><?php esc_html_e('1', 'academica'); ?></td>
                            <td><?php esc_html_e('3', 'academica'); ?></td>
                        </tr>
                        <tr>
                            <td><h3><?php esc_html_e('Featured Area on Homepage (Slideshow)', 'academica'); ?></h3></td>
                            <td><span class="dashicons dashicons-no"></span></td>
                            <td><span class="dashicons dashicons-yes"></span></td>
                        </tr>
                        <tr>
                            <td><h3><?php esc_html_e('Custom Homepage', 'academica'); ?></h3></td>
                            <td><span class="dashicons dashicons-no"></span></td>
                            <td><span class="dashicons dashicons-yes"></span></td>
                        </tr>
                        <tr>
                            <td><h3><?php esc_html_e('Demo Content Importer', 'academica'); ?></h3></td>
                            <td><span class="dashicons dashicons-no"></span></td>
                            <td><span class="dashicons dashicons-yes"></span></td>
                        </tr>
                        <tr>
                            <td><h3><?php esc_html_e('WooCommerce Support', 'academica'); ?></h3></td>
                            <td><span class="dashicons dashicons-no"></span></td>
                            <td><span class="dashicons dashicons-yes"></span></td>
                        </tr>
                        <tr>
                            <td><h3><?php esc_html_e('Social Icons in the Header', 'academica'); ?></h3></td>
                            <td><span class="dashicons dashicons-no"></span></td>
                            <td><span class="dashicons dashicons-yes"></span></td>
                        </tr>
						<tr>
							<td><h3><?php esc_html_e('Events Plugin Integration', 'academica'); ?></h3></td>
							<td><span class="dashicons dashicons-no"></span></td>
							<td><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td><h3><?php esc_html_e('Theme Options', 'academica'); ?></h3></td>
                            <td><span class="dashicons dashicons-no"></span></td>
							<td><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td><h3><?php esc_html_e('50+ Color Options', 'academica'); ?></h3></td>
							<td><span class="dashicons dashicons-no"></span></td>
							<td><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td><h3><?php esc_html_e('600+ Google Fonts', 'academica'); ?></h3></td>
							<td><span class="dashicons dashicons-no"></span></td>
							<td><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td><h3><?php esc_html_e('Typography Options', 'academica'); ?></h3></td>
							<td><span class="dashicons dashicons-no"></span></td>
							<td><span class="dashicons dashicons-yes"></span></td>
						</tr>
						<tr>
							<td><h3><?php esc_html_e('Support', 'academica'); ?></h3></td>
							<td><?php esc_html_e('Support Forum', 'academica'); ?></td>
							<td><?php esc_html_e('Fast & Friendly Email Support', 'academica'); ?></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>
								<a href="<?php echo esc_url(__('https://www.wpzoom.com/themes/academica-pro-3/', 'academica')); ?>" target="_blank" class="upgrade-button">
									<?php esc_html_e('Upgrade to Academica PRO', 'academica'); ?>
								</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

		</div><?php
	}
}

?>