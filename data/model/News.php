<?php

/**
 * モデルクラス：案件データ
 *
 * @package Model
 * @author SUSH
 * @version 0.0.1
 */

class News_model extends Model_post {
    protected static
        $_data_model = 'News_data_model',
        $_post_type  = 'news',
        $_row_data   = array( 'content' );

    private static $_category = array(
        1 => 'お知らせ',
        2 => 'イベント情報',
        3 => '採用情報',
    );

    public static function get_category() {
        return self::$_category;
    }

    public static function get_category_label( $id ) {
        return isset( self::$_category[ $id ] ) ? self::$_category[ $id ] : NULL;
    }
}

class News_data_model extends Data_model_post {
    protected static $_meta_schema = array(
        'category'   => self::INTEGER,
        'content'    => self::STRING,
    );

    function __get( $key ) {
        switch( $key ) {
            case 'content':
                if( isset( $this->_data[ 'content' ] ) )
                    return stripcslashes( $this->_data[ 'content' ] );
            break;
        }

        return parent::__get( $key );
    }
}