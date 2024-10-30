<?php
namespace CompensateAdmin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.

 */
class CompensateAdmin {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     */
    public function enqueue_styles() {

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../dist/admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     */
    public function enqueue_scripts() {


        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../dist/admin.js', array( 'jquery' ), $this->version, true );

        wp_localize_script( $this->plugin_name, 'compensate_i18n', [
            'store_info_agreed_required'      =>  __('Please allow us to access your store information to continue', 'compensate'),
            'privacy_accepted_required'       =>  __('Please accept the Terms and Conditions and Privacy Policy to continue', 'compensate'),
            'store_country_required'          =>  __('Please add your warehouse location', 'compensate'),
            'average_weight_required'         =>  __('Please add your average package weight', 'compensate'),
            'business_id_required'            =>  __('Please add your Business ID', 'compensate'),
            'vat_number_required'             =>  __('Please add your VAT number', 'compensate'),
            'company_name_required'           =>  __('Please add your company name', 'compensate'),
            'company_address_required'        =>  __('Please add your company address', 'compensate'),
            'company_city_required'           =>  __('Please add your town/city', 'compensate'),
            'company_postcode_required'       =>  __('Please add your postcode/ZIP', 'compensate'),
            'company_state_required'          =>  __('Please add your state / county / province ', 'compensate'),
            'company_country_required'        =>  __('Please add the country in which your company is registered', 'compensate'),
        ]);

    }

    /**
     * Register the Settings page.
     *
     */
    public function admin_menu() {

        add_menu_page( __('Compensate', $this->plugin_name), __('Compensate', $this->plugin_name), 'manage_options', $this->plugin_name, array($this, 'display_plugin_admin_page'),'', 60);
    }

    /**
     * Callback function for the admin settings page.
     *
     */
    public function display_plugin_admin_page() {

        $compensate_show_wizard_settings = OptionsHelper::get('show_wizard_settings');
        $show_wizard_settings_success = OptionsHelper::get('show_wizard_settings_success');

        $args = array(
            'general_options'               => get_option('compensate_general_options'),
            'show_wizard_settings_success'  => $show_wizard_settings_success
        );

        if ($compensate_show_wizard_settings) {
            compensate_get_template('compensate-wizard-settings.php', $args,COMPENSATE_PATH . 'admin/templates');
        } else {
            compensate_get_template('compensate-settings.php', $args,COMPENSATE_PATH . 'admin/templates');
        }
    }

    /**
     * Show wizard settings notice
     *
     */
    public function admin_wizard_settings_notice() {
        if (OptionsHelper::get('show_wizard_notice') ) {

            OptionsHelper::update('show_wizard_notice', 0);

            compensate_get_template('compensate-wizard-notice.php', '',COMPENSATE_PATH . 'admin/templates');
        }
    }

    /**
     * Add settings link.
     *
     */
    public function settings_link( $links ) {
        $settings_link = '<a href="admin.php?page=compensate">' . __('Settings', 'compensate') . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Add compensate surcharge value to order metadata
     */
    function add_compensate_to_order_metadata( $order ) {
        $compensate_value = WC()->session->get('compensate_surcharge_value', 0);
        $compensate_calculation_id = WC()->session->get('compensate_calculation_id', '');
        $order->update_meta_data( 'compensate_surcharge_value',  $compensate_value);
        $order->update_meta_data( 'compensate_calculation_id',  $compensate_calculation_id);
    }


    /**
     * Add custom order column
     */
    function custom_compensate_order_column($columns){
        $reordered_columns = array();

        // Inserting columns to a specific location
        foreach( $columns as $key => $column){
            $reordered_columns[$key] = $column;
            if( $key ==  'order_status' ){
                // Inserting after "Status" column
                $reordered_columns['compensate'] = __( 'Compensate','compensate');
            }
        }

        return $reordered_columns;
    }

    /**
     *  Fill custom Order column with Compensate amount
     */
    function custom_orders_column_content( $column, $post_id ) {

        switch ( $column ) {
            case 'compensate' :
                $order = wc_get_order($post_id);
                echo wc_price($order->get_meta('compensate_surcharge_value', true));

                break;
        }
    }

    /**
     *  Send order to compensate server
     */
    function save_transaction($order_id) {
        $order = wc_get_order($order_id);
        $order_data = $order->get_data();

        $order_items = $order->get_items();
        $items_data  = array();

         // Loop through order items
        foreach ( $order_items as $item_id => $item ) {
            $variation_id = $item->get_variation_id();
            $product_id   = $variation_id > 0 ? $variation_id : $item->get_product_id();

            // Set specific data for each item in the array
            $items_data[] = array(
                'id'          => $product_id,
                'description' => $item->get_name(),
                'quantity'    => $item->get_quantity(),
                'value'       => $item->get_subtotal(),
                'salesTax'    => $item->get_subtotal_tax(),
            );
        }

        $body = array(
            'orderData' => $order_data,
            'orderItems' => $items_data,
        );

        $results = AdminApiHelper::post_order($body);

        if (is_wp_error($results)) {
            error_log('Compensate: Fail to post oder data');
        }
    }

    /**
     *  Change compensate label in email
     */
    function change_compensate_fee_label( $total_rows ) {

        foreach($total_rows as $key => $value) {

            if ($value['label'] === 'Compensate:') {
                $total_rows[$key]['label'] = __('Compensate (Compensate Foundation, Business ID 2914937-8)', 'compensate');
            }
        }

        return $total_rows;
    }
}
