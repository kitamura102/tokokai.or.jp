<?php

namespace TCD\PWA\WebPush;

use TCD\PWA\Helper;

/**
 * OneSignalを使ってWebプッシュ通知を送信する
 */
add_action( 'init', __NAMESPACE__ . '\init' );
function init(){

  // Webプッシュ通知がONの場合のみ
  if( ! Helper\admin()->get_option( 'webpush_os_active' ) ){
    return;
  }

  // OneSignal初期化
  add_action( 'wp_head', __NAMESPACE__ . '\os_init' );

  // 投稿タイプ別、自動通知
  add_action( 'transition_post_status', __NAMESPACE__ . '\os_auto_notification', 10, 3 );
}

/**
 * OneSignalの初期化
 *
 * NOTE: OneSignal連携には、App IDが必要
 * 初期化時に sdk_filesを参照する
 */
function os_init(){

  // APP ID の取得
  $app_id = Helper\admin()->get_option( 'webpush_os_app_id' );

  // 未入力の場合ｊは終了
  if( ! $app_id ){
    return;
  }

  // ベースURL
  $base_url = TCDPWA_URL;
  // https://example.com/wp-content/themes/my_theme/sdk_files//
  $path = rtrim( parse_url( $base_url )['path'], '/' );
  // /wp-content/themes/my_theme/sdk_files/push/onesignal/
  $scope = $path . '/sdk_files/push/onesignal/';

?>
<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
<script>
window.OneSignalDeferred = window.OneSignalDeferred || [];
OneSignalDeferred.push(async function(OneSignal) {
  await OneSignal.init({
    appId: "<?php echo esc_js( $app_id ); ?>",
    serviceWorkerOverrideForTypical: true,
    path: "<?php echo esc_url( $base_url ) . 'sdk_files/'; ?>",
    serviceWorkerParam: { scope: "<?php echo esc_js( $scope ); ?>" },
    serviceWorkerPath: "OneSignalSDKWorker.js",
  });
});
</script>
<style>
<?php // OneSignalモーダルが最前面に出ないよう対策 ?>
#onesignal-slidedown-container,
#onesignal-popover-container {
  z-index: 99998 !important;
}
</style>
<?php // モバイル端末、A2HSが有効化、インストール可能端末、スタンドアローンモード以外、OneSignalを非表示 ?>
<?php if( wp_is_mobile() && Helper\admin()->get_option( 'a2hs_footer_active' ) ){ ?>
<script>
  window.addEventListener('beforeinstallprompt', (e) => {
    if ( ! window.matchMedia('(display-mode: standalone)').matches ) {
      document.body.classList.add('hide-onesignal-slidedown');
    }
  });
</script>
<style>
body.hide-onesignal-slidedown .onesignal-slidedown-container {
  display: none;
}
</style>
<?php } ?>
<?php
}

/**
 * 投稿タイプ別自動通知
 *
 * NOTE: 自動通知に OneSignal APIを利用
 */
function os_auto_notification( $new_status, $old_status, $post ){

  /**
   * 新規公開 or 予約投稿 から公開に設定した場合のみ発火
   *
   * NOTE: new_status が publish かつ old_status が publish でない場合のみ送信
   */
  $is_publish = $new_status === 'publish' && $old_status !== 'publish';

  // 公開時じゃなければ終了
  if( ! $is_publish ){
    return;
  }

  // APIキー または App ID が登録されていなければ終了
  $api_key = Helper\admin()->get_option( 'webpush_os_api_key' );
  $app_id = Helper\admin()->get_option( 'webpush_os_app_id' );
  if( ! $api_key || ! $app_id ){
    return;
  }

  // 送信フラグ
  $is_send = false;

  // 送信タイトル
  // NOTE: デフォルトはサイトタイトル
  $send_title = sanitize_text_field( get_bloginfo( 'name' ) );

  // 送信メッセージ
  // NOTE: デフォルトは記事タイトル
  $send_message = sanitize_text_field( $post->post_title );

  // 投稿タイプ別設定
  $webpush_os_post_type_settings = Helper\admin()->get_option( 'webpush_os_post_type_settings' );
  if( ! empty( $webpush_os_post_type_settings ) ){
    foreach( $webpush_os_post_type_settings as $webpush_os_post_type => $webpush_os_setting ){

      // 該当する投稿タイプじゃなければ終了
      if( get_post_type( $post ) !== $webpush_os_post_type ){
        continue;
      }

      // 通知が許可されていなければ終了
      $active = $webpush_os_setting['active'] ?? '';
      if( ! $active ){
        continue;
      }

      // 送信フラグをON
      $is_send = true;

      // 送信タイトルが入力されていれば上書き
      $webpush_os_title = $webpush_os_setting['title'] ?? '';
      if( $webpush_os_title ){
        $send_title = sanitize_text_field( $webpush_os_title );
      }

      // 送信メッセージが入力されていれば上書き
      $webpush_os_message = $webpush_os_setting['message'] ?? '';
      if( $webpush_os_message ){
        $send_message = sanitize_text_field( $webpush_os_message );
      }
    }
  }

  // 送信フラグがFFだったら終了
  if( ! $is_send ){
    return;
  }

  // ここからAPI送信設定

  // OneSignal API に渡すデータ
  $fields = [
    'app_id'   => $app_id,
    'headings' => [ 'en' => $send_title ],
    'contents' => [ 'en' => $send_message ],
    'isAnyWeb' => true,
    'included_segments' => ['All'], // 全ユーザーに通知したい場合
    'url' => get_permalink( $post->ID ),
  ];

  // サイトアイコンのセット
  $icon192 = get_site_icon_url( 192 );
  if ( $icon192 ) {
    $fields['firefox_icon']     = $icon192;
    $fields['chrome_web_icon']  = $icon192;
    $fields['chrome_web_image'] = get_site_icon_url();
  }

  // HTTPリクエスト用の引数を整形
  $args = [
    'headers' => [
      // OneSignalの認証キー
      // "Basic" または "Key" の形式が正しいか確認
      'Authorization' => 'Key ' . $api_key,
      'accept'        => 'application/json',
      'content-type'  => 'application/json',
    ],
    'body' => json_encode( $fields ),
  ];

  // OneSignalのAPIエンドポイントへ送信
  $response = wp_remote_post(
    'https://onesignal.com/api/v1/notifications',
    $args
  );

  // エラー時はログに記録
  if ( is_wp_error( $response ) ) {
    error_log( 'API request failed: ' . $response->get_error_message() );
  }
}