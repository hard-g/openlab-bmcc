<?php

/**
 * @package     Tag Groups
 *
 * @author      Christoph Amthor
 * @copyright   2019 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPL-3.0+
 */
if ( !class_exists( 'TagGroups_Enqueue' ) ) {
    /**
     *
     */
    class TagGroups_Enqueue
    {
        /**
         * Add js and css to frontend
         *
         * @param  void
         * @return void
         */
        public function wp_enqueue_scripts()
        {
            global  $post ;
            $tag_group_shortcode_enqueue_always = TagGroups_Options::get_option( 'tag_group_shortcode_enqueue_always', 1 );
            /* enqueue frontend scripts and styling only if shortcode in use */
            
            if ( $tag_group_shortcode_enqueue_always || !is_a( $post, 'WP_Post' ) || has_shortcode( $post->post_content, 'tag_groups_cloud' ) || has_shortcode( $post->post_content, 'tag_groups_accordion' ) || has_shortcode( $post->post_content, 'tag_groups_alphabet_tabs' ) || strpos( $post->post_content, '<!-- wp:chatty-mango/tag-groups-cloud-tabs' ) !== false || strpos( $post->post_content, '<!-- wp:chatty-mango/tag-groups-cloud-accordion' ) !== false || strpos( $post->post_content, '<!-- wp:chatty-mango/tag-groups-alphabet-tabs' ) !== false ) {
                
                if ( TagGroups_Options::get_option( 'tag_group_enqueue_jquery', 1 ) ) {
                    wp_enqueue_script( 'jquery' );
                    wp_enqueue_script( 'jquery-ui-core' );
                    wp_enqueue_script( 'jquery-ui-tabs' );
                    wp_enqueue_script( 'jquery-ui-accordion' );
                }
                
                
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    wp_register_script(
                        'tag-groups-js-frontend',
                        TAG_GROUPS_PLUGIN_URL . '/assets/js/frontend.js',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                } else {
                    wp_register_script(
                        'tag-groups-js-frontend',
                        TAG_GROUPS_PLUGIN_URL . '/assets/js/frontend.min.js',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                }
                
                wp_enqueue_script( 'tag-groups-js-frontend' );
                $theme = TagGroups_Options::get_option( 'tag_group_theme', TAG_GROUPS_STANDARD_THEME );
                if ( '' == $theme ) {
                    return;
                }
                $default_themes = explode( ',', TAG_GROUPS_BUILT_IN_THEMES );
                wp_register_style(
                    'tag-groups-css-frontend-structure',
                    TAG_GROUPS_PLUGIN_URL . '/assets/css/jquery-ui.structure.min.css',
                    array(),
                    TAG_GROUPS_VERSION
                );
                
                if ( in_array( $theme, $default_themes ) ) {
                    wp_register_style(
                        'tag-groups-css-frontend-theme',
                        TAG_GROUPS_PLUGIN_URL . '/assets/css/' . $theme . '/jquery-ui.theme.min.css',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                } else {
                    /*
                     * Load minimized css if available
                     */
                    
                    if ( file_exists( WP_CONTENT_DIR . '/uploads/' . $theme . '/jquery-ui.theme.min.css' ) ) {
                        wp_register_style(
                            'tag-groups-css-frontend-theme',
                            get_bloginfo( 'wpurl' ) . '/wp-content/uploads/' . $theme . '/jquery-ui.theme.min.css',
                            array(),
                            TAG_GROUPS_VERSION
                        );
                    } else {
                        
                        if ( file_exists( WP_CONTENT_DIR . '/uploads/' . $theme . '/jquery-ui.theme.css' ) ) {
                            wp_register_style(
                                'tag-groups-css-frontend-theme',
                                get_bloginfo( 'wpurl' ) . '/wp-content/uploads/' . $theme . '/jquery-ui.theme.css',
                                array(),
                                TAG_GROUPS_VERSION
                            );
                        } else {
                            /*
                             * Fallback: Is this a custom theme of an old version or did we revert to old plugin version?
                             */
                            
                            if ( file_exists( WP_CONTENT_DIR . '/uploads/' . $theme ) ) {
                                $dh = opendir( WP_CONTENT_DIR . '/uploads/' . $theme );
                                if ( !empty($dh) ) {
                                    while ( false !== ($filename = @readdir( $dh )) ) {
                                        
                                        if ( preg_match( "/jquery-ui-\\d+\\.\\d+\\.\\d+\\.custom\\.(min\\.)?css/i", $filename ) ) {
                                            wp_register_style(
                                                'tag-groups-css-frontend-theme',
                                                get_bloginfo( 'wpurl' ) . '/wp-content/uploads/' . $theme . '/' . $filename,
                                                array(),
                                                TAG_GROUPS_VERSION
                                            );
                                            break;
                                        }
                                    
                                    }
                                }
                            } else {
                                TagGroups_Error::log( '[Tag Groups] Error finding %s/uploads/%s', WP_CONTENT_DIR, $theme );
                            }
                        
                        }
                    
                    }
                
                }
                
                wp_enqueue_style( 'tag-groups-css-frontend-structure' );
                wp_enqueue_style( 'tag-groups-css-frontend-theme' );
                $this->enqueue_frontend_css();
                $this->enqueue_premium_frontend_css();
            }
            
            /**
             * Equeue features that appear in lists and don't need jQuery UI
             */
            
            if ( $tag_group_shortcode_enqueue_always || !is_a( $post, 'WP_Post' ) || has_shortcode( $post->post_content, 'tag_groups_tag_list' ) || has_shortcode( $post->post_content, 'tag_groups_alphabetical_index' ) || strpos( $post->post_content, '<!-- wp:chatty-mango/tag-groups-tag-list' ) !== false || strpos( $post->post_content, '<!-- wp:chatty-mango/tag-groups-alphabetical-tag-index' ) !== false ) {
                $this->enqueue_frontend_css();
                $this->enqueue_premium_frontend_css();
            }
        
        }
        
        /**
         * Add css to backend
         *
         * @param  string $where
         * @return void
         */
        public function admin_enqueue_scripts( $where )
        {
            global  $tag_groups_premium_fs_sdk ;
            $backend_plugin_url = TAG_GROUPS_PLUGIN_URL;
            
            if ( strpos( $where, 'tag-groups-settings' ) !== false ) {
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( 'jquery-ui-core' );
                wp_enqueue_script( 'jquery-ui-accordion' );
                
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    wp_register_style(
                        'tag-groups-css-backend-tgb',
                        $backend_plugin_url . '/assets/css/backend.css',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                } else {
                    wp_register_style(
                        'tag-groups-css-backend-tgb',
                        $backend_plugin_url . '/assets/css/backend.min.css',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                }
                
                wp_enqueue_style( 'tag-groups-css-backend-tgb' );
                wp_register_style(
                    'tag-groups-css-backend-structure',
                    TAG_GROUPS_PLUGIN_URL . '/assets/css/jquery-ui.structure.min.css',
                    array(),
                    TAG_GROUPS_VERSION
                );
                wp_enqueue_style( 'tag-groups-css-backend-structure' );
                wp_register_script(
                    'sumoselect-js',
                    TAG_GROUPS_PLUGIN_URL . '/assets/js/jquery.sumoselect.min.js',
                    array(),
                    TAG_GROUPS_VERSION
                );
                wp_enqueue_script( 'sumoselect-js' );
                wp_register_style(
                    'sumoselect-css',
                    TAG_GROUPS_PLUGIN_URL . '/assets/css/sumoselect.css',
                    array(),
                    TAG_GROUPS_VERSION
                );
                wp_enqueue_style( 'sumoselect-css' );
            } elseif ( strpos( $where, '_page_tag-groups' ) !== false ) {
                
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    wp_register_style(
                        'tag-groups-css-backend-tgb',
                        $backend_plugin_url . '/assets/css/backend.css',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                } else {
                    wp_register_style(
                        'tag-groups-css-backend-tgb',
                        $backend_plugin_url . '/assets/css/backend.min.css',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                }
                
                wp_enqueue_style( 'tag-groups-css-backend-tgb' );
                
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    wp_register_script(
                        'tag-groups-js-backend',
                        TAG_GROUPS_PLUGIN_URL . '/assets/js/taggroups.js',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                } else {
                    wp_register_script(
                        'tag-groups-js-backend',
                        TAG_GROUPS_PLUGIN_URL . '/assets/js/taggroups.min.js',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                }
                
                wp_enqueue_script( 'tag-groups-js-backend' );
                wp_enqueue_script( 'jquery-ui-sortable' );
                wp_enqueue_script( 'jquery-ui-core' );
                wp_enqueue_script( 'jquery-ui-accordion' );
            } elseif ( strpos( $where, 'edit-tags.php' ) !== false || strpos( $where, 'term.php' ) !== false || strpos( $where, 'edit.php' ) !== false ) {
                wp_register_script(
                    'sumoselect-js',
                    TAG_GROUPS_PLUGIN_URL . '/assets/js/jquery.sumoselect.min.js',
                    array(),
                    TAG_GROUPS_VERSION
                );
                wp_enqueue_script( 'sumoselect-js' );
                wp_register_style(
                    'sumoselect-css',
                    TAG_GROUPS_PLUGIN_URL . '/assets/css/sumoselect.css',
                    array(),
                    TAG_GROUPS_VERSION
                );
                wp_enqueue_style( 'sumoselect-css' );
                
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    wp_register_style(
                        'tag-groups-css-backend-tgb',
                        $backend_plugin_url . '/assets/css/backend.css',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                } else {
                    wp_register_style(
                        'tag-groups-css-backend-tgb',
                        $backend_plugin_url . '/assets/css/backend.min.css',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                }
                
                wp_enqueue_style( 'tag-groups-css-backend-tgb' );
            } elseif ( strpos( $where, 'post-new.php' ) !== false || strpos( $where, 'post.php' ) !== false ) {
                wp_register_style(
                    'react-select-css',
                    TAG_GROUPS_PLUGIN_URL . '/assets/css/react-select.css',
                    array(),
                    TAG_GROUPS_VERSION
                );
                wp_enqueue_style( 'react-select-css' );
                
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    wp_register_style(
                        'tag-groups-css-backend-tgp',
                        $backend_plugin_url . '/assets/css/backend.css',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                } else {
                    wp_register_style(
                        'tag-groups-css-backend-tgp',
                        $backend_plugin_url . '/assets/css/backend.min.css',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                }
                
                wp_enqueue_style( 'tag-groups-css-backend-tgp' );
            }
            
            if ( TagGroups_Gutenberg::is_gutenberg_active() ) {
                $this->admin_enqueue_scripts_for_gutenberg();
            }
        }
        
        /**
         * Adds js and css to the Gutenberg editor page
         *
         *
         * @param  void
         * @return void
         */
        public function admin_enqueue_scripts_for_gutenberg()
        {
            /* enqueue frontend scripts and styling only if shortcode in use */
            // global $tag_groups_premium_fs_sdk;
            $screen = get_current_screen();
            if ( 'post' != $screen->base ) {
                return;
            }
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-tabs' );
            wp_enqueue_script( 'jquery-ui-accordion' );
            $theme = TagGroups_Options::get_option( 'tag_group_theme', TAG_GROUPS_STANDARD_THEME );
            if ( '' == $theme ) {
                return;
            }
            wp_register_style(
                'tag-groups-css-frontend-structure',
                TAG_GROUPS_PLUGIN_URL . '/assets/css/jquery-ui.structure.min.css',
                array(),
                TAG_GROUPS_VERSION
            );
            $default_themes = explode( ',', TAG_GROUPS_BUILT_IN_THEMES );
            
            if ( in_array( $theme, $default_themes ) ) {
                wp_register_style(
                    'tag-groups-css-frontend-theme',
                    TAG_GROUPS_PLUGIN_URL . '/assets/css/' . $theme . '/jquery-ui.theme.min.css',
                    array(),
                    TAG_GROUPS_VERSION
                );
            } else {
                /*
                 * Load minimized css if available
                 */
                
                if ( file_exists( WP_CONTENT_DIR . '/uploads/' . $theme . '/jquery-ui.theme.min.css' ) ) {
                    wp_register_style(
                        'tag-groups-css-frontend-theme',
                        get_bloginfo( 'wpurl' ) . '/wp-content/uploads/' . $theme . '/jquery-ui.theme.min.css',
                        array(),
                        TAG_GROUPS_VERSION
                    );
                } else {
                    
                    if ( file_exists( WP_CONTENT_DIR . '/uploads/' . $theme . '/jquery-ui.theme.css' ) ) {
                        wp_register_style(
                            'tag-groups-css-frontend-theme',
                            get_bloginfo( 'wpurl' ) . '/wp-content/uploads/' . $theme . '/jquery-ui.theme.css',
                            array(),
                            TAG_GROUPS_VERSION
                        );
                    } else {
                        /*
                         * Fallback: Is this a custom theme of an old version or did we revert to old plugin version?
                         */
                        
                        if ( file_exists( WP_CONTENT_DIR . '/uploads/' . $theme ) ) {
                            $dh = opendir( WP_CONTENT_DIR . '/uploads/' . $theme );
                            if ( !empty($dh) ) {
                                while ( false !== ($filename = @readdir( $dh )) ) {
                                    
                                    if ( preg_match( "/jquery-ui-\\d+\\.\\d+\\.\\d+\\.custom\\.(min\\.)?css/i", $filename ) ) {
                                        wp_register_style(
                                            'tag-groups-css-frontend-theme',
                                            get_bloginfo( 'wpurl' ) . '/wp-content/uploads/' . $theme . '/' . $filename,
                                            array(),
                                            TAG_GROUPS_VERSION
                                        );
                                        break;
                                    }
                                
                                }
                            }
                        } else {
                            TagGroups_Error::log( '[Tag Groups] Error finding %s/uploads/%s', WP_CONTENT_DIR, $theme );
                        }
                    
                    }
                
                }
            
            }
            
            wp_enqueue_style( 'tag-groups-css-frontend-structure' );
            wp_enqueue_style( 'tag-groups-css-frontend-theme' );
            $this->enqueue_frontend_css();
            $this->enqueue_premium_frontend_css();
        }
        
        /**
         * enqueue CSS for free features
         *
         * @return void
         */
        private function enqueue_frontend_css()
        {
            
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                wp_register_style(
                    'tag-groups-css-frontend',
                    TAG_GROUPS_PLUGIN_URL . '/assets/css/frontend.css',
                    array(),
                    TAG_GROUPS_VERSION
                );
            } else {
                wp_register_style(
                    'tag-groups-css-frontend',
                    TAG_GROUPS_PLUGIN_URL . '/assets/css/frontend.min.css',
                    array(),
                    TAG_GROUPS_VERSION
                );
            }
            
            wp_enqueue_style( 'tag-groups-css-frontend' );
        }
        
        /**
         *  enqueue CSS for premium features
         *
         * @return void
         */
        private function enqueue_premium_frontend_css()
        {
            global  $tag_groups_premium_fs_sdk ;
        }
    
    }
}