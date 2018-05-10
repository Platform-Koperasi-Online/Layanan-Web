<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_KP_Shortcodes {

	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'simpan'                    => __CLASS__ . '::simpan',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
    }

    public static function simpan() {
        $user = wp_get_current_user();
        $simpan = new WC_KP_Simpan($user);
        $simpan->output_page();
    }
}