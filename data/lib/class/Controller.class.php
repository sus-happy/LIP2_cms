<?php

/**
 * コントローラークラス
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 */

class Controller extends Object {

    const MESSAGE_SUCCESS  = 1;
    const MESSAGE_ADD      = 2;
    const MESSAGE_EDIT     = 3;
    const MESSAGE_DELETE   = 4;
    const MESSAGE_NOTFOUND = 404;
    const MESSAGE_ERROR    = 9;
    const MESSAGE_FORMAT_FAILED = 500;

    const LABEL_SUCCESS    = 1;
    const LABEL_ADD        = 2;
    const LABEL_EDIT       = 3;
    const LABEL_DELETE     = 4;
    const LABEL_NOTFOUND   = 404;
    const LABEL_ERROR      = 9;

    /**
     * 関数読込
     *
     * @access public
     * @param string $func 関数名
     * @param string $param 引数
     */
    public function load_func( $func, $param ) {
        if( is_callable( array( $this, $func ) ) )
            call_user_func_array( array( $this, $func ), is_array( $param ) ? $param : array( $param ) );
    }

    /**
     * ナンス作成
     *
     * @access protected
     * @param string $key ナンスキー
     * @return string $nonce ナンス
     */
    protected static function make_nonce( $key ) {
        $nonce = sha1( uniqid() );
        Session::set_param( $key, $nonce );
        return $nonce;
    }

    /**
     * ナンス確認
     *
     * @access protected
     * @param string $key ナンスキー
     * @param string $nonce ナンス
     * @return boolean
     */
    protected static function check_nonce( $key, $nonce ) {
        if( empty( $nonce ) )
            return FALSE;

        $s_nonce = Session::get_param( $key );
        Session::remove_param( $key );

        return (string)$nonce === (string)$s_nonce;
    }

}