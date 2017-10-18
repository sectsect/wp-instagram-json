<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/sectsect/
 * @since      1.0.0
 *
 * @package    WP_Instagram_JSON
 * @subpackage WP_Instagram_JSON/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Instagram_JSON
 * @subpackage WP_Instagram_JSON/includes
 */

add_action('admin_menu', 'wp_instagram_json_menu');
/**
 * Add options page.
 *
 * @return void "description".
 */
function wp_instagram_json_menu() {
	add_menu_page('Instagram', 'Instagram', 5, 'instagram_menu', 'wp_instagram_json_options_page');
	add_action('admin_init', 'register_wp_instagram_json_settings');
}

/**
 * Register setting.
 *
 * @return void "description".
 */
function register_wp_instagram_json_settings() {
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_cache_time');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_count');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_account_name');
	register_setting('wp_instagram_json-settings-group', 'wp_instagram_json_access_token');
}

/**
 * Require file.
 *
 * @return void "description".
 */
function wp_instagram_json_options_page() {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/index.php';
}
