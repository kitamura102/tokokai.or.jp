<?php
function treatment_meta_box() {
  $options = get_design_plus_option();
  $treatment_label = $options['treatment_label'] ? esc_html( $options['treatment_label'] ) : __( 'Treatment', 'tcd-serum' );
  add_meta_box(
    'treatment_meta_box',//ID of meta box
    sprintf(__('%s page', 'tcd-serum'), $treatment_label),//label
    'show_treatment_meta_box',//callback function
    'treatment',// post type
    'normal',// context
    'high'// priority
  );
}
add_action('add_meta_boxes', 'treatment_meta_box');

function show_treatment_meta_box() {

  global $post;

  $price_list = get_post_meta($post->ID, 'price_list', true);
  $faq_list = get_post_meta($post->ID, 'faq_list', true);


  echo '<input type="hidden" name="treatment_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

  //入力欄 ***************************************************************************************************************************************************************************************
?>

<div class="tcd_custom_field_wrap">


  <?php // FAQの設定 --------------------------------------------------- ?>
  <div id="page_faq_option" class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e( 'FAQ', 'tcd-serum' ); ?></h3>
    <div class="theme_option_field_ac_content">

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


  <?php // 料金の設定 --------------------------------------------------- ?>
  <div id="page_price_option" class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e( 'Price', 'tcd-serum' ); ?></h3>
    <div class="theme_option_field_ac_content">

      <div class="theme_option_message2">
        <p><?php _e('Please copy and paste the short code below where you want to display price list.', 'tcd-serum'); ?></p>
      </div>

      <h3 class="theme_option_headline2"><?php _e('Short code', 'tcd-serum'); ?></h3>
      <input class="fullwidth" type="text" value="[sc_price]" readonly>

      <?php // リスト ------------------------------------------------------------------------- ?>
      <h4 class="theme_option_headline2"><?php _e( 'Price list', 'tcd-serum' ); ?></h4>
      <?php //繰り返しフィールド ----- ?>
      <div class="repeater-wrapper">
        <div class="repeater sortable" data-delete-confirm="<?php echo tcd_admin_label('delete'); ?>">
          <?php
              if ( $price_list ) :
                foreach ( $price_list as $key => $value ) :
          ?>
          <div class="sub_box repeater-item repeater-item-<?php echo $key; ?>">
            <h4 class="theme_option_subbox_headline"><?php echo esc_html( ! empty( $price_list[$key]['title'] ) ? $price_list[$key]['title'] : tcd_admin_label('new_item') ); ?></h4>
            <div class="sub_box_content">
              <h4 class="theme_option_headline2"><?php _e( 'Title', 'tcd-serum' ); ?></h4>
              <p><input class="repeater-label full_width" type="text" name="price_list[<?php echo esc_attr( $key ); ?>][title]" value="<?php echo esc_attr( isset( $price_list[$key]['title'] ) ? $price_list[$key]['title'] : '' ); ?>" /></p>
              <h4 class="theme_option_headline2"><?php _e( 'Price', 'tcd-serum' ); ?></h4>
              <textarea class="full_width" cols="50" rows="5" name="price_list[<?php echo esc_attr( $key ); ?>][price]"><?php echo esc_attr( isset( $price_list[$key]['price'] ) ? $price_list[$key]['price'] : '' ); ?></textarea>
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
            <h4 class="theme_option_subbox_headline"><?php echo esc_html( ! empty( $price_list[$key]['question'] ) ? $price_list[$key]['question'] : tcd_admin_label('new_item') ); ?></h4>
            <div class="sub_box_content">
              <h4 class="theme_option_headline2"><?php _e( 'Title', 'tcd-serum' ); ?></h4>
              <p><input class="repeater-label full_width" type="text" name="price_list[<?php echo esc_attr( $key ); ?>][title]" value="<?php echo esc_attr( isset( $price_list[$key]['title'] ) ? $price_list[$key]['title'] : '' ); ?>" /></p>
              <h4 class="theme_option_headline2"><?php _e( 'Price', 'tcd-serum' ); ?></h4>
              <textarea class="full_width" cols="50" rows="5" name="price_list[<?php echo esc_attr( $key ); ?>][price]"><?php echo esc_attr( isset( $price_list[$key]['price'] ) ? $price_list[$key]['price'] : '' ); ?></textarea>
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


</div><!-- END .tcd_custom_field_wrap -->

<?php
}

function save_treatment_meta_box( $post_id ) {

  // verify nonce
  if (!isset($_POST['treatment_meta_box_nonce']) || !wp_verify_nonce($_POST['treatment_meta_box_nonce'], basename(__FILE__))) {
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
  $cf_keys = array();
  foreach ($cf_keys as $cf_key) {
    $old = get_post_meta($post_id, $cf_key, true);

    if (isset($_POST[$cf_key])) {
      $new = $_POST[$cf_key];
    } else {
      $new = '';
    }

    if ($new && $new != $old) {
      update_post_meta($post_id, $cf_key, $new);
    } elseif ('' == $new && $old) {
      delete_post_meta($post_id, $cf_key, $old);
    }
  }

  // repeater save or delete
  $cf_keys = array('faq_list','price_list');
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
add_action('save_post', 'save_treatment_meta_box');



?>