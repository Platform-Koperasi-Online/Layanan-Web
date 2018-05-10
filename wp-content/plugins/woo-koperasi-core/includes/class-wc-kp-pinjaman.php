<?php

class WC_KP_Pinjaman {
    private $id;
    private $user;

    public function __construct($user) {
        $this->id = 'koperasi_pinjaman';
        $this->user = $user;
    }

    public function output_page() {
        echo 'Hello Pinjaman';
    }
}