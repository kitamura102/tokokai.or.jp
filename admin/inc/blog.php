<?php
/*
 * ブログの設定
 */


// Add default values
add_filter( 'before_getting_design_plus_option', 'add_blog_dp_default_options' );


//  Add label of blog tab
add_action( 'tcd_tab_labels', 'add_blog_tab_label' );


// Add HTML of blog tab
add_action( 'tcd_tab_panel', 'add_blog_tab_panel' );


// Register sanitize function
add_filter( 'theme_options_validate', 'add_blog_theme_options_validate' );


// タブの名前
function add_blog_tab_label( $tab_labels ) {
  global $blog_label;
  $options = get_design_plus_option();
  $tab_label = $blog_label;
	$tab_labels['blog'] = $tab_label;
	return $tab_labels;
}


// 初期値
function add_blog_dp_default_options( $dp_default_options ) {

	// 基本設定
	$dp_default_options['blog_show_date'] = 'display';

	// アーカイブページ
	$dp_default_options['archive_blog_catch'] = __( 'Blog', 'tcd-serum' );
	$dp_default_options['archive_blog_catch_direction'] = 'type2';
	$dp_default_options['archive_blog_header_bg_image'] = false;
	$dp_default_options['archive_blog_header_overlay_color'] = '#000000';
	$dp_default_options['archive_blog_header_overlay_opacity'] = '0.3';

	$dp_default_options['archive_blog_desc'] = '';
	$dp_default_options['archive_blog_desc_mobile'] = '';

	// 詳細ページ
	$dp_default_options['single_blog_show_sns_top'] = 'display';
	$dp_default_options['single_blog_show_sns_btm'] = 'display';
	$dp_default_options['single_blog_show_copy_top'] = 'display';
	$dp_default_options['single_blog_show_copy_btm'] = 'hide';
	$dp_default_options['single_blog_show_tag_list'] = 'display';

	// 関連記事
	$dp_default_options['related_post_headline'] = __( 'Related post', 'tcd-serum' );
	$dp_default_options['related_post_num'] = '3';
	$dp_default_options['related_post_num_sp'] = '3';

	// 記事ページのバナー
	$dp_default_options['single_top_ad_code'] = '';
	$dp_default_options['single_bottom_ad_code'] = '';
	$dp_default_options['single_mobile_ad_code'] = '';

	// CTA
	$dp_default_options['show_blog_cta'] = '';
	$dp_default_options['blog_cta_type'] = 'type1';

	$dp_default_options['blog_cta_type1_image'] = '';
	$dp_default_options['blog_cta_type1_url'] = '';
	$dp_default_options['blog_cta_type1_target'] = '';
	$dp_default_options['blog_cta_type1_catch'] = '';
	$dp_default_options['blog_cta_type1_overlay_color'] = '#000000';
	$dp_default_options['blog_cta_type1_overlay_opacity'] = '0.3';

  for ( $i = 1; $i <= 2; $i++ ) :

	$dp_default_options['blog_cta_type2_image'.$i] = '';
	$dp_default_options['blog_cta_type2_url'.$i] = '';
	$dp_default_options['blog_cta_type2_target'.$i] = '';
	$dp_default_options['blog_cta_type2_catch'.$i] = '';
	$dp_default_options['blog_cta_type2_desc'.$i] = '';
	$dp_default_options['blog_cta_type2_overlay_color'.$i] = '#000000';

  endfor;

	return $dp_default_options;

}


// 入力欄の出力　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_blog_tab_panel( $options ) {

  global $blog_label, $dp_default_options, $font_type_options, $post_list_animation_type_options, $basic_display_options, $font_direction_options, $cta_options;

?>

<div id="tab-content-blog" class="tab-content">

   <?php // 基本設定 -------------------------------------------------------------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Common setting', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <?php
          $blog_page_id = get_option( 'page_for_posts' );
          if($blog_page_id) {
     ?>

     <div class="front_page_image">
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/blog_name_image.jpg" alt="" title="" />
     </div>

     <h4 class="theme_option_headline_number"><span class="num">1</span><?php _e('Name of content', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2">
      <p><?php printf(__('Title that are set on the <a href="post.php?post=%s&action=edit" target="_blank">post page</a> will affect to blog content name and breadcrumb link name.', 'tcd-serum'), $blog_page_id); ?></p>
     </div>

     <h4 class="theme_option_headline_number"><span class="num">2</span><?php _e('Slug', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2">
      <p><?php printf(__('Permalinks that are set on the <a href="post.php?post=%s&action=edit" target="_blank">post page</a> will affect to blog page URL.', 'tcd-serum'), $blog_page_id); ?></p>
     </div>

     <?php } else { ?>

     <div class="theme_option_message2">
      <p><?php _e('After creating the blog page by <a href="./edit.php?post_type=page" target="_blank">WP-page</a>, please register the page as a blog from the <a href="./options-reading.php" target="_blank">display settings page</a>.', 'tcd-serum'); ?></p>
     </div>

     <?php }; ?>

     <h4 class="theme_option_headline2"><?php _e('Date', 'tcd-serum');  ?></h4>
     <div class="clearfix"><?php echo tcd_basic_radio_button($options, 'blog_show_date', $basic_display_options); ?></div>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // アーカイブページ ----------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Blog archive page', 'tcd-serum'); ?></h3>
    <div class="theme_option_field_ac_content">

     <div class="front_page_image">
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/blog_archives_image.jpg?2.0" alt="" title="" />
     </div>

     <div class="theme_option_message2" style="margin-top:20px;">
      <p><?php _e('Settings for the post archive page.', 'tcd-serum'); ?></p>
      <?php
           if($blog_page_id) {
             $blog_page_url = get_page_link( $blog_page_id );
             if($blog_page_url){
      ?>
      <p><?php _e('URL of the post archive page:', 'tcd-serum'); ?><a class="e_link" href="<?php echo esc_url($blog_page_url) ?>"><?php echo esc_url($blog_page_url) ?></a></p>
      <?php
             };
           } else {
      ?>
      <p><?php _e('The page for the post archive page is not set.', 'tcd-serum'); ?>
         <?php _e('Please refer to the <a href="https://tcd-theme.com/2022/07/wordpress-homepage.html" target="_blank">manual</a> to create and configure.', 'tcd-serum'); ?></p>
      <?php } ?>
     </div>

     <h4 class="theme_option_headline2"><?php _e('Header', 'tcd-serum'); ?></h4>
     <ul class="option_list">
      <li class="cf"><span class="label"><span class="num">1</span><?php _e('Catchphrase', 'tcd-serum'); ?></span><textarea class="full_width" cols="50" rows="2" name="dp_options[archive_blog_catch]"><?php echo esc_textarea(  $options['archive_blog_catch'] ); ?></textarea></li>
      <li class="cf"><span class="label"><span class="num">1</span><?php _e('Font direction', 'tcd-serum'); ?></span><?php echo tcd_basic_radio_button($options, 'archive_blog_catch_direction', $font_direction_options); ?></li>
      <li class="cf">
       <span class="label">
        <span class="num">2</span>
        <?php _e('Background image', 'tcd-serum'); ?>
        <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '1450', '450'); ?></span>
       </span>
       <div class="image_box cf">
        <div class="cf cf_media_field hide-if-no-js archive_blog_header_bg_image">
         <input type="hidden" value="<?php echo esc_attr( $options['archive_blog_header_bg_image'] ); ?>" id="archive_blog_header_bg_image" name="dp_options[archive_blog_header_bg_image]" class="cf_media_id">
         <div class="preview_field"><?php if($options['archive_blog_header_bg_image']){ echo wp_get_attachment_image($options['archive_blog_header_bg_image'], 'medium'); }; ?></div>
         <div class="buttton_area">
          <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
          <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['archive_blog_header_bg_image']){ echo 'hidden'; }; ?>">
         </div>
        </div>
       </div>
      </li>
      <li class="cf"><span class="label"><span class="num">2</span><?php _e('Color of overlay', 'tcd-serum'); ?></span><input type="text" name="dp_options[archive_blog_header_overlay_color]" value="<?php echo esc_attr( $options['archive_blog_header_overlay_color'] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
      <li class="cf">
       <span class="label"><span class="num">2</span><?php _e('Transparency of overlay', 'tcd-serum'); ?></span><input class="hankaku" style="width:70px;" type="number" max="1" min="0" step="0.1" name="dp_options[archive_blog_header_overlay_opacity]" value="<?php echo esc_attr( $options['archive_blog_header_overlay_opacity'] ); ?>" />
       <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
        <p><?php _e('Please specify the number of 0 from 0.9. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?>
        <?php _e('Please enter 0 if you don\'t want to use overlay.', 'tcd-serum');  ?></p>
       </div>
      </li>
      <li class="cf"><span class="label"><span class="num">3</span><?php _e('Description', 'tcd-serum'); ?></span><textarea class="full_width" cols="50" rows="3" name="dp_options[archive_blog_desc]"><?php echo esc_textarea(  $options['archive_blog_desc'] ); ?></textarea></li>
      <li class="cf"><span class="label"><span class="num">3</span><?php _e('Description (mobile)', 'tcd-serum'); ?></span><textarea placeholder="<?php _e( 'Please indicate if you would like to display a short text for mobile sizes.', 'tcd-serum' ); ?>" class="full_width" cols="50" rows="2" name="dp_options[archive_blog_desc_mobile]"><?php echo esc_textarea(  $options['archive_blog_desc_mobile'] ); ?></textarea></li>
     </ul>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // 詳細ページの設定 -------------------------------------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Blog article', 'tcd-serum'); ?></h3>
    <div class="theme_option_field_ac_content">

     <div class="front_page_image">
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/blog_main_image.jpg?2.0" alt="" title="" />
     </div>

     <h4 class="theme_option_headline2"><?php _e('Display setting', 'tcd-serum');  ?></h4>
     <div class="theme_option_message2">
      <p><?php _e('You can set share button design from basic setting menu in theme option page.', 'tcd-serum');  ?></p>
     </div>
     <ul class="option_list">
      <li class="cf"><span class="label"><span class="num">1</span><?php _e('Share button above post content', 'tcd-serum');  ?></span><?php echo tcd_basic_radio_button($options, 'single_blog_show_sns_top', $basic_display_options); ?></li>
      <li class="cf"><span class="label"><span class="num">2</span><?php _e('Share button under post content', 'tcd-serum');  ?></span><?php echo tcd_basic_radio_button($options, 'single_blog_show_sns_btm', $basic_display_options); ?></li>
      <li class="cf"><span class="label"><span class="num">3</span><?php _e('"COPY Title&amp;URL" button under title', 'tcd-serum');  ?></span><?php echo tcd_basic_radio_button($options, 'single_blog_show_copy_top', $basic_display_options); ?></li>
      <li class="cf"><span class="label"><span class="num">4</span><?php _e('"COPY Title&amp;URL" button under post content', 'tcd-serum');  ?></span><?php echo tcd_basic_radio_button($options, 'single_blog_show_copy_btm', $basic_display_options); ?></li>
      <li class="cf"><span class="label"><span class="num">5</span><?php _e('Tag cloud', 'tcd-serum');  ?></span><?php echo tcd_basic_radio_button($options, 'single_blog_show_tag_list', $basic_display_options); ?></li>
     </ul>

     <?php // 関連記事 ----------------------------- ?>
     <h4 class="theme_option_headline2"><?php _e('Related post', 'tcd-serum');  ?></h4>
     <ul class="option_list">
      <li class="cf"><span class="label"><span class="num">6</span><?php _e('Headline', 'tcd-serum');  ?></span><input type="text" class="full_width" name="dp_options[related_post_headline]" value="<?php echo esc_attr($options['related_post_headline']); ?>"></li>
      <li class="cf"><span class="label"><span class="num">7</span><?php _e('Number of post to display', 'tcd-serum');  ?></span><?php echo tcd_display_post_num_option('related_post_num', array(3,9,3), array(3,6,1)); ?></li>
     </ul>

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

     <p class="displayment_checkbox"><label><input name="dp_options[show_blog_cta]" type="checkbox" value="1" <?php checked( $options['show_blog_cta'], 1 ); ?>><?php printf(__( 'Display CTA at %s single page', 'tcd-serum' ), $blog_label); ?></label></p>
     <div style="<?php if($options['show_blog_cta'] == 1) { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">

     <h4 class="theme_option_headline2"><?php _e('Type of cta', 'tcd-serum');  ?></h4>
     <?php echo tcd_admin_image_radio_button($options, 'blog_cta_type', $cta_options) ?>

     <div id="blog_cta_type1_area">
      <h4 class="theme_option_headline2"><?php _e('Large banner', 'tcd-serum');  ?></h4>
      <ul class="option_list">
       <li class="cf"><span class="label"><?php _e('Catchphrase', 'tcd-serum'); ?></span><textarea class="full_width" cols="50" rows="2" name="dp_options[blog_cta_type1_catch]"><?php echo esc_textarea(  $options['blog_cta_type1_catch'] ); ?></textarea></li>
       <li class="cf">
        <span class="label"><?php _e('URL', 'tcd-serum'); ?></span>
        <div class="admin_link_option">
         <input type="text" name="dp_options[blog_cta_type1_url]" placeholder="https://example.com/" value="<?php esc_attr_e( $options['blog_cta_type1_url'] ); ?>">
         <input id="blog_cta_type1_target" class="admin_link_option_target" name="dp_options[blog_cta_type1_target]" type="checkbox" value="1" <?php checked( $options['blog_cta_type1_target'], 1 ); ?>>
         <label for="blog_cta_type1_target">&#xe92a;</label>
        </div>
       </li>
       <li class="cf">
        <span class="label">
         <?php _e('Background image', 'tcd-serum'); ?>
         <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '880', '320'); ?></span>
        </span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js blog_cta_type1_image">
          <input type="hidden" value="<?php echo esc_attr( $options['blog_cta_type1_image'] ); ?>" id="blog_cta_type1_image" name="dp_options[blog_cta_type1_image]" class="cf_media_id">
          <div class="preview_field"><?php if($options['blog_cta_type1_image']){ echo wp_get_attachment_image($options['blog_cta_type1_image'], 'medium'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['blog_cta_type1_image']){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
       <li class="cf"><span class="label"><?php _e('Color of overlay', 'tcd-serum'); ?></span><input type="text" name="dp_options[blog_cta_type1_overlay_color]" value="<?php echo esc_attr( $options['blog_cta_type1_overlay_color'] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
       <li class="cf">
        <span class="label"><?php _e('Transparency of overlay', 'tcd-serum'); ?></span><input class="hankaku" style="width:70px;" type="number" max="1" min="0" step="0.1" name="dp_options[blog_cta_type1_overlay_opacity]" value="<?php echo esc_attr( $options['blog_cta_type1_overlay_opacity'] ); ?>" />
        <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
         <p><?php _e('Please specify the number of 0 from 0.9. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?>
         <?php _e('Please enter 0 if you don\'t want to use overlay.', 'tcd-serum');  ?></p>
        </div>
       </li>
      </ul>
     </div>

     <div id="blog_cta_type2_area" class="tab_parent">
      <h4 class="theme_option_headline2"><?php _e('Small banner', 'tcd-serum');  ?></h4>
      <div class="sub_box_tab">
       <div class="tab active" data-tab="tab1"><?php _e('Banner', 'tcd-serum'); ?>1</div>
       <div class="tab" data-tab="tab2"><?php _e('Banner', 'tcd-serum'); ?>2</div>
      </div>

      <?php for ( $i = 1; $i <= 2; $i++ ) : ?>
      <div class="sub_box_tab_content<?php if($i == 1){ echo ' active'; }; ?>" data-tab-content="tab<?php echo $i; ?>">
      <ul class="option_list">
       <li class="cf"><span class="label"><?php _e('Catchphrase', 'tcd-serum'); ?></span><textarea class="full_width" cols="50" rows="2" name="dp_options[blog_cta_type2_catch<?php echo $i; ?>]"><?php echo esc_textarea(  $options['blog_cta_type2_catch'.$i] ); ?></textarea></li>
       <li class="cf"><span class="label"><?php _e('Description', 'tcd-serum'); ?></span><textarea class="full_width" cols="50" rows="2" name="dp_options[blog_cta_type2_desc<?php echo $i; ?>]"><?php echo esc_textarea(  $options['blog_cta_type2_desc'.$i] ); ?></textarea></li>
       <li class="cf">
        <span class="label"><?php _e('URL', 'tcd-serum'); ?></span>
        <div class="admin_link_option">
         <input type="text" name="dp_options[blog_cta_type2_url<?php echo $i; ?>]" placeholder="https://example.com/" value="<?php esc_attr_e( $options['blog_cta_type2_url'.$i] ); ?>">
         <input id="blog_cta_type2_target<?php echo $i; ?>" class="admin_link_option_target" name="dp_options[blog_cta_type2_target<?php echo $i; ?>]" type="checkbox" value="1" <?php checked( $options['blog_cta_type2_target'.$i], 1 ); ?>>
         <label for="blog_cta_type2_target<?php echo $i; ?>">&#xe92a;</label>
        </div>
       </li>
       <li class="cf">
        <span class="label">
         <?php _e('Background image', 'tcd-serum'); ?>
         <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '930', '320'); ?></span>
        </span>
        <div class="image_box cf">
         <div class="cf cf_media_field hide-if-no-js blog_cta_type2_image<?php echo $i; ?>">
          <input type="hidden" value="<?php echo esc_attr( $options['blog_cta_type2_image'.$i] ); ?>" id="blog_cta_type2_image<?php echo $i; ?>" name="dp_options[blog_cta_type2_image<?php echo $i; ?>]" class="cf_media_id">
          <div class="preview_field"><?php if($options['blog_cta_type2_image'.$i]){ echo wp_get_attachment_image($options['blog_cta_type2_image'.$i], 'medium'); }; ?></div>
          <div class="buttton_area">
           <input type="button" value="<?php _e('Select Image', 'tcd-serum'); ?>" class="cfmf-select-img button">
           <input type="button" value="<?php _e('Remove Image', 'tcd-serum'); ?>" class="cfmf-delete-img button <?php if(!$options['blog_cta_type2_image'.$i]){ echo 'hidden'; }; ?>">
          </div>
         </div>
        </div>
       </li>
       <li class="cf color_picker_bottom"><span class="label"><?php _e('Color of overlay', 'tcd-serum'); ?></span><input type="text" name="dp_options[blog_cta_type2_overlay_color<?php echo $i; ?>]" value="<?php echo esc_attr( $options['blog_cta_type2_overlay_color'.$i] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
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

   <?php // 広告 -------------------------------------------------------------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Additional content', 'tcd-serum'); ?></h3>
    <div class="theme_option_field_ac_content tab_parent">

     <div class="theme_option_message2">
      <p><?php _e('Additional content can be placed above and below all articles. HTML can also be used, so please use it for affiliates as well.', 'tcd-serum');  ?></p>
     </div>

     <div class="sub_box_tab">
      <div class="tab active" data-tab="tab1"><?php _e('Above main content', 'tcd-serum'); ?></div>
      <div class="tab" data-tab="tab2"><?php _e('Below main content', 'tcd-serum'); ?></div>
      <div class="tab" data-tab="tab3"><?php _e('Mobile device', 'tcd-serum'); ?></div>
     </div>

     <?php // メインコンテンツの上部 ----------------------- ?>
     <div class="sub_box_tab_content active" data-tab-content="tab1">

      <div class="theme_option_message2" style="margin-top:20px;">
       <p><?php _e('This content will be displayed above main content.', 'tcd-serum');  ?></p>
      </div>

      <h4 class="theme_option_headline2"><?php _e('Free HTML area', 'tcd-serum');  ?></h4>
      <textarea class="full_width" cols="50" rows="10" name="dp_options[single_top_ad_code]"><?php echo esc_textarea( $options['single_top_ad_code'] ); ?></textarea>

     </div><!-- END .sub_box_tab_content -->

     <?php // メインコンテンツの下部 ----------------------- ?>
     <div class="sub_box_tab_content" data-tab-content="tab2">

      <div class="theme_option_message2" style="margin-top:20px;">
       <p><?php _e('This banner will be displayed after main content.', 'tcd-serum');  ?></p>
      </div>

      <textarea class="full_width" cols="50" rows="10" name="dp_options[single_bottom_ad_code]"><?php echo esc_textarea( $options['single_bottom_ad_code'] ); ?></textarea>

     </div><!-- END .sub_box_tab_content -->

     <?php // モバイル用 ----------------------- ?>
     <div class="sub_box_tab_content" data-tab-content="tab3">

      <div class="theme_option_message2" style="margin-top:20px;">
       <p><?php _e('This content will be displayed in mobile device only.', 'tcd-serum');  ?></p>
       <p><?php _e('This content will be display after main content and will be repleace by additional content for PC device.', 'tcd-serum');  ?></p>
      </div>

      <textarea class="full_width" cols="50" rows="10" name="dp_options[single_mobile_ad_code]"><?php echo esc_textarea( $options['single_mobile_ad_code'] ); ?></textarea>

     </div><!-- END .sub_box_tab_content -->

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


</div><!-- END .tab-content -->

<?php
} // END add_blog_tab_panel()


// バリデーション　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_blog_theme_options_validate( $input ) {

  global $dp_default_options, $font_type_options, $post_list_animation_type_options;

  // 基本設定
  $input['blog_show_date'] = wp_filter_nohtml_kses( $input['blog_show_date'] );

  // アーカイブ
  $input['archive_blog_catch'] = wp_filter_nohtml_kses( $input['archive_blog_catch'] );
  $input['archive_blog_catch_direction'] = wp_filter_nohtml_kses( $input['archive_blog_catch_direction'] );
  $input['archive_blog_header_bg_image'] = wp_filter_nohtml_kses( $input['archive_blog_header_bg_image'] );
  $input['archive_blog_header_overlay_color'] = wp_filter_nohtml_kses( $input['archive_blog_header_overlay_color'] );
  $input['archive_blog_header_overlay_opacity'] = wp_filter_nohtml_kses( $input['archive_blog_header_overlay_opacity'] );
  $input['archive_blog_desc'] = wp_filter_nohtml_kses( $input['archive_blog_desc'] );
  $input['archive_blog_desc_mobile'] = wp_filter_nohtml_kses( $input['archive_blog_desc_mobile'] );


  // 記事ページ
  $input['single_blog_show_sns_top'] = wp_filter_nohtml_kses( $input['single_blog_show_sns_top'] );
  $input['single_blog_show_sns_btm'] = wp_filter_nohtml_kses( $input['single_blog_show_sns_btm'] );
  $input['single_blog_show_copy_top'] = wp_filter_nohtml_kses( $input['single_blog_show_copy_top'] );
  $input['single_blog_show_copy_btm'] = wp_filter_nohtml_kses( $input['single_blog_show_copy_btm'] );
  $input['single_blog_show_tag_list'] = wp_filter_nohtml_kses( $input['single_blog_show_tag_list'] );


  // 関連記事
  $input['related_post_headline'] = wp_filter_nohtml_kses( $input['related_post_headline'] );
  $input['related_post_num'] = wp_filter_nohtml_kses( $input['related_post_num'] );
  $input['related_post_num_sp'] = wp_filter_nohtml_kses( $input['related_post_num_sp'] );


  // 記事ページのバナー広告
  $input['single_top_ad_code'] = $input['single_top_ad_code'];
  $input['single_bottom_ad_code'] = $input['single_bottom_ad_code'];
  $input['single_mobile_ad_code'] = $input['single_mobile_ad_code'];

  // CTA
  $input['show_blog_cta'] = ! empty( $input['show_blog_cta'] ) ? 1 : 0;
  $input['blog_cta_type'] = wp_filter_nohtml_kses( $input['blog_cta_type'] );

  $input['blog_cta_type1_image'] = wp_filter_nohtml_kses( $input['blog_cta_type1_image'] );
  $input['blog_cta_type1_url'] = wp_filter_nohtml_kses( $input['blog_cta_type1_url'] );
  $input['blog_cta_type1_target'] = ! empty( $input['blog_cta_type1_target'] ) ? 1 : 0;
  $input['blog_cta_type1_catch'] = wp_filter_nohtml_kses( $input['blog_cta_type1_catch'] );
  $input['blog_cta_type1_overlay_color'] = wp_filter_nohtml_kses( $input['blog_cta_type1_overlay_color'] );
  $input['blog_cta_type1_overlay_opacity'] = wp_filter_nohtml_kses( $input['blog_cta_type1_overlay_opacity'] );

  for ( $i = 1; $i <= 2; $i++ ) :

  $input['blog_cta_type2_image'.$i] = wp_filter_nohtml_kses( $input['blog_cta_type2_image'.$i] );
  $input['blog_cta_type2_url'.$i] = wp_filter_nohtml_kses( $input['blog_cta_type2_url'.$i] );
  $input['blog_cta_type2_target'.$i] = ! empty( $input['blog_cta_type2_target'.$i] ) ? 1 : 0;
  $input['blog_cta_type2_catch'.$i] = wp_filter_nohtml_kses( $input['blog_cta_type2_catch'.$i] );
  $input['blog_cta_type2_desc'.$i] = wp_filter_nohtml_kses( $input['blog_cta_type2_desc'.$i] );
  $input['blog_cta_type2_overlay_color'.$i] = wp_filter_nohtml_kses( $input['blog_cta_type2_overlay_color'.$i] );

  endfor;

	return $input;

};


?>