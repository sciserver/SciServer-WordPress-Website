jQuery(function(){
	jQuery(document).on( 'change', '#wck_opc_fields #field-type', function () {
		value = jQuery(this).val();
		
		if( value == 'select' || value == 'checkbox' || value == 'radio' ){
			jQuery( '#wck_opc_fields .row-options' ).show();
		}
		else{
			jQuery( '#wck_opc_fields .row-options' ).hide();
		}

		if( value == 'cpt select' ){
			jQuery( '#wck_opc_fields .row-cpt' ).show();
		}
		else{
			jQuery( '#wck_opc_fields .row-cpt' ).hide();
		}	
		
	});
	
	jQuery(document).on( 'change', '#container_wck_opc_fields #field-type', function () {
		value = jQuery(this).val();

		if( value == 'select' || value == 'checkbox' || value == 'radio' ){
			jQuery(this).parent().parent().parent().children(".row-options").show();
		}
		else{
			jQuery(this).parent().parent().parent().children(".row-options").hide();
		}
	
		if( value == 'cpt select' ){
			jQuery(this).parent().parent().parent().children(".row-cpt").show();
		}
		else{
			jQuery(this).parent().parent().parent().children(".row-cpt").hide();
		}		

		
	});
});