<?php

// フッターバー フロントエンドフック登録
function footer_bar_wp() {

	global $dp_options, $post;
	if ( ! $dp_options ) $dp_options = get_design_plus_option();

	// フッターバーを表示するかどうか
	$show_footer_bar = ( $dp_options['footer_bar_type'] == 'type1' ) ? false : true;

	if( $show_footer_bar ){

		if( !is_mobile() )
			$show_footer_bar = false;

		if( is_404() )
			$show_footer_bar = false;

	}

	// フック登録
	if ( $show_footer_bar ) {
		add_action( 'wp_enqueue_scripts', 'footer_bar_enqueue_script' );
		add_filter( 'body_class', 'footer_bar_body_class' );
		add_action( 'tcd_footer_after', 'render_footer_bar' );
	}

}
add_action( 'wp', 'footer_bar_wp' );


// フッターバー css & js
function footer_bar_enqueue_script() {
  wp_enqueue_style( 'footer-bar-css', get_template_directory_uri()  . '/css/footer-bar.css');
//	wp_enqueue_script( 'footer-bar-js', get_template_directory_uri() . '/js/footer-bar.js', array( 'jquery' ), version_num(), false );
}

// フッターバー body class
function footer_bar_body_class( $classes ) {
  $classes[] = 'show_footer_bar';
	return $classes;
}

// フッターバー 出力
function render_footer_bar() {


	// テーマオプションを取得
	$options = get_design_plus_option();

	// ページタイトルを取得
	$title = wp_title( '|', false, 'right' );

	// ページ URL を取得
	$url = ( empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	$footer_bar_btn_classes = array();
	$footer_bar_btn_url = '';
	$footer_bar_type = $options['footer_bar_type'];

?>
<div id="js-footer-bar" class="p-footer-bar">
	<div class="p-footer-bar__inner">
		<ul class="p-footer-bar__list p-footer-bar--<?php echo esc_attr($footer_bar_type); ?>">
<?php
	// ボタンを表示

	$is_share = false;
	foreach ( $options['footer_bar_btns'] as $key => $value ) :

		switch ( $value['type'] ) {

			// ボタンタイプ：デフォルト
			case 'type1' :
				$footer_bar_btn_class = '';
				$footer_bar_btn_url = $value['url'];
				break;

			// ボタンタイプ：シェア
			case 'type2' :
				$footer_bar_btn_class = 'js-footer-bar-share';
				$footer_bar_btn_url = '#';
				$is_share = true;
				break;
			
			// ボタンタイプ：電話番号
			case 'type3' :
				$footer_bar_btn_class = '';
				$footer_bar_btn_url = 'tel:' . $value['number'];
				break;

		}

		$button_color = ( $footer_bar_type === 'type4' ) ? 'style="background-color:'.esc_attr($value['color']).';"' : '' ;


?>
			<li class="p-footer-bar__item <?php echo $footer_bar_btn_class; ?>">
				<a class="p-footer-bar__item-link no_auto_scroll" href="<?php echo esc_url( $footer_bar_btn_url ); ?>" <?php echo $button_color; ?>>
					<?php if( $footer_bar_type !== 'type4' ) { ?>
          <?php if($value['icon'] == 'material_icon'){ ?>
					<span class="p-footer-bar__icon p-footer-bar__icon--<?php echo esc_attr( $value['icon'] ); ?>"><?php if($value['icon'] == 'material_icon' && $value['material_icon']){ ?><span class="google_icon">&#x<?php echo esc_attr($value['material_icon']); ?>;</span><?php }; ?></span>
          <?php } else { ?>
					<span class="p-footer-bar__icon p-footer-bar__icon--<?php echo esc_attr( $value['icon'] ); ?>"></span>
					<?php } ?>
					<?php } ?>
					<span class="p-footer-bar__item-label"><?php echo esc_html( $value['label'] ); ?></span>
				</a>
			</li>
<?php
	endforeach;
?>
		</ul>
	</div>

	<?php if( $is_share ){ ?>
	<div id="js-footer-bar-modal" class="modal-overlay p-footer-bar__modal">
		<ul class="p-footer-bar__modal-share">
			<li class="p-footer-bar__modal-share-item" style="border-radius:2px; overflow:hidden;">
				<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($url); ?>&text=<?php echo urlencode($title); ?>" onClick="window.open(this.href, 'tweetwindow', 'width=650, height=470, personalbar=0, toolbar=0, scrollbars=1, sizable=1'); return false;"><img src="<?php echo get_template_directory_uri(); ?>/img/common/twitter_x.png" alt=""></a>
			</li>
			<li class="p-footer-bar__modal-share-item">
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( $url ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/common/facebook.png?2.0" alt=""></a>
			</li>
			<li class="p-footer-bar__modal-share-item">
				<a href="https://line.me/R/msg/text/?<?php echo rawurlencode( $title ); ?><?php echo rawurlencode( $url ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/common/line.png?2.0" alt=""></a>
			</li>
			<li class="p-footer-bar__modal-share-item">
				<a href="https://b.hatena.ne.jp/entry/" class="hatena-bookmark-button" data-hatena-bookmark-layout="simple" data-hatena-bookmark-width="100" data-hatena-bookmark-height="100" title="このエントリーをはてなブックマークに追加"><img src="<?php echo get_template_directory_uri(); ?>/img/common/hatena.png" alt="このエントリーをはてなブックマークに追加" width="100" height="100" style="border: none;" /></a><script type="text/javascript" src="https://b.st-hatena.com/js/bookmark_button.js" charset="utf-8" async="async"></script>
			</li>
		</ul>
		<div id="js-footer-bar-modal-overlay" class="p-footer-bar__modal-overlay"></div>
	</div>
	<?php } ?>

</div>
<?php

}
