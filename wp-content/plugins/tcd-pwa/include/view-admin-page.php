<?php

/**
 * 管理画面のPWA設定ページ用テンプレート
 */

use function TCD\PWA\Helper\__;
use function TCD\PWA\Helper\_e;
use function TCD\PWA\Helper\admin;

// name属性の出力を簡易化
$name = function( $key, $subkey = '' ){
  echo sprintf(
    '%s[%s]%s',
    esc_attr( admin()->option_name ),
    esc_attr( $key ),
    $subkey ? '[' . esc_attr( $subkey ) . ']' : ''
  );
};

// value値の取得を簡易化
$value = function( $key ){
  echo esc_attr( admin()->get_option( $key ) );
};

?>
<div class="tcd-pwa-page">
  <h2 class="tcd-pwa-page-title">
    <?php echo get_admin_page_title(); ?>
  </h2>
  <p class="tcd-pwa-page-desc">
    <?php _e( 'PWA (Progressive Web Apps) is a website mechanism that can be launched like an app from the home screen of a smartphone or other device.' ); ?><br>
    <a href=" https://tcd-theme.com/2025/05/tcd-pwa.html" target="_blank">
      <?php _e( 'Click here to learn how to use PWA settings.' ); ?>
    </a>
  </p>
  <form id="js-tcd-pwa-page-form" class="tcd-pwa-page-form" method="post" action="options.php">
    <?php settings_fields( admin()->option_name . '_group' ); ?>
<?php
/**
 * 基本設定
 */
?>
    <h3 class="tcd-pwa-page-form-title">
      <?php _e( 'Basic setting' ); ?>
    </h3>
    <p>
      <?php _e( 'SSL and site icon settings are required to activate PWA settings.' ) ?><br>
      <?php _e( 'If not set, set the following in order.' ) ?>
    </p>
    <?php // SSLチェック ?>
    <h4 class="tcd-pwa-page-form-subtitle">
      <?php _e( 'SSL (HTTPS) conversion' ); ?>
    </h4>
    <?php if( is_ssl() ){ ?>
      <div class="tcd-pwa-page-form-notice" style="border-color:#00c554;">
        <?php _e( 'SSL-enabled.' ) ?>
      </div>
    <?php }else{ ?>
      <div class="tcd-pwa-page-form-notice" style="border-color:#d90000;">
        <?php printf( __( 'SSL is not enabled. Please refer to <a href="%s" target="_blank">here</a> to convert to SSL.' ), 'https://tcd-theme.com/2018/07/ssl-conversion.html' ) ?>
      </div>
    <?php } ?>
    <?php // サイトアイコンチェック ?>
    <h4 class="tcd-pwa-page-form-subtitle">
      <?php _e( 'Site Icon' ); ?>
    </h4>
    <?php if( has_site_icon() ){ ?>
      <div class="tcd-pwa-page-form-notice" style="border-color:#00c554;">
        <?php _e( 'The site icon is set.' ) ?>
      </div>
    <?php }else{ ?>
      <div class="tcd-pwa-page-form-notice" style="border-color:#d90000;">
        <?php printf( __( 'The site icon is not yet set. Please refer to <a href="%s" target="_blank">this page</a> to set up.' ), 'https://tcd-theme.com/2021/02/wp-favicon-setting.html' ) ?>
      </div>
    <?php } ?>
<?php
/**
 * A2HS設定
 */
?>
    <h3 class="tcd-pwa-page-form-title">
      <?php _e( 'A2HS (Android devices only)' ); ?>
    </h3>
    <p>
      <?php _e( 'A2HS (Add to Home Screen) is a feature that allows a website to be added to the home screen on a mobile device.' ) ?><br>
      <?php _e( 'When activated, the footer displays a follow-up button that prompts the user to install the application.' ) ?><br>
      <b><?php _e( 'Currently iOS devices do not support this feature.' ) ?></b>
    </p>
    <div class="tcd-pwa-page-form-section">
      <div class="tcd-pwa-page-form-section-image">
        <img src="<?php echo TCDPWA_URL . '/assets/image/a2hs.jpg?v=' . TCDPWA_VER ?>">
      </div>
      <div class="tcd-pwa-page-form-section-contents">
        <?php // A2HS機能を利用する ?>
        <label class="tcd-pwa-page-form-input-check">
          <input type="checkbox" class="" name="<?php $name( 'a2hs_footer_active' ); ?>" value="1" <?php checked( 1, admin()->get_option( 'a2hs_footer_active' ) ); ?>/>
          <?php _e( 'Use the A2HS function' ); ?>
        </label>
        <div class="tcd-pwa-page-form-checked-section-disabled">
          <span class="tcd-pwa-page-form-section-label">
            <?php _e( 'Label' ); ?>
          </span>
          <input type="text" name="<?php $name( 'a2hs_footer_label' ); ?>" value="<?php $value( 'a2hs_footer_label' ); ?>" class="tcd-pwa-page-form-input-text" placeholder="<?php echo admin()->get_default( 'a2hs_footer_label' ) ?>">
          <span class="tcd-pwa-page-form-section-label">
            <?php _e( 'Description' ); ?>
          </span>
          <input type="text" name="<?php $name( 'a2hs_footer_desc' ); ?>" value="<?php $value( 'a2hs_footer_desc' ); ?>" class="tcd-pwa-page-form-input-text" placeholder="<?php echo admin()->get_default( 'a2hs_footer_desc' ) ?>">
          <span class="tcd-pwa-page-form-section-label">
            <?php _e( 'Font color' ); ?>
          </span>
          <input type="text" name="<?php $name( 'a2hs_footer_font_color' ); ?>" value="<?php $value( 'a2hs_footer_font_color' ); ?>" data-default-color="#ffffff" class="js-tcd-pwa-color-pucker"/>
          <span class="tcd-pwa-page-form-section-label">
            <?php _e( 'Background color' ); ?>
          </span>
          <input type="text" name="<?php $name( 'a2hs_footer_bg_color' ); ?>" value="<?php $value( 'a2hs_footer_bg_color' ); ?>" data-default-color="#222222" class="js-tcd-pwa-color-pucker"/>
          <span class="tcd-pwa-page-form-section-label">
            <?php _e( 'Button label' ); ?>
          </span>
          <input type="text" name="<?php $name( 'a2hs_footer_button_label' ); ?>" value="<?php $value( 'a2hs_footer_button_label' ); ?>" class="tcd-pwa-page-form-input-text" placeholder="<?php echo admin()->get_default( 'a2hs_footer_button_label' ) ?>">
          <span class="tcd-pwa-page-form-section-label">
            <?php _e( 'Button Font color' ); ?>
          </span>
          <input type="text" name="<?php $name( 'a2hs_footer_button_font_color' ); ?>" value="<?php $value( 'a2hs_footer_button_font_color' ); ?>" data-default-color="#ffffff" class="js-tcd-pwa-color-pucker"/>
          <span class="tcd-pwa-page-form-section-label">
            <?php _e( 'Button background color' ); ?>
          </span>
          <input type="text" name="<?php $name( 'a2hs_footer_button_bg_color' ); ?>" value="<?php $value( 'a2hs_footer_button_bg_color' ); ?>" data-default-color="#0066cc" class="js-tcd-pwa-color-pucker"/>
          <?php // iOS対応 ?>
          <span class="tcd-pwa-page-form-section-label">
            <?php _e( 'iOS device support' ); ?>
          </span>
          <div class="tcd-pwa-page-form-note">
            <?php _e( 'iOS devices do not support this feature. Instead, any URL can be specified as a button and displayed.' ); ?>
            <?php _e( 'The wording can also be changed, so if you wish to display it, please check the box below.' ); ?>
          </div>
          <label class="tcd-pwa-page-form-input-check" style="margin-top:20px;">
            <input type="checkbox" name="<?php $name( 'a2hs_ios_footer_active' ); ?>" value="1" <?php checked( 1, admin()->get_option( 'a2hs_ios_footer_active' ) ); ?>/>
            <?php _e( 'Display on iOS devices as well.' ); ?>
          </label>
          <div class="tcd-pwa-page-form-checked-section-hide">
            <span class="tcd-pwa-page-form-section-label">
              <?php _e( 'Labels for iOS devices' ); ?>
            </span>
            <input type="text" name="<?php $name( 'a2hs_ios_footer_label' ); ?>" value="<?php $value( 'a2hs_ios_footer_label' ); ?>" class="tcd-pwa-page-form-input-text" placeholder="<?php echo admin()->get_default( 'a2hs_ios_footer_label' ) ?>">
            <span class="tcd-pwa-page-form-section-label">
              <?php _e( 'Description for iOS devices' ); ?>
            </span>
            <input type="text" name="<?php $name( 'a2hs_ios_footer_desc' ); ?>" value="<?php $value( 'a2hs_ios_footer_desc' ); ?>" class="tcd-pwa-page-form-input-text" placeholder="<?php echo admin()->get_default( 'a2hs_ios_footer_desc' ) ?>">
            <span class="tcd-pwa-page-form-section-label">
              <?php _e( 'Button label for iOS devices' ); ?>
            </span>
            <input type="text" name="<?php $name( 'a2hs_ios_footer_button_label' ); ?>" value="<?php $value( 'a2hs_ios_footer_button_label' ); ?>" class="tcd-pwa-page-form-input-text" placeholder="<?php echo admin()->get_default( 'a2hs_ios_footer_button_label' ) ?>">
            <span class="tcd-pwa-page-form-section-label">
              <?php _e( 'URL of the button link for iOS devices' ); ?>
            </span>
            <div class="tcd-pwa-page-form-note" style="margin-bottom:15px;">
              <?php _e( 'It would be helpful if the link specifies a page that explains how to add it to the home screen.' ); ?><br>
              <a href="https://tcd-theme.com/2025/05/tcd-pwa.html#ios" target="_blank">
                <?php _e( 'How to add to the home screen in iOS' ); ?>
              </a>
            </div>
            <input type="text" name="<?php $name( 'a2hs_ios_footer_button_link' ); ?>" value="<?php $value( 'a2hs_ios_footer_button_link' ); ?>" class="tcd-pwa-page-form-input-text" placeholder="">
          </div>
        </div>
        <label class="tcd-pwa-page-form-submit js-tcd-pwa-page-form-submit">
          <input type="submit" class="" value="<?php _e( 'Save Changes' ); ?>">
          <span class="tcd-pwa-page-form-submit-success">
            <?php _e( 'Settings saved' ); ?>
          </span>
        </label>
      </div>
    </div>
<?php
/**
 * Webプッシュ設定
 */
?>
    <h3 class="tcd-pwa-page-form-title">
      <?php _e( 'Web push notification' ); ?>
    </h3>
    <p>
      <?php _e( 'This function allows push notifications to be sent to PCs and mobile devices via the browser.' ) ?><br>
      <b><?php _e( 'iOS devices can send push notifications only when they are added to the home screen.' ) ?></b>
    </p>
    <?php // Webプッシュ通知を利用する ?>
    <label class="tcd-pwa-page-form-input-check" style="margin-top:30px;">
      <input type="checkbox" class="" name="<?php $name( 'webpush_os_active' ); ?>" value="1" <?php checked( 1, admin()->get_option( 'webpush_os_active' ) ); ?>/>
      <?php _e( 'Use Web Push Notifications' ); ?>
    </label>
    <div class="tcd-pwa-page-form-checked-section-disabled">
      <?php // 基本設定 ?>
      <h4 class="tcd-pwa-page-form-subtitle">
        <?php _e( 'Basic setting' ); ?>
      </h4>
      <div class="tcd-pwa-page-form-note">
        <?php _e( 'To send Web push notification function, it is necessary to link with the external service "OneSignal".' ); ?><br>
        <?php _e( 'Get APP ID and API KEY and enter them below.' ); ?><br>
        <a href="https://tcd-theme.com/2017/01/wordpress-push.html#OneSignal" target="_blank">
          <?php _e( 'Please click here for the initial setup on how to obtain APP ID and API KEY.' ); ?>
        </a>
      </div>
      <span class="tcd-pwa-page-form-section-label">
        OneSignal App ID
      </span>
      <input type="text" name="<?php $name( 'webpush_os_app_id' ); ?>" value="<?php $value( 'webpush_os_app_id' ); ?>" class="tcd-pwa-page-form-input-text">
      <span class="tcd-pwa-page-form-section-label">
        OneSignal API Key
      </span>
      <input type="text" name="<?php $name( 'webpush_os_api_key' ); ?>" value="<?php $value( 'webpush_os_api_key' ); ?>" class="tcd-pwa-page-form-input-text">
      <?php // プッシュ通知の自動送信設定 ?>
      <h4 class="tcd-pwa-page-form-subtitle">
        <?php _e( 'Automatic push notification settings' ); ?>
      </h4>
      <div class="tcd-pwa-page-form-note">
        <?php _e( 'This setting allows push notifications to be sent automatically when a page is published.' ); ?><br>
        <?php _e( 'The activation and message can be set for each post type.' ); ?>
      </div>
<?php

// 投稿タイプ別の通知設定

// 登録されているデフォルトの投稿タイプ
$defualt_post_types = [
  'post',
  'page'
];

$built_in_post_types = array_keys(
  get_post_types(
    [
      'public'   => true,
      '_builtin' => false,
    ]
  )
);

$current_post_types = array_merge(
  $defualt_post_types,
  $built_in_post_types
);

// 設定
$webpush_os_post_type_settings = admin()->get_option( 'webpush_os_post_type_settings' );
foreach( $current_post_types as $current_post_type ){

?>
      <label class="tcd-pwa-page-form-input-check" style="margin-top:20px;">
        <input type="checkbox" name="<?php $name( 'webpush_os_post_type_settings', $current_post_type ); ?>[active]" value="1" <?php checked( 1, $webpush_os_post_type_settings[$current_post_type]['active'] ?? 0 ); ?>/>
        <?php printf( __( 'Allow notifications with "%s".' ), get_post_type_object( $current_post_type )?->label ); ?>
      </label>
      <div class="tcd-pwa-page-form-checked-section-hide">
        <span class="tcd-pwa-page-form-section-label">
          <?php _e( 'Transmission Title' ); ?>
        </span>
        <input type="text" name="<?php $name( 'webpush_os_post_type_settings', $current_post_type ); ?>[title]" value="<?php echo esc_attr( $webpush_os_post_type_settings[$current_post_type]['title'] ?? '' ); ?>" class="tcd-pwa-page-form-input-text" placeholder="<?php _e( 'If not entered, the site title is reflected.' ); ?>">
        <span class="tcd-pwa-page-form-section-label">
          <?php _e( 'Send Message' ); ?>
        </span>
        <input type="text" name="<?php $name( 'webpush_os_post_type_settings', $current_post_type ); ?>[message]" value="<?php echo esc_attr( $webpush_os_post_type_settings[$current_post_type]['message'] ?? '' ); ?>" class="tcd-pwa-page-form-input-text" placeholder="<?php _e( 'If not entered, the article title is reflected.' ); ?>">
      </div>
<?php

}

?>
    </div>
    <label class="tcd-pwa-page-form-submit js-tcd-pwa-page-form-submit">
      <input type="submit" class="" value="<?php _e( 'Save Changes' ); ?>">
      <span class="tcd-pwa-page-form-submit-success">
        <?php _e( 'Settings saved' ); ?>
      </span>
    </label>
  </form>
</div>