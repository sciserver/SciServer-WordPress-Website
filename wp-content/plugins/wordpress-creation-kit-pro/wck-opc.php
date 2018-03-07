<?php
/* Creates Option Pages for WordPress. */

/* Add Scripts */
add_action('admin_enqueue_scripts', 'wck_opc_print_scripts' );
function wck_opc_print_scripts($hook){
	if( isset( $_GET['post_type'] ) || isset( $_GET['post'] ) ){
		if( isset( $_GET['post_type'] ) )
			$post_type = sanitize_text_field( $_GET['post_type'] );
		else if( isset( $_GET['post'] ) )
			$post_type = get_post_type( absint( $_GET['post'] ) );			
		
		if( 'wck-option-page' == $post_type || 'wck-option-field' == $post_type ){
			wp_register_style('wck-opc-css', plugins_url('/css/wck-opc.css', __FILE__));
			wp_enqueue_style('wck-opc-css');

			wp_register_script('wck-opc-js', plugins_url('/js/wck-opc.js', __FILE__), array( 'jquery' ), '1.0' );
			wp_enqueue_script('wck-opc-js');
		}	
	}
}

/* hook to create custom post types */
add_action( 'init', 'wck_opc_create_option_pages_cpt' );

function wck_opc_create_option_pages_cpt(){	
	if( is_admin() && current_user_can( 'edit_theme_options' ) ){		
		$labels = array(
			'name' => _x( 'WCK Option Pages', 'post type general name'),
			'singular_name' => _x( 'Option Page', 'post type singular name'),
			'add_new' => _x( 'Add New', 'Option Page' ),
			'add_new_item' => __( "Add New Option Page", "wck" ),
			'edit_item' => __( "Edit Option Page", "wck" ) ,
			'new_item' => __( "New Option Page", "wck" ),
			'all_items' => __( "Option Pages Creator", "wck" ),
			'view_item' => __( "View Option Page", "wck" ),
			'search_items' => __( "Search Option Pages", "wck" ),
			'not_found' =>  __( "No Option Pages found", "wck" ),
			'not_found_in_trash' => __( "No Option Pages found in Trash", "wck" ), 
			'parent_item_colon' => '',
			'menu_name' => __( "Option Pages", "wck" )
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
			'supports' => array( 'title' )	
		);			
				
		register_post_type( 'wck-option-page', $args );
	}
}

/* hook to create options field cpt */
add_action( 'init', 'wck_opc_create_option_fields_cpt' );
function wck_opc_create_option_fields_cpt(){	
	if( is_admin() && current_user_can( 'edit_theme_options' ) ){		
		$labels = array(
			'name' => _x( 'WCK Option Field', 'post type general name'),
			'singular_name' => _x( 'Option Field', 'post type singular name'),
			'add_new' => _x( 'Add New', 'Option Field' ),
			'add_new_item' => __( "Add New Option Field", "wck" ),
			'edit_item' => __( "Edit Option Field", "wck" ) ,
			'new_item' => __( "New Option Field", "wck" ),
			'all_items' => __( "Option Fields Creator", "wck" ),
			'view_item' => __( "View Option Field", "wck" ),
			'search_items' => __( "Search Option Fields", "wck" ),
			'not_found' =>  __( "No Option Fields found", "wck" ),
			'not_found_in_trash' => __( "No Option Fields found in Trash", "wck" ), 
			'parent_item_colon' => '',
			'menu_name' => __( "Option Fields", "wck" )
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
			'supports' => array( 'title' )	
		);			
				
		register_post_type( 'wck-option-field', $args );
	}
}

/* add admin body class to opc custom post types */
add_filter( 'admin_body_class', 'wck_opc_admin_body_class' );
function wck_opc_admin_body_class( $classes ){
	if( isset( $_GET['post_type'] ) || isset( $_GET['post'] ) ){
		if( isset( $_GET['post_type'] ) )
			$post_type = sanitize_text_field( $_GET['post_type'] );
		else if( isset( $_GET['post'] ) )
			$post_type = get_post_type( absint( $_GET['post'] ) );
		
		if( 'wck-option-page' == $post_type || 'wck-option-field' == $post_type ){			
			$classes .= ' wck_page_opc-page ';
		}
	}
	return $classes;
}

/* Remove view action from post list view */
add_filter('post_row_actions','wck_opc_remove_view_action');
function wck_opc_remove_view_action($actions){
	global $post;
	if ($post->post_type =="wck-option-page" || $post->post_type =="wck-option-field"){
		unset( $actions['view'] );
	}
	return $actions;
}


/* create the meta box */
add_action( 'init', 'wck_opc_create_box', 500 );
function wck_opc_create_box(){
	global $wpdb;
	
	/* set up the fields array */
	$opc_box_args_fields = array(
		array( 'type' => 'text', 'title' => __( 'Page name in menu', 'wck' ), 'slug' => 'page-name-in-menu', 'description' => __( 'The name of the option page in the menu.', 'wck' ), 'required' => true ),
		array( 'type' => 'select', 'title' => __( 'Display', 'wck' ), 'slug' => 'display', 'options' => array( 'As toplevel page', 'Under Appearance menu', 'Under Settings menu' ), 'default-option' => true, 'description' => __( 'Choose where in the admin menu should the page appear', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Page Title', 'wck' ), 'slug' => 'page-title', 'description' => __( 'The title that is displayed on the page. If this is not filled in then it\'s the same as Page name in menu', 'wck' ) ),
		array( 'type' => 'select', 'title' => __( 'Page Type', 'wck' ), 'slug' => 'page-type', 'options' => array( '%Toplevel Page%menu_page', '%Submenu Page%submenu_page' ), 'default-option' => true, 'description' => __( 'Choose what type the page should be. IMPORTANT: this overrides the Display option.', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Parent Slug (for Submenu Page)', 'wck' ), 'slug' => 'parent-slug-for-submenu-page', 'description' => __( 'The slug name for the parent menu (or the file name of a standard WordPress admin page) For examples see http://codex.wordpress.org/Function_Reference/add_submenu_page $parent_slug parameter', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Position (for Toplevel Page)', 'wck' ), 'slug' => 'position-for-toplevel-page', 'description' => __( 'The position in the menu order this menu should appear. Available for toplevel pages only.', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Capability', 'wck' ), 'slug' => 'capability', 'description' => __( 'The capability required for this menu to be displayed to the user. Defaults to "edit_theme_options"', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Icon Url (for Toplevel Page)', 'wck' ), 'slug' => 'icon-url-for-toplevel-page', 'description' => __( 'The icon for the menu button', 'wck' ) ),
	);
		
	/* set up the box arguments */
	$args = array(
		'metabox_id' => 'wck-opc-args',
		'metabox_title' => __( 'Option Page Arguments', 'wck' ),
		'post_type' => 'wck-option-page',
		'meta_name' => 'wck_opc_args',
		'meta_array' => $opc_box_args_fields,			
		'sortable' => false,
		'single' => true
	);

	/* create the box */
	new Wordpress_Creation_Kit( $args );
	
	/* Get all Option Pages */
	$args = array(
		'post_type' => 'wck-option-page',
		'numberposts' => -1
	);
	
	$all_option_pages = get_posts( $args );
	$all_option_pages_titles = array();
	if( !empty( $all_option_pages ) ){
		foreach( $all_option_pages as $option_page ){			
			$page_title = get_the_title( $option_page->ID );
			$all_option_pages_titles[] = '%'.$page_title.'%'.Wordpress_Creation_Kit::wck_generate_slug( $page_title );
		}
	}
	
	/* set up the fields arguments array */
	$opc_box_fields_fields = apply_filters( 'wck_opc_box_fields_fields', array(		
		array( 'type' => 'text', 'title' => __( 'Group Title', 'wck' ), 'slug' => 'group-title', 'description' => __( 'The name of the group field', 'wck' ), 'required' => true ),
		array( 'type' => 'select', 'title' => __( 'Option Page', 'wck' ), 'slug' => 'option-page', 'options' => $all_option_pages_titles, 'default-option' => true, 'description' => __( 'Select the option page here', 'wck' ), 'required' => true ),
		array( 'type' => 'text', 'title' => __( 'Option Name', 'wck' ), 'slug' => 'option-name', 'description' => __( 'The name of the option. Must be unique, only lowercase letters, no spaces and no special characters.', 'wck' ), 'required' => true ),
		array( 'type' => 'select', 'title' => __( 'Repeater', 'wck' ), 'slug' => 'repeater', 'options' => array( 'false', 'true' ), 'default' => 'false', 'description' => __( 'Whether the box supports just one entry or if it is a repeater field. By default it is a single field.', 'wck' ) ),
		array( 'type' => 'select', 'title' => __( 'Sortable', 'wck' ), 'slug' => 'sortable', 'options' => array( 'true', 'false' ), 'default' => 'false', 'description' => __( 'Whether the entries are sortable or not. This is valid for repeater fields.', 'wck' ) ),
	) );	
	
	/* set up the box arguments */
	$args = array(
		'metabox_id' => 'wck-opc-field-args',
		'metabox_title' => __( 'Select on which option page this field group will appear', 'wck' ),
		'post_type' => 'wck-option-field',
		'meta_name' => 'wck_opc_field_args',
		'meta_array' => $opc_box_fields_fields,
		'sortable' => false,
		'single' => true
	);

	/* create the box */
	new Wordpress_Creation_Kit( $args );
	
	/* set up field types */

	$field_types = array( 'heading', 'text', 'number', 'textarea', 'select', 'checkbox', 'radio', 'phone', 'upload', 'wysiwyg editor', 'datepicker', 'timepicker', 'colorpicker', 'country select', 'user select', 'cpt select', 'currency select', 'html', 'map' );

	$field_types = apply_filters( 'wck_field_types', $field_types );
	
	/* setup post types */
	$post_types = get_post_types( '', 'names' ); 
	
	/* set up the fields array */
	$opc_box_fields_fields = array(
		array( 'type' => 'text', 'title' => __( 'Field Title', 'wck' ), 'slug' => 'field-title', 'description' => __( 'Title of the field. A slug will automatically be generated.', 'wck' ), 'required' => true ),
		array( 'type' => 'select', 'title' => __( 'Field Type', 'wck' ), 'slug' => 'field-type', 'options' => $field_types, 'default-option' => true, 'description' => __( 'The field type', 'wck' ), 'required' => true ),
		array( 'type' => 'textarea', 'title' => __( 'Description', 'wck' ), 'slug' => 'description', 'description' => 'The description of the field.' ),
		array( 'type' => 'select', 'title' => __( 'Required', 'wck' ), 'slug' => 'required', 'options' => array( 'false', 'true' ), 'default' => 'false', 'description' => __( 'Whether the field is required or not', 'wck' ) ),
		array( 'type' => 'select', 'title' => __( 'CPT', 'wck' ), 'slug' => 'cpt', 'options' => $post_types, 'default' => 'post', 'description' => __( 'Select what custom post type should be used in the CPT Select.', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Default Value', 'wck' ), 'slug' => 'default-value', 'description' => __( 'Default value of the field. For Checkboxes if there are multiple values separate them with a ","', 'wck' ) ),
		array( 'type' => 'textarea', 'title' => __( 'Default Text', 'wck' ), 'slug' => 'default-text', 'description' => __( 'Default text of the textarea.', 'wck' ) ),
		array( 'type' => 'textarea', 'title' => __( 'HTML Content', 'wck' ), 'slug' => 'html-content', 'description' => __( 'Add you HTML (or text) content.', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Options', 'wck' ), 'slug' => 'options', 'description' => __( 'Options for field types "select", "checkbox" and "radio". For multiple options separate them with a ",".', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Labels', 'wck' ), 'slug' => 'labels', 'description' => __( 'Labels for field types "select", "checkbox" and "radio". For multiple options separate them with a ",".', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Phone Format', 'wck' ), 'slug' => 'phone-format', 'default' => '(###) ###-####', 'description' => __( "You can use: # for numbers, parentheses ( ), - sign, + sign, dot . and spaces.", 'wck' ) .'<br>'.  __( "Eg. (###) ###-####", 'wck' ) .'<br>'. __( "Empty field won't check for correct phone number.", 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Min Number Value', 'wck' ), 'slug' => 'min-number-value', 'description' => __( "Min allowed number value (0 to allow only positive numbers)", 'wck' ) .'<br>'. __( "Leave it empty for no min value", 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Max Number Value', 'wck' ), 'slug' => 'max-number-value', 'description' => __( "Max allowed number value (0 to allow only negative numbers)", 'wck' ) .'<br>'. __( "Leave it empty for no max value", 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Number Step Value', 'wck' ), 'slug' => 'number-step-value', 'description' => __( "Step value 1 to allow only integers, 0.1 to allow integers and numbers with 1 decimal", 'wck' ) .'<br>'. __( "To allow multiple decimals use for eg. 0.01 (for 2 deciamls) and so on", 'wck' ) .'<br>'. __( "You can also use step value to specify the legal number intervals (eg. step value 2 will allow only -4, -2, 0, 2 and so on)", 'wck' ) .'<br>'. __( "Leave it empty for no restriction", 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Number of rows', 'wck' ), 'slug' => 'number-of-rows', 'description' => __( 'Number of rows for the textarea', 'wck' ), 'default' => '5' ),
        array( 'type' => 'select', 'title' => __( 'Readonly', 'wck' ), 'slug' => 'readonly', 'options' => array( 'false', 'true' ), 'default' => 'false', 'description' => __( 'Whether the textarea is readonly or not', 'wck' ) ),
        array( 'type' => 'text', 'title' => __( 'Default Latitude', 'wck' ), 'slug' => 'map-default-latitude', 'description' => __( 'The latitude at which the map should be displayed when no pins are attached.', 'wck' ), 'default' => 0 ),
        array( 'type' => 'text', 'title' => __( 'Default Longitude', 'wck' ), 'slug' => 'map-default-longitude', 'description' => __( 'The longitude at which the map should be displayed when no pins are attached.', 'wck' ), 'default' => 0 ),
        array( 'type' => 'text', 'title' => __( 'Default Zoom', 'wck' ), 'slug' => 'map-default-zoom', 'description' => __( 'Add a number from 0 to 19. The higher the number the higher the zoom.', 'wck' ), 'default' => 15 ),
        array( 'type' => 'text', 'title' => __( 'Map Height', 'wck' ), 'slug' => 'map-height', 'description' => __( 'The height of the map.', 'wck' ), 'default' => 350 ),
		array( 'type' => 'select', 'title' => __( 'Date Format', 'wck' ), 'slug' => 'date-format', 'description' => __( 'The format of the datepicker date', 'wck' ), 'options' => array( '%Default - dd-mm-yy%dd-mm-yy', '%Datepicker default - mm/dd/yy%mm/dd/yy', '%ISO 8601 - yy-mm-dd%yy-mm-dd', '%Short - d M, y%d M, y', '%Medium - d MM, y%d MM, y', '%Full - DD, d MM, yy%DD, d MM, yy', '%With text - \'day\' d \'of\' MM \'in the year\' yy%\'day\' d \'of\' MM \'in the year\' yy' ), 'default' => 'dd-mm-yy' ),
	);	
	
	/* set up the box arguments */
	$args = array(
		'metabox_id' => 'wck-opc-fields',
		'metabox_title' => __( 'Option Page Fields', 'wck' ),
		'post_type' => 'wck-option-field',
		'meta_name' => 'wck_opc_fields',
		'meta_array' => $opc_box_fields_fields
	);

	/* create the box */
	new Wordpress_Creation_Kit( $args );
}

/* add refresh to options creator page */
add_action("wck_refresh_list_wck_opc_args", "wck_opc_args_after_refresh_list");
add_action("wck_refresh_entry_wck_opc_args", "wck_opc_args_after_refresh_list");
add_action("wck_refresh_list_wck_opc_field_args", "wck_opc_args_after_refresh_list");
add_action("wck_refresh_entry_wck_opc_field_args", "wck_opc_args_after_refresh_list");
function wck_opc_args_after_refresh_list($id){
	echo '<script type="text/javascript">window.location="'. get_admin_url() . 'post.php?post='.$id.'&action=edit' .'";</script>';
}

/* advanced options container for add form */
add_action( "wck_before_add_form_wck_opc_args_element_2", 'wck_opc_form_wrapper_start' );
function wck_opc_form_wrapper_start(){
	echo '<li><a href="javascript:void(0)" onclick="jQuery(\'#opc-args-advanced-options-container\').toggle(); if( jQuery(this).text() == \''. __( 'Show Advanced Options', 'wck' ) .'\' ) jQuery(this).text(\''. __( 'Hide Advanced Options', 'wck' ) .'\');  else if( jQuery(this).text() == \''. __( 'Hide Advanced Options', 'wck' ) .'\' ) jQuery(this).text(\''. __( 'Show Advanced Options', 'wck' ) .'\');">'. __( 'Show Advanced Options', 'wck' ) .'</a></li>';
	echo '<li id="opc-args-advanced-options-container" style="display:none;"><ul>';
}

add_action( "wck_after_add_form_wck_opc_args_element_7", 'wck_opc_form_wrapper_end' );
function wck_opc_form_wrapper_end(){
	echo '</ul></li>';	
}

/* advanced options container for update form */
add_filter( "wck_before_update_form_wck_opc_args_element_2", 'wck_opc_update_form_wrapper_start', 10, 2 );
function wck_opc_update_form_wrapper_start( $form, $i ){
	$form .=  '<li><a href="javascript:void(0)" onclick="jQuery(\'#opc-args-advanced-options-update-container-'.$i.'\').toggle(); if( jQuery(this).text() == \''. __( 'Show Advanced Options', 'wck' ) .'\' ) jQuery(this).text(\''. __( 'Hide Advanced Options', 'wck' ) .'\');  else if( jQuery(this).text() == \''. __( 'Hide Advanced Options', 'wck' ) .'\' ) jQuery(this).text(\''. __( 'Show Advanced Options', 'wck' ) .'\');">'. __( 'Show Advanced Options', 'wck' ) .'</a></li>';
	$form .= '<li id="opc-args-advanced-options-update-container-'.$i.'" style="display:none;"><ul>';
	return $form;
}

add_filter( "wck_after_update_form_wck_opc_args_element_7", 'wck_opc_update_form_wrapper_end', 10, 2 );
function wck_opc_update_form_wrapper_end( $form, $i ){
	$form .=  '</ul></li>';	
	return $form;
}

/* advanced options container for display */
add_filter( "wck_before_listed_wck_opc_args_element_2", 'wck_opc_display_adv_wrapper_start', 10, 2 );
function wck_opc_display_adv_wrapper_start( $form, $i ){
	$form .=  '<li><a href="javascript:void(0)" onclick="jQuery(\'#opc-args-advanced-options-display-container-'.$i.'\').toggle(); if( jQuery(this).text() == \''. __( 'Show Advanced Options', 'wck' ) .'\' ) jQuery(this).text(\''. __( 'Hide Advanced Options', 'wck' ) .'\');  else if( jQuery(this).text() == \''. __( 'Hide Advanced Options', 'wck' ) .'\' ) jQuery(this).text(\''. __( 'Show Advanced Options', 'wck' ) .'\');">'. __( 'Show Advanced Options', 'wck' ) .'</a></li>';
	$form .= '<li id="opc-args-advanced-options-display-container-'.$i.'" style="display:none;"><ul>';
	return $form;
}

add_filter( "wck_after_listed_wck_opc_args_element_7", 'wck_opc_display_adv_wrapper_end', 10, 2 );
function wck_opc_display_adv_wrapper_end( $form, $i ){
	$form .=  '</ul></li>';	
	return $form;
}


/* Add css classes on update form. Allows us to show/hide elements based on field type select value */
add_filter( 'wck_update_container_class_wck_opc_fields', 'wck_opc_update_container_class', 10, 4 );
function wck_opc_update_container_class($wck_update_container_css_class, $meta, $results, $element_id) {
	$wck_element_type = Wordpress_Creation_Kit::wck_generate_slug( $results[$element_id]["field-type"] );
	return "class='update_container_$meta update_container_$wck_element_type element_type_$wck_element_type'";
}

add_filter( 'wck_element_class_wck_opc_fields', 'wck_opc_element_class', 10, 4 );
function wck_opc_element_class($wck_element_class, $meta, $results, $element_id){
	$wck_element_type = Wordpress_Creation_Kit::wck_generate_slug( $results[$element_id]["field-type"] );
	$wck_element_class = "class='element_type_$wck_element_type'";
	return $wck_element_class;
}


/* Show the slug for page */
add_filter( "wck_after_listed_wck_opc_args_element_0", 'wck_opc_display_page_slug', 10, 3 );
function wck_opc_display_page_slug( $form, $i, $value ){	
		$form .= '<li class="slug-title"><em>'. __( 'Slug:', 'wck' ) .'</em><span>'. Wordpress_Creation_Kit::wck_generate_slug( $value ) .'</span></li>';
	return $form;
}

/* Show the slug for field title */
add_filter( "wck_after_listed_wck_opc_fields_element_0", 'wck_opc_display_field_title_slug', 10, 3 );
function wck_opc_display_field_title_slug( $form, $i, $value ){	
		$form .= '<li class="slug-title"><em>'. __( 'Slug:', 'wck' ) .'</em><span>'. Wordpress_Creation_Kit::wck_generate_slug( $value ) .'</span> '. __( '(Note:changing the slug when you already have a lot of existing entries may result in unexpected behavior.)', 'wck' ) .' </li>';
	return $form;
}

/* create pages */
add_action( 'init', 'wck_opc_create_pages' );
function wck_opc_create_pages(){
	$args = array(
		'post_type' => 'wck-option-page',
		'numberposts' => -1
	);
	
	$all_option_pages = get_posts( $args );
	
	if( !empty( $all_option_pages ) ){
		foreach( $all_option_pages as $option_page ){
			
			$wck_opc_args = get_post_meta( $option_page->ID, 'wck_opc_args', true );
			$page_title = get_the_title( $option_page->ID );
			/* treat case where the post has no title */
			if( empty( $page_title ) )
				$page_title = '(no title)';
			
			if( !empty( $wck_opc_args ) ){
				$wck_opc_args = $wck_opc_args[0];
				
				$args = array(
							'menu_title' => $wck_opc_args['page-name-in-menu'],							
							'menu_slug' => Wordpress_Creation_Kit::wck_generate_slug( $page_title ),
							'priority' => 5
						);
				if( !empty( $wck_opc_args['page-type'] ) ){
					$args['page_type'] = $wck_opc_args['page-type'];					
					if( !empty( $wck_opc_args['parent-slug-for-submenu-page'] ) ){
						$args['parent_slug'] = $wck_opc_args['parent-slug-for-submenu-page'];
					}					
				}
				else{
					if( !empty( $wck_opc_args['display'] ) ){
						switch ($wck_opc_args['display']) {
							case 'As toplevel page':
								$args['page_type'] = 'menu_page';
								break;
							case 'Under Appearance menu':
								$args['page_type'] = 'submenu_page';
								$args['parent_slug'] = 'themes.php';
								
								break;
							case 'Under Settings menu':
								$args['page_type'] = 'submenu_page';
								$args['parent_slug'] = 'options-general.php';
								break;
						}
					}
				}
				
				if( !empty( $wck_opc_args['page-title'] ) )
					$args['page_title'] = $wck_opc_args['page-title'];
				else 
					$args['page_title'] = $wck_opc_args['page-name-in-menu'];
				
				
				if( !empty( $wck_opc_args['position-for-toplevel-page'] ) )
					$args['position'] = $wck_opc_args['position-for-toplevel-page'];
				else
					$args['position'] = null;
					
				if( !empty( $wck_opc_args['capability'] ) )
					$args['capability'] = $wck_opc_args['capability'];
				else 
					$args['capability'] = 'edit_theme_options';
					
				if( !empty( $wck_opc_args['icon-url-for-toplevel-page'] ) )
					$args['icon_url'] = $wck_opc_args['icon-url-for-toplevel-page'];
					
				$page = new WCK_Page_Creator( $args );
			}
		}
	}
}


/* hook to create option fields groups */
add_action( 'init', 'wck_opc_create_option_pages_fields' );
function wck_opc_create_pages_args(){
	$args = array(
		'post_type' => 'wck-option-field',
		'numberposts' => -1
	);
	
	$all_option_fields = get_posts( $args );
	
	$all_pages_args = array();
	
	if( !empty( $all_option_fields ) ){
		foreach( $all_option_fields as $option_field ){
			$wck_opc_field_args = get_post_meta( $option_field->ID, 'wck_opc_field_args', true );
			$wck_opc_fields = get_post_meta( $option_field->ID, 'wck_opc_fields', true );
			
			$page_title = get_the_title( $option_field->ID );
			/* treat case where the post has no title */
			if( empty( $page_title ) )
				$page_title = '(no title)';
			
			$fields_array = array();
			if( !empty( $wck_opc_fields ) ){
				foreach( $wck_opc_fields as $wck_opc_field ){
					$fields_inner_array = array( 'type' => $wck_opc_field['field-type'], 'title' => $wck_opc_field['field-title'] );

					if( !empty( $wck_opc_field['field-slug'] ) )
						$fields_inner_array['slug'] = $wck_opc_field['field-slug'];
					else
						$fields_inner_array['slug'] = Wordpress_Creation_Kit::wck_generate_slug( $wck_opc_field['field-title'] );

					if( !empty( $wck_opc_field['description'] ) )
						$fields_inner_array['description'] = $wck_opc_field['description']; 
					if( !empty( $wck_opc_field['required'] ) )
						$fields_inner_array['required'] = $wck_opc_field['required'] == 'false' ? false : true;
					if ( !empty( $wck_opc_field['cpt'] ) )
						$fields_inner_array['cpt'] = $wck_opc_field['cpt']; 					
					if( !empty( $wck_opc_field['default-value'] ) )
						$fields_inner_array['default'] = $wck_opc_field['default-value'];
                    if( isset( $wck_opc_field['default-text'] ) && !empty( $wck_opc_field['default-text'] ) )
                        $fields_inner_array['default'] = $wck_opc_field['default-text'];
					if( !empty( $wck_opc_field['options'] ) ){
						$fields_inner_array['options'] = array_map( 'trim', explode( ',', $wck_opc_field['options'] ) );

                        if( !empty( $wck_opc_field['labels'] ) ){
                            $labels = array_map( 'trim', explode( ',', $wck_opc_field['labels'] ) );
                        }
						
						if( !empty( $fields_inner_array['options'] ) ){
							foreach( $fields_inner_array['options'] as  $key => $value ){
								$fields_inner_array['options'][$key] = trim( $value );
                                if( strpos( $value, '%' ) === false && !empty( $labels[$key] ) )
                                    $fields_inner_array['options'][$key] = '%'.$labels[$key].'%'.$value;
							}
						}
						
					}

                    if( !empty( $wck_opc_field['number-of-rows'] ) )
                        $fields_inner_array['number_of_rows'] = trim( $wck_opc_field['number-of-rows'] );

                    if( !empty( $wck_opc_field['readonly'] ) )
                        $fields_inner_array['readonly'] = $wck_opc_field['readonly'] == 'true' ? true : false;

					if( ! empty( $wck_opc_field['phone-format'] ) ) {
						$phone_format_description = __( 'Required phone number format: ', 'wck' ) . $wck_opc_field['phone-format'];
						$phone_format_description = apply_filters( 'wck_phone_format_description', $phone_format_description );
						if( $wck_opc_field['field-type'] === 'phone' ) {
							$fields_inner_array['phone-format'] = $wck_opc_field['phone-format'];
							if( ! empty( $wck_opc_field['description'] ) ) {
								$fields_inner_array['description'] .= '<br>' . $phone_format_description;
							} else {
								$fields_inner_array['description'] = $phone_format_description;
							}
						}
					}


                    if( $wck_opc_field['field-type'] === 'html' && isset( $wck_opc_field['html-content'] ) ) {
                        $fields_inner_array['html-content'] = $wck_opc_field['html-content'];
                    }


                    if( isset( $wck_opc_field['map-default-latitude'] ) )
                        $fields_inner_array['map_default_latitude'] = trim( $wck_opc_field['map-default-latitude'] );

                    if( isset( $wck_opc_field['map-default-longitude'] ) )
                        $fields_inner_array['map_default_longitude'] = trim( $wck_opc_field['map-default-longitude'] );

                    if( !empty( $wck_opc_field['map-default-zoom'] ) )
                        $fields_inner_array['map_default_zoom'] = trim( $wck_opc_field['map-default-zoom'] );

                    if( !empty( $wck_opc_field['map-height'] ) )
                        $fields_inner_array['map_height'] = trim( $wck_opc_field['map-height'] );

					if( !empty( $wck_opc_field['min-number-value'] ) || ( isset( $wck_opc_field['min-number-value'] ) && $wck_opc_field['min-number-value'] == '0' ) )
						$fields_inner_array['min-number-value'] = trim( $wck_opc_field['min-number-value'] );

					if( !empty( $wck_opc_field['max-number-value'] ) || ( isset( $wck_opc_field['max-number-value'] ) && $wck_opc_field['max-number-value'] == '0' ) )
						$fields_inner_array['max-number-value'] = trim( $wck_opc_field['max-number-value'] );

					if( !empty( $wck_opc_field['number-step-value'] ) )
						$fields_inner_array['number-step-value'] = trim( $wck_opc_field['number-step-value'] );

					if( $wck_opc_field['field-type'] === 'datepicker' ) {
						if( !empty( $wck_opc_field['date-format'] ) )
							$fields_inner_array['date-format'] = $wck_opc_field['date-format'];
					}

					$fields_array[] = $fields_inner_array;
				}
			}
			
			if( !empty( $wck_opc_field_args ) ){
				foreach( $wck_opc_field_args as $wck_opc_field_arg ){								
				
					/* metabox_id must be different from meta_name */
					$metabox_id = Wordpress_Creation_Kit::wck_generate_slug( $page_title );				
					if( $wck_opc_field_arg['option-name'] == $metabox_id )
						$metabox_id = 'wck-'. $metabox_id;
					
					$page_args = array(
									'metabox_id' => $metabox_id,
									'metabox_title' => $page_title,
									'post_type' => $wck_opc_field_arg['option-page'],
									'meta_name' => $wck_opc_field_arg['option-name'],
									'meta_array' => $fields_array,
									'context' => 'option'
								);
					if( !empty( $wck_opc_field_arg['sortable'] ) )
						$page_args['sortable'] = $wck_opc_field_arg['sortable'] == 'false' ? false : true;
					
					if( !empty( $wck_opc_field_arg['repeater'] ) )					
						$page_args['single'] = $wck_opc_field_arg['repeater'] == 'false' ? true : false;
					
					$all_pages_args[] = $page_args;
				}
			}
		}
	}
	return $all_pages_args;
}

function wck_opc_create_option_pages_fields(){
	$all_pages_args = wck_opc_create_pages_args();
	if( !empty( $all_pages_args ) ){
		foreach( $all_pages_args as $page_args ){
			new Wordpress_Creation_Kit( $page_args );
		}
	}
}


/* Filter post update message */
add_filter( 'post_updated_messages', 'wck_opc_filter_post_update_message' );
function wck_opc_filter_post_update_message($messages){
	$messages['wck-option-page'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __('Option Page updated.', 'wck')
	);
	$messages['wck-option-field'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __('Option Field updated.', 'wck')
	);
	return $messages;
}


/* Meta Name Verification */
add_filter( 'wck_required_test_wck_opc_field_args_option-name', 'wck_opc_ceck_option_name', 10, 6 );
function wck_opc_ceck_option_name( $bool, $value, $post_id, $field, $meta, $fields ){
	global $wpdb;
	
	$wck_opc_field_args = get_post_meta( $post_id, 'wck_opc_field_args', true );
	
	if( empty( $wck_opc_field_args ) ){		
		//this is the add case		
		$check_option_existance = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(option_name) FROM $wpdb->options WHERE option_name = %s", $value ) );		
	}
	else{
		//this is the update case
		if( $wck_opc_field_args[0]['option-name'] != $value ){
			$check_option_existance = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(option_name) FROM $wpdb->options WHERE option_name = %s", $value ) );
		}
		else 
			$check_option_existance = false;
	}
	
	if( strpos( $value, ' ' ) === false )
		$contains_spaces = false;
	else 
		$contains_spaces = true;
		
	if( preg_match("/[A-Z]/", $value) === 0 )
		$contains_uppercase = false;	
	else
		$contains_uppercase = true;	
	
	return ( $check_option_existance || empty($value) || $contains_spaces || $contains_uppercase );
}

add_filter( 'wck_required_message_wck_opc_field_args_option-name', 'wck_opc_change_option_message', 10, 3 );
function wck_opc_change_option_message( $message, $value, $required_field ){
	if( empty( $value ) )
		return $message;
	else if( strpos( $value, ' ' ) !== false )
		return __( "Choose a different Option Name as this one contains spaces\n", "wck" );
	else if( preg_match("/[A-Z]/", $value) !== 0 )
		return __( "Choose a different Option Name as this one has Uppercase letters\n", "wck" );
	else
		return __( "Choose a different Option Name as this one already exists\n", "wck" );
}

/* Change option_name in db if field changed */
add_action( 'wck_before_update_meta', 'wck_opc_change_option_name', 10, 4 );
function wck_opc_change_option_name( $meta, $id, $values, $element_id ){
	global $wpdb;
	if( $meta == 'wck_opc_field_args' ){
		$wck_opc_field_args = get_post_meta( $id, 'wck_opc_field_args', true );		
		if( !empty( $wck_opc_field_args ) ) {
            if ($wck_opc_field_args[0]['option-name'] != $values['option-name']) {
                $wpdb->update(
                    $wpdb->options,
                    array('option_name' => $values['option-name']),
                    array('option_name' => $wck_opc_field_args[0]['option-name'])
                );
            }
        }
	}
}

/* Change Field Title in db if field changed */
add_action( 'wck_before_update_meta', 'wck_opc_change_field_title', 10, 4 );
function wck_opc_change_field_title( $meta, $id, $values, $element_id ){
	if( $meta == 'wck_opc_fields' ) {
        $wck_opc_fields = get_post_meta($id, 'wck_opc_fields', true);

        if(!empty($wck_opc_fields)){
            if ($wck_opc_fields[$element_id]['field-title'] != $values['field-title']) {

                $wck_opc_field_args = get_post_meta($id, 'wck_opc_field_args', true);
                $option_name = $wck_opc_field_args[0]['option-name'];

                $results = get_option($option_name);
                if (!empty($results)) {
                    foreach ($results as $key => $result) {
                        $results[$key][Wordpress_Creation_Kit::wck_generate_slug($values['field-title'])] = $results[$key][Wordpress_Creation_Kit::wck_generate_slug($wck_opc_fields[$element_id]['field-title'])];
                        unset($results[$key][Wordpress_Creation_Kit::wck_generate_slug($wck_opc_fields[$element_id]['field-title'])]);
                    }
                }
                update_option($option_name, $results);
            }
        }
	}
}

/* Change Option Page arg in db if Page name changed */
add_action( 'wck_before_update_meta', 'wck_opc_change_option_page_arg', 10, 4 );
function wck_opc_change_option_page_arg( $meta, $id, $values, $element_id ){
	global $wpdb;
	if( $meta == 'wck_opc_args' ){
		$wck_opc_args = get_post_meta( $id, 'wck_opc_args', true );
		if( !empty( $wck_opc_args ) ){
            if ($wck_opc_args[$element_id]['page-name-in-menu'] != $values['page-name-in-menu']) {

                /* Get all Option Fields */
                $args = array(
                    'post_type' => 'wck-option-field',
                    'numberposts' => -1
                );

                $all_option_fields = get_posts($args);
                if (!empty($all_option_fields)) {
                    foreach ($all_option_fields as $all_option_field) {
                        $option_field_args = get_post_meta($all_option_field->ID, 'wck_opc_field_args', true);
                        if (!empty($option_field_args)) {
                            if ($option_field_args[0]['option-page'] == Wordpress_Creation_Kit::wck_generate_slug($wck_opc_args[$element_id]['page-name-in-menu'])) {
                                $option_field_args[0]['option-page'] = Wordpress_Creation_Kit::wck_generate_slug($values['page-name-in-menu']);
                                update_post_meta($all_option_field->ID, 'wck_opc_field_args', $option_field_args);
                            }
                        }
                    }
                }
            }
        }
	}
}

/* save page title on adding or updating the option page */
add_action( 'wck_before_add_meta', 'wck_opc_update_page_title', 10, 3 );
add_action( 'wck_before_update_meta', 'wck_opc_update_page_title', 10, 3 );
function wck_opc_update_page_title( $meta, $id, $values ){
    global $wpdb;
	if( $meta == 'wck_opc_args' ){
        $wpdb->update( $wpdb->posts, array( 'post_title' =>  stripslashes( $values['page-name-in-menu'] ), 'post_status' => 'publish' ), array( 'ID' => $id ) );
	}
	
	if( $meta == 'wck_opc_field_args' ){
        $wpdb->update( $wpdb->posts, array( 'post_title' =>  stripslashes( $values['group-title'] ), 'post_status' => 'publish' ), array( 'ID' => $id ) );
	}
}

/* Filter Field Types for free version */
add_filter( 'wck_field_types', 'wck_opc_filter_field_types' );
function wck_opc_filter_field_types( $field_types ){
	$wck_premium_update = WCK_PLUGIN_DIR.'/update/';
	if ( !file_exists ($wck_premium_update . 'update-checker.php'))
		$field_types = array( 'text', 'textarea', 'select', 'select multiple', 'checkbox', 'radio', 'upload', 'wysiwyg editor', 'heading', 'colorpicker', 'currency select', 'phone', 'timepicker', 'html', 'number' );
	
	return $field_types;
}
?>