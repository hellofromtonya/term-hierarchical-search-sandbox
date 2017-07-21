<?php
/**
 * Term Hierarchical Search Testing Sandbox Plugin
 *
 * @package     KnowTheCode\TermHierarchicalSearch
 * @author      hellofromTonya
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Term Hierarchical Search Testing Sandbox Plugin
 * Plugin URI:  https://github.com/hellofromtonya/term-hierarchical-search-sandbox
 * Description: Term hierarchical search plugin allows you to search the levels of a term to find the meta value you want.  It walks from the term ID up to each parent until it finds the meta value or runs out of levels to search.
 * Version:     1.0.0
 * Author:      hellofromTonya
 * Author URI:  https://KnowTheCode.io
 * Text Domain: sandbox
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace KnowTheCode\TermHierarchicalSearch;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Cheatin&#8217; uh?' );
}
require_once( __DIR__ . '/src/term-hierarchical-search.php' );

add_action( 'loop_start', __NAMESPACE__ . '\run_the_test' );
/**
 * Testing sandbox.
 *
 * @since 1.0.0
 *
 * @return void
 */
function run_the_test() {

	$term = get_term( 26, 'category' );

d( $term );

	// Set the meta key to what you want to search for.
	$meta_key = 'headline';

	$meta = get_term_meta_value_in_hierarchy(
		$term,
		$meta_key,
		true
	);

ddd( $meta );
}

