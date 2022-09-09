<?php

namespace CharlieEtienne\WCSOB;

class Elementor {

	public static function fix_missing_hook( $content, $widget ) {
		if( $widget->get_name() === 'woocommerce-product-images' ) {
			ob_start();
			Badge::display();
			return ob_get_clean() . $content;
		}
		return $content;
	}
}