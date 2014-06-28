<?php

require_once( dirname( __FILE__ ).'/Loader.php' );

$news = News_model::get_list( array( 'offset' => 0, 'limit' => 10 ) );

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>公開画面サンプル</title>
</head>
<body>

<h2>ニュース</h2>
<ul>
<?php foreach($news as $n): ?>
    <li><?php _e( $n->post_title ); ?></li>
<?php endforeach; ?>
</ul>

</body>
</html>

