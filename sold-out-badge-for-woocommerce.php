<?php
/**
 * Plugin Name:             Sold Out Badge for WooCommerce
 * Description:             Display a "Sold Out!" badge on out-of-stock products
 * Version:                 3.2.2
 * Requires at least:       5.2
 * Requires PHP:            7.2
 * WC requires at least:    4.0
 * WC tested up to:         6.2
 * Author:                  Charlie Etienne
 * Author URI:              https://web-nancy.fr
 * License:                 GPL v2 or later
 * License URI:             https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:             sold-out-badge-for-woocommerce
 * Domain Path:             /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Helper\Helper as CarbonHelper;

// Load Carbon Fields plugin main file
require_once dirname( __FILE__ ) . '/vendor/carbon-fields/carbon-fields-plugin.php';

class WCSOB {

	private static $instance;
	public         $outofstock_class_single;

	final public static function get_instance(): WCSOB {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->outofstock_class_single = 'wcsob-outofstock-product';
	}

	public function init() {
		// Plugin actions
		add_action( 'init', [ $this, 'load_plugin_textdomain' ] );
		add_action( 'after_setup_theme', [ $this, 'load_carbon_fields' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'carbon_fields_register_fields', [ $this, 'add_plugin_settings_page' ] );
		add_action( 'woocommerce_before_single_variation', [ $this, 'show_badge_on_variation_select' ] );
		add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'display_sold_out_in_loop' ], 10 );
		add_action( 'woocommerce_before_single_product_summary', [ $this, 'display_sold_out_in_single' ], 30 );
		add_action( 'woocommerce_product_options_inventory_product_data', [ $this, 'setting_hide_per_product' ] );
		add_action( 'woocommerce_admin_process_product_object', [ $this, 'save_setting_hide_per_product' ] );

		// Plugin filters
		add_filter( 'body_class', [ $this, 'add_body_class' ] );
		add_filter( 'post_thumbnail_html', [ $this, 'display_sold_out_in_search_loop' ], 10 );
		add_filter( 'woocommerce_sale_flash', [ $this, 'hide_sale_flash' ], 10, 3 );
		add_filter( 'woocommerce_get_stock_html', [ $this, 'replace_out_of_stock_text' ], 10, 2 );
		add_filter( 'woocommerce_locate_template', [ $this, 'woocommerce_locate_template' ], 1, 3 );
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
				         // Content
				         Field::make( 'separator', 'wcsob_content', __( 'Content', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_text', __( 'Label', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( __( 'Sold out!', 'sold-out-badge-for-woocommerce' ) ),

				         // Colors
				         Field::make( 'separator', 'wcsob_colors', __( 'Colors', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'color', 'wcsob_background_color', __( 'Background Color', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( '#222222' )->set_width( 50 )
				              ->set_help_text( __( 'Default #222222', 'sold-out-badge-for-woocommerce' ) ),

				         Field::make( 'color', 'wcsob_text_color', __( 'Text Color', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( '#ffffff' )->set_width( 50 )
				              ->set_help_text( __( 'Default #ffffff', 'sold-out-badge-for-woocommerce' ) ),

				         // Other appearance settings
				         Field::make( 'separator', 'wcsob_other_appearance_settings', __( 'Other appearance settings', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_font_size', __( 'Font size', 'sold-out-badge-for-woocommerce' ) . ' (px)' )
				              ->set_default_value( '12' )->set_attribute( 'type', 'number' )->set_width( 16 )->set_help_text( __( 'Default "12"', 'sold-out-badge-for-woocommerce' ) ),

				         Field::make( 'text', 'wcsob_width', __( 'Width', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( 'auto' )->set_width( 16 )->set_help_text( __( 'Value in px or "auto". Default "auto".', 'sold-out-badge-for-woocommerce' ) ),

				         Field::make( 'text', 'wcsob_height', __( 'Height', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( 'auto' )->set_width( 16 )->set_help_text( __( 'Value in px or "auto". Default "auto".', 'sold-out-badge-for-woocommerce' ) ),

				         Field::make( 'text', 'wcsob_border_radius', __( 'Border radius', 'sold-out-badge-for-woocommerce' ) . ' (px)' )
				              ->set_default_value( '0' )->set_attribute( 'type', 'number' )->set_width( 16 )->set_help_text( __( 'Default "0"', 'sold-out-badge-for-woocommerce' ) ),

				         Field::make( 'select', 'wcsob_font_weight', __( 'Font weight', 'sold-out-badge-for-woocommerce' ) )
				              ->add_options( [ 'normal' => 'normal', 'bold' => 'bold', ] )
				              ->set_default_value( 'bold' )->set_width( 16 )->set_help_text( __( 'Default "bold"', 'sold-out-badge-for-woocommerce' ) ),

				         Field::make( 'text', 'wcsob_z_index', __( 'Z-index', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( '9999' )->set_attribute( 'type', 'number' )->set_width( 16 )
				              ->set_help_text( __( 'Try to increase this value if your badge is still invisible. Default "9999".', 'sold-out-badge-for-woocommerce' ) ),

				         // Padding
				         Field::make( 'separator', 'wcsob_padding', __( 'Padding', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_padding_top', __( 'Padding top', 'sold-out-badge-for-woocommerce' ) . ' (px)' )
				              ->set_default_value( '3' )->set_attribute( 'type', 'number' )->set_width( 25 )->set_help_text( __( 'Default "3"', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_padding_right', __( 'Padding right', 'sold-out-badge-for-woocommerce' ) . ' (px)' )
				              ->set_default_value( '8' )->set_attribute( 'type', 'number' )->set_width( 25 )->set_help_text( __( 'Default "8"', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_padding_bottom', __( 'Padding bottom', 'sold-out-badge-for-woocommerce' ) . ' (px)' )
				              ->set_default_value( '3' )->set_attribute( 'type', 'number' )->set_width( 25 )->set_help_text( __( 'Default "3"', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_padding_left', __( 'Padding left', 'sold-out-badge-for-woocommerce' ) . ' (px)' )
				              ->set_default_value( '8' )->set_attribute( 'type', 'number' )->set_width( 25 )->set_help_text( __( 'Default "8"', 'sold-out-badge-for-woocommerce' ) ),

				         // Position (in product loop)
				         Field::make( 'separator', 'wcsob_position', __( 'Position (in product loop)', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_position_top', __( 'Position from top', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( '6' )->set_width( 25 )->set_help_text( __( 'Value in px or "auto". Default "6".', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_position_right', __( 'Position from right', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( 'auto' )->set_width( 25 )->set_help_text( __( 'Value in px or "auto". Default "auto".', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_position_bottom', __( 'Position from bottom', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( 'auto' )->set_width( 25 )->set_help_text( __( 'Value in px or "auto". Default "auto".', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_position_left', __( 'Position from left', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( '6' )->set_width( 25 )->set_help_text( __( 'Value in px or "auto". Default "6".', 'sold-out-badge-for-woocommerce' ) ),

				         // Position (in single product)
				         Field::make( 'separator', 'wcsob_single_position', __( 'Position (in single product)', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_single_position_top', __( 'Position from top', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( '6' )->set_width( 25 )->set_help_text( __( 'Value in px or "auto". Default "6".', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_single_position_right', __( 'Position from right', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( 'auto' )->set_width( 25 )->set_help_text( __( 'Value in px or "auto". Default "auto".', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_single_position_bottom', __( 'Position from bottom', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( 'auto' )->set_width( 25 )->set_help_text( __( 'Value in px or "auto". Default "auto".', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_single_position_left', __( 'Position from left', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( '6' )->set_width( 25 )->set_help_text( __( 'Value in px or "auto". Default "6".', 'sold-out-badge-for-woocommerce' ) ),

				         // Other settings
				         Field::make( 'separator', 'wcsob_other_settings', __( 'Other settings', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'checkbox', 'wcsob_hide_sale_flash', __( 'Hide Sale badge?', 'sold-out-badge-for-woocommerce' ) )
				              ->set_help_text( __( 'Do you want to hide the "Sale!" badge when a product is sold out?', 'sold-out-badge-for-woocommerce' ) . ' ' . __( 'Checked by default.', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( true ),
				         Field::make( 'checkbox', 'wcsob_alt_method', __( 'Use alternative method? (pure CSS)', 'sold-out-badge-for-woocommerce' ) )
				              ->set_help_text( __( 'Try this method in case of odd badge placement or if the badge does not show. Useful for some themes like Divi. The <code>.product</code> div needs to have a <code>.outofstock</code> class.', 'sold-out-badge-for-woocommerce' ) . ' ' . __( 'Unchecked by default.', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( false ),
			         ] );
	}

	/**
	 * Enqueue plugin scripts and styles
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'wcsob', plugin_dir_url( __FILE__ ) . '/style.css' );

		// Product Loop CSS
		$style = ".wcsob_soldout {";
		$style = $this->product_loop_css( $style );
		$style .= "}";

		// Single product CSS
		$style .= ".single-product .wcsob_soldout {";
		$style .= "    top: " . $this->get_value_from_string( 'wcsob_single_position_top' ) . ";";
		$style .= "    right: " . $this->get_value_from_string( 'wcsob_single_position_right' ) . ";";
		$style .= "    bottom: " . $this->get_value_from_string( 'wcsob_single_position_bottom' ) . ";";
		$style .= "    left: " . $this->get_value_from_string( 'wcsob_single_position_left' ) . ";";
		$style .= "}";

		// alternative method (pure CSS)
		if ( $this->use_alt_method() ) {
			$selectors = [
				".woocommerce .product.outofstock .woocommerce-LoopProduct-link:before", // Products loop
				"." . $this->outofstock_class_single . " .woocommerce-product-gallery:before" // Single product
			];
			$style     .= implode( ', ', $selectors ) . ' {';
			$style     .= "    content: '" . WCSOB::get_badge_text() . "';";
			$style     = $this->product_loop_css( $style );
			$style     .= "}";
		}

		wp_add_inline_style( 'wcsob', $style );
	}

	/**
	 * @param string    $style
	 *
	 * @return string
	 */
	public function product_loop_css( string $style ): string {
		$style .= "    color: " . esc_html( carbon_get_theme_option( 'wcsob_text_color' ) ) . ";";
		$style .= "    background: " . esc_html( carbon_get_theme_option( 'wcsob_background_color' ) ) . ";";
		$style .= "    font-size: " . esc_html( carbon_get_theme_option( 'wcsob_font_size' ) ) . "px;";
		$style .= "    padding-top: " . esc_html( carbon_get_theme_option( 'wcsob_padding_top' ) ) . "px;";
		$style .= "    padding-right: " . esc_html( carbon_get_theme_option( 'wcsob_padding_right' ) ) . "px;";
		$style .= "    padding-bottom: " . esc_html( carbon_get_theme_option( 'wcsob_padding_bottom' ) ) . "px;";
		$style .= "    padding-left: " . esc_html( carbon_get_theme_option( 'wcsob_padding_left' ) ) . "px;";
		$style .= "    font-weight: " . esc_html( carbon_get_theme_option( 'wcsob_font_weight' ) ) . ";";
		$style .= "    top: " . $this->get_value_from_string( 'wcsob_position_top' ) . ";";
		$style .= "    right: " . $this->get_value_from_string( 'wcsob_position_right' ) . ";";
		$style .= "    bottom: " . $this->get_value_from_string( 'wcsob_position_bottom' ) . ";";
		$style .= "    left: " . $this->get_value_from_string( 'wcsob_position_left' ) . ";";
		$style .= "    width: " . $this->get_value_from_string( 'wcsob_width' ) . ";";
		$style .= "    height: " . $this->get_value_from_string( 'wcsob_height' ) . ";";
		$style .= "    border-radius: " . esc_html( carbon_get_theme_option( 'wcsob_border_radius' ) ) . "px;";
		$style .= "    z-index: " . esc_html( carbon_get_theme_option( 'wcsob_z_index' ) ) . ";";
		$style .= "    text-align: center;";
		$style .= "    position: absolute;";

		return $style;
	}

	public function add_body_class( $classes ) {
		global $post;
		if ( ! is_singular( 'product' ) ) {
			return $classes;
		}

		$product = wc_get_product( $post->ID );

		$outofstock_class = [];

		if ( ! empty( $product ) && ! $product->is_in_stock() && ! $this->is_hidden() ) {
			$outofstock_class = [ $this->outofstock_class_single ];
		}

		return array_merge( $classes, $outofstock_class );
	}

	/**
	 * Display Sold Out badge in products loop
	 */
	public function display_sold_out_in_loop() {
		if ( ! $this->is_hidden() && ! $this->use_alt_method() ) {
			wc_get_template( 'single-product/sold-out.php' );
		}
	}

	/**
	 * Display Sold Out badge in search loop
	 */
	public function display_sold_out_in_search_loop( $html ) {
		global $post, $product;

		if ( is_search() && isset( $product ) && ! $product->is_in_stock() && ! $this->is_hidden() ) {
			$badge = apply_filters( 'wcsob_soldout', '<span class="wcsob_soldout">' . WCSOB::get_badge_text() . '</span>', $post, $product );
			$html  = $badge . $html;
		}

		return $html;
	}

	/**
	 * Display Sold Out badge in single product
	 */
	public function display_sold_out_in_single() {
		if ( ! $this->is_hidden() && ! $this->use_alt_method() ) {
			wc_get_template( 'single-product/sold-out.php' );
		}
	}

	public function setting_hide_per_product() {
		global $product_object;

		$values = $product_object->get_meta( '_wcsob_hide' );

		woocommerce_wp_checkbox(
			[
				'id'          => '_wcsob_hide',
				'label'       => __( 'Sold Out Badge: Exclude', 'sold-out-badge-for-woocommerce' ),
				'description' => __( 'Don\'t display SOLD OUT! badge on this product.', 'sold-out-badge-for-woocommerce' ),
				'value'       => empty( $values ) ? 'no' : $values,
			] );
	}

	public function save_setting_hide_per_product( $product ) {
		$product->update_meta_data( '_wcsob_hide', isset( $_POST[ '_wcsob_hide' ] ) ? 'yes' : 'no' );
	}

	public function is_hidden(): bool {
		global $post;

		return get_post_meta( $post->ID, '_wcsob_hide', true ) === 'yes';
	}

	/**
	 * Check if we are using alternative method
	 *
	 * @return mixed
	 */
	public function use_alt_method() {
		return carbon_get_theme_option( 'wcsob_alt_method' );
	}

	/**
	 * Get badge text
	 *
	 * @return string
	 */
	public static function get_badge_text(): string {
		return esc_html__( carbon_get_theme_option( 'wcsob_text' ), 'sold-out-badge-for-woocommerce' );
	}

	/**
	 * Get value and append "px" if numeric, or "auto" if auto, or default value
	 *
	 * @param string    $option
	 *
	 * @return mixed|string
	 */
	public function get_value_from_string( string $option ) {
		if ( is_numeric( carbon_get_theme_option( $option ) ) ) {
			return esc_html( carbon_get_theme_option( $option ) ) . 'px';
		} elseif ( 'auto' === carbon_get_theme_option( $option ) ) {
			return 'auto';
		} else {
			$field = CarbonHelper::get_field( 'theme_options', null, $option );
			if ( ! isset( $field ) ) {
				return '';
			}

			return $field->get_default_value();
		}
	}

	/**
	 * Show or hide Sold Out badge when user select a variation in dropdown
	 */
	public function show_badge_on_variation_select() {
		?>
        <script type="text/javascript">
            (function ($) {
                let $form         = $('form.variations_form');
                let $product      = $form.closest('.product');
                let sold_out_text = "<?php echo WCSOB::get_badge_text() ?>";
                $form.on('show_variation', function (event, data) {
                    if (!data.is_in_stock) {
                        $product.prepend('<span class="wcsob_soldout">' + sold_out_text + '</span>');
                    } else {
                        $('.wcsob_soldout').remove();
                    }
                });
                $form.on('reset_data', function () {
                    $('.wcsob_soldout').remove();
                });
            })(jQuery);
        </script>
		<?php
	}

	/**
	 * Replace "Out of stock" text with "Sold out!"
	 *
	 * @param string                   $html
	 * @param false|null|WC_Product    $product
	 *
	 * @return string
	 */
	public function replace_out_of_stock_text( string $html, $product ): string {
		if ( ! $product->is_in_stock() && ! $this->is_hidden() ) {
			return '<p class="wcsob_soldout_text">' . WCSOB::get_badge_text() . '</p>';
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
	 * @return string|null
	 */
	public function hide_sale_flash( string $content, $post, $product ): ?string {
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
	public function woocommerce_locate_template( $template, $template_name, $template_path ): string {
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

WCSOB::get_instance()->init();