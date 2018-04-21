<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_Gateway_KP_Bank_Bootstrapper {
    private $servername = "localhost";
    private $username = "virtualbank";
    private $password = "virtualbank";
    private $dbname = "virtualbank";

    private $receiver_email;

    function __construct($receiver_email) {
        $this->receiver_email = $receiver_email;
    }

    function do_transaction($price, $sender_email) {
        $conn = new mysqli(
            $this->servername, 
            $this->username, 
            $this->password, 
            $this->dbname);

        // Check connection
        if ($conn->connect_error) {
            kp_log_to_file( "Failed to connect to MySQL: " .  $conn->connect_error);
        }

        // Perform queries
        $sql = "UPDATE akun SET saldo = saldo - ".$price." WHERE email LIKE '".$sender_email."'";
        if ($conn->query($sql) === TRUE) {
            kp_log_to_file( "Saldo of ".$price." successfully substracted from akun ".$sender_email);
        } else {
            kp_log_to_file( "Error updating record: " . $conn->error);
        }
        $receiver_email = $this->receiver_email;
        $sql = "UPDATE akun SET saldo = saldo + ".$price." WHERE email LIKE '".$receiver_email."'";
        if ($conn->query($sql) === TRUE) {
            kp_log_to_file( "Saldo of ".$price." successfully added to akun ".$receiver_email);
        } else {
            kp_log_to_file( "Error updating record: " . $conn->error);
        }

        $conn->close();
    }
}