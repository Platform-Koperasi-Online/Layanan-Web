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
    class WC_Gateway_Koperasi_Gateway extends WC_Payment_Gateway {
        public function __construct() {
            //Gateway information
            $this->id = 'koperasi';
            $this->has_fields = true;
            $this->method_title = 'Koperasi Payment Gateway';
            $this->method_description = 'Woocommerce payment plugin for Platform Koperasi';

            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }

            /**
         * Initialise Gateway Settings Form Fields.
         */
        public function init_form_fields() {

            $this->form_fields = array(
                'enabled' => array(
                    'title'   => __( 'Enable/Disable', 'woocommerce' ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Enable Koperasi payment', 'woocommerce' ),
                    'default' => 'no',
                ),
                'title' => array(
                    'title'       => __( 'Title', 'woocommerce' ),
                    'type'        => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                    'default'     => __( 'Direct bank transfer', 'woocommerce' ),
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => __( 'Description', 'woocommerce' ),
                    'type'        => 'textarea',
                    'description' => __( 'Payment method description that the customer will see on your checkout.', 'woocommerce' ),
                    'default'     => __( 'Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.', 'woocommerce' ),
                    'desc_tip'    => true,
                ),
                'instructions' => array(
                    'title'       => __( 'Instructions', 'woocommerce' ),
                    'type'        => 'textarea',
                    'description' => __( 'Instructions that will be added to the thank you page and emails.', 'woocommerce' ),
                    'default'     => '',
                    'desc_tip'    => true,
                ),
            );

        }
    }
}

add_action( 'plugins_loaded', 'declare_koperasi_gateway_class' );

function add_your_gateway_class( $methods ) {
    $methods[] = 'WC_Gateway_Koperasi_Gateway'; 
    return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'add_your_gateway_class' );
