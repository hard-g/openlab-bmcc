<?php

/**
* @package     Tag Groups
* @author      Christoph Amthor
* @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPL-3.0+
*/

if ( !class_exists( 'TagGroups_Shortcode' ) ) {
    class TagGroups_Shortcode
    {
        /*
         * Register the shortcodes with WordPress
         */
        static function register()
        {
            /**
             * Tabbed tag cloud
             */
            add_shortcode( 'tag_groups_cloud', array( 'TagGroups_Shortcode_Tabs', 'tag_groups_cloud' ) );
            // add_shortcode( 'tag_groups_tabs', array( 'TagGroups_Shortcode_Tabs', 'tag_groups_cloud' ) );
            if ( function_exists( 'register_block_type' ) ) {
                register_block_type( 'chatty-mango/tag-groups-cloud-tabs', array(
                    'attributes'      => TagGroups_Shortcode_Tabs::$attributes,
                    'render_callback' => array( 'TagGroups_Shortcode_Tabs', 'tag_groups_cloud' ),
                ) );
            }
            /**
             * Accordion tag cloud
             */
            add_shortcode( 'tag_groups_accordion', array( 'TagGroups_Shortcode_Accordion', 'tag_groups_accordion' ) );
            if ( function_exists( 'register_block_type' ) ) {
                register_block_type( 'chatty-mango/tag-groups-cloud-accordion', array(
                    'attributes'      => TagGroups_Shortcode_Accordion::$attributes,
                    'render_callback' => array( 'TagGroups_Shortcode_Accordion', 'tag_groups_accordion' ),
                ) );
            }
            /**
             * Tabbed tag cloud with first letters as tabs
             */
            add_shortcode( 'tag_groups_alphabet_tabs', array( 'TagGroups_Shortcode_Alphabet_Tabs', 'tag_groups_alphabet_tabs' ) );
            if ( function_exists( 'register_block_type' ) ) {
                register_block_type( 'chatty-mango/tag-groups-alphabet-tabs', array(
                    'attributes'      => TagGroups_Shortcode_Alphabet_Tabs::$attributes,
                    'render_callback' => array( 'TagGroups_Shortcode_Alphabet_Tabs', 'tag_groups_alphabet_tabs' ),
                ) );
            }
            /**
             * Group info
             */
            add_shortcode( 'tag_groups_info', array( 'TagGroups_Shortcode_Info', 'tag_groups_info' ) );
            /**
             * Tags listed under group names
             */
            add_shortcode( 'tag_groups_tag_list', array( 'TagGroups_Shortcode_Tag_List', 'tag_groups_tag_list' ) );
            if ( function_exists( 'register_block_type' ) ) {
                register_block_type( 'chatty-mango/tag-groups-tag-list', array(
                    'attributes'      => TagGroups_Shortcode_Tag_List::$attributes,
                    'render_callback' => array( 'TagGroups_Shortcode_Tag_List', 'tag_groups_tag_list' ),
                ) );
            }
            /**
             * Tags listed under first letters
             */
            add_shortcode( 'tag_groups_alphabetical_index', array( 'TagGroups_Shortcode_Alphabetical_Index', 'tag_groups_alphabetical_index' ) );
            if ( function_exists( 'register_block_type' ) ) {
                register_block_type( 'chatty-mango/tag-groups-alphabetical-tag-index', array(
                    'attributes'      => TagGroups_Shortcode_Alphabetical_Index::$attributes,
                    'render_callback' => array( 'TagGroups_Shortcode_Alphabetical_Index', 'tag_groups_alphabetical_index' ),
                ) );
            }
        }
        
        /**
         * Makes sure that shortcodes work in text widgets.
         */
        static function maybe_do_shortcode_in_widgets()
        {
            $tag_group_shortcode_widget = TagGroups_Options::get_option( 'tag_group_shortcode_widget', 0 );
            if ( $tag_group_shortcode_widget ) {
                add_filter( 'widget_text', 'do_shortcode' );
            }
        }
        
        /**
         * Calculates the font size for the cloud tag for a particular tag ($min, $max and $size with same unit, e.g. pt.)
         *
         * @param int $count
         * @param int $min
         * @param int $max
         * @param int $smallest
         * @param int $largest
         * @return int
         */
        static function font_size(
            $count,
            $min,
            $max,
            $smallest,
            $largest
        )
        {
            
            if ( $max > $min ) {
                $size = round( ($count - $min) * ($largest - $smallest) / ($max - $min) + $smallest );
            } else {
                $size = round( $smallest );
            }
            
            return $size;
        }
        
        /**
         * A piece of script for the tabs to work, including options, for each individual cloud
         *
         * @param type $id
         * @param type $option_mouseover
         * @param type $option_collapsible
         * @return string
         */
        static function custom_js_tabs(
            $id = null,
            $option_mouseover = null,
            $option_collapsible = null,
            $option_active = null,
            $delay = false
        )
        {
            $options = array();
            
            if ( isset( $option_mouseover ) ) {
                if ( $option_mouseover ) {
                    $options['event'] = 'mouseover';
                }
            } else {
                if ( TagGroups_Options::get_option( 'tag_group_mouseover', 0 ) ) {
                    $options['event'] = 'mouseover';
                }
            }
            
            
            if ( isset( $option_collapsible ) ) {
                if ( $option_collapsible ) {
                    $options['collapsible'] = true;
                }
            } else {
                if ( TagGroups_Options::get_option( 'tag_group_collapsible', 0 ) ) {
                    $options['collapsible'] = true;
                }
            }
            
            if ( isset( $option_active ) ) {
                
                if ( $option_active >= 0 ) {
                    $options['active'] = $option_active;
                } else {
                    $options['active'] = false;
                }
            
            }
            // if ( $delay ) {
            //   $options['create'] = 'tagGroupsShow';
            // }
            // if ( empty( $options ) ) {
            //   $options_js_object = '';
            // } else {
            // $options_js_object = json_encode( (object) $options );
            // }
            
            if ( empty($id) ) {
                $id = 'tag-groups-cloud-tabs';
            } else {
                $id = TagGroups_Base::sanitize_html_classes( $id );
            }
            
            $view = new TagGroups_View( 'shortcodes/js_tabs_snippet' );
            $view->set( array(
                'id'                => $id,
                'options_js_object' => json_encode( (object) $options ),
                'delay'             => $delay,
            ) );
            return $view->return_html();
        }
        
        /**
         * A piece of script for the tabs to work, including options, for each individual cloud
         *
         * @param type $id
         * @param type $option_mouseover
         * @param type $option_collapsible
         * @return string
         */
        static function custom_js_accordion(
            $id = null,
            $option_mouseover = null,
            $option_collapsible = null,
            $option_active = null,
            $heightstyle = null,
            $delay = false
        )
        {
            $options = array();
            
            if ( isset( $option_mouseover ) ) {
                if ( $option_mouseover ) {
                    $options['event'] = 'mouseover';
                }
            } else {
                if ( TagGroups_Options::get_option( 'tag_group_mouseover', 0 ) ) {
                    $options['event'] = 'mouseover';
                }
            }
            
            
            if ( isset( $option_collapsible ) ) {
                if ( $option_collapsible ) {
                    $options['collapsible'] = true;
                }
            } else {
                if ( TagGroups_Options::get_option( 'tag_group_collapsible', 0 ) ) {
                    $options['collapsible'] = true;
                }
            }
            
            if ( !empty($heightstyle) ) {
                $options['heightStyle'] = sanitize_title( $heightstyle );
            }
            if ( isset( $option_active ) ) {
                
                if ( $option_active >= 0 ) {
                    $options['active'] = $option_active;
                } else {
                    $options['active'] = false;
                }
            
            }
            // if ( empty( $options ) ) {
            //   $options_js_object = '';
            // } else {
            //   $options_js_object = "{\n" . implode( ",\n", $options ) . "\n}";
            // }
            
            if ( !isset( $id ) ) {
                $id = 'tag-groups-cloud-accordion';
            } else {
                $id = TagGroups_Base::sanitize_html_classes( $id );
            }
            
            $view = new TagGroups_View( 'shortcodes/js_accordion_snippet' );
            $view->set( array(
                'id'                => $id,
                'options_js_object' => json_encode( (object) $options ),
                'delay'             => $delay,
            ) );
            return $view->return_html();
        }
        
        /*
         *  find minimum and maximum of quantity of posts for each tag
         *
         * @param
         * @return array $min_max
         */
        static function determine_min_max(
            $tags,
            $amount,
            $tag_group_ids,
            $include_tags_post_id_groups = null,
            $data = null,
            $post_counts = null
        )
        {
            $min_max = array();
            $count_amount = array();
            foreach ( $tag_group_ids as $tag_group_id ) {
                $count_amount[$tag_group_id] = 0;
                $min_max[$tag_group_id]['min'] = 0;
                $min_max[$tag_group_id]['max'] = 0;
            }
            if ( empty($tags) || !is_array( $tags ) ) {
                return $min_max;
            }
            foreach ( $tags as $tag ) {
                $post_count_per_group = array();
                $post_count_total = 0;
                $term_o = new TagGroups_Term( $tag );
                
                if ( $term_o->is_in_group( $tag_group_ids ) ) {
                    // check if tag has posts for a particular group
                    
                    if ( !empty($data) && !empty($post_counts) ) {
                        foreach ( $tag_group_ids as $tag_group_id ) {
                            
                            if ( isset( $post_counts[$tag->term_id][$tag_group_id] ) ) {
                                $post_count_per_group[$tag_group_id] = $post_counts[$tag->term_id][$tag_group_id];
                                $post_count_total += $post_counts[$tag->term_id][$tag_group_id];
                            } else {
                                $post_count_per_group[$tag_group_id] = $tag->count;
                                $post_count_total += $tag->count;
                            }
                        
                        }
                    } else {
                        $post_count_total = $tag->count;
                    }
                    
                    
                    if ( $post_count_total > 0 ) {
                        /**
                         * Use only groups that are in the list
                         */
                        $term_groups = array_intersect( $term_o->get_groups(), $tag_group_ids );
                        foreach ( $term_groups as $term_group ) {
                            
                            if ( isset( $post_count_per_group[$term_group] ) ) {
                                $tag_count_this_group = $post_count_per_group[$term_group];
                            } else {
                                $tag_count_this_group = $post_count_total;
                            }
                            
                            if ( 0 == $amount || $count_amount[$term_group] < $amount ) {
                                
                                if ( empty($include_tags_post_id_groups) || in_array( $tag->term_id, $include_tags_post_id_groups[$term_group] ) ) {
                                    if ( isset( $min_max[$term_group]['max'] ) && $tag_count_this_group > $min_max[$term_group]['max'] ) {
                                        $min_max[$term_group]['max'] = $tag_count_this_group;
                                    }
                                    if ( isset( $min_max[$term_group]['min'] ) && ($tag_count_this_group < $min_max[$term_group]['min'] || 0 == $min_max[$term_group]['min']) ) {
                                        $min_max[$term_group]['min'] = $tag_count_this_group;
                                    }
                                    $count_amount[$term_group]++;
                                }
                            
                            }
                        }
                    }
                
                }
            
            }
            return $min_max;
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
        static function natural_sorting( $terms, $order )
        {
            $factor = ( 'desc' == strtolower( $order ) ? -1 : 1 );
            uasort( $terms, function ( $a, $b ) use( $factor ) {
                return $factor * strnatcasecmp( $a->name, $b->name );
            } );
            return $terms;
        }
        
        /**
         * Helper for (pseudo-)random sorting
         *
         *
         * @param array $terms
         * @return array
         */
        static function random_sorting( $terms )
        {
            uasort( $terms, function ( $a, $b ) {
                return 2 * mt_rand( 0, 1 ) - 1;
            } );
            return $terms;
        }
        
        /**
         * Sort terms
         *
         * @param array $terms
         * @param string $orderby
         * @param string $order
         * @return array
         */
        static function sort( $terms, $orderby, $order )
        {
            if ( count( $terms ) == 0 ) {
                return $terms;
            }
            if ( 'random' == $orderby ) {
                return self::random_sorting( $terms );
            }
            if ( 'natural' == $orderby ) {
                return self::natural_sorting( $terms, $order );
            }
            $factor = ( 'desc' == strtolower( $order ) ? -1 : 1 );
            /**
             * name
             * count
             * slug
             * term_id
             * description
             * term_order
             */
            switch ( $orderby ) {
                case 'name':
                    uasort( $terms, function ( $a, $b ) use( $factor ) {
                        return $factor * strnatcasecmp( $a->name, $b->name );
                    } );
                    break;
                case 'count':
                    uasort( $terms, function ( $a, $b ) use( $factor ) {
                        return $factor * (( $a->count > $b->count ? 1 : -1 ));
                    } );
                    break;
                case 'slug':
                    uasort( $terms, function ( $a, $b ) use( $factor ) {
                        return $factor * strcmp( $a->slug, $b->slug );
                    } );
                    break;
                case 'term_id':
                    uasort( $terms, function ( $a, $b ) use( $factor ) {
                        return $factor * (( $a->term_id > $b->term_id ? 1 : -1 ));
                    } );
                    break;
                case 'description':
                    uasort( $terms, function ( $a, $b ) use( $factor ) {
                        return $factor * strcmp( $a->description, $b->description );
                    } );
                    break;
                case 'term_order':
                    
                    if ( !isset( $terms[0]->term_order ) ) {
                        TagGroups_Error::log( '[Tag Groups] Field term_order not found.' );
                        return $terms;
                    }
                    
                    uasort( $terms, function ( $a, $b ) use( $factor ) {
                        return $factor * (( $a->term_order > $b->term_order ? 1 : -1 ));
                    } );
                    break;
                default:
                    break;
            }
            return $terms;
        }
        
        /**
         * Adds all IDs of groups that provide tags for a given post
         *
         *
         * @param int $post_id
         * @param array $taxonomies
         * @param array $include_array
         * @return array
         */
        static function add_groups_of_post( $post_id, $taxonomies, $include_array )
        {
            $post_id_terms = array();
            /*
             *  get all tags of this post
             */
            foreach ( $taxonomies as $taxonomy_item ) {
                $terms = get_the_terms( (int) $post_id, $taxonomy_item );
                if ( !empty($terms) && is_array( $terms ) ) {
                    $post_id_terms = array_merge( $post_id_terms, $terms );
                }
            }
            /*
             *  get all involved groups, append them to $include
             */
            if ( $post_id_terms ) {
                foreach ( $post_id_terms as $term ) {
                    $term_o = new TagGroups_Term( $term );
                    if ( !$term_o->is_in_group( $include_array ) ) {
                        $include_array = array_merge( $include_array, $term_o->get_groups() );
                    }
                }
            }
            return $include_array;
        }
        
        /**
         * Adds the tags of a particular post to the tags of a tag cloud
         *
         *
         * @param int $post_id
         * @param array $taxonomies
         * @param array $posttags
         * @param string $assigned_class
         * @return array
         */
        static function add_tags_of_post(
            $post_id,
            $taxonomies,
            $posttags,
            $assigned_class
        )
        {
            global  $tag_groups_premium_fs_sdk ;
            $post_id_terms = array();
            $assigned_terms = array();
            $include_tags_post_id_groups = array();
            /*
             *  we have a particular post ID
             *  get all tags of this post
             */
            foreach ( $taxonomies as $taxonomy_item ) {
                $terms = get_the_terms( (int) $post_id, $taxonomy_item );
                /*
                 *  merging the results of selected taxonomies
                 */
                if ( !empty($terms) && is_array( $terms ) ) {
                    $post_id_terms = array_merge( $post_id_terms, $terms );
                }
            }
            /*
             *  clean all others from $posttags
             */
            foreach ( $posttags as $key => $tag ) {
                $found = false;
                foreach ( $post_id_terms as $id_tag ) {
                    
                    if ( $tag->term_id == $id_tag->term_id ) {
                        $found = true;
                        break;
                    }
                
                }
                
                if ( !empty($assigned_class) ) {
                    /*
                     *  Keep all terms but mark for different styling
                     */
                    if ( $found ) {
                        $assigned_terms[$tag->term_id] = true;
                    }
                } else {
                    /*
                     *  Remove unused terms.
                     */
                    if ( !$found ) {
                        unset( $posttags[$key] );
                    }
                }
            
            }
            return array(
                'assigned_terms'              => $assigned_terms,
                'posttags'                    => $posttags,
                'include_tags_post_id_groups' => $include_tags_post_id_groups,
            );
        }
        
        /**
         * Sorts the tags array according to the post count of a particular group
         *
         * @since 1.21.3
         * @param array $posttags
         * @param int $group_id
         * @param string $order
         * @return return type
         */
        public static function sort_within_groups(
            $posttags,
            $group_id,
            $post_counts,
            $order = 'asc'
        )
        {
            uasort( $posttags, function ( $a, $b ) use( $post_counts, $group_id, $order ) {
                if ( !isset( $post_counts[$a->term_id][$group_id] ) ) {
                    $post_counts[$a->term_id][$group_id] = $a->count;
                }
                if ( !isset( $post_counts[$b->term_id][$group_id] ) ) {
                    $post_counts[$b->term_id][$group_id] = $b->count;
                }
                if ( $post_counts[$a->term_id][$group_id] == $post_counts[$b->term_id][$group_id] ) {
                    return 0;
                }
                
                if ( 'asc' == strtolower( $order ) ) {
                    return ( $post_counts[$a->term_id][$group_id] > $post_counts[$b->term_id][$group_id] ? 1 : -1 );
                } else {
                    return ( $post_counts[$a->term_id][$group_id] > $post_counts[$b->term_id][$group_id] ? -1 : 1 );
                }
            
            } );
            return $posttags;
        }
        
        /**
         * Extract the first letter of a name
         *
         * @param string $tag tag name
         * @return string
         */
        public static function get_first_letter( $tag, $ignore_accents = false )
        {
            $first_letter = mb_strtolower( mb_substr( $tag, 0, 1 ) );
            if ( $ignore_accents ) {
                $first_letter = remove_accents( $first_letter );
            }
            return $first_letter;
        }
        
        /**
         * Extract the first letters of the tags
         *
         * @param array $posttags names of tags in WP array
         * @return array
         */
        public static function extract_alphabet( $posttags, $ignore_accents = false )
        {
            $alphabet = array();
            foreach ( $posttags as $tag ) {
                $first_letter = self::get_first_letter( $tag->name, $ignore_accents );
                if ( !in_array( $first_letter, $alphabet ) ) {
                    $alphabet[] = $first_letter;
                }
            }
            return $alphabet;
        }
        
        /**
         * Sorts the alphabet according to the current sort order
         *
         * @param array $alphabet first letters
         * @return array
         */
        public static function sort_alphabet( $alphabet, $locale = '' )
        {
            if ( empty($locale) ) {
                $locale = get_locale();
            }
            
            if ( class_exists( 'Collator' ) ) {
                // Collator is more reliable
                $collator = new Collator( $locale );
                
                if ( !(array) $collator ) {
                    $error_message = intl_get_error_message();
                    if ( 'U_USING_DEFAULT_WARNING' == $error_message ) {
                        $error_message = sprintf( 'Collator used the default locale data ("%s"); neither the requested locale "%s" nor any of its fall back locales could be found.', $collator->getLocale( Locale::ACTUAL_LOCALE ), $locale );
                    }
                    if ( 'U_USING_FALLBACK_WARNING' == $error_message ) {
                        $error_message = sprintf( 'Collator used the fall back locale "%s" because the requested locale "%s" could not be found.', $collator->getLocale( Locale::ACTUAL_LOCALE ), $locale );
                    }
                    TagGroups_Error::verbose_log( '[Tag Groups] ' . $error_message );
                }
                
                $collator->sort( $alphabet );
            } else {
                if ( strpos( $locale, ',' ) !== false ) {
                    $locale = array_map( 'trim', explode( ',', $locale ) );
                }
                $result = @setlocale( LC_COLLATE, $locale );
                
                if ( false === $result ) {
                    TagGroups_Error::verbose_log( '[Tag Groups] Cannot set locale %s', $locale );
                } else {
                    sort( $alphabet, SORT_LOCALE_STRING );
                }
            
            }
            
            return $alphabet;
        }
        
        /*
         *  find minimum and maximum of quantity of posts for each tag
         */
        static function determine_min_max_alphabet(
            $tags,
            $amount,
            $alphabet,
            $min_max_per_letter = false,
            $ignore_accents = false
        )
        {
            $min_max = array();
            $count_amount = array();
            
            if ( $min_max_per_letter ) {
                foreach ( $alphabet as $letter ) {
                    $count_amount[$letter] = 0;
                    $min_max[$letter]['min'] = 0;
                    $min_max[$letter]['max'] = 0;
                }
                if ( empty($tags) || !is_array( $tags ) ) {
                    return $min_max;
                }
                foreach ( $tags as $tag ) {
                    $first_letter = self::get_first_letter( $tag->name, $ignore_accents );
                    if ( !in_array( $first_letter, $alphabet ) ) {
                        continue;
                    }
                    if ( $amount > 0 && $count_amount[$first_letter] > $amount ) {
                        continue;
                    }
                    $post_count_total = $tag->count;
                    
                    if ( $post_count_total > 0 ) {
                        if ( isset( $min_max[$first_letter]['max'] ) && $post_count_total > $min_max[$first_letter]['max'] ) {
                            $min_max[$first_letter]['max'] = $post_count_total;
                        }
                        if ( isset( $min_max[$first_letter]['min'] ) && ($post_count_total < $min_max[$first_letter]['min'] || 0 == $min_max[$first_letter]['min']) ) {
                            $min_max[$first_letter]['min'] = $post_count_total;
                        }
                        $count_amount[$first_letter]++;
                    }
                
                }
            } else {
                $absolute_min = 0;
                $absolute_max = 0;
                foreach ( $alphabet as $letter ) {
                    $count_amount[$letter] = 0;
                }
                foreach ( $tags as $tag ) {
                    $first_letter = self::get_first_letter( $tag->name, $ignore_accents );
                    if ( !in_array( $first_letter, $alphabet ) ) {
                        continue;
                    }
                    if ( $count_amount[$first_letter] > $amount ) {
                        continue;
                    }
                    $post_count_total = $tag->count;
                    
                    if ( $post_count_total > 0 ) {
                        if ( $post_count_total > $absolute_max ) {
                            $absolute_max = $post_count_total;
                        }
                        if ( $post_count_total < $absolute_min || 0 == $absolute_min ) {
                            $absolute_min = $post_count_total;
                        }
                        $count_amount[$first_letter]++;
                    }
                
                }
                foreach ( $alphabet as $letter ) {
                    $min_max[$letter]['min'] = $absolute_min;
                    $min_max[$letter]['max'] = $absolute_max;
                }
            }
            
            return $min_max;
        }
        
        /**
         * renders a shortcode output
         *
         * @param string $tag
         * @param array $attr
         * @return string
         */
        static function render( $tag, $attr )
        {
            $whitelist = array( 'tag_groups_cloud' );
            if ( !in_array( $tag, $whitelist ) ) {
                return 'Unidentified shortcode';
            }
            return json_encode( apply_filters(
                'do_shortcode_tag',
                '',
                $tag,
                $attr,
                array()
            ) );
        }
        
        /**
         * decodes a string that has been encoded for Ajax transmission
         *
         * @param string $maybe_encoded_template
         * @return string
         */
        static function decode_string( $maybe_encoded_template )
        {
            if ( '' === $maybe_encoded_template ) {
                return '';
            }
            $maybe_base64_decoded = base64_decode( $maybe_encoded_template, true );
            if ( false === $maybe_base64_decoded ) {
                return $maybe_encoded_template;
            }
            return urldecode( $maybe_base64_decoded );
        }
        
        /**
         * get the taxonomies for the term query, based on the shortcode arguments
         *
         *
         * @param array $requested_taxonomies
         * @param string $shortcode
         * @return array
         */
        static function get_taxonomies( $requested_taxonomies, $shortcode = '' )
        {
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            
            if ( !empty($requested_taxonomies) ) {
                $taxonomies = array_intersect( $enabled_taxonomies, $requested_taxonomies );
                
                if ( empty($taxonomies) ) {
                    TagGroups_Error::log( '[Tag Groups Premium] Wrong taxonomy or taxonomies (%s) in shortcode %s', implode( ',', $requested_taxonomies ), $shortcode );
                    // return ''; // We are forgiving and let the shortcode work with any taxonomy
                    return $requested_taxonomies;
                }
                
                return $taxonomies;
            }
            
            return $enabled_taxonomies;
        }
        
        /**
         * Enable sorting by term_order if needed
         *
         * @param [type] $orderby
         * @return void
         */
        static function maybe_enable_terms_order( $orderby )
        {
            global  $tag_group_terms, $wpdb ;
            
            if ( 'term_order' == $orderby ) {
                $tables = $wpdb->tables();
                $columns = $wpdb->get_col( "DESC {$tables['terms']}", 0 );
                
                if ( !in_array( 'term_order', $columns ) ) {
                    TagGroups_Error::log( '[Tag Groups] If you use orderby=term_order, your database needs to have the term_order column.' );
                    return;
                }
                
                add_filter(
                    'get_terms_orderby',
                    array( $tag_group_terms, 'enable_terms_order' ),
                    10,
                    2
                );
            }
        
        }
        
        /**
         * modifies the term query to return only terms that have a minimum post count
         *
         * @param array $pieces
         * @param array $taxonomies
         * @param array $args
         * @return array
         */
        public static function terms_clauses_threshold( $pieces, $taxonomies, $args )
        {
            if ( empty($args['threshold']) ) {
                return $pieces;
            }
            $threshold = $args['threshold'] - 1;
            /**
             * We first try to find "AND tt.count > 0" and replace the number
             */
            $result = preg_replace( '/(.*AND tt.count > )(\\d+)(.*)/imu', '${1}' . (int) $threshold . '$3', $pieces['where'] );
            
            if ( $result != $pieces['where'] ) {
                /**
                 * we found it
                 */
                $pieces['where'] = $result;
            } else {
                /**
                 * we haven't found it amd simply attach our condition
                 */
                $pieces['where'] = sprintf( "%s AND tt.count > %d", $pieces['where'], $threshold );
            }
            
            return $pieces;
        }
    
    }
    // class
}
