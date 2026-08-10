<?php
/*
 * ヘッダーの設定
 */


// Add default values
add_filter( 'before_getting_design_plus_option', 'add_header_dp_default_options' );


// Add label of logo tab
add_action( 'tcd_tab_labels', 'add_header_tab_label' );


// Add HTML of logo tab
add_action( 'tcd_tab_panel', 'add_header_tab_panel' );


// Register sanitize function
add_filter( 'theme_options_validate', 'add_header_theme_options_validate' );


// タブの名前
function add_header_tab_label( $tab_labels ) {
	$tab_labels['header'] = __( 'Header', 'tcd-serum' );
	return $tab_labels;
}


// 初期値
function add_header_dp_default_options( $dp_default_options ) {

  $main_color = $dp_default_options['main_color'];

  //言語ボタン
	$dp_default_options['show_lang_button'] = '';
	$dp_default_options['lang_button'] = array();

  //検索フォーム
	$dp_default_options['show_header_search'] = 'display';

  // メガメニュー
  $dp_default_options['megamenu_new'] = array();

  // メッセージ
	$dp_default_options['show_header_message'] = 'hide';
	$dp_default_options['header_message'] = __('Header message', 'tcd-serum');
  $dp_default_options['header_message_url'] = '#';
  $dp_default_options['header_message_url_target'] = '';
  $dp_default_options['header_message_font_color'] = '#ffffff';
  $dp_default_options['header_message_bg_color'] = $main_color;
	$dp_default_options['megamenu_a_post_type'] = 'recent_post';
	$dp_default_options['megamenu_a_post_order'] = 'date';
	$dp_default_options['megamenu_a_post_num'] = '8';

  // アイコンボタン
	$dp_default_options['show_side_icon_button'] = '1';
	$dp_default_options['side_icon_button'] = array(
		array(
			"title" => __( 'Button', 'tcd-serum' ),
			"url" => "#",
			"target" => "",
			"icon" => "no_icon",
    ),
		array(
			"title" => __( 'Button', 'tcd-serum' ),
			"url" => "#",
			"target" => "",
			"icon" => "clock",
    ),
		array(
			"title" => __( 'Button', 'tcd-serum' ),
			"url" => "#",
			"target" => "",
			"icon" => "map",
    ),
		array(
			"title" => __( 'Button', 'tcd-serum' ),
			"url" => "#",
			"target" => "",
			"icon" => "calendar",
    ),
  );

	return $dp_default_options;

}


// 入力欄の出力　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_header_tab_panel( $options ) {

  global $blog_label, $dp_default_options, $header_fix_options, $header_fix_options2, $content_width_options, $font_type_options, $megamenu_options, $basic_display_options, $bool_options, $drawer_menu_color_type_options, $footer_bar_icon_options;
  $news_label = $options['news_label'] ? esc_html( $options['news_label'] ) : __( 'News', 'tcd-serum' );
  $treatment_label = $options['treatment_label'] ? esc_html( $options['treatment_label'] ) : __( 'Treatment', 'tcd-serum' );

  $main_color = $options['main_color'];

?>

<div id="tab-content-header" class="tab-content">


   <?php // 基本設定 ----------------------------------------------------------------- ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Global menu', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <h4 class="theme_option_headline2"><?php _e( 'Logo', 'tcd-serum' ); ?></h4>
     <div class="theme_option_message2">
      <p><?php echo __('You can set logo from "Basic Settings" logo section.', 'tcd-serum'); ?></p>
     </div>

     <h4 class="theme_option_headline2"><?php _e( 'Menu', 'tcd-serum' ); ?></h4>
     <div class="theme_option_message2">
      <p><?php echo __('You can set menu from <a href="./nav-menus.php" target="_blank">Menu page</a>.', 'tcd-serum'); ?></p>
     </div>

     <?php // 検索フォーム ----------------------------------------------------------------- ?>
     <h4 class="theme_option_headline2"><?php _e('Search form', 'tcd-serum');  ?></h4>
     <div class="theme_option_message2">
      <p><?php _e('You can set more details about search form from basic setting menu.','tcd-serum'); ?></p>
     </div>
     <p><?php echo tcd_basic_radio_button($options, 'show_header_search', $basic_display_options); ?></p>
     <br style="clear:both;">

     <?php // 言語ボタン ----------------------------------------------------------------- ?>
     <h4 class="theme_option_headline2"><?php _e('Language button', 'tcd-serum');  ?></h4>
     <p class="displayment_checkbox"><label><input name="dp_options[show_lang_button]" type="checkbox" value="1" <?php checked( $options['show_lang_button'], 1 ); ?>><?php _e( 'Display language button', 'tcd-serum' ); ?></label></p>
     <div style="<?php if($options['show_lang_button'] == 1) { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">
      <div class="theme_option_message2">
       <p><?php _e('Displays a link button to a multilingual site. (example: JP / EN, etc)', 'tcd-serum');  ?><br>
       <?php _e('Click add item button to start this option.<br />You can change order by dragging each headline of option field.', 'tcd-serum');  ?></p>
      </div>
      <?php //繰り返しフィールド ----- ?>
      <div class="repeater-wrapper">
       <input type="hidden" name="dp_options[lang_button]" value="">
       <div class="repeater sortable" data-delete-confirm="<?php _e( 'Delete?', 'tcd-serum' ); ?>">
        <?php
             if ( $options['lang_button'] ) :
               foreach ( $options['lang_button'] as $key => $value ) :
        ?>
        <div class="sub_box repeater-item repeater-item-<?php echo esc_attr( $key ); ?>">
         <h4 class="theme_option_subbox_headline"><?php if($value['name']) { echo esc_html( $value['name'] ); } else { _e( 'Item', 'tcd-serum' ); }; ?></h4>
         <div class="sub_box_content">
          <p><label><input name="dp_options[lang_button][<?php echo esc_attr( $key ); ?>][active_button]" type="checkbox" value="1" <?php checked( $value['active_button'], 1 ); ?>><?php _e( 'Set this language button as active button', 'tcd-serum' ); ?></label></p>
          <h4 class="theme_option_headline2"><?php _e( 'Language name', 'tcd-serum' ); ?></h4>
          <input class="repeater-label full_width" type="text" name="dp_options[lang_button][<?php echo esc_attr( $key ); ?>][name]" value="<?php echo esc_attr( $value['name'] ); ?>" />
          <h4 class="theme_option_headline2"><?php _e( 'URL', 'tcd-serum' ); ?></h4>
          <input class="full_width" type="text" name="dp_options[lang_button][<?php echo esc_attr( $key ); ?>][url]" value="<?php echo esc_url( $value['url'] ); ?>" />
          <p class="delete-row right-align"><a href="#" class="button button-secondary button-delete-row"><?php _e( 'Delete item', 'tcd-serum' ); ?></a></p>
         </div><!-- END .sub_box_content -->
        </div><!-- END .sub_box -->
        <?php
               endforeach;
             endif;
             $key = 'addindex';
             $value = array(
              'active_button' => '',
              'name' => '',
              'url' => '',
             );
             ob_start();
        ?>
        <div class="sub_box repeater-item repeater-item-<?php echo $key; ?>">
         <h4 class="theme_option_subbox_headline"><?php _e( 'New item', 'tcd-serum' ); ?></h4>
         <div class="sub_box_content">
          <p><label><input name="dp_options[lang_button][<?php echo esc_attr( $key ); ?>][active_button]" type="checkbox" value="1" <?php checked( $value['active_button'], 1 ); ?>><?php _e( 'Set this language button as active button', 'tcd-serum' ); ?></label></p>
          <h4 class="theme_option_headline2"><?php _e( 'Language name', 'tcd-serum' ); ?></h4>
          <input class="repeater-label full_width" type="text" name="dp_options[lang_button][<?php echo esc_attr( $key ); ?>][name]" value="<?php echo esc_attr( $value['name'] ); ?>" />
          <h4 class="theme_option_headline2"><?php _e( 'URL', 'tcd-serum' ); ?></h4>
          <input class="full_width" type="text" name="dp_options[lang_button][<?php echo esc_attr( $key ); ?>][url]" value="<?php echo esc_url( $value['url'] ); ?>" />
          <p class="delete-row right-align"><a href="#" class="button button-secondary button-delete-row"><?php _e( 'Delete item', 'tcd-serum' ); ?></a></p>
         </div><!-- END .sub_box_content -->
        </div><!-- END .sub_box -->
        <?php
             $clone = ob_get_clean();
        ?>
       </div><!-- END .repeater -->
       <a href="#" class="button button-secondary button-add-row" data-clone="<?php echo esc_attr( $clone ); ?>"><?php _e( 'Add item', 'tcd-serum' ); ?></a>
      </div><!-- END .repeater-wrapper -->
      <?php //繰り返しフィールドここまで ----- ?>
     </div>

     <?php // メガメニュー ----------------------------------------------------------------- ?>
     <h4 class="theme_option_headline2"><?php _e('Mega menu', 'tcd-serum');  ?></h4>
     <div class="theme_option_message2">
      <p><?php _e('If any of the following pages are set to "Global Menu" on the <a href="./nav-menus.php" target="_blank">"Menu Screen"</a>, they can be displayed as mega menus.', 'tcd-serum'); ?></p>
      <p><?php printf(__('%s archive page<br>%s archive page (Please set %s category page for child menu under parent menu)<br>%s archive page (Please set %s category for child menu under parent menu)', 'tcd-serum'), $blog_label, $news_label, $news_label, $treatment_label, $treatment_label); ?></p>
     </div>
     <ul class="megamenu_image clearfix">
      <li>
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/megamenu3.jpg" alt="<?php printf(__('%s carousel', 'tcd-serum'), $blog_label); ?>" title="" />
       <p><?php echo esc_attr($blog_label); ?></p>
      </li>
      <li>
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/megamenu2.jpg" alt="<?php printf(__('%s post', 'tcd-serum'), $news_label); ?>" title="" />
       <p><?php echo esc_attr($news_label); ?></p>
      </li>
      <li>
       <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/megamenu4.jpg" alt="<?php printf(__('%s carousel', 'tcd-serum'), $treatment_label); ?>" title="" />
       <p><?php echo esc_attr($treatment_label); ?></p>
      </li>
     </ul>

     <h4 class="theme_option_headline2 no_megamenu_option"><?php _e('Menu type setting', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2 no_megamenu_option">
      <p><?php _e('None of the menu items registered in the <a href="./nav-menus.php" target="_blank">"Menu Screen"</a> can be turned into a mega menu.', 'tcd-serum'); ?></p>
     </div>

     <?php
          $menu_locations = get_nav_menu_locations();
          $nav_menus = wp_get_nav_menus();
          $global_nav_items = array();
          if ( isset( $menu_locations['global-menu'] ) ) {
            foreach ( (array) $nav_menus as $menu ) {
              if ( $menu_locations['global-menu'] === $menu->term_id ) {
                $global_nav_items = wp_get_nav_menu_items( $menu );
                break;
              }
            }
          }
     ?>
     <h4 class="theme_option_headline2 megamenu_option"><?php _e('Menu type setting', 'tcd-serum'); ?></h4>
     <div class="theme_option_message2 megamenu_option">
      <p><?php _e('The menu items set in the <a href="./nav-menus.php" target="_blank">"Menu screen"</a> that can be turned into a mega menu are displayed below.', 'tcd-serum'); ?></p>
     </div>
     <ul class="option_list megamenu_option">
      <?php
           $i = 1;
           $megamenu_a_flag = true;
           $megamenu_b_flag = true;
           $megamenu_c_flag = true;
           foreach ( $global_nav_items as $item ) :
             if ( $item->menu_item_parent ) continue;
             if( $megamenu_a_flag && ( ($item->object == 'news' && $item->type == 'post_type_archive') || $item->url == get_post_type_archive_link('news') ) ){
               $has_term_menu = false;
               foreach ( $global_nav_items as $item2 ) {
                 if ( $item2->menu_item_parent == $item->ID && $item2->object === 'news_category' ) {
                   $has_term_menu = true;
                   break;
                 }
               }
               if ( $has_term_menu ) {
                 $value = isset( $options['megamenu_new'][$item->ID] ) ? $options['megamenu_new'][$item->ID] : 'dropdown';
                 $megamenu_a_flag = false;
      ?>
      <li class="cf">
       <span class="label"><?php echo esc_html( $item->title ); ?></span>
       <div class="standard_radio_button">
        <input id="use_megamenu_a_yes_<?php echo $item->ID . $i; ?>" type="radio" name="dp_options[megamenu_new][<?php echo $item->ID; ?>]" value="use_megamenu_a" <?php checked( $value, 'use_megamenu_a' ); ?>>
        <label for="use_megamenu_a_yes_<?php echo $item->ID . $i; ?>"><?php _e('Mega menu', 'tcd-serum'); ?></label>
        <input id="use_megamenu_a_no_<?php echo $item->ID . $i; ?>" type="radio" name="dp_options[megamenu_new][<?php echo $item->ID; ?>]" value="dropdown" <?php checked( $value, 'dropdown' ); ?>>
        <label for="use_megamenu_a_no_<?php echo $item->ID . $i; ?>"><?php _e('Normal menu', 'tcd-serum'); ?></label>
       </div>
      </li>
      <?php
               };
             } elseif( $megamenu_b_flag && ( $item->url == get_permalink(get_option('page_for_posts')) ) ){
               $value = isset( $options['megamenu_new'][$item->ID] ) ? $options['megamenu_new'][$item->ID] : 'dropdown';
               $megamenu_b_flag = false;
      ?>
      <li class="cf">
       <span class="label"><?php echo esc_html( $item->title ); ?></span>
       <div class="standard_radio_button">
        <input id="use_megamenu_b_yes_<?php echo $item->ID . $i; ?>" type="radio" name="dp_options[megamenu_new][<?php echo $item->ID; ?>]" value="use_megamenu_b" <?php checked( $value, 'use_megamenu_b' ); ?>>
        <label for="use_megamenu_b_yes_<?php echo $item->ID . $i; ?>"><?php _e('Mega menu', 'tcd-serum'); ?></label>
        <input id="use_megamenu_b_no_<?php echo $item->ID . $i; ?>" type="radio" name="dp_options[megamenu_new][<?php echo $item->ID; ?>]" value="dropdown" <?php checked( $value, 'dropdown' ); ?> >
        <label for="use_megamenu_b_no_<?php echo $item->ID . $i; ?>"><?php _e('Normal menu', 'tcd-serum'); ?></label>
       </div>
      </li>
      <?php
             } elseif( $megamenu_c_flag && ( ($item->object == 'treatment' && $item->type == 'post_type_archive') || $item->url == get_post_type_archive_link('treatment') ) ){
               $has_term_menu = false;
               foreach ( $global_nav_items as $item2 ) {
                 if ( $item2->menu_item_parent == $item->ID && $item2->object === 'treatment_category') {
                   $has_term_menu = true;
                   break;
                 }
               }
               if ( $has_term_menu ) {
                 $value = isset( $options['megamenu_new'][$item->ID] ) ? $options['megamenu_new'][$item->ID] : 'dropdown';
                 $megamenu_c_flag = false;
      ?>
      <li class="cf">
       <span class="label"><?php echo esc_html( $item->title ); ?></span>
       <div class="standard_radio_button">
        <input id="use_megamenu_c_yes_<?php echo $item->ID . $i; ?>" type="radio" name="dp_options[megamenu_new][<?php echo $item->ID; ?>]" value="use_megamenu_c" <?php checked( $value, 'use_megamenu_c' ); ?>>
        <label for="use_megamenu_c_yes_<?php echo $item->ID . $i; ?>"><?php _e('Mega menu', 'tcd-serum'); ?></label>
        <input id="use_megamenu_c_no_<?php echo $item->ID . $i; ?>" type="radio" name="dp_options[megamenu_new][<?php echo $item->ID; ?>]" value="dropdown" <?php checked( $value, 'dropdown' ); ?>>
        <label for="use_megamenu_c_no_<?php echo $item->ID . $i; ?>"><?php _e('Normal menu', 'tcd-serum'); ?></label>
       </div>
      </li>
      <?php
               }
             }
             $i++;
           endforeach;
      ?>
     </ul>

     <h4 class="theme_option_headline2"><?php printf(__('%s carousel', 'tcd-serum'), $blog_label); ?></h4>
     <ul class="option_list">
      <li class="cf">
       <span class="label"><?php _e('Post type', 'tcd-serum');  ?></span>
       <div class="standard_radio_button">
        <input id="megamenu_a_post_type1" type="radio" name="dp_options[megamenu_a_post_type]" value="recent_post" <?php checked( $options['megamenu_a_post_type'], 'recent_post' ); ?>>
        <label for="megamenu_a_post_type1"><?php _e('Recent post', 'tcd-serum'); ?></label>
        <input id="megamenu_a_post_type2" type="radio" name="dp_options[megamenu_a_post_type]" value="recommend_post" <?php checked( $options['megamenu_a_post_type'], 'recommend_post' ); ?>>
        <label for="megamenu_a_post_type2"><?php _e('Recommend post', 'tcd-serum'); ?></label>
        <input id="megamenu_a_post_type3" type="radio" name="dp_options[megamenu_a_post_type]" value="recommend_post2" <?php checked( $options['megamenu_a_post_type'], 'recommend_post2' ); ?>>
        <label for="megamenu_a_post_type3"><?php _e('Recommend post', 'tcd-serum'); ?>2</label>
        <input id="megamenu_a_post_type4" type="radio" name="dp_options[megamenu_a_post_type]" value="recommend_post3" <?php checked( $options['megamenu_a_post_type'], 'recommend_post3' ); ?>>
        <label for="megamenu_a_post_type4"><?php _e('Recommend post', 'tcd-serum'); ?>3</label>
       </div>
      </li>
      <li class="cf">
       <span class="label"><?php _e('Post order', 'tcd-serum');  ?></span>
       <div class="standard_radio_button">
        <input id="megamenu_a_post_order1" type="radio" name="dp_options[megamenu_a_post_order]" value="date" <?php checked( $options['megamenu_a_post_order'], 'date' ); ?>>
        <label for="megamenu_a_post_order1"><?php _e('Date', 'tcd-serum'); ?></label>
        <input id="megamenu_a_post_order2" type="radio" name="dp_options[megamenu_a_post_order]" value="rand" <?php checked( $options['megamenu_a_post_order'], 'rand' ); ?>>
        <label for="megamenu_a_post_order2"><?php _e('Random', 'tcd-serum'); ?></label>
       </div>
      </li>
      <li class="cf">
       <span class="label"><?php _e('Number of post to display', 'tcd-serum'); ?></span>
       <select name="dp_options[megamenu_a_post_num]">
        <option value="4"<?php selected( $options['megamenu_a_post_num'], '4' ); ?>>4</option>
        <option value="8"<?php selected( $options['megamenu_a_post_num'], '8' ); ?>>8</option>
        <option value="12"<?php selected( $options['megamenu_a_post_num'], '12' ); ?>>12</option>
       </select>
      </li>
     </ul>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>

    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // アイコンボタンの設定 ------------------------------------------------------------ ?>
   <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Side icon button', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <div class="front_page_image">
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/side_icon_image.jpg" alt="" title="" />
     </div>

     <p class="displayment_checkbox"><label><input name="dp_options[show_side_icon_button]" type="checkbox" value="1" <?php checked( $options['show_side_icon_button'], 1 ); ?>><?php _e( 'Display icon button', 'tcd-serum' ); ?></label></p>

     <div style="<?php if($options['show_side_icon_button'] == 1) { echo 'display:block;'; } else { echo 'display:none;'; }; ?>">

      <div class="theme_option_message">
       <p><?php _e('Click add button button to start this option.<br />You can change order by dragging each headline of option field.', 'tcd-serum');  ?></p>
      </div>

      <?php //繰り返しフィールド ----- ?>
      <div class="repeater-wrapper">
       <input type="hidden" name="dp_options[side_icon_button]" value="">
       <div class="repeater sortable" data-delete-confirm="<?php _e( 'Delete this button?', 'tcd-serum' ); ?>">
        <?php
             if ( $options['side_icon_button'] ) :
               foreach ( $options['side_icon_button'] as $key => $value ) :
        ?>
        <div class="sub_box repeater-item repeater-item-<?php echo esc_attr( $key ); ?>">
         <h4 class="theme_option_subbox_headline"><?php _e( 'Button', 'tcd-serum' ); echo $key+1; ?></h4>
         <div class="sub_box_content">

          <h4 class="theme_option_headline2"><?php _e('Button', 'tcd-serum'); ?></h4>
          <ul class="option_list">
           <li class="cf"><span class="label"><?php _e('Title', 'tcd-serum'); ?></span><textarea class="repeater-label full_width" cols="50" rows="2" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][title]"><?php echo esc_textarea( $value['title'] ); ?></textarea></li>
           <li class="cf button_option">
            <span class="label"><?php _e('URL', 'tcd-serum'); ?></span>
            <div class="admin_link_option">
             <input class="full_width" type="text" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][url]" value="<?php echo esc_attr( $value['url'] ); ?>" placeholder="https://example.com/">
             <input id="side_icon_button_target<?php echo $key; ?>" class="admin_link_option_target" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][target]" type="checkbox" value="1" <?php checked( $value['target'], 1 ); ?>>
             <label for="side_icon_button_target<?php echo $key; ?>">&#xe92a;</label>
            </div>
           </li>
          </ul>

          <div class="footer_bar_icon_option">
           <h4 class="theme_option_headline2"><?php _e('Icon', 'tcd-serum'); ?></h4>
           <ul class="footer_bar_icon_type cf">
            <?php foreach( $footer_bar_icon_options as $option ) : ?>
            <li><label><input type="radio" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][icon]" value="<?php echo esc_attr($option['value']); ?>" <?php checked( $option['value'], $value['icon'] ); ?>><span class="icon icon-<?php echo esc_attr($option['value']); ?>"></span></label></li>
            <?php endforeach; ?>
            <li class="wide material_icon"><label><input type="radio" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][icon]" value="material_icon" <?php checked( 'material_icon', $value['icon'] ); ?>><span class="icon_label"><?php _e( 'Others', 'tcd-serum' ); ?></span></label></li>
            <li class="wide"><label><input type="radio" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][icon]" value="no_icon" <?php checked( 'no_icon', $value['icon'] ); ?>><span class="icon no_icon"><?php _e( 'NO ICON', 'tcd-serum' ); ?></span></label></li>
           </ul>
           <div class="theme_option_message2 material_icon_option">
            <p><?php _e('Please enter any icon code from Google Fonts.<br><a href="https://fonts.google.com/icons?selected=Material+Symbols+Outlined:redo:FILL@0;wght@400;GRAD@0;opsz@24" target="_blank">Click here for a list of icons from Google Fonts.</a>', 'tcd-serum'); ?></p>
           </div>
           <input class="full_width material_icon_option"  style="display:none;" type="text" placeholder="<?php _e( 'ex: e88a', 'tcd-serum' ); ?>" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][material_icon]" value="<?php if(isset($value['material_icon'])){ echo esc_attr( $value['material_icon'] ); }; ?>">
          </div>

          <ul class="button_list cf">
           <li style="float:right; margin:0;" class="delete-row"><a class="button-delete-row button-ml red_button" href="#"><?php echo __( 'Delete button', 'tcd-serum' ); ?></a></li>
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
         <h4 class="theme_option_subbox_headline"><?php _e( 'New button', 'tcd-serum' ); ?></h4>
         <div class="sub_box_content">

          <h4 class="theme_option_headline2"><?php _e('Button', 'tcd-serum'); ?></h4>
          <ul class="option_list">
           <li class="cf"><span class="label"><?php _e('Title', 'tcd-serum'); ?></span><textarea class="repeater-label full_width" cols="50" rows="2" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][title]"><?php echo esc_textarea( $value['title'] ); ?></textarea></li>
           <li class="cf button_option">
            <span class="label"><?php _e('URL', 'tcd-serum'); ?></span>
            <div class="admin_link_option">
             <input class="full_width" type="text" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][url]" value="<?php echo esc_attr( $value['url'] ); ?>" placeholder="https://example.com/">
             <input id="side_icon_button_target<?php echo $key; ?>" class="admin_link_option_target" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][target]" type="checkbox" value="1" <?php checked( $value['target'], 1 ); ?>>
             <label for="side_icon_button_target<?php echo $key; ?>">&#xe92a;</label>
            </div>
           </li>
          </ul>

          <div class="footer_bar_icon_option">
           <h4 class="theme_option_headline2"><?php _e('Icon', 'tcd-serum'); ?></h4>
           <ul class="footer_bar_icon_type cf">
            <?php foreach( $footer_bar_icon_options as $option ) : ?>
            <li><label><input type="radio" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][icon]" value="<?php echo esc_attr($option['value']); ?>" <?php checked( $option['value'], $value['icon'] ); ?>><span class="icon icon-<?php echo esc_attr($option['value']); ?>"></span></label></li>
            <?php endforeach; ?>
            <li class="wide material_icon"><label><input type="radio" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][icon]" value="material_icon" <?php checked( 'material_icon', $value['icon'] ); ?>><span class="icon_label"><?php _e( 'Others', 'tcd-serum' ); ?></span></label></li>
            <li class="wide"><label><input type="radio" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][icon]" value="no_icon" <?php checked( 'no_icon', $value['icon'] ); ?>><span class="icon no_icon"><?php _e( 'NO ICON', 'tcd-serum' ); ?></span></label></li>
           </ul>
           <div class="theme_option_message2 material_icon_option" style="display:none;">
            <p><?php _e('Please enter any icon code from Google Fonts.<br><a href="https://fonts.google.com/icons?selected=Material+Symbols+Outlined:redo:FILL@0;wght@400;GRAD@0;opsz@24" target="_blank">Click here for a list of icons from Google Fonts.</a>', 'tcd-serum'); ?></p>
           </div>
           <input class="full_width material_icon_option"  style="display:none;" type="text" placeholder="<?php _e( 'ex: e88a', 'tcd-serum' ); ?>" name="dp_options[side_icon_button][<?php echo esc_attr( $key ); ?>][material_icon]" value="<?php if(isset($value['material_icon'])){ echo esc_attr( $value['material_icon'] ); }; ?>">
          </div>

          <ul class="button_list cf">
           <li style="float:right; margin:0;" class="delete-row"><a class="button-delete-row button-ml red_button" href="#"><?php echo __( 'Delete button', 'tcd-serum' ); ?></a></li>
          </ul>
         </div><!-- END .sub_box_content -->
        </div><!-- END .sub_box -->
        <?php
             $clone = ob_get_clean();
        ?>
       </div><!-- END .repeater -->
       <a href="#" class="button button-secondary button-add-row" data-clone="<?php echo esc_attr( $clone ); ?>"><?php _e( 'Add button', 'tcd-serum' ); ?></a>
      </div><!-- END .repeater-wrapper -->
      <?php //繰り返しフィールドここまで ----- ?>

     </div>

     <ul class="button_list cf">
      <li><input type="submit" class="button-ml ajax_button" value="<?php echo __( 'Save Changes', 'tcd-serum' ); ?>" /></li>
      <li><a class="close_ac_content button-ml" href="#"><?php echo __( 'Close', 'tcd-serum' ); ?></a></li>
     </ul>
    </div><!-- END .theme_option_field_ac_content -->
   </div><!-- END .theme_option_field -->


   <?php // メッセージ ----------------------------------------- ?>
  <div class="theme_option_field cf theme_option_field_ac">
    <h3 class="theme_option_headline"><?php _e('Header message', 'tcd-serum');  ?></h3>
    <div class="theme_option_field_ac_content">

     <div class="front_page_image">
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/admin/img/header_message_image.jpg" alt="" title="" />
     </div>

      <div class="theme_option_message2">
       <p><?php _e('The "header message" is displayed at the top of the site (above the header bar).', 'tcd-serum'); ?></br>
       <?php _e('If you are using LP template, you can set display setting individually from page edit screen.', 'tcd-serum'); ?></p>
      </div>

      <ul class="option_list">
       <li class="cf"><span class="label"><?php _e('Header message', 'tcd-serum');  ?></span><?php echo tcd_basic_radio_button($options, 'show_header_message', $basic_display_options); ?></li>
       <li class="cf"><span class="label"><?php _e('Message', 'tcd-serum');  ?></span><textarea class="full_width" cols="50" rows="2" name="dp_options[header_message]"><?php echo esc_textarea( $options['header_message'] ); ?></textarea></li>
       <li class="cf">
        <span class="label"><?php _e('URL', 'tcd-serum');  ?></span>
        <div class="admin_link_option">
            <input placeholder="https://example.com/" id="dp_options[header_message_url]" class="full_width" type="text" name="dp_options[header_message_url]" value="<?php echo esc_attr( $options['header_message_url'] ); ?>" />
            <input id="header_message_url_target" class="admin_link_option_target" name="dp_options[header_message_url_target]" type="checkbox" value="1" <?php checked( $options['header_message_url_target'], 1 ); ?>>
            <label for="header_message_url_target">&#xe92a;</label>
        </div>
       </li>
       <li class="cf color_picker_bottom"><span class="label"><?php echo tcd_admin_label('color'); ?></span><input type="text" name="dp_options[header_message_font_color]" value="<?php echo esc_attr( $options['header_message_font_color'] ); ?>" data-default-color="#ffffff" class="c-color-picker"></li>
       <li class="cf color_picker_bottom"><span class="label"><?php echo tcd_admin_label('bg_color'); ?></span><input type="text" name="dp_options[header_message_bg_color]" value="<?php echo esc_attr( $options['header_message_bg_color'] ); ?>" data-default-color="<?php echo esc_attr($main_color); ?>" class="c-color-picker"></li>
      </ul>

      <ul class="button_list cf">
        <li><input type="submit" class="button-ml ajax_button" value="<?php echo tcd_admin_label('save'); ?>" /></li>
        <li><a class="close_ac_content button-ml" href="#"><?php echo tcd_admin_label('close'); ?></a></li>
      </ul>

    </div><!-- END .theme_option_field_ac_content -->
  </div><!-- END .theme_option_field -->

</div><!-- END .tab-content -->

<?php
} // END add_header_tab_panel()


// バリデーション　■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■■
function add_header_theme_options_validate( $input ) {

  global $dp_default_options, $header_fix_options, $header_fix_options2, $content_width_options, $font_type_options, $logo_type_options, $megamenu_options;

  // 言語ボタン
  $input['show_lang_button'] = ! empty( $input['show_lang_button'] ) ? 1 : 0;
  $lang_button = array();
  if ( isset( $input['lang_button'] ) && is_array( $input['lang_button'] ) ) {
    foreach ( $input['lang_button'] as $key => $value ) {
      $lang_button[] = array(
        'active_button' => ! empty( $input['lang_button'][$key]['active_button'] ) ? 1 : 0,
        'name' => isset( $input['lang_button'][$key]['name'] ) ? wp_filter_nohtml_kses( $input['lang_button'][$key]['name'] ) : '',
        'url' => isset( $input['lang_button'][$key]['url'] ) ? wp_filter_nohtml_kses( $input['lang_button'][$key]['url'] ) : '',
      );
    }
  };
  $input['lang_button'] = $lang_button;

  // 検索フォーム
  $input['show_header_search'] = wp_filter_nohtml_kses( $input['show_header_search'] );

  // メガメニュー
  $new_megamenu_options = array(
    'dropdown' => array('value' => 'dropdown'),
    'use_megamenu_a' => array('value' => 'use_megamenu_a'),
    'use_megamenu_b' => array('value' => 'use_megamenu_b'),
    'use_megamenu_c' => array('value' => 'use_megamenu_c'),
  );
  foreach ( array_keys( $input['megamenu_new'] ) as $index ) {
    if ( ! array_key_exists( $input['megamenu_new'][$index], $new_megamenu_options ) ) {
      $input['megamenu_new'][$index] = null;
    }
  }

  $input['megamenu_a_post_type'] = wp_filter_nohtml_kses( $input['megamenu_a_post_type'] );
  $input['megamenu_a_post_order'] = wp_filter_nohtml_kses( $input['megamenu_a_post_order'] );
  $input['megamenu_a_post_num'] = wp_filter_nohtml_kses( $input['megamenu_a_post_num'] );


  // メッセージ
  $input['show_header_message'] = wp_filter_nohtml_kses( $input['show_header_message'] );
  $input['header_message'] = wp_filter_nohtml_kses( $input['header_message'] );
  $input['header_message_url'] = wp_filter_nohtml_kses( $input['header_message_url'] );
  $input['header_message_url_target'] = ! empty( $input['header_message_url_target'] ) ? 1 : 0;
  $input['header_message_font_color'] = sanitize_hex_color( $input['header_message_font_color'] );
  $input['header_message_bg_color'] = sanitize_hex_color( $input['header_message_bg_color'] );

  // アイコンボタンの設定
  $input['show_side_icon_button'] = ! empty( $input['show_side_icon_button'] ) ? 1 : 0;
  $side_icon_button = array();
  if ( isset( $input['side_icon_button'] ) && is_array( $input['side_icon_button'] ) ) {
    foreach ( $input['side_icon_button'] as $key => $value ) {
      $side_icon_button[] = array(
        'title' => isset( $input['side_icon_button'][$key]['title'] ) ? wp_kses_post( $input['side_icon_button'][$key]['title'] ) : '',
        'url' => isset( $input['side_icon_button'][$key]['url'] ) ? wp_filter_nohtml_kses( $input['side_icon_button'][$key]['url'] ) : '',
        'target' => isset( $input['side_icon_button'][$key]['target'] ) ? wp_filter_nohtml_kses( $input['side_icon_button'][$key]['target'] ) : '',
        'icon' => isset( $input['side_icon_button'][$key]['icon'] ) ? wp_filter_nohtml_kses( $input['side_icon_button'][$key]['icon'] ) : '',
        'material_icon' => isset( $input['side_icon_button'][$key]['material_icon'] ) ? wp_filter_nohtml_kses( $input['side_icon_button'][$key]['material_icon'] ) : '',
      );
    }
  };
  $input['side_icon_button'] = $side_icon_button;

  return $input;

};


?>