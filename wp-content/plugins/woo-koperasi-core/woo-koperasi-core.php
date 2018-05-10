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
    function koperasi_core() {
        static $core;

        if ( ! isset( $core ) ) {
            require_once( 'includes/class-wc-admin-kp-core.php' );

            $core = new WC_Admin_KP_Core_Plugin();
        }

        return $core;
    }

    function declare_koperasi_core_classes() {
        require_once('includes/class-wc-gateway-kp-debugger.php');
        require_once('includes/class-wc-gateway-kp-bank-bootstrapper.php');
        require_once('includes/class-wc-user-kp-member.php');
        require_once('includes/class-wc-gateway-kp-payment-gateway.php');
        require_once('includes/class-wc-kp-simpan.php');
        require_once('includes/class-wc-kp-shortcodes.php');

        koperasi_core();
    }

    add_action( 'plugins_loaded', 'declare_koperasi_core_classes', 1000);

    function add_gateway_class( $methods ) {
        $methods[] = 'WC_Gateway_KP_Payment_Gateway'; 
        return $methods;
    }

    add_filter( 'woocommerce_payment_gateways', 'add_gateway_class' );
}