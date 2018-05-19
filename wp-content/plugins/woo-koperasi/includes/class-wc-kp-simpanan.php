<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_KP_Simpanan extends WC_KP_Page {

    public function __construct($user = null) {
        $this->id = 'koperasi_simpanan';
        $this->user = $user;
    }

    public static function admin_page() {
        echo '<h1>Hello, Admin Simpanan<h1>';
        echo '<div>';
        echo '<h3>Semua Simpanan</h3>';
        $all_simpanan = self::get_all_simpanan();
        if ($all_simpanan != null) {
            foreach ($all_simpanan as $key => $pinjaman) {
                echo '<p>';
                echo $key.' -> ';
                print_r($pinjaman);
                echo '</p>';
            }
        }
        echo '</div>';
    }

    public function output_page() {
        if (isset( $_POST['_wpnonce'] ) 
            && wp_verify_nonce( $_POST['_wpnonce'], $this->id.'_button_clicked' )
            && isset($_POST[$this->id.'_button'])) {
			self::submit_button_action();
		}
        $user = $this->user;
        echo '<p>Transaksi Simpan untuk user: '.$user->get('first_name').' '.$user->get('last_name').'</p>';
        $this->simpanan_status();
        $this->simpanan_forms();
    }

    function simpanan_status() {
        echo '<div>';
        echo '<h3>Status Simpanan</h3>';
        echo '<p>Simpanan pokok : ';
        echo '<b>';
        echo self::get_simpanan_member($this->user->ID);
        echo '</b>';
        echo '</p>';
        echo '<p>Simpanan sukarela : ';
        echo '<b>';
        echo self::get_simpanan_member($this->user->ID,'sukarela');
        echo '</b>';
        echo '</p>';
        echo '</div>';
    }

    function simpanan_forms() {
        echo '<div>';
        echo '<h3>Form Simpanan</h3>';
        echo '<form method="post" id="mainform" action="" enctype="multipart/form-data">';
        $form_input = self::get_form_fields();
        foreach ($form_input as $form_id => $form) {
            $name = 'woocommerce_'.$this->id.'_'.$form_id;
            $type = $form['type'];
            if ( method_exists( $this, 'generate_' . $type . '_form_html' ) ) {
				$this->{'generate_' . $type . '_form_html'}( $name, $form );
			} else {
				$this->generate_text_form_html( $name, $form );
            }
        }
        wp_nonce_field($this->id.'_button_clicked');
        echo '<input type="hidden" value="true" name="'.$this->id.'_button" />';
		echo '<input type="submit" name="'.$this->id.'_button" value="Lakukan Simpanan">';
        echo '</form>';
        echo '</div>';
    }

    function get_form_fields() {
        return array(
            'tipe_simpanan' => array(
                'type' => 'radio',
                'label' => 'Tipe simpanan',
                'values' => array(
                    'pokok' => 'Pokok',
                    'sukarela' => 'Sukarela'
                )
            ), 
            'nilai_simpanan' => array(
                'type' => 'text',
                'label' => 'Jumlah yang ingin disimpan',
                'default' => 0
            )
        );
    }

    function submit_button_action() {
        $user = $this->user;
        $nilai_simpanan = $_POST[$this->get_form_name('nilai_simpanan')];
        $tipe_simpanan = $_POST[$this->get_form_name('tipe_simpanan')];
        self::add_simpanan_member($user->ID,$nilai_simpanan,$tipe_simpanan);
    }

    public static function get_simpanan_member($user_id, $type = 'pokok') {
        if (self::is_simpanan_type_correct($type)) {
            global $wpdb;
            $table_name = $wpdb->prefix.'kp_simpanan';
            $sum = $wpdb->get_var("SELECT SUM(nilai_simpanan) FROM $table_name WHERE user_id = $user_id AND tipe_simpanan = \"$type\"");
            if (is_numeric($sum)) {
                return $sum; 
            } else {
                return 0;
            }
        }
    }

    public static function add_simpanan_member($user_id, $value, $type = 'pokok') {
        if (self::is_simpanan_type_correct($type)) {
            $value_to_insert = array( 
                'user_id' => $user_id,
                'tipe_simpanan' => $type, 
                'nilai_simpanan' => $value,
                'waktu' => date("Y-m-d H:i:s")
            );

            global $wpdb;
            $table_name = $wpdb->prefix.'kp_simpanan';
            $wpdb->insert( 
                $table_name, 
                $value_to_insert
            );
        }
    }

    public static function is_simpanan_type_correct($type) {
        return $type == 'wajib' || $type == 'pokok' || $type == 'sukarela';
    }

    public static function get_all_simpanan($type = null) {
        if ($type == null) {
            global $wpdb;
            $table_name = $wpdb->prefix.'kp_simpanan';
            return $wpdb->get_results("SELECT id_simpanan, user_id, tipe_simpanan, nilai_simpanan, waktu FROM $table_name");
        } else {
            global $wpdb;
            $table_name = $wpdb->prefix.'kp_simpanan';
            return $wpdb->get_results("SELECT id_simpanan, user_id, tipe_simpanan, nilai_simpanan, waktu FROM $table_name WHERE tipe_simpanan = \"$type\"");
        }
    }
}