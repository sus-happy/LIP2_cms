<?php

class Logout_action extends Controller {
    public function init() {
        Auth::need_login();

        Session::destroy();

        View::show( 'logout' );
    }
}