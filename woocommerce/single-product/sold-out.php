<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

?>
<?php if ( ! $product->is_in_stock() ) : ?>

	<?php echo apply_filters( 'wcsob_soldout', '<span class="wcsob_soldout">' . esc_html__( carbon_get_theme_option( 'wcsob_text' ), 'sold-out-badge-for-woocommerce' ) . '</span>', $post, $product ); ?>

	<?php
endif;
