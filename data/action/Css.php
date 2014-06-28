<?php

class Css_action extends Controller {

    function __construct() {
        header( 'Content-type: text/css' );
    }

    function mode( $path ) {
        header("Content-type: text/css");
        View::show( 'css/'.$path );
    }

}