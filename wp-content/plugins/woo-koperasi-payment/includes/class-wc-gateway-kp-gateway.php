<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_Gateway_KP_Gateway extends WC_Payment_Gateway {
    private $koperasi_bank_email;

    public function __construct() {
        //Gateway information
        $this->id = 'koperasi';
        $this->has_fields = true;
        $this->method_title = 'Koperasi Payment';
        $this->method_description = 'Woocommerce payment plugin for Platform Koperasi';

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables
        $this->title        = $this->get_option( 'title' );
        $this->description  = $this->get_option( 'description' );
        $this->instructions = $this->get_option( 'instructions' );
        $this->koperasi_bank_email = $this->get_option( 'koperasi_bank_email');

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
                'default'     => __( 'Koperasi Payment', 'woocommerce' ),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => __( 'Description', 'woocommerce' ),
                'type'        => 'textarea',
                'description' => __( 'Payment method description that the customer will see on your checkout.', 'woocommerce' ),
                'default'     => __( 'Payment method for Platform Koperasi.', 'woocommerce' ),
                'desc_tip'    => true,
            ),
            'instructions' => array(
                'title'       => __( 'Instructions', 'woocommerce' ),
                'type'        => 'textarea',
                'description' => __( 'Instructions that will be added to the thank you page and emails.', 'woocommerce' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'koperasi_bank_email' => array(
                'title'       => __( 'Akun Simpanan Koperasi', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( 'Akun bank untuk simpanan koperasi', 'woocommerce' ),
                'default'     => __( '', 'woocommerce' ),
            ),
        );

    }

    /**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {

        $order = wc_get_order( $order_id );
        $email = $order->get_billing_email();

        $price = $order->get_total();
		if ( $price > 0 ) {
            $bank = new WC_Gateway_KP_Payment_Bootstrapper($this->koperasi_bank_email);
            $bank->do_transaction($price, $email);
		}
        $order->payment_complete();

		// Reduce stock levels
		wc_reduce_stock_levels( $order_id );

		// Remove cart
		WC()->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result'    => 'success',
			'redirect'  => $this->get_return_url( $order ),
		);

    }
}