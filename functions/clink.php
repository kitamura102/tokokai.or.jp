<?php
/**
 * カードリンクパーツ
 */
function get_the_custom_excerpt( $content, $length ) {
	$length = ( $length ? $length : 70 ); // デフォルトの長さを指定する
  $content =  preg_replace( '/<!--more-->.+/is', '', $content ); // moreタグ以降削除
 	$content =  strip_shortcodes( $content ); // ショートコード削除
  $content =  strip_tags( $content ); // タグの除去
  $content =  str_replace( '&nbsp;', '', $content ); // 特殊文字の削除（今回はスペースのみ）
  $content =  mb_substr( $content, 0, $length ); // 文字列を指定した長さで切り取る
  return $content.'...';
}

/**
 * カードリンクショートコード
 *
 * @param array $atts ユーザーがショートコードタグに指定した属性
 * @return string カードリンクの HTML
 */
function clink_scode( $atts ) {

  // ユーザーがショートコードに指定した属性を、あらかじめ定義した属性と結合
  $atts = shortcode_atts(
    array(
      'url' => '',
      'title' => '',
      'excerpt' => ''
    ),
    $atts
  );

  if ( ! $atts['url'] ) return;

  // URL から投稿 ID を取得
  $id = url_to_postid( $atts['url'] );

  if ( $id ) {
    return get_internal_clink_html( $id, $atts );
  } else {
    return get_external_clink_html( $atts );
  }
}

/**
 * 内部リンクのカードリンクの HTML を作成
 *
 * @param int $id 投稿 ID
 * @param array $atts ユーザーがショートコードタグに指定した属性
 * @return string カードリンクの HTML
 */
function get_internal_clink_html( $id, $atts ) {

  $options = get_design_plus_option();

  // ID から投稿情報を取得
  $post = get_post( $id );

if(!empty($post)){

  // 投稿日を取得
  $date = mysql2date( 'Y.m.d', $post->post_date );

  // タイトルを取得
  $title = $atts['title'] ? $atts['title'] : get_the_title( $id );
  $title = is_mobile() ? wp_trim_words( $title, 42, '...' ) : $title;

  // 抜粋を取得
  $excerpt = $atts['excerpt'];

  if ( ! $excerpt ) {
    if ( $post->post_excerpt ) {
      $excerpt = get_the_custom_excerpt( $post->post_excerpt, 150 );
    } else {
      $excerpt = get_the_custom_excerpt( $post->post_content, 150 );
    }
  }

  $excerpt = is_mobile() ? wp_trim_words( $excerpt, 50, '...' ) : $excerpt;

  // アイキャッチ画像を取得
  if ( has_post_thumbnail( $id ) ) {
    $img = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'size-card' );
    $img = $img[0];
  } elseif($options['no_image']) {
    $img = wp_get_attachment_image_src( $options['no_image'], 'full' );
    $img = $img[0];
  } else {
    $img = get_template_directory_uri() . "/img/common/no_image2.gif";
  }

  $clink  = '<div class="cardlink">
    <a class="image" href="' . esc_url( $atts['url'] ) . '">
     <img src="' . esc_attr( $img ) . '">
    </a>
    <div class="content">
     <div class="title_area">
      <p class="date">' . esc_html( $date ) . '</p>
      <div class="title">
       <a href="' . esc_url( $atts['url'] ) . '">' . esc_html( $title ) . '</a>
      </div>
     </div>
     <p class="desc"><span>' . esc_html( $excerpt ) . '</span></p>
    </div>
   </div>' . "\n";

  return $clink;

} else {

  return;

}

}

require_once( 'OpenGraph.php' );

/**
 * 外部リンクのカードリンクの HTML を作成
 *
 * @see OpenGraph::fetch()
 *
 * @param array $atts ユーザーがショートコードタグに指定した属性
 * @return string カードリンクの HTML
 */
function get_external_clink_html( $atts ) {

  $options = get_design_plus_option();

  $graph = OpenGraph::fetch( $atts['url'] );

if(!empty($graph)){

  // タイトルを取得
  $title = $atts['title'] ? $atts['title'] : $graph->title;

  // 抜粋を取得
  $excerpt = $atts['excerpt'] ? $atts['excerpt'] : $graph->description;

  // 画像を取得
  if($graph->image){
    $img = $graph->image;
  } elseif($options['no_image']) {
    $img = wp_get_attachment_image_src( $options['no_image'], 'full' );
    $img = $img[0];
  } else {
    $img = get_template_directory_uri() . "/img/common/no_image2.gif";
  }

  $clink  = '<div class="cardlink">
    <a class="image" href="' . esc_url( $atts['url'] ) . '">
     <img src="' . esc_attr( $img ) . '">
    </a>
    <div class="content">
     <div class="title_area">
      <div class="title">
       <a href="' . esc_url( $atts['url'] ) . '">' . esc_html( $title ) . '</a>
      </div>
     </div>
     <p class="desc"><span>' . esc_html( $excerpt ) . '</span></p>
    </div>
   </div>' . "\n";

  return $clink;

} else {

  return;

}

}
add_shortcode( 'clink', 'clink_scode' );
