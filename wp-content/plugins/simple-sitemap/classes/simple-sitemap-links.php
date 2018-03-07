<?php
/*
 *	Main WordPress plugin index page links and admin notices
*/

class WPGO_Simple_Sitemap_Links {

	protected $module_roots;

	/* Main class constructor. */
	public function __construct($module_roots) {

		$this->module_roots = $module_roots;

		add_filter( 'plugin_row_meta', array( &$this, 'plugin_action_links' ), 10, 2 );
		add_filter( 'plugin_action_links', array( &$this, 'plugin_settings_link' ), 10, 2 );

		// display dismissible admin notice when user updates plugin to new version
		add_action( 'admin_init', array( &$this, 'register_admin_notice' ) );
		add_action( 'admin_notices', array( &$this, 'admin_notice' ) );

		// redirect user to plugin settings page when plugin activated manually
		register_activation_hook( $this->module_roots['file'], array( $this, 'set_redirect_transient' ) );
		add_action( 'admin_init', array( &$this, 'redirect_settings_page' ) );
	}

	/**
	 * Setup transient for admin notice to be displayed
	 */
	public function register_admin_notice() {

		$plugin_data = get_plugin_data( $this->module_roots['file'], false, false );
		$current_version = $plugin_data['Version'];
		$version = get_option('ss_plugin_version');

		// Perform tasks after plugin updated
		if ($version != $current_version) {
			update_option('ss_plugin_version', $current_version);
			set_transient( 'simple-sitemap-admin-notice', true, 60 );
		}
	}

	/* Admin Notice first time plugin is activated. */
	public function admin_notice(){

		/* Only display admin notice if transient exists */
		if( get_transient( 'simple-sitemap-admin-notice' ) ){
			?>
			<div class="updated notice is-dismissible">
				<p>*NEW* Simple Sitemap live demo gallery added to plugin <a href="<?php echo get_admin_url() . 'options-general.php?page=simple-sitemap/classes/simple-sitemap-settings.php'; ?>"><strong>settings</strong></a> page. What sitemap will you create today?</p>
			</div>
			<?php
			delete_transient( 'simple-sitemap-admin-notice' );
		}
	}

	/* Runs only when the plugin is activated. */
	public function set_redirect_transient() {
		set_transient( 'simple-sitemap-redirect', true, 60 );
	}

	/**
	 * Redirect automatically to plugin settings page
	 */
	public function redirect_settings_page() {
		// only do this if the user can activate plugins
		if ( ! current_user_can( 'manage_options' ) )
			return;

		// don't do anything if the transient isn't set
		if ( ! get_transient( 'simple-sitemap-redirect' ) )
			return;

		delete_transient( 'simple-sitemap-redirect' );
		wp_safe_redirect( admin_url( 'options-general.php?page=simple-sitemap%2Fclasses%2Fsimple-sitemap-settings.php') );
		exit;
	}

	// Display a Settings link on the main Plugins page
	public function plugin_action_links( $links, $file ) {

		//if ( $file == plugin_basename( __FILE__ ) ) {
		// add a link to pro plugin
		//$links[] = '<a style="color:limegreen;" href="https://wpgoplugins.com/plugins/simple-sitemap-pro/" target="_blank" title="Upgrade to Pro - 100% money back guarantee"><span class="dashicons dashicons-awards"></span></a>';
		//}

		return $links;
	}

	// Display a Settings link on the main Plugins page
	public function plugin_settings_link( $links, $file ) {

		if ( $file == 'simple-sitemap/simple-sitemap.php') {
			$pccf_links = '<a href="' . get_admin_url() . 'options-general.php?page=simple-sitemap/classes/simple-sitemap-settings.php">' . __( 'Get Started' ) . '</a>';
			array_unshift( $links, $pccf_links );

		if ( $file == 'simple-sitemap/simple-sitemap.php') {
			$pccf_links = '<a style="color:#60a559;" href="https://wpgoplugins.com/plugins/simple-sitemap-pro/" target="_blank" title="Try Simple Sitemap Pro today for just $29 - 100% money back guarantee"><b>Go Pro</b></a>';
			array_push( $links, $pccf_links );
		}

			//$pccf_links = '<a style="color:#60a559;" href="https://wpgoplugins.com/plugins/simple-sitemap-pro/" target="_blank" title="Try Simple Sitemap Pro today - 100% money back guarantee"><b>Upgrade to Pro</b></a>';
			//array_push( $links, $pccf_links );
		}

		return $links;
	}

} /* End class definition */