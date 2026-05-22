<?php
/*
 * 基本設定
 */


// Add default values
add_filter( 'before_getting_design_plus_option', 'add_basic_dp_default_options' );


// Add label of basic tab
add_action( 'tcd_tab_labels', 'add_basic_tab_label' );


// Add HTML of basic tab
add_action( 'tcd_tab_panel', 'add_basic_tab_panel' );


// Register sanitize function
add_filter( 'theme_options_validate', 'add_basic_theme_options_validate' );


// タブの名前
function add_basic_tab_label( $tab_labels ) {
	$tab_labels['basic'] = __( 'Basic setting', 'tcd-serum' );
	return $tab_labels;
}


// 初期値
function add_basic_dp_default_options( $dp_default_options ) {

	// 色の設定
	$dp_default_options['main_color'] = '#937960';
	$dp_default_options['bg_color'] = '#f4f1ef';
	$dp_default_options['content_link_color'] = '#1578d6';

	// フォントの種類
	$dp_default_options['content_font_type'] = 'type2';
	$dp_default_options['content_font_size'] = '16';
	$dp_default_options['content_font_size_sp'] = '14';
	$dp_default_options['page_header_font_type'] = 'type2';
	$dp_default_options['page_header_font_size'] = '34';
	$dp_default_options['page_header_font_size_sp'] = '24';
	$dp_default_options['catch_font_type'] = 'type2';
	$dp_default_options['catch_font_size'] = '32';
	$dp_default_options['catch_font_size_sp'] = '20';
	$dp_default_options['single_title_font_type'] = 'type2';
	$dp_default_options['single_title_font_size'] = '28';
	$dp_default_options['single_title_font_size_sp'] = '20';

	// ボタン
	$dp_default_options['design_button_type'] = 'type2';
	$dp_default_options['design_button_border_radius'] = 'square';
	$dp_default_options['design_button_size'] = 'medium';
	$dp_default_options['design_button_animation_type'] = 'animation_type1';
	$dp_default_options['design_button_color'] = '#937960';
	$dp_default_options['design_button_color_hover'] = '#7c6552';

	// アニメーションの設定
	$dp_default_options['hover_type'] = 'type1';
	$dp_default_options['hover1_zoom'] = '1.2';
	$dp_default_options['hover2_zoom'] = '1.2';
	$dp_default_options['hover3_direct'] = 'type1';
	$dp_default_options['hover3_opacity'] = '0.5';
	$dp_default_options['hover3_bgcolor'] = '#FFFFFF';
	$dp_default_options['hover4_opacity'] = '0.5';
	$dp_default_options['hover4_bgcolor'] = '#FFFFFF';

	$dp_default_options['no_image'] = false;

	// オリジナルスクリプトの設定
	$dp_default_options['script_code'] = '';
	$dp_default_options['footer_script_code'] = '';
	$dp_default_options['css_code'] = '';

  // ロードアイコンの設定ここから
  $dp_default_options['show_loading'] = '';
  $dp_default_options['loading_type'] = 'type1';
  $dp_default_options['loading_icon_color'] = '#000000';
  $dp_default_options['loading_bg_color'] = '#ffffff';

  // ロードアイコンの設定ここまで

	// ソーシャルシェアボタン
	$dp_default_options['sns_share_design_type'] = 'type1';
	$dp_default_options['show_sns_share_twitter'] = 1;
	$dp_default_options['show_sns_share_fblike'] = 1;
	$dp_default_options['show_sns_share_fbshare'] = 1;
	$dp_default_options['show_sns_share_hatena'] = 1;
	$dp_default_options['show_sns_share_pocket'] = 1;
	$dp_default_options['show_sns_share_feedly'] = 1;
	$dp_default_options['show_sns_share_rss'] = 1;
	$dp_default_options['show_sns_share_pinterest'] = 1;
	$dp_default_options['twitter_info'] = '';

  // ソーシャルボタン
	$dp_default_options['sns_button_color_type'] = 'type1';
	$dp_default_options['sns_button_facebook_url'] = '';
	$dp_default_options['sns_button_twitter_url'] = '';
	$dp_default_options['sns_button_instagram_url'] = '';
	$dp_default_options['sns_button_pinterest_url'] = '';
	$dp_default_options['sns_button_youtube_url'] = '';
	$dp_default_options['sns_button_contact_url'] = '';
	$dp_default_options['sns_button_show_rss'] = 1;
	$dp_default_options['sns_button_tiktok_url'] = '';

	$dp_default_options['show_sns_footer'] = 'display';

  // 検索の対象
	$dp_default_options['search_type_post'] = 'yes';
	$dp_default_options['search_type_page'] = 'no';
	$dp_default_options['search_type_news'] = 'no';
	$dp_default_options['search_type_treatment'] = 'no';

	$dp_default_options['search_result_bg_image'] = false;
	$dp_default_options['search_result_headline'] = __( 'Search result', 'tcd-serum' );
	$dp_default_options['search_result_desc'] = __( 'The page you are looking for are not found', 'tcd-serum' );
	$dp_default_options['search_result_overlay_color'] = '#000000';
	$dp_default_options['search_result_overlay_opacity'] = '0.5';
	$dp_default_options['search_result_placeholder'] = __( 'Enter search keyword', 'tcd-serum' );;

	// 404 ページ
	$dp_default_options['page_404_bg_image'] = false;
	$dp_default_options['page_404_catch'] = '404 NOT FOUND';
	$dp_default_options['page_404_desc'] = __( 'The page you are looking for are not found', 'tcd-serum' );
	$dp_default_options['page_404_overlay_color'] = '#000000';
	$dp_default_options['page_404_overlay_opacity'] = '0.5';


  //ヘッダーロゴ
	$dp_default_options['header_logo_type'] = 'type1';
	$dp_default_options['header_logo_font_size'] = '26';
	$dp_default_options['header_logo_font_size_sp'] = '20';
	$dp_default_options['header_logo_font_type'] = 'type2';
	$dp_default_options['header_logo_image'] = false;
	$dp_default_options['header_logo_retina'] = 'no';
	$dp_default_options['header_logo_image_mobile'] = false;

	$dp_default_options['header_logo_show_icon_image'] = 'no';

	$dp_default_options['header_logo_image2'] = false;
	$dp_default_options['header_logo_image_mobile2'] = false;

	$dp_default_options['header_logo_image3'] = false;
	$dp_default_options['header_logo_image_mobile3'] = false;

	return $dp_default_options;

}


// 入力欄の出力　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_basic_tab_panel( $options ) {

  global $blog_label, $dp_default_options, $font_type_options, $hover_type_options, $hover3_direct_options, $sns_type_options, $footer_bar_icon_options, $bool_options, $basic_display_options,
         $button_type_options, $button_border_radius_options, $button_size_options, $button_animation_options,
         $loading_type, $loading_display_page_options, $loading_display_time_options, $time_options, $logo_type_options, $font_direction_options, $splash_options;

  $news_label = $options['news_label'] ? esc_html( $options['news_label'] ) : __( 'News', 'tcd-serum' );
  $treatment_label = $options['treatment_label'] ? esc_html( $options['treatment_label'] ) : __( 'treatment', 'tcd-serum' );

?>

<div id="tab-content-basic" class="tab-content">

   <?php // ヘッダーのロゴの設定 ----------------------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Logo', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <h4 class="theme_option_headline2"><?php _e('Type of logo', 'tcd-serum');  ?></h4>
     <?php echo tcd_admin_image_radio_button($options, 'header_logo_type', $logo_type_options) ?>

     <div id="header_logo_type1_area">

      <h4 class="theme_option_headline2"><?php _e('Font size', 'tcd-serum');  ?></h4>
      <ul class="option_list">
       <li class="cf"><span class="label"><?php echo tcd_admin_label('font_type'); ?></span><?php echo tcd_basic_radio_button($options, 'header_logo_font_type', $font_type_options); ?></li>
       <li class="cf"><span class="label"><?php _e('Font size', 'tcd-serum'); ?></span><?php echo tcd_font_size_option($options, 'header_logo_font_size'); ?></li>
      </ul>

     </div>

     <div id="header_logo_type2_area">

      <div class="theme_option_message2" style="margin-top:10px;">
        <p><?php printf( __( 'TCD demo site use these sizes of the logo images for Retina Display.<br>PC: Width: %dpx, Height: %dpx<br>Mobile: Width: %dpx, Height: %dpx', 'tcd-serum' ), 230, 74, 172, 56 ); ?></p>
       <p><?php _e('In order to avoid blurring images on retina displays, it is necessary to register an image that is at least twice the size that is actually displayed. Read <a href="https://tcd-theme.com/2019/04/retina-display.html" target="_blank">TCD article</a> for more details.','tcd-serum'); ?></p>
      </div>
      <ul class="option_list">
       <li class="cf"><span class="label"><?php _e('Use retina display image', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'header_logo_retina', $bool_options); ?></li>
      </ul>

      <h4 class="theme_option_headline2"><?php _e('Header bar logo', 'tcd-serum');  ?></h4>
      <div class="front_page_image middle">
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/header_logo_image.jpg" alt="" title="" />
      </div>
      <ul class="option_list">
       <li class="cf">
        <span class="label"><?php _e('Image', 'tcd-serum'); ?></span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js header_logo_image2">
          <input type="hidden" value="<?php echo esc_attr( $options['header_logo_image2'] ); ?>" id="header_logo_image2" name="dp_options[header_logo_image2]" class="cf_media_id">
          <div class="preview_field"><?php if($options['header_logo_image2']){ echo wp_get_attachment_image($options['header_logo_image2'], 'full'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['header_logo_image2']){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
       <li class="cf">
        <span class="label"><?php _e('Image (mobile)', 'tcd-serum'); ?></span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js header_logo_image_mobile2">
          <input type="hidden" value="<?php echo esc_attr( $options['header_logo_image_mobile2'] ); ?>" id="header_logo_image_mobile2" name="dp_options[header_logo_image_mobile2]" class="cf_media_id">
          <div class="preview_field"><?php if($options['header_logo_image_mobile2']){ echo wp_get_attachment_image($options['header_logo_image_mobile2'], 'full'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['header_logo_image_mobile2']){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
       <li class="cf"><span class="label"><?php _e('Display icon image', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'header_logo_show_icon_image', $bool_options); ?></li>
       <li class="cf header_logo_icon_option" style="border:none;">
        <div class="front_page_image middle" style="margin-top:10px;">
         <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/header_icon_image.jpg" alt="" title="" />
        </div>
        <span class="label"><?php _e('Icon image', 'tcd-serum'); ?><span class="recommend_desc"><?php _e('Main color will be applyed for background color, so please upload transparent image.', 'tcd-serum'); ?><br><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '40', '40'); ?></span></span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js header_logo_image3">
          <input type="hidden" value="<?php echo esc_attr( $options['header_logo_image3'] ); ?>" id="header_logo_image3" name="dp_options[header_logo_image3]" class="cf_media_id">
          <div class="preview_field"><?php if($options['header_logo_image3']){ echo wp_get_attachment_image($options['header_logo_image3'], 'full'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['header_logo_image3']){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
       <li class="cf header_logo_icon_option">
        <span class="label"><?php _e('Icon image (mobile)', 'tcd-serum'); ?><span class="recommend_desc"><?php _e('White color will be applyed for background color, so please upload colored image.', 'tcd-serum'); ?><br><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '35', '35'); ?></span></span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js header_logo_image_mobile3">
          <input type="hidden" value="<?php echo esc_attr( $options['header_logo_image_mobile3'] ); ?>" id="header_logo_image_mobile3" name="dp_options[header_logo_image_mobile3]" class="cf_media_id">
          <div class="preview_field"><?php if($options['header_logo_image_mobile3']){ echo wp_get_attachment_image($options['header_logo_image_mobile3'], 'full'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['header_logo_image_mobile3']){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
      </ul>

      <h4 class="theme_option_headline2"><?php _e('Square logo', 'tcd-serum');  ?></h4>
      <div class="front_page_image middle">
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/header_square_image.jpg" alt="" title="" />
      </div>
      <div class="theme_option_message2">
      <p><?php printf( __( 'TCD demo site use [Width: %dpx, Height: %dpx] sizes of the logo image for Retina Display.', 'tcd-serum' ), 232, 232 ); ?></p>
       <p><?php _e('This logo will be displayed as follows.<br>Header before page scrolling (Front page/archive page) - It takes precedence over the header bar logo.<br>Left side of footer when desktop size','tcd-serum'); ?></p>
      </div>
      <ul class="option_list">
       <li class="cf">
        <span class="label"><?php _e('Image', 'tcd-serum'); ?></span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js header_logo_image">
          <input type="hidden" value="<?php echo esc_attr( $options['header_logo_image'] ); ?>" id="header_logo_image" name="dp_options[header_logo_image]" class="cf_media_id">
          <div class="preview_field"><?php if($options['header_logo_image']){ echo wp_get_attachment_image($options['header_logo_image'], 'full'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['header_logo_image']){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
       <li class="cf">
        <span class="label"><?php _e('Image (mobile)', 'tcd-serum'); ?></span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js header_logo_image_mobile">
          <input type="hidden" value="<?php echo esc_attr( $options['header_logo_image_mobile'] ); ?>" id="header_logo_image_mobile" name="dp_options[header_logo_image_mobile]" class="cf_media_id">
          <div class="preview_field"><?php if($options['header_logo_image_mobile']){ echo wp_get_attachment_image($options['header_logo_image_mobile'], 'full'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['header_logo_image_mobile']){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
      </ul>

     </div>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // ロード画面の設定 ----------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Loading screen', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <input id="show_loading" class="show_checkbox" name="dp_options[show_loading]" type="checkbox" value="1" <?php checked( $options['show_loading'], 1 ); ?>>
     <label for="show_loading"><?php _e( 'Display loading screen', 'tcd-serum' ); ?></label>

     <div class="show_checkbox_area">

      <div class="theme_option_message2" style="margin-top:20px;">
       <p><?php _e('You can set the load screen displayed during page transition.', 'tcd-serum');  ?></p>
      </div>

      <input class="tcd_admin_image_radio_button" id="loading_type1" type="radio" name="dp_options[loading_type]" value="type1" <?php checked( $options['loading_type'], 'type1' ); ?>>
      <label for="loading_type1">
       <div class="loading_icon_wrap">
        <div class="circular_loader">
         <svg class="circular" viewBox="25 25 50 50">
          <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
         </svg>
        </div>
       </div>
       <span class="title_wrap"><span class="title"><?php _e( 'Circle icon', 'tcd-serum' ); ?></span></span>
      </label>
      <input class="tcd_admin_image_radio_button" id="loading_type2" type="radio" name="dp_options[loading_type]" value="type2" <?php checked( $options['loading_type'], 'type2' ); ?>>
      <label for="loading_type2">
       <div class="loading_icon_wrap">
        <div class="sk-cube-grid">
         <div class="sk-cube sk-cube1"></div><div class="sk-cube sk-cube2"></div><div class="sk-cube sk-cube3"></div><div class="sk-cube sk-cube4"></div><div class="sk-cube sk-cube5"></div><div class="sk-cube sk-cube6"></div><div class="sk-cube sk-cube7"></div><div class="sk-cube sk-cube8"></div><div class="sk-cube sk-cube9"></div></div>
       </div>
       <span class="title_wrap"><span class="title"><?php _e( 'Square icon', 'tcd-serum' ); ?></span></span>
      </label>
      <input class="tcd_admin_image_radio_button" id="loading_type3" type="radio" name="dp_options[loading_type]" value="type3" <?php checked( $options['loading_type'], 'type3' ); ?>>
      <label for="loading_type3">
       <div class="loading_icon_wrap">
        <div class="sk-circle">
         <div class="sk-circle1 sk-child"></div><div class="sk-circle2 sk-child"></div><div class="sk-circle3 sk-child"></div><div class="sk-circle4 sk-child"></div><div class="sk-circle5 sk-child"></div><div class="sk-circle6 sk-child"></div><div class="sk-circle7 sk-child"></div><div class="sk-circle8 sk-child"></div><div class="sk-circle9 sk-child"></div><div class="sk-circle10 sk-child"></div><div class="sk-circle11 sk-child"></div><div class="sk-circle12 sk-child"></div>
        </div>
       </div>
       <span class="title_wrap"><span class="title"><?php _e( 'Dot circle icon', 'tcd-serum' ); ?></span></span>
      </label>

      <?php // type1 - type3 ----------------------------------------- ?>
      <div id="loading_type1-3_area">
       <h4 class="theme_option_headline2"><?php _e('Color', 'tcd-serum');  ?></h4>
       <ul class="option_list">
        <li class="cf"><span class="label"><?php _e('Icon', 'tcd-serum');  ?></span><input type="text" name="dp_options[loading_icon_color]" value="<?php echo esc_attr( $options['loading_icon_color'] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
        <li class="cf"><span class="label"><?php _e('Background', 'tcd-serum');  ?></span><input type="text" name="dp_options[loading_bg_color]" value="<?php echo esc_attr( $options['loading_bg_color'] ); ?>" data-default-color="#ffffff" class="c-color-picker"></li>
       </ul>
      </div>


     </div><!-- END .show_checkbox_area -->

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo tcd_admin_label('save'); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo tcd_admin_label('close'); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // 色の設定 ----------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Color', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <ul class="option_list">
      <li class="cf"><span class="label"><?php _e('Accent color', 'tcd-serum'); ?></span><input type="text" name="dp_options[main_color]" value="<?php echo esc_attr( $options['main_color'] ); ?>" data-default-color="#937960" class="c-color-picker"></li>
      <li class="cf">
       <span class="label">
        <?php _e('Background color', 'tcd-serum'); ?>
        <span class="recommend_desc"><?php _e('This color will be applied to content has background color (e.g. top page content builder)', 'tcd-serum'); ?></span>
       </span>
       <input type="text" name="dp_options[bg_color]" value="<?php echo esc_attr( $options['bg_color'] ); ?>" data-default-color="#f4f1ef" class="c-color-picker">
      </li>
      <li class="cf"><span class="label"><?php _e('Single page text link color', 'tcd-serum'); ?></span><input type="text" name="dp_options[content_link_color]" value="<?php echo esc_attr( $options['content_link_color'] ); ?>" data-default-color="#1578d6" class="c-color-picker"></li>
     </ul>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // フォントの種類 ----------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Font', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <div class="option_with_image_preview">
      <div class="image_area">
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/font_image_page_header.jpg" alt="" title="" />
      </div>
      <div class="option_area">
       <h4 class="theme_option_headline2"><?php _e('Page header', 'tcd-serum'); ?></h4>
       <div class="theme_option_message2">
        <p><?php _e('This settings will be applied to page header.', 'tcd-serum'); ?></p>
       </div>
       <ul class="option_list">
        <li class="cf"><span class="label"><?php _e('Font type', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'page_header_font_type', $font_type_options); ?></li>
        <li class="cf"><span class="label"><?php _e('Font size', 'tcd-serum'); ?></span><?php echo tcd_font_size_option($options, 'page_header_font_size'); ?></li>
       </ul>
      </div>
     </div>

     <div class="option_with_image_preview">
      <div class="image_area">
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/font_image_headline.jpg" alt="" title="" />
      </div>
      <div class="option_area">
       <h4 class="theme_option_headline2"><?php _e('Catchphrase / Headline', 'tcd-serum'); ?></h4>
       <div class="theme_option_message2">
        <p><?php _e('This settings will be applied to catchphrase and headline.', 'tcd-serum'); ?></p>
       </div>
       <ul class="option_list">
        <li class="cf"><span class="label"><?php _e('Font type', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'catch_font_type', $font_type_options); ?></li>
        <li class="cf"><span class="label"><?php _e('Font size', 'tcd-serum'); ?></span><?php echo tcd_font_size_option($options, 'catch_font_size'); ?></li>
       </ul>
      </div>
     </div>

     <div class="option_with_image_preview">
      <div class="image_area">
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/font_image_post_title.jpg" alt="" title="" />
      </div>
      <div class="option_area">
       <h4 class="theme_option_headline2"><?php _e('Post title', 'tcd-serum'); ?></h4>
       <div class="theme_option_message2">
        <p><?php _e('This settings will be applied to single page post title.', 'tcd-serum'); ?></p>
       </div>
       <ul class="option_list">
        <li class="cf"><span class="label"><?php _e('Font type', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'single_title_font_type', $font_type_options); ?></li>
        <li class="cf"><span class="label"><?php _e('Font size', 'tcd-serum'); ?></span><?php echo tcd_font_size_option($options, 'single_title_font_size'); ?></li>
       </ul>
      </div>
     </div>

     <div class="option_with_image_preview">
      <div class="image_area">
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/font_image_post_content.jpg" alt="" title="" />
      </div>
      <div class="option_area">
       <h4 class="theme_option_headline2"><?php _e('Post content', 'tcd-serum'); ?></h4>
       <div class="theme_option_message2">
        <p><?php _e( 'This setting will be used in post contents and descriptions.', 'tcd-serum' ); ?></p>
       </div>
       <ul class="option_list">
        <li class="cf"><span class="label"><?php _e('Font type', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'content_font_type', $font_type_options); ?></li>
        <li class="cf"><span class="label"><?php _e('Font size', 'tcd-serum'); ?></span><?php echo tcd_font_size_option($options, 'content_font_size'); ?></li>
       </ul>
      </div>
     </div>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // ボタンの設定 ----------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Button', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content qt-btn-preview-wrapper">

     <div class="theme_option_message2">
      <p><?php _e('This settings will be applied to front page contents builder button.', 'tcd-serum'); ?></p>
     </div>

     <div class="admin_preview_area qt-btn-preview">
      <div class="qt_button_wrap">
       <div class="qt_button type1"><span class="text"><?php _e('Normal', 'tcd-serum'); ?></span><span class="background"></span></div>
       <div class="qt_button type2"><span class="text"><?php _e('Ghost', 'tcd-serum'); ?></span><span class="background"></span></div>
       <div class="qt_button type3"><span class="text"><?php _e('Reverse', 'tcd-serum'); ?></span><span class="background"></span></div>
      </div>
     </div>

     <ul class="option_list">
      <li class="cf"><span class="label"><?php _e('Type', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'design_button_type', $button_type_options); ?></li>
      <li class="cf"><span class="label"><?php _e('Shape', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'design_button_border_radius', $button_border_radius_options); ?></li>
      <li class="cf"><span class="label"><?php _e('Size', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'design_button_size', $button_size_options); ?></li>
      <li class="cf"><span class="label"><?php _e('Mouseover animation', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'design_button_animation_type', $button_animation_options); ?></li>
      <li class="cf"><span class="label"><?php _e('Color scheme', 'tcd-serum'); ?></span><input type="text" name="dp_options[design_button_color]" value="<?php echo esc_attr( $options['design_button_color'] ); ?>" data-default-color="#937960" class="c-color-picker"></li>
      <li class="cf"><span class="label"><?php _e('Color scheme on mouseover', 'tcd-serum'); ?></span><input type="text" name="dp_options[design_button_color_hover]" value="<?php echo esc_attr( $options['design_button_color_hover'] ); ?>" data-default-color="#7c6552" class="c-color-picker"></li>
     </ul>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // ホバーアニメーション ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Featured image', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <?php // 代替画像 ------------------------ ?>
     <h4 class="theme_option_headline2"><?php _e('Alternate image to be displayed when featured image is not registered', 'tcd-serum');  ?></h4>
     <div class="theme_option_message2">
      <p><?php _e('If you set image here, you can display alternative image for article which featured image is not set.', 'tcd-serum');  ?></p>
      <p><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '830', '480'); ?></p>
     </div>
     <div class="image_box cf">
      <div class="cf cf_media_field hide-if-no-js no_image">
       <input type="hidden" value="<?php echo esc_attr( $options['no_image'] ); ?>" id="blog_no_image" name="dp_options[no_image]" class="cf_media_id">
       <div class="preview_field"><?php if($options['no_image']){ echo wp_get_attachment_image($options['no_image'], 'medium'); }; ?></div>
       <div class="buttton_area">
        <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
        <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['no_image']){ echo 'hidden'; }; ?>">
       </div>
      </div>
     </div>

     <h4 class="theme_option_headline2"><?php _e('Hover effect type', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2">
      <p><?php _e('You can select the featured image hover effect from 4 types.', 'tcd-serum'); ?></p>
     </div>
     <ul class="design_radio_button">
      <?php foreach ( $hover_type_options as $option ) { ?>
      <li>
       <input type="radio" id="hover_type_<?php esc_attr_e( $option['value'] ); ?>" name="dp_options[hover_type]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php checked( $options['hover_type'], $option['value'] ); ?> />
       <label for="hover_type_<?php esc_attr_e( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></label>
      </li>
      <?php } ?>
     </ul>

     <div id="hover_type1_area" style="<?php if($options['hover_type'] == 'type1'){ echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
      <h4 class="theme_option_headline2"><?php _e('Zoom in', 'tcd-serum'); ?></h4>
      <div class="theme_option_message2">
       <p><?php _e('Please enter a value between 1 (same size) and 1.5.<br>There is no limit to the number you can enter, but we recommend a maximum of 1.5.', 'tcd-serum'); ?></p>
      </div>
      <ul class="option_list">
       <li class="cf"><span class="label"><?php _e('Magnification rate', 'tcd-serum'); ?></span><input class="hankaku" style="width:70px;" type="number" max="10" min="1" step="0.1" name="dp_options[hover1_zoom]" value="<?php esc_attr_e( $options['hover1_zoom'] ); ?>" /></li>
      </ul>
     </div>

     <div id="hover_type2_area" style="<?php if($options['hover_type'] == 'type2'){ echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
      <h4 class="theme_option_headline2"><?php _e('Zoom out', 'tcd-serum'); ?></h4>
      <div class="theme_option_message2">
       <p><?php _e('Please enter a value between 1 (same size) and 1.5.<br>There is no limit to the number you can enter, but we recommend a maximum of 1.5.', 'tcd-serum'); ?></p>
      </div>
      <ul class="option_list">
       <li class="cf"><span class="label"><?php _e('Reduction rate', 'tcd-serum'); ?></span><input class="hankaku" style="width:70px;" type="number" max="10" min="1" step="0.1" name="dp_options[hover2_zoom]" value="<?php esc_attr_e( $options['hover2_zoom'] ); ?>" /></li>
      </ul>
     </div>

     <div id="hover_type3_area" style="<?php if($options['hover_type'] == 'type3'){ echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
      <h4 class="theme_option_headline2"><?php _e('Slide', 'tcd-serum'); ?></h4>
      <ul class="option_list">
       <li class="cf"><span class="label"><?php _e('Direction', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'hover3_direct', $hover3_direct_options); ?></li>
       <li class="cf"><span class="label"><?php _e('Background color', 'tcd-serum'); ?></span><input type="text" name="dp_options[hover3_bgcolor]" value="<?php echo esc_attr( $options['hover3_bgcolor'] ); ?>" data-default-color="#ffffff" class="c-color-picker"></li>
       <li class="cf">
        <span class="label"><?php _e('Opacity of background color', 'tcd-serum'); ?></span>
        <input class="hankaku" style="width:70px;" type="number" max="1" min="0" step="0.1" name="dp_options[hover3_opacity]" value="<?php esc_attr_e( $options['hover3_opacity'] ); ?>" />
        <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
         <p><?php _e('Please specify the number of 0 from 1.0. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?></p>
        </div>
       </li>
      </ul>
     </div>

     <div id="hover_type4_area" style="<?php if($options['hover_type'] == 'type4'){ echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
      <h4 class="theme_option_headline2"><?php _e('Fade', 'tcd-serum'); ?></h4>
      <ul class="option_list">
       <li class="cf"><span class="label"><?php _e('Background color', 'tcd-serum'); ?></span><input type="text" name="dp_options[hover4_bgcolor]" value="<?php echo esc_attr( $options['hover4_bgcolor'] ); ?>" data-default-color="#ffffff" class="c-color-picker"></li>
       <li class="cf">
        <span class="label"><?php _e('Opacity of background color', 'tcd-serum'); ?></span>
        <input class="hankaku" style="width:70px;" type="number" max="1" min="0" step="0.1" name="dp_options[hover4_opacity]" value="<?php esc_attr_e( $options['hover4_opacity'] ); ?>" />
        <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
         <p><?php _e('Please specify the number of 0 from 1.0. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?></p>
        </div>
       </li>
      </ul>
     </div>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // SNSボタン  ------------------------------------------------------------------ ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('SNS', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <?php // ソーシャルシェアボタン ------------------------------------------------------------------- ?>
     <div class="sub_box cf"> 
      <h3 class="theme_option_subbox_headline"><?php _e('Share button', 'tcd-serum');  ?></h3>
      <div class="sub_box_content">

       <div class="theme_option_message2" style="margin-top:20px;">
        <p><?php printf(__('Share button will be displayed in blog article and %s article.', 'tcd-serum'), $news_label); ?></p>
        <p><?php _e('Display position can be set from each post type.', 'tcd-serum');  ?></p>
       </div>

       <h4 class="theme_option_headline2"><?php _e('Share button design', 'tcd-serum');  ?></h4>
       <ul class="design_radio_button image_radio_button cf">
        <?php foreach ( $sns_type_options as $option ) { ?>
        <li>
         <input type="radio" id="sns_share_design_type_<?php esc_attr_e( $option['value'] ); ?>" name="dp_options[sns_share_design_type]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php checked( $options['sns_share_design_type'], $option['value'] ); ?> />
         <label for="sns_share_design_type_<?php esc_attr_e( $option['value'] ); ?>">
          <span><?php echo esc_html($option['label']); ?></span>
          <img src="<?php bloginfo('template_url'); ?>/admin/img/<?php echo esc_attr($option['img']); ?>?2.0" alt="" title="" />
         </label>
        </li>
        <?php } ?>
       </ul>

       <h4 class="theme_option_headline2"><?php _e('Display setting', 'tcd-serum');  ?></h4>
       <ul>
        <li><label><input name="dp_options[show_sns_share_twitter]" type="checkbox" value="1" <?php checked( '1', $options['show_sns_share_twitter'] ); ?> /> <?php _e('Display X button', 'tcd-serum');  ?></label></li>
        <li><label><input name="dp_options[show_sns_share_fblike]" type="checkbox" value="1" <?php checked( '1', $options['show_sns_share_fblike'] ); ?> /> <?php _e('Display facebook like button -Button type 5 (Default button) only', 'tcd-serum');  ?></label></li>
        <li><label><input name="dp_options[show_sns_share_fbshare]" type="checkbox" value="1" <?php checked( '1', $options['show_sns_share_fbshare'] ); ?> /> <?php _e('Display facebook share button', 'tcd-serum');  ?></label></li>
        <li><label><input name="dp_options[show_sns_share_hatena]" type="checkbox" value="1" <?php checked( '1', $options['show_sns_share_hatena'] ); ?> /> <?php _e('Display hatena button', 'tcd-serum');  ?></label></li>
        <li><label><input name="dp_options[show_sns_share_pocket]" type="checkbox" value="1" <?php checked( '1', $options['show_sns_share_pocket'] ); ?> /> <?php _e('Display pocket button', 'tcd-serum');  ?></label></li>
        <li><label><input name="dp_options[show_sns_share_rss]" type="checkbox" value="1" <?php checked( '1', $options['show_sns_share_rss'] ); ?> /> <?php _e('Display rss button', 'tcd-serum');  ?></label></li>
        <li><label><input name="dp_options[show_sns_share_feedly]" type="checkbox" value="1" <?php checked( '1', $options['show_sns_share_feedly'] ); ?> /> <?php _e('Display feedly button', 'tcd-serum');  ?></label></li>
        <li><label><input name="dp_options[show_sns_share_pinterest]" type="checkbox" value="1" <?php checked( '1', $options['show_sns_share_pinterest'] ); ?> /> <?php _e('Display pinterest button', 'tcd-serum');  ?></label></li>
       </ul>

       <h4 class="theme_option_headline2"><?php _e('Setting for the X button', 'tcd-serum');  ?></h4>
       <label style="margin-top:20px;"><?php _e('Set of X account. (ex.tcd_jp)', 'tcd-serum');  ?></label>
       <input style="display:block; margin:.6em 0 1em;" id="dp_options[twitter_info]" class="regular-text" type="text" name="dp_options[twitter_info]" value="<?php esc_attr_e( $options['twitter_info'] ); ?>" />

      </div><!-- END .sub_box_content -->
     </div><!-- END .sub_box -->

     <?php // ソーシャルボタン ------------------------------------------------------------------- ?>
     <div class="sub_box cf"> 
      <h3 class="theme_option_subbox_headline"><?php _e('SNS icon', 'tcd-serum');  ?></h3>
      <div class="sub_box_content">

       <h4 class="theme_option_headline2"><?php _e('SNS icon design', 'tcd-serum');  ?></h4>
       <ul class="design_radio_button image_radio_button cf">
        <li>
         <input type="radio" id="sns_button_color_type1" name="dp_options[sns_button_color_type]" value="type1" <?php checked( $options['sns_button_color_type'], 'type1' ); ?> />
         <label for="sns_button_color_type1">
          <span><?php _e('Monochrome (TCD ver)', 'tcd-serum');  ?></span>
          <img src="<?php bloginfo('template_url'); ?>/admin/img/sns_color_type1.png?2.0" alt="" title="" />
         </label>
        </li>
        <li>
         <input type="radio" id="sns_button_color_type2" name="dp_options[sns_button_color_type]" value="type2" <?php checked( $options['sns_button_color_type'], 'type2' ); ?> />
         <label for="sns_button_color_type2">
          <span><?php _e('Official color', 'tcd-serum');  ?></span>
          <img src="<?php bloginfo('template_url'); ?>/admin/img/sns_color_type2.png?2.0" alt="" title="" />
         </label>
        </li>
       </ul>

       <h4 class="theme_option_headline2"><?php _e('Link', 'tcd-serum');  ?></h4>
       <div class="theme_option_message2">
        <p><?php _e('Enter url of your SNS. Please leave the field empty if you don\'t want to display certain sns button.', 'tcd-serum');  ?></p>
       </div>
       <ul class="option_list">
        <li class="cf"><span class="label"><?php _e('Instagram URL', 'tcd-serum'); ?></span><input class="full_width" type="text" name="dp_options[sns_button_instagram_url]" value="<?php echo esc_attr( $options['sns_button_instagram_url'] ); ?>"></li>
        <li class="cf"><span class="label"><?php _e('TikTok URL', 'tcd-serum'); ?></span><input class="full_width" type="text" name="dp_options[sns_button_tiktok_url]" value="<?php echo esc_attr( $options['sns_button_tiktok_url'] ); ?>"></li>
        <li class="cf"><span class="label"><?php _e('X URL', 'tcd-serum'); ?></span><input class="full_width" type="text" name="dp_options[sns_button_twitter_url]" value="<?php echo esc_attr( $options['sns_button_twitter_url'] ); ?>"></li>
        <li class="cf"><span class="label"><?php _e('Facebook URL', 'tcd-serum'); ?></span><input class="full_width" type="text" name="dp_options[sns_button_facebook_url]" value="<?php echo esc_attr( $options['sns_button_facebook_url'] ); ?>"></li>
        <li class="cf"><span class="label"><?php _e('Pinterest URL', 'tcd-serum'); ?></span><input class="full_width" type="text" name="dp_options[sns_button_pinterest_url]" value="<?php echo esc_attr( $options['sns_button_pinterest_url'] ); ?>"></li>
        <li class="cf"><span class="label"><?php _e('Youtube URL', 'tcd-serum'); ?></span><input class="full_width" type="text" name="dp_options[sns_button_youtube_url]" value="<?php echo esc_attr( $options['sns_button_youtube_url'] ); ?>"></li>
        <li class="cf"><span class="label"><?php _e('Contact page URL (You can use mailto:)', 'tcd-serum'); ?></span><input class="full_width" type="text" name="dp_options[sns_button_contact_url]" value="<?php echo esc_attr( $options['sns_button_contact_url'] ); ?>"></li>
        <li class="cf"><span class="label"><?php _e('Display RSS button', 'tcd-serum'); ?></span><input name="dp_options[sns_button_show_rss]" type="checkbox" value="1" <?php checked( '1', $options['sns_button_show_rss'] ); ?> /></li>
       </ul>

       <h4 class="theme_option_headline2"><?php _e('Display setting', 'tcd-serum');  ?></h4>
       <ul class="option_list">
        <li class="cf"><span class="label"><?php _e('Left bottom on sidebar / Footer (mobile)', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'show_sns_footer', $basic_display_options); ?></li>
       </ul>

      </div><!-- END .sub_box_content -->
     </div><!-- END .sub_box -->

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // 404 ページ ----------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e( '404 page / Search result page', 'tcd-serum' ); ?></h3>
    <div class="theme_option_field_ac_content tab_parent">

     <div class="sub_box_tab_content active" data-tab-content="tab1">
      <div class="front_page_image">
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/404_image.jpg" alt="" title="" />
      </div>
     </div>

     <div class="sub_box_tab_content" data-tab-content="tab2">
      <div class="front_page_image">
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/no_result_image.jpg" alt="" title="" />
      </div>
     </div>

     <div class="sub_box_tab">
      <div class="tab active" data-tab="tab1"><?php _e('404 page', 'tcd-serum'); ?></div>
      <div class="tab" data-tab="tab2"><?php _e('Search result page', 'tcd-serum'); ?></div>
     </div>

     <?php // 404ページ ----------------------- ?>
     <div class="sub_box_tab_content active" data-tab-content="tab1">

     <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
      <p><?php printf(__('You can check 404 page form <a href="%s" target="_blank">this page</a>.', 'tcd-serum'), esc_url(home_url('check404notfoundpage')) );  ?></p>
     </div>

     <ul class="option_list">
      <li class="cf"><span class="label"><span class="num">1</span><?php _e('Headline', 'tcd-serum'); ?></span><input type="text" class="full_width" placeholder="<?php _e( 'e.g.', 'tcd-serum' ); ?>404 NOT FOUND" name="dp_options[page_404_catch]" value="<?php echo esc_attr( $options['page_404_catch'] ); ?>" /></li>
      <li class="cf"><span class="label"><span class="num">2</span><?php _e('Description', 'tcd-serum'); ?></span><textarea class="full_width" cols="50" rows="2" name="dp_options[page_404_desc]"><?php echo esc_textarea( $options['page_404_desc'] ); ?></textarea></li>
      <li class="cf">
       <span class="label">
        <span class="num">3</span>
        <span><?php _e('Background image', 'tcd-serum'); ?></span>
        <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '1450', '1100'); ?></span>
       </span>
       <div class="image_box cf">
        <div class="cf cf_media_field hide-if-no-js page_404_bg_image">
         <input type="hidden" value="<?php echo esc_attr( $options['page_404_bg_image'] ); ?>" id="page_404_bg_image" name="dp_options[page_404_bg_image]" class="cf_media_id">
         <div class="preview_field"><?php if ( $options['page_404_bg_image'] ) { echo wp_get_attachment_image( $options['page_404_bg_image'], 'medium' ); } ?></div>
         <div class="button_area">
          <input type="button" value="<?php _e( 'Select Image', 'tcd-serum' ); ?>" class="cfmf-select-img button">
          <input type="button" value="<?php _e( 'Remove Image', 'tcd-serum' ); ?>" class="cfmf-delete-img button <?php if ( ! $options['page_404_bg_image'] ) { echo 'hidden'; } ?>">
         </div>
        </div>
       </div>
      </li>
      <li class="cf"><span class="label"><span class="num">3</span><?php _e('Overlay color', 'tcd-serum'); ?></span><input type="text" name="dp_options[page_404_overlay_color]" value="<?php echo esc_attr( $options['page_404_overlay_color'] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
      <li class="cf">
       <span class="label"><span class="num">3</span><?php _e('Transparency of overlay', 'tcd-serum'); ?></span>
       <input class="hankaku" style="width:70px;" type="number" max="1" min="0" step="0.1" name="dp_options[page_404_overlay_opacity]" value="<?php echo esc_attr( $options['page_404_overlay_opacity'] ); ?>" />
       <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
        <p><?php _e('Please specify the number of 0 from 0.9. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?><br>
        <?php _e('Please enter 0 if you don\'t want to use overlay.', 'tcd-serum');  ?></p>
       </div>
      </li>
     </ul>

     </div><!-- END .sub_box_tab_content -->

     <?php // 検索結果ページ ----------------------- ?>
     <div class="sub_box_tab_content" data-tab-content="tab2">

     <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
      <p><?php printf(__('You can check search result page form <a href="%s" target="_blank">this page</a>.', 'tcd-serum'), esc_url(home_url('?s=checksearchresultpage')) );  ?></p>
     </div>

     <ul class="option_list">
      <li class="cf"><span class="label"><span class="num">1</span><?php _e('Headline', 'tcd-serum'); ?></span><input type="text" placeholder="<?php _e( 'e.g.', 'tcd-serum' ); _e( 'Search result', 'tcd-serum' ); ?>" class="full_width" name="dp_options[search_result_headline]" value="<?php echo esc_attr( $options['search_result_headline'] ); ?>" /></li>
      <li class="cf"><span class="label"><span class="num">2</span><?php _e('Description', 'tcd-serum'); ?></span><textarea placeholder="<?php _e( 'e.g.', 'tcd-serum' ); _e( 'The page you are looking for are not found', 'tcd-serum' ); ?>" class="full_width" cols="50" rows="2" name="dp_options[search_result_desc]"><?php echo esc_textarea( $options['search_result_desc'] ); ?></textarea></li>
      <li class="cf"><span class="label"><span class="num">3</span><?php _e('Placeholder for search form', 'tcd-serum'); ?></span><input type="text" placeholder="<?php _e( 'e.g.', 'tcd-serum' ); _e( 'Enter search keyword', 'tcd-serum' ); ?>" class="full_width" name="dp_options[search_result_placeholder]" value="<?php echo esc_attr( $options['search_result_placeholder'] ); ?>" /></li>
      <li class="cf">
       <span class="label">
        <span class="num">4</span>
        <span><?php _e('Background image', 'tcd-serum'); ?></span>
        <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '1450', '1100'); ?></span>
       </span>
       <div class="image_box cf">
        <div class="cf cf_media_field hide-if-no-js search_result_bg_image">
         <input type="hidden" value="<?php echo esc_attr( $options['search_result_bg_image'] ); ?>" id="search_result_bg_image" name="dp_options[search_result_bg_image]" class="cf_media_id">
         <div class="preview_field"><?php if ( $options['search_result_bg_image'] ) { echo wp_get_attachment_image( $options['search_result_bg_image'], 'medium' ); } ?></div>
         <div class="button_area">
          <input type="button" value="<?php _e( 'Select Image', 'tcd-serum' ); ?>" class="cfmf-select-img button">
          <input type="button" value="<?php _e( 'Remove Image', 'tcd-serum' ); ?>" class="cfmf-delete-img button <?php if ( ! $options['search_result_bg_image'] ) { echo 'hidden'; } ?>">
         </div>
        </div>
       </div>
      </li>
      <li class="cf"><span class="label"><span class="num">4</span><?php _e('Overlay color', 'tcd-serum'); ?></span><input type="text" name="dp_options[search_result_overlay_color]" value="<?php echo esc_attr( $options['search_result_overlay_color'] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
      <li class="cf">
       <span class="label"><span class="num">4</span><?php _e('Transparency of overlay', 'tcd-serum'); ?></span>
       <input class="hankaku" style="width:70px;" type="number" max="1" min="0" step="0.1" name="dp_options[search_result_overlay_opacity]" value="<?php echo esc_attr( $options['search_result_overlay_opacity'] ); ?>" />
       <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
        <p><?php _e('Please specify the number of 0 from 0.9. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?><br>
        <?php _e('Please enter 0 if you don\'t want to use overlay.', 'tcd-serum');  ?></p>
       </div>
      </li>
     </ul>

     <h4 class="theme_option_headline2"><?php _e('Search range', 'tcd-serum');  ?></h4>
     <div class="theme_option_message2">
      <p><?php _e('This setting will be applied to all search forms, including the search widget. ', 'tcd-serum');  ?></p>
     </div>

     <ul class="option_list">
      <li class="cf"><span class="label"><?php printf(__('Include %s in search range', 'tcd-serum'), __('post', 'tcd-serum') ); ?></span><?php echo tcd_basic_radio_button($options, 'search_type_post', $bool_options); ?></li>
      <li class="cf"><span class="label"><?php printf(__('Include %s in search range', 'tcd-serum'), __('pages', 'tcd-serum') ); ?></span><?php echo tcd_basic_radio_button($options, 'search_type_page', $bool_options); ?></li>
      <li class="cf" style="<?php if($options['use_news']){ echo 'display:block;'; } else { echo 'display:none;'; }; ?>"><span class="label"><?php printf(__('Include %s in search range', 'tcd-serum'), $news_label ); ?></span></span><?php echo tcd_basic_radio_button($options, 'search_type_news', $bool_options); ?></li>
      <li class="cf" style="<?php if($options['use_treatment']){ echo 'display:block;'; } else { echo 'display:none;'; }; ?>"><span class="label"><?php printf(__('Include %s in search range', 'tcd-serum'), $treatment_label ); ?></span></span><?php echo tcd_basic_radio_button($options, 'search_type_treatment', $bool_options); ?></li>
     </ul>

     </div><!-- END .sub_box_tab_content -->

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // カスタムスクリプト用の自由記入欄 ----------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Custom CSS and script', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <h4 class="theme_option_headline2"><?php _e('Custom CSS', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2">
      <p><?php _e( 'This css will be displayed inside &lt;head&gt; tag.<br />You don\'t need to enter &lt;style&gt; tag in this field.', 'tcd-serum' ); ?></p>
      <p><?php _e('Example:<br><strong>.custom_css { font-size:12px; }</strong>', 'tcd-serum');  ?></p>
     </div>
     <textarea class="large-text" cols="50" rows="10" name="dp_options[css_code]"><?php echo esc_textarea( $options['css_code'] ); ?></textarea>

     <h4 class="theme_option_headline2"><?php _e('Custom script for header', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2" style="margin-top:10px;">
      <p><?php _e( 'This script will be displayed inside &lt;head&gt; tag.<br>Please use this option when adding Google Analytics tracking code, etc.', 'tcd-serum' ); ?></p>
     </div>
     <textarea class="large-text" cols="50" rows="10" name="dp_options[script_code]"><?php echo esc_textarea( $options['script_code'] ); ?></textarea>

     <h4 class="theme_option_headline2"><?php _e('Custom script for footer', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2" style="margin-top:10px;">
      <p><?php _e( 'This script will be displayed before &lt;/body&gt; tag.<br>Please use this option when adding conversion analysis script. Example: An analysis script installed on a sales page, etc.', 'tcd-serum' ); ?></p>
     </div>
     <textarea class="large-text" cols="50" rows="10" name="dp_options[footer_script_code]"><?php echo esc_textarea( $options['footer_script_code'] ); ?></textarea>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


</div><!-- END .tab-content -->

<?php
} // END add_basic_tab_panel()


// バリデーション　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_basic_theme_options_validate( $input ) {

  global $dp_default_options, $font_type_options, $hover_type_options, $hover3_direct_options, $sns_type_options, $footer_bar_icon_options, $bool_options,
         $button_type_options, $button_border_radius_options, $button_size_options, $button_animation_options,
         $loading_type, $loading_display_page_options, $loading_display_time_options, $time_options, $logo_type_options;

  // 色の設定
  $input['main_color'] = sanitize_hex_color( $input['main_color'] );
  $input['bg_color'] = sanitize_hex_color( $input['bg_color'] );
  $input['content_link_color'] = sanitize_hex_color( $input['content_link_color'] );


  // フォントの種類
  if ( ! isset( $input['content_font_type'] ) )
    $input['content_font_type'] = null;
  if ( ! array_key_exists( $input['content_font_type'], $font_type_options ) )
    $input['content_font_type'] = null;
  $input['content_font_size'] = wp_filter_nohtml_kses( $input['content_font_size'] );
  $input['content_font_size_sp'] = wp_filter_nohtml_kses( $input['content_font_size_sp'] );

  if ( ! isset( $input['page_header_font_type'] ) )
    $input['page_header_font_type'] = null;
  if ( ! array_key_exists( $input['page_header_font_type'], $font_type_options ) )
    $input['page_header_font_type'] = null;
  $input['page_header_font_size'] = wp_filter_nohtml_kses( $input['page_header_font_size'] );
  $input['page_header_font_size_sp'] = wp_filter_nohtml_kses( $input['page_header_font_size_sp'] );

  if ( ! isset( $input['catch_font_type'] ) )
    $input['catch_font_type'] = null;
  if ( ! array_key_exists( $input['catch_font_type'], $font_type_options ) )
    $input['catch_font_type'] = null;
  $input['catch_font_size'] = wp_filter_nohtml_kses( $input['catch_font_size'] );
  $input['catch_font_size_sp'] = wp_filter_nohtml_kses( $input['catch_font_size_sp'] );

  if ( ! isset( $input['single_title_font_type'] ) )
    $input['single_title_font_type'] = null;
  if ( ! array_key_exists( $input['single_title_font_type'], $font_type_options ) )
    $input['single_title_font_type'] = null;
  $input['single_title_font_size'] = wp_filter_nohtml_kses( $input['single_title_font_size'] );
  $input['single_title_font_size_sp'] = wp_filter_nohtml_kses( $input['single_title_font_size_sp'] );


  // ボタン
  $input['design_button_type'] = wp_filter_nohtml_kses( $input['design_button_type'] );
  $input['design_button_border_radius'] = wp_filter_nohtml_kses( $input['design_button_border_radius'] );
  $input['design_button_size'] = wp_filter_nohtml_kses( $input['design_button_size'] );
  $input['design_button_animation_type'] = wp_filter_nohtml_kses( $input['design_button_animation_type'] );
  $input['design_button_color'] = wp_filter_nohtml_kses( $input['design_button_color'] );
  $input['design_button_color_hover'] = wp_filter_nohtml_kses( $input['design_button_color_hover'] );


  // アニメーションの設定
  if ( ! isset( $input['hover_type'] ) )
    $input['hover_type'] = null;
  if ( ! array_key_exists( $input['hover_type'], $hover_type_options ) )
    $input['hover_type'] = null;
  $input['hover1_zoom'] = wp_filter_nohtml_kses( $input['hover1_zoom'] );
  $input['hover2_zoom'] = wp_filter_nohtml_kses( $input['hover2_zoom'] );
  if ( ! isset( $input['hover3_direct'] ) )
    $input['hover3_direct'] = null;
  if ( ! array_key_exists( $input['hover3_direct'], $hover3_direct_options ) )
    $input['hover3_direct'] = null;
  $input['hover3_opacity'] = wp_filter_nohtml_kses( $input['hover3_opacity'] );
  $input['hover3_bgcolor'] = sanitize_hex_color( $input['hover3_bgcolor'] );
  $input['hover4_opacity'] = wp_filter_nohtml_kses( $input['hover4_opacity'] );
  $input['hover4_bgcolor'] = sanitize_hex_color( $input['hover4_bgcolor'] );

  $input['no_image'] = wp_filter_nohtml_kses( $input['no_image'] );

  // オリジナルスクリプトの設定
  $input['script_code'] = $input['script_code'];
  $input['footer_script_code'] = $input['footer_script_code'];
  $input['css_code'] = $input['css_code'];


  // ロード画面
  $input['show_loading'] = ! empty( $input['show_loading'] ) ? 1 : 0;
  if ( ! isset( $input['loading_type'] ) )
    $input['loading_type'] = null;
  if ( ! array_key_exists( $input['loading_type'], $loading_type ) )
    $input['loading_type'] = null;
  $input['loading_icon_color'] = sanitize_hex_color( $input['loading_icon_color'] );
  $input['loading_bg_color'] = sanitize_hex_color( $input['loading_bg_color'] );


  // ソーシャルシェアボタン
  $input['sns_share_design_type'] = wp_filter_nohtml_kses( $input['sns_share_design_type'] );
  $input['show_sns_share_twitter'] = ! empty( $input['show_sns_share_twitter'] ) ? 1 : 0;
  $input['show_sns_share_fblike'] = ! empty( $input['show_sns_share_fblike'] ) ? 1 : 0;
  $input['show_sns_share_fbshare'] = ! empty( $input['show_sns_share_fbshare'] ) ? 1 : 0;
  $input['show_sns_share_hatena'] = ! empty( $input['show_sns_share_hatena'] ) ? 1 : 0;
  $input['show_sns_share_pocket'] = ! empty( $input['show_sns_share_pocket'] ) ? 1 : 0;
  $input['show_sns_share_feedly'] = ! empty( $input['show_sns_share_feedly'] ) ? 1 : 0;
  $input['show_sns_share_rss'] = ! empty( $input['show_sns_share_rss'] ) ? 1 : 0;
  $input['show_sns_share_pinterest'] = ! empty( $input['show_sns_share_pinterest'] ) ? 1 : 0;
  $input['twitter_info'] = wp_filter_nohtml_kses( $input['twitter_info'] );


  // ソーシャルボタン
  $input['sns_button_color_type'] = wp_filter_nohtml_kses( $input['sns_button_color_type'] );
  $input['sns_button_facebook_url'] = wp_filter_nohtml_kses( $input['sns_button_facebook_url'] );
  $input['sns_button_twitter_url'] = wp_filter_nohtml_kses( $input['sns_button_twitter_url'] );
  $input['sns_button_instagram_url'] = wp_filter_nohtml_kses( $input['sns_button_instagram_url'] );
  $input['sns_button_pinterest_url'] = wp_filter_nohtml_kses( $input['sns_button_pinterest_url'] );
  $input['sns_button_youtube_url'] = wp_filter_nohtml_kses( $input['sns_button_youtube_url'] );
  $input['sns_button_contact_url'] = wp_filter_nohtml_kses( $input['sns_button_contact_url'] );
  $input['sns_button_show_rss'] = ! empty( $input['sns_button_show_rss'] ) ? 1 : 0;
  $input['sns_button_tiktok_url'] = wp_filter_nohtml_kses( $input['sns_button_tiktok_url'] );

  $input['show_sns_footer'] = wp_filter_nohtml_kses( $input['show_sns_footer'] );


  // 検索の対象
  $input['search_type_post'] = wp_filter_nohtml_kses( $input['search_type_post'] );
  $input['search_type_page'] = wp_filter_nohtml_kses( $input['search_type_page'] );
  $input['search_type_news'] = wp_filter_nohtml_kses( $input['search_type_news'] );
  $input['search_type_treatment'] = wp_filter_nohtml_kses( $input['search_type_treatment'] );

  $input['search_result_headline'] = wp_filter_nohtml_kses( $input['search_result_headline'] );
  $input['search_result_desc'] = wp_kses_post( $input['search_result_desc'] );
  $input['search_result_bg_image'] = wp_filter_nohtml_kses( $input['search_result_bg_image'] );
  $input['search_result_overlay_color'] = wp_filter_nohtml_kses( $input['search_result_overlay_color'] );
  $input['search_result_overlay_opacity'] = wp_filter_nohtml_kses( $input['search_result_overlay_opacity'] );


  // 404 ページ
  $input['page_404_catch'] = wp_filter_nohtml_kses( $input['page_404_catch'] );
  $input['page_404_desc'] = wp_kses_post( $input['page_404_desc'] );
  $input['page_404_bg_image'] = wp_filter_nohtml_kses( $input['page_404_bg_image'] );
  $input['page_404_overlay_color'] = wp_filter_nohtml_kses( $input['page_404_overlay_color'] );
  $input['page_404_overlay_opacity'] = wp_filter_nohtml_kses( $input['page_404_overlay_opacity'] );


  // ヘッダーロゴ
  if ( ! isset( $input['header_logo_type'] ) )
    $input['header_logo_type'] = null;
  if ( ! array_key_exists( $input['header_logo_type'], $logo_type_options ) )
    $input['header_logo_type'] = null;
  $input['header_logo_font_size'] = wp_filter_nohtml_kses( $input['header_logo_font_size'] );
  $input['header_logo_font_size_sp'] = wp_filter_nohtml_kses( $input['header_logo_font_size_sp'] );
  $input['header_logo_font_type'] = wp_filter_nohtml_kses( $input['header_logo_font_type'] );
  $input['header_logo_image'] = wp_filter_nohtml_kses( $input['header_logo_image'] );
  $input['header_logo_retina'] = wp_filter_nohtml_kses( $input['header_logo_retina'] );
  $input['header_logo_image_mobile'] = wp_filter_nohtml_kses( $input['header_logo_image_mobile'] );

  $input['header_logo_image2'] = wp_filter_nohtml_kses( $input['header_logo_image2'] );
  $input['header_logo_image_mobile2'] = wp_filter_nohtml_kses( $input['header_logo_image_mobile2'] );
  $input['header_logo_show_icon_image'] = wp_filter_nohtml_kses( $input['header_logo_show_icon_image'] );
  $input['header_logo_image3'] = wp_filter_nohtml_kses( $input['header_logo_image3'] );
  $input['header_logo_image_mobile3'] = wp_filter_nohtml_kses( $input['header_logo_image_mobile3'] );


  return $input;

};


?>