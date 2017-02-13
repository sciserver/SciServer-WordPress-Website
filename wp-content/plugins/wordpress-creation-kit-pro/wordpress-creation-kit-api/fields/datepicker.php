<?php
/* @param string $meta Meta name.
* @param array $details Contains the details for the field.
* @param string $value Contains input value;
* @param string $context Context where the function is used. Depending on it some actions are preformed.;
* @return string $element input element html string. */

$date_format = 'dd-mm-yy';
if( !empty( $details['date-format'] ) )
    $date_format = trim( $details['date-format'] );

$element .= '<input type="text" name="'. $single_prefix . esc_attr( Wordpress_Creation_Kit::wck_generate_slug( $details['title'], $details ) ) .'" value="'. esc_attr( $value ) .'" class="mb-datepicker mb-field"/>';
$element .= '<script type="text/javascript">jQuery( function(){jQuery(".mb-datepicker").datepicker({'. apply_filters('wck-datepicker-args', 'dateFormat : "'. $date_format .'",changeMonth: true,changeYear: true') .'})});</script>';
?>