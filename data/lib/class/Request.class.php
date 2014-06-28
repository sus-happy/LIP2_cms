<?php

/**
 * リクエストクラス
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 */

class Request extends Object {

    const GET_METHOD  = 1;
    const POST_METHOD = 2;

    private static $_first    = TRUE;
    private static $_data     = array();
    private static $_raw_data = array();

    private static function get_instance() {
        if(! self::$_first )
            return;
        self::$_first = FALSE;

        self::$_data['get']      = new Request_data( self::GET_METHOD );
        self::$_raw_data['get']  = new Request_raw_data();
        self::$_data['post']     = new Request_data( self::POST_METHOD );
        self::$_raw_data['post'] = new Request_raw_data();

        if( isset( $_POST ) ) {
            foreach( $_POST as $key => $val ) {
                self::$_data['post']->$key = $val;
                self::$_raw_data['post']->$key = $val;
            }
        }
        if( isset( $_GET ) ) {
            foreach( $_GET as $key => $val ) {
                self::$_data['get']->$key = $val;
                self::$_raw_data['get']->$key = $val;
            }
        }
    }

    /**
     * GET値を取得する
     *
     * @access public
     * @param string $key キー
     * @param boolean $raw = FALSE 変換せずに取得する
     * @return mixed $result
     */
    public static function get( $key = NULL, $raw = FALSE ) {
        self::get_instance();

        if(! $key ) {
            if( $raw )
                return self::$_raw_data['get']->to_object();
            else
                return self::$_data['get']->to_object();
        }

        if( $raw )
            return self::$_raw_data['get']->$key;
        else
            return self::$_data['get']->$key;
    }

    /**
     * POST値を取得する
     *
     * @access public
     * @param string $key キー
     * @param boolean $raw = FALSE 変換せずに取得する
     * @return mixed $result
     */
    public static function post( $key = NULL, $raw = FALSE ) {
        self::get_instance();

        if(! $key ) {
            if( $raw )
                return self::$_raw_data['post']->to_object();
            else
                return self::$_data['post']->to_object();
        }

        if( $raw )
            return self::$_raw_data['post']->$key;
        else
            return self::$_data['post']->$key;
    }

}

class Request_data extends Object {
    private static $_method = NULL;
    protected static $_data = array();

    function __construct( $method ) {
        self::$_method = $method;
    }

    function __set( $key, $val ) {
        static::$_data[ $key ] = self::deep_convert( $val );
    }

    function __get( $key ) {
        return static::$_data[ $key ];
    }

    function __isset( $key ) {
        return isset( static::$_data[ $key ] );
    }

    function __unset( $key ) {
        unset( static::$_data[ $key ] );
    }

    public function to_array() {
        return static::$_data;
    }

    public function to_object() {
        return (object) static::$_data;
    }

    /**
     * 再帰的に変換する
     *
     * @access private
     * @param mixed $param 変換する値
     * @return mixed $param
     */
    private static function deep_convert( $param ) {
        if( is_array( $param ) || is_object( $param ) ) {
            foreach( $param as &$row ) {
                $row = self::deep_convert( $row );
            }
        } else {
            if( self::$_method === Request::GET_METHOD )
                $param = urldecode( $param );
            $param = htmlspecialchars( $param, ENT_QUOTES );
        }

        return $param;
    }
}

class Request_raw_data extends Request_data {
    function __construct() {
    }

    function __set( $key, $val ) {
        static::$_data[ $key ] = $val;
    }
}


