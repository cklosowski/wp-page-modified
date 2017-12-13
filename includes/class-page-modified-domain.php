<?php

/**
 * Class Page_Modified_Domain
 *
 * Access a domain and it's history via this class.
 *
 * @since 1.0
 */
class Page_Modified_Domain {

	private $id;
	public $history;
	public $last_crawl;

	/**
	 * Page_Modified_Domain constructor.
	 *
	 * @since 1.0
	 *
	 * @param $domain_id
	 */
	public function __construct( $domain_id ) {
		if ( empty( $domain_id ) || ! is_numeric( $domain_id ) ) {
			return false;
		}

		$this->id = $domain_id;
		$domain   = wp_page_modified()->domains->get( $domain_id );

		if ( is_array( $domain ) && $domain['error'] ) {
			return false;
		}

		foreach ( get_object_vars( $domain ) as $key => $value ) {
			$this->{$key} = $value;
		}

		$this->history    = new Page_Modified_API_Histories();
		$this->history->set_domain( $this->id );

		$this->last_crawl = $this->last_crawl();
	}

	/**
	 * Get a history, or list of histories for a domain.
	 *
	 * @since 1.0
	 * @param bool $id
	 *
	 * @return array|mixed|object|WP_Error
	 */
	private function get_history( $id = false ) {
		if ( false === $id ) {
			return $this->history->get_list();
		} else {
			return $this->history->get( $id );
		}
	}

	/**
	 * Get the last completed crawl.
	 *
	 * @since 1.0
	 * @return array|mixed|object|WP_Error
	 */
	private function last_crawl() {
		return $this->history->last();
	}

}
