<?php
/**
 * Checkout page.
 *
 * @package WhatsApp_Cart_Button
 */

defined( 'ABSPATH' ) || exit;

// add WhatsApp checkout button on cart and checkout pages
add_action( 'woocommerce_proceed_to_checkout', 'whatsapp_cart_checkout_button' );
add_action( 'woocommerce_review_order_before_submit', 'whatsapp_cart_checkout_button' );

function whatsapp_cart_checkout_button() {
    global $woocommerce;
    
    // retrieve plugin settings
    $phone_number = get_option( '_whatsapp_cart_phone_number' );
    $chat_template = get_option( '_whatsapp_cart_chat_template' );
    
    if ( ! empty( $phone_number ) ) {
        ?>
        <a href="https://api.whatsapp.com/send?phone=<?php echo esc_attr( $phone_number ); ?>&text=<?php echo urlencode( $chat_template ); ?>" id="whatsapp-cart-checkout-button" class="button alt"><?php _e( 'Checkout with WhatsApp', 'whatsapp-cart-button' ); ?></a>
        <?php
    }
}

// register WhatsApp checkout button as payment gateway
add_filter( 'woocommerce_payment_gateways', 'whatsapp_cart_add_gateway_class' );
 
function whatsapp_cart_add_gateway_class( $gateways ) {
    $gateways[] = 'WC_Gateway_WhatsApp_Cart';
    return $gateways;
}
 
// define WhatsApp checkout payment gateway class
add_action( 'plugins_loaded', 'whatsapp_cart_init_gateway_class' );
 
function whatsapp_cart_init_gateway_class() {
 
    class WC_Gateway_WhatsApp_Cart extends WC_Payment_Gateway {
 
        public function __construct() {
            $this->id = 'whatsapp_cart';
            $this->icon = '';
            $this->has_fields = false;
            $this->method_title = __( 'WhatsApp Cart', 'whatsapp-cart-button' );
            $this->method_description = __( 'Checkout with WhatsApp.', 'whatsapp-cart-button' );
            $this->supports = array(
                'products'
            );
 
            $this->init_form_fields();
 
            $this->init_settings();
            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
 
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }
 
        public function init_form_fields() {
 
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __( 'Enable/Disable', 'whatsapp-cart-button' ),
                    'type' => 'checkbox',
                    'label' => __( 'Enable WhatsApp Cart', 'whatsapp-cart-button' ),
                    'default' => 'no',
                ),
                'title' => array(
                    'title' => __( 'Title', 'whatsapp-cart-button' ),
                    'type' => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'whatsapp-cart-button' ),
                    'default' => __( 'WhatsApp Cart', 'whatsapp-cart-button' ),
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => __( 'Description', 'whatsapp-cart-button' ),
                    'type' => 'textarea',
                    'description' => __( 'This controls the description which the user sees during checkout.', 'whatsapp-cart-button' ),
                    'default' => '',
                ),
            );
        }
 
        public function process_payment( $order_id ) {
            $order = wc_get_order( $order_id );
 
            // Mark as on-hold (we're awaiting the transfer).
            $order->update_status( 'on-hold', __( 'Awaiting WhatsApp Cart payment.', 'whatsapp-cart-button' ) );
 
            // Reduce stock levels.
            wc_reduce_stock_levels( $order_id );
 
            // Remove cart.
            WC()->cart->empty_cart();
 
            // Redirect to thank you page.
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url( $order ),
            );
        }
    }
}
