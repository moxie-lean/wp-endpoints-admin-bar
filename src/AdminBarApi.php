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
  protected $cookieName = 'wp_admin_bar_user'; 

  /**
   * Cookie path
   *
   * @var String
   */
  protected $cookiePath = ''; 

  /**
   * Cookie domain
   *
   * @var String
   */
  protected $cookieDomain = '';

  /**
   * Constructor
   */
  function __construct() {
    add_action('wp_login', [ $this, 'login_action' ], 10, 1);
    add_action('wp_logout', [ $this, 'logout_action' ]);

    $this->cookiePath = parse_url( get_option( 'siteurl' ), PHP_URL_PATH );
    $this->cookieDomain = '.' . parse_url( get_option( 'siteurl' ), PHP_URL_HOST );
  }

  /**
   * Login action listener
   */
  public function login_action( $user_login ) {
    $adminBarUser = $_COOKIE[$this->cookieName];

    if ( $adminBarUser != $user_login ) {
      setcookie( $this->cookieName, $user_login, strtotime( '+1 day' ), $this->cookiePath, $this->cookieDomain );
    }
  }

  /**
   * Logout action listener
   */
  public function logout_action() {
    setcookie( $this->cookieName, '', strtotime( '-1 day' ), $this->cookiePath, $this->cookieDomain );
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
    $adminBarUser = $_COOKIE[$this->cookieName];
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
      'user_name' => $adminBarUser,
      'dashboard_url' => $admin_url,
      'logout_url' => wp_logout_url(),
      'edit_page_url' => $admin_url . 'post.php?action=edit&post=',
      'post_types' => $post_types_data,
    ];

    return $this->filter_data( $data );
  }
}
