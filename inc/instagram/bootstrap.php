<?php
/**
 * Instagram Feed Module Bootstrap.
 *
 * @package Custom_Theme
 * @subpackage Instagram
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$instagram_dir = __DIR__ . '/';

require_once $instagram_dir . 'class-instagram-api.php';
require_once $instagram_dir . 'class-instagram-sync.php';
require_once $instagram_dir . 'class-instagram-settings.php';

// Initialize services
Custom_Theme_Instagram_Sync::init();
Custom_Theme_Instagram_Settings::init();
