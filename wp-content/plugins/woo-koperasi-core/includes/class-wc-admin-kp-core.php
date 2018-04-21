<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_Admin_KP_Core_Plugin {
    private $koperasi_bank_email;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 1000 );
        
    }

    /**
	 * Add menu items.
	 */
	public function admin_menu() {
		add_submenu_page( 'woocommerce', __( 'Koperasi', 'woocommerce' ), __( 'Koperasi', 'woocommerce' ), 'manage_woocommerce', 'koperasi_core', array( $this, 'koperasi_core_page' ) );
    }
    
    /**
	 * Init the koperasi page.
	 */
	public function koperasi_core_page() {
		echo "<h1> Hello, Koperasi </h1>";
	}
}