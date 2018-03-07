jQuery( function() {
    jQuery(document).ready( function() {
        if( typeof jQuery.ui.dialog != "undefined") {
            jQuery('.stp-modal-box').dialog({
                autoOpen: false,
                modal: true,
                draggable: false,
                minWidth: 450,
                minHeight: 450
            });

            jQuery('.stp-view-more-tags').click(function (e) {
                e.preventDefault();
                jQuery('#' + jQuery(this).attr('href')).dialog('open');
            });
        }
    });
});