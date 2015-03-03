<?php
// create custom plugin settings menu
add_action('admin_menu', 'tcp_create_menu');

function tcp_create_menu() {
	add_options_page('Add Tags And Category', 'Add Tags And Category', 'administrator', __FILE__, 'tcp_settings_page');
}

function tcp_settings_page() {
	include('includes/tcp_header.php');
	include('includes/tcp_post_type_select.php');
	include('includes/tcp_footer.php');
}