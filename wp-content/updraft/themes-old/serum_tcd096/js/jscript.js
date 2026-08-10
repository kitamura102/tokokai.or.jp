jQuery(document).ready(function($){

  var $window = $(window);
  var $body = $('body');

  // タブコンテンツ　ショートコード用
  $(".qt_tab_content_header .item").on('click',function() {
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
    var target_content = $(this).data('tab-target');
    $(this).closest('.qt_tab_content_wrap').find(".qt_tab_content").removeClass('active');
    $(this).closest('.qt_tab_content_wrap').find(target_content).addClass('active');
    return false;
  });

  // mega menu -------------------------------------------------

  $('a.megamenu_button').parent().addClass('megamenu_parent');

  // carousel
  if( $('.mega_carousel .item').length > 4){
    var mega_splide = new Splide( '.mega_carousel', {
      type   : 'loop',
      rewind: true,
      perPage: 4,
      perMove: 1,
      autoHeight: true,
      pagination: false,
      autoplay: true,
      pauseOnFocus: false,
      interval: 3000,
    });
    mega_splide.mount();
  } else if($('.mega_carousel').length){
    var mega_splide = new Splide( '.mega_carousel', {
      type   : 'loop',
      rewind: false,
      drag: false,
      perPage: 4,
      perMove: 1,
      autoHeight: true,
      pagination: false,
      arrow: false,
      autoplay: false,
      pauseOnFocus: false,
    });
    mega_splide.mount();
  };
  if( $('.mega_treatment_category_wrap .item').length > 2){
    var mega_treatment_splide = new Splide( '.mega_treatment_category_wrap', {
      type   : 'loop',
      rewind: true,
      perPage: 2,
      perMove: 1,
      autoHeight: true,
      pagination: false,
      autoplay: true,
      pauseOnFocus: false,
      interval: 3000,
    });
    mega_treatment_splide.mount();
  } else if( $('.mega_treatment_category_wrap').length){
    var mega_treatment_splide = new Splide( '.mega_treatment_category_wrap', {
      type   : 'loop',
      drag: false,
      rewind: false,
      perPage: 2,
      perMove: 1,
      autoHeight: true,
      pagination: false,
      arrow: false,
      autoplay: false,
      pauseOnFocus: false,
    });
    mega_treatment_splide.mount();
  };

  // tab post list
  $(document).on({mouseenter : function(){
    $(this).parent().siblings().removeClass('active')
    $(this).parent().addClass('active');
    var $content_id = "." + $(this).attr('data-cat-id');
    $(".megamenu_a .post_list").css("display","none");
    $($content_id).css("display","flex");
    return false;
  }}, '.megamenu_a .category_list a');

  // mega menu basic animation
  $('[data-megamenu]').each(function() {

    var mega_menu_button = $(this);
    var sub_menu_wrap =  "#" + $(this).data("megamenu");
    var hide_sub_menu_timer;
    var hide_sub_menu_interval = function() {
      if (hide_sub_menu_timer) {
        clearInterval(hide_sub_menu_timer);
        hide_sub_menu_timer = null;
      }
      hide_sub_menu_timer = setInterval(function() {
        if (!$(mega_menu_button).is(':hover') && !$(sub_menu_wrap).is(':hover')) {
          $(sub_menu_wrap).stop().removeClass('active_mega_menu');
          clearInterval(hide_sub_menu_timer);
          hide_sub_menu_timer = null;
        }
      }, 20);
    };

    mega_menu_button.hover(
     function(){
       if (hide_sub_menu_timer) {
         clearInterval(hide_sub_menu_timer);
         hide_sub_menu_timer = null;
       }
       if ($('html').hasClass('pc')) {
         $('#header').addClass('active');
         $(this).parent().addClass('active_megamenu_button');
         $(this).parent().find("ul").addClass('megamenu_child_menu');
         $(sub_menu_wrap).stop().addClass('active_mega_menu');
         if( $('.mega_carousel',sub_menu_wrap).length ){
           mega_splide.destroy();
           mega_splide.mount();
         };
         if( $('.mega_treatment_category_wrap',sub_menu_wrap).length ){
           mega_treatment_splide.destroy();
           mega_treatment_splide.mount();
         };
       };
     },
     function(){
       if ($('html').hasClass('pc')) {
         $('#header').removeClass('active');
         $(this).parent().removeClass('active_megamenu_button');
         $(this).parent().find("ul").removeClass('megamenu_child_menu');
         hide_sub_menu_interval();
       }
     }
    );

    $(sub_menu_wrap).hover(
      function(){
        $('#header').addClass('active');
        $(mega_menu_button).parent().addClass('active_megamenu_button');
      },
      function(){
        $('#header').removeClass('active');
        $(mega_menu_button).parent().removeClass('active_megamenu_button');
      }
    );


    $('#header').on('mouseout', sub_menu_wrap, function(){
     if ($('html').hasClass('pc')) {
       hide_sub_menu_interval();
     }
    });

  }); // end mega menu


  // inner link ---------------------------------
    $('a[href*=#], area[href*=#]').not("a.no_auto_scroll").click(function() {

      var speed = 1000,
          href = $(this).prop("href"),
          hrefPageUrl = href.split("#")[0],
          currentUrl = location.href,
          currentUrl = currentUrl.split("#")[0];

      if(hrefPageUrl == currentUrl){

        href = href.split("#");
        href = href.pop();
        href = "#" + href;

        var target = $(href == "#" || href == "" ? 'html' : href);
        if( target.length ){
          var position = target.offset().top - 30,
              body = 'html',
              userAgent = window.navigator.userAgent.toLowerCase(),
              header_height = $('#header').innerHeight();

          $(body).animate({ scrollTop: position - header_height }, speed, 'easeOutExpo');
        }

        return false;
      }

    });


  //front page scroll button
  $('#header_scroll_button').click(function() {
    var myHref= $(this).attr("href");
    if($('body').hasClass('hide_page_header_bar')){
      var myPos = $(myHref).offset().top;
    } else {
      var myPos = $(myHref).offset().top - 60;
    }
    $("html,body").animate({scrollTop : myPos}, 1000, 'easeOutExpo');
    return false;
  });


  // header search
  $("#header_search_button").on('click',function() {
    if($(this).parent().hasClass("active")) {
      $(this).parent().removeClass("active");
      return false;
    } else {
      $(this).parent().addClass("active");
      $('#header_search_input').focus();
      return false;
    }
  });


  // global menu
  $("#global_menu li:not(.megamenu_parent)").hover(function(){
    $(">ul:not(:animated)",this).slideDown("fast");
    $(this).addClass("active");
    $('#header').addClass('active');
  }, function(){
    $(">ul",this).slideUp("fast");
    $(this).removeClass("active");
  });


  // active header
  $("#header").hover(function(){
    $('#header').addClass('active');
  }, function(){
    $('#header').removeClass('active');
  });


  //fixed footer content
  var fixedFooter = $('#fixed_footer_content');
  fixedFooter.removeClass('active');
  $window.scroll(function () {
    if ($body.hasClass('show-serumtal')) return;

    if ($(this).scrollTop() > 330) {
      fixedFooter.addClass('active');
    } else {
      fixedFooter.removeClass('active');
    }
  });
  $('#fixed_footer_content .close').click(function() {
    $("#fixed_footer_content").hide();
    return false;
  });


  // comment button
  $("#comment_tab li").click(function() {
    $("#comment_tab li").removeClass('active');
    $(this).addClass("active");
    $(".tab_contents").hide();
    var selected_tab = $(this).find("a").attr("href");
    $(selected_tab).fadeIn();
    return false;
  });


  //custom drop menu widget
  $(".tcdw_custom_drop_menu li:has(ul)").addClass('parent_menu');
  $(".tcdw_custom_drop_menu li").hover(function(){
     $(">ul:not(:animated)",this).slideDown("fast");
     $(this).addClass("active");
  }, function(){
     $(">ul",this).slideUp("fast");
     $(this).removeClass("active");
  });


  // design select box widget
  $(".design_select_box select").on("click" , function() {
    $(this).closest('.design_select_box').toggleClass("open");
  });
  $(document).mouseup(function (e){
    var container = $(".design_select_box");
    if (container.has(e.target).length === 0) {
      container.removeClass("open");
    }
  });


  //archive list widget
  if ($('.p-dropdown').length) {
    $('.p-dropdown__title').click(function() {
      $(this).toggleClass('is-active');
      $('+ .p-dropdown__list:not(:animated)', this).slideToggle();
    });
  }


  //category widget
  $(".tcd_category_list li:has(ul)").addClass('parent_menu');
  $(".tcd_category_list li.parent_menu > a").parent().prepend("<span class='child_menu_button'></span>");
  $(".tcd_category_list li .child_menu_button").on('click',function() {
     if($(this).parent().hasClass("open")) {
       $(this).parent().removeClass("active");
       $(this).parent().removeClass("open");
       $(this).parent().find('>ul:not(:animated)').slideUp("fast");
       return false;
     } else {
       $(this).parent().addClass("active");
       $(this).parent().addClass("open");
       $(this).parent().find('>ul:not(:animated)').slideDown("fast");
       return false;
     };
  });


  //tab post list widget
  $('.widget_tab_post_list_button').on('click', '.tab1', function(){
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
    $(this).closest('.tab_post_list_widget').find('.widget_tab_post_list1').addClass('active');
    $(this).closest('.tab_post_list_widget').find('.widget_tab_post_list2').removeClass('active');
    return false;
  });
  $('.widget_tab_post_list_button').on('click', '.tab2', function(){
    $(this).siblings().removeClass('active');
    $(this).addClass('active');
    $(this).closest('.tab_post_list_widget').find('.widget_tab_post_list2').addClass('active');
    $(this).closest('.tab_post_list_widget').find('.widget_tab_post_list1').removeClass('active');
    return false;
  });


  //search widget
  $('.widget_search #searchsubmit').wrap('<div class="submit_button"></div>');
  $('.google_search #searchsubmit').wrap('<div class="submit_button"></div>');


  //calendar widget
  $('.wp-calendar-table td').each(function () {
    if ( $(this).children().length == 0 ) {
      $(this).addClass('no_link');
      $(this).wrapInner('<span></span>');
    } else {
      $(this).addClass('has_link');
    }
  });


  //text widget
  $('.widget_text .textwidget').each(function () {
    $(this).addClass('post_content clearfix');
  });


  //image widget
  $('.widget_media_image').each(function () {
    $(this).wrapInner('<div class="post_content clearfix"></div>');
  });


  // FAQ list
  $('.faq_list .title').on('click', function() {
    var desc = $(this).next('.desc_area');
    var acc_height = desc.find('.desc').outerHeight(true);
    if($(this).hasClass('active')){
      desc.css('height', '');
      $(this).removeClass('active');
    }else{
      desc.css('height', acc_height);
      $(this).addClass('active');
    }
  });


  //return top button
  var return_top_button = $('#return_top');
  $('a',return_top_button).click(function() {
    var myHref= $(this).attr("href");
    var myPos = $(myHref).offset().top;
    $("html,body").animate({scrollTop : myPos}, 1000, 'easeOutExpo');
    return false;
  });


// responsive ------------------------------------------------------------------------
var mql = window.matchMedia('screen and (min-width: 1221px)');
function checkBreakPoint(mql) {

  if(mql.matches){ //PC

    $("html").removeClass("mobile");
    $("html").addClass("pc");

  } else { //smart phone

    $("html").removeClass("pc");
    $("html").addClass("mobile");

    // perfect scroll
    if ($('#drawer_menu').length) {
      if(! $(body).hasClass('mobile_device') ) {
        new SimpleBar($('#drawer_menu')[0]);
      };
    };

    // drawer menu
    $("#mobile_menu .child_menu_button").remove();
    $('#mobile_menu li > ul').parent().prepend("<span class='child_menu_button'><span class='icon'></span></span>");
    $("#mobile_menu .child_menu_button").on('click',function() {
      if($(this).parent().hasClass("open")) {
        $(this).parent().removeClass("open");
        var parent_menu = $(this).parent().find('>ul:not(:animated)');
        parent_menu.slideUp("fast");
        $('li',parent_menu).removeClass('animate');
        return false;
      } else {
        $(this).parent().addClass("open");
        var parent_menu = $(this).parent().find('>ul:not(:animated)');
        parent_menu.slideDown("fast");
        $('li',parent_menu).each(function(i){
          $(this).delay(i *100).queue(function(next) {
            $(this).addClass('animate');
            next();
          });
        });
        return false;
      };
    });

    // drawer menu button
    var menu_button = $('#drawer_menu_button');
    menu_button.off();
    menu_button.toggleClass("active",false);
    var scrollTop;

    // open drawer menu
    menu_button.on('click', function(e) {
      if ($(this).attr('href') === '#') {
        e.preventDefault(); // デフォルト動作を防ぐ
      }
      e.stopPropagation();
      $('html').toggleClass('open_menu');
      scrollTop = $(window).scrollTop();
      $('body').css({
        position: 'fixed',
        top: -scrollTop
      });
    });

    // close button
    $("#drawer_menu .close_button").off();
    $("#drawer_menu .close_button,#mobile_menu a[href*='#']").on('click',function() {
      
      // 他ページへページ内リンクなら、デフォルトのリンク動作を許可する
      var href = $(this).prop("href");
      if (typeof href !== "undefined" && href !== null) {
        hrefPageUrl = href.split("#")[0],  // リンク先のページURL部分を取得
        currentUrl = location.href.split("#")[0];  // 現在のページURL部分を取得

        // 他ページからのリンクの場合
        if (hrefPageUrl !== currentUrl) {
        return true; 
        }
      }

      $('html').toggleClass("open_menu");
      $('body').css({
        position: '',
        top: ''
      });
      $(window).scrollTop(scrollTop);
      return true;
    });

    // footer bar
    var footerBar = $("#js-footer-bar");
    if( footerBar.length == 0 ) return;

    footerBar.find( '.js-footer-bar-share, #js-footer-bar-modal-overlay' ).on('click', function(e) {
      e.preventDefault();
      footerBar.find('#js-footer-bar-modal').toggleClass('is-active');		
      return false;
    });
    footerBar.find('#js-footer-bar-modal').on('touchmove', function(e) {
      e.preventDefault();
    });

    (new IntersectionObserver(function (entries) {
      if( entries[0].isIntersecting ){
        footerBar[0].classList.remove('is-active');
      } else {
        footerBar[0].classList.add('is-active');
      }
    })).observe(document.getElementById('js-body-start'));

  };

};
mql.addListener(checkBreakPoint);
checkBreakPoint(mql);


});