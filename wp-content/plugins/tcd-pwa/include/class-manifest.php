<?php

namespace TCD\PWA;

/**
 * Web Application Manifest の対応
 *
 * NOTE: PWAのベースとなる機能
 * ルートディレクトリに擬似的にjsonファイルを設置して読み込む
 * 正常に動作しない時は、リライトルールの再生成を試す
 */
class Manifest {

  /**
   * クエリ変数名
   *
   * @var string
   */
  private string $query_var = '';

  /**
   * JSON ファイル名
   *
   * @var string
   */
  private string $json_filename = '';

  /**
   * 必要なフックを登録
   */
  public function __construct() {

    // 変数のセット
    $this->query_var     = 'tcdpwa_manifest';
    $this->json_filename = 'tcdpwa-manifest.json';

    // リライトルールの追加
    add_action( 'init', [ $this, 'add_rewrite_rule' ] );

    // クエリ変数の追加
    add_filter( 'query_vars', [ $this, 'add_query_var' ] );

    // manifest.jsonの出力
    add_action( 'template_redirect', [ $this, 'output_manifest_json' ] );

    // <head>に<link rel="manifest">を追加
    add_action( 'wp_head', [ $this, 'add_manifest_link' ], 7 );

    // apple touch iconだけ書き換える場合
    // NOTE: 180 * 180 の画像が必要、512 * 512に変更？
    // add_filter( 'site_icon_meta_tags', function( $meta_tags ){
    //   foreach ( $meta_tags as $index => $tag ) {
    //     // apple-touch-icon が含まれるタグを検出
    //     if ( false !== strpos( $tag, 'apple-touch-icon' ) ) {
    //       // 別のタグに置き換える
    //       $meta_tags[ $index ] = sprintf(
    //           '<link rel="apple-touch-icon" href="%s"/>',
    //           esc_url( get_template_directory_uri() . '/images/apple-touch-icon.png' )
    //       );
    //     }
    //   }
    //   return $meta_tags;
    // } );
  }

  /**
   * manifest.jsonへのリライトルールを追加
   *
   * NOTE: manifest.jsonを動的に作成する必要があるため
   */
  public function add_rewrite_rule() {

    // .json の部分を正規表現エスケープしておく
    $escaped_json_filename = preg_quote( $this->json_filename, '/' );
    add_rewrite_rule(
      '^' . $escaped_json_filename . '$',
      'index.php?' . $this->query_var . '=1',
      'top'
    );
  }

  /**
   * クエリ変数に 'manifest' を追加
   */
  public function add_query_var( $vars ) {
    $vars[] = $this->query_var;
    return $vars;
  }

  /**
   * 指定のクエリ変数が入っていれば JSON を出力
   */
  public function output_manifest_json() {

    // 指定したクエリ変数じゃなければ終了
    if( ! get_query_var( $this->query_var ) ) {
      return;
    }

    // 〜110テーマの高速化機能のデバッグメッセージを表示しない
    // NOTE: Memory usage〜 のメッセージを表示するとjsonファイルにエラーが発生する
    remove_action( 'shutdown', 'tcd_acceleration_ob_end', 1 );
    add_filter( 'tcd_acceleration_debug_massage', '__return_empty_string' );

    // JSON を返すヘッダー
    header( 'Content-Type: application/json; charset=UTF-8' );
    // キャッシュ抑制 アイコンなど変更時に反映されないため
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // WordPress から取得できる基本情報
    $site_name = get_bloginfo( 'name' ) ?: '';

    // カスタマイザーのサイトアイコン (192px, 512px)
    $icon192 = get_site_icon_url( 192 );
    $icon512 = get_site_icon_url( 512 );

    // サイトのトップページURL
    $start_url = home_url( '/' );

    // Manifestのベース構造
    $manifest = [
      'name'        => $site_name,
      'short_name'  => $site_name,
      'start_url'   => $start_url,
      'theme_color' => '#000000',
      'display'     => 'standalone',
      'icons'       => [],
    ];

    // サイトアイコンがあれば追加
    if ( $icon192 ) {
      $manifest['icons'][] = [
        'src'   => $icon192,
        'sizes' => '192x192',
        'type'  => 'image/png',
      ];
    }
    if ( $icon512 ) {
      $manifest['icons'][] = [
        'src'   => $icon512,
        'sizes' => '512x512',
        'type'  => 'image/png',
      ];
    }

    echo json_encode( $manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
    exit; // 出力して終了
  }

  /**
   * <head>に<link rel="manifest">を追加
   */
  public function add_manifest_link() {
    global $wp_rewrite;

    // manifest用jsonファイルのURL
    $manifest_url = $wp_rewrite->using_permalinks()
      // パーマリンク設定が基本以外、リライトルールを利用
      ? home_url( $this->json_filename )
      // パーマリンク設定が基本の場合、パラメータで処理
      : add_query_arg( [ $this->query_var => 1 ], home_url( 'index.php' ) );

    echo '<link rel="manifest" href="' . esc_url( $manifest_url ) . '">' . "\n";
  }
}

/**
 * インスタンス化
 */
$tcd_pwa_manifest = new Manifest();