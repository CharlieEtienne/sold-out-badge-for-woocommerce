<?php
/**
 * Plugin Name:       WooCommerce Sold Out Badge
 * Plugin URI:        https://web-nancy.fr
 * Description:       Affiche un badge "Vendu" sur les produits en rupture de stock
 * Version:           2.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Charlie Etienne
 * Author URI:        https://web-nancy.fr
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wcsob
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Load Carbon Fields plugin main file
require_once dirname( __FILE__ ) . '/vendor/carbon-fields/carbon-fields-plugin.php';

class WCSOB {

	public function __construct() {
		// Plugin actions
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'after_setup_theme', [ $this, 'load_carbon_fields' ] );
		add_action( 'carbon_fields_register_fields', [ $this, 'add_plugin_settings_page' ] );
		add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'display_sold_out_in_loop' ], 10 );
		add_action( 'woocommerce_before_single_product_summary', [ $this, 'display_sold_out_in_single' ], 30 );

		// Plugin filters
		add_filter( 'woocommerce_get_stock_html', [ $this, 'replace_out_of_stock_text' ], 10, 2 );
		add_filter( 'woocommerce_locate_template', [ $this, 'woocommerce_locate_template' ], 1, 3 );
		add_filter( 'woocommerce_sale_flash', [ $this, 'hide_sale_flash' ], 10, 3 );
	}

	/**
	 * Boot Carbon Fields
	 */
	public function load_carbon_fields() {
		Carbon_Fields::boot();
	}

	/**
	 * Add nav menu and declare fields
	 */
	public function add_plugin_settings_page() {
		Container::make( 'theme_options', __( 'WooCommerce Sold Out Badge' ) )
		         ->set_page_parent( 'options-general.php' )
		         ->add_fields(
			         [
				         Field::make( 'text', 'wcsob_text', __( 'Label' ) )
				              ->set_default_value( __( 'Sold out!' ) ),

				         Field::make( 'color', 'wcsob_background_color', __( 'Background Color' ) )
				              ->set_default_value( '#222222' ),

				         Field::make( 'color', 'wcsob_text_color', __( 'Text Color' ) )
				              ->set_default_value( '#ffffff' ),

				         Field::make( 'text', 'wcsob_font_size', __( 'Font size' ) )
				              ->set_default_value( '12' ),

				         Field::make( 'checkbox', 'wcsob_hide_sale_flash', __( 'Hide Sale badge?' ) )
				              ->set_help_text( __( 'Do you want to hide the "Sale!" badge when a product is sold out?' ) )
				              ->set_default_value( true ),
			         ] );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'wcsob', plugin_dir_url( __FILE__ ) . '/style.css' );
		$style = '';
		$style .= ".wcsob_soldout {";
		$style .= "	padding: 3px 8px;";
		$style .= "    text-align: center;";
		$style .= "    background: " . esc_html( carbon_get_theme_option( 'wcsob_background_color' ) ) . ";";
		$style .= "    color:  " . esc_html( carbon_get_theme_option( 'wcsob_text_color' ) ) . ";";
		$style .= "    font-weight: bold;";
		$style .= "    position: absolute;";
		$style .= "    top: 6px;";
		$style .= "    right: 6px;";
		$style .= "    font-size:  " . esc_html( carbon_get_theme_option( 'wcsob_font_size' ) ) . "px;";
		$style .= "}";

		wp_add_inline_style( 'wcsob', $style );
	}

	public function display_sold_out_in_loop() {
		wc_get_template( 'single-product/sold-out.php' );
	}

	public function display_sold_out_in_single() {
		wc_get_template( 'single-product/sold-out.php' );
	}

	public function replace_out_of_stock_text( $html, $product ) {
		if ( ! $product->is_in_stock() ) {
			return '<p class="wcsob_soldout_text">' . esc_html__( carbon_get_theme_option( 'wcsob_text' ), 'wcsob' ) . '</p>';
		}

		return $html;
	}

	public function hide_sale_flash( $content, $post, $product ) {
		global $post, $product;

		return ( carbon_get_theme_option( 'wcsob_hide_sale_flash' ) && ! $product->is_in_stock() ) ? null : $content;
	}

	public function woocommerce_locate_template( $template, $template_name, $template_path ) {
		global $woocommerce;
		$_template = $template;
		if ( ! $template_path ) {
			$template_path = $woocommerce->template_url;
		}

		$plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/woocommerce/';

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name
			)
		);

		if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}

		if ( ! $template ) {
			$template = $_template;
		}

		return $template;
	}
}

new WCSOB();