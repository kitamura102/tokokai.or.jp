<?php
/*
 * フッターの設定
 */


// Add default values
add_filter( 'before_getting_design_plus_option', 'add_footer_dp_default_options' );


// Add label of footer tab
add_action( 'tcd_tab_labels', 'add_footer_tab_label' );


// Add HTML of footer tab
add_action( 'tcd_tab_panel', 'add_footer_tab_panel' );


// Register sanitize function
add_filter( 'theme_options_validate', 'add_footer_theme_options_validate' );


// タブの名前
function add_footer_tab_label( $tab_labels ) {
	$tab_labels['footer'] = __( 'Footer', 'tcd-serum' );
	return $tab_labels;
}


// 初期値
function add_footer_dp_default_options( $dp_default_options ) {

	// 住所
  $dp_default_options['footer_address'] = '';

  //画像カルーセルの設定
	$dp_default_options['show_image_carousel'] = '1';
	$dp_default_options['image_carousel'] = array(
		array(
			"image" => "",
    ),
		array(
			"image" => "",
    ),
		array(
			"image" => "",
    ),
		array(
			"image" => "",
    ),
  );

  // アイコンフォントバナー
	$dp_default_options['show_footer_icon_banner'] = '1';
	$dp_default_options['footer_icon_banner'] = array(
		array(
			"title" => __( 'Button', 'tcd-serum' ),
			"url" => "#",
			"target" => "",
			"icon" => "mail",
    ),
		array(
			"title" => __( 'Button', 'tcd-serum' ),
			"url" => "#",
			"target" => "",
			"icon" => "calendar",
    ),
		array(
			"title" => __( 'Button', 'tcd-serum' ),
			"url" => "#",
			"target" => "",
			"icon" => "user",
    ),
  );

  // コピーライト
	$dp_default_options['copyright'] = 'Copyright &copy; ' . date('Y');

	// フッターバー
  $dp_default_options['footer_bar_type'] = 'type1';

  // アイコン付きメニュー
	$dp_default_options['footer_bar_btns'] = array();


	return $dp_default_options;

}


// 入力欄の出力　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_footer_tab_panel( $options ) {

  global $dp_default_options, $footer_bar_button_options, $footer_bar_icon_options, $footer_bar_type_options, $font_type_options, $logo_type_options, $bool_options;

?>

<div id="tab-content-footer" class="tab-content">

   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Footer customize', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <?php // 画像カルーセルの設定 ----------------------------------------------------- ?>
     <h3 class="theme_option_headline2"><?php _e('Image carousel', 'tcd-serum');  ?></h3>

     <p class="displayment_checkbox"><label><input name="dp_options[show_image_carousel]" type="checkbox" value="1" <?php checked( $options['show_image_carousel'], 1 ); ?>><?php _e( 'Display image carousel', 'tcd-serum' ); ?></label></p>

     <div style="<?php if($options['show_image_carousel'] == 1) { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">

      <div class="front_page_image middle">
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/footer_carousel_image.jpg" alt="" title="" />
      </div>

      <div class="theme_option_message">
       <p><?php _e('Click add image button to start this option.<br />You can change order by dragging each headline of option field.', 'tcd-serum');  ?></p>
       <p><?php _e('Image carousel will be used if registered item is more than 5. Please use as mini image gallery.', 'tcd-serum');  ?></p>
      </div>

      <?php //繰り返しフィールド ----- ?>
      <div class="repeater-wrapper">
       <input type="hidden" name="dp_options[image_carousel]" value="">
       <div class="repeater sortable" data-delete-confirm="<?php _e( 'Delete this image?', 'tcd-serum' ); ?>">
        <?php
             if ( $options['image_carousel'] ) :
               foreach ( $options['image_carousel'] as $key => $value ) :
        ?>
        <div class="sub_box repeater-item repeater-item-<?php echo esc_attr( $key ); ?>">
         <h4 class="theme_option_subbox_headline"><?php _e( 'Image', 'tcd-serum' ); echo $key+1; ?></h4>
         <div class="sub_box_content">
          <div class="theme_option_message2" style="margin-top:20px;">
           <p><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '323', '209'); ?></p>
          </div>
          <div class="image_box cf">
           <div class="cf cf_media_field hide-if-no-js image_carousel<?php echo esc_attr( $key ); ?>">
            <input type="hidden" value="<?php if ( $value['image'] ) echo esc_attr( $value['image'] ); ?>" id="image_carousel<?php echo esc_attr( $key ); ?>" name="dp_options[image_carousel][<?php echo esc_attr( $key ); ?>][image]" class="cf_media_id">
            <div class="preview_field"><?php if ( $value['image'] ) echo wp_get_attachment_image( $value['image'], 'medium'); ?></div>
            <div class="button_area">
             <input type="button" value="<?php _e( 'Select Image', 'tcd-serum' ); ?>" class="cfmf-select-img button">
             <input type="button" value="<?php _e( 'Remove Image', 'tcd-serum' ); ?>" class="cfmf-delete-img button <?php if ( ! $value['image'] ) echo 'hidden'; ?>">
            </div>
           </div>
          </div>
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
              'image' => '',
             );
             ob_start();
        ?>
        <div class="sub_box repeater-item repeater-item-<?php echo $key; ?>">
         <h4 class="theme_option_subbox_headline"><?php _e( 'New image', 'tcd-serum' ); ?></h4>
         <div class="sub_box_content">
          <div class="theme_option_message2" style="margin-top:20px;">
           <p><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '323', '209'); ?></p>
          </div>
          <div class="image_box cf">
           <div class="cf cf_media_field hide-if-no-js image_carousel<?php echo esc_attr( $key ); ?>">
            <input type="hidden" value="" id="image_carousel<?php echo esc_attr( $key ); ?>" name="dp_options[image_carousel][<?php echo esc_attr( $key ); ?>][image]" class="cf_media_id">
            <div class="preview_field"></div>
            <div class="button_area">
             <input type="button" value="<?php _e( 'Select Image', 'tcd-serum' ); ?>" class="cfmf-select-img button">
             <input type="button" value="<?php _e( 'Remove Image', 'tcd-serum' ); ?>" class="cfmf-delete-img button hidden">
            </div>
           </div>
          </div>
          <ul class="button_list cf">
           <li style="float:right; margin:0;" class="delete-row"><a class="button-delete-row button-ml red_button" href="#"><?php echo __( 'Delete item', 'tcd-serum' ); ?></a></li>
          </ul>
         </div><!-- END .sub_box_content -->
        </div><!-- END .sub_box -->
        <?php
             $clone = ob_get_clean();
        ?>
       </div><!-- END .repeater -->
       <a href="#" class="button button-secondary button-add-row" data-clone="<?php echo esc_attr( $clone ); ?>"><?php _e( 'Add image', 'tcd-serum' ); ?></a>
      </div><!-- END .repeater-wrapper -->
      <?php //繰り返しフィールドここまで ----- ?>

     </div>

     <?php // アイコンバナーの設定 ----------------------------------------------------- ?>

     <h3 class="theme_option_headline2"><?php _e('Icon banner', 'tcd-serum');  ?></h3>

     <p class="displayment_checkbox"><label><input name="dp_options[show_footer_icon_banner]" type="checkbox" value="1" <?php checked( $options['show_footer_icon_banner'], 1 ); ?>><?php _e( 'Display icon banner', 'tcd-serum' ); ?></label></p>

     <div style="<?php if($options['show_footer_icon_banner'] == 1) { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">

      <div class="front_page_image middle">
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/footer_icon_image.jpg" alt="" title="" />
      </div>

      <div class="theme_option_message">
       <p><?php _e('Click add banner button to start this option.<br />You can change order by dragging each headline of option field.', 'tcd-serum');  ?></p>
       <p><?php _e('We recommend registering up to 4 banners.', 'tcd-serum');  ?></p>
      </div>

      <?php //繰り返しフィールド ----- ?>
      <div class="repeater-wrapper">
       <input type="hidden" name="dp_options[footer_icon_banner]" value="">
       <div class="repeater sortable" data-delete-confirm="<?php _e( 'Delete this banner?', 'tcd-serum' ); ?>">
        <?php
             if ( $options['footer_icon_banner'] ) :
               foreach ( $options['footer_icon_banner'] as $key => $value ) :
        ?>
        <div class="sub_box repeater-item repeater-item-<?php echo esc_attr( $key ); ?>">
         <h4 class="theme_option_subbox_headline"><?php _e( 'Banner', 'tcd-serum' ); echo $key+1; ?></h4>
         <div class="sub_box_content">

          <h4 class="theme_option_headline2"><?php _e('Button', 'tcd-serum'); ?></h4>
          <ul class="option_list">
           <li class="cf"><span class="label"><?php _e('Title', 'tcd-serum'); ?></span><input class="full_width repeater-label" type="text" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][title]" value="<?php echo esc_attr( $value['title'] ); ?>"></li>
           <li class="cf button_option">
            <span class="label"><?php _e('URL', 'tcd-serum'); ?></span>
            <div class="admin_link_option">
             <input class="full_width" type="text" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][url]" value="<?php echo esc_attr( $value['url'] ); ?>" placeholder="https://example.com/">
             <input id="footer_icon_button_target<?php echo $key; ?>" class="admin_link_option_target" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][target]" type="checkbox" value="1" <?php checked( $value['target'], 1 ); ?>>
             <label for="footer_icon_button_target<?php echo $key; ?>">&#xe92a;</label>
            </div>
           </li>
          </ul>

          <div class="footer_bar_icon_option">
           <h4 class="theme_option_headline2"><?php _e('Icon', 'tcd-serum'); ?></h4>
           <ul class="footer_bar_icon_type cf">
            <?php foreach( $footer_bar_icon_options as $option ) : ?>
            <li><label><input type="radio" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][icon]" value="<?php echo esc_attr($option['value']); ?>" <?php checked( $option['value'], $value['icon'] ); ?>><span class="icon icon-<?php echo esc_attr($option['value']); ?>"></span></label></li>
            <?php endforeach; ?>
            <li class="wide material_icon"><label><input type="radio" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][icon]" value="material_icon" <?php checked( 'material_icon', $value['icon'] ); ?>><span class="icon_label"><?php _e( 'Others', 'tcd-serum' ); ?></span></label></li>
            <li class="wide"><label><input type="radio" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][icon]" value="no_icon" <?php checked( 'no_icon', $value['icon'] ); ?>><span class="icon_label"><?php _e( 'NO ICON', 'tcd-serum' ); ?></span></label></li>
           </ul>
           <div class="theme_option_message2 material_icon_option">
            <p><?php _e('Please enter any icon code from Google Fonts.<br><a href="https://fonts.google.com/icons?selected=Material+Symbols+Outlined:redo:FILL@0;wght@400;GRAD@0;opsz@24" target="_blank">Click here for a list of icons from Google Fonts.</a>', 'tcd-serum'); ?></p>
           </div>
           <input class="full_width material_icon_option"  style="display:none;" type="text" placeholder="<?php _e( 'ex: e88a', 'tcd-serum' ); ?>" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][material_icon]" value="<?php if(isset($value['material_icon'])){ echo esc_attr( $value['material_icon'] ); }; ?>">
          </div>

          <ul class="button_list cf">
           <li style="float:right; margin:0;" class="delete-row"><a class="button-delete-row button-ml red_button" href="#"><?php echo __( 'Delete banner', 'tcd-serum' ); ?></a></li>
          </ul>
         </div><!-- END .sub_box_content -->
        </div><!-- END .sub_box -->
        <?php
               endforeach;
             endif;
             $key = 'addindex';
             $value = array(
              'title' => '',
              'url' => '',
              'target' => '',
              'icon' => 'twitter',
              'material_icon' => '',
             );
             ob_start();
        ?>
        <div class="sub_box repeater-item repeater-item-<?php echo $key; ?>">
         <h4 class="theme_option_subbox_headline"><?php _e( 'New banner', 'tcd-serum' ); ?></h4>
         <div class="sub_box_content">

          <h4 class="theme_option_headline2"><?php _e('Button', 'tcd-serum'); ?></h4>
          <ul class="option_list">
           <li class="cf"><span class="label"><?php _e('Title', 'tcd-serum'); ?></span><input class="full_width repeater-label" type="text" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][title]" value="<?php echo esc_attr( $value['title'] ); ?>"></li>
           <li class="cf button_option">
            <span class="label"><?php _e('URL', 'tcd-serum'); ?></span>
            <div class="admin_link_option">
             <input class="full_width" type="text" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][url]" value="<?php echo esc_attr( $value['url'] ); ?>" placeholder="https://example.com/">
             <input id="footer_icon_button_target<?php echo $key; ?>" class="admin_link_option_target" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][target]" type="checkbox" value="1" <?php checked( $value['target'], 1 ); ?>>
             <label for="footer_icon_button_target<?php echo $key; ?>">&#xe92a;</label>
            </div>
           </li>
          </ul>

          <div class="footer_bar_icon_option">
           <h4 class="theme_option_headline2"><?php _e('Icon', 'tcd-serum'); ?></h4>
           <ul class="footer_bar_icon_type cf">
            <?php foreach( $footer_bar_icon_options as $option ) : ?>
            <li><label><input type="radio" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][icon]" value="<?php echo esc_attr($option['value']); ?>" <?php checked( $option['value'], $value['icon'] ); ?>><span class="icon icon-<?php echo esc_attr($option['value']); ?>"></span></label></li>
            <?php endforeach; ?>
            <li class="wide material_icon"><label><input type="radio" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][icon]" value="material_icon" <?php checked( 'material_icon', $value['icon'] ); ?>><span class="icon_label"><?php _e( 'Others', 'tcd-serum' ); ?></span></label></li>
            <li class="wide"><label><input type="radio" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][icon]" value="no_icon" <?php checked( 'no_icon', $value['icon'] ); ?>><span class="icon_label"><?php _e( 'NO ICON', 'tcd-serum' ); ?></span></label></li>
           </ul>
           <div class="theme_option_message2 material_icon_option" style="display:none;">
            <p><?php _e('Please enter any icon code from Google Fonts.<br><a href="https://fonts.google.com/icons?selected=Material+Symbols+Outlined:redo:FILL@0;wght@400;GRAD@0;opsz@24" target="_blank">Click here for a list of icons from Google Fonts.</a>', 'tcd-serum'); ?></p>
           </div>
           <input class="full_width material_icon_option"  style="display:none;" type="text" placeholder="<?php _e( 'ex: e88a', 'tcd-serum' ); ?>" name="dp_options[footer_icon_banner][<?php echo esc_attr( $key ); ?>][material_icon]" value="<?php if(isset($value['material_icon'])){ echo esc_attr( $value['material_icon'] ); }; ?>">
          </div>

          <ul class="button_list cf">
           <li style="float:right; margin:0;" class="delete-row"><a class="button-delete-row button-ml red_button" href="#"><?php echo __( 'Delete banner', 'tcd-serum' ); ?></a></li>
          </ul>
         </div><!-- END .sub_box_content -->
        </div><!-- END .sub_box -->
        <?php
             $clone = ob_get_clean();
        ?>
       </div><!-- END .repeater -->
       <a href="#" class="button button-secondary button-add-row" data-clone="<?php echo esc_attr( $clone ); ?>"><?php _e( 'Add banner', 'tcd-serum' ); ?></a>
      </div><!-- END .repeater-wrapper -->
      <?php //繰り返しフィールドここまで ----- ?>

     </div>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // ロゴエリアの設定 ------------------------------------------------------------ ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Footer main area', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <div class="front_page_image">
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/footer_main_image.jpg?2.0" alt="" title="" />
     </div>

     <h4 class="theme_option_headline_number"><span class="num">1</span><?php _e('Logo', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2">
      <p><?php echo __('"Square logo" set in "Basic setting" of theme option will be displayed on the left side of the footer on PC.', 'tcd-serum'); ?></p>
     </div>

     <h4 class="theme_option_headline_number"><span class="num">2</span><?php _e('Menu', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2">
      <p><?php echo __('Please set menu from <a href="./nav-menus.php" target="_blank">"Menu Screen"</a> in theme menu.', 'tcd-serum'); ?><br>
      <?php echo __('Footer menu 1, 2, 3, and 4 will be displayed vertically from left to right.', 'tcd-serum'); ?></p>
     </div>

     <h4 class="theme_option_headline_number"><span class="num">3</span><?php _e('Footer information', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2">
      <p>
       <?php _e('You can use this option by entering the store name, address, phone number, etc. for each line.', 'tcd-serum');  ?><br>
       <?php _e('In mobile size, it will be displayed with line breaks as entered, and in PC size, it will be displayed in one line on the copyright.', 'tcd-serum');  ?>
      </p>
     </div>
     <textarea class="large-text" cols="50" rows="3" name="dp_options[footer_address]"><?php echo esc_textarea(  $options['footer_address'] ); ?></textarea>

     <h4 class="theme_option_headline_number"><span class="num">4</span><?php _e('Copyright', 'tcd-serum'); ?></h4>
     <input class="full_width" type="text" name="dp_options[copyright]" value="<?php echo esc_attr( $options['copyright'] ); ?>" />

     <h4 class="theme_option_headline_number"><span class="num">5</span><?php _e('SNS icon', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2">
      <p><?php _e('The SNS icon displayed at the bottom of the left sidebar on PC size and on the copyright in the footer on mobile size can be set in the basic settings.', 'tcd-serum'); ?></p>
     </div>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // フッターバーの設定 -------------------------------------------------------------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e( 'Footer bar (mobile device only)', 'tcd-serum' ); ?></h3>
    <div class="theme_option_field_ac_content">

      <div class="theme_option_message2"><p><?php _e( 'Footer bar will only be displayed at mobile device.', 'tcd-serum' ); ?></div>

      <h4 class="theme_option_headline2"><?php _e('Footer bar type', 'tcd-serum'); ?></h4>
      <?php echo tcd_admin_image_radio_button($options, 'footer_bar_type', $footer_bar_type_options) ?>

      <div class="theme_option_message2 footer_bar_not_type4_option">
        <p><?php _e( 'You can display the button with icon. (We recommend you to set max 4 buttons.)', 'tcd-serum' ); ?></p>
      </div>
      <div class="theme_option_message2 footer_bar_type4_option">
        <p><?php _e( 'Simple buttons without icons can be displayed. (We recommend you to set max 2 buttons.)', 'tcd-serum' ); ?></p>
      </div>

      <h4 class="theme_option_headline2"><?php _e('Settings for the contents of the footer bar', 'tcd-serum'); ?></h4>
      <div class="theme_option_message" style="margin-top:10px;">
        <p><?php _e( 'Click "Add item", and set the button for footer bar. You can drag the item to change their order.', 'tcd-serum' ); ?></p>
      </div>
        
      <div class="repeater-wrapper">
        <input type="hidden" name="dp_options[footer_bar_btns]" value="">
        <div class="repeater sortable" data-delete-confirm="<?php _e('Delete?', 'tcd-serum'); ?>">
          <?php
                if ( $options['footer_bar_btns'] ) :
                  foreach ( $options['footer_bar_btns'] as $key => $value ) :  
          ?>
          <div class="sub_box repeater-item repeater-item-<?php echo esc_attr( $key ); ?>">
            <h4 class="theme_option_subbox_headline"><?php echo esc_attr( $value['label'] ); ?></h4>
            <div class="sub_box_content">

              <h4 class="theme_option_headline2"><?php _e('Button type', 'tcd-serum'); ?></h4>
              <?php foreach ( $footer_bar_button_options as $option ) { ?>
              <span class="simple_radio_button spacer"></span>
              <input type="radio" id="footer_bar_btns_<?php echo esc_attr( $option['value'] ).'_'.esc_attr( $key ); ?>" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][type]" value="<?php echo esc_attr( $option['value'] ); ?>" <?php checked( $value['type'], $option['value'] ); ?> />
              <label for="footer_bar_btns_<?php echo esc_attr( $option['value'] ).'_'.esc_attr( $key ); ?>"><?php echo esc_html( $option['label'] ); ?></label></br>
              <?php } ?>

              <div class="theme_option_message2 footer_bar_btns_type1_option" style="margin-top:20px;">
                <p><?php _e( 'You can set link URL.', 'tcd-serum' ); ?></p>
              </div>
              
              <div class="theme_option_message2 footer_bar_btns_type2_option" style="margin-top:20px;">
                <p><?php _e( 'Share buttons are displayed if you tap this button.', 'tcd-serum' ); ?></p>
              </div>
              
              <div class="theme_option_message2 footer_bar_btns_type3_option" style="margin-top:20px;">
                <p><?php _e( 'You can call this number.', 'tcd-serum' ); ?></p>
              </div>

              <h4 class="theme_option_headline2"><?php _e('Button setting', 'tcd-serum'); ?></h4>
              <ul class="option_list">
                <li class="cf"><span class="label"><?php _e('Label', 'tcd-serum'); ?></span><input class="full_width repeater-label" type="text" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][label]" value="<?php echo esc_attr( $value['label'] ); ?>"></li>
                <li class="cf footer_bar_btns_type1_option"><span class="label"><?php _e('URL', 'tcd-serum'); ?></span><input class="full_width" type="text" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][url]" value="<?php echo esc_attr( $value['url'] ); ?>" placeholder="https://example.com/"></li>
                <li class="cf footer_bar_btns_type3_option"><span class="label"><?php _e('Phone number', 'tcd-serum'); ?></span><input class="full_width" type="text" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][number]" value="<?php echo esc_attr( $value['number'] ); ?>" placeholder="000-0000-0000"></li>
                <li class="cf footer_bar_type4_option"><span class="label"><?php _e('Background color', 'tcd-serum'); ?></span><input class="c-color-picker" type="text" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][color]" value="<?php echo esc_attr( $value['color'] ); ?>" data-default-color="#000000"></li>
              </ul>

              <div class="footer_bar_icon_option footer_bar_not_type4_option">
               <h4 class="theme_option_headline2"><?php _e('Icon', 'tcd-serum'); ?></h4>
               <ul class="footer_bar_icon_type cf">
                <?php foreach( $footer_bar_icon_options as $option ) : ?>
                <li><label><input type="radio" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][icon]" value="<?php echo esc_attr($option['value']); ?>" <?php checked( $option['value'], $value['icon'] ); ?>><span class="icon icon-<?php echo esc_attr($option['value']); ?>"></span></label></li>
                <?php endforeach; ?>
                <li class="wide material_icon"><label><input type="radio" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][icon]" value="material_icon" <?php checked( 'material_icon', $value['icon'] ); ?>><span class="icon_label"><?php _e( 'Others', 'tcd-serum' ); ?></span></label></li>
               </ul>
               <div class="theme_option_message2 material_icon_option">
                <p><?php _e('Please enter any icon code from Google Fonts.<br><a href="https://fonts.google.com/icons?selected=Material+Symbols+Outlined:redo:FILL@0;wght@400;GRAD@0;opsz@24" target="_blank">Click here for a list of icons from Google Fonts.</a>', 'tcd-serum'); ?></p>
               </div>
               <input class="full_width material_icon_option"  style="display:none;" type="text" placeholder="<?php _e( 'ex: e88a', 'tcd-serum' ); ?>" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][material_icon]" value="<?php if(isset($value['material_icon'])){ echo esc_attr( $value['material_icon'] ); }; ?>">
              </div>

              <ul class="button_list cf">
                <li><a class="close_sub_box button-ml" href="#"><?php _e('Close', 'tcd-serum'); ?></a></li>
                <li style="float:right; margin:0;" class="delete-row"><a class="button-delete-row button-ml red_button" href="#"><?php _e('Delete item', 'tcd-serum'); ?></a></li>
              </ul>
            </div>
          </div>
          <?php
                  endforeach;
                endif;
                $key = 'addindex';
                $value = array(
                  'type' => 'type1',
                  'label' => '',
                  'url' => '',
                  'number' => '',
                  'icon' => 'twitter',
                  'material_icon' => '',
                  'color' => '#000000'
                );
                ob_start();
          ?>
          <div class="sub_box repeater-item repeater-item-<?php echo $key; ?>">
            <h4 class="theme_option_subbox_headline"><?php _e('New item', 'tcd-serum'); ?></h4>
            <div class="sub_box_content">

              <h4 class="theme_option_headline2"><?php _e('Button type', 'tcd-serum'); ?></h4>
              <?php foreach ( $footer_bar_button_options as $option ) { ?>
              <span class="simple_radio_button spacer"></span>
              <input type="radio" id="footer_bar_btns_<?php echo esc_attr( $option['value'] ).'_'.esc_attr( $key ); ?>" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][type]" value="<?php echo esc_attr( $option['value'] ); ?>" <?php checked( $value['type'], $option['value'] ); ?> />
              <label for="footer_bar_btns_<?php echo esc_attr( $option['value'] ).'_'.esc_attr( $key ); ?>"><?php echo esc_html( $option['label'] ); ?></label></br>
              <?php } ?>

              <div class="theme_option_message2 footer_bar_btns_type1_option" style="margin-top:20px;">
                <p><?php _e( 'You can set link URL.', 'tcd-serum' ); ?></p>
              </div>
              
              <div class="theme_option_message2 footer_bar_btns_type2_option" style="margin-top:20px;">
                <p><?php _e( 'Share buttons are displayed if you tap this button.', 'tcd-serum' ); ?></p>
              </div>
              
              <div class="theme_option_message2 footer_bar_btns_type3_option" style="margin-top:20px;">
                <p><?php _e( 'You can call this number.', 'tcd-serum' ); ?></p>
              </div>

              <h4 class="theme_option_headline2"><?php _e('Button setting', 'tcd-serum'); ?></h4>
              <ul class="option_list">
               <li class="cf"><span class="label"><?php _e('Label', 'tcd-serum'); ?></span><input class="full_width repeater-label" type="text" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][label]" value=""></li>
               <li class="cf footer_bar_btns_type1_option"><span class="label"><?php _e('URL', 'tcd-serum'); ?></span><input class="full_width" type="text" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][url]" value="" placeholder="https://example.com/"></li>
               <li class="cf footer_bar_btns_type3_option"><span class="label"><?php _e('Phone number', 'tcd-serum'); ?></span><input class="full_width" type="text" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][number]" value="" placeholder="000-0000-0000"></li>
               <li class="cf footer_bar_type4_option"><span class="label"><?php _e('Background color', 'tcd-serum'); ?></span><input class="c-color-picker" type="text" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][color]" value="<?php echo esc_attr( $value['color'] ); ?>" data-default-color="#000000"></li>
              </ul>

              <div class="footer_bar_icon_option footer_bar_not_type4_option">
               <h4 class="theme_option_headline2"><?php _e('Icon', 'tcd-serum'); ?></h4>
               <ul class="footer_bar_icon_type cf">
                <?php foreach( $footer_bar_icon_options as $option ) : ?>
                <li><label><input type="radio" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][icon]" value="<?php echo esc_attr($option['value']); ?>" <?php checked( $option['value'], $value['icon'] ); ?>><span class="icon icon-<?php echo esc_attr($option['value']); ?>"></span></label></li>
                <?php endforeach; ?>
                <li class="wide material_icon"><label><input type="radio" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][icon]" value="material_icon" <?php checked( 'material_icon', $value['icon'] ); ?>><span class="icon_label"><?php _e( 'Others', 'tcd-serum' ); ?></span></label></li>
               </ul>
               <div class="theme_option_message2 material_icon_option" style="display:none;">
                <p><?php _e('Please enter any icon code from Google Fonts.<br><a href="https://fonts.google.com/icons?selected=Material+Symbols+Outlined:redo:FILL@0;wght@400;GRAD@0;opsz@24" target="_blank">Click here for a list of icons from Google Fonts.</a>', 'tcd-serum'); ?></p>
               </div>
               <input class="full_width material_icon_option"  style="display:none;" type="text" placeholder="<?php _e( 'ex: e88a', 'tcd-serum' ); ?>" name="dp_options[footer_bar_btns][<?php echo esc_attr( $key ); ?>][material_icon]" value="<?php if(isset($value['material_icon'])){ echo esc_attr( $value['material_icon'] ); }; ?>">
              </div>

              <ul class="button_list cf">
                <li><a class="close_sub_box button-ml" href="#"><?php _e('Close', 'tcd-serum'); ?></a></li>
                <li style="float:right; margin:0;" class="delete-row"><a class="button-delete-row button-ml red_button" href="#"><?php _e('Delete item', 'tcd-serum'); ?></a></li>
              </ul>
            </div>
          </div>
          <?php
                $clone = ob_get_clean();
          ?>
        </div><!-- END .repeater -->
        <a href="#" class="button button-secondary button-add-row" data-clone="<?php echo esc_attr( $clone ); ?>"><?php _e('Add item', 'tcd-serum'); ?></a>
      </div><!-- END .repeater-wrapper -->

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php _e('Close', 'tcd-serum'); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->

</div><!-- END .tab-content -->

<?php
} // END add_footer_tab_panel()


// バリデーション　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_footer_theme_options_validate( $input ) {

  global $dp_default_options, $footer_bar_button_options, $footer_bar_icon_options, $footer_bar_type_options, $font_type_options, $logo_type_options;

  // 画像カルーセルの設定
  $input['show_image_carousel'] = ! empty( $input['show_image_carousel'] ) ? 1 : 0;
  $image_carousel = array();
  if ( isset( $input['image_carousel'] ) && is_array( $input['image_carousel'] ) ) {
    foreach ( $input['image_carousel'] as $key => $value ) {
      $image_carousel[] = array(
        'image' => isset( $input['image_carousel'][$key]['image'] ) ? wp_filter_nohtml_kses( $input['image_carousel'][$key]['image'] ) : '',
      );
    }
  };
  $input['image_carousel'] = $image_carousel;


  // アイコンバナーの設定
  $input['show_footer_icon_banner'] = ! empty( $input['show_footer_icon_banner'] ) ? 1 : 0;
  $footer_icon_banner = array();
  if ( isset( $input['footer_icon_banner'] ) && is_array( $input['footer_icon_banner'] ) ) {
    foreach ( $input['footer_icon_banner'] as $key => $value ) {
      $footer_icon_banner[] = array(
        'title' => isset( $input['footer_icon_banner'][$key]['title'] ) ? wp_filter_nohtml_kses( $input['footer_icon_banner'][$key]['title'] ) : '',
        'url' => isset( $input['footer_icon_banner'][$key]['url'] ) ? wp_filter_nohtml_kses( $input['footer_icon_banner'][$key]['url'] ) : '',
        'target' => isset( $input['footer_icon_banner'][$key]['target'] ) ? wp_filter_nohtml_kses( $input['footer_icon_banner'][$key]['target'] ) : '',
        'icon' => isset( $input['footer_icon_banner'][$key]['icon'] ) ? wp_filter_nohtml_kses( $input['footer_icon_banner'][$key]['icon'] ) : '',
        'material_icon' => isset( $input['footer_icon_banner'][$key]['material_icon'] ) ? wp_filter_nohtml_kses( $input['footer_icon_banner'][$key]['material_icon'] ) : '',
      );
    }
  };
  $input['footer_icon_banner'] = $footer_icon_banner;


  // 住所
  $input['footer_address'] = wp_kses_post($input['footer_address']);


  // コピーライト
  $input['copyright'] = wp_kses_post($input['copyright']);


  // スマホ用固定フッターバーの設定
  if ( ! isset( $input['footer_bar_type'] ) || ! array_key_exists( $input['footer_bar_type'], $footer_bar_type_options ) )
    $input['footer_bar_type'] = $dp_default_options['footer_bar_type'];

  $footer_bar_btns = array();
  if ( isset( $input['footer_bar_btns'] ) && is_array( $input['footer_bar_btns'] ) ) {
    foreach ( $input['footer_bar_btns'] as $key => $value ) {
      $footer_bar_btns[] = array(
        'type' => ( isset( $input['footer_bar_btns'][$key]['type'] ) && array_key_exists( $input['footer_bar_btns'][$key]['type'], $footer_bar_button_options ) ) ? $input['footer_bar_btns'][$key]['type'] : 'type1',
        'label' => isset( $input['footer_bar_btns'][$key]['label'] ) ? wp_filter_nohtml_kses( $input['footer_bar_btns'][$key]['label'] ) : '',
        'url' => isset( $input['footer_bar_btns'][$key]['url'] ) ? wp_filter_nohtml_kses( $input['footer_bar_btns'][$key]['url'] ) : '',
        'number' => isset( $input['footer_bar_btns'][$key]['number'] ) ? wp_filter_nohtml_kses( $input['footer_bar_btns'][$key]['number'] ) : '',
        'color' => isset( $input['footer_bar_btns'][$key]['color'] ) ? sanitize_hex_color( $input['footer_bar_btns'][$key]['color'] ) : '',
        'icon' => isset( $input['footer_bar_btns'][$key]['icon'] ) ? wp_filter_nohtml_kses( $input['footer_bar_btns'][$key]['icon'] ) : '',
        'material_icon' => isset( $input['footer_bar_btns'][$key]['material_icon'] ) ? wp_filter_nohtml_kses( $input['footer_bar_btns'][$key]['material_icon'] ) : '',
      );
    };
  };
  $input['footer_bar_btns'] = $footer_bar_btns;

	return $input;

};


?>