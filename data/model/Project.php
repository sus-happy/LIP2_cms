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
        2 => '土地探し',
        3 => 'エクステリア',
        4 => 'リフォーム',
    );

    public static function get_category() {
        return self::$_category;
    }

    public static function get_category_label( $id ) {
        return isset( self::$_category[ $id ] ) ? self::$_category[ $id ] : NULL;
    }

    private static $_build_type = array(
        1 => '木造軸組工法',
        2 => 'ADVANCE WOOD工法',
        3 => 'COMBINATION WOOD工法',
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
    );

    public static function get_template() {
        return self::$_template;
    }

    public static function get_template_label( $id ) {
        return isset( self::$_template[ $id ] ) ? self::$_template[ $id ] : NULL;
    }
}

class Project_data_model extends Data_model_post {
    protected static $_meta_schema = array(
        'main_image' => self::INTEGER,
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
}