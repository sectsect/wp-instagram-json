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

use Aws\CloudFront\CloudFrontClient;

/**
 * Run AWS CloudFront Invalidation.
 *
 * @return void "description".
 */
function run_cf_invalidation() {
	$client = new \Aws\CloudFront\CloudFrontClient([
		'region'      => get_option( 'wp_instagram_json_s3_region' ),
		'version'     => '2016-01-28',
		'credentials' => [
			'key'     => get_option( 'wp_instagram_json_aws_credentials_key' ),
			'secret'  => get_option( 'wp_instagram_json_aws_credentials_secret' ),
		],
	]);
	$filepath = '/' . get_option( 'wp_instagram_json_s3_path' ) . '/instagram.json';
	$paths    = [
		$filepath,
	];
	$result   = $client->createInvalidation([
		'DistributionId'      => get_option( 'wp_instagram_json_cf_distribution_id' ),
		'InvalidationBatch'   => [
			'Paths'           => [
				'Quantity'    => count( $paths ),
				'Items'       => $paths,
			],
			'CallerReference' => time(),
		],
	]);
}
?>
<div class="wrap">
	<h1>Instagram Settings<span style="font-size: 10px; padding-left: 12px;">- For Instagram API -</span></h1>

	<?php if ( isset( $_POST['delete'] ) ) : ?>
		<?php
			global $wpdb;
			$result1 = $wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_wp_instagram_json%')" );
			$result2 = $wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_timeout_wp_instagram_json%')" );
			if ( wp_instagram_json_is_cf() ) {
				run_cf_invalidation();
			}
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

			<h2>AWS S3 Settings</h2>
			<hr>
			<table class="form-table">
				<tbody>
					<?php if ( get_option('wp_instagram_json_s3_enable') && get_option('wp_instagram_json_s3_latest_upload_datetime') ): ?>
					<tr>
						<th scope="row">
							<label><?php _e( 'Latest Upload', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<?php echo esc_html( get_option('wp_instagram_json_s3_latest_upload_datetime') ); ?>
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_s3_enable"><?php _e( 'S3 Upload', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<input type="checkbox" id="wp_instagram_json_s3_enable" class="regular-text" name="wp_instagram_json_s3_enable"<?php if ( get_option('wp_instagram_json_s3_enable') ): ?> checked<?php endif; ?> style="opacity: 0;">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_aws_credentials_key"><?php _e( 'AWS credentials key', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<input type="text" id="wp_instagram_json_aws_credentials_key" class="regular-text" name="wp_instagram_json_aws_credentials_key" value="<?php echo esc_html( get_option('wp_instagram_json_aws_credentials_key') ); ?>" style="width: 420px;">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_aws_credentials_secret"><?php _e( 'AWS credentials secret', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<input type="text" id="wp_instagram_json_aws_credentials_secret" class="regular-text" name="wp_instagram_json_aws_credentials_secret" value="<?php echo esc_html( get_option('wp_instagram_json_aws_credentials_secret') ); ?>" style="width: 420px;">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_s3_region"><?php _e( 'Region', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<?php
							$regions = array(
								'us-east-2' => 'us-east-2',
								'us-east-1' => 'us-east-1',
								'us-west-1' => 'us-west-1',
								'us-west-2' => 'us-west-2',
								'ap-south-1' => 'ap-south-1',
								'ap-northeast-2' => 'ap-northeast-2',
								'ap-southeast-1' => 'ap-southeast-1',
								'ap-southeast-2' => 'ap-southeast-2',
								'ap-northeast-1' => 'ap-northeast-1',
								'ca-central-1' => 'ca-central-1',
								'eu-central-1' => 'eu-central-1',
								'eu-west-1' => 'eu-west-1',
								'eu-west-2' => 'eu-west-2',
								'sa-east-1' => 'sa-east-1',
							);
							?>
							<select id="wp_instagram_json_s3_region" name="wp_instagram_json_s3_region" style="width: 150px;">
								<option value=""><?php _e( 'Select Region', 'wp_instagram_json' ); ?></option>
								<?php foreach ( $regions as $region ): ?>
									<?php $selected = ( get_option('wp_instagram_json_s3_region') == $region ) ? "selected" : ""; ?>
									<option value="<?php echo $region; ?>" <?php echo $selected; ?>><?php echo $region; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_s3_bucket"><?php _e( 'Bucket name', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<input type="text" id="wp_instagram_json_s3_bucket" class="regular-text" name="wp_instagram_json_s3_bucket" value="<?php echo esc_html( get_option('wp_instagram_json_s3_bucket') ); ?>" style="width: 150px;">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_s3_path"><?php _e( 'The Path on S3', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<code>{Bucket name}/</code> <input type="text" id="wp_instagram_json_s3_path" class="regular-text" name="wp_instagram_json_s3_path" value="<?php echo esc_html( get_option('wp_instagram_json_s3_path') ); ?>" placeholder="path/to" style="width: 170px;"> <code>/instagram.json</code>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_s3_custom_url"><?php _e( 'Custom URL (CDN)', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<input type="text" id="wp_instagram_json_s3_custom_url" class="regular-text" name="wp_instagram_json_s3_custom_url" value="<?php echo esc_html( get_option('wp_instagram_json_s3_custom_url') ); ?>" style="width: 420px;">
							<p style="font-size: 10px; color: #aaa;"><?php _e( 'Default:', 'wp_instagram_json' ); ?> https://{bucket}.s3.amazonaws.com</p>
							<p style="font-size: 10px; color: #aaa;"><?php _e( 'without Trailing Slash', 'wp_instagram_json' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>

			<h2>AWS CloudFront Settings</h2>
			<hr>
			<table class="form-table">
				<tbody>
					<?php
					if ( wp_instagram_json_is_s3() && get_option( 'wp_instagram_json_cf_distribution_id' ) ):
						$client = new \Aws\CloudFront\CloudFrontClient([
							'region'      => get_option( 'wp_instagram_json_s3_region' ),
							'version'     => '2016-01-28',
							'credentials' => [
								'key'     => get_option( 'wp_instagram_json_aws_credentials_key' ),
								'secret'  => get_option( 'wp_instagram_json_aws_credentials_secret' ),
							],
						]);
						$listresult = $client->listInvalidations([
							'DistributionId' => get_option( 'wp_instagram_json_cf_distribution_id' ),
						]);
						$items       = $listresult->search('InvalidationList.Items');
						$item        = reset($items);
						$time        = $item['CreateTime'];
						$wp_timezone = get_option( 'timezone_string' );
						$time->setTimezone( new DateTimeZone('Asia/Tokyo') );
						if ( get_option('wp_instagram_json_cf_enable') && $time->format('Y/m/d H:i:s') ):
					?>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_cf_enable"><?php _e( 'Latest Invalidation', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<?php echo $time->format('Y/m/d H:i:s'); ?>
						</td>
					</tr>
					<?php
						endif;
					endif;
					?>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_cf_enable"><?php _e( 'CloudFront Invalidation', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<input type="checkbox" id="wp_instagram_json_cf_enable" class="regular-text" name="wp_instagram_json_cf_enable"<?php if ( get_option('wp_instagram_json_cf_enable') ): ?> checked<?php endif; ?> style="opacity: 0;"<?php if ( ! wp_instagram_json_is_s3() ): ?> disabled="disabled"<?php endif; ?>>
							<p style="font-size: 10px; color: #aaa;"><?php _e( '"S3 Upload" must be enabled.', 'wp_instagram_json' ); ?></p>
							<p style="font-size: 10px; color: #aaa;"><?php _e( 'If this field is enable, the "CloudFront Invalidation" process runs in the "Delete Cache" process.', 'wp_instagram_json' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wp_instagram_json_cf_distribution_id"><?php _e( 'Distribution ID', 'wp_instagram_json' ); ?></label>
						</th>
						<td>
							<input type="text" id="wp_instagram_json_cf_distribution_id" class="regular-text" name="wp_instagram_json_cf_distribution_id" value="<?php echo esc_html( get_option('wp_instagram_json_cf_distribution_id') ); ?>" style="width: 170px;"<?php if ( ! wp_instagram_json_is_s3() ): ?> readonly="readonly"<?php endif; ?>>
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
	</section>
</div>
