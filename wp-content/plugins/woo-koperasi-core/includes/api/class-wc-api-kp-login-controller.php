<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_API_KP_Login_Controller extends WP_REST_Controller {
 
    /**
   * Register the routes for the objects of the controller.
   */
  public function register_routes() {
    $version = '1';
    $namespace = 'w-kp/v' . $version;
    $base = 'login';
    register_rest_route( $namespace, '/' . $base, array(
      array(
        'methods'         => 'POST',
        'callback'        => array( $this, 'login' ),
        'args'            => array(
          'username'          => array(
            'required'      => true,
          ),
          'password'          => array(
            'required'      => true,
          ),
        ),
      ),
    ) );
  }
 
  /**
   * Get user data given username and password
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function login( $request ) {
    $creds = array();
    $creds['user_login'] = $request["username"];
    $creds['user_password'] =  $request["password"];
    $creds['remember'] = true;
    $user = wp_signon( $creds, false );
    
    if ( is_wp_error($user) ) {
      //return new WP_Error( 'failed-login', __( 'message', 'Failed to login user'), array( 'status' => 403 ) );
      return $user;
    }
    
    $data = array();
    $data['message'] = 'Login successful';
    $userdata = (array) $user->data;
    unset( $userdata['user_pass'] );
    $user->data = $userdata;
    $data['user'] = $user;
    
    return new WP_REST_Response( $data, 200 );
  }
}
