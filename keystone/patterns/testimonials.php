<?php
/**
 * Title: Testimonials
 * Slug: keystone/testimonials
 * Categories: keystone
 * Description: Two customer quotes side by side.
 * Viewport width: 1280
 *
 * @package Keystone
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"surface","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div class="wp-block-group alignfull has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'What our patients say', 'keystone' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:columns -->
<div class="wp-block-columns">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p><?php echo esc_html__( 'The calmest appointment I have ever had. Everything was explained before it happened.', 'keystone' ); ?></p>
<!-- /wp:paragraph --><cite><?php echo esc_html__( 'Priya, patient since 2021', 'keystone' ); ?></cite></blockquote>
<!-- /wp:quote -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p><?php echo esc_html__( 'Our children actually look forward to their check-ups now.', 'keystone' ); ?></p>
<!-- /wp:paragraph --><cite><?php echo esc_html__( 'Daniel, parent', 'keystone' ); ?></cite></blockquote>
<!-- /wp:quote -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
