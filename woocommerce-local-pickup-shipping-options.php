<?php
/**
 * Plugin Name:       WooCommerce Local Pickup shipping options
 * Description:       Add options dedicated to the local pickup shipping method in WooCommerce.
 * Version:           0.1
 * Text Domain:       woocommerce-local-pickup-shipping-options
 * Domain Path:       /languages
 * Author:            Sébastien Méric
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Plugin URI:        https://github.com/smeric/woocommerce-local-pickup-shipping-options
 * GitHub Plugin URI: https://github.com/smeric/woocommerce-local-pickup-shipping-options
 */


 // Traduction files localisation
function woocommerce_local_pickup_shipping_options_load_textdomain() {
    load_plugin_textdomain( 'woocommerce-local-pickup-shipping-options', false, plugin_basename( plugin_dir_path( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'woocommerce_local_pickup_shipping_options_load_textdomain' );

function woocommerce_local_pickup_shipping_options_get_settings( $settings, $current_section ) {
    // Local Pickup options tab sections
    if ( '' == $current_section ) {
        // Discount field
        $custom_settings_field = array(
            'name'     => __( 'Special discount (%) applied to local pickup', 'woocommerce-local-pickup-shipping-options' ),
            'type'     => 'number',
            'desc'     => __( 'How many % discount ?', 'woocommerce-local-pickup-shipping-options' ),
            'desc_tip' => true,
            'id'       => 'local_pickup_shipping_discount',
            'css'      => 'max-width:60px;',
        );
        // Add discount field to the Settings, just before the submit button
        array_splice( $settings, count( $settings ) - 1, 0, array( $custom_settings_field ) );
    }

    return $settings;
}
add_filter( 'woocommerce_get_settings_shipping' , 'woocommerce_local_pickup_shipping_options_get_settings' , 10, 2 );

function woocommerce_local_pickup_shipping_options_display_discount( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    // Get the discount
    $discount = (int) get_option( 'local_pickup_shipping_discount' );

    // If there is a valid discount
    if ( $discount > 0 && $discount <= 100 ) {
        $chosen_shipping_method_id = WC()->session->get( 'chosen_shipping_methods' )[0];

        // Only for Local pickup chosen shipping method
        if ( false !== strpos( $chosen_shipping_method_id, 'local_pickup' ) ) {
            // Discount percentage
            $discount_percent = $cart->get_subtotal() * ( $discount / 100 );
            // Add the discount
            $cart->add_fee(  sprintf( __( 'Local Pickup discount (%s%%)', 'woocommerce-local-pickup-shipping-options' ), $discount ), -$discount_percent );
        }
    }
}
add_action( 'woocommerce_cart_calculate_fees', 'woocommerce_local_pickup_shipping_options_display_discount' );
