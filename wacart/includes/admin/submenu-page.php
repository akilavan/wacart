<?php
/**
 * Submenu page content.
 *
 * @package WhatsApp_Cart_Button
 */

defined( 'ABSPATH' ) || exit;

// add new setting field for category selection
add_action( 'woocommerce_product_options_general_product_data', 'whatsapp_cart_add_category_field' );


function whatsapp_cart_add_category_field() {
    woocommerce_wp_checkbox( array(
        'id' => '_whatsapp_cart_categories',
        'label' => __( 'WhatsApp Cart Button Categories', 'whatsapp-cart-button' ),
        'description' => __( 'Select the categories where the WhatsApp cart button should be displayed.', 'whatsapp-cart-button' ),
        'desc_tip' => true,
    ) );
}

// sanitize category selection field
function whatsapp_cart_sanitize_category_field( $input ) {
    if ( is_array( $input ) ) {
        return array_map( 'sanitize_text_field', $input );
    } else {
        return sanitize_text_field( $input );
    }
}

// register plugin settings
add_action( 'admin_init', 'whatsapp_cart_register_settings' );

function whatsapp_cart_register_settings() {
    register_setting( 'whatsapp_cart_settings', '_whatsapp_cart_categories', 'whatsapp_cart_sanitize_category_field' );
}

// render plugin settings page content
function whatsapp_cart_settings_page_content() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    if ( isset( $_GET['settings-updated'] ) ) {
        add_settings_error( 'whatsapp_cart_messages', 'whatsapp_cart_message', __( 'Settings saved.', 'whatsapp-cart-button' ), 'updated' );
    }
    
    settings_errors( 'whatsapp_cart_messages' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
                settings_fields( 'whatsapp_cart_settings' );
                do_settings_sections( 'whatsapp_cart_settings' );
                submit_button( __( 'Save Settings', 'whatsapp-cart-button' ) );
            ?>
        </form>
    </div>
    <?php
}
