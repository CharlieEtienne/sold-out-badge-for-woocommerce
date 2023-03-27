<?php
/**
 * Plugin Name:             Sold Out Badge for WooCommerce
 * Description:             Display a "Sold Out!" badge on out-of-stock products
 * Version:                 4.3.6
 * Requires at least:       5.2
 * Requires PHP:            7.2
 * WC requires at least:    4.0
 * WC tested up to:         6.8
 * Author:                  Charlie Etienne
 * Author URI:              https://web-nancy.fr
 * License:                 GPL v2 or later
 * License URI:             https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:             sold-out-badge-for-woocommerce
 * Domain Path:             /languages
 */

namespace CharlieEtienne\WCSOB;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Require autoloader
require dirname( __FILE__ ) . '/vendor/autoload.php';

// Define plugin consts
if ( ! defined( 'WCSOB_PLUGIN_PATH' ) ) {
	define( 'WCSOB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WCSOB_PLUGIN_REL_PATH' ) ) {
	define( 'WCSOB_PLUGIN_REL_PATH', dirname( plugin_basename( __FILE__ ) ) );
}
if ( ! defined( 'WCSOB_PLUGIN_URL' ) ) {
	define( 'WCSOB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

use Carbon_Fields\Carbon_Fields;

class WCSOB {

	private static $instance;
	public static  $outofstock_class_single = 'wcsob-outofstock-product';
	public static  $backorder_class_single = 'wcsob-backorder-product';

	final public static function get_instance(): WCSOB {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init() {
		// Plugin actions
		add_action( 'init', [ WP::class, 'load_plugin_textdomain' ] );
		add_action( 'after_setup_theme', [ Carbon_Fields::class, 'boot' ] );
		add_action( 'wp_enqueue_scripts', [ WP::class, 'enqueue_scripts' ] );
		add_action( 'carbon_fields_register_fields', [ Settings::class, 'add_plugin_settings_page' ] );
		add_action( 'elementor/widget/render_content', [ Elementor::class, 'fix_missing_hook' ], 10, 2 );
		add_action( 'woocommerce_before_single_variation', [ WooCommerce::class, 'show_badge_on_variation_select' ] );
		add_action( 'woocommerce_before_shop_loop_item_title', [ Badge::class, 'display' ], 10 );
		add_action( 'woocommerce_before_single_product_summary', [ Badge::class, 'display' ], 30 );
		add_action( 'woocommerce_product_options_inventory_product_data', [ Settings::class, 'hide_per_product' ] );
		add_action( 'woocommerce_admin_process_product_object', [ Settings::class, 'save_hide_per_product' ] );

		// Plugin filters
		add_filter( 'body_class', [ WP::class, 'add_body_class' ] );
		add_filter( 'post_thumbnail_html', [ WP::class, 'display_sold_out_in_search_loop' ], 10 );
		add_filter( 'woocommerce_sale_flash', [ WooCommerce::class, 'hide_sale_flash' ], 10, 3 );
		add_filter( 'woocommerce_get_stock_html', [ WooCommerce::class, 'replace_out_of_stock_text' ], 10, 2 );
		add_filter( 'woocommerce_locate_template', [ WooCommerce::class, 'locate_template' ], 1, 3 );
	}

}

WCSOB::get_instance()->init();

add_action( 'before_woocommerce_init', function() {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
} );
