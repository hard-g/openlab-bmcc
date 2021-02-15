/**
 * Last modified: 2021/01/12 12:11:32
 * Part of the WordPress plugin Tag Groups
 * Plugin URI: https://chattymango.com/tag-groups/
 * Author: Christoph Amthor
 * License: GNU GENERAL PUBLIC LICENSE, Version 3
 */

 var TagGroupsBase = {
     accordion: function(id, options, delay) {
       if (delay) {
         options['create'] = function (event) {
           jQuery(event.target).removeClass('tag-groups-cloud-hidden');
         };
       }
       jQuery('#' + id).accordion(options);
     },
     tabs: function(id, options, delay) {
       if (delay) {
         options['create'] = function (event) {
           jQuery(event.target).removeClass('tag-groups-cloud-hidden');
         };
       }
       jQuery('#' + id).tabs(options);
     },

 }

