<?php
     // display loading screen -----------------------------------------------------------------
     function show_loading_screen(){
       $options = get_design_plus_option();
?>
<script>

jQuery(window).bind("pageshow", function(event) {
  if (event.originalEvent.persisted) {
    window.location.reload()
  }
});

<?php
     if(is_front_page()) {
       if($options['show_index_news'] == '1'){
?>
if( jQuery('#news_ticker').length ){
  var newsticker_splide = new Splide( '#news_ticker', {
    type   : 'fade',
    rewind   : true,
    speed: 2000,
    interval: 5000,
    autoplay: true,
    perPage: 1,
    perMove: 1,
    arrows: false,
    pagination: false,
  } );
  newsticker_splide.mount();
}
<?php }; ?>

<?php
     if ( $options['contents_builder'] ) {
       $content_count = 1;
       $contents_builder = $options['contents_builder'];
       if ($contents_builder) :
         foreach($contents_builder as $content) :
           if ( $content['cb_content_select'] == 'carousel' && $content['show_content'] ) {
             $layout = $content['layout'];
             if($layout == 'type1'){
?>
if( jQuery('.num<?php echo $content_count; ?> .index_carousel').length ){
  var index_splide = new Splide( '.num<?php echo $content_count; ?> .index_carousel', {
    type   : 'loop',
    autoplay: true,
    pauseOnFocus: false,
    interval: 7000,
    perPage: 2,
    perMove: 1,
    autoHeight: true,
    pagination: false,
  } );
  index_splide.mount();
}
<?php        } else { ?>
if( jQuery('.num<?php echo $content_count; ?> .index_carousel').length ){
  var index_splide = new Splide( '.num<?php echo $content_count; ?> .index_carousel', {
    type   : 'loop',
    autoplay: true,
    pauseOnFocus: false,
    interval: 7000,
    perPage: 3,
    breakpoints: {
      1000: {
        perPage: 2,
      },
    },
    perMove: 1,
    autoHeight: true,
    pagination: false,
  } );
  index_splide.mount();
}
<?php
             }
           };
           $content_count++;
         endforeach;
       endif;
     };
?>
<?php }; // is front page?>

<?php
     global $post;
     if(is_page()){ 
       $page_hide_footer = get_post_meta($post->ID, 'page_hide_footer', true) ?  get_post_meta($post->ID, 'page_hide_footer', true) : 'no';
     } else {
       $page_hide_footer = 'no';
     }
     if( ($page_hide_footer != 'yes') && $options['show_image_carousel'] && $options['image_carousel']){
       $item_count = count($options['image_carousel']);
       if($item_count > 4){
?>
if( jQuery('#footer_image_carousel').length ){
  var footer_splide = new Splide( '#footer_image_carousel', {
    type   : 'loop',
    drag   : 'free',
    focus  : 'center',
    autoWidth: true,
    autoHeight: true,
    perPage: 3,
    arrows: false,
    pagination: false,
    autoScroll: {
      speed: 0.5,
    },
  } );
  footer_splide.mount(window.splide.Extensions);
}
<?php
       };
     };
?>

function after_load() {
  (function($) {

  $('body').addClass('end_loading');

  <?php
       // front page -----------------------------------
       if(is_front_page()) {
         get_template_part('functions/slider_ini');
         if( ($options['show_splash'] && !isset($_COOKIE['splash_screen']) && $options['splash_display_time'] == 'type1') || ($options['show_splash'] && $options['splash_display_time'] == 'type2') ){
  ?>
  $.cookie('splash_screen', 'displayed', {
    path:'/'
  });
  <?php
         };
       };
  ?>

  <?php if(!is_front_page() || (is_front_page() && count($options['index_slider'])<1)) { ?>
  <?php if(is_front_page()){ ?>
  $('#header_logo, #news_ticker, #site_desc, #drawer_menu_button').addClass('animate');
  <?php } ?>
  <?php  if($options['header_logo_type'] == 'type2' && empty($options['header_logo_image'])){ ?>
  $('#header_logo2').addClass('animate');
  <?php } else { ?>
  $('#header_logo').addClass('animate');
  <?php }; ?>
  $('#drawer_menu_button').addClass('animate');
  setTimeout(function(){
    $("#page_header .catch span").each(function(i){
      $(this).delay(i *400).queue(function(next) {
        $(this).addClass('animate');
        next();
      });
    });
  },900);
  <?php }; ?>

  <?php if(is_single()) { ?>
  $('#header_logo2, #single_post_title .title, #single_post_category').addClass('animate');
  <?php }; ?>

  <?php if(is_404()){ ?>
  $('#page_404_header').addClass('animate');
  <?php }; ?>
  <?php if( (is_search() && isset($_GET['s']) && empty($_GET['s'])) || (is_search() && !have_posts()) ){ ?>
  $('#page_404_header').addClass('animate');
  <?php }; ?>

  })( jQuery );
}

jQuery(function($){

  <?php if( (is_front_page() && $options['show_splash'] && !isset($_COOKIE['splash_screen']) && $options['splash_display_time'] == 'type1') || (is_front_page() && $options['show_splash'] && $options['splash_display_time'] == 'type2') ){ ?>
  $('#splash_screen').addClass('animate');
  <?php }; ?>

  $('a:not([href*=#]):not([href^="tel:"]):not([target]):not(.no_move_page)').click(function(){
    var url = $(this).attr('href');
    if(url){
      $('#site_loader_overlay').addClass('move_next_page');
      setTimeout(function(){
        location.href = url;
      }, 300);
    }
    return false;
  });

  if( $('#site_loader_overlay').length ){
    var winH = $(window).innerHeight();
    $('#site_loader_overlay').css('height', winH);
    $('#site_loader_overlay').addClass('animate');
    $(window).on('resize', function(){
      var winH = $(window).innerHeight();
      $('#site_loader_overlay').css('height', winH);
      $('#site_loader_overlay').addClass('animate');
    });
  }

});

(function($) {
  $(window).on('load', function(){
    setTimeout(function(){
      after_load();
    }, '<?php if ( (is_front_page() && $options['show_splash'] && !isset($_COOKIE['splash_screen']) && $options['splash_display_time'] == 'type1') || (is_front_page() && $options['show_splash'] && $options['splash_display_time'] == 'type2') ) { if($options['splash_type'] == 'type1'){ echo '3500'; } else { echo '2500'; }; } else { echo '500'; }; ?>');
  });
})(jQuery);

</script>
<?php
     };

     // no loading screen ------------------------------------------------------------------------------------------------------------------
     function no_loading_screen(){
       $options = get_design_plus_option();
?>
<script>

<?php if(wp_is_mobile()) { ?>
jQuery(window).bind("pageshow", function(event) {
  if (event.originalEvent.persisted) {
    window.location.reload()
  }
});
<?php }; ?>

<?php
     if(is_front_page()) {
       if($options['show_index_news'] == '1'){
?>
if( jQuery('#news_ticker').length ){
  var newsticker_splide = new Splide( '#news_ticker', {
    type   : 'fade',
    rewind   : true,
    speed: 2000,
    interval: 5000,
    autoplay: true,
    perPage: 1,
    perMove: 1,
    arrows: false,
    pagination: false,
  } );
  newsticker_splide.mount();
}
<?php }; ?>

<?php
     if ( $options['contents_builder'] ) {
       $content_count = 1;
       $contents_builder = $options['contents_builder'];
       if ($contents_builder) :
         foreach($contents_builder as $content) :
           if ( $content['cb_content_select'] == 'carousel' && $content['show_content'] ) {
             $layout = $content['layout'];
             if($layout == 'type1'){
?>
if( jQuery('.num<?php echo $content_count; ?> .index_carousel').length ){
  var index_splide = new Splide( '.num<?php echo $content_count; ?> .index_carousel', {
    type   : 'loop',
    autoplay: true,
    pauseOnFocus: false,
    interval: 7000,
    perPage: 2,
    perMove: 1,
    autoHeight: true,
    pagination: false,
  } );
  index_splide.mount();
}
<?php        } else { ?>
if( jQuery('.num<?php echo $content_count; ?> .index_carousel').length ){
  var index_splide = new Splide( '.num<?php echo $content_count; ?> .index_carousel', {
    type   : 'loop',
    autoplay: true,
    pauseOnFocus: false,
    interval: 7000,
    perPage: 3,
    breakpoints: {
      1000: {
        perPage: 2,
      },
    },
    perMove: 1,
    autoHeight: true,
    pagination: false,
  } );
  index_splide.mount();
}
<?php
             }
           };
           $content_count++;
         endforeach;
       endif;
     };
?>
<?php }; // is front page?>

<?php
     global $post;
     if(is_page()){ 
       $page_hide_footer = get_post_meta($post->ID, 'page_hide_footer', true) ?  get_post_meta($post->ID, 'page_hide_footer', true) : 'no';
     } else {
       $page_hide_footer = 'no';
     }
     if( ($page_hide_footer != 'yes') && $options['show_image_carousel'] && $options['image_carousel']){
       $item_count = count($options['image_carousel']);
       if($item_count > 4){
?>
if( jQuery('#footer_image_carousel').length ){
  var footer_splide = new Splide( '#footer_image_carousel', {
    type   : 'loop',
    drag   : 'free',
    focus  : 'center',
    autoWidth: true,
    autoHeight: true,
    perPage: 3,
    arrows: false,
    pagination: false,
    autoScroll: {
      speed: 0.5,
    },
  } );
  footer_splide.mount(window.splide.Extensions);
}
<?php
       };
     };
?>

jQuery(document).ready(function($){

  <?php if(!is_front_page() || (is_front_page() && count($options['index_slider'])<1)) { ?>
  <?php if(is_front_page()){ ?>
  $('#header_logo, #news_ticker, #site_desc, #drawer_menu_button').addClass('animate');
  <?php } ?>
  <?php  if($options['header_logo_type'] == 'type2' && empty($options['header_logo_image'])){ ?>
  $('#header_logo2').addClass('animate');
  <?php } else { ?>
  $('#header_logo').addClass('animate');
  <?php }; ?>
  $('#drawer_menu_button').addClass('animate');
  setTimeout(function(){
    $("#page_header .catch span").each(function(i){
      $(this).delay(i *300).queue(function(next) {
        $(this).addClass('animate');
        next();
      });
    });
  },1000);
  <?php }; ?>

  <?php if(is_single()) { ?>
  $('#header_logo2, #single_post_title .title, #single_post_category').addClass('animate');
  <?php }; ?>

  <?php if(is_404()){ ?>
  $('#page_404_header').addClass('animate');
  <?php }; ?>
  <?php if( (is_search() && isset($_GET['s']) && empty($_GET['s'])) || (is_search() && !have_posts()) ){ ?>
  $('#page_404_header').addClass('animate');
  <?php }; ?>

});

</script>
<?php } ?>