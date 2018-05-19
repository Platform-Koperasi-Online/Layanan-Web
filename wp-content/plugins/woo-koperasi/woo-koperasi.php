<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
Plugin Name:  Koperasi Core
Description:  Koperasi core in Woocommerce for Platform Koperasi
Version:      0.1
Author:       Kevin Erdiza
Author URI:   github.com/keychera
*/

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    function koperasi() {
        static $core;

        if ( ! isset( $core ) ) {
            require_once( 'includes/class-wc-kp-core.php' );

            $core = new WC_KP_Core();
        }

        return $core;
    }

    function declare_koperasi_classes() {
        foreach (glob(dirname(__FILE__).'/includes/*.php') as $filename)
        {
            require_once($filename);
        }

        koperasi();
    }

    add_action( 'plugins_loaded', 'declare_koperasi_classes', 1000);

    function add_gateway_class( $methods ) {
        $methods[] = 'WC_Gateway_KP_Payment_Gateway'; 
        return $methods;
    }

    add_filter( 'woocommerce_payment_gateways', 'add_gateway_class' );
}