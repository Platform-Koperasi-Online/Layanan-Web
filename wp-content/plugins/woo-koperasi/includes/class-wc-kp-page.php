<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_KP_Page {
    protected $id;
    protected $user;
    
    protected function generate_text_form_html( $name, $form ) {
        $label = $form['label'];
        $default_value = $form['default'];
        echo'<p>
            <label for="'.$name.'">'.$label.'</label>
            <input class="input-text regular-input " name="'.$name.'" id="'.$name.'" style="" value="'.$default_value.'" placeholder="" type="text">
            </p>';
    }

    protected function generate_radio_form_html( $name, $form ) {
        $label = $form['label'];
        $values = $form['values'];
        echo'<div>
            <label for="'.$name.'">'.$label.'</label>';
        foreach ($values as $value => $value_label ) {
            echo'<p>';
            echo '<input class="input-text regular-input " name="'.$name.'" id="'.$name.'" style="" value="'.$value.'" placeholder="" type="radio">';
            echo $value_label;
            echo '</p>';
        }
        echo '</div>';
    }

    protected function generate_date_form_html( $name, $form) {
        $label = $form['label'];
        $default_value = $form['default'];
        echo'<p>
            <label for="'.$name.'">'.$label.'</label>
            <input class="input-text regular-input " name="'.$name.'" id="'.$name.'" style="" value="'.$default_value.'" placeholder="" type="date">
            </p>';
    }

    protected function get_form_name($form_id) {
        return 'woocommerce_'.$this->id.'_'.$form_id;
    }
}