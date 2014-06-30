<?php

/**
 * モデルクラス：案件データ
 *
 * @package Model
 * @author SUSH
 * @version 0.0.1
 */

class Project_model extends Model_post {
    protected static $_data_model = 'Project_data_model';
    protected static $_post_type  = 'project';

    private static $_category = array(
        1 => '住まいづくり',
        2 => 'エクステリア',
        3 => 'リフォーム',
    );

    public static function get_category() {
        return self::$_category;
    }

    public static function get_category_label( $id ) {
        return isset( self::$_category[ $id ] ) ? self::$_category[ $id ] : NULL;
    }

    private static $_build_type = array(
        1 => '木造軸組工法',
        7 => '木造軸組金物工法',
        8 => '木造軸組金物工法(WINWOOD) + NPパネル',
        // 2 => 'ADVANCE WOOD工法',
        // 3 => 'COMBINATION WOOD工法',
        4 => '重量鉄骨造',
        5 => 'RC造',
        6 => 'その他',
    );

    public static function get_build_type() {
        return self::$_build_type;
    }

    public static function get_build_type_label( $id ) {
        return isset( self::$_build_type[ $id ] ) ? self::$_build_type[ $id ] : NULL;
    }

    private static $_template = array(
        1 => 'テンプレートA',
        2 => 'テンプレートB',
        3 => 'テンプレートC',
        4 => 'テンプレートD',
    );

    public static function get_template() {
        return self::$_template;
    }

    public static function get_template_label( $id ) {
        return isset( self::$_template[ $id ] ) ? self::$_template[ $id ] : NULL;
    }

    private static $_default = array(
        'offset'    => 0,
        'limit'     => 20,
    );
    public static function search( $option ) {
        // デフォルトパラム
        $_set = static::param_merge( self::$_default, $option );
        $offset = isset( $_set['offset'] ) ? $_set['offset'] : NULL;
        $limit  = isset( $_set['limit'] )  ? $_set['limit']  : NULL;
        unset( $_set['offset'], $_set['limit'] );

        $sql = static::make_sql();

        self::make_where( $sql, $_set );
        $sql .= ' GROUP BY `cms_post`.`post_id`';

        self::add_order_by( $sql, 'created', 'DESC' );
        self::add_order_by( $sql, array( 'cms_post', 'post_id' ), 'DESC' );

        static::add_offset_limit( $sql, $offset, $limit );
        Database::set_class_object( static::$_data_model );
        return Database::execute( $sql );
    }

    public static function search_count( $option ) {
        // デフォルトパラム
        $_set = static::param_merge( self::$_default, $option );

        $sql = static::make_sql();

        self::make_where( $sql, $_set );
        $sql .= ' GROUP BY `cms_post`.`post_id`';

        // Database::set_class_object( static::$_data_model );
        $row = Database::execute( $sql )->fetchAll();
        return count( $row );
        // return (int)$row['count'];
    }
        private static function make_where( &$sql, $_set ) {
            self::add_where( $sql, 'post_type', static::$_post_type );
            self::add_where( $sql, 'valid', self::POST_ENABLED );
            if(! defined( 'IS_AUTH' ) || ! IS_AUTH )
                self::add_where( $sql, 'visible', self::POST_SHOW );

            $sql .= ' AND ( 1 = 1';

            if( $_set['category'] ) {
                $keis  = array();
                $index = 0;
                foreach ( $_set['category'] as $cat ) {
                    $sql_key_child = sprintf( 'project_category_search%d', $index );
                    $keis[] = ':'.$sql_key_child;
                    Database::bind( $sql_key_child, $cat );
                    $index ++;
                }
                $sql .= sprintf( ' AND EXISTS (
                    SELECT *
                    FROM `cms_meta`
                    WHERE
                        `cms_post`.`post_id` = `cms_meta`.`post_id` AND
                        `meta_key` = \'category\' AND
                        `meta_value` IN ( %s )
                )', implode( ',', $keis ) );
            }
            if( $_set['adults'] )      self::add_where( $sql, 'adults', $_set['adults'], '=', PDO::PARAM_INT );
            if( $_set['children'] )    self::add_where( $sql, 'children', $_set['children'], '=', PDO::PARAM_INT );
            if( $_set['land_area_d'] ) self::add_where( $sql, 'land_area', $_set['land_area_d'], '>=', PDO::PARAM_INT );
            if( $_set['land_area_u'] ) self::add_where( $sql, 'land_area', $_set['land_area_u'], '<=', PDO::PARAM_INT );
            if( $_set['template'] )    self::add_where( $sql, 'template', $_set['template'], '=', PDO::PARAM_INT );

            /**
             * キーワード対象
             * キャッチコピー、コメント、家族構成（文章）、各テキスト
             */
            if( $_set['keyword'] ) {
                $sql .= ' AND ( 1 = 0';
                self::add_or_search( $sql, 'post_title', $_set['keyword'] );
                self::add_or_search( $sql, 'content', $_set['keyword'] );
                self::add_or_search( $sql, 'family', $_set['keyword'] );
                self::add_or_search( $sql, 'text_a', $_set['keyword'] );
                self::add_or_search( $sql, 'text_b', $_set['keyword'] );
                self::add_or_search( $sql, 'text_f', $_set['keyword'] );
                self::add_or_search( $sql, 'text_g', $_set['keyword'] );
                $sql .= ' )';
            }

            $sql .= ' )';
        }
}

class Project_data_model extends Data_model_post {
    protected static $_meta_schema = array(
        'main_image' => self::IMAGE,
        'content'    => self::STRING,
        'category'   => self::XARRAY,
        'family'     => self::STRING,
        'adults'     => self::INTEGER,
        'children'   => self::INTEGER,
        'build_type' => self::INTEGER,
        'b_year'     => self::INTEGER,
        'b_month'    => self::INTEGER,
        'land_area'  => self::FLOAT,
        'build_area' => self::FLOAT,
        'template'   => self::INTEGER,
        'text_a'     => self::STRING,
        'text_b'     => self::STRING,
        'image_c'    => self::INTEGER,
        'image_d'    => self::INTEGER,
        'image_e'    => self::INTEGER,
        'text_f'     => self::STRING,
        'text_g'     => self::STRING,
    );

    function __get( $key ) {
        return parent::__get( $key );
    }

    protected function check_schema( $key ) {
        if( $key == 'width' || $key == 'height' )
            return self::IMAGE;

        return parent::check_schema( $key );
    }
}