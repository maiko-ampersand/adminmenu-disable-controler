<?php
/*
Plugin Name: Adminmenu Disable Controler
Plugin URI: http://google.com
Description: You are able to flexible authorized setting for Custom post types and Custom roles.
Version: 0.1
Author: Maiko Seino
Author URI: http://hoge.com
License: GPLv2
*/


class AdminmenuDisableControler {
	public static $plugin_name = 'Adminmenu Disable Controler';
	public static $plugin_fix = 'adminmenu-disable-controler';
	public static $table_name = 'adminmenu_disable_controler';

	public function _($str) {
		echo htmlspecialchars(__($str));
	}
	public function getMenuKey($menu_position) {
		global $wp_post_types;
		foreach ($wp_post_types as $key => $value) {
			if($value->menu_position == $menu_position) {
				return $key;
			}
		}
		return $menu_position;
	}
	public function AdminmenuDisableControler() {
		global $wpdb;
		define('CA_PLUGIN_TABLE_NAME', $wpdb->prefix . AdminmenuDisableControler::$table_name);

		add_action('admin_menu', 'adminmenu_disable_controler_menu');
		function adminmenu_disable_controler_menu() {
			add_options_page(__(AdminmenuDisableControler::$plugin_name), __(AdminmenuDisableControler::$plugin_name), 8, __FILE__, 'adminmenu_disable_controler_options');
		}
		function adminmenu_disable_controler_options() {
			include_once 'adminmenu-disable-controler-setting-page.php';
		}
		function regist_ajax_actions() {
			add_action( 'wp_ajax_adminmenu_disable_controler_read', 'adminmenu_disable_controler_read_callback' );
			function adminmenu_disable_controler_read_callback() {
				global $wpdb;
				if(!is_user_logged_in()) {
					die('read faild.');
				}

				$get_meta = $wpdb->get_results(
					'SELECT * FROM '.CA_PLUGIN_TABLE_NAME
				);
				echo json_encode($get_meta);
				die();
			}

			add_action( 'wp_ajax_adminmenu_disable_controler_regist', 'adminmenu_disable_controler_regist_callback' );
			function adminmenu_disable_controler_regist_callback() {
				global $wpdb;
				if(!is_user_logged_in()) {
					die('save faild.');
				}

				$wpdb->query("DELETE FROM ".CA_PLUGIN_TABLE_NAME);
				foreach ($_POST['values'] as $key => $value) {
					$chk = explode(',', $value);
					if(!isset($chk[2])) {
						$chk[2] = '';
					}
					if(!isset($chk[3])) {
						$chk[3] = '';
					}
					$wpdb->query(
						$wpdb->prepare('INSERT INTO '.CA_PLUGIN_TABLE_NAME.'(role_name ,disable_menu_key ,disable_submenu_key ,remove_cap) VALUES (%s ,%s ,%s ,%s);',$chk[0] ,$chk[1] ,$chk[2] ,$chk[3])
					);
				}
				die();
			}

			add_action( 'wp_ajax_adminmenu_disable_controler_reset', 'adminmenu_disable_controler_reset_callback' );
			function adminmenu_disable_controler_reset_callback() {
				global $wpdb;
				if(!is_user_logged_in()) {
					die('reset faild.');
				}

				$wpdb->query("DELETE FROM ".CA_PLUGIN_TABLE_NAME);
				die();
			}
		}
		regist_ajax_actions();

		function init_database(){
			global $wpdb;
			$table_name = $wpdb->prefix . AdminmenuDisableControler::$table_name;
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
				$sql = "CREATE TABLE " . $table_name . " (
					id bigint(20) NOT NULL AUTO_INCREMENT,
					role_name varchar(100) NOT NULL,
					disable_menu_key varchar(100) NOT NULL,
					disable_submenu_key varchar(100),
					remove_cap varchar(100),
					UNIQUE KEY id (id)
				);";

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			}
		}
		init_database();


		function set_admin_menu_disabled(){
			global $wpdb;
			global $menu;
			global $submenu;
			global $wp_post_types;
			global $wp_roles;
			if(!function_exists('wp_get_current_user')) {
			    include(ABSPATH . "wp-includes/pluggable.php"); 
			}
		    $user = wp_get_current_user();
		    $role = $user->roles[0];

		    if($role === 'administrator') {
		    	return;
		    }
			$settings = $wpdb->get_results(
		    	$wpdb->prepare('SELECT role_name , disable_menu_key , disable_submenu_key , remove_cap  FROM '.CA_PLUGIN_TABLE_NAME.' WHERE role_name = %s',$role)
			);


			// remove caps	
			foreach ($settings as $setting) {
				$_type = $wp_post_types[$setting->disable_menu_key];
				if (isset($_type->cap->{$setting->remove_cap})) {
					unset($_type->cap->{$setting->remove_cap});
				}
			}
			// remove menu	
			foreach ($settings as $setting) {
				if(!empty($setting->disable_submenu_key)) {
					unset($submenu[$menu[$setting->disable_menu_key][2]][$setting->disable_submenu_key]);
				}
			}
			foreach ($settings as $setting) {
				if(empty($setting->disable_submenu_key) && empty($setting->remove_cap)) {
					unset($menu[$setting->disable_menu_key]);
				}
			}
		}
		add_action( 'admin_menu', 'set_admin_menu_disabled', 999 );
	}
}

if (is_admin()) {
	$CA = new AdminmenuDisableControler();
}

?>