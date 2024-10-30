<?php
namespace CompensateAdmin;
use CompensateAdmin\OptionsHelper;
use CompensateInc\ApiHelper;

/**
 *
 * Handle Admin API
 */

class AdminApiHelper {


    /**
     * Register merchant
     *
     * @param $body
     * @return array|\WP_Error
     */
    public static function register_merchant($body) {
        $request = ApiHelper::post(
            '/install',
            array(
                'body' => json_encode(
                    array(
                        'shopName'   => $body['shop_name'],
                        'shopEmail'  => $body['shop_email'],
                        'shopId'     => $body['shop_id'],
                        'shopUrl'    => $body['shop_url']
                    )
                ),
            )
        );


        if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
            return new \WP_Error( 'compensate_register_merchant', __( 'There was an error registering to Compensate. Please try again.', 'compensate' ), 500 );
        }


        $results = json_decode( wp_remote_retrieve_body( $request ), true );
        if ( ! $results ) {
            return new \WP_Error( 'compensate_register_merchant', __( 'There was an error registering to Compensate. Please try again.', 'compensate' ), 500 );
        }

        //update wordpress database
        $current_auth = OptionsHelper::get('auth');
        OptionsHelper::update(
            'auth',
            wp_parse_args(array(
                'access_token'        => $results['accessToken'],
                'updated'             => time(),
            ), $current_auth)
        );

        return array(
            'success' => true,
            'result'  => $results
        );
    }

    /**
     * Save merchant profile
     *
     * @param $body
     * @return array|\WP_Error
     */
    public static function save_merchant_profile($body) {

        $request = ApiHelper::post(
            '/profile',
            array(
                'body' => json_encode(
                    array(
                        'deliveredFromCountry'  => $body['delivered_from_country'],
                        'averageWeight'         => $body['average_weight'],
                        'shopId'                => $body['shop_id'],
                        'businessId'            => $body['business_id'],
                        'vatId'                 => $body['vat_number'],
                        'businessName'          => $body['company_name'],
                        'city'                  => $body['company_city'],
                        'country'               => $body['company_country'],
                        'state'                 => $body['company_state'],
                        'postcode'              => $body['company_postcode'],
                        'address'               => $body['company_address'],
                        'acceptedTC'            => true
                    )
                ),
                'authenticated' => true
            )
        );


        if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
            return new \WP_Error( 'compensate_update_merchant_profile', __( 'There was an error updating merchant profile to Compensate. Please try again.', 'compensate' ), 500 );
        }

        $results = json_decode( wp_remote_retrieve_body( $request ), true );

        return array(
            'success' => true,
            'result'  => $results
        );
    }


    /**
     *
     * Post order object
     *
     * @param $body
     * @return array|\WP_Error
     */
    public static function post_order($body){
        $request = ApiHelper::post(
            '/order',
            array(
                'body' => json_encode($body),
                'authenticated' => true
            )
        );


        if (is_wp_error($request) || wp_remote_retrieve_response_code($request) !== 200) {
            return new \WP_Error('compensate_post_order', __('There was an error posting order to the compensate server. Please try again.', 'compensate'), 500);
        }

        $results = json_decode( wp_remote_retrieve_body( $request ), true );

        return array(
            'success' => true,
            'result'  => $results
        );
    }


    /**
     * Cleanup merchant profile
     *
     * @return array|\WP_Error
     */
    public static function cleanup_profile() {
        $request = ApiHelper::delete(
            '/uninstall',
            array(
                'authenticated' => true
            )
        );

        if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
            return new \WP_Error( 'compensate_update_merchant_profile', __( 'There was an error cleanup merchant profile. Please try again.', 'compensate' ), 500 );
        }

        $results = json_decode( wp_remote_retrieve_body( $request ), true );
        OptionsHelper::update('show_wizard_settings', 1);
        OptionsHelper::update('show_wizard_notice', 1);
        OptionsHelper::update('show_wizard_settings_success', 1);


        return array(
            'success' => true,
            'result'  => $results
        );
    }
}