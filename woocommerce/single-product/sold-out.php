<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

?>
<?php if ( ! $product->is_in_stock() ) : ?>

	<?php echo apply_filters( 'wcsob_soldout', '<span class="wcsob_soldout">' . WCSOB::get_badge_text() . '</span>', $post, $product ); ?>

	<?php
endif;
