<?php
/**
 * Title: Simple pricing
 * Slug: keystone/pricing
 * Categories: keystone
 * Description: Three prices in columns with a short note.
 * Viewport width: 1280
 *
 * @package Keystone
 */

?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)">
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'Clear prices', 'keystone' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:columns -->
<div class="wp-block-columns">
<?php foreach ( array( array( __( 'Check-up', 'keystone' ), '£55' ), array( __( 'Hygienist visit', 'keystone' ), '£65' ), array( __( 'Emergency appointment', 'keystone' ), '£85' ) ) as $keystone_price ) : ?>
<!-- wp:column {"style":{"border":{"width":"1px","radius":"8px"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"borderColor":"line"} -->
<div class="wp-block-column has-border-color has-line-border-color" style="border-width:1px;border-radius:8px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $keystone_price[0] ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"fontSize":"x-large"} -->
<p class="has-x-large-font-size"><strong><?php echo esc_html( $keystone_price[1] ); ?></strong></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns -->
<!-- wp:paragraph {"textColor":"muted","fontSize":"small"} -->
<p class="has-muted-color has-text-color has-small-font-size"><?php echo esc_html__( 'You always get a written estimate before any treatment.', 'keystone' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
