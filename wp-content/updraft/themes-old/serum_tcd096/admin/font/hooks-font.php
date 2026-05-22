<?php

/* 使用するファイルの読み込み　*/
require_once ( dirname(__FILE__) . '/ui/ui.php' );
require_once ( dirname(__FILE__) . '/font_Sanitization.php' );
require_once ( dirname(__FILE__) . '/class-font-manager.php' );

/**
 * フォントの互換性を維持する
 *
 */
function tcd_covert_font_type( $value ){
    // 互換性を維持するための対策
    if( $value === 'type1' ){
      return 1;
    }elseif( $value === 'type2' ){
      return 1;
    }elseif( $value === 'type3' ){
      return 2;
    }
    return $value;
  }
?>