<?php

/**
* Tag Groups
*
* @package     Tag Groups
* @author      Christoph Amthor
* @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPL-3.0+
*
*/
if ( !class_exists( 'TagGroups_Terms' ) ) {
    class TagGroups_Terms
    {
        /**
         * Get all term IDs that match a group
         *
         * @since 1.23.0
         * @param integer $group_id
         * @return array
         */
        public function get_all_term_ids_no_meta( $group_id = 0 )
        {
            global  $wpdb ;
            $term_ids = array();
            $group_id = (int) $group_id;
            $terms_matching_group = $wpdb->get_results( "SELECT term_id FROM {$wpdb->prefix}terms WHERE term_group = {$group_id}", ARRAY_A );
            foreach ( $terms_matching_group as $term_matching_group ) {
                $term_ids[] = (int) $term_matching_group['term_id'];
            }
            return array_values( $term_ids );
        }
        
        /**
         * Converts all WP-native term_group attributes to the term meta format, if no term meta format was found.
         * Term meta must use _cm_term_group_array as key.
         *
         * @param void
         * @return int number of processed items
         */
        public function convert_to_term_meta( $count_only = false, $offset = null, $length = null )
        {
            
            if ( $count_only ) {
                // In case we restarted the tasks, we need to get fresh data
                $terms = false;
            } else {
                $terms = TagGroups_Transients::get_transient( 'tag_groups_convert_to_term_meta_terms' );
            }
            
            
            if ( $terms === false ) {
                /**
                 * Process only those that don't have any meta
                 */
                $args = array(
                    'hide_empty' => false,
                    'taxoomy'    => TagGroups_Taxonomy::get_enabled_taxonomies(),
                    'meta_query' => array( array(
                    'key'     => '_cm_term_group_array',
                    'compare' => 'NOT EXISTS',
                ) ),
                );
                $terms = get_terms( $args );
                // Try to keep for 10 minutes so that our offset always starts with unprocessed items.
                TagGroups_Transients::set_transient( 'tag_groups_convert_to_term_meta_terms', $terms, 600 );
            }
            
            if ( $count_only ) {
                
                if ( is_array( $terms ) ) {
                    return count( $terms );
                } else {
                    return 0;
                }
            
            }
            if ( isset( $offset ) && isset( $length ) ) {
                $terms = array_slice( $terms, $offset, $length );
            }
            $count = 0;
            if ( is_array( $terms ) ) {
                foreach ( $terms as $term ) {
                    TagGroups_Error::verbose_log( '[Tag Groups Premium] Migrating term meta for term ID %d.', $term->term_id );
                    /**
                     *  Fast way of saving: not necessary to use method save()
                     */
                    $result = update_term_meta( $term->term_id, '_cm_term_group_array', ',' . $term->term_group . ',' );
                    if ( is_int( $result ) || $result === true ) {
                        $count++;
                    }
                }
            }
            return $count;
        }
        
        /**
         * Recommend to run the migration
         *
         * @since 1.24.0
         * @param void
         * @return void
         */
        public static function recommend_to_run_migration()
        {
            global  $tag_groups_premium_fs_sdk ;
            TagGroups_Admin_Notice::add( 'info', sprintf( __( 'Please <a %s>click here to run the migration routines</a> to make sure we have migrated all tags.', 'tag-groups' ), 'href="' . admin_url( 'admin.php?page=tag-groups-settings-troubleshooting&process-tasks=migratetermmeta&task-set-name=Migration' ) . '"' ) );
        }
        
        /**
         * Check if we need to run the migration of terms
         *
         * @since 1.24.0
         * @param void
         * @return void
         */
        public static function check_if_we_need_to_run_migration()
        {
            global  $tag_group_terms ;
            TagGroups_Error::verbose_log( '[Tag Groups] Checking if we should migrate terms.' );
            $count = $tag_group_terms->convert_to_term_meta( true );
            
            if ( $count ) {
                TagGroups_Error::verbose_log( '[Tag Groups] %d terms should be migrated.', $count );
                // $tag_group_terms->convert_to_term_meta();
                wp_schedule_single_event( time() + 2, 'tag_groups_run_term_migration' );
                if ( $count > 1000 ) {
                    // If there's a lot to do, we also want to show the admin notice
                    self::recommend_to_run_migration();
                }
            }
        
        }
        
        /**
         * executes the routines to add the required term meta
         *
         *
         * @param  void
         * @return void
         */
        public static function run_term_migration()
        {
            global  $tag_group_terms ;
            /**
             * Add group affiliation to the term meta; term_group will be only secondary
             */
            TagGroups_Error::verbose_log( '[Tag Groups] Migrating terms.' );
            $start_time = microtime( true );
            $count_term = $tag_group_terms->convert_to_term_meta();
            TagGroups_Error::verbose_log( '[Tag Groups] %d term(s) migrated in %d milliseconds.', $count_term, round( (microtime( true ) - $start_time) * 1000 ) );
            if ( $count_term > 0 ) {
                $tag_group_terms->clear_term_cache();
            }
        }
        
        /**
         * Removes non-existent groups from term meta
         *
         * @param boolean $count_only
         * @param integer $offset
         * @param integer $length
         * @return integer
         */
        public function remove_missing_groups( $count_only = false, $offset = null, $length = null )
        {
            global  $tag_group_groups ;
            $group_ids = $tag_group_groups->get_group_ids();
            $count = 0;
            
            if ( $count_only ) {
                // In case we restarted the tasks, we need to get fresh data
                $term_ids = false;
            } else {
                $term_ids = TagGroups_Transients::get_transient( 'tag_groups_remove_missing_groups' );
            }
            
            
            if ( $term_ids === false ) {
                $args = array(
                    'hide_empty' => false,
                    'taxonomy'   => TagGroups_Taxonomy::get_enabled_taxonomies(),
                    'fields'     => 'ids',
                    'meta_query' => array( array(
                    'key'     => '_cm_term_group_array',
                    'compare' => 'EXISTS',
                ) ),
                );
                $term_ids = get_terms( $args );
                // Try to keep for 10 minutes so that our offset always starts with unprocessed items.
                TagGroups_Transients::set_transient( 'tag_groups_remove_missing_groups', $term_ids, 600 );
            }
            
            if ( $count_only ) {
                
                if ( is_array( $term_ids ) ) {
                    return count( $term_ids );
                } else {
                    return 0;
                }
            
            }
            if ( isset( $offset ) && isset( $length ) ) {
                $term_ids = array_slice( $term_ids, $offset, $length );
            }
            if ( is_array( $term_ids ) ) {
                foreach ( $term_ids as $term_id ) {
                    $changed = false;
                    $groups = get_term_meta( $term_id, '_cm_term_group_array', true );
                    
                    if ( ',,' == $groups ) {
                        // fixing results of bug in version <= 1.23.0
                        $groups_a = array( 0 );
                        TagGroups_Error::verbose_log( '[Tag Groups Premium] We remove an empty group from the term meta of term ID %d.', $term_id );
                        $changed = true;
                    } else {
                        $groups_a = explode( ',', $groups );
                        // remove empty values
                        $groups_a = array_filter( $groups_a, function ( $v ) {
                            return $v != '';
                        } );
                        foreach ( $groups_a as $key => $group ) {
                            
                            if ( !in_array( $group, $group_ids ) ) {
                                // This group id doesn't exist -> we remove it from the term meta
                                TagGroups_Error::verbose_log( '[Tag Groups Premium] We remove a non-existent group ID %d from the term meta of term ID %d.', $group, $term_id );
                                unset( $groups_a[$key] );
                                $changed = true;
                            }
                        
                        }
                    }
                    
                    
                    if ( $changed ) {
                        if ( count( $groups_a ) == 0 ) {
                            $groups_a = array( 0 );
                        }
                        // We need to update the term meta.
                        update_term_meta( $term_id, '_cm_term_group_array', ',' . implode( ',', $groups_a ) . ',' );
                        $count++;
                    }
                
                }
            }
            return $count;
        }
        
        /**
         * Sort groups in the term meta - this is needed for sorting by term group on the tags admin page
         *
         * @param boolean $count_only
         * @param integer $offset
         * @param integer $length
         * @return integer
         */
        public function sort_groups(
            $force_rerun = true,
            $count_only = false,
            $offset = null,
            $length = null
        )
        {
            global  $tag_group_groups ;
            $count = 0;
            
            if ( $force_rerun || $count_only ) {
                // In case we restarted the tasks, we need to get fresh data
                $term_ids = false;
            } else {
                $term_ids = TagGroups_Transients::get_transient( 'tag_groups_sort_groups' );
            }
            
            
            if ( $term_ids === false ) {
                $args = array(
                    'hide_empty' => false,
                    'taxonomy'   => TagGroups_Taxonomy::get_enabled_taxonomies(),
                    'fields'     => 'ids',
                    'meta_query' => array( array(
                    'key'     => '_cm_term_group_array',
                    'compare' => 'EXISTS',
                ) ),
                );
                $term_ids = get_terms( $args );
                // Try to keep for 10 minutes so that our offset always starts with unprocessed items.
                TagGroups_Transients::set_transient( 'tag_groups_sort_groups', $term_ids, 600 );
            }
            
            if ( $count_only ) {
                
                if ( is_array( $term_ids ) ) {
                    return count( $term_ids );
                } else {
                    return 0;
                }
            
            }
            if ( isset( $offset ) && isset( $length ) ) {
                $term_ids = array_slice( $term_ids, $offset, $length );
            }
            if ( is_array( $term_ids ) ) {
                foreach ( $term_ids as $term_id ) {
                    $groups = get_term_meta( $term_id, '_cm_term_group_array', true );
                    $groups_a = explode( ',', $groups );
                    // remove empty values and zeros
                    $groups_a = array_filter( $groups_a, function ( $v ) {
                        return $v != '';
                    } );
                    $groups_a = array_map( 'intval', $groups_a );
                    
                    if ( array_search( 0, $groups_a ) ) {
                        $groups_sorted = array( 0 );
                    } else {
                        $groups_sorted = array();
                        foreach ( $tag_group_groups->get_positions() as $group => $position ) {
                            if ( array_search( $group, $groups_a ) !== false ) {
                                $groups_sorted[] = $group;
                            }
                        }
                    }
                    
                    $groups_sorted = array_values( $groups_sorted );
                    $groups_a = array_values( $groups_a );
                    
                    if ( $groups_sorted !== $groups_a ) {
                        // comparing order
                        TagGroups_Error::verbose_log( '[Tag Groups Premium] Group order changed in meta of term %d.', $term_id );
                        if ( count( $groups_sorted ) == 0 ) {
                            $groups_sorted = array( 0 );
                        }
                        // We need to update the term meta.
                        update_term_meta( $term_id, '_cm_term_group_array', ',' . implode( ',', $groups_sorted ) . ',' );
                        $count++;
                    }
                
                }
            }
            return $count;
        }
        
        /**
         * Deletes the transients that are relevant for terms and groups
         *
         * To be called only if general purging of cache is needed. Otherwise use hooks.
         *
         * @param int $rebuild_in_seconds Turned off if less than 0.
         * @return void
         */
        public function clear_term_cache( $rebuild_in_seconds = 10 )
        {
            $this->clear_post_count_transient( $rebuild_in_seconds );
            $languages = apply_filters( 'wpml_active_languages', NULL, '' );
            
            if ( !empty($languages) ) {
                foreach ( $languages as $language_code => $language_info ) {
                    TagGroups_Transients::delete_transient( 'tag_groups_group_terms-' . $language_code );
                }
            } else {
                TagGroups_Transients::delete_transient( 'tag_groups_group_terms' );
            }
            
            TagGroups_Transients::delete_transient( 'tag_groups_post_terms' );
            TagGroups_Transients::delete_transient( 'tag_groups_post_types' );
            TagGroups_Transients::delete_transient( 'tag_groups_post_ids_groups' );
        }
        
        /**
         * Clears the transient of post counts per group for terms
         *
         * @param integer $rebuild_in_seconds
         * @return void
         */
        public function clear_post_count_transient( $rebuild_in_seconds = 5 )
        {
            global  $tag_groups_premium_fs_sdk ;
            $languages = apply_filters( 'wpml_active_languages', NULL, '' );
            
            if ( !empty($languages) ) {
                foreach ( $languages as $language_code => $language_info ) {
                    TagGroups_Transients::delete_transient( 'tag_groups_post_counts-' . $language_code );
                }
            } else {
                TagGroups_Transients::delete_transient( 'tag_groups_post_counts' );
            }
            
            if ( $rebuild_in_seconds < 0 ) {
                return;
            }
        }
        
        /**
         * Helper for natural sorting of names
         *
         * Inspired by _wp_object_name_sort_cb
         *
         * @param array $terms
         * @param string $order asc or desc
         * @return array
         */
        public function natural_sorting( $terms, $order )
        {
            $factor = ( 'desc' == strtolower( $order ) ? -1 : 1 );
            // "use" requires PHP 5.3+
            uasort( $terms, function ( $a, $b ) use( $factor ) {
                return $factor * strnatcasecmp( $a->name, $b->name );
            } );
            return $terms;
        }
        
        /**
         * Modifies the query to show only terms that belong to particular tag group
         *
         * @param array $args
         * @param array|int|string $group_ids Tag Group IDs (array of integers or comma-separated list of integers)
         * @param string $relation Logical relation between the Tag Group IDs (and|or)
         * @return array
         */
        public function modify_query_args( $args, $group_ids = null, $relation = 'OR' )
        {
            global  $tag_group_groups ;
            if ( empty($group_ids) ) {
                return $args;
            }
            if ( !is_array( $group_ids ) ) {
                $group_ids = explode( ',', $group_ids );
            }
            $group_ids = array_map( 'intval', $group_ids );
            // intval also trims spaces
            if ( strtoupper( $relation ) != 'OR' ) {
                $relation = 'AND';
            }
            /**
             * searching for not-assigned terms
             */
            
            if ( in_array( 0, $group_ids ) ) {
                $args['meta_query'] = array(
                    'relation' => 'OR',
                    array(
                    'key'     => '_cm_term_group_array',
                    'value'   => ',0,',
                    'compare' => 'LIKE',
                ),
                    array(
                    'key'     => '_cm_term_group_array',
                    'compare' => 'NOT EXISTS',
                ),
                );
                return $args;
            }
            
            $meta_query = array(
                'relation' => $relation,
            );
            $group_ids = array_intersect( $group_ids, $tag_group_groups->get_group_ids() );
            
            if ( count( $group_ids ) == 0 ) {
                // never matches -> create dummy condition that never is true
                $meta_query[] = array(
                    'key'     => '_cm_term_group_array_dummy',
                    'compare' => 'EXISTS',
                );
            } else {
                foreach ( $group_ids as $group_id ) {
                    $meta_query[] = array(
                        'key'     => '_cm_term_group_array',
                        'value'   => ',' . $group_id . ',',
                        'compare' => 'LIKE',
                    );
                }
            }
            
            $args['meta_query'] = $meta_query;
            return $args;
        }
        
        /**
         * Filter to enable term_order for orderby
         *
         * @param string $orderby
         * @param array $query_vars
         * @return array
         */
        public function enable_terms_order( $orderby, $query_vars )
        {
            return ( $query_vars['orderby'] == 'term_order' ? 'term_order' : $orderby );
        }
    
    }
}