<?php
/*
Plugin Name: VoiceThread Auto Embed
Plugin URI: http://orthogonalcreations.com/voicethread-auto-embed-plugin/
Description: Adds auto embed support to WordPress for VoiceThreads.
Author: Jon Breitenbucher
Author URI: http://jon.breitenbucher.net
Version: 1.0.1
*/

/*
VoiceThread Auto Embed (Wordpress Plugin)
Copyright (C) 2011 Jon Breitenbucher
Contact me at http://orthogonalcreations.com/contact-me/

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// create custom plugin settings menu
add_action('admin_menu', 'voicethreadautoembed_create_menu');

function voicethreadautoembed_create_menu() {
// create new menu in the Settings panel
	add_options_page('VoiceThread Auto Embed Options', 'VoiceThread Auto Embed', 'manage_options', 'voicethreadautoembed_settings_page', 'voicethreadautoembed_settings_page');

// call register settings function
	add_action( 'admin_init', 'register_voicethreadautoembed_settings' );
}


function register_voicethreadautoembed_settings() {
// register our settings
	register_setting( 'voicethreadautoembed-settings-group', 'voicethreadautoembed_player_width' );
	register_setting( 'voicethreadautoembed-settings-group', 'voicethreadautoembed_player_height' );
}

// create the actual contents of the settings page
function voicethreadautoembed_settings_page() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
?>
<div class="wrap">
<h2>VoiceThread Auto Embed Options</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'voicethreadautoembed-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">VoiceThread defaults</th>
        <td>&nbsp;</td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Default width:</th>
        <td><input type="text" name="voicethreadautoembed_player_width" value="<?php echo get_option('voicethreadautoembed_player_width'); ?>" /> The default width of the VoiceThread player.</td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Default height:</th>
        <td><input type="text" name="voicethreadautoembed_player_height" value="<?php echo get_option('voicethreadautoembed_player_height'); ?>" /> The default height of the VoiceThread player.</td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php } ?>
<?php
wp_embed_register_handler( 'voicethread', '#https?://voicethread\.com/\?\#[qu][0-9]*?\.b([\d]+)\.?i?[0-9]*?#', 'wp_embed_handler_voicethread' );

function wp_embed_handler_voicethread( $matches, $attr, $url, $rawattr ) {
	$width = get_option('voicethreadautoembed_player_width', '480');
	$height = get_option('voicethreadautoembed_player_height', '360');

	$embed = sprintf(
			'<object width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"><param name="movie" value="http://voicethread.com/book.swf?b=%1$s"></param><param name="wmode" value="transparent"></param><embed src="http://voicethread.com/book.swf?b=%1$s" type="application/x-shockwave-flash" wmode="transparent" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"></embed></object>',
			esc_attr($matches[1])
			);

	return apply_filters( 'embed_voicethread', $embed, $matches, $attr, $url, $rawattr );
}

?>