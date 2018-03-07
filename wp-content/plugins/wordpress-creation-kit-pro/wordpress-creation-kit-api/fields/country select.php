<?php 
/* @param string $meta Meta name.	 
 * @param array $details Contains the details for the field.	 
 * @param string $value Contains input value;
 * @param string $context Context where the function is used. Depending on it some actions are preformed.;
 * @return string $element input element html string. */

$extra_attr = apply_filters( 'wck_extra_field_attributes', '', $details, $meta );

require_once( plugin_dir_path(__FILE__) . '../assets/country/country-select.php' );
$country_list = apply_filters( 'wck-country-list', wck_country_list() );
$element .= '<select name="'. $single_prefix . esc_attr( Wordpress_Creation_Kit::wck_generate_slug( $details['title'], $details ) ) .'"  id="';
if( !empty( $frontend_prefix ) )
	$element .=	$frontend_prefix;  
$element .= esc_attr( Wordpress_Creation_Kit::wck_generate_slug( $details['title'], $details ) ) .'" class="mb-country-select mb-field" '.$extra_attr.'>';
$element .= '<option value="">'. __('...Choose', 'wck') .'</option>';
if( !empty( $country_list ) ){					
	foreach( $country_list as $option ){							
		$element .= '<option value="'. esc_attr( $option ) .'"  '. selected( $option, $value, false ) .' >'. esc_html( $option ) .'</option>';					
	}
}			
$element .= '</select>';		
?>