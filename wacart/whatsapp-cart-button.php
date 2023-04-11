<?php
/**
 * Plugin Name: WhatsApp Cart Button
 * Plugin URI: https://example.com/
 * Description: Add a WhatsApp cart button on product pages and checkout pages in WooCommerce.
 * Version: 1.0.0
 * Author: John Doe
 * Author URI: https://example.com/
 * Text Domain: whatsapp-cart-button
 *
 * @package WhatsApp_Cart_Button
 */

defined( 'ABSPATH' ) || exit;

// include plugin functions
require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';

// include plugin checkout functionality (optional)
if ( class_exists( 'WC_Payment_Gateway' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/checkout.php';
}

// plugin activation hook
register_activation_hook( __FILE__, 'whatsapp_cart_activate' );

function whatsapp_cart_activate() {
    // flush rewrite rules
    flush_rewrite_rules();
}

// plugin deactivation hook
register_deactivation_hook( __FILE__, 'whatsapp_cart_deactivate' );

function whatsapp_cart_deactivate() {
    // flush rewrite rules
    flush_rewrite_rules();
}

// enqueue plugin styles and scripts
add_action( 'wp_enqueue_scripts', 'whatsapp_cart_enqueue_scripts' );

function whatsapp_cart_enqueue_scripts() {
    wp_enqueue_style( 'whatsapp-cart-button-style', plugins_url( '/assets/css/style.css', __FILE__ ), array(), '1.0.0' );
    wp_enqueue_script( 'whatsapp-cart-button-script', plugins_url( '/assets/js/script.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
}

// add custom settings fields to the WooCommerce settings page
add_filter( 'woocommerce_settings_tabs_array', 'whatsapp_cart_settings_tab', 50 );
add_action( 'woocommerce_settings_tabs_whatsapp_cart', 'whatsapp_cart_settings_tab_content' );
add_action( 'woocommerce_update_options_whatsapp_cart', 'whatsapp_cart_settings_save' );

function whatsapp_cart_settings_tab( $tabs ) {
    $tabs['whatsapp_cart'] = __( 'WhatsApp Cart Button', 'whatsapp-cart-button' );
    return $tabs;
}

function whatsapp_cart_settings_tab_content() {
    woocommerce_admin_fields( whatsapp_cart_settings() );
}

function whatsapp_cart_settings() {
    $settings = array(
        'section_title' => array(
            'name'     => __( 'WhatsApp Cart Button Settings', 'whatsapp-cart-button' ),
            'type'     => 'title',
            'desc'     => '',
            'id'       => 'whatsapp_cart_section_title'
        ),
        'phone_number' => array(
            'name'     => __( 'Phone Number', 'whatsapp-cart-button' ),
            'type'     => 'text',
            'desc'     => __( 'Enter your WhatsApp phone number including the country code.', 'whatsapp-cart-button' ),
            'id'       => '_whatsapp_cart_phone_number'
        ),
        'chat_template' => array(
            'name'     => __( 'Chat Template', 'whatsapp-cart-button' ),
            'type'     => 'textarea',
            'desc'     => __( 'Enter your custom message template that will be sent to your WhatsApp account when the user clicks the "Add to WhatsApp Cart" or "Checkout with WhatsApp" button. You can use the following placeholders in your message: %product_name%, %product_price%, %product_url%.', 'whatsapp-cart-button' ),
            'id'       => '_whatsapp_cart_chat_template'
        ),
        'button_image' => array(
            'name'     => __( 'Button Image', 'whatsapp-cart-button' ),
            'type'     => 'text',
            'desc'     => __( 'Enter the URL of your custom button image or leave blank to use the default WhatsApp button image.', 'whatsapp-cart-button' ),
            'id'       => '_whatsapp_cart_button_image'
        ),
        'categories' => array(
            'name'    => __( 'Categories', 'whatsapp-cart-button' ),
            'type'    => 'multiselect',
            'class'   => 'wc-enhanced-select',
            'css'     => 'min-width:300px;',
            'desc'    => __( 'Select the categories where you want to display the WhatsApp cart button. Leave blank to display the WhatsApp cart button on all product pages.', 'whatsapp-cart-button' ),
            'options' => get_product_categories(),
            'id'      => '_whatsapp_cart_categories'
        ),
        'section_end' => array(
             'type' => 'sectionend',
             'id' => 'whatsapp_cart_section_end'
        )
    );

    return apply_filters( 'whatsapp_cart_settings', $settings );
    }
    
    // get product categories for settings field
    function get_product_categories() {
    $terms = get_terms(
    array(
    'taxonomy' => 'product_cat',
    'hide_empty' => false,
    )
    );
    
    $options = array();
    
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        foreach ( $terms as $term ) {
            $options[ $term->term_id ] = $term->name;
        }
    }
    
    return $options;
    }
    
    // save plugin settings
    function whatsapp_cart_settings_save() {
    woocommerce_update_options( whatsapp_cart_settings() );
    }
    
    // add custom action to the WooCommerce checkout page
    add_action( 'woocommerce_review_order_after_submit', 'whatsapp_checkout_add_button', 20 );
    
    
    function whatsapp_checkout_add_button($checkout) {
        // retrieve plugin settings
        $phone_number = get_option( '_whatsapp_cart_phone_number' );
        $chat_template = get_option( '_whatsapp_cart_chat_template' );
        $button_image = get_option( '_whatsapp_cart_button_image' );
        
        // get cart contents
        $cart_items = WC()->cart->get_cart();
        $product_names = array();
        foreach ( $cart_items as $item ) {
            $product_names[] = $item['data']->get_name();
        }
        $product_list_text = implode( ', ', $product_names );
        $product_list_text = str_replace( ' ', '+', $product_list_text );
        
        if ( ! empty( $phone_number ) && ! empty( $chat_template ) ) {
            ?>
            <div class="whatsapp-checkout-button">
                <a href="https://api.whatsapp.com/send?phone=<?php echo esc_attr( $phone_number ); ?>&text=<?php echo urlencode( str_replace( '%product_name%', $product_list_text, str_replace( '%product_url%', get_permalink( wc_get_page_id( 'cart' ) ), $chat_template ) ) ); ?>" class="button alt"><?php _e( 'Checkout with WhatsApp', 'whatsapp-cart-button' ); ?></a>
            </div>
            <?php
        }
    }
    