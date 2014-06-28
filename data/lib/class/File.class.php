<?php

/**
 * ファイルクラス
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 */

class File extends Object {
    /* アップロードディレクトリ */
    private static $up_dir = "";
    /* アップ制限 */
    private static $max_file_size = 2, $file_type = array("jpg", "jpeg", "png", "gif");
    /* アップファイル情報 */
    private static $data = array();
    /* エラーメッセージ */
    private static $error_message = array();

    public static function set_upload_dir( $dir ) {
        if(! is_dir( $dir ) ) {
            if(! @mkdir( $dir, 0777, TRUE ) ) {
                self::push_error( 'start', 'Not Found Session dir' );
            }
        }

        if( is_writable( $dir ) ) {
            self::$up_dir = $dir;
            return TRUE;
        } else return FALSE;
    }
    public static function get_upload_dir() {
        return self::$up_dir;
    }

    public static function set_max_file_size( $size ) {
        if( is_numeric( $size ) ) {
            self::$max_file_size = $size;
            return TRUE;
        } return FALSE;
    }
    public static function set_file_type( $type ) {
        if( is_array( $type ) ) {
            self::$file_type = $type;
            return TRUE;
        } return FALSE;
    }

    public static function setup_data( $name, $data ) {
        self::$data[$name] = $data;
    }
    public static function get_data( $name ) {
        return self::$data[$name];
    }

    public static function upload( $name, $up_name, $label = "ファイル" ) {
        $flag = TRUE;
        self::$error_message[$name] = NULL;

        if ( $_FILES[$name]["size"] !== 0 && ! empty( $_FILES[$name] ) ) {
            $extension = pathinfo($_FILES[$name]["name"], PATHINFO_EXTENSION);
            if( $_FILES[$name]["size"] > self::$max_file_size*1024*1024 ) {
                $flag = false;
                self::$error_message[$name][] .= sprintf( "%sのサイズが大きすぎます", $label );
            }
            if( !empty($limExt) ) {
                if(! in_array($extension, self::$file_type) ) {
                    $flag = false;
                    self::$error_message[$name][] .= sprintf( "%sが指定の型式で選択されていません", $label );
                }
            }

            if( $flag ) {
                $file_place = sprintf( "%s/%s.%s", self::$up_dir, $up_name, $extension );
                if( @move_uploaded_file( $_FILES[$name]["tmp_name"], $file_place ) ) {
                    $data = new File_data();
                    list( $data->width, $data->height, $data->type, $data->attr ) = getimagesize( $file_place );
                    $data->type = image_type_to_mime_type( $data->type );
                    $data->file_name = $_FILES[$name]["name"];
                    $data->name = $up_name.".".$extension;
                    self::setup_data( $name, $data );
                } else {
                    $flag = FALSE;
                    self::$error_message[$name][] = sprintf( "%sのアップロードに失敗しました", $label );
                }
            }
        } else {
            $flag = FALSE;
            self::$error_message[$name][] = sprintf( "%sが選択されていません", $label );
        }
        return $flag;
    }

    public static function delete( $name ) {
        $file_place = sprintf( "%s/%s", self::$up_dir, $name );
        return @ unlink( $file_place );
    }

    public static function get_error_message( $name ) {
        return self::$error_message[$name];
    }
}

class File_data extends Object {
}
