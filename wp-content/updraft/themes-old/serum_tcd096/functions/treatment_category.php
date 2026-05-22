<?php

// カテゴリー編集用入力欄を出力 -------------------------------------------------------
function treatment_category_edit_extra_fields( $term ) {
	$term_meta = get_option( 'taxonomy_' . $term->term_id, array() );
	$term_meta = array_merge( array(
		'desc1' => '',
		'desc2' => '',
		'desc3' => '',
		'image1' => null,
		'catch' => '',
		'desc4' => '',
		'image2' => null,
		'header_image' => null,
		'mega_desc' => '',
		'list_headline' => '',
		'list_desc' => '',
		'overlay_color' => '#000000',
		'overlay_opacity' => '0.2',
	), $term_meta );

  $options = get_design_plus_option();
  $treatment_label = $options['treatment_label'] ? esc_html( $options['treatment_label'] ) : __( 'Treatment', 'tcd-serum' );
  $current_category_name = $term->name;

?>
<tr class="form-field">
	<th colspan="2">

<div class="custom_category_meta">
 <h3 class="ccm_headline"><?php _e( 'Archive page', 'tcd-serum' ); ?></h3>

 <div class="ccm_content clearfix">
  <div class="input_field">
   <div class="theme_option_message no_arrow treatment_category_message">
    <p><?php echo __('The content set in this area will be displayed side by side with other categories in archive page.', 'tcd-serum'); ?></p>
   </div>
   <div class="treatment_category_image">
    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/treatment_archive_main_image.jpg" alt="" title="" />
   </div>
   <ul class="option_list">
    <li class="cf"><span class="label"><span class="num">1</span><?php _e('Description', 'tcd-serum'); ?></span><textarea placeholder="<?php _e( 'Please enter brief description of the category.', 'tcd-serum' ); ?>" class="full_width" cols="50" rows="5" name="term_meta[desc1]"><?php echo esc_textarea(  $term_meta['desc1'] ); ?></textarea></li>
    <li class="cf">
     <span class="label">
      <span class="num">2</span><?php _e('Image', 'tcd-serum'); ?>
      <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '930', '330'); ?></span>
     </span>
     <div class="image_box cf">
      <div class="cf cf_media_field hide-if-no-js image1">
       <input type="hidden" value="<?php if ( $term_meta['image1'] ) echo esc_attr( $term_meta['image1'] ); ?>" id="image1" name="term_meta[image1]" class="cf_media_id">
       <div class="preview_field"><?php if ( $term_meta['image1'] ) echo wp_get_attachment_image( $term_meta['image1'], 'medium'); ?></div>
       <div class="button_area">
        <input type="button" value="<?php _e( 'Select Image', 'tcd-serum' ); ?>" class="cfmf-select-img button">
        <input type="button" value="<?php _e( 'Remove Image', 'tcd-serum' ); ?>" class="cfmf-delete-img button <?php if ( ! $term_meta['image1'] ) echo 'hidden'; ?>">
       </div>
      </div>
     </div>
    </li>
    <li class="cf"><span class="label"><span class="num">3</span><?php _e('Description', 'tcd-serum'); ?></span><textarea placeholder="<?php _e( 'Please enter a little more in-depth content and an explanation according to the registered image.', 'tcd-serum' ); ?>" class="full_width" cols="50" rows="5" name="term_meta[desc2]"><?php echo esc_textarea(  $term_meta['desc2'] ); ?></textarea></li>
    <li class="cf"><span class="label"><span class="num">4</span><?php _e('Description', 'tcd-serum'); ?></span><textarea placeholder="<?php _e( 'Please enter a little more in-depth content and an explanation according to the registered image.', 'tcd-serum' ); ?>" class="full_width" cols="50" rows="5" name="term_meta[desc3]"><?php echo esc_textarea(  $term_meta['desc3'] ); ?></textarea></li>
   </ul>
  </div><!-- END input_field -->
 </div><!-- END ccm_content -->

</div><!-- END .custom_category_meta -->

<div class="custom_category_meta">
 <h3 class="ccm_headline"><?php _e( 'Category page', 'tcd-serum' ); ?></h3>

 <div class="ccm_content clearfix">
  <div class="input_field">
   <div class="treatment_category_image">
    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/treatment_category_image.jpg?2.0" alt="" title="" />
   </div>
   <ul class="option_list">
    <li class="cf">
     <span class="label">
      <span class="num">1</span><?php _e('Header image', 'tcd-serum'); ?>
      <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '1450', '450'); ?><br><?php _e('This image will also be used in the mega menu.', 'tcd-serum'); ?></span>
     </span>
     <div class="image_box cf">
      <div class="cf cf_media_field hide-if-no-js header_image">
       <input type="hidden" value="<?php if ( $term_meta['header_image'] ) echo esc_attr( $term_meta['header_image'] ); ?>" id="header_image" name="term_meta[header_image]" class="cf_media_id">
       <div class="preview_field"><?php if ( $term_meta['header_image'] ) echo wp_get_attachment_image( $term_meta['header_image'], 'medium'); ?></div>
       <div class="button_area">
        <input type="button" value="<?php _e( 'Select Image', 'tcd-serum' ); ?>" class="cfmf-select-img button">
        <input type="button" value="<?php _e( 'Remove Image', 'tcd-serum' ); ?>" class="cfmf-delete-img button <?php if ( ! $term_meta['header_image'] ) echo 'hidden'; ?>">
       </div>
      </div>
     </div>
    </li>
    <li class="cf"><span class="label"><span class="num">1</span><?php _e('Color of overlay', 'tcd-serum'); ?></span><input type="text" name="term_meta[overlay_color]" value="<?php echo esc_attr( $term_meta['overlay_color'] ); ?>" data-default-color="#000000" class="c-color-picker"></li>
    <li class="cf">
     <span class="label"><span class="num">1</span><?php _e('Transparency of overlay', 'tcd-serum'); ?></span><input class="hankaku" style="width:70px;" type="number" max="1" min="0" step="0.1" name="term_meta[overlay_opacity]" value="<?php echo esc_attr( $term_meta['overlay_opacity'] ); ?>" />
     <div class="theme_option_message2" style="clear:both; margin:7px 0 0 0;">
      <p><?php _e('Please specify the number of 0 from 0.9. Overlay color will be more transparent as the number is small.', 'tcd-serum');  ?>
      <?php _e('Please enter 0 if you don\'t want to use overlay.', 'tcd-serum');  ?></p>
     </div>
    </li>
    <li class="cf"><span class="label"><span class="num">2</span><?php _e('Catchphrase', 'tcd-serum'); ?></span><textarea placeholder="<?php _e( 'Please enter catchphrase of the category.', 'tcd-serum' ); ?>" class="full_width" cols="50" rows="3" name="term_meta[catch]"><?php echo esc_textarea(  $term_meta['catch'] ); ?></textarea></li>
    <li class="cf"><span class="label"><span class="num">3</span><?php _e('Description', 'tcd-serum'); ?></span><textarea placeholder="<?php _e( 'Please enter brief description of the category.', 'tcd-serum' ); ?>" class="full_width" cols="50" rows="5" name="term_meta[desc4]"><?php echo esc_textarea(  $term_meta['desc4'] ); ?></textarea></li>
    <li class="cf">
     <span class="label">
      <span class="num">4</span><?php _e('Image', 'tcd-serum'); ?>
      <span class="recommend_desc"><?php printf(__('Recommend image size. Width:%1$spx, Height:%2$spx.', 'tcd-serum'), '930', '330'); ?></span>
     </span>
     <div class="image_box cf">
      <div class="cf cf_media_field hide-if-no-js image2">
       <input type="hidden" value="<?php if ( $term_meta['image2'] ) echo esc_attr( $term_meta['image2'] ); ?>" id="image2" name="term_meta[image2]" class="cf_media_id">
       <div class="preview_field"><?php if ( $term_meta['image2'] ) echo wp_get_attachment_image( $term_meta['image2'], 'medium'); ?></div>
       <div class="button_area">
        <input type="button" value="<?php _e( 'Select Image', 'tcd-serum' ); ?>" class="cfmf-select-img button">
        <input type="button" value="<?php _e( 'Remove Image', 'tcd-serum' ); ?>" class="cfmf-delete-img button <?php if ( ! $term_meta['image2'] ) echo 'hidden'; ?>">
       </div>
      </div>
     </div>
    </li>
   </ul>
  </div><!-- END input_field -->
 </div><!-- END ccm_content -->

</div><!-- END .custom_category_meta -->

<div class="custom_category_meta">
 <h3 class="ccm_headline"><?php printf(__('%s list', 'tcd-serum'), $treatment_label); ?></h3>

 <div class="ccm_content clearfix">
  <div class="input_field">
   <div class="treatment_category_image">
    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/treatment_list_image.jpg?2.0" alt="" title="" />
   </div>
   <ul class="option_list">
    <li class="cf"><span class="label"><?php _e('Headline', 'tcd-serum'); ?></span><input placeholder="<?php printf(__('Please enter headline of %s list', 'tcd-serum'), $treatment_label); ?>" type="text" class="full_width" name="term_meta[list_headline]" value="<?php echo esc_html($term_meta['list_headline'] ); ?>" /></li>
    <li class="cf"><span class="label"><?php _e('Description', 'tcd-serum'); ?></span><textarea placeholder="<?php printf(__('Please enter description of %s list', 'tcd-serum'), $treatment_label); ?>" class="full_width" cols="50" rows="5" name="term_meta[list_desc]"><?php echo esc_textarea(  $term_meta['list_desc'] ); ?></textarea></li>
   </ul>
  </div><!-- END input_field -->
 </div><!-- END ccm_content -->

</div><!-- END .custom_category_meta -->


<div class="custom_category_meta">
 <h3 class="ccm_headline"><?php _e( 'Mega menu', 'tcd-serum' ); ?></h3>

 <div class="ccm_content clearfix">
  <div class="input_field">
   <div class="treatment_category_image">
    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/mega_menu_image.jpg" alt="" title="" />
   </div>
   <div class="theme_option_message2">
    <p><?php _e('The "header image" of the category page will be used for the maga menu image.', 'tcd-serum'); ?></p>
   </div>
   <ul class="option_list">
    <li class="cf"><span class="label"><?php _e('Description', 'tcd-serum'); ?></span><textarea placeholder="<?php _e( 'Please enter brief description of the category.', 'tcd-serum' ); ?>" class="full_width" cols="50" rows="5" name="term_meta[mega_desc]"><?php echo esc_textarea(  $term_meta['mega_desc'] ); ?></textarea></li>
   </ul>
  </div><!-- END input_field -->
 </div><!-- END ccm_content -->

</div><!-- END .custom_category_meta -->

 </th>
</tr><!-- END .form-field -->
<?php
}
add_action( 'treatment_category_edit_form_fields', 'treatment_category_edit_extra_fields' );


// データを保存 -------------------------------------------------------
function treatment_category_save_extra_fileds( $term_id ) {
  $new_meta = array();
  if ( isset( $_POST['term_meta'] ) ) {
		$current_term_id = $term_id;
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$new_meta[$key] = stripslashes($_POST['term_meta'][$key]);
			}
		}
	}
  update_option( "taxonomy_$current_term_id", $new_meta );
}
add_action( 'edited_treatment_category', 'treatment_category_save_extra_fileds' );


