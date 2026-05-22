jQuery(function($){

  var themeOptionsForm = $('#myOptionsForm');

  // クイックタグ見出しプレビュー
	themeOptionsForm.on('change', '.qt-hn-preview-wrapper :input', function() {

		var $cl = $(this).closest('.qt-hn-preview-wrapper');
		var $preview = $cl.find('.qt-hn-preview').children().removeAttr('style');

		// そのままvalue
		var obj = {
			color: '[name*="_font_color"]',
			textAlign: '[name*="_text_align"]',
			fontWeight: '[name*="_font_weight"]',
			backgroundColor: '[name*="_bg_color"]',
			borderColor: '[name*="_border_color"]',
			borderStyle: '[name*="_border_style"]',
		};
		Object.keys(obj).forEach(function (key) {
			$preview.css(key, $cl.find(obj[key]).val());
			if ($cl.find(obj[key]).attr('type') == 'radio') {
				$preview.css(key, $cl.find(obj[key] + ':checked').val());
			}
		});

		// px単位
		obj = {
			fontSize: '[name$="_font_size\\]"]',
			borderWidth: '[name*="_border_width"]',
		};
		Object.keys(obj).forEach(function (key) {
			$preview.css(key, $cl.find(obj[key]).val() + 'px');
		});

		// クラス制御（チェックボックス）
		obj = {
			ignore_bg: ['[name*="_ignore_bg"]', 'no_bg', 'use_bg'],
			show_border_top: ['[name*="_border_top"]', 'show_top', 'hide_top'],
			show_border_right: ['[name*="_border_right"]', 'show_right', 'hide_right'],
			show_border_bottom: ['[name*="_border_bottom"]', 'show_bottom', 'hide_bottom'],
			show_border_left: ['[name*="_border_left"]', 'show_left', 'hide_left'],
		};
		Object.keys(obj).forEach(key => {
			if ($cl.find(obj[key][0] + ':checkbox').is(':checked')) {
				$preview.addClass(obj[key][1]).removeClass(obj[key][2]);
			}else{
				$preview.removeClass(obj[key][1]).addClass(obj[key][2]);
			}										
		});

	});
	$('.qt-hn-preview-wrapper').each(function(){
		$(this).find(':input:first').trigger('change');
	});


  // クイックタグボタンプレビュー
  themeOptionsForm.on('change', '.qt-btn-preview-wrapper :input', function() {

		var $cl = $(this).closest('.qt-btn-preview-wrapper');
		var $preview = $cl.find('.qt_button_wrap').removeClass().addClass('qt_button_wrap');
		
		// ボタン共通クラス切り替え
		var array = [
			'[name*="_type"]',
			'[name*="_border_radius"]',
			'[name*="_size"]',
			'[name*="_animation_type"]',
		];
		array.forEach(function (el) {
			$value = $cl.find(el + ':checked').val();
			$preview.addClass($value);
		});

		// 各ボタンに適用される配色
		var color = $cl.find('[name*="_color"]').val();
		var hoverColor = $cl.find('[name*="_color_hover"]').val();

		// 通常時のカラー
		var obj = {
			type1 : ['#fff', color, color],
      type2 : [color, color, 'transparent'],
			type3 : ['#fff', color, 'transparent'],
		};
		Object.keys(obj).forEach(key => {
			var target = $cl.find( '.qt_button.' + key );
			target.css({ 'color' :obj[key][0], 'borderColor': obj[key][1], 'backgroundColor': obj[key][2] });
			if(key == 'type3'){ target.find('.background').css('backgroundColor', color); }
			target.data('colors', obj[key]);
		});

		// ホバー時の配色
		obj = {
			type1 : ['#fff', hoverColor, hoverColor],
			type2 : ['#fff', hoverColor, hoverColor],
			type3 : [hoverColor, hoverColor, 'transparent'],
		};
		Object.keys(obj).forEach(key => {
			var target = $cl.find( '.qt_button.' + key );
			target.data('hover-colors', obj[key]);
		});

	});
	$('.qt-btn-preview-wrapper').each(function(){
		$(this).find(':input:first').trigger('change');
	});

	// マウスオーバー時のアクション
	$(".qt-btn-preview-wrapper .qt_button").hover(
		function () {
			var hoverColors = $(this).data('hover-colors');
			$(this).css({ 'color' :hoverColors[0], 'borderColor': hoverColors[1] });
			if(!$(this).hasClass('type3')){
				$(this).find('.background').css('backgroundColor', hoverColors[2]);
			}				
		},
		function () {
			var colors = $(this).data('colors');
			$(this).css({ 'color' :colors[0], 'borderColor': colors[1], 'backgroundColor': colors[2] });
		}
	);


  // クイックタグアンダーラインプレビュー
	themeOptionsForm.on('change', '.qt-ul-preview-wrapper :input', function() {

		var $cl = $(this).closest('.qt-ul-preview-wrapper');
		var $preview = $cl.find('.qt-ul-preview').find('.qt_underline').removeAttr('style');

		$preview.css('background-image', 'linear-gradient(to right, transparent 50%, ' + $cl.find('[name*="_border_color"]').val() + ' 50%)');
		$preview.css('fontWeight', $cl.find('[name*="_font_weight"]:checked').val());
		$cl.find('.qt-ul-preview > div').removeClass().addClass( $cl.find('[name*="_use_animation"]:checked').val())

	});
	$('.qt-ul-preview-wrapper').each(function(){
		$(this).find(':input:first').trigger('change');
	});



  // 吹き出し
	themeOptionsForm.on('change', '.qt-sb-preview-wrapper :input', function() {

		var $cl = $(this).closest('.qt-sb-preview-wrapper');
		var $preview = $cl.find('.qt-sb-preview').find('.content').removeAttr('style');

		var userName = $cl.find('[name*="_user_name"]').val();
		$cl.find('.qt-sb-preview').find('.name').text(userName);

		var obj = {
			color: '[name*="_font_color"]',
			backgroundColor: '[name*="_bg_color"]',
			borderColor: '[name*="_border_color"]'
		};
		Object.keys(obj).forEach(function (key) {
			$preview.css(key, $cl.find(obj[key]).val());
			if(key == 'backgroundColor'){
        if($cl.find('.qt_speech_bubble').hasClass('right')){
          $preview.find('.after').css('borderLeftColor', $cl.find(obj[key]).val());
        }else{
          $preview.find('.after').css('borderRightColor', $cl.find(obj[key]).val());
        }
			}
		});

	});
	$('.qt-sb-preview-wrapper').each(function(){
		$(this).find(':input:first').trigger('change');
	});



  // Googleマップ
	themeOptionsForm.on('change', '.qt-gm-preview-wrapper :input', function() {

		var $cl = $(this).closest('.qt-gm-preview-wrapper');

		var $map = $cl.find('#qt_google_map');
		var $marker = $map.find('.marker.custom');

		var array = [
			'input[name="dp_options[qt_access_saturation]"]:checked',
			'input[name="dp_options[qt_gmap_marker_type]"]:checked',
			'input[name="dp_options[qt_gmap_marker_bg]"]',
			'input[name="dp_options[qt_gmap_marker_text]"]',
			'input[name="dp_options[qt_gmap_marker_color]"]',
		];

		$values = [];
		array.forEach(function (el) {
			$values.push( $cl.find(el).val() );
		});

		$map.removeClass().addClass($values[0] + ' ' + $values[1]);

		$marker.removeAttr('style').css({
			backgroundColor:$values[2],
    	borderTopColor:$values[2],
    	color: $values[4]
		});

		$map.find('.marker.custom.text').text($values[3]);

	});
  $('.qt-gm-preview-wrapper').each(function(){
		$(this).find(':input:first').trigger('change');
	});


  // 記事下CTA
	themeOptionsForm.on('change', '.single-cta-preview-wrapper :input', function() {

		var $cl = $(this).closest('.single-cta-preview-wrapper');
    var $preview = $cl.find('.single_cta').removeAttr('style');
    var $overlay = $cl.find('.overlay').removeAttr('style');

    // キャッチフレーズ
    var title = $cl.find('[name*="_catch"]').val().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/`/g, '&#x60;').replace(/\r?\n/g,'<br />');
		$preview.find('.title').html(title);
    $preview.css('color', $cl.find('[name*="_catch_font_color"]').val());
    $preview.css('fontSize', $cl.find('[name*="_catch_font_size"]').val() + 'px');

    // タイプ
    $value = $cl.find('[name*="_type"]:checked').val();
		$preview.removeClass().addClass('single_cta ' + $value);

    // オーバーレイ
    var obj = {
			backgroundColor: '[name*="_overlay_color"]',
			opacity: '[name*="_overlay_opacity"]',
		};
    Object.keys(obj).forEach(function (key) {
			$overlay.css(key, $cl.find(obj[key]).val());
		});


	});
  $('.single-cta-preview-wrapper').each(function(){
		$(this).find(':input:first').trigger('change');
	});



  // ミニCTA
	themeOptionsForm.on('change', '.mini-cta-preview-wrapper :input', function() {

		var $cl = $(this).closest('.mini-cta-preview-wrapper');
    var $preview = $cl.find('.mini_cta');

		// キャッチフレーズ
    var title = $cl.find('textarea[name="dp_options[mini_cta_catch]"]').val().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/`/g, '&#x60;').replace(/\r?\n/g,'<br />');
		var title_color = $cl.find('input[name="dp_options[mini_cta_catch_font_color]"]').val();
		if(title === ""){
			$preview.find('.mini_cta_catch').removeAttr('style').css({'margin':0, 'color':title_color}).find('.catch').html(title);
		}else{
			$preview.find('.mini_cta_catch').removeAttr('style').css('color', title_color).find('.catch').html(title);
		}

		// 説明文
    var desc = $cl.find('textarea[name="dp_options[mini_cta_desc]"]').val().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/`/g, '&#x60;').replace(/\r?\n/g,'<br />');
		if(desc === ""){
			$preview.find('.mini_cta_desc').css('margin',0).find('.desc').html(desc);
		}else{
			$preview.find('.mini_cta_desc').removeAttr('style').find('.desc').html(desc);
		}

		// ボタン
    var button_label = $cl.find('input[name="dp_options[mini_cta_button_label]"]').val().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/`/g, '&#x60;').replace(/\r?\n/g,'<br />');
		var button_color = $cl.find('input[name="dp_options[mini_cta_button_bg_color]"]').val();
		$preview.find('.mini_cta_button').removeAttr('style').css('backgroundColor', button_color).html(button_label);

	});
  $('.mini-cta-preview-wrapper').each(function(){
		$(this).find(':input:first').trigger('change');
	});


  // モーダル CTA
	themeOptionsForm.on('change', '.modal-cta-preview-wrapper :input', function() {

		var $cl = $(this).closest('.modal-cta-preview-wrapper');
    var $preview = $cl.find('.modal_cta');

    // キャッチフレーズ
    var title = $cl.find('textarea[name="dp_options[modal_cta_catch]"]').val().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/`/g, '&#x60;').replace(/\r?\n/g,'<br />');
		$preview.find('.modal_cta_catch').html(title).css('fontSize', $cl.find('input[name="dp_options[modal_cta_catch_font_size]"]').val() + 'px')

    // 背景色と透明度
    $preview.find('.modal_cta_overlay').removeAttr('style').css({
      'backgroundColor': $cl.find('input[name="dp_options[modal_cta_overlay_color]"]').val(),
      'opacity': $cl.find('input[name="dp_options[modal_cta_overlay_opacity]"]').val()
    });

	});
  $('.modal-cta-preview-wrapper').each(function(){
		$(this).find(':input:first').trigger('change');
	});


  $('.full_screen_button').on('click', function() {
    var preview = $(this).next().clone().addClass('is_open');
    $('body').append(preview);

    $(document).on('click', '.modal_cta.is_open .modal_cta_close, .modal_cta.is_open .full_screen_overlay', function(){
      $(this).parent().closest('.modal_cta').remove();
    });
  
  });


  // 汎用タブ
  $(document).on('click', '.tcd_standard_tab_label', function(event){
    var tab_index = $('.tcd_standard_tab_label').index(this);
    $(this).addClass('is_active').siblings('.tcd_standard_tab_label').removeClass('is_active');
    $(this).closest('.tcd_standard_tab_area').next('.tcd_standard_tab_contents').find('.tcd_standard_tab_content').removeClass('is_active');
    $('.tcd_standard_tab_content').eq(tab_index).addClass('is_active');
  });


  // デザイン見出しのプレビュー
	themeOptionsForm.on('change', '.design_headline-preview-wrapper :input', function() {

		var $cl = $(this).closest('.design_headline-preview-wrapper');
    var $preview = $cl.find('.design_headline-preview');

    $preview.find('.border').removeAttr('style').css({
      'backgroundColor': $cl.find('input.c-color-picker').val()
    });

	});
	$('.design_headline-preview-wrapper').each(function(){
		$(this).find(':input:first').trigger('change');
	});


});