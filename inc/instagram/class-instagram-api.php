<?php
/**
 * Meta Graph API HTTP Client for Instagram Business/Professional Accounts.
 *
 * @package Custom_Theme
 * @subpackage Instagram
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Theme_Instagram_API {

	/**
	 * Meta Graph API version.
	 */
	const API_VERSION = 'v21.0';

	/**
	 * Meta Graph API base URL.
	 */
	const GRAPH_URL = 'https://graph.facebook.com/';

	/**
	 * Request timeout in seconds.
	 */
	const TIMEOUT = 20;

	/**
	 * Test connection to the Instagram Business Account.
	 *
	 * @param string $account_id   Instagram Business Account ID.
	 * @param string $access_token Meta Access Token.
	 * @return array Result array with status, data or error message.
	 */
	public static function test_connection( $account_id, $access_token ) {
		$account_id   = sanitize_text_field( trim( $account_id ) );
		$access_token = sanitize_text_field( trim( $access_token ) );

		if ( empty( $account_id ) ) {
			return array(
				'success' => false,
				'error'   => __( 'Instagram Account ID is required.', 'custom-theme' ),
				'code'    => 'missing_account_id',
			);
		}

		if ( empty( $access_token ) ) {
			return array(
				'success' => false,
				'error'   => __( 'Instagram Access Token is required.', 'custom-theme' ),
				'code'    => 'missing_token',
			);
		}

		$endpoint = self::GRAPH_URL . self::API_VERSION . '/' . rawurlencode( $account_id );
		$url      = add_query_arg(
			array(
				'fields'       => 'id,username,name,profile_picture_url,media_count',
				'access_token' => $access_token,
			),
			$endpoint
		);

		$response = wp_remote_get(
			$url,
			array(
				'timeout'     => self::TIMEOUT,
				'sslverify'   => true,
				'httpversion' => '1.1',
				'headers'     => array(
					'Accept' => 'application/json',
				),
			)
		);

		return self::parse_response( $response, 'account' );
	}

	/**
	 * Fetch latest media posts from the Instagram Business Account.
	 *
	 * @param string $account_id   Instagram Business Account ID.
	 * @param string $access_token Meta Access Token.
	 * @param int    $limit        Number of posts to fetch (max 50).
	 * @return array Result array with status and normalized posts or error.
	 */
	public static function fetch_posts( $account_id, $access_token, $limit = 24 ) {
		$account_id   = sanitize_text_field( trim( $account_id ) );
		$access_token = sanitize_text_field( trim( $access_token ) );
		$limit        = max( 1, min( 50, absint( $limit ) ) );

		if ( empty( $account_id ) || empty( $access_token ) ) {
			return array(
				'success' => false,
				'error'   => __( 'Instagram Account ID and Access Token must be configured.', 'custom-theme' ),
				'code'    => 'missing_credentials',
			);
		}

		$endpoint = self::GRAPH_URL . self::API_VERSION . '/' . rawurlencode( $account_id ) . '/media';
		$url      = add_query_arg(
			array(
				'fields'       => 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp,username,children{id,media_type,media_url,thumbnail_url}',
				'limit'        => $limit,
				'access_token' => $access_token,
			),
			$endpoint
		);

		$response = wp_remote_get(
			$url,
			array(
				'timeout'     => self::TIMEOUT,
				'sslverify'   => true,
				'httpversion' => '1.1',
				'headers'     => array(
					'Accept' => 'application/json',
				),
			)
		);

		$result = self::parse_response( $response, 'media' );

		if ( ! $result['success'] ) {
			return $result;
		}

		$raw_posts  = ! empty( $result['data']['data'] ) ? $result['data']['data'] : array();
		$normalized = array();

		foreach ( $raw_posts as $post ) {
			$item = self::normalize_post( $post );
			if ( ! empty( $item['thumbnail_url'] ) || ! empty( $item['media_url'] ) ) {
				$normalized[] = $item;
			}
		}

		return array(
			'success' => true,
			'posts'   => $normalized,
			'total'   => count( $normalized ),
		);
	}

	/**
	 * Normalize a single raw Meta post object into a safe, uniform structure.
	 *
	 * @param array $post Raw post from Meta API.
	 * @return array Normalized post data.
	 */
	public static function normalize_post( $post ) {
		$id         = isset( $post['id'] ) ? sanitize_text_field( $post['id'] ) : '';
		$media_type = isset( $post['media_type'] ) ? sanitize_text_field( $post['media_type'] ) : 'IMAGE';
		$media_url  = isset( $post['media_url'] ) ? esc_url_raw( $post['media_url'] ) : '';
		$thumb_url  = isset( $post['thumbnail_url'] ) ? esc_url_raw( $post['thumbnail_url'] ) : '';
		$permalink  = isset( $post['permalink'] ) ? esc_url_raw( $post['permalink'] ) : 'https://www.instagram.com/';
		$caption    = isset( $post['caption'] ) ? sanitize_textarea_field( $post['caption'] ) : '';
		$timestamp  = isset( $post['timestamp'] ) ? sanitize_text_field( $post['timestamp'] ) : '';
		$username   = isset( $post['username'] ) ? sanitize_text_field( $post['username'] ) : '';

		// For videos/reels, thumbnail_url is the primary image. For images/carousels, media_url is used.
		if ( empty( $thumb_url ) && ! empty( $media_url ) ) {
			$thumb_url = $media_url;
		}

		// Fallback for thumbnail if media_url is empty but thumb exists
		if ( empty( $media_url ) && ! empty( $thumb_url ) ) {
			$media_url = $thumb_url;
		}

		// Format date
		$formatted_date = '';
		if ( ! empty( $timestamp ) ) {
			$time = strtotime( $timestamp );
			if ( $time ) {
				$date_format    = get_option( 'date_format', 'M j, Y' );
				$formatted_date = wp_date( $date_format, $time );
			}
		}

		// Extract caption excerpt for previews and alt attributes
		$clean_caption   = wp_strip_all_tags( $caption );
		$caption_excerpt = wp_trim_words( $clean_caption, 18, '...' );
		$accessible_alt  = ! empty( $clean_caption ) ? wp_trim_words( $clean_caption, 12, '' ) : sprintf( __( 'Instagram post by %s', 'custom-theme' ), $username ? '@' . $username : 'Sarathi AI Labs' );

		return array(
			'id'              => $id,
			'media_type'      => strtoupper( $media_type ),
			'media_url'       => $media_url,
			'thumbnail_url'   => $thumb_url,
			'permalink'       => $permalink,
			'caption'         => $caption,
			'caption_excerpt' => $caption_excerpt,
			'alt_text'        => $accessible_alt,
			'timestamp'       => $timestamp,
			'formatted_date'  => $formatted_date,
			'username'        => $username,
		);
	}

	/**
	 * Parse WordPress HTTP response and translate Meta Graph API errors.
	 *
	 * @param array|WP_Error $response WP HTTP response.
	 * @param string         $context  Context identifier (account, media).
	 * @return array
	 */
	private static function parse_response( $response, $context = '' ) {
		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'error'   => sprintf( __( 'Network connection error: %s', 'custom-theme' ), $response->get_error_message() ),
				'code'    => 'http_error',
			);
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );
		$data        = json_decode( $body, true );

		if ( 200 !== $status_code || ! empty( $data['error'] ) ) {
			$error_obj    = ! empty( $data['error'] ) ? $data['error'] : array();
			$meta_msg     = ! empty( $error_obj['message'] ) ? sanitize_text_field( $error_obj['message'] ) : __( 'Unknown Meta API error.', 'custom-theme' );
			$meta_code    = ! empty( $error_obj['code'] ) ? absint( $error_obj['code'] ) : $status_code;
			$meta_subcode = ! empty( $error_obj['error_subcode'] ) ? absint( $error_obj['error_subcode'] ) : 0;

			// Humanized error guidance
			$friendly_msg = self::get_friendly_error_message( $meta_code, $meta_subcode, $meta_msg );

			return array(
				'success'       => false,
				'error'         => $friendly_msg,
				'raw_error'     => $meta_msg,
				'code'          => $meta_code,
				'error_subcode' => $meta_subcode,
				'http_status'   => $status_code,
			);
		}

		return array(
			'success' => true,
			'data'    => $data,
		);
	}

	/**
	 * Convert Meta error codes into helpful developer/administrator guidance.
	 *
	 * @param int    $code    Meta error code.
	 * @param int    $subcode Meta error subcode.
	 * @param string $default Default message.
	 * @return string
	 */
	private static function get_friendly_error_message( $code, $subcode, $default ) {
		if ( 190 === $code ) {
			if ( 463 === $subcode ) {
				return __( 'Meta Access Token has expired. Please generate and save a new Long-Lived Token in Theme Settings.', 'custom-theme' );
			}
			if ( 467 === $subcode ) {
				return __( 'Meta Access Token is invalid or revoked. Please verify your token in Theme Settings.', 'custom-theme' );
			}
			return __( 'Meta Access Token authentication failed (Code 190). Please verify the token has valid permissions.', 'custom-theme' );
		}

		if ( 100 === $code ) {
			return __( 'Invalid Account ID or parameters (Code 100). Verify your numerical Instagram Business Account ID.', 'custom-theme' );
		}

		if ( 10 === $code || 200 === $code ) {
			return __( 'Permission Denied: Your Meta Access Token is missing required scopes (instagram_basic, pages_show_list).', 'custom-theme' );
		}

		if ( 4 === $code || 17 === $code || 32 === $code ) {
			return __( 'Meta Graph API rate limit reached. Cached posts will continue to be served.', 'custom-theme' );
		}

		return esc_html( $default );
	}
}
