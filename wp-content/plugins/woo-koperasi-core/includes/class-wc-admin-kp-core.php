<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_Admin_KP_Core_Plugin extends WC_Settings_API {
	private $koperasi_bank_email;

    public function __construct() {
		$this->id = 'koperasi_core';
		self::init_koperasi_data();
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 1000 );
		add_action( 'init', array( 'WC_KP_Shortcodes', 'init' ) );
	}
	
	private function init_koperasi_data() {
		global $wpdb;

		$table_name = $wpdb->prefix.'koperasi';
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			//table not in database. Create new table
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				kp_key text NOT NULL,
				kp_value text NOT NULL,
				PRIMARY KEY (id)
			) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$values_to_update = array(
			array( 
				'kp_key' => 'simpanan_dari_donasi', 
				'kp_value' => 0
			),
			array( 
				'kp_key' => 'dummy', 
				'kp_value' => 100
			),
		);

		foreach ($values_to_update as $index => $value) {
			$kp_key = $value['kp_key'];
			if ($wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE kp_key = \"$kp_key\"") == 0) {
				$wpdb->insert( 
					$table_name, 
					$value
				);
			}
		}
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
		// Check whether the button has been pressed AND also check the nonce
		if (isset($_POST[$this->id.'_button']) && check_admin_referer($this->id.'_button_clicked')) {
			self::submit_button_action();
		}
		$koperasi_data = self::get_data();
		echo "<h1> Hello, Koperasi </h1>";
		echo '<div class="wrap woocommerce">';
		self::output_status($koperasi_data['status']);
		self::output_dummy_anggota_page($koperasi_data['customers'],$koperasi_data['shu_data']);
		self::output_settings();
		echo '</div>';
	}
	
	public function get_data() {
		$customer_data = self::get_filtered_customers_data();
		$simpanan_dari_anggota = self::count_simpanan($customer_data);
		$simpanan_dari_donasi = self::get_simpanan_donasi($customer_data);
		$shu_simulasi = $this->get_option('shu_simulasi');
		$total_penjualan_simulasi = $this->get_option('total_penjualan_simulasi');
		$persen_jasa_modal = $this->get_option('persen_jasa_modal');
		$persen_jasa_usaha = $this->get_option('persen_jasa_usaha');
		$data =  array(
			'status' => array(
				'Anggota Koperasi' => array(
					'Jumlah anggota' => self::count_anggota($customer_data),
				),
				'Simpanan Koperasi' => array(
					'Simpanan dari anggota' => wc_price($simpanan_dari_anggota),
					'Simpanan dari donasi' => wc_price($simpanan_dari_donasi),
					'Total' => wc_price($simpanan_dari_anggota + $simpanan_dari_donasi)
				),
				'Simulasi Perhitungan SHU' => array(
					'SHU Simulasi' => wc_price($shu_simulasi),
					'Total Penjualan Simulasi' => wc_price($total_penjualan_simulasi),
					'Persen Jasa Modal' => $persen_jasa_modal . ' %',
					'Persen Jasa Usaha' => $persen_jasa_usaha . ' %',
				)
			),
			'customers' => $customer_data,
			'shu_data' => array (
				'simpanan_total' => $simpanan_dari_anggota + $simpanan_dari_donasi,
				'shu_simulasi' => $shu_simulasi,
				'total_penjualan_simulasi' => $total_penjualan_simulasi,
				'persen_jasa_modal' => $persen_jasa_modal,
				'persen_jasa_usaha' => $persen_jasa_usaha,
			)
		);
		return $data;
	}

	private function get_simpanan_donasi($customer_data) {
		$simpanan = 0;
		foreach ($customer_data as $id => $data) {
			if (WC_User_KP_Member::is_a_member($id)) {
				$simpanan += WC_User_KP_Member::get_simpanan_member($id,'sukarela');
			}
		}
		return $simpanan;
	}

	private function count_anggota($customer_data) {
		$count = 0;
		foreach ($customer_data as $id => $data) {
			if (WC_User_KP_Member::is_a_member($id)) {
				$count++;
			}
		}
		return $count;
	}

	private function count_simpanan($customer_data) {
		$simpanan = 0;
		foreach ($customer_data as $id => $data) {
			if (WC_User_KP_Member::is_a_member($id)) {
				$simpanan += WC_User_KP_Member::get_simpanan_member($id);
			}
		}
		return $simpanan;
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

	public function output_dummy_anggota_page($customers,$shu_data) {
		$shu_simulasi = $shu_data['shu_simulasi'];
		$persen_jasa_modal = $shu_data['persen_jasa_modal'];
		$persen_jasa_usaha = $shu_data['persen_jasa_usaha'];
		$total_penjualan_simulasi = $shu_data['total_penjualan_simulasi'];
		$simpanan = $shu_data['simpanan_total'];
		

		echo "<h2>Cek customer</h2>";
		echo '
			<table class="wc_status_table widefat" cellspacing="0" style="width:70%;table-layout:fixed">
				<col style="width:10%" span="7"/>
				<thead>
					<tr>
						<th colspan="1"><h2> Nama customer</h2></th>
						<th colspan="1"><h2> Koperasi Member? </h2></th>
						<th colspan="1"><h2> Simpanan </h2></th>
						<th colspan="1"><h2> Total Pembelian </h2></th>
						<th colspan="1"><h2> Dari Jasa Modal </h2></th>
						<th colspan="1"><h2> Dari Jasa Usaha </h2></th>
						<th colspan="1"><h2> Total Yang Didapat </h2></th>
					</tr>
				</thead>
				<tbody>';
		foreach ($customers as $customer_id => $customer_data) {
			$dari_jasa_modal = self::calculate_yang_didapat($simpanan, $shu_simulasi, $persen_jasa_modal, $customer_data['simpanan_koperasi']);
			$dari_jasa_usaha = self::calculate_yang_didapat($total_penjualan_simulasi, $shu_simulasi, $persen_jasa_usaha, $customer_data['total_spent']);
			echo '
			<tr>
				<td colspan="1">'.$customer_data['name'].' </td>
				<td colspan="1">'.self::get_member_text($customer_data['is_a_koperasi_member']).' </td>
				<td colspan="1">'.wc_price($customer_data['simpanan_koperasi']).' </td>
				<td colspan="1">'.wc_price($customer_data['total_spent']).' </td>
				<td colspan="1">'.wc_price($dari_jasa_modal).' </td>
				<td colspan="1">'.wc_price($dari_jasa_usaha).' </td>
				<td colspan="1">'.wc_price($dari_jasa_modal + $dari_jasa_usaha).' </td>
			</tr>';
		}
		echo '</tbody></table>';
	}

	private function calculate_yang_didapat($basis, $shu_simulasi, $persen, $nilai_yang_dikalkulasi) {
		return ((($persen/100) * $shu_simulasi)/$basis) * $nilai_yang_dikalkulasi ;
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
		foreach ($this->form_fields as $key => $value) {
			$options_to_save[$key] = sanitize_text_field($_POST['woocommerce_'.$this->id.'_'.$key]);
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
            'shu_simulasi' => array(
                'title'       => __( 'SHU Simulasi', 'woocommerce' ),
                'type'        => 'price',
                'description' => __( 'nilai SHU untuk dihitung', 'woocommerce' ),
                'default'     => __( '10000000', 'woocommerce' )
			),
			'total_penjualan_simulasi' => array(
                'title'       => __( 'Total Penjualan Simulasi', 'woocommerce' ),
                'type'        => 'price',
                'description' => __( 'nilai Total Penjualan untuk dihitung', 'woocommerce' ),
                'default'     => __( '35000000', 'woocommerce' )
			),
			'persen_jasa_modal' => array(
                'title'       => __( 'Persen Jasa Modal', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( '(%)', 'woocommerce' ),
                'default'     => __( '20', 'woocommerce' )
			),
			'persen_jasa_usaha' => array(
                'title'       => __( 'Persen Jasa Usaha', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( '(%)', 'woocommerce' ),
                'default'     => __( '15', 'woocommerce' )
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