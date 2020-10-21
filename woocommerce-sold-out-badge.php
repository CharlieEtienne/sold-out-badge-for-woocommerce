<?php
/**
 * Plugin Name:       WooCommerce Sold Out Badge
 * Plugin URI:        https://web-nancy.fr
 * Description:       Affiche un badge "Vendu" sur les produits en rupture de stock
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Charlie Etienne
 * Author URI:        https://web-nancy.fr
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wcsob
 * Domain Path:       /languages
 */

add_action( 'woocommerce_before_shop_loop_item_title', 'wcsob_display_sold_out_loop_woocommerce' );
add_action( 'wp_enqueue_scripts', 'wcsob_enqueue_scripts' );

function wcsob_display_sold_out_loop_woocommerce() {
    global $product;

    if ( !$product->is_in_stock() ) {
        echo '<span class="wcsob_soldout">' . __( 'VENDU', 'woocommerce' ) . '</span>';
    }
}

function wcsob_enqueue_scripts(){
    wp_enqueue_style( 'wcsob', plugin_dir_url(__FILE__) . '/style.css' );
}