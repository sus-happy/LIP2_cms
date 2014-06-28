<?php

/**
 * ローダークラス
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 */

class Loader extends Object {

    public static function load_class( $class ) {
        if( preg_match( '/(.*)_(.*)$/', $class, $matches ) ) {
            $class_name  = $matches[1];
            $action_name = $matches[2];

            switch( $action_name ) {
                case 'model':
                    self::get_model( $class_name );
                break;
                default:
                    self::get_class( explode( '_', $class ) );
                break;
            }
        } else {
            self::get_class( $class );
        }
    }

    /**
     * クラス読込
     *
     * @access public
     * @param mixed $package パッケージ名
     * @param string $str モデル名
     */
    public static function get_class( $str ) {
        if( is_array( $str ) || is_object( $str ) ) {

            $class_name = implode( '_', array_map( 'ucfirst', $str ) );
            $file_name  = implode( '/', array_map( 'ucfirst', $str ) );

            // 読込済みであれば終了
            if( class_exists( $class_name ) )
                return TRUE;

            $file_name = Config::get_param( 'global', 'site', 'data' ).'/lib/class/'.$file_name.'.class.php';

            if(! file_exists( $file_name ) ) {
                self::push_error( 'get_class', 'Class file is Not Found. ['. $class_name .']' );
                return FALSE;
            }

            require_once( $file_name );
        } else {
            $class_name = ucfirst( $str );

            // 読込済みであれば終了
            if( class_exists( $class_name ) )
                return TRUE;

            $file_name = Config::get_param( 'global', 'site', 'data' ).'/lib/class/'.$class_name.'.class.php';

            if(! file_exists( $file_name ) ) {
                self::push_error( 'get_class', 'Class file is Not Found. ['. $class_name .']' );
                return FALSE;
            }

            require_once( $file_name );
        }
        return TRUE;
    }

    /**
     * アクション読込
     *
     * @access public
     * @param string $act
     */
    public static function get_action( $act, $func, $param = NULL ) {
        // コントローラクラスを探しに行く
        $dir = Config::get_param( 'global', 'site', 'data' );
        $file_name = $dir.'/action/'.implode( '/', array_map( 'ucfirst', explode( '_', $act ) ) ).'.php';
        if(! file_exists( $file_name ) ) {
            throw new LIP2LoadException( 'Controller File is Not Found. ['.$file_name.']' );
            // self::push_error( 'analyze', 'Controller File is Not Found. ['.$file_name.']' );
            return FALSE;
        }

        $class_name = ucfirst( $act ).'_action';
        require_once( $file_name );
        $cl = new $class_name();
        $cl->load_func( $func, $param );

        return $cl;
    }

    /**
     * モデル読込
     *
     * @access public
     * @param string $str モデル名
     */
    public static function get_model( $str ) {
        $model_name = ucfirst( $str );

        // 読込済みであれば終了
        if( class_exists( $model_name.'_model' ) )
            return TRUE;

        $file_name = Config::get_param( 'global', 'site', 'data' ).'/model/'.$model_name.'.php';

        if(! file_exists( $file_name ) ) {
            self::push_error( 'get_model', 'Model file is Not Found.' );
            return FALSE;
        }

        require_once( $file_name );
        return TRUE;
    }

    /**
     * 関数群読込
     *
     * @access public
     * @param string $str 関数郡名
     */
    public static function get_func( $str ) {
        $func_name = ucfirst( $str );
        $file_name = Config::get_param( 'global', 'site', 'data' ).'/lib/func/'.$func_name.'.php';

        if(! file_exists( $file_name ) ) {
            self::push_error( 'get_func', 'Functions file is Not Found.' );
            return FALSE;
        }

        require_once( $file_name );
        return TRUE;
    }

}

class LIP2LoadException extends LIP2Exception {
}

spl_autoload_register( array( 'Loader', 'load_class' ) );