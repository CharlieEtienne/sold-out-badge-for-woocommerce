<?php

namespace CharlieEtienne\WCSOB;

use WC_Product;
use WP_Post;

class WooCommerce {

	/**
	 * Locate plugin WooCommerce templates to override WooCommerce default ones
	 *
	 * @param $template
	 * @param $template_name
	 * @param $template_path
	 *
	 * @return string
	 */
	public static function locate_template( $template, $template_name, $template_path ): string {
		global $woocommerce;
		$_template = $template;
		if ( ! $template_path ) {
			$template_path = $woocommerce->template_url;
		}

		$plugin_path = untrailingslashit( WCSOB_PLUGIN_PATH ) . '/woocommerce/';

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
	public static function hide_sale_flash( string $content, $post, $product ): ?string {
		global $post, $product;

		return ( Settings::should_hide_sale_flash() && Badge::showOnOutofstock() && ! $product->is_in_stock() ) ? null : $content;
	}

	/**
	 * Replace "Out of stock" text with "Sold out!"
	 *
	 * @param string                   $html
	 * @param false|null|WC_Product    $product
	 *
	 * @return string
	 */
	public static function replace_out_of_stock_text( string $html, $product ): string {
		if ( Badge::showOnOutofstock() && ! $product->is_in_stock() && ! Badge::is_hidden() ) {
			return '<p class="wcsob_soldout_text">' . Badge::get_text() . '</p>';
		}

		return $html;
	}

	/**
	 * Show or hide Sold Out badge when user select a variation in dropdown
	 */
	public static function show_badge_on_variation_select() {
		?>
		<script type="text/javascript">
            (function ($) {
                let $form         = $('form.variations_form');
                let $product      = $form.closest('.product');
                let sold_out_text = "<?php echo Badge::get_text() ?>";
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
}