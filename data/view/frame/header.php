<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php _e( Config::get_param( 'global', 'site', 'name' ) ) ?></title>
    <link rel="stylesheet" href="<?php _e( Url::site_url( 'css/mode/base' ) ) ?>">
    <?php View::call_hook( 'head_css' ); ?>
</head>
<body class="<?php _e( Auth::is_login() ? 'logined' : '' ) ?>">

<?php if( Auth::is_login() ): ?>
<div id="header">
    <div class="header header-brand">
        <a href="<?php _e( Url::site_url() ) ?>"><img src="<?php _e( Url::site_url( 'resource/frame/logo.png' ) ) ?>" alt="" /> <?php _e( Config::get_param( 'global', 'site', 'name' ) ) ?></a>
    </div>

    <p class="header header-right">
        <span><a href="<?php _e( Url::site_url( 'user' ) ) ?>"><?php _e( Auth::get_param( 'display_name' ) ) ?></a></span>
        <span><a href="<?php _e( Config::get_param( 'global', 'site', 'pub' ) ) ?>">サイトを確認</a></span>
        <a href="<?php _e( Url::site_url( 'logout' ) ) ?>">ログアウト</a>
    </p>
</div>
<?php endif; ?>

<div id="content">
