jQuery(function(){
	jQuery( '#wck_fep_fields #field-type' ).change(function () {
		value = jQuery(this).val();
		
		defaultFields = new Array( "Post Title", "Post Content", 'Post Excerpt', "Featured Image" );
		
		
		if( jQuery.inArray( value, defaultFields ) > -1 || value.indexOf('Taxonomy: ') != -1 ){
			jQuery( '#wck_fep_fields .row-required' ).show();
		}
		else{
			jQuery( '#wck_fep_fields .row-required' ).hide();
		}
	});
	
	jQuery(document).on( 'click', '#wck_fep_args input[name="wck_fep_args_anonymous-posting"], .update_container_wck_fep_args input[name="wck_fep_args_anonymous-posting"]', function () {
		value = jQuery(this).val();
		if( value == 'yes' ){
			jQuery( '.row-assign-to-user', jQuery(this).parent().parent().parent().parent().parent().parent() ).show();
		}
		else if( value == 'no' ){
			jQuery( '.row-assign-to-user', jQuery(this).parent().parent().parent().parent().parent().parent() ).hide();
		}
	});
});