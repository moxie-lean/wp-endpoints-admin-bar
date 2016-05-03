<?php namespace Lean\Endpoints;

use Lean\AbstractEndpoint;

/**
 * Class to provide activation point for our endpoints.
 */
class AdminBarApi extends AbstractEndpoint {

	/**
	 * Endpoint path
	 *
	 * @Override
	 * @var String
	 */
	protected $endpoint = '/admin-bar';

	/**
	 * Get the data.
	 *
	 * @Override
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return array|\WP_Error
	 */
	public function endpoint_callback( \WP_REST_Request $request ) {
		global $wp_admin_bar;

		$current_user = wp_get_current_user();

		$data = [
			'site_name' => get_bloginfo( 'name' ),
			'user_name' => $current_user->user_login,
			'dashboard_url' => admin_url(),
			'logout_url' => wp_logout_url(),
			'edit_page_url' => '',
			'widgets' => $wp_admin_bar->get_node( 'new-content' ),
		]);

		return $this->filter_data( $data );
	}
}
