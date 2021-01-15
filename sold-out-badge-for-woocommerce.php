<?php
/**
 * Plugin Name:       Sold Out Badge for WooCommerce
 * Description:       Display a "Sold Out!" badge on out of stock products
 * Version:           2.0.10
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Charlie Etienne
 * Author URI:        https://web-nancy.fr
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sold-out-badge-for-woocommerce
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
		add_action( 'init', [ $this, 'load_plugin_textdomain' ] );
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
	 * Loads plugin's translated strings.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wcsob', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Boot Carbon Fields
	 */
	public function load_carbon_fields() {
		Carbon_Fields::boot();
	}

	/**
	 * Add nav menu and declare fields
	 *
	 * @noinspection PhpPossiblePolymorphicInvocationInspection
	 */
	public function add_plugin_settings_page() {
		Container::make( 'theme_options', __( 'Sold Out Badge for WooCommerce', 'sold-out-badge-for-woocommerce' ) )
		         ->set_page_file( 'wcsob' )
		         ->set_page_parent( 'options-general.php' )
		         ->add_fields(
			         [
				         Field::make( 'text', 'wcsob_text', __( 'Label', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( __( 'Sold out!', 'sold-out-badge-for-woocommerce' ) ),

				         Field::make( 'color', 'wcsob_background_color', __( 'Background Color', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( '#222222' ),

				         Field::make( 'color', 'wcsob_text_color', __( 'Text Color', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( '#ffffff' ),

				         Field::make( 'text', 'wcsob_font_size', __( 'Font size', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( '12' ),

				         Field::make( 'checkbox', 'wcsob_hide_sale_flash', __( 'Hide Sale badge?', 'sold-out-badge-for-woocommerce' ) )
				              ->set_help_text( __( 'Do you want to hide the "Sale!" badge when a product is sold out?', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( true ),
			         ] );
	}

	/**
	 * Enqueue plugin scripts and styles
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'wcsob', plugin_dir_url( __FILE__ ) . '/style.css' );
		$style = '';
		$style .= ".wcsob_soldout {";
		$style .= "    padding: 3px 8px;";
		$style .= "    text-align: center;";
		$style .= "    background: " . esc_html( carbon_get_theme_option( 'wcsob_background_color' ) ) . ";";
		$style .= "    color: " . esc_html( carbon_get_theme_option( 'wcsob_text_color' ) ) . ";";
		$style .= "    font-weight: bold;";
		$style .= "    position: absolute;";
		$style .= "    top: 6px;";
		$style .= "    right: 6px;";
		$style .= "    z-index: 9;";
		$style .= "    font-size: " . esc_html( carbon_get_theme_option( 'wcsob_font_size' ) ) . "px;";
		$style .= "}";

		wp_add_inline_style( 'wcsob', $style );
	}

	/**
	 * Display Sold Out badge in products loop
	 */
	public function display_sold_out_in_loop() {
		wc_get_template( 'single-product/sold-out.php' );
	}

	/**
	 * Display Sold Out badge in single product
	 */
	public function display_sold_out_in_single() {
		wc_get_template( 'single-product/sold-out.php' );
	}

	/**
	 * Replace "Out of stock" text with "Sold out!"
	 *
	 * @param string                   $html
	 * @param false|null|WC_Product    $product
	 *
	 * @return string
	 */
	public function replace_out_of_stock_text( string $html, $product ) {
		if ( ! $product->is_in_stock() ) {
			return '<p class="wcsob_soldout_text">' . esc_html__( carbon_get_theme_option( 'wcsob_text' ), 'sold-out-badge-for-woocommerce' ) . '</p>';
		}

		return $html;
	}

	/**
	 * Hide Sale badge if product is out of stock
	 *
	 * @noinspection PhpUnusedLocalVariableInspection
	 *
	 * @param string                   $content
	 * @param array|null|WP_Post       $post
	 * @param false|null|WC_Product    $product
	 *
	 * @return mixed|null
	 */
	public function hide_sale_flash( string $content, $post, $product ) {
		global $post, $product;

		return ( carbon_get_theme_option( 'wcsob_hide_sale_flash' ) && ! $product->is_in_stock() ) ? null : $content;
	}

	/**
	 * Locate plugin WooCommerce templates to override WooCommerce default ones
	 *
	 * @param $template
	 * @param $template_name
	 * @param $template_path
	 *
	 * @return string
	 */
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