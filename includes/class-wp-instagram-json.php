<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.ilovesect.com/
 * @since      1.0.0
 *
 * @package    WP_Instagram_JSON
 * @subpackage WP_Instagram_JSON/includes
 */

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use Aws\CloudFront\CloudFrontClient;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WP_Instagram_JSON
 * @subpackage WP_Instagram_JSON/includes
 */
class WP_Instagram_JSON {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since  1.0.0
	 * @return void "description".
	 */
	public function __construct() {
		$this->generate_json_file();
	}

	/**
	 * Generate JSON file from object.
	 *
	 * @return void "description".
	 */
	public function generate_json_file() {
		$tname     = 'wp_instagram_json';
		$transient = get_transient( $tname );
		if ( empty( $transient ) ) {
			$user_account = get_option( 'wp_instagram_json_account_name' );
			$access_token = get_option( 'wp_instagram_json_access_token' );
			$count        = ( get_option( 'wp_instagram_json_count' ) ) ? (int) get_option( 'wp_instagram_json_count' ) : 20;
			$date         = date_i18n( 'Y/m/d H:i:s' );
			if ( $user_account && $access_token ) {
				$user_api_url = 'https://api.instagram.com/v1/users/search?q=' . $user_account . '&access_token=' . $access_token;
				$user_data    = json_decode( file_get_contents( $user_api_url ) );
				foreach ( $user_data->data as $user_data ) {
					if ( $user_account == $user_data->username ) {
						$user_id = $user_data->id;
					}
				}
				$api_url   = 'https://api.instagram.com/v1/users/' . $user_id . '/media/recent?access_token=' . $access_token . '&count=' . $count;
				$ig_data   = json_decode( file_get_contents( $api_url ), true );
				$date_data = array(
					'file_generate_datetime' => $date,
				);
				$res       = array_merge_recursive( $ig_data, $date_data );
				$obj       = json_encode( $res );

				$filepath = plugin_dir_path( dirname( __FILE__ ) ) . 'json/instagram.json';
				file_put_contents( $filepath, $obj );
			}
			set_transient( $tname, $date, 60 * get_option( 'wp_instagram_json_cache_time' ) );
			if ( wp_instagram_json_is_s3() && file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'json/instagram.json' ) ) {
				$this->put_object_to_s3();
				if ( wp_instagram_json_is_cf() ) {
					$this->run_cf_invalidation();
				}
			}
		}
	}

	/**
	 * Uploas JSON file to AWS S3.
	 *
	 * @return void "description".
	 */
	public function put_object_to_s3() {
		$sdkconfig  = [
			'region'      => get_option( 'wp_instagram_json_s3_region' ),
			'version'     => 'latest',
			'credentials' => [
				'key'    => get_option( 'wp_instagram_json_aws_credentials_key' ),
				'secret' => get_option( 'wp_instagram_json_aws_credentials_secret' ),
			],
		];
		$sdk        = new Aws\Sdk( $sdkconfig );
		$s3         = $sdk->createS3();
		$bucketname = get_option( 'wp_instagram_json_s3_bucket' );
		$keyname    = get_option( 'wp_instagram_json_s3_path' ) . '/instagram.json';
		$srcfile    = plugin_dir_path( dirname( __FILE__ ) ) . 'json/instagram.json';
		$result     = $s3->putObject([
			'Bucket'      => $bucketname,
			'Key'         => $keyname,
			'SourceFile'  => $srcfile,
			'ContentType' => 'application/json',
		]);
		$result     = $result->toArray();
		$object     = json_decode( json_encode( $result ) );
		$statuscode = $object->{'@metadata'}->statusCode;
		$fileexists = $s3->doesObjectExist( $bucketname, $keyname );
		if ( 200 === $statuscode && $fileexists ) {
			update_option( 'wp_instagram_json_s3_latest_upload_datetime', date_i18n( 'Y/m/d H:i:s' ) );
		}
	}

	/**
	 * Run AWS CloudFront Invalidation.
	 *
	 * @return void "description".
	 */
	public function run_cf_invalidation() {
		$client   = new \Aws\CloudFront\CloudFrontClient([
			'region'      => get_option( 'wp_instagram_json_s3_region' ),
			'version'     => '2016-01-28',
			'credentials' => [
				'key'    => get_option( 'wp_instagram_json_aws_credentials_key' ),
				'secret' => get_option( 'wp_instagram_json_aws_credentials_secret' ),
			],
		]);
		$filepath = '/' . get_option( 'wp_instagram_json_s3_path' ) . '/instagram.json';
		$paths    = [
			$filepath,
		];
		$result   = $client->createInvalidation([
			'DistributionId'    => get_option( 'wp_instagram_json_cf_distribution_id' ),
			'InvalidationBatch' => [
				'Paths'           => [
					'Quantity' => count( $paths ),
					'Items'    => $paths,
				],
				'CallerReference' => time(),
			],
		]);
	}
}
