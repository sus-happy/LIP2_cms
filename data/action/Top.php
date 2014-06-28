<?php

class Top_action extends Controller {

    private static $message;

    public function __construct() {
        Auth::need_login();

        self::$message = array(
            self::MESSAGE_SUCCESS  => array( 'type' => 'success', 'cont' => '完了しました' ),
            self::MESSAGE_DELETE   => array( 'type' => 'success', 'cont' => '削除完了しました' ),
            self::MESSAGE_NOTFOUND => array( 'type' => 'danger',  'cont' => '該当記事が見つかりませんでした' ),
            self::MESSAGE_ERROR    => array( 'type' => 'danger',  'cont' => 'エラーが発生しました' ),
        );
    }

    public function init( $message = NULL ) {
        $data = array();

        View::show( 'top', $data );
    }
}