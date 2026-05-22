<?php

/**
 * エディターに関連する記述をここにまとめる
 *
 * NOTE: TCD Classic Editorの個別対応もここ
 */

/**
 * プラグインが有効化されている場合の処理
 *
 * NOTE: TCDCE_ACTIVEは、プラグインで定義された定数（有効化されていればtrue）
 */
if ( defined( 'TCDCE_ACTIVE' ) && TCDCE_ACTIVE ) {
	/**
	 * スタートガイド
	 */
	// 告知追加： このプラグインを有効化している間、TCDテーマの「クイックタグ」機能は利用できません。
	add_action( 'tcdce_top_menu', 'tcdce_top_menu_common_caution', 9 );
	/**
	 * 基本設定
	 */
	// 告知追加： TCDテーマオプションの設定が本文に反映されるため、基本設定はお使いいただけません。
	add_action( 'tcdce_submenu_tcd_classic_editor_basic', 'tcdce_submenu_basic_common_caution' );
	// 基本設定のスタイルを読み込まない
	remove_filter( 'tcdce_render_quicktag_style', 'tcdce_render_quicktag_basic_style' );
	/**
	 * クイックタグ
	 */
	// フロントの use_quicktagオプションを強制的にオフにする（元テーマの関連スタイルを除去）
	add_filter( 'option_dp_options', 'tcdce_disable_theme_quicktag' );
	/**
	 * Googleマップ
	 */
	// 特に無し
	/**
	 * 目次
	 */
	// 告知追加： このテーマはサイドバーに目次を表示できません。
	add_action( 'tcdce_submenu_tcd_classic_editor_toc', 'tcdce_submenu_disable_sidebar_toc_caution' );

	// スマホ用目次ウィジェットアイコンを表示するブレイクポイントはサイドバーがないため、767に指定
	add_filter( 'tcdce_toc_show_breakpoint', fn() => 767 );

	// ワンカラムテーマのため、目次ウィジェットを削除
	add_action( 'widgets_init', 'tcdce_unregister_toc_widget' );

	// ワンカラムテーマのため、目次の選択肢からサイドバーを無くす
	add_filter( 'tcdce_toc_setting_display_options', function( $options ){
		unset($options[2]);
		unset($options[3]);
		return $options;
	});

	// 目次のスタイル調整
	add_filter( 'tcdce_enqueue_inline_style', function( $style ){
		$style .=
		// 目次のスタイル調整（背景がグレーの場合など）
		// '.widget_tcdce_toc_widget { background:initial; }
		// .p-toc--sidebar { background:#fff; }' .
		// 目次アイコン表示時は、トップに戻るボタンを非表示にする
		'body:has(.p-toc-open) #return_top { display: none; }' .
		// スマホフッターバー表示時の対策
		'body:has(.p-footer-bar) .p-toc-open { margin-bottom: 50px; }' .
		// ドロワーメニュー表示に目次アイコン非表示
		'html.open_menu .p-toc-open { display:none; }';
		return $style;
	} );

	// 目次の投稿タイプから「診療案内」を削除
	add_filter( 'tcdce_toc_setting_post_types_options', function( $post_types ){
		return array_filter( $post_types, function ( $post_type ) {
			return $post_type !== 'treatment';
		} );
	} );
	/**
	 * design-plus.cssを取り除く
	 *
	 * NOTE: design-plus.cssの中に必要な記述があればスタイルシートに移設
	 */
	add_action( 'wp_enqueue_scripts', function(){
		wp_dequeue_style( 'design-plus' );
	} );
	/**
	 * エディタ独自スタイル対応
	 */
	add_filter( 'tcdce_enqueue_inline_style', function( $style ){
		$style .=
		/* LP内のテーブルスタイル */
		'#lp_table th { background:#f7f7f7; }' .
		'#lp_table :is(td, th) { padding: 15px 30px 13px; line-height: 2.2; }' .
		'@media screen and (max-width:800px) { #lp_table :is(td, th) { padding: 14px 15px; line-height: 1.8; } }' .
		'';
		return $style;
	} );
	/**
	 * 有効化されていれば、ココで処理を止める
	 */
	return;
}
/**
 * 以下はテーマのエディタ周りの機能
 *
 * NOTE: プラグイン有効化時は、以下は実行されない
 * テーマの機能を移設する場合は、この下に追記してください
 */

/**
 * the_contentで実行されているもの
 */

// table スクロール対応 ------------------------------------------------------------------------
add_filter('the_content', function( $content ){
  if( !has_blocks() ){
    $content = str_replace( '<table', '<div class="s_table"><table', $content );
    $content = str_replace( '</table>', '</table></div>', $content );
  }
  return $content;
} );

/**
 * mce関連のカスタマイズ
 */
// ビジュアルエディタに表(テーブル)の機能を追加 -----------------------------------------------
function mce_external_plugins_table($plugins) {
	$plugins['table'] = 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.7.4/plugins/table/plugin.min.js';
	return $plugins;
}
add_filter( 'mce_external_plugins', 'mce_external_plugins_table' );

// tinymceのtableボタンにclass属性プルダウンメニューを追加
function mce_buttons_table($buttons) {
	$buttons[] = 'table';
	return $buttons;
}
add_filter( 'mce_buttons', 'mce_buttons_table' );

function bootstrap_classes_tinymce($settings) {
	$styles = array(
		array('title' => __('Default style', 'tcd-serum'), 'value' => ''),
		array('title' => __('No border', 'tcd-serum'), 'value' => 'table_no_border'),
		array('title' => __('Display only serumtal border', 'tcd-serum'), 'value' => 'table_border_serumtal')
	);
	$settings['table_class_list'] = json_encode($styles);
	return $settings;
}
add_filter('tiny_mce_before_init', 'bootstrap_classes_tinymce');


// ビジュアルエディタに書体を追加 ---------------------------------------------------------------------
add_filter('mce_buttons', function($buttons){
	array_unshift($buttons, 'fontselect');
	return $buttons;
});
add_filter('tiny_mce_before_init', function($settings){
	$settings['font_formats'] =
		"メイリオ=Arial, 'ヒラギノ角ゴ ProN W3', 'Hiragino Kaku Gothic ProN', 'メイリオ', Meiryo, sans-serif;" .
		"游ゴシック='Hiragino Sans', 'ヒラギノ角ゴ ProN', 'Hiragino Kaku Gothic ProN', '游ゴシック', YuGothic, 'メイリオ', Meiryo, sans-serif;" .
		"游明朝='Times New Roman' , '游明朝' , 'Yu Mincho' , '游明朝体' , 'YuMincho' , 'ヒラギノ明朝 Pro W3' , 'Hiragino Mincho Pro' , 'HiraMinProN-W3' , 'HGS明朝E' , 'ＭＳ Ｐ明朝' , 'MS PMincho' , serif;" .
		"Andale Mono=andale mono,times;" .
		"Arial=arial,helvetica,sans-serif;" .
		"Arial Black=arial black,avant garde;" .
		"Book Antiqua=book antiqua,palatino;" .
		"Comic Sans MS=comic sans ms,sans-serif;" .
		"Courier New=courier new,courier;" .
		"Georgia=georgia,palatino;" .
		"Helvetica=helvetica;" .
		"Impact=impact,chicago;" .
		"Symbol=symbol;" .
		"Tahoma=tahoma,arial,helvetica,sans-serif;" .
		"Terminal=terminal,monaco;" .
		"Times New Roman=times new roman,times;" .
		"Trebuchet MS=trebuchet ms,geneva;" .
		"Verdana=verdana,geneva;" .
		"Webdings=webdings;" .
		"Wingdings=wingdings,zapf dingbats";
	;
	return $settings;
});


// ビジュアルエディタに文字サイズを追加 ---------------------------------------------------------------------
function add_font_size_to_tinymce( $buttons ) {
	array_unshift( $buttons, 'fontsizeselect' );
	return $buttons;
}
add_filter( 'mce_buttons_2', 'add_font_size_to_tinymce' );

function change_font_size_of_tinymce( $initArray ){
	$initArray['fontsize_formats'] = "10px 11px 12px 14px 16px 18px 20px 24px 28px 32px 38px";
	return $initArray;
}
add_filter( 'tiny_mce_before_init', 'change_font_size_of_tinymce' );

// ページ分割
add_filter("mce_buttons", "add_nextpage_buttons");

/**
 * ビジュアルエディタ用スタイルシートの読み込みを移設
 */
// ビジュアルエディタ用スタイルシートの読み込み
function wpdocs_theme_add_editor_styles() {
  add_theme_support('editor-styles');
  add_editor_style( get_template_directory_uri()."/admin/css/editor-style-07.css?d=".date('YmdGis', filemtime(get_template_directory().'/admin/css/editor-style-07.css')) );
}
add_action( 'admin_init', 'wpdocs_theme_add_editor_styles' );

/**
 * TCDCE有効化時に無効化したいテーマのスタイル、スクリプトをココに記載
 */
add_action( 'wp_head', function(){
	/**
	 * エディタに使われているhead内のスタイルがあればココに移設
	 *
	 * NOTE: use_quicktagsをオフにするので、head内のクイックタグスタイルは移設不要（要確認）
	 * NOTE: その他styleの上書きが必要なら
	 */
	/**
	 * エディタに使われているスクリプトをココに移設
	 *
	 * NOTE: マーカーは干渉するので移設が必要
	 */
?>
<script>
jQuery(function ($) {
	var $window = $(window);
	var $body = $('body');
	  // quick tag - underline ------------------------------------------
		if ($('.q_underline').length) {
    var gradient_prefix = null;

    $('.q_underline').each(function(){
      var bbc = $(this).css('borderBottomColor');
      if (jQuery.inArray(bbc, ['transparent', 'rgba(0, 0, 0, 0)']) == -1) {
        if (gradient_prefix === null) {
          gradient_prefix = '';
          var ua = navigator.userAgent.toLowerCase();
          if (/webkit/.test(ua)) {
            gradient_prefix = '-webkit-';
          } else if (/firefox/.test(ua)) {
            gradient_prefix = '-moz-';
          } else {
            gradient_prefix = '';
          }
        }
        $(this).css('borderBottomColor', 'transparent');
        if (gradient_prefix) {
          $(this).css('backgroundImage', gradient_prefix+'linear-gradient(left, transparent 50%, '+bbc+ ' 50%)');
        } else {
          $(this).css('backgroundImage', 'linear-gradient(to right, transparent 50%, '+bbc+ ' 50%)');
        }
      }
    });

    $window.on('scroll.q_underline', function(){
      $('.q_underline:not(.is-active)').each(function(){
        if ($body.hasClass('show-serumtal')) {
          var left = $(this).offset().left;
          if (window.scrollX > left - window.innerHeight) {
            $(this).addClass('is-active');
          }
        } else {
          var top = $(this).offset().top;
          if (window.scrollY > top - window.innerHeight) {
            $(this).addClass('is-active');
          }
        }
      });
      if (!$('.q_underline:not(.is-active)').length) {
        $window.off('scroll.q_underline');
      }
    });
  }
} );
</script>
<?php
} );

/**
 * テーマのクイックタグの登録
 */

function tcd_quicktag_admin_init() {
	global $dp_options;
	if ( ! $dp_options ) $dp_options = get_design_plus_option();

	if ( $dp_options['use_quicktags'] && ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) ) {
		add_filter( 'mce_external_plugins', 'tcd_add_tinymce_plugin' );
		add_filter( 'mce_buttons', 'tcd_register_mce_button' );
		add_action( 'admin_print_footer_scripts', 'tcd_add_quicktags' );

		// Dynamic css for classic visual editor
		add_filter( 'editor_stylesheets', 'editor_stylesheets_tcd_visual_editor_dynamic_css' );

		// Dymamic css for visual editor on block editor
		wp_enqueue_style( 'tcd-quicktags', get_tcd_quicktags_dynamic_css_url(), false, version_num() );
	}
}
add_action( 'admin_init', 'tcd_quicktag_admin_init' );

// Declare script for new button
function tcd_add_tinymce_plugin( $plugin_array ) {
	$plugin_array['tcd_mce_button'] = get_template_directory_uri() . '/admin/js/mce-button.js?ver=' . version_num();
	return $plugin_array;
}

// Register new button in the editor
function tcd_register_mce_button( $buttons ) {
	array_push( $buttons, 'tcd_mce_button' );
	return $buttons;
}

function tcd_add_quicktags() {
	global $dp_options;
	if ( ! $dp_options ) $dp_options = get_design_plus_option();

	$tcdQuicktagsL10n = array(
		'pulldown_title' => array(
			'display' => __( 'quicktags', 'tcd-serum' ),
		),
		'ytube' => array(
			'display' => __( 'YouTube', 'tcd-serum' ),
			'tag' => __( '<div class="ytube">YouTube code here</div>', 'tcd-serum' )
		),
		'relatedcardlink' => array(
			'display' => __( 'Cardlink', 'tcd-serum' ),
			'tag' => __( '[clink url="Post URL to display"]', 'tcd-serum' )
		),
		'post_col-2' => array(
			'display' => __( '2 column', 'tcd-serum' ),
			'tag' => __( '<div class="post_row"><div class="post_col post_col-2">Text and image tags to display in the left column</div><div class="post_col post_col-2">Text and image tags to display in the right column</div></div>', 'tcd-serum' )
		),
		'post_col-3' => array(
			'display' => __( '3 column', 'tcd-serum' ),
			'tag' => __( '<div class="post_row"><div class="post_col post_col-3">Text and image tags to display in the left column</div><div class="post_col post_col-3">Text and image tags to display in the center column</div><div class="post_col post_col-3">Text and image tags to display in the right column</div></div>', 'tcd-serum' )
		),
		'q_comment_out' => array(
			'display' => __( 'Comment out', 'tcd-serum' ),
			'tagStart' => '<div class="hidden"><!-- ',
			'tagEnd' => ' --></div>'
		),
		'q_h2' => array(
			'display' => __( 'Styled h2 tag', 'tcd-serum' ),
			'tagStart' => '<h2 class="styled_h2">',
			'tagEnd' => '</h2>'
		),
		'q_h3' => array(
			'display' => __( 'Styled h3 tag', 'tcd-serum' ),
			'tagStart' => '<h3 class="styled_h3">',
			'tagEnd' => '</h3>'
		),
		'q_h4' => array(
			'display' => __( 'Styled h4 tag', 'tcd-serum' ),
			'tagStart' => '<h4 class="styled_h4">',
			'tagEnd' => '</h4>'
		),
		'q_h5' => array(
			'display' => __( 'Styled h5 tag', 'tcd-serum' ),
			'tagStart' => '<h5 class="styled_h5">',
			'tagEnd' => '</h5>'
		),
		'q_ol' => array(
			'display' => __( 'Styled ol', 'tcd-serum' ),
			'tag' => '<ol class="q_styled_ol">'."\n".'<li>'.__('List', 'tcd-serum').'1</li>'."\n".'<li>'.__('List', 'tcd-serum').'2</li>'."\n".'<li>'.__('List', 'tcd-serum').'3</li>'."\n".'</ol>'
		),
		'gray_bg' => array(
			'display' => __( 'Gray background', 'tcd-serum' ),
			'tag' => "<div class='gray_bg'>\n\n" . __('Enter content here', 'tcd-serum') . "\n\n</div>",
		),
		'q_frame1' => array(
			'display' => __( 'Frame style', 'tcd-serum' ).'1',
			'tagStart' => '<p class="q_frame q_frame1"><span class="q_frame_label">'.esc_html($dp_options['qt_frame1_label']).'</span>',
			'tagEnd' => '</p>'
		),
		'q_frame2' => array(
			'display' => __( 'Frame style', 'tcd-serum' ).'2',
			'tagStart' => '<p class="q_frame q_frame2"><span class="q_frame_label">'.esc_html($dp_options['qt_frame2_label']).'</span>',
			'tagEnd' => '</p>'
		),
		'q_frame3' => array(
			'display' => __( 'Frame style', 'tcd-serum' ).'3',
			'tagStart' => '<p class="q_frame q_frame3"><span class="q_frame_label">'.esc_html($dp_options['qt_frame3_label']).'</span>',
			'tagEnd' => '</p>'
		),
		'q_custom_button1' => array(
			'display' => sprintf( __( 'Button %d', 'tcd-serum' ), 1 ),
			'tag' => '<div class="q_button_wrap"><a href="#" class="q_custom_button q_custom_button1">' . sprintf( __( 'Button %d', 'tcd-serum' ), 1 ) . '</a></div>'
		),
		'q_custom_button2' => array(
			'display' => sprintf( __( 'Button %d', 'tcd-serum' ), 2 ),
			'tag' => '<div class="q_button_wrap"><a href="#" class="q_custom_button q_custom_button2">' . sprintf( __( 'Button %d', 'tcd-serum' ), 2 ) . '</a></div>'
		),
		'q_custom_button3' => array(
			'display' => sprintf( __( 'Button %d', 'tcd-serum' ), 3 ),
			'tag' => '<div class="q_button_wrap"><a href="#" class="q_custom_button q_custom_button3">' . sprintf( __( 'Button %d', 'tcd-serum' ), 3 ) . '</a></div>'
		),
		'q_underline1' => array(
			'display' => sprintf( __( 'Underline %d', 'tcd-serum' ), 1 ),
			'tagStart' => '<span class="q_underline q_underline1" style="border-bottom-color:;">',
			'tagEnd' => '</span>'
		),
		'q_underline2' => array(
			'display' => sprintf( __( 'Underline %d', 'tcd-serum' ), 2 ),
			'tagStart' => '<span class="q_underline q_underline2" style="border-bottom-color:;">',
			'tagEnd' => '</span>'
		),
		'q_underline3' => array(
			'display' => sprintf( __( 'Underline %d', 'tcd-serum' ), 3 ),
			'tagStart' => '<span class="q_underline q_underline3" style="border-bottom-color:;">',
			'tagEnd' => '</span>'
		),
		'speech_balloon_left1' => array(
			'display' => __( 'Speech balloon left 1', 'tcd-serum' ),
			'tagStart' => '[speech_balloon_left1]',
			'tagEnd' => '[/speech_balloon_left1]'
		),
		'speech_balloon_left2' => array(
			'display' => __( 'Speech balloon left 2', 'tcd-serum' ),
			'tagStart' => '[speech_balloon_left2]',
			'tagEnd' => '[/speech_balloon_left2]'
		),
		'speech_balloon_right1' => array(
			'display' => __( 'Speech balloon right 1', 'tcd-serum' ),
			'tagStart' => '[speech_balloon_right1]',
			'tagEnd' => '[/speech_balloon_right1]'
		),
		'speech_balloon_right2' => array(
			'display' => __( 'Speech balloon right 2', 'tcd-serum' ),
			'tagStart' => '[speech_balloon_right2]',
			'tagEnd' => '[/speech_balloon_right2]'
		),
		'google_map' => array(
			'display' => __( 'Google map', 'tcd-serum' ),
			'tag' => '[qt_google_map address="'. __( 'Enter address here', 'tcd-serum' ) . '"]'
		),
		'schedule' => array(
			'display' => __( 'Basic information and business day', 'tcd-serum' ),
			'tag' => '[sc_basic_info]'
		),
		'responsive_desc' => array(
			'display' => __( 'PC / mobile content', 'tcd-serum' ),
			'tag' => __( "<div class='responsive_desc'>\n<div class='pc'>\nPlease enter content for PC here.\n</div>\n<div class='mobile'>\nPlease enter content for mobile device here.\n</div>\n</div>", 'tcd-serum' )
		),
		'tab_content' => array(
			'display' => __( 'Tab content', 'tcd-serum' ),
			'tag' => '[tcd_tab tab1="'. __( 'Tab1 headline', 'tcd-serum' ) . '" img1="'. __( 'Enter tab1 image url here', 'tcd-serum' ) . '" tab2="'. __( 'Tab2 headline', 'tcd-serum' ) . '" img2="'. __( 'Enter tab2 image url here', 'tcd-serum' ) . '"]'
		),
	);
?>
<script type="text/javascript">
<?php
	// check if WYSIWYG is enabled
	if ( 'true' == get_user_option( 'rich_editing' ) ) {
		echo "var tcdQuicktagsL10n = " . json_encode( $tcdQuicktagsL10n ) . ";\n";
	}
	if ( wp_script_is( 'quicktags' ) ) {
		foreach ( $tcdQuicktagsL10n as $key => $value ) {
			if ( is_numeric( $key ) || empty( $value['display'] ) ) continue;
			if ( empty( $value['tag'] ) && empty( $value['tagStart'] ) ) continue;

			if ( isset( $value['tag'] ) && ! isset( $value['tagStart'] ) ) {
				$value['tagStart'] = $value['tag'] . "\n\n";
			}
			if ( ! isset( $value['tagEnd'] ) ) {
				$value['tagEnd'] = '';
			}

			$key = json_encode( $key );
			$display = json_encode( $value['display'] );
			$tagStart = json_encode( $value['tagStart'] );
			$tagEnd = json_encode( $value['tagEnd'] );
			echo "QTags.addButton($key, $display, $tagStart, $tagEnd);\n";
		}
	}
?>
</script>
<?php
}

// Get dymamic css url
function get_tcd_quicktags_dynamic_css_url() {
	return admin_url( 'admin-ajax.php?action=tcd_quicktags_dynamic_css' );
}

// Dymamic css for visual editor
function tcd_ajax_quicktags_dynamic_css() {
	global $dp_options;
	if ( ! $dp_options ) $dp_options = get_design_plus_option();

	header( 'Content-Type: text/css; charset=UTF-8' );

?>
<?php
     if($dp_options['catch_font_type'] == 'type1') {
?>
body.cb_wysiwyg_editor h2, body.cb_wysiwyg_editor h3 { font-family: Arial, "ヒラギノ角ゴ ProN W3", "Hiragino Kaku Gothic ProN", "メイリオ", Meiryo, sans-serif; font-weight:600; }
<?php } elseif($dp_options['catch_font_type'] == 'type2') { ?>
body.cb_wysiwyg_editor h2, body.cb_wysiwyg_editor h3 { font-family: Arial, "Hiragino Sans", "ヒラギノ角ゴ ProN", "Hiragino Kaku Gothic ProN", "游ゴシック", YuGothic, "メイリオ", Meiryo, sans-serif; font-weight:600; }
<?php } else { ?>
body.cb_wysiwyg_editor h2, body.cb_wysiwyg_editor h3 { font-family: "Times New Roman" , "游明朝" , "Yu Mincho" , "游明朝体" , "YuMincho" , "ヒラギノ明朝 Pro W3" , "Hiragino Mincho Pro" , "HiraMinProN-W3" , "HGS明朝E" , "ＭＳ Ｐ明朝" , "MS PMincho" , serif; font-weight:600; }
<?php }; ?>

body.cb_wysiwyg_editor h2.styled_h2:before { display:none; }
<?php
      if($dp_options['content_font_type'] == 'type1') {
?>
body.cb_wysiwyg_editor h2.styled_h2, body.cb_wysiwyg_editor h3.styled_h3 { font-family: Arial, "ヒラギノ角ゴ ProN W3", "Hiragino Kaku Gothic ProN", "メイリオ", Meiryo, sans-serif; }
<?php } elseif($dp_options['content_font_type'] == 'type2') { ?>
body.cb_wysiwyg_editor h2.styled_h2, body.cb_wysiwyg_editor h3.styled_h3 { font-family: Arial, "Hiragino Sans", "ヒラギノ角ゴ ProN", "Hiragino Kaku Gothic ProN", "游ゴシック", YuGothic, "メイリオ", Meiryo, sans-serif; }
<?php } else { ?>
body.cb_wysiwyg_editor h2.styled_h2, body.cb_wysiwyg_editor h3.styled_h3 { font-family: "Times New Roman" , "游明朝" , "Yu Mincho" , "游明朝体" , "YuMincho" , "ヒラギノ明朝 Pro W3" , "Hiragino Mincho Pro" , "HiraMinProN-W3" , "HGS明朝E" , "ＭＳ Ｐ明朝" , "MS PMincho" , serif; }
<?php
			};

			for ( $i = 2; $i <= 5; $i++ ){

				$heading_font_size = $dp_options['qt_h'.$i.'_font_size'];
				$heading_font_size_sp = $dp_options['qt_h'.$i.'_font_size_sp'];
				$heading_text_align = $dp_options['qt_h'.$i.'_text_align'];
				$heading_font_weight = $dp_options['qt_h'.$i.'_font_weight'];
				$heading_font_color = $dp_options['qt_h'.$i.'_font_color'];
				$heading_bg_color = $dp_options['qt_h'.$i.'_bg_color'];
				$heading_ignore_bg = $dp_options['qt_h'.$i.'_ignore_bg'];
				$heading_border = 'qt_h'.$i.'_border_';
				$heading_border_color = $dp_options['qt_h'.$i.'_border_color'];
				$heading_border_width = $dp_options['qt_h'.$i.'_border_width'];
				$heading_border_style = $dp_options['qt_h'.$i.'_border_style'];

?>
.styled_h<?php echo $i ?>, .editor-styles-wrapper .styled_h<?php echo $i ?> {
  font-size:<?php echo esc_attr($heading_font_size); ?>px!important;
  text-align:<?php echo esc_attr($heading_text_align); ?>!important;
  font-weight:<?php echo esc_attr($heading_font_weight); ?>!important;
  color:<?php echo esc_attr($heading_font_color); ?>;
  border-color:<?php echo esc_attr($heading_border_color); ?>;
  border-width:<?php echo esc_attr($heading_border_width); ?>px;
  border-style:<?php echo esc_attr($heading_border_style); ?>;
<?php

  $border_potition = array('left', 'right', 'top', 'bottom');
  foreach( $border_potition as $position ):

    if($dp_options[$heading_border.$position]){
      if($position == 'left' || $position == 'right'){
        echo 'padding-'.$position.':1em!important;'."\n".'padding-top:0.5em!important;'."\n".'padding-bottom:0.5em!important;'."\n";
      }else{
        echo 'padding-'.$position.':0.8em!important;'."\n";
      }
    }else{
      echo 'border-'.$position.':none;'."\n";
    }

  endforeach;

  if($heading_ignore_bg){
    echo 'background-color:transparent;'."\n";
  }else{
    echo 'background-color:'.esc_attr($heading_bg_color).';'."\n".'padding:0.8em 1em!important;'."\n";
  }

?>
}
<?php
		
		}
		
		// カスタムボタン
		for ( $i = 1; $i <= 3; $i++ ) {

	$button_type = $dp_options['qt_button'.$i.'_type'];
	$button_shape = $dp_options['qt_button'.$i.'_border_radius'];
	$button_size = $dp_options['qt_button'.$i.'_size'];
	$button_animation_type = $dp_options['qt_button'.$i.'_animation_type'];
	$button_color = $dp_options['qt_button'.$i.'_color'];
	$button_color_hover = $dp_options['qt_button'.$i.'_color_hover'];

	$colors = array();
	$animations = array();

	switch ($button_shape){
		case 'flat': $shape = 'border-radius:0px;'; break;
		case 'rounded': $shape = 'border-radius:6px;'; break;
		case 'oval': $shape = 'border-radius:70px;'; break;
	}
	switch ($button_size){
		case 'small': $size = 'min-width:130px; height:40px; line-height:40px;'; break;
		case 'medium': $size = 'min-width:280px; height:60px; line-height:60px;'; break;
		case 'large': $size = 'min-width:400px; height:70px; line-height:70px;'; break;
	}
	switch ($button_type){
    case 'type1': $colors = array('color:#fff !important; background-color:'.$button_color.';border:none;', 'background-color:'.$button_color_hover.';', '' ); break;
    case 'type2': $colors = array('color:'.$button_color.' !important; border-color:'.$button_color.';', 'background-color:'.$button_color_hover.';', 'color:#fff !important; border-color:'.$button_color_hover.';'); break;
    case 'type3': $colors = array('color:#fff !important; border-color:'.$button_color.';','background-color:'.$button_color.';', 'color:'.$button_color_hover.' !important; border-color:'.$button_color_hover.';' ); break;
	}
	switch ($button_animation_type){
    case 'animation_type1': $animations = ($button_type != 'type3') ? array('opacity:0;', 'opacity:1;') : array('opacity:1;', 'opacity:0;'); break;
    case 'animation_type2': $animations = ($button_type != 'type3') ? array('left:-100%;', 'left:0;') : array('left:0;', 'left:100%;'); break;
    case 'animation_type3': $animations = ($button_type != 'type3') ? array('left:calc(-100% - 110px);transform:skewX(45deg); width:calc(100% + 70px);', 'left:-35px;') : array('left:-35px;transform:skewX(45deg); width:calc(100% + 70px);', 'left:calc(100% + 50px);'); break;
	}

?>
.q_custom_button<?php echo $i; ?> { <?php echo $size.$shape.$colors[0]; ?> }
.q_custom_button<?php echo $i; ?>:before { <?php echo $colors[1].$animations[0]; ?> }
<?php

	}


	// アンダーライン
	for ( $i = 1; $i <= 3; $i++ ) {

		$underline_color = $dp_options['qt_underline'.$i.'_border_color'];
		$underline_font_weight = $dp_options['qt_underline'.$i.'_font_weight'];

?>
.q_underline<?php echo $i; ?> {
	font-weight:<?php echo esc_attr($underline_font_weight); ?>;
	border-bottom-color:<?php echo esc_attr($underline_color); ?>;
}

<?php

  }

	// 囲み枠
	for ( $i = 1; $i <= 3; $i++ ) {

    $label_color = $dp_options['qt_frame'.$i.'_label_color'];
    $bg_color = $dp_options['qt_frame'.$i.'_content_bg_color'];
		$border_radius = $dp_options['qt_frame'.$i.'_content_shape'];
    $border_width = $dp_options['qt_frame'.$i.'_content_border_width'];
    $border_color = $dp_options['qt_frame'.$i.'_content_border_color'];
		$border_style = $dp_options['qt_frame'.$i.'_content_border_style'];


?>
.q_frame<?php echo $i; ?> {
	background:<?php echo esc_attr($bg_color); ?>;
	border-radius:<?php echo esc_attr($border_radius); ?>px;
	border-width:<?php echo esc_attr($border_width); ?>px;
	border-color:<?php echo esc_attr($border_color); ?>;
	border-style:<?php echo esc_attr($border_style); ?>;
}
.q_frame<?php echo $i; ?> .q_frame_label {
	color:<?php echo esc_attr($label_color); ?>;
}


/* グレー背景のボックス */
.gray_bg { background: #f3f3f3; padding:50px; margin-bottom:50px; margin-top:50px; }
@media screen and (max-width: 1200px){
  .gray_bg { padding: 20px; }
}
@media screen and (max-width: 800px){
  .gray_bg { margin-bottom:40px; margin-top:40px; }
}


/* ブロックエディタ */
.wp-block-social-links a { color:#fff !important; }
.has-small-font-size { font-size:.8125em !important; }
.has-normal-font-size,
.has-regular-font-size { font-size:1em !important; }
.has-medium-font-size { font-size:1.25em !important; }
.has-large-font-size { font-size:2.25em !important; }
.has-huge-font-size, .has-larger-font-size { font-size:2.625em !important; }
.has-text-align-left { text-align:left !important; }
.has-text-align-right { text-align:right !important; }
.wp-block-embed { margin:0 0 2em 0; }
.wp-block-embed__wrapper { position:relative; width:100%; padding-top:56.25%; }
.wp-block-embed__wrapper iframe { position:absolute; top:0; right:0; width:100%; height:100%; }


<?php

	}

	exit;
}
add_action( 'wp_ajax_tcd_quicktags_dynamic_css', 'tcd_ajax_quicktags_dynamic_css' );

// add_editor_style()だとテーマ内のcssが最後になるためここで最後尾にcss追加
function editor_stylesheets_tcd_visual_editor_dynamic_css( $stylesheets ) {
	$stylesheets[] = get_tcd_quicktags_dynamic_css_url();
	$stylesheets = array_unique( $stylesheets );
	return $stylesheets;
}
