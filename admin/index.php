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
 * @subpackage WP_Instagram_JSON/admin
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
 * @subpackage WP_Instagram_JSON/admin
 */
?>
<div class="wrap">
	<h1>Instagram Settings<span style="font-size: 10px; padding-left: 12px;">- For Instagram API -</span></h1>

	<?php if ( isset( $_POST['delete'] ) ) : ?>
		<?php
			global $wpdb;
			$result1 = $wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_wp_instagram_json%')" );
			$result2 = $wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_timeout_wp_instagram_json%')" );
			if ( $result1 && $result2 ) :
		?>
			<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
				<p><strong><?php _e( 'Cache deleted.', 'wp_instagram_json' ); ?></strong></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>
			</div>
		<?php else: ?>
			<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
				<p><strong><?php _e( 'Currently no cache.', 'wp_instagram_json' ); ?></strong></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>
			</div>
		<?php endif; ?>
	<?php elseif ( isset( $_GET['settings-updated'] ) ) : ?>
		<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
			<p><strong><?php _e( 'Changes saved.' ); ?></strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>
		</div>
	<?php endif; ?>

	<section>
		<form method="post" action="">
			<table class="form-table">
	            <tbody>
	                <tr>
	                    <th scope="row">
	                        <label for="wmp_range" style="font-size: 14px; margin: 0;"><?php _e( 'Delete Cache', 'wp_instagram_json' ); ?></label>
	                    </th>
	                    <td>
	                    	<input name="delete" class="button button-primary" type="submit" value="Delete Cache" onClick="window.alert('<?php _e( 'Delete Cache?', 'wp_instagram_json' ); ?>')" >
	                    </td>
	                </tr>
	            </tbody>
	        </table>
		</form>
	</section>

	<section>
		<form method="post" action="options.php">
	        <?php
	            settings_fields( 'wp_instagram_json-settings-group' );
	            do_settings_sections( 'wp_instagram_json-settings-group' );
	        ?>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_cache_time"><?php _e( 'Cache Expire (min)', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<input type="text" id="wp_instagram_json_cache_time" class="regular-text" name="wp_instagram_json_cache_time" value="<?php echo esc_html( get_option('wp_instagram_json_cache_time') ); ?>" style="width: 60px;"> <?php _e( 'mins', 'wp_instagram_json' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_count"><?php _e( 'Count', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<input type="number" min="1" max="20" id="wp_instagram_json_count" class="regular-text" name="wp_instagram_json_count" value="<?php echo esc_html( get_option('wp_instagram_json_count') ); ?>" style="width: 60px;">  <?php _e( '(Range: 1 - 20)', 'wp_instagram_json' ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_account_name"><?php _e( 'Account Name', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<input type="text" id="wp_instagram_json_account_name" class="regular-text" name="wp_instagram_json_account_name" value="<?php echo esc_html( get_option('wp_instagram_json_account_name') ); ?>" style="width: 150px;">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_access_token"><?php _e( 'Access Token', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<input type="text" id="wp_instagram_json_access_token" class="regular-text" name="wp_instagram_json_access_token" value="<?php echo esc_html( get_option('wp_instagram_json_access_token') ); ?>" style="width: 420px;">
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
	</section>
</div>
