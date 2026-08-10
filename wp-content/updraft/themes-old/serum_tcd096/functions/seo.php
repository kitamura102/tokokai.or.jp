<?php
function seo_meta_box() {
  $post_types = array ( 'post', 'page', 'news', 'treatment');
  add_meta_box(
    'show_seo_meta_box',//ID of meta box
    __('SEO', 'tcd-serum'),//label
    'show_seo_meta_box',//callback function
    $post_types,// post type
    'normal',// context
    'low'// priority
  );
}
add_action('add_meta_boxes', 'seo_meta_box', 998);

function show_seo_meta_box() {
  global $post;
  $options =  get_design_plus_option();

  $seo_title = get_post_meta($post->ID, 'tcd-w_meta_title', true);
  $seo_desc = get_post_meta($post->ID, 'tcd-w_meta_description', true);

  echo '<input type="hidden" name="seo_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

  //入力欄 ***************************************************************************************************************************************************************************************
?>
<div class="tcd_custom_fields">

 <div class="tcd_cf_content">
  <h3 class="tcd_cf_headline"><?php _e( 'Title tag', 'tcd-serum' ); ?></h3>
  <input type="text" name="tcd-w_meta_title" value="<?php if(!empty($seo_title)){ echo esc_attr($seo_title); }; ?>" style="width:100%" />
 </div><!-- END .content -->

 <div class="tcd_cf_content">
  <h3 class="tcd_cf_headline"><?php _e( 'Meta description tag', 'tcd-serum' ); ?></h3>
  <p><?php printf(__('Recommended number of characters is %s.', 'tcd-serum'), '180'); ?></p>
  <textarea class="large-text word_count" cols="50" rows="2" name="tcd-w_meta_description"><?php if(!empty($seo_desc)){ echo esc_textarea($seo_desc); }; ?></textarea>
  <p class="word_count_result"><?php _e( 'Current character is : <span>0</span>', 'tcd-serum' ); ?></p>
 </div><!-- END .content -->

</div><!-- END #tcd_custom_fields -->

<?php
}

function save_seo_meta_box( $post_id ) {

  // verify nonce
  if (!isset($_POST['seo_meta_box_nonce']) || !wp_verify_nonce($_POST['seo_meta_box_nonce'], basename(__FILE__))) {
    return $post_id;
  }

  // check autosave
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $post_id;
  }

  // save or delete
  $cf_keys = array('tcd-w_meta_title','tcd-w_meta_description');
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

}
add_action('save_post', 'save_seo_meta_box');


// titleタグの出力 --------------------------------------------------------------------------------
function change_title_tag($title) {

	global $blog_label, $post, $page, $paged;
  $options = get_design_plus_option();

	$site_title = get_bloginfo( 'name' );
	$custom_post_types = get_post_types( array( 'public' => true, '_builtin' => false ) );

	// 個別ページ
	if ( is_single() && get_post_meta( $post->ID, 'tcd-w_meta_title', true ) or is_page() && get_post_meta( $post->ID, 'tcd-w_meta_title', true ) ) {
		$title = get_post_meta( $post->ID, 'tcd-w_meta_title', true );

	// トップページ
	} elseif ( is_front_page() && $options['front_page_meta_title'] ) {
		$title = $options['front_page_meta_title'];

	// 投稿アーカイブ
	} elseif ( is_home() ) {
		if($options['blog_archive_meta_title']){
			$title = $options['blog_archive_meta_title'];
		}else{
			$title = $blog_label .  ' | ' . $site_title;
		}

	// カスタム投稿アーカイブ
	} elseif ( is_post_type_archive($custom_post_types) ) {

		foreach($custom_post_types as $post_type):
			if(is_post_type_archive($post_type)):

				if($options[$post_type.'_archive_meta_title']){
					$title = $options[$post_type.'_archive_meta_title'];
				}else{
					$title = $options[$post_type.'_label'] .  ' | ' . $site_title;
				}

			endif;
		endforeach;

	// 各タクソノミーアーカイブ
 	} elseif ( is_category() || is_tag() || is_tax() ) {

		 $query_obj = get_queried_object();
		 $term_id = $query_obj->term_id;
		 $term_name = $query_obj->name;
		 $term_meta = get_option( 'taxonomy_' . $term_id, array() );
		 if (!empty($term_meta['meta_title'])){
    	 $title = $term_meta['meta_title'];
		 }else{
			 $title = sprintf( __( 'Post list for %s', 'tcd-serum' ), $term_name );
		 }

	// 検索結果ページ
 	} elseif ( is_search() ) {
    if ( !empty( get_search_query() ) ) {
      $title = sprintf( __( 'Search result for %s', 'tcd-serum' ), get_search_query() );
    } else {
      $title = __( 'Search result', 'tcd-serum' );
    }

	// 日月アーカイブ
 	} elseif ( is_day() ) {
  	$title = sprintf( __( 'Archive for %s', 'tcd-serum' ), get_the_time( __( 'F jS, Y', 'tcd-serum' ) ) );
 	} elseif ( is_month() ) {
  	$title = sprintf( __( 'Archive for %s', 'tcd-serum' ), get_the_time( __( 'F, Y', 'tcd-serum') ) );
 	} elseif ( is_year() ) {
  	$title = sprintf( __( 'Archive for %s', 'tcd-serum' ), get_the_time( __( 'Y', 'tcd-serum') ) );

	// 著者ページ
 	} elseif ( is_author() ) {
 		global $wp_query;
  	$curauth = $wp_query->get_queried_object();
    $author_name = sprintf( __( '%s blog list', 'tcd-serum' ), $curauth->display_name );

  	$title = $author_name . ' | ' . $site_title;

  }

  return esc_attr($title);

}
add_filter( 'pre_get_document_title', 'change_title_tag' );


// meta descriptionタグの出力 --------------------------------------------------------------------------------
function get_seo_description() {

	global $post;
	$options = get_design_plus_option();
  $site_description = get_bloginfo( 'description', 'display' );
	$custom_post_types = get_post_types( array( 'public' => true, '_builtin' => false ) );

	// 個別ページ
 	// カスタムフィールドがある場合
 	if ( ( is_single() || is_page() ) && get_post_meta( $post->ID, 'tcd-w_meta_description', true ) ) {
	$trim_content = get_post_meta( $post->ID, 'tcd-w_meta_description', true );
  	$trim_content = str_replace( array( "\r\n", "\r", "\n" ), '', $trim_content );
  	$trim_content = htmlspecialchars( $trim_content );
  	return esc_html( $trim_content );

 	// 抜粋記事が登録されている場合は出力
 	} elseif ( ( is_single() || is_page() ) && has_excerpt() ) { 
  	$trim_content = get_the_excerpt();
  	$trim_content = str_replace( array( "\r\n", "\r", "\n" ), '', $trim_content );
  	return esc_html( $trim_content );

	// トップページの場合
	} elseif ( is_front_page() ) {
		if ( $options['front_page_meta_description'] ) {
			$trim_content = $options['front_page_meta_description'];
			$trim_content = str_replace( array( "\r\n", "\r", "\n" ), '', $trim_content );
			return esc_html( $trim_content );
		}else{
			return esc_html( $site_description );
		}

	// 投稿アーカイブ
 	} elseif ( is_home() ) {
		if ( $options['blog_archive_meta_description'] ) {
			$trim_content = $options['blog_archive_meta_description'];
			$trim_content = str_replace( array( "\r\n", "\r", "\n" ), '', $trim_content );
			return esc_html( $trim_content );
		}else{
			return esc_html( $site_description );
		}

 	// 上記が無い場合は本文から120文字を抜粋
 	} elseif ( is_single() || is_page() ) {
   	$base_content = $post->post_content;
   	$base_content = preg_replace( '!<style.*?>.*?</style.*?>!is', '', $base_content );
   	$base_content = preg_replace( '!<script.*?>.*?</script.*?>!is', '', $base_content );
   	$base_content = preg_replace( '/\[.+\]/','', $base_content );
   	$base_content = strip_tags( $base_content );
   	$trim_content = mb_substr( $base_content, 0, 120, 'utf-8' );
   	$trim_content = str_replace( ']]>', ']]&gt;', $trim_content );
   	$trim_content = str_replace( array( "\r\n", "\r", "\n" ), '', $trim_content );
   	$trim_content = htmlspecialchars( $trim_content );

   	if ( preg_match( '/。/', $trim_content ) ) { 
		// 指定した文字数内にある、最後の「。」以降をカットして表示
    	mb_regex_encoding( 'UTF-8' ); 
     	$trim_content = mb_ereg_replace( '。[^。]*$', '。', $trim_content );
  		return esc_html( $trim_content );
   	} else { 
			// 指定した文字数内に「。」が無い場合は、指定した文字数の文章を表示し、末尾に「…」を表示
			if ( $trim_content == '' ) {
				return esc_html( $site_description );
     	} else {
				return esc_html( $trim_content ) . '...';
			}
   	}

	// カスタム投稿アーカイブ
	} elseif ( is_post_type_archive($custom_post_types) ) {

		 foreach($custom_post_types as $post_type):
			if(is_post_type_archive($post_type)):

				if($options[$post_type.'_archive_meta_description']){
					$trim_content = $options[$post_type.'_archive_meta_description'];
					$trim_content = str_replace( array( "\r\n", "\r", "\n" ), '', $trim_content );
					return esc_html( $trim_content );
				}else{
					return esc_html( $site_description );
				}

			endif;
		endforeach;

	// タクソノミーアーカイブ
 	} elseif ( is_category() || is_tag() || is_tax() ) {

		$query_obj = get_queried_object();
		$term_id = $query_obj->term_id;
		$term_name = $query_obj->name;
		$term_description = $query_obj->description;
		$term_meta = get_option( 'taxonomy_' . $term_id, array() );

		if (!empty($term_meta['meta_description'])){
			$trim_content = $term_meta['meta_description'];
  		$trim_content = str_replace( array( "\r\n", "\r", "\n" ), '', $trim_content );
  		$trim_content = htmlspecialchars( $trim_content );
  		return esc_html( $trim_content );
    	
		}elseif($term_description){
			$term_description = strip_tags( $term_description );
			$term_description = str_replace( array( "\r\n", "\r", "\n" ), '', $term_description );
			return esc_html( $term_description );

		}else{
			return esc_html( $site_description );
		}
	
 	} elseif ( is_day() ) {
    return sprintf( __( 'Archive for %s', 'tcd-serum' ), get_the_time( __( 'F jS, Y', 'tcd-serum' ) ) );
 	} elseif ( is_month() ) {
    return sprintf( __( 'Archive for %s', 'tcd-serum' ), get_the_time( __( 'F, Y', 'tcd-serum' ) ) );
 	} elseif ( is_year() ) {
    return sprintf( __( 'Archive for %s', 'tcd-serum' ), get_the_time( __( 'Y', 'tcd-serum' ) ) );
 	} elseif ( is_author() ) {
    global $wp_query;
    $curauth = $wp_query->get_queried_object();
    return sprintf( __( 'Archive for %s', 'tcd-serum' ), esc_html( $curauth->display_name ) );
 	} elseif ( is_search() ) {
    return sprintf( __( 'Post list for %s', 'tcd-serum' ), get_search_query() );

 	} else {
    return esc_html( $site_description );
 	}

}