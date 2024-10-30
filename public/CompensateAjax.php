<?php
namespace CompensateFront;
use CompensateAdmin\OptionsHelper;

/**
 * Handle Compensate AJAX
 */
class CompensateAjax {

    /**
     * AJAX toggle compensate surcharge
     */
    public function toggle_compensate_surcharge() {
        if (isset( $_POST['is_compensate_added'])) {
            $is_compensate_surcharged_added =  filter_var ($_POST['is_compensate_added'], FILTER_VALIDATE_BOOLEAN);
            WC()->session->set( 'is_compensate_surcharged_added', $is_compensate_surcharged_added );
        }

        wp_die();
    }

    /**
     * AJAX update compensate surcharge value based on cart items
     */
    public function update_compensate_surcharge_value() {

        $store_country = Compensate()->settings->get_store_country();
        $currency = get_woocommerce_currency();
        $weight = compensate_get_weight();


        $shipping_country = isset($_POST['shipping_country']) ? sanitize_text_field($_POST['shipping_country']) : WC()->customer->get_shipping_country();
        $ipLocation = isset($_POST['ipLocation']) ? sanitize_text_field($_POST['ipLocation']) : get_option( 'woocommerce_default_country', '' );;
        $customer_default_location = wc_get_customer_default_location();
        $country_to = isset($shipping_country) ? $shipping_country : $customer_default_location['country'];

        $user_shipping_location = (empty($country_to) || $country_to === 'default') ? $ipLocation : $country_to;

        $shopId = OptionsHelper::get('auth')['shopId'];

        $response = PublicApiHelper::post_price(array(
            'country_from' => $store_country,
            'currency' => $currency,
            'country_to' => $user_shipping_location,
            'weight' => $weight,
            'shop' => $shopId
        ));

        if (is_wp_error($response)) {
            // TODO: Handle error
        }

        $calculation_id = $response['result']['id'];
        $price_eur = $response['result']['price'];

        $compensate_amount = $price_eur;

        // Save surcharge value to WC session
        WC()->session->set( 'compensate_surcharge_value', $compensate_amount);
        WC()->session->set( 'compensate_calculation_id', $calculation_id);

        // Update cart totals
        WC()->cart->calculate_totals();

        ob_start();

        woocommerce_cart_totals();

        $cart_totals = ob_get_clean();

        $response = array(
            'updated_cart_total'            => $cart_totals,
            'compensate_surcharge_value'	=> wc_price($compensate_amount),
            'compensate_amount'             => $compensate_amount,
        );

        wp_send_json($response);
    }
}
