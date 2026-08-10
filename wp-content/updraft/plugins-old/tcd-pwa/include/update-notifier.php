<?php

namespace TCD\PWA;

use function TCD\PWA\Helper\__;
use function TCD\PWA\Helper\_e;

/**
 * プラグイン用更新通知ファイルの設置URL
 */
define(
	'TCDPWA_UPDATE_NOTIFIER_XML_URL',
	determine_locale() === 'ja'
		? 'http://design-plus1.com/notifier_pwa.xml'
		: 'http://design-plus1.com/notifier_pwa_en.xml' // 英語など他の言語用URL
);

/**
 * 管理画面にメニューを追加
 *
 * @return void
 */
add_action( 'admin_menu', __NAMESPACE__ . '\add_admin_menu' );
function add_admin_menu(){

	// XMLファイルの取得
	$xml = get_xml();

	// バージョン比較
	if ( $xml && version_compare( TCDPWA_VER, $xml->latest, '<' ) ) {
		// 更新バッジ追加
		$tcd_memu_title = __( 'Plugin Update' ) . '<span class="update-plugins count-1"><span class="update-count">1</span></span>';
	}else{
		$tcd_memu_title = __( 'Plugin Update' ) . '';
	}

	add_dashboard_page(
		TCDPWA_NAME . ' ' . __( 'Plugin Update Information' ),
		$tcd_memu_title,
		'administrator',
		'tcd-pwa-updates',
		__NAMESPACE__ . '\view_admin_page'
	);
}

/**
 * 更新ページのコンテンツ
 *
 * @return void
 */
function view_admin_page() {

	// XMLファイルの取得
	$xml = get_xml();

	// バージョンを取得できなければ終了
	if ( ! $xml || empty( $xml->latest ) ) {
		// プラグインの最新情報の取得ができませんでした。時間をおいてから再度お試しください。
		printf(
			'<p style="font-size:14px;">%s</p>',
			__( 'We were unable to get the latest information on the plugin. Please wait some time and try again.' )
		);
		return;
	}

?>
	<style>
	.update-nag {
		display: none;
	}
	.tcd-update-info {
		background: #fff;
		border: 1px solid #ccc;
		border-radius: 5px;
		float: left;
		width: 400px;
		margin: 0 20px 20px 0;
	}
	.tcd-update-info h3 {
		background: #f2f2f2;
		background: linear-gradient(to bottom, #fff, #eee);
		border-bottom: 1px solid #ccc;
		border-radius: 5px 5px 0 0;
		font-size: 14px;
		margin: 0 0 15px 0;
		padding: 15px 15px 12px;
	}
	.tcd-update-info dl {
		font-size: 12px;
		margin: 0 15px 5px 15px;
	}
	.tcd-update-info dt {
		font-weight: 700;
		margin: 0 0 2px 0;
	}
	.tcd-update-info dd {
		margin: 0 0 15px 0;
	}
	.tcd-update-theme-thumbnail {
		border:1px solid #ccc;
		display: block;
		height: auto;
		max-width: 100%;
		width: 600px;
	}
	</style>
	<div class="wrap">
		<div id="icon-tools" class="icon32"></div>
		<h2>
			<?php echo esc_html( TCDPWA_NAME ); ?>
			<?php _e( 'Plugin Update Information'); ?>
		</h2>
		<?php if ( $xml && version_compare( TCDPWA_VER, $xml->latest, '<' ) ) { ?>
			<h3>
				<strong>
					<?php
						printf(
							/* translators: %s: plugin name. */
							__( 'The latest version of %s is released.'),
							esc_html( TCDPWA_NAME )
						);
					?>
				</strong>
			</h3>
			<p style="font-size:14px;">
			<?php
					printf(
						__( 'Current version is %1$s. You can update to the latest version, %2$s.'),
						esc_html( TCDPWA_VER ),
						esc_html( $xml->latest )
					);
				?>
			</p>
			<?php }else{ ?>
			<p style="font-size:14px;">
				<?php printf( __( 'The current version of %s is %s. This is the latest version.' ), esc_html( TCDPWA_NAME ),esc_html( TCDPWA_VER )); ?>
			</p>
			<?php } ?>
		<div class="tcd-update-instructions wp-clearfix">
			<p style="font-size:14px;">
				<strong>
					<!--最新版のプラグインは<a href="https://tcd.style/order-history" rel="noopener" target="_blank">マイページ</a> からダウンロードできます。-->
					<?php
						_e(
							'The latest version of the plugin can be downloaded from <a href="https://tcd.style/order-history" rel="noopener" target="_blank">Mypage</a>.',
						);
					?>
				</strong>
					<!--プラグインアップデートの方法はこちらをご確認ください。-->
					<?php
						_e(
							'Click <a href="https://tcd-theme.com/2010/10/plugin-install.html#manual_upload" rel="noopener" target="_blank">here</a> to find out how to update the theme.'
						);
					?>
			</p>
		</div>
		<div style="font-weight: bold;font-size:15px;display: block;padding: 20px;border: 1px solid #ccc;margin-bottom: 1.5em;background-color: #fff;">
			<!--最新版のプラグインへアップデートする前に、必ずご利用中のテーマファイルのバックアップをしてください。-->
			<?php _e( 'Please be sure to back up your plugin files before updating to the latest version.'); ?>
		</div>
		<div class="tcd-update-instructions wp-clearfix">
			<div class="tcd-update-info">
				<h3><!--更新履歴--><?php _e( 'Changelog'); ?></h3>
				<?php echo $xml->changelog; ?>
			</div>
			<?php // <img class="tcd-update-theme-thumbnail" src="" alt=""> ?>
		</div>
	</div>
	<?php
}

/**
 * リモートXMLファイルの取得
 *
 * @return SimpleXMLElement|false
 */
function get_xml() {

	// Load cache.
	$cache_key  = 'tcd_notifier_' . md5( TCDPWA_UPDATE_NOTIFIER_XML_URL );
	$cache_data = get_transient( $cache_key );

	// No cache or expired.
	if ( false === $cache_data ) {
		// Get remote xml.
		$response = wp_safe_remote_get( TCDPWA_UPDATE_NOTIFIER_XML_URL );

		if (
			! is_wp_error( $response ) &&
			! empty( $response['response']['code'] ) &&
			200 === $response['response']['code'] &&
			! empty( $response['body'] )
		) {
			$cache_data = $response['body'];
		} else {
			$cache_data = null;
		}

		// Save cache.
		set_transient( $cache_key, $cache_data, HOUR_IN_SECONDS * 6 );
	}

	if ( $cache_data ) {
		$xml = simplexml_load_string( $cache_data );
		return $xml;
	}

	return false;
}