<?php
/**
 * Keystone evaluation: WCAG contrast for every text/background pair the theme uses, in the default palette and
 * all three style variations, plus the page-weight, axe-core and (when present) Lighthouse comparison against
 * Twenty Twenty-Five written by eval/compare.py.
 *
 * Usage: php eval/run.php [--gate]
 * Writes eval/reports/report.{md,json,html}. With --gate, exits 1 when any check fails.
 *
 * @package Keystone
 */

declare(strict_types=1);

$root  = dirname( __DIR__ );
$gate  = in_array( '--gate', $argv, true );
$fails = array();

/**
 * WCAG 2.x relative luminance of a #rrggbb colour.
 *
 * @param string $hex Colour.
 */
function keystone_luminance( string $hex ): float {
	$rgb = array_map( 'hexdec', str_split( ltrim( $hex, '#' ), 2 ) );
	$lin = array_map(
		static function ( $c ): float {
			$c = $c / 255;
			return $c <= 0.04045 ? $c / 12.92 : ( ( $c + 0.055 ) / 1.055 ) ** 2.4;
		},
		$rgb
	);
	return 0.2126 * $lin[0] + 0.7152 * $lin[1] + 0.0722 * $lin[2];
}

/**
 * WCAG contrast ratio between two colours.
 *
 * @param string $a Colour.
 * @param string $b Colour.
 */
function keystone_contrast( string $a, string $b ): float {
	$la = keystone_luminance( $a );
	$lb = keystone_luminance( $b );
	return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
}

// Sanity checks on the formula itself, so a broken formula cannot pass every palette.
$known = array( array( '#000000', '#ffffff', 21.0 ), array( '#777777', '#ffffff', 4.48 ), array( '#ffffff', '#ffffff', 1.0 ) );
foreach ( $known as [ $x, $y, $want ] ) {
	if ( abs( round( keystone_contrast( $x, $y ), 2 ) - $want ) > 0.01 ) {
		$fails[] = "contrast formula: {$x} on {$y} gave " . round( keystone_contrast( $x, $y ), 2 ) . ", expected {$want}";
	}
}

// Every pairing that appears in the templates, parts, patterns and theme.json element styles.
$pairs      = array(
	array( 'contrast', 'base', 4.5, 'Body text and headings' ),
	array( 'contrast', 'surface', 4.5, 'Text in the hero, testimonials and footer bands' ),
	array( 'muted', 'base', 4.5, 'Dates, captions, small print' ),
	array( 'muted', 'surface', 4.5, 'Footer tagline and credits' ),
	array( 'primary', 'base', 4.5, 'Links, and the focus outline (3:1 needed)' ),
	array( 'primary', 'surface', 4.5, 'Links in the hero and footer bands' ),
	array( 'on-primary', 'primary', 4.5, 'Button text' ),
	array( 'on-primary', 'contrast', 4.5, 'Button text on hover' ),
	array( 'base', 'contrast', 4.5, 'Contact band text, links and button' ),
);
$decorative = array( array( 'accent', 'base', 'Quote border' ), array( 'line', 'base', 'Separators and FAQ dividers' ) );

$palette_of = static function ( string $file ): array {
	$json = json_decode( (string) file_get_contents( $file ), true, 64, JSON_THROW_ON_ERROR );
	$out  = array();
	foreach ( $json['settings']['color']['palette'] ?? array() as $c ) {
		$out[ $c['slug'] ] = strtolower( $c['color'] );
	}
	return array( $json['title'] ?? 'Default', $out );
};

$palettes            = array();
[ , $default ]       = $palette_of( $root . '/keystone/theme.json' );
$palettes['Default'] = $default;
foreach ( (array) glob( $root . '/keystone/styles/*.json' ) as $file ) {
	[ $title, $p ]      = $palette_of( $file );
	$palettes[ $title ] = $p + $default;
}

$contrast = array();
foreach ( $palettes as $name => $p ) {
	foreach ( $pairs as [ $fg, $bg, $min, $role ] ) {
		if ( ! isset( $p[ $fg ], $p[ $bg ] ) ) {
			$fails[] = "{$name}: colour {$fg} or {$bg} missing";
			continue;
		}
		$ratio      = round( keystone_contrast( $p[ $fg ], $p[ $bg ] ), 2 );
		$contrast[] = array(
			'palette'    => $name,
			'text'       => $fg,
			'background' => $bg,
			'ratio'      => $ratio,
			'needed'     => $min,
			'role'       => $role,
			'gated'      => true,
		);
		if ( $ratio < $min ) {
			$fails[] = "{$name}: {$fg} on {$bg} is {$ratio}:1, needs {$min}:1 ({$role})";
		}
	}
	foreach ( $decorative as [ $fg, $bg, $role ] ) {
		$contrast[] = array(
			'palette'    => $name,
			'text'       => $fg,
			'background' => $bg,
			'ratio'      => round( keystone_contrast( $p[ $fg ], $p[ $bg ] ), 2 ),
			'needed'     => null,
			'role'       => $role . ' (decorative, not required)',
			'gated'      => false,
		);
	}
}
$gated_pairs = array_filter( $contrast, static fn ( array $r ): bool => $r['gated'] );
$min_ratio   = min( array_column( $gated_pairs, 'ratio' ) );

// Comparison with Twenty Twenty-Five.
$compare_file = $root . '/eval/reports/compare.json';
$compare      = is_file( $compare_file ) ? json_decode( (string) file_get_contents( $compare_file ), true, 64, JSON_THROW_ON_ERROR ) : null;
$rows         = array();
if ( null === $compare ) {
	$fails[] = 'eval/reports/compare.json missing: run eval/compare.py first';
} else {
	foreach ( $compare['keystone'] as $page => $k ) {
		$t      = $compare['twentytwentyfive'][ $page ];
		$row    = array(
			'page'           => $page,
			'keystone_bytes' => $k['total'],
			'tt5_bytes'      => $t['total'],
			'saving_pct'     => round( 100 * ( 1 - $k['total'] / $t['total'] ), 1 ),
			'keystone_fonts' => $k['bytes']['font'] ?? 0,
			'tt5_fonts'      => $t['bytes']['font'] ?? 0,
			'keystone_axe'   => array_sum( array_column( $k['axe'], 'nodes' ) ),
			'tt5_axe'        => array_sum( array_column( $t['axe'], 'nodes' ) ),
			'keystone_lh'    => $k['lighthouse'] ?? null,
			'tt5_lh'         => $t['lighthouse'] ?? null,
		);
		$rows[] = $row;
		if ( $row['keystone_bytes'] >= $row['tt5_bytes'] ) {
			$fails[] = "{$page}: Keystone ({$row['keystone_bytes']} B) is not lighter than Twenty Twenty-Five ({$row['tt5_bytes']} B)";
		}
		if ( $row['keystone_fonts'] > 0 ) {
			$fails[] = "{$page}: Keystone downloaded {$row['keystone_fonts']} B of web fonts";
		}
		if ( $row['keystone_axe'] > 0 ) {
			$fails[] = "{$page}: {$row['keystone_axe']} axe-core violations";
		}
		if ( null !== $row['keystone_lh'] ) {
			if ( $row['keystone_lh']['accessibility'] < 100 ) {
				$fails[] = "{$page}: Lighthouse accessibility {$row['keystone_lh']['accessibility']}, needs 100";
			}
			if ( $row['keystone_lh']['cls'] > 0.1 ) {
				$fails[] = "{$page}: Lighthouse layout shift {$row['keystone_lh']['cls']}, needs 0.1 or less";
			}
		}
	}
}

$has_lh  = $rows && null !== $rows[0]['keystone_lh'];
$summary = array(
	'palettes'       => count( $palettes ),
	'pairs_checked'  => count( $gated_pairs ),
	'lowest_ratio'   => $min_ratio,
	'avg_saving_pct' => $rows ? round( array_sum( array_column( $rows, 'saving_pct' ) ) / count( $rows ), 1 ) : null,
	'keystone_axe'   => array_sum( array_column( $rows, 'keystone_axe' ) ),
	'tt5_axe'        => array_sum( array_column( $rows, 'tt5_axe' ) ),
	'lighthouse'     => $has_lh,
	'passed'         => ! $fails,
);

// Reports.
$md   = array( '# Keystone evaluation', '' );
$md[] = '## Summary';
$md[] = '';
$md[] = "- Contrast: {$summary['pairs_checked']} text/background pairs across {$summary['palettes']} palettes; lowest ratio {$min_ratio}:1 (WCAG AA needs 4.5:1).";
if ( $rows ) {
	$md[] = "- Page weight: Keystone is {$summary['avg_saving_pct']}% lighter than Twenty Twenty-Five on the same content (average of " . count( $rows ) . ' pages).';
	$md[] = "- axe-core (WCAG 2.1 A/AA): Keystone {$summary['keystone_axe']} violations, Twenty Twenty-Five {$summary['tt5_axe']}.";
}
$md[] = '- Result: ' . ( $fails ? 'FAILED' : 'all checks passed' );
$md[] = '';
if ( $rows ) {
	$md[] = '## Keystone against Twenty Twenty-Five (same blocks, same content)';
	$md[] = '';
	$md[] = '| Page | Keystone bytes | Twenty Twenty-Five bytes | Saving | Web fonts (K / TT5) | axe violations (K / TT5)' . ( $has_lh ? ' | Lighthouse perf (K / TT5) | Lighthouse a11y (K / TT5)' : '' ) . ' |';
	$md[] = '|---|---:|---:|---:|---:|---:|' . ( $has_lh ? '---:|---:|' : '' );
	foreach ( $rows as $r ) {
		$line = sprintf( '| %s | %s | %s | %s%% | %s / %s | %d / %d', $r['page'], number_format( $r['keystone_bytes'] ), number_format( $r['tt5_bytes'] ), $r['saving_pct'], number_format( $r['keystone_fonts'] ), number_format( $r['tt5_fonts'] ), $r['keystone_axe'], $r['tt5_axe'] );
		if ( $has_lh ) {
			$line .= sprintf( ' | %d / %d | %d / %d', $r['keystone_lh']['performance'], $r['tt5_lh']['performance'], $r['keystone_lh']['accessibility'], $r['tt5_lh']['accessibility'] );
		}
		$md[] = $line . ' |';
	}
	$md[] = '';
	$md[] = 'Bytes are uncompressed response bodies for every request the page makes, measured in Chromium with an empty cache. Both themes load the same core JavaScript (the Navigation block\'s mobile menu).';
	$md[] = '';
}
$md[] = '## Contrast';
$md[] = '';
$md[] = '| Palette | Text | Background | Ratio | Needed | Used for |';
$md[] = '|---|---|---|---:|---:|---|';
foreach ( $contrast as $c ) {
	$md[] = sprintf( '| %s | %s | %s | %.2f:1 | %s | %s |', $c['palette'], $c['text'], $c['background'], $c['ratio'], null === $c['needed'] ? '-' : $c['needed'] . ':1', $c['role'] );
}
if ( $fails ) {
	$md[] = '';
	$md[] = '## Failures';
	$md[] = '';
	foreach ( $fails as $f ) {
		$md[] = '- ' . $f;
	}
}
$markdown = implode( "\n", $md ) . "\n";

$out = $root . '/eval/reports';
if ( ! is_dir( $out ) ) {
	mkdir( $out, 0777, true );
}
file_put_contents( $out . '/report.md', $markdown );
file_put_contents(
	$out . '/report.json',
	json_encode(
		array(
			'summary'  => $summary,
			'compare'  => $rows,
			'contrast' => $contrast,
			'failures' => $fails,
		),
		JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
	) . "\n"
);

// A small self-contained HTML version of the same report.
$esc   = static fn ( $s ): string => htmlspecialchars( (string) $s, ENT_QUOTES );
$html  = '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Keystone evaluation</title>';
$html .= '<style>body{font:16px/1.5 system-ui,sans-serif;max-width:1000px;margin:2rem auto;padding:0 1rem;color:#14213d}table{border-collapse:collapse;width:100%;margin:1rem 0 2rem}th,td{border-bottom:1px solid #d7dde5;padding:.4rem .6rem;text-align:left}td.n{text-align:right;font-variant-numeric:tabular-nums}.sw{display:inline-block;width:1.4rem;height:1.4rem;border-radius:4px;vertical-align:middle;border:1px solid #d7dde5;font-size:.8rem;text-align:center;line-height:1.4rem}.ok{color:#0a6b2e;font-weight:600}.bad{color:#b42318;font-weight:600}.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem}.card{background:#f2f5f9;border-radius:8px;padding:1rem}.card b{display:block;font-size:1.8rem}</style>';
$html .= '<h1>Keystone evaluation</h1><p class="' . ( $fails ? 'bad">FAILED' : 'ok">All checks passed' ) . '</p><div class="cards">';
$html .= '<div class="card"><b>' . $esc( $summary['avg_saving_pct'] ) . '%</b>lighter than Twenty Twenty-Five</div>';
$html .= '<div class="card"><b>' . $esc( $summary['keystone_axe'] ) . '</b>axe-core violations</div>';
$html .= '<div class="card"><b>' . $esc( $min_ratio ) . ':1</b>lowest text contrast across ' . $esc( $summary['palettes'] ) . ' palettes</div></div>';
if ( $rows ) {
	$html .= '<h2>Same content, two themes</h2><table><tr><th>Page</th><th>Keystone</th><th>Twenty Twenty-Five</th><th>Saving</th><th>axe (K / TT5)</th>' . ( $has_lh ? '<th>Lighthouse perf (K / TT5)</th><th>Lighthouse a11y (K / TT5)</th>' : '' ) . '</tr>';
	foreach ( $rows as $r ) {
		$html .= '<tr><td>' . $esc( $r['page'] ) . '</td><td class="n">' . number_format( $r['keystone_bytes'] ) . ' B</td><td class="n">' . number_format( $r['tt5_bytes'] ) . ' B</td><td class="n">' . $esc( $r['saving_pct'] ) . '%</td><td class="n">' . $r['keystone_axe'] . ' / ' . $r['tt5_axe'] . '</td>';
		if ( $has_lh ) {
			$html .= '<td class="n">' . $r['keystone_lh']['performance'] . ' / ' . $r['tt5_lh']['performance'] . '</td><td class="n">' . $r['keystone_lh']['accessibility'] . ' / ' . $r['tt5_lh']['accessibility'] . '</td>';
		}
		$html .= '</tr>';
	}
	$html .= '</table>';
}
$html .= '<h2>Contrast</h2><table><tr><th>Palette</th><th>Sample</th><th>Pair</th><th>Ratio</th><th>Needed</th><th>Used for</th></tr>';
foreach ( $contrast as $c ) {
	$p     = $palettes[ $c['palette'] ];
	$pass  = null === $c['needed'] || $c['ratio'] >= $c['needed'];
	$html .= '<tr><td>' . $esc( $c['palette'] ) . '</td><td><span class="sw" style="background:' . $esc( $p[ $c['background'] ] ) . ';color:' . $esc( $p[ $c['text'] ] ) . '">Aa</span></td><td>' . $esc( $c['text'] . ' on ' . $c['background'] ) . '</td><td class="n ' . ( $pass ? 'ok' : 'bad' ) . '">' . number_format( $c['ratio'], 2 ) . ':1</td><td class="n">' . ( null === $c['needed'] ? '-' : $c['needed'] . ':1' ) . '</td><td>' . $esc( $c['role'] ) . '</td></tr>';
}
$html .= '</table>';
if ( $fails ) {
	$html .= '<h2>Failures</h2><ul><li>' . implode( '</li><li>', array_map( $esc, $fails ) ) . '</li></ul>';
}
file_put_contents( $out . '/report.html', $html . "\n" );

echo $markdown;
if ( $gate && $fails ) {
	exit( 1 );
}
