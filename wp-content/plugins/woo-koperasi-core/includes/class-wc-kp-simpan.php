<?php

class WC_KP_Simpan {
    private $user;

    public function __construct($user) {
        $this->user = $user;
    }

    public function output_page() {
        $user = $this->user;
        echo "Hellooo Simpan from a Class for user: ".$user->get('first_name').' '.$user->get('last_name');
    }
}