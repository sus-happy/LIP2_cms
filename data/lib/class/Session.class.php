<?php

/**
 * セッションクラス
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 */

class Session extends Object {

    private static $param;

    /**
     * セッションスタート
     *
     * @access public
     * @param string $sessid セッションID　現在意味なし
     */
    public static function start( $key ) {
        $session_dir = Config::get_param( 'global', 'site', 'data' ).'/tmp/sess/'.$key;
        if(! is_dir( $session_dir ) ) {
            if(! @mkdir( $session_dir, 0777, TRUE ) ) {
                self::push_error( 'start', 'Not Found Session dir' );
            }
        }

        session_save_path( $session_dir );
        session_name( $key );
        session_start();

        foreach( $_SESSION as $k => $v ) {
            self::$param[$k] = $v;
        }
    }

    /**
     * セッション変数を取得
     *
     * @access public
     * @param string $key 変数名
     * @return mixed
     */
    public static function get_param( $key ) {
        return self::$param[ $key ];
    }

    /**
     * セッション変数をセット
     *
     * @access public
     * @param string $key 変数名
     * @param mixed $param セットする値
     */
    public static function set_param( $key, $param ) {
        self::$param[ $key ] = $param;
        $_SESSION[ $key ] = self::$param[ $key ];
    }

    /**
     * セッション変数を削除
     *
     * @access public
     * @param string $key 変数名
     */
    public static function remove_param( $key ) {
        unset( self::$param[ $key ] );
        unset( $_SESSION[ $key ] );
    }

    /**
     * セッションを全て破棄
     *
     * @access public
     */
    public static function destroy() {
        // セッション変数を全て解除する
        $_SESSION = array();
        self::$param = array();

        // セッションを切断するにはセッションクッキーも削除する。
        // Note: セッション情報だけでなくセッションを破壊する。
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }

        // 最終的に、セッションを破壊する
        session_destroy();
    }
}