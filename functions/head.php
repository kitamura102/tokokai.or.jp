<?php
     function tcd_head() {
       $options = get_design_plus_option();
       global $post;

       /* URLやモバイル等でcssが変わらないものをここで出力 */
?>
<style type="text/css">
<?php
     // フォントの設定　------------------------------------------------------------------
?>
body { font-size:<?php echo esc_html($options['content_font_size']); ?>px; }
.single_title { font-size:<?php echo esc_html($options['single_title_font_size']); ?>px; }
#page_header .catch, #page_header_small .catch { font-size:<?php echo esc_html($options['page_header_font_size']); ?>px; }
.common_catch, .cb_free_space .post_content h2:not(.styled_h2) { font-size:<?php echo esc_html($options['catch_font_size']); ?>px !important; }
@media screen and (max-width:1221px) {
  .common_catch,  .cb_free_space .post_content h2:not(.styled_h2) { font-size:<?php echo esc_html(floor( (( int )$options['catch_font_size'] + ( int )$options['catch_font_size_sp']) / 2)); ?>px  !important; }
  #page_header .catch, #page_header_small .catch { font-size:<?php echo esc_html(floor( (( int )$options['page_header_font_size'] + ( int )$options['page_header_font_size_sp']) / 2 ) ); ?>px; }
}
@media screen and (max-width:800px) {
  body { font-size:<?php echo esc_html($options['content_font_size_sp']); ?>px; }
  #page_header .catch, #page_header_small .catch { font-size:<?php echo esc_html($options['page_header_font_size_sp']); ?>px; }
  .single_title { font-size:<?php echo esc_html($options['single_title_font_size_sp']); ?>px; }
  .common_catch,  .cb_free_space .post_content h2:not(.styled_h2) { font-size:<?php echo esc_html($options['catch_font_size_sp']); ?>px !important; }
}
<?php
     // 基本のフォントタイプ
     if($options['content_font_type'] == 'type1') {
?>
body, input, textarea { font-family: Arial, "ヒラギノ角ゴ ProN W3", "Hiragino Kaku Gothic ProN", "メイリオ", Meiryo, sans-serif; }
<?php } elseif($options['content_font_type'] == 'type2') { ?>
body, input, textarea { font-weight:500; font-family: Arial, "Hiragino Sans", "ヒラギノ角ゴ ProN", "Hiragino Kaku Gothic ProN", "游ゴシック", YuGothic, "メイリオ", Meiryo, sans-serif; }
<?php } else { ?>
body, input, textarea { font-family: "Times New Roman" , "游明朝" , "Yu Mincho" , "游明朝体" , "YuMincho" , "ヒラギノ明朝 Pro W3" , "Hiragino Mincho Pro" , "HiraMinProN-W3" , "HGS明朝E" , "ＭＳ Ｐ明朝" , "MS PMincho" , serif; }
<?php }; ?>

<?php
     // ページヘッダーのフォントタイプ
     if($options['page_header_font_type'] == 'type1') {
?>
#page_header .catch, #page_header_small .catch { font-family: Arial, "ヒラギノ角ゴ ProN W3", "Hiragino Kaku Gothic ProN", "メイリオ", Meiryo, sans-serif; font-weight:600; }
<?php } elseif($options['page_header_font_type'] == 'type2') { ?>
#page_header .catch, #page_header_small .catch { font-family: Arial, "Hiragino Sans", "ヒラギノ角ゴ ProN", "Hiragino Kaku Gothic ProN", "游ゴシック", YuGothic, "メイリオ", Meiryo, sans-serif; font-weight:600; }
<?php } else { ?>
#page_header .catch, #page_header_small .catch { font-family: "Times New Roman" , "游明朝" , "Yu Mincho" , "游明朝体" , "YuMincho" , "ヒラギノ明朝 Pro W3" , "Hiragino Mincho Pro" , "HiraMinProN-W3" , "HGS明朝E" , "ＭＳ Ｐ明朝" , "MS PMincho" , serif; font-weight:600; }
<?php }; ?>

<?php
     // 見出しのフォントタイプ
     if($options['catch_font_type'] == 'type1') {
?>
.common_catch,  .cb_free_space .post_content h2:not(.styled_h2), .design_headline, .rich_font, .p-vertical { font-family: Arial, "ヒラギノ角ゴ ProN W3", "Hiragino Kaku Gothic ProN", "メイリオ", Meiryo, sans-serif; font-weight:600; }
<?php } elseif($options['catch_font_type'] == 'type2') { ?>
.common_catch,  .cb_free_space .post_content h2:not(.styled_h2), .design_headline, .rich_font, .p-vertical { font-family: Arial, "Hiragino Sans", "ヒラギノ角ゴ ProN", "Hiragino Kaku Gothic ProN", "游ゴシック", YuGothic, "メイリオ", Meiryo, sans-serif; font-weight:600; }
<?php } else { ?>
.common_catch,  .cb_free_space .post_content h2:not(.styled_h2), .design_headline, .rich_font, .p-vertical { font-family: "Times New Roman" , "游明朝" , "Yu Mincho" , "游明朝体" , "YuMincho" , "ヒラギノ明朝 Pro W3" , "Hiragino Mincho Pro" , "HiraMinProN-W3" , "HGS明朝E" , "ＭＳ Ｐ明朝" , "MS PMincho" , serif; font-weight:600; }
<?php }; ?>

<?php
     // 詳細ページの記事タイトルのフォントタイプ
     if(is_single() && $options['single_title_font_type'] == 'type1' || is_page() && $options['single_title_font_type'] == 'type1') {
?>
.single_title{ font-family: Arial, "ヒラギノ角ゴ ProN W3", "Hiragino Kaku Gothic ProN", "メイリオ", Meiryo, sans-serif; font-weight:600; }
<?php } elseif(is_single() && $options['single_title_font_type'] == 'type2' || is_page() && $options['single_title_font_type'] == 'type2') { ?>
.single_title { font-family: Arial, "Hiragino Sans", "ヒラギノ角ゴ ProN", "Hiragino Kaku Gothic ProN", "游ゴシック", YuGothic, "メイリオ", Meiryo, sans-serif; font-weight:600; }
<?php } elseif(is_single() && $options['single_title_font_type'] == 'type3' || is_page() && $options['single_title_font_type'] == 'type3') { ?>
.single_title { font-family: "Times New Roman" , "游明朝" , "Yu Mincho" , "游明朝体" , "YuMincho" , "ヒラギノ明朝 Pro W3" , "Hiragino Mincho Pro" , "HiraMinProN-W3" , "HGS明朝E" , "ＭＳ Ｐ明朝" , "MS PMincho" , serif; font-weight:600; }
<?php }; ?>

.rich_font_type1 { font-family: Arial, "ヒラギノ角ゴ ProN W3", "Hiragino Kaku Gothic ProN", "メイリオ", Meiryo, sans-serif; font-weight:600; }
.rich_font_type2 { font-family: Arial, "Hiragino Sans", "ヒラギノ角ゴ ProN", "Hiragino Kaku Gothic ProN", "游ゴシック", YuGothic, "メイリオ", Meiryo, sans-serif; font-weight:600; }
.rich_font_type3 { font-family: "Times New Roman" , "游明朝" , "Yu Mincho" , "游明朝体" , "YuMincho" , "ヒラギノ明朝 Pro W3" , "Hiragino Mincho Pro" , "HiraMinProN-W3" , "HGS明朝E" , "ＭＳ Ｐ明朝" , "MS PMincho" , serif; font-weight:600; }

<?php
     // ヘッダー -------------------------------------------------------------------------------

     // ロゴ
?>
.logo_text { font-size:<?php echo esc_html($options['header_logo_font_size']); ?>px; }
@media screen and (max-width:1201px) {
  .logo_text { font-size:<?php echo esc_html($options['header_logo_font_size_sp']); ?>px; }
}
<?php
     // メッセージ -----------------------------------------------------------------------------------
     if(!is_404()){
       if( (is_front_page() && $options['show_header_message'] == 'display') || (!is_page() && $options['show_header_message'] == 'display') || (is_page() && !is_page_template('page-tcd-lp.php') && $options['show_header_message'] == 'display') || (is_page() && is_page_template('page-tcd-lp.php') && (get_post_meta($post->ID, 'hide_header_message', true)) == 'no') ) {
?>
#header_message { background:<?php echo esc_attr($options['header_message_bg_color']); ?>; color:<?php echo esc_attr($options['header_message_font_color']); ?>; }
#close_header_message:before { color:<?php echo esc_attr($options['header_message_font_color']); ?>; }
#header_message a { color:<?php echo esc_attr($options['header_message_font_color']); ?> !important; }
<?php
       };
     };

      // フッター -------------------------------------------------------------------------------

     // サムネイルのホバーアニメーション設定　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
     if($options['hover_type']!="type5"){

       // ズームイン ------------------------------------------------------------------------------
       if($options['hover_type']=="type1"){
?>
.author_profile .avatar_area img, .animate_image img, .animate_background .image {
  width:100%; height:auto; will-change:transform;
  -webkit-transition: transform  0.5s ease;
  transition: transform  0.5s ease;
}
.author_profile a.avatar:hover img, .animate_image:hover img, .animate_background:hover .image {
  -webkit-transform: scale(<?php echo $options['hover1_zoom']; ?>);
  transform: scale(<?php echo $options['hover1_zoom']; ?>);
}

<?php
     // ズームアウト ------------------------------------------------------------------------------
     } if($options['hover_type']=="type2"){
?>
.author_profile .avatar_area img, .animate_image img, .animate_background .image {
  width:100%; height:auto; will-change:transform;
  -webkit-transition: transform  0.5s ease;
  transition: transform  0.5s ease;
  -webkit-transform: scale(<?php echo $options['hover2_zoom']; ?>);
  transform: scale(<?php echo $options['hover2_zoom']; ?>);
}
.author_profile a.avatar:hover img, .animate_image:hover img, .animate_background:hover .image {
  -webkit-transform: scale(1);
  transform: scale(1);
}

<?php
     // スライド ------------------------------------------------------------------------------
     } elseif($options['hover_type']=="type3"){
       $hover3_bgcolor_hex = hex2rgb($options['hover3_bgcolor']);
       $hover3_bgcolor_hex = implode(",",$hover3_bgcolor_hex);
?>
.author_profile .avatar_area:before, .animate_image:before, .animate_background .image_wrap:before {
  background:rgba(<?php echo esc_attr($hover3_bgcolor_hex); ?>,<?php echo esc_attr($options['hover3_opacity']); ?>); content:''; display:block; position:absolute; top:0; left:0; z-index:10; width:100%; height:100%; opacity:0; pointer-events:none;
  -webkit-transition: opacity 0.3s ease; transition: opacity 0.3s ease;
}
.author_profile .avatar_area:hover:before, .animate_image:hover:before, .animate_background:hover .image_wrap:before {
  opacity:1;
}
.animate_image img, .animate_background .image {
  -webkit-width:calc(100% + 30px) !important; width:calc(100% + 30px) !important; height:auto; max-width:inherit !important;
  <?php if($options['hover3_direct']=='type1'): ?>
  -webkit-transform: translate(-15px, 0px); -webkit-transition-property: opacity, translateX; -webkit-transition: 0.5s;
  transform: translate(-15px, 0px); transition-property: opacity, translateX; transition: 0.5s;
  <?php else: ?>
  -webkit-transform: translate(-15px, 0px); -webkit-transition-property: opacity, translateX; -webkit-transition: 0.5s;
  transform: translate(-15px, 0px); transition-property: opacity, translateX; transition: 0.5s;
  <?php endif; ?>
}
.animate_image.avatar_area img {
  width:calc(100% + 10px) !important;
  <?php if($options['hover3_direct']=='type1'): ?>
  -webkit-transform: translate(-5px, 0px); transform: translate(-5px, 0px);
  <?php else: ?>
  -webkit-transform: translate(-5px, 0px); transform: translate(-5px, 0px);
  <?php endif; ?>
}
.animate_image:hover img, .animate_background:hover .image {
  <?php if($options['hover3_direct']=='type1'): ?>
  -webkit-transform: translate(0px, 0px);
  transform: translate(0px, 0px);
  <?php else: ?>
  -webkit-transform: translate(-30px, 0px);
  transform: translate(-30px, 0px);
  <?php endif; ?>
}
<?php
     // フェードアウト ------------------------------------------------------------------------------
     } elseif($options['hover_type']=="type4"){
       $hover3_bgcolor_hex = hex2rgb($options['hover4_bgcolor']);
       $hover3_bgcolor_hex = implode(",",$hover3_bgcolor_hex);
?>
.author_profile .avatar_area:before, .animate_image:before, .animate_background .image_wrap:before {
  background:rgba(<?php echo esc_attr($hover3_bgcolor_hex); ?>,<?php echo esc_attr($options['hover4_opacity']); ?>); content:''; display:block; position:absolute; top:0; left:0; z-index:10; width:100%; height:100%; opacity:0; pointer-events:none;
  -webkit-transition: opacity 0.3s ease; transition: opacity 0.3s ease;
}
.author_profile .avatar_area:hover:before, .animate_image:hover:before, .animate_background:hover .image_wrap:before {
  opacity:1;
}
<?php }; }; // アニメーションここまで ?>

<?php
     // 色関連のスタイル　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
?>
a { color:#000; }

<?php
     // メインカラー ----------------------------------
     $main_color = $options['main_color'];
     $main_color_hex = hex2rgb($main_color);
     $main_color_hex = implode(",",$main_color_hex);
     $hover_color = adjustBrightness($main_color, -40);
?>
:root {
  --tcd-accent-color:<?php echo esc_html(implode(",",hex2rgb($options['main_color']))); ?>;
}
#header_logo2 .icon_image, #comment_tab li.active a, .widget_tab_post_list_button div.active, .widget_categories a:before, #single_post_category, #header_slider_wrap .slick-dots .slick-active button::before, #side_icon_button a.no_icon, #drawer_icon_button .item.long a, .tcdw_search_box_widget .tag_list a:hover
{ background-color:<?php echo esc_html($main_color); ?>; }

.schedule_content table, .schedule_content td, .splide__arrow, #header_slider_wrap .slick-dots button::before, #side_icon_button a.no_icon, #side_icon_button .item:first-of-type a.no_icon, #drawer_icon_button .item.long a
{ border-color:<?php echo esc_html($main_color); ?>; }

#side_icon_button a, #footer_icon_banner a:before, .icon_button .google_icon, #footer_sns.color_type1 li a:before, #bread_crumb, #bread_crumb li.last, #related_post .headline, .news_category_button li.current a, .schedule_content table, #comments .headline, .splide__arrow:before, #treatment_list .header .catch, #page_contents .content_header .common_catch, #post_pagination p,
  #global_menu > ul > li.current-menu-item > a, .megamenu_a .category_list li.active a, #global_menu > ul > li.active_megamenu_button > a, .faq_list .title.active, .page_navi span.current, #drawer_icon_button a, #drawer_icon_button a:before, #drawer_icon_button .item.long a, .doctor_meta .item.name a:hover
{ color:<?php echo esc_html($main_color); ?>; }

.cardlink .title a:hover { color:<?php echo esc_html($hover_color); ?> !important; }

.doctor_meta .pos { background-color:rgba(<?php echo esc_html($main_color_hex); ?>,0.7); }
#global_menu ul ul a:hover { background-color:rgba(<?php echo esc_html($main_color_hex); ?>,0.25); }

#side_icon_button a.no_icon:hover, .splide__arrow:hover, #single_post_category:hover, #p_readmore .button:hover, .c-pw__btn:hover, #comment_tab li a:hover, #submit_comment:hover, #cancel_comment_reply a:hover, #post_tag_list a:hover,  #wp-calendar #prev a:hover, #wp-calendar #next a:hover, #wp-calendar td a:hover, #comment_tab li a:hover, .tcdw_tag_list_widget ol a:hover,
  .widget_tag_cloud .tagcloud a:hover, #wp-calendar tbody a:hover, #drawer_menu .menu li.menu-item-has-children > a > .button:hover:after, #drawer_menu .menu li.menu-item-has-children > a > .button:hover:before, #mobile_menu .child_menu_button:hover:after, #mobile_menu .child_menu_button:hover:before, #header_slider_wrap .slick-dots button:hover::before
{ background-color:<?php echo esc_html($hover_color); ?>; }

#side_icon_button a.no_icon:hover, .splide__arrow:hover, #comment_textarea textarea:focus, .c-pw__box-input:focus, .tcdw_tag_list_widget ol a:hover, .widget_tag_cloud .tagcloud a:hover, #header_slider_wrap .slick-dots button:hover::before, #side_icon_button .item:first-of-type a.no_icon:hover
{ border-color:<?php echo esc_html($hover_color); ?>; }

a:hover, #header_logo a:hover, #drawer_menu .menu ul ul a:hover, #drawer_menu .menu li > a:hover > span:after, #drawer_menu .menu li.active > a > .button:after, #featured_post a:hover, #drawer_menu .close_button:hover:before, #drawer_menu_search .button_area:hover:before, #drawer_lang_button li a:hover, .megamenu_b .splide__arrow:hover:before, #related_post .meta .news_category:hover,
  #header.active #header_search_button:hover:before, #global_menu > ul > li > a:hover, #global_menu > ul > li.current-menu-parent > a, #global_menu > ul > li.current-menu-ancestor > a, #header_search_button:hover:before, #header_search .button:hover label:before, .single_post_nav:hover span:after, .faq_list .title:hover,
    #drawer_menu .menu a:hover, #drawer_menu .menu > ul > li.active > a, #drawer_menu .menu > ul > li.current-menu-item > a, #drawer_menu .menu > li > a > .title:hover, .cb_news_list .news_category_sort_button li.active span, .cb_news_list .news_category_sort_button li:hover span, #searchform .submit_button:hover:before, #footer_social_link li a:hover:before, #next_prev_post a:hover, .tcdw_search_box_widget .search_area .search_button:hover:before,
      #single_author_title_area .author_link li a:hover:before, .author_profile a:hover, #post_meta_bottom a:hover, .cardlink_title a:hover, .comment a:hover, .comment_form_wrapper a:hover, #tcd_toc.styled .toc_link:hover, .tcd_toc_widget.no_underline .toc_widget_wrap.styled .toc_link:hover, #news_list .category:hover, #single_post_title .meta .news_category:hover, #treatment_list .post_list a:hover, .mega_treatment_category a:hover .title
{ color:<?php echo esc_html($hover_color); ?>; }

#archive_blog, .breadcrumb_type2 #bread_crumb, .cb_carousel, .cb_free_space:before, #treatment_list, #mobile_menu li li a, #mobile_menu li ul, body.single-post #main_content, #page_contents .color_bg_content::before, .cb_two_column
{ background-color:<?php echo esc_html($options['bg_color']); ?>; }

<?php
     // 詳細ページのテキストカラー ----------------------------------
     $content_link_color = $options['content_link_color'];
     $content_link_color_hex = hex2rgb($content_link_color);
     $content_link_color_hex = implode(",",$content_link_color_hex);
?>
.post_content a, .widget_block a, .textwidget a, #no_post a, #page_404_header .desc a { color:<?php echo esc_html($content_link_color); ?>; }
.widget_block a:hover, #no_post a:hover, #page_404_header .desc a:hover { color:rgba(<?php echo esc_html($content_link_color_hex); ?>,0.6); }
<?php
     // デザインボタン ----------------------------------------------
     $button_type = $options['design_button_type'];
     $button_shape = $options['design_button_border_radius'];
     $button_size = $options['design_button_size'];
     $button_animation_type = $options['design_button_animation_type'];
     $button_color = $options['design_button_color'];
     $button_color_hover = $options['design_button_color_hover'];
     $colors = array();
     $animations = array();
     if($button_shape == 'flat'){
       $shape = 'border-radius:0px;';
     } elseif($button_shape == 'rounded'){
       $shape = 'border-radius:6px;';
     } else {
       $shape = 'border-radius:70px;';
     }
     if($button_size == 'small'){
       $size = 'width:130px; height:40px; line-height:40px;';
       $sp_size1 = 'width:130px;';
       $sp_size2 = 'width:130px;';
     } elseif($button_size == 'medium'){
       $size = 'width:280px; height:60px; line-height:60px;';
       $sp_size1 = 'width:260px;';
       $sp_size2 = 'width:240px; height:50px; line-height:50px;';
     } else {
       $size = 'width:400px; height:70px; line-height:70px;';
       $sp_size1 = 'width:400px;';
       $sp_size2 = 'width:400px;';
     }
     if($button_type == 'type1'){
       $colors = array('color:#fff !important; background-color:'.$button_color.'; border:none;', 'background-color:'.$button_color_hover.';', '' );
     } elseif($button_type == 'type2'){
       $colors = array('color:'.$button_color.' !important; border-color:'.$button_color.';', 'background-color:'.$button_color_hover.';', 'color:#fff !important; border-color:'.$button_color_hover.';');
     } else {
       $colors = array('border-color:'.$button_color.';','background-color:'.$button_color.';', 'color:'.$button_color_hover.' !important; border-color:'.$button_color_hover.';' );
     }
     if($button_animation_type == 'animation_type1'){
       $animations = ($button_type != 'type3') ? array('opacity:0;', 'opacity:1;') : array('opacity:1;', 'opacity:0;');
     } elseif($button_animation_type == 'animation_type2'){
       $animations = ($button_type != 'type3') ? array('left:-100%;', 'left:0;') : array('left:0;', 'left:100%;');
     } else {
       $animations = ($button_type != 'type3') ? array('left:calc(-100% - 110px);transform:skewX(45deg); width:calc(100% + 70px);', 'left:-35px;') : array('left:-35px;transform:skewX(45deg); width:calc(100% + 70px);', 'left:calc(100% + 50px);');
     }
?>
.design_button { <?php echo $size.$shape.$colors[0]; ?> }
.design_button:before { <?php echo $colors[1].$animations[0]; ?> }
.design_button:hover, .cb_box_content a:hover .design_button { <?php echo $colors[2]; ?> }
.design_button:hover:before, .cb_box_content a:hover .design_button:before { <?php echo $animations[1]; ?> }
@media (max-width: 1200px) {
  .design_button { <?php echo $sp_size1; ?> }
}
@media (max-width: 800px) {
  .design_button { <?php echo $sp_size2; ?> }
}
<?php
     // クイックタグ --------------------------------------------
    if ( $options['use_quicktags'] ) :

    
    for ( $i = 2; $i <= 5; $i++ ){

    // 見出し
    $heading_font_size = $options['qt_h'.$i.'_font_size'];
    $heading_font_size_sp = $options['qt_h'.$i.'_font_size_sp'];
    $heading_text_align = $options['qt_h'.$i.'_text_align'];
    $heading_font_weight = $options['qt_h'.$i.'_font_weight'];
    $heading_font_color = $options['qt_h'.$i.'_font_color'];
    $heading_bg_color = $options['qt_h'.$i.'_bg_color'];
    $heading_ignore_bg = $options['qt_h'.$i.'_ignore_bg'];
    $heading_border = 'qt_h'.$i.'_border_';
    $heading_border_color = $options['qt_h'.$i.'_border_color'];
    $heading_border_width = $options['qt_h'.$i.'_border_width'];
    $heading_border_style = $options['qt_h'.$i.'_border_style'];

?>
.styled_h<?php echo $i ?> {
  font-size:<?php echo esc_attr($heading_font_size); ?>px!important;
  text-align:<?php echo esc_attr($heading_text_align); ?>!important;
  font-weight:<?php echo esc_attr($heading_font_weight); ?>!important;
  color:<?php echo esc_attr($heading_font_color); ?>;
  border-color:<?php echo esc_attr($heading_border_color); ?>;
  border-width:<?php echo esc_attr($heading_border_width); ?>px;
  border-style:<?php echo esc_attr($heading_border_style); ?>;
<?php

  $border_potition = array('left', 'right', 'top', 'bottom');
  foreach( $border_potition as $position ):

    if($options[$heading_border.$position]){
      if($position == 'left' || $position == 'right'){
        echo 'padding-'.$position.':1em!important;'."\n".'padding-top:0.5em!important;'."\n".'padding-bottom:0.5em!important;'."\n";
      }else{
        echo 'padding-'.$position.':0.8em!important;'."\n";
      }
    }else{
      echo 'border-'.$position.':none;'."\n";
    }

  endforeach;

  if($heading_ignore_bg){
    echo 'background-color:transparent;'."\n";
  }else{
    echo 'background-color:'.esc_attr($heading_bg_color).';'."\n".'padding:0.8em 1em!important;'."\n";
  }

?>
}
@media screen and (max-width:800px) {
  .styled_h<?php echo $i ?> { font-size:<?php echo esc_attr($heading_font_size_sp); ?>px!important; }
}
<?php

    }

    // ボタン
    for ( $i = 1; $i <= 3; $i++ ) {
      $button_type = $options['qt_button'.$i.'_type'];
      $button_shape = $options['qt_button'.$i.'_border_radius'];
      $button_size = $options['qt_button'.$i.'_size'];
      $button_animation_type = $options['qt_button'.$i.'_animation_type'];
      $button_color = $options['qt_button'.$i.'_color'];
      $button_color_hover = $options['qt_button'.$i.'_color_hover'];

      $colors = array();
      $animations = array();

      switch ($button_shape){
        case 'flat': $shape = 'border-radius:0px;'; break;
        case 'rounded': $shape = 'border-radius:6px;'; break;
        case 'oval': $shape = 'border-radius:70px;'; break;
      }
      switch ($button_size){
        case 'small':
         $size = 'width:130px; height:40px; line-height:40px;';
         $sp_size1 = 'width:130px;';
         $sp_size2 = 'width:130px;';
         break;
        case 'medium':
          $size = 'width:280px; height:60px; line-height:60px;';
          $sp_size1 = 'width:260px;';
          $sp_size2 = 'width:240px; height:50px; line-height:50px;';
          break;
        case 'large':
          $size = 'width:400px; height:70px; line-height:70px;';
          $sp_size1 = 'width:400px;';
          $sp_size2 = 'width:400px;';
          break;
      }
      switch ($button_type){
        case 'type1': $colors = array('color:#fff !important; background-color:'.$button_color.';border:none;', 'background-color:'.$button_color_hover.';', '' ); break;
        case 'type2': $colors = array('color:'.$button_color.' !important; border-color:'.$button_color.';', 'background-color:'.$button_color_hover.';', 'color:#fff !important; border-color:'.$button_color_hover.';'); break;
        case 'type3': $colors = array('color:#fff !important; border-color:'.$button_color.';','background-color:'.$button_color.';', 'color:'.$button_color_hover.' !important; border-color:'.$button_color_hover.';' ); break;
      }
      switch ($button_animation_type){
        case 'animation_type1': $animations = ($button_type != 'type3') ? array('opacity:0;', 'opacity:1;') : array('opacity:1;', 'opacity:0;'); break;
        case 'animation_type2': $animations = ($button_type != 'type3') ? array('left:-100%;', 'left:0;') : array('left:0;', 'left:100%;'); break;
        case 'animation_type3': $animations = ($button_type != 'type3') ? array('left:calc(-100% - 110px);transform:skewX(45deg); width:calc(100% + 70px);', 'left:-35px;') : array('left:-35px;transform:skewX(45deg); width:calc(100% + 70px);', 'left:calc(100% + 50px);'); break;
      }

?>
.q_custom_button<?php echo $i; ?> { <?php echo $size.$shape.$colors[0]; ?> }
.q_custom_button<?php echo $i; ?>:before { <?php echo $colors[1].$animations[0]; ?> }
.q_custom_button<?php echo $i; ?>:hover { <?php echo $colors[2]; ?> }
.q_custom_button<?php echo $i; ?>:hover:before { <?php echo $animations[1]; ?> }
@media (max-width: 1200px) {
  .q_custom_button<?php echo $i; ?> { <?php echo $sp_size1; ?> }
}
@media (max-width: 800px) {
  .q_custom_button<?php echo $i; ?> { <?php echo $sp_size2; ?> }
}
<?php

    };

    // 囲み枠
    for ( $i = 1; $i <= 3; $i++ ) {

      $label_color = $options['qt_frame'.$i.'_label_color'];
      $bg_color = $options['qt_frame'.$i.'_content_bg_color'];
      $border_radius = $options['qt_frame'.$i.'_content_shape'];
      $border_width = $options['qt_frame'.$i.'_content_border_width'];
      $border_color = $options['qt_frame'.$i.'_content_border_color'];
      $border_style = $options['qt_frame'.$i.'_content_border_style'];


?>
.q_frame<?php echo $i; ?> {
  background:<?php echo esc_attr($bg_color); ?>;
  border-radius:<?php echo esc_attr($border_radius); ?>px;
  border-width:<?php echo esc_attr($border_width); ?>px;
  border-color:<?php echo esc_attr($border_color); ?>;
  border-style:<?php echo esc_attr($border_style); ?>;
}
.q_frame<?php echo $i; ?> .q_frame_label {
  color:<?php echo esc_attr($label_color); ?>;
}
<?php

    }

    // アンダーライン
    for ( $i = 1; $i <= 3; $i++ ) {

      $underline_color = $options['qt_underline'.$i.'_border_color'];
      $underline_font_weight = $options['qt_underline'.$i.'_font_weight'];
      $underline_use_animation = $options['qt_underline'.$i.'_use_animation'];

?>
.q_underline<?php echo $i; ?> {
  font-weight:<?php echo esc_attr($underline_font_weight); ?>;
  background-image: -webkit-linear-gradient(left, transparent 50%, <?php echo esc_attr($underline_color); ?> 50%);
  background-image: -moz-linear-gradient(left, transparent 50%, <?php echo esc_attr($underline_color); ?> 50%);
  background-image: linear-gradient(to right, transparent 50%, <?php echo esc_attr($underline_color); ?> 50%);
  <?php if($underline_use_animation == 'no') echo 'background-position:-100% 0.8em;'; ?>
}
<?php

    }

    // 吹き出し
    for ( $i = 1; $i <= 4; $i++ ) {

      $sb_font_color = $options['qt_speech_balloon'.$i.'_font_color'];
      $sb_bg_color = $options['qt_speech_balloon'.$i.'_bg_color'];
      $sb_border_color = $options['qt_speech_balloon'.$i.'_border_color'];
      $sb_direction = ($i >= 3) ? 'left' : 'right';

?>
.speech_balloon<?php echo $i; ?> .speech_balloon_text_inner {
  color:<?php echo esc_attr($sb_font_color); ?>;
  background-color:<?php echo esc_attr($sb_bg_color); ?>;
  border-color:<?php echo esc_attr($sb_border_color); ?>;
}
.speech_balloon<?php echo $i; ?> .before { border-left-color:<?php echo esc_attr($sb_border_color); ?>; }
.speech_balloon<?php echo $i; ?> .after { border-right-color:<?php echo esc_attr($sb_bg_color); ?>; }
<?php

    }

    endif;

    // Google map
    $qt_gmap_marker_bg = $options['qt_gmap_marker_bg'];
?>
.qt_google_map .pb_googlemap_custom-overlay-inner { background:<?php echo esc_attr($qt_gmap_marker_bg); ?>; color:<?php echo esc_attr($options['qt_gmap_marker_color']); ?>; }
.qt_google_map .pb_googlemap_custom-overlay-inner::after { border-color:<?php echo esc_attr($qt_gmap_marker_bg); ?> transparent transparent transparent; }
<?php
  // tcd_head_css action
  do_action( 'tcd_head_css' );
?>
</style>

<?php /* URLやモバイル等でcssが変わるものはここで出力 ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■ */ ?>
<style id="current-page-style" type="text/css">
<?php
     // トップページ -----------------------------------------------------------------------------
     if(is_front_page()){

       // ヘッダースライダー
       if($options['index_slider']){
         $i = 1;
         foreach ( $options['index_slider'] as $key => $value ) :
           $overlay_color = hex2rgb($value['overlay_color']);
           $overlay_opacity = $value['overlay_opacity'];
           $overlay_color = implode(",",$overlay_color);
?>
#header_slider .item<?php echo $i; ?> .overlay { background-color:rgba(<?php echo esc_attr($overlay_color); ?>,<?php echo esc_attr($overlay_opacity); ?>); }
#header_slider .item<?php echo $i; ?> .catch { font-size:<?php echo esc_attr($value['catch_font_size']); ?>px !important; }
@media screen and (max-width:1221px) {
  #header_slider .item<?php echo $i; ?> .catch { font-size:<?php echo esc_attr( floor( (( int )$value['catch_font_size'] + ( int )$value['catch_font_size_mobile']) / 2 )); ?>px !important; }
}
@media screen and (max-width:800px) {
  #header_slider .item<?php echo $i; ?> .catch { font-size:<?php echo esc_attr($value['catch_font_size_mobile']); ?>px !important; }
}
<?php
         $i++;
         endforeach;
       };

     // 診療 -----------------------------------------------------------------------------
     } elseif(is_post_type_archive('treatment') || is_tax('treatment_category')) {
       $overlay_color = hex2rgb($options['archive_treatment_header_overlay_color']);
       $overlay_opacity = $options['archive_treatment_header_overlay_opacity'];
       $overlay_color = implode(",",$overlay_color);
?>
#page_header .overlay { background-color:rgba(<?php echo esc_attr($overlay_color); ?>,<?php echo esc_attr($overlay_opacity); ?>); }
<?php
     // 治療詳細ページ -----------------------------------------------------------------------------
     } elseif(is_singular('treatment')) {
       $overlay_color = hex2rgb($options['archive_treatment_header_overlay_color']);
       $overlay_opacity = $options['archive_treatment_header_overlay_opacity'];
       $overlay_color = implode(",",$overlay_color);
?>
#page_header_small .overlay { background-color:rgba(<?php echo esc_attr($overlay_color); ?>,<?php echo esc_attr($overlay_opacity); ?>); }
<?php
     // お知らせアーカイブ -----------------------------------------------------------------------------
     } elseif(is_post_type_archive('news') || is_tax('news_category')) {
       $overlay_color = hex2rgb($options['archive_news_header_overlay_color']);
       $overlay_opacity = $options['archive_news_header_overlay_opacity'];
       $overlay_color = implode(",",$overlay_color);
?>
#page_header .overlay { background-color:rgba(<?php echo esc_attr($overlay_color); ?>,<?php echo esc_attr($overlay_opacity); ?>); }
<?php
     // お知らせ詳細ページ -----------------------------------------------------------------------------
     } elseif(is_singular('news')) {
?>
#single_post_title .title { font-size:<?php echo esc_attr($options['single_title_font_size']); ?>px; }
@media screen and (max-width:800px) {
  #single_post_title .title { font-size:<?php echo esc_attr($options['single_title_font_size_sp']); ?>px; }
}
<?php
     // ブログアーカイブ -----------------------------------------------------------------------------
     } elseif(is_archive() || is_home() || is_search()) {
       $overlay_color = hex2rgb($options['archive_blog_header_overlay_color']);
       $overlay_opacity = $options['archive_blog_header_overlay_opacity'];
       $overlay_color = implode(",",$overlay_color);
?>
#page_header .overlay { background-color:rgba(<?php echo esc_attr($overlay_color); ?>,<?php echo esc_attr($overlay_opacity); ?>); }
<?php
     // ブログ詳細ページ -----------------------------------------------------------------------------
     } elseif(is_single()){
?>
#single_post_title .title { font-size:<?php echo esc_attr($options['single_title_font_size']); ?>px; }
@media screen and (max-width:800px) {
  #single_post_title .title { font-size:<?php echo esc_attr($options['single_title_font_size_sp']); ?>px; }
}
<?php
     // 固定ページ --------------------------------------------------------------------
     } elseif(is_page()) {

       $overlay_opacity = get_post_meta($post->ID, 'overlay_color_opacity', true) ?  get_post_meta($post->ID, 'overlay_color_opacity', true) : '0.3';
       if($overlay_opacity == 'zero'){
         $overlay_opacity = '0';
       }
       if($overlay_opacity != '0'){
         $overlay_color = get_post_meta($post->ID, 'overlay_color', true) ?  get_post_meta($post->ID, 'overlay_color', true) : '#000000';
         $overlay_color = hex2rgb($overlay_color);
         $overlay_color = implode(",",$overlay_color);
?>
#page_header .overlay { background-color:rgba(<?php echo esc_attr($overlay_color); ?>,<?php echo esc_attr($overlay_opacity); ?>); }
<?php
       };
       if(is_page_template('page-tcd-lp.php')) {
         $page_catch_font_size = get_post_meta($post->ID, 'page_catch_font_size', true) ?  get_post_meta($post->ID, 'page_catch_font_size', true) : '32';
         $page_catch_font_size_sp = get_post_meta($post->ID, 'page_catch_font_size_sp', true) ?  get_post_meta($post->ID, 'page_catch_font_size_sp', true) : '20';
?>
#page_header .catch { font-size:<?php echo esc_attr($page_catch_font_size); ?>px !important; }
@media screen and (max-width:1221px) {
  #page_header .catch { font-size:<?php echo esc_html(floor( (( int )$page_catch_font_size + ( int )$page_catch_font_size_sp) / 2 ) ); ?>px !important; }
}
@media screen and (max-width:800px) {
  #page_header .catch { font-size:<?php echo esc_attr($page_catch_font_size_sp); ?>px !important; }
}
<?php
       };

     // 404ページ -----------------------------------------------------------------------------
     } elseif( is_404()) {

       if($options['page_404_overlay_opacity'] != 0) {
         $overlay_color = hex2rgb($options['page_404_overlay_color']);
         $overlay_opacity = $options['page_404_overlay_opacity'];
         $overlay_color = implode(",",$overlay_color);
?>
#page_404_header .overlay { background-color:rgba(<?php echo esc_attr($overlay_color); ?>,<?php echo esc_attr($overlay_opacity); ?>); }
<?php
       }; // END overlay

     }; //END page setting

     // ロード画面 -----------------------------------------
     if( $options['show_loading'] || ($options['show_splash'] && !isset($_COOKIE['splash_screen']) && $options['splash_display_time'] == 'type1') || ($options['show_splash'] && $options['splash_display_time'] == 'type2') ){
       get_template_part('functions/loader_css');
     };
     
    // カスタムCSS --------------------------------------------
    if($options['css_code']) {
      echo  " \n\n /*** Theme Options Custom CSS ***/ \n\n";
      echo $options['css_code'];
    };
    if(is_single() || is_page()) {
      $custom_css = get_post_meta($post->ID, 'custom_css', true);
      if($custom_css) {
        echo  " \n\n /** Page Custom CSS**/ \n\n";
        echo $custom_css;
      };
    }

  // tcd_head_css_current_page action
  do_action( 'tcd_head_css_current_page' );
?>
</style>

<?php
     // JavaScriptの設定はここから　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■

     // トップページ
     if(is_front_page()) {

       // ヘッダースライダー
       if($options['index_slider']){
         wp_enqueue_style('slick-style', get_template_directory_uri() . '/js/slick.css', '', '1.0.0');
         wp_enqueue_script('slick-script', get_template_directory_uri() . '/js/slick.min.js', '', '1.0.0', true);
         if( $options['show_loading'] || ($options['show_splash'] && !isset($_COOKIE['splash_screen']) && $options['splash_display_time'] == 'type1') || ($options['show_splash'] && $options['splash_display_time'] == 'type2') ){ } else {
?>
<script type="text/javascript">
jQuery(document).ready(function($){
<?php get_template_part('functions/slider_ini'); ?>
});
</script>
<?php
         };
       };

       // コンテンツビルダー -------------------------------------------------------------------------------------------------------------
       if ( $options['contents_builder'] ) {

         $contents_builder = $options['contents_builder'];
         if ($contents_builder) :
?>
<script type="text/javascript">
jQuery(document).ready(function($){
<?php
           $content_count = 1;
           $carousel = true;
           foreach($contents_builder as $content) :
             // カルーセル ---------------------------------------------------------
             if ( $content['cb_content_select'] == 'carousel' && $content['show_content'] && ($carousel == true) ) {
               $carousel = false;
               $post_num = '12';
               $args = array( 'post_type' => 'treatment', 'showposts'=> $post_num, 'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC') );
               $post_list = new wp_query($args);
               if($post_list->have_posts()):
                 $num_post = $post_list->post_count;
               endif;
             };
           $content_count++;
           endforeach;
?>
});
</script>
<?php
         endif;
       };// END コンテンツビルダーここまで

     }; // トップページここまで

     // ヘッダーメッセージ
     if(!is_404()){
       if( (is_front_page() && $options['show_header_message'] == 'display') || (!is_page() && $options['show_header_message'] == 'display') || (is_page() && !is_page_template('page-tcd-lp.php') && $options['show_header_message'] == 'display') || (is_page() && is_page_template('page-tcd-lp.php') && (get_post_meta($post->ID, 'hide_header_message', true)) == 'no') ) {
?>
<script type="text/javascript">
jQuery(document).ready(function($){
  function adjust_header_position(){
    var header_message_height = $("#header_message").innerHeight();
    var header_height = 0;
    if($('#header').length){
      header_height = $("#header").innerHeight();
    }
    $('#header, #header_logo').css('top', header_message_height);
    $('#side_icon_button').css('top', header_message_height + header_height);
    $('body').css('padding-top', header_message_height + header_height);
  }
  adjust_header_position();
  $(window).on('resize', function(){
    adjust_header_position();
  });
});
</script>
<?php
       };
     };

     // カスタムスクリプト--------------------------------------------
     if($options['script_code']) {
       echo $options['script_code'];
     };
     if(is_single() || is_page()) {
       $custom_script = get_post_meta($post->ID, 'custom_script', true);
       if($custom_script) {
         echo $custom_script;
       };
     };
?>

<?php
     }; // END function tcd_head()
     add_action("wp_head", "tcd_head");
?>