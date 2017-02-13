<?php
/* FrontEnd posting capabilities  */

/* hook to create custom post types */
add_action( 'init', 'wck_fep_create_frontend_posting_cpt' );
/**
 * Function that creates the "wck-frontend-posting" post type
 */
function wck_fep_create_frontend_posting_cpt(){	
	if( is_admin() && current_user_can( 'edit_theme_options' ) ){		
		$labels = array(
			'name' => _x( 'WCK Frontend Posting', 'post type general name'),
			'singular_name' => _x( 'Frontend Posting', 'post type singular name'),
			'add_new' => _x( 'Add New', 'Frontend Posting' ),
			'add_new_item' => __( "Add New Frontend Posting", "wck" ),
			'edit_item' => __( "Edit Frontend Posting", "wck" ) ,
			'new_item' => __( "New Frontend Posting", "wck" ),
			'all_items' => __( "Frontend Posting", "wck" ),
			'view_item' => __( "View Frontend Posting", "wck" ),
			'search_items' => __( "Search Frontend Posting", "wck" ),
			'not_found' =>  __( "No Frontend Posting found", "wck" ),
			'not_found_in_trash' => __( "No Frontend Posting found in Trash", "wck" ), 
			'parent_item_colon' => '',
			'menu_name' => __( 'Frontend Posting', 'wck' )
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
				
		register_post_type( 'wck-frontend-posting', $args );
	}
}

/* add admin body class to fep custom post type */
add_filter( 'admin_body_class', 'wck_fep_admin_body_class' );
function wck_fep_admin_body_class( $classes ){
	if( isset( $_GET['post_type'] ) || isset( $_GET['post'] ) ){
		if( isset( $_GET['post_type'] ) )
			$post_type = $_GET['post_type'];
		else if( isset( $_GET['post'] ) )
			$post_type = get_post_type( $_GET['post'] );
		
		if( 'wck-frontend-posting' == $post_type ){			
			$classes .= ' wck_page_fep-page ';
		}
	}
	return $classes;
}

/* Remove view action from post list view */
add_filter('post_row_actions','wck_fep_remove_view_action');
function wck_fep_remove_view_action($actions){
	global $post;
	if ($post->post_type =="wck-frontend-posting"){	
	   unset( $actions['view'] );	  
	}
   return $actions;
}

/* Register and enqueue scripts in backend */
add_action('admin_enqueue_scripts', 'wck_print_scripts' );
function wck_print_scripts($hook){
	if( isset( $_GET['post_type'] ) || isset( $_GET['post'] ) ){
		if( isset( $_GET['post_type'] ) )
				$post_type = $_GET['post_type'];
			else if( isset( $_GET['post'] ) )
				$post_type = get_post_type( $_GET['post'] );		
		
		if( 'wck-frontend-posting' == $post_type ){
			wp_register_style('wck-fep-backend-css', plugins_url('/css/wck-fep.css', __FILE__) );
			wp_enqueue_style('wck-fep-backend-css');
			
			wp_register_script('wck-fep-backend-js', plugins_url('/js/wck-fep.js', __FILE__), array( 'jquery' ), '1.0' );
			wp_enqueue_script('wck-fep-backend-js');
		}
	}
}

/* create the settings meta boxes. Using a priority of 500 to hopefully get all registered custom post types on init hook that don't have the default priority */
add_action( 'init', 'wck_fep_create_boxes', 500 );
function wck_fep_create_boxes(){
	
	/* get all post types */
	$args = array(
			'public'   => true
		);
	$output = 'objects'; // or objects
	$post_types = get_post_types($args,$output);
	$post_type_names = array(); 
	if( !empty( $post_types ) ){
		foreach ( $post_types  as $post_type ) {
			if ( $post_type->name != 'attachment' && $post_type->name != 'wck-meta-box' && $post_type->name != 'wck-frontend-posting' && $post_type->name != 'wck-option-page' && $post_type->name != 'wck-option-field' && $post_type->name != 'wck-swift-template' ) 
				$post_type_names[] = $post_type->name;
		}
	}
	
	/* get all page names and ids */
	$args = array(
				'post_type' => 'page',
				'numberposts' => -1				
			);
	$pages = get_posts( $args );
	
	$page_names = array();
	$page_ids = array();
	if( !empty( $pages ) ){
		foreach( $pages as $page ){
			$page_names[] = get_the_title( $page->ID );
			$page_ids[] = $page->ID;		
		}
	}
	
	
	/* set up the fields array */
	$fep_box_args_fields = array( 				
		array( 'type' => 'select', 'title' => __( 'Post Type', 'wck' ), 'slug' => 'post-type', 'options' => $post_type_names, 'default-option' => true, 'description' => __( 'Select in what post type the entries of this form will be added.', 'wck' ), 'required' => true ),
		array( 'type' => 'radio', 'title' => __( 'Anonymous posting', 'wck' ), 'slug' => 'anonymous-posting', 'options' => array( 'yes', 'no' ), 'default' => 'no', 'description' => __( 'Enable anonymous posting. If enabled users can use this form without being logged in.', 'wck' ) ),
		array( 'type' => 'user select', 'title' => __( 'Assign to user', 'wck' ), 'slug' => 'assign-to-user', 'description' => __( 'The user that the posts are assigned to with "Anonymous posting". Defaults to admin', 'wck' ) ),
		array( 'type' => 'radio', 'title' => __( 'Admin Approval', 'wck' ), 'slug' => 'admin-approval', 'options' => array( 'yes', 'no' ), 'default' => 'no', 'description' => __( 'Enable Admin Approval. If enabled the entries from this form will be set as drafts and will require an admin to approve them.', 'wck' ) ),
		array( 'type' => 'select', 'title' => __( 'Shortcode Page', 'wck' ), 'slug' => 'shortcode-page', 'options' => $page_names, 'values' => $page_ids, 'default-option' => true, 'description' => __( 'The page on which the shortcode will be placed. You can come back and edit this later.', 'wck' ) ),
	);
	
	/* set up the box arguments */
	$args = array(
		'metabox_id' => 'wck-fep-args',
		'metabox_title' => __( 'Form Setup', 'wck' ),
		'post_type' => 'wck-frontend-posting',
		'meta_name' => 'wck_fep_args',
		'meta_array' => $fep_box_args_fields,			
		'sortable' => false,
		'single' => true
	);
	/* create the Form Setup box */
	new Wordpress_Creation_Kit( $args );
	
	
	/* default form fields */
	$default_fields_titles = array( 'Post Title', 'Post Content', 'Post Excerpt', 'Featured Image' );
	
	/* set up the fields array */
	$fep_box_fields_fields = array(		
		array( 'type' => 'select', 'title' => __( 'Field Type', 'wck' ), 'slug' => 'field-type', 'options' => $default_fields_titles, 'default-option' => true, 'description' => __( 'The field type', 'wck' ), 'required' => true ),
		array( 'type' => 'radio', 'title' => __( 'Required', 'wck' ), 'slug' => 'required', 'options' => array( 'yes', 'no' ), 'default' => 'no', 'description' => __( 'Is this field required?', 'wck' ) )
	);	
	
	/* add to Field Types all the taxonomies registered for the post type selected in the Form Setup */
	if( isset( $_GET['post'] ) ){
		$taxonomy_titles = wck_fep_get_taxonomies_for_post_type( $_GET['post'] );	
		if( !empty( $taxonomy_titles ) ){
			foreach( $taxonomy_titles as $tax_name => $taxonomy_title ){
				$fep_box_fields_fields[0]['options'][] = $taxonomy_title;
			}
		}
	}
	
	/* add to Field Types all the CFC registered ( with Custom Fields Creator ) for the post type selected in the Form Setup */
	if( isset( $_GET['post'] ) ){
		$cfc_titles = wck_fep_get_cfcs_for_post_type( $_GET['post'] ); 
		if( !empty( $cfc_titles ) ){
			foreach( $cfc_titles as $cfc_title ){
				$fep_box_fields_fields[0]['options'][] = $cfc_title;
			}
		}
	}
	
	/* set up the box arguments */
	$args = array(
		'metabox_id' => 'wck-fep-fields',
		'metabox_title' => __( 'Form Fields', 'wck' ),
		'post_type' => 'wck-frontend-posting',
		'meta_name' => 'wck_fep_fields',
		'meta_array' => $fep_box_fields_fields
	);

	/* create the Form Fields box */
	new Wordpress_Creation_Kit( $args );
	
	
	/* Labels for the default fields */
	
	/* set up the labels array */
	$fep_box_label_fields = array(		
		array( 'type' => 'text', 'title' => __( 'Post Title', 'wck' ), 'slug' => 'post-title', 'description' => __( 'Change the "Post Title" label', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Post Content', 'wck' ), 'slug' => 'post-content', 'description' => __( 'Change the "Post Content" label', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Post Excerpt', 'wck' ), 'slug' => 'post-excerpt', 'description' => __( 'Change the "Post Excerpt" label', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Featured Image', 'wck' ), 'slug' => 'featured-image', 'description' => __( 'Change the "Featured Image" label', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Add Post', 'wck' ), 'slug' => 'add-post', 'description' => __( 'Change the "Add Post" label for the submit button', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Update Post', 'wck' ), 'slug' => 'update-post', 'description' => __( 'Change the "Update Post" label for the submit button', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Post Added', 'wck' ), 'slug' => 'post-added', 'description' => __( 'Change the "Post Added" success message', 'wck' ) ),
		array( 'type' => 'text', 'title' => __( 'Post Updated', 'wck' ), 'slug' => 'post-updated', 'description' => __( 'Change the "Post Updated" success message', 'wck' ) )
	);		
	
	/* set up the box arguments */
	$args = array(
		'metabox_id' => 'wck-fep-labels',
		'metabox_title' => __( 'Form Labels and Messages', 'wck' ),
		'post_type' => 'wck-frontend-posting',
		'meta_name' => 'wck_fep_labels',
		'meta_array' => $fep_box_label_fields,
		'single' => true
	);

	/* create the Form Fields box */
	new Wordpress_Creation_Kit( $args );
}


/**
 * Funtion that retrieves all the CFC's for the selected post type in form args
 *
 * @since 1.0.0
 *
 * @param int $post_id Post ID of the form.	  
 * @return array $cfc_titles containing the Meta Boxes Titles created with CFC.
 */
function wck_fep_get_cfcs_for_post_type( $post_id ){
	$cfc_titles = array();
	
	if( function_exists( 'wck_cfc_create_boxes_args' ) ){
		
		$all_box_args = wck_cfc_create_boxes_args();	
		
		if( !empty( $all_box_args ) ){
			foreach( $all_box_args as $box_args ){

				$form_args = get_post_meta( $post_id, 'wck_fep_args', true );		
				
				if( !empty( $form_args ) ){
					if( ( $box_args['post_type'] == $form_args[0]['post-type'] ) ){					
						$cfc_titles[] = 'CFC-'.$box_args['metabox_title'];
					}
				}
			}
		}
	
	}
	
	return $cfc_titles;
}

/**
 * Funtion that retrieves all the registered taxonomie names for the selected post type in form args
 *
 * @since 1.0.0
 *
 * @param int $post_id Post ID of the form.	  
 * @return array $taxonomy_titles containing the Taxonomy names.
 */
function wck_fep_get_taxonomies_for_post_type( $post_id ){
	$taxonomy_titles = array();		

	$form_args = get_post_meta( $post_id, 'wck_fep_args', true );		
			
	if( !empty( $form_args ) ){
		$object_taxonomies = get_object_taxonomies( $form_args[0]['post-type'] );
		if( !empty( $object_taxonomies ) ){
			foreach( $object_taxonomies as $tax_name ){
				$taxonomy_object = get_taxonomy( $tax_name );
				if(  $taxonomy_object->label != 'Format' )
					$taxonomy_titles['Taxonomy: '.$taxonomy_object->name] = __( 'Taxonomy: ', 'wck' ) .$taxonomy_object->label;
			}
		}
	}
	
	return $taxonomy_titles;
}

/* Add an extra verification for the Field Type select in the Form Fields Meta Box. Throw error if the field type is allready added  */
add_filter( 'wck_required_test_wck_fep_fields_field-type', 'wck_fep_ceck_field_type', 10, 6 );
function wck_fep_ceck_field_type( $bool, $value, $post_id, $field, $meta, $fields ){
	$wck_fep_fields = get_post_meta( $post_id, 'wck_fep_fields', true );
	
	$allready_exists = false;
	
	if( !empty( $wck_fep_fields ) ){		
		foreach( $wck_fep_fields as $wck_fep_field ){
			if( in_array( $value, $wck_fep_field ) ){
				$allready_exists = true;
				break;
			}				
		}
	}	
	
	return ( $allready_exists || empty($value) );
}
/* Message filter if the allready existing error is thrown */
add_filter( 'wck_required_message_wck_fep_fields_field-type', 'wck_fep_change_fields_message', 10, 3 );
function wck_fep_change_fields_message( $message, $value, $required_field ){
	if( empty( $value ) )
		return $message;	
	else
		return __( "Choose a different Field Type as this one already exists in your Form\n", 'wck' );
}

/* add CFC Titles and Taxonomy Names to Field Type Select when changing post type for the form */
add_action("wck_refresh_list_wck_fep_args", "wck_fep_change_field_type_select");
add_action("wck_refresh_entry_wck_fep_args", "wck_fep_change_field_type_select");
add_action("wck_ajax_add_form_wck_fep_fields", "wck_fep_change_field_type_select");
function wck_fep_change_field_type_select( $id ){
	
	/* make sure it is the same as  the one in wck_fep_create_boxes(). maybe use a global */
	$default_fields_titles = array( 'Post Title' => 'Post Title', 'Post Content' => 'Post Content', 'Post Excerpt' => 'Post Excerpt', 'Featured Image' => 'Featured Image' );
	
	
	/* get taxonomy titles for the post type and add them to the default fields titles */
	$taxonomy_titles = wck_fep_get_taxonomies_for_post_type( $id );	
	if( !empty( $taxonomy_titles ) ){
		foreach( $taxonomy_titles as $tax_name => $taxonomy_title ){
			$default_fields_titles[$taxonomy_title] = $taxonomy_title;
		}
	}	
	
	/* get cfc titles for the post type and add them to the default titles */
	$cfc_titles = wck_fep_get_cfcs_for_post_type( $id ); 
	if( !empty( $cfc_titles ) ){
		foreach( $cfc_titles as $cfc_title ){
			$default_fields_titles[$cfc_title] = $cfc_title;
		}
	}
	
	/* build the select html */
	$select_html = '<option value=\"\">'. __( '...Choose', 'wck' ) .'</option>';
	foreach( $default_fields_titles as $default_fields_title ){
		$select_html .= '<option value=\"'. $default_fields_title .'\">'. $default_fields_title .'</option>';
	}	
	
	echo '<script type="text/javascript">jQuery("#field-type").html("'. $select_html .'")</script>';
}

/* display or show options based on the field type */
add_filter( "wck_before_listed_wck_fep_fields_element_0", 'wck_fep_display_label_wrapper_start', 10, 3 );
function wck_fep_display_label_wrapper_start( $form, $i, $value ){
	$GLOBALS['wck_fep_field_type'] = $value;
	return $form;
}

add_filter( "wck_before_listed_wck_fep_fields_element_1", 'wck_fep_display_label_wrapper_options_start', 10, 3 );
function wck_fep_display_label_wrapper_options_start( $form, $i, $value ){
	if( ( !in_array( $GLOBALS['wck_fep_field_type'], array( 'Post Title', 'Post Content', 'Post Excerpt', 'Featured Image' ) ) ) && ( strpos( $GLOBALS['wck_fep_field_type'], 'Taxonomy: ' ) === false ) )
		$form .= '<div style="display:none;">';
	return $form;
}

add_filter( "wck_after_listed_wck_fep_fields_element_1", 'wck_fep_display_label_wrapper_options_end', 10, 3 );
function wck_fep_display_label_wrapper_options_end( $form, $i, $value ){
	if( !in_array( $GLOBALS['wck_fep_field_type'], array( 'Post Title', 'Post Content', 'Post Excerpt', 'Featured Image' ) ) && strpos( $GLOBALS['wck_fep_field_type'], 'Taxonomy: ' ) === false )
		$form .= '</div>';
	return $form;
}

/**
 * Function that gets the arguments of all the forms and returns them
 * 
 * @return array $all_form_args Contains all the existing forms arguments.
 */
function wck_fep_create_forms_args(){
	$args = array(
		'post_type' => 'wck-frontend-posting',
		'numberposts' => -1
	);
	
	$all_forms = get_posts( $args );
	
	$all_form_args = array();
	
	if( !empty( $all_forms ) ){
		foreach( $all_forms as $form ){
			$wck_fep_args = get_post_meta( $form->ID, 'wck_fep_args', true );
			$wck_fep_fields = get_post_meta( $form->ID, 'wck_fep_fields', true );
			
			$form_title = get_the_title( $form->ID );
			/* treat case where the post has no title */
			if( empty( $form_title ) )
				$form_title = '(no title)';
				
			/* form name */
			$form_name = Wordpress_Creation_Kit::wck_generate_slug( $form_title );
			
			/* will contain any taxonomies that are added to the form */
			$taxonomies_array = array();
			
			$fields_array = array();
			if( !empty( $wck_fep_fields ) ){
				foreach( $wck_fep_fields as $wck_fep_field ){
				
					switch ( $wck_fep_field['field-type'] ) {
						case 'Post Title':
							$fields_inner_array = array( 'type' => 'text', 'title' => __( 'Post Title', 'wck' ), 'description' => __( 'The title of the post', 'wck' ) ); 						
							break;
						case 'Post Content':
							$fields_inner_array = array( 'type' => 'wysiwyg editor', 'title' => __( 'Post Content', 'wck' ), 'description' => __( 'The content of the post', 'wck' ) ); 						
							break;
						case 'Post Excerpt':
							$fields_inner_array = array( 'type' => 'textarea', 'title' => __( 'Post Excerpt', 'wck' ), 'description' => __( 'The excerpt of the post', 'wck' ) );
							break;					
						case 'Featured Image':
							$fields_inner_array = array( 'type' => 'upload', 'title' => __( 'Featured Image', 'wck' ), 'description' => __( 'The Featured Image', 'wck' ), 'attach_to_post' => apply_filters( 'wck_fep_attach_featured_image_to_post', true ) );
							break;
						default:
							if( strpos( $wck_fep_field['field-type'], 'CFC-' ) !== false ){
								/* CFC */
								$fields_inner_array = array( 'cfc' => 'true', 'title' => preg_replace('/CFC-/', '', $wck_fep_field['field-type'], 1) );
							}
							else if( strpos( $wck_fep_field['field-type'], 'Taxonomy: ' ) !== false ){
							
								$taxonomy_label = preg_replace( '/Taxonomy: /', '', $wck_fep_field['field-type'], 1 );
								
								/* taxonomy */
								$found_taxonomy = false;
								$taxonomy_objects = get_object_taxonomies( $wck_fep_args[0]['post-type'], 'objects' );
								
								if( !empty( $taxonomy_objects ) ){
									foreach( $taxonomy_objects as $taxonomy ){
										if( $taxonomy->label == $taxonomy_label ){
											$found_taxonomy = true;
											$taxonomies_array[] = $taxonomy;
											
											if( $taxonomy->hierarchical == true ){
												$fields_inner_array = array( 'type' => apply_filters( 'wck_fep_hierarchical_taxonomy_field_type', 'checkbox'), 'title' => $taxonomy_label );							
												$fields_inner_array['options']	= wck_fep_handle_hierarhical_taxonomy( array(), $taxonomy->name, 0 );										
											}
											else{
												$fields_inner_array = array( 'type' => 'text', 'title' => $taxonomy_label );
											}
										}
									}
								}
								
								/* if the taxonomy in the form isn't any more added to the post wee need to add something instead */
								if( !$found_taxonomy )
									$fields_inner_array = array( 'type' => 'not_found', 'title' => $taxonomy_label );
							}
					}
					
					if( !empty( $wck_fep_field['required'] ) )
						$fields_inner_array['required'] = $wck_fep_field['required'] == 'no' ? false : true;				
						
					$fields_array[] = $fields_inner_array;
				}
			}
			
			if( !empty( $wck_fep_args ) ){
				foreach( $wck_fep_args as $wck_fep_arg ){		
				
					$form_args = array(
									'form_title' => $form_title,
									'post_type' => $wck_fep_arg['post-type'],
									'admin_approval' => $wck_fep_arg['admin-approval'],
									'anonymous_posting' => $wck_fep_arg['anonymous-posting'],									
									'form_name' => $form_name,
									'meta_array' => $fields_array,
									'taxonomies' => $taxonomies_array
								);
					if( !empty( $wck_fep_arg['assign-to-user'] ) )
						$form_args['assign_to_user'] = $wck_fep_arg['assign-to-user'];
					else 
						$form_args['assign_to_user'] = 1; //assign to admin by default
						
					$all_form_args[] = $form_args;
				}
			}
		}
	}
	return $all_form_args;
}

function wck_fep_handle_hierarhical_taxonomy( $array_options, $taxonomy_name, $parent, $level = 0  ){
	$args = array(													
		'hide_empty'               => 0,
		'parent'			   	   => $parent
	);
	
	$terms = get_terms( $taxonomy_name, $args );
	
	if( ! empty( $terms ) ){		
		foreach( $terms as $term ){						
			$option = '%';
			for( $i = 0; $i < $level; $i++ ){
				$option .= '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			$option .= $term->name.'%'.$term->name;
			
			$array_options[] = $option;					
			
			$next_level = $level + 1;
			$array_options = wck_fep_handle_hierarhical_taxonomy( $array_options, $taxonomy_name, $term->term_id, $next_level );
			
		}
	}
	
	return $array_options;
}

/* hook to create the forms */
add_action( 'init', 'wck_fep_create_forms', 11 );
function wck_fep_create_forms(){
	
	/* get all the forms args */
	$all_form_args = wck_fep_create_forms_args();	
	
	if( !empty( $all_form_args ) ){
		foreach( $all_form_args as $form_args ){
			/* create form */
			new WCK_FrontEnd_Posting( $form_args );
		}
	}
}

/* change labels */
add_action( 'init', 'wck_fep_change_form_labels', 11 );
function wck_fep_change_form_labels(){	
	$args = array(
		'post_type' => 'wck-frontend-posting',
		'numberposts' => -1
	);
	
	$all_forms = get_posts( $args );
	
	$all_form_args = array();
	if( !empty( $all_forms ) ){
		foreach( $all_forms as $form ){
			$wck_fep_labels = get_post_meta( $form->ID, 'wck_fep_labels', true );
			if( !empty( $wck_fep_labels ) ){
				foreach( $wck_fep_labels as $wck_fep_label ){
					if( !empty( $wck_fep_label ) ){
						foreach( $wck_fep_label as $field_slug => $new_label ){						
							if( !empty( $new_label ) ){
								/* take care of special characters */
								$new_label = esc_html( $new_label );
								$this_form_name = Wordpress_Creation_Kit::wck_generate_slug( $form->post_title );
								/*  Create the anonymous (lambda-style) function to add to pur filters */
								$wck_fep_change_labels_and_messages = create_function('$label,$form_name','
									if( $form_name == "'.$this_form_name.'" ) return "'.$new_label.'"; else return $label;
								');	
								if( $field_slug == 'add-post' ){															
									add_filter( "wck_fep_form_button_add", $wck_fep_change_labels_and_messages, 10, 2 );
								}
								else if( $field_slug == 'update-post' ){															
									add_filter( "wck_fep_form_button_update", $wck_fep_change_labels_and_messages, 10, 2 );
								}
								else if( $field_slug == 'post-added' ){									
									add_filter( "wck_fep_post_added_message", $wck_fep_change_labels_and_messages, 10, 2 );
								}
								else if( $field_slug == 'post-updated' ){									
									add_filter( "wck_fep_post_updated_message", $wck_fep_change_labels_and_messages, 10, 2 );
								}
								else{
									/*  Another anonymous (lambda-style) function to use for the labels change */
									$wck_label_change = create_function('','
										return "'.$new_label.'";
									');	
									add_filter( "wck_label_". $this_form_name ."_".$field_slug, $wck_label_change );
								}
							}
						}
					}
				}
			}
		}
	}
}

/* register FEP labels for translation with wpml */
add_action( "update_post_meta", 'wck_fep_register_labels_for_translation', 10, 4 );
function wck_fep_register_labels_for_translation( $meta_id, $object_id, $meta_key, $_meta_value ){
    if( $meta_key == 'wck_fep_labels' ){
        if( !empty( $_meta_value[0] ) ) {
            foreach( $_meta_value[0] as $field_key => $field_value ) {
                if (function_exists('icl_register_string')) {
                    icl_register_string('plugin wck', 'fep_label_' . $field_key . '_translation', $field_value );
                }
            }
        }
    }
}

/* Show "Assign to user" row  */
add_filter("wck_add_form_class_wck_fep_args", 'wck_fep_update_form_assign_user', 10, 3 );
function wck_fep_update_form_assign_user( $wck_update_container_css_class, $meta, $results ){
	if( !empty( $results ) ) {
        if ($results[0]['anonymous-posting'] == 'yes')
            $wck_update_container_css_class = "update_container_$meta anonymous-posting-enabled";
        return $wck_update_container_css_class;
    }
}

/* Contextual Help */
add_action('current_screen', 'wck_fep_help');

function wck_fep_help () {    
    $screen = get_current_screen();	
    /*
     * Check if current screen is wck_page_cptc-page
     * Don't add help tab if it's not
     */
    if ( $screen->id != 'wck-frontend-posting' )
        return;

    // Add help tabs
    $screen->add_help_tab( array(
        'id'	=> 'wck_cfc_overview',
        'title'	=> __( 'Overview', 'wck' ),
        'content'	=> '<p>' . __( 'WCK Frontend Posting gives you the possibility to create post ( and custom posts ) entries from the frontend, and also edit them. <br />You can create a custom form for every post type by selecting from the most common post elements( Post Title, Post Content, Excerpt, Featured Image ), the registered taxonomies for that post type  and also any Meta Box created with WCK Custom Fields Creator ( repeater or single ).', 'wck' ) . '</p>',
    ) );
	
	$screen->add_help_tab( array(
        'id'	=> 'wck_cfc_arguments',
        'title'	=> __( 'Form Setup', 'wck' ),
        'content'	=> '<p>' . __( 'In the "Form Setup" metabox you set the form arguments. <br />The "Post type" argument determines in what post type the entries from the form will be added and also what field types you can add to the form.<br /> "Anonymous posting" and "Admin Approval" determine weather entries can be added without users being logged in and weather the posts will be automatically published.<br /> The "Shortcode Page" argument is used to determine on what page the form for this post type is displayed so that when we want to edit a post of this post type we are directed to the correct form (even tough this argument isn\'t required it needs to be set for editing posts to work correctly).', 'wck' ) . '</p>',
    ) );
	
	$screen->add_help_tab( array(
        'id'	=> 'wck_cfc_fields',
        'title'	=> __( 'Form Fields', 'wck' ),
        'content'	=> '<p>' . __( 'Define here the fields contained by the form. <br />"Field Type" is dynamically constructed from the default fields( Post title, Post content, Excerpt, Featured image ), all the registered taxonomies and all the registered Meta Boxes for the post type selected in the "Post Type" option in the "Form Setup" Meta Box.', 'wck' ) . '</p>',
    ) );
	
	$screen->add_help_tab( array(
        'id'	=> 'wck_cfc_example',
        'title'	=> __( 'Frontend Usage', 'wck' ),
        'content'	=> '<p>' . __( 'To display a form in the frontend you must place it\'s corresponding shortcode on the desired page. You can take this shortcode from the "Form Shortcode" side metabox displayed on every form creation page.<br /> To display the Frontend Dashboard where you can see your posts, edit or delete them just add the following shortcode on the desired page "<strong>[fep-dashboard]</strong>"<br />Frontend Posting also provides a login/logout/register widget that also has a corresponding shortcode "[fep-lilo]".', 'wck' ) . '</p>'
    ) );
}


/* Change Shortcode Page field dispayed val */
add_filter( "wck_displayed_value_wck_fep_args_shortcode-page", 'wck_fep_change_shortcode_page_displayed_val' );
function wck_fep_change_shortcode_page_displayed_val( $value ){
	return get_the_title( intval( $value ) );
}

/* Add side metaboxes */
add_action( 'add_meta_boxes', 'wck_fep_add_side_boxes' );
function wck_fep_add_side_boxes(){
	add_meta_box( 'wck-fep-side', __( 'Form Shortcode', 'wck' ), 'wck_fep_side_shortcode', 'wck-frontend-posting', 'side', 'low' );	
}
/**
 * Callback function for the shortcode side meta box
 */
function wck_fep_side_shortcode(){
	global $post;
	
	if( empty( $post->post_title ) )
		$post->post_title = '(no title)';
		
	echo '<p>'. __( 'Use this shortcode on the page you want the form to be displayed:', 'wck' ) .'<br/><strong>[fep form_name="'. Wordpress_Creation_Kit::wck_generate_slug( $post->post_title ) .'"]</strong></p>';
	echo '<p>'. __( 'Note: changing the Form Title also changes the shortcode!', 'wck' ) . '</p>';
}

/* Add Custom columns to listing */
add_filter("manage_wck-frontend-posting_posts_columns", "wck_fep_edit_columns" );
function wck_fep_edit_columns($columns){
	$columns['fep-shortcode'] = __( "Shortcode", "wck" );	
	return $columns;
}

/* Let's set up what to display in the columns */
add_action("manage_wck-frontend-posting_posts_custom_column",  "wck_fep_custom_columns", 10, 2);
function wck_fep_custom_columns( $column_name, $post_id ){
	if( $column_name == 'fep-shortcode' ){
		$post = get_post( $post_id );
		
		if( empty( $post->post_title ) )
			$post->post_title = '(no title)';
		
		echo '<strong>[fep form_name="'. Wordpress_Creation_Kit::wck_generate_slug( $post->post_title ) .'"]</strong>';
	}
}

/**
 * Adds Login/Logout Widget
 */
class WCK_FEP_Login_Logout extends WP_Widget {
	
	static $wck_fep_lilo_presence;
	
	public function __construct() {	
		parent::__construct(
	 		'wck_fep_login_logout', // Base ID
			'FEP Login/Logout Widget', // Name
			array( 'description' => __( 'Login/Logout Widget', 'wck' ) ) // Args
		);
		
		/* register scripts */
		add_action( 'init', array( &$this, 'wck_fep_lilo_register_script' ), 12 );
		/* print scripts */
		add_action( 'wp_footer', array( &$this, 'wck_fep_lilo_print_script' ) );
		/* Set up ajax hooks */
		add_action( 'wp_ajax_wck_fep_register_user', array( &$this, 'wck_fep_register_user' ) );
		add_action( 'wp_ajax_nopriv_wck_fep_register_user', array( &$this, 'wck_fep_register_user' ) );
		
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		self::$wck_fep_lilo_presence = true;
		
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
		/* get login logout form */
		$lilo_form = wck_fep_output_lilo_form();		
		echo $lilo_form;
		
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Login Widget', 'wck' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wck' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
	
	/**
	 * Register Scripts
	 */
	static function wck_fep_lilo_register_script(){
		wp_register_script( 'wck-fep-lilo-js', plugins_url('/wordpress-creation-kit-api/wck-fep/wck-fep.js', __FILE__), array( 'jquery' ), '1.0' );
		wp_register_style( 'wck-fep-lilo-css', plugins_url('/wordpress-creation-kit-api/wck-fep/wck-fep.css', __FILE__) );
	}
	
	/**
	 * Print Scripts only if $wck_fep_lilo_presence true
	 */
	static function wck_fep_lilo_print_script(){
		if( ! self::$wck_fep_lilo_presence )
			return;
		
		wp_print_scripts( 'wck-fep-lilo-js' );
		wp_print_styles( 'wck-fep-lilo-css' );
	}
	
	/**
	 * AJAX callback for registering
	 */
	static function wck_fep_register_user(){
		wck_fep_handle_user_action();
	}
	
}
/* register the widget */
add_action( 'widgets_init', create_function( '', 'register_widget( "wck_fep_login_logout" );' ) );


/**
 * Login/ Logout shortcode Class
 */
class Fep_Lilo_Shortcode {
	static $add_script;
 
	static function init() {
		/* create the shortcode */
		add_shortcode('fep-lilo', array(__CLASS__, 'handle_shortcode'));
		/* register scripts */
		add_action('init', array(__CLASS__, 'register_script'));
		/* print scripts */
		add_action('wp_footer', array(__CLASS__, 'print_script'));
		/* set up ajax hooks */
		add_action( 'wp_ajax_wck_fep_register_user', array( 'Fep_Lilo_Shortcode', 'wck_fep_register_user' ) );
		add_action( 'wp_ajax_nopriv_wck_fep_register_user', array( 'Fep_Lilo_Shortcode', 'wck_fep_register_user' ) );
	}
	
	/**
	 * Shortcode Function
	 */ 
	static function handle_shortcode($atts) {
		self::$add_script = true;
		/* output login logout form */
		$lilo_form = wck_fep_output_lilo_form();
		return $lilo_form;
	}
	
	/**
	 * Register Scripts
	 */ 
	static function register_script() {
		wp_register_script( 'wck-fep-lilo-js', plugins_url('/wordpress-creation-kit-api/wck-fep/wck-fep.js', __FILE__), array( 'jquery' ), '1.0' );
	}
	
	/**
	 * Print scripts only if $add_script is true
	 */
	static function print_script() {
		if ( ! self::$add_script )
			return;
 
		wp_print_scripts('wck-fep-lilo-js');
	}
}
/**
 * Initialize the shortcode
 */ 
Fep_Lilo_Shortcode::init();

/* Filter post update message */
add_filter( 'post_updated_messages', 'wck_fep_filter_post_update_message' );
function wck_fep_filter_post_update_message($messages){
	$messages['wck-frontend-posting'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __('Frontend Posting form updated.', 'wck')
	);
	return $messages;
}
?>