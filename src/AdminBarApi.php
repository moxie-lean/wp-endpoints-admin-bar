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
	 * Cookie name
	 *
	 * @var String
	 */
	protected $cookie_name = 'wp_admin_bar_user';

	/**
	 * Constructor
	 */
	function __construct() {
		add_action( 'wp_login', [ $this, 'login_action' ] );
		add_action( 'wp_logout', [ $this, 'logout_action' ] );
	}

	/**
	 * Login action listener
	 */
	public function login_action( $user_login ) {
		$admin_bar_user = '';
		$parsed = wp_parse_url( home_url() );

		if ( isset( $_COOKIE[ $this->cookie_name ] ) ) { // Input var okay.
			$admin_bar_user = sanitize_text_field( wp_unslash( $_COOKIE[ $this->cookie_name ] ) ); // Input var okay.
		}

		if ( $admin_bar_user !== $user_login ) {
			setcookie( $this->cookie_name, $user_login, 0, '/', '.' . $parsed['host'] );
		}
	}

	/**
	 * Logout action listener
	 */
	public function logout_action() {
		$parsed = wp_parse_url( home_url() );

		setcookie( $this->cookie_name, '', strtotime( '-1 day' ), '/', '.' . $parsed['host'] );
	}

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
		$edit_url = '';
		$admin_bar_user = '';
		$user_id = 0;
		$user = false;

		if ( isset( $_COOKIE[ $this->cookie_name ] ) ) { // Input var okay.
			$admin_bar_user = sanitize_text_field( wp_unslash( $_COOKIE[ $this->cookie_name ] ) ); // Input var okay.
		}

		if ( '' !== $admin_bar_user ) {
			$user = get_userdatabylogin( $admin_bar_user );

			if ( $user ) {
				$user_id = $user->ID;
			}
		}

		if ( 0 !== $user_id && user_can( $user_id, 'edit_posts' ) ) {
			$edit_url = $admin_url . 'post.php?action=edit&post=';
		}

		$data = [
			'site_name' => get_bloginfo( 'name' ),
			'user_name' => $admin_bar_user,
			'dashboard_url' => $admin_url,
			'logout_url' => wp_logout_url(),
			'edit_page_url' => $edit_url,
			'post_types' => $this->post_types( $admin_url, $user_id ),
			'nicename' => $user ? $user->user_nicename : '',
			'display_name' => $user ? $user->display_name : '',
			'first_name' => $user_id ? get_user_meta( $user_id, 'first_name', true ) : '',
			'last_name' => $user_id ? get_user_meta( $user_id, 'last_name', true ) : '',
		];

		return $this->filter_data( $data );
	}

	/**
	 * Get available post types
	 */
	private function post_types( $admin_url, $user_id ) {
		$post_types = get_post_types( [], 'objects' );
		$post_types_data = [];

		foreach ( $post_types as $type ) {
			if ( $type->show_in_menu && user_can( $user_id, $type->cap->create_posts ) ) {
				$post_types_data[] = [
					'name' => $type->labels->singular_name,
					'url' => $admin_url . 'post-new.php?post_type=' . $type->name,
				];
			}
		}

		return $post_types_data;
	}
}
