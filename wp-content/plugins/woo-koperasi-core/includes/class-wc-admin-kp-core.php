<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_Admin_KP_Core_Plugin extends WC_Settings_API {
    private $koperasi_bank_email;

    public function __construct() {
        $this->id = 'koperasi_core';
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 1000 );
    }

    /**
	 * Add menu items.
	 */
	public function admin_menu() {
		// Load the settings.
        $this->init_form_fields();
		add_submenu_page( 'woocommerce', __( 'Koperasi', 'woocommerce' ), __( 'Koperasi', 'woocommerce' ), 'manage_woocommerce', 'koperasi_core', array( $this, 'koperasi_core_page' ) );
    }
    
    /**
	 * Init the koperasi page.
	 */
	public function koperasi_core_page() {
		$koperasi_data = self::get_data();
		echo "<h1> Hello, Koperasi </h1>";
		echo '<div class="wrap woocommerce">';
		self::output_status($koperasi_data['status']);
		self::output_dummy_anggota_page($koperasi_data['customers']);
		self::output_settings();
		echo '</div>';
	}
	
	public function get_data() {
		$data =  array(
			'status' => array(
				'Anggota Koperasi' => array(
					'Jumlah anggota' => 5,
				),
				'Simpanan Koperasi' => array(
					'Saldo' => 5,
				)
			),
			'customers' => self::get_filtered_customers_data(),
		);
		return $data;
	}

	public function get_filtered_customers_data() {
		$users = get_users();
		$filtered_customers = array();
		foreach ($users as $user) {
			$customer_id = $user->ID;
			$filtered_customers[$customer_id] = self::filter_customer_data_from($user);
		}
		return $filtered_customers;
	}

	public function filter_customer_data_from($user) {
		$customer = new WC_Customer($user->ID);
		return array(
			'name' => $customer->get_first_name() . ' ' . $customer->get_last_name(),
			'is_a_koperasi_member' => WC_User_KP_Member::is_a_member($customer->get_id()),
			'simpanan_koperasi' => WC_User_KP_Member::get_simpanan_member($customer->get_id()),
			'total_spent' => $customer->get_total_spent()
		);
	}

	/**
	 * Output the status.
	 */
	public function output_status($status) {
		foreach ($status as $status_header => $status_info) {
			echo '
			<table class="wc_status_table widefat" cellspacing="0" style="width:50%;table-layout:fixed">
				<col style="width:10%" span="5"/>
				<thead>
					<tr>
						<th colspan="5"><h2>'.$status_header.'</h2></th>
					</tr>
				</thead>
				<tbody>';
			foreach ($status_info as $key => $value) {
				echo'	
					<tr>
						<td colspan="2">'.$key.'</td>
						<td colspan="3">'.$value.'</td>
					</tr>
				';
			}
			echo '</tbody></table>';
		}
	}

	public function output_dummy_anggota_page($customers) {
		echo "<h2>Cek customer</h2>";
		echo '
			<table class="wc_status_table widefat" cellspacing="0" style="width:70%;table-layout:fixed">
				<col style="width:10%" span="7"/>
				<thead>
					<tr>
						<th colspan="2"><h2> Nama customer</h2></th>
						<th colspan="1"><h2> Koperasi Member? </h2></th>
						<th colspan="2"><h2> Simpanan </h2></th>
						<th colspan="2"><h2> Total Pembelian </h2></th>
					</tr>
				</thead>
				<tbody>';
		foreach ($customers as $customer_id => $customer_data) {
			echo '
			<tr>
				<td colspan="2">'.$customer_data['name'].' </td>
				<td colspan="1">'.self::get_member_text($customer_data['is_a_koperasi_member']).' </td>
				<td colspan="2">'.$customer_data['simpanan_koperasi'].' </td>
				<td colspan="2">'.$customer_data['total_spent'].' </td>
			</tr>';
		}
		echo '</tbody></table>';
	}

	private function get_member_text($is_member) {
		if ($is_member) {
			return 'yes';
		} else {
			return 'no';
		}
	}

	/**
	 * Output the settings.
	 */
	public function output_settings() {
		
		// Check whether the button has been pressed AND also check the nonce
		if (isset($_POST[$this->id.'_button']) && check_admin_referer($this->id.'_button_clicked')) {
			self::submit_button_action();
		}

		echo "<h2>Settings</h2>";
		echo '<form method="post" id="mainform" action="" enctype="multipart/form-data">';
		self::admin_options();
		wp_nonce_field($this->id.'_button_clicked');
  		echo '<input type="hidden" value="true" name="'.$this->id.'_button" />';
		echo get_submit_button();
		echo '</form>';
	}

	public function submit_button_action() {
		$options_to_save = array();
		foreach ($this->form_fields as $key) {
			$options_to_save[$key] = $_POST['woocommerce_'.$this->id.'_'.$key];
		}
		update_option('woocommerce_'.$this->id.'_settings',$options_to_save);
	}

	/**
     * Initialise Settings Form Fields.
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'koperasi_bank_email' => array(
                'title'       => __( 'Email Akun Bank Simpanan Koperasi', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( 'email untuk tujuan transaksi', 'woocommerce' ),
                'default'     => __( '', 'woocommerce' )
            ),
            'setting2' => array(
                'title'       => __( 'Setting 2', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( 'another setting', 'woocommerce' ),
                'default'     => __( 'nothing here yet just a default value', 'woocommerce' )
            ),
        );

	}
	
	/**
	 * Generate Text Input HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  1.0.0
	 * @return string
	 */
	public function generate_textinfo_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'disabled'          => true,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<?php echo $this->get_tooltip_html( $data ); ?>
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( $this->get_option( $key ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}
}