<?php

/**
 * モデルクラス：ベースモデルデータ
 *
 * @package Model
 * @author SUSH
 * @version 0.0.1
 */

class Model_post extends Model {

    // 完/未完
    const POST_ENABLED  = 1;
    const POST_DISABLED = 0;

    private static $_default = array(
        'post_type' => NULL,
        'valid'     => TRUE,
        'offset'    => 0,
        'limit'     => 20,
    );
    protected static $_data_model = 'Post_data_model';
    protected static $_post_type  = NULL;

    public static function get_list( $option = NULL ) {
        // デフォルトパラム
        $_set = static::param_merge( self::$_default, $option );
        $offset = isset( $_set['offset'] ) ? $_set['offset'] : NULL;
        $limit  = isset( $_set['limit'] )  ? $_set['limit']  : NULL;
        unset( $_set['offset'], $_set['limit'] );

        $sql = static::make_sql();

        if( $_set['post_type'] )
            self::add_where( $sql, 'post_type', $_set['post_type'] );
        else if( static::$_post_type )
            self::add_where( $sql, 'post_type', static::$_post_type );
        unset( $_set['post_type'] );

        self::add_where( $sql, 'valid', self::POST_ENABLED );

        $sql .= ' GROUP BY `cms_post`.`post_id`';
        self::add_order_by( $sql, 'created', 'DESC' );

        static::add_offset_limit( $sql, $_set['offset'], $_set['limit'] );
        Database::set_class_object( static::$_data_model );
        return Database::execute( $sql );
    }

    public static function get_detail( $post_id ) {
        $sql = static::make_sql();

        if( static::$_post_type )
            self::add_where( $sql, 'post_type', static::$_post_type );
        self::add_where( $sql, array( 'cms_post', 'post_id' ), $post_id );
        self::add_where( $sql, 'valid', TRUE );

        Database::set_class_object( static::$_data_model );
        return Database::execute( $sql )->fetch();
    }
        protected static function make_sql() {
            $class = static::$_data_model;
            static::$_data_instance = new $class();

            $param = '`cms_post`.*';
            $join  = '';

            $count = 0;
            foreach( static::$_data_instance->get_meta_schema() as $key => $meta ) {
                $meta_key = Database::escape( $key );

                switch( $meta ) {
                    case Data_model::XARRAY:
                        $param .= sprintf( ', "||" || GROUP_CONCAT( `meta_%d`.`meta_value` , "||" ) || "||" AS `%s`', $count, $meta_key );
                        break;
                    case Data_model::INTEGER:
                        $param .= sprintf( ', CAST( `meta_%d`.`meta_value` AS INTEGER ) AS `%s`', $count, $meta_key );
                        break;
                    case Data_model::DOUBLE:
                    case Data_model::FLOAT:
                        $param .= sprintf( ', CAST( `meta_%d`.`meta_value` AS NUMERIC ) AS `%s`', $count, $meta_key );
                        break;
                    default:
                        $param .= sprintf( ', `meta_%d`.`meta_value` AS `%s`', $count, $meta_key );
                        break;
                }

                $join  .= sprintf( ' LEFT JOIN `cms_meta` AS `meta_%d` ON `cms_post`.`post_id` = `meta_%d`.`post_id` AND `meta_%d`.`meta_key` = %s', $count, $count, $count, Database::quote( $meta_key ) );
                $count ++;
            }

            $sql = 'SELECT '.$param.' FROM `cms_post`'.$join;

            return $sql;
        }

    public static function get_count( $option = NULL ) {
        // デフォルトパラム
        // $_set = static::param_merge( self::$_default, $option );

        $sql = 'SELECT COUNT(*) as `count` FROM `cms_post`';

        if( static::$_post_type )
            static::add_where( $sql, 'post_type', static::$_post_type );
        self::add_where( $sql, 'valid', TRUE );

        $row = Database::execute( $sql )->fetch();
        return $row['count'];
    }


    /**
     * INSERT
     */
    public static function insert( $data ) {
        $class = static::$_data_model;
        static::$_data_instance = new $class();

        if(! $data->visible ) {
            $data->visible = FALSE;
        }

        $now = time();

        static::$_data_instance->post_title = $data->post_title;
        static::$_data_instance->visible    = $data->visible;
        static::$_data_instance->created    = $data->created;

        $sql = 'INSERT INTO `cms_post`( `post_title`, `valid`, `visible`, `post_type`, `created`, `modified` ) VALUES( :post_title, :valid, :visible, :post_type, :created, :modified )';
        Database::bind( array(
            'post_title' => array( 'val' => static::$_data_instance->post_title ),
            'valid'      => array( 'val' => static::$_data_instance->valid, 'type' => PDO::PARAM_BOOL ),
            'visible'    => array( 'val' => static::$_data_instance->visible, 'type' => PDO::PARAM_BOOL ),
            'post_type'  => array( 'val' => static::$_post_type ),
            'created'    => array( 'val' => static::$_data_instance->created, 'type' => PDO::PARAM_INT ),
            'modified'   => array( 'val' => $now, 'type' => PDO::PARAM_INT ),
        ) );
        Database::execute( $sql );

        // POST IDを取得
        $post_id = Database::insert_id();

        $meta_schema = static::$_data_instance->get_meta_schema();
        $sql = 'INSERT INTO `cms_meta` ( `post_id`, `meta_key`, `meta_value` ) VALUES( :post_id, :meta_key, :meta_value )';
        foreach( $meta_schema as $key => $type ) {
            static::$_data_instance->$key = $data->$key;

            $is_array  = FALSE;
            $data_type = PDO::PARAM_STR;
            switch( $type ) {
                case Data_model::BOOLEAN:
                    $data_type = PDO::PARAM_BOOL;
                    break;
                case Data_model::INTEGER:
                    $data_type = PDO::PARAM_INT;
                    break;
                case Data_model::XARRAY:
                    $is_array = TRUE;
                    break;
            }

            if( $is_array ) {

                foreach( static::$_data_instance->$key as $val ) {
                    Database::bind( array(
                        'post_id'    => array( 'val' => $post_id, 'type' => PDO::PARAM_INT ),
                        'meta_key'   => array( 'val' => $key ),
                        'meta_value' => array( 'val' => $val ),
                    ) );
                    Database::execute( $sql );
                }

            } else {
                Database::bind( array(
                    'post_id'    => array( 'val' => $post_id, 'type' => PDO::PARAM_INT ),
                    'meta_key'   => array( 'val' => $key ),
                    'meta_value' => array( 'val' => static::$_data_instance->$key, 'type' => $data_type ),
                ) );
                Database::execute( $sql );
            }

        }

        return $post_id;
    }

    /**
     * UPDATE
     */
    public static function update( $post_id, $data ) {
        if(! static::$_data_instance ) {
            $class = static::$_data_model;
            static::$_data_instance = new $class();
        }

        if(! $data->visible ) {
            $data->visible = FALSE;
        }

        $schema = static::$_data_instance->get_schema();
        $sqls = array();
        foreach( $schema as $key => $type ) {
            if( isset( $data->$key ) ) {
                static::$_data_instance->$key = $data->$key;
                $key = Database::escape( $key );
                $sqls[] = sprintf( '`%s` = :%s', $key, $key );
                Database::bind( $key, static::$_data_instance->$key );
            }
        }
        $sql = 'UPDATE `cms_post` SET '.implode( ', ', $sqls ).' WHERE `post_id` = :post_id';
        Database::bind( 'post_id', $post_id, PDO::PARAM_INT );
        Database::execute( $sql );

        $meta_schema = static::$_data_instance->get_meta_schema();
        $u_sql = 'UPDATE `cms_meta` SET `meta_value` = :meta_value WHERE `post_id` = :post_id AND `meta_key` = :meta_key';
        $i_sql = 'INSERT INTO `cms_meta` ( `post_id`, `meta_key`, `meta_value` ) VALUES( :post_id, :meta_key, :meta_value )';
        $d_sql = 'DELETE FROM `cms_meta` WHERE `post_id` = :post_id AND `meta_key` = :meta_key';
        foreach( $meta_schema as $key => $type ) {
            if( isset( $data->$key ) ) {
                static::$_data_instance->$key = $data->$key;

                $is_array  = FALSE;
                $data_type = PDO::PARAM_STR;
                switch( $type ) {
                    case Data_model::BOOLEAN:
                        $data_type = PDO::PARAM_BOOL;
                        break;
                    case Data_model::INTEGER:
                        $data_type = PDO::PARAM_INT;
                        break;
                    case Data_model::XARRAY:
                        $is_array = TRUE;
                        break;
                }

                if( $is_array ) {

                    Database::bind( array(
                        'post_id'    => array( 'val' => $post_id, 'type' => PDO::PARAM_INT ),
                        'meta_key'   => array( 'val' => $key ),
                    ) );
                    Database::execute( $d_sql );

                    foreach( static::$_data_instance->$key as $val ) {
                        Database::bind( array(
                            'post_id'    => array( 'val' => $post_id, 'type' => PDO::PARAM_INT ),
                            'meta_key'   => array( 'val' => $key ),
                            'meta_value' => array( 'val' => $val ),
                        ) );
                        Database::execute( $i_sql );
                    }

                } else {

                    Database::bind( array(
                        'post_id'    => array( 'val' => $post_id, 'type' => PDO::PARAM_INT ),
                        'meta_key'   => array( 'val' => $key ),
                        'meta_value' => array( 'val' => static::$_data_instance->$key, 'type' => $data_type ),
                    ) );
                    Database::execute( $u_sql );

                    if(! Database::affected_rows() ) {
                        Database::bind( array(
                            'post_id'    => array( 'val' => $post_id, 'type' => PDO::PARAM_INT ),
                            'meta_key'   => array( 'val' => $key ),
                            'meta_value' => array( 'val' => static::$_data_instance->$key, 'type' => $data_type ),
                        ) );
                        Database::execute( $i_sql );
                    }

                }
            }
        }

        return $post_id;
    }

    /**
     * DELETE
     */
    public static function delete( $post_id ) {
        $sql = 'UPDATE `cms_post` SET `valid` = 0 WHERE `post_id` = :post_id';
        Database::bind( 'post_id', $post_id, PDO::PARAM_INT );
        Database::execute( $sql );

        return (boolean) Database::affected_rows();
    }
}


/**
 * データモデルクラス
 */
class Data_model_post extends Data_model {
    protected static $_schema = array(
        'post_id'    => self::INTEGER,
        'post_title' => self::STRING,
        'valid'      => self::BOOLEAN,
        'visible'    => self::BOOLEAN,
        'post_type'  => self::STRING,
        'created'    => self::INTEGER,
        'modified'   => self::INTEGER,
    );
    protected static $_meta_schema = array(
    );

    protected $_data = array(
        'valid'   => TRUE,
        'visible' => TRUE,
    );

    function __construct() {
        parent::__construct();
    }

    function __set( $key, $val ) {
        switch( $key ) {
            case 'created':
            case 'modified':
                if(! is_numeric( $val ) ) {
                    $val = strtotime( $val );
                }
                break;
        }

        parent::__set( $key, $val );
    }

    function __get( $key ) {
        if( isset( $this->_data[ $key ] ) )
            return $this->_data[ $key ];
        if( isset( static::$_schema[ $key ] ) )
            return NULL;
        if( isset( static::$_meta_schema[ $key ] ) )
            return NULL;

        self::push_error( 'get', 'Invalid Arguments' );
    }

    protected function check_schema( $key ) {
        if( isset( static::$_schema[ $key ] ) )
            return static::$_schema[ $key ];
        if( isset( static::$_meta_schema[ $key ] ) )
            return static::$_meta_schema[ $key ];

        self::push_error( 'set', 'Invalid Arguments' );
    }

    public static function get_meta_schema() {
        return static::$_meta_schema;
    }
}




