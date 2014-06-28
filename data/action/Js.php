<?php

class Js_action extends Controller {

    function __construct() {
        header("Content-type: application/x-javascript");
    }

    function mode( $path ) {
        View::show( 'js/'.$path );
    }

}