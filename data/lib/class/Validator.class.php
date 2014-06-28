<?php

/**
 * バリデート拡張クラス
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 * @todo クラス上でルール登録できるようにする
 */

class Validator extends Object {
    private static
            $check,
            $data,
            $character = "utf-8";

    /**
     * バリデートルールの登録
     *
     * @access public
     * @param array $check
     */
    public static function set_validate_data( $check ) {
        self::$check = $check;
    }

    /**
     * バリデーション開始
     *
     * @access public
     * @param array $check
     * @return boolean $flag
     */
    public static function check_validation( $data ) {
        self::$data = (array) $data;
        if( !empty(self::$check) ) { foreach( self::$check as $key=>$val ) {
            if( !$err = self::check_rule( $key ) ) {
                /* success :) */
            } else {
                self::push_error( $key, $err, self::ERROR_FLAG_FORCE_HIDE );
            }
        } }

        return ! self::get_check_error();
    }

    /**
     * ルール検証
     *
     * @access public
     * @param string $key
     * @return boolean
     */
    public static function check_rule( $key ) {
        /*
            require
            必須項目指定
        */
        if( self::$check[$key]["require"]["check"] )
            if( !self::check_require($key) )
                return self::$check[$key]["require"]["error"];

        /*
            isset
            指定項目入力時に必須項目
        */
        if( !empty( self::$check[$key]["isset"]["check"] ) )
            if( !empty( self::$check[$key]["isset"]["val"] ) ) {
                if( self::$data[ self::$check[$key]["isset"]["key"] ] == self::$check[$key]["isset"]["val"] )
                    if( !self::check_require($key) )
                        return self::$check[$key]["isset"]["error"];
            } else {
                if( !empty( self::$data[ self::$check[$key]["isset"]["key"] ] ) )
                    if( !self::check_require($key) )
                        return self::$check[$key]["isset"]["error"];
            }

        /*
            equal
            指定項目と同値
        */
        if( !empty( self::$check[$key]["equal"]["check"] ) )
            if( self::$data[ $key ] !== self::$data[ self::$check[$key]["equal"]["key"] ] )
                return self::$check[$key]["equal"]["error"];

        /*
            mail
            メールアドレスチェック
        */
        if( !empty( self::$check[$key]["mail"]["check"] ) ) {
            if( !empty(self::$data[ $key ]) && !preg_match("/^([a-z0-9_]|\-|\.|\+)+@(([a-z0-9_]|\-)+\.)+[a-z]{2,6}$/i", self::$data[ $key ]) )
                return self::$check[$key]["mail"]["error"];
        }

        /*
            num
            数字入力チェック
        */
        if( !empty( self::$check[$key]["num"]["check"] ) )
            if( !$this->check_numeric($key) )
                return self::$check[$key]["num"]["error"];

        /*
            kana
            カタカナ入力チェック
        */
        if( !empty( self::$check[$key]["kana"]["check"] ) )
            if( !$this->check_kana($key) )
                return self::$check[$key]["kana"]["error"];

        return FALSE;
    }

    /**
     * 未入力検証
     *
     * @access private
     * @param string $key
     * @return boolean
     */
    private static function check_require( $key ) {
        if( count(self::$check[$key]["require"]["and"])>0 ) {
            if( self::check_deep_empty( self::$data[ $key ] ) ) {
                return TRUE;
            } else {
                foreach( self::$check[$key]["require"]["and"] as $aKey ) {
                    if( self::check_deep_empty( self::$data[ $aKey ] ) ) {
                        return TRUE;
                    }
                }
            }
            return FALSE;
        } else {
            if( self::check_deep_empty( self::$data[ $key ] ) ) {
                if( count(self::$check[$key]["require"]["or"])>0 ) {
                    foreach( self::$check[$key]["require"]["or"] as $oKey ) {
                        if( self::check_deep_empty( self::$data[ $oKey ] ) ) {
                        } else return FALSE;
                    }
                }
                return TRUE;
            } else return FALSE;
        }
        return FALSE;
    }

    /**
     * 空白文字も空として扱う
     *
     * @access private
     * @param string $str
     * @return string
     */
    private static function check_deep_empty( $str ) {
        $str = self::space_remove( $str );
        return !empty( $str );
    }

    /**
     * 空白文字を削除
     *
     * @access private
     * @param string $str
     * @return string
     */
    private static function space_remove( $str ) {
        return str_replace( " ", "", str_replace( "　", "", $str ) );
    }

    /**
     * 数字判定検証
     *
     * @access private
     * @param string $key
     * @return boolean
     */
    private static function check_numeric($key) {
        if( empty(self::$data[ $key ]) || ( !empty(self::$data[ $key ]) && strval(self::$data[ $key ]) == strval(intval(self::$data[ $key ])) ) ) {
            if( count(self::$check[$key]["num"]["or"])>0 ) {
                foreach( self::$check[$key]["num"]["or"] as $oKey ) {
                    if( empty(self::$data[ $oKey ]) || ( !empty(self::$data[ $oKey ]) && strval(self::$data[ $oKey ]) == strval(intval(self::$data[ $oKey ])) ) ) {
                    } else return FALSE;
                }
            }
            if( count(self::$check[$key]["num"]["and"])>0 ) {
                foreach( self::$check[$key]["num"]["and"] as $aKey ) {
                    if( empty(self::$data[ $aKey ]) || ( !empty(self::$data[ $aKey ]) && strval(self::$data[ $aKey ]) == strval(intval(self::$data[ $aKey ])) ) ) {
                    } else return FALSE;
                }
            }
            return TRUE;
        } else return FALSE;
    }

    /**
     * カナ文字判定検証
     *
     * @access private
     * @param string $key
     * @todo ひらがな検証とかも作ったほうがいいかな？
     */
    private static function check_kana( $key ) {
        if( !empty(self::$data[$key]) )
            return self::kh_check( self::$data[$key], "K" );
        return TRUE;
    }

    /**
     * カナ文字判定
     *
     * @access private
     * @param String $str
     * @param String $flag
     * @return boolean
     * @todo バージョンによって動かないっぽいので一旦コメントアウト
     */
    private static function kh_check( $str, $flag ){
        /*
        mb_regex_encoding( self::$character );
        switch ( $flag ) {
            case "H":
                //ひらがなチェック
                if (!mbereg('^([あ-ん]|[ー 　]){1,16}$',$str,$this->character)) {
                    return FALSE;
                }
                break;
            case "K":
                //カタカナチェック
                if (!mbereg('^([ァ-ヶ]|[ー 　]){1,16}$',$str,$this->character)) {
                    return FALSE;
                }
                break;
            default:
                exit("specify 'H' or 'K'");
                break;
        }
        */
        return TRUE;
    }
}