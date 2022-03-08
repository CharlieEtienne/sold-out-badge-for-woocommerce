<?php

namespace CharlieEtienne\WCSOB;

class Badge {

	public static function is_hidden(): bool {
		global $post;

		return get_post_meta( $post->ID, '_wcsob_hide', true ) === 'yes';
	}

	/**
	 * Get badge text
	 *
	 * @return string
	 */
	public static function get_text(): string {
		return Settings::get_text( 'wcsob_text' );
	}

	/**
	 * Display Sold Out badge in products loop
	 */
	public static function display() {
		if ( ! Badge::is_hidden() && ! Settings::use_alt_method() ) {
			wc_get_template( 'single-product/sold-out.php' );
		}
	}

	/**
	 * @return string
	 */
	public static function get_css(): string {
		// Product Loop CSS
		$style = ".wcsob_soldout {";
		$style = Badge::product_loop_css( $style );
		$style .= "}";

		// Single product CSS
		$style .= ".single-product .wcsob_soldout {";
		$style .= "    top: " . Settings::get_value_from_string( 'wcsob_single_position_top' ) . ";";
		$style .= "    right: " . Settings::get_value_from_string( 'wcsob_single_position_right' ) . ";";
		$style .= "    bottom: " . Settings::get_value_from_string( 'wcsob_single_position_bottom' ) . ";";
		$style .= "    left: " . Settings::get_value_from_string( 'wcsob_single_position_left' ) . ";";
		$style .= "}";

		// alternative method (pure CSS)
		if ( Settings::use_alt_method() ) {
			$selectors = [
				".woocommerce .product.outofstock .woocommerce-LoopProduct-link:before", // Products loop
				"." . WCSOB::$outofstock_class_single . " .woocommerce-product-gallery:before" // Single product
			];
			$style     .= implode( ', ', $selectors ) . ' {';
			$style     .= "    content: '" . Badge::get_text() . "';";
			$style     = Badge::product_loop_css( $style );
			$style     .= "}";
		}

		return $style;
	}

	/**
	 * @param string    $style
	 *
	 * @return string
	 */
	public static function product_loop_css( string $style ): string {
		$style .= "    color: " . Settings::get_value( 'wcsob_text_color' ) . ";";
		$style .= "    background: " . Settings::get_value( 'wcsob_background_color' ) . ";";
		$style .= "    font-size: " . Settings::get_value( 'wcsob_font_size' ) . "px;";
		$style .= "    padding-top: " . Settings::get_value( 'wcsob_padding_top' ) . "px;";
		$style .= "    padding-right: " . Settings::get_value( 'wcsob_padding_right' ) . "px;";
		$style .= "    padding-bottom: " . Settings::get_value( 'wcsob_padding_bottom' ) . "px;";
		$style .= "    padding-left: " . Settings::get_value( 'wcsob_padding_left' ) . "px;";
		$style .= "    font-weight: " . Settings::get_value( 'wcsob_font_weight' ) . ";";
		$style .= "    top: " . Settings::get_value_from_string( 'wcsob_position_top' ) . ";";
		$style .= "    right: " . Settings::get_value_from_string( 'wcsob_position_right' ) . ";";
		$style .= "    bottom: " . Settings::get_value_from_string( 'wcsob_position_bottom' ) . ";";
		$style .= "    left: " . Settings::get_value_from_string( 'wcsob_position_left' ) . ";";
		$style .= "    width: " . Settings::get_value_from_string( 'wcsob_width' ) . ";";
		$style .= "    height: " . Settings::get_value_from_string( 'wcsob_height' ) . ";";
		$style .= "    border-radius: " . Settings::get_value( 'wcsob_border_radius' ) . "px;";
		$style .= "    z-index: " . Settings::get_value( 'wcsob_z_index' ) . ";";
		$style .= "    text-align: center;";
		$style .= "    position: absolute;";

		return $style;
	}
}