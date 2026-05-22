<?php
/**
 * 吹き出しクイックタグ用ショートコード
 */
function tcd_shortcode_speech_balloon( $atts, $content, $tag ) {
	global $dp_options;
	if ( ! $dp_options ) $dp_options = get_design_plus_option();

	$atts = shortcode_atts( array(
		'user_image_url' => '',
		'user_name' => ''
	), $atts );

	// user_image_urlが指定されていればメディアID取得・差し替えを試みる
	$user_image_url = $atts['user_image_url'];
	if ( $atts['user_image_url'] ) {
		$attachment_id = attachment_url_to_postid( $atts['user_image_url'] );
		if ( $attachment_id ) {
			$user_image = wp_get_attachment_image_src( $attachment_id, array( 300, 300, true ) );
			if ( $user_image ) {
				$atts['user_image_url'] = $user_image[0];
			}
		}
	}

	$html = '<div class="speach_balloon ' . esc_attr( $tag ) . '">'
		  . '<div class="speach_balloon_user">';

	if ( $atts['user_image_url'] ) {
		$html .= '<img class="speach_balloon_user_image" src="' . esc_attr( $atts['user_image_url'] ) . '" alt="' . esc_attr( $atts['user_image_url'] ) . '">';
	}

	$html .= '<div class="speach_balloon_user_name">' . esc_html( $atts['user_name'] ) . '</div>'
		  . '</div>'
		  . '<div class="speach_balloon_text">' .  wpautop( $content )   . '</div>'
		  .  '</div>';

	return $html;
}
// add_shortcode( 'speech_balloon_left1', 'tcd_shortcode_speech_balloon' );
// add_shortcode( 'speech_balloon_left2', 'tcd_shortcode_speech_balloon' );
// add_shortcode( 'speech_balloon_right1', 'tcd_shortcode_speech_balloon' );
// add_shortcode( 'speech_balloon_right2', 'tcd_shortcode_speech_balloon' );


function speech_balloon_template( $content, $i, $type = 'left' ) {

  $options = get_design_plus_option();

  $image = get_template_directory_uri().'/img/common/no_avatar.png';
  if($options['qt_speech_balloon'.$i.'_user_image']){
    $image = wp_get_attachment_image_src( $options['qt_speech_balloon'.$i.'_user_image'], array( 300, 300, true ) );
    if(!empty($image)) $image = $image[0];
  }
  $name = $options['qt_speech_balloon'.$i.'_user_name'];

  $html = '<div class="speech_balloon '.$type.'">'."\n";
  $html .= '<div class="speech_balloon_user">'."\n";
	$html .= '<img class="speech_balloon_user_image" src="'.esc_attr($image).'" alt="">'."\n";
  if($name) $html .= '<div class="speech_balloon_user_name">' . esc_html($name) . '</div>'."\n";
  $html .= '</div>'."\n";
  $html .= '<div class="speech_balloon_text speech_balloon'.$i.'">'."\n";
  $html .= '<span class="before"></span>';
  $html .= '<div class="speech_balloon_text_inner">'.wpautop( $content ).'</div>'."\n";
  $html .= '<span class="after"></span>';
  $html .= '</div>'."\n";
  $html .= '</div>'."\n";

  return $html;

}


function tcd_speech_balloon1( $attr, $content ) {
  return speech_balloon_template($content, 1, 'left');
}
add_shortcode( 'speech_balloon_left1', 'tcd_speech_balloon1' );

function tcd_speech_balloon2( $attr, $content ) {
  return speech_balloon_template($content, 2, 'left');
}
add_shortcode( 'speech_balloon_left2', 'tcd_speech_balloon2' );

function tcd_speech_balloon3( $attr, $content ) {
  return speech_balloon_template($content, 3, 'right');
}
add_shortcode( 'speech_balloon_right1', 'tcd_speech_balloon3' );

function tcd_speech_balloon4( $attr, $content ) {
  return speech_balloon_template($content, 4, 'right');
}
add_shortcode( 'speech_balloon_right2', 'tcd_speech_balloon4' );





/**
 * 吹き出しクイックタグ用ショートコード（フリー）
 */
function tcd_shortcode_speech_balloon_free( $atts, $content ) {

	$atts = shortcode_atts( array(
		'image' => '',
		'name' => '',
    'type' => 'left',
    'color' => '',
    'bg_color' => '',
    'border_color' => ''
	), $atts );

	// user_image_urlが指定されていればメディアID取得・差し替えを試みる
  $image = get_template_directory_uri().'/img/common/no_avatar.png';
	$user_image_url = $atts['image'];
	if ( $atts['image'] ) {
		$attachment_id = attachment_url_to_postid( $atts['image'] );
		if ( $attachment_id ) {
			$user_image = wp_get_attachment_image_src( $attachment_id, array( 300, 300, true ) );
			if ( $user_image ) {
				$image = esc_attr($user_image[0]);
			}
		}
	}

  $name = esc_html($atts['name']);
  $type = esc_attr($atts['type']);
  $color = ($atts['color']) ? 'color:'.esc_attr($atts['color']).';' : '';
  $bg_color = ($atts['bg_color']) ? 'background-color:'.esc_attr($atts['bg_color']).';' : '';
  $border_color = ($atts['border_color']) ? 'border-color:'.esc_attr($atts['border_color']).';' : '';

  $border_right_color = ($atts['bg_color']) ? 'border-right-color:'.esc_attr($atts['bg_color']).';' : '';
  $border_left_color = ($atts['border_color']) ? 'border-left-color:'.esc_attr($atts['border_color']).';' : '';

	$html = '<div class="speech_balloon '.$type.'">'."\n";
  $html .= '<div class="speech_balloon_user">'."\n";
	$html .= '<img class="speech_balloon_user_image" src="'.$image.'" alt="">'."\n";
  if($name) $html .= '<div class="speech_balloon_user_name">' . $name . '</div>'."\n";
  $html .= '</div>'."\n";
  $html .= '<div class="speech_balloon_text">' ."\n";
  $html .= '<span class="before" style="'.$border_left_color.'"></span>';
  $html .= '<div class="speech_balloon_text_inner" style="'.$color.$bg_color.$border_color.'">' .  wpautop( $content )   . '</div>'."\n";
  $html .= '<span class="after" style="'.$border_right_color.'"></span>';
  $html .= '</div>'."\n";
  $html .= '</div>'."\n";

	return $html;
}
add_shortcode( 'speech_balloon_free', 'tcd_shortcode_speech_balloon_free' );




/**
 * Google Map用ショートコード
 */
function tcd_google_map( $atts) {
  global $options;
  if ( ! $options ) $options = get_design_plus_option();

  $atts = shortcode_atts( array(
    'address' => '',
  ), $atts );

  $html = '';

  if ( $atts['address'] ) {

    $use_custom_overlay = 'type1' !== $options['qt_gmap_marker_type'] ? 1 : 0;
    $custom_marker_type = $options['qt_gmap_marker_type'] ? $options['qt_gmap_marker_type'] : 'type2';

    $marker_img = $options['qt_gmap_marker_img'] ? wp_get_attachment_url( $options['qt_gmap_marker_img'] ) : get_template_directory_uri().'/img/common/gmap_no_image.png';
    if(($custom_marker_type == 'type3') && !empty($marker_img)) {
      $marker_text = '';
    } else {
      $marker_text = $options['qt_gmap_marker_text'];
    }
    if($options['qt_access_saturation'] == 'default'){
      $access_saturation = 0;
    }else{
      $access_saturation = -100;
    }
    $rand = rand();

    $html .= "<div class='qt_google_map'>\n";
    $html .= " <div class='qt_googlemap clearfix'>\n";
    $html .= "  <div id='qt_google_map" . $rand . "' class='qt_googlemap_embed'></div>\n";
    $html .= " </div>\n";
    $html .= " <script>\n";
    $html .= " jQuery(window).on('load', function() {\n";
    $html .= "  initMap('qt_google_map" . $rand . "', '" . esc_js( $atts['address'] ) . "', " . esc_js( $access_saturation ) . ", " . esc_js( $use_custom_overlay ) . ", '" . esc_js( $marker_img ) . "', '" . esc_js( $marker_text ) . "');\n";
    $html .= " });\n";
    $html .= " </script>\n";
    $html .= "</div>\n";

    if ( ! wp_script_is( 'qt_google_map_api', 'enqueued' ) ) {
      wp_enqueue_script( 'qt_google_map_api', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr( $options['qt_gmap_api_key'] ), array(), version_num(), true );
      wp_enqueue_script( 'qt_google_map', get_template_directory_uri() . '/js/googlemap.js', array(), version_num(), true );
    }
  }

	return $html;
}
add_shortcode( 'qt_google_map', 'tcd_google_map' );




/**
 * FAQ用ショートコード
 */
function tcd_faq( $atts, $post_id = 0  ) {
  global $post;

  // ショートコードの属性を取得
  $atts = shortcode_atts(
    array(
      'post_id' => $post->ID, // デフォルトは0
    ),
    $atts
  );

  // ショートコードの post_id が指定されていれば、それを使用
  if ( $atts['post_id'] ) {
    $post_id = $atts['post_id'];
  } elseif ( $post_id === 0 ) { // post_id が0の場合、現在の投稿IDを使用
    $post_id = $post->ID;
  }

  $faq_list = get_post_meta($post_id, 'faq_list', true);

  $html = '';

  if ( $faq_list ) {
    $html .= "<div class='faq_list'>\n";
    foreach ( $faq_list as $key => $value ) :
      $question = $value['question'];
      $answer = $value['answer'];
      if ( $question && $answer) {
        $html .= "<div class='item'>\n";
        $html .= '<div class="title no_editor_style"><span>' . esc_html($question) . "</span></div>\n";
        $html .= '<div class="desc_area"><p class="desc no_editor_style"><span>' . wp_kses_post(nl2br($answer)) . "</span></p></div>\n";
        $html .= "</div>\n";
      }
    endforeach;
    $html .= "</div>\n";
  }

	return $html;
}
add_shortcode( 'sc_faq', 'tcd_faq' );


/**
 * 料金用ショートコード
 */
function tcd_price( $atts, $post_id = 0 ) {
  global $post;
  
  // ショートコードの属性を取得
  $atts = shortcode_atts(
    array(
      'post_id' => $post->ID, // デフォルトは0
    ),
    $atts
  );
    
    
  // ショートコードの post_id が指定されていれば、それを使用
  if ( $atts['post_id'] ) {
    $post_id = $atts['post_id'];
  } elseif ( $post_id === 0 ) { // post_id が0の場合、現在の投稿IDを使用
    $post_id = $post->ID;
  }

  $price_list = get_post_meta($post_id, 'price_list', true);

  $html = '';

  if ( $price_list ) {
    $html .= "<div class='price_list'>\n";
    foreach ( $price_list as $key => $value ) :
      $title = $value['title'];
      $price = $value['price'];
      if ( $title && $price) {
        $html .= "<div class='item'>\n";
        $html .= '<p class="title">' . esc_html($title) . "</p>\n";
        $html .= '<p class="price">' . esc_html($price) . "</p>\n";
        $html .= "</div>\n";
      }
    endforeach;
    $html .= "</div>\n";
  }

	return $html;
}
add_shortcode( 'sc_price', 'tcd_price' );


/**
 * スケジュール
 */
function tcd_schedule( $atts) {
  $options = get_design_plus_option();

  $html = '';

  $html .= "<div class='schedule_content'>\n";

  if($options['schedule_info']){
    $html .= "<div class='info'>\n";
    $html .= apply_filters('the_content', $options['schedule_info'] );
    $html .= "</div>\n";
  }

  if($options['schedule']){

    $html .= "<div class='schedule'>\n";
    $html .= "<table>\n";

    $i = 1;
    foreach ( $options['schedule'] as $key => $value ) :

      $html .= "<tr class='row" . $i . "'>\n";
      $html .= "<td class='col1'><div class='content'>" . wp_kses_post(nl2br($value['header'])) . "</div></td>\n";
      $html .= "<td class='col2'><div class='content'>" . wp_kses_post(nl2br($value['col1'])) . "</div></td>\n";
      $html .= "<td class='col3'><div class='content'>" . wp_kses_post(nl2br($value['col2'])) . "</div></td>\n";
      $html .= "<td class='col4'><div class='content'>" . wp_kses_post(nl2br($value['col3'])) . "</div></td>\n";
      $html .= "<td class='col5'><div class='content'>" . wp_kses_post(nl2br($value['col4'])) . "</div></td>\n";
      $html .= "<td class='col6'><div class='content'>" . wp_kses_post(nl2br($value['col5'])) . "</div></td>\n";
      $html .= "<td class='col7'><div class='content'>" . wp_kses_post(nl2br($value['col6'])) . "</div></td>\n";
      $html .= "<td class='col8'><div class='content'>" . wp_kses_post(nl2br($value['col7'])) . "</div></td>\n";
      $html .= "</tr>\n";

    $i++;
    endforeach;

    $html .= "</table>\n";
    $html .= "</div>\n";

  };

  $html .= "</div>";

	return $html;
}
add_shortcode( 'sc_basic_info', 'tcd_schedule' );


/**
 * スタッフ一覧
 */
function tcd_staff_list( $atts) {
  global $post;
  $author_list_order = get_post_meta($post->ID, 'staff_list_order', true);
  if (empty($author_list_order) || !is_array($author_list_order)) {
    $author_list_order = array();
  }

  $users = get_users(array(
    'fields' => array('ID'),
    'role__not_in' => array('subscriber','contributor'),
    'orderby' => 'ID',
    'order' => 'ASC'
  ));

  if ($users) {
    $user_ids = array();
    foreach ($users as $user) {
      $user_ids[] = $user->ID;
    }
    if ($author_list_order) {
      foreach ($author_list_order as $key => $author_id) {
        if (!in_array($author_id, $user_ids)) {
          unset($author_list_order[$key]);
        }
      }
    }
    foreach ($user_ids as $user_id) {
      if (!in_array($user_id, $author_list_order)) {
        $author_list_order[] = $user_id;
      }
    }
    unset($user_ids, $user_id);
  } else {
    $author_list_order = array();
  }
  unset($users);

  $html = '';

  if ($author_list_order) {

    $html .= "<div id='staff_list'>\n";
    $html .= "<div class='two_column_content no_bg_color'>\n";
    $html .= "<div class='post_list'>\n";

    foreach((array) $author_list_order as $author_id) :
      $user_data = get_userdata($author_id);
      $name = $user_data->display_name;
      $author_url = get_author_posts_url($author_id);
      $position = $user_data->user_position;
      $image_id = $user_data->staff_page_image;
      if($image_id) {
        $image = wp_get_attachment_image_src( $image_id, 'full' );
      }
      $desc = $user_data->description;
      $staff_page_displayment = $user_data->staff_page_displayment;
      if($staff_page_displayment == 'type2') {
        $html .= "<div class='item'>\n";
        if($image) {
          $html .= "<div class='image_wrap'>\n";
          $html .= "<div class='doctor_meta'>\n";
          if($position){
            $html .= "<div class='item pos'>" . esc_html($position) . "</div>\n";
          }
          if($name){
            $html .= "<div class='item name'><a href='" . esc_url($author_url) . "'>" . esc_html($name) . "</a></div>\n";
          }
          $html .= "</div>\n";
          $html .= "<img class='image' loading='lazy' src='" . esc_attr($image[0]) . "' alt='' title='' width='" . esc_attr($image[1]) . "' height='" . esc_attr($image[2]) . "'>\n";
          $html .= "</div>\n";
        }
        if($desc){
          $html .= "<p class='desc'>" . wp_kses_post(nl2br($desc)) . "</p>\n";
        }
        $html .= "</div>\n";
      }
    endforeach;

    $html .= "</div>\n";
    $html .= "</div>\n";
    $html .= "</div>";

  };

	return $html;
}
add_shortcode( 'sc_staff_list', 'tcd_staff_list' );


/**
 * 代表者の情報
 */
function tcd_representative_info( $atts) {

  global $post;

  $users = get_users(array(
    'fields' => array('ID'),
    'role__not_in' => array('subscriber','contributor'),
    'meta_key' => 'staff_page_displayment',
    'meta_value' => 'type1',
    'orderby' => 'ID',
    'order' => 'ASC'
  ));

  $html = '';

  if(!empty($users)){

    $user_count = count($users);

    if($user_count > 1){
      $user_id = get_post_meta($post->ID, 'representative_user', true) ?  get_post_meta($post->ID, 'representative_user', true) : 'hide';
    } else {
      foreach ($users as $user):
        $user_id = $user->ID;
      endforeach;
    }
    if($user_id != 'hide'){
      $user_data = get_userdata($user_id);
      $name = $user_data->display_name;
      $author_url = get_author_posts_url($user_id);
      $position = $user_data->user_position;
      $image_id = $user_data->staff_page_image;
      if($image_id) {
        $image = wp_get_attachment_image_src( $image_id, 'full' );
      }

      if(!empty($image)) {
        $html .= "<div id='staff_info' class='design_content'>\n";
        $html .= "<div class='image_wrap'>\n";
        $html .= "<div class='doctor_meta'>\n";
        if($position){
          $html .= "<div class='item pos'>" . esc_html($position) . "</div>\n";
        }
        if($name){
          $html .= "<div class='item name'><a href='" . esc_url($author_url) . "'>" . esc_html($name) . "</a></div>\n";
        }
        $html .= "</div>\n";
        $html .= "<img class='image' loading='lazy' src='" . esc_attr($image[0]) . "' alt='' title='' width='" . esc_attr($image[1]) . "' height='" . esc_attr($image[2]) . "'>\n";
        $html .= "</div>\n";
        $html .= "</div>";
      };
    };

  };

	return $html;
}
add_shortcode( 'sc_representative_info', 'tcd_representative_info' );


/**
 * タブコンテンツ
 */
function qt_tab_content($atts) {

  $atts = shortcode_atts( array(
    'tab1' => '',
    'img1' => '',
    'tab2' => '',
    'img2' => '',
  ), $atts );


  $html = '';

  if ( $atts['tab1'] || $atts['tab2']) {

  $html .= "<div class='qt_tab_content_wrap'>\n";

  $html .= "<div class='qt_tab_content_header'>\n";

  if ( $atts['tab1'] ) {
    $html .= '<div class="item active" data-tab-target=".qt_tab_content1">' . esc_html($atts['tab1']) . "</div>\n";
  }
  if ( $atts['tab2'] ) {
    $html .= '<div class="item" data-tab-target=".qt_tab_content2">' . esc_html($atts['tab2']) . "</div>\n";
  }

  $html .= "</div>\n";

  $html .= "<div class='qt_tab_content_main'>\n";

  if ( $atts['img1'] ) {
    $html .= '<div class="qt_tab_content active qt_tab_content1">' . "\n";
    if ( $atts['img1'] ) {
      $html .= '<img src="' . esc_url($atts['img1']) . '" title="" alt="">' . "\n";
      $image_id = attachment_url_to_postid($atts['img1']);
      $image_caption = $image_id ?  get_post($image_id)->post_excerpt : '';
      if ($image_caption) {
        $html .= '<p class="desc">' . wp_kses_post($image_caption) . "</p>\n";
      }
    }
    $html .= "</div>\n";
  }

  if ( $atts['img2'] ) {
    $html .= '<div class="qt_tab_content qt_tab_content2">' . "\n";
    if ( $atts['img2'] ) {
      $html .= '<img src="' . esc_url($atts['img2']) . '" title="" alt="">' . "\n";
      $image_id = attachment_url_to_postid($atts['img2']);
      $image_caption = $image_id ?  get_post($image_id)->post_excerpt : '';
      if ($image_caption) {
        $html .= '<p class="desc">' . wp_kses_post($image_caption) . "</p>\n";
      }
    }
    $html .= "</div>\n";
  }

  $html .= "</div>\n";

  $html .= "</div>\n";

  };

	return $html;
}
add_shortcode( 'tcd_tab', 'qt_tab_content' );


?>