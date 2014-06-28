<?php View::show( 'frame/header' ) ?>

    <div class="panel">
        <p class="center mt20"><img src="<?php _e( Url::site_url( 'resource/frame/logo.png' ) ) ?>" alt="" /></p>

        <h1>ログイン</h1>

        <?php if( $message ) _ea( $message, 'danger' ) ?>

        <form action="" method="post">
            <dl>
                <dt>ユーザID</dt>
                <dd><input class="text full input-lg" type="text" name="user_name" /></dd>
                <dt>パスワード</dt>
                <dd><input class="text full input-lg" type="password" name="password"></dd>
            </dl>
            <p><button type="submit" class="btn btn-primary btn-lg btn-block">ログイン</button><p>
        </form>
    </div>

<?php View::show( 'frame/footer' ) ?>
