<?php

namespace TCD\PWA\Helper;

/**
 * 指定した文字列をテキストドメインを付けて翻訳取得する関数
 *
 * WordPress 標準の __( $text, $domain ) 関数をラップ
 */
function __( $text ) {
  return \__( $text, TCDPWA_TEXTDOMAIN );
}

/**
* 指定した文字列をテキストドメインを付けて翻訳し、出力する関数
*
* WordPress 標準の _e( $text, $domain ) 関数をラップ
*/
function _e( $text ) {
  \_e( $text, TCDPWA_TEXTDOMAIN );
}

/**
 * CSSの読み込み
 *
 * ver情報にファイルの更新時間を付与する（キャッシュ対策）
 */
function enqueue_style( $handle, $src, $deps = [], $ver = null, $media = 'all' ) {
  wp_enqueue_style(
    $handle,
    TCDPWA_URL . $src,
    $deps,
    $ver ?? filemtime( TCDPWA_PATH . $src ),
    $media
  );
}

/**
 * JavaScriptの読み込み
 *
 * ver情報にファイルの更新時間を付与する（キャッシュ対策）
 */
function enqueue_script( $handle, $src, $deps = [], $ver = null, $args = true ) {
  wp_enqueue_script(
    $handle,
    TCDPWA_URL . $src,
    $deps,
    $ver ?? filemtime( TCDPWA_PATH . $src ),
    $args
  );
}

/**
 * TCD\PWA\AdminSettingsのインスタンス取得用関数
 */
function admin() {
  global $tcd_pwa_admin_settings;
  return $tcd_pwa_admin_settings;
}