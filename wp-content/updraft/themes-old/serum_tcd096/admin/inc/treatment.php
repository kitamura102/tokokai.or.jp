<?php
/*
 * 治療の設定
 */


// Add default values
add_filter( 'before_getting_design_plus_option', 'add_treatment_dp_default_options' );


// Add label of logo tab
add_action( 'tcd_tab_labels', 'add_treatment_tab_label' );


// Add HTML of logo tab
add_action( 'tcd_tab_panel', 'add_treatment_tab_panel' );


// Register sanitize function
add_filter( 'theme_options_validate', 'add_treatment_theme_options_validate' );


// タブの名前
function add_treatment_tab_label( $tab_labels ) {
  $options = get_design_plus_option();
  if($options['use_treatment']){
    $tab_label = $options['treatment_label'] ? esc_html( $options['treatment_label'] ) : __( 'Treatment', 'tcd-serum' );
  } else {
    $title = $options['treatment_label'] ? esc_html( $options['treatment_label'] ) : __( 'Treatment', 'tcd-serum' );
    $tab_label = __('(N/A) ', 'tcd-serum') . $title;
  }
  $tab_labels['treatment'] = $tab_label;
  return $tab_labels;
}


// 初期値
function add_treatment_dp_default_options( $dp_default_options ) {

	// 基本設定
	$dp_default_options['use_treatment'] = '1';
	$dp_default_options['treatment_label'] = __( 'Treatment', 'tcd-serum' );
	$dp_default_options['treatment_slug'] = 'treatment';

	// アーカイブページ
	$dp_default_options['archive_treatment_catch'] = __( 'Treatment', 'tcd-serum' );
	$dp_default_options['archive_treatment_catch_direction'] = 'type2';
	$dp_default_options['archive_treatment_header_bg_image'] = false;
	$dp_default_options['archive_treatment_header_overlay_color'] = '#000000';
	$dp_default_options['archive_treatment_header_overlay_opacity'] = '0.3';

	// CTA
	$dp_default_options['show_treatment_cta'] = '';
	$dp_default_options['treatment_cta_type'] = 'type1';

	$dp_default_options['treatment_cta_type1_image'] = '';
	$dp_default_options['treatment_cta_type1_url'] = '';
	$dp_default_options['treatment_cta_type1_target'] = '';
	$dp_default_options['treatment_cta_type1_catch'] = '';
	$dp_default_options['treatment_cta_type1_overlay_color'] = '#000000';
	$dp_default_options['treatment_cta_type1_overlay_opacity'] = '0.3';

  for ( $i = 1; $i <= 2; $i++ ) :

	$dp_default_options['treatment_cta_type2_image'.$i] = '';
	$dp_default_options['treatment_cta_type2_url'.$i] = '';
	$dp_default_options['treatment_cta_type2_target'.$i] = '';
	$dp_default_options['treatment_cta_type2_catch'.$i] = '';
	$dp_default_options['treatment_cta_type2_desc'.$i] = '';
	$dp_default_options['treatment_cta_type2_overlay_color'.$i] = '#000000';

  endfor;

	return $dp_default_options;

}


// 入力欄の出力　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_treatment_tab_panel( $options ) {

  global $dp_default_options, $font_type_options, $basic_display_options, $font_direction_options, $cta_options;
  $treatment_label = $options['treatment_label'] ? esc_html( $options['treatment_label'] ) : __( 'Treatment', 'tcd-serum' );

?>

<div id="tab-content-treatment" class="tab-content">


   <?php // 有効化 -------------------------------------------------------------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac active open custon_post_usage_option">
    <h3 class="theme_option_headline"><?php _e('Validation', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <div class="theme_option_message2 custon_post_usage_option_message" style="<?php if($options['use_treatment']){ echo 'display:none;'; } else { echo 'display:block;'; }; ?>">
      <p><?php printf(__('Currently, all function related to custom post "%s" have been disabled.<br>All areas that have already been set up will be hidden from the site.<br>Please use this option only if you don\'t want to use the custom post "%s" at all. (No archive page will be generated either).', 'tcd-serum'), $treatment_label, $treatment_label); ?></p>
     </div>
     <div class="theme_option_message2" style="<?php if($options['use_treatment']){ echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
      <p><?php printf(__('Please check off the checkbox if you don\'t want to use custom post "%s".', 'tcd-serum'), $treatment_label); ?></p>
     </div>
     <p><label><input class="custon_post_usage_option_checkbox" name="dp_options[use_treatment]" type="checkbox" value="1" <?php checked( 1, $options['use_treatment'] ); ?>><?php printf(__('Use custom post "%s"', 'tcd-serum'), $treatment_label); ?></label></p>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // 基本設定 -------------------------------------------------------------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Common setting', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <div class="front_page_image">
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/treatment_name_image.jpg?2.0" alt="" title="" />
     </div>

     <h4 class="theme_option_headline_number"><span class="num">1</span><?php _e('Name of content', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2">
      <p><?php _e('This name will also be used in breadcrumb link.', 'tcd-serum'); ?></p>
     </div>
     <input type="text" name="dp_options[treatment_label]" value="<?php echo esc_attr( $options['treatment_label'] ); ?>" />

     <h4 class="theme_option_headline_number"><span class="num">2</span><?php _e('Slug', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2">
      <p><?php _e('Please enter word by alphabet only.<br />After changing slug, please update permalink setting form <a href="./options-permalink.php"><strong>permalink option page</strong></a>.', 'tcd-serum'); ?></p>
     </div>
     <p><input class="hankaku" type="text" name="dp_options[treatment_slug]" value="<?php echo sanitize_title( $options['treatment_slug'] ); ?>" /></p>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // アーカイブページ ----------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Archive page', 'tcd-serum'); ?></h3>
    <div class="theme_option_field_ac_content">

     <div class="front_page_image">
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/treatment_archives_image.jpg?2.0" alt="" title="" />
     </div>

     <h4 class="theme_option_headline2"><?php _e('Header', 'tcd-serum'); ?></h4>
     <ul class="option_list">
      <li class="cf"><span class="label"><span class="num">1</span><?php _e('Catchphrase', 'tcd-serum'); ?></span><textarea class="full_width" cols="50" rows="2" name="dp_options[archive_treatment_catch]"><?php echo esc_textarea(  $options['archive_treatment_catch'] ); ?></textarea></li>
      <li class="cf"><span class="label"><span class="num">1</span><?php _e('Font direction', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'archive_treatment_catch_direction', $font_direction_options); ?></li>
      <li class="cf">
       <span class="label">
        <span class="num">2</span>
        <?php _e('Background image', 'tcd-serum'); ?>
        <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '1450', '450'); ?></span>
       </span>
       <div class="image_box cf">
        <div class="cf cf_media_field hide-if-no-js archive_treatment_header_bg_image">
         <input type="hidden" value="<?php echo esc_attr( $options['archive_treatment_header_bg_image'] ); ?>" id="archive_treatment_header_bg_image" name="dp_options[archive_treatment_header_bg_image]" class="cf_media_id">
         <div class="preview_field"><?php if($options['archive_treatment_header_bg_image']){ echo wp_get_attachment_image($options['archive_treatment_header_bg_image'], 'medium'); }; ?></div>
         <div class="buttton_area">
          <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
          <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['archive_treatment_header_bg_image']){ echo 'hidden'; }; ?>">
         </div>
        </div>
       </div>
      </li>
      <li class="cf"><span class="label"><span class="num">2</span><?php _e('Color of overlay', 'tcd-serum'); ?></span><input type="text" name="dp_options[archive_treatment_header_overlay_color]" value="<?php echo esc_attr( $options['archive_treatment_header_overlay_color'] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
      <li class="cf">
       <span class="label"><span class="num">2</span><?php _e('Transparency of overlay', 'tcd-serum'); ?></span><input class="hankaku" style="width:70px;" type="number" max="1" min="0" step="0.1" name="dp_options[archive_treatment_header_overlay_opacity]" value="<?php echo esc_attr( $options['archive_treatment_header_overlay_opacity'] ); ?>" />
       <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
        <p><?php _e('Please specify the number of 0 from 0.9. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?>
        <?php _e('Please enter 0 if you don\'t want to use overlay.', 'tcd-serum');  ?></p>
       </div>
      </li>
     </ul>

     <h4 class="theme_option_headline2"><?php _e('Main content', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2">
      <p><?php printf(__('In this page main content, the contents set in the <a target="_blank" href="./edit-tags.php?taxonomy=treatment_category&post_type=treatment">%s category</a> page will be displayed by list.', 'tcd-serum'), $treatment_label, $treatment_label); ?></p>
     </div>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // CTAの設定 ----------------------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('CTA', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <p class="displayment_checkbox"><label><input name="dp_options[show_treatment_cta]" type="checkbox" value="1" <?php checked( $options['show_treatment_cta'], 1 ); ?>><?php printf(__( 'Display CTA at %s single page', 'tcd-serum' ), $treatment_label); ?></label></p>
     <div style="<?php if($options['show_treatment_cta'] == 1) { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">

     <h4 class="theme_option_headline2"><?php _e('Type of cta', 'tcd-serum');  ?></h4>
     <?php echo tcd_admin_image_radio_button($options, 'treatment_cta_type', $cta_options) ?>

     <div id="treatment_cta_type1_area">
      <h4 class="theme_option_headline2"><?php _e('Large banner', 'tcd-serum');  ?></h4>
      <ul class="option_list">
       <li class="cf"><span class="label"><?php _e('Catchphrase', 'tcd-serum'); ?></span><textarea class="full_width" cols="50" rows="2" name="dp_options[treatment_cta_type1_catch]"><?php echo esc_textarea(  $options['treatment_cta_type1_catch'] ); ?></textarea></li>
       <li class="cf">
        <span class="label"><?php _e('URL', 'tcd-serum'); ?></span>
        <div class="admin_link_option">
         <input type="text" name="dp_options[treatment_cta_type1_url]" placeholder="https://example.com/" value="<?php esc_attr_e( $options['treatment_cta_type1_url'] ); ?>">
         <input id="treatment_cta_type1_target" class="admin_link_option_target" name="dp_options[treatment_cta_type1_target]" type="checkbox" value="1" <?php checked( $options['treatment_cta_type1_target'], 1 ); ?>>
         <label for="treatment_cta_type1_target">&#xe92a;</label>
        </div>
       </li>
       <li class="cf">
        <span class="label">
         <?php _e('Background image', 'tcd-serum'); ?>
         <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '880', '320'); ?></span>
        </span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js treatment_cta_type1_image">
          <input type="hidden" value="<?php echo esc_attr( $options['treatment_cta_type1_image'] ); ?>" id="treatment_cta_type1_image" name="dp_options[treatment_cta_type1_image]" class="cf_media_id">
          <div class="preview_field"><?php if($options['treatment_cta_type1_image']){ echo wp_get_attachment_image($options['treatment_cta_type1_image'], 'medium'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['treatment_cta_type1_image']){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
       <li class="cf"><span class="label"><?php _e('Color of overlay', 'tcd-serum'); ?></span><input type="text" name="dp_options[treatment_cta_type1_overlay_color]" value="<?php echo esc_attr( $options['treatment_cta_type1_overlay_color'] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
       <li class="cf">
        <span class="label"><?php _e('Transparency of overlay', 'tcd-serum'); ?></span><input class="hankaku" style="width:70px;" type="number" max="1" min="0" step="0.1" name="dp_options[treatment_cta_type1_overlay_opacity]" value="<?php echo esc_attr( $options['treatment_cta_type1_overlay_opacity'] ); ?>" />
        <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
         <p><?php _e('Please specify the number of 0 from 0.9. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?>
         <?php _e('Please enter 0 if you don\'t want to use overlay.', 'tcd-serum');  ?></p>
        </div>
       </li>
      </ul>
     </div>

     <div id="treatment_cta_type2_area" class="tab_parent">
      <h4 class="theme_option_headline2"><?php _e('Small banner', 'tcd-serum');  ?></h4>
      <div class="sub_box_tab">
       <div class="tab active" data-tab="tab1"><?php _e('Banner', 'tcd-serum'); ?>1</div>
       <div class="tab" data-tab="tab2"><?php _e('Banner', 'tcd-serum'); ?>2</div>
      </div>

      <?php for ( $i = 1; $i <= 2; $i++ ) : ?>
      <div class="sub_box_tab_content<?php if($i == 1){ echo ' active'; }; ?>" data-tab-content="tab<?php echo $i; ?>">
      <ul class="option_list">
       <li class="cf"><span class="label"><?php _e('Catchphrase', 'tcd-serum'); ?></span><textarea class="full_width" cols="50" rows="2" name="dp_options[treatment_cta_type2_catch<?php echo $i; ?>]"><?php echo esc_textarea(  $options['treatment_cta_type2_catch'.$i] ); ?></textarea></li>
       <li class="cf"><span class="label"><?php _e('Description', 'tcd-serum'); ?></span><textarea class="full_width" cols="50" rows="2" name="dp_options[treatment_cta_type2_desc<?php echo $i; ?>]"><?php echo esc_textarea(  $options['treatment_cta_type2_desc'.$i] ); ?></textarea></li>
       <li class="cf">
        <span class="label"><?php _e('URL', 'tcd-serum'); ?></span>
        <div class="admin_link_option">
         <input type="text" name="dp_options[treatment_cta_type2_url<?php echo $i; ?>]" placeholder="https://example.com/" value="<?php esc_attr_e( $options['treatment_cta_type2_url'.$i] ); ?>">
         <input id="treatment_cta_type2_target<?php echo $i; ?>" class="admin_link_option_target" name="dp_options[treatment_cta_type2_target<?php echo $i; ?>]" type="checkbox" value="1" <?php checked( $options['treatment_cta_type2_target'.$i], 1 ); ?>>
         <label for="treatment_cta_type2_target<?php echo $i; ?>">&#xe92a;</label>
        </div>
       </li>
       <li class="cf">
        <span class="label">
         <?php _e('Background image', 'tcd-serum'); ?>
         <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '930', '320'); ?></span>
        </span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js treatment_cta_type2_image<?php echo $i; ?>">
          <input type="hidden" value="<?php echo esc_attr( $options['treatment_cta_type2_image'.$i] ); ?>" id="treatment_cta_type2_image<?php echo $i; ?>" name="dp_options[treatment_cta_type2_image<?php echo $i; ?>]" class="cf_media_id">
          <div class="preview_field"><?php if($options['treatment_cta_type2_image'.$i]){ echo wp_get_attachment_image($options['treatment_cta_type2_image'.$i], 'medium'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['treatment_cta_type2_image'.$i]){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
       <li class="cf color_picker_bottom"><span class="label"><?php _e('Color of overlay', 'tcd-serum'); ?></span><input type="text" name="dp_options[treatment_cta_type2_overlay_color<?php echo $i; ?>]" value="<?php echo esc_attr( $options['treatment_cta_type2_overlay_color'.$i] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
      </ul>
      </div>
      <?php endfor; ?>
     </div>

     </div>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


</div><!-- END .tab-content -->

<?php
} // END add_treatment_tab_panel()


// バリデーション　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_treatment_theme_options_validate( $input ) {

  global $dp_default_options, $no_image_options, $font_type_options;

  //基本設定
  $input['use_treatment'] = wp_filter_nohtml_kses( $input['use_treatment'] );
  $input['treatment_slug'] = sanitize_title( $input['treatment_slug'] );
  $input['treatment_label'] = wp_filter_nohtml_kses( $input['treatment_label'] );


  // アーカイブ
  $input['archive_treatment_catch'] = wp_filter_nohtml_kses( $input['archive_treatment_catch'] );
  $input['archive_treatment_catch_direction'] = wp_filter_nohtml_kses( $input['archive_treatment_catch_direction'] );
  $input['archive_treatment_header_bg_image'] = wp_filter_nohtml_kses( $input['archive_treatment_header_bg_image'] );
  $input['archive_treatment_header_overlay_color'] = wp_filter_nohtml_kses( $input['archive_treatment_header_overlay_color'] );
  $input['archive_treatment_header_overlay_opacity'] = wp_filter_nohtml_kses( $input['archive_treatment_header_overlay_opacity'] );


  // CTA
  $input['show_treatment_cta'] = ! empty( $input['show_treatment_cta'] ) ? 1 : 0;
  $input['treatment_cta_type'] = wp_filter_nohtml_kses( $input['treatment_cta_type'] );

  $input['treatment_cta_type1_image'] = wp_filter_nohtml_kses( $input['treatment_cta_type1_image'] );
  $input['treatment_cta_type1_url'] = wp_filter_nohtml_kses( $input['treatment_cta_type1_url'] );
  $input['treatment_cta_type1_target'] = ! empty( $input['treatment_cta_type1_target'] ) ? 1 : 0;
  $input['treatment_cta_type1_catch'] = wp_filter_nohtml_kses( $input['treatment_cta_type1_catch'] );
  $input['treatment_cta_type1_overlay_color'] = wp_filter_nohtml_kses( $input['treatment_cta_type1_overlay_color'] );
  $input['treatment_cta_type1_overlay_opacity'] = wp_filter_nohtml_kses( $input['treatment_cta_type1_overlay_opacity'] );

  for ( $i = 1; $i <= 2; $i++ ) :

  $input['treatment_cta_type2_image'.$i] = wp_filter_nohtml_kses( $input['treatment_cta_type2_image'.$i] );
  $input['treatment_cta_type2_url'.$i] = wp_filter_nohtml_kses( $input['treatment_cta_type2_url'.$i] );
  $input['treatment_cta_type2_target'.$i] = ! empty( $input['treatment_cta_type2_target'.$i] ) ? 1 : 0;
  $input['treatment_cta_type2_catch'.$i] = wp_filter_nohtml_kses( $input['treatment_cta_type2_catch'.$i] );
  $input['treatment_cta_type2_desc'.$i] = wp_filter_nohtml_kses( $input['treatment_cta_type2_desc'.$i] );
  $input['treatment_cta_type2_overlay_color'.$i] = wp_filter_nohtml_kses( $input['treatment_cta_type2_overlay_color'.$i] );

  endfor;

	return $input;

};


?>