<?php

/* フォーム用 画像フィールド出力 */
function mlcf_media_form($cf_key, $label) {
	global $post;
	if (empty($cf_key)) return false;
	if (empty($label)) $label = $cf_key;

	$media_id = get_post_meta($post->ID, $cf_key, true);
?>
 <div class="image_box cf">
  <div class="cf cf_media_field hide-if-no-js <?php echo esc_attr($cf_key); ?>">
    <input type="hidden" class="cf_media_id" name="<?php echo esc_attr($cf_key); ?>" id="<?php echo esc_attr($cf_key); ?>" value="<?php echo esc_attr($media_id); ?>" />
    <div class="preview_field"><?php if ($media_id) the_mlcf_image($post->ID, $cf_key); ?></div>
    <div class="buttton_area">
     <input type="button" class="cfmf-select-img button" value="<?php _e('Select Image', 'tcd-serum'); ?>" />
     <input type="button" class="cfmf-delete-img button<?php if (!$media_id) echo ' hidden'; ?>" value="<?php _e('Remove Image', 'tcd-serum'); ?>" />
    </div>
  </div>
 </div>
<?php
}




/* 画像フィールドで選択された画像をimgタグで出力 */
function the_mlcf_image($post_id, $cf_key, $image_size = 'medium') {
	echo get_mlcf_image($post_id, $cf_key, $image_size);
}

/* 画像フィールドで選択された画像をimgタグで返す */
function get_mlcf_image($post_id, $cf_key, $image_size = 'medium') {
	global $post;
	if (empty($cf_key)) return false;
	if (empty($post_id)) $post_id = $post->ID;

	$media_id = get_post_meta($post_id, $cf_key, true);
	if ($media_id) {
		return wp_get_attachment_image($media_id, $image_size, $image_size);
	}

	return false;
}

/* 画像フィールドで選択された画像urlを返す */
function get_mlcf_image_url($post_id, $cf_key, $image_size = 'medium') {
	global $post;
	if (empty($cf_key)) return false;
	if (empty($post_id)) $post_id = $post->ID;

	$media_id = get_post_meta($post_id, $cf_key, true);
	if ($media_id) {
		$img = wp_get_attachment_image_src($media_id, $image_size);
		if (!empty($img[0])) {
			return $img[0];
		}
	}

	return false;
}

/* 画像フィールドで選択されたメディアのURLを出力 */
function the_mlcf_media_url($post_id, $cf_key) {
	echo get_mlcf_media_url($post_id, $cf_key);
}

/* 画像フィールドで選択されたメディアのURLを返す */
function get_mlcf_media_url($post_id, $cf_key) {
	global $post;
	if (empty($cf_key)) return false;
	if (empty($post_id)) $post_id = $post->ID;

	$media_id = get_post_meta($post_id, $cf_key, true);
	if ($media_id) {
		return wp_get_attachment_url($media_id);
	}

	return false;
}


// ヘッダーの設定 -------------------------------------------------------

function page_header_meta_box() {
  add_meta_box(
    'tcd_meta_box1',//ID of meta box
    __('Page setting', 'tcd-serum'),//label
    'show_page_header_meta_box',//callback function
    'page',// post type
    'normal',// context
    'high'// priority
  );
}
add_action('add_meta_boxes', 'page_header_meta_box');

function show_page_header_meta_box() {

  global $post, $font_type_options, $font_direction_options;

  // ヘッダーの設定
  $page_catch_font_size = get_post_meta($post->ID, 'page_catch_font_size', true) ?  get_post_meta($post->ID, 'page_catch_font_size', true) : '32';
  $page_catch_font_size_sp = get_post_meta($post->ID, 'page_catch_font_size_sp', true) ?  get_post_meta($post->ID, 'page_catch_font_size_sp', true) : '20';
  $page_catch_direction = get_post_meta($post->ID, 'page_catch_direction', true) ?  get_post_meta($post->ID, 'page_catch_direction', true) : 'type1';
  $overlay_color = get_post_meta($post->ID, 'overlay_color', true) ?  get_post_meta($post->ID, 'overlay_color', true) : '#000000';
  $overlay_color_opacity = get_post_meta($post->ID, 'overlay_color_opacity', true) ?  get_post_meta($post->ID, 'overlay_color_opacity', true) : '0.3';
  if($overlay_color_opacity == 'zero'){
    $overlay_color_opacity = '0';
  }
  $header_type = get_post_meta($post->ID, 'header_type', true) ?  get_post_meta($post->ID, 'header_type', true) : 'type1';

  // 表示設定
  $hide_page_header = get_post_meta($post->ID, 'hide_page_header', true) ?  get_post_meta($post->ID, 'hide_page_header', true) : 'no';
  $hide_page_header_bar = get_post_meta($post->ID, 'hide_page_header_bar', true) ?  get_post_meta($post->ID, 'hide_page_header_bar', true) : 'no';
  $hide_page_side_bar = get_post_meta($post->ID, 'hide_page_side_bar', true) ?  get_post_meta($post->ID, 'hide_page_side_bar', true) : 'no';
  $page_hide_footer = get_post_meta($post->ID, 'page_hide_footer', true) ?  get_post_meta($post->ID, 'page_hide_footer', true) : 'no';
  $page_hide_bread = get_post_meta($post->ID, 'page_hide_bread', true) ?  get_post_meta($post->ID, 'page_hide_bread', true) : 'no';

  $hide_logo = get_post_meta($post->ID, 'hide_logo', true) ?  get_post_meta($post->ID, 'hide_logo', true) : 'no';

  $hide_header_message = get_post_meta($post->ID, 'hide_header_message', true);
  if(empty($hide_header_message)){
    $hide_header_message = 'yes';
  }

  $page_width = get_post_meta($post->ID, 'page_width', true);
  if(empty($page_width)){
    $page_width = 'normal';
  }

  // FAQ
  $faq_list = get_post_meta($post->ID, 'faq_list', true);


  // 代表者の情報
  $representative_user = get_post_meta($post->ID, 'representative_user', true) ?  get_post_meta($post->ID, 'representative_user', true) : 'hide';

  // 投稿者
  $staff_list_order = get_post_meta($post->ID, 'staff_list_order', true);
  if (empty($staff_list_order) || !is_array($staff_list_order)) {
    $staff_list_order = array();
  }

  $users = get_users(array(
    'fields' => array('ID'),
    'role__not_in' => array('subscriber','contributor'),
    'meta_key' => 'staff_page_displayment',
    'meta_value' => 'type2',
    'orderby' => 'ID',
    'order' => 'ASC'
  ));

  if ($users) {
    $user_ids = array();
    foreach ($users as $user) {
      $user_ids[] = $user->ID;
    }

    if ($staff_list_order) {
      foreach ($staff_list_order as $key => $author_id) {
        if (!in_array($author_id, $user_ids)) {
          unset($staff_list_order[$key]);
        }
      }
    }

    foreach ($user_ids as $user_id) {
      if (!in_array($user_id, $staff_list_order)) {
        $staff_list_order[] = $user_id;
      }
    }

    unset($user_ids, $user_id);
  } else {
    $staff_list_order = array();
  }
  unset($users);

  echo '<input type="hidden" name="page_header_custom_fields_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

  //入力欄 ***************************************************************************************************************************************************************************************
?>

<?php
     // WP5.0対策として隠しフィールドを用意　選択されているページテンプレートによってLPページ用入力欄を表示・非表示する
     if ( count( get_page_templates( $post ) ) > 0 && get_option( 'page_for_posts' ) != $post->ID ) :
       $template = ! empty( $post->page_template ) ? $post->page_template : false;
?>
<select name="hidden_page_template" id="hidden_page_template" style="display:none;">
 <option value="default">Default Template</option>
 <?php page_template_dropdown( $template, 'page' ); ?>
</select>
<?php endif; ?>

<div class="tcd_custom_field_wrap">

  <?php // 基本設定 --------------------------------------------------- ?>
  <div class="theme_option_field cf theme_option_field_ac" id="basic_page_setting">
   <h3 class="theme_option_headline"><?php _e( 'Display setting', 'tcd-serum' ); ?></h3>
   <div class="theme_option_field_ac_content">

    <div class="cb_image">
     <img src="<?php bloginfo('template_url'); ?>/admin/img/lp_display.jpg" width="" height="" />
    </div>

    <div class="theme_option_message2">
     <p><?php _e('Please set header message from <a href="./wp-admin/admin.php?page=theme_options">theme option page</a>.', 'tcd-serum'); ?></p>
    </div>

    <ul class="option_list">
     <li class="cf">
      <span class="label"><span class="num">1</span><?php _e('Header message', 'tcd-serum');  ?></span>
      <div class="standard_radio_button">
       <input type="radio" id="hide_header_message_no" name="hide_header_message" value="no"<?php checked( $hide_header_message, 'no' ); ?>>
       <label for="hide_header_message_no"><?php _e('Display', 'tcd-serum');  ?></label>
       <input type="radio" id="hide_header_message_yes" name="hide_header_message" value="yes"<?php checked( $hide_header_message, 'yes' ); ?>>
       <label for="hide_header_message_yes"><?php _e('Hide', 'tcd-serum');  ?></label>
      </div>
     </li>
     <li class="cf">
      <span class="label"><?php _e('Header', 'tcd-serum');  ?></span>
      <div class="standard_radio_button">
       <input type="radio" id="hide_page_header_no" name="hide_page_header" value="no"<?php checked( $hide_page_header, 'no' ); ?>>
       <label for="hide_page_header_no"><?php _e('Display', 'tcd-serum');  ?></label>
       <input type="radio" id="hide_page_header_yes" name="hide_page_header" value="yes"<?php checked( $hide_page_header, 'yes' ); ?>>
       <label for="hide_page_header_yes"><?php _e('Hide', 'tcd-serum');  ?></label>
      </div>
     </li>
     <li class="cf page_header_option">
      <span class="label"><span style="padding-left:1em;"><span class="num">2</span><?php _e('Header logo', 'tcd-serum');  ?></span></span>
      <div class="standard_radio_button">
       <input type="radio" id="hide_logo_no" name="hide_logo" value="no"<?php checked( $hide_logo, 'no' ); ?>>
       <label for="hide_logo_no"><?php _e('Display', 'tcd-serum');  ?></label>
       <input type="radio" id="hide_logo_yes" name="hide_logo" value="yes"<?php checked( $hide_logo, 'yes' ); ?>>
       <label for="hide_logo_yes"><?php _e('Hide', 'tcd-serum');  ?></label>
      </div>
     </li>
     <li class="cf page_header_option">
      <span class="label"><span style="padding-left:1em;"><span class="num">3</span><?php _e('Header bar', 'tcd-serum');  ?></span></span>
      <div class="standard_radio_button">
       <input type="radio" id="hide_page_header_bar_no" name="hide_page_header_bar" value="no"<?php checked( $hide_page_header_bar, 'no' ); ?>>
       <label for="hide_page_header_bar_no"><?php _e('Display', 'tcd-serum');  ?></label>
       <input type="radio" id="hide_page_header_bar_yes" name="hide_page_header_bar" value="yes"<?php checked( $hide_page_header_bar, 'yes' ); ?>>
       <label for="hide_page_header_bar_yes"><?php _e('Hide', 'tcd-serum');  ?></label>
      </div>
     </li>
     <li class="cf page_header_option">
      <span class="label"><span style="padding-left:1em;"><span class="num">4</span><?php _e('Sidebar', 'tcd-serum');  ?></span></span>
      <div class="standard_radio_button">
       <input type="radio" id="hide_page_side_bar_no" name="hide_page_side_bar" value="no"<?php checked( $hide_page_side_bar, 'no' ); ?>>
       <label for="hide_page_side_bar_no"><?php _e('Display', 'tcd-serum');  ?></label>
       <input type="radio" id="hide_page_side_bar_yes" name="hide_page_side_bar" value="yes"<?php checked( $hide_page_side_bar, 'yes' ); ?>>
       <label for="hide_page_side_bar_yes"><?php _e('Hide', 'tcd-serum');  ?></label>
      </div>
     </li>
     <li class="cf page_header_option">
      <span class="label"><span style="padding-left:1em;"><span class="num">5</span><?php _e('Bread crumb navigation', 'tcd-serum');  ?></span></span>
      <div class="standard_radio_button">
       <input type="radio" id="hide_bread_no" name="page_hide_bread" value="no"<?php checked( $page_hide_bread, 'no' ); ?>>
       <label for="hide_bread_no"><?php _e('Display', 'tcd-serum');  ?></label>
       <input type="radio" id="hide_bread_yes" name="page_hide_bread" value="yes"<?php checked( $page_hide_bread, 'yes' ); ?>>
       <label for="hide_bread_yes"><?php _e('Hide', 'tcd-serum');  ?></label>
      </div>
     </li>
     <li class="cf">
      <span class="label"><span class="num">6</span><?php _e('Footer', 'tcd-serum');  ?></span>
      <div class="standard_radio_button">
       <input type="radio" id="page_hide_footer_no" name="page_hide_footer" value="no"<?php checked( $page_hide_footer, 'no' ); ?>>
       <label for="page_hide_footer_no"><?php _e('Display', 'tcd-serum');  ?></label>
       <input type="radio" id="page_hide_footer_yes" name="page_hide_footer" value="yes"<?php checked( $page_hide_footer, 'yes' ); ?>>
       <label for="page_hide_footer_yes"><?php _e('Hide', 'tcd-serum');  ?></label>
      </div>
     </li>
     <li class="cf">
      <span class="label"><span class="num">7</span><?php _e('Content width', 'tcd-serum');  ?></span>
      <div class="standard_radio_button">
       <input type="radio" id="page_width_small" name="page_width" value="small"<?php checked( $page_width, 'small' ); ?>>
       <label for="page_width_small"><?php _e('Narrow width', 'tcd-serum');  ?></label>
       <input type="radio" id="page_width_normal" name="page_width" value="normal"<?php checked( $page_width, 'normal' ); ?>>
       <label for="page_width_normal"><?php _e('Normal width', 'tcd-serum');  ?></label>
       <input type="radio" id="page_width_large" name="page_width" value="large"<?php checked( $page_width, 'large' ); ?>>
       <label for="page_width_large"><?php _e('Wide width', 'tcd-serum');  ?></label>
      </div>
     </li>
    </ul>

    <ul class="button_list cf">
     <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
    </ul>
   </div><!-- END .theme_option_field_ac_content -->
  </div><!-- END .theme_option_field -->


  <?php // ページヘッダーの設定 --------------------------------------------------- ?>
  <div class="theme_option_field cf theme_option_field_ac" id="page_header_setting_area">
   <h3 class="theme_option_headline"><?php _e( 'Header', 'tcd-serum' ); ?></h3>
   <div class="theme_option_field_ac_content">

    <div class="cb_image header_type1_option">
     <img src="<?php bloginfo('template_url'); ?>/admin/img/page_header_image.jpg" width="" height="" />
    </div>

    <div class="cb_image header_type2_option">
     <img src="<?php bloginfo('template_url'); ?>/admin/img/lp_header_image.jpg" width="" height="" />
    </div>

    <div class="theme_option_message2">
     <p><?php _e('When you set the "Square Logo" in the basic settings of <a href="./admin.php?page=theme_options" target="_blank">theme options</a>, we recommend to set the header image in page.', 'tcd-serum'); ?></p>
    </div>

    <ul class="option_list">
     <li class="cf" id="page_catch_font_size_option" style="border-bottom:1px dotted #ccc; padding-bottom:10px; margin-bottom:-5px;">
      <span class="label"><span class="num">1</span><?php _e('Font size of page title', 'tcd-serum'); ?></span>
      <div class="font_size_option">
       <label class="font_size_label number_option">
        <input class="hankaku input_font_size" type="number" name="page_catch_font_size" min="9" max="100" value="<?php esc_attr_e( $page_catch_font_size ); ?>"><span class="icon icon_pc"></span>
       </label>
       <label class="font_size_label number_option">
        <input class="hankaku input_font_size_sp" type="number" name="page_catch_font_size_sp" min="9" max="100" value="<?php esc_attr_e( $page_catch_font_size_sp ); ?>"><span class="icon icon_sp"></span>
       </label>
      </div>
     </li>
     <li class="cf" style="border-top:none;">
      <span class="label">
       <span class="num">1</span>
       <?php _e('Page title direction', 'tcd-serum'); ?>
       <span class="recommend_desc"><?php _e('If background image is not set, the page title will be display horizontally.', 'tcd-serum'); ?></span>
      </span>
      <div class="standard_radio_button">
       <input type="radio" id="page_catch_direction_type1" name="page_catch_direction" value="type1"<?php checked( $page_catch_direction, 'type1' ); ?>>
       <label for="page_catch_direction_type1"><?php _e('Horizontal', 'tcd-serum');  ?></label>
       <input type="radio" id="page_catch_direction_type2" name="page_catch_direction" value="type2"<?php checked( $page_catch_direction, 'type2' ); ?>>
       <label for="page_catch_direction_type2"><?php _e('Vertical', 'tcd-serum');  ?></label>
      </div>
     </li>
     <li class="cf" id="page_header_bar_display_option">
      <span class="label"><span class="num">2</span><?php _e('Header height', 'tcd-serum');  ?></span>
      <div class="standard_radio_button">
       <input type="radio" id="header_type1" name="header_type" value="type1"<?php checked( $header_type, 'type1' ); ?>>
       <label for="header_type1"><?php _e('Normal size', 'tcd-serum');  ?></label>
       <input type="radio" id="header_type2" name="header_type" value="type2"<?php checked( $header_type, 'type2' ); ?>>
       <label for="header_type2"><?php _e('LP size', 'tcd-serum');  ?></label>
      </div>
     </li>
     <li class="cf">
      <span class="label">
       <span class="num header_type1_option">2</span>
       <span class="num header_type2_option">3</span>
       <?php _e('Background image', 'tcd-serum'); ?>
       <span class="header_type1_option recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '1450', '450'); ?></span>
       <span class="header_type2_option recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '1450', '730'); ?></span>
      </span>
      <?php mlcf_media_form('bg_image', __('Background image', 'tcd-serum')); ?>
     </li>
     <li class="cf"><span class="label"><span class="num header_type1_option">2</span><span class="num header_type2_option">3</span><?php echo tcd_admin_label('overlay_color'); ?></span><input type="text" name="overlay_color" value="<?php echo esc_attr( $overlay_color ); ?>" data-default-color="#000000" class="c-color-picker"></li>
     <li class="cf">
      <span class="label"><span class="num header_type1_option">2</span><span class="num header_type2_option">3</span><?php _e('Transparency of overlay', 'tcd-serum'); ?></span><input class="hankaku" style="width:70px;" type="number" max="1" min="0" step="0.1" name="overlay_color_opacity" value="<?php echo esc_attr( $overlay_color_opacity ); ?>" />
      <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
       <p><?php _e('Please specify the number of 0 from 0.9. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?><br>
       <?php _e('Please enter 0 if you don\'t want to use overlay.', 'tcd-serum');  ?></p>
      </div>
     </li>
    </ul>

    <ul class="button_list cf">
     <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
    </ul>
   </div><!-- END .theme_option_field_ac_content -->
  </div><!-- END .theme_option_field -->


  <?php // FAQの設定 --------------------------------------------------- ?>
  <div id="page_faq_option" class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e( 'FAQ', 'tcd-serum' ); ?></h3>
    <div class="theme_option_field_ac_content">

      <div class="cb_image">
       <img src="<?php bloginfo('template_url'); ?>/admin/img/sc_faq_image.jpg" width="" height="" />
      </div>

      <div class="theme_option_message2">
        <p><?php _e('Please copy and paste the short code below where you want to display FAQ list.', 'tcd-serum'); ?></p>
      </div>

      <h3 class="theme_option_headline2"><?php _e('Short code', 'tcd-serum'); ?></h3>
      <input class="fullwidth" type="text" value="[sc_faq]" readonly>

      <?php // リスト ------------------------------------------------------------------------- ?>
      <h4 class="theme_option_headline2"><?php _e( 'FAQ list', 'tcd-serum' ); ?></h4>
      <?php //繰り返しフィールド ----- ?>
      <div class="repeater-wrapper">
        <div class="repeater sortable" data-delete-confirm="<?php echo tcd_admin_label('delete'); ?>">
          <?php
              if ( $faq_list ) :
                foreach ( $faq_list as $key => $value ) :
          ?>
          <div class="sub_box repeater-item repeater-item-<?php echo $key; ?>">
            <h4 class="theme_option_subbox_headline"><?php echo esc_html( ! empty( $faq_list[$key]['question'] ) ? $faq_list[$key]['question'] : tcd_admin_label('new_item') ); ?></h4>
            <div class="sub_box_content">
              <h4 class="theme_option_headline2"><?php _e( 'Question', 'tcd-serum' ); ?></h4>
              <p><input class="repeater-label full_width" type="text" name="faq_list[<?php echo esc_attr( $key ); ?>][question]" value="<?php echo esc_attr( isset( $faq_list[$key]['question'] ) ? $faq_list[$key]['question'] : '' ); ?>" /></p>
              <h4 class="theme_option_headline2"><?php _e( 'Answer', 'tcd-serum' ); ?></h4>
              <textarea class="full_width" cols="50" rows="5" name="faq_list[<?php echo esc_attr( $key ); ?>][answer]"><?php echo esc_attr( isset( $faq_list[$key]['answer'] ) ? $faq_list[$key]['answer'] : '' ); ?></textarea>
              <p class="delete-row right-align"><a href="#" class="button button-secondary button-delete-row"><?php echo tcd_admin_label('delete_item'); ?></a></p>
            </div><!-- END .sub_box_content -->
          </div><!-- END .sub_box -->
          <?php
                endforeach;
              endif;
              $key = 'addindex';
              ob_start();
          ?>
          <div class="sub_box repeater-item repeater-item-<?php echo $key; ?>">
            <h4 class="theme_option_subbox_headline"><?php echo esc_html( ! empty( $faq_list[$key]['question'] ) ? $faq_list[$key]['question'] : tcd_admin_label('new_item') ); ?></h4>
            <div class="sub_box_content">
              <h4 class="theme_option_headline2"><?php _e( 'Question', 'tcd-serum' ); ?></h4>
              <p><input class="repeater-label full_width" type="text" name="faq_list[<?php echo esc_attr( $key ); ?>][question]" value="<?php echo esc_attr( isset( $faq_list[$key]['question'] ) ? $faq_list[$key]['question'] : '' ); ?>" /></p>
              <h4 class="theme_option_headline2"><?php _e( 'Answer', 'tcd-serum' ); ?></h4>
              <textarea class="full_width" cols="50" rows="5" name="faq_list[<?php echo esc_attr( $key ); ?>][answer]"><?php echo esc_attr( isset( $faq_list[$key]['answer'] ) ? $faq_list[$key]['answer'] : '' ); ?></textarea>
              <p class="delete-row right-align"><a href="#" class="button button-secondary button-delete-row"><?php echo tcd_admin_label('delete_item'); ?></a></p>
            </div><!-- END .sub_box_content -->
          </div><!-- END .sub_box -->
          <?php
              $clone = ob_get_clean();
          ?>
          </div><!-- END .repeater -->
        <a href="#" class="button button-secondary button-add-row" data-clone="<?php echo esc_attr( $clone ); ?>"><?php echo tcd_admin_label('add_item'); ?></a>
      </div><!-- END .repeater-wrapper -->
      <?php //繰り返しフィールドここまで ----- ?>

      <ul class="button_list cf">
        <li><a class="close_ac_content button-ml" href="#"><?php echo tcd_admin_label('close'); ?></a></li>
      </ul>
    </div><!-- END .theme_option_field_ac_content -->
  </div><!-- END .theme_option_field -->


  <?php // 代表者の情報の設定 --------------------------------------------------- ?>
  <div class="staff_page_option theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e( 'Representative information', 'tcd-serum' ); ?></h3>
    <div class="theme_option_field_ac_content">

     <div class="cb_image">
      <img src="<?php bloginfo('template_url'); ?>/admin/img/sc_representative_image.jpg" width="" height="" />
     </div>

     <?php
          $users = get_users(array(
            'fields' => array('ID'),
            'role__not_in' => array('subscriber','contributor'),
            'meta_key' => 'staff_page_displayment',
            'meta_value' => 'type1',
            'orderby' => 'ID',
            'order' => 'ASC'
          ));
          if(!empty($users)){
     ?>

     <div class="theme_option_message2">
      <p><?php _e('Please copy and paste the short code below where you want to display representative information.<br>You can display users who have selected "Display in representative\'s information" on the <a href="./users.php" target="_blank">profile edit screen</a>.', 'tcd-serum');  ?></p>
     </div>

     <h3 class="theme_option_headline2"><?php _e('Short code', 'tcd-serum'); ?></h3>
     <input class="fullwidth" type="text" value="[sc_representative_info]" readonly>

     <?php
          $user_count = count($users);
          if($user_count > 1){
     ?>
     <h3 class="theme_option_headline2"><?php _e('Representative information', 'tcd-serum'); ?></h3>
     <ul class="option_list">
      <li class="cf">
       <span class="label"><?php _e('User', 'tcd-serum'); ?></span>
       <select name="representative_user">
        <option value="hide" <?php selected( $representative_user, 'hide' ); ?>><?php _e('Please select user', 'tcd-serum'); ?></option>
        <?php
             foreach ($users as $user):
               $user_id = $user->ID;
               $user_data = get_userdata($user_id);
               $user_name = $user_data->display_name;
        ?>
        <option value="<?php echo esc_attr($user_id); ?>" <?php selected( $representative_user, $user_id ); ?>><?php echo esc_attr($user_name); ?></option>
        <?php endforeach; ?>
       </select>
      </li>
     </ul>
     <?php }; ?>

     <?php } else { ?>

     <div class="theme_option_message2">
      <p><?php _e('Plese set user to display in representative information from <a href="./users.php" target="_blank">profile edit screen</a>.', 'tcd-serum');  ?></p>
     </div>

     <?php }; ?>

      <ul class="button_list cf">
        <li><a class="close_ac_content button-ml" href="#"><?php echo tcd_admin_label('close'); ?></a></li>
      </ul>
    </div><!-- END .theme_option_field_ac_content -->
  </div><!-- END .theme_option_field -->


  <?php // スタッフ一覧の設定 --------------------------------------------------- ?>
  <div class="staff_page_option theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e( 'Staff list', 'tcd-serum' ); ?></h3>
    <div class="theme_option_field_ac_content">

     <div class="cb_image">
      <img src="<?php bloginfo('template_url'); ?>/admin/img/sc_staff_lists_image.jpg" width="" height="" />
     </div>

     <div class="theme_option_message2">
      <p><?php _e('Plese set user to display in staff list from <a href="./users.php" target="_blank">profile edit screen</a>.<br>You can change staff order by dragging each headline of option field.<br>Please copy and paste the short code below where you want to display staff list.', 'tcd-serum');  ?></p>
     </div>

     <h3 class="theme_option_headline2"><?php _e('Short code', 'tcd-serum'); ?></h3>
     <input class="fullwidth" type="text" value="[sc_staff_list]" readonly>

     <?php if(!empty($staff_list_order)){ ?>

     <?php // 投稿者一覧の並び替え ----- ?>
     <h3 class="theme_option_headline2"><?php _e('Staff list', 'tcd-serum'); ?></h3>
     <div id="staff_list_order">
      <?php
           foreach((array) $staff_list_order as $author_id) :
             $user_data = get_userdata($author_id);
             $user_name = $user_data->display_name;
      ?>
      <div class="item user<?php echo esc_attr($user_data->ID); ?>">
       <h3 class="name"><?php echo esc_html($user_name); ?></h3>
       <input type="hidden" name="staff_list_order[]" value="<?php echo esc_attr($author_id); ?>" />
      </div>
      <?php endforeach; ?>
     </div><!-- END #staff_list_order -->

     <?php }; ?>

      <ul class="button_list cf">
        <li><a class="close_ac_content button-ml" href="#"><?php echo tcd_admin_label('close'); ?></a></li>
      </ul>
    </div><!-- END .theme_option_field_ac_content -->
  </div><!-- END .theme_option_field -->


</div><!-- END .tcd_custom_field_wrap -->

<?php
}

function save_page_header_meta_box( $post_id ) {

  // verify nonce
  if (!isset($_POST['page_header_custom_fields_meta_box_nonce']) || !wp_verify_nonce($_POST['page_header_custom_fields_meta_box_nonce'], basename(__FILE__))) {
    return $post_id;
  }

  // check autosave
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $post_id;
  }

  // check permissions
  if ('page' == $_POST['post_type']) {
    if (!current_user_can('edit_page', $post_id)) {
      return $post_id;
    }
  } elseif (!current_user_can('edit_post', $post_id)) {
      return $post_id;
  }

  // save or delete
  $cf_keys = array(
    'page_catch_font_size','page_catch_font_size_sp','page_catch_direction','header_type','bg_image','overlay_color','overlay_color_opacity',
    'hide_page_header','hide_page_header_bar','hide_page_side_bar','page_hide_bread','page_hide_footer','hide_header_message','hide_logo','page_width','representative_user'
  );
  foreach ($cf_keys as $cf_key) {

    $old = get_post_meta($post_id, $cf_key, true);

    if (isset($_POST[$cf_key])) {
      $new = $_POST[$cf_key];
    } else {
      $new = '';
    }

    if($cf_key == 'overlay_color_opacity'){
      if ( $new == '0' ) {
        $new = 'zero';
      }
    }

    if ($new && $new != $old) {
      update_post_meta($post_id, $cf_key, $new);
    } elseif ('' == $new && $old) {
      delete_post_meta($post_id, $cf_key, $old);
    }

  }

  // repeater save or delete
  $cf_keys = array('faq_list','staff_list_order');
  foreach ( $cf_keys as $cf_key ) {
    $old = get_post_meta( $post_id, $cf_key, true );

    if ( isset( $_POST[$cf_key] ) && is_array( $_POST[$cf_key] ) ) {
      $new = array_values( $_POST[$cf_key] );
    } else {
      $new = false;
    }

    if ( $new && $new != $old ) {
      update_post_meta( $post_id, $cf_key, $new );
    } elseif ( ! $new && $old ) {
      delete_post_meta( $post_id, $cf_key, $old );
    }
  }

}
add_action('save_post', 'save_page_header_meta_box');



?>