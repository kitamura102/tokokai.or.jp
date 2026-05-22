<?php

namespace TCD\Modules;

/**
 * TCDテーマで使えるフォントを拡張するシステム
 *
 * NOTE:テーマ内で使えるフォントを組み合わせて利用する
 */
if ( ! class_exists( 'TCD\Modules\FontManager' ) ) {
  class FontManager {

    /**
     * すべてのフォント
     *
     * @var array
     */
    public $font_list = [];

    /**
     * システムフォント和文
     *
     * @var array
     */
    public $system_font_japan = [];

    /**
     * システムフォント欧文
     *
     * @var array
     */
    public $system_font_latin = [];

    /**
     * Webフォント和文
     *
     * @var array
     */
    public $web_font_japan = [];

    /**
     * Webフォント欧文
     *
     * @var array
     */
    public $web_font_latin = [];

    /**
     * 読み込むWebフォント
     *
     * @var array
     */
    public $web_font_source_list = [];

    /**
     * フロントで読み込むフォントリスト
     *
     * @var array
     */
    public $front_font_assets = [];

    /**
     * 管理画面のフォント周りで利用するラベル
     *
     * @var array
     */
    public $admin_font_labels = [];

    /**
     * Constructor
     */
    public function __construct(){

      // システムフォント和文のセット
      $this->set_system_font_japan();

      // システムフォント欧文のセット
      $this->set_system_font_latin();

      // webフォント和文のセット
      $this->set_web_font_japan();

      // webフォント欧文のセット
      $this->set_web_font_latin();

      // テーマオプションから読み込むフォントデータを組み立てる
      $this->build_frontend_font_assets();

      // フロントのwebフォント読み込み
      add_action( 'wp_head', [ $this, 'add_google_fonts_preconnect' ], 7 );

      // フロントにCSSカスタムプロパティを出力
      add_action( 'wp_head', [ $this, 'output_root_custom_properties' ], 8 );

      // CSS変数のセット
      add_filter( 'tcd_current_css_custom_properties', [ $this, 'add_current_font_style' ] );

      // 管理画面
      add_action( 'admin_head', [ $this, 'admin_font_preview_assets' ] );
    }

    /**
     * システムフォント和文のセット
     */
    public function set_system_font_japan(){
      $this->system_font_japan = [
        /**
         * ゴシック体
         */
        'sans-serif' => [
          'label' => __( 'Sans-serif','tcd-serum' ),
          'css' => [ '"Hiragino Sans"', '"Yu Gothic Medium"', '"Meiryo"', 'sans-serif' ],
        ],
        /**
         * 明朝体
         */
        'serif' => [
          'label' => __( 'Serif','tcd-serum' ),
          'css' => [ '"Yu Mincho"', '"游明朝"', '"游明朝体"', '"Hiragino Mincho Pro"', 'serif' ],
        ],
        /**
         * 教科書体
         */
        'kyokasho' => [
          'label' => __( 'Kyokasho','tcd-serum' ),
          'css' => [ '"Yu Kyokasho"', '"游教科書体"', '"UD デジタル 教科書体 N"', '"游明朝"', '"游明朝体"', '"Hiragino Mincho Pro"', '"Meiryo"', 'serif' ],
        ]
      ];

      // すべてのフォントリストにも登録
      $this->font_list += $this->system_font_japan;
    }

    /**
     * システムフォント欧文のセット
     */
    public function set_system_font_latin(){

      // サンセリフ
      $sans_serif = [
        /**
         * Arial
         */
        'arial' => [
          'label' => 'Arial',
          'css' => [ 'Arial' ],
          'fb' => 'sans-serif'
        ],
        /**
         * Optima
         *
         * NOTE: Win環境では、Segoe UIで代用
         */
        'optima' => [
          'label' => 'Optima',
          'css' => [ 'Optima', '"Segoe UI"' ],
          'fb' => 'sans-serif'
        ],
        /**
         * Helvetica
         *
         * NOTE: Win環境では、Arialで代用
         */
        'helvetica' => [
          'label' => 'Helvetica',
          'css' => [ '"Helvetica Neue"', 'Helvetica', 'Arial' ],
          'fb' => 'sans-serif'
        ],
        /**
         * Avenir
         *
         * NOTE: Win環境では、Calibriで代用
         */
        'avenir' => [
          'label' => 'Avenir',
          'css' => [ '"Avenir Next"', 'Avenir', 'Calibri' ],
          'fb' => 'sans-serif'
        ],
        /**
         * Verdana
         */
        'verdana' => [
          'label' => 'Verdana',
          'css' => [ 'Verdana' ],
          'fb' => 'sans-serif'
        ],
        /**
         * Tahoma
         */
        'tahoma' => [
          'label' => 'Tahoma',
          'css' => [ 'Tahoma' ],
          'fb' => 'sans-serif'
        ],
      ];

      // セリフ
      $serif = [
        /**
         * Palatino
         */
        'palatino' => [
          'label' => 'Palatino',
          'css' => [ 'Palatino' ],
          'fb' => 'serif'
        ],
        /**
         * Times New Roman
         */
        'times' => [
          'label' => 'Times New Roman',
          'css' => [ '"Times New Roman"', 'Times' ],
          'fb' => 'serif'
        ],
        /**
         * Georgia
         */
        'georgia' => [
          'label' => 'Georgia',
          'css' => [ 'Georgia' ],
          'fb' => 'serif'
        ],
        /**
         * Didot
         *
         * NOTE: Win環境では、Times New Romanで代用
         */
        'didot' => [
          'label' => 'Didot',
          'css' => [ 'Didot', "Didot LT STD", '"Times New Roman"' ],
          'fb' => 'serif'
        ],
        /**
         * Baskerville
         *
         * NOTE: Win環境では、Georgiaで代用
         */
        'baskerville' => [
          'label' => 'Baskerville',
          'css' => [ 'Baskerville', '"Baskerville Old Face"', 'Georgia' ],
          'fb' => 'serif'
        ],
        /**
         * Rockwell
         */
        'rockwell' => [
          'label' => 'Rockwell',
          'css' => [ 'Rockwell', 'Palatino Linotype' ],
          'fb' => 'serif'
        ],
      ];

      // 筆記体
      $script = [
        /**
         * Macでは"Zapfino"、Winでは"Ink Free"
         */
        'cursive' => [
          'label' => 'Zapfino',
          'css' => [ '"Zapfino"', '"Ink Free"', 'cursive' ],
        ],
      ];

      $this->system_font_latin = $sans_serif + $serif + $script;

      // すべてのフォントリストにも登録
      $this->font_list += $this->system_font_latin;
    }

    /**
     * Webフォント和文のセット
     */
    public function set_web_font_japan(){
      $this->web_font_japan = [
        /**
         * Noto Sans JP
         */
        'noto_sans' => [
          'label' => 'Noto Sans JP',
          'css' => [ '"Noto Sans JP"', 'sans-serif' ],
          'source' => 'Noto+Sans+JP:wght@400;600',
        ],
        /**
         * Zen Kaku Gothic New
         */
        'zen_kaku_gothic_new' => [
          'label' => 'Zen Kaku Gothic New',
          'css' => [ '"Zen Kaku Gothic New"', 'sans-serif' ],
          'source' => 'Zen+Kaku+Gothic+New:wght@400;700',
        ],
        /**
         * Noto Serif JP
         */
        'noto_serif' => [
          'label' => 'Noto Serif JP',
          'css' => [ '"Noto Serif JP"', 'sans-serif' ],
          'source' => 'Noto+Serif+JP:wght@400;600',
        ],
        /**
         * Sawarabi Mincho
         */
        'zen_old_mincho' => [
          'label' => 'Zen Old Mincho',
          'css' => [ '"Zen Old Mincho"', 'sans-serif' ],
          'source' => 'Zen+Old+Mincho:wght@400;600',
        ],
        /**
         * Klee One
         */
        'klee_one' => [
          'label' => 'Klee One',
          'css' => [ '"Klee One"', 'cursive' ],
          'source' => 'Klee+One:wght@400;600',
        ],
      ];

      // すべてのフォントリストにも登録
      $this->font_list += $this->web_font_japan;
    }

    /**
     * Webフォント欧文のセット
     */
    public function set_web_font_latin(){
      $this->web_font_latin = [
        /**
         * Roboto
         */
        'roboto' => [
          'label' => 'Roboto',
          'css' => [ '"Roboto"' ],
          'source' => 'Roboto:wght@400;700',
          'fb' => 'sans-serif'
        ],
        /**
         * Hind Madurai
         */
        'Hind_Madurai' => [
          'label' => 'Hind Madurai',
          'css' => [ '"Hind Madurai"' ],
          'source' => 'Hind+Madurai:wght@500;700',
          'fb' => 'sans-serif'
        ],
      ];

      // すべてのフォントリストにも登録
      $this->font_list += $this->web_font_latin;
    }

    /**
     * フォントのキーから cssの取得
     */
    function get_font_value( $font_key, $property = 'css' ){
      return $this->font_list[$font_key][$property] ?? '';
    }

    /**
     * GoogleフォントのURL生成
     */
    function generate_google_fonts_url( $fonts ){

      // 例)"family=Klee+One:wght@400;600"などを配列内で組み立てる
      $families = array_map(
        fn ( $font ) => 'family=' . $font,
        $fonts
      );

      $swap = '';
      // &で連結し、最後に display=swap を付与
      // NOTE: ロゴなど、swapによる代替フォントが不要な場合もあるので解除
      // $swap = '&display=swap';
      return 'https://fonts.googleapis.com/css2?' . implode( '&', $families ) . $swap;
    }

    /**
     * Googleフォントの読み込み
     */
    function add_google_fonts_preconnect(){
      if( ! empty( $this->web_font_source_list ) ){
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link href="' . esc_attr( $this->generate_google_fonts_url( $this->web_font_source_list ) ) . '" rel="stylesheet">' . "\n";
      }
    }

    /**
     * フロントで利用するフォントの組み立て
     */
    function build_frontend_font_assets(){
      $options = get_design_plus_option();

      // テーマのフォント設定
      $font_list = $options['font_list'];
      foreach( $font_list as $font_key => $font_item ){

        // 組み立てるフォント
        $build_font = [];

        // システムフォントの和文・欧文セット
        $font_item_latin = $font_item['latin'] ?? '';
        $font_item_japan = $font_item['japan'] ?? '';

        // Webフォントの和文・欧文セット
        if( $font_item['type'] === 'web' ){
          $font_item_latin = $font_item['web_latin'] ?? '';
          $font_item_japan = $font_item['web_japan'] ?? '';

          // webフォント用リソースを追加
          if( $font_item_latin ){
            $this->web_font_source_list[] = $this->get_font_value( $font_item_latin, 'source' );
          }
          if( $font_item_japan ){
            $this->web_font_source_list[] = $this->get_font_value( $font_item_japan, 'source' );
          }
        }

        // 欧文フォントのセット
        if( $font_latin = $this->get_font_value( $font_item_latin ) ){
          $build_font = array_merge( $build_font, $font_latin );
        }

        // 和文フォントのセット
        if( $font_japan = $this->get_font_value( $font_item_japan ) ){
          $build_font = array_merge( $build_font, $font_japan );

        }elseif( $font_latin_fb = $this->get_font_value( $font_item_latin, 'fb' ) ){
          // 和文フォントが指定無しの場合のフォールバックを追加
          $build_font[] = $font_latin_fb;
        }

        // 管理画面のフォントラベル ロゴは除く
        if( $font_key !== 'logo' ){
          $this->admin_font_labels[$font_key] = sprintf(
            '%s, %s',
            $this->get_font_value( $font_item_japan, 'label' ) ?: __( 'Japanese (unspecified)','tcd-serum' ),
            $this->get_font_value( $font_item_latin, 'label' ) ?: __( 'Latin (unspecified)','tcd-serum' ),
          );
        }

        // フォントの組み立て
        $this->front_font_assets[$font_key] = implode( ',', $build_font );
      }
    }

    /**
     * CSS変数にフォントを追加
     */
    function add_current_font_style( $styles ){
      return wp_parse_args(
        [
          '--tcd-font-type1' => $this->front_font_assets[1],
          '--tcd-font-type2' => $this->front_font_assets[2],
          '--tcd-font-type3' => $this->front_font_assets[3],
          '--tcd-font-type-logo' => $this->front_font_assets['logo'],
        ],
        $styles
      );
    }

  /**
   * <head>に:root CSS変数を出力
   */
  public function output_root_custom_properties() {
    $custom_properties = apply_filters( 'tcd_current_css_custom_properties', [] );

    if ( empty( $custom_properties ) ) return;

    echo '<style>:root {' . "\n";
    foreach ( $custom_properties as $var => $value ) {
      echo "  {$var}: {$value};\n";
    }
    echo '}</style>' . "\n";
  }

    /**
     * 管理画面のフォントプレビュー
     *
     * NOTE: 日本語と英語の文章、文章変更したい時は引数に入れる
     */
    function view_admin_font_preview( $custom_preview = '' ){

      // デフォルトプレビュー
      $preview = 'あのイーハトーヴォのすきとおった風、夏でも底に冷たさをもつ青いそら、うつくしい森で飾られたモリーオ市、郊外のぎらぎらひかる草の波。Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sem arcu, convallis vehicula mauris sit amet, pellentesque ultricies arcu.';
      if( $custom_preview ){
        $preview = $custom_preview;
      }

      return sprintf(
        '<div class="tcd-admin-preview-font js-tcd-admin-font-preview">
          <div class="tcd-admin-preview-font-system js-tcd-admin-font-preview-system">
            %1$s
          </div>
          <div class="tcd-admin-preview-font-web js-tcd-admin-font-preview-web">
            %1$s
          </div>
        </div>',
        $preview
      );
    }

    /**
     * 管理画面のフォントプレビューに利用する CSS / JS
     *
     * NOTE: 管理画面のプレビューに関する記述はここ
     */
    function admin_font_preview_assets(){
      global $plugin_page;

      // テーマオプション以外のページでは読み込まない
      if( $plugin_page !== 'theme_options' ){
        return;
      }

      // プレビュー用にWebフォントの読み込む
      $web_font_labels = array_map(
        fn ( $value ) => $value['source'],
        $this->web_font_japan + $this->web_font_latin
      );

      echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
      echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
      echo '<link href="' . esc_attr( $this->generate_google_fonts_url( $web_font_labels ) ) . '" rel="stylesheet">' . "\n";

      // フォントプレビューのスタイル
      echo '<style>
            .tcd-ui-section:not(:has(select[data-font-type-select][data-selected="0"])) .js-tcd-admin-font-preview-system,
            .tcd-ui-section:not(:has(select[data-font-type-select][data-selected="1"])) .js-tcd-admin-font-preview-web
            { display:none; }
            .tcd-admin-preview-font { font-size: 1.2em; border: 1px solid #ddd;padding: 1em 1.5em;line-height: 2; }
            .tcd-admin-preview-font-system { font-family:var(--tcd-font-preview-latin, \'\'), var(--tcd-font-preview-japan, \'\'); }
            .tcd-admin-preview-font-web { font-family:var(--tcd-font-preview-web-latin, \'\'), var(--tcd-font-preview-web-japan, \'\'); }
            .tcd-ui-section:has(input[value="bold"]:checked) .tcd-admin-preview-font { font-weight:bold; }
            </style>';

      // 各フォントのCSS指定を配列にまとめてjsに渡す
      $font_css_list = array_map(
        fn ( $value ) => implode( ", ", $value['css'] ),
        $this->font_list
      );

      // フォントプレビューのスクリプト
      echo "<script>
              document.addEventListener('DOMContentLoaded', () => {
                const tcdFontCssList = " . wp_json_encode( $font_css_list ) . ";
                const updateFontPreview = (el) => {
                  const fontType = el.dataset.fontPreviewSelect;
                  const selectValue = el.value;
                  const preview = el.closest('details').querySelector('.js-tcd-admin-font-preview');
                  preview.style.setProperty('--tcd-font-preview-' + fontType, tcdFontCssList[selectValue]);
                }
                const target = document.querySelectorAll('[data-font-preview-select]');
                target.forEach((el) => {
                  updateFontPreview(el);
                  el.addEventListener('change', (e) => {
                    updateFontPreview(el);
                  });
                });
              });
            </script>" . "\n";
    }

  }

  /**
   * インスタンス化
   */
  $tcd_font_manager = new FontManager();
}