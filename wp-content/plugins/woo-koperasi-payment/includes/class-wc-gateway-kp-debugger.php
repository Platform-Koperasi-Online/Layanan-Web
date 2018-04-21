<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/* Log to File
* Description: Log into system php error log, usefull for Ajax and stuff that FirePHP doesn't catch
*/
function kp_log_to_file( $msg)
{
    static $logger;
    if ( ! isset( $logger ) ) {
		$logger = wc_get_logger();
    }
    $log = "[KP]  |  " . $msg . "\n";
    $logger->debug($log);
}