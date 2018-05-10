<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_KP_Shortcodes {

	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'simpanan'                    => __CLASS__ . '::simpanan',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
    }

    public static function simpanan() {
        $user = wp_get_current_user();
        $simpanan= new WC_KP_Simpanan($user);
        $simpanan->output_page();
    }
}