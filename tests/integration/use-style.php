<?php
/**
 * Applies one of Keystone's style variations, as choosing it in the Site Editor would.
 * Usage: wp eval-file tests/use-style.php <title|default> --user=admin, e.g. "Midnight (dark)". The user matters:
 * WordPress only attaches the post to the theme when the current user may assign terms.
 *
 * @package Keystone
 */

$wanted = $args[0] ?? 'default';
$data   = array(
	'version'                     => 3,
	'isGlobalStylesUserThemeJSON' => true,
);
if ( 'default' !== $wanted ) {
	$found = null;
	foreach ( WP_Theme_JSON_Resolver::get_style_variations() as $variation ) {
		if ( $variation['title'] === $wanted ) {
			$found = $variation;
		}
	}
	if ( ! $found ) {
		WP_CLI::error( "no variation called {$wanted}" );
	}
	$data = array_merge( $found, $data );
}
$post_id = WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
wp_update_post(
	array(
		'ID'           => $post_id,
		'post_content' => wp_slash( wp_json_encode( $data ) ),
	)
);
WP_CLI::success( "style: {$wanted}" );
