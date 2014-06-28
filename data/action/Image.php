<?php

class Image_action extends Controller {

    private static $message = array(
        self::MESSAGE_SUCCESS  => array( 'type' => 'success', 'cont' => '登録完了しました' ),
        self::MESSAGE_DELETE   => array( 'type' => 'success', 'cont' => '削除完了しました' ),
        self::MESSAGE_NOTFOUND => array( 'type' => 'danger',  'cont' => '該当案件が見つかりませんでした' ),
        self::MESSAGE_ERROR    => array( 'type' => 'danger',  'cont' => 'エラーが発生しました' ),
    ), $yaml_file;

    const PER_PAGE = 20;

    function __construct() {
        File::set_upload_dir( Config::get_param( 'global', 'site', 'data' ).'/upfiles/img' );

        self::$yaml_file = Config::get_param( 'global', 'site', 'data' ).'/yaml/form/Image.yml';
    }

    public function init( $message = NULL ) {
        $this->lists( 1, $message );
    }

    public function lists( $page = 1, $message = NULL ) {
        Auth::need_login();

        Pagenation::set_up( array(
            'path'     => 'image/lists',
            'total'    => Image_model::get_count( Request::get() ),
            'per_page' => self::PER_PAGE,
            'current'  => $page,
            'param'    => Request::get()
        ) );

        $data['list'] = Image_model::get_list( Request::get() );

        if( $message )
            $data['message'] = self::$message[ $message ];

        View::show( 'image/lists', $data );
    }

    public function add() {
        Auth::need_login();

        $data['nonce_key'] = 'image_add';
        $data['label'] = '新規追加';

        if( self::check_nonce( $data['nonce_key'], Request::post( $data['nonce_key'] ) ) ) {

            $file_name = 'img_'.date('YmdHis');
            if( File::upload( 'file', $file_name ) ) {
                $data = File::get_data( 'file' );
                $data->post_title = $data->file_name;
                $data->valid   = TRUE;
                $data->created = time();

                // トランザクションスタート
                Database::start_trans();

                // 案件情報追加
                $image_id = Image_model::insert( $data );

                // トランザクションエンド
                Database::complete_trans();
                Url::redirect( sprintf( 'image/init/%d', self::MESSAGE_SUCCESS ) );
            }

            $data['post'] = Request::post();

        } else {
        }

        $data['nonce'] = self::make_nonce( $data['nonce_key'] );

        View::show( 'image/form', $data );
    }

    public function edit( $id ) {
        Auth::need_login();

        $data['nonce_key'] = 'image_edit';
        $data['label'] = '編集';

        if( self::check_nonce( $data['nonce_key'], Request::post( $data['nonce_key'] ) ) ) {

            $target_id = Session::get_param( $data['nonce_key'].'_id' );

            if( $target_id ) {
                // バリデーションルール設定
                Validator::set_validate_data( Spicy::loadFile( self::$yaml_file ) );

                // 入力内容確認
                if( Validator::check_validation( Request::post() ) ) {
                    // トランザクションスタート
                    Database::start_trans();

                    // 問題なければ更新
                    Image_model::update( $target_id, Request::post() );

                    // トランザクションエンド
                    Database::complete_trans();

                    // 対象IDをセッションから削除
                    Session::remove_param( $data['nonce_key'].'_id' );

                    // 一覧にリダイレクト
                    Url::redirect( sprintf( 'image/init/%d', self::MESSAGE_SUCCESS ) );
                }

                $data['post'] = Request::post();
            } else {
                // 対象IDがセッションに無ければエラー
                Url::redirect( sprintf( 'image/init/%d', self::MESSAGE_ERROR ) );
            }

        } else {
            // 入力開始時はDBからデータ取得
            $data['post'] = Image_model::get_detail( $id );
        }

        // データがからの場合は一覧にリダイレクト
        if(! $data['post'] )
            Url::redirect( sprintf( 'image/init/%d', self::MESSAGE_NOTFOUND ) );

        // nonceキーを作成
        Session::set_param( $data['nonce_key'].'_id', $id );
        $data['nonce'] = self::make_nonce( $data['nonce_key'] );
        View::show( 'image/form', $data );

    }

    public function delete( $id ) {
        Auth::need_login();

        $data['nonce_key'] = 'image_delete';
        $data['label'] = '削除';

        $data['post'] = Image_model::get_detail( $id );
        // データがからの場合は一覧にリダイレクト
        if(! $data['post'] )
            Url::redirect( sprintf( 'image/init/%d', self::MESSAGE_NOTFOUND ) );

        if( self::check_nonce( $data['nonce_key'], Request::post( $data['nonce_key'] ) ) ) {
            $target_id = Session::get_param( $data['nonce_key'].'_id' );

            // 案件情報を削除
            Image_model::delete( $target_id );

            // 対象IDをセッションから削除
            Session::remove_param( $data['nonce_key'].'_id' );

            // 一覧にリダイレクト
            Url::redirect( sprintf( 'image/init/%d', self::MESSAGE_DELETE ) );
        }

        // nonceキーを作成
        Session::set_param( $data['nonce_key'].'_id', $id );
        $data['nonce'] = self::make_nonce( $data['nonce_key'] );
        View::show( 'image/delete', $data );

    }

    /*
     * サムネイル表示
     */
    function get( $id, $w = NULL, $h = NULL, $f = FALSE ) {
        $idata = Image_model::get_detail( $id );

        if( $idata ) {
            $file = File::get_upload_dir()."/".$idata->name;
            switch( $idata->type ) {
                case "image/jpg":
                case "image/jpe":
                case "image/jpeg":
                case "image/pjpeg":
                    $gimg = imagecreatefromjpeg( $file );
                break;
                case "image/gif":
                    $gimg = imagecreatefromgif( $file );
                break;
                case "image/png":
                    $gimg = imagecreatefrompng( $file );
                break;
                default:
                    echo "NotImage";
                    exit;
                break;
            }

            $w = is_numeric( $w ) ? $w : $idata->width;
            $h = is_numeric( $h ) ? $h : $idata->height;
            /* 比率無視 */
            if( empty( $f ) ) {
                $sw = $w/$idata->width;
                $sh = $h/$idata->height;
                if( $sw < 1 || $sh < 1 ) {
                    if( $sw <= $sh ) {
                        $h = $idata->height*$sw;
                    } else {
                        $w = $idata->width*$sh;
                    }
                } else {
                    $w = $idata->width;
                    $h = $idata->height;
                }
            }
            $timg = imagecreatetruecolor($w, $h);
            imagecopyresampled(
                $timg,      //貼り付けするイメージID
                $gimg,      //コピーする元になるイメージID
                0,          //int dstX (貼り付けを開始するX座標)
                0,          //int dstY (貼り付けを開始するY座標)
                0,          //int srcX (コピーを開始するX座標)
                0,          //int srcY (コピーを開始するY座標)
                $w,         //int dstW (貼り付けする幅)
                $h,         //int dstH (貼り付けする高さ)
                $idata->width,    //int srcW (コピーする幅)
                $idata->height    //int srcH (コピーする高さ)
            );

            header("Content-type: image/jpeg");
            // 表示
            imagejpeg($timg, NULL, 90);
            imagedestroy( $timg );
        } else {
            echo "NotImage";
        }
    }
}