window.img_target = null;

Modal_class.prototype.set_image = function( image_id ) {
    if( window.img_target ) {
        var key = window.img_target.attr( 'data-key' );
        var wrap = window.img_target.closest( 'div.image_wrap' );
        wrap.html( '' ).append( this.make_selected( image_id, key ) );

        $('input[name="'+key+'"]').val( image_id );

        this.hide();
    }
};
Modal_class.prototype.make_selected = function( image_id, key ) {
    return $('<dl>', { 'class': 'img_selected' })
        .append( $('<dt>').append( $('<img>', { 'src': '<?php _e( Url::site_url( 'image/get' ) ) ?>/'+image_id+'/300/300' }) ) )
        .append( $('<dd>').append( $('<a>', { 'class': 'change_image btn btn-small btn-danger', 'href':'#', 'text':'別の画像を選択する', 'data-key': key }) ) );
};
Modal_class.prototype.set_change = function() {
    if( window.img_target ) {
        var key = window.img_target.attr( 'data-key' );
        var wrap = window.img_target.closest( 'div.image_wrap' );
        wrap.html( '' ).append( this.make_changed( key ) );

        $('input[name="'+key+'"]').val( 0 );

        this.hide();
    }
};
Modal_class.prototype.make_changed = function( key ) {
    return $('<a>', { 'class': 'modal_image btn btn-small btn-primary', 'href':'<?php _e( Url::site_url( 'image/add' ) ) ?>', 'text':'画像を選択', 'data-key': key });
};

( function( $ ) {
    $( function() {
        $(document).on('click', 'a.modal_image', function() {
            if(! window.Modal )
                window.Modal = new Modal_class;

            window.img_target = $(this);
            window.Modal.view( $(this).attr('href'), $(this).data('width'), $(this).data('height') );

            return false;
        } );
        $(document).on('click', 'a.change_image', function() {
            if(! window.Modal )
                window.Modal = new Modal_class;

            window.img_target = $(this);
            window.Modal.set_change();

            return false;
        } );
    } );
} )( jQuery );