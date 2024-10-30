<?php
namespace CompensateAdmin;

/**
 * General settings controller
 *
 * Handle (save, update, etc) setting options
 */

class GeneralSettings {

    protected static $_instance = null;

    private $settings_group;

    public function __construct() {
        $this->settings_group = 'compensate_general_options';
    }

    /**
     * General Settings Instance.
     *
     * Ensures only one instance of General Settings is loaded or can be loaded.
     *
     * @static
     * @return GeneralSettings
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Save general settings
     */
    public function save_setting_options() {

        if (!current_user_can('edit_theme_options')) {
            wp_die(__('You are not allowed to be on this page', 'compensate'));
        }

        check_admin_referer('compensate_settings_verify');

        // Sanitize field inputs
//        $compensation_opt_out = filter_var($_POST['compensation_opt_out'] === 'on', FILTER_SANITIZE_NUMBER_INT);
        $is_sustainability_statement_accepted = filter_var(true, FILTER_SANITIZE_NUMBER_INT);
        $store_country = sanitize_text_field($_POST['store_country']);
        $average_weight = (float)sanitize_text_field($_POST['average_weight']);
        $weight_unit = sanitize_text_field($_POST['weight_unit']);

        $business_id = $this->get('business_id');
        $vat_number = $this->get('vat_number');
        $company_name = $this->get('company_name');
        $company_address = $this->get('company_address');
        $company_city = $this->get('company_city');
        $company_postcode = $this->get('company_postcode');
        $company_country = $this->get('company_country');
        $company_state = $this->get('company_state');

        // Send profile data to api
        $auth_options = OptionsHelper::get('auth');

        if(!isset($auth_options['shopId'])) {
            //TODO: log error when shopId is null
            return;
        }

        $results = AdminApiHelper::save_merchant_profile(array(
            'delivered_from_country'     => $store_country,
            'average_weight'             => wc_get_weight( $average_weight, 'g', $weight_unit), // Convert to grams
            'shop_id'                    => $auth_options['shopId'],
            'business_id'                => $business_id,
            'vat_number'                 => $vat_number,
            'company_name'               => $company_name,
            'company_city'               => $company_city,
            'company_country'            => $company_country,
            'company_state'              => $company_state,
            'company_postcode'           => $company_postcode,
            'company_address'            => $company_address
        ));

        if (is_wp_error($results)) {
            wp_redirect(admin_url('admin.php?page=compensate&status=0'));
        }

        // Save data to wordpress db
        $current_options = get_option($this->settings_group, array());

        $general_options['is_sustainability_statement_accepted'] = $is_sustainability_statement_accepted;
//        $general_options['compensation_opt_out'] = $compensation_opt_out;
        $general_options['store_country'] = $store_country;
        $general_options['average_weight'] = $average_weight;
        $general_options['weight_unit'] = $weight_unit;

        $merged_options = wp_parse_args($general_options, $current_options);
        update_option($this->settings_group, $merged_options);

        wp_redirect(admin_url('admin.php?page=compensate&status=1'));
    }


    /**
     * Save wizard settings
     */
    public function save_wizard_setting_options() {

        if (!current_user_can('edit_theme_options')) {
            wp_die(__('You are not allowed to be on this page', 'compensate'));
        }

        check_admin_referer('compensate_wizard_settings_verify');

        // Sanitize field inputs
        $is_privacy_accepted = filter_var($_POST['is_privacy_accepted'], FILTER_SANITIZE_NUMBER_INT);
        $is_store_info_agreed = filter_var($_POST['is_store_info_agreed'], FILTER_SANITIZE_NUMBER_INT);
        $store_country = sanitize_text_field($_POST['store_country']);
        $average_weight = (float)sanitize_text_field($_POST['average_weight']);
        $weight_unit = sanitize_text_field($_POST['weight_unit']);
        $business_id = sanitize_text_field($_POST['business_id']);
        $vat_number = sanitize_text_field($_POST['vat_number']);

        $company_name = sanitize_text_field($_POST['company_name']);
        $company_address = sanitize_text_field($_POST['company_address']);
        $company_city = sanitize_text_field($_POST['company_city']);
        $company_postcode = sanitize_text_field($_POST['company_postcode']);
        $company_country = sanitize_text_field($_POST['company_country']);
        $company_state = !empty( $_POST['company_state'] ) ? sanitize_text_field($_POST['company_state']) : '';


        // Send profile data to api
        $auth_options = OptionsHelper::get('auth');

        if(!isset($auth_options['shopId'])) {
            //TODO: log error when shopId is null
            return;
        }

        $results = AdminApiHelper::save_merchant_profile(array(
            'delivered_from_country'     => $store_country,
            'average_weight'             => wc_get_weight( $average_weight, 'g', $weight_unit), // Convert to grams
            'shop_id'                    => $auth_options['shopId'],
            'business_id'                => $business_id,
            'vat_number'                 => $vat_number,
            'company_name'               => $company_name,
            'company_city'               => $company_city,
            'company_country'            => $company_country,
            'company_state'              => $company_state,
            'company_postcode'           => $company_postcode,
            'company_address'            => $company_address
        ));

        if (is_wp_error($results)) {
            wp_redirect(admin_url('admin.php?page=compensate&status=0'));
        }

        // Save data to wordpress db
        $current_options = get_option($this->settings_group, array());

        $general_options['is_privacy_accepted'] = $is_privacy_accepted;
        $general_options['is_store_info_agreed'] = $is_store_info_agreed;
        $general_options['is_sustainability_statement_accepted'] = filter_var(true, FILTER_SANITIZE_NUMBER_INT);
        $general_options['store_country'] = $store_country;
        $general_options['average_weight'] = $average_weight;
        $general_options['weight_unit'] = $weight_unit;
        $general_options['business_id'] = $business_id;
        $general_options['vat_number'] = $vat_number;

        $general_options['company_name'] = $company_name;
        $general_options['company_address'] = $company_address;
        $general_options['company_city'] = $company_city;
        $general_options['company_postcode'] = $company_postcode;
        $general_options['company_country'] = $company_country;
        $general_options['company_state'] = $company_state;

        $merged_options = wp_parse_args($general_options, $current_options);
        update_option($this->settings_group, $merged_options);

        // Success: update option
        OptionsHelper::update('show_wizard_settings', 0);

        wp_redirect(admin_url('admin.php?page=compensate&status=1'));
    }


    /**
     * Get settings compensation_opt_out
     *
     * @return mixed
     */
    public function get_compensate_opt_out() {
        return $this->get('compensation_opt_out', false);
    }


    /**
     * Set settings compensation_opt_out
     *
     * @param $value
     * @return bool True if the option has been updated.
     */
    public function set_compensate_opt_out($value) {
        return $this->update('compensation_opt_out', filter_var($value, FILTER_SANITIZE_NUMBER_INT));
    }

    /**
     * Get settings store_country
     *
     * @return mixed
     */
    public function get_store_country() {
        return $this->get('store_country', false);
    }

    /**
     * Get settings average_weight
     *
     * @return mixed
     */
    public function get_average_packages_weight() {
        return $this->get('average_weight');
    }

    /**
     * Get settings weight_unit
     *
     * @return mixed
     */
    public function get_weight_unit() {
        return $this->get('weight_unit');
    }


    /**
     *
     * Cleanup genernal options table
     *
     * @return bool
     */
    public function cleanup() {
        return delete_option($this->settings_group);
    }

    /**
     * Update an option by key
     *
     * @param string $key The key to update.
     * @param mixed  $value The new option value.
     *
     * @return bool True if the option has been updated.
     */
    public function update( $key, $value ) {
        $options         = get_option($this->settings_group, array() );
        $options[ $key ] = $value;

        return update_option($this->settings_group, $options, true );
    }

    /**
     * Get an option by key
     *
     *
     * @param string $key The key to fetch.
     * @param mixed  $default The default option to return if the key does not exist.
     *
     * @return mixed An option or the default.
     */
    public function get( $key, $default = false ) {
        $options = get_option($this->settings_group, array());

        if ( array_key_exists( $key, $options ) ) {
            return $options[ $key ];
        }

        return $default;
    }

    /**
     * Initialize general settings with default values
     */
    public function initGeneralSettings() {

        $settings = get_option($this->settings_group, null );

        // If there are no settings defined, use defaults.
        if ( ! is_array($settings) ) {
            $this->update('store_country', '');
            $this->update('average_weight', '');
            $this->update('weight_unit', 'kg');
            $this->update('business_id', '');
            $this->update('vat_number', '');
            $this->update('company_name', '');
            $this->update('company_address', '');
            $this->update('company_city', '');
            $this->update('company_postcode', '');
            $this->update('company_country', '');
            $this->update('company_state', '');
            $this->update('compensation_opt_out', 0);
            $this->update('is_privacy_accepted', 0);
            $this->update('is_store_info_agreed', 0);
            $this->update('is_sustainability_statement_accepted', 0);
        }
    }
}