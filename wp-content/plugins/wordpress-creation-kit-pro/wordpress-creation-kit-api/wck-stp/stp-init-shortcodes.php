<?php

// initialize core rewrite class and functions
require_once ('stp-pagination-class.php');


function wck_stp_template_in_frontend( $atts ){	
	
	/* extract shortcode args */
	extract( shortcode_atts( array(
		'name' => '',
	), $atts ) );
	
	$single = get_query_var('stpsingle');
	
	if( !$single ) {
		$listing = wck_stp_all_listing($name);
	} else {
		$listing = wck_stp_single_listing($name, $single);
		add_filter( 'wp_title', create_function('$title,$sep', 'return $title . $sep . $single;' ), 99990, 2);
	}

	return $listing;
}

// register the shortcode 
add_shortcode( 'swift-template', 'wck_stp_template_in_frontend' );

// the function that generates the Archive Listing Page 
function wck_stp_all_listing($name) {
	
	// get the Swift Template CPT that dictates what we output in this function
	$args=array(	  
	  'post_type' => 'wck-swift-template',
	  'post_status' => 'publish',
	  'posts_per_page' => -1,
	  'numberposts' => -1
	);
	$swift_templates = get_posts($args);	
	
	// variable to store if we have a valid shortcode 
	$valid_shortcode = false;
	foreach( $swift_templates as $swift_template ){
		if( $name == Wordpress_Creation_Kit::wck_generate_slug( $swift_template->post_title ) ){
			// we have a valid shortcode 
			$valid_shortcode = true;
			$stp_id = $swift_template->ID;
			
			$template = get_post_meta( $stp_id, 'wck_stp_template_all', true);
			
			// find out the CPT we're quering from Swift Arguments
			$stp_cpt = get_post_meta( $stp_id, 'wck_stp_args_cpt', true );
			$stp_cpt_name = $stp_cpt;
			
			// get all posts that we want listed in our shortcode
			$args=array(
			  'post_type' => $stp_cpt_name,
			  'post_status' => 'publish',
			  'posts_per_page' => -1
			);
			
			$args = apply_filters( 'wck_stp_archive_query_args', $args, $stp_id );

			// paginate our results after filters
			$currentPage = get_query_var('pag');
			if ( $currentPage != '' ){
				$args['paged'] = $currentPage;
			}

			$posts = get_posts($args);

			// generate our mustache array with available data
			$mustache_posts = array( 'posts' => array() );
			foreach ($posts as $post){
				$mustache_posts['posts'][] = wck_stp_generate_mustache_single_array($post->post_name, $post->post_type);
			}
			
			// setup the pagination
			$pagination = wck_stp_pagination($args);
			$mustache_posts['pagination'] = '<div class="stp_paginate">' . $pagination . '</div>';
			
			// parse our template
			$m = new Mustache_Engine;
			try {
				$content = '<div class="stp-bubble-wrap stp-archive stp-archive-'. $name .'">' . $m->render( $template, $mustache_posts ) . '</div>';
			} catch (Exception $e) {
				$content = $e->getMessage();
			}			

			return apply_filters( 'wck_stp_template_content', do_shortcode( $content ), $content );
		}
	}
	// do nothing if shortcode doesn't exist
	if ( !$valid_shortcode ){
		return '<p class="wck-stp-error ">'.sprintf( __( 'The shortcode with the name <em>%s</em> dose not exist.', 'wck' ), $name ).'</p>';
	}
}

// the function that generates the Single Listing Page 
function wck_stp_single_listing($name, $single) {

	// get information from Swift Template CPT
	$args=array(	  
	  'post_type' => 'wck-swift-template',
	  'post_status' => 'publish',
	  'posts_per_page' => -1,
	  'numberposts' => -1
	);
	$stp_posts = get_posts($args);
	
	// variable to store if we have a valid shortcode 
	$valid_shortcode = false;
	foreach( $stp_posts as $stp_post ){
		if( $name == Wordpress_Creation_Kit::wck_generate_slug( $stp_post->post_title ) ){
			// we have a valid shortcode 
			$valid_shortcode = true;
			$stp_id = $stp_post->ID;

			// find out the CPT we're quering from Swift Arguments
			$stp_cpt_name = get_post_meta( $stp_id, 'wck_stp_args_cpt', true );				
			
			// get information from the current post queried in the front-end by our shortcode

			$mustache_vars = wck_stp_generate_mustache_single_array($single, $stp_cpt_name);
			$template = get_post_meta( $stp_id, 'wck_stp_template_single', true);
			$m = new Mustache_Engine;
			
			$content = '<div class="stp-bubble-wrap stp-single stp-single-'. $name .'">' . $m->render( $template, $mustache_vars ) . '</div>';

            return apply_filters( 'wck_stp_template_content_single', do_shortcode( $content ), $content );
		}
	}
	if (!$valid_shortcode) {
		return '<p class="wck-stp-error">'.sprintf( __( 'The single listing post with the name <em>%s</em> dose not exist.', 'wck' ), $name ).'</p>';
	}
}

function wck_stp_pagination($args){
	
	// calculate the total number of posts
	$total_args = $args;
	$total_args['posts_per_page'] = -1;
	$posts = get_posts($total_args);
	
	$total_posts = count ( $posts );

	$html_pagination = '';
	
	//start creating the pagination
	if ( $total_posts != 0 ){
		$pagination = new wck_stp_pagination;
		$first = __('&laquo;&laquo; First', 'wck');
		$prev = __('&laquo; Prev', 'wck');
		$next = __('Next &raquo; ', 'wck');
		$last = __('Last &raquo;&raquo;', 'wck');

		$currentPage = get_query_var('pag');

		if ($currentPage == 0){
			$currentPage = 1; 
		}
		$html_pagination = $pagination->generate($total_posts, $args['posts_per_page'], '', $first, $prev, $next, $last, $currentPage); 
		$html_pagination = $pagination->links();
	}
	
	return $html_pagination;
}


// Pass the arguments defined by the user to the archive query 
add_filter( 'wck_stp_archive_query_args', 'wck_stp_pass_archive_query_args', 10, 2);
function wck_stp_pass_archive_query_args( $args, $stp_id ){
	$stp_args = get_cfc_meta( 'wck_stp_query-args', $stp_id );
	
	foreach( $stp_args as $argument ){
		$args[$argument['query-arguments']] = $argument['value'];
	}
	
	return $args;
}

// Add the rewrite rule
function wck_stp_rwr_add_rules() {
	add_rewrite_rule( '(.+?)/pag/([^/]+)','index.php?pagename=$matches[1]&pag=$matches[2]', 'top' );
	add_rewrite_rule( '(.+?)/s/([^/]+)','index.php?pagename=$matches[1]&stpsingle=$matches[2]', 'top' );
}
add_action( 'admin_init', 'wck_stp_rwr_add_rules' );

// Add our own query vars
add_filter( 'query_vars', 'wck_stp_add_query_vars_filter' );
function wck_stp_add_query_vars_filter( $vars ){
  $vars[] = "pag";
  $vars[] = "stpsingle";
  return $vars;
}

add_action('template_redirect', 'wck_stp_redirect_pag_params');
function wck_stp_redirect_pag_params(){
	global $post, $wp_rewrite;

	$stp_pag = get_query_var('pag');

    if( is_object( $post ) ){
        if( isset( $_GET['pag'] ) || $stp_pag == '1' ){
            if ($wp_rewrite->permalink_structure != ''){

                $redirect_permalink = get_permalink( $post->ID );

                if ( $stp_pag == '1' ){
                    wp_redirect( $redirect_permalink );
                    exit();
                }

                if( isset($_GET['pag']) ){
                    $pag = $_GET['pag'];
                    wp_redirect( $redirect_permalink . '/pag/' . $pag );
                    exit();
                }
            }
        }
    }
}