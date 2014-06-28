<?php

class User_action extends Controller {

    private static $message = array(
        self::MESSAGE_ADD    => array( 'type' => 'success', 'cont'=> 'パスワードの再設定が完了しました' ),
        self::MESSAGE_ERROR  => array( 'type' => 'danger', 'cont'=> '現在のパスワードの入力が不正です' ),
        self::MESSAGE_FORMAT_FAILED  => array( 'type' => 'danger', 'cont'=> '新しいパスワードの入力間違いがありました' ),
    );

    public function init( $message_id = NULL ) {
        Auth::need_login();

        $data['nonce_key'] = 'user_password';

        if( self::check_nonce( $data['nonce_key'], Request::post( $data['nonce_key'] ) ) ) {

            // 現在のパスワードの確認
            $user_name = Auth::get_param( 'user_name' );
            $now_pass  = Request::post( 'now_pass' );
            if(! Auth::check( Auth::get_param( 'user_name' ), Request::post( 'now_pass' ) ) ) {
                Url::redirect( sprintf( 'user/init/%d', self::MESSAGE_ERROR ) );
            }

            // 新しいパスワードの確認
            $new_pass = trim( Request::post( 'new_pass' ) );
            $re_pass  = trim( Request::post( 're_pass' ) );
            if( $new_pass !== $re_pass || empty( $new_pass ) ) {
                Url::redirect( sprintf( 'user/init/%d', self::MESSAGE_FORMAT_FAILED ) );
            }

            User_model::change_password( $new_pass );
            Url::redirect( sprintf( 'user/init/%d', self::MESSAGE_ADD ) );
        }

        if( isset( self::$message[$message_id] ) )
            $data['message'] = self::$message[ $message_id ];
        $data['nonce'] = self::make_nonce( $data['nonce_key'] );

        View::show( 'user/form', $data );
    }

    /* ユーザ追加 : 通常は使わない */
    public function create( $user, $display, $pass ) {

        $salt = md5( uniqid( rand(), 1 ) );

        User_model::insert( array(
            'user_name'    => $user,
            'display_name' => urldecode( $display ),
            'password'     => Auth::crypt( $pass, $salt, $user ),
            'salt'         => $salt,
        ) );

    }
    /**/
}