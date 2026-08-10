<?php

namespace TCD\PWA\A2HS;

use TCD\PWA\Helper;

/**
 * クリックでホーム画面にインストールフッターバーを表示する
 */
add_action( 'wp', __NAMESPACE__ . '\wp' );
function wp(){

  // 有効化されていなければ終了
  if( ! Helper\admin()->get_option( 'a2hs_footer_active' ) ){
    return;
  }

  // モバイル端末じゃなければ終了
  if( ! wp_is_mobile() ){
    return;
  }

  // 閉じるボタンを押下していたら終了（期限1日）
  if( $_COOKIE['tcd_footer_a2hs_close'] ?? 0 ){
    return;
  }

  // CSS / JS の読み込み
  add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue', 11 );

  // 出力
  add_action( 'wp_footer', __NAMESPACE__ . '\view' );
}

/**
 * A2HSに必要なCSS/JS
 */
function enqueue(){
  // CSS
  Helper\enqueue_style( 'tcd-pwa-a2hs', '/assets/css/a2hs.css' );
  // JS
  Helper\enqueue_script( 'tcd-pwa-a2hs', '/assets/js/a2hs.js' );
}

/**
 * フッターに出力するA2HSバー
 */
function view(){

  // 必要であればフィルターで書き換え可能
  $args = apply_filters(
    'tcd_pwa_footer_a2hs_args',
    [
      'font_color'        => Helper\admin()->get_option( 'a2hs_footer_font_color' ),
      'bg_color'          => Helper\admin()->get_option( 'a2hs_footer_bg_color' ),
      'label'             => Helper\admin()->get_option( 'a2hs_footer_label' ),
      'desc'              => Helper\admin()->get_option( 'a2hs_footer_desc' ),
      'button_label'      => Helper\admin()->get_option( 'a2hs_footer_button_label' ),
      'button_font_color' => Helper\admin()->get_option( 'a2hs_footer_button_font_color' ),
      'button_bg_color'   => Helper\admin()->get_option( 'a2hs_footer_button_bg_color' ),
      // iOS端末
      'ios_active'        => Helper\admin()->get_option( 'a2hs_ios_footer_active' ),
      'ios_label'         => Helper\admin()->get_option( 'a2hs_ios_footer_label' ),
      'ios_desc'          => Helper\admin()->get_option( 'a2hs_ios_footer_desc' ),
      'ios_button_label'  => Helper\admin()->get_option( 'a2hs_ios_footer_button_label' ),
      'ios_button_link'   => Helper\admin()->get_option( 'a2hs_ios_footer_button_link' ),
    ]
  );

  extract( $args );

  $wrapper_class = '';

  // サイトアイコン
  $site_icon_url = get_site_icon_url();

  $styles = [
    '--tcd-pwa-a2hs-footer-font-color'        => $font_color,
    '--tcd-pwa-a2hs-footer-bg-color'          => $bg_color,
    '--tcd-pwa-a2hs-footer-button-font-color' => $button_font_color,
    '--tcd-pwa-a2hs-footer-button-bg-color'   => $button_bg_color,
  ];

  $output_style = '';
  foreach( $styles as $property => $value ){
    $output_style .= sprintf( '%s:%s;', $property, $value );
  }

  // iOSアクティブ時
  if( $ios_active ){

    // クラス
    $wrapper_class = 'is-uninstallable';

    // ラベル
    if( $ios_label ){
      $label = '<span class="p-a2hs-el-android">' . $label . '</span>';
      $label .= '<span class="p-a2hs-el-ios">' . $ios_label . '</span>';
    }

    // 説明
    if( $ios_desc ){
      $desc = '<span class="p-a2hs-el-android">' . $desc . '</span>';
      $desc .= '<span class="p-a2hs-el-ios">' . $ios_desc . '</span>';
    }

    // ボタン
    if( $ios_button_label ){
      $button_label = '<span class="p-a2hs-el-android">' . $button_label . '</span>';
      $button_label .= '<span class="p-a2hs-el-ios" data-url="' . esc_attr( $ios_button_link ) . '">' . $ios_button_label . '</span>';
    }
  }

?>
<div id="js-tcd-footer-a2hs" class="p-a2hs-footer <?php echo esc_attr( $wrapper_class ); ?>" style="<?php echo esc_attr( $output_style ); ?>">
  <button id="js-tcd-footer-a2hs-close" type="button" class="p-a2hs-footer-button p-a2hs-footer-close">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" width="1em" height="1em" fill="currentColor"><path d="M256-227.69 227.69-256l224-224-224-224L256-732.31l224 224 224-224L732.31-704l-224 224 224 224L704-227.69l-224-224-224 224Z"/></svg>
  </button>
  <?php if( $site_icon_url ){ ?>
    <div class="p-a2hs-footer-icon">
      <img class="p-a2hs-footer-icon-image" src="<?php echo esc_url( $site_icon_url ); ?>" alt="">
    </div>
  <?php } ?>
  <div class="p-a2hs-footer-contents">
    <?php if( $label ){ ?>
      <p class="p-a2hs-footer-contents-title">
        <?php echo wp_kses_post( $label ); ?>
      </p>
    <?php } ?>
    <?php if( $desc ){ ?>
      <p class="p-a2hs-footer-contents-desc">
        <?php echo wp_kses_post( $desc ); ?>
      </p>
    <?php } ?>
  </div>
  <button id="js-tcd-footer-a2hs-install" type="button" class="p-a2hs-footer-button p-a2hs-footer-install">
    <?php echo wp_kses_post( $button_label ); ?>
  </button>
</div>
<?php

}
