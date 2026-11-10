<?php
/**
 * Demo content shared by both themes (run with: wp eval-file setup.php while Keystone is active).
 * Keystone's patterns are expanded into plain core-block markup and saved into the home page, so Twenty
 * Twenty-Five renders exactly the same blocks when it is compared. Names and details are invented.
 *
 * @package Keystone
 */

$registry = WP_Block_Patterns_Registry::get_instance();
$home     = '';
foreach ( array( 'hero', 'services', 'pricing', 'testimonials', 'faq', 'contact' ) as $slug ) {
	$pattern = $registry->get_registered( 'keystone/' . $slug );
	if ( ! $pattern ) {
		fwrite( STDERR, "pattern keystone/{$slug} missing\n" );
		exit( 1 );
	}
	$home .= $pattern['content'] . "\n\n";
}

$upsert = static function ( string $type, string $slug, string $title, string $content, string $status = 'publish' ): int {
	$existing = get_page_by_path( $slug, OBJECT, $type );
	return (int) wp_insert_post(
		array(
			'ID'           => $existing ? $existing->ID : 0,
			'post_type'    => $type,
			'post_status'  => $status,
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_content' => wp_slash( $content ),
		)
	);
};

$para     = static fn ( string $t ): string => "<!-- wp:paragraph -->\n<p>{$t}</p>\n<!-- /wp:paragraph -->\n\n";
$home_id  = $upsert( 'page', 'home', 'Home', $home );
$news_id  = $upsert( 'page', 'news', 'News', '' );
$about_id = $upsert(
	'page',
	'about',
	'About us',
	$para( 'Riverside Dental has looked after families in Riverside since 2009. We are a team of five dentists, four hygienists and a front-of-house team who will remember your name.' )
	. "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">How we work</h2>\n<!-- /wp:heading -->\n\n"
	. $para( 'Every visit starts with a conversation. We explain what we see, what we recommend and what it costs, and we never start treatment you have not agreed to.' )
	. $para( 'We keep same-day appointments free every morning for pain and accidents, and we run evening clinics on Wednesdays for people who cannot get away from work.' )
);
foreach (
	array(
		array( 'brushing-guide', 'How long should you really brush for?', 'Two minutes, twice a day, is the advice that holds up. Most people stop after about 45 seconds. A timer or an electric brush with a built-in timer makes the difference.' ),
		array( 'nervous-patients', 'Nervous about the dentist? You are not alone', 'About one adult in four feels anxious before a dental visit. Tell us when you book; we can give you a longer appointment, explain each step before it happens and agree a signal to pause.' ),
		array( 'children-first-visit', 'Your child’s first dental visit', 'Bring your child along to your own check-up from their first birthday so the surgery feels familiar. Their first examination is short, gentle and free.' ),
	) as [ $slug, $title, $text ]
) {
	$upsert( 'post', $slug, $title, $para( $text ) . $para( 'If you have questions, call us or ask at reception. We are always happy to help.' ) );
}

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
update_option( 'page_for_posts', $news_id );
update_option( 'blogname', 'Riverside Dental' );
update_option( 'blogdescription', 'Friendly family dentistry in Riverside' );

// A navigation menu both themes use.
$nav   = get_posts(
	array(
		'post_type'   => 'wp_navigation',
		'numberposts' => 1,
		'post_status' => 'publish',
	)
);
$links = '';
foreach ( array( array( 'Home', '/' ), array( 'About us', '/about/' ), array( 'News', '/news/' ) ) as [ $label, $url ] ) {
	$links .= '<!-- wp:navigation-link {"label":"' . $label . '","url":"' . $url . '","kind":"custom"} /-->';
}
wp_insert_post(
	array(
		'ID'           => $nav ? $nav[0]->ID : 0,
		'post_type'    => 'wp_navigation',
		'post_status'  => 'publish',
		'post_title'   => 'Main',
		'post_content' => $links,
	)
);
echo "demo content ready\n";
