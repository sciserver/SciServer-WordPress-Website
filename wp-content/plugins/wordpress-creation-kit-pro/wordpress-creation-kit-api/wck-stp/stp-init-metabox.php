<?php
$prefix = 'wck_stp_args_';
$wck_stp_all_post_types = wck_stp_get_all_post_types();
$wck_stp_cpt_select_options = array(
	$prefix.'chose' => array('label' => '...Choose', 'value' => '' ),
);

foreach($wck_stp_all_post_types as $value){
	$wck_stp_cpt_select_options[$value] = array(
		'label' => $value,
		'value' => $value,
	);
}

/*
 * Select the CPT on which our templates will be based on. 
 *
 */ 
 
$fields = array(
	array( // Select box
		'label'	=> 'Custom Post Type ', // <label>
		'desc'	=> 'Select a custom post type that our archive and single templates will be based on.', // description
		'id'	=> $prefix.'cpt', // field id and name
		'type'	=> 'select', // type of field
		'options' => $wck_stp_cpt_select_options,
		),
	);
$sample_box = new wck_stp_custom_add_meta_box( 'wck_stp_basic_args', 'Swift Template Arguments', $fields, 'wck-swift-template', 'high' );

/*
 * Create a repeater with available WP_Query arguments.
 *
 */ 
$available_args = array(
	'posts_per_page' 	=> 'Posts Per Page',
	'order' 			=> 'Order',
	'orderby' 			=> 'Order By',
	'author' 			=> 'Author ID',
	'author_name'		=> 'Author nicename (not name)',
	'cat'				=> 'Category ID',
	'category_name'		=> 'Category Slug',
	'tag_id'			=> 'Tag ID',
	'tag'				=> 'Tag Slug',
	'post_parent'		=> 'Post Parent ID',
	'year'				=> 'Year',
	'monthnum'			=> 'Month',
	'w'					=> 'Week',
	'day'				=> 'Day',
	'hour'				=> 'Hour',
	'minute'			=> 'Minute',
	'second'			=> 'Second',
	'meta_key'			=> 'Meta Key',
	'meta_value'		=> 'Meta Value',
	'meta_value_num'	=> 'Meta Value Numeric',
	'meta_compare'		=> 'Meta Compare',
);
	
$process_args = array();
foreach( $available_args as $key => $value) {
	$process_args[] = "%" . $value . "%" . $key;
}
	
/* set up the fields array */
$stp_box_args_fields = array( 				
	array( 'type' => 'select', 'title' => __( 'Query Arguments', 'wck' ), 'slug' => 'query-arguments', 'options' => $process_args, 'default-option' => true,
	'description' => __( 'Setup query arguments. <a href="'. plugins_url( 'wck-stp/wck_stp_query_docs.html' , dirname(__FILE__) ) . '?TB_iframe=true&width=600&height=550" class="thickbox">see accepted values</a>', 'wck' ), 'required' => true ),
	array( 
		'type' => 'text', 
		'title' => __( 'Value', 'wck' ),
        'slug' => 'value',
		'description' => __( 'The value of the selected query argument. Parameters normaly passed as arrays (some tax args, meta_query, etc) are not supported in the UI. Use the <b>wck_stp_archive_query_args</b> filter for those.  ', 'wck' ), 
		'required' => false ),
);

/* set up the box arguments */
$args = array(
	'metabox_id' => 'wck-stp-query-args',
	'metabox_title' => __( 'Query Arguments', 'wck' ),
	'post_type' => 'wck-swift-template',
	'meta_name' => 'wck_stp_query-args',
	'meta_array' => $stp_box_args_fields,
	'sortable' => true,
	'single' => false,
);
/* create the Form Setup box */
new Wordpress_Creation_Kit( $args );
	
/*
 * Create template textareas
 *
 */ 
$prefix = 'wck_stp_template_';

$fields = array(
	array( // Textarea
		'label'	=> 'Archive Template - Press F11 for full screen, ESC to exit', // <label>
		'desc'	=> '', // description
		'id'	=> $prefix.'all', // field id and name
		'type'	=> 'textarea' // type of field
	),
	array( // Textarea
		'label'	=> 'Single Template - Press F11 for full screen, ESC to exit', // <label>
		'desc'	=> '', // description
		'id'	=> $prefix.'single', // field id and name
		'type'	=> 'textarea' // type of field
	),	
	array( // Checkbox
		'label'	=> 'Use template on all posts', // <label>
		'desc'	=> 'Use this Single Template on all posts for the selected CPT', // description
		'id'	=> $prefix . 'base_single_template', // field id and name
		'type'	=> 'checkbox',
	),
);

/**
 * Instantiate the class with all variables to create a meta box
 * var $id string meta box id
 * var $title string title
 * var $fields array fields
 * var $page string|array post type to add meta box to
 * var $js bool including javascript or not
 */
$sample_box = new wck_stp_custom_add_meta_box( 'wck_stp_templates', 'Swift Templates', $fields, 'wck-swift-template' );

/*
 * Create single template textareas
 *
 */ 
$prefix = 'wck_stp_singlepost_';

$fields = array(
	array( // Checkbox
		'label'	=> 'Use template on this post', // <label>
		'desc'	=> 'Apply the template on this single post only', // description
		'id'	=> $prefix . 'enable', // field id and name
		'type'	=> 'checkbox',
	),
	array( // Textarea
		'label'	=> 'Single Template - Press F11 for full screen, ESC to exit', // <label>
		'desc'	=> '', // description
		'id'	=> $prefix.'template', // field id and name
		'type'	=> 'textarea' // type of field
	),
);

/**
 * Instantiate the class with all variables to create a meta box
 * var $id string meta box id
 * var $title string title
 * var $fields array fields
 * var $page string|array post type to add meta box to
 * var $js bool including javascript or not
 */
if( is_admin() && current_user_can( 'edit_theme_options' ) ){
	foreach ( wck_stp_get_all_post_types() as $available_cpt ) {
		$sample_box = new wck_stp_custom_add_meta_box( 'wck_stp_templates', 'Swift Templates', $fields, $available_cpt );
	}
}


function wck_stp_get_all_post_types(){
	/* get all post types */ 
	$args = array(
			'public'   => true
		);
	$output = 'objects'; // or objects
	$post_types = get_post_types($args,$output);
	$post_type_names = array(); 
	
	$ignored_cpts = array( 'wck-meta-box', 'wck-frontend-posting', 'wck-option-page', 'wck-option-field', 'wck-swift-template');
	$ignored_cpts = apply_filters( 'wck_stp_ignored_cpts', $ignored_cpts );
	
	foreach ($post_types as $post_type ) {
		if ( !in_array( $post_type->name, $ignored_cpts ) ) 
			$post_type_names[] = $post_type->name;
	}
	return $post_type_names;
}