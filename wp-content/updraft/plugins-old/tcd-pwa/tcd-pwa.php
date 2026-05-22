<?php
/**
 * Plugin Name: TCD Progressive Web Apps
 * Plugin URI: https://tcd-theme.com/2025/05/tcd-pwa.html
 * Description: This plugin allows TCD themes to be compatible with PWA (Progressive Web Apps).
 * Version: 1.0
 * Requires at least: 6.7
 * Requires PHP: 8.0
 * Author: TCD
 * Author URI: https://tcd-theme.com/
 * Text Domain: tcd-pwa
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Plugin is active
if ( ! defined( 'TCDPWA_ACTIVE' ) ) {
	define(
		'TCDPWA_ACTIVE',
		true
	);
}

// Plugin Path
if ( ! defined( 'TCDPWA_PATH' ) ) {
	define(
		'TCDPWA_PATH',
		plugin_dir_path( __FILE__ )
	);
}

// Plugin Url
if ( ! defined( 'TCDPWA_URL' ) ) {
	define(
		'TCDPWA_URL',
		plugins_url( '/', __FILE__ )
	);
}

// Plugin Ver
if ( ! defined( 'TCDPWA_VER' ) ) {
	define(
		'TCDPWA_VER',
		current( get_file_data( __FILE__ , [ 'Version' ], 'plugin' ) )
	);
}

// Plugin base name
if ( ! defined( 'TCDPWA_BASE_NAME' ) ) {
	define(
		'TCDPWA_BASE_NAME',
		plugin_basename( __FILE__ )
	);
}

// Plugin Text Domain
if ( ! defined( 'TCDPWA_TEXTDOMAIN' ) ) {
	define(
		'TCDPWA_TEXTDOMAIN',
		'tcd-pwa'
	);
}

// load textdomain
load_textdomain(
	TCDPWA_TEXTDOMAIN,
	TCDPWA_PATH . 'languages/' . TCDPWA_TEXTDOMAIN . '-' . determine_locale() . '.mo'
);

// Plugin Name
if ( ! defined( 'TCDPWA_NAME' ) ) {
	define(
		'TCDPWA_NAME',
		__( 'TCD Progressive Web Apps', TCDPWA_TEXTDOMAIN )
	);
}

// プラグインの説明
__( 'This plugin allows TCD themes to be compatible with PWA (Progressive Web Apps).', TCDPWA_TEXTDOMAIN );

// 更新通知
require_once TCDPWA_PATH . 'include/update-notifier.php';

// ヘルパ関数
require_once TCDPWA_PATH . 'include/helper.php';

// 管理画面の設定
require_once TCDPWA_PATH . 'include/class-admin-settings.php';

// ウェブアプリマニフェストの登録
require_once TCDPWA_PATH . 'include/class-manifest.php';

// サービスワーカーの登録
require_once TCDPWA_PATH . 'include/class-service-worker.php';

// A2HS機能
require_once TCDPWA_PATH . 'include/hooks-a2hs.php';

// Webプッシュ通知
require_once TCDPWA_PATH . 'include/hooks-web-push.php';

// プラグインに設定リンク追加
add_filter( 'plugin_action_links_' . TCDPWA_BASE_NAME, 'tcd_pwa_add_setting_link' );
function tcd_pwa_add_setting_link( $actions ){
	array_unshift( $actions, '<a href="' . menu_page_url( TCD\PWA\Helper\admin()->page_slug, false ) . '">' . __( 'Settings' ) . '</a>' );
	return $actions;
}

// プラグイン有効化時の処理
register_activation_hook( __FILE__, 'tcd_pwa_activation' );
function tcd_pwa_activation(){

	// フラッシュリライトルールを実行するためのフラグ
	update_option( 'tcd_pwa_needs_flush', 1 );
}

// プラグイン有効化後にリライトルールを更新する
add_action( 'init', 'tcd_pwa_flush_rewrite_rules', 11 );
function tcd_pwa_flush_rewrite_rules(){

	// フラグがONの場合はリライトルールを再生成
	if( get_option( 'tcd_pwa_needs_flush' ) ){
		flush_rewrite_rules( false );
		// 実行後はフラグをオフ
		update_option( 'tcd_pwa_needs_flush', 0 );
	}
}