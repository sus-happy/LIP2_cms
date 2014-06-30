<?php

/**
 * ベースコントローラークラス
 *
 * @package Controller
 * @author SUSH
 * @version 0.0.1
 */

class Controller_Post extends Controller {

    protected static
        $message = array(
            self::MESSAGE_SUCCESS  => array( 'type' => 'success', 'cont' => '登録完了しました' ),
            self::MESSAGE_DELETE   => array( 'type' => 'success', 'cont' => '削除完了しました' ),
            self::MESSAGE_NOTFOUND => array( 'type' => 'danger',  'cont' => '該当案件が見つかりませんでした' ),
            self::MESSAGE_ERROR    => array( 'type' => 'danger',  'cont' => 'エラーが発生しました' ),
        ),
        $_yaml_file,
        $_model,
        $_per_page = 20,
        $_preffix  = 'post',
        $_row_data = array();

    public function __construct() {
        Auth::need_login();

        $model_class = sprintf( '%s_model', ucfirst( static::$_preffix ) );
        self::$_model = new $model_class();
        self::$_yaml_file = sprintf( '%s/yaml/form/%s.yml', Config::get_param( 'global', 'site', 'data' ), ucfirst( static::$_preffix ) );
    }

    public function init( $message = NULL ) {
        $this->lists( 1, $message );
    }

    public function lists( $page = 1, $message = NULL ) {
        Pagenation::set_up( array(
            'path'     => sprintf( '%s/lists', static::$_preffix ),
            'total'    => self::$_model->get_count( Request::get() ),
            'per_page' => static::$_per_page,
            'current'  => $page,
            'param'    => Request::get()
        ) );

        $param = Request::get();
        $param->limit  = static::$_per_page;
        $param->offset = ( Pagenation::get_page()-1 ) * static::$_per_page;
        $data['list'] = self::$_model->get_list( $param );

        if( $message )
            $data['message'] = static::$message[ $message ];

        View::show( sprintf( '%s/lists', static::$_preffix ), $data );
    }

    public function add( $mode = NULL, $id = NULL ) {
        $data['nonce_key'] = sprintf( '%s_add', static::$_preffix );
        $data['label'] = '新規追加';

        if( self::check_nonce( $data['nonce_key'], Request::post( $data['nonce_key'] ) ) ) {

            // バリデーションルール設定
            Validator::set_validate_data( Spicy::loadFile( self::$_yaml_file ) );

            if( Validator::check_validation( Request::post() ) ) {
                // トランザクションスタート
                Database::start_trans();

                // 案件情報追加
                $post_data = Request::post();
                foreach( static::$_row_data as $val ) {
                    $post_data->$val = Request::post( $val, TRUE );
                }
                $post_id = self::$_model->insert( $post_data );

                // トランザクションエンド
                Database::complete_trans();
                //Url::redirect( sprintf( '%s/init/%d', static::$_preffix, self::MESSAGE_SUCCESS ) );
                Url::redirect( sprintf( '%s/edit/%d/%d', static::$_preffix, $post_id, self::MESSAGE_SUCCESS ) );
            }

            $data['post'] = Request::post();
            if( $data['post']->created )
                $data['post']->created = strtotime( $data['post']->created );

        } else {
            if( $mode === 'copy' && ! empty( $id ) ) {
                $data['post'] = self::$_model->get_detail( $id );
            } else {
                $model_data_class = sprintf( '%s_data_model', ucfirst( static::$_preffix ) );
                $data['post'] = new $model_data_class();
                $data['post']->created = time();
            }
        }

        $data['nonce'] = self::make_nonce( $data['nonce_key'] );

        View::show( sprintf( '%s/form', static::$_preffix ), $data );
    }

    public function edit( $id, $message = NULL ) {
        $data['nonce_key'] = sprintf( '%s_edit', static::$_preffix );
        $data['label'] = '編集';

        if( self::check_nonce( $data['nonce_key'], Request::post( $data['nonce_key'] ) ) ) {

            $target_id = Session::get_param( $data['nonce_key'].'_id' );

            if( $target_id ) {
                // バリデーションルール設定
                Validator::set_validate_data( Spicy::loadFile( self::$_yaml_file ) );

                // 入力内容確認
                if( Validator::check_validation( Request::post() ) ) {
                    // トランザクションスタート
                    Database::start_trans();

                    // 問題なければ更新
                    $post_data = Request::post();
                    foreach( static::$_row_data as $val ) {
                        $post_data->$val = Request::post( $val, TRUE );
                    }
                    self::$_model->update( $target_id, $post_data );

                    // トランザクションエンド
                    Database::complete_trans();

                    // 対象IDをセッションから削除
                    Session::remove_param( $data['nonce_key'].'_id' );

                    // リダイレクト
                    // Url::redirect( sprintf( '%s/init/%d', static::$_preffix, self::MESSAGE_SUCCESS ) );
                    Url::redirect( sprintf( '%s/edit/%d/%d', static::$_preffix, $target_id, self::MESSAGE_SUCCESS ) );
                }

                $data['post'] = Request::post();
                if( $data['post']->created )
                    $data['post']->created = strtotime( $data['post']->created );

            } else {
                // 対象IDがセッションに無ければエラー
                Url::redirect( sprintf( '%s/init/%d', static::$_preffix, self::MESSAGE_ERROR ) );
            }

        } else {
            // 入力開始時はDBからデータ取得
            $data['post'] = self::$_model->get_detail( $id );
        }

        // データが空の場合は一覧にリダイレクト
        if(! $data['post'] )
            Url::redirect( sprintf( '%s/init/%d', static::$_preffix, self::MESSAGE_NOTFOUND ) );

        // nonceキーを作成
        Session::set_param( $data['nonce_key'].'_id', $id );
        $data['nonce'] = self::make_nonce( $data['nonce_key'] );

        if( $message )
            $data['message'] = static::$message[ $message ];

        View::show( sprintf( '%s/form', static::$_preffix ), $data );

    }

    public function delete( $id ) {
        $data['nonce_key'] = sprintf( '%s_delete', static::$_preffix );
        $data['label'] = '削除';

        $data['post'] = self::$_model->get_detail( $id );
        // データがからの場合は一覧にリダイレクト
        if(! $data['post'] )
            Url::redirect( sprintf( '%s/init/%d', static::$_preffix, self::MESSAGE_NOTFOUND ) );

        if( self::check_nonce( $data['nonce_key'], Request::post( $data['nonce_key'] ) ) ) {
            $target_id = Session::get_param( $data['nonce_key'].'_id' );

            // 案件情報を削除
            self::$_model->delete( $target_id );

            // 対象IDをセッションから削除
            Session::remove_param( $data['nonce_key'].'_id' );

            // 一覧にリダイレクト
            Url::redirect( sprintf( '%s/init/%d', static::$_preffix, self::MESSAGE_DELETE ) );
        }

        // nonceキーを作成
        Session::set_param( $data['nonce_key'].'_id', $id );
        $data['nonce'] = self::make_nonce( $data['nonce_key'] );
        View::show( sprintf( '%s/delete', static::$_preffix ), $data );

    }

}