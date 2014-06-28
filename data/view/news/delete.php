<?php View::show( 'frame/header' ) ?>
<div id="search" class="main_block">

    <h1>ニュース管理<small>削除</small></h1>

    <?php if( isset( $message ) ) _ea( $message['cont'], $message['type'] ) ?>

    <form action="" method="post">
        <div class="section">

            <div class="form_box">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>表示/非表示</th>
                            <td><?php _e( $post->visible ? '表示' : '非表示' ) ?></td>
                        </tr>
                        <tr>
                            <th>タイトル</th>
                            <td><?php _e( $post->post_title ) ?></td>
                        </tr>
                        <tr>
                            <th>投稿日</th>
                            <td><?php _e( date( 'Y/m/d', $post->created ) ) ?></td>
                        </tr>
                    </tbody>
                </table>

                <h2>基本情報</h2>

                <div class="cont_wrap">
                    <?php _e( $post->content ) ?>
                </div>
            </div>

        </div>

        <?php _ea( 'この記事を削除してもよろしいですか？', 'danger' ) ?>

        <p class="center">
            <button type="submit" class="btn btn-primary">登録</button>
            <a href="<?php _e( Url::site_url( 'news' ) ) ?>" class="btn btn-default">キャンセル</a>
        </p>

        <input type="hidden" name="<?php _e( $nonce_key ) ?>" value="<?php _e( $nonce ) ?>">
    </form>

</div>

<?php View::show( 'frame/sidebar' ) ?>
<?php View::show( 'frame/footer' ) ?>