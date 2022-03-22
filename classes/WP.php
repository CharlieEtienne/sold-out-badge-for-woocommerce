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
		$backorder_class = [];

		if ( ! empty( $product ) && Badge::showOnOutofstock() && ! $product->is_in_stock() && ! Badge::is_hidden() ) {
			$outofstock_class = [ WCSOB::$outofstock_class_single ];
		}

		if ( ! empty( $product ) && Badge::showOnBackorder() && $product->is_on_backorder() && ! Badge::is_hidden() ) {
			$backorder_class = [ WCSOB::$backorder_class_single ];
		}

		return array_merge( $classes, $outofstock_class, $backorder_class );
	}

	/**
	 * Loads plugin's translated strings.
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'wcsob', false, WCSOB_PLUGIN_REL_PATH . '/languages/' );
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

		if ( is_search() && isset( $product ) && Badge::shoudDisplay( $product ) && ! Badge::is_hidden() ) {
			$badge = apply_filters( 'wcsob_soldout', '<span class="wcsob_soldout">' . Badge::get_text() . '</span>', $post, $product );
			$html  = $badge . $html;
		}

		return $html;
	}

}