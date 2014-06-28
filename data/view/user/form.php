<?php View::show( 'frame/header' ) ?>

<div class="main_block">
    <h1>パスワード変更</h1>

    <?php if( isset( $message ) ) _ea( $message['cont'], $message['type'] ) ?>

    <div class="section panel">
        <form action="" method="post">
            <dl>
                <dt>現在のパスワード</dt>
                <dd>
                    <input type="password" class="text full" name="now_pass" />
                </dd>
                <dt>新しいパスワード</dt>
                <dd>
                    <input type="password" class="text full" name="new_pass" />
                </dd>
                <dt>もう一度ご入力下さい</dt>
                <dd>
                    <input type="password" class="text full" name="re_pass" />
                </dd>
            </dl>
            <p><button type="submit" class="btn btn-primary btn-block">登録</button><p>
            <input type="hidden" name="<?php _e( $nonce_key ) ?>" value="<?php _e( $nonce ) ?>">
        </form>
    </div>

    <p class="center">
        <a href="<?php _e( Url::site_url( '' ) ) ?>" class="btn btn-default">管理トップに戻る</a>
    </p>
</div>

<?php View::show( 'frame/sidebar' ) ?>
<?php View::show( 'frame/footer' ) ?>
