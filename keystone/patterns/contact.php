<?php
/**
 * Title: Contact and opening hours
 * Slug: keystone/contact
 * Categories: keystone
 * Description: Address, phone, email and hours in two columns, with a call to action.
 * Viewport width: 1280
 *
 * @package Keystone
 */

?>
<!-- wp:group {"anchor":"contact","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"contrast","textColor":"base","layout":{"type":"constrained","contentSize":"1200px"}} -->
<div id="contact" class="wp-block-group alignfull has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:columns -->
<div class="wp-block-columns">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"textColor":"base"} -->
<h2 class="wp-block-heading has-base-color has-text-color"><?php echo esc_html__( 'Visit or call us', 'keystone' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php echo esc_html__( '42 Mill Lane, Riverside', 'keystone' ); ?><br><a class="has-base-color has-text-color" href="tel:01615550142">0161 555 0142</a></p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"backgroundColor":"base","textColor":"contrast"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-contrast-color has-base-background-color has-text-color has-background wp-element-button" href="#"><?php echo esc_html__( 'Book online', 'keystone' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"textColor":"base"} -->
<h3 class="wp-block-heading has-base-color has-text-color"><?php echo esc_html__( 'Opening hours', 'keystone' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php echo esc_html__( 'Monday to Friday: 8 am to 6 pm', 'keystone' ); ?><br><?php echo esc_html__( 'Saturday: 9 am to 1 pm', 'keystone' ); ?><br><?php echo esc_html__( 'Sunday: closed', 'keystone' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
