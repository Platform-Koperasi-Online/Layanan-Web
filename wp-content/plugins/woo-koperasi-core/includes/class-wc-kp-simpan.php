<?php

class WC_KP_Simpan {
    private $id;
    private $user;

    public function __construct($user) {
        $this->id = 'koperasi_simpan';
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
		echo '<input type="submit" name="'.$this->id.'_button" value="Simpan!">';
		echo '</form>';
    }

    function simpan_forms() {
        echo '<div>';
        $form_input = self::get_form_fields();
        foreach ($form_input as $form_id => $label) {
            $name = 'woocommerce_'.$this->id.'_'.$form_id;
            echo'<p>
                <label for="'.$name.'">'.$label.'</label>
                <input class="input-text regular-input " name="'.$name.'" id="'.$name.'" style="" value="" placeholder="" type="text">
                </p>';
        }
        echo '</div>';
    }

    function get_form_fields() {
        return array(
            'test_1' => 'Test 1',
            'test_2' => 'Test 2'
        );
    }

    function submit_button_action() {
        echo '<h1>YO</h1>';
        echo '<p>the inputs are :</p>';
        $form_input = self::get_form_fields();
        foreach ($form_input as $form_id => $label) {
            $name = 'woocommerce_'.$this->id.'_'.$form_id;
            if (isset($_POST[$name])) {
                echo '<p> '.$label.' : '.$_POST[$name].'</p>';
            }
        }
    }
}