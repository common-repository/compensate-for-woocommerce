<?php
namespace CompensateFront;
use CompensateAdmin\OptionsHelper;

/**
 * Handle Compensate Carts
 */
class CompensateCart {

    /**
     * Display widget template
     *
     */
    public function display_widget() {
        $compensate_opt_out = Compensate()->settings->get_compensate_opt_out();
        $is_compensate_surcharged_added = WC()->session->get( 'is_compensate_surcharged_added', $compensate_opt_out);
        $compensate_surcharge_value = WC()->session->get( 'compensate_surcharge_value');

        // Only show widget if finishing onboarding
        if (OptionsHelper::get('show_wizard_settings')) {
            return;
        }

        $args = array(
            'is_compensate_surcharged_added' => $is_compensate_surcharged_added,
            'compensate_surcharge_value' => wc_price($compensate_surcharge_value)
        );

        compensate_get_template('compensate-widget-cart.php', $args, COMPENSATE_PATH . 'public/templates');
    }

    /**
     * Add compensate surcharge
     */
    public function add_custom_surcharge($cart) {

        if ( is_admin() && ! defined( 'DOING_AJAX' ) )
            return;

        $compensate_opt_out = Compensate()->settings->get_compensate_opt_out();
        $is_compensate_added = WC()->session->get( 'is_compensate_surcharged_added', $compensate_opt_out);
        $surchargeValue = WC()->session->get( 'compensate_surcharge_value');

        if ($is_compensate_added)
            $cart->add_fee('Compensate', $surchargeValue);
    }
}
