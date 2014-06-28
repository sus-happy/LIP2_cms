<?php View::show( 'frame/header' ) ?>

<div class="main_block">
    <div class="panel">
        <h1>ログアウトしました</h1>
        <p><a href="<?php _e( Url::site_url( 'login' ) ) ?>" class="btn btn-primary btn-lg btn-block">ログインページに戻る</a><p>
    </div>
</div>

<?php View::show( 'frame/sidebar' ) ?>
<?php View::show( 'frame/footer' ) ?>
