<?php

/**
 * URL解析クラス
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 */

class Url extends Object {

    private static $param;
    private static $class;
    private static $function;
    private static $controller;
    private static $full_url;
    private static $url;
    private static $query;
    private static $base_path;
    private static $base_url;

    /**
     * URL解析
     *
     * @access public
     */
    public static function analyze() {
        $base_url = implode( '/', array_slice( explode( '/', self::base_path() ), 3, -1 ) );
        $base_url = $base_url ? '/'.$base_url : '';

        $base_url_x = implode( '/', array_slice( explode( '/', self::base_url() ), 3, -1 ) );
        $base_url_x = $base_url_x ? '/'.$base_url_x : '';

        self::$full_url = str_replace( array( $base_url, $base_url_x ), '', $_SERVER["REQUEST_URI"] );
        list( self::$url, self::$query ) = explode( '?', self::$full_url );

        if(! empty( self::$url ) && self::$url !== "/" ) {
            self::$param = explode( "/", self::$url );
            array_shift( self::$param );
            self::$class = array_shift( self::$param );
            self::$function = array_shift( self::$param );
            if( empty( self::$function ) )
                self::$function = Config::get_param( 'global', 'index', 'func' );
        } else {
            self::$class = Config::get_param( 'global', 'index', 'path' );
            self::$function = Config::get_param( 'global', 'index', 'func' );
            self::$param = array();
        }

        // コントローラクラスを探しに行く
        try {
            self::$controller = Loader::get_action( self::$class, self::$function, self::$param );
        } catch( LIP2LoadException $e ) {
            self::$class = Config::get_param( 'global', '404', 'path' );
            self::$function = Config::get_param( 'global', '404', 'func' );
            self::$param = array();
            self::$controller = Loader::get_action( self::$class, self::$function, self::$param );
        }
    }

    /**
     * 解析結果取得
     *
     * @access public
     * @return array( 'full' => GETクエリ付きURL, 'url' => URL, 'query' => GETクエリ )
     */
    public static function get_url() {
        return array(
            'full'  => self::$full_url,
            'url'   => self::$url,
            'query' => self::$query,
        );
    }

    public static function get_class() {
        return self::$class;
    }
    public static function get_func() {
        return self::$function;
    }

    public static function base_url() {
        if(! self::$base_url ) {
            self::$base_url = Config::get_param( 'global', 'site', 'url' );
            if(! preg_match( '/\/$/', self::$base_url ) )
                self::$base_url .= '/';
        }

        return self::$base_url;
    }

    public static function base_path() {
        if(! self::$base_path ) {
            self::$base_path = Config::get_param( 'global', 'site', 'file' );
            if( self::$base_path ) {
                self::$base_path = self::base_url().self::$base_path.'/';
            } else {
                self::$base_path = Config::get_param( 'global', 'site', 'url' ).'/';
            }
        }

        return self::$base_path;
    }

    /**
     * URL取得
     *
     * @access public
     * @param string $path = NULL リンク先、空の場合はトップ
     * @param mixed $query = NULL GETパラメータ
     * @return string
     */
    public static function site_url( $path = NULL, $query = NULL ) {
        if( is_array( $query ) )
            $query = '?'.http_build_query( $query );
        else
            $query = NULL;

        return self::base_path().implode( '/', array_map( 'urlencode', explode( '/', $path ) ) ).$query;
    }

    /**
     * リダイレクト
     *
     * @access public
     * @param string $path = NULL リダイレクト先、空の場合はトップにリダイレクト
     * @param mixed $query = NULL GETパラメータ
     */
    public static function redirect( $path = NULL, $query = NULL ) {
        $redirect = self::site_url( $path, $query );
        header( 'Location: '.$redirect );
        exit();
    }

    /**
     * ダンプ
     *
     * @access public
     * @param boolean $hide 表示/非表示
     */
    public static function dump( $hide = FALSE ) {
        echo $hide ? '<!--' : '<pre>';
        echo "CONTROL:\n";
        var_dump( self::$class );
        echo "\n\nFUNCTION:\n";
        var_dump( self::$function );
        echo "\n\nPARAM:\n";
        var_dump( self::$param );
        echo $hide ? '-->' : '</pre>';
    }

}