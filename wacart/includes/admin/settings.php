<?php
/**
 * Plugin settings page.
 *
 * @package WhatsApp_Cart_Button
 */

defined( 'ABSPATH' ) || exit;

// add plugin settings page
add_action( 'admin_menu', 'whatsapp_cart_settings_page' );

function whatsapp_cart_settings_page() {
    add_submenu_page(
        'woocommerce',
        __( 'WhatsApp Cart Button Settings', 'whatsapp-cart-button' ),
        __( 'WhatsApp Cart Button', 'whatsapp-cart-button' ),
        'manage_options',
        'whatsapp-cart-button-settings',
        'whatsapp_cart_settings_page_content'
    );
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
