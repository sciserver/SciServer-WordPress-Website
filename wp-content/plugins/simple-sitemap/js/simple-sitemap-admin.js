jQuery(document).ready(function( $ ) {

	$('#shortcodes-btn').on('click', function() {
		var isHidden = $('#shortcodes-wrap').is( ":hidden" );
		$('#shortcodes-wrap').toggle( function() {
			if(isHidden) {
				$('#shortcodes-btn').html('Collapse <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-up-alt2"></span>');
			} else {
				$('#shortcodes-btn').html('Expand <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-down-alt2"></span>');
			}
		});
	});

	$('#attributes-btn').on('click', function() {
		var isHidden = $('#attributes-wrap').is( ":hidden" );
		$('#attributes-wrap').toggle( function() {
			if(isHidden) {
				$('#attributes-btn').html('Collapse <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-up-alt2"></span>');
			} else {
				$('#attributes-btn').html('Expand <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-down-alt2"></span>');
			}
		});
	});
});