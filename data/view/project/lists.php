<?php View::show( 'frame/header' ) ?>
<div id="search" class="main_block">

    <h1>施工事例管理 <a href="<?php _e( Url::site_url( 'project/add' ) ) ?>" class="btn btn-small btn-primary">新規追加</a></h1>
    <p>施工事例の新規追加・編集・削除を行います。</p>

    <?php if( isset( $message ) ) _ea( $message['cont'], $message['type'] ) ?>

    <!--
    <div class="section">
        <form action="" method="get">
            <dl>
                <dt>タイプ別で検索</dt>
                <dd>
                </dd>
            </dl>
            <dl>
                <dt>家族構成</dt>
                <dd>
                    <label>大人 <input type="text" name="" class="text micro"> 人</label>
                    <label>小人 <input type="text" name="" class="text micro"> 人</label>
                </dd>
            </dl>
            <dl>
                <dt>土地面積</dt>
                <dd>
                    <label><input type="text" name="" class="text micro"> m&#178;</label>
                    <label>〜</label>
                    <label><input type="text" name="" class="text micro"> m&#178;</label>
                </dd>
            </dl>
            <dl>
                <dt>延床面積</dt>
                <dd>
                    <label><input type="text" name="" class="text micro"> m&#178;</label>
                    <label>〜</label>
                    <label><input type="text" name="" class="text micro"> m&#178;</label>
                </dd>
            </dl>
            <dl>
                <dt>フリーワード検索</dt>
                <dd>
                    <input type="text" class="text full" name="word" value="<?php _e( Request::get( 'word' ) ) ?>" />
                </dd>
            </dl>
            <div class="clearfix">
                <div class="w65 fleft"><button type="submit" class="btn btn-primary btn-block">検索</button></div>
                <div class="w30 fright"><a class="btn btn-default btn-block" href="<?php _e( Url::site_url( 'project' ) ) ?>">検索条件をクリア</a></div>
            </div>
        </form>
    </div>
    -->

    <div class="section">

        <?php if( Pagenation::get_total() > 0 ): ?>

        <?php Pagenation::view(); ?>

        <div class="table_wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>表示/非表示</th>
                        <th>投稿日</th>
                        <th>画像</th>
                        <th>キャッチコピー</th>
                        <th>カテゴリー</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $list as $post ): ?>
                    <tr<?php _e( $post->visible ? '' : ' class="row-disabled"' ) ?>>
                        <td><?php _e( $post->visible ? '表示' : '非表示' ) ?></td>
                        <td><?php _e( date( 'Y/m/d', $post->created ) ) ?></td>
                        <td>
                            <?php if( $post->main_image ): ?>
                            <img src="<?php _e( Url::site_url( sprintf( 'image/get/%d/100/100', $post->main_image ) ) ) ?>" alt="" />
                            <?php endif; ?>
                        </td>
                        <td><?php _e( $post->post_title ) ?></td>
                        <td>
                            <?php $cats = array(); foreach( $post->category as $cat ) { $cats[] = Project_model::get_category_label( $cat ); } _e( $cats, '、' ) ?>
                        </td>
                        <td>
                            <a href="<?php _e( Url::site_url( sprintf( 'project/add/copy/%d', $post->post_id ) ) ) ?>" class="btn btn-small btn-default">複製</a>
                            <a href="<?php _e( Url::site_url( sprintf( 'project/edit/%d', $post->post_id ) ) ) ?>" class="btn btn-small btn-default">編集</a>
                            <a href="<?php _e( Url::site_url( sprintf( 'project/delete/%d', $post->post_id ) ) ) ?>" class="btn btn-small btn-danger">削除</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php Pagenation::view(); ?>

        <?php else: ?>
            <?php _ea( '該当の事例は見つかりませんでした', 'warning' ) ?>
        <?php endif; ?>

    </div>

    <p class="center">
        <a href="<?php _e( Url::site_url( 'project/add' ) ) ?>" class="btn btn-primary">新規追加</a>
        <a href="<?php _e( Url::site_url( '' ) ) ?>" class="btn btn-default">管理トップに戻る</a>
    </p>

</div>

<?php View::show( 'frame/sidebar' ) ?>
<?php View::show( 'frame/footer' ) ?>
