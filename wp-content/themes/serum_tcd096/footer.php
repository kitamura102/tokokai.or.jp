<?php $options = get_design_plus_option(); ?>

 <?php
      if(is_page()){ 
        $page_hide_footer = get_post_meta($post->ID, 'page_hide_footer', true) ?  get_post_meta($post->ID, 'page_hide_footer', true) : 'no';
        $hide_page_header = get_post_meta($post->ID, 'hide_page_header', true) ?  get_post_meta($post->ID, 'hide_page_header', true) : 'no';
        $hide_page_side_bar = get_post_meta($post->ID, 'hide_page_side_bar', true) ?  get_post_meta($post->ID, 'hide_page_side_bar', true) : 'no';
      } else {
        $page_hide_footer = 'no';
        $hide_page_header = 'no';
        $hide_page_side_bar = 'no';
      }

      if($page_hide_footer != 'yes'){
 ?>

 <?php
      // 画像カルーセル --------------------------------------------------
      if( $options['show_image_carousel'] && $options['image_carousel']){
        $item_count = count($options['image_carousel']);
 ?>
 <div id="footer_image_carousel" class="splide<?php if($item_count <= 4){ echo ' no_slide'; }; ?>">
  <div class="splide__track">
   <div class="splide__list">
    <?php
         foreach ( $options['image_carousel'] as $key => $value ) :
           $image = $value['image'] ? wp_get_attachment_image_src( $value['image'], 'full' ) : '';
           if($image){
    ?>
    <div class="splide__slide item">
     <img loading="lazy" src="<?php echo esc_attr($image[0]); ?>" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>" alt="">
    </div>
    <?php }; endforeach; ?>
   </div><!-- END .splide__list -->
  </div><!-- END .splide__track -->
 </div><!-- END .splide -->
 <?php
      };
 ?>

 <?php
      // アイコンバナー --------------------------------------------------
      if( $options['show_footer_icon_banner'] && $options['footer_icon_banner']){
 ?>
 <div id="footer_icon_banner" class="icon_button">
  <?php
       foreach ( $options['footer_icon_banner'] as $key => $value ) :
         $title = $value['title'];
         $url = $value['url'];
         $icon = $value['icon'];
         $target = $value['target'];
         $material_icon = isset($value['material_icon']) ? $value['material_icon'] : NULL;
  ?>
  <div class="item">
   <a class="<?php echo esc_attr($icon); ?>" href="<?php echo esc_url($url); ?>"<?php if($target){ echo ' target="_blank"'; }; ?>><?php if($icon == 'material_icon' && $material_icon){ ?><span class="google_icon">&#x<?php echo esc_attr($material_icon); ?>;</span><?php }; ?><span><?php echo wp_kses_post($title); ?></span></a>
  </div>
  <?php endforeach; ?>
 </div>
 <?php
      };
 ?>

 <?php // ロゴエリア -------------------------------------------------- ?>
 <footer id="footer">

  <div id="footer_top_wrap">
   <div id="footer_top"<?php if(has_nav_menu('footer-menu-mobile')){ echo ' class="has_mobile_menu"'; }; ?>>
    <?php
         // ロゴ
         footer_logo();

         // メニュー
         if (has_nav_menu('footer-menu1') || has_nav_menu('footer-menu2') || has_nav_menu('footer-menu3') || has_nav_menu('footer-menu4')) {
    ?>
    <?php if(has_nav_menu('footer-menu1')){ wp_nav_menu( array( 'sort_column' => 'menu_order', 'theme_location' => 'footer-menu1' , 'depth' => '1', 'container' => 'div', 'container_class' => 'footer_menu' ) ); }; ?>
    <?php if(has_nav_menu('footer-menu2')){ wp_nav_menu( array( 'sort_column' => 'menu_order', 'theme_location' => 'footer-menu2' , 'depth' => '1', 'container' => 'div', 'container_class' => 'footer_menu' ) ); }; ?>
    <?php if(has_nav_menu('footer-menu3')){ wp_nav_menu( array( 'sort_column' => 'menu_order', 'theme_location' => 'footer-menu3' , 'depth' => '1', 'container' => 'div', 'container_class' => 'footer_menu' ) ); }; ?>
    <?php if(has_nav_menu('footer-menu4')){ wp_nav_menu( array( 'sort_column' => 'menu_order', 'theme_location' => 'footer-menu4' , 'depth' => '1', 'container' => 'div', 'container_class' => 'footer_menu' ) ); }; ?>
    <?php if(has_nav_menu('footer-menu-mobile')){ wp_nav_menu( array( 'sort_column' => 'menu_order', 'theme_location' => 'footer-menu-mobile' , 'depth' => '1', 'container' => 'div', 'container_class' => 'footer_menu footer_menu_mobile' ) ); }; ?>
    <?php }; ?>
   </div>
  </div>

  <?php
       // 住所
       if($options['footer_address']){
  ?>
  <p id="footer_address"><?php echo wp_kses_post(sepLine($options['footer_address'])); ?></p>
  <?php }; ?>

  <?php
       // SNSボタン ------------------------------------
       if($options['show_sns_footer'] == 'display') {
         $facebook = $options['sns_button_facebook_url'];
         $twitter = $options['sns_button_twitter_url'];
         $insta = $options['sns_button_instagram_url'];
         $pinterest = $options['sns_button_pinterest_url'];
         $youtube = $options['sns_button_youtube_url'];
         $contact = $options['sns_button_contact_url'];
         $show_rss = $options['sns_button_show_rss'];
         $tiktok = $options['sns_button_tiktok_url'];
         $line = $options['sns_button_line_url'];
         $note = $options['sns_button_note_url'];
  ?>
  <ul id="footer_sns" class="sns_button_list clearfix color_<?php echo esc_attr($options['sns_button_color_type']); ?>">
   <?php if($line) { ?><li class="line"><a href="<?php echo esc_url($line); ?>" rel="nofollow noopener" target="_blank" title="LINE"><span>LINE</span></a></li><?php }; ?>
   <?php if($insta) { ?><li class="insta"><a href="<?php echo esc_url($insta); ?>" rel="nofollow noopener" target="_blank" title="Instagram"><span>Instagram</span></a></li><?php }; ?>
   <?php if($tiktok) { ?><li class="tiktok"><a href="<?php echo esc_url($tiktok); ?>" rel="nofollow noopener" target="_blank" title="TikTok"><span>TikTok</span></a></li><?php }; ?>
   <?php if($twitter) { ?><li class="twitter"><a href="<?php echo esc_url($twitter); ?>" rel="nofollow noopener" target="_blank" title="X"><span>X</span></a></li><?php }; ?>
   <?php if($facebook) { ?><li class="facebook"><a href="<?php echo esc_url($facebook); ?>" rel="nofollow noopener" target="_blank" title="Facebook"><span>Facebook</span></a></li><?php }; ?>
   <?php if($pinterest) { ?><li class="pinterest"><a href="<?php echo esc_url($pinterest); ?>" rel="nofollow noopener" target="_blank" title="Pinterest"><span>Pinterest</span></a></li><?php }; ?>
   <?php if($youtube) { ?><li class="youtube"><a href="<?php echo esc_url($youtube); ?>" rel="nofollow noopener" target="_blank" title="Youtube"><span>Youtube</span></a></li><?php }; ?>
   <?php if($note) { ?><li class="note"><a href="<?php echo esc_url($note); ?>" rel="nofollow noopener" target="_blank" title="note"><span>note</span></a></li><?php }; ?>
   <?php if($contact) { ?><li class="contact"><a href="<?php echo esc_url($contact); ?>" rel="nofollow noopener" target="_blank" title="Contact"><span>Contact</span></a></li><?php }; ?>
   <?php if($show_rss) { ?><li class="rss"><a href="<?php echo esc_url(get_bloginfo('rss2_url')); ?>" rel="nofollow noopener" target="_blank" title="RSS"><span>RSS</span></a></li><?php }; ?>
  </ul>
  <?php }; ?>

  <?php // コピーライト ?>
  <p id="copyright"><?php echo wp_kses_post($options['copyright']); ?></p>

 </footer>

 <?php
      }; // hide footer
 ?>

 <?php if( $hide_page_header != 'yes' && $hide_page_side_bar != 'yes' ){ ?>
 <div id="return_top">
  <a class="no_auto_scroll" href="#body"><span>PAGE TOP</span></a>
 </div>
 <?php }; ?>

</div><!-- #container -->

<?php
     // ドロワーメニュー --------------------------------------------
     if (has_nav_menu('global-menu')) {
?>
<div id="drawer_menu">

 <div id="drawer_logo">
  <?php drawer_logo(); ?>
 </div>

 <div class="close_button_area">
  <div class="close_button"></div>
 </div>

 <?php
      // 検索フォーム --------------------------------------------------------------------
      if( $options['show_header_search'] == 'display') {
 ?>
 <div id="drawer_menu_search">
  <form role="search" method="get" action="<?php echo esc_url(home_url()); ?>">
   <div class="input_area"><input type="text" value="" name="s" autocomplete="off"></div>
   <div class="button_area"><label for="drawer_menu_search_button"></label><input id="drawer_menu_search_button" type="submit" value=""></div>
  </form>
 </div>
 <?php }; ?>

 <?php
     // アイコンボタン --------------------------------------------------
     if( $options['show_side_icon_button'] && $options['side_icon_button']){
 ?>
 <div id="drawer_icon_button" class="icon_button">
  <?php
       foreach ( $options['side_icon_button'] as $key => $value ) :
         $title = $value['title'];
         $url = $value['url'];
         $icon = $value['icon'];
         $target = $value['target'];
         $material_icon = isset($value['material_icon']) ? $value['material_icon'] : NULL;
  ?>
  <div class="item<?php if($icon == 'no_icon'){ echo ' long'; }; ?>">
   <a class="<?php echo esc_attr($icon); ?>" href="<?php echo esc_url($url); ?>"<?php if($target){ echo ' target="_blank"'; }; ?>><?php if($icon == 'material_icon' && $material_icon){ ?><span class="google_icon">&#x<?php echo esc_attr($material_icon); ?>;</span><?php }; ?><span><?php echo wp_kses_post($title); ?></span></a>
  </div>
  <?php endforeach; ?>
 </div>
 <?php
      };
 ?>

 <?php // メニュー -------------------  ?>
 <nav id="mobile_menu">
  <?php wp_nav_menu( array( 'sort_column' => 'menu_order', 'theme_location' => 'global-menu' , 'container' => '' ) ); ?>
 </nav>

 <?php
      // 言語ボタン ------------------------------------
      if($options['show_lang_button'] && $options['lang_button']) {
 ?>
 <ul id="drawer_lang_button">
  <?php foreach ( $options['lang_button'] as $key => $value ) : ?>
  <li<?php if($value['active_button']){ echo ' class="active"'; }; ?>><a href="<?php if($value['url']) { echo esc_url($value['url']); }; ?>" target="_blank"><?php if($value['name']) { echo esc_html($value['name']); }; ?></a></li>
  <?php endforeach; ?>
 </ul>
 <?php }; ?>

</div>
<?php }; ?>

<?php
     // フッターバー ----------------------------------------------------------
     do_action( 'tcd_footer_after', $options );
?>
<?php
     // share button ----------------------------------------------------------------------
     if ( is_single() && ( $options['single_blog_show_sns_top'] == 'display' || $options['single_blog_show_sns_btm'] == 'display' || $options['single_news_show_sns_top'] == 'display' || $options['single_news_show_sns_btm'] == 'display') ) :
       if ( $options['sns_share_design_type'] == 'type5') :
         if ( $options['show_sns_share_twitter'] ) :
?>
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
<?php
         endif;
         if ( $options['show_sns_share_fblike'] || $options['show_sns_share_fbshare'] ) :
?>
<div id="fb-root"></div>
<script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.5"; fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk'));</script>
<?php
         endif;
         if ( $options['show_sns_share_hatena'] ) :
?>
<script type="text/javascript" src="//b.st-hatena.com/js/bookmark_button.js" charset="utf-8" async="async"></script>
<?php
         endif;
         if ( $options['show_sns_share_pocket'] ) :
?>
<script type="text/javascript">!function(d,i){if(!d.getElementById(i)){var j=d.createElement("script");j.id=i;j.src="https://widgets.getpocket.com/v1/j/btn.js?v=1";var w=d.getElementById(i);d.body.appendChild(j);}}(document,"pocket-btn-js");</script>
<?php
         endif;
         if ( $options['show_sns_share_pinterest'] ) :
?>
<script async defer src="//assets.pinterest.com/js/pinit.js"></script>
<?php
         endif;
         if ( $options['show_sns_share_line'] ) :
?>
<script src="https://www.line-website.com/social-plugins/js/thirdparty/loader.min.js" async="async" defer="defer"></script>
<?php
         endif;
       endif;
     endif;
?>

<?php wp_footer(); ?>
<?php
     // load script -----------------------------------------------------------
     if( $options['show_loading'] || ($options['show_splash'] && !isset($_COOKIE['splash_screen']) && $options['splash_display_time'] == 'type1') || ($options['show_splash'] && $options['splash_display_time'] == 'type2') ){
       show_loading_screen();
     } else {
       no_loading_screen();
     };

     // カスタムスクリプト--------------------------------------------
     if($options['footer_script_code']) {
       echo $options['footer_script_code'];
     };
     if(is_single() || is_page()) {
       global $post;
       $footer_custom_script = get_post_meta($post->ID, 'footer_custom_script', true);
       if($footer_custom_script) {
         echo $footer_custom_script;
       };
     };
?>
</body>
</html>