<?php

namespace CharlieEtienne\WCSOB;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Helper\Helper as CarbonHelper;

class Settings {
	/**
	 * Add nav menu and declare fields
	 *
	 * @noinspection PhpPossiblePolymorphicInvocationInspection
	 */
	public static function add_plugin_settings_page() {
		Container::make( 'theme_options', __( 'Sold Out Badge for WooCommerce', 'sold-out-badge-for-woocommerce' ) )
		         ->set_page_file( 'wcsob' )
		         ->set_page_parent( 'options-general.php' )
		         ->add_fields(
			         [
				         // Content
				         Field::make( 'separator', 'wcsob_content', __( 'Content', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'text', 'wcsob_text' . I18n::get_suffix(), __( 'Label', 'sold-out-badge-for-woocommerce' ) )
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
				         Field::make( 'multiselect', 'wcsob_behaviour', __( 'Behaviour', 'sold-out-badge-for-woocommerce' ) )
				              ->set_options( [
					                             'out-of-stock' => __( 'Display on "out of stock" products (default)', 'sold-out-badge-for-woocommerce' ),
					                             'backorder' => __( 'Display on "available on backorder" products', 'sold-out-badge-for-woocommerce' ),
				                             ] )
				              ->set_default_value( 'out-of-stock' )
					          ->set_help_text( __( 'Choose whether to display badge on "out of stock" products (default) or/and on "available on backorder" products', 'sold-out-badge-for-woocommerce' ) ),
				         Field::make( 'checkbox', 'wcsob_alt_method', __( 'Use alternative method? (pure CSS)', 'sold-out-badge-for-woocommerce' ) )
				              ->set_help_text( __( 'Try this method in case of odd badge placement or if the badge does not show. Useful for some themes like Divi. The <code>.product</code> div needs to have a <code>.outofstock</code> class.', 'sold-out-badge-for-woocommerce' ) . ' ' . __( 'Unchecked by default.', 'sold-out-badge-for-woocommerce' ) )
				              ->set_default_value( false ),
			         ] );
	}

	public static function get_option( string $option ) {
		return carbon_get_theme_option( $option );
	}

	public static function get_value( string $option, string $format = "" ): string {
		return sprintf( "%s%s",
		                esc_html( self::get_option( $option ) ),
		                empty( $format ) ? "" : $format
		);
	}

	public static function get_text( string $option ): string {
		if ( empty( I18n::get_theme_option( $option ) ) ) {
			return esc_html__( self::get_option( $option ), 'sold-out-badge-for-woocommerce' );
		}

		return esc_html( I18n::get_theme_option( $option ) );
	}

	public static function get_behaviour() {
		return self::get_option( 'wcsob_behaviour' );
	}

	/**
	 * Get value and append "px" if numeric, or "auto" if auto, or default value
	 *
	 * @param string    $option
	 *
	 * @return mixed|string
	 */
	public static function get_value_from_string( string $option ) {
		if ( is_numeric( self::get_option( $option ) ) ) {
			return self::get_value( $option, 'px' );
		} elseif ( 'auto' === self::get_option( $option ) ) {
			return 'auto';
		} else {
			return self::get_default_value( $option );
		}
	}

	public static function get_default_value( $option ) {
		$field = CarbonHelper::get_field( 'theme_options', null, $option );
		if ( ! isset( $field ) ) {
			return '';
		}

		return $field->get_default_value();
	}

	/**
	 * @return mixed
	 */
	public static function should_hide_sale_flash() {
		return self::get_option( 'wcsob_hide_sale_flash' );
	}

	/**
	 * Check if we are using alternative method
	 *
	 * @return mixed
	 */
	public static function use_alt_method() {
		return self::get_option( 'wcsob_alt_method' );
	}

	/**
	 * Hide per product
	 *
	 * @return void
	 */
	public static function hide_per_product(): void {
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

	/**
	 * @param $product
	 *
	 * @return void
	 */
	public static function save_hide_per_product( $product ): void {
		$product->update_meta_data( '_wcsob_hide', isset( $_POST[ '_wcsob_hide' ] ) ? 'yes' : 'no' );
	}
}