<?php

/**
 * 認証クラス
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 */

class Auth extends Object {

    private static $user = NULL, $is_login = NULL;

    /**
     * 認証確認
     *
     * @access public
     * @return boolean
     */
    public static function is_login() {
        if( self::$is_login === NULL ) {
            $s = Session::get_param( 'auth' );
            if( $s )
                self::$user = User_model::get_detail( $s['user_id'], $s['user_name'] );

            self::$is_login = ! empty( self::$user );
        }

        return self::$is_login;
    }

    /**
     * ログイン認証
     *  / 非ログイン時はログインページにリダイレクト
     *
     * @access public
     * @param string $redirect リダイレクト先
     */
    public static function need_login( $redirect = 'login' ) {
        if(! self::is_login() )
            Url::redirect( $redirect );
    }

    /**
     * ログインユーザのパラメータを取得
     *
     * @access public
     * @param string $key パラメータキー
     * @return mixed　パラメータ
     */
    public static function get_param( $key ) {
        return self::$user[ $key ];
    }

    /**
     * ログイン確認
     *
     * @access public
     * @param string $user_name ユーザ名
     * @param string $password パスワード
     * @return mixed ユーザ情報 / ログイン不可の場合はNULLが返る
     */
    public static function check( $user_name, $password ) {
        return User_model::get_detail_from_name( $user_name, self::crypt( $password ) );
    }

    /**
     * 暗号化
     *
     * @access private
     * @param string $str 暗号化したい文字列
     * @return string 暗号化された文字列
     */
    public static function crypt( $str ) {
        return crypt( $str, Config::get_param( 'auth', 'sig' ) );
    }

}