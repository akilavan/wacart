<?php
/**
 * The template for displaying product content within single product page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see      https://docs.woocommerce.com/document/template-structure/
 * @package  WooCommerce/Templates
 * @version  3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>
    <?php
        /**
         * Hook: woocommerce_before_single_product.
         *
         * @hooked wc_print_notices - 10
         */
        do_action( 'woocommerce_before_single_product' );
    ?>

    <div class="summary entry-summary">
        <?php
            /**
             * Hook: woocommerce_single_product_summary.
             *
             * @hooked woocommerce_template_single_title - 5
             * @hooked woocommerce_template_single_rating - 10
             * @hooked woocommerce_template_single_price - 10
             * @hooked woocommerce_template_single_excerpt - 20
             * @hooked woocommerce_template_single_add_to_cart - 30
             * @hooked woocommerce_template_single_meta - 40
             * @hooked woocommerce_template_single_sharing - 50
             * @hooked WC_Structured_Data::generate_product_data() - 60
             */
            do_action( 'woocommerce_single_product_summary' );
        ?>
    </div>

    <?php
        /**
         * Hook: woocommerce_after_single_product_summary.
         *
         * @hooked woocommerce_output_product_data_tabs - 10
         * @hooked woocommerce_upsell_display - 15
         * @hooked woocommerce_output_related_products - 20
         */
        do_action( 'woocommerce_after_single_product_summary' );
    ?>

    <meta itemprop="url" content="<?php the_permalink(); ?>" />

    <?php
        /**
         * Hook: woocommerce_after_single_product.
         *
         * @hooked woocommerce_output_product_image_gallery - 10
         */
        do_action( 'woocommerce_after_single_product' );
    ?>
    
    <?php
        /**
         * Add WhatsApp cart button.
         */
        whatsapp_cart_add_button();
    ?>

    <?php
        /**
         * Hook: woocommerce_before_single_product_summary.
         *
         * @hooked woocommerce_output_all_notices - 10
         */
        do_action( 'woocommerce_before_single_product_summary' );
    ?>
</div>
