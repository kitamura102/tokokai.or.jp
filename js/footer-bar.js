jQuery(document).ready(function($){

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


});
