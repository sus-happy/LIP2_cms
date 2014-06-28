<?php View::show( 'frame/header' ) ?>

<div id="top_page" class="main_block">
    <?php if( isset( $message ) ) _ea( $message['cont'], $message['type'] ) ?>

    <h1>管理トップ</h1>

    <ul>
        <li><a href="<?php _e( Url::site_url( 'project' ) ) ?>" class="btn btn-lg btn-default btn-block">施工事例管理</a></li>
        <li><a href="<?php _e( Url::site_url( 'news' ) ) ?>" class="btn btn-lg btn-default btn-block">ニュース</a></li>
        <li><a href="<?php _e( Url::site_url( 'user' ) ) ?>" class="btn btn-lg btn-default btn-block">パスワード再設定</a></li>
    </ul>
</div>

<?php View::show( 'frame/sidebar' ) ?>
<?php View::show( 'frame/footer' ) ?>
