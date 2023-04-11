// WhatsApp Cart Button script

jQuery( document ).ready( function($) {

    // add to cart button
    $( '.whatsapp-add-to-cart-button' ).on( 'click', function() {
        var product_name = $( this ).data( 'product-name' );
        var product_price = $( this ).data( 'product-price' );
        var product_url = $( this ).data( 'product-url' );
        var phone_number = $( this ).data( 'phone-number' );
        var chat_template = $( this ).data( 'chat-template' );
        var message = chat_template.replace( '%product_name%', product_name ).replace( '%product_price%', product_price ).replace( '%product_url%', product_url );
        var whatsapp_url = 'https://api.whatsapp.com/send?phone=' + encodeURIComponent( phone_number ) + '&text=' + encodeURIComponent( message );
        window.open( whatsapp_url );
    });

});
