<?php
/**
 * Theme checks inside WordPress (run with: wp eval-file checks.php while Keystone is active).
 *
 * @package Keystone
 */

$failures = 0;
$check    = static function ( string $name, bool $ok, string $detail = '' ) use ( &$failures ): void {
	echo ( $ok ? '  ok    ' : '  FAIL  ' ) . $name . ( $ok || '' === $detail ? '' : ' (' . $detail . ')' ) . "\n";
	$failures += $ok ? 0 : 1;
};

$theme = wp_get_theme();
$check( 'Keystone is active and valid', 'Keystone' === $theme->get( 'Name' ) && ! $theme->errors() && wp_is_block_theme() );

$templates = array_map( static fn ( $t ) => $t->slug, get_block_templates( array(), 'wp_template' ) );
$need      = array( 'index', 'home', 'front-page', 'page', 'single', 'archive', 'search', '404' );
$check( 'all templates present', ! array_diff( $need, $templates ), implode( ',', array_diff( $need, $templates ) ) );

$broken = array();
$files  = array_merge( (array) glob( get_stylesheet_directory() . '/templates/*.html' ), (array) glob( get_stylesheet_directory() . '/parts/*.html' ) ); // No GLOB_BRACE: unavailable on musl.
foreach ( $files as $file ) {
	$html   = (string) file_get_contents( $file );
	$blocks = parse_blocks( $html );
	$walk   = static function ( array $block_list ) use ( &$walk, &$broken, $file ): void {
		foreach ( $block_list as $b ) {
			if ( null !== $b['blockName'] && ! WP_Block_Type_Registry::get_instance()->is_registered( $b['blockName'] ) ) {
				$broken[] = basename( $file ) . ': ' . $b['blockName'];
			}
			$walk( $b['innerBlocks'] );
		}
	};
	$walk( $blocks );
}
$check( 'templates use only registered core blocks', ! $broken, implode( '; ', $broken ) );

$patterns = array_filter( WP_Block_Patterns_Registry::get_instance()->get_all_registered(), static fn ( array $p ): bool => str_starts_with( $p['name'], 'keystone/' ) );
$check( 'six patterns registered', 6 === count( $patterns ), (string) count( $patterns ) );

$variations = WP_Theme_JSON_Resolver::get_style_variations();
$check( 'three style variations', 3 === count( $variations ), (string) count( $variations ) );

$settings = wp_get_global_settings();
$check( 'no web fonts registered (system fonts only)', empty( $settings['typography']['fontFamilies']['theme'][0]['fontFace'] ) && empty( $settings['typography']['fontFamilies']['theme'][1]['fontFace'] ) );
$check( 'default colour palette and custom colours disabled (brand consistency)', false === ( $settings['color']['defaultPalette'] ?? true ) && false === ( $settings['color']['custom'] ?? true ) );

echo $failures ? "\n{$failures} theme check(s) failed\n" : "\nall theme checks passed\n";
if ( $failures ) {
	WP_CLI::halt( 1 );
}
