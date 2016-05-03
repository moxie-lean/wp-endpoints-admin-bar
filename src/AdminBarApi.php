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
		$admin_url = admin_url();
    $post_types = get_post_types( array(), 'objects' );
    $post_types_data = array();

    foreach ($post_types as $type) {
      if ( $type->show_in_menu === true ) {
        $post_types_data[] = array(
          'name' => $type->labels->singular_name,
          'url' => $admin_url . 'post-new.php?post_type=' . $type->name
        );
      }
    }

    $data = [
      'site_name' => get_bloginfo( 'name' ),
      'user_name' => '',
      'dashboard_url' => $admin_url,
      'logout_url' => wp_logout_url(),
      'edit_page_url' => '',
      'post_types' => $post_types_data,
    ];

		return $this->filter_data( $data );
	}
}
