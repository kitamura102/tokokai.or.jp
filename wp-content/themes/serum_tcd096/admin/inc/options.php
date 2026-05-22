<?php
/*
 * オプションの設定
 */

//フォントの縦方向
global $font_direction_options;
$font_direction_options = array(
  'type1' => array('value' => 'type1','label' => __( 'Horizontal', 'tcd-serum' )),
  'type2' => array('value' => 'type2','label' => __( 'Vertical', 'tcd-serum' )),
);


// hover effect
global $hover_type_options;
$hover_type_options = array(
  'type1' => array('value' => 'type1','label' => __( 'Zoom in', 'tcd-serum' )),
  'type2' => array('value' => 'type2','label' => __( 'Zoom out', 'tcd-serum' )),
  'type3' => array('value' => 'type3','label' => __( 'Slide', 'tcd-serum' )),
  'type4' => array('value' => 'type4','label' => __( 'Fade', 'tcd-serum' )),
  'type5' => array('value' => 'type5','label' => __( 'No animation', 'tcd-serum' ))
);
global $hover3_direct_options;
$hover3_direct_options = array(
  'type2' => array('value' => 'type2','label' => __( 'Right to Left', 'tcd-serum' )),
  'type1' => array('value' => 'type1','label' => __( 'Left to Right', 'tcd-serum' )),
);


//フォントタイプ
global $font_type_options;
$font_type_options = array(
  'type1' => array('value' => 'type1','label' => __( 'Meiryo', 'tcd-serum' ),'label_en' => 'Arial'),
  'type2' => array('value' => 'type2','label' => __( 'YuGothic', 'tcd-serum' ),'label_en' => 'San Serif'),
  'type3' => array('value' => 'type3','label' => __( 'YuMincho', 'tcd-serum' ),'label_en' => 'Times New Roman')
);


// ヘッダーの固定設定
global $header_fix_options;
$header_fix_options = array(
  'type1' => array('value' => 'type1','label' => __( 'Normal position', 'tcd-serum' )),
  'type2' => array('value' => 'type2','label' => __( 'Fix at top after page scroll', 'tcd-serum' )),
);
// ヘッダーの固定設定
global $header_fix_options2;
$header_fix_options2 = array(
  'type1' => array('value' => 'type1','label' => __( 'Normal header', 'tcd-serum' )),
  'type2' => array('value' => 'type2','label' => __( 'Fix logo area at top after page scroll', 'tcd-serum' )),
  'type3' => array('value' => 'type3','label' => __( 'Fix global menu at top after page scroll', 'tcd-serum' )),
  'type4' => array('value' => 'type4','label' => __( 'Fix all header content at top after page scroll', 'tcd-serum' ))
);


// レイアウトの設定
global $layout_options;
$layout_options = array(
 'type0' => array('value' => 'type0','label' => __( 'Use theme option setting', 'tcd-serum' )),
 'type1' => array('value' => 'type1','label' => __( 'Don\'t display', 'tcd-serum' )),
 'type2' => array('value' => 'type2','label' => __( 'Display on right side', 'tcd-serum' )),
 'type3' => array('value' => 'type3','label' => __( 'Display on left side', 'tcd-serum' )),
);


// ソーシャルボタンの設定
global $sns_type_options;
$sns_type_options = array(
  'type1' => array( 'value' => 'type1', 'label' => __( 'Type1 (color)', 'tcd-serum' ), 'img' => 'share_type1.jpg'),
  'type2' => array( 'value' => 'type2', 'label' => __( 'Type2 (mono)', 'tcd-serum' ), 'img' => 'share_type2.jpg'),
  'type3' => array( 'value' => 'type3', 'label' => __( 'Type3 (4 column - color)', 'tcd-serum' ), 'img' => 'share_type3.jpg'),
  'type4' => array( 'value' => 'type4', 'label' => __( 'Type4 (4 column - mono)', 'tcd-serum' ), 'img' => 'share_type4.jpg'),
  'type5' => array( 'value' => 'type5', 'label' => __( 'Type5 (official design)', 'tcd-serum' ), 'img' => 'share_type5.jpg')
);


// 記事タイプ
global $post_type_options;
$post_type_options = array(
  'recent_post' => array('value' => 'recent_post','label' => __( 'All post', 'tcd-serum' )),
  'recommend_post' => array('value' => 'recommend_post','label' => __( 'Recommend post1', 'tcd-serum' )),
  'recommend_post2' => array('value' => 'recommend_post2','label' => __( 'Recommend post2', 'tcd-serum' )),
  'pickup_post' => array('value' => 'pickup_post','label' => __( 'Pickup post', 'tcd-serum' ))
);


// 記事の並び順
global $post_type_order_options;
$post_type_order_options = array(
  'date1' => array('value' => 'date1','label' => __( 'Date (DESC)', 'tcd-serum' )),
  'date2' => array('value' => 'date2','label' => __( 'Date (ASC)', 'tcd-serum' )),
  'rand' => array('value' => 'rand','label' => __( 'Random', 'tcd-serum' ))
);


// スライダーやロードアイコンで使用
global $time_options;
$time_options = array(
  '1000' => array('value' => '1000','label' => sprintf(__('%s second', 'tcd-serum'), 1)),
  '2000' => array('value' => '2000','label' => sprintf(__('%s second', 'tcd-serum'), 2)),
  '3000' => array('value' => '3000','label' => sprintf(__('%s second', 'tcd-serum'), 3)),
  '4000' => array('value' => '4000','label' => sprintf(__('%s second', 'tcd-serum'), 4)),
  '5000' => array('value' => '5000','label' => sprintf(__('%s second', 'tcd-serum'), 5)),
  '6000' => array('value' => '6000','label' => sprintf(__('%s second', 'tcd-serum'), 6)),
  '7000' => array('value' => '7000','label' => sprintf(__('%s second', 'tcd-serum'), 7)),
  '8000' => array('value' => '8000','label' => sprintf(__('%s second', 'tcd-serum'), 8)),
  '9000' => array('value' => '9000','label' => sprintf(__('%s second', 'tcd-serum'), 9)),
  '10000' => array('value' => '10000','label' => sprintf(__('%s second', 'tcd-serum'), 10)),
  '11000' => array('value' => '11000','label' => sprintf(__('%s second', 'tcd-serum'), 11)),
  '12000' => array('value' => '12000','label' => sprintf(__('%s second', 'tcd-serum'), 12)),
  '13000' => array('value' => '13000','label' => sprintf(__('%s second', 'tcd-serum'), 13)),
  '14000' => array('value' => '14000','label' => sprintf(__('%s second', 'tcd-serum'), 14)),
  '15000' => array('value' => '15000','label' => sprintf(__('%s second', 'tcd-serum'), 15))
);


// ロゴに画像を使うか否か
global $logo_type_options;
$logo_type_options = array(
  'type1' => array(
    'value' => 'type1',
    'label' => __( 'Use text for logo', 'tcd-serum' ),
    'image' => get_template_directory_uri() . '/admin/img/header_logo_type1.gif'
  ),
  'type2' => array(
    'value' => 'type2',
    'label' => __( 'Use image for logo', 'tcd-serum' ),
    'image' => get_template_directory_uri() . '/admin/img/header_logo_type2.gif'
  )
);


// Google Maps
global $gmap_marker_type_options;
$gmap_marker_type_options = array(
  'type1' => array( 'value' => 'type1', 'label' => __( 'Use default marker', 'tcd-serum' ), 'img' => 'gmap_marker_type1.jpg'),
  'type2' => array( 'value' => 'type2', 'label' => __( 'Use custom marker', 'tcd-serum' ), 'img' => 'gmap_marker_type2.jpg' )
);
global $gmap_custom_marker_type_options;
$gmap_custom_marker_type_options = array(
  'type1' => array( 'value' => 'type1', 'label' => __( 'Text', 'tcd-serum' ) ),
  'type2' => array( 'value' => 'type2', 'label' => __( 'Image', 'tcd-serum' ) )
);


// ページ分割ナビのタイプ
global $pagenation_type_options;
$pagenation_type_options = array(
  'type1' => array( 'value' => 'type1', 'label' => __( 'Page numbers', 'tcd-serum' ), 'img' => 'page_link_type1.jpg' ),
  'type2' => array( 'value' => 'type2', 'label' => __( 'Read more button', 'tcd-serum' ), 'img' => 'page_link_type2.jpg' )
);


// スライダーのアニメーション
global $slider_animation_options;
$slider_animation_options = array(
  'type1' => array('value' => 'type1','label' => __( 'Zoom out', 'tcd-serum' )),
  'type2' => array('value' => 'type2','label' => __( 'Zoom in', 'tcd-serum' )),
  'type3' => array('value' => 'type3','label' => __( 'Move right', 'tcd-serum' )),
  'type4' => array('value' => 'type4','label' => __( 'Move left', 'tcd-serum' )),
  'type5' => array('value' => 'type5','label' => __( 'Move top', 'tcd-serum' )),
  'type6' => array('value' => 'type6','label' => __( 'Move bottom', 'tcd-serum' )),
  'type7' => array('value' => 'type7','label' => __( 'No animation', 'tcd-serum' ))
);


// レイヤー画像のアニメーション
global $layer_image_animation_options;
$layer_image_animation_options = array(
  'type1' => array('value' => 'type1','label' => __( 'No animation', 'tcd-serum' )),
  'type2' => array('value' => 'type2','label' => __( 'Fade in', 'tcd-serum' )),
  'type3' => array('value' => 'type3','label' => __( 'Slide in', 'tcd-serum' )),
);


// コンテンツの方向
global $content_direction_options;
$content_direction_options = array(
 'type1' => array('value' => 'type1', 'label' => __( 'Align left', 'tcd-serum' )),
 'type2' => array('value' => 'type2', 'label' => __( 'Align center', 'tcd-serum' )),
 'type3' => array('value' => 'type3', 'label' => __( 'Align right', 'tcd-serum' ))
);
// コンテンツの方向（縦方向）
global $content_direction_options2;
$content_direction_options2 = array(
 'type1' => array('value' => 'type1', 'label' => __( 'Align top', 'tcd-serum' )),
 'type2' => array('value' => 'type2', 'label' => __( 'Align middle', 'tcd-serum' )),
 'type3' => array('value' => 'type3', 'label' => __( 'Align bottom', 'tcd-serum' ))
);


// アイテムのタイプ
global $item_type_options;
$item_type_options = array(
  'type1' => array('value' => 'type1','label' => __( 'Image', 'tcd-serum' )),
  'type2' => array('value' => 'type2','label' => __( 'Video', 'tcd-serum' )),
  'type3' => array('value' => 'type3','label' => __( 'Youtube', 'tcd-serum' )),
);


// スライダーのコンテンツタイプ
global $index_slider_content_type_options;
$index_slider_content_type_options = array(
  'type1' => array('value' => 'type1','label' => __( 'Same as PC setting', 'tcd-serum' )),
  'type2' => array('value' => 'type2','label' => __( 'Display diffrent content in mobile size', 'tcd-serum' )),
);


// スライダーのアイテムタイプ
global $slider_type_options;
$slider_type_options = array(
  'type1' => array('value' => 'type1','label' => __( 'Image', 'tcd-serum' )),
  'type2' => array('value' => 'type2','label' => __( 'Video', 'tcd-serum' )),
  'type3' => array('value' => 'type3','label' => __( 'Youtube', 'tcd-serum' )),
  'type4' => array('value' => 'type4','label' => __( 'Logo content', 'tcd-serum' ))
);


// メガメニュー
global $megamenu_options;
$megamenu_options = array(
  'type2' => array('value' => 'type2', 'title' => __( 'Mega menu A', 'tcd-serum' ), 'label' => __( 'Mega menu A', 'tcd-serum' ), 'img' => 'megamenu2.jpg'),
  'type3' => array('value' => 'type3', 'title' => __( 'Mega menu B', 'tcd-serum' ), 'label' => __( 'Mega menu B', 'tcd-serum' ), 'img' => 'megamenu3.jpg'),
  'type4' => array('value' => 'type4', 'title' => __( 'Mega menu C', 'tcd-serum' ), 'label' => __( 'Mega menu C', 'tcd-serum' ), 'img' => 'megamenu4.jpg'),
);


// パララックスの設定
global $para_options;
$para_options = array(
  'type1' => array('value' => 'type1', 'label' => __( 'Use parallax effect', 'tcd-serum' )),
  'type2' => array('value' => 'type2', 'label' => __( 'Don\'t use parallax effect', 'tcd-serum' ))
);


// クイックタグ カスタムボタンタイプ
global $qt_custom_button_type_options;
$qt_custom_button_type_options = array(
	'type1' => array(
		'value' => 'type1',
		'label' => __( 'Flat button', 'tcd-serum' )
	),
	'type2' => array(
		'value' => 'type2',
		'label' => __( 'Rounded button', 'tcd-serum' )
	),
	'type3' => array(
		'value' => 'type3',
		'label' => __( 'Oval button', 'tcd-serum' )
	)
);


// クイックタグ カスタムボタンサイズ
global $qt_custom_button_size_options;
$qt_custom_button_size_options = array(
	'type1' => array(
		'value' => 'type1',
		'label' => __( 'Small size button - Width:130px Height:40px', 'tcd-serum' )
	),
	'type2' => array(
		'value' => 'type2',
		'label' => __( 'Medium size button - Width:270px Height:60px', 'tcd-serum' )
	),
	'type3' => array(
		'value' => 'type3',
		'label' => __( 'Large size button - Width:400px Height:70px', 'tcd-serum' )
	)
);


// テキストの方向
global $text_align_options;
$text_align_options = array(
 'left' => array('value' => 'left', 'label' => __( 'Align left', 'tcd-serum' )),
 'center' => array('value' => 'center', 'label' => __( 'Align center', 'tcd-serum' )),
);


// テキストの方向2
global $text_direction_options;
$text_direction_options = array(
 'type1' => array('value' => 'type1', 'label' => __( 'Display serumtally', 'tcd-serum' )),
 'type2' => array('value' => 'type2', 'label' => __( 'Display vertically', 'tcd-serum' )),
);


// コンテンツの横幅
global $content_width_options;
$content_width_options = array(
  'type1' => array('value' => 'type1','label' => __( 'Normal content width', 'tcd-serum' )),
  'type2' => array('value' => 'type2','label' => __( 'Full screen width', 'tcd-serum' ))
);


// キャッチコピーのアニメーションのタイプ
global $catch_animation_type_options;
$catch_animation_type_options = array(
  'type1' => array('value' => 'type1','label' => __( 'Animate all words from bottom to upward', 'tcd-serum' )),
  'type2' => array('value' => 'type2','label' => __( 'Animate letter one by one', 'tcd-serum' )),
);


//記事一覧のアニメーションタイプ
global $post_list_animation_type_options;
$post_list_animation_type_options = array(
  'type1' => array('value' => 'type1','label' => __( 'Fade in', 'tcd-serum' )),
  'type2' => array('value' => 'type2','label' => __( 'Popup', 'tcd-serum' )),
  'type3' => array('value' => 'type3','label' => __( 'Slide up', 'tcd-serum' ))
);


// 表示設定
global $basic_display_options;
$basic_display_options = array(
	'display' => array(
		'value' => 'display',
		'label' => __( 'Display', 'tcd-serum' ),
	),
	'hide' => array(
		'value' => 'hide',
		'label' => __( 'Hide', 'tcd-serum' ),
	)
);




// クイックタグ関連 -------------------------------------------------------------------------------------------


// 見出し
global $font_weight_options;
$font_weight_options = array(
	'400' => array('value' => '400','label' => __( 'Normal', 'tcd-serum' )),
	'600' => array('value' => '600','label' => __( 'Bold', 'tcd-serum' ))
);
global $border_potition_options;
$border_potition_options = array(
	'left' => array('value' => 'left','label' => __( 'Left', 'tcd-serum' )),
	'top' => array('value' => 'top','label' => __( 'Top', 'tcd-serum' )),
	'bottom' => array('value' => 'bottom','label' => __( 'Bottom', 'tcd-serum' )),
	'right' => array('value' => 'right','label' => __( 'Right', 'tcd-serum' ))
);
global $border_style_options;
$border_style_options = array(
	'solid' => array('value' => 'solid','label' => __( 'Solid line', 'tcd-serum' )),
	'dotted' => array('value' => 'dotted','label' => __( 'Dot line', 'tcd-serum' )),
	'double' => array('value' => 'double','label' => __( 'Double line', 'tcd-serum' ))
);


// ボタン
global $button_type_options;
$button_type_options = array(
	'type1' => array('value' => 'type1','label' => __( 'Normal', 'tcd-serum' )),
	'type2' => array('value' => 'type2','label' => __( 'Ghost', 'tcd-serum' )),
	'type3' => array('value' => 'type3','label' => __( 'Reverse', 'tcd-serum' ))
);
global $button_border_radius_options;
$button_border_radius_options = array(
	'flat' => array('value' => 'flat','label' => __( 'Square', 'tcd-serum' )),
	'rounded' => array('value' => 'rounded','label' => __( 'Rounded', 'tcd-serum' )),
	'oval' => array('value' => 'oval','label' => __( 'Pill', 'tcd-serum' ))
);
global $button_size_options;
$button_size_options = array(
	'small' => array('value' => 'small','label' => __( 'Small', 'tcd-serum' )),
	'medium' => array('value' => 'medium','label' => __( 'Medium', 'tcd-serum' )),
	'large' => array('value' => 'large','label' => __( 'Large', 'tcd-serum' ))
);
global $button_animation_options;
$button_animation_options = array(
	'animation_type1' => array('value' => 'animation_type1','label' => __( 'Fade', 'tcd-serum' )),
	'animation_type2' => array('value' => 'animation_type2','label' => __( 'Swipe', 'tcd-serum' )),
	'animation_type3' => array('value' => 'animation_type3','label' => __( 'Diagonal swipe', 'tcd-serum' ))
);


// 囲み枠
global $flame_border_radius_options;
$flame_border_radius_options = array(
	'0' => array('value' => '0','label' => __( 'Square', 'tcd-serum' )),
	'10' => array('value' => '10','label' => __( 'Rounded', 'tcd-serum' ))
);


// アンダーライン
global $bool_options;
$bool_options = array(
	'yes' => array('value' => 'yes','label' => __( 'Yes', 'tcd-serum' )),
	'no' => array('value' => 'no','label' => __( 'No', 'tcd-serum' ))
);


// Google Map
global $google_map_design_options;
$google_map_design_options = array(
	'default' => array('value' => 'default','label' => __( 'Default', 'tcd-serum' )),
	'monochrome' => array('value' => 'monochrome','label' => __( 'Monochrome', 'tcd-serum' ))
);
global $google_map_marker_options;
$google_map_marker_options = array(
	'type1' => array('value' => 'type1','label' => __( 'Default', 'tcd-serum' )),
	'type2' => array('value' => 'type2','label' => __( 'Text', 'tcd-serum' )),
	'type3' => array('value' => 'type3','label' => __( 'Image', 'tcd-serum' ))
);



// ロード画面関連 -------------------------------------------------------------------------------------------


// ローディングアイコンの種類の設定
global $loading_type;
$loading_type = array(
	'type1' => array(
		'value' => 'type1',
		'label' => __( 'Circle icon', 'tcd-serum' ),
		'image' => get_template_directory_uri() . '/admin/img/load_smaple.jpg'
	),
	'type2' => array(
		'value' => 'type2',
		'label' => __( 'Square icon', 'tcd-serum' ),
		'image' => get_template_directory_uri() . '/admin/img/load_smaple.jpg'
	),
	'type3' => array(
		'value' => 'type3',
		'label' => __( 'Dot circle icon', 'tcd-serum' ),
		'image' => get_template_directory_uri() . '/admin/img/load_smaple.jpg'
	),
	'type4' => array(
		'value' => 'type4',
		'label' => __( 'Logo', 'tcd-serum' ),
		'image' => get_template_directory_uri() . '/admin/img/load_smaple.jpg'
	),
	'type5' => array(
		'value' => 'type5',
		'label' => __( 'Catchphrase', 'tcd-serum' ),
		'image' => get_template_directory_uri() . '/admin/img/load_smaple.jpg'
	)
);


global $loading_display_page_options;
$loading_display_page_options = array(
 'type1' => array('value' => 'type1','label' => __( 'Front page', 'tcd-serum' )),
 'type2' => array('value' => 'type2','label' => __( 'All pages', 'tcd-serum' ))
);


global $loading_display_time_options;
$loading_display_time_options = array(
 'type1' => array('value' => 'type1','label' => __( 'Only once', 'tcd-serum' )),
 'type2' => array('value' => 'type2','label' => __( 'Every time', 'tcd-serum' ))
);


// ドロワーメニュー
global $drawer_menu_color_type_options;
$drawer_menu_color_type_options = array(
	'dark' => array(
		'value' => 'dark',
		'label' => __( 'Dark color', 'tcd-serum' ),
		'image' => get_template_directory_uri() . '/admin/img/drawer_menu_color_type1.jpg?ver2'
	),
	'light' => array(
		'value' => 'light',
		'label' => __( 'Light color', 'tcd-serum' ),
		'image' => get_template_directory_uri() . '/admin/img/drawer_menu_color_type2.jpg?ver2'
	)
);


// フッター関連 -------------------------------------------------------------------------------------------
global $footer_bar_type_options;
$footer_bar_type_options = array(
	'type1' => array(
		'value' => 'type1',
		'label' => __( 'Hide', 'tcd-serum' ),
		'image' => get_template_directory_uri() . '/admin/img/footer_bar_type1.jpg'
	),
	'type2' => array(
		'value' => 'type2',
		'label' => __( 'Button with icon (Dark color)', 'tcd-serum' ),
		'image' => get_template_directory_uri() . '/admin/img/footer_bar_type2.jpg'
	),
	'type3' => array(
		'value' => 'type3',
		'label' => __( 'Button with icon (Light color)', 'tcd-serum' ),
		'image' => get_template_directory_uri() . '/admin/img/footer_bar_type3.jpg'
	),
	'type4' => array(
		'value' => 'type4',
		'label' => __( 'Button without icon', 'tcd-serum' ),
		'image' => get_template_directory_uri() . '/admin/img/footer_bar_type4.jpg'
	)
);


// フッターの固定メニュー ボタンのタイプ
global $footer_bar_button_options;
$footer_bar_button_options = array(
  'type1' => array('value' => 'type1', 'label' => __( 'Default', 'tcd-serum' )),
  'type2' => array('value' => 'type2', 'label' => __( 'Share', 'tcd-serum' )),
  'type3' => array('value' => 'type3', 'label' => __( 'Telephone', 'tcd-serum' ))
);

// フッターの固定メニューのアイコン
global $footer_bar_icon_options;
$footer_bar_icon_options = array(
  'twitter' => array('value' => 'twitter'),
  'facebook' => array('value' => 'facebook'),
  'instagram' => array('value' => 'instagram'),
  'youtube' => array('value' => 'youtube'),
	'tiktok' => array('value' => 'tiktok'),
  'line' => array('value' => 'line'),
  'heart' => array('value' => 'heart'),
  'star1' => array('value' => 'star1'),
  'list2' => array('value' => 'list2'),
  'fire' => array('value' => 'fire'),
  'bubble' => array('value' => 'bubble'),
  'bell' => array('value' => 'bell'),
  'cart' => array('value' => 'cart'),
  'user' => array('value' => 'user'),
  'map' => array('value' => 'map'),
  'film' => array('value' => 'film'),
  'camera' => array('value' => 'camera'),
  'news' => array('value' => 'news'),
  'office' => array('value' => 'office'),
  'home' => array('value' => 'home'),
  'help' => array('value' => 'help'),
  'light' => array('value' => 'light'),
  'menu' => array('value' => 'menu'),
  'grid' => array('value' => 'grid'),
  'search' => array('value' => 'search'),
  'tel' => array('value' => 'tel'),
  'calendar' => array('value' => 'calendar'),
  'mail' => array('value' => 'mail'),
  'pdf' => array('value' => 'pdf'),
  'pencil' => array('value' => 'pencil'),
  'clock' => array('value' => 'clock'),
  'crown' => array('value' => 'crown'),
  'share' => array('value' => 'share'),
);


// CTA
global $cta_options;
$cta_options = array(
  'type1' => array(
    'value' => 'type1',
    'label' => __( 'One large banner', 'tcd-serum' ),
    'image' => get_template_directory_uri() . '/admin/img/cta_image1.jpg'
  ),
  'type2' => array(
    'value' => 'type2',
    'label' => __( 'Two small banner', 'tcd-serum' ),
    'image' => get_template_directory_uri() . '/admin/img/cta_image2.jpg'
  )
);



?>