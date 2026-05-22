<?php
     global $post;
     $options = get_design_plus_option();
?>
<div id="side_col">
<?php
     // アバウトページ ------------------------------------------------------------
     if(is_page_template('page-about.php')){
?>
<div id="about_page_menu">
</div>
<script type="text/javascript">
(function($){

  var count = 0,
      chapter_id = '',
      menu_id = '',
      menu_item = '',
      mobile_menu_item = '';

  $('#about_post_content h2').each(function(){
    count ++;
    chapter_id = 'chapter_' + count;
    menu_id = 'about_menu_' + count;
    $(this).attr('id',chapter_id);
    menu_item = '<a class="no_auto_scroll" href="#' + chapter_id + '">' + $(this).text() + '</a>';
    mobile_menu_item = '<a class="no_auto_scroll" href="#' + chapter_id + '">' + $(this).text() + '</a>';
    $('#about_page_menu').append(menu_item);
    $('#mobile_about_page_menu .menu').append(mobile_menu_item);
  });

  $('#about_page_menu a').on('click', function() {
    var myHref= $(this).attr("href");
    var myPos = $(myHref).offset().top - 110;
    $("html,body").animate({scrollTop : myPos}, 1000, 'easeOutExpo');
    return false;
  });

  $('#mobile_about_page_menu a').on('click', function() {
    var myHref= $(this).attr("href");
    var myPos = $(myHref).offset().top - 150;
    $("html,body").animate({scrollTop : myPos}, 1000, 'easeOutExpo');
    return false;
  });

  if ($('#mobile_about_page_menu').length) {
    function about_menu_scroll_bar(){
      var about_menu_width = 0;
      $('#mobile_about_page_menu a').each(function(i){
        about_menu_width += $(this).outerWidth(true);
      });
      var winW = $(window).innerWidth();
      if(winW < about_menu_width){
        $("#mobile_about_page_menu .menu_wrap").addClass('use_scroll');
        if(! $(body).hasClass('mobile_device') ) {
          new SimpleBar($('#mobile_about_page_menu .menu_wrap')[0]);
        };
      } else {
        $("#mobile_about_page_menu .menu_wrap").removeClass('use_scroll');
      }
    };
    about_menu_scroll_bar();
    $(window).on('resize', function(){
      about_menu_scroll_bar();
    });
  }

})(jQuery);
const select = document.querySelector('#js-body-start');
const observer = new window.IntersectionObserver( (entry) => {
  if (!entry[0].isIntersecting) {
    document.querySelector('body').classList.add('mobile_about_page_menu_sticky');
  } else {
    document.querySelector('body').classList.remove('mobile_about_page_menu_sticky');
  }
});
observer.observe(select);
</script>
<?php
     }; // アバウトページここまで ------------------------------------------------------------

     $sidebar = '';

     if ( is_mobile() ) {

       if(is_singular('news')) {
         $sidebar = 'news_single_widget_mobile';
       } elseif ( is_single() || is_home() || is_archive() || is_search()) {
         $sidebar = 'single_widget_mobile';
       } elseif(is_page()) {
         $sidebar = 'page_widget_mobile';
       }

       if ( is_active_sidebar( $sidebar ) || is_active_sidebar( 'common_widget_mobile' )) {
         if ( is_active_sidebar( $sidebar ) ) { dynamic_sidebar( $sidebar ); } elseif(is_active_sidebar( 'common_widget_mobile' )) { dynamic_sidebar( 'common_widget_mobile' ); };
       };

     } else {

       if(is_singular('news')) {
         $sidebar = 'news_single_widget';
       } elseif ( is_single() || is_home() || is_archive() || is_search()) {
         $sidebar = 'single_widget';
       } elseif(is_page()) {
         $sidebar = 'page_widget';
       }

       if ( is_active_sidebar( $sidebar ) || is_active_sidebar( 'common_widget' )) {
         if ( is_active_sidebar( $sidebar ) ) { dynamic_sidebar( $sidebar ); } elseif(is_active_sidebar( 'common_widget' )) { dynamic_sidebar( 'common_widget' ); };
       };

     };
?>
</div>