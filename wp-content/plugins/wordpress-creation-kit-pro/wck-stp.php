<?php
/* Swift Templates */
/* hook to create custom post types */
add_action( 'init', 'wck_stp_create_swift_template_cpt', 20 );
/**
 * Function that creates the "wck-swift-template" post type
 */
function wck_stp_create_swift_template_cpt(){
	if( is_admin() && current_user_can( 'edit_theme_options' ) ){		
		$labels = array(
			'name' => _x( 'WCK Swift Templates', 'post type general name'),
			'singular_name' => _x( 'Swift Template', 'post type singular name'),
			'add_new' => _x( 'Add New', 'Swift Template' ),
			'add_new_item' => __( "Add New Swift Template", "wck" ),
			'edit_item' => __( "Edit Swift Template", "wck" ) ,
			'new_item' => __( "New Swift Template", "wck" ),
			'all_items' => __( "Swift Templates", "wck" ),
			'view_item' => __( "View Swift Template", "wck" ),
			'search_items' => __( "Search Swift Templates", "wck" ),
			'not_found' =>  __( "No Swift Templates found", "wck" ),
			'not_found_in_trash' => __( "No Swift Templates found in Trash", "wck" ), 
			'parent_item_colon' => '',
			'menu_name' => __( 'Swift Templates', 'wck' )
		);
		
		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => false,
			'show_ui' => true, 	
			'show_in_menu' => 'wck-page',	
			'has_archive' => false,
			'hierarchical' => false,									
			'capability_type' => 'post',
			'supports' => array( 'title' ),
		);			
				
		register_post_type( 'wck-swift-template', $args );
	}
	
	// create custom metabox (non-wck) for our template editors.
	require_once ('wordpress-creation-kit-api/wck-stp/stp-init-metabox.php');

	// initialize mustache and process tags
	require_once ('wordpress-creation-kit-api/wck-stp/stp-init-mustache.php');
	
	// initialize swift shortcode
	require_once ('wordpress-creation-kit-api/wck-stp/stp-init-shortcodes.php');
}

/* Remove view action from post list view */
add_filter('post_row_actions','wck_stp_remove_view_action');
function wck_stp_remove_view_action($actions){
	global $post;
	if ($post->post_type =="wck-swift-template"){	
	   unset( $actions['view'] );	  
	}
   return $actions;
}

/* Register and enqueue scripts in backend */
add_action('admin_enqueue_scripts', 'wck_stp_print_scripts' );
function wck_stp_print_scripts($hook){
	
	global $pagenow;	
	$sent_post_type = (isset($_GET['post_type'])) ? sanitize_text_field( $_GET['post_type'] ) : $sent_post_type = false;
	$post_type = $sent_post_type ? $sent_post_type : get_post_type( $sent_post_type );
	
	if( ( $pagenow=='post-new.php' || $pagenow=='post.php' ) || 'wck-swift-template' == $post_type ){
		// initiate codemirror
 		wp_register_style('wck-codemirror-css', plugins_url('wordpress-creation-kit-api/wck-stp/codemirror/lib/codemirror.css', __FILE__) );
		wp_enqueue_style('wck-codemirror-css');		

 		wp_register_style('wck-codemirror-fullscreen-css', plugins_url('wordpress-creation-kit-api/wck-stp/codemirror/addon/display/fullscreen.css', __FILE__) );
		wp_enqueue_style('wck-codemirror-fullscreen-css');		
		
		wp_register_script('wck-codemirror-js', plugins_url('wordpress-creation-kit-api/wck-stp/codemirror/codemirror-compressed.js', __FILE__), array( ), '1.0', true );
		wp_enqueue_script('wck-codemirror-js');

		wp_register_script('wck-stp-codemirror-init-js', plugins_url('wordpress-creation-kit-api/wck-stp/js/wck-stp-codemirror.js', __FILE__), array( 'jquery' ), '1.0', true );
		wp_enqueue_script('wck-stp-codemirror-init-js');
		
		//initiate default css and js for Swift Templates
		wp_register_style('wck-stp-backend-css', plugins_url('wordpress-creation-kit-api/wck-stp/css/wck-stp.css', __FILE__) );
		wp_enqueue_style('wck-stp-backend-css');
		
		wp_register_script('wck-stp-backend-js', plugins_url('wordpress-creation-kit-api/wck-stp/js/wck-stp.js', __FILE__), array( 'jquery', 'jquery-ui-dialog' ), '2.0', true );
		wp_enqueue_script('wck-stp-backend-js');
	}
}

/**
 * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
 */
add_action( 'wp_enqueue_scripts', 'wck_stp_front' );

/**
 * Enqueue plugin scripts style files
 *
 */
function wck_stp_front() {

    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'wck-stp-front-style', plugins_url('wordpress-creation-kit-api/wck-stp/css/wck-stp-front.css', __FILE__) );
    wp_enqueue_style( 'wck-stp-front-style' );

    $wck_tools = get_option( 'wck_tools' );

    if( empty( $wck_tools ) || ( !empty( $wck_tools[0]['swift-templates'] ) && $wck_tools[0]['swift-templates'] == 'enabled' ) ){

    	// map field
	    $options = get_option( 'wck_extra_options' );

	    if( !empty( $options[0]['google-maps-api'] ) ) {
	        wp_enqueue_script( 'wck-google-maps-api-script', 'https://maps.googleapis.com/maps/api/js?key=' . $options[0]['google-maps-api'] . '&libraries=places', array('jquery') );
	        wp_enqueue_script( 'wck-google-maps-script', plugin_dir_url( __FILE__ ) . 'wordpress-creation-kit-api/assets/map/map.js', array('jquery') );
	        wp_enqueue_style( 'wck-google-maps-style', plugin_dir_url( __FILE__ ) . 'wordpress-creation-kit-api/assets/map/map.css' );
	    }

    }

}



/* Change Shortcode Page field dispayed val */
add_filter( "wck_displayed_value_wck_stp_args_element_3", 'wck_stp_change_shortcode_page_displayed_val' );
function wck_stp_change_shortcode_page_displayed_val( $value ){
	return get_the_title( intval( $value ) );
}

/* Add side metaboxes */
add_action( 'add_meta_boxes', 'wck_stp_add_side_boxes' );
function wck_stp_add_side_boxes(){
	add_meta_box( 'wck-stp-side', __( 'Swift Template Shortcode', 'wck' ), 'wck_stp_side_shortcode', 'wck-swift-template', 'side', 'low' );	
}

/**
 * Callback function for the shortcode side meta box
 */
function wck_stp_side_shortcode(){
	global $post;
	
	if( empty( $post->post_title ) )
		$post->post_title = '(no title)';
		
	echo '<p>'. __( 'Use this shortcode on the page you want the form to be displayed:', 'wck' ) .'<br/><strong>[swift-template name="'. Wordpress_Creation_Kit::wck_generate_slug( $post->post_title ) .'"]</strong></p>';
	echo '<p>'. __( 'Note: changing the Form Title also changes the shortcode!', 'wck' ) . '</p>';
}

/* Add Custom columns to listing */
add_filter("manage_wck-swift-template_posts_columns", "wck_stp_edit_columns" );
function wck_stp_edit_columns($columns){
	$columns['stp-shortcode'] = __( "Shortcode", "wck" );	
	return $columns;
}

/* Let's set up what to display in the columns */
add_action("manage_wck-swift-template_posts_custom_column",  "wck_stp_custom_columns", 10, 2);
function wck_stp_custom_columns( $column_name, $post_id ){
	if( $column_name == 'stp-shortcode' ){
		$post = get_post( $post_id );
		
		if( empty( $post->post_title ) )
			$post->post_title = '(no title)';
		
		echo '<strong>[swift-template name="'. Wordpress_Creation_Kit::wck_generate_slug( $post->post_title ) .'"]</strong>';
	}
}

