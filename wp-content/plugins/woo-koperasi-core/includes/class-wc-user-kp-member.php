<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_User_KP_Member {
    private static $IS_MEMBER_KEY = 'is_a_koperasi_member';
    private static $SIMPANAN_KEY = 'simpanan_koperasi';

    public static function is_a_member($user_id) {
        return get_user_meta($user_id,self::$IS_MEMBER_KEY,true) == 'true';
    }

    public static function register_as_member($user_id) {
        update_user_meta($user_id,self::$IS_MEMBER_KEY,'true',true);
    }

    public static function deregister_as_member($user_id) {
        update_user_meta($user_id,self::$IS_MEMBER_KEY,'false',true);
    }

    public static function get_simpanan_member($user_id) {
        $value = get_user_meta($user_id, self::$SIMPANAN_KEY,true);
        if ($value == '') {
            return 0;
        } else {
            return $value;
        }
    }
}