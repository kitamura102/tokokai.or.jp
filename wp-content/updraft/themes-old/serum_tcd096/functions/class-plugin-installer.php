<?php

namespace TCD\Modules;

/**
 * 任意のプラグインを未インストール or 未有効化の場合に、
 * 管理画面に通知を表示してインストール/有効化URLを生成するクラス。
 */
if ( ! class_exists( 'TCD\Modules\PluginInstaller' ) ) {
  class PluginInstaller {

    /**
     * プラグインのディレクトリ・スラッグ
     * 例: contact-form-7
     *
     * @var string
     */
    private $plugin_slug;

    /**
     * プラグインのメインファイルパス (プラグインディレクトリから見た相対パス)
     * 例: contact-form-7/wp-contact-form-7.php
     *
     * @var string
     */
    private $plugin_file;

    /**
     * コンストラクタ
     *
     * @param string $plugin_slug  プラグインディレクトリ名 (slug)
     * @param string $plugin_file  プラグインファイル (例: "contact-form-7/wp-contact-form-7.php")
     */
    public function __construct( $plugin_slug, $plugin_file ) {
      $this->plugin_slug = $plugin_slug;
      $this->plugin_file = $plugin_file;

      add_action( 'admin_notices', [ $this, 'show_notice' ] );
    }

    /**
     * プラグインが有効化されているかどうかを判定
     *
     * @return bool
     */
    public function is_plugin_active() {
      if ( ! function_exists( 'is_plugin_active' ) ) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
      }
      return is_plugin_active( $this->plugin_file );
    }

    /**
     * プラグインがインストールされているかどうかを判定
     * （フォルダが存在するかどうかでざっくり判定）
     *
     * @return bool
     */
    public function is_plugin_installed() {
      return file_exists( WP_PLUGIN_DIR . '/' . dirname( $this->plugin_file ) );
    }

    /**
     * 現在の管理画面が「このプラグインをインストール中」の画面かどうかを判定
     *
     * @return bool
     */
    public function is_installing_this_plugin() {
      global $pagenow;
      if(
        'update.php' === $pagenow &&
        'install-plugin' === ( $_GET['action'] ?? '' ) &&
        $this->plugin_slug === ( $_GET['plugin'] ?? '' )
      ){
        return true;
      }
      return false;
    }

    /**
     * プラグインインストールURLを生成
     *
     * /wp-admin/update.php?action=install-plugin&plugin={slug}&_wpnonce={nonce}
     *
     * @return string
     */
    public function get_install_url() {
      return wp_nonce_url(
        self_admin_url( 'update.php?action=install-plugin&plugin=' . $this->plugin_slug ),
        'install-plugin_' . $this->plugin_slug
      );
    }

    /**
     * プラグイン有効化URLを生成
     *
     * /wp-admin/plugins.php?action=activate&plugin={slug}/{main-file}.php&_wpnonce={nonce}
     *
     * @return string
     */
    public function get_activate_url() {
      $url = add_query_arg(
        [
          'action' => 'activate',
          'plugin' => urlencode( $this->plugin_file ),
        ],
        self_admin_url( 'plugins.php' )
      );
      return wp_nonce_url( $url, 'activate-plugin_' . $this->plugin_file );
    }

    /**
     * プラグインの状態に応じた通知を表示する
     * 例）管理画面の admin_notices アクションで呼び出すイメージ
     */
    public function show_notice() {
      global $plugin_page;

      // テーマオプションページ以外では表示しない
      if( $plugin_page !== 'theme_options' ){
        return;
      }

      // すでに有効化済みなら何もしない
      if ( $this->is_plugin_active() ) {
        return;
      }

      // インストール中は表示しない
      if( $this->is_installing_this_plugin() ){
        return;
      }

      // プラグインがインストールされていない場合のみメッセージを表示
      // NOTE: GRAVITYより前のテーマは、TCD Classic Editorが必須ではないため、メッセージの内容を変更
      if ( ! $this->is_plugin_installed() ) {

        // notice作成
        printf(
          '<div class="notice notice-info is-dismissible">
            <p>%1$s</p>
            <p>
              <a class="button" href="%2$s" target="_blank">%3$s</a>
              <a class="button button-primary" href="%4$s">%5$s</a>
            </p>
          </div>',
          // TCDテーマのエディタを拡張するプラグイン「TCDクラシックエディタ」を利用できます。
          __( 'You can use the "TCD Classic Editor" plugin to extend the TCD theme editor.', TCD_TEXTDOMAIN ),
          // 解説記事URL
          'https://tcd-theme.com/2025/02/tcd-classic-editor.html',
          // 設定・使い方
          __( 'Settings/How to use', TCD_TEXTDOMAIN ),
          // インストール用URL
          $this->get_install_url(),
          // 今すぐインストール
          __( 'Install Now', TCD_TEXTDOMAIN )
        );
      }

    }
  }

  /**
   * インスタンス化
   *
   * TCDクラシックエディタプラグイン用
   */
  $tcd_classic_editor = new PluginInstaller(
    // プラグインスラッグ
    'tcd-classic-editor',
    // プラグインファイル
    'tcd-classic-editor/tcd-classic-editor.php'
  );
}
