<?php

namespace CharlieEtienne\WCSOB;

class Badge {

	public static $selectors;

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
		$style = sprintf(
			"%s { %s }",
			Badge::get_selectors( 'loop' ),
			Badge::product_loop_css()
		);

		// Single product CSS
		$style .= sprintf(
			"%s { %s }",
			Badge::get_selectors( 'single' ),
			Badge::single_css_position()
		);

		// alternative method (pure CSS)
		if ( Settings::use_alt_method() ) {
			// Product Loop AND Single product CSS
			$style .= sprintf(
				"%s { %s%s }",
				Badge::get_alt_selectors(),
				Badge::get_content(),
				Badge::product_loop_css()
			);

			// Single product CSS
			$style .= sprintf(
				"%s { %s }",
				Badge::get_alt_selectors( 'single' ),
				Badge::single_css_position()
			);

			// Related products
			$style .= Badge::related_products();
		}

		return $style;
	}

	public static function related_products(): string {
		$style = "";
		if( self::showOnOutofstock() ) {
			$style .= ".woocommerce .related .product:not(.outofstock) :before{display:none}";
			$style .= ".woocommerce .upsells .product:not(.outofstock) :before{display:none}";
		}
		if( self::showOnBackorder() ) {
			$style = ".woocommerce .related .product:not(.onbackorder) :before{display:none}";
			$style .= ".woocommerce .upsells .product:not(.onbackorder) :before{display:none}";
		}
		return $style;
	}

	public static function product_loop_css(): string {
		$style = "color: " . Settings::get_value( 'wcsob_text_color' ) . ";";
		$style .= "background: " . Settings::get_value( 'wcsob_background_color' ) . ";";
		$style .= "font-size: " . Settings::get_value( 'wcsob_font_size', 'px' ) . ";";
		$style .= "padding-top: " . Settings::get_value( 'wcsob_padding_top', 'px' ) . ";";
		$style .= "padding-right: " . Settings::get_value( 'wcsob_padding_right', 'px' ) . ";";
		$style .= "padding-bottom: " . Settings::get_value( 'wcsob_padding_bottom', 'px' ) . ";";
		$style .= "padding-left: " . Settings::get_value( 'wcsob_padding_left', 'px' ) . ";";
		$style .= "font-weight: " . Settings::get_value( 'wcsob_font_weight' ) . ";";
		$style .= "width: " . Settings::get_value_from_string( 'wcsob_width' ) . ";";
		$style .= "height: " . Settings::get_value_from_string( 'wcsob_height' ) . ";";
		$style .= "border-radius: " . Settings::get_value( 'wcsob_border_radius', 'px' ) . ";";
		$style .= "z-index: " . Settings::get_value( 'wcsob_z_index' ) . ";";
		$style .= "text-align: center;";
		$style .= "position: absolute;";
		$style .= Badge::loop_css_position();

		return $style;
	}

	public static function single_css_position(): string {
		$style = "top: " . Settings::get_value_from_string( 'wcsob_single_position_top' ) . ";";
		$style .= "right: " . Settings::get_value_from_string( 'wcsob_single_position_right' ) . ";";
		$style .= "bottom: " . Settings::get_value_from_string( 'wcsob_single_position_bottom' ) . ";";
		$style .= "left: " . Settings::get_value_from_string( 'wcsob_single_position_left' ) . ";";

		return $style;
	}

	public static function loop_css_position(): string {
		$style = "top: " . Settings::get_value_from_string( 'wcsob_position_top' ) . ";";
		$style .= "right: " . Settings::get_value_from_string( 'wcsob_position_right' ) . ";";
		$style .= "bottom: " . Settings::get_value_from_string( 'wcsob_position_bottom' ) . ";";
		$style .= "left: " . Settings::get_value_from_string( 'wcsob_position_left' ) . ";";

		return $style;
	}

	public static function get_content(): string {
		return sprintf( "content: '%s';", Badge::get_text() );
	}

	public static function get_selectors( string $selector = "" ): string {
		Badge::$selectors = [
			"loop"   => ".wcsob_soldout", // Products loop
			"single" => ".single-product .wcsob_soldout" // Single product
		];

		return Badge::get_css_classes( $selector );
	}

	public static function get_alt_selectors( string $selector = "" ): string {

		$loop_selectors = [];
		$single_selectors = [];

		if( self::showOnOutofstock()) {
			$loop_selectors[] = '.woocommerce .product.outofstock .woocommerce-LoopProduct-link:before';
			$single_selectors[] = "." . WCSOB::$outofstock_class_single . " .woocommerce-product-gallery:before";
		}
		if( self::showOnBackorder()) {
			$loop_selectors[] = '.woocommerce .product.onbackorder .woocommerce-LoopProduct-link:before';
			$single_selectors[] = "." . WCSOB::$backorder_class_single . " .woocommerce-product-gallery:before";
		}

		// Elementor Archive posts compat
		$loop_selectors[] = '.elementor-posts .product.outofstock .elementor-post__thumbnail__link:before';

		Badge::$selectors = [
			"loop"   => implode( ', ', $loop_selectors), // Products loop
			"single" => implode( ', ', $single_selectors) // Single product
		];

		return Badge::get_css_classes( $selector );
	}

	public static function get_css_classes( string $selector = "" ): string {
		if ( ! empty( $selector ) ) {
			return Badge::$selectors[ $selector ];
		}

		return implode( ', ', Badge::$selectors );
	}

	/**
	 * @param $product
	 *
	 * @return bool
	 */
	public static function shoudDisplay( $product ): bool {
		return (
			( self::showOnOutofstock() && ! $product->is_in_stock() ) ||
			( self::showOnBackorder() && $product->is_on_backorder() )
		);
	}

	public static function showOnOutofstock(): bool {
		return empty( Settings::get_behaviour() ) || ( is_array( Settings::get_behaviour() ) && in_array( 'out-of-stock', Settings::get_behaviour() ) );
	}

	public static function showOnBackorder(): bool {
		return is_array( Settings::get_behaviour() ) && in_array( 'backorder', Settings::get_behaviour() );
	}
}