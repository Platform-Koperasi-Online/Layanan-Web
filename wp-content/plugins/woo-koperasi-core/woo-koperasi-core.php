<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
Plugin Name:  Koperasi Core
Description:  Koperasi core in Woocommerce for Platform Koperasi
Version:      0.1
Author:       Kevin Erdiza
Author URI:   github.com/keychera
*/


function declare_koperasi_core_class() {
    require_once('includes/class-wc-admin-kp-core.php');

    new WC_Admin_KP_Core_Plugin();
}

add_action( 'plugins_loaded', 'declare_koperasi_core_class', 1000);