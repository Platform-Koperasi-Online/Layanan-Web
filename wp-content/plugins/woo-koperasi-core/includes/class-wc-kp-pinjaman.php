<?php

class WC_KP_Pinjaman {
    private $id;
    private $user;

    public function __construct($user = null) {
        $this->id = 'koperasi_pinjaman';
        $this->user = $user;
    }

    public function output_page() {
        echo '<h1>Hello Pinjaman<h1>';
    }

    public static function admin_page() {
        echo '<h1>Hello, Admin Pinjaman<h1>';
    }
}