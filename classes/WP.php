<?php

namespace CharlieEtienne\WCSOB;

class WP {

	/**
	 * Enqueue plugin scripts and styles
	 */
	public static function enqueue_scripts() {
		wp_enqueue_style( 'wcsob', WCSOB_PLUGIN_URL . '/style.css' );
		wp_add_inline_style( 'wcsob', Badge::get_css() );
	}

	public static function add_body_class( $classes ) {
		global $post;
		if ( ! is_singular( 'product' ) ) {
			return $classes;
		}

		$product = wc_get_product( $post->ID );

		$outofstock_class = [];

		if ( ! empty( $product ) && ! $product->is_in_stock() && ! Badge::is_hidden() ) {
			$outofstock_class = [ WCSOB::$outofstock_class_single ];
		}

		return array_merge( $classes, $outofstock_class );
	}

	/**
	 * Loads plugin's translated strings.
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'wcsob', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Display Sold Out badge in search loop
	 *
	 * @param $html
	 *
	 * @return mixed|string
	 */
	public static function display_sold_out_in_search_loop( $html ) {
		global $post, $product;

		if ( is_search() && isset( $product ) && ! $product->is_in_stock() && ! Badge::is_hidden() ) {
			$badge = apply_filters( 'wcsob_soldout', '<span class="wcsob_soldout">' . Badge::get_text() . '</span>', $post, $product );
			$html  = $badge . $html;
		}

		return $html;
	}

}