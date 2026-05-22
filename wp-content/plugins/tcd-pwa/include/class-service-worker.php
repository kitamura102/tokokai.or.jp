<?php

namespace TCD\PWA;

/**
 * Service Workerの登録
 */
class ServiceWorker {

  /**
   * クエリ変数名
   *
   * @var string
   */
  private string $query_var = '';

  /**
   * jsファイル名
   *
   * @var string
   */
  private string $js_filename = '';

  /**
   * 必要なフックを登録
   */
  public function __construct() {

    // 変数のセット
    $this->query_var     = 'tcd_pwa_sw';
    $this->js_filename   = 'tcd-pwa-sw.js';

    // リライトルールの追加
    add_action( 'init', [ $this, 'add_rewrite_rule' ] );

    // クエリ変数の追加
    add_filter( 'query_vars', [ $this, 'add_query_var' ] );

    // URLの正規化を無効化
    add_filter( 'redirect_canonical', [ $this, 'disable_redirect_canonical' ] );

    // jsファイルの出力
    add_action( 'template_redirect', [ $this, 'output_sw_js' ] );

    // <head>にscriptを追加
    add_action( 'wp_head', [ $this, 'add_sw_script' ], 7 );
  }

  /**
   * Service Workerファイルへのリライトルールを追加
   *
   * NOTE: ルートディレクトリにファイルを置く必要があるため
   */
  public function add_rewrite_rule() {

    // .js の部分を正規表現エスケープしておく
    $escaped_js_filename = preg_quote( $this->js_filename, '/' );
    add_rewrite_rule(
      '^' . $escaped_js_filename . '$',
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
   * URLの正規化を無効化
   *
   * NOTE: WordPressのURLの末尾にスラッシュを付ける処理が走るとリダイレクトが発生
   * service worakerは、リダイレクトが発生すると動作しない
   */
  public function disable_redirect_canonical( $redirect_url ) {
    // クエリ変数がセットされているときはリダイレクト無効化
    if ( get_query_var( $this->query_var ) ) {
      return false;
    }
    return $redirect_url;
  }

  /**
   * リクエストが Service Worker用jsの場合に、jsファイルを出力する
   */
  public function output_sw_js() {
    // クエリ変数がセットされていればjsファイルを返す
    if ( get_query_var( $this->query_var ) ) {

      // 〜110テーマの高速化機能のデバッグメッセージを表示しない
      remove_action( 'shutdown', 'tcd_acceleration_ob_end', 1 );
      add_filter( 'tcd_acceleration_debug_massage', '__return_empty_string' );

      // JavaScriptとして返すヘッダー
      header( 'Content-Type: application/javascript; charset=UTF-8' );

      // tcdpwa-sw.js を読み込む
      $sw_file_path = TCDPWA_PATH . '/assets/js/' . $this->js_filename;

      // ファイルが無ければ404
      if( ! file_exists( $sw_file_path ) ) {
				status_header( 404 );
        exit( '/* tcd-pwa-sw.js not found */' );
      }

      // ファイル内容をそのまま出力
      readfile($sw_file_path);
      exit;
    }
  }

  /**
   * sw用スクリプト登録
   */
  function add_sw_script(){
    global $wp_rewrite;

    // service worker用ファイルのURL
    $url = $wp_rewrite->using_permalinks()
      // パーマリンク設定が基本以外、リライトルールを利用
      ? home_url( '/' . $this->js_filename )
      // パーマリンク設定が基本の場合はパラメータで処理
      : add_query_arg( $this->query_var, 1, home_url( 'index.php' ) );
?>
<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      // ここでルート直下を指すURLを指定
      navigator.serviceWorker.register('<?php echo esc_url( $url ); ?>')
        .then(reg => {
          console.log('Service Worker registered. Scope is:', reg.scope);
        })
        .catch(err => {
          console.error('Service Worker registration failed:', err);
        });
    });
  }
</script>
<?php

  }

}

/**
 * インスタンス化
 */
$tcd_pwa_sw = new ServiceWorker();