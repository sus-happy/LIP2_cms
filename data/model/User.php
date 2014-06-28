<?php

/**
 * モデルクラス：ユーザデータ
 *
 * @package Model
 * @author SUSH
 * @version 0.0.1
 */

class User_model extends Model {

    const USER_ENABLED  = 1;
    const USER_DISABLED = 2;

    /**
     * ユーザ一覧を取得する
     *
     * @return array $users
     */
    public static function get_list() {
        $sql = 'SELECT * FROM `cms_user` WHERE `valid` = 1 ORDER BY `orderby` ASC';
        $rs  = Database::execute( $sql );

        $users = array();
        foreach( $rs as $row ) {
            $users[] = $row;
        }

        return $users;
    }

    /**
     * ユーザIDとユーザ名から詳細を取得する
     */
    public static function get_detail( $user_id, $user_name ) {
        $sql = 'SELECT * FROM `cms_user` WHERE `user_id` = :user_id AND  `user_name` = :user_name AND `valid` = :valid';
        Database::bind( array(
            'user_id'   => array( 'val' => $user_id, 'type' => PDO::PARAM_INT ),
            'user_name' => array( 'val' => $user_name ),
            'valid'     => array( 'val' => self::USER_ENABLED ),
        ) );
        return Database::execute( $sql )->fetch();
    }

    /**
     * ユーザ名とパスワードから詳細を取得する
     */
    public static function get_detail_from_name( $user_name, $password ) {
        $sql = 'SELECT * FROM `cms_user` WHERE `user_name` = :user_name AND `password` = :password AND `valid` = :valid';
        Database::bind( array(
            'user_name' => array( 'val' => $user_name ),
            'password'  => array( 'val' => $password ),
            'valid'     => array( 'val' => self::USER_ENABLED ),
        ) );
        return Database::execute( $sql )->fetch();
    }

    /**
     * ユーザを追加する
     */
    public static function insert( $data ) {
        $sql = 'INSERT INTO
                    `cms_user`
                        ( `user_name`, `display_name`, `password`, `salt`, `created`, `modified`, `valid` )
                    VALUES
                        ( :user_name, :display_name, :password, :salt, :created, :modified, :valid )';
        Database::bind( array(
            'user_name'    => array( 'val' => $data['user_name'] ),
            'display_name' => array( 'val' => $data['display_name'] ),
            'password'     => array( 'val' => $data['password'] ),
            'salt'         => array( 'val' => $data['salt'] ),
            'created'      => array( 'val' => time(), 'type' => PDO::PARAM_INT ),
            'modified'     => array( 'val' => time(), 'type' => PDO::PARAM_INT ),
            'valid'        => array( 'val' => self::USER_ENABLED, 'type' => PDO::PARAM_INT ),
        ) );
        Database::execute( $sql );
    }

    /**
     * パスワードを変更する
     */
    public static function change_password( $pass ) {
        $id   = Auth::get_param( 'user_id' );
        $user = Auth::get_param( 'user_name' );
        $salt = md5( uniqid( rand(), 1 ) );

        $sql = 'UPDATE `cms_user` SET `password` = :password, `salt` = :salt, `modified` = :modified WHERE `user_id` = :user_id';
        Database::bind( array(
            'password'     => array( 'val' => Auth::crypt( $pass, $salt, $user ) ),
            'salt'         => array( 'val' => $salt ),
            'modified'     => array( 'val' => time(), 'type' => PDO::PARAM_INT ),
            'user_id'      => array( 'val' => $id ),
        ) );

        Database::execute( $sql );
    }

}