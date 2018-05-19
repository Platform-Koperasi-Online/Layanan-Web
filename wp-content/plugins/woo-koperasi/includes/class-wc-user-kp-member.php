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
}