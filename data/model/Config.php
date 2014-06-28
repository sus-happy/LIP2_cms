<?php

/**
 * モデルクラス：設定データ
 *
 * @package Model
 * @author SUSH
 * @version 0.0.1
 */

class Config_model extends Model {

    /**
     * パラメータ取得
     *
     * @param string $key 設定情報キー
     * @return string 設定内容
     */
    static function get_param( $key ) {
        $sql = 'SELECT `config_val` FROM `cms_config` WHERE `config_key` = :config_key';
        Database::bind( 'config_key', $key );
        $rs = Database::execute( $sql );

        $row = $rs->fetch();
        return $row['config_val'];
    }

    /**
     * パラメータ群取得
     *  / 「key.***」という設定情報を配列で取得する
     *
     * @param string $key 設定情報キー
     * @return array $result 設定内容群
     */
    static function get_params( $key ) {
        $sql = 'SELECT `config_key`, `config_val` FROM `cms_config` WHERE `config_key` LIKE :config_key';
        Database::bind( 'config_key', $key.'.%' );
        $rs = Database::execute( $sql );

        $result = array();
        foreach( $rs as $row ) {
            $keis = explode( '.', $row['config_key'] );
            // 先頭のキーを削除する
            array_shift( $keis );
            self::make_param( $result, $keis, $row['config_val'] );
            // $result[ str_replace( $key.'.', '', $row['config_key'] ) ] = $row['config_val'];
        }

        return $result;
    }
        /**
         * パラメータ生成
         *
         * @param array &$data 生成パラメータ
         * @param array $key 設定情報キー
         * @param string $val 設定内容
         */
        private static function make_param( &$data, $key, $val ) {
            $k = array_shift( $key );
            if( count( $key ) ) {
                $data[ $k ] = self::make_param( $key, $val );
            } else {
                $data[ $k ] = $val;
            }
        }

    /**
     * パラメータを更新
     *  / 対象が無かった場合は挿入する
     *
     * @param string $key 設定情報キー
     * @param string $key 設定内容
     */
    static function update_param( $key, $val ) {
        if( is_array( $val ) || is_object( $val ) ) {
            foreach( $val as $k => $v ) {
                self::update_param( $key.'.'.$k, $v );
            }
        } else {
            $sql = 'UPDATE `cms_config` SET `config_val` = :config_val, `modified` = :modified WHERE `config_key` = :config_key';
            Database::bind( array(
                'config_val' => array( 'val' => $val ),
                'modified'   => array( 'val' => date('Y-m-d H:i:s') ),
                'config_key' => array( 'val' => $key ),
            ) );

            $rs = Database::execute( $sql );
            if(! Database::affected_rows() ) {
                self::insert_param( $key, $val );
            }
        }
    }

    /**
     * パラメータを挿入
     *
     * @param string $key 設定情報キー
     * @param string $key 設定内容
     */
    static function insert_param( $key, $val ) {
        $sql = 'INSERT INTO `cms_config`( `config_key`, `config_val`, `created` ) VALUES( :config_key, :config_val, NULL )';
        Database::bind( array(
            'config_key' => array( 'val' => $key ),
            'config_val' => array( 'val' => $val ),
        ) );

        $rs = Database::execute( $sql );
    }

}