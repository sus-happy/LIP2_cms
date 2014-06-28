<?php View::show( 'frame/header' ) ?>
<div id="search" class="main_block">

    <h1>ニュース管理<small><?php _e( $label ) ?></small></h1>

    <?php if( isset( $message ) ) _ea( $message['cont'], $message['type'] ) ?>

    <form action="" method="post">
        <div class="section">

            <?php if( Validator::get_check_error() ): ?>
            <div class="alert alert-danger">
                <div class="alert-header">
                    <h2>入力エラー</h2>
                </div>
                <ul>
                    <?php foreach( Validator::get_error_text() as $val ): ?>
                    <li><?php _e( $val ) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="form_box">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>表示/非表示</th>
                            <td><label><input type="checkbox" name="visible" value="1"<?php _ec( $post->visible, TRUE, ' checked="checked"' ) ?> /> 表示する</label></td>
                        </tr>
                        <tr>
                            <th>タイトル</th>
                            <td><input type="text" name="post_title" class="text full" value="<?php _e( $post->post_title ) ?>" /></td>
                        </tr>
                        <tr>
                            <th>投稿日</th>
                            <td><input type="text" name="created" class="text datepicker" value="<?php _e( date( 'Y/m/d', $post->created ) ) ?>" /></td>
                        </tr>
                        <tr>
                            <th>カテゴリー</th>
                            <td>
                                <select name="category">
                                    <option value="">カテゴリー選択</option>
                                    <?php foreach( News_model::get_category() as $key => $val ): ?>
                                    <option value="<?php _e( $key ) ?>"<?php _ec( $post->category, $key, ' selected="selected"' ) ?>><?php _e( $val ) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h2>内容</h2>
                <textarea id="content_textarea" name="content" rows="10" class="text full"><?php _e( $post->content ) ?></textarea>
            </div>

        </div>

        <p class="center">
            <button type="submit" class="btn btn-primary">登録</button>
            <a href="<?php _e( Url::site_url( 'news' ) ) ?>" class="btn btn-default">キャンセル</a>
        </p>

        <input type="hidden" name="<?php _e( $nonce_key ) ?>" value="<?php _e( $nonce ) ?>">
    </form>

</div>

<?php View::show( 'frame/sidebar' ) ?>
<?php View::show( 'frame/footer' ) ?>