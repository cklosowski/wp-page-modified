<?php
/*
 Plugin Name: Page Modified
 Plugin URI: https://pagemodified.com
 Description: Get results from your Page Modified crawls in your WordPress admin.
 Author: cklosows
 Version: 1.0.4
 Author URI: https://chrisk.io
 Text Domain: wp-page-modified
 Domain Path: languages
 */

/**
 * Class WP_Page_Modified
 */
class WP_Page_Modified {

	private static $instance;
	public static  $version;
	public static  $plugin_dir;
	public static  $plugin_url;

	public $domains;
	public $active_domain;

	/**
	 * Kick the tires and light the fires.
	 *
	 * @since 1.0
	 * WP_Page_Modified constructor.
	 */
	private function __construct() {
		self::init();
	}

	/**
	 * Load the one true instance of the WP_Page_Modified plugin.
	 *
	 * @since 1.0
	 * @return WP_Page_Modified
	 */
	static public function instance() {

		if ( ! self::$instance ) {
			self::$instance = new WP_Page_Modified();

			self::$instance->domains       = new Page_Modified_API_Domains();
			self::$instance->active_domain = new Page_Modified_Domain( get_option( 'page_modified_domain_id' ) );

		}

		return self::$instance;

	}

	/**
	 * Initialize the plugin and it's configurations.
	 *
	 * @since 1.0
	 */
	private function init() {
		self::constants();
		self::includes();
		self::hooks();
	}

	/**
	 * Define some constants to use in the rest of the plugin.
	 *
	 * @since 1.0
	 */
	private function constants() {
		// Plugin version
		self::$version = '1.0.4';

		// Plugin Folder Path
		self::$plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );

		// Plugin Folder URL
		self::$plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Include the necessary files.
	 *
	 * @since 1.0
	 */
	private function includes() {
		// Include the API endpoints.
		include self::$plugin_dir . 'includes/api/class-api.php';
		include self::$plugin_dir . 'includes/api/class-api-domains.php';
		include self::$plugin_dir . 'includes/api/class-api-histories.php';

		include self::$plugin_dir . 'includes/class-page-modified-domain.php';
		include self::$plugin_dir . 'includes/admin/class-page-modified-settings.php';
		include self::$plugin_dir . 'includes/admin/class-dashboard-widget.php';
	}

	/**
	 * Register any WordPress hooks we'll need to work off of.
	 *
	 * @since 1.0
	 */
	private function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ), 99999999 );
	}

	/**
	 * Register scripts for our CSS and JS
	 *
	 * @since 1.0
	 */
	function scripts() {
		$current_screen = get_current_screen();
		if ( 'dashboard' !== $current_screen->id ) {
			return;
		}

		wp_register_script( 'wppm-chart-js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.min.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'wppm-chart-js' );

		wp_register_style( 'wppm-admin-styles', self::$plugin_url . 'assets/css/admin.css' );
		wp_enqueue_style( 'wppm-admin-styles' );
	}

}

/**
 * Allow accessing the one true instance via a function call.
 *
 * @since 1.0
 * @return WP_Page_Modified
 */
function wp_page_modified() {
	return WP_Page_Modified::instance();
}
add_action( 'plugins_loaded', 'wp_page_modified', PHP_INT_MAX ); // Load as late as possible on plugins_loaded
