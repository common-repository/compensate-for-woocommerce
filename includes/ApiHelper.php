<?php
namespace CompensateInc;

use CompensateAdmin\OptionsHelper;
use WP_Error;

/**
 * Wrapper of Wordpress API
 */

class ApiHelper {

    public static $api_base = 'https://plugins-api-production.compensate.com/woocommerce';

    /**
     * Wrapper for self::request().
     *
     * @param string $endpoint The helper API endpoint to request.
     * @param array  $args Arguments passed to wp_remote_request().
     *
     * @return array The response object from wp_safe_remote_request().
     */
    public static function get( $endpoint, $args = array() ) {
        $args['method'] = 'GET';
        return self::request( $endpoint, $args );
    }

    /**
     * Wrapper for self::request().
     *
     * @param string $endpoint The helper API endpoint to request.
     * @param array  $args Arguments passed to wp_remote_request().
     *
     * @return array The response object from wp_safe_remote_request().
     */
    public static function post( $endpoint, $args = array() ) {
        $args['method'] = 'POST';

        $args['headers'] = array(
            'content-type'   => 'application/json',
        );

        return self::request( $endpoint, $args );
    }

    /**
     * Wrapper for self::request().
     *
     * @param string $endpoint The helper API endpoint to request.
     * @param array  $args Arguments passed to wp_remote_request().
     *
     * @return array The response object from wp_safe_remote_request().
     */
    public static function delete( $endpoint, $args = array() ) {
        $args['method'] = 'DELETE';

        $args['headers'] = array(
            'content-type'   => 'application/json',
        );

        return self::request( $endpoint, $args );
    }


    /**
     * Perform an HTTP request to the Helper API.
     *
     * @param string $endpoint The endpoint to request.
     * @param array  $args Additional data for the request. Set authenticated to a truthy value to enable auth.
     *
     * @return array|WP_Error The response from wp_safe_remote_request()
     */
    public static function request( $endpoint, $args = array() ) {
        $url = self::url( $endpoint );

        if ( ! empty( $args['authenticated'] ) ) {
            if ( ! self::_authenticate($args ) ) {
                return new WP_Error( 'authentication', 'Authentication failed.' );
            }
        }
        
        return wp_remote_request( $url, $args );
    }

    /**
     * Adds authentication headers to an HTTP request.
     *
     * @param array  $args By-ref, the args that will be passed to wp_remote_request().
     * @return bool Were the headers added?
     */
    private static function _authenticate(&$args ) {
        $auth = OptionsHelper::get( 'auth' );

        if (empty( $auth['access_token'])) {
            return false;
        }


        if ( empty( $args['headers'] ) ) {
            $args['headers'] = array();
        }

        $args['headers']['Authorization'] = 'Bearer ' . $auth['access_token'];


        return true;
    }

    /**
     * Using the API base, form a request URL from a given endpoint.
     *
     * @param string $endpoint The endpoint to request.
     *
     * @return string The absolute endpoint URL.
     */
    public static function url( $endpoint ) {
        $endpoint = ltrim( $endpoint, '/' );
        $endpoint = sprintf( '%s/%s', self::$api_base, $endpoint );
        $endpoint = esc_url_raw( $endpoint );
        return $endpoint;
    }
}