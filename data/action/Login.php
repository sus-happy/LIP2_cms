<?php

class Login_action extends Controller {
    public function init() {
        if( Auth::is_login() )
            Url::redirect();

        $data = array();

        if( count( $_POST ) >0 ) {
            sleep( Config::get_param( 'auth', 'sleep' ) );

            if( $user = Auth::check( $_POST['user_name'], $_POST['password'] ) ) {
                session_regenerate_id( TRUE );
                Session::set_param( 'auth', array( 'user_id' => $user['user_id'], 'user_name' => $user['user_name'] ) );
                Url::redirect();
            } else {
                $data['message'] = 'ユーザID / パスワードをご確認ください';
            }
        }

        View::show( 'login', $data );
    }
}