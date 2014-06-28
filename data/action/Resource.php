<?php

class Resource_action extends Controller {

    function __construct() {
    }

    function __call( $name, $args ) {
        if(! empty( $args ) ) {
            $file = Config::get_param( 'global', 'site', 'data' ).'/resource/'.$name.'/'.implode( '/', $args );
        } else
            $file = Config::get_param( 'global', 'site', 'data' ).'/resource/'.$name;

        if(! file_exists( $file ) ) {
            self::push_error( 'not found', 'File not existed'.$file );
            return FALSE;
        }

        $path = pathinfo( $file, PATHINFO_EXTENSION );
        switch ( $path ) {
            case 'png':
                header( 'Content-type: image/png' );
                break;
            case 'jpg':
                header( 'Content-type: image/jpeg' );
                break;
            case 'gif':
                header( 'Content-type: image/gif' );
                break;
            case 'js':
                header( 'Content-type: application/x-javascript' );
                break;
            case 'css':
                header( 'Content-type: text/css' );
                break;
            case 'pdf':
                header( 'Content-type: application/pdf' );
                break;
            default:
                header( 'Content-type: text/plain' );
                break;
        }

        echo file_get_contents( $file );
    }

}