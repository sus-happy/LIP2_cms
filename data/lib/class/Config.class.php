<?php
require_once( dirname(__FILE__).'/Object.class.php' );

/**
 * 設定情報の読込、取得
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 */

class Config extends Object {

    private static $data, $ver = 'dev';

    /**
     * 設定情報の取得
     *
     * @access public
     * @param string $ver バージョン名（config配下のフォルダ名）設定情報を切り替える
     */
    public static function set_version( $ver ) {
        self::$ver = $ver;
    }

    /**
     * 設定情報の取得
     *
     * @access public
     * [ @param string $args パラメータキー [ @param string $... ] ]
     * @return mixed $result
     */
    public static function get_param() {
        $num = func_num_args();
        $result = self::$data;

        if( $num > 0 ) {
            for( $i=0; $i<$num; $i++ ) {
                $key = func_get_arg( $i );

                if( is_array( $key ) || is_object( $key ) ) {
                    self::push_error( 'get_param', 'Key must be String.' );
                    return FALSE;
                }

                if(! isset( $result[$key] ) ) {
                    self::push_error( 'get_param', 'Parameter is Not Found. ['.$key.']' );
                    return FALSE;
                }

                $result = $result[$key];
            }
        }

        return $result;
    }

    /**
     * 設定情報の読込トリガー
     *
     * @access public
     * @param mixed $object 設定情報ファイル名（群）
     */
    public static function import( $object ) {
        if( is_array( $object ) || is_object( $object ) ) {
            foreach( $object as $obj ) {
                self::import( $obj );
            }
        } else {
            self::set_config( $object );
        }
    }

    /**
     * 設定情報の読込
     *
     * @access public
     * @param mixed $name 設定情報ファイル名
     */
    private static function set_config( $name ) {
        $file_name = sprintf( '%s/../../conf/%s/config.%s.php', dirname( __FILE__ ), self::$ver, $name );

        if(! file_exists( $file_name ) ) {
            self::push_error( 'set_config', 'Config File is Not Found.' );
            return FALSE;
        }

        require( $file_name );
        self::$data[ $name ] = $conf;
    }

}