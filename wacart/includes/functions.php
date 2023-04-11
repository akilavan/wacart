<?php
/**
 * Plugin functions.
 *
 * @package WhatsApp_Cart_Button
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add "Add to WhatsApp Cart" button to WooCommerce product page.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string Button markup.
 */
function whatsapp_cart_button_shortcode( $atts ) {
    // retrieve plugin settings
    $phone_number = get_option( '_whatsapp_cart_phone_number' );
    $chat_template = get_option( '_whatsapp_cart_chat_template' );

    // get product details
    global $product;
    $product_name = $product->get_name();
    $product_price = $product->get_price();
    $product_url = get_permalink( $product->get_id() );

    // create message from chat template and product details
    $message = str_replace( '%product_name%', $product_name, $chat_template );
    $message = str_replace( '%product_price%', $product_price, $message );
    $message = str_replace( '%product_url%', $product_url, $message );

    // construct WhatsApp API URL
    $whatsapp_url = 'https://api.whatsapp.com/send?phone=' . urlencode( $phone_number ) . '&text=' . urlencode( $message );

    // create "Add to WhatsApp Cart" button
    $button_text = __( 'Add to WhatsApp Cart', 'whatsapp-cart-button' );
    $button = '<a href="' . esc_url( $whatsapp_url ) . '" class="button whatsapp-add-to-cart-button" data-product-name="' . esc_attr( $product_name ) . '" data-product-price="' . esc_attr( $product_price ) . '" data-product-url="' . esc_attr( $product_url ) . '" data-phone-number="' . esc_attr( $phone_number ) . '" data-chat-template="' . esc_attr( $chat_template ) . '">' . esc_html( $button_text ) . '</a>';

    return $button;
}
add_shortcode( 'whatsapp_cart_button', 'whatsapp_cart_button_shortcode' );

/**
 * Add product name and URL to Order Notes on checkout page.
 *
 * @param WC_Order $order Order object.
 */
function whatsapp_cart_add_product_info_to_order_notes( $order ) {
    // retrieve cart contents
    $items = $order->get_items();

    // loop through order items
    foreach ( $items as $item_id => $item ) {
        // get product information from order item meta
        $product_name = $item->get_meta( 'whatsapp_cart_product_name' );
        $product_url = $item->get_meta( 'whatsapp_cart_product_url' );

        // add product information to Order Notes
        if ( ! empty( $product_name ) && ! empty( $product_url ) ) {
            echo '<p>' . sprintf( __( 'I would like to order %s, %s', 'whatsapp-cart-button' ), '<strong>' . $product_name . '</strong>', '<a href="' . esc_url( $product_url ) . '">' . esc_html( $product_url ) . '</a>' ) . '</p>';
        }
    }
}
add_action( 'woocommerce_order_details_after_order_table', 'whatsapp_cart_add_product_info_to_order_notes', 10, 1 );


/**
 * Add "Add to WhatsApp Cart" button to WooCommerce product loop and add product information to cart item data.
 *
 * @param string $button Button HTML.
 * @param array $product Product data.
 *
 * @return string Button HTML with added WhatsApp functionality.
 */
function whatsapp_cart_add_to_cart( $button, $product ) {
    // retrieve plugin settings
    $phone_number = get_option( '_whatsapp_cart_phone_number' );
    $chat_template = get_option( '_whatsapp_cart_chat_template' );

    // get product details
    $product_name = $product->get_name();
    $product_price = $product->get_price();
    $product_url = get_permalink( $product->get_id() );

    // create message from chat template and product details
    $message = str_replace( '%product_name%', $product_name, $chat_template );
    $message = str_replace( '%product_price%', $product_price, $message );
    $message = str_replace( '%product_url%', $product_url, $message );

    // construct WhatsApp API URL
    $whatsapp_url = 'https://api.whatsapp.com/send?phone='. urlencode( $phone_number ) . '&text=' . urlencode( $message );

    // add product information to cart item data
    $item_data = array(
        'whatsapp_cart_product_name' => $product_name,
        'whatsapp_cart_product_url'  => $product_url,
    );
    
    // create "Add to WhatsApp Cart" button
    $button_text = __( 'Add to WhatsApp Cart', 'whatsapp-cart-button' );
    $button .= '<a href="' . esc_url( $whatsapp_url ) . '" class="button whatsapp-add-to-cart-button" data-product-name="' . esc_attr( $product_name ) . '" data-product-price="' . esc_attr( $product_price ) . '" data-product-url="' . esc_attr( $product_url ) . '" data-phone-number="' . esc_attr( $phone_number ) . '" data-chat-template="' . esc_attr( $chat_template ) . '">' . esc_html( $button_text ) . '</a>';
    
    return $button;
    }
    add_filter( 'woocommerce_loop_add_to_cart_link', 'whatsapp_cart_add_to_cart', 10, 2 );
    
    /**
    
    Add admin settings page. */ function whatsapp_cart_add_admin_settings() { add_options_page( __( 'WhatsApp Cart Button Settings', 'whatsapp-cart-button' ), __( 'WhatsApp Cart Button', 'whatsapp-cart-button' ), 'manage_options', 'whatsapp_cart_button_settings', 'whatsapp_cart_render_admin_settings' ); } add_action( 'admin_menu', 'whatsapp_cart_add_admin_settings' );
    /**
    
    Render admin settings page. */ function whatsapp_cart_render_admin_settings() { ?> <div class="wrap"> <h1><?php esc_html_e( 'WhatsApp Cart Button Settings', 'whatsapp-cart-button' ); ?></h1> <form method="post" action="options.php"> <?php settings_fields( 'whatsapp_cart_button_settings' ); do_settings_sections( 'whatsapp_cart_button_settings' ); submit_button(); ?> </form> </div> <?php }
    /**
    
    Register admin settings.
    */
    function whatsapp_cart_register_admin_settings() {
    register_setting(
    'whatsapp_cart_button_settings',
    '_whatsapp_cart_phone_number',
    array(
    'type'              => 'string',
    'sanitize_callback' => 'sanitize_text_field',
    'default'           => '',
    )
    );
    
    register_setting(
    'whatsapp_cart_button_settings',
    '_whatsapp_cart_chat_template',
    array(
    'type'              => 'string',
    'sanitize_callback' => 'sanitize_text_field',
    'default'           => __( 'I would like to order %product_name%, %product_url%', 'whatsapp-cart-button' ),
    )
    );
    
    add_settings_section(
    'whatsapp_cart_button_general',
    __( 'General Settings', 'whatsapp-cart-button' ),
    '__return_false',
    'whatsapp_cart_button_settings'
    );
    
    add_settings_field(
    'whatsapp_cart_button_phone_number',
    __( 'WhatsApp Phone Number', 'whatsapp-cart-button' ),
    'whatsapp_cart_render_phone_number_setting',
    'whatsapp_cart_button_settings',
    'whatsapp_cart_button_general'
    );
    
    add_settings_field(
    'whatsapp_cart_button_chat_template',
    __( 'Chat Message Template', 'whatsapp-cart-button' ),
    'whatsapp_cart_render_chat_template_setting',
    'whatsapp_cart_button_settings',
    'whatsapp_cart_button_general'
    );
    }
    add_action( 'admin_init', 'whatsapp_cart_register_admin_settings' );
    
    /**
    
    Render phone number setting field. */ function whatsapp_cart_render_phone_number_setting() { $value = get_option( '_whatsapp_cart_phone_number' ); ?> <input type="text" name="_whatsapp_cart_phone_number" value="<?php echo esc_attr( $value ); ?>" /> <?php }
    /**
    
    Render chat template setting field. */ function whatsapp_cart_render_chat_template_setting() { $value = get_option( '_whatsapp_cart_chat_template' ); ?> <textarea name="_whatsapp_cart_chat_template"><?php echo esc_textarea( $value ); ?></textarea> <?php }
    