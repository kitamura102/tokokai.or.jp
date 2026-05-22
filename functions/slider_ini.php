<?php
     $options = get_design_plus_option();

     $index_slider = $options['index_slider'];

     $slider_item_total = count($index_slider);
?>

  var slideWrapper = $('#header_slider'),
      iframes = slideWrapper.find('.youtube-player'),
      ytPlayers = {},
      timers = { slickNext: null };

  // YouTube IFrame Player API script load
  if ($('#header_slider .youtube-player').length) {
    if (!$('script[src="//www.youtube.com/iframe_api"]').length) {
      var tag = document.createElement('script');
      tag.src = 'https://www.youtube.com/iframe_api';
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }
  }

  // YouTube IFrame Player API Ready
  window.onYouTubeIframeAPIReady = function(){
    slideWrapper.find('.youtube-player').each(function(){
      var ytPlayerId = $(this).attr('id');
      if (!ytPlayerId) return;
      var player = new YT.Player(ytPlayerId, {
        events: {
          onReady: function(e) {
            $('#'+ytPlayerId).css('opacity', 0).css('pointerEvents', 'none');
            iframes = slideWrapper.find('.youtube-player');
            ytPlayers[ytPlayerId] = player;
            ytPlayers[ytPlayerId].mute();
            ytPlayers[ytPlayerId].lastStatus = -1;
            var item = $('#'+ytPlayerId).closest('.item');
            if (item.hasClass('slick-current')) {
              playPauseVideo(item, 'play');
            }
          },
          onStateChange: function(e) {
            if (e.data === 0) { // ended
              if (slideWrapper.find('.item').length == 1) {<?php // youtubeスライド1枚のみのループ処理 ?>
                var slide = slideWrapper.find('.slick-current');
                slide.css('transition', 'none').removeClass('animate');
                slide.find('.animate_item').css('transition', 'none').removeClass('animate');
                setTimeout(function(){
                  slide.removeAttr('style');
                  slide.find('.animate_item').removeAttr('style');
                  slide.addClass('animate');
                  playPauseVideo(slide, 'play');
                }, 20);
              } else {
                $('#'+ytPlayerId).stop().css('opacity', 0);
                if ($('#'+ytPlayerId).closest('.item').hasClass('slick-current')) {
                  if (timers.slickNext) {
                    clearTimeout(timers.slickNext);
                    timers.slickNext = null;
                  }
                  slideWrapper.slick('slickNext');
                }
              }
            } else if (e.data === 1) { // play
              $('#'+ytPlayerId).not(':animated').css('opacity', 1);
              if (slideWrapper.find('.item').length > 1) {
                var slide = $('#'+ytPlayerId).closest('.item');
                var slickIndex = slide.attr('data-slick-index') || 0;
                clearInterval(timers[slickIndex]);
                timers[slickIndex] = setInterval(function(){
                  var state = ytPlayers[ytPlayerId].getPlayerState();
                  if (state != 1 && state != 3) {
                    clearInterval(timers[slickIndex]);
                  } else if (ytPlayers[ytPlayerId].getDuration() - ytPlayers[ytPlayerId].getCurrentTime() < 1) {
                    clearInterval(timers[slickIndex]);
                    if (timers.slickNext) {
                      clearTimeout(timers.slickNext);
                      timers.slickNext = null;
                    }
                    slideWrapper.slick('slickNext');
                  }
                }, 200);
              }
            } else if (e.data === 3) { // buffering
              if (ytPlayers[ytPlayerId].lastStatus === -1) {
                $('#'+ytPlayerId).delay(100).animate({opacity: 1}, 400);
              }
            }
            ytPlayers[ytPlayerId].lastStatus = e.data;
          }
        }
      });
    });
  };

  // play or puase video
  function playPauseVideo(slide, control){
    if (!slide) {
      slide = slideWrapper.find('.slick-current');
    }
    // animate caption and logo
    function captionAnimation() {
        slide.find(".catch span").each(function(i){
          $(this).delay(i *400).queue(function(next) {
            $(this).addClass('animate');
            next();
          });
        });
    }
    // youtube item --------------------------
    if (slide.hasClass('youtube')) {
      var ytPlayerId = slide.find('.youtube-player').attr('id');
      if (ytPlayerId) {
        switch (control) {
          case 'play':
            if (ytPlayers[ytPlayerId]) {
              ytPlayers[ytPlayerId].seekTo(0, true);
            }
            // breakしない
          case 'resume':
            if (ytPlayers[ytPlayerId]) {
              ytPlayers[ytPlayerId].playVideo();
            }
            if( slide.hasClass('first_item') ){
              setTimeout(function(){
                captionAnimation();
              }, 1000);
            } else {
              setTimeout(function(){
                captionAnimation();
              }, 600);
            }
            if (timers.slickNext) {
              clearTimeout(timers.slickNext);
              timers.slickNext = null;
            }
            break;
          case 'pause':
            slide.find(".catch span").removeClass('animate');
            if (ytPlayers[ytPlayerId]) {
              ytPlayers[ytPlayerId].pauseVideo();
            }
            if(slide.hasClass('first_item')){
              setTimeout(function(){
                slide.removeClass('first_item');
              }, 1000);
            }
            break;
        }
      }
    // video item ------------------------
    } else if (slide.hasClass('video')) {
      var video = slide.find('video').get(0);
      if (video) {
        switch (control) {
          case 'play':
            video.currentTime = 0;
            // breakしない
          case 'resume':
            video.play();
            if( slide.hasClass('first_item') ){
              setTimeout(function(){
                captionAnimation();
              }, 1000);
            } else {
              setTimeout(function(){
                captionAnimation();
              }, 600);
            }
            var slickIndex = slide.attr('data-slick-index') || 0;
            clearInterval(timers[slickIndex]);
            timers[slickIndex] = setInterval(function(){
              if (video.paused) {
                // clearInterval(timers[slickIndex]);
              } else if (video.duration - video.currentTime < 2) {
                clearInterval(timers[slickIndex]);
                if (timers.slickNext) {
                  clearTimeout(timers.slickNext);
                  timers.slickNext = null;
                }
                slideWrapper.slick('slickNext');
                setTimeout(function(){
                  video.currentTime = 0;
                }, 2000);
              }
            }, 200);
            break;
          case 'pause':
            slide.find(".catch span").removeClass('animate');
            video.pause();
            if(slide.hasClass('first_item')){
              setTimeout(function(){
                slide.removeClass('first_item');
              }, 1000);
            }
            break;
        }
      }
    // normal image item --------------------
    } else if (slide.hasClass('image_item')) {
      switch (control) {
        case 'play':
        case 'resume':
          if( slide.hasClass('first_item') ){
            setTimeout(function(){
              captionAnimation();
            }, 1000);
          } else {
            setTimeout(function(){
              captionAnimation();
            }, 600);
          }
          if (timers.slickNext) {
            clearTimeout(timers.slickNext);
            timers.slickNext = null;
          }
          timers.slickNext = setTimeout(function(){
            slideWrapper.slick('slickNext');
          }, 4000);
          break;
        case 'pause':
          slide.find(".catch span").removeClass('animate');
          if(slide.hasClass('first_item')){
            setTimeout(function(){
              slide.removeClass('first_item');
            }, 1000);
          }
          break;
      }
    }
  }


  // resize youtube
  function youtube_resize(){
    var header_slider = $('#header_slider');
    var content_height = header_slider.innerHeight();
    var content_width = header_slider.innerWidth();
    var youtube_height = content_width*(9/16);
    var youtube_width = content_height*(16/9);
    if(content_width > youtube_width) {
      header_slider.find('.youtube_wrap').addClass('type1');
      header_slider.find('.youtube_wrap').removeClass('type2');
      header_slider.find('.youtube_wrap').css({'width': '100%', 'height': youtube_height});
    } else {
      header_slider.find('.youtube_wrap').removeClass('type1');
      header_slider.find('.youtube_wrap').addClass('type2');
      header_slider.find('.youtube_wrap').css({'width':youtube_width, 'height':content_height });
    }
  }


  // Adjust size
  function adjust_size(){
    var winH = $(window).innerHeight();
    $('#header_slider_wrap').css('height', winH);
    $('#header_slider').css('height', winH);
    $('#header_slider .item').css('height', winH);
    $('#header_slider .slick-track').css('height', winH);
  }


  // DOM Ready
  $(function() {
    slideWrapper.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
      if (currentSlide == nextSlide) return;
      slick.$slides.eq(nextSlide).addClass('animate');
      slick.$slides.eq(currentSlide).addClass('end_animate');
      setTimeout(function(){
        playPauseVideo(slick.$slides.eq(currentSlide), 'pause');
      }, slick.options.speed);
      playPauseVideo(slick.$slides.eq(nextSlide), 'play');
    });
    slideWrapper.on('afterChange', function(event, slick, currentSlide) {
      slick.$slides.not(':eq(' + currentSlide + ')').removeClass('animate end_animate');
    });
    slideWrapper.on('swipe', function(event, slick, direction){
      slideWrapper.slick('setPosition');
    });

    //start the slider
    slideWrapper.slick({
      slide: '.item',
      infinite: true,
<?php if($slider_item_total != 1){ ?>
      dots: true,
      appendDots: $('#header_slider_wrap'),
<?php } else { ?>
      dots: false,
<?php }; ?>
      arrows: false,
      slidesToShow: 1,
      slidesToScroll: 1,
      swipe: true,
      pauseOnFocus: false,
      pauseOnHover: false,
      autoplay: false,
      fade: true,
      autoplaySpeed:9000,
      speed:1500,
      easing: 'easeOutExpo',
    });

    // initialize / first animate
    //adjust_size();
    youtube_resize();
    playPauseVideo($('#header_slider .item1'), 'play');
    $('#header_slider .first_item').addClass('animate');
    $('#header_logo, #news_ticker, #site_desc, #drawer_menu_button').addClass('animate');
    <?php  if($options['header_logo_type'] == 'type2' && empty($options['header_logo_image'])){ ?>
    $('#header_logo2').addClass('animate');
    <?php }; ?>
  });

  $(document).on('click', '#header_slider .slick-dots', function(event){
    $(this).addClass('no_click');
    setTimeout(function(){
      $('#header_slider .slick-dots').removeClass('no_click');
    }, 2000);
  });

  // Resize event
  var currentWidth = $(window).innerWidth();
  $(window).on('resize', function(){
    //adjust_size();
    if (currentWidth == $(this).innerWidth()) {
      return;
    } else {
      youtube_resize();
    };
  });
