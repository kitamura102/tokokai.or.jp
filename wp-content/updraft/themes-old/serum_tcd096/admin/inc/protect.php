<?php
/*
 * 保護ページの設定
 */


// Add default values
add_filter( 'before_getting_design_plus_option', 'add_protect_dp_default_options' );


// Add label of logo tab
add_action( 'tcd_tab_labels', 'add_protect_tab_label' );


// Add HTML of logo tab
add_action( 'tcd_tab_panel', 'add_protect_tab_panel' );


// Register sanitize function
add_filter( 'theme_options_validate', 'add_protect_theme_options_validate' );


// タブの名前
function add_protect_tab_label( $tab_labels ) {
	$tab_labels['protect'] = __( 'Password protected page', 'tcd-serum' );
	return $tab_labels;
}


// 初期値
function add_protect_dp_default_options( $dp_default_options ) {

	for ( $i = 1; $i <= 3; $i++ ) {
		$dp_default_options['pw_name' . $i] = '';
		$dp_default_options['pw_editor' . $i] = '';
    $dp_default_options['pw_label' . $i] = '';
		$dp_default_options['pw_password_button_label' . $i] = '';
	}

	return $dp_default_options;

}


// 入力欄の出力　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_protect_tab_panel( $options ) {

  global $dp_default_options;

?>

<div id="tab-content-protect" class="tab-content">


  <?php // 保護ページの設定 ?>
  <div class="theme_option_field cf theme_option_field_ac open active">
    <h3 class="theme_option_headline"><?php _e( 'Password protected pages', 'tcd-serum' ); ?></h3>
    <div class="theme_option_field_ac_content">
      <h4 class="theme_option_headline2"><?php _e( 'Contents to encourage member registration', 'tcd-serum' ); ?></h4>
      <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
      <div class="sub_box">
        <h4 class="theme_option_subbox_headline"><?php echo tcd_admin_label('content').$i; ?><span><?php if ( $options['pw_name' . $i] ) { echo ' : ' . esc_html( $options['pw_name' . $i] ); } ?></span></h4>
        <div class="sub_box_content">
          <h4 class="theme_option_headline2"><?php echo tcd_admin_label('content_name'); ?></h4>
          <p><input type="text" class="theme_option_subbox_headline_label full_width" name="dp_options[pw_name<?php echo $i; ?>]" value="<?php echo esc_attr( $options['pw_name' . $i] ); ?>"></label></p>
          <div class="theme_option_message2">
            <p><?php _e( '"Name of contents" is used in edit post page.', 'tcd-serum' ); ?></p>
          </div>
          <h4 class="theme_option_headline2"><?php _e( 'Sentences to encourage member registration', 'tcd-serum' ); ?></h4>
          <div class="theme_option_message2">
            <p><?php _e( '"Sentences to encourage member registration" is displayed under excerpts.', 'tcd-serum' ); ?></p>
          </div>
          <?php wp_editor( $options['pw_editor' . $i], 'pw_editor' . $i, array ( 'textarea_name' => 'dp_options[pw_editor' . $i . ']' ) ); ?>
          <h4 class="theme_option_headline2"><?php _e( 'Password input field', 'tcd-serum' ); ?></h4>
          <ul class="option_list">
            <li class="cf"><span class="label"><?php _e( 'Label of password field', 'tcd-serum' ); ?></span><input class="full_width" type="text" name="dp_options[pw_label<?php echo $i; ?>]" value="<?php echo esc_attr( $options['pw_label' . $i] ); ?>"></li>
            <li class="cf"><span class="label"><?php _e( 'Name of password button', 'tcd-serum' ); ?></span><input class="full_width" type="text" name="dp_options[pw_password_button_label<?php echo $i; ?>]" value="<?php echo esc_attr( $options['pw_password_button_label' . $i] ); ?>"></li>
          </ul>
        </div>
      </div>
      <?php endfor; ?>
      <ul class="button_list cf">
        <li><input type="submit" class="button-ml ajax_button" value="<?php echo tcd_admin_label('save'); ?>" /></li>
        <li><a class="close_ac_content button-ml" href="#"><?php echo tcd_admin_label('close'); ?></a></li>
      </ul>
    </div><!-- END .theme_option_field_ac_content -->
  </div><!-- END .theme_option_field -->


</div><!-- END .tab-content -->

<?php
} // END add_protect_tab_panel()


// バリデーション　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_protect_theme_options_validate( $input ) {

  global $dp_default_options;

  for ( $i = 1; $i <= 3; $i++ ) {
    $input['pw_name' . $i] = wp_filter_nohtml_kses( $input['pw_name' . $i] );
    $input['pw_editor' . $i] = wp_kses_post($input['pw_editor' . $i]);
    $input['pw_label' . $i] = wp_filter_nohtml_kses( $input['pw_label' . $i] );
    $input['pw_password_button_label' . $i] = wp_filter_nohtml_kses( $input['pw_password_button_label' . $i] );
  }


	return $input;

};


?>