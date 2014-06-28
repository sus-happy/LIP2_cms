;( function( $ ) {
    $( function() {
        $.datepicker.setDefaults( $.datepicker.regional[ "ja" ] );
        $('.datepicker').datepicker( $.datepicker.regional[ "ja" ] );
        $('.datepicker').datepicker( 'option', 'onSelect', function() {
            var that = this;
            setTimeout( function() { $(that).inputDefault( { 'check': true } ); }, 50 );
        } );

        $('.de_toggle').click( function( e ) {
            e.preventDefault();
            $(this).addClass( 'btn-disabled' );
            $(this).attr( 'disabled', 'disabled' );
            $(this).closest( 'form' ).submit();
        } );

        $('.side_block').css( { 'height': getInnerHeight()-51, 'top': 51 } ).find( '.side_inner' ).css( 'padding-top', 0 );
        $(window).resize( function() {
            $('.side_block').height( getInnerHeight()-51 );
        } );
    } );
} )( jQuery );

function getScrollPosition() {
    return (document.documentElement.scrollTop||document.body.scrollTop);
}
function getInnerHeight() {
    return (window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight);
}