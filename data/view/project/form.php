<?php View::show( 'frame/header' ) ?>
<div id="product_form" class="main_block">

    <h1>施工事例管理<small><?php _e( $label ) ?></small></h1>

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
                            <th>キャッチコピー</th>
                            <td><input type="text" name="post_title" class="text full" value="<?php _e( $post->post_title ) ?>" /></td>
                        </tr>
                        <tr class="top">
                            <th>コメント</th>
                            <td><textarea name="content" rows="4" class="text full"><?php _e( $post->content ) ?></textarea></td>
                        </tr>
                        <tr>
                            <th>投稿日</th>
                            <td><input type="text" name="created" class="text datepicker" value="<?php _e( date( 'Y/m/d', $post->created ) ) ?>" /></td>
                        </tr>
                        <tr class="top">
                            <th>メイン画像</th>
                            <td>
                                <div class="image_wrap">
                                <?php if( $post->main_image ): ?>
                                    <dl>
                                        <dt><img src="<?php _e( Url::site_url( sprintf( 'image/get/%d/300/300', $post->main_image ) ) ) ?>" alt=""></dt>
                                        <dd><a href="#" class="change_image btn btn-small btn-danger" data-key="main_image">別の画像を選択する</a></dd>
                                    </dl>
                                <?php else: ?>
                                    <a href="<?php _e( Url::site_url( 'image/add' ) ) ?>" class="modal_image btn btn-small btn-primary" data-key="main_image">画像を選択</a>
                                <?php endif; ?>
                                </div>
                                <input type="hidden" name="main_image" value="<?php _e( $post->main_image ) ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th>事例タイプ</th>
                            <td>
                                <?php foreach( Project_model::get_category() as $key => $val ): ?>
                                <label><input type="checkbox" value="<?php _e( $key ) ?>" name="category[]"<?php _ec( $key, $post->category, ' checked="checked"' ) ?>> <?php _e( $val ) ?></label>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h2>基本情報</h2>
                <table class="table">
                    <tbody>
                        <tr>
                            <th>家族構成</th>
                            <td><input type="text" name="family" class="text mini" value="<?php _e( $post->family ) ?>" /> / 大人 <input type="text" name="adults" class="text micro" value="<?php _e( $post->adults ) ?>" />人 : 小人 <input type="text" name="children" class="text micro" value="<?php _e( $post->children ) ?>" /> 人</td>
                        </tr>
                        <tr>
                            <th>築年月</th>
                            <td><input type="text" name="b_year" class="text mini" value="<?php _e( $post->b_year ) ?>" /> 年 <input type="text" name="b_month" class="text micro" value="<?php _e( $post->b_month ) ?>" />月 築</td>
                        </tr>
                        <tr>
                            <th>構造</th>
                            <td>
                                <select name="build_type" id="">
                                    <option value="">選択してください</option>
                                    <?php foreach( Project_model::get_build_type() as $key => $val ): ?>
                                    <option value="<?php _e( $key ) ?>"<?php _ec( $key, $post->build_type, ' selected="selected"' ) ?>><?php _e( $val ) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>土地面積</th>
                            <td><input type="text" name="land_area" class="text mini" value="<?php _e( $post->land_area ) ?>" /> m&#178;</td>
                        </tr>
                        <tr>
                            <th>延床面積</th>
                            <td><input type="text" name="build_area" class="text mini" value="<?php _e( $post->build_area ) ?>" /> m&#178;</td>
                        </tr>
                    </tbody>
                </table>

                <h2>詳細情報</h2>
                <h3>テンプレート選択</h3>
                <p>
                    <?php foreach( Project_model::get_template() as $key => $val ): ?>
                    <label id="<?php printf( 'label_template_%d', $key ) ?>" class="temp_label" for="<?php printf( 'template_%d', $key ) ?>">
                        <input name="template" id="<?php printf( 'template_%d', $key ) ?>" type="radio" value="<?php _e( $key ) ?>"<?php _ec( $key, $post->template, ' checked="checked"' ) ?> /> <?php _e( $val ) ?>
                    </label>
                    <?php endforeach; ?>
                </p>

                <table class="table">
                    <tbody>
                        <tr class="top">
                            <th>テキスト (A)<br />
                            (中見出し)</th>
                            <td><textarea name="text_a" rows="2" class="text full"><?php _e( $post->text_a ) ?></textarea></td>
                        </tr>
                        <tr class="top">
                            <th>テキスト (B)<br />
                            (説明文)</th>
                            <td><textarea name="text_b" rows="4" class="text full"><?php _e( $post->text_b ) ?></textarea></td>
                        </tr>
                        <tr class="top">
                            <th>画像 (C)</th>
                            <td>
                                <div class="image_wrap">
                                <?php if( $post->image_c ): ?>
                                    <dl>
                                        <dt><img src="<?php _e( Url::site_url( sprintf( 'image/get/%d/300/300', $post->image_c ) ) ) ?>" alt=""></dt>
                                        <dd><a href="#" class="change_image btn btn-small btn-danger" data-key="image_c">別の画像を選択する</a></dd>
                                    </dl>
                                <?php else: ?>
                                    <a href="<?php _e( Url::site_url( 'image/add' ) ) ?>" class="modal_image btn btn-small btn-primary" data-key="image_c">画像を選択</a>
                                <?php endif; ?>
                                </div>
                                <input type="hidden" name="image_c" value="<?php _e( $post->image_c ) ?>" />
                            </td>
                        </tr>
                        <tr class="top">
                            <th>画像 (D)</th>
                            <td>
                                <div class="image_wrap">
                                <?php if( $post->image_d ): ?>
                                    <dl>
                                        <dt><img src="<?php _e( Url::site_url( sprintf( 'image/get/%d/300/300', $post->image_d ) ) ) ?>" alt=""></dt>
                                        <dd><a href="#" class="change_image btn btn-small btn-danger" data-key="image_d">別の画像を選択する</a></dd>
                                    </dl>
                                <?php else: ?>
                                    <a href="<?php _e( Url::site_url( 'image/add' ) ) ?>" class="modal_image btn btn-small btn-primary" data-key="image_d">画像を選択</a>
                                <?php endif; ?>
                                </div>
                                <input type="hidden" name="image_d" value="<?php _e( $post->image_d ) ?>" />
                            </td>
                        </tr>
                        <tr class="top">
                            <th>画像 (E)</th>
                            <td>
                                <div class="image_wrap">
                                <?php if( $post->image_e ): ?>
                                    <dl>
                                        <dt><img src="<?php _e( Url::site_url( sprintf( 'image/get/%d/300/300', $post->image_e ) ) ) ?>" alt=""></dt>
                                        <dd><a href="#" class="change_image btn btn-small btn-danger" data-key="image_e">別の画像を選択する</a></dd>
                                    </dl>
                                <?php else: ?>
                                    <a href="<?php _e( Url::site_url( 'image/add' ) ) ?>" class="modal_image btn btn-small btn-primary" data-key="image_e">画像を選択</a>
                                <?php endif; ?>
                                </div>
                                <input type="hidden" name="image_e" value="<?php _e( $post->image_e ) ?>" />
                            </td>
                        </tr>
                        <tr class="top">
                            <th>テキスト (F)<br />
                            (画像(D)の説明)</th>
                            <td><textarea name="text_f" rows="4" class="text full"><?php _e( $post->text_f ) ?></textarea></td>
                        </tr>
                        <tr class="top">
                            <th>テキスト (G)<br />
                            (画像(E)の説明)</th>
                            <td><textarea name="text_g" rows="4" class="text full"><?php _e( $post->text_g ) ?></textarea></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <p class="center">
            <button type="submit" class="btn btn-primary">登録</button>
            <a href="<?php _e( Url::site_url( 'project' ) ) ?>" class="btn btn-default">キャンセル</a>
        </p>

        <input type="hidden" name="<?php _e( $nonce_key ) ?>" value="<?php _e( $nonce ) ?>">
    </form>

</div>

<?php View::show( 'frame/sidebar' ) ?>
<?php
function project_form() {
    printf( '<script type="text/javascript" src="%s"></script>', Url::site_url( 'js/mode/form' ) );
}
View::push_hook( 'foot_js', 'project_form' );

View::show( 'frame/footer' ) ?>