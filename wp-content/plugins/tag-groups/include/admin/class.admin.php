<?php

/**
* @package     Tag Groups
* @author      Christoph Amthor
* @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPL-3.0+
*/

if ( !class_exists( 'TagGroups_Admin' ) ) {
    class TagGroups_Admin
    {
        function __construct()
        {
        }
        
        /**
         * Initial settings after calling the plugin
         * Effective only for admin backend
         */
        // static function admin_init() {
        //   if ( ! is_admin() ) {
        //     return false;
        //   }
        // }
        /**
         * Adds the submenus and the settings page to the admin backend
         */
        static function register_menus()
        {
            global  $tag_groups_premium_fs_sdk ;
            // Add the main menu
            add_menu_page(
                __( 'Home', 'tag-groups' ),
                'Tag Groups',
                'manage_options',
                'tag-groups-settings',
                array( 'TagGroups_Settings', 'settings_page_home' ),
                'dashicons-tag',
                '99.01'
            );
            // Define the menu structure
            $tag_groups_admin_structure = array(
                0  => array(
                'title'    => __( 'Home', 'tag-groups' ),
                'slug'     => 'tag-groups-settings',
                'parent'   => 'tag-groups-settings',
                'user_can' => 'manage_options',
                'function' => array( 'TagGroups_Settings', 'settings_page_home' ),
            ),
                1  => array(
                'title'    => __( 'Taxonomies', 'tag-groups' ),
                'slug'     => 'tag-groups-settings-taxonomies',
                'parent'   => 'tag-groups-settings',
                'user_can' => 'manage_options',
                'function' => array( 'TagGroups_Settings', 'settings_page_taxonomies' ),
            ),
                3  => array(
                'title'    => __( 'Front End', 'tag-groups' ),
                'slug'     => 'tag-groups-settings-front-end',
                'parent'   => 'tag-groups-settings',
                'user_can' => 'manage_options',
                'function' => array( 'TagGroups_Settings', 'settings_page_front_end' ),
            ),
                4  => array(
                'title'    => __( 'Back End', 'tag-groups' ),
                'slug'     => 'tag-groups-settings-back-end',
                'parent'   => 'tag-groups-settings',
                'user_can' => 'manage_options',
                'function' => array( 'TagGroups_Settings', 'settings_page_back_end' ),
            ),
                5  => array(
                'title'    => __( 'Tools', 'tag-groups' ),
                'slug'     => 'tag-groups-settings-tools',
                'parent'   => 'tag-groups-settings',
                'user_can' => 'manage_options',
                'function' => array( 'TagGroups_Settings', 'settings_page_tools' ),
            ),
                6  => array(
                'title'    => __( 'Troubleshooting', 'tag-groups' ),
                'slug'     => 'tag-groups-settings-troubleshooting',
                'parent'   => 'tag-groups-settings',
                'user_can' => 'manage_options',
                'function' => array( 'TagGroups_Settings', 'settings_page_troubleshooting' ),
            ),
                7  => array(
                'title'    => __( 'Premium', 'tag-groups' ),
                'slug'     => 'tag-groups-settings-premium',
                'parent'   => 'tag-groups-settings',
                'user_can' => 'manage_options',
                'function' => array( 'TagGroups_Settings', 'settings_page_premium' ),
            ),
                8  => array(
                'title'    => __( 'About', 'tag-groups' ),
                'slug'     => 'tag-groups-settings-about',
                'parent'   => 'tag-groups-settings',
                'user_can' => 'manage_options',
                'function' => array( 'TagGroups_Settings', 'settings_page_about' ),
            ),
                9  => array(
                'title'    => __( 'First Steps', 'tag-groups' ),
                'slug'     => 'tag-groups-settings-first-steps',
                'parent'   => null,
                'user_can' => 'manage_options',
                'function' => array( 'TagGroups_Settings', 'settings_page_onboarding' ),
            ),
                10 => array(
                'title'    => __( 'Setup Wizard', 'tag-groups' ),
                'slug'     => 'tag-groups-settings-setup-wizard',
                'parent'   => null,
                'user_can' => 'manage_options',
                'function' => array( 'TagGroups_Settings', 'settings_page_setup_wizard' ),
            ),
            );
            // hook for premium plugin to modify the menu
            $tag_groups_admin_structure = apply_filters( 'tag_groups_admin_structure', $tag_groups_admin_structure );
            // make sure they all have the right order
            ksort( $tag_groups_admin_structure );
            // register the menus and pages
            foreach ( $tag_groups_admin_structure as $tag_groups_admin_page ) {
                add_submenu_page(
                    $tag_groups_admin_page['parent'],
                    $tag_groups_admin_page['title'],
                    $tag_groups_admin_page['title'],
                    $tag_groups_admin_page['user_can'],
                    $tag_groups_admin_page['slug'],
                    $tag_groups_admin_page['function']
                );
            }
            // for each registered taxonomy a tag group admin page
            $tag_group_taxonomies = TagGroups_Options::get_option( 'tag_group_taxonomy', array( 'post_tag' ) );
            $tag_group_role_edit_groups = 'edit_pages';
            $tag_group_post_types = TagGroups_Taxonomy::post_types_from_taxonomies( $tag_group_taxonomies );
            foreach ( $tag_group_post_types as $post_type ) {
                
                if ( 'post' == $post_type ) {
                    $post_type_query = '';
                } else {
                    $post_type_query = '?post_type=' . $post_type;
                }
                
                $submenu_page = add_submenu_page(
                    'edit.php' . $post_type_query,
                    'Tag Group Admin',
                    'Tag Group Admin',
                    $tag_group_role_edit_groups,
                    'tag-groups_' . $post_type,
                    array( 'TagGroups_Admin', 'render_group_administration' )
                );
            }
        }
        
        /**
         * Create the html to add tags to tag groups on single tag view (after clicking tag for editing)
         * @param type $tag
         */
        static function render_edit_tag_menu( $tag )
        {
            global  $tag_groups_premium_fs_sdk, $tag_group_groups ;
            $screen = get_current_screen();
            
            if ( 'post' == $screen->post_type ) {
                $url_post_type = '';
            } else {
                $url_post_type = '&post_type=' . $screen->post_type;
            }
            
            $tag_group_admin_url = admin_url( 'edit.php?page=tag-groups_' . $screen->post_type . $url_post_type );
            $term_groups = $tag_group_groups->get_all_with_position_as_key();
            unset( $term_groups[0] );
            $tg_term = new TagGroups_Term( $tag );
            $view = new TagGroups_View( 'admin/new_tag_main' );
            $view->set( array(
                'term_groups'         => $term_groups,
                'screen'              => $screen,
                'tg_term'             => $tg_term,
                'tag_group_admin_url' => $tag_group_admin_url,
            ) );
            $view->render();
        }
        
        /**
         * Create the html to assign tags to tag groups upon new tag creation (left of the table)
         * @param type $tag
         */
        static function render_new_tag_menu( $tag )
        {
            global  $tag_groups_premium_fs_sdk, $tag_group_groups ;
            $screen = get_current_screen();
            $term_groups = $tag_group_groups->get_all_with_position_as_key();
            unset( $term_groups[0] );
            $tag_group_new_tag_default_groups = array();
            $view = new TagGroups_View( 'admin/new_tag_from_list' );
            $view->set( array(
                'term_groups'            => $term_groups,
                'screen'                 => $screen,
                'new_tag_default_groups' => $tag_group_new_tag_default_groups,
            ) );
            $view->render();
        }
        
        /**
         * adds a custom column to the table of tags/terms
         * thanks to http://coderrr.com/add-columns-to-a-taxonomy-terms-table/
         * @global object $wp
         * @param array $columns
         * @return string
         */
        static function add_taxonomy_columns( $columns )
        {
            global  $wp ;
            $new_order = ( isset( $_GET['order'] ) && $_GET['order'] == 'asc' && isset( $_GET['orderby'] ) && $_GET['orderby'] == 'term_group' ? 'desc' : 'asc' );
            $screen = get_current_screen();
            
            if ( !empty($screen) ) {
                $taxonomy = $screen->taxonomy;
                $link = add_query_arg( array(
                    'orderby'  => 'term_group',
                    'order'    => $new_order,
                    'taxonomy' => $taxonomy,
                ), admin_url( "edit-tags.php" . $wp->request ) );
                $link_escaped = esc_url( $link );
                $columns['term_group'] = '<a href="' . $link_escaped . '"><span>' . __( 'Tag Groups', 'tag-groups' ) . '</span><span class="sorting-indicator"></span></a>';
            } else {
                $columns['term_group'] = '';
            }
            
            return $columns;
        }
        
        /**
         * adds data into custom column of the table for each row
         * thanks to http://coderrr.com/add-columns-to-a-taxonomy-terms-table/
         * @param type $a
         * @param type $b
         * @param type $term_id
         * @return string
         */
        static function add_taxonomy_column_content( $content = '', $column_name = '', $term_id = 0 )
        {
            global  $tag_group_groups ;
            if ( 'term_group' != $column_name ) {
                return $content;
            }
            
            if ( !empty($_REQUEST['taxonomy']) ) {
                $taxonomy = sanitize_title( $_REQUEST['taxonomy'] );
            } else {
                return '';
            }
            
            $term = get_term( $term_id, $taxonomy );
            
            if ( isset( $term ) ) {
                $term_o = new TagGroups_Term( $term );
                return implode( ', ', $tag_group_groups->get_labels_by_position( $term_o->get_groups() ) );
            } else {
                return '';
            }
        
        }
        
        /**
         * Modify the term query so that we can sort by the term meta
         *
         * @param [type] $pieces
         * @param [type] $taxonomies
         * @param [type] $args
         * @return void
         */
        static function sort_taxonomy_columns( $pieces, $taxonomies, $args )
        {
            global  $wpdb ;
            $screen = get_current_screen();
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            if ( empty($screen) || !in_array( $screen->taxonomy, $enabled_taxonomies ) ) {
                return $pieces;
            }
            if ( empty($_GET['orderby']) || 'term_group' != $_GET['orderby'] ) {
                return $pieces;
            }
            
            if ( isset( $_GET['order'] ) && strtoupper( $_GET['order'] ) == 'DESC' ) {
                $order = "DESC";
            } else {
                $order = 'ASC';
            }
            
            $pieces['join'] .= ' INNER JOIN ' . $wpdb->termmeta . ' AS tm ON t.term_id = tm.term_id ';
            $pieces['where'] .= ' AND tm.meta_key = "_cm_term_group_array"';
            $pieces['orderby'] = ' ORDER BY tm.meta_value ';
            $pieces['order'] = $order;
            return $pieces;
        }
        
        /**
         *
         * processing actions defined in bulk_admin_footer()
         * credits http://www.foxrunsoftware.net
         * @global int $tg_update_edit_term_group_called
         * @return void
         */
        static function do_bulk_action()
        {
            global  $tg_update_edit_term_group_called, $tag_group_groups ;
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            $screen = get_current_screen();
            $taxonomy = $screen->taxonomy;
            if ( is_object( $screen ) && !in_array( $taxonomy, $enabled_taxonomies ) ) {
                return;
            }
            $show_filter_tags = TagGroups_Options::get_option( 'tag_group_show_filter_tags', 1 );
            
            if ( $show_filter_tags ) {
                $tag_group_tags_filter = TagGroups_Options::get_option( 'tag_group_tags_filter', array() );
                /*
                 * Processing the filter
                 * Values come as POST (via menu, precedence) or GET (via link from group admin)
                 */
                
                if ( isset( $_POST['term-filter'] ) ) {
                    $term_filter = (int) $_POST['term-filter'];
                } elseif ( isset( $_GET['term-filter'] ) ) {
                    $term_filter = (int) $_GET['term-filter'];
                    // We need to remove the term-filter piece, or it will stay forever
                    $sendback = remove_query_arg( array( 'term-filter' ), $_SERVER['REQUEST_URI'] );
                }
                
                
                if ( isset( $term_filter ) ) {
                    
                    if ( '-1' == $term_filter ) {
                        unset( $tag_group_tags_filter[$taxonomy] );
                        update_option( 'tag_group_tags_filter', $tag_group_tags_filter );
                    } else {
                        $tag_group_tags_filter[$taxonomy] = $term_filter;
                        update_option( 'tag_group_tags_filter', $tag_group_tags_filter );
                        /*
                         * Modify the query
                         */
                        add_action(
                            'terms_clauses',
                            array( 'TagGroups_Admin', 'modify_terms_query' ),
                            10,
                            3
                        );
                    }
                    
                    
                    if ( isset( $sendback ) ) {
                        // remove filter that destroys WPML's "&lang="
                        remove_all_filters( 'wp_redirect' );
                        // escaping $sendback
                        wp_redirect( esc_url_raw( $sendback ) );
                        exit;
                    }
                
                } else {
                    /*
                     * If filter is set, make sure to modify the query
                     */
                    if ( isset( $tag_group_tags_filter[$taxonomy] ) ) {
                        add_action(
                            'terms_clauses',
                            array( 'TagGroups_Admin', 'modify_terms_query' ),
                            10,
                            3
                        );
                    }
                }
            
            }
            
            $wp_list_table = _get_list_table( 'WP_Terms_List_Table' );
            $action = $wp_list_table->current_action();
            $allowed_actions = array( 'assign' );
            if ( !in_array( $action, $allowed_actions ) ) {
                return;
            }
            if ( isset( $_REQUEST['delete_tags'] ) ) {
                $term_ids = $_REQUEST['delete_tags'];
            }
            
            if ( isset( $_REQUEST['term-group-top'] ) ) {
                $term_group = (int) $_REQUEST['term-group-top'];
            } else {
                return;
            }
            
            $sendback = remove_query_arg( array( 'assigned', 'deleted' ), wp_get_referer() );
            if ( !$sendback ) {
                $sendback = admin_url( 'edit-tags.php?taxonomy=' . $taxonomy );
            }
            
            if ( empty($term_ids) ) {
                $sendback = add_query_arg( array(
                    'number_assigned' => 0,
                    'group_id'        => $term_group,
                ), $sendback );
                $sendback = remove_query_arg( array(
                    'action',
                    'action2',
                    'tags_input',
                    'post_author',
                    'comment_status',
                    'ping_status',
                    '_status',
                    'post',
                    'bulk_edit',
                    'post_view'
                ), $sendback );
                // escaping $sendback
                wp_redirect( esc_url_raw( $sendback ) );
                exit;
            }
            
            $pagenum = $wp_list_table->get_pagenum();
            $sendback = add_query_arg( 'paged', $pagenum, $sendback );
            $tg_update_edit_term_group_called = true;
            // skip update_edit_term_group()
            switch ( $action ) {
                case 'assign':
                    $assigned = 0;
                    foreach ( $term_ids as $term_id ) {
                        $term = new TagGroups_Term( $term_id );
                        
                        if ( false !== $term ) {
                            
                            if ( 0 == $term_group ) {
                                if ( $term->get_groups() != array( 0 ) ) {
                                    $term->remove_all_groups()->save();
                                }
                            } else {
                                if ( !in_array( $term_group, $term->get_groups() ) ) {
                                    $term->add_group( $term_group )->save();
                                }
                            }
                            
                            $assigned++;
                        }
                    
                    }
                    
                    if ( 0 == $term_group ) {
                        $message = _n(
                            'The term has been removed from all groups.',
                            sprintf( '%d terms have been removed from all groups.', number_format_i18n( (int) $assigned ) ),
                            (int) $assigned,
                            'tag-groups'
                        );
                    } else {
                        $tg_group = new TagGroups_Group( $term_group );
                        $message = _n(
                            sprintf( 'The term has been assigned to the group %s.', '<i>' . $tg_group->get_label() . '</i>' ),
                            sprintf( '%d terms have been assigned to the group %s.', number_format_i18n( (int) $assigned ), '<i>' . $tg_group->get_label() . '</i>' ),
                            (int) $assigned,
                            'tag-groups'
                        );
                    }
                    
                    break;
                default:
                    // Need to show a message?
                    exit;
                    break;
            }
            TagGroups_Admin_Notice::add( 'success', $message );
            $sendback = remove_query_arg( array(
                'action',
                'action2',
                'tags_input',
                'post_author',
                'comment_status',
                'ping_status',
                '_status',
                'post',
                'bulk_edit',
                'post_view'
            ), $sendback );
            wp_redirect( esc_url_raw( $sendback ) );
            exit;
        }
        
        /**
         * Filter the tags on the tag page
         *
         * @return void
         */
        static function do_filter_tags()
        {
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            $screen = get_current_screen();
            $taxonomy = $screen->taxonomy;
            if ( is_object( $screen ) && !in_array( $taxonomy, $enabled_taxonomies ) ) {
                return;
            }
            $show_filter_tags = TagGroups_Options::get_option( 'tag_group_show_filter_tags', 1 );
            
            if ( $show_filter_tags ) {
                $tag_group_tags_filter = TagGroups_Options::get_option( 'tag_group_tags_filter', array() );
                /*
                 * Processing the filter
                 * Values come as POST (via menu, precedence) or GET (via link from group admin)
                 */
                
                if ( isset( $_POST['term-filter'] ) ) {
                    $term_filter = (int) $_POST['term-filter'];
                } elseif ( isset( $_GET['term-filter'] ) ) {
                    $term_filter = (int) $_GET['term-filter'];
                }
                
                
                if ( isset( $term_filter ) ) {
                    
                    if ( '-1' == $term_filter ) {
                        unset( $tag_group_tags_filter[$taxonomy] );
                        update_option( 'tag_group_tags_filter', $tag_group_tags_filter );
                    } else {
                        $tag_group_tags_filter[$taxonomy] = $term_filter;
                        update_option( 'tag_group_tags_filter', $tag_group_tags_filter );
                        /*
                         * Modify the query
                         */
                        add_action(
                            'terms_clauses',
                            array( 'TagGroups_Admin', 'modify_terms_query' ),
                            10,
                            3
                        );
                    }
                    
                    
                    if ( isset( $sendback ) ) {
                        // We need to remove the term-filter piece, or it will stay forever
                        // Also return to first page, trying to solve error "A variable mismatch has been detected."
                        $sendback = remove_query_arg( array( 'term-filter', 'paged' ) );
                        // let WP use $_SERVER['REQUEST_URI'] and apply whitelisting etc. if desired
                        // remove filter that destroys WPML's "&lang="
                        remove_all_filters( 'wp_redirect' );
                        // escaping $sendback
                        wp_redirect( esc_url_raw( $sendback ) );
                        exit;
                    }
                
                } else {
                    /*
                     * If filter is set, make sure to modify the query
                     */
                    if ( isset( $tag_group_tags_filter[$taxonomy] ) ) {
                        add_action(
                            'terms_clauses',
                            array( 'TagGroups_Admin', 'modify_terms_query' ),
                            10,
                            3
                        );
                    }
                }
            
            }
        
        }
        
        /**
         * modifies Quick Edit link to call JS when clicked
         * thanks to http://shibashake.com/WordPress-theme/expand-the-WordPress-quick-edit-menu
         * @param array $actions
         * @param object $tag
         * @return array
         */
        static function expand_quick_edit_link( $actions, $tag )
        {
            $screen = get_current_screen();
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            if ( is_object( $screen ) && !in_array( $screen->taxonomy, $enabled_taxonomies ) ) {
                return $actions;
            }
            $term_o = new TagGroups_Term( $tag );
            $groups = htmlspecialchars( json_encode( $term_o->get_groups() ) );
            $nonce = wp_create_nonce( 'tag-groups-nonce' );
            $actions['inline hide-if-no-js'] = '<a href="javascript:void(0)" class="editinline" title="';
            $actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline', 'tag-groups' ) ) . '" ';
            $actions['inline hide-if-no-js'] .= " onclick=\"set_inline_tag_group_selected('{$groups}', '{$nonce}')\">";
            $actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit', 'tag-groups' );
            $actions['inline hide-if-no-js'] .= '</a>';
            return $actions;
        }
        
        /**
         * adds JS function that sets the saved tag group for a given element when it's opened in quick edit
         * thanks to http://shibashake.com/WordPress-theme/expand-the-WordPress-quick-edit-menu
         * @return void
         */
        static function render_quick_edit_javascript()
        {
            global  $tag_groups_premium_fs_sdk ;
            $screen = get_current_screen();
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            if ( !in_array( $screen->taxonomy, $enabled_taxonomies ) ) {
                return;
            }
            $view = new TagGroups_View( 'partials/quick_edit_javascript' );
            $view->render();
        }
        
        /**
         * Create the html to assign tags to tag groups directly in tag table ('quick edit')
         * @return type
         */
        static function quick_edit_tag()
        {
            global  $tg_quick_edit_tag_called, $tag_group_groups, $tag_groups_premium_fs_sdk ;
            if ( $tg_quick_edit_tag_called ) {
                return;
            }
            $tg_quick_edit_tag_called = true;
            $screen = get_current_screen();
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            if ( !in_array( $screen->taxonomy, $enabled_taxonomies ) ) {
                return;
            }
            $term_groups = $tag_group_groups->get_all_with_position_as_key();
            unset( $term_groups[0] );
            $view = new TagGroups_View( 'partials/quick_edit_tag' );
            $view->set( array(
                'term_groups' => $term_groups,
                'screen'      => $screen,
            ) );
            $view->render();
        }
        
        /**
         * Saves the term group without the standard tag information
         *
         * @global int $tg_update_edit_term_group_called
         * @param int $term_id
         * @return void
         */
        public static function save_term_group_without_tag_info( $term_id )
        {
            // next lines to prevent infinite loops when the hook edit_term is called again from the function wp_update_term
            global  $tg_update_edit_term_group_called ;
            if ( $tg_update_edit_term_group_called ) {
                return;
            }
            self::save_term_group( $term_id );
        }
        
        /**
         * Saves the term group and the standard tag information
         *
         * Called when editing an existing tag
         *
         * @global int $tg_update_edit_term_group_called
         * @param int $term_id
         * @return void
         */
        public static function save_term_group_with_tag_info( $term_id )
        {
            // next lines to prevent infinite loops when the hook edit_term is called again from the function wp_update_term
            global  $tg_update_edit_term_group_called ;
            if ( $tg_update_edit_term_group_called ) {
                return;
            }
            self::save_term_group( $term_id );
            /**
             *   If necessary we also save default WP term properties.
             *   Make sure we have a taxonomy
             */
            
            if ( isset( $_POST['tag-groups-taxonomy'] ) ) {
                $taxonomy = sanitize_title( $_POST['tag-groups-taxonomy'] );
                $args = array();
                /**
                 * Save the tag name
                 */
                if ( isset( $_POST['name'] ) && $_POST['name'] != '' ) {
                    // allow zeros
                    $args['name'] = stripslashes( sanitize_text_field( $_POST['name'] ) );
                }
                /**
                 * Save the tag slug
                 */
                if ( isset( $_POST['slug'] ) ) {
                    // allow empty values
                    $args['slug'] = sanitize_title( $_POST['slug'] );
                }
                /**
                 * Save the tag description
                 */
                if ( isset( $_POST['description'] ) ) {
                    // allow empty values
                    /**
                     * Check if the settings require us to omit sanitization
                     */
                    
                    if ( current_user_can( 'unfiltered_html' ) || TagGroups_Options::get_option( 'tag_group_html_description', 0 ) ) {
                        $args['description'] = $_POST['description'];
                    } else {
                        $args['description'] = stripslashes( sanitize_text_field( $_POST['description'] ) );
                    }
                
                }
                /**
                 * Save the parent
                 */
                if ( isset( $_POST['parent'] ) && $_POST['parent'] != '' ) {
                    $args['parent'] = (int) $_POST['parent'];
                }
                /**
                 * Some plugins save also additonal fields. We therefore allow to whitelist further arguments.
                 * example for array usage:
                 * define( 'TAG_GROUPS_ADDITIONAL_TERM_ARGS', array(
                 *  'tag-image' => 'sanitize_text_field'
                 * ));
                 * (min. PHP 5.6)
                 */
                if ( defined( 'TAG_GROUPS_ADDITIONAL_TERM_ARGS' ) ) {
                    
                    if ( is_bool( TAG_GROUPS_ADDITIONAL_TERM_ARGS ) ) {
                        $exclude = array(
                            'action',
                            '_wp_original_http_referer',
                            '_wpnonce',
                            '_wp_http_referer',
                            'term-group',
                            'tag-groups-nonce',
                            'tag-groups-taxonomy'
                        );
                        foreach ( $_POST as $posted_key => $posted_value ) {
                            if ( !in_array( $posted_key, $exclude ) && !isset( $args[$posted_key] ) ) {
                                $args[$posted_key] = $posted_value;
                            }
                        }
                    } elseif ( is_array( TAG_GROUPS_ADDITIONAL_TERM_ARGS ) ) {
                        $permitted_sanitation_functions = array(
                            'intval',
                            'sanitize_email',
                            'sanitize_file_name',
                            'sanitize_html_class',
                            'sanitize_key',
                            'sanitize_meta',
                            'sanitize_mime_type',
                            'sanitize_option',
                            'sanitize_sql_orderby',
                            'sanitize_text_field',
                            'sanitize_textarea_field',
                            'sanitize_title',
                            'sanitize_title_for_query',
                            'sanitize_title_with_dashes',
                            'sanitize_user'
                        );
                        foreach ( TAG_GROUPS_ADDITIONAL_TERM_ARGS as $additonal_arg => $sanitization ) {
                            if ( function_exists( $sanitization ) && in_array( $sanitization, $permitted_sanitation_functions ) ) {
                                $args[$additonal_arg] = call_user_func( $sanitization, $_POST[$additonal_arg] );
                            }
                        }
                    }
                
                }
                wp_update_term( $term_id, $taxonomy, $args );
            }
        
        }
        
        /**
         * Get the $_POSTed value after saving a tag/term and save it in the database
         *
         * Called when creating a new tag or editing an existing tag
         *
         * @param int $term_id
         * @return void
         */
        public static function save_term_group( $term_id )
        {
            global  $tg_update_edit_term_group_called ;
            $screen = get_current_screen();
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            $tg_update_edit_term_group_called = true;
            // if ( ( ! is_object( $screen ) || ! in_array( $screen->taxonomy, $enabled_taxonomies ) ) && ! isset( $_POST['new-tag-created'] ) ) {
            if ( is_object( $screen ) && !in_array( $screen->taxonomy, $enabled_taxonomies ) && !isset( $_POST['new-tag-created'] ) ) {
                return;
            }
            if ( empty($_POST['tag-groups-nonce']) || !wp_verify_nonce( $_POST['tag-groups-nonce'], 'tag-groups-nonce' ) ) {
                return;
            }
            $term = new TagGroups_Term( (int) $term_id );
            
            if ( !empty($_POST['term-group']) ) {
                
                if ( is_array( $_POST['term-group'] ) ) {
                    $term_group = array_map( 'intval', $_POST['term-group'] );
                    if ( $term_group != $term->get_groups() ) {
                        $term->set_group( $term_group )->save();
                    }
                } else {
                    $term_group = (int) $_POST['term-group'];
                    if ( !in_array( $term_group, $term->get_groups() ) ) {
                        $term->set_group( $term_group )->save();
                    }
                }
            
            } else {
                // $_POST['term-group'] won't be set if multi select is empty => evaluate empty value as "unassigned"
                if ( $term->get_groups() != array( 0 ) ) {
                    $term->remove_all_groups()->save();
                }
            }
        
        }
        
        /**
         * WPML: Check if we need to copy group info to the translation
         *
         * Copy the groups of an original term to its translation if a translation is saved
         *
         * @param type $term_id
         * @return type
         */
        public static function maybe_copy_term_group_to_translation( $term_id )
        {
            /**
             * Check if WPML is available
             */
            $default_language_code = apply_filters( 'wpml_default_language', null );
            if ( !isset( $default_language_code ) ) {
                return;
            }
            /**
             * Check if the new tag has no group set or groups set to unassigned
             */
            $term = new TagGroups_Term( $term_id );
            $translated_term_groups = $term->get_groups();
            if ( !empty($translated_term_groups) && $translated_term_groups != array( 0 ) ) {
                return;
            }
            /**
             *   edit-tags.php form
             */
            
            if ( isset( $_POST['icl_tax_post_tag_language'] ) && $_POST['icl_tax_post_tag_language'] != $default_language_code ) {
                
                if ( !empty($_POST['icl_translation_of']) ) {
                    // translated from the default language
                    $original_term_id = $_POST['icl_translation_of'];
                } elseif ( !empty($_POST['icl_trid']) ) {
                    // translated from another translated language
                    $translations = apply_filters( 'wpml_get_element_translations', null, $_POST['icl_trid'] );
                    if ( isset( $translations[$default_language_code]->element_id ) ) {
                        $original_term_id = $translations[$default_language_code]->element_id;
                    }
                }
            
            } elseif ( isset( $_POST['term_language_code'] ) && $_POST['term_language_code'] != $default_language_code && !empty($_POST['trid']) ) {
                $translations = apply_filters( 'wpml_get_element_translations', null, $_POST['trid'] );
                if ( isset( $translations[$default_language_code]->element_id ) ) {
                    $original_term_id = $translations[$default_language_code]->element_id;
                }
            }
            
            
            if ( isset( $original_term_id ) ) {
                $tg_original_term = new TagGroups_Term( $original_term_id );
                $original_term_groups = $tg_original_term->get_groups();
                if ( !empty($original_term_groups) ) {
                    $term->set_group( $original_term_groups )->save();
                }
            }
        
        }
        
        /**
         * Adds a bulk action menu to a term list page
         * credits http://www.foxrunsoftware.net
         * @return void
         */
        static function bulk_admin_footer()
        {
            global  $tag_group_groups ;
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            $screen = get_current_screen();
            if ( is_object( $screen ) && !in_array( $screen->taxonomy, $enabled_taxonomies ) ) {
                return;
            }
            $term_groups = $tag_group_groups->get_all_with_position_as_key();
            $view = new TagGroups_View( 'partials/bulk_admin_footer' );
            $view->set( array(
                'term_groups' => $term_groups,
            ) );
            $view->render();
        }
        
        /**
         * Adds a filter menu to a term list page
         * @return void
         */
        static function filter_admin_footer()
        {
            global  $tag_group_groups ;
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            $screen = get_current_screen();
            if ( is_object( $screen ) && !in_array( $screen->taxonomy, $enabled_taxonomies ) ) {
                return;
            }
            if ( !TagGroups_Options::get_option( 'tag_group_show_filter_tags', 1 ) ) {
                return;
            }
            $term_groups = $tag_group_groups->get_all_with_position_as_key();
            $tag_group_tags_filter = TagGroups_Options::get_option( 'tag_group_tags_filter', array() );
            
            if ( isset( $tag_group_tags_filter[$screen->taxonomy] ) ) {
                $tag_filter = $tag_group_tags_filter[$screen->taxonomy];
                
                if ( $tag_filter > 0 ) {
                    // check if group exists (could be deleted since last time the filter was set)
                    $tg_group = new TagGroups_Group( $tag_filter );
                    if ( !$tg_group->exists() ) {
                        $tag_filter = -1;
                    }
                }
            
            } else {
                $tag_filter = -1;
            }
            
            $view = new TagGroups_View( 'partials/filter_admin_footer' );
            $view->set( array(
                'term_groups' => $term_groups,
                'tag_filter'  => $tag_filter,
            ) );
            $view->render();
        }
        
        /**
         * Adds a button to reset the filter on the tags page, in case JavaScript breaks
         *
         * @since 1.25.0
         * @param void
         * @return void
         */
        static function add_admin_footer_text( $text )
        {
            $screen = get_current_screen();
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            
            if ( !empty($screen) && 'edit-tags' == $screen->base && TagGroups_Options::get_option( 'tag_group_show_filter_tags', 1 ) && in_array( $screen->taxonomy, $enabled_taxonomies ) ) {
                $view = new TagGroups_View( 'partials/admin_footer' );
                $view->set( 'reset_url', esc_url( add_query_arg( 'term-filter', -1 ) ) );
                return $view->return_html() . $text;
            }
            
            return $text;
        }
        
        /**
         * Adds a pull-down menu to the filters above the posts.
         * Based on the code by Ohad Raz, http://wordpress.stackexchange.com/q/45436/2487
         * License: Creative Commons Share Alike
         * @return void
         */
        static function add_post_filter()
        {
            global  $tag_group_groups ;
            if ( !TagGroups_Options::get_option( 'tag_group_show_filter', 1 ) ) {
                return;
            }
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            $post_type = ( isset( $_GET['post_type'] ) ? sanitize_title( $_GET['post_type'] ) : 'post' );
            
            if ( count( array_intersect( $enabled_taxonomies, get_object_taxonomies( $post_type ) ) ) ) {
                $term_groups = $tag_group_groups->get_all_term_group_label();
                $current_term_group = ( isset( $_GET['tg_filter_posts_value'] ) ? sanitize_text_field( $_GET['tg_filter_posts_value'] ) : '' );
                $view = new TagGroups_View( 'admin/post_filter' );
                $view->set( array(
                    'term_groups'        => $term_groups,
                    'current_term_group' => $current_term_group,
                ) );
                $view->render();
            }
        
        }
        
        /**
         * Applies the filter, if used.
         * Based on the code by Ohad Raz, http://wordpress.stackexchange.com/q/45436/2487
         * License: Creative Commons Share Alike
         *
         * @global type $pagenow
         * @param type $query
         * @return type
         */
        static function apply_post_filter( $query )
        {
            global  $pagenow, $tag_groups_premium_fs_sdk ;
            if ( $pagenow != 'edit.php' ) {
                return $query;
            }
            $show_filter_posts = TagGroups_Options::get_option( 'tag_group_show_filter', 1 );
            if ( !$show_filter_posts ) {
                return $query;
            }
            
            if ( isset( $_GET['post_type'] ) ) {
                $post_type = sanitize_title( $_GET['post_type'] );
            } else {
                $post_type = 'post';
            }
            
            /**
             * Losing here the filter by language from Polylang, but currently no other way to show any posts when combining tax_query and meta_query
             */
            unset( $query->query_vars['tax_query'] );
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            // note: removed restriction count( $tg_taxonomy ) <= 1 - rather let user figure out if the result works
            $taxonomy_intersect = array_intersect( $enabled_taxonomies, get_object_taxonomies( $post_type ) );
            
            if ( count( $taxonomy_intersect ) && isset( $_GET['tg_filter_posts_value'] ) && $_GET['tg_filter_posts_value'] !== '' ) {
                $tg_group = new TagGroups_Group( (int) $_GET['tg_filter_posts_value'] );
                $tags = $tg_group->get_group_terms( $taxonomy_intersect, true, 'ids' );
                
                if ( empty($tags) ) {
                    $query->query_vars['tag__in'] = array( 0 );
                    // produce an empty list
                } else {
                    $query->query_vars['tag__in'] = $tags;
                }
            
            }
            
            return $query;
        }
        
        /**
         * AJAX handler to get a feed
         */
        static function ajax_get_feed()
        {
            
            if ( isset( $_REQUEST['url'] ) ) {
                $url = esc_url_raw( $_REQUEST['url'] );
            } else {
                $url = '';
            }
            
            if ( strpos( $url, 'https://chattymango.com/' ) !== 0 ) {
                TagGroups_Error::log( '[Tag Groups] Wrong feed URL: ' . $url );
            }
            
            if ( isset( $_REQUEST['amount'] ) ) {
                $amount = (int) $_REQUEST['amount'];
            } else {
                $amount = 5;
            }
            
            /**
             * Assuming that the posts URL is the $url minus the trailing /feed
             */
            $posts_url = preg_replace( '/(.+)feed\\/?/i', '$1', $url );
            $rss = new TagGroups_Feed();
            if ( defined( 'WP_DEBUG' ) ) {
                $rss->set_debug( WP_DEBUG );
            }
            $rss->set_url( $url )->set_posts_url( $posts_url )->set_amount( $amount );
            echo  json_encode( $rss->get_html() ) ;
            wp_die();
        }
        
        /**
         * AJAX handler to manage Tag Groups
         */
        static function ajax_manage_groups()
        {
            global  $tag_groups_premium_fs_sdk, $tag_group_groups ;
            
            if ( isset( $_REQUEST['task'] ) ) {
                $task = $_REQUEST['task'];
            } else {
                $task = 'refresh';
            }
            
            
            if ( isset( $_REQUEST['taxonomy'] ) ) {
                
                if ( is_array( $_REQUEST['taxonomy'] ) ) {
                    $taxonomy = array_map( 'sanitize_title', $_REQUEST['taxonomy'] );
                } else {
                    $taxonomy = sanitize_title( $_REQUEST['taxonomy'] );
                }
            
            } else {
                $taxonomy = array( 'post_tag' );
            }
            
            $message = '';
            $tag_group_role_edit_groups = 'edit_pages';
            
            if ( $task != 'refresh' && $task != 'test' && !(current_user_can( $tag_group_role_edit_groups ) && wp_verify_nonce( $_REQUEST['nonce'], 'tg_groups_management' )) ) {
                TagGroups_Admin::ajax_send_error( 'Security check', $task );
                exit;
            }
            
            
            if ( isset( $_REQUEST['position'] ) ) {
                $position = (int) $_REQUEST['position'];
            } else {
                $position = 0;
            }
            
            
            if ( isset( $_REQUEST['new_position'] ) ) {
                $new_position = (int) $_REQUEST['new_position'];
            } else {
                $new_position = 0;
            }
            
            if ( isset( $_REQUEST['start_position'] ) ) {
                $start_position = (int) $_REQUEST['start_position'];
            }
            if ( empty($start_position) || $start_position < 1 ) {
                $start_position = 1;
            }
            if ( isset( $_REQUEST['end_position'] ) ) {
                $end_position = (int) $_REQUEST['end_position'];
            }
            if ( empty($end_position) || $end_position < 1 ) {
                $end_position = 1;
            }
            $tg_group = new TagGroups_Group();
            switch ( $task ) {
                case "sortup":
                    $tag_group_groups->sort( 'up' )->save();
                    $message = __( 'The groups have been sorted alphabetically.', 'tag-groups' );
                    break;
                case "sortdown":
                    $tag_group_groups->sort( 'down' )->save();
                    $message = __( 'The groups have been sorted alphabetically.', 'tag-groups' );
                    break;
                case "new":
                    if ( isset( $_REQUEST['label'] ) ) {
                        $label = stripslashes( sanitize_text_field( $_REQUEST['label'] ) );
                    }
                    
                    if ( empty($label) ) {
                        $message = __( 'The label cannot be empty.', 'tag-groups' );
                        TagGroups_Admin::ajax_send_error( $message, $task );
                    } elseif ( $tg_group->find_by_label( $label ) ) {
                        $message = sprintf( __( 'A tag group with the label \'%s\' already exists, or the label has not changed. Please choose another one or go back.', 'tag-groups' ), $label );
                        TagGroups_Admin::ajax_send_error( $message, $task );
                    } else {
                        $tg_group->create( $label, $position + 1 );
                        $message = sprintf( __( 'A new tag group with the label \'%s\' has been created!', 'tag-groups' ), $label );
                    }
                    
                    break;
                case "update":
                    if ( isset( $_REQUEST['label'] ) ) {
                        $label = stripslashes( sanitize_text_field( $_REQUEST['label'] ) );
                    }
                    
                    if ( empty($label) ) {
                        $message = __( 'The label cannot be empty.', 'tag-groups' );
                        TagGroups_Admin::ajax_send_error( $message, $task );
                    } elseif ( $tg_group->find_by_label( $label ) ) {
                        
                        if ( !empty($position) && $position == $tg_group->get_position() ) {
                            // Label hast not changed, just ignore
                        } else {
                            $message = sprintf( __( 'A tag group with the label \'%s\' already exists.', 'tag-groups' ), $label );
                            TagGroups_Admin::ajax_send_error( $message, $task );
                        }
                    
                    } else {
                        
                        if ( !empty($position) ) {
                            if ( $tg_group->find_by_position( $position ) ) {
                                $tg_group->set_label( $label )->save();
                            }
                        } else {
                            TagGroups_Admin::ajax_send_error( 'error: invalid position: ' . $position, $task );
                        }
                        
                        $message = sprintf( __( 'The tag group with the label \'%s\' has been saved!', 'tag-groups' ), $label );
                    }
                    
                    break;
                case "delete":
                    
                    if ( !empty($position) && $tg_group->find_by_position( $position ) ) {
                        $message = sprintf( __( 'A tag group with the id %1$s and the label \'%2$s\' has been deleted.', 'tag-groups' ), $tg_group->get_group_id(), $tg_group->get_label() );
                        $tg_group->delete();
                    } else {
                        TagGroups_Admin::ajax_send_error( 'error: invalid position: ' . $position, $task );
                    }
                    
                    break;
                case "up":
                    if ( $position > 1 && $tg_group->find_by_position( $position ) ) {
                        if ( $tg_group->move_to_position( $position - 1 ) !== false ) {
                            $tg_group->save();
                        }
                    }
                    break;
                case "down":
                    if ( $position < $tag_group_groups->get_max_position() && $tg_group->find_by_position( $position ) ) {
                        if ( $tg_group->move_to_position( $position + 1 ) !== false ) {
                            $tg_group->save();
                        }
                    }
                    break;
                case "move":
                    if ( $new_position < 1 ) {
                        $new_position = 1;
                    }
                    if ( $new_position > $tag_group_groups->get_max_position() ) {
                        $new_position = $tag_group_groups->get_max_position();
                    }
                    if ( $position == $new_position ) {
                        break;
                    }
                    if ( $tg_group->find_by_position( $position ) ) {
                        if ( $tg_group->move_to_position( $new_position ) !== false ) {
                            $tg_group->save();
                        }
                    }
                    break;
                case "refresh":
                    // do nothing here
                    break;
                case 'test':
                    echo  json_encode( array(
                        'data'         => 'success',
                        'supplemental' => array(
                        'message' => 'This is the regular Ajax response.',
                    ),
                    ) ) ;
                    exit;
                    break;
            }
            $number_of_term_groups = $tag_group_groups->get_number_of_term_groups() - 1;
            // "not assigned" won't be displayed
            if ( $start_position > $number_of_term_groups ) {
                $start_position = $number_of_term_groups;
            }
            $items_per_page = self::get_items_per_page();
            // calculate start and end positions
            $start_position = floor( ($start_position - 1) / $items_per_page ) * $items_per_page + 1;
            
            if ( $start_position + $items_per_page - 1 < $number_of_term_groups ) {
                $end_position = $start_position + $items_per_page - 1;
            } else {
                $end_position = $number_of_term_groups;
            }
            
            echo  json_encode( array(
                'data'         => 'success',
                'supplemental' => array(
                'task'           => $task,
                'message'        => $message,
                'nonce'          => wp_create_nonce( 'tg_groups_management' ),
                'start_position' => $start_position,
                'groups'         => TagGroups_Admin::assemble_group_table( $start_position, $end_position, $taxonomy ),
                'max_number'     => $number_of_term_groups,
            ),
            ) ) ;
            exit;
        }
        
        /**
         *  Rerturns an error message to AJAX
         */
        static function ajax_send_error( $message = 'error', $task = 'unknown' )
        {
            echo  json_encode( array(
                'data'         => 'error',
                'supplemental' => array(
                'message' => $message,
                'task'    => $task,
            ),
            ) ) ;
            exit;
        }
        
        /**
         * Assemble the content of the table of tag groups for AJAX
         */
        static function assemble_group_table( $start_position, $end_position, $taxonomy )
        {
            global  $tag_group_groups ;
            $term_groups = $tag_group_groups->get_all_with_position_as_key();
            $output = array();
            if ( count( $term_groups ) > 1 ) {
                for ( $i = $start_position ;  $i <= $end_position ;  $i++ ) {
                    
                    if ( !empty($term_groups[$i]) ) {
                        $tg_group = new TagGroups_Group( $term_groups[$i]['term_group'] );
                        array_push( $output, array(
                            'id'     => $term_groups[$i]['term_group'],
                            'label'  => $term_groups[$i]['label'],
                            'amount' => $tg_group->get_number_of_terms( $taxonomy ),
                        ) );
                    }
                
                }
            }
            return $output;
        }
        
        /**
         * Outputs a table on a submenu page where you can add, delete, change tag groups, their labels and their order.
         *
         * @param void
         * @return void
         */
        static function render_group_administration()
        {
            $tag_group_show_filter_tags = TagGroups_Options::get_option( 'tag_group_show_filter_tags', 1 );
            //tags
            $tag_group_show_filter = TagGroups_Options::get_option( 'tag_group_show_filter', 1 );
            // posts
            if ( $tag_group_show_filter_tags || $tag_group_show_filter ) {
                $this_post_type = preg_replace( '/tag-groups_(.+)/', '$1', sanitize_title( $_GET['page'] ) );
            }
            $first_enabled_taxonomy = '';
            $post_type_taxonomies = get_object_taxonomies( $this_post_type );
            $taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies( $post_type_taxonomies );
            /**
             * Check if the tag filter is activated
             */
            if ( $tag_group_show_filter_tags ) {
                // get first of taxonomies that are associated with that $post_type
                /**
                 * Show the link to the taxonomy filter only if there is only one taxonomy for this post type (otherwise ambiguous where to link)
                 */
                if ( !empty($taxonomies) && count( $taxonomies ) == 1 ) {
                    $first_enabled_taxonomy = TagGroups_Base::get_first_element( $taxonomies );
                }
            }
            /**
             * In case we use the WPML plugin: consider the language
             */
            $current_language = TagGroups_WPML::get_current_language();
            
            if ( $current_language ) {
                
                if ( 'all' == $current_language ) {
                    $wpml_piece = '&lang=' . (string) apply_filters( 'wpml_default_language', NULL );
                } else {
                    $wpml_piece = '&lang=' . $current_language;
                }
            
            } else {
                $wpml_piece = '';
            }
            
            
            if ( $this_post_type == 'post' ) {
                $post_type_piece = '';
            } else {
                $post_type_piece = '&post_type=' . $this_post_type;
            }
            
            $items_per_page = self::get_items_per_page();
            $protocol = ( isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://' );
            $post_url = ( empty($tag_group_show_filter) ? '' : admin_url( 'edit.php?post_type=' . $this_post_type . $wpml_piece, $protocol ) );
            $tags_url = ( empty($first_enabled_taxonomy) ? '' : admin_url( 'edit-tags.php?taxonomy=' . $first_enabled_taxonomy . $wpml_piece . $post_type_piece, $protocol ) );
            $settings_url = admin_url( 'admin.php?page=tag-groups-settings' );
            $admin_url = admin_url( 'admin-ajax.php', $protocol );
            if ( isset( $_GET['lang'] ) ) {
                $admin_url = add_query_arg( 'lang', sanitize_key( $_GET['lang'] ), $admin_url );
            }
            
            if ( 'all' == $current_language ) {
                $view = new TagGroups_View( 'partials/language_notice' );
                $view->render();
            }
            
            $view = new TagGroups_View( 'admin/tag_groups_admin' );
            $view->set( array(
                'tag_group_show_filter' => $tag_group_show_filter,
                'post_url'              => $post_url,
                'tags_url'              => $tags_url,
                'items_per_page'        => $items_per_page,
                'settings_url'          => $settings_url,
                'admin_url'             => $admin_url,
                'taxonomies'            => $taxonomies,
            ) );
            $view->render();
        }
        
        /**
         *
         * Modifies the query to retrieve tags for filtering in the backend.
         *
         * @param array $pieces
         * @param array $taxonomies
         * @param array $args
         * @return array
         */
        static function modify_terms_query( $pieces, $taxonomies, $args )
        {
            $taxonomy = TagGroups_Base::get_first_element( $taxonomies );
            if ( empty($taxonomy) || is_array( $taxonomy ) ) {
                $taxonomy = 'post_tag';
            }
            $show_filter_tags = TagGroups_Options::get_option( 'tag_group_show_filter_tags', 1 );
            
            if ( $show_filter_tags ) {
                $tag_group_tags_filter = TagGroups_Options::get_option( 'tag_group_tags_filter', array() );
                
                if ( isset( $tag_group_tags_filter[$taxonomy] ) ) {
                    $group_id = $tag_group_tags_filter[$taxonomy];
                    
                    if ( $group_id > 0 ) {
                        // check if group exists (could be deleted since last time the filter was set)
                        $tg_group = new TagGroups_Group( $group_id );
                        if ( !$tg_group->exists() ) {
                            $group_id = -1;
                        }
                    }
                
                } else {
                    $group_id = -1;
                }
                
                
                if ( $group_id > -1 ) {
                    $tg_group = new TagGroups_Group( $group_id );
                    $mq_sql = $tg_group->terms_clauses();
                    
                    if ( !empty($pieces['join']) ) {
                        $pieces['join'] .= $mq_sql['join'];
                    } else {
                        $pieces['join'] = $mq_sql['join'];
                    }
                    
                    
                    if ( !empty($pieces['where']) ) {
                        $pieces['where'] .= $mq_sql['where'];
                    } else {
                        $pieces['where'] = $mq_sql['where'];
                    }
                
                }
            
            }
            
            return $pieces;
        }
        
        /**
         * Adds Settings link to plugin list
         *
         * @param array $links
         * @return array
         */
        static function add_plugin_settings_link( $links )
        {
            $settings_link = '<a href="' . admin_url( 'admin.php?page=tag-groups-settings' ) . '">' . __( 'Settings', 'tag-groups' ) . '</a>';
            array_unshift( $links, $settings_link );
            
            if ( defined( 'TAG_GROUPS_PLUGIN_IS_FREE' ) && TAG_GROUPS_PLUGIN_IS_FREE ) {
                $settings_link = '<a href="' . admin_url( 'admin.php?page=tag-groups-settings-premium' ) . '"><span style="color:#3A0;">' . __( 'Try Premium', 'tag-groups' ) . '</span></a>';
                array_unshift( $links, $settings_link );
            }
            
            return $links;
        }
        
        /**
         * Returns the items per page on the tag groups screen
         *
         *
         * @param void
         * @return int
         */
        public static function get_items_per_page()
        {
            global  $tag_groups_premium_fs_sdk ;
            $items_per_page = TAG_GROUPS_ITEMS_PER_PAGE;
            return $items_per_page;
        }
        
        /**
         * Add a warning if the WPML/Polylang language switch is set to "all"
         *
         *
         * @param void
         * @return void
         */
        public static function add_language_notice()
        {
            $screen = get_current_screen();
            if ( !$screen || 'edit-tags' !== $screen->base && 'term' !== $screen->base ) {
                return;
            }
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            if ( !in_array( $screen->taxonomy, $enabled_taxonomies ) ) {
                return;
            }
            
            if ( 'all' == TagGroups_WPML::get_current_language() ) {
                $view = new TagGroups_View( 'partials/language_notice' );
                $view->render();
            }
        
        }
        
        /**
         * Add inline styling to the tags page
         *
         * @param void
         * @return void
         */
        public static function add_tag_page_styling()
        {
            $view = new TagGroups_View( 'partials/tag_page_inline_style' );
            $view->render();
        }
    
    }
    // class
}
