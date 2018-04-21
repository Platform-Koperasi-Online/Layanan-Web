<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_Admin_KP_Core_Plugin extends WC_Settings_API {
    private $koperasi_bank_email;

    public function __construct() {
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
		self::output_settings();
		echo '</div>';
	}
	
	public function get_data() {
		$data =  array(
			'status' => array(
				'Top level' => array(
					'hey' => 5,
					'hgd' => 142,
					'h2d' => 1322,
				),
				'another Top level' => array(
					'hey' => 5,
					'hgd' => 142,
					'h2d' => 1322,
				)
			)
		);
		return $data;
	}

	/**
	 * Output the status.
	 */
	public function output_status($status) {
		foreach ($status as $status_header => $status_info) {
			echo '
			<table class="wc_status_table widefat" cellspacing="0">
				<thead>
					<tr>
						<th colspan="3"><h2>'.$status_header.'</h2></th>
					</tr>
				</thead>
				<tbody>';
			foreach ($status_info as $key => $value) {
				echo'	
					<tr>
						<td>'.$key.'</td>
						<td>'.$value.'</td>
					</tr>
				';
			}
			echo '</tbody></table>';
		}
	}

	/**
	 * Output the settings.
	 */
	public function output_settings() {
		echo "<h2>Settings</h2>";
		echo '<form method="post" id="mainform" action="" enctype="multipart/form-data">';
		self::admin_options();
		echo self::output_submit_button();
		echo '</form>';
	}

	public function output_submit_button() {
		echo '
		<p class="submit">
			<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Save changes">Save changes</button>
		</p>';
	}

	/**
     * Initialise Settings Form Fields.
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'setting1' => array(
                'title'       => __( 'Setting 1', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( 'a setting', 'woocommerce' ),
                'default'     => __( 'nothing here yet just a default value', 'woocommerce' )
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