<?php View::show( 'frame/pop_header' ) ?>

    <div>
        <h1>新規画像追加</h1>

        <p>新しく画像をアップロードします。</p>

        <form action="" method="post" enctype="multipart/form-data" class="mb20">
            <fieldset>
                <p class="center"><input type="file" name="file" /></p>
                <p class="center">
                    <button type="submit" class="btn btn-primary">アップロード</button>
                </p>
            </fieldset>
            <input type="hidden" name="<?php _e( $nonce_key ) ?>" value="<?php _e( $nonce ) ?>">
        </form>

        <p class="center">
            <a href="<?php _e( Url::site_url( 'image' ) ) ?>" class="btn btn-primary">アップロード済みファイル一覧</a>
            <a href="javascript:window.parent.Modal.hide();" class="btn btn-default">キャンセル</a>
        <p>
    </div>

<?php View::show( 'frame/pop_footer' ) ?>
