<?php

// 外観>カスタマイズからウィジェットを取り除く
// add_filter( 'customize_loaded_components', ～ );はプラグインでは動くがテーマでは動作せず、対処法がないので@でエラーを消しています。----------------------------------------------
add_action( 'customize_register', function( $wp_customize ){
  @$wp_customize->remove_panel( 'widgets' );
 } );

 
// 外観>カスタマイズにダミーのカスタマイザーを作成。----------------------------------------------
  
function widgets_customize_register( $wp_customize ) {
  $num = 1;
  // セクション
  $wp_customize->add_section( 'widgets_customize_origin_scheme', array(
    'title'     =>  __('Widgets', 'tcd-serum'),
    'priority'  => 100,
    'description' => sprintf( __( "Please set it up on the <a href='%s'>widgets pages</a>", 'tcd-serum' ) ,admin_url('widgets.php')), //セクションの説明
  ));

  // セッティング
  $wp_customize->add_setting( 'widgets_customize_options[originText]', array(
    'default'   => '',
    'type'      => 'option',
    'transport' => 'postMessage',
  ));

  // コントロール
  $wp_customize->add_control( 'widgets_customize_options_origin_text', array(
    'settings'  => 'widgets_customize_options[originText]',
    'label'     => '',
    'section'   => 'widgets_customize_origin_scheme',
    'type'      => 'hidden',
  )
);
  
  }
  add_action( 'customize_register', 'widgets_customize_register' );