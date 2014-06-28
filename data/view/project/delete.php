<?php View::show( 'frame/header' ) ?>
<div id="search" class="main_block">


    <h1>施工事例管理<small>削除</small></h1>

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
                            <th>キャッチコピー</th>
                            <td><?php _e( $post->post_title ) ?></td>
                        </tr>
                        <tr>
                            <th>投稿日</th>
                            <td><?php _e( date( 'Y/m/d', $post->created ) ) ?></td>
                        </tr>
                        <tr class="top">
                            <th>メイン画像</th>
                            <td>
                                <?php if( $post->main_image ): ?>
                                <img src="<?php _e( Url::site_url( sprintf( 'image/get/%d/300/300', $post->main_image ) ) ) ?>" alt="">
                                <?php else: ?>
                                画像なし
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>事例タイプ</th>
                            <td><?php _e( $post->category ) ?></td>
                        </tr>
                    </tbody>
                </table>

                <h2>基本情報</h2>
                <table class="table">
                    <tbody>
                        <tr>
                            <th>家族構成</th>
                            <td><?php _e( $post->family ) ?> / 大人 <?php _e( $post->adults ) ?>人 : 小人 <?php _e( $post->children ) ?> 人</td>
                        </tr>
                        <tr>
                            <th>築年月</th>
                            <td><?php _e( $post->b_year ) ?> 年 <?php _e( $post->b_month ) ?>月 築</td>
                        </tr>
                        <tr>
                            <th>構造</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th>土地面積</th>
                            <td><?php _e( $post->land_area ) ?> m&#178;</td>
                        </tr>
                        <tr>
                            <th>延床面積</th>
                            <td><?php _e( $post->build_area ) ?> m&#178;</td>
                        </tr>
                    </tbody>
                </table>

                <h2>詳細情報</h2>
                <h3>テンプレート選択</h3>

                <table class="table">
                    <tbody>
                        <tr class="top">
                            <th>テキスト (A)</th>
                            <td><?php _e( nl2br( $post->text_a ) ) ?></td>
                        </tr>
                        <tr class="top">
                            <th>テキスト (B)</th>
                            <td><?php _e( nl2br( $post->text_b ) ) ?></td>
                        </tr>
                        <tr class="top">
                            <th>画像 (C)</th>
                            <td>
                                <?php if( $post->image_c ): ?>
                                <img src="<?php _e( Url::site_url( sprintf( 'image/get/%d/300/300', $post->image_c ) ) ) ?>" alt="">
                                <?php else: ?>
                                画像なし
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr class="top">
                            <th>画像 (D)</th>
                            <td>
                                <?php if( $post->image_d ): ?>
                                <img src="<?php _e( Url::site_url( sprintf( 'image/get/%d/300/300', $post->image_d ) ) ) ?>" alt="">
                                <?php else: ?>
                                画像なし
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr class="top">
                            <th>画像 (E)</th>
                            <td>
                                <?php if( $post->image_e ): ?>
                                <img src="<?php _e( Url::site_url( sprintf( 'image/get/%d/300/300', $post->image_e ) ) ) ?>" alt="">
                                <?php else: ?>
                                画像なし
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr class="top">
                            <th>テキスト (F)</th>
                            <td><?php _e( nl2br( $post->text_f ) ) ?></td>
                        </tr>
                        <tr class="top">
                            <th>テキスト (G)</th>
                            <td><?php _e( nl2br( $post->text_g ) ) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <?php _ea( 'この事例を削除してもよろしいですか？', 'danger' ) ?>

        <p class="center">
            <button type="submit" class="btn btn-primary">登録</button>
            <a href="<?php _e( Url::site_url( 'project' ) ) ?>" class="btn btn-default">キャンセル</a>
        </p>

        <input type="hidden" name="<?php _e( $nonce_key ) ?>" value="<?php _e( $nonce ) ?>">
    </form>

</div>

<?php View::show( 'frame/sidebar' ) ?>
<?php View::show( 'frame/footer' ) ?>