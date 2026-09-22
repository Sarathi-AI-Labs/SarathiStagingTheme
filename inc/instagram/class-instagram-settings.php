<?php
/**
 * Instagram Theme Settings & ACF Admin Controller.
 *
 * @package Custom_Theme
 * @subpackage Instagram
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Theme_Instagram_Settings {

	/**
	 * Nonce action for admin AJAX operations.
	 */
	const NONCE_ACTION = 'custom_theme_instagram_admin_nonce';

	/**
	 * Initialize admin hooks.
	 */
	public static function init() {
		// AJAX Endpoints
		add_action( 'wp_ajax_custom_theme_instagram_test_connection', array( __CLASS__, 'ajax_test_connection' ) );
		add_action( 'wp_ajax_custom_theme_instagram_sync_now', array( __CLASS__, 'ajax_sync_now' ) );

		// Enqueue admin scripts & styles
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );

		// Dynamic ACF choices for Fixed/Manual post selection
		add_filter( 'acf/load_field/name=manual_instagram_posts', array( __CLASS__, 'populate_manual_posts_choices' ) );
		add_filter( 'acf/load_field/key=field_insta_feed_manual_posts', array( __CLASS__, 'populate_manual_posts_choices' ) );

		// Custom render for Status Diagnostic Card in Theme Settings
		add_action( 'acf/render_field/name=instagram_connection_diagnostic', array( __CLASS__, 'render_diagnostic_card' ) );

		// Register local field groups as fail-safe code fallback
		add_action( 'acf/init', array( __CLASS__, 'register_acf_settings_field_group' ) );
		add_filter( 'acf/load_field/name=page_sections', array( __CLASS__, 'inject_flexible_content_layout' ), 20 );

		// Trigger auto-sync when settings are updated in Theme Settings
		add_action( 'acf/save_post', array( __CLASS__, 'on_save_theme_settings' ), 20 );
	}

	/**
	 * Enqueue admin scripts and CSS on Theme Settings and Post Edit screens.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public static function enqueue_admin_assets( $hook ) {
		$screen = get_current_screen();

		$is_settings_page = $screen && strpos( $screen->id, 'instagram-feed-settings' ) !== false;
		$is_page_edit     = in_array( $hook, array( 'post.php', 'post-new.php' ), true );

		if ( ! $is_settings_page && ! $is_page_edit ) {
			return;
		}

		$theme_uri = get_template_directory_uri();
		$theme_dir = get_template_directory();

		// Admin CSS
		if ( file_exists( $theme_dir . '/assets/css/admin-instagram.css' ) ) {
			wp_enqueue_style(
				'custom-theme-admin-instagram',
				$theme_uri . '/assets/css/admin-instagram.css',
				array(),
				filemtime( $theme_dir . '/assets/css/admin-instagram.css' )
			);
		}

		// Admin JS (primarily for the Theme Settings AJAX test & sync)
		if ( file_exists( $theme_dir . '/assets/js/admin-instagram.js' ) ) {
			wp_enqueue_script(
				'custom-theme-admin-instagram',
				$theme_uri . '/assets/js/admin-instagram.js',
				array( 'jquery' ),
				filemtime( $theme_dir . '/assets/js/admin-instagram.js' ),
				true
			);

			wp_localize_script(
				'custom-theme-admin-instagram',
				'sarathiInstagramAdmin',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
					'strings' => array(
						'testing'     => __( 'Testing connection to Meta Graph API...', 'custom-theme' ),
						'syncing'     => __( 'Synchronizing posts from Instagram...', 'custom-theme' ),
						'testSuccess' => __( 'Connection successful!', 'custom-theme' ),
						'syncSuccess' => __( 'Posts synchronized successfully!', 'custom-theme' ),
						'error'       => __( 'Operation failed. Please check error details.', 'custom-theme' ),
					),
				)
			);
		}
	}

	/**
	 * Populate dynamic choices for the Fixed/Manual post selection ACF field.
	 *
	 * @param array $field ACF field array.
	 * @return array
	 */
	public static function populate_manual_posts_choices( $field ) {
		$field['choices'] = array();
		$posts = Custom_Theme_Instagram_Sync::get_cached_posts();

		if ( empty( $posts ) ) {
			$field['instructions'] = sprintf(
				'%s <a href="%s" target="_blank">%s &rarr;</a>',
				__( 'No cached posts found. Please configure credentials and click "Sync Posts Now" in', 'custom-theme' ),
				admin_url( 'admin.php?page=instagram-feed-settings' ),
				__( 'Theme Settings &rarr; Instagram Feed', 'custom-theme' )
			);
			return $field;
		}

		foreach ( $posts as $post ) {
			$id        = ! empty( $post['id'] ) ? $post['id'] : '';
			$thumb     = ! empty( $post['thumbnail_url'] ) ? $post['thumbnail_url'] : ( ! empty( $post['media_url'] ) ? $post['media_url'] : '' );
			$date      = ! empty( $post['formatted_date'] ) ? $post['formatted_date'] : '';
			$type      = ! empty( $post['media_type'] ) ? $post['media_type'] : 'POST';
			$caption   = ! empty( $post['caption_excerpt'] ) ? $post['caption_excerpt'] : __( 'Post', 'custom-theme' );
			$permalink = ! empty( $post['permalink'] ) ? $post['permalink'] : '#';

			// Build visual choice card
			$choice_html = sprintf(
				'<span class="sarathi-insta-picker-card" data-post-id="%s">' .
					'<span class="sarathi-insta-picker-thumb-wrap">' .
						'<img src="%s" alt="" class="sarathi-insta-picker-thumb" loading="lazy" />' .
						'<span class="sarathi-insta-picker-type">%s</span>' .
					'</span>' .
					'<span class="sarathi-insta-picker-meta">' .
						'<span class="sarathi-insta-picker-date">%s</span>' .
						'<span class="sarathi-insta-picker-caption">%s</span>' .
					'</span>' .
				'</span>',
				esc_attr( $id ),
				esc_url( $thumb ),
				esc_html( $type ),
				esc_html( $date ),
				esc_html( $caption )
			);

			$field['choices'][ $id ] = $choice_html;
		}

		return $field;
	}

	/**
	 * Render the Connection Status & Diagnostic Card inside Theme Settings.
	 *
	 * @param array $field ACF field array.
	 */
	public static function render_diagnostic_card( $field ) {
		$meta   = Custom_Theme_Instagram_Sync::get_meta();
		$config = Custom_Theme_Instagram_Sync::get_config();

		$status = $meta['status'];
		$badge_class = 'status-neutral';
		$badge_label = __( 'Not Configured', 'custom-theme' );

		if ( 'connected' === $status ) {
			$badge_class = 'status-connected';
			$badge_label = __( 'Connected & Verified', 'custom-theme' );
		} elseif ( 'token_expired' === $status ) {
			$badge_class = 'status-expired';
			$badge_label = __( 'Token Expired', 'custom-theme' );
		} elseif ( 'error' === $status ) {
			$badge_class = 'status-error';
			$badge_label = __( 'API / Connection Error', 'custom-theme' );
		}

		$last_sync_text = $meta['last_sync'] ? wp_date( 'M j, Y g:i A', $meta['last_sync'] ) : __( 'Never', 'custom-theme' );
		$account_info   = ! empty( $meta['account_info'] ) ? $meta['account_info'] : array();
		$username       = ! empty( $account_info['username'] ) ? $account_info['username'] : ( ! empty( $config['username'] ) ? $config['username'] : __( 'Not Set', 'custom-theme' ) );
		$account_id     = ! empty( $config['account_id'] ) ? $config['account_id'] : __( 'Not Set', 'custom-theme' );
		$post_count     = (int) $meta['post_count'];
		$error_message  = ! empty( $meta['error_message'] ) ? $meta['error_message'] : '';
		$avatar_url     = ! empty( $account_info['profile_picture'] ) ? $account_info['profile_picture'] : '';

		?>
		<div class="sarathi-insta-diag-card" id="sarathi-insta-diagnostic-wrap">
			<div class="sarathi-insta-diag-header">
				<div class="sarathi-insta-diag-profile">
					<?php if ( $avatar_url ) : ?>
						<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $username ); ?>" class="sarathi-insta-diag-avatar" />
					<?php else : ?>
						<div class="sarathi-insta-diag-avatar-placeholder">
							<span class="dashicons dashicons-instagram"></span>
						</div>
					<?php endif; ?>
					<div class="sarathi-insta-diag-titles">
						<h3 class="sarathi-insta-diag-title">@<?php echo esc_html( $username ); ?></h3>
						<span class="sarathi-insta-diag-subtitle">Account ID: <code><?php echo esc_html( $account_id ); ?></code></span>
					</div>
				</div>
				<div class="sarathi-insta-diag-badge-wrap">
					<span class="sarathi-insta-status-badge <?php echo esc_attr( $badge_class ); ?>" id="sarathi-insta-status-badge">
						<span class="sarathi-insta-status-dot"></span>
						<span class="sarathi-insta-status-text"><?php echo esc_html( $badge_label ); ?></span>
					</span>
				</div>
			</div>

			<?php if ( ! empty( $error_message ) ) : ?>
				<div class="sarathi-insta-diag-alert sarathi-insta-diag-alert-error" id="sarathi-insta-error-notice">
					<span class="dashicons dashicons-warning"></span>
					<div class="sarathi-insta-alert-body">
						<strong><?php esc_html_e( 'Connection Notice:', 'custom-theme' ); ?></strong>
						<span class="sarathi-insta-error-text"><?php echo esc_html( $error_message ); ?></span>
					</div>
				</div>
			<?php endif; ?>

			<div class="sarathi-insta-diag-grid">
				<div class="sarathi-insta-diag-stat">
					<span class="sarathi-insta-stat-label"><?php esc_html_e( 'Last Successful Sync', 'custom-theme' ); ?></span>
					<strong class="sarathi-insta-stat-value" id="sarathi-insta-last-sync"><?php echo esc_html( $last_sync_text ); ?></strong>
				</div>
				<div class="sarathi-insta-diag-stat">
					<span class="sarathi-insta-stat-label"><?php esc_html_e( 'Cached Posts', 'custom-theme' ); ?></span>
					<strong class="sarathi-insta-stat-value" id="sarathi-insta-cached-count"><?php echo esc_html( (string) $post_count ); ?></strong>
				</div>
				<div class="sarathi-insta-diag-stat">
					<span class="sarathi-insta-stat-label"><?php esc_html_e( 'Cache Expiry Duration', 'custom-theme' ); ?></span>
					<strong class="sarathi-insta-stat-value"><?php printf( esc_html__( '%d Hours', 'custom-theme' ), (int) $config['cache_duration'] ); ?></strong>
				</div>
				<div class="sarathi-insta-diag-stat">
					<span class="sarathi-insta-stat-label"><?php esc_html_e( 'Background Auto-Sync', 'custom-theme' ); ?></span>
					<strong class="sarathi-insta-stat-value">
						<?php echo $config['auto_sync'] ? sprintf( esc_html__( 'Active (%s)', 'custom-theme' ), esc_html( ucfirst( $config['sync_frequency'] ) ) ) : esc_html__( 'Disabled', 'custom-theme' ); ?>
					</strong>
				</div>
			</div>

			<div class="sarathi-insta-diag-actions">
				<button type="button" class="button button-secondary sarathi-insta-btn" id="sarathi-btn-test-connection">
					<span class="dashicons dashicons-update sarathi-btn-icon"></span>
					<?php esc_html_e( 'Test Connection', 'custom-theme' ); ?>
				</button>
				<button type="button" class="button button-primary sarathi-insta-btn" id="sarathi-btn-sync-now">
					<span class="dashicons dashicons-cloud-saved sarathi-btn-icon"></span>
					<?php esc_html_e( 'Sync Instagram Posts Now', 'custom-theme' ); ?>
				</button>
				<span class="spinner sarathi-insta-spinner" id="sarathi-insta-action-spinner"></span>
				<div class="sarathi-insta-ajax-feedback" id="sarathi-insta-ajax-feedback"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX handler: Test Instagram connection.
	 */
	public static function ajax_test_connection() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'custom-theme' ) ) );
		}

		$account_id   = ! empty( $_POST['account_id'] ) ? sanitize_text_field( wp_unslash( $_POST['account_id'] ) ) : '';
		$access_token = ! empty( $_POST['access_token'] ) ? sanitize_text_field( wp_unslash( $_POST['access_token'] ) ) : '';

		// Fallback to saved config if empty in POST
		if ( empty( $account_id ) || empty( $access_token ) ) {
			$config       = Custom_Theme_Instagram_Sync::get_config();
			$account_id   = ! empty( $account_id ) ? $account_id : $config['account_id'];
			$access_token = ! empty( $access_token ) ? $access_token : $config['access_token'];
		}

		$result = Custom_Theme_Instagram_API::test_connection( $account_id, $access_token );

		if ( ! $result['success'] ) {
			wp_send_json_error(
				array(
					'message'       => $result['error'],
					'status_class'  => ( 190 === (int) ( $result['code'] ?? 0 ) ) ? 'status-expired' : 'status-error',
					'status_label'  => ( 190 === (int) ( $result['code'] ?? 0 ) ) ? __( 'Token Expired', 'custom-theme' ) : __( 'Connection Failed', 'custom-theme' ),
				)
			);
		}

		$data = $result['data'];
		wp_send_json_success(
			array(
				'message'       => __( 'Connection successfully verified with Meta Graph API!', 'custom-theme' ),
				'status_class'  => 'status-connected',
				'status_label'  => __( 'Connected & Verified', 'custom-theme' ),
				'username'      => $data['username'] ?? '',
				'name'          => $data['name'] ?? '',
				'avatar'        => $data['profile_picture_url'] ?? '',
				'media_count'   => $data['media_count'] ?? 0,
			)
		);
	}

	/**
	 * AJAX handler: Trigger manual Instagram sync.
	 */
	public static function ajax_sync_now() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'custom-theme' ) ) );
		}

		$result = Custom_Theme_Instagram_Sync::sync( true );

		if ( ! $result['success'] ) {
			wp_send_json_error(
				array(
					'message'      => $result['error'],
					'status_class' => ( 190 === (int) ( $result['code'] ?? 0 ) ) ? 'status-expired' : 'status-error',
					'status_label' => ( 190 === (int) ( $result['code'] ?? 0 ) ) ? __( 'Token Expired', 'custom-theme' ) : __( 'Sync Error', 'custom-theme' ),
				)
			);
		}

		$meta = Custom_Theme_Instagram_Sync::get_meta();

		wp_send_json_success(
			array(
				'message'      => sprintf( __( 'Successfully synchronized %d posts from Instagram!', 'custom-theme' ), (int) $result['total'] ),
				'total'        => (int) $result['total'],
				'last_sync'    => wp_date( 'M j, Y g:i A', $meta['last_sync'] ),
				'status_class' => 'status-connected',
				'status_label' => __( 'Connected & Verified', 'custom-theme' ),
			)
		);
	}

	/**
	 * Trigger sync when Theme Settings are saved.
	 *
	 * @param mixed $post_id ACF Post ID being saved.
	 */
	public static function on_save_theme_settings( $post_id ) {
		if ( 'options' !== $post_id ) {
			return;
		}

		// If access token was supplied or modified, test and sync
		$config = Custom_Theme_Instagram_Sync::get_config();
		if ( ! empty( $config['account_id'] ) && ! empty( $config['access_token'] ) ) {
			Custom_Theme_Instagram_Sync::sync( true );
		}
	}

	/**
	 * Programmatically register ACF Field Group for Theme Settings -> Instagram Feed.
	 * This ensures the settings page always has fields loaded even before JSON sync.
	 */
	public static function register_acf_settings_field_group() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'                   => 'group_instagram_feed_settings',
				'title'                 => __( 'Instagram Feed Settings', 'custom-theme' ),
				'fields'                => array(
					// Tab: Status
					array(
						'key'   => 'field_insta_tab_status',
						'label' => __( 'Connection Status & Diagnostics', 'custom-theme' ),
						'type'  => 'tab',
					),
					array(
						'key'   => 'field_insta_connection_diagnostic',
						'label' => '',
						'name'  => 'instagram_connection_diagnostic',
						'type'  => 'message',
						'message' => '',
					),

					// Tab: Credentials
					array(
						'key'   => 'field_insta_tab_credentials',
						'label' => __( 'API Configuration', 'custom-theme' ),
						'type'  => 'tab',
					),
					array(
						'key'          => 'field_insta_account_id',
						'label'        => __( 'Instagram Account ID', 'custom-theme' ),
						'name'         => 'instagram_account_id',
						'type'         => 'text',
						'instructions' => __( 'Numerical Instagram Business/Professional Account ID (e.g. 17841428741416965).', 'custom-theme' ),
						'required'     => 1,
						'wrapper'      => array( 'width' => '50' ),
					),
					array(
						'key'          => 'field_insta_username',
						'label'        => __( 'Instagram Username', 'custom-theme' ),
						'name'         => 'instagram_username',
						'type'         => 'text',
						'instructions' => __( 'Your Instagram username without @ (e.g. sarathiailabs).', 'custom-theme' ),
						'required'     => 0,
						'wrapper'      => array( 'width' => '50' ),
					),
					array(
						'key'          => 'field_insta_access_token',
						'label'        => __( 'Meta Graph API Access Token', 'custom-theme' ),
						'name'         => 'instagram_access_token',
						'type'         => 'password',
						'instructions' => __( 'Long-Lived Page or User Access Token with instagram_basic scope. Stored securely on server-side only.', 'custom-theme' ),
						'required'     => 1,
						'wrapper'      => array( 'width' => '100' ),
					),
					array(
						'key'          => 'field_insta_app_id',
						'label'        => __( 'Meta App ID (Optional)', 'custom-theme' ),
						'name'         => 'instagram_app_id',
						'type'         => 'text',
						'instructions' => __( 'Meta App ID from Meta Developers portal.', 'custom-theme' ),
						'wrapper'      => array( 'width' => '50' ),
					),
					array(
						'key'          => 'field_insta_app_secret',
						'label'        => __( 'Meta App Secret (Optional)', 'custom-theme' ),
						'name'         => 'instagram_app_secret',
						'type'         => 'password',
						'instructions' => __( 'Meta App Secret for server-side token debug/renewal. Stored securely.', 'custom-theme' ),
						'wrapper'      => array( 'width' => '50' ),
					),

					// Tab: Sync & Cache
					array(
						'key'   => 'field_insta_tab_sync',
						'label' => __( 'Sync & Cache Settings', 'custom-theme' ),
						'type'  => 'tab',
					),
					array(
						'key'           => 'field_insta_cache_duration',
						'label'         => __( 'Cache Expiration Duration', 'custom-theme' ),
						'name'          => 'instagram_cache_duration',
						'type'          => 'select',
						'instructions'  => __( 'Duration before refreshing posts from the Meta API.', 'custom-theme' ),
						'choices'       => array(
							'1'  => __( '1 Hour', 'custom-theme' ),
							'2'  => __( '2 Hours', 'custom-theme' ),
							'4'  => __( '4 Hours (Recommended)', 'custom-theme' ),
							'8'  => __( '8 Hours', 'custom-theme' ),
							'12' => __( '12 Hours', 'custom-theme' ),
							'24' => __( '24 Hours', 'custom-theme' ),
						),
						'default_value' => '4',
						'wrapper'       => array( 'width' => '50' ),
					),
					array(
						'key'           => 'field_insta_posts_fetch_limit',
						'label'         => __( 'Maximum Posts to Synchronize', 'custom-theme' ),
						'name'          => 'instagram_posts_fetch_limit',
						'type'          => 'number',
						'instructions'  => __( 'Total number of latest posts to fetch and cache (max 50).', 'custom-theme' ),
						'default_value' => 24,
						'min'           => 1,
						'max'           => 50,
						'wrapper'       => array( 'width' => '50' ),
					),
					array(
						'key'           => 'field_insta_auto_sync',
						'label'         => __( 'Automatic Background Synchronization', 'custom-theme' ),
						'name'          => 'instagram_auto_sync',
						'type'          => 'true_false',
						'instructions'  => __( 'Use WP-Cron to periodically fetch new posts automatically without slowing frontend page loads.', 'custom-theme' ),
						'default_value' => 1,
						'ui'            => 1,
						'wrapper'       => array( 'width' => '50' ),
					),
					array(
						'key'           => 'field_insta_sync_frequency',
						'label'         => __( 'Sync Frequency (WP-Cron)', 'custom-theme' ),
						'name'          => 'instagram_sync_frequency',
						'type'          => 'select',
						'instructions'  => __( 'How frequently the background sync should run.', 'custom-theme' ),
						'choices'       => array(
							'hourly'     => __( 'Hourly', 'custom-theme' ),
							'twicedaily' => __( 'Twice Daily (Recommended)', 'custom-theme' ),
							'daily'      => __( 'Daily', 'custom-theme' ),
						),
						'default_value' => 'twicedaily',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_insta_auto_sync',
									'operator' => '==',
									'value'    => '1',
								),
							),
						),
						'wrapper'       => array( 'width' => '50' ),
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'instagram-feed-settings',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'active'                => true,
			)
		);
	}

	/**
	 * Programmatically ensure the instagram_feed_section layout is available in page_sections flexible content.
	 *
	 * @param array $field ACF Flexible Content field array.
	 * @return array
	 */
	public static function inject_flexible_content_layout( $field ) {
		if ( empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) ) {
			return $field;
		}

		// Check if layout already registered
		if ( isset( $field['layouts']['layout_instagram_feed_section'] ) ) {
			return $field;
		}

		// Define the layout subfields
		$field['layouts']['layout_instagram_feed_section'] = array(
			'key'        => 'layout_instagram_feed_section',
			'name'       => 'instagram_feed_section',
			'label'      => __( 'Instagram Feed Section', 'custom-theme' ),
			'display'    => 'block',
			'sub_fields' => array(
				// Section settings clone
				array(
					'key'          => 'field_insta_feed_clone_section',
					'label'        => __( 'Section Settings', 'custom-theme' ),
					'name'         => 'section_settings',
					'type'         => 'clone',
					'clone'        => array( 'group_clone_section' ),
					'display'      => 'seamless',
					'layout'       => 'block',
				),
				// Heading fields clone
				array(
					'key'          => 'field_insta_feed_clone_heading',
					'label'        => __( 'Heading Fields', 'custom-theme' ),
					'name'         => 'heading_fields',
					'type'         => 'clone',
					'clone'        => array( 'group_clone_heading' ),
					'display'      => 'seamless',
					'layout'       => 'block',
				),
				// Section Description
				array(
					'key'   => 'field_insta_feed_description',
					'label' => __( 'Section Description', 'custom-theme' ),
					'name'  => 'description',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				// Mode: Automatic vs Manual
				array(
					'key'           => 'field_insta_feed_mode',
					'label'         => __( 'Select Layout Mode', 'custom-theme' ),
					'name'          => 'feed_mode',
					'type'          => 'radio',
					'instructions'  => __( 'Choose whether to automatically display the newest posts or select specific posts manually.', 'custom-theme' ),
					'choices'       => array(
						'automatic' => __( 'Automatic (Display Latest Posts)', 'custom-theme' ),
						'manual'    => __( 'Fixed / Manual (Select Specific Posts)', 'custom-theme' ),
					),
					'default_value' => 'automatic',
					'layout'        => 'horizontal',
				),
				// Automatic Mode: Posts Limit
				array(
					'key'               => 'field_insta_feed_posts_count',
					'label'             => __( 'Number of Posts', 'custom-theme' ),
					'name'              => 'posts_count',
					'type'              => 'number',
					'instructions'      => __( 'Number of recent posts to display.', 'custom-theme' ),
					'default_value'     => 6,
					'min'               => 1,
					'max'               => 24,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_insta_feed_mode',
								'operator' => '==',
								'value'    => 'automatic',
							),
						),
					),
					'wrapper'           => array( 'width' => '50' ),
				),
				// Manual Mode: Select Posts
				array(
					'key'               => 'field_insta_feed_manual_posts',
					'label'             => __( 'Select Instagram Posts', 'custom-theme' ),
					'name'              => 'manual_instagram_posts',
					'type'              => 'checkbox',
					'instructions'      => __( 'Select the specific Instagram posts to display for this section instance.', 'custom-theme' ),
					'choices'           => array(),
					'layout'            => 'horizontal',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_insta_feed_mode',
								'operator' => '==',
								'value'    => 'manual',
							),
						),
					),
					'wrapper'           => array( 'width' => '100' ),
				),
				// Grid columns
				array(
					'key'           => 'field_insta_feed_columns',
					'label'         => __( 'Columns', 'custom-theme' ),
					'name'          => 'columns',
					'type'          => 'select',
					'choices'       => array(
						'3' => __( '3 Columns', 'custom-theme' ),
						'4' => __( '4 Columns', 'custom-theme' ),
					),
					'default_value' => '3',
					'wrapper'       => array( 'width' => '33' ),
				),
				// Show caption overlay
				array(
					'key'           => 'field_insta_feed_show_caption',
					'label'         => __( 'Show Caption on Hover', 'custom-theme' ),
					'name'          => 'show_caption',
					'type'          => 'true_false',
					'default_value' => 1,
					'ui'            => 1,
					'wrapper'       => array( 'width' => '33' ),
				),
				// Show post date
				array(
					'key'           => 'field_insta_feed_show_date',
					'label'         => __( 'Show Post Date', 'custom-theme' ),
					'name'          => 'show_date',
					'type'          => 'true_false',
					'default_value' => 1,
					'ui'            => 1,
					'wrapper'       => array( 'width' => '33' ),
				),
				// Profile CTA Link Button
				array(
					'key'          => 'field_insta_feed_cta_button',
					'label'        => __( 'Instagram Follow Button', 'custom-theme' ),
					'name'         => 'cta_button',
					'type'         => 'link',
					'instructions' => __( 'Optional CTA link (e.g. Follow @sarathiailabs on Instagram).', 'custom-theme' ),
					'wrapper'      => array( 'width' => '50' ),
				),
				// Signature text
				array(
					'key'           => 'field_insta_feed_signature',
					'label'         => __( 'Signature / Accent Text', 'custom-theme' ),
					'name'          => 'signature_text',
					'type'          => 'text',
					'instructions'  => __( 'Accent script text shown below the description.', 'custom-theme' ),
					'default_value' => 'Follow us @sarathiailabs',
					'wrapper'       => array( 'width' => '50' ),
				),
			),
		);

		return $field;
	}
}
