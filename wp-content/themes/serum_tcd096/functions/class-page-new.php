<?php

namespace TCD\Modules;

// NOTE: 翻訳用関数 以下のテキストドメインをテーマ毎に調整してください。
function __( $text ){
  return \__( $text, 'tcd-serum' );
}

/**
 * 表示設定用の固定ページを自動生成&設定する機能
 *
 * NOTE: テーマ有効化後に自動設定
 * NOTE: 未設定状態になると警告表示
 * この警告のボタンクリックで固定ページ生成&設定可能
 */
if ( ! class_exists( 'TCD\Modules\PageNew' ) ) {
  class PageNew {

    /**
     * 表示設定
     * 「最新の投稿(posts)」 or 「固定ページ(page)」
     *
     * @var string
     */
    public $show_on_front = '';

    /**
     * ホームページの投稿ID
     *
     * @var string
     */
    public $page_on_front = '';

    /**
     * 投稿ページの投稿ID
     *
     * @var string
     */
    public $page_for_posts = '';

    /**
     * 生成ページのクエリーストリング
     *
     * @var string
     */
    public $param = 'tcd-page-new';

    /**
     * 失敗時のメッセージ
     *
     * @var string
     */
    public $failed_message = '';

    /**
     * Constructor
     */
    public function __construct(){

      /**
       * 最初に表示設定のオプションをセット
       */
      $this->set_options();

      /**
       * 生成ページにアクセスした場合
       */
      if( $_GET[$this->param] ?? 0 ){

        // 固定ページの生成と設定
        $set_page_on_front = $this->set_page_on_front();
        $set_page_for_posts = $this->set_page_for_posts();

        // 表示設定のオプションを更新する
        $this->set_options();

        // どちらも成功したら
        if( $set_page_on_front && $set_page_for_posts ){
          add_action( 'admin_notices', [ $this, 'success' ] );
          add_action( 'admin_print_footer_scripts', [ $this, 'inline_script' ] );

        }else{
          // どちらか失敗したら

          // 固定ページの生成がうまくいきませんでした。もう一度お試しください。
          $this->failed_message = __( 'Generating a page did not work. Please try again.' );
          add_action( 'admin_notices', [ $this, 'failed' ] );
        }
      }

      /**
       * テーマ有効化直後に作成、設定する（initのタイミング）
       */
      add_action( 'after_switch_theme', [ $this, 'initial_setup' ] );

      /**
       * 未設定状態になると警告を表示する
       */
      add_action( 'admin_notices', [ $this, 'notice' ] );

    }

    /**
     * オプションをセット
     */
    public function set_options(){

      // ホームページの表示設定を取得
      $this->show_on_front = get_option( 'show_on_front' );

      // ホームページの設定を取得
      $this->page_on_front = get_option( 'page_on_front' );

      // 投稿ページの設定を取得
      $this->page_for_posts = get_option( 'page_for_posts' );

    }

    /**
     * 管理画面の通知
     */
    public function notice(){
      /**
       * ホームページと投稿ページが未設定の場合のみ表示
       */
      if( ! $this->page_on_front || ! $this->page_for_posts ){
        printf(
          '<div class="notice notice-error">
            <p style="font-weight:700;">%s</p>
            <p>
              <a id="js-tcd-page-new-action" class="button" href="%s">%s</a>
            </p>
          </div>',
          // 初期設定に必要な「表示設定」が未完です。以下のボタンを押して完了してください。
          __( 'The "Display Settings" required for initial setup has not been completed. Please click the button below to complete.' ),
          esc_url( add_query_arg( [ $this->param => 1 ], admin_url( 'options-reading.php' ) ) ),
          // 表示設定を完了する
          __( 'Configure display settings.' )
        );
      }
    }

    /**
     * 生成後の成功メッセージ
     */
    public function success(){
      printf(
        "<div class=\"notice notice-success is-dismissible\"><p style=\"font-weight:bold;\">%s</p></div>",
        // 表示設定が完了しました。
        __( 'Display settings have been completed.' )
      );
    }

    /**
     * 生成後のページURLからクエリーストリングを削除する
     */
    public function inline_script(){
      echo "<script>const url = new URL(window.location);url.searchParams.delete('{$this->param}');window.history.replaceState({},'',url);</script>";
    }

    /**
     * 生成失敗時のエラーメッセージ表示
     */
    public function failed(){
      if( $this->failed_message ){
        printf(
          "<div class=\"notice notice-error is-dismissible\"><p style=\"font-weight:bold;\">%s</p></div>",
          esc_html( $this->failed_message )
        );
      }
    }

    /**
     * フロントページの生成
     */
    public function set_page_on_front(){

      // 設定済みかチェック
      if( $this->page_on_front ){
        return true;
      }

      // 新規固定ページ作成
      $new_front_page_id = wp_insert_post( [
        // トップページ
        'post_title'   => __( 'Front page' ),
        'post_content' => '',
        'post_name'    => 'front-page',
        'post_status'  => 'publish',
        'post_type'    => 'page',
      ] );

      // 成功したら保存
      if( $new_front_page_id ){
        if( $this->show_on_front == 'posts' ){
          update_option( 'show_on_front', 'page' );
        }
        update_option( 'page_on_front', $new_front_page_id );
        return true;
      }
      return false;
    }

    /**
     * 投稿ページの生成
     */
    public function set_page_for_posts(){

      // 設定済みかチェック
      if( $this->page_for_posts ){
        return true;
      }

      // 新規固定ページ作成
      $new_home_page_id = wp_insert_post( [
        // ブログ
        'post_title'   => __( 'Blog' ),
        'post_content' => '',
        'post_name'    => 'blog',
        'post_status'  => 'publish',
        'post_type'    => 'page',
      ] );

      // 成功したら保存
      if( $new_home_page_id ){
        if( $this->show_on_front == 'posts' ){
          update_option( 'show_on_front', 'page' );
        }
        update_option( 'page_for_posts', $new_home_page_id );
        return true;
      }
      return false;
    }

    /**
     * テーマ有効化直後に固定ページを作成して設定する
     */
    public function initial_setup(){

      // 固定ページの生成と設定
      $this->set_page_on_front();
      $this->set_page_for_posts();

      // 表示設定のオプションを更新する
      $this->set_options();
    }

  }

  /**
   * インスタンス化
   */
  $tcd_page_new = new PageNew();
}