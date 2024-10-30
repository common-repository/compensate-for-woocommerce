<?php

namespace CompensateAdmin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * OptionsHelper Class
 *
 * Handle compensate_helper_data in wp_options table
 */
class OptionsHelper {
    /**
     * The option name used to store the helper data.
     *
     * @var string
     */
    private static $option_name = 'compensate_helper_data';

    /**
     * Update an option by key
     *
     * @param string $key The key to update.
     * @param mixed  $value The new option value.
     *
     * @return bool True if the option has been updated.
     */
    public static function update( $key, $value ) {
        $options         = get_option( self::$option_name, array() );
        $options[ $key ] = $value;

        return update_option( self::$option_name, $options, true );
    }

    /**
     * Get an option by key
     *
     * @see self::update
     *
     * @param string $key The key to fetch.
     * @param mixed  $default The default option to return if the key does not exist.
     *
     * @return mixed An option or the default.
     */
    public static function get( $key, $default = false ) {
        $options = get_option( self::$option_name, array() );
        if ( array_key_exists( $key, $options ) ) {
            return $options[ $key ];
        }

        return $default;
    }


    /**
     * Initialize compensate_helper_data with default values
     */
    public static function initHelperData() {

        $options = get_option(self::$option_name, null);

        // If there are no options defined, use defaults.
        if ( ! is_array($options) ) {
            self::update('show_wizard_notice', 1);
            self::update('show_wizard_settings', 1);
            self::update('show_wizard_settings_success', 1);
        }
    }
}