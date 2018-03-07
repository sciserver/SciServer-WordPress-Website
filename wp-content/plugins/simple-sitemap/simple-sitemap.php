<?php
/*
Plugin Name: Simple Sitemap
Plugin URI: http://wordpress.org/plugins/simple-sitemap/
Description: HTML sitemap to display content as a single linked list of posts, pages, or custom post types. You can even display posts in groups sorted by taxonomy!
Version: 2.4
Author: David Gwyer
Author URI: http://www.wpgoplugins.com
Text Domain: simple-sitemap
*/

/*  Copyright 2009 David Gwyer (email : david@wpgoplugins.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class WPGO_Simple_Sitemap {

	protected $module_roots;

	/* Main class constructor. */
	public function __construct($module_roots) {

		$this->module_roots = $module_roots;

		$this->load_supported_features();
		//add_action( 'plugins_loaded', array( &$this, 'load_supported_features' ), 12 );

		add_action( 'plugins_loaded', array( &$this, 'localize_plugin' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_scripts' ) );
	}

	/* Scripts just for the plugin settings page. */
	public function enqueue_admin_scripts($hook) {
		if($hook != 'settings_page_simple-sitemap/classes/simple-sitemap-settings') {
			return;
		}
		wp_enqueue_style( 'simple-sitemap-css', plugins_url('css/simple-sitemap-admin.css', __FILE__) );
		wp_enqueue_script( 'simple-sitemap-js', plugins_url('js/simple-sitemap-admin.js', __FILE__) );
	}

	/* Check for specific CPT used in the current WPGO theme. */
	public function load_supported_features() {

		$root = $this->module_roots['dir'];

		// [simple-sitemap] shortcode
		require_once( $root . 'classes/shortcodes/simple-sitemap-shortcode.php' );
		new WPGO_Simple_Sitemap_Shortcode($this->module_roots);

		// [simple-sitemap-group] shortcode
		require_once( $root . 'classes/shortcodes/simple-sitemap-group-shortcode.php' );
		new WPGO_Simple_Sitemap_Group_Shortcode($this->module_roots);

		// plugin docs/settings page
		require_once( $root . 'classes/simple-sitemap-settings.php' );
		new WPGO_Simple_Sitemap_Settings($this->module_roots);

		// links on the main plugin index page
		require_once( $root . 'classes/simple-sitemap-links.php' );
		new WPGO_Simple_Sitemap_Links($this->module_roots);
	}

	/**
	 * Add Plugin localization support.
	 */
	public function localize_plugin() {

		load_plugin_textdomain( 'simple-sitemap', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

} /* End class definition */

$module_roots = array(
	'dir' => plugin_dir_path( __FILE__ ),
	'uri' => plugins_url( '', __FILE__ ),
	'file' => __FILE__
);
new WPGO_Simple_Sitemap( $module_roots );