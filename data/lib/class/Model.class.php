<?php

/**
 * モデルクラス
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 * @uses ADOdb
 */

abstract class Model extends Object {

    protected static
        $_data_model    = 'Data_model',
        $_data_instance = NULL;

    /**
     * クエリ分にORDER句を追加する
     *
     * @access public
     * @param string &$sql クエリ文
     * @param integer $order キー
     * @param integer $sort 方向
     */
    public static function add_search( &$sql, $key, $word, $dur = 'ALL' ) {
        $sql_key = self::make_where_key( $key, 'add_search' );

        if( preg_match( '/WHERE/', $sql ) )
            $sql .= sprintf( ' AND `%s` LIKE :%s', $key, $sql_key );
        else
            $sql .= sprintf( ' WHERE `%s` LIKE :%s', $key, $sql_key );

        switch( $dur ) {
            case 'HEAD':
                $word = $word.'%';
            break;
            case 'FOOT':
                $word = '%'.$word;
            break;
            default:
                $word = '%'.$word.'%';
            break;
        }

        Database::bind( $sql_key, $word );
    }
        public static function add_or_search( &$sql, $key, $word, $dur = 'ALL' ) {
            $sql_key = self::make_where_key( $key, 'add_or_search' );

            if( preg_match( '/WHERE/', $sql ) )
                $sql .= sprintf( ' OR `%s` LIKE :%s', $key, $sql_key );
            else
                $sql .= sprintf( ' WHERE `%s` LIKE :%s', $key, $sql_key );

            switch( $dur ) {
                case 'HEAD':
                    $word = $word.'%';
                break;
                case 'FOOT':
                    $word = '%'.$word;
                break;
                default:
                    $word = '%'.$word.'%';
                break;
            }

            Database::bind( $sql_key, $word );
        }
    public static function add_where( &$sql, $key, $word, $diff = '=' ) {
        $sql_key = self::make_where_key( $key, 'add_where' );

        if( preg_match( '/WHERE/', $sql ) )
            $sql .= sprintf( ' AND `%s` %s :%s', $key, $diff, $sql_key );
        else
            $sql .= sprintf( ' WHERE `%s` %s :%s', $key, $diff, $sql_key );

        Database::bind( $sql_key, $word );
    }
        public static function add_or_where( &$sql, $key, $word, $diff = '=' ) {
            $sql_key = self::make_where_key( $key, 'add_or_where' );

            if( preg_match( '/WHERE/', $sql ) )
                $sql .= sprintf( ' OR `%s` %s :%s', $key, $diff, $sql_key );
            else
                $sql .= sprintf( ' WHERE `%s` %s :%s', $key, $diff, $sql_key );

            Database::bind( $sql_key, $word );
        }

        private static function make_where_key( &$key, $label ) {
            if( is_array( $key ) ) {
                $b_k = Database::escape( $key[0] );
                $a_k = Database::escape( $key[1] );

                $key = $b_k.'`.`'.$a_k;
                $sql_key = $label.'_'.$b_k.'_'.$a_k;
            } else {
                $key = Database::escape( $key );
                $sql_key = $label.'_'.$key;
            }

            return $sql_key;
        }

    /**
     * クエリ分にORDER句を追加する
     *
     * @access public
     * @param string &$sql クエリ文
     * @param integer $order キー
     * @param integer $sort 方向
     */
    public static function add_order_by( &$sql, $order, $sort = 'ASC' ) {
        if( preg_match( '/ORDER BY/', $sql ) )
            $sql .= sprintf( ', `%s` %s', $order, $sort );
        else
            $sql .= sprintf( ' ORDER BY `%s` %s', $order, $sort );
    }

    /**
     * クエリ分にLIMIT句を追加する
     *
     * @access public
     * @param string &$sql クエリ文
     * @param mixed &$param パラメータ
     * @param integer $offset 開始行数
     * @param integer $limit 取得行数
     */
    public static function add_offset_limit( &$sql, $offset, $limit ) {
        if( ! empty( $offset ) && ! empty( $limit ) ) {
            $sql .= ' LIMIT :offset, :limit';
            Database::bind( 'offset', $offset, PDO::PARAM_INT );
            Database::bind( 'limit', $limit, PDO::PARAM_INT );
        } else if( ! empty( $limit ) ) {
            $sql .= ' LIMIT :limit';
            Database::bind( 'limit', $limit, PDO::PARAM_INT );
        }
    }

    public static function update( $post_id, $data ) {
    }
}

/**
 * データモデルクラス
 */
abstract class Data_model Extends Object {
    const
        BOOLEAN  = 'boolean',
        INTEGER  = 'integer',
        DOUBLE   = 'double',
        FLOAT    = 'float',
        STRING   = 'string',
        DATETIME = 'datetime',
        XARRAY   = 'array';

    protected $_data          = array();
    protected static $_schema = array();

    function __construct() {
    }

    function __get( $key ) {
        if( isset( $this->_data[ $key ] ) )
            return $this->_data[ $key ];
        if( isset( static::$_schema[ $key ] ) )
            return NULL;

        self::push_error( 'get', 'Invalid Arguments' );
    }

    function __isset( $key ) {
        return isset( $this->_data[$key] );
    }

    function __set( $key, $val ) {
        $schema = $this->check_schema( $key );
        $type = gettype($val);

        if ($schema === self::DATETIME) {
            if ($val instanceof DateTime) {
                $this->_data[$key] = $val;
            } else {
                $this->_data[$key] = new DateTime($val);
            }
            return;
        }

        if ( $type === $schema ) {
            $this->_data[$key] = $val;
            return;
        }

        switch ($schema) {
            case self::BOOLEAN:
                return $this->_data[$key] = (bool)$val;
            case self::INTEGER:
                return $this->_data[$key] = (int)$val;
            case self::DOUBLE:
                return $this->_data[$key] = (double)$val;
            case self::XARRAY:
                if(! is_array( $val ) && ! is_object( $val ) )
                    $val = explode( '||', $val );
                return $this->_data[$key] = (array)$val;
            case self::STRING:
            default:
                return $this->_data[$key] = (string)$val;
        }
    }

    protected function check_schema( $key ) {
        if( isset( static::$_schema[ $key ] ) )
            return static::$_schema[ $key ];

        self::push_error( 'set', 'Invalid Arguments' );
    }

    public static function get_schema() {
        return static::$_schema;
    }

    function to_array() {
        return $this->_data;
    }

    function to_object() {
        return (object) $this->_data;
    }
}