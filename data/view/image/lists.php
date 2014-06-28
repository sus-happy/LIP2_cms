<?php View::show( 'frame/pop_header' ) ?>

    <div>
        <h1>画像選択</h1>

        <p>投稿済みの画像から選択します。</p>


        <?php if( Pagenation::get_total() > 0 ): ?>

        <?php Pagenation::view(); ?>

        <table class="table mb20">
            <tbody>
                <?php foreach( $list as $post ): ?>
                <tr>
                    <td><img src="<?php _e( Url::site_url( sprintf( 'image/get/%d/200/200', $post->post_id ) ) ) ?>" alt=""></td>
                    <td>
                        <dl>
                            <dt><?php _e( $post->post_title ) ?></dt>
                            <dd><?php _e( sprintf( '%d x %d', $post->width, $post->height ) ) ?></dd>
                            <dd><a class="btn btn-small btn-primary" href="javascript:window.parent.Modal.set_image(<?php _e( $post->post_id ) ?>)">この画像を使用</a></dd>
                        </dl>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php Pagenation::view(); ?>

        <?php else: ?>
            <?php _ea( 'アップロードされている画像はありません', 'warning' ) ?>
        <?php endif; ?>


        <p class="center">
            <a href="<?php _e( Url::site_url( 'image/add' ) ) ?>" class="btn btn-primary">新規アップロード</a>
            <a href="javascript:window.parent.Modal.hide();" class="btn btn-default">キャンセル</a>
        <p>
    </div>

<?php View::show( 'frame/pop_footer' ) ?>
