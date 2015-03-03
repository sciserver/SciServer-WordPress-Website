<?php
// Include Mustache Templates 
require_once ('Mustache/Autoloader.php');
Mustache_Autoloader::register();

function wck_stp_get_image_metadata_types() {
    return array( 'title', 'caption', 'alt', 'description' );
}


// Setup "Available Tags" in the backend.
add_action('wck_stp_meta_box_after_element_wck_stp_template_all', 'wck_stp_list_available_vars');
add_action('wck_stp_meta_box_after_element_wck_stp_template_single', 'wck_stp_list_available_vars');
add_action('wck_stp_meta_box_after_element_wck_stp_singlepost_template', 'wck_stp_list_available_vars');
function wck_stp_list_available_vars(){
	add_thickbox();
	?>
	<div class="stp-extra">
	<h4>Available Variables (also see <a href="<?php echo plugins_url( 'wck-stp/wck_stp_template_docs.html' , dirname(__FILE__) ) ?>?TB_iframe=true&width=600&height=550" class="thickbox">the documentation</a>)</h4>
	<?php
	$template_id = (isset($_GET['post'])) ? $_GET['post'] : $template_id = false;
	$mustache_available_keys = array();

	$cpt_arg = false;

	if ($template_id && ( get_post_type($template_id) == 'wck-swift-template' )){
		$cpt_arg = get_post_meta($template_id, "wck_stp_args_cpt", true);
	} elseif ( $template_id ) {
		$cpt_arg = get_post_type($template_id);
	}

	if( $cpt_arg != '' || $cpt_arg != false ) {
		$mustache_available_keys = wck_stp_mustache_tags_array( $cpt_arg, $template_id );
	}
	?>
	<pre><?php
	if ( current_filter() == 'wck_stp_meta_box_after_element_wck_stp_template_all' ){
		echo "{{#posts}}" . PHP_EOL . wck_stp_generate_mustache_variables_rec($mustache_available_keys) . "{{/posts}}" . PHP_EOL;
		echo "{{{pagination}}}";
	} else {
		echo wck_stp_generate_mustache_variables_rec($mustache_available_keys, -1) ;
	}
    ?>
	</pre>

        <div id="wck-stp-featured-image-available-sizes" class="stp-modal-box">
            <h3><?php _e( 'Available image sizes for the featured image', 'wck' ); ?></h3>

            <pre><?php
                $mustache_default_image_size_vars = array();
                foreach( get_intermediate_image_sizes() as $image_size ) {
                    $mustache_default_image_size_vars = $mustache_default_image_size_vars + array( '{featured_image_' . $image_size . '}' => '' );
                }

                echo wck_stp_generate_mustache_variables_rec( $mustache_default_image_size_vars, -1 );
                ?>
            </pre>

            <h3><?php _e( 'Available meta-data for the featured image', 'wck' ); ?></h3>

            <pre><?php
                $mustache_default_image_metadata_vars = array();

                foreach( wck_stp_get_image_metadata_types() as $metadata ) {
                    $mustache_default_image_metadata_vars = $mustache_default_image_metadata_vars + array( '{featured_image_' . $metadata . '}' => '' );
                }

                echo wck_stp_generate_mustache_variables_rec( $mustache_default_image_metadata_vars, -1 );
            ?>
            </pre>

        </div>

        <?php
            $custom_fields_array = wck_stp_generate_custom_fields_keys( $cpt_arg, $template_id );

            $custom_fields = array();
            foreach( $custom_fields_array as $key => $custom_field ) {
                if ( is_array( $custom_field ) ) {
                    foreach( $custom_field as $field_key => $field ) {
                        $custom_fields[ $field_key ] = $field;
                    }
                } else {
                    $custom_fields[ $key ] = $custom_field;
                }
            }
        ?>
        <?php foreach( $custom_fields as $custom_field => $custom_field_type ): ?>
            <?php if( $custom_field_type == 'upload' ): ?>
                <div id="<?php echo 'wck-stp-' . Wordpress_Creation_Kit::wck_generate_slug( $custom_field ); ?>" class="stp-modal-box">
                    <h3><?php printf( __( 'Available image sizes for %1$s', 'wck' ), $custom_field ); ?></h3>

                    <pre><?php
                        $mustache_custom_fields_upload_size_vars = array();
                        foreach( get_intermediate_image_sizes() as $key => $image_size ) {
                            $mustache_custom_fields_upload_size_vars[ $custom_field . '_' . $image_size ] = '';
                        }
                        unset( $custom_fields[$custom_field] );

                        echo wck_stp_generate_mustache_variables_rec( $mustache_custom_fields_upload_size_vars, -1 );
                    ?>
                    </pre>

                    <h3><?php printf( __( 'Available meta-data for %1$s', 'wck' ), $custom_field ); ?></h3>

                    <pre><?php
                        $mustache_custom_fields_upload_metadata_vars = array();

                        foreach( wck_stp_get_image_metadata_types() as $metadata ) {
                            $mustache_custom_fields_upload_metadata_vars[ '{' . $custom_field . '_' . $metadata . '}' ] = '';
                        }

                        echo wck_stp_generate_mustache_variables_rec( $mustache_custom_fields_upload_metadata_vars, -1 );
                        ?>
                    </pre>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

	</div>
<?php
}

// Recursive function to generate available tags based on context 
function wck_stp_mustache_tags_array($cpt_name, $post_id, $level = 0){
	if ( $level > 1 ) { return; }
	$level++;
	
	$mustache_default_keys = wck_stp_populate_default_template_vars( '', '', $cpt_name );
	$mustache_custom_field_keys = wck_stp_generate_custom_fields_keys($cpt_name, $post_id, $level);
	$mustache_available_taxonomies = wck_stp_generate_term_keys( $cpt_name );
	$mustache_available_keys = array_merge( $mustache_default_keys, $mustache_custom_field_keys, $mustache_available_taxonomies) ;	
	
	return $mustache_available_keys;
}

// Default Post Tags. They also get prepopulated if $post && $post_author is passed 
function wck_stp_populate_default_template_vars( $post, $post_author, $post_type_of_template = '' ){
	
	/* this generates the tags for the mustache variables */
	$mustache_default_vars = array(
		'post_id' 			=> '',
		'post_title' 		=> '',
		'post_name' 		=> '',
		'post_excerpt' 		=> '',
		'post_date'	 		=> '',
		'post_author'	 	=> '',
		'post_permalink'	=> '',
		'swift_permalink'	=> '',
	);
	
	if( !empty( $post_type_of_template ) ){
		if( post_type_supports( $post_type_of_template, 'thumbnail' ) ){
			$mustache_default_vars = array( '{featured_image}' => '' ) + $mustache_default_vars;
		}		
		if( post_type_supports( $post_type_of_template, 'editor' ) ){
			$mustache_default_vars = array( '{post_content}' => '' ) + $mustache_default_vars;
		}
	}
	
	
	if ( is_object($post) && is_object($post_author) ){
		unset( $mustache_default_vars['{featured_image}'] );
		unset( $mustache_default_vars['{post_content}'] );

        /* for drafts */
        if( is_preview() && isset( $_GET['p'] ) )
            $post = get_post( $_GET['p'] );

        /* for post preview */
        if ( is_preview() && isset($_GET['preview_id']) && isset($_GET['preview_nonce']) )
            $post = _set_preview($post);

		$mustache_default_vars['post_id'] = $post->ID;
		$mustache_default_vars['post_title'] = $post->post_title;
		$mustache_default_vars['post_name'] = $post->post_name;
		$mustache_default_vars['post_content'] = wpautop( $post->post_content );
		if( $post->post_excerpt == '' ){
			$excerpt = $post->post_content;
			$excerpt = strip_shortcodes( $excerpt );
			
			// we're not applying filters on the content or we're create an infinite loop with Swift Templates
			//$excerpt = apply_filters( 'the_content', $text );
			
			$excerpt = str_replace( ']]>', ']]>', $excerpt );

			$excerpt_length = apply_filters( 'excerpt_length', 55 );
			$excerpt_more   = apply_filters( 'excerpt_more', ' ' . '[...]' );
			$excerpt        = wp_trim_words( $excerpt, $excerpt_length, '' );
			$mustache_default_vars['post_excerpt'] = $excerpt . $excerpt_more;
		}
		else
			$mustache_default_vars['post_excerpt'] = $post->post_excerpt;
		$mustache_default_vars['post_date'] = $post->post_date;
		$mustache_default_vars['post_author'] = $post_author->display_name;

        /* Featured image sizes */
        $mustache_default_vars['featured_image'] = get_the_post_thumbnail( $post->ID );
        foreach( get_intermediate_image_sizes() as $image_size ) {
            $mustache_default_vars['featured_image_' . $image_size] = get_the_post_thumbnail( $post->ID, $image_size );
        }

        /* Featured image metadata */
        $post_thumbnail = get_post( get_post_thumbnail_id( $post->ID ) );
        $mustache_default_vars['featured_image_title'] = $post_thumbnail->post_title;
        $mustache_default_vars['featured_image_caption'] = $post_thumbnail->post_excerpt;
        $mustache_default_vars['featured_image_alt_text'] = get_post_meta($post_thumbnail->ID, '_wp_attachment_image_alt', true);
        $mustache_default_vars['featured_image_description'] = $post_thumbnail->post_content;

		$mustache_default_vars['post_permalink'] = get_permalink( $post->ID );
		global $wp_rewrite;
		if( get_permalink() == get_permalink( $post->ID ) ){
			$mustache_default_vars['swift_permalink'] = get_permalink( $post->ID );
		}
		else {
			if ($wp_rewrite->permalink_structure != ''){					
				$mustache_default_vars['swift_permalink'] = get_permalink( ) . 's/' . $post->post_name ;
			} else {
				$mustache_default_vars['swift_permalink'] = wck_stp_post_permalink( $post->post_name );
			}
		}
	}
	
	return $mustache_default_vars;
}

// Generates available tags for custom fields based on context 
function wck_stp_generate_custom_fields_keys($custom_post_type, $post_id, $level = 0){
	
	$page_template = get_post_meta( $post_id, '_wp_page_template', true);
	
	$mustache_vars_cfc = array();
	if( function_exists( 'wck_cfc_create_boxes_args' ) ){
		$all_cfc = wck_cfc_create_boxes_args();
		(!empty( $all_cfc )) ? $all_box_args = $all_cfc : $all_box_args = array();
		
		foreach( $all_box_args as $box_args ){
			// Some times we don't have all these keys in our array so we need to initialize them. 
			$expected_keys = array('post_type', 'post_id', 'page_template', 'meta_name');
			foreach( $expected_keys as $expected_key ){
				if ( !array_key_exists( $expected_key, $box_args ) ){
					$box_args[$expected_key] = '';
				}
			}
			
			if( ( $box_args['post_type'] == $custom_post_type && !is_numeric( $box_args['post_id'] ) && is_null( $box_args['page_template'] ) ) || ( $box_args['post_id'] === $post_id ) || ( $box_args['post_type'] == $custom_post_type && $box_args['page_template'] == $page_template ) ) {
				if ( $box_args['single'] ) {
					foreach( $box_args['meta_array'] as $value ){
						$slug = Wordpress_Creation_Kit::wck_generate_slug( $value['title'] );
						$name = $box_args['meta_name']. '_' . $slug;
						$mustache_vars_cfc[ $name ] = apply_filters( 'wck_stp_tagtype_' . Wordpress_Creation_Kit::wck_generate_slug( $value['type'] ), '', $value, $level );
					}
				} else {
					$mustache_vars_cfc[ $box_args['meta_name'] ] = array();
					foreach( $box_args['meta_array'] as $key => $value ){
						$slug = Wordpress_Creation_Kit::wck_generate_slug( $value['title'] );
						$name = $box_args['meta_name'];
						$mustache_vars_cfc[ $box_args['meta_name'] ][$slug] = apply_filters( 'wck_stp_tagtype_' . Wordpress_Creation_Kit::wck_generate_slug( $value['type'] ), '', $value, $level );
					}
				}
			}
		}
	}
	
	return $mustache_vars_cfc;
}

// Generates array with available taxonomies for related taxonomies to a cpt 
function wck_stp_generate_term_keys( $cpt ){
	$taxonomies = get_object_taxonomies( $cpt );
	$available_taxonomies = array();
	if( is_array( $taxonomies ) ){
		foreach ( $taxonomies as $key => $value ){
			$available_taxonomies['taxonomy_' . $value] = array('term_id' => '', 'term_name' => '', 'term_slug' => '', 'term_link' => '', 'term_description' => '');
		}
	}
	
	return $available_taxonomies;
}

// Generates mustaches based on our array while preserving depth 
function wck_stp_generate_mustache_variables_rec($array, $level = 0){
	$level++;
	$tags = '';
	
	(!empty( $array )) ? $array = $array : $array = array();
	foreach ( $array as $key => $value ){
		if ( !is_array( $value ) ){
            if( $key == '{featured_image}' )
                $tags .= str_repeat("&nbsp;&nbsp;", $level) . "{{" . $key . "}}" . " - " . "<a class='stp-view-more-tags' href='wck-stp-featured-image-available-sizes'>" . __( 'View more', 'wck' ) . "</a>" . PHP_EOL;
            elseif( $value == 'upload' )
                $tags .= str_repeat("&nbsp;&nbsp;", $level) . "{{" . $key . "}}" . " - " . "<a class='stp-view-more-tags' href='wck-stp-" . Wordpress_Creation_Kit::wck_generate_slug( $key ) . "'>" . __( 'View more', 'wck' ) . "</a>" . PHP_EOL;
            else
			    $tags .= str_repeat("&nbsp;&nbsp;", $level) . "{{" . $key . "}}" . PHP_EOL;

		} else {
			$tags .= str_repeat("&nbsp;&nbsp;", $level) . "{{#$key}}" . PHP_EOL;
			$tags .= wck_stp_generate_mustache_variables_rec($value, $level);
			$tags .= str_repeat("&nbsp;&nbsp;", $level) . "{{/$key}}" . PHP_EOL;
		}
	}
	return $tags;
}

 
// Initialize Mustache Tags in the front-end.
// generate an array with information that users can use inside Mustache Templates
function wck_stp_generate_mustache_single_array($single, $post_type, $level = 0){
	if ( $level > 1 ) { return ''; }
	$level++;
	
	$args=array(
	  'name' 			=> $single,
	  'post_type' 		=> $post_type,
	  'post_status' 	=> 'publish',
	  'posts_per_page' 	=> 1
	);
	$user_posts = get_posts($args);
	if ( !$user_posts ){
		return;
	}
	$user_post_id = $user_posts[0]->post_author;
	
	$user = get_userdata( $user_post_id );

	//can create the mustache array based on available fields
	$mustache_vars_cfc = array();
	if( function_exists( 'wck_cfc_create_boxes_args' ) ){
		
		$all_cfc = wck_cfc_create_boxes_args();
		(!empty( $all_cfc )) ? $all_box_args = $all_cfc : $all_box_args = array();

		foreach( $all_box_args as $box_args ){
			if( ( $box_args['post_type'] == $post_type ) ){					
				if ( $box_args['single'] ) {
					foreach( $box_args['meta_array'] as $value ){
						$slug = Wordpress_Creation_Kit::wck_generate_slug( $value['title'] );
						$processed = the_cfc_field($box_args['meta_name'], $slug, $user_posts[0]->ID, 0, false);
						$unprocessed = get_cfc_field( $box_args['meta_name'], $slug, $user_posts[0]->ID, 0 );
						$mustache_vars_cfc[ $box_args['meta_name'] . '_' . $slug ] = apply_filters('wck_stp_unprocessed_content_' . Wordpress_Creation_Kit::wck_generate_slug( $value['type'] ), $processed, $unprocessed, $level );

                        /* Check to see if the returned value is the array for an image upload */
                        if( $value['type'] == 'upload' && is_array($unprocessed) && isset( $unprocessed['sizes'] ) ){
                            foreach( get_intermediate_image_sizes() as $image_size ) {
                                $mustache_vars_cfc[ $box_args['meta_name'] . '_' . $slug . '_' .$image_size] = $unprocessed['sizes'][$image_size];
                            }

                            foreach( wck_stp_get_image_metadata_types() as $metadata ) {
                                $mustache_vars_cfc[ $box_args['meta_name'] . '_' . $slug . '_' .$metadata] = $unprocessed[$metadata];
                            }
                        }

					}
				} else {
					foreach( $box_args['meta_array'] as $key => $value ){
						$slug = Wordpress_Creation_Kit::wck_generate_slug( $value['title'] );
						foreach ( get_cfc_meta( $box_args['meta_name'], $user_posts[0]->ID ) as $mkey => $mvalue ){
							$processed = the_cfc_field($box_args['meta_name'], $slug, $user_posts[0]->ID, $mkey, false);							
							$unprocessed = get_cfc_field( $box_args['meta_name'], $slug, $user_posts[0]->ID, $mkey );							
							$mustache_vars_cfc[ $box_args['meta_name'] ][$mkey][$slug] = apply_filters('wck_stp_unprocessed_content_' . Wordpress_Creation_Kit::wck_generate_slug( $value['type'] ), $processed, $unprocessed, $level );

                            if( $value['type'] == 'upload' && is_array($unprocessed) && isset( $unprocessed['sizes'] ) ) {
                                foreach (get_intermediate_image_sizes() as $image_size) {
                                    $mustache_vars_cfc[$box_args['meta_name']][$mkey][$slug . '_' . $image_size] = $unprocessed['sizes'][$image_size];
                                }
                            }
						}
						/* for cpt select in the second level we need to initailize with an empty array the metabox even if we don't have values. 
						When we have the same cpt to same cpt relationship and in the "son" post the metabox is empty mustache will show the value from the parent so we need to avoid this */
						if( $level == 2 && get_cfc_meta( $box_args['meta_name'], $user_posts[0]->ID ) == array() ){
							$mustache_vars_cfc[ $box_args['meta_name'] ] = array();
						}
					}
				}
			}
		}
	}	
	
	$object_terms = array();
	$object_taxonomies = get_object_taxonomies( $post_type );
	
	if ( is_array ( $object_taxonomies ) ) {
		foreach ( $object_taxonomies as $key => $object_tax ) {
			$wp_object_terms = wp_get_object_terms($user_posts[0]->ID, $object_tax);
			if( !is_wp_error( $wp_object_terms ) ){
				$j = 0;
				foreach( $wp_object_terms as $term_key => $term_value ){
					$object_terms['taxonomy_' . $object_tax][$j]['term_id'] = $term_value->term_id;
					$object_terms['taxonomy_' . $object_tax][$j]['term_name'] = $term_value->name;
					$object_terms['taxonomy_' . $object_tax][$j]['term_slug'] = $term_value->slug;
					
					$term_link = get_term_link( intval($term_value->term_id), $object_tax );
					if( !is_wp_error( $term_link ) ){
						$object_terms['taxonomy_' . $object_tax][$j]['term_link'] = $term_link;
					} else {
						$object_terms['taxonomy_' . $object_tax][$j]['term_link'] = '';
					}
					$object_terms['taxonomy_' . $object_tax][$j]['term_description'] = $term_value->description;
					$j++;
				}
				
			}
		}
	}
	
	// merge cfc, default fields and taxonomy terms 
	$mustache_vars = wck_stp_populate_default_template_vars($user_posts[0], $user);
	$cfc_and_postdata = array_merge( $mustache_vars, $mustache_vars_cfc, $object_terms) ;	
	
	return $cfc_and_postdata;
}

function wck_stp_post_permalink($slug){
	$post_permalink = wck_stp_cur_page_url();
	$query = parse_url($post_permalink, PHP_URL_QUERY);
	if ($query) {
		$post_permalink .= "&stpsingle=" . $slug;
	} else {
		$post_permalink .= "?stpsingle=" . $slug;
	}
	
	return $post_permalink;
}

function wck_stp_cur_page_url() {
	$pageURL = 'http';
	if ( isset( $_SERVER["HTTPS"] ) ) {
		if ($_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
	}
	
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}


// add support for wp_title for seo purposes 
function wck_stp_seo($title, $sep='|'){
	global $wpdb;
	
	$single = get_query_var('stpsingle');
	$post_title = $wpdb->get_row( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE post_name LIKE %s LIMIT 1", $single ), ARRAY_A );

	if( $single ) {
		return $post_title['post_title'] . ' ' . $sep . ' ' . $title;
	} else {
		return $title;
	}	
}
add_filter('wp_title', 'wck_stp_seo', 20, 2);


// Single post template 
add_action('wck_stp_meta_box_after_element_wck_stp_singlepost_enable', 'wck_stp_singlepost_enable_script'); 
function wck_stp_singlepost_enable_script(){
	?>
	<script>
	jQuery(function(){
		var template_holder = jQuery("#wck_stp_singlepost_template").parent().parent()
		var checkbox = jQuery("#wck_stp_singlepost_enable")
		
		jQuery( template_holder ).hide()
		
		if (jQuery( checkbox ).is(":checked"))
			jQuery( template_holder ).show()
			
		jQuery( checkbox ).change( function(){
			jQuery( template_holder ).toggle(this.checked)
		})
	})
	</script>
	<?php
}

// Process the single post content and output in the front end. 
add_filter("the_content", "wck_stp_render_single_template");
function wck_stp_render_single_template( $content ){
	global $post;
	$id = $post->ID;
	
	$post_type = get_post_type( $id );
	
	$args = array(
		'post_type' 	=> 'wck-swift-template',
		'post_status' 	=> 'publish',
		'posts_per_page'=> 1,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'wck_stp_template_base_single_template',
				'value' => '1',
				'compare' => '='
			),
			array(
				'key' => 'wck_stp_args_cpt',
				'value' => $post_type,
				'compare' => '='
			),
		),
    );		

	$swift_posts = get_posts( $args );
	
	if ( $swift_posts ){
		$render_template_all = get_post_meta( $swift_posts[0]->ID, 'wck_stp_template_base_single_template', true);
		$template_post_type_arg = get_post_meta( $swift_posts[0]->ID, 'wck_stp_args_cpt', true );
	} else {
		$render_template_all = '';
		$template_post_type_arg = '';
	}
	
	$render_template = get_post_meta( $id, 'wck_stp_singlepost_enable', true);
	
	
	if( ($post_type == $template_post_type_arg ) || ($render_template == '1') ){
	
		$template = '';

		if( $render_template == '1' ){
			$template = get_post_meta( $id, 'wck_stp_singlepost_template', true);
		} elseif ( $render_template_all == '1' ){
			$template = get_post_meta( $swift_posts[0]->ID, 'wck_stp_template_single', true);
		}
		
		if ( $template != '') {
			$single = wck_stp_get_slug ( $id );
			$mustache_vars = wck_stp_generate_mustache_single_array($single, $post_type) ;
			
			$m = new Mustache_Engine;
			try {
				$content = '<div class="stp-bubble-wrap stp-single">' . $m->render( $template, $mustache_vars ) . '</div>';
			} catch (Exception $e) {
				$content = $e->getMessage();
			}			
			
			return apply_filters( 'wck_stp_template_content', $content );			
		}
	}
		
	return apply_filters( 'wck_stp_template_content', $content );	
}

// get slug of a post
function wck_stp_get_slug($id) {
	$post_data = get_post($id, ARRAY_A);
	$slug = $post_data['post_name'];
	return $slug; 
}

// add support for going inside a cpt_select and access it's content inside swift templates
add_filter( 'wck_stp_unprocessed_content_cpt-select', 'wck_stp_go_another_level', 10, 3);
function wck_stp_go_another_level( $processed_single, $single, $level ){
	if( $level == 2 )
		return $processed_single;
		
	// get_post_meta returns an array if nothing is found. We need a string.
	if( empty($single) ){
		return '';
	}	
	$reprocess = wck_stp_generate_mustache_single_array( $single->post_name, $single->post_type, $level );
	return $reprocess;
}

// return the custom field type for uploads
add_filter( 'wck_stp_tagtype_upload', 'wck_stp_return_upload_field_type', 10, 2 );
function wck_stp_return_upload_field_type( $string, $value) {
    return $value['type'];
}


add_filter( 'wck_stp_tagtype_cpt-select', 'wck_stp_cptselect_tags', 10, 4 );
function wck_stp_cptselect_tags( $content, $cfc_details, $level ){
	$all_mustache_tags_array = wck_stp_mustache_tags_array( $cfc_details[ 'cpt' ], false, $level );
	return $all_mustache_tags_array;
}
