<?php
/* 
Plugin Name: Add Tags And Category To Page And Post Types
Plugin URI: http://dineshkarki.com.np/add-tags-and-category-to-page
Description: This plugin adds tags and category to wordpress pages and post types.
Author: Dinesh Karki
Version: 1.1
Author URI: http://www.dineshkarki.com.np
*/

/*  Copyright 2012  Dinesh Karki  (email : dnesskarki@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function add_tags_and_category_for_post_types(){ 
	$tcp_add_tag 				= get_option('tcp_add_tag');
	$tcp_add_category 			= get_option('tcp_add_category');
	
	$tcp_add_tag_array 			= explode(',',$tcp_add_tag);
	$tcp_add_category_array 	= explode(',',$tcp_add_category);

	foreach ($tcp_add_tag_array as $postType):
		//add_meta_box('tagsdiv', __('Tags'), 'post_tags_meta_box', $postType, 'side', 'default');
		register_taxonomy_for_object_type('post_tag', $postType); 
	endforeach;
	
	foreach ($tcp_add_category_array as $postType):
		//add_meta_box(    'categorydiv', __('Categories'), 'post_categories_meta_box', $postType, 'side', 'default');
		register_taxonomy_for_object_type('category', $postType);
	endforeach;
}

function tcp_add_post_types_in_category_and_tag_template($query) {
  if (is_category()){
	$tcp_add_category 			= get_option('tcp_add_category');
	$tcp_add_category_array 	= explode(',',$tcp_add_category);
	$tcp_add_category_array[] 	= 'post';
	$post_type = get_query_var('post_type');
	if($post_type)
	    $post_type = $post_type;
	else
		$post_type = $tcp_add_category_array;
    	$query->set('post_type',$post_type);
		return $query;  
  }
  
  if (is_tag()){
	$tcp_add_tag 				= get_option('tcp_add_tag');
	$tcp_add_tag_array 			= explode(',',$tcp_add_tag);
	$tcp_add_tag_array[] 		= 'post';
	$post_type = get_query_var('post_type');
	if($post_type)
	    $post_type = $post_type;
	else
		$post_type = $tcp_add_tag_array;
    	$query->set('post_type',$post_type);
		return $query;  
  }
  
}
add_action('admin_init', 'add_tags_and_category_for_post_types');
add_filter('pre_get_posts', 'tcp_add_post_types_in_category_and_tag_template');
include('plugin_interface.php');
?>