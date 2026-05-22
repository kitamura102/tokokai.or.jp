<?php
namespace TCD\Helper\UI;



/**
 * HTML属性配列から属性文字列を生成する
 *
 * @param array $attributes 属性配列(key => value)
 * @return string HTML属性文字列
 */
function build_html_attributes( array $attributes ): string {
  $attr_string = '';
  foreach ( $attributes as $attr => $value ) {
    if ( true === $value ) {
      // boolean属性対応
      $attr_string .= sprintf( ' %s', esc_attr( $attr ) );
    } elseif ( null !== $value && '' !== $value ) {
      $attr_string .= sprintf( ' %s="%s"', esc_attr( $attr ), esc_attr( $value ) );
    }
  }
  return $attr_string;
}

/**
 * アコーディオン開始タグ
 *
 * @param string $label ラベル
 * @param array $classes 追加クラス
 * @return void
 */
function start( string $label = '', array $classes = [] ) {
  $class_str = '';
  if ( ! empty( $classes ) ) {
    $class_str = implode( ' ', array_map( 'esc_attr', $classes ) );
  }

  $output = "<div class=\"theme_option_field theme_option_field_ac {$class_str}\">";
  $output .= '<h3 class="theme_option_headline">';
  $output .= esc_html( $label );
  $output .= '<span class="theme_option_headline-toggle"></span>';
  $output .= '</h3>';
  $output .= '<div class="theme_option_field_ac_content">';

  echo $output;
}


/**
 * アコーディオン終了タグ
 *
 * @return void
 */
function end() {
  echo '</div>';
  echo '</div>';
}

/**
 * 汎用リピーター
 *
 * NOTE: シンプルな繰り返しフィールドを作成可能
 *
 * @param string $name name属性
 * @param mixed $values 配列のリピーターデータ
 * @param mixed $inputs フィールドを定義したコールバック関数
 * @return string リピーター HTML
 */
function repeater( $name, $values = array(), $inputs = '', $args = array() ) {

  $default = array(
      'add'           => true,
      'add_label'     => __( 'Add item','tcd-serum' ),
      'sort'          => true,
      'show'          => true,
      'delete'        => true,
      'wrap'          => true,
      'default_title' => null,
      'index'         => '__INDEX__'
  );

  $args = wp_parse_args( $args, $default );

  $r_wrap = '';
  if ( ! empty( $values ) ) {
      foreach ( $values as $key => $value ) {
          if ( empty( $value['title'] ) ) {
              $label = $key === 'logo' ? __( '(Logo)', 'tcd-serum' ) : $key;
              $value['title'] = sprintf( __( 'Font %s', 'tcd-serum' ), $label );
          }

          $r_wrap .= r_item( $name, $key, $value, $inputs, $args );
      }
  }

  // ラッパー
  return r_wrap(
      $name,
      $r_wrap,
      $args['add'] ? r_add( r_item( $name, $args['index'], array(), $inputs, $args ), $args ) : ''
  );
}
  
/**
 * 汎用サブリピーター
 *
 * NOTE: repeaterの中で使用
 *
 * @param string $name name属性
 * @param mixed $values 配列のリピーターデータ
 * @param mixed $inputs フィールドを定義したコールバック関数
 * @return string リピーター HTML
 */
function subrepeater( $name, $values = array(), $inputs = '', $args = array() ) {
  return repeater(
    $name,
    $values,
    $inputs,
    array(
      'index' => '__SUB_INDEX__'
    )
  );
}
  
  /**
   * リピータをパーツ化する
   *
   * NOTE: 要件に応じてパーツで繰り返しフィールドを作る
   */
  function r_wrap( $name, $output, $after = '' ) {
    return implode( "\n", [
      '<div class="tcd-ui-repeater js-tcd-ui-repeater">',
      // アイテム0の時の対策
      "<input type=\"hidden\" name=\"{$name}\" value=\"\"/>",
      // メインコンテンツ
      '<div class="tcd-ui-repeater__list js-tcd-ui-repeater-list">',
      is_callable( $output ) ? call_user_func( $output, $name ) : $output,
      '</div>',
      // 追加ボタンなど
      $after,
      '</div>'
    ] );
  }
  
  /**
   * 並び替え用ハンドル
   */
  function r_handle(){
    return '<span class="tcd-ui-repeater__item-handle js-tcd-ui-repeater-handle"></span>';
  }
  
  /**
   * トグルスイッチ
   */
  function r_show( $name, $value, $new_value ){
    return implode( "\n", [
      '<label class="tcd-ui-repeater__item-show">',
      sprintf(
        '<input type="checkbox" class="screen-reader-text" name="%s" value="%s" %s />',
        esc_attr( $name ),
        esc_attr( $value ),
        checked( $value, $new_value, false )
      ),
      '</label>'
    ] );
    return $output;
  }
  
  /**
   * タイトル
   */
  function r_title( $title = '', $default = null ){
    return sprintf(
      '<span class="tcd-ui-repeater__item-title js-tcd-ui-repeater-title" data-default="%s">%s</span>',
      $default ?? __( 'New item' ),
      $title
    );
  }
  
  /**
   * 削除
   */
  function r_delete(){
    return sprintf(
      '<span class="tcd-ui-repeater__item-delete js-tcd-ui-repeater-remove" data-alert-msg="%s"></span>',
      __( 'Delete?' )
    );
  }
  
  /**
   * 追加
   */
  function r_add( $clone, $args ){
  
    // template要素でDOM上に設置
    $clone_template = '<template class="js-tcd-ui-repeater-clone">' . $clone . '</template>';
  
    return sprintf(
      '%s<button type="button" class="tcd-ui-repeater__add js-tcd-ui-repeater-add">%s</button>',
      $clone_template,
      $args['add_label']
    );
  }
  
  /**
   * 基本アイテム
   */
  function r_item( $name, $key, $value, $callback, $args ){
  
    // デフォルトタイトルに番号を付ける
    if( $default_title = $args['default_title'] ){
      $default_title = str_replace( '__KEY__', $key, $default_title );
    }
  
    return implode( "\n", [
      '<div class="js-tcd-ui-repeater-item" data-key="' . $key . '">',
      '<details class="tcd-ui-repeater__item">',
      '<summary class="tcd-ui-repeater__item-summery">',
      $args['sort'] ? r_handle() : '',
      $args['show'] ? r_show( "{$name}[{$key}][show]", 1, $value['show'] ?? 1 ) : '',
      r_title( $value['title'] ?? '', $default_title ),
      $args['delete'] ? r_delete() : '',
      '</summary>',
      '<div class="tcd-ui-repeater__item-content">',
      call_user_func( $callback, "{$name}[{$key}]", $key, $value ),
      '</div>',
      '</details>',
      '</div>'
    ] );
  }

/**
 * 注釈
 *
 * @param mixed $text 注釈文字列または配列
 * @param array $attributes HTML属性
 * @return string 注釈HTML
 */
function note( $text, $attributes = array() ) {
  if ( is_array( $text ) ) {
      $text = implode( '<br>', $text );
  }

  $default = array(
      'class' => 'tcd-ui-note'
  );

  $attributes = wp_parse_args( $attributes, $default );
  $attr_str   = build_html_attributes( $attributes );

  return sprintf(
      '<div%s>%s</div>',
      $attr_str,
      $text
  );
}

  /**
 * 番号付きタイトル
 *
 * @param string $text タイトルテキスト
 * @param int $number 番号
 * @param string $id 任意のID（デフォルトは空）
 * @return string タイトルHTML
 */
function title( string $text, int $number = 0, string $id = '' ): string {
    return sprintf(
      '<span class="tcd-ui-title" data-number="%s"%s>%s</span>',
      esc_attr( $number ),
      $id ? ' id="' . esc_attr( $id ) . '"' : '', // `id` が空でない場合のみ追加
      esc_html( $text )
    );
  }

/**
 * ラジオボタン
 *
 * @param string $name name属性
 * @param mixed $value 選択中の値
 * @param array $options 選択肢配列(key => label)
 * @param array $attributes HTML属性
 * @return string ラジオボタンHTML
 */
function radio( $name, $value, $options, $attributes = array() ) {
  $default = array(
      'class' => 'tcd-ui-radio js-tcd-ui-radio'
  );

  $attributes = wp_parse_args( $attributes, $default );
  $attr_str   = build_html_attributes( $attributes );

  $output = "<div{$attr_str}>";
  foreach ( $options as $key => $label ) {
      $output .= sprintf(
          '<label class="tcd-ui-radio__label"><input type="radio" name="%s" value="%s" %s />%s</label>',
          esc_attr( $name ),
          esc_attr( $key ),
          checked( $value, $key, false ),
          esc_html( $label )
      );
  }
  $output .= '</div>';

  return $output;
}

/**
 * セクション表示
 *
 * NOTE: 画像が左、右に設定のフィールドを作成する
 *
 * @param mixed $preview プレビュー表示用HTMLまたは配列
 * @param array $fields フィールド配列
 * @param bool $echo 出力する場合true、戻り値として返す場合false
 * @param array $attributes セクションコンテナに付与するHTML属性
 * @return string|void セクションHTMLまたは直接出力
 */
function section( $preview, $fields, $echo = true, $attributes = array() ) {
  $attributes = wp_parse_args(
      $attributes,
      array( 'class' => 'tcd-ui-section' )
  );

  $attr_str = build_html_attributes( $attributes );

  $output  = "<div{$attr_str}>\n";
  if ( $preview ) {
      $preview_str = is_array( $preview ) ? implode( "\n", $preview ) : $preview;
      $output     .= sprintf(
          '<div class="tcd-ui-section__preview">%s</div>',
          $preview_str
      );
  }

  if ( ! empty( $fields ) ) {
      $output .= "<div class=\"tcd-ui-section__settings\">\n";
      $output .= section_fields( $fields );
      $output .= "</div>\n";
  }

  $output .= "</div>\n";

  if ( $echo ) {
      echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  } else {
      return $output;
  }
}

/**
 * 基本テキスト
 *
 * @param string $name name属性
 * @param mixed $value 値
 * @param array $attributes HTML属性
 * @return string input要素HTML
 */
function text( $name, $value, $attributes = array() ) {
  $default = array(
      'type'  => 'text',
      'name'  => $name,
      'value' => $value,
      'class' => 'tcd-ui-text'
  );

  $attributes = wp_parse_args( $attributes, $default );
  $attr_str   = build_html_attributes( $attributes );

  return sprintf(
      '<input%s>',
      $attr_str
  );
}

/**
 * セレクトボックス
 *
 * @param string $name name属性
 * @param mixed $value 選択中の値
 * @param array $options 選択肢配列(key => label)
 * @param array $attributes HTML属性
 * @return string セレクトボックスHTML
 */
function select( $name, $value, $options, $attributes = array() ) {
  $default = array(
      'name'  => $name,
      'class' => 'tcd-ui-select js-tcd-ui-select'
  );

  $attributes = wp_parse_args( $attributes, $default );
  $attr_str   = build_html_attributes( $attributes );

  $outputs = '';
  foreach ( $options as $key => $label ) {
      $outputs .= sprintf(
          '<option value="%s" %s>%s</option>' . "\n",
          esc_attr( $key ),
          selected( $value, $key, false ),
          esc_html( $label )
      );
  }

  return sprintf(
      '<select%s>%s</select>',
      $attr_str,
      $outputs
  );
}

  /**
 * 変更内容の保存ボタン
 *
 * @param array $attributes HTML属性
 * @return string ボタン要素HTML
 */
function submit( array $attributes = [] ): string {
    $attributes = wp_parse_args(
      $attributes,
      [
        'type'  => 'submit',
        'class' => 'tcd-ui-submit ajax_button',
        'value' => __( 'Save Changes' )
      ]
    );
  
    $attr_str = build_html_attributes( $attributes );
  
    return sprintf(
      '<input%s/>',
      $attr_str
    );
  }

  /**
 * セクションタイトル
 *
 * @param string $text タイトルテキスト
 * @param bool $echo 結果をechoするかどうか（デフォルト：false）
 * @return string|void セクションタイトルHTML（$echoがtrueの場合はechoして戻り値なし）
 */
function heading( string $text, bool $echo = false ) {
    $html = sprintf(
      '<h4 class="tcd-ui-heading">%s</h4>',
      esc_html( $text )
    );
  
    if ( $echo ) {
      echo $html;
    } else {
      return $html;
    }
  }

/**
 * フォントセレクトボックス
 *
 * NOTE: フォント選択に利用、テーマオプションの設定を反映
 *
 * @param string $name name属性
 * @param mixed $value 選択中の値
 * @param array $attributes HTML属性
 * @return string セレクトボックスHTML
 */
function font_select( $name, $value, $attributes = array() ) {
  global $tcd_font_manager;

  // 互換性を維持するための対策
  if ( $value === 'type1' ) {
      $value = 1;
  } elseif ( $value === 'type2' ) {
      $value = 1;
  } elseif ( $value === 'type3' ) {
      $value = 2;
  }

  $options = array();
  foreach ( $tcd_font_manager->admin_font_labels as $key => $font_label ) {
      $options[$key] = sprintf(
          __( 'Font %s: %s','tcd-serum' ),
          $key,
          $font_label
      );
  }

  return select( $name, $value, $options, $attributes );
}
  

  

/**
 * セクション内のフィールドを再帰的にレンダリングするヘルパー関数
 *
 * @param array $fields フィールド配列
 * @return string レンダリング結果HTML
 */
function section_fields( $fields ) {
  $output = '';
  foreach ( $fields as $field ) {
    if ( is_callable( $field ) ) {
      $output .= call_user_func( $field );
    } elseif ( is_array( $field ) ) {
      $output .= '<div class="tcd-ui-section__settings-sub">' . section_fields( $field ) . '</div>' . "\n";
    } else {
      $output .= $field . "\n";
    }
  }
  return $output;
}
