;var Modal_class = function() {
    var that = this;

    this.cover = $( '<div>', { 'id': 'modal_cover' } ).css( 'opacity', 0.8 ).appendTo( 'body' );
    this.modal = $( '<div>', { 'id': 'modal_box' } ).append( $( '<iframe>', { 'frameborder': 0 } ) ).appendTo( 'body' );

    this.w = { 'ih':0, 'oh':0 };
    this.pad = 50;
    this.resize();

    this.cover.click( function() {
        // that.hide();
    } );
};

Modal_class.prototype = {
    'view': function( url, width, height ) {
        var that = this;

        if(! width )
            width = 600;
        if(! height )
            height = 600;

        var top = (document.documentElement.scrollTop||document.body.scrollTop) + this.pad;
        if( top + height > this.w.oh )
            top = this.w.oh - height - this.pad;

        this.resize();
        this.cover.show();
        this.modal.css( { 'margin-left': -width/2, 'top': top } ).find( 'iframe' ).width( width ).height( height ).attr( 'src', url ).load( function() {
            that.modal.show();
        } );
    },
    'hide': function() {
        this.modal.hide();
        this.cover.hide();
    },
    'resize': function() {
        this.w.oh = Math.max.apply( null, [document.body.clientHeight , document.body.scrollHeight, document.documentElement.scrollHeight, document.documentElement.clientHeight] );
        this.w.ih = (window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight);
        this.cover.height( this.w.oh );
    }
};

window.Modal = null;
( function( $ ) {
    $( function() {
        $('a.modal').click( function() {
            if(! window.Modal )
                window.Modal = new Modal_class;

            window.Modal.view( $(this).attr('href'), $(this).data('width'), $(this).data('height') );

            return false;
        } );
    } );
} )( jQuery );