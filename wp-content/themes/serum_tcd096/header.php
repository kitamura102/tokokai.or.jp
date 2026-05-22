<?php $options = get_design_plus_option(); ?>
<!DOCTYPE html>
<html class="pc" <?php language_attributes(); ?>>
<?php if($options['use_ogp']) { ?>
<head prefix="og: https://ogp.me/ns# fb: https://ogp.me/ns/fb#">
<?php } else { ?>
<head>
<?php }; ?>
<meta charset="<?php bloginfo('charset'); ?>">
<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
<meta name="viewport" content="width=device-width">
<meta name="description" content="<?php echo get_seo_description(); ?>">
<?php if(is_attachment() && (get_option( 'blog_public' ) != 0)) { ?>
<meta name='robots' content='noindex, nofollow' />
<?php }; ?>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
<?php wp_head(); ?>
</head>
<body id="body" <?php body_class(); ?>>
<div id="js-body-start"></div>

<?php
     // ロード画面 --------------------------------------------------------------------
     if( $options['show_loading'] || ($options['show_splash'] && !isset($_COOKIE['splash_screen']) && $options['splash_display_time'] == 'type1') || ($options['show_splash'] && $options['splash_display_time'] == 'type2') ){
       load_icon();
     };


     // メッセージ --------------------------------------------------------------------
     if(!is_404()){
       if( (is_front_page() && $options['show_header_message'] == 'display') || (!is_page() && $options['show_header_message'] == 'display') || (is_page() && !is_page_template('page-tcd-lp.php') && $options['show_header_message'] == 'display') || (is_page() && is_page_template('page-tcd-lp.php') && (get_post_meta($post->ID, 'hide_header_message', true)) == 'no') ) {
         $message = $options['header_message'];
         $url = $options['header_message_url'];
         $target = $options['header_message_url_target'];
         $font_color = $options['header_message_font_color'];
         $bg_color = $options['header_message_bg_color'];
         if($message){
?>
<div id="header_message" style="color:<?php esc_attr_e($font_color); ?>;background-color:<?php esc_attr_e($bg_color); ?>;">
  <?php if($url){ ?>
  <a href="<?php echo esc_url($url); ?>" class="label"<?php if($target){ echo ' target="_blank" rel="nofollow noopener"'; }; ?>><?php echo wp_kses_post(nl2br($message)); ?></a>
  <?php }else{ ?>
  <p class="label"><?php echo wp_kses_post(nl2br($message)); ?></p>
  <?php } ?>
</div>
<?php
         };
       };
     };
?>

<?php
      if(is_page()){ 
        $hide_page_header = get_post_meta($post->ID, 'hide_page_header', true) ?  get_post_meta($post->ID, 'hide_page_header', true) : 'no';
        $hide_page_header_bar = get_post_meta($post->ID, 'hide_page_header_bar', true) ?  get_post_meta($post->ID, 'hide_page_header_bar', true) : 'no';
        $hide_page_side_bar = get_post_meta($post->ID, 'hide_page_side_bar', true) ?  get_post_meta($post->ID, 'hide_page_side_bar', true) : 'no';
      } else {
        $hide_page_header = 'no';
        $hide_page_header_bar = 'no';
        $hide_page_side_bar = 'no';
      }

     // ヘッダー ----------------------------------------------------------------------
?>
<?php header_logo(); ?>
<?php if( $hide_page_header != 'yes' && $hide_page_header_bar != 'yes' ) { ?>
<header id="header">
 <?php header_logo2(); ?>
 <?php
      // グローバルメニュー ----------------------------------------------------------------
      if ( has_nav_menu('global-menu') ) {
 ?>
 <a id="drawer_menu_button" href="#"><span></span><span></span><span></span></a>
 <nav id="global_menu">
  <?php wp_nav_menu( array( 'sort_column' => 'menu_order', 'theme_location' => 'global-menu' , 'container' => '' ) ); ?>
 </nav>
 <?php }; ?>
 <?php
      // 検索フォーム --------------------------------------------------------------------
      if( $options['show_header_search'] == 'display') {
 ?>
 <div id="header_search">
  <div id="header_search_button"></div>
  <form role="search" method="get" id="header_searchform" action="<?php echo esc_url(home_url()); ?>">
   <div class="input_area"><input type="text" value="" id="header_search_input" name="s" autocomplete="off"></div>
   <div class="button"><label for="header_search_button"></label><input type="submit" id="header_search_button" value=""></div>
  </form>
 </div>
 <?php }; ?>
 <?php
      // 言語ボタン ------------------------------------
      if($options['show_lang_button'] && $options['lang_button']) {
 ?>
 <ul id="header_lang_button">
  <?php foreach ( $options['lang_button'] as $key => $value ) : ?>
  <li<?php if($value['active_button']){ echo ' class="active"'; }; ?>><a href="<?php if($value['url']) { echo esc_url($value['url']); }; ?>" target="_blank"><?php if($value['name']) { echo esc_html($value['name']); }; ?></a></li>
  <?php endforeach; ?>
 </ul>
 <?php }; ?>
 <?php get_template_part( 'template-parts/megamenu' ); ?>
</header>
<?php }; ?>

<?php
     if( $hide_page_header != 'yes' && $hide_page_side_bar != 'yes' ) {

     // サイトの説明文 ------------------------------------
     $site_description = get_bloginfo( 'description', 'display' );
     if($site_description){
?>
<div id="site_desc">
 <p class="desc"><?php echo esc_html($site_description); ?></p>
</div>
<?php
     };

     // アイコンボタン --------------------------------------------------
     if( $options['show_side_icon_button'] && $options['side_icon_button']){
 ?>
 <div id="side_icon_button" class="icon_button">
  <?php
       foreach ( $options['side_icon_button'] as $key => $value ) :
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
      }; // hide sidebar
 ?>

<div id="container">

 <?php
      //  トップページ -------------------------------------------------------------------------
      if(is_front_page()) {

        $index_slider = $options['index_slider'];

        //  ヘッダースライダー -------------------------------------------------------------------------
        if($index_slider){
 ?>
 <div id="header_slider_wrap">
  <div id="header_slider">

   <?php
        // スライダーのアイテム --------------------------------
        $i = 1;
        $slider_item_total = count($index_slider);
        foreach ( $index_slider as $key => $value ) :
          $item_type = $value['slider_type'];
          $image = isset($value['image']) ? wp_get_attachment_image_src( $value['image'], 'full' ) : '';
          $image_mobile = isset($value['image_mobile']) ? wp_get_attachment_image_src( $value['image_mobile'], 'full' ) : '';
          $video = $value['video'];
          $youtube_url = $value['youtube'];
          $url = $value['url'];
          $target = $value['target'];
   ?>
   <div class="item <?php if( ($item_type == 'type2') && $video && auto_play_movie() ) { echo 'video'; } elseif( ($item_type == 'type3') && $youtube_url && auto_play_movie() ) { echo 'youtube'; } else { echo 'image_item'; }; ?> item<?php echo $i; ?> <?php if($i == 1){ echo 'first_item'; }; ?> slick-slide">

    <?php if(!empty($url)){ ?>
    <a href="<?php echo esc_url($url); ?>"<?php if(!empty($target)){ echo ' target="_blank"'; }; ?>>
    <?php }; ?>

    <?php if(!empty($value['catch'])){ ?>
    <h2 class="catch animate_item <?php if($i == 1){ echo 'first_animate_item'; }; ?> common_catch direction_<?php echo esc_attr($value['catch_font_direction']); ?>"><?php echo wp_kses_post(sepLine($value['catch'])); ?></h2>
    <?php }; ?>

    <div class="overlay"></div>

    <?php if( ($item_type == 'type2') && $video && auto_play_movie() ) { ?>
    <video preload="auto" muted playsinline <?php if($slider_item_total == 1) { echo "loop"; }; ?>>
     <source src="<?php echo esc_url(wp_get_attachment_url($video)); ?>" type="video/mp4" />
    </video>
    <?php
         } elseif( ($item_type == 'type3') && $youtube_url && auto_play_movie() ) {
           if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[\w\-?&!#=,;]+/[\w\-?&!#=/,;]+/|(?:v|e(?:mbed)?)/|[\w\-?&!#=,;]*[?&]v=)|youtu\.be/)([\w-]{11})(?:[^\w-]|\Z)%i', $youtube_url, $matches)) {
    ?>
    <div class="youtube_wrap">
     <div class="youtube_inner">
      <iframe id="youtube-player-<?php echo $i; ?>" class="youtube-player slide-youtube" src="https://www.youtube.com/embed/<?php echo esc_attr($matches[1]); ?>?enablejsapi=1&controls=0&fs=0&iv_load_policy=3&rel=0&showinfo=0&<?php if($slider_item_total > 1) { echo "loop=0"; } else { echo "playlist=" . esc_attr($matches[1]); }; ?>&playsinline=1" frameborder="0"></iframe>
     </div>
    </div>
    <?php
           };
         } else {
    ?>
    <?php if($image) { ?>
    <div class="bg_image">
     <picture>
      <?php if($image_mobile) { ?>
      <source media="(max-width: 800px)" srcset="<?php echo esc_attr($image_mobile[0]); ?>">
      <?php }; ?>
      <img src="<?php echo esc_attr($image[0]); ?>" alt="" width="<?php echo esc_attr($image[1]); ?>" height="<?php echo esc_attr($image[2]); ?>">
     </picture>
    </div>
    <?php
           };
         };
    ?>

    <?php if(!empty($url)){ ?>
    </a>
    <?php }; ?>

   </div><!-- END .item -->
   <?php
        $i++;
        endforeach;
   ?>
  </div><!-- END #header_slider -->

  <?php
       // ニュースティッカー
       if($options['show_index_news']){
        $post_num = 5;
        $post_type = $options['index_news_post_type'];
        if(!$options['use_news']){
          $post_type = 'post';
        }
        $post_order = $options['index_news_post_order'];
        $args = array( 'post_type' => $post_type, 'posts_per_page' => $post_num, 'orderby' => $post_order );
        $post_list = new wp_query($args);
        if($post_list->have_posts()):
  ?>
  <div class="splide" id="news_ticker">
   <div class="splide__track">
    <div class="splide__list">
     <?php while( $post_list->have_posts() ) : $post_list->the_post(); ?>
     <div class="splide__slide news_item">
      <a href="<?php the_permalink(); ?>">
       <time class="date entry-date published" datetime="<?php the_modified_time('c'); ?>"><?php the_time('Y.m.d'); ?></time>
       <p class="title"><?php the_title_attribute(); ?></p>
      </a>
     </div>
     <?php endwhile; wp_reset_query(); ?>
    </div>
   </div>
  </div>
  <?php endif; }; ?>

 </div><!-- END #header_slider_wrap -->
 <?php
        }; // END display index slider
      }; // END front page
 ?>

