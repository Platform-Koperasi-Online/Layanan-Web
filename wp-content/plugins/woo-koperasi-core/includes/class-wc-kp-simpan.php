<?php

class WC_KP_Simpanan{
    private $id;
    private $user;

    public function __construct($user) {
        $this->id = 'koperasi_simpanan';
        $this->user = $user;
    }

    public function output_page() {
        if (isset( $_POST['_wpnonce'] ) 
            && wp_verify_nonce( $_POST['_wpnonce'], $this->id.'_button_clicked' )
            && isset($_POST[$this->id.'_button'])) {
			self::submit_button_action();
		}
        $user = $this->user;
        echo '<p>Transaksi Simpan untuk user: '.$user->get('first_name').' '.$user->get('last_name').'</p>';
		echo '<form method="post" id="mainform" action="" enctype="multipart/form-data">';
		self::simpan_forms();
		wp_nonce_field($this->id.'_button_clicked');
        echo '<input type="hidden" value="true" name="'.$this->id.'_button" />';
		echo '<input type="submit" name="'.$this->id.'_button" value="Simpanan">';
		echo '</form>';
    }

    function simpan_forms() {
        echo '<div>';
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
                'label' => 'Jumlah yang ingin disimpan'
            )
        );
    }

    function submit_button_action() {
        $user = $this->user;
        $nilai_simpanan_lama = WC_User_KP_Member::get_simpanan_member($user->ID);
        $nilai_simpanan_tambah = $_POST[$this->get_form_name('nilai_simpanan')];
        WC_User_KP_Member::add_simpanan_member($user->ID,$nilai_simpanan_tambah);
        $nilai_simpanan_baru = WC_User_KP_Member::get_simpanan_member($user->ID);
        echo '<p>lama : <b>' . $nilai_simpanan_lama . '</b> menjadi baru : <b>'.$nilai_simpanan_baru.'</b>';
        $form_input = self::get_form_fields();
        foreach ($form_input as $form_id => $form) {
            $name = $this->get_form_name( $form_id );
            $label = $form['label'];
            if (isset($_POST[$name])) {
                echo '<p> '.$label.' : '.$_POST[$name].'</p>';
            }
        }
    }

    function generate_text_form_html( $name, $form ) {
        $label = $form['label'];
        echo'<p>
            <label for="'.$name.'">'.$label.'</label>
            <input class="input-text regular-input " name="'.$name.'" id="'.$name.'" style="" value="" placeholder="" type="text">
            </p>';
    }

    function generate_radio_form_html( $name, $form ) {
        $label = $form['label'];
        $values = $form['values'];
        echo'<p>
            <label for="'.$name.'">'.$label.'</label>';
        foreach ($values as $value => $value_label ) {
            echo '<input class="input-text regular-input " name="'.$name.'" id="'.$name.'" style="" value="'.$value.'" placeholder="" type="radio">';
            echo $value_label;
        }
        echo '</p>';
    }

    function get_form_name($form_id) {
        return 'woocommerce_'.$this->id.'_'.$form_id;
    }
}