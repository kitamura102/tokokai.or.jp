(function($) {

  var flag = false;
  var flag_mobile = false;

  $(window).on('load resize', function(){

    var body = $('body');
    var header_height = $("#header").innerHeight();
    var header_message = $("#header_message");

    var header_message_height = 0;
    if(header_message.length){
      header_message_height = header_message.innerHeight();
    }

    $(window).scroll(function () {
      if( $(this).scrollTop() > header_message_height + 800) {
        body.addClass("open_header");
        flag = true;
      } else {
        body.removeClass("open_header");
        if (flag == true){
          body.addClass("close_header");
          setTimeout(function(){
            body.removeClass("close_header");
            flag = false;
          },200);
        }
      };
      if ($(this).scrollTop() > 100) {
        $('#return_top').addClass('active');
      } else {
        $('#return_top').removeClass('active');
      }
      if( $(this).scrollTop() > header_message_height + 300) {
        body.addClass("open_header_mobile");
        flag_mobile = true;
      } else {
        if( !$('html').hasClass('open_menu') ) {
          body.removeClass("open_header_mobile");
          if (flag_mobile == true){
            body.addClass("close_header_mobile");
            setTimeout(function(){
              body.removeClass("close_header_mobile");
              flag_mobile = false;
            },200);
          }
        }
      };

    });

  });

})(jQuery);