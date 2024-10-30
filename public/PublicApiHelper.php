<?php
namespace CompensateFront;
use CompensateInc\ApiHelper;

/**
 *
 * Handle Front API
 */

class PublicApiHelper {

    public static function post_price($body) {
        $request = ApiHelper::post(
            '/price',
            array(
                'body' => json_encode(
                    array(
                        'countryFrom'   => $body['country_from'],
                        'currency'  => $body['currency'],
                        'countryTo'     => $body['country_to'],
                        'weight'     => $body['weight'],
                        'shop'     => $body['shop'],
                    )
                ),
                'authenticated' => true
            )
        );


        if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
            return new \WP_Error( 'compensate_post_price', __( 'There was an error updating the compensate price. Please try again.', 'compensate' ), 500 );
        }


        $results = json_decode( wp_remote_retrieve_body( $request ), true );
        if ( ! $results ) {
            return new \WP_Error( 'compensate_post_price', __( 'There was an error updating the compensate price. Please try again.', 'compensate' ), 500 );
        }

        return array(
            'success' => true,
            'result'  => $results
        );
    }
}