<?php

/**
 * Class Page_Modified_API_Domains
 *
 * @since 1.0
 */
class Page_Modified_API_Domains extends Page_Modified_API {

	/**
	 * List the domains for the current API Key
	 *
	 * @since 1.0
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get_list() {
		return $this->request( 'domain' );
	}

	/**
	 * Get the details for a specific domain ID
	 *
	 * @since 1.0
	 *
	 * @param int $domain_id
	 *
	 * @return array|mixed|object|WP_Error
	 */
	public function get( $domain_id = 0 ) {
		return $this->request( 'domain/' . absint( $domain_id ) );
	}

}
