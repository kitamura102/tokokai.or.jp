jQuery(document).ready(function($) {

  if($('body').hasClass('widgets-php')) {
    var current_item;
    var target_id;
    $(document).on('click', '.tcd_ad_widget_headline', function(){
      $(this).toggleClass('active');
      $(this).next('.tcd_ad_widget_box').toggleClass('open');
    });
  }

  // デザイン見出しのアイコン
  $(document).on('click', '.widget_headline_hide_icon', function(event){
    if ($(this).is(":checked")) {
      $(this).closest('.tcd_widget_content').next().hide();
    } else {
      $(this).closest('.tcd_widget_content').next().show();
    }
  });


  // 記事タイプが最新の時は並び順を表示しない
  $('.style_post_list1_field select.style_post_list1_post_type').change(function(){
    if ( $(this).val() == 'recent_post' ) {
      $(this).closest('.style_post_list1_field').find('.style_post_list1_post_order').hide();
    } else {
      $(this).closest('.style_post_list1_field').find('.style_post_list1_post_order').show();
    }
  });
  $('.style_post_list1_field select.style_post_list1_post_type').each(function(){
    if ( $(this).val() == 'recent_post' ) {
      $(this).closest('.style_post_list1_field').find('.style_post_list1_post_order').hide();
    } else {
      $(this).closest('.style_post_list1_field').find('.style_post_list1_post_order').show();
    }
  });


  $(".search_box_search_order").sortable({
    helper: "clone",
    placeholder: "search_order_placeholder",
    handle: '.search_order_headline',
    forceHelperSize: true,
    forcePlaceholderSize: true,
    tolerance: "pointer",
    update: function(event,ui){
      $('.search_order_input',this).change();
    }
  });
  $( document ).on( 'widget-added widget-updated', function(event, widget) {
    $(".search_box_search_order").sortable({
      helper: "clone",
      placeholder: "search_order_placeholder",
      handle: '.search_order_headline',
      forceHelperSize: true,
      forcePlaceholderSize: true,
      tolerance: "pointer",
      update: function(event,ui){
        $('.search_order_input',this).change();
      }
    });
  });


  // 追加：2023.09.2
  $(document).on('change', 'select.styled_post_list1_widget_content_type', function(event){
    if ( $(this).val() == 'post' ) {
      $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_type_option').show();
      $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_order_option').show();
    } else if ( $(this).val() == 'news' ) {
      $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_type_option').hide();
      $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_order_option').show();
    } else if ( $(this).val() == 'treatment' ) {
      $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_type_option').hide();
      $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_order_option').hide();
    }
  });
  $('select.styled_post_list1_widget_content_type').each(function(){
    if ( $(this).val() == 'post' ) {
      $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_type_option').show();
      $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_order_option').show();
    } else if ( $(this).val() == 'news' ) {
      $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_type_option').hide();
      $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_order_option').show();
    } else if ( $(this).val() == 'treatment' ) {
      $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_type_option').hide();
      $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_order_option').hide();
    }
  });
  $( document ).on( 'widget-added widget-updated', function(event, widget) {
    $('select.styled_post_list1_widget_content_type').each(function(){
      if ( $(this).val() == 'post' ) {
        $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_type_option').show();
        $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_order_option').show();
      } else if ( $(this).val() == 'news' ) {
        $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_type_option').hide();
        $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_order_option').show();
      } else if ( $(this).val() == 'treatment' ) {
        $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_type_option').hide();
        $(this).closest('.tcd_widget_contents_wrap').find('.styled_post_list1_widget_post_order_option').hide();
      }
    });
  });


    // デザインされた記事一覧
    var $widgets_right = $('#widgets-right');
    // デザインされた記事一覧
    $widgets_right.on(
      'change',
      '.js-styled-post-list-tab-post_type',
      function () {
          var $widget_box = $(this).closest('.tcd_toggle_widget_box');
          var post_type = $(this).val();
          $widget_box.find('[class*=styled-post-list-tab-post_type-]').hide();
          $widget_box
              .find('.styled-post-list-tab-post_type-' + post_type)
              .show();
      }
    );


});



//カラーピッカー
(function($){
	function initColorPicker(widget) {
		widget.find('.color-picker').wpColorPicker( {
			change: _.throttle(function() { // For Customizer
				$(this).trigger('change');
			}, 3000 )
		});
	}
	function onFormUpdate(event, widget) {
		initColorPicker(widget);
	}
	$(document).on( 'widget-added widget-updated', onFormUpdate );
	$(document).ready( function() {
		$('#widgets-right .widget:has(.color-picker)').each(function(){
			initColorPicker($(this));
		});
	});
}(jQuery));

