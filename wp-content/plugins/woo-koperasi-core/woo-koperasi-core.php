<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
Plugin Name:  Koperasi Core
Description:  Koperasi core in Woocommerce for Platform Koperasi
Version:      0.1
Author:       Kevin Erdiza
Author URI:   github.com/keychera
*/


function declare_koperasi_core_classes() {
    require_once('includes/class-wc-gateway-kp-debugger.php');
    require_once('includes/class-wc-gateway-kp-payment-bootstrapper.php');

    require_once('includes/class-wc-admin-kp-core.php');
    require_once('includes/class-wc-gateway-kp-gateway.php');

    new WC_Admin_KP_Core_Plugin();
}

add_action( 'plugins_loaded', 'declare_koperasi_core_classes', 1000);

function add_gateway_class( $methods ) {
    $methods[] = 'WC_Gateway_KP_Gateway'; 
    return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'add_gateway_class' );