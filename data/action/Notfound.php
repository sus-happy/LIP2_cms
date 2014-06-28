<?php

class Notfound_action extends Controller {

    function error() {
        header("HTTP/1.0 404 Not Found");
        echo "Application Not Found.<br />";
    }

}