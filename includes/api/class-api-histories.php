<?php

/**
 * Class Page_Modified_API_Histories
 *
 * @since 1.0
 */
class Page_Modified_API_Histories extends Page_Modified_API {

	private static $domain_id;

	/**
	 * Set the domain ID to get history for.
	 *
	 * @since 1.0
	 *
	 * @param $domain_id
	 */
	public function set_domain( $domain_id ) {
		self::$domain_id = $domain_id;
	}

	/**
	 * Get a list of histories for the set domain.
	 *
	 * @since 1.0
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get_list() {
		return $this->request( 'domain/' . self::$domain_id . '/histories' );
	}

	/**
	 * Get the last completed crawl for a domain.
	 *
	 * @since 1.0
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function last() {
		$history    = $this->get_list();
		$last_crawl = false;

		foreach ( $history as $key => $crawl ) {
			if ( $crawl->stop_message === null ) {
				continue;
			}

			$last_crawl = $crawl;
			break;
		}
		return $this->get( $last_crawl->id );
	}

	/**
	 * Get a specific history for the set domain.
	 *
	 * @since 1.0
	 *
	 * @param $id
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get( $id ) {
		return $this->request( 'domain/' . self::$domain_id . '/histories/' . $id );
	}

}
