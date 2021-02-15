<?php

/**
* @package     Tag Groups
* @author      Christoph Amthor
* @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPL-3.0+
*/

if ( !class_exists( 'TagGroups_Shortcode_Info' ) ) {
    class TagGroups_Shortcode_Info extends TagGroups_Shortcode
    {
        /**
         *
         * Render information about tag groups
         *
         * For <p> wrapping issue check: https://gist.github.com/bitfade/4555047
         *
         * @param array $atts
         * @return string
         */
        static function tag_groups_info( $atts = array() )
        {
            global  $tag_group_groups ;
            $shortcode_id = 'tag_groups_info';
            if ( is_array( $atts ) ) {
                asort( $atts );
            }
            $cache_key = md5( 'tag_groups_info' . serialize( $atts ) );
            // check for a cached version (premium plugin)
            $html = apply_filters( 'tag_groups_hook_cache_get', false, $cache_key );
            if ( $html ) {
                return $html;
            }
            extract( shortcode_atts( array(
                'info'         => 'number_of_tags',
                'group_id'     => 'all',
                'html_id'      => '',
                'html_class'   => '',
                'taxonomy'     => null,
                'target'       => '_self',
                'link_pattern' => '{slug}',
            ), $atts ) );
            
            if ( !empty($div_id) ) {
                $id_string = ' id="' . $html_id . '"';
            } else {
                $id_string = '';
            }
            
            
            if ( !empty($html_class) ) {
                $class_string = ' class="' . $html_class . '"';
            } else {
                $class_string = '';
            }
            
            $taxonomy_array = array();
            if ( !empty($taxonomy) ) {
                $taxonomy_array = array_map( 'trim', explode( ',', $taxonomy ) );
            }
            $tag_group_taxonomies = self::get_taxonomies( $taxonomy_array, $shortcode_id );
            
            if ( 'all' == $group_id ) {
                $term_groups = $tag_group_groups->get_group_ids_by_position();
                $output = self::render_table(
                    $id_string,
                    $class_string,
                    $term_groups,
                    $tag_group_taxonomies,
                    $info,
                    $target,
                    $link_pattern
                );
            } elseif ( strpos( $group_id, ',' ) !== false ) {
                $term_groups = array_map( 'intval', explode( ',', $group_id ) );
                $output = self::render_table(
                    $id_string,
                    $class_string,
                    $term_groups,
                    $tag_group_taxonomies,
                    $info,
                    $target,
                    $link_pattern
                );
            } else {
                $output = self::render_one(
                    $id_string,
                    $class_string,
                    (int) $group_id,
                    $tag_group_taxonomies,
                    $info,
                    $target,
                    $link_pattern
                );
            }
            
            // create a cached version (premium plugin)
            do_action( 'tag_groups_hook_cache_set', $cache_key, $output );
            return $output;
        }
        
        static function render_table(
            $id_string,
            $class_string,
            $term_groups,
            $tag_group_taxonomies,
            $info,
            $target,
            $link_pattern
        )
        {
            global  $tag_groups_premium_fs_sdk ;
            $output = '<table' . $id_string . $class_string . '>';
            foreach ( $term_groups as $term_group ) {
                $tg_group = new TagGroups_Group( $term_group );
                
                if ( !$tg_group->exists() ) {
                    TagGroups_Error::verbose_log( '[Tag Groups] Unknown group ID in "tag_groups_info": %s', $term_group );
                    continue;
                }
                
                switch ( $info ) {
                    case 'label':
                        $output .= '<tr>
            <td class="tag-groups-td-label" data-group-id="' . $term_group . '">';
                        $output .= $tg_group->get_label();
                        $output .= '</td>
            </tr>';
                        break;
                    case 'link':
                        break;
                    case 'number_of_tags':
                    default:
                        $output .= '<tr>
            <td class="tag-groups-td-label" data-group-id="' . $term_group . '">';
                        $output .= $tg_group->get_label();
                        $output .= '</td>
            <td class="tag-groups-td-number">';
                        $output .= (int) $tg_group->get_number_of_terms( $tag_group_taxonomies );
                        $output .= '</td>
            </tr>';
                        break;
                }
            }
            $output .= '</table>';
            return $output;
        }
        
        static function render_one(
            $id_string,
            $class_string,
            $group_id,
            $tag_group_taxonomies,
            $info,
            $target,
            $link_pattern
        )
        {
            global  $tag_groups_premium_fs_sdk ;
            $output = '';
            $tg_group = new TagGroups_Group( $group_id );
            
            if ( $tg_group->exists() ) {
                TagGroups_Error::verbose_log( '[Tag Groups] Unknown group ID in "tag_groups_info": %s', $group_id );
                return '';
            }
            
            switch ( $info ) {
                case 'label':
                    $output .= '<span' . $id_string . $class_string . '>';
                    $output .= $tg_group->get_label();
                    $output .= '</span>';
                    break;
                case 'link':
                    break;
                case 'number_of_tags':
                default:
                    $output .= '<span' . $id_string . $class_string . '>';
                    $output .= (int) $tg_group->get_number_of_terms( $tag_group_taxonomies );
                    $output .= '</span>';
                    break;
            }
            return $output;
        }
    
    }
    // class
}
