<?php View::show( 'frame/header' ) ?>
<div id="search" class="main_block">

    <h1>ニュース管理 <a href="<?php _e( Url::site_url( 'news/add' ) ) ?>" class="btn btn-small btn-primary">新規追加</a></h1>
    <p>ニュースの新規追加・編集・削除を行います。</p>

    <?php if( isset( $message ) ) _ea( $message['cont'], $message['type'] ) ?>

    <div class="section">

        <?php if( Pagenation::get_total() > 0 ): ?>

        <?php Pagenation::view(); ?>

        <div class="table_wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>投稿日</th>
                        <th>タイトル</th>
                        <th>カテゴリー</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $list as $post ): ?>
                    <tr>
                        <td><?php _e( date( 'Y/m/d', $post->created ) ) ?></td>
                        <td><?php _e( $post->post_title ) ?></td>
                        <td><?php _e( News_model::get_category_label( $post->category ) ) ?></td>
                        <td>
                            <a href="<?php _e( Url::site_url( sprintf( 'news/edit/%d', $post->post_id ) ) ) ?>" class="btn btn-small btn-default">編集</a>
                            <a href="<?php _e( Url::site_url( sprintf( 'news/delete/%d', $post->post_id ) ) ) ?>" class="btn btn-small btn-danger">削除</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php Pagenation::view(); ?>

        <?php else: ?>
            <?php _ea( '該当の記事は見つかりませんでした', 'warning' ) ?>
        <?php endif; ?>

    </div>

    <p class="center">
        <a href="<?php _e( Url::site_url( 'news/add' ) ) ?>" class="btn btn-primary">新規追加</a>
        <a href="<?php _e( Url::site_url( '' ) ) ?>" class="btn btn-default">管理トップに戻る</a>
    </p>

</div>

<?php View::show( 'frame/sidebar' ) ?>
<?php View::show( 'frame/footer' ) ?>
