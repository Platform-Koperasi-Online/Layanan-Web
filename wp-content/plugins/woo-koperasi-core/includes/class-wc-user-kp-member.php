<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_User_KP_Member {
    private static $IS_MEMBER_KEY = 'is_a_koperasi_member';


    public static function is_a_member($user_id) {
        return get_user_meta($user_id,self::$IS_MEMBER_KEY,true) == 'true';
    }

    public static function register_as_member($user_id) {
        update_user_meta($user_id,self::$IS_MEMBER_KEY,'true',true);
    }

    public static function deregister_as_member($user_id) {
        update_user_meta($user_id,self::$IS_MEMBER_KEY,'false',true);
    }

    public static function get_simpanan_member($user_id, $type = 'pokok') {
        if (self::is_simpanan_type_correct($type)) {
            global $wpdb;
            $table_name = $wpdb->prefix.'kp_simpanan';
            $sum = $wpdb->get_var("SELECT SUM(nilai_simpanan) FROM $table_name WHERE user_id = $user_id AND tipe_simpanan = \"$type\"");
            if (is_numeric($sum)) {
                return $sum; 
            } else {
                return 0;
            }
        }
    }

    public static function add_simpanan_member($user_id, $value, $type = 'pokok') {
        if (self::is_simpanan_type_correct($type)) {
            $value_to_insert = array( 
                'user_id' => $user_id,
                'tipe_simpanan' => $type, 
                'nilai_simpanan' => $value,
                'waktu' => date("Y-m-d H:i:s")
            );

            global $wpdb;
            $table_name = $wpdb->prefix.'kp_simpanan';
            $wpdb->insert( 
                $table_name, 
                $value_to_insert
            );
        }
    }

    public static function is_simpanan_type_correct($type) {
        return $type == 'wajib' || $type == 'pokok' || $type == 'sukarela';
    }
}