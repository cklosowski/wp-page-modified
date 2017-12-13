<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WP_Page_Modified_Settings
 *
 * Registers the settings for Page Modified, and the interface for saving settings.
 *
 * @since 1.0
 */
class WP_Page_Modified_Settings {

	private static $instance;

	private function __construct() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->hooks();
	}

	static public function instance() {
		if ( !self::$instance ) {
			self::$instance = new WP_Page_Modified_Settings();
		}

		return self::$instance;
	}

	/**
	 * Setup any hooks into WordPress we need
	 *
	 * @since 1.0
	 */
	private function hooks() {
		add_filter( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'admin_menu', array( $this, 'register_menu' ) );
	}

	/**
	 * Register Page Modified settings, settings section, and settings fields
	 *
	 * @since 1.0
	 */
	public function register_settings() {
		register_setting( 'page_modified_options', 'page_modified_api_key', 'sanitize_text_field' );
		register_setting( 'page_modified_options', 'page_modified_domain_id', 'intval' );

		add_settings_section(
			'page_modified_page_modified_options_section',
			'',
			array( $this, 'settings_section_callback' ),
			'page_modified_options'
		);

		add_settings_field(
			'page_modified_api_key',
			__( 'Page Modified API Key', 'wp-page-modified' ),
			array( $this, 'api_key_setting_render' ),
			'page_modified_options',
			'page_modified_page_modified_options_section'
		);

		add_settings_field(
			'page_modified_domain_id',
			__( 'Select a Domain', 'wp-page-modified' ),
			array( $this, 'domain_id_setting_render' ),
			'page_modified_options',
			'page_modified_page_modified_options_section'
		);

	}

	/**
	 * Register the Page Modified setting page in the 'Settings' menu.
	 *
	 * @since 1.0
	 */
	public function register_menu() {
		add_options_page( __( 'Page Modified', 'wp-page-modified' ), __( 'Page Modified', 'wp-page-modified' ), 'manage_options', 'page-modified', array( $this, 'render_settings' ) );
	}

	/**
	 * Render the API Key setting field
	 *
	 * @since 1.0
	 */
	public function api_key_setting_render() {
		$api_key = get_option( 'page_modified_api_key' );
		?>
		<input type="password" class="page-modified-api-key-input" name="page_modified_api_key" value="<?php echo $api_key; ?>" placeholder="<?php _e( 'Enter your API Key', 'wp-page-modified' ); ?>" />
		&nbsp;
		<span onClick="jQuery('.page-modified-key-toggle').toggle(); jQuery('.page-modified-api-key-input').attr('type', 'text');" class="page-modified-key-toggle dashicons dashicons-visibility"></span>
		<span onclick="jQuery('.page-modified-key-toggle').toggle(); jQuery('.page-modified-api-key-input').attr('type', 'password');" class="page-modified-key-toggle dashicons dashicons-hidden" style="display:none"></span>
		<?php
	}

	/**
	 * Render the Domain ID setting field
	 *
	 * @since 1.0
	 */
	public function domain_id_setting_render() {
		$api_key   = get_option( 'page_modified_api_key' );

		if ( empty( $api_key ) ) {
			return;
		}

		$domain_id = get_option( 'page_modified_domain_id' );
		$domains   = wp_page_modified()->domains->get_list();
		if ( is_array( $domains ) ) {
			?>
			<select name="page_modified_domain_id">
				<?php foreach ( $domains as $domain ) : ?>
				<option value="<?php echo $domain->id; ?>" <?php selected( $domain_id, $domain->id ); ?>>
					<?php echo $domain->url . ' (' . $domain->user_agent . ')'; ?>
				</option>
				<?php endforeach; ?>
			</select>
			<?php
		}
	}

	/**
	 * Output the description of our settings section
	 *
	 * @since 1.0
	 */
	public function settings_section_callback() {
		echo __( 'Configure your Page Modified API and domain to display crawl information.', 'wp-page-modified' );
	}

	/**
	 * Render the settings page, complete with nonces and submit buttons.
	 *
	 * @since 1.0
	 */
	public function render_settings() {
		?>
		<form action='options.php' method='post'>

			<h2><?php _e( 'Page Modified', 'wp-page-modified' ); ?></h2>

			<?php
			settings_fields( 'page_modified_options' );
			do_settings_sections( 'page_modified_options' );
			submit_button();
			?>

		</form>
		<?php
	}

}

WP_Page_Modified_Settings::instance();
