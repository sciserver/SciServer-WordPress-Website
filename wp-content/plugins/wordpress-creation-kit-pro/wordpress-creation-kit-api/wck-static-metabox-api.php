<?php
// include non-ajax metabox class
// A fork of https://github.com/pippinsplugins/Reusable-Custom-WordPress-Meta-Boxes

function wck_stp_meta_box_find_field_type( $needle, $haystack ) {
    foreach ( $haystack as $item )
        if ( $item['type'] == $needle )
            return true;
    return false;
}

class wck_stp_custom_add_meta_box {
	
	var $id; // string meta box id
	var $title; // string title
	var $fields; // array fields
	var $page; // string|array post type to add meta box to
	var $priority; // string The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') 
	
    public function __construct( $id, $title, $fields, $page, $priority = "default" ) {
		$this->id = $id;
		$this->title = $title;
		$this->fields = $fields;
		$this->page = $page;
		$this->priority = $priority;
				
		if(!is_array($this->page)) {
			$this->page = array($this->page);
		}

        if( defined( 'DOING_AJAX' ) && DOING_AJAX )
            return;
		
		add_action( 'admin_menu', array( $this, 'add_box' ) );
		add_action( 'save_post',  array( $this, 'save_box' ), 10, 2);
		add_action( 'wp_insert_post',  array( $this, 'save_box' ), 10, 2);
    }
	
	
	function add_box() {
		foreach ( $this->page as $page ) {
			add_meta_box( $this->id, $this->title, array( $this, 'meta_box_callback' ), $page, 'normal', $this->priority );
		}
	}
	
	function meta_box_callback() {
		global $post, $post_type;
		// Use nonce for verification
		echo '<input type="hidden" name="' . $post_type . '_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__) ) . '" />';
		
		// Begin the field table and loop
		echo '<table class="form-table meta_box">';
		foreach ( $this->fields as $field ) {
			
			// get data for this field
			extract( $field );
			if ( !empty( $desc ) )
				$desc = '<span class="description">' . $desc . '</span>';
				
			// get value of this field if it exists for this post
			$meta = get_post_meta( $post->ID, $id, true );
			
			// begin a table row with
			echo '<tr>
					<td class="' . $id . '">
					<label for="' . $id . '">' . esc_html( $label ) . '</label>';
					switch( $type ) {
						// text
						case 'text':
							echo '<input type="text" name="' . $id . '" id="' . $id . '" value="' . esc_attr( $meta ) . '" size="30" />
									<br />' . $desc ;
						break;
						// textarea
						case 'textarea':
							
							echo '<textarea name="' . $id . '" id="' . $id . '" cols="220" rows="4">' . esc_attr( $meta ) . '</textarea>';
							echo  $desc ;
						break;
						// checkbox
						case 'checkbox':
							echo '<input type="checkbox" name="' . $id . '" id="' . $id . '"' . checked( esc_attr( $meta ), true, false ) . ' value="1" />
									<label for="' . $id . '">' . $desc . '</label>';
						break;
						// select
						case 'select':
							echo '<select name="' . $id . '" id="' . $id . '">';
							foreach ( $options as $option )
								echo '<option' . selected( esc_attr( $meta ), $option['value'], false ) . ' value="' . $option['value'] . '">' . esc_html( $option['label'] ) . '</option>';
							echo '</select><br />' . $desc;
						break;
						// radio
						case 'radio':
							foreach ( $options as $option )
								echo '<input type="radio" name="' . $id . '" id="' . $id . '-' . $option['value'] . '" value="' . $option['value'] . '"' . checked( esc_attr( $meta ), $option['value'], false ) . ' />
										<label for="' . $id . '-' . $option['value'] . '">' . $option['label'] . '</label><br />';
							echo '' . esc_html( $desc );
						break;
						// checkbox_group
						case 'checkbox_group':
							foreach ( $options as $option )
								echo '<input type="checkbox" value="' . $option['value'] . '" name="' . $id . '[]" id="' . $id . '-' . $option['value'] . '"' , is_array( $meta ) && in_array( $option['value'], $meta ) ? ' checked="checked"' : '' , ' /> 
										<label for="' . $id . '-' . $option['value'] . '">' . $option['label'] . '</label><br />';
							echo '' . esc_html( $desc );
						break;
					} //end switch
			do_action('wck_stp_meta_box_after_element_' . $id, $id);
			echo '</td></tr>';
		} // end foreach
		echo '</table>'; // end table
	}
	
	// Save the Data
	function save_box( $post_id, $post ) {
		global $post_type;
		
		// verify nonce
		if ( ! ( in_array( $post_type, $this->page ) && @wp_verify_nonce( $_POST[$post_type . '_meta_box_nonce'],  basename( __FILE__ ) ) ) )
			return $post_id;
		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		// check permissions
		if ( !current_user_can( 'edit_page', $post_id ) )
			return $post_id;
		
		// loop through fields and save the data
		foreach ( $this->fields as $field ) {
			if( $field['type'] == 'tax_select' ) {
				// save taxonomies
				if ( isset( $_POST[$field['id']] ) )
					$term = $_POST[$field['id']];
				wp_set_object_terms( $post_id, $term, $field['id'] );
			}
			else {
				// save the rest
				$old = get_post_meta( $post_id, $field['id'], true );
				if ( isset( $_POST[$field['id']] ) ) {
					$new = $_POST[$field['id']];
				} else {
					$new = '';
				}
				
				if ( $new && $new != $old ) {
					if ( is_array( $new ) ) {
						foreach ( $new as &$item ) {
							//$item = esc_attr( $item );
						}
						unset( $item );
					} else {
						//$new = esc_attr( $new );
					}
					update_post_meta( $post_id, $field['id'], $new );
				} elseif ( '' == $new && $old ) {
					delete_post_meta( $post_id, $field['id'], $old );
				}
			}
		} // end foreach
	}
}