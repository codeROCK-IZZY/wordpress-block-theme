<?php
/**
 * Keystone theme setup. Deliberately small: block themes need almost no PHP.
 *
 * @package Keystone
 */

if ( ! function_exists( 'keystone_setup' ) ) {
	/**
	 * Theme supports and the editor stylesheet.
	 */
	function keystone_setup(): void {
		add_theme_support( 'wp-block-styles' );
		add_editor_style( 'style.css' );
	}
}
add_action( 'after_setup_theme', 'keystone_setup' );

/**
 * The small stylesheet theme.json cannot replace (focus outlines, reduced motion, FAQ cursor).
 */
function keystone_styles(): void {
	wp_enqueue_style( 'keystone', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'keystone_styles' );

/**
 * A pattern category so the theme's sections are easy to find in the inserter.
 */
function keystone_pattern_category(): void {
	register_block_pattern_category( 'keystone', array( 'label' => __( 'Keystone sections', 'keystone' ) ) );
}
add_action( 'init', 'keystone_pattern_category' );
