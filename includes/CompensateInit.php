<?php
namespace CompensateInc;

use CompensateAdmin\CompensateAdmin;
use CompensateAdmin\GeneralSettings;
use CompensateFront\CompensatePublic;
use CompensateFront\CompensateCart;
use CompensateFront\CompensateAjax;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

class CompensateInit {

    /**
     * GeneralSettings instance.
     *
     * @var GeneralSettings
     */
    public $settings;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     */
    protected $version;

    /**
     * The current version of the plugin.
     */
    protected $plugin_base_name;


    protected static $_instance = null;


    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.

     */
    public function __construct() {
        if ( defined( 'COMPENSATE_VERSION' ) ) {
            $this->version = COMPENSATE_VERSION;
        } else {
            $this->version = '1.0.7';
        }
        $this->plugin_name = 'compensate';
        $this->plugin_base_name = plugin_basename( dirname( __FILE__, 2 ) ) . '/compensate.php';

        $this->loader = new Loader();
        $this->settings = GeneralSettings::instance();

        $this->define_constants();
        $this->includes();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * CompensateInit Instance.
     *
     * Ensures only one instance of CompensateInit is loaded or can be loaded.
     *
     * @static
     * @return CompensateInit
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Localization class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Localization();
        $plugin_i18n->load_plugin_textdomain();
    }

    /**
     * Define Compensate Constants.
     */
    private function define_constants() {
        $this->define( 'COMPENSATE_VERSION', $this->version );
        $this->define( 'COMPENSATE_PATH', plugin_dir_path(COMPENSATE_PLUGIN_FILE) );
        $this->define( 'COMPENSATE_IMG_ADMIN_URI', plugin_dir_url(COMPENSATE_PLUGIN_FILE) . '/admin/assets/images/' );
        $this->define( 'COMPENSATE_IMG_PUBLIC_URI', plugin_dir_url(COMPENSATE_PLUGIN_FILE) . '/public/assets/images/' );
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }


    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     */
    private function define_admin_hooks() {

        $plugin_admin = new CompensateAdmin( $this->get_plugin_name(), $this->get_version() );
        $settings_general = new GeneralSettings();

        // Enqueue scripts and styles
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        // Handle settings
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_wizard_settings_notice' );
        $this->loader->add_action('admin_post_compensate_settings_save_options', $settings_general, 'save_setting_options');
        $this->loader->add_action('admin_post_compensate_wizard_save_options', $settings_general, 'save_wizard_setting_options');

        // Add settings link
        $this->loader->add_filter('plugin_action_links_' . $this->plugin_base_name, $plugin_admin, 'settings_link');

        // Add custom order column
        $this->loader->add_action('woocommerce_checkout_create_order', $plugin_admin, 'add_compensate_to_order_metadata', 20, 1);
        $this->loader->add_action( 'manage_shop_order_posts_custom_column',$plugin_admin, 'custom_orders_column_content', 20, 2);
        $this->loader->add_filter( 'manage_edit-shop_order_columns', $plugin_admin, 'custom_compensate_order_column', 20);

        $this->loader->add_action('woocommerce_order_status_processing', $plugin_admin, 'save_transaction', 20, 1);

        $this->loader->add_filter( 'woocommerce_get_order_item_totals', $plugin_admin,'change_compensate_fee_label', 20, 1 );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     */
    private function define_public_hooks() {
        $plugin_public = new CompensatePublic($this->get_plugin_name(), $this->get_version());
        $compensate_cart = new CompensateCart();
        $compensate_ajax = new CompensateAjax();

        // Enqueue scripts and styles
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

        // Add compensate widget
        $this->loader->add_action( 'woocommerce_cart_collaterals', $compensate_cart, 'display_widget', 5);
        $this->loader->add_action( 'woocommerce_checkout_order_review', $compensate_cart, 'display_widget');

        // Handle AJAX
        $this->loader->add_action('wp_ajax_toggle_compensate_surcharge', $compensate_ajax, 'toggle_compensate_surcharge');
        $this->loader->add_action('wp_ajax_nopriv_toggle_compensate_surcharge', $compensate_ajax, 'toggle_compensate_surcharge');

        $this->loader->add_action('wp_ajax_update_compensate_surcharge_value', $compensate_ajax, 'update_compensate_surcharge_value');
        $this->loader->add_action('wp_ajax_nopriv_update_compensate_surcharge_value', $compensate_ajax, 'update_compensate_surcharge_value');

        // Add compensate surcharge
        $this->loader->add_action('woocommerce_cart_calculate_fees', $compensate_cart, 'add_custom_surcharge', 20, 1);
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        include_once COMPENSATE_PATH . 'includes/compensate-functions.php';
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}


