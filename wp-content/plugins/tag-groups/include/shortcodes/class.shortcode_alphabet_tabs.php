<?php
/**
* @package     Tag Groups
* @author      Christoph Amthor
* @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPL-3.0+
*/

if ( ! class_exists('TagGroups_Shortcode_Alphabet_Tabs') ) {

  class TagGroups_Shortcode_Alphabet_Tabs extends TagGroups_Shortcode {


    /**
     * attributes that we can use in the Gutenberg editor for server-side render
     *
     * @var array
     */
    public static $attributes = array(
      'source' => array(
        'type' => 'string',
        'default' => '',
      ),
      'active' => array(
        'type' => 'integer',
        'default' => -1,
      ),
      'adjust_separator_size' => array(
        'type' => 'integer',
        'default' => 1,
      ),
      'amount' => array(
        'type' => 'integer',
        'default' => 0,
      ),
      'append' => array(
        'type' => 'string',
        'default' => '',
      ),
      'assigned_class' => array(
        'type' => 'string',
        'default' => '',
      ),
      'collapsible' => array(
        'type' => 'integer',
        'default' => 0,
      ),
      'custom_title' => array(
        'type' => 'string',
        'default' => '{description} ({count})',
      ),
      'delay' => array(
        'type' => 'integer',
        'default' => 1,
      ),
      'div_class' => array(
        'type' => 'string',
        'default' => 'tag-groups-cloud',
      ),
      'div_id' => array(
        'type' => 'string',
        'default' => '',
      ),
      'exclude_letters' => array(
        'type' => 'string',
        'default' => '',
      ),
      'exclude_terms' => array(
        'type' => 'string',
        'default' => '',
      ),
      'hide_empty' => array(
        'type' => 'integer',
        'default' => 1,
      ),
      'include' => array(
        'type' => 'string',
        'default' => '',
      ),
      'include_letters' => array(
        'type' => 'string',
        'default' => '',
      ),
      'include_terms' => array(
        'type' => 'string',
        'default' => '',
      ),
      'largest' => array(
        'type' => 'integer',
        'default' => 22,
      ),
      'link_append' => array(
        'type' => 'string',
        'default' => '',
      ),
      'link_target' => array(
        'type' => 'string',
        'default' => '_self',
      ),
      'locale' => array(
        'type' => 'string',
        'default' => '',
      ),
      'mouseover' => array(
        'type' => 'integer',
        'default' => 0,
      ),
      'order' => array(
        'type' => 'string',
        'default' => 'ASC',
      ),
      'orderby' => array(
        'type' => 'string',
        'default' => 'name',
      ),
      'prepend' => array(
        'type' => 'string',
        'default' => '',
      ),
      'separator' => array(
        'type' => 'string',
        'default' => '',
      ),
      'separator_size' => array(
        'type' => 'integer',
        'default' => 22,
      ),
      'show_tag_count' => array(
        'type' => 'integer',
        'default' => 1,
      ),
      'smallest' => array(
        'type' => 'integer',
        'default' => 12,
      ),
      'tags_post_id' => array(
        'type' => 'integer',
        'default' => -1,
      ),
      'taxonomy' => array(
        'type' => 'string',
        'default' => '',
      ),
      'threshold' => array(
        'type' => 'integer',
        'default' => 0,
      ),
      'ul_class' => array(
        'type' => 'string',
        'default' => '',
      ),
    );

    /**
    *
    * Render the tabbed tag cloud, usually by a shortcode, or returning a multidimensional array
    *
    * @param array $atts
    * @param bool $return_array
    * @return string
    */
    static function tag_groups_alphabet_tabs( $atts = array() ) {

      global $tag_group_groups;

      $shortcode_id = 'tag_groups_alphabet_tabs';

      extract( shortcode_atts( array(
        'active' => -1,
        'adjust_separator_size' => true,
        'amount' => 0,
        'append' => '',
        'assigned_class' => null,
        'collapsible' => null,
        'custom_title' => null,
        'delay' => true,
        'div_class' => 'tag-groups-cloud',  // tag-groups-cloud preserved to create tab functionality
        'div_id' => '',
        'exclude_letters' => '',
        'exclude_terms' => '',
        // 'hide_empty_tabs' => false, // doesn't make sense here
        'hide_empty' => true,
        'ignore_accents' => false,
        'include' => '',
        'include_letters' => '',
        'include_terms' => '',
        'largest' => 22,
        'link_append' => '',
        'link_target' => '',
        'locale'  => '',
        'min_max_per_letter' => 1, // option to assign post counts to font sizes for each letter separately; here different than Alphabetical Tag List because we don't see all tab contents simultaneously
        'mouseover' => null,
        'order' => 'ASC',
        'orderby' => 'name',
        'prepend' => '',
        'remove_filters' => 1,
        'separator_size' => 12,
        'separator' => '',
        'show_tag_count' => true,
        'smallest' => 12,
        'source' => 'shortcode',
        'tags_post_id' => -1,
        'taxonomy' => implode( ',', TagGroups_Taxonomy::get_enabled_taxonomies() ),
        'threshold' => 0, // minimum number of posts, total (independent of groups)
        'ul_class' => ''
      ), $atts ) );

      /**
       * Don't set it as default in extract( shortcode_atts() ) because the block sends an empty string
       */
      if ( empty( $div_id ) ) {

        $div_id = 'tag-groups-cloud-alphabet-tabs-' . uniqid();

      }

      /**
      * Keep always jQuery UI class to produce correct output
      */
      if ( ! in_array( 'tag-groups-cloud', array_map( 'trim', explode( ' ', $div_class ) ) ) ) {

        $div_class .= ' tag-groups-cloud';

      }

      if ( $delay ) {

        $div_class .= ' tag-groups-cloud-hidden';
        
      }

      $div_id_output = $div_id ? ' id="' . TagGroups_Base::sanitize_html_classes( $div_id ) . '"' : '';

      $div_class_output = $div_class ? ' class="' . TagGroups_Base::sanitize_html_classes( $div_class ) . '"' : '';

      if ( is_array( $atts ) ) {

        asort( $atts );

      }

      /*
      *  applying parameter tags_post_id
      */
      if ( $tags_post_id == 0 ) {

        $tags_post_id = get_the_ID();

      }

      $cache_key = md5( 'alphabet-tabs' . serialize( $atts ) . serialize( $tags_post_id ) );

      // check for a cached version (premium plugin)
      $html = apply_filters( 'tag_groups_hook_cache_get', false, $cache_key );

      if ( ! $html ) {

        $html_tabs = array();

        $html_tags = array();

        $post_id_terms = array();

        $assigned_terms = array();

        if ( 'shortcode' == $source ) {

          $prepend = html_entity_decode( $prepend );

          $append = html_entity_decode( $append );

          $separator = html_entity_decode( $separator );

        }

        
        if ( $threshold ) {

          $final_orderby = $orderby;

          $final_order = $order;

          $orderby = 'count';

          $order = 'DESC';

          add_filter( 'terms_clauses', array( 'TagGroups_Shortcode', 'terms_clauses_threshold'), 10, 3 );

        } else {

          $final_orderby = $orderby;

          $final_order = $order;

        }


        if ( $smallest < 1 ) {

          $smallest = 1;

        }

        if ( $largest < $smallest ) {

          $largest = $smallest;

        }

        if ( $amount < 0 ) {

          $amount = 0;

        }

        if ( ! empty( $link_append ) && mb_strpos( $link_append, '?' ) === 0 ) {

          $link_append = mb_substr( $link_append, 1 );

        }

        
        $taxonomy_array = array();

        if ( ! empty( $taxonomy ) ) {

            $taxonomy_array = array_map( 'trim', explode( ',', $taxonomy ) );

        }

        $taxonomies = self::get_taxonomies( $taxonomy_array, $shortcode_id );


        $tag_group_ids = $tag_group_groups->get_group_ids();

        if ( $include !== '' ) {

          $include_array = explode( ',', str_replace( ' ', '', $include ) );

        } else {

          $include_array = $tag_group_ids;

        }

        $tag_groups_hooks = new TagGroups_Hooks();

        /**
        * Reduce the risk of interference from other plugins
        */
        if ( $remove_filters ) {

          $tag_groups_hooks->remove_all_filters( array('get_terms_orderby', 'get_terms', 'list_terms_exclusions' ) ); // keep terms_clauses for WPML

        }

        /**
         * term_order requires special treatment
         */
        self::maybe_enable_terms_order( $orderby );
        
        $term_query = new WP_Term_Query( array( 'taxonomy' => $taxonomies, 'hide_empty' => $hide_empty, 'orderby' => $orderby, 'order' => $order, 'include' => $include_terms, 'exclude' => $exclude_terms, 'threshold' => $threshold ) );

        $posttags = empty( $term_query->terms ) ? array() : $term_query->terms;

        $tag_groups_hooks->restore_hooks();

        /**
        * In case of errors: return empty array
        */
        if ( ! is_array( $posttags ) ) {

          $posttags = array();

          TagGroups_Error::log( '[Tag Groups] Error retrieving tags with get_terms.' );

        }

        /*
        *  include
        */
        if ( $posttags ) {

          foreach ( $posttags as $key => $term ) {

            $term_o = new TagGroups_Term( $term->term_id );

            if ( ! $term_o->is_in_group( $include_array ) ) {

              unset( $posttags[ $key ] );

            }

          }

        }

        $ul_class_output = $ul_class ? ' class="' . TagGroups_Base::sanitize_html_classes( $ul_class ) . '"' : '';


        if ( $separator_size < 1 ) {

          $separator_size = 12;

        } else {

          $separator_size = (int) $separator_size;

        }

        if ( $tags_post_id > 0 ) {

          /*
          *  we have a particular post ID
          *  get all tags of this post
          */
          foreach ( $taxonomies as $taxonomy_item ) {

            $terms = get_the_terms( (int) $tags_post_id, $taxonomy_item );

            /*
            *  merging the results of selected taxonomies
            */
            if ( ! empty( $terms ) && is_array( $terms ) ) {

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

            if ( ! empty( $assigned_class ) ) {

              /*
              *  Keep all terms but mark for different styling
              */
              if ( $found ) {

                $assigned_terms[ $tag->term_id ] = true;

              }

            } else {

              /*
              *  Remove unused terms.
              */
              if ( ! $found ) {

                unset( $posttags[ $key ] );

              }

            }

          }

        }

        // apply sorting that cannot be done on database level
        if ( 'natural' == $orderby || 'random' == $orderby || $threshold ) {

          $posttags = self::sort( $posttags, $final_orderby, $final_order );
          
        }

        /**
        * Extract the alphabet
        */
        $alphabet = self::extract_alphabet( $posttags, $ignore_accents );

        /**
        * Use provided list to include
        */
        $include_letters = str_replace( ' ', '', $include_letters );

        if ( $include_letters != '' ) { // don't use empty()

          $include_letters_array = array();

          $include_letters = mb_strtolower( $include_letters );

          for ( $i = 0; $i < mb_strlen( $include_letters ); $i++ ) {

            $include_letters_array[] = mb_substr( $include_letters, $i, 1 );

          }

          $alphabet = array_intersect( $alphabet, $include_letters_array );

        }

        /**
        * Use provided list to exclude
        */
        $exclude_letters = str_replace( ' ', '', $exclude_letters );

        if ( $exclude_letters != '' ) { // don't use empty()

          $exclude_letters_array = array();

          $exclude_letters = mb_strtolower( $exclude_letters );

          for ( $i = 0; $i < mb_strlen( $exclude_letters ); $i++ ) {

            $exclude_letters_array[] = mb_substr( $exclude_letters, $i, 1 );

          }

          $alphabet = array_diff( $alphabet, $exclude_letters_array );

        }

        $alphabet = self::sort_alphabet( $alphabet, $locale );

        $html = '';

        /*
        *  render the tabs
        */

        $i = 0;

        foreach ( $alphabet as $letter ) {

          /**
          * Convert to upper case only now; otherwise ß would become SS and affect all cases with S
          */
          $html_tabs[ $i ] = '<li><a href="#tabs-1' . $i . '" >' . htmlentities( mb_strtoupper( $letter ), ENT_QUOTES, "UTF-8" ) . '</a></li>';

          $i++;

        }

        /*
        *  render the tab content
        */
        $min_max = self::determine_min_max_alphabet( $posttags, $amount, $alphabet, $min_max_per_letter, $ignore_accents );

        $i = 0;

        foreach ( $alphabet as $letter ) {

          $count_amount = 0;

          $html_tags[ $i ] = '';

          foreach ( $posttags as $key => $tag ) {

            $other_tag_classes = '';

            $description = '';

            if ( ! empty( $amount ) && $count_amount >= $amount ) {

              break;

            }

            if ( self::get_first_letter( $tag->name, $ignore_accents ) == $letter ) {

              $tag_count = $tag->count;

              if ( ! $hide_empty || $tag_count > 0 ) {

                $tag_link = get_term_link( $tag );

                if ( ! empty( $link_append ) ) {

                  if ( mb_strpos( $tag_link, '?' ) === false ) {

                    $tag_link = esc_url( $tag_link . '?' . $link_append );

                  } else {

                    $tag_link = esc_url( $tag_link . '&' . $link_append );

                  }

                }

                $font_size = self::font_size( $tag_count, $min_max[ $letter ]['min'], $min_max[ $letter ]['max'], $smallest, $largest );

                $font_size_separator = $adjust_separator_size ? $font_size : $separator_size;

                if ( $count_amount > 0 && ! empty( $separator ) ) {

                  $html_tags[ $i ] .= '<span style="font-size:' . $font_size_separator . 'px">' . $separator . '</span> ';

                }

                if ( ! empty( $assigned_class ) ) {

                  if ( ! empty( $assigned_terms[ $tag->term_id ] ) ) {

                    $other_tag_classes = ' ' . $assigned_class . '_1';

                  } else {

                    $other_tag_classes = ' ' . $assigned_class . '_0';

                  }

                }

                if ( ! is_null( $custom_title ) ) {

                  $description = ! empty( $tag->description ) ? esc_html( $tag->description ) : '';

                  $title = preg_replace("/(\{description\})/", $description, $custom_title);

                  $title = preg_replace("/(\{count\})/", $tag_count, $title);

                } else {
                  // description and number
                  $description = ! empty( $tag->description ) ? esc_html( $tag->description ) . ' ' : '';

                  $tag_count_brackets = $show_tag_count ? '(' . $tag_count . ')' : '';

                  $title = $description . $tag_count_brackets;

                }

                /**
                 * Filter hook to modify the HTML title attribute of tags
                 * 
                 * @param string $title
                 * @param string $shortcode_id The name of the shortcode
                 * @param string $tag->description The description of the tag, unescaped.
                 * @param integer $tag_count The number of posts using this tag.
                 */
                $title = apply_filters( 'tag_groups_tag_title', $title, $shortcode_id, $tag->description, $tag_count );
                    
                $title_html = ( $title == '' ) ? '' : ' title="' .  $title . '"';
                

                // replace placeholders in prepend and append
                if ( ! empty( $prepend ) ) {

                  $prepend_output = preg_replace("/(\{count\})/", $tag_count, $prepend );

                } else {

                  $prepend_output = '';

                }

                if ( ! empty( $append ) ) {

                  $append_output = preg_replace("/(\{count\})/", $tag_count, $append );

                } else {

                  $append_output = '';

                }

                // adding link target
                $link_target_html = ! empty( $link_target ) ? 'target="' . $link_target . '"' : '';

                // assembling a tag
                $html_tags[ $i ] .= '<span class="tag-groups-tag' . $other_tag_classes . '" style="font-size:' . $font_size . 'px"><a href="' . $tag_link . '" ' . $link_target_html . '' . $title_html . '  class="' . $tag->slug . '">';

                if ( '' != $prepend_output ) {

                  $prepend_html = '<span class="tag-groups-prepend" style="font-size:' . $font_size . 'px">' . htmlentities( $prepend_output, ENT_QUOTES, "UTF-8" ) . '</span>';

                } else {

                  $prepend_html = '';

                }

                $html_tags[ $i ] .= apply_filters( 'tag_groups_cloud_tag_prepend', $prepend_html, $tag->term_id, $font_size, $tag_count, $shortcode_id );

                $html_tags[ $i ] .= apply_filters( 'tag_groups_cloud_tag_outer', '<span class="tag-groups-label" style="font-size:' . $font_size . 'px">' . apply_filters( 'tag_groups_cloud_tag_inner', $tag->name, $tag->term_id, $shortcode_id ) . '</span>', $tag->term_id, $shortcode_id );

                if ( '' != $append_output ) {

                  $append_html = '<span class="tag-groups-append" style="font-size:' . $font_size . 'px">' . htmlentities( $append_output, ENT_QUOTES, "UTF-8" ) . '</span>';

                } else {

                  $append_html = '';

                }

                $html_tags[ $i ] .= apply_filters( 'tag_groups_cloud_tag_append', $append_html, $tag->term_id, $font_size, $tag_count, $shortcode_id );

                $html_tags[ $i ] .= '</a></span> ';

                $count_amount++;

              }

              unset( $posttags[ $key ] ); // We don't need to look into that one again, since it can only appear under on tab

            }

          }

          if ( ! $count_amount ) {

            unset( $html_tabs[ $i ] );

            unset( $html_tags[ $i ] );

          } elseif ( isset( $html_tags[ $i ] ) ) {

            $html_tags[ $i ] = '<div id="tabs-1' . $i . '">' .  $html_tags[ $i ] . '</div>';

          }

          $i++;

        }

        /*
        * assemble tabs
        */
        $html .= '<ul' . $ul_class_output . '>' . implode( "\n", $html_tabs ) . '</ul>';

        /*
        * assemble tags
        */
        $html .= implode( "\n", $html_tags );

        // create a cached version (premium plugin)
        do_action( 'tag_groups_hook_cache_set', $cache_key, $html );

      }

      $html = '<div' . $div_id_output . $div_class_output . '>' . $html . '</div>'; // entire wrapper

      $html .= self::custom_js_tabs( $div_id, $mouseover, $collapsible, $active, $delay );

      apply_filters( 'tag_groups_cloud_html', $html, $shortcode_id, $atts );

      return $html;

    }



  } // class

}
