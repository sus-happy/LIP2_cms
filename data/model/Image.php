<?php

/**
 * モデルクラス：案件データ
 *
 * @package Model
 * @author SUSH
 * @version 0.0.1
 */

class Image_model extends Model_post {
    protected static $_data_model = 'Image_data_model';
    protected static $_post_type  = 'image';
}

class Image_data_model extends Data_model_post {
    protected static $_meta_schema = array(
        'type'   => self::STRING,
        'name'   => self::STRING,
        'width'  => self::INTEGER,
        'height' => self::INTEGER,
    );
}