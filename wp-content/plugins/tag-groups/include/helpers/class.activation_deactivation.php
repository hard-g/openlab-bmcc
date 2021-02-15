<?php

/**
* @package     Tag Groups
* @author      Christoph Amthor
* @copyright   2019 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPL-3.0+
*/
if ( !class_exists( 'TagGroups_Activation_Deactivation' ) ) {
    /**
     *
     */
    class TagGroups_Activation_Deactivation
    {
        /**
         *   Initializes values and prevents errors that stem from wrong values, e.g. based on earlier bugs.
         *   Runs when plugin is activated.
         *
         * @param void
         * @return void
         */
        static function on_activation()
        {
            global  $tag_groups_premium_fs_sdk ;
            
            if ( !current_user_can( 'activate_plugins' ) ) {
                TagGroups_Error::log( '[Tag Groups] Insufficient permissions to activate plugin.' );
                return;
            }
            
            if ( TAG_GROUPS_PLUGIN_IS_KERNL ) {
                register_uninstall_hook( TAG_GROUPS_PLUGIN_ABSOLUTE_PATH, array( 'TagGroups_Activation_Deactivation', 'on_uninstall' ) );
            }
            $tag_groups_loader = new TagGroups_Loader( __FILE__ );
            // $tag_groups_loader->require_classes();
            if ( !defined( 'TAG_GROUPS_VERSION' ) ) {
                $tag_groups_loader->set_version();
            }
            update_option( 'tag_group_base_version', TAG_GROUPS_VERSION );
            /*
             * Taxonomy should not be empty
             */
            $tag_group_taxonomy = TagGroups_Options::get_option( 'tag_group_taxonomy', array() );
            
            if ( empty($tag_group_taxonomy) ) {
                update_option( 'tag_group_taxonomy', array( 'post_tag' ) );
            } elseif ( !is_array( $tag_group_taxonomy ) ) {
                // Prevent some weird errors
                update_option( 'tag_group_taxonomy', array( $tag_group_taxonomy ) );
            }
            
            /*
             * Theme should not be empty
             */
            if ( '' == TagGroups_Options::get_option( 'tag_group_theme', '' ) ) {
                update_option( 'tag_group_theme', TAG_GROUPS_STANDARD_THEME );
            }
            /**
             * Register time of first use
             */
            
            if ( defined( 'TAG_GROUPS_PLUGIN_IS_FREE' ) && TAG_GROUPS_PLUGIN_IS_FREE ) {
                if ( !TagGroups_Options::get_option( 'tag_group_base_first_activation_time', false ) ) {
                    update_option( 'tag_group_base_first_activation_time', time() );
                }
            } else {
            }
            
            // If requested and new options exist, then remove old options.
            
            if ( defined( 'TAG_GROUPS_REMOVE_OLD_OPTIONS' ) && TAG_GROUPS_REMOVE_OLD_OPTIONS && TagGroups_Options::get_option( 'term_groups', false ) && TagGroups_Options::get_option( 'term_group_positions', false ) && TagGroups_Options::get_option( 'term_group_labels', false ) ) {
                delete_option( 'tag_group_labels' );
                delete_option( 'tag_group_ids' );
                delete_option( 'max_tag_group_id' );
                TagGroups_Error::log( '[Tag Groups] Deleted deprecated options' );
            }
            
            // purge cache
            
            if ( class_exists( 'TagGroups_Cache' ) ) {
                $cache = new TagGroups_Cache();
                $cache->type( TagGroups_Options::get_option( 'tag_group_object_cache', TagGroups_Cache::WP_TRANSIENTS ) )->path( WP_CONTENT_DIR . '/chatty-mango/cache/' )->purge_all();
            }
            
            $tag_groups_loader->register_CRON();
            /**
             * Start with some delay so that in the case of simultaneous activation the base plugin will be available
             */
            wp_schedule_single_event( time() + 2, 'tag_groups_check_tag_migration' );
            wp_schedule_single_event( time() + 20, 'tag_groups_run_post_migration' );
            /**
             * Reset the group filter above the tags list
             */
            update_option( 'tag_group_tags_filter', array() );
        }
        
        /**
         * This script is executed when the (inactive) plugin is deleted through the admin backend.
         *
         *It removes the plugin settings from the option table and all tag groups. It does not change the term_group field of the taxonomies.
         *
         * @phpunit
         * @param void
         * @return void
         */
        public static function on_uninstall()
        {
            
            if ( !current_user_can( 'install_plugins' ) ) {
                TagGroups_Error::log( '[Tag Groups] Insufficient permissions to uninstall plugin.' );
                return;
            }
            
            // Referrer is wrong when triggered via Freemius
            // check_admin_referer( 'bulk-plugins' );
            /**
             * Delete options only if requested
             */
            // Note: WP_UNINSTALL_PLUGIN is not defined when using the deinstallation hook
            TagGroups_Error::log( '[Tag Groups] Starting uninstall routine.' );
            
            if ( !file_exists( dirname( __FILE__ ) . '/class.options.php' ) ) {
                TagGroups_Error::log( '[Tag Groups] Options class not available.' );
                return;
            }
            
            require_once dirname( __FILE__ ) . '/class.options.php';
            
            if ( !file_exists( dirname( __FILE__ ) . '/class.cache.php' ) ) {
                TagGroups_Error::log( '[Tag Groups] Cache class not available.' );
                return;
            }
            
            require_once dirname( __FILE__ ) . '/class.cache.php';
            /**
             * Purge cache
             */
            
            if ( class_exists( 'TagGroups_Cache' ) ) {
                $cache = new TagGroups_Cache();
                $cache->type( TagGroups_Options::get_option( 'tag_group_object_cache', TagGroups_Cache::WP_TRANSIENTS ) )->path( WP_CONTENT_DIR . '/chatty-mango/cache/' )->purge_all();
            }
            
            /**
             * Erase /chatty-mango/cache/ directory
             */
            
            if ( file_exists( WP_CONTENT_DIR . '/chatty-mango/cache' ) && is_dir( WP_CONTENT_DIR . '/chatty-mango/cache' ) ) {
                /**
                 * Attempt to empty and remove chatty-mango/cache directory
                 * (Different from purging cache because the previous one can be database.)
                 */
                foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( WP_CONTENT_DIR . '/chatty-mango/cache/' ) ) as $file ) {
                    // filter out "." and ".."
                    if ( $file->isDir() ) {
                        continue;
                    }
                    @unlink( $file->getPathname() );
                }
                @rmdir( WP_CONTENT_DIR . '/chatty-mango/cache' );
            }
            
            /**
             * Remove transients
             *
             * Do this before deleting options, because we need to know the array in 'tag_group_used_transient_names'
             * Don't call the method clear_term_cache since we don't know if it is still available.
             */
            TagGroups_Error::log( '[Tag Groups] Removing transients.' );
            TagGroups_Transients::delete_all_transients();
            /**
             * Maybe delete options
             */
            $tag_group_reset_when_uninstall = TagGroups_Options::get_option( 'tag_group_reset_when_uninstall', 0 );
            $option_count = 0;
            
            if ( $tag_group_reset_when_uninstall ) {
                $option_names = TagGroups_Options::get_available_options();
                if ( is_array( TagGroups_Options::get_option( 'tag_group_group_languages', false ) ) ) {
                    foreach ( TagGroups_Options::get_option( 'tag_group_group_languages' ) as $language ) {
                        if ( delete_option( 'term_group_labels_' . $language ) ) {
                            $option_count++;
                        }
                    }
                }
                foreach ( $option_names as $key => $value ) {
                    if ( delete_option( $key ) ) {
                        $option_count++;
                    }
                }
                TagGroups_Error::log( '[Tag Groups] %d options deleted.', $option_count );
            }
            
            /**
             * Remove regular crons
             */
            wp_clear_scheduled_hook( 'tag_groups_purge_expired_transients' );
            TagGroups_Error::log( '[Tag Groups] Finished uninstall routine.' );
        }
    
    }
}