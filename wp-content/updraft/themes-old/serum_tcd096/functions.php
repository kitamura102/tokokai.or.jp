<?php

// 言語ファイル --------------------------------------------------------------------------------
load_textdomain('tcd-serum', dirname(__FILE__).'/languages/tcd-serum-' . determine_locale() . '.mo');

// テーマの説明文
__('WordPress theme "SERUM" is a template with the image of a dermatology clinic, featuring side icon buttons and a post type that allows you to organize the subjects of treatment. CTAs can also be easily set up.', 'tcd-serum');


// hook wp_head --------------------------------------------------------------------------------
require get_template_directory() . '/functions/head.php';


// テーマオプション --------------------------------------------------------------------------------
require_once ( dirname(__FILE__) . '/admin/theme-options.php' );
$options = get_design_plus_option();

// セットアップ -------------------------------------------------------------------------------
require_once ( dirname(__FILE__) . '/functions/theme-setup.php' );

// 更新通知 --------------------------------------------------------------------------------
require_once ( dirname(__FILE__) . '/functions/update_notifier.php' );


// マニュアル --------------------------------------------------------------------------------
require_once  ( dirname(__FILE__) . '/functions/manual.php' );


// カスタマイザー設定( 外観 > ウィジェットから設定を取り除く)--------------------------------------------------------------------------------
require_once  ( dirname(__FILE__) . '/functions/customizer.php' );


// 「トップページ」と「ブログ一覧ページ」用の固定ページ作成機能の実装----------------------------------
require_once  ( dirname(__FILE__) . '/functions/class-page-new.php' );

// 新フォント機能 --------------------------------------------------------------------------------
require_once ( dirname(__FILE__) . '/admin/font/hooks-font.php' );

// TCDクラシックエディタのインストールを促す告知機能----------------------------------
require_once get_template_directory() . '/functions/class-plugin-installer.php';


// フロントページ用スクリプト --------------------------------------------------------------
function front_page_scripts(){
  $options = get_design_plus_option();
  wp_enqueue_style( 'main-style', get_stylesheet_uri(), false, version_num(), 'all');
  wp_enqueue_style( 'design-plus', get_template_directory_uri() . '/css/design-plus.css', array('main-style'),version_num() );
  wp_enqueue_style( 'responsive', get_template_directory_uri() . '/css/responsive.css', array('main-style'),version_num(), 'screen and (max-width:1221px)' );
  if( $options['use_google_material_icon'] === 1 ) {
    wp_enqueue_style( 'google-material-icon-css', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200', array('main-style'), version_num() );
  }
  wp_enqueue_style( 'footer-bar', get_template_directory_uri() . '/css/footer-bar.css', array('main-style'),version_num(), 'screen and (max-width:1221px)' );

  wp_enqueue_script( 'jquery' );
  if ( is_single() ) {
    wp_enqueue_script('comment-reply');
    wp_enqueue_script( 'comment', get_template_directory_uri() . '/js/comment.js', array(), version_num(), true );
  }
  wp_enqueue_script( 'jquery.easing.1.4', get_template_directory_uri() . '/js/jquery.easing.1.4.js', array(), version_num(), true );
  wp_enqueue_script( 'jscript', get_template_directory_uri() . '/js/jscript.js', array(), version_num(), true );
  wp_enqueue_script( 'tcd_cookie', get_template_directory_uri() . '/js/tcd_cookie.js', array(), version_num(), true );
  wp_enqueue_script( 'header_fix', get_template_directory_uri() . '/js/header_fix.js', array(),version_num() );

  if(!is_mobile()) {
    wp_enqueue_style( 'simplebar', get_template_directory_uri() . '/js/simplebar.css', array('main-style'),version_num() );
    wp_enqueue_script( 'simplebar.min', get_template_directory_uri() . '/js/simplebar.min.js', array(), version_num(), true );
  }

  wp_enqueue_style( 'splide_css', get_template_directory_uri() . '/js/splide-core.min.css', array('main-style'), '4.1.3' );
  wp_enqueue_script( 'splide_script', get_template_directory_uri() . '/js/splide.min.js', array(), '4.1.3', true );
  wp_enqueue_script( 'splide_scroll_script', get_template_directory_uri() . '/js/splide-extension-auto-scroll.min.js', array(), '0.5.3', true );
  wp_enqueue_script( 'splide_intersection_script', get_template_directory_uri() . '/js/splide-extension-intersection.min.js', array(), '0.2.0', true );
}
add_action('wp_enqueue_scripts', 'front_page_scripts', 8); //8が無いとブロックエディタによって上書きされる


// 管理画面用スクリプト --------------------------------------------------------------------------
function my_admin_scripts() {
  $options = get_design_plus_option();
  wp_enqueue_script( 'wp-color-picker');
  wp_enqueue_script('thickbox');
  wp_enqueue_script('media-upload');
  wp_enqueue_script('jquery-ui-resizable');//トップページヘッダーコンテンツのロゴリサイズ機能で使用
  wp_enqueue_script('ml-widget-js', get_template_directory_uri().'/widget/js/script.js', '', '1.0.5', true);
  wp_enqueue_script('font_ui', get_template_directory_uri().'/admin/font/ui/font_ui.js', '', '1.0.4', true);
  wp_enqueue_script('jquery.cookieTab', get_template_directory_uri().'/admin/js/jquery.cookieTab.js', '', '1.0.0', true);
  wp_enqueue_script('jquery.cookie', get_template_directory_uri().'/js/tcd_cookie.js', '', '1.0.0', true);
  wp_enqueue_script('my_script', get_template_directory_uri().'/admin/js/my_script.js', '', '1.5.0', true);
  wp_enqueue_script('new_ui', get_template_directory_uri().'/admin/js/new_ui.js', '', '1.0.2', true);
  wp_enqueue_script('lightcase_script', get_template_directory_uri().'/admin/js/lightcase/lightcase.js', '', '1.0.0', true);
  wp_localize_script( 'my_script', 'TCD_MESSAGES', array(
    'cookieResetSuccess' => __( 'Cookie has been deleted', 'tcd-serum' ),
    'ajaxSubmitSuccess' => __( 'Settings Saved Successfully', 'tcd-serum' ),
    'ajaxSubmitError' => __( 'Can not save data. Please try again', 'tcd-serum' ),
    'tabChangeWithoutSave' => __( "Your changes on the current tab have not been saved.\nTo stay on the current tab so that you can save your changes, click Cancel.", 'tcd-serum' ),
    'contentBuilderDelete' => __( 'Are you sure you want to delete this content?', 'tcd-serum' ),
    'imageContentWidthMessage' => __( '<span>You can display image by content width when you displaying border around the content on LP page.</span>', 'tcd-serum' ),
    'mainColor' => $options['main_color'],
    'deleteCookie' => __( 'Cookie is deleted', 'tcd-serum' ),
  ) );
  wp_enqueue_media();//画像アップロード用
  wp_enqueue_script('cf-media-field', get_template_directory_uri().'/admin/js/cf-media-field.js', '', '1.0.2', true); //画像アップロード用
  wp_localize_script( 'cf-media-field', 'cfmf_text', array(
    'image_title' => __( 'Please select image', 'tcd-serum' ),
    'image_button' => __( 'Use this image', 'tcd-serum' ),
    'video_title' => __( 'Please select MP4 file', 'tcd-serum' ),
    'video_button' => __( 'Use this MP4 file', 'tcd-serum' ),
    'image_save' => __( 'Save', 'tcd-serum' ),
  ) );
}
add_action('admin_print_scripts', 'my_admin_scripts');


// 管理画面用スタイルシートの読み込み -----------------------------------------------------------------------
function my_admin_styles() {
  wp_enqueue_style('imgareaselect');
  wp_enqueue_style('jquery-ui-draggable');
  wp_enqueue_style('wp-color-picker');
  wp_enqueue_style('thickbox');
  wp_enqueue_style('font_ui_css', get_template_directory_uri() . '/admin/font/ui/font_ui.css','','1.0.0');
  wp_enqueue_style('my_widget_css', get_template_directory_uri() . '/widget/css/style.css','','1.0.3');
  wp_enqueue_style('my_admin_css', get_template_directory_uri() .'/admin/css/my_admin.css','','1.3.1');
  wp_enqueue_style('new_ui_css', get_template_directory_uri() .'/admin/css/new_ui.css','','1.0.2');
  wp_enqueue_style('lightcase_style', get_template_directory_uri() . '/admin/js/lightcase/lightcase.css','','1.0.2');
}
add_action('admin_print_styles', 'my_admin_styles');


// ウィジェット ------------------------------------------------------------------------
require_once ( dirname(__FILE__) . '/widget/styled_post_list.php' );
require_once ( dirname(__FILE__) . '/widget/search_box.php' );

$news_label = $options['news_label'] ? esc_html( $options['news_label'] ) : __( 'News', 'tcd-serum' );

register_sidebar(array(
  'before_widget' => '<div class="widget_content clearfix %2$s" id="%1$s">'."\n",
  'after_widget' => "</div>\n",
  'before_title' => '<div class="widget_headline"><span>',
  'after_title' => "</span></div>",
  'name' => __('Common widget', 'tcd-serum'),
  'description' => sprintf(__('Widgets registered in this area will be displayed in the blog and %s page in common. Widget will be displayed at the bottom of the article.', 'tcd-serum'), $news_label),
  'id' => 'common_widget'
));
register_sidebar(array(
  'before_widget' => '<div class="widget_content clearfix %2$s" id="%1$s">'."\n",
  'after_widget' => "</div>\n",
  'before_title' => '<div class="widget_headline"><span>',
  'after_title' => "</span></div>",
  'name' => __('Common widget (smarphone)', 'tcd-serum'),
  'description' => sprintf(__('Widgets registered in this area will be displayed in the blog and %s page in common. Widget will be displayed at the bottom of the article. They will be replaced by other widgets only when viewed on a smartphone.', 'tcd-serum'), $news_label),
  'id' => 'common_widget_mobile'
));
register_sidebar(array(
  'before_widget' => '<div class="widget_content clearfix %2$s" id="%1$s">'."\n",
  'after_widget' => "</div>\n",
  'before_title' => '<div class="widget_headline"><span>',
  'after_title' => "</span></div>",
  'name' => __('Blog page', 'tcd-serum'),
  'description' => __('Widgets registered in this area will be displayed in the widget area under "Related Posts" at the bottom of the article. They have priority over the basic widgets.', 'tcd-serum'),
  'id' => 'single_widget'
));
register_sidebar(array(
  'before_widget' => '<div class="widget_content clearfix %2$s" id="%1$s">'."\n",
  'after_widget' => "</div>\n",
  'before_title' => '<div class="widget_headline"><span>',
  'after_title' => "</span></div>",
  'name' => __('Blog page (smartphone)', 'tcd-serum'),
  'description' => __('Widgets registered in this area will be displayed in the widget area under "Related Posts" at the bottom of the article. They will be replaced by other widgets only when viewed on a smartphone.', 'tcd-serum'),
  'id' => 'single_widget_mobile'
));
register_sidebar(array(
  'before_widget' => '<div class="widget_content clearfix %2$s" id="%1$s">'."\n",
  'after_widget' => "</div>\n",
  'before_title' => '<div class="widget_headline"><span>',
  'after_title' => "</span></div>",
  'name' => sprintf(__('%s page', 'tcd-serum'), $news_label),
  'description' => sprintf(__('Widgets registered in this area will be displayed in the widget area under "%s list" at the bottom of the %s article. They have priority over the basic widgets.', 'tcd-serum'), $news_label, $news_label),
  'id' => 'news_single_widget'
));
register_sidebar(array(
  'before_widget' => '<div class="widget_content clearfix %2$s" id="%1$s">'."\n",
  'after_widget' => "</div>\n",
  'before_title' => '<div class="widget_headline"><span>',
  'after_title' => "</span></div>",
  'name' => sprintf(__('%s page (smartphone)', 'tcd-serum'), $news_label),
  'description' => sprintf(__('Widgets registered in this area will be displayed in the widget area under "%s list" at the bottom of the %s article. They will be replaced by other widgets only when viewed on a smartphone.', 'tcd-serum'), $news_label, $news_label),
  'id' => 'news_single_widget_mobile'
));


// ウィジェットのブロックエディタ無効化
function example_theme_support() {
  remove_theme_support( 'widgets-block-editor' );
}
add_action( 'after_setup_theme', 'example_theme_support' );


// アーカイブウィジェットのタイトルが空の場合見出しを表示しない
function filter_wp_widget_archives_widget_title ( $title, $instance = array(), $id_base = null ) {
	if ( 'archives' === $id_base && empty( $instance['title'] ) || 'categories' === $id_base && empty( $instance['title'] )) {
		$title = '';
	}
	return $title;
}
add_filter( 'widget_title', 'filter_wp_widget_archives_widget_title', 10, 3 );


// カテゴリーウィジェットの記事数をspanで囲む
function smittenkitchen_cat_count_span( $links ) {
	$links = str_replace( '</a> (', '</a><span class="post-count">', $links );
	$links = str_replace( ')', '</span>', $links );
	return $links;
}
add_filter( 'wp_list_categories', 'smittenkitchen_cat_count_span' );


// アーカイブウィジェットの記事数をspanで囲む
function smittenkitchen_archive_count_span( $links ) {
	$links = str_replace( '</a>&nbsp;(', '</a><span class="post-count">', $links );
	$links = str_replace( ')</li>', '</span></li>', $links );
	return $links;
}
add_filter( 'get_archives_link', 'smittenkitchen_archive_count_span' );


// カードリンクパーツ --------------------------------------------------------------------------------
require get_template_directory() . '/functions/clink.php';


// フッターバー --------------------------------------------------------------------------------
require get_template_directory() . '/functions/footer-bar.php';


// おすすめ記事 --------------------------------------------------------------------------------
require get_template_directory() . '/functions/recommend.php';


// meta title meta description  --------------------------------------------------------------------------------
add_theme_support('title-tag');
require_once ( dirname(__FILE__) . '/functions/seo.php' );
function title_separator_change( $sep ){
  $sep = '|';
  return $sep;
}
add_filter('document_title_separator', 'title_separator_change');



// 管理画面の記事一覧、クイック編集 --------------------------------------------------------------------------------
require get_template_directory() . '/functions/admin_column.php';
require get_template_directory() . '/functions/quick_edit.php';


// カスタムフィールド --------------------------------------------------------------------------------
require get_template_directory() . '/functions/page_cf.php';
require get_template_directory() . '/functions/treatment_category.php';
require get_template_directory() . '/functions/treatment_cf.php';


// 並び替え --------------------------------------------------------------------------------
require get_template_directory() . '/functions/post_order.php';


// カスタムCSS・スクリプト --------------------------------------------------------------------------------
require get_template_directory() . '/functions/custom_script.php';


// ビジュアルエディタにクイックタグを追加 --------------------------------------------------------------------------------
require get_template_directory() . '/functions/custom_editor.php';


// ショートコード --------------------------------------------------------------------------------
require get_template_directory() . '/functions/short_code.php';


// カスタムページリンク  --------------------------------------------------------------------------------
require_once ( dirname(__FILE__) . '/functions/custom_page_link.php' );


// OGP tag  -------------------------------------------------------------------------------------------
require get_template_directory() . '/functions/ogp.php';


// 次のページリンク  --------------------------------------------------------------------------------
require_once ( dirname(__FILE__) . '/functions/next_prev.php' );


//ロゴ用関数 --------------------------------------------------------------------------------
require_once ( dirname(__FILE__) . '/functions/logo.php' );


// プロフィール追加情報 --------------------------------------------------------------------------------
require get_template_directory() . '/functions/user-profile.php';


// ロードアイコン -----------------------------------------------------------------------------
require get_template_directory() . '/functions/load_icon.php';
require get_template_directory() . '/functions/footer_script.php';


// パスワード保護 -----------------------------------------------------------------------------
require_once ( dirname(__FILE__) . '/functions/password_form.php' );


// 高速化 --------------------------------------------------------------------------------
require ( dirname(__FILE__) . '/functions/acceleration.php' );


// ユーザーエージェントを判定するための関数---------------------------------------------------------------------
function is_mobile() {
  if ( isset( $_SERVER['HTTP_SEC_CH_UA_MOBILE'] ) ) {
      $is_mobile = ( '?1' === $_SERVER['HTTP_SEC_CH_UA_MOBILE'] );
  } elseif ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
      $is_mobile = false;
  } elseif (
      (str_contains( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) && !str_contains( $_SERVER['HTTP_USER_AGENT'], 'iPad' )) // iPad を除外
      || (str_contains( $_SERVER['HTTP_USER_AGENT'], 'Android' ) && !str_contains( $_SERVER['HTTP_USER_AGENT'], 'Tablet' )) // Android タブレットを除外
      || str_contains( $_SERVER['HTTP_USER_AGENT'], 'iPhone' ) // iPhone を明示的に含む
      || str_contains( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' )
      || str_contains( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' )
      || str_contains( $_SERVER['HTTP_USER_AGENT'], 'Opera Mobi' )
  ) {
      $is_mobile = true;
  } else {
      $is_mobile = false;
  }

return $is_mobile;

}

// videoタグやyoutubeの自動再生に対応しているか判定 ----------------------------------------------
// Android 標準ブラウザは不可、Android版 Chrome ver53以下は不可、iOS ver10以下は不可、それ以外は再生を許可
function auto_play_movie() {
  $ua = mb_strtolower($_SERVER['HTTP_USER_AGENT']);
  // Android -----------------------------------
  if( preg_match('/android/ui', $ua) ) {
    // 標準ブラウザ
    if (strpos($ua, 'android') !== false && strpos($ua, 'linux; u;') !== false && strpos($ua, 'chrome') === false) {
      return FALSE;
    // Chrome
    } elseif ( preg_match('/(chrome)\/([0-9\.]+)/', $ua , $matches) ){
      if (intval($matches[2]) < 53) {
        return FALSE;
      } else {
        return TRUE;
      }
    } else {
      return TRUE;
    }
  // iOS ---------------------------------------
  } elseif(preg_match('/iphone|ipod|ipad/ui', $ua)) {
    if ( preg_match('/(iphone|ipod|ipad) os ([0-9_]+)/', $ua, $matches) ) {
      if (intval($matches[2]) < 10) {
        return FALSE;
      } else {
        return TRUE;
      }
    } else {
      return TRUE;
    }
  // PC等、その他のOS ---------------------------------------
  } else {
    //echo 'OTHER OS<br />';
    return TRUE;
  }
}


// スクリプトのバージョン管理 ----------------------------------------------------------------------------------------------
function version_num() {

 if (function_exists('wp_get_theme')) {
   $theme_data = wp_get_theme( get_template() );
 } else {
   $theme_data = get_theme_data(TEMPLATEPATH . '/style.css');
 };

 $current_version = $theme_data['Version'];

 return $current_version;

};


// オリジナルの抜粋記事 --------------------------------------------------------------------------------
function trim_excerpt($a) {

 if(has_excerpt()) { 

   $base_content = get_the_excerpt();
   $base_content = str_replace(array("\r\n", "\r", "\n"), "", $base_content);
   $trim_content = mb_substr($base_content, 0, $a ,"utf-8");

 } else {

   $base_content = get_the_content();
   $base_content = preg_replace('!<style.*?>.*?</style.*?>!is', '', $base_content);
   $base_content = preg_replace('!<script.*?>.*?</script.*?>!is', '', $base_content);
   $base_content = preg_replace('/\[.+\]/','', $base_content);
   $base_content = strip_tags($base_content);
   $trim_content = mb_substr($base_content, 0, $a,"utf-8");
   $trim_content = str_replace(']]>', ']]&gt;', $trim_content);
   $trim_content = str_replace(array("\r\n", "\r", "\n" , "&nbsp;"), "", $trim_content);
   $trim_content = htmlspecialchars($trim_content);

 };

 return $trim_content;

};
function trim_desc($desc,$num) {

  $trim_desc = mb_substr($desc, 0, $num ,"utf-8");
  $count_word = mb_strlen($trim_desc,"utf-8");
  return $trim_desc;

};

//抜粋からPタグを取り除く
remove_filter( 'the_excerpt', 'wpautop' );


// 記事タイトルの文字数制限 --------------------------------------------------------------------------------
function trim_title($num) {
 $base_title = strip_tags(get_the_title());
 $trim_title = mb_substr($base_title, 0, $num ,"utf-8");
 $count_title = mb_strlen($trim_title,"utf-8");
 if($count_title > $num-1) {
  echo $trim_title . '…';
 } else {
  echo $trim_title;
 };
};

function trim_title2($num) {
 $base_title = strip_tags(get_the_title());
 $trim_title = mb_substr($base_title, 0, $num ,"utf-8");
 $count_title = mb_strlen($trim_title,"utf-8");
 if($count_title > $num-1) {
  return $trim_title . '…';
 } else {
  return $trim_title;
 };
};

/* ショートコード用 */
function trim_title_sc($num) {
 $base_title = get_the_title();
 $trim_title = mb_substr($base_title, 0, $num ,"utf-8");
 $count_title = mb_strwidth($trim_title,"utf-8");
 if($count_title > $num-1) {
  return $trim_title . '…';
 } else {
  return $trim_title;
 };
};


// タイトルをエンコード --------------------------------------------------------------------------------
function get_encoded_title($title){
  return urlencode(mb_convert_encoding($title, "UTF-8"));
}


// セルフピンバックを禁止する -------------------------------------------------------------------------------------
function no_self_ping( &$links ) {
  $home = home_url();
  foreach ( $links as $l => $link )
  if ( 0 === strpos( $link, $home ) )
  unset($links[$l]);
}
add_action( 'pre_ping', 'no_self_ping' );


// RSS用のフィードを追加 ---------------------------------------------------------------------------------------------------
add_theme_support( 'automatic-feed-links' );


//　ヘッダーから余分なMETA情報を削除 --------------------------------------------------------------------
remove_action( 'wp_head', 'wp_generator' ); 
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );


// インラインスタイルを取り除く --------------------------------------------------------------------------------
function remove_recent_comments_style() {
  global $wp_widget_factory;
  if ( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
    remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
  }
}
add_action( 'widgets_init', 'remove_recent_comments_style' );

add_action( 'get_header', function() {
  remove_action( 'wp_head', '_admin_bar_bump_cb' );
  remove_action( 'wp_head', 'wp_admin_bar_header' );
  remove_action( 'wp_enqueue_scripts', 'wp_enqueue_admin_bar_bump_styles' );
  remove_action( 'wp_enqueue_scripts', 'wp_enqueue_admin_bar_header_styles' );
} );


//　サムネイルの設定 --------------------------------------------------------------------------------
if ( function_exists('add_theme_support') ) {
  add_theme_support( 'post-thumbnails' );
  add_image_size( 'size1', 160, 160, true );
  add_image_size( 'size2', 620, 360, true );
  add_image_size( 'size3', 830, 480, true );
}


// アイキャッチ画像登録エリアに推奨サイズを表示する
function message_image_meta_box($content, $post_id, $thumbnail_id) {
  $post = get_post($post_id);
  $options = get_design_plus_option();
  if ( $post->post_type == 'post' || $post->post_type == 'news') {
    $content .= '<p>' . sprintf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '830', '480') . '</p>';
    return $content;
  }
  if ( $post->post_type == 'treatment') {
    $content .= '<p>' . sprintf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '160', '160') . '</p>';
    return $content;
  }
  if ( $post->post_type == 'page') {
    $content .= '<p>' . sprintf(__('Recommend image size. Width:%1$spx, Height:%2$spx.<br>This image will be used in search result and OGP tag.', 'tcd-serum'),'1200','630') . '</p>';
    return $content;
  }
  return $content;
}
add_filter('admin_post_thumbnail_html', 'message_image_meta_box', 10, 3);


//require get_template_directory() . '/functions/blur_image.php'; //ぼかし画像


// カスタムメニューの設定 --------------------------------------------------------------------------------
if(function_exists('register_nav_menu')) {
  register_nav_menu( 'global-menu', __( 'Global menu', 'tcd-serum' ));
  register_nav_menu( 'footer-menu1', __( 'Footer menu1', 'tcd-serum' ));
  register_nav_menu( 'footer-menu2', __( 'Footer menu2', 'tcd-serum' ));
  register_nav_menu( 'footer-menu3', __( 'Footer menu3', 'tcd-serum' ));
  register_nav_menu( 'footer-menu4', __( 'Footer menu4', 'tcd-serum' ));
  register_nav_menu( 'footer-menu-mobile', __( 'Mobile size footer menu', 'tcd-serum' ));
}

// current-menu-itemを付ける
function custom_active_item_classes($classes, $menu_item) {
  if(is_tax('news_category') || is_singular('news')){
    $news_archive_page_url = get_post_type_archive_link('news');
    if($menu_item->url == $news_archive_page_url){
      $classes[] = 'current-menu-item';
    }
  }
  if(is_tax('treatment_category') || is_singular('treatment')){
    $case_archive_page_url = get_post_type_archive_link('treatment');
    if($menu_item->url == $case_archive_page_url){
      $classes[] = 'current-menu-item';
    }
  }
  if(is_singular('post')){
    $blog_page_url = get_permalink(get_option('page_for_posts'));
    if($menu_item->url == $blog_page_url){
      $classes[] = 'current-menu-item';
    }
  }
  if(is_category() || is_tag() || is_author() || is_day() || is_month() || is_year()){
    $blog_page_url = get_permalink(get_option('page_for_posts'));
    if($menu_item->url == $blog_page_url){
      $classes[] = 'current-menu-item';
    }
  }
  return $classes;
}
add_filter( 'nav_menu_css_class', 'custom_active_item_classes', 10, 2 );


// メガメニュー --------------------------------------------------------------------------------
require get_template_directory() . '/functions/menu.php';
if ( ! function_exists( 'wp_get_nav_menu_name' ) ) {
  function wp_get_nav_menu_name( $location ) {
    $menu_name = '';
    $locations = get_nav_menu_locations();
    if ( isset( $locations[ $location ] ) ) {
      $menu = wp_get_nav_menu_object( $locations[ $location ] );
      if ( $menu && $menu->name ) {
        $menu_name = $menu->name;
      }
    }
    return apply_filters( 'wp_get_nav_menu_name', $menu_name, $location );
  }
}


// 絵文字を消す ------------------------------------------------------------------
function disable_emoji() {
  $options = get_design_plus_option();
  if ( $options['use_emoji'] == 0 ) {

    // remove inline script
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    // remove inline style
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    // remove inline style  6.4 later
    if ( function_exists( 'wp_enqueue_emoji_styles' ) ) {
      remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
      remove_action( 'admin_enqueue_scripts', 'wp_enqueue_emoji_styles' );
    }

    // initだと早いため、admin_initで実行
    add_action( 'admin_init', function(){
      // remove inline script
      remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
      // remove inline style
      remove_action( 'admin_print_styles', 'print_emoji_styles' );
      // remove inline style 6.4 later
      if ( function_exists( 'wp_enqueue_emoji_styles' ) ) {
        remove_action( 'admin_enqueue_scripts', 'wp_enqueue_emoji_styles' );
      }
    } );

  }
}
add_action( 'init', 'disable_emoji' );

add_action( 'init', 'disable_emoji' );


// bodyにclassを追加 --------------------------------------------------------------------------------
function tcd_body_classes($classes) {
    global $wp_query, $post;
    $options = get_design_plus_option();

    if(wp_is_mobile()){ $classes[] = 'mobile_device'; }

    if(is_search() && isset($_GET['s']) && empty($_GET['s'])){ $classes[] = 'search-no-results'; };

    if($options['header_logo_type'] == 'type1'){ $classes[] = 'show_text_logo'; }
    if($options['header_logo_type'] == 'type2' && empty($options['header_logo_image'])){ $classes[] = 'no_header_logo_image'; }
    if($options['header_logo_type'] == 'type2' && empty($options['header_logo_image_mobile'])){ $classes[] = 'no_header_logo_image_mobile'; }

    if($options['show_header_message'] == 'display') { $classes[] = 'show_header_message'; }

    if($options['blog_show_date'] == 'hide') { $classes[] = 'hide_blog_date'; }

    if($options['show_loading']){ $classes[] = 'use_loading_screen'; };

    if( is_page() && is_page_template('page-tcd-lp.php') && (get_post_meta($post->ID, 'hide_header_message', true) == 'yes') ) { $classes[] = 'hide_header_message'; };
    if( is_page() && (get_post_meta($post->ID, 'hide_page_header', true) == 'yes') ) { $classes[] = 'hide_page_header'; } else { $classes[] = 'show_page_header'; };
    if( is_page() && (get_post_meta($post->ID, 'hide_page_header_bar', true) == 'yes' || get_post_meta($post->ID, 'hide_page_header', true) == 'yes') ) { $classes[] = 'hide_page_header_bar'; } else { $classes[] = 'show_page_header_bar'; };
    if( is_page() && (get_post_meta($post->ID, 'hide_page_side_bar', true) == 'yes') ) { $classes[] = 'hide_page_side_bar'; };
    if( is_page() && (get_post_meta($post->ID, 'page_hide_footer', true) == 'yes') ) { $classes[] = 'hide_footer'; };
    if( is_page() && (get_post_meta($post->ID, 'hide_logo', true) == 'yes') ) { $classes[] = 'hide_logo'; };
    if( is_page() && (get_post_meta($post->ID, 'page_width', true) == 'small') ) { $classes[] = 'page_width_small'; };
    if( is_page() && (get_post_meta($post->ID, 'page_width', true) == 'large') ) { $classes[] = 'page_width_large'; };

    if(is_archive()) {
      global $wp_query;
      if($wp_query->max_num_pages == 1) {
        $classes[] = 'no_page_nav';
      }
    }

    if( is_single() && (!comments_open() && !pings_open()) ) { $classes[] = 'no_comment_form'; };

    if (wp_is_mobile()) {
      $classes[] = 'mobile_device';
    };

    if ( is_mobile() && ($options['footer_bar_type'] != 'type1') ) { $classes[] = 'show_footer_bar'; };

    return array_unique($classes);
};
add_filter('body_class','tcd_body_classes');


// HEXをRGBに変換 ------------------------------------------------------------------
function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   return $rgb;
}


// Adjust RGBA color ----------------------------------------------------------------
function adjustBrightness($hex, $steps) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0,min(255,$color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}


// archive_title() 関数をカスタマイズ --------------------------------------------------------------------------------
function monolith_archive_title( $title ) {
	global $author, $post, $wp_query;
	if ( is_author() ) {
		$title = get_the_author_meta( 'display_name', $author );
	} elseif ( is_category() || is_tag() ) {
		$title = single_term_title( '', false );
	} elseif ( is_day() ) {
		$title = get_the_time( __( 'F jS, Y', 'tcd-serum' ), $post );
	} elseif ( is_month() ) {
		$title = get_the_time( __( 'F, Y', 'tcd-serum' ), $post );
	} elseif ( is_year() ) {
		$title = get_the_time( __( 'Y', 'tcd-serum' ), $post );
	} elseif ( is_search() ) {
		if ( $wp_query->found_posts ) {
			//$title = sprintf( __( 'Search results for - ', 'tcd-serum' ) . get_search_query() 
		} else {
			$title = __( 'Search result', 'tcd-serum' );
		}
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'monolith_archive_title', 10 );


// カスタムコメント --------------------------------------------------------------------------------------

if (function_exists('wp_list_comments')) {
	// comment count
	add_filter('get_comments_number', 'comment_count', 0);
	function comment_count( $commentcount ) {
		global $id;
		$_commnets = get_comments('post_id=' . $id);
		$comments_by_type = separate_comments($_commnets);
		return count($comments_by_type['comment']);
	}
}


function custom_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	global $commentcount;
	if(!$commentcount) {
		$commentcount = 0;
	}
?>

 <li class="comment <?php if($comment->comment_author_email == get_the_author_meta('email')) {echo 'admin-comment';} else {echo 'guest-comment';} ?>" id="comment-<?php comment_ID() ?>">
  <div class="comment-meta clearfix">
   <div class="comment-meta-left">
  <?php if (function_exists('get_avatar') && get_option('show_avatars')) { echo get_avatar($comment, 35); } ?>
  
    <ul class="comment-name-date">
     <li class="comment-name">
<?php if (get_comment_author_url()) : ?>
<a id="commentauthor-<?php comment_ID() ?>" class="url <?php if($comment->comment_author_email == get_the_author_meta('email')) {echo 'admin-url';} else {echo 'guest-url';} ?>" href="<?php comment_author_url() ?>" rel="nofollow">
<?php else : ?>
<span id="commentauthor-<?php comment_ID() ?>">
<?php endif; ?>

<?php comment_author(); ?>

<?php if(get_comment_author_url()) : ?>
</a>
<?php else : ?>
</span>
<?php endif; ?>
     <li class="comment-date"><?php echo get_comment_time('Y.m.d'); echo get_comment_time(' g:ia'); ?></li>
    </ul>
   </div>

   <ul class="comment-act">
<?php if (function_exists('comment_reply_link')) { 
        if ( get_option('thread_comments') == '1' ) { ?>
    <li class="comment-reply"><?php comment_reply_link(array_merge( $args, array('add_below' => 'comment-content', 'depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => '<span><span>'.__('REPLY','tcd-serum').'</span></span>'))) ?></li>
<?php   } else { ?>
    <li class="comment-reply"><a href="javascript:void(0);" onclick="MGJS_CMT.reply('commentauthor-<?php comment_ID() ?>', 'comment-<?php comment_ID() ?>', 'comment');"><?php _e('REPLY', 'tcd-serum'); ?></a></li>
<?php   }
      } else { ?>
    <li class="comment-reply"><a href="javascript:void(0);" onclick="MGJS_CMT.reply('commentauthor-<?php comment_ID() ?>', 'comment-<?php comment_ID() ?>', 'comment');"><?php _e('REPLY', 'tcd-serum'); ?></a></li>
<?php } ?>
    <li class="comment-quote"><a href="javascript:void(0);" onclick="MGJS_CMT.quote('commentauthor-<?php comment_ID() ?>', 'comment-<?php comment_ID() ?>', 'comment-content-<?php comment_ID() ?>', 'comment');"><?php _e('QUOTE', 'tcd-serum'); ?></a></li>
    <?php edit_comment_link(__('EDIT', 'tcd-serum'), '<li class="comment-edit">', '</li>'); ?>
   </ul>

  </div>
  <div class="comment-content post_content" id="comment-content-<?php comment_ID() ?>">
  <?php if ($comment->comment_approved == '0') : ?>
   <span class="comment-note"><?php _e('Your comment is awaiting moderation.', 'tcd-serum'); ?></span>
  <?php endif; ?>
  <?php comment_text(); ?>
  </div>

<?php

}


/* 記事編集画面のカテゴリー階層を保つ */
function lig_wp_category_terms_checklist_no_top( $args, $post_id = null ) {
  $args['checked_ontop'] = false;
  return $args;
}
add_action( 'wp_terms_checklist_args', 'lig_wp_category_terms_checklist_no_top' );


// カスタム投稿の数が多い為、メディアメニューの位置を変更 ----------------------------------------------------------
function customize_menus(){
  global $menu;
  $menu[19] = $menu[10];
  unset($menu[10]);
}
add_action( 'admin_menu', 'customize_menus' );


// 投稿（ブログ）のラベルを変更 --------------------------------------------------------------------------------
$blog_page_id = get_option( 'page_for_posts' );
$blog_label = __( 'Blog', 'tcd-serum' );
if($blog_page_id) {
  $blog_label = get_the_title($blog_page_id);
}
function change_blog_label( $args, $post_type ) {
  global $blog_content_label;
  if ( 'post' == $post_type ) {
    $args['label'] = $blog_content_label;
  }
  return $args;
}
add_filter( 'register_post_type_args', 'change_blog_label', 10, 2 );


// カスタム投稿とタクソノミーの追加 --------------------------------------------------------------------------------

function custom_post_type_init() {

$options = get_design_plus_option();


// カスタム投稿「お知らせ」 --------------------------------------------------------------------------------
if($options['use_news']){

$news_label = $options['news_label'] ? esc_html( $options['news_label'] ) : __( 'News', 'tcd-serum' );
$news_slug = $options['news_slug'] ? sanitize_title( $options['news_slug'] ) : 'news';
$news_labels = array(
  'name' => $news_label,
  'add_new' => __( 'Add New', 'tcd-serum' ),
  'add_new_item' => __( 'Add New Item', 'tcd-serum' ),
  'edit_item' => __( 'Edit', 'tcd-serum' ),
  'new_item' => __( 'New item', 'tcd-serum' ),
  'view_item' => __( 'View Item', 'tcd-serum' ),
  'search_items' => __( 'Search Items', 'tcd-serum' ),
  'not_found' => __( 'Not Found', 'tcd-serum' ),
  'not_found_in_trash' => __( 'Not found in trash', 'tcd-serum' ),
  'parent_item_colon' => ''
);

register_post_type( 'news', array(
  'label' => $news_label,
  'labels' => $news_labels,
  'public' => true,
  'publicly_queryable' => true,
  'menu_position' => 5,
  'show_ui' => true,
  'query_var' => true,
  'rewrite' => array( 'slug' => $news_slug ),
  'capability_type' => 'post',
  'has_archive' => true,
  'hierarchical' => false,
  'supports' => array( 'title', 'editor', 'thumbnail' ),
  'show_in_rest' => true	// ブロックエディターを使用しない、REST APIで表示しない
));


// 「お知らせ」カテゴリー
$news_category_label = sprintf(__('%s category', 'tcd-serum'), $news_label);
$news_category_slug = $news_slug . '_category';
$news_category_labels = array(
  'name' => $news_category_label,
  'singular_name' => $news_category_label
);
register_taxonomy( 'news_category', 'news', array(
  'labels' => $news_category_labels,
  'hierarchical' => true,
  'rewrite' => array( 'slug' => $news_category_slug ),
  'show_in_rest' => true	// ブロックエディターを使用しない、REST APIで表示しない
));

/* アーカイブページの記事数を変更 */
function change_news_num( $query ) {
  $options = get_design_plus_option();
  if(is_mobile()){
    $post_num = $options['archive_news_num_sp'];
  } else {
    $post_num = $options['archive_news_num'];
  }
  if( !is_admin() && is_post_type_archive('news')) {
    if($query->is_main_query()) {
      $query->set('posts_per_page', $post_num);
      return;
    };
  }
}
add_action( 'pre_get_posts', 'change_news_num' );

} // end if use news


// カスタム投稿「治療」 --------------------------------------------------------------------------------
if($options['use_treatment']){

$treatment_label = $options['treatment_label'] ? esc_html( $options['treatment_label'] ) : __( 'Treatment', 'tcd-serum' );
$treatment_slug = $options['treatment_slug'] ? sanitize_title( $options['treatment_slug'] ) : 'treatment';
$treatment_labels = array(
  'name' => $treatment_label,
  'add_new' => __( 'Add New', 'tcd-serum' ),
  'add_new_item' => __( 'Add New Item', 'tcd-serum' ),
  'edit_item' => __( 'Edit', 'tcd-serum' ),
  'new_item' => __( 'New item', 'tcd-serum' ),
  'view_item' => __( 'View Item', 'tcd-serum' ),
  'search_items' => __( 'Search Items', 'tcd-serum' ),
  'not_found' => __( 'Not Found', 'tcd-serum' ),
  'not_found_in_trash' => __( 'Not found in trash', 'tcd-serum' ),
  'parent_item_colon' => ''
);

register_post_type( 'treatment', array(
  'label' => $treatment_label,
  'labels' => $treatment_labels,
  'public' => true,
  'publicly_queryable' => true,
  'menu_position' => 5,
  'show_ui' => true,
  'query_var' => true,
  'rewrite' => array( 'slug' => $treatment_slug ),
  'capability_type' => 'post',
  'has_archive' => true,
  'hierarchical' => false,
  'supports' => array( 'title', 'thumbnail', 'editor' ),
  'show_in_rest' => true	// ブロックエディターを使用しない、REST APIで表示しない
));

// 「治療」カテゴリー
$treatment_category_label = sprintf(__('%s category', 'tcd-serum'), $treatment_label);
$treatment_category_slug = $treatment_slug . '_category';
$treatment_category_labels = array(
  'name' => $treatment_category_label,
  'singular_name' => $treatment_category_label
);
register_taxonomy( 'treatment_category', 'treatment', array(
  'labels' => $treatment_category_labels,
  'hierarchical' => true,
  'rewrite' => array( 'slug' => $treatment_category_slug ),
  'show_in_rest' => true	// ブロックエディターを使用しない、REST APIで表示しない
));

} // end if use_treatment


// カスタム投稿ここまで

}
add_action( 'init', 'custom_post_type_init' );


// ブログアーカイブページの表示数 --------------------------------------------------------------------------------
function change_blog_num( $query ) {
  if( (!is_admin() && is_archive()) || (!is_admin() && is_home()) || (!is_admin() && is_search())) {
    if($query->is_main_query()) {
      $post_num = get_option('posts_per_page');
      if(!is_mobile()){
        $query->set('posts_per_page', $post_num);
      }
      return;
    };
  }
}
add_action( 'pre_get_posts', 'change_blog_num' );


// 全てのカスタムフィールドを検索対象に含める --------------------------------------------------------------------------------
function cf_search_join($join, $query) {
    global $wpdb;
    if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
        $join .=' LEFT JOIN '.$wpdb->postmeta. ' AS tcd_pm_search ON '. $wpdb->posts . '.ID = tcd_pm_search.post_id ';
    }
    return $join;
}
add_filter('posts_join', 'cf_search_join', 10, 2);

function cf_search_where($where, $query) {
    global $wpdb;
    if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
        $where = preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (tcd_pm_search.meta_value LIKE $1)", $where);
    }
    return $where;
}
add_filter('posts_where', 'cf_search_where', 10, 2);

function cf_search_distinct($distinct, $query) {
    global $wpdb;
    if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
        return "DISTINCT";
    }
    return $distinct;
}
add_filter('posts_distinct', 'cf_search_distinct', 10, 2);


// 検索対象にする記事タイプを設定 --------------------------------------------------------------------------------
function SearchFilter($query) {
  $options = get_design_plus_option();
  if ( !is_admin() && $query->is_main_query() && $query->is_search() ) {
    $post_types = array();
    if($options['search_type_post'] == 'yes'){
      array_push($post_types,'post');
    }
    if($options['search_type_page'] == 'yes'){
      array_push($post_types,'page');
    }
    if($options['use_news'] && $options['search_type_news'] == 'yes'){
      array_push($post_types,'news');
    }
    if($options['use_treatment'] &&  $options['search_type_treatment'] == 'yes'){
      array_push($post_types,'treatment');
    }
    $query->set('post_type', $post_types );

    if($options['search_type_post'] == 'no' && $options['search_type_page'] == 'no' && $options['search_type_news'] == 'no' && $options['search_type_treatment'] == 'no'){
      $query->set('name', 'set_dummy_page_id' );
    }

    if($options['search_type_page'] == 'yes'){
      $front_page_id = get_option('page_on_front');
      if($front_page_id){
        $query->set('post__not_in', array($front_page_id) );
      }
    }
  }
}
add_action( 'pre_get_posts','SearchFilter' );


// タイトルとurlをコピーのスクリプト --------------------------------------------------------------------------------
function copy_title_url_script() {
  global $options;
  if ( ! $options ) $options = get_design_plus_option();

  if ( (is_singular('post') && $options['single_blog_show_copy_top'] == 'display') || (is_singular('post') && $options['single_blog_show_copy_btm'] == 'display') || (is_singular('news') && $options['single_news_show_copy_top'] == 'display') || (is_singular('news') && $options['single_news_show_copy_btm'] == 'display') ) {
    wp_enqueue_script( 'copy_title_url', get_template_directory_uri().'/js/copy_title_url.js', array(), version_num(), true );
  }
}
add_action( 'wp_enqueue_scripts', 'copy_title_url_script' );


// カテゴリー編集画面にIDを表示する ------------------------------------------------------------------------------------
function add_category_columns( $columns ) {
  echo '<style>
  .taxonomy-category .manage-column.num {width: 90px;}
  .taxonomy-category .manage-column.column-id {width: 60px;}
  </style>';

  $columns['id'] = 'ID';
  return $columns;
}
function add_category_sortable_columns( $columns ) {
  $columns['id'] = 'ID';
  return $columns;
}
function custom_category_column( $content, $column_name, $term_id ) {
  if ( $column_name == 'id' ) {
    echo $term_id;
  }
}
add_filter( 'manage_edit-category_columns', 'add_category_columns' );
add_filter( 'manage_edit-category_sortable_columns', 'add_category_sortable_columns' );
add_action( 'manage_category_custom_column', 'custom_category_column', 10, 3 );


// ページのナビの有無をチェック ---------------------------------------------------------------------------------------
function show_posts_nav() {
  global $wp_query;
  return ($wp_query->max_num_pages > 1);
};


// ブログ用固定ページかっらメタボックス削除 ------------------------------------------------------------------------
function tcd_remove_meta_boxes() {
  global $typenow, $post;

  // ホームページ・投稿ページ表示に設定されているに固定ページ編集時
  if ( 'page' === $typenow && ! empty( $post->ID ) && 'page' === get_option('show_on_front') && in_array( $post->ID, array( get_option( 'page_on_front' ), get_option( 'page_for_posts' ) ) ) ) {
    remove_meta_box( 'tcd_meta_box1', 'page', 'normal' );
    remove_meta_box( 'select_pw_meta_box', 'page', 'normal' );
    remove_meta_box( 'postexcerpt', 'page', 'normal' );
    remove_meta_box( 'pageparentdiv', 'page', 'normal' );
  }

}
add_action( 'add_meta_boxes', 'tcd_remove_meta_boxes', 999 );


// 1行ごとに<span>タグで囲む ------------------------------------------------------------------------
function sepLine($text) {
//  $lines = explode("\n", $text);
  $lines = preg_split("/\<br\>|\<\/br\>|\n/", $text);
  $text = '';
  if ( !empty($lines) ) {
    foreach ( $lines as $line ) {
      $text .= '<span>'. trim($line) .'</span>';
    }
  }
  return $text;
}

/**
 * 管理画面 サイトヘルスのWP情報にユーザーエージェント追加
 *
 * NOTE: カスタマーサポート対策
 */
add_filter( 'debug_information', 'tcd_add_debug_information' );
function tcd_add_debug_information( $info ) {
  if( isset( $info['wp-core']['fields'] ) ){
    $info['wp-core']['fields']['user_agent'] = [
      'label' => 'User Agent',
      'value' => $_SERVER['HTTP_USER_AGENT'] ?? 'UA could not be retrieved',
    ];
  }
  return $info;
}

/**
 * PWAプラグイン未インストール時のメッセージ
 *
 * NOTE: TCDユーザーがPWAプラグインを知る・使うための導線を作るために用意
 */
add_action( 'admin_notices', 'tcd_pwa_admin_notice' );
function tcd_pwa_admin_notice(){
  global $plugin_page;

  // テーマオプションページ以外では表示しない
  if( $plugin_page !== 'theme_options' ){
    return;
  }

  // TCD PWA が有効化されていれば表示しない
  if( defined( 'TCDPWA_ACTIVE' ) && TCDPWA_ACTIVE ){
    return;
  }

  // チェックしたいプラグインのメインファイルを指定
  $target_plugin_file = 'tcd-pwa/tcd-pwa.php';

  // すべてのインストール済みプラグインを取得
  $installed_plugins = get_plugins();

  // インストール済みなら終了
  if( isset( $installed_plugins[$target_plugin_file] ) ){
    return;
  }

  // notice作成
  printf(
    '<div class="notice notice-info is-dismissible">
      <p>%1$s</p>
      <p>
        <a class="button" href="%2$s" target="_blank">%3$s</a>
        <a class="button button-primary" href="%4$s" target="_blank">%5$s</a>
      </p>
    </div>',
    // TCDテーマをPWA化できるプラグイン「TCD Progressive Web Apps」を利用できます。
    __( 'The TCD Progressive Web Apps plugin is available to convert TCD themes into PWAs.','tcd-serum' ),
    // 解説記事URL
    'https://tcd-theme.com/2025/05/tcd-pwa.html',
    // 設定・使い方
    __( 'Settings/How to use','tcd-serum' ),
    // マイページの商品URL
    'https://tcd.style/order-history?pname=TCD+Progressive+Web+Apps',
    // 今すぐインストール
    __( 'Install Now','tcd-serum' )
  );
}