<?php

/**
 * 出力
 *
 * @param mixed $str 表示データ
 * @param string $glue = ',' 区切り文字
 * @param string $last = NULL 末字
 */
function _e( $str, $glue=',', $last=NULL ) {
    if( is_array($str) || is_object($str) ) {
        echo implode( $glue, $str );
        echo $last;
    } else {
        echo $str.$last;
    }
}

/**
 * ライン出力
 *  / 行末に改行を付与する
 *
 * @param mixed $str 表示データ
 * @param string $glue = NULL 区切り文字
 */
function _el( $str, $glue=NULL ) {
    _le($str, $glue, "\n");
}

/**
 * 確認出力
 *  / データの内容が同じ場合に表示する
 *
 * @param string $str 確認元データ
 * @param mixed $check 確認先データ
 * @param string $echo 表示データ
 */
function _ec( $str, $check, $echo ) {
    if( is_array($check) || is_object($check) ) {
        echo in_array($str, $check) ? $echo : "";
    } else {
        echo (string)$str===(string)$check ? $echo : "";
    }
}

/**
 * 数値出力
 *  / number_formatのラッパー
 *
 * @param float $number 表示する数値データ
 * @param integer $decimals = 0 小数点以下の桁数
 */
function _en( $number, $decimals = 0 ) {
    echo number_format( $number, $decimals );
}

/**
 * アラート出力
 *
 * @param
 */
function _ea( $message, $type = 'success' ) {
    printf( '<div class="alert alert-%s">%s</div>', $type, $message );
}