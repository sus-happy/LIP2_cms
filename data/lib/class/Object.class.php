<?php
/**
 * 説明
 * @see push_error
 */
define( 'DEBUG_MODE', TRUE );

/**
 * 共通関数
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 */

abstract class Object {

    const ERROR_FLAG_HIDE = 1;
    const ERROR_FLAG_FORCE_HIDE = 2;
    const ERROR_FLAG_VIEW = 3;

    protected static $error;

    /**
     * パラメータ合成
     *
     * @access public
     * @param array $_set デフォルトパラム
     * @param array $option 上書きパラム
     * @param boolean $del 上書きパラムの値がからの時に削除するか？
     */
    public static function param_merge( $_set, $option, $del = FALSE ) {
        if( $option ) {
            if(! is_array( $option ) && is_object( $option ) )
                $option = (array)$option;
            // 配列じゃなければ終了
            if(! is_array( $option ) ) {
                self::push_error( 'param_merge', 'param_merge parameter need Array' );
                return $_set;
            }
        } else {
            return $_set;
        }
        // 宣言されていても値が空なら削除する
        foreach( $option as $key => $val ) {
            if( is_array( $val ) || is_object( $val ) ) {
                if(! $val )
                    unset( $option[ $key ] );
            } else {
                if(! strlen( $val ) ) {
                    unset( $option[ $key ] );
                    // $delがTRUEの時はデフォルトパラムも削除する
                    if( $del )
                        unset( $_set[ $key ] );
                }
            }
        }

        return array_merge( $_set, $option );
    }

    /**
     * エラーメッセージの追加
     *
     * @access public
     * @param string $key エラーキー
     * @param string $message エラーメッセージ
     * @param bool   $flag = FALSE Exeptionエラーを即時に表示する
     * @param bool   $force = FALSE DEBUGモードだとしてもExeptionエラーを即時に表示しない
     */
    public static function push_error( $key, $message, $flag = self::ERROR_FLAG_HIDE ) {
        if( !empty( self::$error[$key] ) ) {
            if( is_array( self::$error[$key] ) ) {
                if( is_array( $message ) )
                    self::$error[$key] += $message;
                else
                    self::$error[$key][] = $message;
            } else {
            $tmp = self::$error[$key];
            unset( self::$error[$key] );
            if( is_array( $message ) ) {
                self::$error[$key][] = $tmp;
                self::$error[$key] += $message;
            } else
                self::$error[$key] = array( $tmp, $message );
            }
        } else
            self::$error[$key] = $message;

        if(
            ( DEBUG_MODE === TRUE || $flag === self::ERROR_FLAG_VIEW ) &&
            $flag !== self::ERROR_FLAG_FORCE_HIDE
        ) {
            throw new LIP2Exception( sprintf( '[%s] : %s', $key, $message ) );
        }
    }


    /**
     * エラーメッセージを取得
     *  / $key が空の場合は、全てのエラーメッセージを配列で取得
     *
     * @access public
     * @param String $key = NULL 取得するエラーキー
     * @return mixed
     */
    public function get_error_text( $key = NULL ) {
        if( !empty( self::$error ) ) {
            if(! $key ) return self::$error;
            if(! empty(self::$error[$key]) ) {
                return self::$error[$key];
            }
        }
        return NULL;
    }

    /**
     * エラーが発生しているかチェック
     *  / $key が空の場合は、全てのエラーで確認
     *
     * @access public
     * @param string $key = NULL チェックするエラーキー
     * @return bool
     */
    public function get_check_error( $key = NULL ) {
        if(! empty( self::$error ) ) {
            if(! $key ) return TRUE;
            if(! empty( self::$error[$key] ) ) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * エラー変数を全表示
     *  / $hideが正の時は、HTMLのコメントアウト内で出力する
     *
     * @access public
     * @param bool $hide = FALSE 画面表示
     */
    public function error_dump( $hide = FALSE ) {
        echo $hide ? '<!--' : '<pre>';
        var_dump( self::$error );
        echo $hide ? '-->' : '</pre>';
    }
}

class LIP2Exception extends Exception {
    public function __construct( $message, $code = 0, Exception $previous = null ) {
        parent::__construct( "Exeption Error : ".$message, $code );
    }
}