<?php
/*
 * トップページの設定
 */


// Add default values
add_filter( 'before_getting_design_plus_option', 'add_front_page_dp_default_options' );


// Add label of front page tab
add_action( 'tcd_tab_labels', 'add_front_page_tab_label' );


// Add HTML of front page tab
add_action( 'tcd_tab_panel', 'add_front_page_tab_panel' );


// Register sanitize function
add_filter( 'theme_options_validate', 'add_front_page_theme_options_validate' );


// タブの名前
function add_front_page_tab_label( $tab_labels ) {
	$tab_labels['front_page'] = __( 'Front page', 'tcd-serum' );
	return $tab_labels;
}


// 初期値
function add_front_page_dp_default_options( $dp_default_options ) {

  // スプラッシュ画面
  $dp_default_options['show_splash'] = '';
  $dp_default_options['splash_type'] = 'type1';
  $dp_default_options['splash_catch'] = '';
	$dp_default_options['splash_catch_direction'] = 'type2';
  $dp_default_options['splash_image'] = '';
	$dp_default_options['splash_image_mobile'] = '';
	$dp_default_options['splash_overlay_color'] = '#000000';
	$dp_default_options['splash_overlay_opacity'] = '0.3';
  $dp_default_options['splash_logo'] = '';
	$dp_default_options['splash_logo_retina'] = 'no';
	$dp_default_options['splash_catch_font_size'] = '32';
	$dp_default_options['splash_catch_font_size_sp'] = '20';
	$dp_default_options['splash_catch_font_color'] = '#937960';
  $dp_default_options['splash_display_time'] = 'type1';

	// ヘッダースライダー
	$dp_default_options['index_slider'] = array(
		array(
			"slider_type" => "type1",
			"image" => "",
			"image_mobile" => "",
			"video" => "",
			"youtube" => "",
			"catch" => __( 'Catchphrase', 'tcd-serum' ),
			"catch_font_direction" => "type1",
			"catch_font_size" => "34",
			"catch_font_size_mobile" => "24",
			"overlay_color" => "#000000",
			"overlay_opacity" => "0.3",
			"url" => "",
			"target" => "",
    ),
		array(
			"slider_type" => "type1",
			"image" => "",
			"image_mobile" => "",
			"video" => "",
			"youtube" => "",
			"catch" => __( 'Catchphrase', 'tcd-serum' ),
			"catch_font_direction" => "type1",
			"catch_font_size" => "34",
			"catch_font_size_mobile" => "24",
			"overlay_color" => "#000000",
			"overlay_opacity" => "0.3",
			"url" => "",
			"target" => "",
    )
	);

	// ニュースティッカーの設定
	$dp_default_options['show_index_news'] = '1';
	$dp_default_options['index_news_post_type'] = 'post';
	$dp_default_options['index_news_post_order'] = 'date';

  // コンテンツビルダー
	$dp_default_options['page_content_width_type'] = 'type1';
	$dp_default_options['page_content_width'] = '1030';
	$dp_default_options['index_content_type'] = 'type1';
	$dp_default_options['contents_builder'] = array(
		array(
            "cb_content_select" => "free_space",
            "show_content" => 1,
            "free_space" => __( '<p style="text-align:center;">Description will be displayed here.<br>Description will be displayed here.</p>', 'tcd-serum' ),
            "display_bg_color" => 'hide',
		),
		array(
            "cb_content_select" => "box_content",
            "show_content" => 1,
            "catch" => __( 'Catchphrase', 'tcd-serum' ),
            "image1" => '',
            "headline1" => __( 'Headline', 'tcd-serum' ),
            "desc1" => __( 'Description will be displayed here.', 'tcd-serum' ),
            "button_label1" => __( 'Button', 'tcd-serum' ),
            "button_url1" => '#',
            "button_target1" => 0,
            "image2" => '',
            "headline2" => __( 'Headline', 'tcd-serum' ),
            "desc2" => __( 'Description will be displayed here.', 'tcd-serum' ),
            "button_label2" => __( 'Button', 'tcd-serum' ),
            "button_url2" => '#',
            "button_target2" => 0,
            "image3" => '',
            "headline3" => '',
            "desc3" => '',
            "button_label3" => '',
            "button_url3" => '',
            "button_target3" => 0,
		),
		array(
            "cb_content_select" => "carousel",
            "show_content" => 1,
            "catch" => __( 'Catchphrase', 'tcd-serum' ),
            "layout" => 'type1',
            "post_type" => 'post',
            "post_order" => 'date',
            "display_bg_color" => 'show',
		),
		array(
            "cb_content_select" => "free_space",
            "show_content" => 1,
            "free_space" => "<div>[sc_basic_info]</div>",
            "display_bg_color" => 'hide',
		),
	);

	return $dp_default_options;

}

// 入力欄の出力　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_front_page_tab_panel( $options ) {

  global $blog_label, $dp_default_options, $item_type_options, $time_options, $font_type_options, $font_direction_options, $bool_options, $basic_display_options,
         $loading_type, $loading_display_page_options, $loading_display_time_options, $logo_type_options, $font_direction_options, $splash_options;
  $news_label = $options['news_label'] ? esc_html( $options['news_label'] ) : __( 'News', 'tcd-serum' );
  $treatment_label = $options['treatment_label'] ? esc_html( $options['treatment_label'] ) : __( 'Treatment', 'tcd-serum' );

?>

<div id="tab-content-front-page" class="tab-content">

   <?php // スプラッシュ画面 ----------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Splash screen', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <p class="displayment_checkbox"><label><input name="dp_options[show_splash]" type="checkbox" value="1" <?php checked( $options['show_splash'], 1 ); ?>><?php _e( 'Display splash screen', 'tcd-serum' ); ?></label></p>
     <div style="<?php if($options['show_splash'] == 1) { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">

      <div class="theme_option_message2">
       <p><?php _e('Splash screen will be displayed on front page.<br>When you set the number of times to display "only once", you can delete the cookie and check the display again.<br><a href="https://tcd-theme.com/2019/07/cookie-cache.html" target="_blank">What is Cookie?</a>', 'tcd-serum');  ?></p>
      </div>

      <ul class="option_list">
       <li class="cf">
        <span class="label"><?php _e('Display times', 'tcd-serum');  ?></span>
        <div class="standard_radio_button">
         <input type="radio" id="splash_display_time1" name="dp_options[splash_display_time]" value="type1"<?php checked( $options['splash_display_time'], 'type1' ); ?>>
         <label for="splash_display_time1"><?php _e('Only once', 'tcd-serum');  ?></label>
         <input type="radio" id="splash_display_time2" name="dp_options[splash_display_time]" value="type2"<?php checked( $options['splash_display_time'], 'type2' ); ?>>
         <label for="splash_display_time2"><?php _e('Every time', 'tcd-serum');  ?></label>
        </div>
       </li>
       <li class="cf">
        <span class="label">Cookie</span>
        <a class="reset_cookie button" href="#"><?php _e('Delete cookie', 'tcd-serum');  ?></a>
       </li>
       <li class="cf" style="border-top:1px dotted #ccc;">
        <span class="label"><?php _e('Content type', 'tcd-serum');  ?></span>
        <div class="standard_radio_button">
         <input type="radio" id="splash_type1" name="dp_options[splash_type]" value="type1"<?php checked( $options['splash_type'], 'type1' ); ?>>
         <label for="splash_type1"><?php _e('Catchphrase', 'tcd-serum');  ?></label>
         <input type="radio" id="splash_type2" name="dp_options[splash_type]" value="type2"<?php checked( $options['splash_type'], 'type2' ); ?>>
         <label for="splash_type2"><?php _e('Logo', 'tcd-serum');  ?></label>
        </div>
       </li>
       <li class="cf splash_type1_option"><span class="label"><?php _e('Catchphrase', 'tcd-serum'); ?></span><textarea class="full_width" cols="50" rows="2" name="dp_options[splash_catch]"><?php echo esc_textarea(  $options['splash_catch'] ); ?></textarea></li>
       <li class="cf splash_type1_option"><span class="label"><?php _e('Font direction', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'splash_catch_direction', $font_direction_options); ?></li>
       <li class="cf splash_type1_option"><span class="label"><?php _e('Font size', 'tcd-serum'); ?></span><?php echo tcd_font_size_option($options, 'splash_catch_font_size'); ?></li>
       <li class="cf splash_type1_option"><span class="label"><?php _e('Font color', 'tcd-serum'); ?></span><input type="text" name="dp_options[splash_catch_font_color]" value="<?php echo esc_attr( $options['splash_catch_font_color'] ); ?>" data-default-color="#937960" class="c-color-picker"></li>
       <li class="cf splash_type2_option">
        <span class="label">
         <?php _e('Logo image', 'tcd-serum'); ?>
         <span class="recommend_desc"><?php _e('Please select "Yes" for the radio button below if you upload logo image for the retina display.', 'tcd-serum'); ?></span>
        </span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js splash_logo">
          <input type="hidden" value="<?php echo esc_attr( $options['splash_logo'] ); ?>" id="splash_logo" name="dp_options[splash_logo]" class="cf_media_id">
          <div class="preview_field"><?php if($options['splash_logo']){ echo wp_get_attachment_image($options['splash_logo'], 'medium'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['splash_logo']){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
       <li class="cf splash_type2_option"><span class="label"><?php _e('Use retina display image', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'splash_logo_retina', $bool_options); ?></li>
       <li class="cf">
        <span class="label">
         <?php _e('Background image', 'tcd-serum'); ?>
         <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '1450', '1050'); ?></span>
        </span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js splash_image">
          <input type="hidden" value="<?php echo esc_attr( $options['splash_image'] ); ?>" id="splash_image" name="dp_options[splash_image]" class="cf_media_id">
          <div class="preview_field"><?php if($options['splash_image']){ echo wp_get_attachment_image($options['splash_image'], 'medium'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['splash_image']){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
       <li class="cf">
        <span class="label">
         <?php _e('Background image (mobile)', 'tcd-serum'); ?>
         <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '750', '1400'); ?></span>
        </span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js splash_image_mobile">
          <input type="hidden" value="<?php echo esc_attr( $options['splash_image_mobile'] ); ?>" id="splash_image_mobile" name="dp_options[splash_image_mobile]" class="cf_media_id">
          <div class="preview_field"><?php if($options['splash_image_mobile']){ echo wp_get_attachment_image($options['splash_image_mobile'], 'medium'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['splash_image_mobile']){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
       <li class="cf"><span class="label"><?php _e('Color of overlay', 'tcd-serum'); ?></span><input type="text" name="dp_options[splash_overlay_color]" value="<?php echo esc_attr( $options['splash_overlay_color'] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
       <li class="cf">
        <span class="label"><?php _e('Transparency of overlay', 'tcd-serum'); ?></span><input class="hankaku" style="width:70px;" type="number" max="1" min="0" step="0.1" name="dp_options[splash_overlay_opacity]" value="<?php echo esc_attr( $options['splash_overlay_opacity'] ); ?>" />
        <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
         <p><?php _e('Please specify the number of 0 from 0.9. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?>
         <?php _e('Please enter 0 if you don\'t want to use overlay.', 'tcd-serum');  ?></p>
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


   <?php // ヘッダーコンテンツの設定 ---------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Header content', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content header_content_setting_area">

     <div class="front_page_image">
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/header_slider.jpg" alt="" title="" />
     </div>

     <div class="theme_option_message">
      <p><?php _e('Set the background and catchphrase for the red frame area.', 'tcd-serum');  ?></p>
      <p><?php _e('Click add new item button to start this option.<br />You can change order by dragging each headline of option field.', 'tcd-serum');  ?></p>
     </div>

     <?php //繰り返しフィールド ----- ?>
     <div class="repeater-wrapper">
      <input type="hidden" name="dp_options[index_slider]" value="">
      <div class="repeater sortable" data-delete-confirm="<?php _e( 'Delete?', 'tcd-serum' ); ?>">
       <?php
            if ( $options['index_slider'] ) :
              foreach ( $options['index_slider'] as $key => $value ) :
       ?>
       <div class="sub_box repeater-item repeater-item-<?php echo esc_attr( $key ); ?>">
        <h4 class="theme_option_subbox_headline"><?php _e( 'Item', 'tcd-serum' ); echo esc_attr( $key+1 ); ?></h4>
        <div class="sub_box_content tab_parent">

          <h4 class="theme_option_headline2"><?php _e('Slider type', 'tcd-serum');  ?></h4>
          <ul class="design_radio_button horizontal clearfix">
           <?php foreach ( $item_type_options as $option ) { ?>
           <li class="index_slider_item_<?php esc_attr_e( $option['value'] ); ?>">
            <input type="radio" id="index_slider_item_<?php esc_attr_e( $option['value'] ); ?>_<?php echo esc_attr( $key ); ?>" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][slider_type]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php checked( $value['slider_type'], $option['value'] ); ?> />
            <label for="index_slider_item_<?php esc_attr_e( $option['value'] ); ?>_<?php echo esc_attr( $key ); ?>"><?php echo $option['label']; ?></label>
           </li>
           <?php } ?>
          </ul>

          <div class="sub_box_tab">
           <div class="tab active" data-tab="tab1"><?php _e('Background', 'tcd-serum'); ?></div>
           <div class="tab" data-tab="tab2"><?php _e('Catchphrase', 'tcd-serum'); ?></div>
          </div>

          <?php // 背景 ----------------------- ?>
          <div class="sub_box_tab_content active" data-tab-content="tab1">

           <?php // video ----------------------- ?>
           <div class="index_slider_video_area" style="<?php if($value['slider_type'] == 'type2') { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
            <h4 class="theme_option_headline2"><?php _e('Video', 'tcd-serum');  ?></h4>
            <div class="theme_option_message2">
             <p><?php _e('Please upload MP4 format file.', 'tcd-serum');  ?></p>
             <p><?php _e('Web browser takes few second to load the data of video so we recommend to use loading screen if you want to display video.', 'tcd-serum'); ?></p>
            </div>
            <div class="cf cf_media_field hide-if-no-js index_slider<?php echo esc_attr( $key ); ?>_video">
             <input type="hidden" value="<?php if($value['video']) { echo esc_attr( $value['video'] ); }; ?>" id="index_slider<?php echo esc_attr( $key ); ?>_video" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][video]" class="cf_media_id">
             <div class="preview_field preview_field_video">
              <?php if($value['video']){ ?>
              <h4><?php _e( 'Uploaded MP4 file', 'tcd-serum' ); ?></h4>
              <p><?php echo esc_url(wp_get_attachment_url($value['video'])); ?></p>
              <?php }; ?>
             </div>
             <div class="buttton_area">
              <input type="button" value="<?php _e('Select MP4 file', 'tcd-serum'); ?>" class="cfmf-select-video button">
              <input type="button" value="<?php _e('Remove MP4 file', 'tcd-serum'); ?>" class="cfmf-delete-video button <?php if(!$value['video']){ echo 'hidden'; }; ?>">
             </div>
            </div>
           </div><!-- END .index_slider_video_area -->

           <?php // youtube ----------------------- ?>
           <div class="index_slider_youtube_area" style="<?php if($value['slider_type'] == 'type3') { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
            <h4 class="theme_option_headline2"><?php _e('Youtube', 'tcd-serum');  ?></h4>
            <div class="theme_option_message2">
             <p><?php _e('Please enter Youtube URL.', 'tcd-serum');  ?></p>
             <p><?php _e('Web browser takes few second to load the data of video so we recommend to use loading screen if you want to display video.', 'tcd-serum'); ?></p>
            </div>
            <input class="regular-text" type="text" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][youtube]" value="<?php echo esc_attr( $value['youtube'] ); ?>">
           </div><!-- END .index_slider_youtube_area -->

           <?php // 背景画像 ----------------------- ?>
           <h4 class="theme_option_headline2"><?php _e( 'Background image', 'tcd-serum' ); ?></h4>
           <div class="index_slider_video_image" style="<?php if($value['slider_type'] != 'type1') { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
            <div class="theme_option_message2">
             <p><?php _e('If the mobile device can\'t play video this image will be displayed instead.', 'tcd-serum');  ?></p>
            </div>
           </div>

           <ul class="option_list">
            <li class="cf">
             <span class="label">
              <?php _e('Background image', 'tcd-serum'); ?>
              <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '1450', '820'); ?></span>
             </span>
             <div class="image_box cf">
              <div class="cf cf_media_field hide-if-no-js index_slider_image<?php echo esc_attr( $key ); ?>">
               <input type="hidden" value="<?php if($value['image']) { echo esc_attr( $value['image'] ); }; ?>" id="index_slider_image<?php echo esc_attr( $key ); ?>" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][image]" class="cf_media_id">
               <div class="preview_field"><?php if($value['image']){ echo wp_get_attachment_image($value['image'], 'full'); }; ?></div>
               <div class="buttton_area">
                <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
                <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$value['image']){ echo 'hidden'; }; ?>">
               </div>
              </div>
             </div>
            </li>
            <li class="cf">
             <span class="label">
              <?php _e('Background image (mobile)', 'tcd-serum'); ?>
              <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '750', '1050'); ?></span>
             </span>
             <div class="image_box cf">
              <div class="cf cf_media_field hide-if-no-js index_slider_image_mobile<?php echo esc_attr( $key ); ?>">
               <input type="hidden" value="<?php if($value['image_mobile']) { echo esc_attr( $value['image_mobile'] ); }; ?>" id="index_slider_image_mobile<?php echo esc_attr( $key ); ?>" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][image_mobile]" class="cf_media_id">
               <div class="preview_field"><?php if($value['image_mobile']){ echo wp_get_attachment_image($value['image_mobile'], 'full'); }; ?></div>
               <div class="buttton_area">
                <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
                <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$value['image_mobile']){ echo 'hidden'; }; ?>">
               </div>
              </div>
             </div>
            </li>
           </ul>

           <?php // オーバーレイ ----------------------- ?>
           <h4 class="theme_option_headline2"><?php _e( 'Overlay', 'tcd-serum' ); ?></h4>
           <ul class="option_list">
            <li class="cf"><span class="label"><?php _e('Color of overlay', 'tcd-serum'); ?></span><input type="text" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][overlay_color]" value="<?php echo esc_attr( $value['overlay_color'] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
            <li class="cf">
             <span class="label"><?php _e('Transparency of overlay', 'tcd-serum'); ?></span><input class="hankaku index_slider_overlay_opacity<?php echo esc_attr( $key ); ?>" style="width:70px;" type="number" max="1" min="0" step="0.1" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][overlay_opacity]" value="<?php echo esc_attr( $value['overlay_opacity'] ); ?>" />
             <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
              <p><?php _e('Please specify the number of 0 from 0.9. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?>
              <?php _e('Please enter 0 if you don\'t want to use overlay.', 'tcd-serum');  ?></p>
             </div>
            </li>
           </ul>

           <?php // リンク ----------------------- ?>
           <h4 class="theme_option_headline2"><?php _e( 'Link', 'tcd-serum' ); ?></h4>
           <div class="theme_option_message2">
            <p><?php _e('Enter URL only if you want to add a link to the item. You can use it when setting banner images, etc.', 'tcd-serum');  ?></p>
           </div>
           <ul class="option_list">
            <li class="cf button_option">
             <span class="label"><?php _e('URL', 'tcd-serum'); ?></span>
             <div class="admin_link_option">
              <input class="full_width" type="text" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][url]" value="<?php echo esc_attr( $value['url'] ); ?>" placeholder="https://example.com/">
              <input id="index_slider_target<?php echo $key; ?>" class="admin_link_option_target" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][target]" type="checkbox" value="1" <?php checked( $value['target'], 1 ); ?>>
              <label for="index_slider_target<?php echo $key; ?>">&#xe92a;</label>
             </div>
            </li>
           </ul>

          </div><!-- END .sub_box_tab_content -->

          <?php // キャッチフレーズ ----------------------- ?>
          <div class="sub_box_tab_content" data-tab-content="tab2">

           <?php // キャッチフレーズ ----------------------- ?>
           <h4 class="theme_option_headline2"><?php _e( 'Catchphrase', 'tcd-serum' ); ?></h4>
           <textarea class="large-text" cols="50" rows="3" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][catch]"><?php echo esc_textarea(  $value['catch'] ); ?></textarea>
           <ul class="option_list">
            <li class="cf">
             <span class="label"><?php _e('Font direction', 'tcd-serum');  ?></span>
             <div class="standard_radio_button">
              <?php foreach ( $font_direction_options as $option ) { ?>
              <input id="index_slider_catch_font_direction_<?php echo $key; ?>_<?php echo esc_attr($option['value']); ?>" type="radio" name="dp_options[index_slider][<?php echo $key; ?>][catch_font_direction]" value="<?php echo esc_attr($option['value']); ?>"<?php checked( $value['catch_font_direction'], $option['value'] ); ?>>
              <label for="index_slider_catch_font_direction_<?php echo $key; ?>_<?php echo esc_attr($option['value']); ?>"><?php echo esc_html($option['label']); ?></label>
              <?php } ?>
             </div>
            </li>
            <li class="cf">
             <span class="label"><?php _e('Font size', 'tcd-serum'); ?></span>
             <div class="font_size_option">
              <label class="font_size_label number_option">
               <input class="hankaku input_font_size" type="number" name="dp_options[index_slider][<?php echo $key; ?>][catch_font_size]" value="<?php echo esc_attr( $value['catch_font_size'] ); ?>" min="9" max="100" />
               <span class="icon icon_pc"></span>
              </label>
              <label class="font_size_label number_option">
               <input class="hankaku input_font_size" type="number" name="dp_options[index_slider][<?php echo $key; ?>][catch_font_size_mobile]" value="<?php echo esc_attr( $value['catch_font_size_mobile'] ); ?>" min="9" max="100" />
               <span class="icon icon_sp"></span>
              </label>
             </div>
            </li>
           </ul>

          </div><!-- END .sub_box_tab_content -->

         <ul class="button_list cf">
          <li style="float:right; margin:0;" class="delete-row"><a class="button-delete-row button-ml red_button" href="#"><?php echo __( 'Delete item', 'tcd-serum' ); ?></a></li>
         </ul>
        </div><!-- END .sub_box_content -->
       </div><!-- END .sub_box -->
       <?php
              endforeach;
            endif;
            $key = 'addindex';
            $value = array(
             'slider_type' => 'type1',
             'image' => false,
             'image_mobile' => false,
             'video' => '',
             'youtube' => '',
             'catch' => '',
             'catch_font_direction' => 'type2',
             'catch_font_size' => '34',
             'catch_font_size_mobile' => '24',
             'overlay_color' => '#000000',
             'overlay_opacity' => '0.3',
             'url' => '',
             'target' => '',
            );
            ob_start();
       ?>
       <div class="sub_box repeater-item repeater-item-<?php echo $key; ?>">
        <h4 class="theme_option_subbox_headline"><?php _e( 'New item', 'tcd-serum' ); ?></h4>
        <div class="sub_box_content tab_parent">

          <h4 class="theme_option_headline2"><?php _e('Slider type', 'tcd-serum');  ?></h4>
          <ul class="design_radio_button horizontal clearfix">
           <?php foreach ( $item_type_options as $option ) { ?>
           <li class="index_slider_item_<?php esc_attr_e( $option['value'] ); ?>">
            <input type="radio" id="index_slider_item_<?php esc_attr_e( $option['value'] ); ?>_<?php echo esc_attr( $key ); ?>" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][slider_type]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php checked( $value['slider_type'], $option['value'] ); ?> />
            <label for="index_slider_item_<?php esc_attr_e( $option['value'] ); ?>_<?php echo esc_attr( $key ); ?>"><?php echo $option['label']; ?></label>
           </li>
           <?php } ?>
          </ul>

          <div class="sub_box_tab">
           <div class="tab active" data-tab="tab1"><?php _e('Background', 'tcd-serum'); ?></div>
           <div class="tab" data-tab="tab2"><?php _e('Catchphrase', 'tcd-serum'); ?></div>
          </div>

          <?php // 背景 ----------------------- ?>
          <div class="sub_box_tab_content active" data-tab-content="tab1">

           <?php // video ----------------------- ?>
           <div class="index_slider_video_area" style="<?php if($value['slider_type'] == 'type2') { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
            <h4 class="theme_option_headline2"><?php _e('Video', 'tcd-serum');  ?></h4>
            <div class="theme_option_message2">
             <p><?php _e('Please upload MP4 format file.', 'tcd-serum');  ?></p>
             <p><?php _e('Web browser takes few second to load the data of video so we recommend to use loading screen if you want to display video.', 'tcd-serum'); ?></p>
            </div>
            <div class="cf cf_media_field hide-if-no-js index_slider<?php echo esc_attr( $key ); ?>_video">
             <input type="hidden" value="<?php if($value['video']) { echo esc_attr( $value['video'] ); }; ?>" id="index_slider<?php echo esc_attr( $key ); ?>_video" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][video]" class="cf_media_id">
             <div class="preview_field preview_field_video">
              <?php if($value['video']){ ?>
              <h4><?php _e( 'Uploaded MP4 file', 'tcd-serum' ); ?></h4>
              <p><?php echo esc_url(wp_get_attachment_url($value['video'])); ?></p>
              <?php }; ?>
             </div>
             <div class="buttton_area">
              <input type="button" value="<?php _e('Select MP4 file', 'tcd-serum'); ?>" class="cfmf-select-video button">
              <input type="button" value="<?php _e('Remove MP4 file', 'tcd-serum'); ?>" class="cfmf-delete-video button <?php if(!$value['video']){ echo 'hidden'; }; ?>">
             </div>
            </div>
           </div><!-- END .index_slider_video_area -->

           <?php // youtube ----------------------- ?>
           <div class="index_slider_youtube_area" style="<?php if($value['slider_type'] == 'type3') { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
            <h4 class="theme_option_headline2"><?php _e('Youtube', 'tcd-serum');  ?></h4>
            <div class="theme_option_message2">
             <p><?php _e('Please enter Youtube URL.', 'tcd-serum');  ?></p>
             <p><?php _e('Web browser takes few second to load the data of video so we recommend to use loading screen if you want to display video.', 'tcd-serum'); ?></p>
            </div>
            <input class="regular-text" type="text" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][youtube]" value="<?php echo esc_attr( $value['youtube'] ); ?>">
           </div><!-- END .index_slider_youtube_area -->

           <?php // 背景画像 ----------------------- ?>
           <h4 class="theme_option_headline2"><?php _e( 'Background image', 'tcd-serum' ); ?></h4>
           <div class="index_slider_video_image" style="<?php if($value['slider_type'] != 'type1') { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
            <div class="theme_option_message2">
             <p><?php _e('If the mobile device can\'t play video this image will be displayed instead.', 'tcd-serum');  ?></p>
            </div>
           </div>

           <ul class="option_list">
            <li class="cf">
             <span class="label">
              <?php _e('Background image', 'tcd-serum'); ?>
              <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '1450', '820'); ?></span>
             </span>
             <div class="image_box cf">
              <div class="cf cf_media_field hide-if-no-js index_slider_image<?php echo esc_attr( $key ); ?>">
               <input type="hidden" value="<?php if($value['image']) { echo esc_attr( $value['image'] ); }; ?>" id="index_slider_image<?php echo esc_attr( $key ); ?>" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][image]" class="cf_media_id">
               <div class="preview_field"><?php if($value['image']){ echo wp_get_attachment_image($value['image'], 'full'); }; ?></div>
               <div class="buttton_area">
                <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
                <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$value['image']){ echo 'hidden'; }; ?>">
               </div>
              </div>
             </div>
            </li>
            <li class="cf">
             <span class="label">
              <?php _e('Background image (mobile)', 'tcd-serum'); ?>
              <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '750', '1050'); ?></span>
             </span>
             <div class="image_box cf">
              <div class="cf cf_media_field hide-if-no-js index_slider_image_mobile<?php echo esc_attr( $key ); ?>">
               <input type="hidden" value="<?php if($value['image_mobile']) { echo esc_attr( $value['image_mobile'] ); }; ?>" id="index_slider_image_mobile<?php echo esc_attr( $key ); ?>" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][image_mobile]" class="cf_media_id">
               <div class="preview_field"><?php if($value['image_mobile']){ echo wp_get_attachment_image($value['image_mobile'], 'full'); }; ?></div>
               <div class="buttton_area">
                <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
                <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$value['image_mobile']){ echo 'hidden'; }; ?>">
               </div>
              </div>
             </div>
            </li>
           </ul>

           <?php // オーバーレイ ----------------------- ?>
           <h4 class="theme_option_headline2"><?php _e( 'Overlay', 'tcd-serum' ); ?></h4>
           <ul class="option_list">
            <li class="cf"><span class="label"><?php _e('Color of overlay', 'tcd-serum'); ?></span><input type="text" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][overlay_color]" value="<?php echo esc_attr( $value['overlay_color'] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
            <li class="cf">
             <span class="label"><?php _e('Transparency of overlay', 'tcd-serum'); ?></span><input class="hankaku index_slider_overlay_opacity<?php echo esc_attr( $key ); ?>" style="width:70px;" type="number" max="1" min="0" step="0.1" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][overlay_opacity]" value="<?php echo esc_attr( $value['overlay_opacity'] ); ?>" />
             <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
              <p><?php _e('Please specify the number of 0 from 0.9. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?>
              <?php _e('Please enter 0 if you don\'t want to use overlay.', 'tcd-serum');  ?></p>
             </div>
            </li>
           </ul>

           <?php // リンク ----------------------- ?>
           <h4 class="theme_option_headline2"><?php _e( 'Link', 'tcd-serum' ); ?></h4>
           <div class="theme_option_message2">
            <p><?php _e('Enter URL only if you want to add a link to the item. You can use it when setting banner images, etc.', 'tcd-serum');  ?></p>
           </div>
           <ul class="option_list">
            <li class="cf button_option">
             <span class="label"><?php _e('URL', 'tcd-serum'); ?></span>
             <div class="admin_link_option">
              <input class="full_width" type="text" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][url]" value="<?php echo esc_attr( $value['url'] ); ?>" placeholder="https://example.com/">
              <input id="index_slider_target<?php echo $key; ?>" class="admin_link_option_target" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][target]" type="checkbox" value="1" <?php checked( $value['target'], 1 ); ?>>
              <label for="index_slider_target<?php echo $key; ?>">&#xe92a;</label>
             </div>
            </li>
           </ul>

          </div><!-- END .sub_box_tab_content -->

          <?php // キャッチフレーズ ----------------------- ?>
          <div class="sub_box_tab_content" data-tab-content="tab2">

           <?php // キャッチフレーズ ----------------------- ?>
           <h4 class="theme_option_headline2"><?php _e( 'Catchphrase', 'tcd-serum' ); ?></h4>
           <textarea class="large-text" cols="50" rows="3" name="dp_options[index_slider][<?php echo esc_attr( $key ); ?>][catch]"><?php echo esc_textarea(  $value['catch'] ); ?></textarea>
           <ul class="option_list">
            <li class="cf">
             <span class="label"><?php _e('Font direction', 'tcd-serum');  ?></span>
             <div class="standard_radio_button">
              <?php foreach ( $font_direction_options as $option ) { ?>
              <input id="index_slider_catch_font_direction_<?php echo $key; ?>_<?php echo esc_attr($option['value']); ?>" type="radio" name="dp_options[index_slider][<?php echo $key; ?>][catch_font_direction]" value="<?php echo esc_attr($option['value']); ?>"<?php checked( $value['catch_font_direction'], $option['value'] ); ?>>
              <label for="index_slider_catch_font_direction_<?php echo $key; ?>_<?php echo esc_attr($option['value']); ?>"><?php echo esc_html($option['label']); ?></label>
              <?php } ?>
             </div>
            </li>
            <li class="cf">
             <span class="label"><?php _e('Font size', 'tcd-serum'); ?></span>
             <div class="font_size_option">
              <label class="font_size_label number_option">
               <input class="hankaku input_font_size" type="number" name="dp_options[index_slider][<?php echo $key; ?>][catch_font_size]" value="<?php echo esc_attr( $value['catch_font_size'] ); ?>" min="9" max="100" />
               <span class="icon icon_pc"></span>
              </label>
              <label class="font_size_label number_option">
               <input class="hankaku input_font_size" type="number" name="dp_options[index_slider][<?php echo $key; ?>][catch_font_size_mobile]" value="<?php echo esc_attr( $value['catch_font_size_mobile'] ); ?>" min="9" max="100" />
               <span class="icon icon_sp"></span>
              </label>
             </div>
            </li>
           </ul>

          </div><!-- END .sub_box_tab_content -->

         <ul class="button_list cf">
          <li style="float:right; margin:0;" class="delete-row"><a class="button-delete-row button-ml red_button" href="#"><?php echo __( 'Delete item', 'tcd-serum' ); ?></a></li>
         </ul>
        </div><!-- END .sub_box_content -->
       </div><!-- END .sub_box -->
       <?php
            $clone = ob_get_clean();
       ?>
      </div><!-- END .repeater -->
      <a href="#" class="button button-secondary button-add-row" data-clone="<?php echo htmlspecialchars( $clone ); ?>"><?php _e( 'Add item', 'tcd-serum' ); ?></a>
     </div><!-- END .repeater-wrapper -->
     <?php //繰り返しフィールドここまで ----- ?>

     <div class="sub_box cf" style="margin-top:25px;">
      <h3 class="theme_option_subbox_headline"><?php echo __('News ticker', 'tcd-serum'); ?></h3>
      <div class="sub_box_content">

       <div class="front_page_image">
        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/news_ticker_image.jpg" alt="" title="" />
       </div>

       <?php // ニュースティッカーの設定 ---------- ?>
       <p class="displayment_checkbox"><label><input name="dp_options[show_index_news]" type="checkbox" value="1" <?php checked( $options['show_index_news'], 1 ); ?>><?php _e( 'Display news ticker', 'tcd-serum' ); ?></label></p>
       <div style="<?php if($options['show_index_news'] == 1) { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
        <ul class="option_list">
         <li class="cf" style="border-top:1px dotted #ccc;">
          <span class="label"><?php _e('Post type', 'tcd-serum');  ?></span>
          <div class="standard_radio_button">
           <input id="index_news_post_type_post" type="radio" name="dp_options[index_news_post_type]" value="post" <?php checked( $options['index_news_post_type'], 'post' ); ?>>
           <label for="index_news_post_type_post"><?php echo esc_html($blog_label); ?></label>
           <input id="index_news_post_type_news" type="radio" name="dp_options[index_news_post_type]" value="news" <?php checked( $options['index_news_post_type'], 'news' ); ?>>
           <label for="index_news_post_type_news"><?php echo esc_html($news_label); ?></label>
          </div>
         </li>
         <li class="cf">
          <span class="label"><?php _e('Post order', 'tcd-serum');  ?></span>
          <div class="standard_radio_button">
           <input id="index_news_post_order_date" type="radio" name="dp_options[index_news_post_order]" value="date" <?php checked( $options['index_news_post_order'], 'date' ); ?>>
           <label for="index_news_post_order_date"><?php _e('Date', 'tcd-serum'); ?></label>
           <input id="index_news_post_order_rand" type="radio" name="dp_options[index_news_post_order]" value="rand" <?php checked( $options['index_news_post_order'], 'rand' ); ?>>
           <label for="index_news_post_order_rand"><?php _e('Random', 'tcd-serum'); ?></label>
          </div>
         </li>
        </ul>
       </div><!-- END .displayment_checkbox -->

      </div><!-- END .sub_box_content -->
     </div><!-- END .sub_box -->

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->

   <?php // コンテンツビルダー ここから ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■ ?>
   <div class="theme_option_field theme_option_field_ac open active <?php if($options['index_content_type'] == 'type1') { echo 'show_arrow'; }; ?>">
    <h3 class="theme_option_headline"><?php _e('Content builder', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <ul class="design_radio_button" style="margin-bottom:25px;">
      <li class="index_content_type1_button">
       <input type="radio" id="index_content_type1" name="dp_options[index_content_type]" value="type1" <?php checked( $options['index_content_type'], 'type1' ); ?> />
       <label for="index_content_type1"><?php _e('Use content builder', 'tcd-serum');  ?></label>
      </li>
      <li class="index_content_type2_button">
       <input type="radio" id="index_content_type2" name="dp_options[index_content_type]" value="type2" <?php checked( $options['index_content_type'], 'type2' ); ?> />
       <label for="index_content_type2"><?php _e('Use page content instead of content builder', 'tcd-serum');  ?></label>
      </li>
     </ul>

     <?php
          $front_page_id = get_option('page_on_front');
          if($front_page_id){
     ?>
     <div class="index_content_type2_option" style="<?php if($options['index_content_type'] == 'type2') { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
      <div class="theme_option_message2">
       <p><?php printf(__('Please set content from <a href="post.php?post=%s&action=edit" target="_blank">Front page edit screen</a>.', 'tcd-serum'), $front_page_id); ?></p>
      </div>
      <h4 class="theme_option_headline2"><?php _e('Content width', 'tcd-serum');  ?></h4>
      <ul class="option_list">
       <li class="cf">
        <span class="label"><?php _e('Content width type', 'tcd-serum'); ?></span>
        <div class="standard_radio_button">
         <input id="page_content_width_type1" type="radio" name="dp_options[page_content_width_type]" value="type1" <?php checked( $options['page_content_width_type'], 'type1' ); ?>>
         <label for="page_content_width_type1"><?php _e('Any width', 'tcd-serum'); ?></label>
         <input id="page_content_width_type2" type="radio" name="dp_options[page_content_width_type]" value="type2" <?php checked( $options['page_content_width_type'], 'type2' ); ?>>
         <label for="page_content_width_type2"><?php _e('Full screen width', 'tcd-serum'); ?></label>
        </div>
       </li>
       <li class="cf page_content_width_type1_option" style="<?php if($options['page_content_width_type'] == 'type1'){ echo 'display:block;'; } else {  echo 'display:none;'; }; ?>">
        <span class="label"><?php _e('Content width', 'tcd-serum'); ?></span><input class="hankaku page_content_width_input" style="width:100px;" type="number" name="dp_options[page_content_width]" value="<?php echo esc_attr($options['page_content_width']); ?>" /><span>px</span>
       </li>
      </ul>
      <ul class="button_list cf">
       <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      </ul>
     </div>
     <?php }; ?>

     <div class="index_content_type1_option" style="<?php if($options['index_content_type'] == 'type1') { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">

     <div class="theme_option_message no_arrow">
      <?php echo __( '<p>You can build contents freely with this function.</p><br /><p>STEP1: Click Add content button.<br />STEP2: Select content from dropdown menu.<br />STEP3: Input data and save the option.</p><br /><p>You can change order by dragging MOVE button and you can delete content by clicking DELETE button.</p>', 'tcd-serum' ); ?>
      <br>
      <p><?php _e('For headline and description that do not have font type or font size options, please adjust all at once from the font setting section of the basic settings ', 'tcd-serum');  ?></p>
     </div>
     <ul class="design_button_list cf">
      <li><a data-rel="lightcase:indexcb" href="<?php bloginfo('template_url'); ?>/admin/img/cb_box_content.jpg" title="<?php _e( 'Box content', 'tcd-serum' ); ?>"><?php _e( 'Box content', 'tcd-serum' ); ?></a></li>
      <li><a data-rel="lightcase:indexcb" href="<?php bloginfo('template_url'); ?>/admin/img/cb_carousel.jpg" title="<?php _e('Carousel', 'tcd-serum'); ?>"><?php _e('Carousel', 'tcd-serum'); ?></a></li>
     </ul>

     </div>

    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->

   <div class="contents_builder_wrap index_content_type1_option" style="<?php if($options['index_content_type'] == 'type1') { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">

    <div class="contents_builder">
     <p class="cb_message"><?php _e( 'Click Add content button to start content builder', 'tcd-serum' ); ?></p>
     <?php
          if (!empty($options['contents_builder'])) {
            foreach($options['contents_builder'] as $key => $content) :
              $cb_index = 'cb_'.$key.'_'.mt_rand(0,999999);
     ?>
     <div class="cb_row">
      <ul class="cb_button cf">
       <li><span class="cb_move"><?php echo __('Move', 'tcd-serum'); ?></span></li>
       <li><span class="cb_delete"><?php echo __('Delete', 'tcd-serum'); ?></span></li>
      </ul>
      <div class="cb_column_area cf">
       <div class="cb_column">
        <input type="hidden" class="cb_index" value="<?php echo $cb_index; ?>" />
        <?php the_cb_content_select($cb_index, $content['cb_content_select']); ?>
        <?php if (!empty($content['cb_content_select'])) the_cb_content_setting($cb_index, $content['cb_content_select'], $content); ?>
       </div>
      </div><!-- END .cb_column_area -->
     </div><!-- END .cb_row -->
     <?php
          endforeach;
         };
     ?>
    </div><!-- END .contents_builder -->
    <ul class="button_list cf cb_add_row_buttton_area">
     <li><input type="button" value="<?php echo __( 'Add content', 'tcd-serum' ); ?>" class="button-ml add_row"></li>
     <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
    </ul>

    <?php // コンテンツビルダー追加用 非表示 ?>
    <div class="contents_builder-clone hidden">
     <div class="cb_row">
      <ul class="cb_button cf">
       <li><span class="cb_move"><?php echo __('Move', 'tcd-serum'); ?></span></li>
       <li><span class="cb_delete"><?php echo __('Delete', 'tcd-serum'); ?></span></li>
      </ul>
      <div class="cb_column_area cf">
       <div class="cb_column">
        <input type="hidden" class="cb_index" value="cb_cloneindex" />
        <?php the_cb_content_select('cb_cloneindex'); ?>
       </div>
      </div><!-- END .cb_column_area -->
     </div><!-- END .cb_row -->
     <?php
          the_cb_content_setting('cb_cloneindex', 'box_content');
          the_cb_content_setting('cb_cloneindex', 'carousel');
          the_cb_content_setting('cb_cloneindex', 'free_space');
     ?>
    </div><!-- END .contents_builder-clone -->

   </div><!-- END .contents_builder_wrap -->
   <?php // コンテンツビルダーここまで ■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■ ?>


</div><!-- END .tab-content -->

<?php
} // END add_front_page_tab_panel()


// バリデーション　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_front_page_theme_options_validate( $input ) {

  global $dp_default_options, $item_type_options, $time_options, $font_type_options, $font_direction_options;

  // スプラッシュ画面
  $input['show_splash'] = ! empty( $input['show_splash'] ) ? 1 : 0;
  $input['splash_type'] = wp_filter_nohtml_kses( $input['splash_type'] );
  $input['splash_catch'] = wp_filter_nohtml_kses( $input['splash_catch'] );
  $input['splash_catch_direction'] = wp_filter_nohtml_kses( $input['splash_catch_direction'] );
  $input['splash_image'] = wp_filter_nohtml_kses( $input['splash_image'] );
  $input['splash_image_mobile'] = wp_filter_nohtml_kses( $input['splash_image_mobile'] );
  $input['splash_overlay_color'] = wp_filter_nohtml_kses( $input['splash_overlay_color'] );
  $input['splash_overlay_opacity'] = wp_filter_nohtml_kses( $input['splash_overlay_opacity'] );
  $input['splash_logo'] = wp_filter_nohtml_kses( $input['splash_logo'] );
  $input['splash_logo_retina'] = wp_filter_nohtml_kses( $input['splash_logo_retina'] );
  $input['splash_catch_font_size'] = wp_filter_nohtml_kses( $input['splash_catch_font_size'] );
  $input['splash_catch_font_size_sp'] = wp_filter_nohtml_kses( $input['splash_catch_font_size_sp'] );
  $input['splash_catch_font_color'] = wp_filter_nohtml_kses( $input['splash_catch_font_color'] );
  $input['splash_display_time'] = wp_filter_nohtml_kses( $input['splash_display_time'] );

  //スライダーの設定
  $index_slider = array();
  if ( isset( $input['index_slider'] ) && is_array( $input['index_slider'] ) ) {
    foreach ( $input['index_slider'] as $key => $value ) {
      $index_slider[] = array(
        'slider_type' => ( isset( $input['index_slider'][$key]['slider_type'] ) && array_key_exists( $input['index_slider'][$key]['slider_type'], $item_type_options ) ) ? $input['index_slider'][$key]['slider_type'] : 'type1',
        'image' => isset( $input['index_slider'][$key]['image'] ) ? wp_filter_nohtml_kses( $input['index_slider'][$key]['image'] ) : '',
        'image_mobile' => isset( $input['index_slider'][$key]['image_mobile'] ) ? wp_filter_nohtml_kses( $input['index_slider'][$key]['image_mobile'] ) : '',
        'video' => isset( $input['index_slider'][$key]['video'] ) ? wp_filter_nohtml_kses( $input['index_slider'][$key]['video'] ) : '',
        'youtube' => isset( $input['index_slider'][$key]['youtube'] ) ? wp_filter_nohtml_kses( $input['index_slider'][$key]['youtube'] ) : '',
        'catch' => isset( $input['index_slider'][$key]['catch'] ) ? wp_filter_nohtml_kses( $input['index_slider'][$key]['catch'] ) : '',
        'catch_font_direction' => ( isset( $input['index_slider'][$key]['catch_font_direction'] ) && array_key_exists( $input['index_slider'][$key]['catch_font_direction'], $font_direction_options ) ) ? $input['index_slider'][$key]['catch_font_direction'] : 'type2',
        'catch_font_size' => isset( $input['index_slider'][$key]['catch_font_size'] ) ? wp_filter_nohtml_kses( $input['index_slider'][$key]['catch_font_size'] ) : '34',
        'catch_font_size_mobile' => isset( $input['index_slider'][$key]['catch_font_size_mobile'] ) ? wp_filter_nohtml_kses( $input['index_slider'][$key]['catch_font_size_mobile'] ) : '20',
        'overlay_color' => isset( $input['index_slider'][$key]['overlay_color'] ) ? wp_filter_nohtml_kses( $input['index_slider'][$key]['overlay_color'] ) : '#000000',
        'overlay_opacity' => isset( $input['index_slider'][$key]['overlay_opacity'] ) ? wp_filter_nohtml_kses( $input['index_slider'][$key]['overlay_opacity'] ) : '0.3',
        'url' => isset( $input['index_slider'][$key]['url'] ) ? wp_filter_nohtml_kses( $input['index_slider'][$key]['url'] ) : '',
        'target' => isset( $input['index_slider'][$key]['target'] ) ? wp_filter_nohtml_kses( $input['index_slider'][$key]['target'] ) : '',
      );
    }
  };
  $input['index_slider'] = $index_slider;


  // ニュースティッカーの設定
  $input['show_index_news'] = ! empty( $input['show_index_news'] ) ? 1 : 0;
  $input['index_news_post_type'] = wp_kses_post( $input['index_news_post_type'] );
  $input['index_news_post_order'] = wp_filter_nohtml_kses( $input['index_news_post_order'] );


  // コンテンツビルダーの代わりに、固定ページのコンテンツを使う
  $input['index_content_type'] = wp_filter_nohtml_kses( $input['index_content_type'] );
  $input['page_content_width'] = wp_filter_nohtml_kses( $input['page_content_width'] );
  $input['page_content_width_type'] = wp_filter_nohtml_kses( $input['page_content_width_type'] );


  // コンテンツビルダー -----------------------------------------------------------------------------
  if (!empty($input['contents_builder'])) {

    $input_cb = $input['contents_builder'];
    $input['contents_builder'] = array();

    foreach($input_cb as $key => $value) {

      // クローン用はスルー
      //if (in_array($key, array('cb_cloneindex', 'cb_cloneindex2'))) continue;
      if (in_array($key, array('cb_cloneindex', 'cb_cloneindex2'), true)) continue;

      // ボックスコンテンツ -----------------------------------------------------------------------
      if ($value['cb_content_select'] == 'box_content') {

        if ( ! isset( $value['show_content'] ) )
          $value['show_content'] = null;
          $value['show_content'] = ( $value['show_content'] == 1 ? 1 : 0 );

        $value['catch'] = wp_filter_nohtml_kses( $value['catch'] );

        for ( $i = 1; $i <= 3; $i++ ):
          $value['headline'.$i] = wp_filter_nohtml_kses( $value['headline'.$i] );
          $value['desc'.$i] = wp_kses_post( $value['desc'.$i] );
          $value['button_label'.$i] = wp_filter_nohtml_kses( $value['button_label'.$i] );
          $value['button_target'.$i] = ! empty( $value['button_target'.$i] ) ? 1 : 0;
          $value['button_url'.$i] = wp_filter_nohtml_kses( $value['button_url'.$i] );
          $value['image'.$i] = wp_filter_nohtml_kses( $value['image'.$i] );
        endfor;

      // カルーセル -----------------------------------------------------------------------
      } elseif ($value['cb_content_select'] == 'carousel') {

        if ( ! isset( $value['show_content'] ) )
          $value['show_content'] = null;
          $value['show_content'] = ( $value['show_content'] == 1 ? 1 : 0 );

        $value['catch'] = wp_filter_nohtml_kses( $value['catch'] );

        $value['post_type'] = wp_filter_nohtml_kses( $value['post_type'] );
        $value['post_order'] = wp_filter_nohtml_kses( $value['post_order'] );
        $value['layout'] = wp_filter_nohtml_kses( $value['layout'] );

        $value['display_bg_color'] = wp_filter_nohtml_kses( $value['display_bg_color'] );

      // フリースペース -----------------------------------------------------------------------
      } elseif ($value['cb_content_select'] == 'free_space') {

        if ( ! isset( $value['show_content'] ) )
          $value['show_content'] = null;
          $value['show_content'] = ( $value['show_content'] == 1 ? 1 : 0 );

        if ( ! isset( $value['free_space'] )) {
          $value['free_space'] = null;
        } else {
          $value['free_space'] = $value['free_space'];
        }

        $value['display_bg_color'] = wp_filter_nohtml_kses( $value['display_bg_color'] );

      }

      $input['contents_builder'][] = $value;

    }

  } //コンテンツビルダーここまで -----------------------------------------------------------------------

  return $input;

};


/**
 * コンテンツビルダー用 コンテンツ選択プルダウン　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
 */
function the_cb_content_select($cb_index = 'cb_cloneindex', $selected = null) {

  $options = get_design_plus_option();

  $cb_content_select = array(
    'free_space' => __('Free space', 'tcd-serum'),
    'box_content' => __('Box content', 'tcd-serum'),
    'carousel' => __('Carousel', 'tcd-serum'),
  );

  if ($selected && isset($cb_content_select[$selected])) {
    $add_class = ' hidden';
  } else {
    $add_class = '';
  }

  $out = '<select name="dp_options[contents_builder]['.esc_attr($cb_index).'][cb_content_select]" class="cb_content_select'.$add_class.'">';
  $out .= '<option value="" style="padding-right: 10px;">'.__("Choose the content", "tcd-serum").'</option>';

  foreach($cb_content_select as $key => $value) {
    $attr = '';
    if ($key == $selected) {
      $attr = ' selected="selected"';
    }
    $out .= '<option value="'.esc_attr($key).'"'.$attr.' style="padding-right: 10px;">'.esc_html($value).'</option>';
  }

  $out .= '</select>';

  echo $out; 

}


/**
 * コンテンツビルダー用 コンテンツ設定　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
 */
function the_cb_content_setting($cb_index = 'cb_cloneindex', $cb_content_select = null, $value = array()) {

  global $blog_label, $font_type_options, $button_type_options, $button_border_radius_options, $button_size_options, $button_animation_options;
  $options = get_design_plus_option();
  $news_label = $options['news_label'] ? esc_html( $options['news_label'] ) : __( 'News', 'tcd-serum' );
  $treatment_label = $options['treatment_label'] ? esc_html( $options['treatment_label'] ) : __( 'treatment', 'tcd-serum' );

?>

<div class="cb_content_wrap cf <?php echo esc_attr($cb_content_select); ?>">

<?php
     // ボックスコンテンツ　-------------------------------------------------------------
     if ($cb_content_select == 'box_content') {

       if (!isset($value['show_content'])) { $value['show_content'] = 1; }

       if (!isset($value['catch'])) { $value['catch'] = ''; }

       for ( $i = 1; $i <= 3; $i++ ) :
         if (!isset($value['headline'.$i])) { $value['headline'.$i] = ''; }
         if (!isset($value['desc'.$i])) { $value['desc'.$i] = ''; }
         if (!isset($value['button_target'.$i])) { $value['button_target'.$i] = ''; }
         if (!isset($value['button_label'.$i])) { $value['button_label'.$i] = ''; }
         if (!isset($value['button_url'.$i])) { $value['button_url'.$i] = ''; }
         if (!isset($value['image'.$i])) { $value['image'.$i] = ''; }
       endfor;

?>

  <h3 class="cb_content_headline"><?php _e('Box content', 'tcd-serum'); ?><span class="cb_content_headline_sub_title"></span></h3>
  <label class="cb_content_switch"><div class="label_wrap"><input name="dp_options[contents_builder][<?php echo $cb_index; ?>][show_content]" type="checkbox" value="1" <?php checked( $value['show_content'], 1 ); ?>><span class="label"><span class="on">ON</span><span class="sep"></span><span class="off">OFF</span></span></div></label>

  <div class="cb_content tab_parent button_option_parent">

   <div class="cb_content_switch_target">

   <div class="cb_image">
    <img src="<?php bloginfo('template_url'); ?>/admin/img/cb_box_content_image.jpg" width="" height="" />
   </div>

   <h4 class="theme_option_headline2"><?php _e('Header', 'tcd-serum');  ?></h4>
   <ul class="option_list">
    <li class="cf"><span class="label"><span class="num">1</span><?php _e('Catchphrase', 'tcd-serum'); ?></span><textarea class="full_width cb-repeater-label" cols="50" rows="1" name="dp_options[contents_builder][<?php echo $cb_index; ?>][catch]"><?php echo esc_textarea(  $value['catch'] ); ?></textarea></li>
   </ul>

   <h4 class="theme_option_headline2"><?php _e('Box content', 'tcd-serum');  ?></h4>
   <div class="sub_box_tab">
    <div class="tab active" data-tab="tab1"><?php _e('Box content', 'tcd-serum'); ?>1</div>
    <div class="tab" data-tab="tab2"><?php _e('Box content', 'tcd-serum'); ?>2</div>
    <div class="tab" data-tab="tab3"><?php _e('Box content', 'tcd-serum'); ?>3</div>
   </div>

   <?php // ボックス ----------------------- ?>
   <?php for ( $i = 1; $i <= 3; $i++ ): ?>
   <div class="sub_box_tab_content<?php if($i == 1){ echo ' active'; }; ?>" data-tab-content="tab<?php echo $i; ?>">

     <ul class="option_list">
      <li class="cf">
       <span class="label">
        <span class="num">2</span>
        <?php _e('Image', 'tcd-serum'); ?>
        <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '646', '330'); ?></span>
       </span>
       <div class="image_box cf">
        <div class="cf cf_media_field hide-if-no-js image-<?php echo $cb_index; ?>-<?php echo $i; ?>">
         <input type="hidden" class="cf_media_id" name="dp_options[contents_builder][<?php echo $cb_index; ?>][image<?php echo $i; ?>]" id="image-<?php echo $cb_index; ?>-<?php echo $i; ?>" value="<?php echo esc_attr( $value['image'.$i] ); ?>">
         <div class="preview_field"><?php if ( $value['image'.$i] ) echo wp_get_attachment_image( $value['image'.$i], 'medium' ); ?></div>
         <div class="buttton_area">
          <input type="button" class="cfmf-select-img button" value="<?php _e( 'Select Image', 'tcd-serum' ); ?>">
          <input type="button" class="cfmf-delete-img button<?php if ( empty($value['image'.$i]) ) { echo ' hidden'; }; ?>" value="<?php _e( 'Remove Image', 'tcd-serum'); ?>">
         </div>
        </div>
       </div>
      </li>
      <li class="cf"><span class="label"><span class="num">3</span><?php _e('Headline', 'tcd-serum'); ?></span><input type="text" class="tab_label full_width" name="dp_options[contents_builder][<?php echo $cb_index; ?>][headline<?php echo $i; ?>]" value="<?php echo esc_html(  $value['headline'.$i] ); ?>" /></li>
      <li class="cf"><span class="label"><span class="num">4</span><?php _e('Description', 'tcd-serum'); ?></span><textarea class="full_width" cols="50" rows="2" name="dp_options[contents_builder][<?php echo $cb_index; ?>][desc<?php echo $i; ?>]"><?php echo esc_textarea(  $value['desc'.$i] ); ?></textarea></li>
      <li class="cf button_option"><span class="label"><span class="num">5</span><?php _e('Button label', 'tcd-serum');  ?></span><input class="full_width" type="text" name="dp_options[contents_builder][<?php echo $cb_index; ?>][button_label<?php echo $i; ?>]" value="<?php esc_attr_e( $value['button_label'.$i] ); ?>" /></li>
      <li class="cf button_option">
       <span class="label"><span class="num">5</span><?php _e('Button URL', 'tcd-serum'); ?></span>
       <div class="admin_link_option">
        <input type="text" name="dp_options[contents_builder][<?php echo $cb_index; ?>][button_url<?php echo $i; ?>]" placeholder="https://example.com/" value="<?php esc_attr_e( $value['button_url'.$i] ); ?>">
        <input id="button_target<?php echo $cb_index; ?>_<?php echo $i; ?>" class="admin_link_option_target" name="dp_options[contents_builder][<?php echo $cb_index; ?>][button_target<?php echo $i; ?>]" type="checkbox" value="1" <?php checked( $value['button_target'.$i], 1 ); ?>>
        <label for="button_target<?php echo $cb_index; ?>_<?php echo $i; ?>">&#xe92a;</label>
       </div>
      </li>
     </ul>
     <div class="theme_option_message2">
      <p><?php _e('You can set design of button from basic setting menu in theme option page.', 'tcd-serum');  ?></p>
     </div>

   </div><!-- END .sub_box_tab_content -->
   <?php endfor; ?>

   </div><!-- END .cb_content_switch_target -->

<?php
     // カルーセル　-------------------------------------------------------------
     } elseif ($cb_content_select == 'carousel') {

       if (!isset($value['show_content'])) { $value['show_content'] = 1; }

       if (!isset($value['catch'])) { $value['catch'] = ''; }

       if (!isset($value['post_type'])) { $value['post_type'] = 'post'; }
       if (!isset($value['post_order'])) { $value['post_order'] = 'date'; }
       if (!isset($value['layout'])) { $value['layout'] = 'type1'; }

       if (!isset($value['display_bg_color'])) { $value['display_bg_color'] = 'show'; }
?>

  <h3 class="cb_content_headline"><?php _e('Carousel', 'tcd-serum'); ?><span class="cb_content_headline_sub_title"></span></h3>
  <label class="cb_content_switch"><div class="label_wrap"><input name="dp_options[contents_builder][<?php echo $cb_index; ?>][show_content]" type="checkbox" value="1" <?php checked( $value['show_content'], 1 ); ?>><span class="label"><span class="on">ON</span><span class="sep"></span><span class="off">OFF</span></span></div></label>
  <div class="cb_content">

   <div class="cb_content_switch_target">

   <div class="cb_image">
    <img src="<?php bloginfo('template_url'); ?>/admin/img/cb_carousel_image.jpg" width="" height="" />
   </div>

   <h4 class="theme_option_headline_number"><span class="num">1</span><?php _e('Header', 'tcd-serum'); ?></h4>
   <ul class="option_list">
    <li class="cf"><span class="label"><?php _e('Catchphrase', 'tcd-serum'); ?></span><textarea class="full_width cb-repeater-label" cols="50" rows="1" name="dp_options[contents_builder][<?php echo $cb_index; ?>][catch]"><?php echo esc_textarea(  $value['catch'] ); ?></textarea></li>
   </ul>

   <h4 class="theme_option_headline_number"><span class="num">2</span><?php _e('Carousel', 'tcd-serum'); ?></h4>
   <ul class="option_list">
    <li class="cf">
     <span class="label"><?php _e('Layout', 'tcd-serum');  ?></span>
     <div class="standard_radio_button">
      <input id="carousel_layout_type1_<?php echo $cb_index; ?>" type="radio" name="dp_options[contents_builder][<?php echo $cb_index; ?>][layout]" value="type1" <?php checked( $value['layout'], 'type1' ); ?>>
      <label for="carousel_layout_type1_<?php echo $cb_index; ?>"><?php _e('Two column', 'tcd-serum'); ?></label>
      <input id="carousel_layout_type2_<?php echo $cb_index; ?>" type="radio" name="dp_options[contents_builder][<?php echo $cb_index; ?>][layout]" value="type2" <?php checked( $value['layout'], 'type2' ); ?>>
      <label for="carousel_layout_type2_<?php echo $cb_index; ?>"><?php _e('Three column', 'tcd-serum'); ?></label>
     </div>
    </li>

    <li class="cf">
     <span class="label"><?php _e('Post type', 'tcd-serum');  ?></span>
     <div class="standard_radio_button">
      <input id="carousel_post_type_post_<?php echo $cb_index; ?>" type="radio" name="dp_options[contents_builder][<?php echo $cb_index; ?>][post_type]" value="post" <?php checked( $value['post_type'], 'post' ); ?>>
      <label for="carousel_post_type_post_<?php echo $cb_index; ?>"><?php echo esc_html($blog_label); ?></label>
      <input id="carousel_post_type_news_<?php echo $cb_index; ?>" type="radio" name="dp_options[contents_builder][<?php echo $cb_index; ?>][post_type]" value="news" <?php checked( $value['post_type'], 'news' ); ?>>
      <label for="carousel_post_type_news_<?php echo $cb_index; ?>"><?php echo esc_html($news_label); ?></label>
     </div>
    </li>
    <li class="cf">
     <span class="label"><?php _e('Post order', 'tcd-serum');  ?></span>
     <div class="standard_radio_button">
      <input id="carousel_post_order_date_<?php echo $cb_index; ?>" type="radio" name="dp_options[contents_builder][<?php echo $cb_index; ?>][post_order]" value="date" <?php checked( $value['post_order'], 'date' ); ?>>
      <label for="carousel_post_order_date_<?php echo $cb_index; ?>"><?php _e('Date', 'tcd-serum'); ?></label>
      <input id="carousel_post_order_rand_<?php echo $cb_index; ?>" type="radio" name="dp_options[contents_builder][<?php echo $cb_index; ?>][post_order]" value="rand" <?php checked( $value['post_order'], 'rand' ); ?>>
      <label for="carousel_post_order_rand_<?php echo $cb_index; ?>"><?php _e('Random', 'tcd-serum'); ?></label>
     </div>
    </li>
    <li class="cf">
     <span class="label"><?php _e('Display background color', 'tcd-serum');  ?><span class="recommend_desc"><?php _e('Background color can be set from the color setting option.', 'tcd-serum'); ?></span></span>
     <div class="standard_radio_button">
      <input id="carousel_display_bg_color_show_<?php echo $cb_index; ?>" type="radio" name="dp_options[contents_builder][<?php echo $cb_index; ?>][display_bg_color]" value="show" <?php checked( $value['display_bg_color'], 'show' ); ?>>
      <label for="carousel_display_bg_color_show_<?php echo $cb_index; ?>"><?php _e('Display', 'tcd-serum'); ?></label>
      <input id="carousel_display_bg_color_hide_<?php echo $cb_index; ?>" type="radio" name="dp_options[contents_builder][<?php echo $cb_index; ?>][display_bg_color]" value="hide" <?php checked( $value['display_bg_color'], 'hide' ); ?>>
      <label for="carousel_display_bg_color_hide_<?php echo $cb_index; ?>"><?php _e('Hide', 'tcd-serum'); ?></label>
     </div>
    </li>
   </ul>

   </div><!-- END .cb_content_switch_target -->


<?php
     // フリースペース　-------------------------------------------------------------
     } elseif ($cb_content_select == 'free_space') {

       if (!isset($value['show_content'])) { $value['show_content'] = 1; }

       if (!isset($value['free_space'])) {
         $value['free_space'] = '';
       }

       if (!isset($value['catch'])) { $value['catch'] = ''; }

       if (!isset($value['display_bg_color'])) { $value['display_bg_color'] = 'hide'; }
?>
  <h3 class="cb_content_headline"><?php _e('Free space', 'tcd-serum');  ?><span class="cb_content_headline_sub_title"></span></h3>
  <label class="cb_content_switch"><div class="label_wrap"><input name="dp_options[contents_builder][<?php echo $cb_index; ?>][show_content]" type="checkbox" value="1" <?php checked( $value['show_content'], 1 ); ?>><span class="label"><span class="on">ON</span><span class="sep"></span><span class="off">OFF</span></span></div></label>
  <div class="cb_content">

   <div class="cb_content_switch_target">

   <h4 class="theme_option_headline2"><?php _e('Content', 'tcd-serum');  ?></h4>
   <?php
        wp_editor(
          $value['free_space'],
          'cb_wysiwyg_editor-' . $cb_index,
          array (
            'textarea_name' => 'dp_options[contents_builder][' . $cb_index . '][free_space]',
            'editor_class' => 'cb-repeater-label'
          )
       );
   ?>

   <h4 class="theme_option_headline2"><?php _e('Other setting', 'tcd-serum'); ?></h4>
   <ul class="option_list">
    <li class="cf">
     <span class="label"><?php _e('Display background color', 'tcd-serum');  ?><span class="recommend_desc"><?php _e('Background color can be set from the color setting option.', 'tcd-serum'); ?></span></span>
     <div class="standard_radio_button">
      <input id="access_display_bg_color_show_<?php echo $cb_index; ?>" type="radio" name="dp_options[contents_builder][<?php echo $cb_index; ?>][display_bg_color]" value="show" <?php checked( $value['display_bg_color'], 'show' ); ?>>
      <label for="access_display_bg_color_show_<?php echo $cb_index; ?>"><?php _e('Display', 'tcd-serum'); ?></label>
      <input id="access_display_bg_color_hide_<?php echo $cb_index; ?>" type="radio" name="dp_options[contents_builder][<?php echo $cb_index; ?>][display_bg_color]" value="hide" <?php checked( $value['display_bg_color'], 'hide' ); ?>>
      <label for="access_display_bg_color_hide_<?php echo $cb_index; ?>"><?php _e('Hide', 'tcd-serum'); ?></label>
     </div>
    </li>
   </ul>

   </div><!-- END .cb_content_switch_target -->

<?php
     // ボタンの表示　-------------------------------------------------------------
     } else {
?>
  <h3 class="cb_content_headline"><?php echo esc_html($cb_content_select); ?></h3>
  <div class="cb_content">

<?php
     }
?>

   <ul class="button_list cf">
    <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
    <li><a href="#" class="button-ml close-content"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
   </ul>

  </div><!-- END .cb_content -->

</div><!-- END .cb_content_wrap -->

<?php

} // END the_cb_content_setting()

/**
 * クローン用のリッチエディター化処理をしないようにする
 * クローン後のリッチエディター化はjsで行う
 */
function cb_tiny_mce_before_init_theme_option( $mceInit, $editor_id ) {
	if ( strpos( $editor_id, 'cb_cloneindex' ) !== false ) {
		$mceInit['wp_skip_init'] = true;
	}
	return $mceInit;
}
add_filter( 'tiny_mce_before_init', 'cb_tiny_mce_before_init_theme_option', 10, 2 );

?>