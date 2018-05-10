<?php

class WC_KP_Pinjaman extends WC_KP_Page {    
    private static $PINJAMAN_LIMIT = 'pinjaman_limit';

    public function __construct($user = null) {
        $this->id = 'koperasi_pinjaman';
        $this->user = $user;
    }

    public static function admin_page() {
        echo '<h1>Hello, Admin Pinjaman<h1>';
    }

    public function output_page() {
        if (isset( $_POST['_wpnonce'] ) 
            && wp_verify_nonce( $_POST['_wpnonce'], $this->id.'_button_clicked' )
            && isset($_POST[$this->id.'_button'])) {
			self::submit_button_action();
		}
        $user = $this->user;
        echo '<p>Transaksi Pinjam untuk user: '.$user->get('first_name').' '.$user->get('last_name').'</p>';
        $this->pinjaman_status();
        $this->pinjaman_forms();
    }

    public function pinjaman_status() {
        echo '<div>';
        echo '<h3>Status </h3>';
        echo '<p>Limitasi user : ';
        echo '<b>';
        echo self::get_pinjaman_limit($this->user->ID, true);
        echo '</b>';
        echo '</div>';
    }

    public function pinjaman_forms() {
        echo '<div>';
        echo '<h3>Form Pinjaman</h3>';
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
		echo '<input type="submit" name="'.$this->id.'_button" value="Lakukan Pinjaman">';
        echo '</form>';
        echo '</div>';
    }

    function get_form_fields() {
        return array(
            'nilai_pinjaman' => array(
                'type' => 'text',
                'label' => 'Jumlah yang ingin dipinjam',
                'default' => 0
            )
        );
    }

    public static function get_pinjaman_limit($user_id, $return_label = false) {
        $limit = get_user_meta($user_id,self::$PINJAMAN_LIMIT,true);
        if ($return_label) {
            if ($limit = -1) {
                return 'unlimited';
            } else if ($limit = 0) {
                return 'restricted';
            } else {
                return 'limited to this amount : '.$limit;
            }
        } else {
            return $limit;
        }
    }

    function submit_button_action() {
        $user = $this->user;
        $form_input = self::get_form_fields();
        foreach ($form_input as $form_id => $form) {
            $name = $this->get_form_name( $form_id );
            $label = $form['label'];
            echo $label.' : '.$_POST[$name];
        }
    }

    public static function set_pinjaman_capability($user_id, $capability) {
        return update_user_meta( $user_id, self::$PINJAMAN_LIMIT, true);
    }
}