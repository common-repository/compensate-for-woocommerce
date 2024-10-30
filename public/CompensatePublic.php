<?php
namespace CompensateFront;
/**
 * The public-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-specific stylesheet and JavaScript.

 */
class CompensatePublic {

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

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../dist/public.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     */
    public function enqueue_scripts() {

        wp_register_script(
            $this->plugin_name, plugin_dir_url( __FILE__ ) . '../dist/public.js', ['jquery'], $this->version, true);

        wp_enqueue_script( $this->plugin_name);

        wp_localize_script( $this->plugin_name, 'compensate_obj', [
            'ajax_url'      =>  admin_url( 'admin-ajax.php' ),
            'ipinfo_url'      =>  'https://ipinfo.io?token=c2329c67a7ea32',
        ]);
    }
}
