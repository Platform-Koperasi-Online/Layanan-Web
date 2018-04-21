<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_Gateway_KP_Payment_Bootstrapper {
    private $servername = "localhost";
    private $username = "virtualbank";
    private $password = "virtualbank";
    private $dbname = "virtualbank";

    function do_transaction($price, $email) {
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
        $sql = "UPDATE akun SET saldo = saldo - ".$price." WHERE email LIKE '".$email."'";
        if ($conn->query($sql) === TRUE) {
            kp_log_to_file( "Record updated successfully to akun ".$email);
        } else {
            kp_log_to_file( "Error updating record: " . $conn->error);
        }

        $conn->close();
    }
}