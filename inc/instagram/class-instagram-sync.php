<?php
/**
 * Instagram Synchronization & Caching Manager.
 *
 * @package Custom_Theme
 * @subpackage Instagram
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Theme_Instagram_Sync {

	/**
	 * Storage option for cached normalized posts.
	 */
	const CACHE_OPTION = 'custom_theme_instagram_posts_cache';

	/**
	 * Fast transient cache key for refresh intervals.
	 */
	const TRANSIENT_KEY = 'custom_theme_instagram_posts_transient';

	/**
	 * Sync metadata and diagnostic info option.
	 */
	const META_OPTION = 'custom_theme_instagram_sync_meta';

	/**
	 * WP-Cron action hook name.
	 */
	const CRON_HOOK = 'custom_theme_instagram_cron_sync';

	/**
	 * Initialize hooks and cron listeners.
	 */
	public static function init() {
		add_action( self::CRON_HOOK, array( __CLASS__, 'run_cron_sync' ) );
		add_action( 'init', array( __CLASS__, 'check_cron_schedule' ) );
	}

	/**
	 * Retrieve credentials and sync configuration from Theme Settings.
	 *
	 * @return array
	 */
	public static function get_config() {
		// Attempt ACF get_field with fallback to get_option for reliability
		$account_id = function_exists( 'get_field' ) ? get_field( 'instagram_account_id', 'option' ) : '';
		if ( empty( $account_id ) ) {
			$account_id = get_option( 'options_instagram_account_id', '' );
		}

		$access_token = function_exists( 'get_field' ) ? get_field( 'instagram_access_token', 'option' ) : '';
		if ( empty( $access_token ) ) {
			$access_token = get_option( 'options_instagram_access_token', '' );
		}

		$username = function_exists( 'get_field' ) ? get_field( 'instagram_username', 'option' ) : '';
		if ( empty( $username ) ) {
			$username = get_option( 'options_instagram_username', '' );
		}

		$cache_duration = function_exists( 'get_field' ) ? get_field( 'instagram_cache_duration', 'option' ) : '';
		if ( empty( $cache_duration ) ) {
			$cache_duration = get_option( 'options_instagram_cache_duration', 4 );
		}

		$fetch_limit = function_exists( 'get_field' ) ? get_field( 'instagram_posts_fetch_limit', 'option' ) : '';
		if ( empty( $fetch_limit ) ) {
			$fetch_limit = get_option( 'options_instagram_posts_fetch_limit', 24 );
		}

		$auto_sync = function_exists( 'get_field' ) ? get_field( 'instagram_auto_sync', 'option' ) : '';
		if ( '' === $auto_sync ) {
			$auto_sync = get_option( 'options_instagram_auto_sync', 1 );
		}

		$sync_frequency = function_exists( 'get_field' ) ? get_field( 'instagram_sync_frequency', 'option' ) : '';
		if ( empty( $sync_frequency ) ) {
			$sync_frequency = get_option( 'options_instagram_sync_frequency', 'twicedaily' );
		}

		return array(
			'account_id'     => sanitize_text_field( trim( (string) $account_id ) ),
			'access_token'   => sanitize_text_field( trim( (string) $access_token ) ),
			'username'       => sanitize_text_field( trim( (string) $username ) ),
			'cache_duration' => max( 1, absint( $cache_duration ) ),
			'fetch_limit'    => max( 1, min( 50, absint( $fetch_limit ) ) ),
			'auto_sync'      => (bool) $auto_sync,
			'sync_frequency' => in_array( $sync_frequency, array( 'hourly', 'twicedaily', 'daily' ), true ) ? $sync_frequency : 'twicedaily',
		);
	}

	/**
	 * Retrieve sync metadata and status info.
	 *
	 * @return array
	 */
	public static function get_meta() {
		$defaults = array(
			'status'         => 'not_configured', // connected, error, token_expired, not_configured
			'last_sync'      => 0,
			'post_count'     => 0,
			'error_message'  => '',
			'error_code'     => '',
			'account_info'   => array(),
		);

		$meta = get_option( self::META_OPTION, $defaults );
		return wp_parse_args( is_array( $meta ) ? $meta : array(), $defaults );
	}

	/**
	 * Update sync metadata.
	 *
	 * @param array $args New metadata fields.
	 * @return bool
	 */
	public static function update_meta( $args = array() ) {
		$current = self::get_meta();
		$updated = array_merge( $current, $args );
		return update_option( self::META_OPTION, $updated, false );
	}

	/**
	 * Get cached Instagram posts.
	 *
	 * @return array Array of normalized post items.
	 */
	public static function get_cached_posts() {
		$posts = get_option( self::CACHE_OPTION, array() );
		return is_array( $posts ) ? $posts : array();
	}

	/**
	 * Find a specific post by its Instagram media ID.
	 *
	 * @param string $post_id Media ID.
	 * @return array|null
	 */
	public static function get_post_by_id( $post_id ) {
		$posts = self::get_cached_posts();
		foreach ( $posts as $post ) {
			if ( ! empty( $post['id'] ) && (string) $post['id'] === (string) $post_id ) {
				return $post;
			}
		}
		return null;
	}

	/**
	 * Synchronize posts from Meta API.
	 *
	 * @param bool $force Force sync ignoring transient cache.
	 * @return array Status array with success boolean, count or error.
	 */
	public static function sync( $force = false ) {
		$config = self::get_config();

		if ( empty( $config['account_id'] ) || empty( $config['access_token'] ) ) {
			self::update_meta(
				array(
					'status'        => 'not_configured',
					'error_message' => __( 'Missing Instagram Account ID or Access Token.', 'custom-theme' ),
				)
			);
			return array(
				'success' => false,
				'error'   => __( 'Instagram Account ID and Access Token must be configured in Theme Settings.', 'custom-theme' ),
				'code'    => 'missing_credentials',
			);
		}

		// Check transient if not forced
		if ( ! $force && get_transient( self::TRANSIENT_KEY ) ) {
			$cached = self::get_cached_posts();
			return array(
				'success' => true,
				'total'   => count( $cached ),
				'cached'  => true,
				'message' => __( 'Serving from active cache.', 'custom-theme' ),
			);
		}

		// Perform API fetch
		$result = Custom_Theme_Instagram_API::fetch_posts(
			$config['account_id'],
			$config['access_token'],
			$config['fetch_limit']
		);

		if ( ! $result['success'] ) {
			$status = 'error';
			if ( 190 === (int) ( $result['code'] ?? 0 ) ) {
				$status = 'token_expired';
			}

			// Fail-safe: Record error, but preserve existing cache so frontend never breaks
			self::update_meta(
				array(
					'status'        => $status,
					'error_message' => $result['error'],
					'error_code'    => ! empty( $result['code'] ) ? (string) $result['code'] : 'api_error',
				)
			);

			return $result;
		}

		$posts = $result['posts'];

		// Persist post cache
		update_option( self::CACHE_OPTION, $posts, false );

		// Set transient expiration
		$duration_seconds = $config['cache_duration'] * HOUR_IN_SECONDS;
		set_transient( self::TRANSIENT_KEY, true, $duration_seconds );

		// Fetch profile info for diagnostic card
		$account_info = array();
		$profile_res  = Custom_Theme_Instagram_API::test_connection( $config['account_id'], $config['access_token'] );
		if ( $profile_res['success'] && ! empty( $profile_res['data'] ) ) {
			$account_info = array(
				'id'              => $profile_res['data']['id'] ?? $config['account_id'],
				'username'        => $profile_res['data']['username'] ?? $config['username'],
				'name'            => $profile_res['data']['name'] ?? '',
				'profile_picture' => $profile_res['data']['profile_picture_url'] ?? '',
				'media_count'     => $profile_res['data']['media_count'] ?? count( $posts ),
			);
		}

		self::update_meta(
			array(
				'status'        => 'connected',
				'last_sync'     => time(),
				'post_count'    => count( $posts ),
				'error_message' => '',
				'error_code'    => '',
				'account_info'  => $account_info,
			)
		);

		return array(
			'success' => true,
			'total'   => count( $posts ),
			'posts'   => $posts,
		);
	}

	/**
	 * Callback for WP-Cron background sync.
	 */
	public static function run_cron_sync() {
		$config = self::get_config();
		if ( $config['auto_sync'] ) {
			self::sync( true );
		}
	}

	/**
	 * Maintain and reschedule WP-Cron job based on Theme Settings.
	 */
	public static function check_cron_schedule() {
		$config    = self::get_config();
		$scheduled = wp_next_scheduled( self::CRON_HOOK );

		if ( ! $config['auto_sync'] || empty( $config['access_token'] ) ) {
			if ( $scheduled ) {
				wp_unschedule_event( $scheduled, self::CRON_HOOK );
			}
			return;
		}

		if ( ! $scheduled ) {
			wp_schedule_event( time() + 300, $config['sync_frequency'], self::CRON_HOOK );
		}
	}

	/**
	 * Clear all Instagram caches.
	 */
	public static function clear_cache() {
		delete_transient( self::TRANSIENT_KEY );
		delete_option( self::CACHE_OPTION );
		self::update_meta(
			array(
				'status'     => 'not_configured',
				'last_sync'  => 0,
				'post_count' => 0,
			)
		);
	}
}
