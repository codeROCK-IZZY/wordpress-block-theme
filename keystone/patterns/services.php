<?php
/**
 * Title: Services grid
 * Slug: keystone/services
 * Categories: keystone
 * Description: Three services with a short description each.
 * Viewport width: 1280
 *
 * @package Keystone
 */

?>
<!-- wp:group {"anchor":"services","align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"1200px"}} -->
<div id="services" class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50)">
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'What we do', 'keystone' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns">
<?php foreach ( array( array( __( 'Check-ups', 'keystone' ), __( 'A thorough check of teeth and gums every six months, with X-rays when needed.', 'keystone' ) ), array( __( 'Hygiene', 'keystone' ), __( 'Gentle cleaning and advice that keeps problems from starting.', 'keystone' ) ), array( __( 'Emergencies', 'keystone' ), __( 'Same-day appointments kept free every morning for pain and accidents.', 'keystone' ) ) ) as $keystone_service ) : ?>
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $keystone_service[0] ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php echo esc_html( $keystone_service[1] ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
