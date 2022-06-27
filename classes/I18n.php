<?php

namespace CharlieEtienne\WCSOB;

class I18n {
	public static function get_suffix(): string {
		$suffix = '';
		if ( ! defined( 'ICL_LANGUAGE_CODE' ) ) {
			return $suffix;
		}

		return '_' . ICL_LANGUAGE_CODE;
	}

	public static function get_theme_option( $option_name ) {
		return carbon_get_theme_option( $option_name . self::get_suffix() );
	}
}