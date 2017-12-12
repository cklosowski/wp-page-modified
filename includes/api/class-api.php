<?php

/**
 * Class Page_Modified_API
 *
 * @since 1.0
 */
class Page_Modified_API {

	private static $api_key;
	private static $use_cache = true;
	private static $api_url   = 'https://app.pagemodified.com/api/v1/';

	public function __construct() {
		self::$api_key = get_option( 'page_modified_api_key' );
	}

	/**
	 * Process an API call to Page Modified
	 *
	 * @since 1.0
	 *
	 * @param $endpoint
	 *
	 * @return array|mixed|object|WP_Error
	 */
	protected function request( $endpoint ) {

		if ( empty( self::$api_key ) ) {
			return array( 'error' => true, 'message' => 'Missing API Key' );
		}

		$request_args = array(
			'httpversion' => '1.1',
			'user-agent'  => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url(),
			'blocking'    => true,
			'headers'     => array(
				'API-Key'      => self::$api_key,
				'Content-Type' => 'application/json',
			),
			'sslverify'   => true,
		);

		// Check if we have the item cached.
		$cache_key = 'pm_api_' . md5( $endpoint );
		$return    = get_transient( $cache_key );
		if ( self::$use_cache && false !== $return ) {
			return $return;
		}

		$request_url = self::$api_url . $endpoint;
		$request     = wp_remote_get( $request_url, $request_args );

		$response_code = wp_remote_retrieve_response_code( $request );
		if ( is_wp_error( $request ) ) {
			$return = $request;
		} elseif ( 200 !== $response_code ) {
			$return = array( 'error' => true, 'message' => wp_remote_retrieve_body( $request ) );
		} else {
			$return = json_decode( wp_remote_retrieve_body( $request ) );
			if ( property_exists( $return, 'data' ) ) {
				$return = $return->data;
			}
			set_transient( $cache_key, $return, HOUR_IN_SECONDS );
		}

		return $return;
	}

}
