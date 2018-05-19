<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WC_KP_Core {
	private $koperasi_bank_email;
	protected $data;

    public function __construct() {
		$this->id = 'koperasi_core';
		self::init_koperasi_database();
		$classes_to_init = array(
			'WC_KP_Admin',
			'WC_KP_Shortcodes'
		);
		foreach ($classes_to_init as $class) {
			
		add_action( 'init', array( $class, 'init' ) );
		}
	}
	
	private function init_koperasi_database() {
		global $wpdb;

		$table_name = $wpdb->prefix.'kp_simpanan';
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			//table not in database. Create new table
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id_simpanan mediumint(9) NOT NULL AUTO_INCREMENT,
				user_id int NOT NULL,
				tipe_simpanan text NOT NULL,
				nilai_simpanan numeric(19,4) NOT NULL,
				waktu datetime NOT NULL,
				PRIMARY KEY (id_simpanan)
			) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$table_name = $wpdb->prefix.'kp_pinjaman';
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			//table not in database. Create new table
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id_pinjaman mediumint(9) NOT NULL AUTO_INCREMENT,
				user_id int NOT NULL,
				nilai_pinjaman numeric(19,4) NOT NULL,
				batas_akhir date NOT NULL,
				status_pinjaman text NOT NULL,
				PRIMARY KEY (id_pinjaman)
			) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$table_name = $wpdb->prefix.'kp_periode';
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			//table not in database. Create new table
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id_periode mediumint(9) NOT NULL AUTO_INCREMENT,
				awal_periode datetime NOT NULL,
				akhir_periode datetime,
				PRIMARY KEY (id_periode)
			) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}
}