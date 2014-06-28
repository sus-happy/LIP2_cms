<?php

/**
 * テンプレ表示クラス
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 */

class View extends Object {

    private static $var;
    private static $result;
    private static $file;
    private static $hook = array();

    /**
     * 変数リセット
     *
     * @access public
     */
    public static function reset_var() {
        self::$var = array();
    }

    /**
     * 変数セット
     *
     * @access public
     * @param mixed $key テンプレ内の変数名、配列の場合は複数登録
     * @param mixed $cont = NULL $keyに対応する値
     */
    public static function set_var( $key, $cont = NULL ) {
        if( is_array( $key ) || is_object( $key ) ) {
            foreach ( $key as $k => $v ) {
                self::set_var( $k, $v );
            }
        } else {
            self::$var[ $key ] = $cont;
        }
    }

    /**
     * テンプレ読込
     *
     * @access public
     * @param string $path テンプレートファイルパス
     * @param mixed $param テンプレートで利用する変数
     * @return string
     */
    public static function render( $path, $param = NULL ) {
        if( $param !== NULL )
            self::set_var( $param );

        $path = explode( '/', $path );
        $dir = Config::get_param( 'global', 'site', 'data' );

        // テンプレファイルが存在するか？
        self::$file = $dir.'/view/'.implode( '/', $path ).'.php';
        if(! file_exists( self::$file ) ) {
            self::push_error( 'render', 'Template File is Not Found. : '.self::$file );
            return FALSE;
        }

        // self::$var にデータがあればローカライズ
        if( is_array( self::$var ) || is_object( self::$var ) )
            extract( self::$var );

        // テンプレ読込
        self::$result = '';
        ob_start();
        require_once( self::$file );
        self::$result = ob_get_contents();
        ob_end_clean();

        return self::$result;
    }

    /**
     * テンプレ表示
     *
     * @access public
     * @param string $path テンプレートファイルパス
     * @param mixed $param テンプレートで利用する変数
     */
    public static function show( $path = NULL, $param = NULL ) {
        if( $path )
            self::render( $path, $param );

        echo self::$result;
    }


    /**
     * フック追加
     *
     * @access public
     * @param string $key フックキー
     * @param string $callback 関数名
     */
    public static function push_hook( $key, $callback ) {
        $param = array_slice( func_get_args(), 2 );
        self::$hook[ $key ] = array(
            'callback' => $callback,
            'param'    => $param,
        );
    }

    /**
     * フック呼び出し
     *
     * @access public
     * @param string $key
     */
    public static function call_hook( $key ) {
        if( isset( self::$hook[ $key ] ) ) {
            if( is_callable( self::$hook[ $key ]['callback'] ) ) {
                return call_user_func_array( self::$hook[ $key ]['callback'], self::$hook[ $key ]['param'] );
            }
        }
    }

}