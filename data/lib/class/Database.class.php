<?php

/**
 * データベース接続クラス
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 * @uses PDO
 */

class Database extends Object {

    private static $dbh, $last_query, $class_object = NULL, $bind_value = array();

    /**
     * データベース接続
     *
     * @access public
     */
    public static function connect() {
        $d = Config::get_param( 'database' );
        switch( $d['driver'] ) {
            case 'sqlite':
                $dsn = sprintf( '%s:%s', $d['driver'], $d['dbname'] );
                try {
                    self::$dbh = new PDO( $dsn, $d['user'], $d['pass'] );
                } catch ( PDOException $e ) {
                    self::push_error( 'connect', $e->getMessage() );
                }

                Driver_SQLite::init( self::$dbh );
                break;
            default:
                $dsn = sprintf( '%s:dbname=%s;host=%s',
                    $d['driver'],
                    $d['dbname'],
                    $d['host']
                );
                try {
                    self::$dbh = new PDO( $dsn, $d['user'], $d['pass'] );
                } catch ( PDOException $e ) {
                    self::push_error( 'connect', $e->getMessage() );
                }
                break;
        }
        self::$dbh->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
    }

    /**
     * プリペアドステートメント
     *
     * @access public
     * @param $sql SQLクエリ
     */
    public static function prepare( $sql ) {
        try {
            if(! self::$last_query = self::$dbh->prepare( $sql ) ) {
                $error = self::$dbh->errorInfo();
                self::push_error( 'execute', $error[2].' : '.$sql );
            }
        } catch( PDOException $e ) {
            self::push_error( 'prepare', $e->getMessage().' : '.$sql );
        }
    }

    /**
     * 変数をセット
     *
     * @access public
     * @param $key 変数名
     * @param $val 値
     * @param $type = PDO::PARAM_STR 変数型
     */
    public static function bind( $key, $val = NULL, $type = PDO::PARAM_STR ) {
        if(! is_array( $key ) ) {
            self::$bind_value[ $key ] = array( 'val' => $val, 'type' => $type );
        } else {
            foreach ( $key as $k => $v ) {
                if(! isset( $v['type'] ) )
                    $v['type'] = $type;
                self::$bind_value[ $k ] = array( 'val' => $v['val'], 'type' => $v['type'] );
            }
        }
    }

    public static function set_class_object( $class_name ) {
        self::$class_object = (string) $class_name;
    }

    /**
     * クエリ実行
     *
     * @access public
     * @param $sql = NULL SQLクエリ：入力されていればprepareする
     * @return PDOStatement $rs 実行結果
     */
    public static function execute( $sql = NULL ) {
        if( strlen( $sql ) ) {
            self::prepare( $sql );
        }

        foreach( self::$bind_value as $key => $val ) {
            self::$last_query->bindValue( $key, $val['val'], $val['type'] );
        }

        if( self::$class_object ) {
            self::$last_query->setFetchMode( PDO::FETCH_CLASS, self::$class_object );
        }

        if(! self::$last_query->execute() ) {
            $error = self::$last_query->errorInfo();
            self::push_error( 'execute', $error[2].' : '.$sql );
        }

        self::$bind_value = array();
        self::$class_object = NULL;
        return self::$last_query;
    }
        public static function debug( $sql ) {
            var_dump( $sql, self::$bind_value );
            exit();
        }

    /**
     * クオート
     */
    public static function quote( $str ) {
        return self::$dbh->quote( $str );
    }
    public static function escape( $str ) {
        return addslashes( $str );
    }

    /**
     * 影響行数
     *
     * @access public
     * @return integer 影響した行数
     */
    public static function affected_rows() {
        return self::$last_query->rowCount();
    }

    /**
     * LAST INSERT ID
     *
     * @access public
     * @return integer 影響した行数
     */
    public static function insert_id() {
        return self::$dbh->lastInsertId();
    }

    /**
     * トランザクション開始
     *
     * @access public
     */
    public static function start_trans() {
        self::$dbh->beginTransaction();
    }

    /**
     * トランザクション中止
     *
     * @access public
     */
    public static function fail_trans() {
        self::$dbh->rollBack();
    }

    /**
     * トランザクション完了
     *
     * @access public
     */
    public static function complete_trans() {
        self::$dbh->commit();
    }
}