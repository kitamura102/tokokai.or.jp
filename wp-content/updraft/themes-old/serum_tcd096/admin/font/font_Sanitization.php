<?php

namespace TCD\Helper\Sanitization;


/**
 * ラジオボタンやセレクトボックスのサニタイズ
 *
 * NOTE: 許可リスト($allowed_values)に含まれていればその値を、含まれていなければデフォルト値を返す。
 *
 * @param mixed $input 入力値
 * @param array $allowed_values 許可する値の配列
 * @return mixed サニタイズ後の値
 */
function choice( $input, array $allowed_values ) {
    if ( in_array( $input, $allowed_values, true ) ) {
      return $input;
    }
    return array_shift( $allowed_values );
  }


/**
 * リピーターフィールド用サニタイズ
 *
 * NOTE: 入力が配列でなければ空配列を返す。
 * 配列の場合は$callbackを各要素に適用。
 *
 * @param mixed $input 入力値
 * @param callable $callback 要素毎に適用するサニタイズコールバック関数
 * @return array サニタイズ後の配列
 */
function repeater( $input, callable $callback ): array {
    if ( is_array( $input ) && ! empty( $input ) ) {
      return array_map(
        $callback,
        $input
      );
    }
    return [];
  }