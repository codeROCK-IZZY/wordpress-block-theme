<?php
/**
 * Title: Frequently asked questions
 * Slug: keystone/faq
 * Categories: keystone
 * Description: Questions that open and close, built on the core Details block.
 * Viewport width: 1280
 *
 * @package Keystone
 */

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'Questions people ask', 'keystone' ); ?></h2>
<!-- /wp:heading -->
<?php foreach ( array( array( __( 'Do you take new patients?', 'keystone' ), __( 'Yes. Register online or at reception; your first check-up can usually be booked within a week.', 'keystone' ) ), array( __( 'Is there parking?', 'keystone' ), __( 'Free parking for twelve cars behind the building, including two accessible spaces.', 'keystone' ) ), array( __( 'How do I pay?', 'keystone' ), __( 'By card, phone or cash on the day. Monthly plans spread the cost of routine care.', 'keystone' ) ) ) as $keystone_faq ) : ?>
<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo esc_html( $keystone_faq[0] ); ?></summary><!-- wp:paragraph -->
<p><?php echo esc_html( $keystone_faq[1] ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
