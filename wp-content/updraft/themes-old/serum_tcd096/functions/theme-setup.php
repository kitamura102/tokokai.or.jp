<?php
/**
 * テーマ毎に必要なグローバル設定をここで定義。
 *
 * NOTE: テーマに応じて設定する
 */

if ( ! defined( 'TCD_UPDATE_NOTIFIER_XML_URL' ) ) {
    $update_notifier_url = ( get_locale() === 'ja' )
        ? 'http://design-plus1.com/notifier_serum.xml'
        : 'http://design-plus1.com/notifier_serum_en.xml'; // 英語など他の言語用URL

    define( 'TCD_UPDATE_NOTIFIER_XML_URL', $update_notifier_url );
}

if ( ! defined( 'TCD_MANUAL_URL' ) ) {
    define(
        'TCD_MANUAL_URL',
        'https://dl.tcd-theme.com/tcd096/'
    );
}

if ( ! defined( 'TCD_TEXTDOMAIN' ) ) {
	define(
		'TCD_TEXTDOMAIN',
		'tcd-serum'
	);
}

/**
 * Get the remote xml file.
 *
 * @return SimpleXMLElement|false
 */
function get_tcd_update_notifier_xml() {
	if ( ! defined( 'TCD_UPDATE_NOTIFIER_XML_URL' ) ) {
		return;
	}

	// Load cache.
	$cache_key  = 'tcd_notifier_' . md5( TCD_UPDATE_NOTIFIER_XML_URL );
	$cache_data = get_transient( $cache_key );

	// No cache or expired.
	if ( false === $cache_data ) {
		// Get remote xml.
		$response = wp_safe_remote_get( TCD_UPDATE_NOTIFIER_XML_URL );

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

/**
 * 管理画面 カテゴリーやタグなどの説明文でHTMLを許可範囲を広げる
 *
 * NOTE: wp_filter_post_ksesを新たに追加して、scriptタグを無害化する
 */
remove_filter( 'pre_term_description', 'wp_filter_kses' );
add_filter( 'pre_term_description', 'wp_filter_post_kses' );