<?php

namespace TCD\PWA;

use TCD\PWA\Helper;

/**
 * 管理画面の設定ページの作成
 */
class AdminSettings {

  /**
   * 設定ページのスラッグ
   *
   * @var string
   */
  public string $page_slug = '';

  /**
   * オプション名
   *
   * @var string
   */
  public string $option_name = '';

  /**
   * デフォルト値
   *
   * @var array
   */
  public array $defaults = [];

  /**
   * 必要なフックを登録
   */
  public function __construct() {

    // 設定ページのスラッグをセット
    $this->page_slug = 'tcd-pwa';

    // オプション名のセット
    $this->option_name = 'tcd_pwa_options';

    // デフォルト値のセット
    $this->defaults = [
      // A2HS
      'a2hs_footer_active'     => 1,
      'a2hs_footer_label'      => Helper\__( 'Add to Home Screen' ),
      'a2hs_footer_desc'       => Helper\__( 'Receive special deals with the app!' ),
      'a2hs_footer_font_color' => '#ffffff',
      'a2hs_footer_bg_color'   => '#222222',
      'a2hs_footer_button_label' => Helper\__( 'Get' ),
      'a2hs_footer_button_font_color' => '#ffffff',
      'a2hs_footer_button_bg_color' => '#0066cc',
      // A2HS iOS
      'a2hs_ios_footer_active'        => 0,
      'a2hs_ios_footer_label'         => '',
      'a2hs_ios_footer_desc'          => '',
      'a2hs_ios_footer_button_label'  => '',
      'a2hs_ios_footer_button_link'   => '',
      // WebPush
      'webpush_os_active' => 0,
      'webpush_os_app_id' => '',
      'webpush_os_api_key' => '',
      'webpush_os_post_type_settings' => [
        'post' => [
          'active' => 1,
          'title' => get_bloginfo( 'name' ),
          'message' => Helper\__( 'A new article has been published!' ),
        ],
        'page' => [
          'active' => 0,
          'title' => '',
          'message' => '',
        ]
      ],
    ];

    // メニューの追加
    add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );

    // 設定の追加
    add_action( 'admin_init', [ $this, 'add_admin_settings' ] );

    // 設定値のマージ
    add_action( "option_{$this->option_name}", [ $this, 'merge_options' ] );

    // 設定保存時のサニタイズ処理
    add_filter( "sanitize_option_{$this->option_name}", [ $this, 'sanitize_options' ] );

    // 管理画面の CSS / JS の読み込み
    add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
  }

  /**
   * メニューの追加
   */
  public function add_admin_menu() {
    $page_hook = add_menu_page(
      Helper\__( 'PWA Settings' ),
      Helper\__( 'PWA Settings' ),
      'manage_options',
      $this->page_slug,
      [ $this, 'load_template' ],
      'dashicons-admin-generic',
      2
    );
  }

  /**
   * 設定ページの読み込み
   */
  public function load_template() {
    require_once TCDPWA_PATH . 'include/view-admin-page.php';
  }

  /**
   * 設定データの登録
   */
  public function add_admin_settings() {

    // 設定データ登録
    register_setting(
      "{$this->option_name}_group",
      $this->option_name,
      [
        'type' => 'array',
        'description' => '',
        'sanitize_callback' => null,
        'show_in_rest' => false,
        'default' => $this->get_default()
      ]
    );
  }

  /**
   * 設定データのマージ
   */
  public function merge_options( $value ) {
    return shortcode_atts(
      $this->get_default(),
      $value
    );
  }

  /**
   * 設定保存時のサニタイズ処理
   */
  public function sanitize_options( $value ) {

    // 投稿タイプ別プッシュ通知設定
    $saved_webpush_os_post_type_settings = [];
    $webpush_os_post_type_settings = $value['webpush_os_post_type_settings'];
    if ( is_array( $webpush_os_post_type_settings ) && ! empty( $webpush_os_post_type_settings ) ) {
      foreach( $webpush_os_post_type_settings as $post_type => $setting ){
        $saved_webpush_os_post_type_settings[$post_type] = [
          'active'  => ! empty( $setting['active'] ) ? 1 : 0,
          'title'   => wp_filter_nohtml_kses( $setting['title'] ?? '' ),
          'message' => wp_filter_nohtml_kses( $setting['message'] ?? '' ),
        ];
      }
    }

    return [
      // A2HS
      'a2hs_footer_active'            => ! empty( $value['a2hs_footer_active'] ) ? 1 : 0,
      'a2hs_footer_label'             => wp_kses_post( $value['a2hs_footer_label'] ),
      'a2hs_footer_desc'              => wp_kses_post( $value['a2hs_footer_desc'] ),
      'a2hs_footer_font_color'        => sanitize_hex_color( $value['a2hs_footer_font_color'] ),
      'a2hs_footer_bg_color'          => sanitize_hex_color( $value['a2hs_footer_bg_color'] ),
      'a2hs_footer_button_label'      => wp_filter_nohtml_kses( $value['a2hs_footer_button_label'] ),
      'a2hs_footer_button_font_color' => sanitize_hex_color( $value['a2hs_footer_button_font_color'] ),
      'a2hs_footer_button_bg_color'   => sanitize_hex_color( $value['a2hs_footer_button_bg_color'] ),
      // A2HS iOS
      'a2hs_ios_footer_active'        => ! empty( $value['a2hs_ios_footer_active'] ) ? 1 : 0,
      'a2hs_ios_footer_label'         => wp_kses_post( $value['a2hs_ios_footer_label'] ),
      'a2hs_ios_footer_desc'          => wp_kses_post( $value['a2hs_ios_footer_desc'] ),
      'a2hs_ios_footer_button_label'  => wp_filter_nohtml_kses( $value['a2hs_ios_footer_button_label'] ),
      'a2hs_ios_footer_button_link'   => wp_filter_nohtml_kses( $value['a2hs_ios_footer_button_link'] ),
      // WebPush
      'webpush_os_active'             => ! empty( $value['webpush_os_active'] ) ? 1 : 0,
      'webpush_os_app_id'             => wp_filter_nohtml_kses( $value['webpush_os_app_id'] ),
      'webpush_os_api_key'            => wp_filter_nohtml_kses( $value['webpush_os_api_key'] ),
      'webpush_os_post_type_settings' => $saved_webpush_os_post_type_settings,
    ];
  }

  /**
   * 管理画面の CSS / JS の読み込み
   */
  public function admin_enqueue_scripts() {
    global $plugin_page;
    if( $plugin_page === $this->page_slug ){

      // カラーピッカー
      wp_enqueue_script( 'wp-color-picker' );
      wp_enqueue_style( 'wp-color-picker' );

      // 管理画面用の CSS と JS
      Helper\enqueue_style( $this->page_slug, '/assets/css/admin.css' );
      Helper\enqueue_script( $this->page_slug, '/assets/js/admin.js' );
    }
  }

  /**
   * 初期値
   */
  public function get_default( $key = null ) {

    // キーの指定がなければ初期値をすべて返す
    if( is_null( $key ) ){
      return $this->defaults;
    }

    // キーがあれば、特定のデフォルト値を返す
    if( isset( $this->defaults[$key] ) ){
      return $this->defaults[$key];
    }

    // キーがなければ空文字を返す
    return '';
  }

  /**
   * オプションの取得
   */
  public function get_option( $key = null ) {

    // オプションをすべて取得
    $options = get_option(
      $this->option_name,
      $this->get_default()
    );

    // キーの指定がなければオプションをすべて返す
    if( is_null( $key ) ){
      return $options;
    }

    // キーが存在していれば特定の値を返す
    if( isset( $options[$key] ) ){
      return $options[$key];
    }

    // キーが存在しなければ初期値を返す
    return $this->get_default( $key );
  }
}

/**
 * インスタンス化
 */
$tcd_pwa_admin_settings = new AdminSettings();