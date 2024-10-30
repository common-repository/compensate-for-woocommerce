<?php
/**
 * Plugin Name: Compensate for WooCommerce
 *
 * @package      CompensatePlugin
 * @author       Huy Trinh
 * @copyright    2020 Compensate
 * @license      GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:       Compensate for WooCommerce
 * Plugin URI:        https://www.compensate.com
 * Description:       The WooCommerce plugin for CO2 offsetting on demand – take climate action with your customers. True impact, high integrity.
 * Version:           1.0.7
 * Author:            Compensate
 * Author URI:        https://www.compensate.com/about-us
 * Text Domain:       compensate
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI:
 *
 * Tested up to: 5.5
 * WC requires at least: 2.5.0
 * WC tested up to: 4.4.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if(file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

use CompensateInc\Activator;
use CompensateInc\Deactivator;
use CompensateInc\Uninstallation;
use CompensateInc\CompensateInit;

if ( ! defined( 'COMPENSATE_PLUGIN_FILE' ) ) {
    define( 'COMPENSATE_PLUGIN_FILE', __FILE__ );
}

if( ! function_exists('compensate_activate')) {
    function compensate_activate()
    {
        Activator::activate();
    }
}

if( ! function_exists('compensate_deactivate')) {
    function compensate_deactivate() {
        Deactivator::deactivate();
    }
}


if( ! function_exists('compensate_uninstall')) {
    function compensate_uninstall() {
        Uninstallation::uninstall();
    }

}

register_activation_hook( __FILE__, 'compensate_activate' );
register_deactivation_hook( __FILE__, 'compensate_deactivate' );
register_uninstall_hook(__FILE__, 'compensate_uninstall');



/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */

if( ! function_exists('run_compensate')) {
    function run_compensate() {

        $plugin = new CompensateInit();
        $plugin->run();
    }
}

/**
 * Check if WooCommerce is activated
 *
 * @since    1.0.0
 */

if( ! function_exists('compensate_init')) {
    function compensate_init(){
        if ( function_exists( 'WC' ) ) {

            $store_country = get_option( 'woocommerce_default_country' );
            $store_currency = get_woocommerce_currency();

            if (!compensate_is_country_supported($store_country)) {

                add_action( 'admin_notices', 'compensate_wc_unsupported_country_notice' );

            } elseif (!compensate_is_currency_supported($store_currency)) {

                add_action( 'admin_notices', 'compensate_wc_unsupported_currency_notice' );
            } else {
                /**
                 * The core plugin class that is used to define internationalization,
                 * admin-specific hooks, and public-facing site hooks.
                 */
                run_compensate();
            }
        }
        else{
            add_action( 'admin_notices', 'compensate_wc_notice' );
        }
    }
}

add_action('plugins_loaded','compensate_init');

/**
 * WooCommerce not activated admin notice
 *
 * @since    1.0.0
 */
if( ! function_exists('compensate_wc_notice')) {
    function compensate_wc_notice()
    {
        ?>
        <div class="error">
            <p><?php _e('Compensate requires WooCommerce in order to work.', 'compensate'); ?></p>
        </div>
        <?php
    }
}

/**
 * WooCommerce not supported country notice
 *
 * @since    1.0.0
 */
if( ! function_exists('compensate_wc_unsupported_country_notice')) {
    function compensate_wc_unsupported_country_notice(){
        ?>
        <div class="error">
            <p><?php _e( 'Oh no, it seems Compensate isn’t yet available in your location! Supported languages are English, Swedish, Danish, German, Dutch, and Finnish. Supported currencies are EUR, USD, SEK, DKK, GBP, and CAD.', 'compensate' ); ?></p>
        </div>
        <?php
    }
}

/**
 * WooCommerce not supported country notice
 *
 * @since    1.0.0
 */
if( ! function_exists('compensate_wc_unsupported_currency_notice')) {
    function compensate_wc_unsupported_currency_notice(){
        ?>
        <div class="error">
            <p><?php _e( 'Oh no, it seems Compensate doesn’t yet support your language or currency! Supported languages are English, Swedish, Danish, German, Dutch, and Finnish. Supported currencies are EUR, USD, SEK, DKK, GBP, and CAD.', 'compensate' ); ?></p>
        </div>
        <?php
    }
}


/**
 * Returns the main instance of Compensate.
 *
 * @return CompensateInit
 */
function Compensate() {
    return CompensateInit::instance();
}


/**
 *
 * Return true if store country is supported
 *
 * @param $country
 * @return bool
 */

if( ! function_exists('compensate_is_country_supported')) {
    function compensate_is_country_supported($country) {

        $supported_countries = array(

            // EU
            'BE' => 'Belgium',
            'BG' => 'Bulgaria',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DE' => 'Germany',
            'EE' => 'Estonia',
            'IE' => 'Ireland',
            'GR' => 'Greece',
            'ES' => 'Spain',
            'FR' => 'France',
            'HR' => 'Croatia',
            'IT' => 'Italy',
            'CY' => 'Cyprus',
            'LV' => 'Latvia',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'HU' => 'Hungary',
            'MT' => 'Malta',
            'NL' => 'Netherlands',
            'AT' => 'Austria',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'RO' => 'Romania',
            'SI' => 'Slovenia',
            'SK' => 'Slovakia',
            'FI' => 'Finland',
            'SE' => 'Sweden',

            // Others
            'NO' => 'Norway',
        );

        return array_key_exists ($country , $supported_countries);
    }
}

/**
 *
 * Return true if store currency is supported
 *
 * @param $currency
 * @return bool
 */

if( ! function_exists('compensate_is_currency_supported')) {
    function compensate_is_currency_supported($currency) {

        $supported_currencies = array(
            'EUR' => 'Euro',
            'USD' => 'United States (US) dollar',
            'SEK' => 'Swedish krona',
            'DKK' => 'Danish krone',
            'GBP' => 'Pound sterling',
            'CAD' => 'Canadian dollar',
        );

        return array_key_exists ($currency , $supported_currencies);
    }
}
