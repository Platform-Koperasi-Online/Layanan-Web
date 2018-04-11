<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
Plugin Name:  Koperasi Payment
Description:  Woocommerce payment plugin for Platform Koperasi
Version:      0.1
Author:       Kevin Erdiza
Author URI:   github.com/keychera
*/


function declare_koperasi_gateway_class() {
    require_once('includes/class-wc-gateway-kp-gateway.php');
}

add_action( 'plugins_loaded', 'declare_koperasi_gateway_class' );

function add_gateway_class( $methods ) {
    $methods[] = 'WC_Gateway_KP_Gateway'; 
    return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'add_gateway_class' );
