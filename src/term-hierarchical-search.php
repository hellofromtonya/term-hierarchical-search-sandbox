<?php
/**
 * Term Hierarchical Search
 *
 * These methods provide a utility interface to find a meta value
 * within the term's hierarchy.  It walks up the hierarchy starting
 * at the given term, i.e. walks to its parent -> grandparent -> N levels.
 *
 * It's performant as it grabs the records it needs out of the database
 * with one SQL query, i.e. to avoid multiple trips to gather each level's
 * record and then metadata.
 *
 * @package     KnowTheCode\TermHierarchicalSearch
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://KnowTheCode.io
 * @license     GNU-2.0+
 */
namespace KnowTheCode\TermHierarchicalSearch;

use WP_Term;

/**
 * Get the metadata value for the term. This function walks up the term hierarchy,
 * searching each parent level to find a value for the given meta key. When it finds
 * one, it's returned.
 *
 * To perform an archive settings check, turn on the $check_for_archive_setting flag to
 * true.  This extra check does the following:
 *
 *      1.  Checks each level's  `enable_content_archive_settings` value.
 *      2.  If it's enabled, then that level's meta value is returned, regardless if
 *          it has a value or not.
 *
 * It works a level override, forcing that level to return it's value.
 *
 * @since 1.0.0
 *
 * @param WP_Term $term Term object
 * @param string $meta_key Meta key for the value you want to retrieve
 * @param bool $check_for_archive_setting Flag to check if the `enable_content_archive_settings`
 *                          is set.  When TRUE, check if this flag is set.
 *
 * @return mixed
 */
function get_term_meta_value_in_hierarchy(  WP_Term $term, $meta_key, $check_for_archive_setting = false ) {
	$meta_keys = array( $meta_key );
	if ( $check_for_archive_setting ) {
		$meta_keys[] = 'enable_content_archive_settings';
	}

	$term_ancestors = get_hierarchichal_term_metadata( $term, $meta_keys );
	if ( $term_ancestors === false ) {
		return;
	}

	// Loop through the objects until you find one that has a meta value.
	foreach( (array) $term_ancestors as $term_ancestor ) {

		d( $term_ancestor );

		// When enabled, check to see if the content archive setting is turned on.
		if ( $check_for_archive_setting && $term_ancestor->metadata2 ) {
			return $term_ancestor->metadata1;
		}

		if ( $term_ancestor->metadata1 ) {
			return $term_ancestor->metadata1;
		}
	}

	// Whoops, didn't find one with a value for that meta key.
	return;
}

/**
 * Get the specified metadata value for the term or from
 * one of it's parent terms.
 *
 * @since 1.0.0
 *
 * @param WP_Term $term Term object
 * @param string|array $meta_key The meta key(s) to retrieve.
 *
 * @return mixed|null
 */
function get_hierarchichal_term_metadata( WP_Term $term, $meta_key ) {

	if ( ! is_taxonomy_hierarchical( $term->taxonomy ) ) {
		return;
	}

	if ( ! has_parent_term( $term ) ) {
		return;
	}

	return get_terms_ancestory_tree( $term->term_id, $meta_key );
}

/**
 * Get an array of term ancestors for the given term id, meaning
 * the SQL query starts at the given term id and then walks up
 * the parent tree as it stores the columns.
 *
 * The result is an array of stdClass objects that have the following:
 *      term_id => int
 *      parent_id => int
 *      metadata1 => value of that meta key's column
 *      ..
 *      metadataN => value of the meta key #N
 *
 * @since 1.0.0
 *
 * @param integer $term_id
 * @param array $meta_keys Array of meta key(s) to retrieve.
 *
 * @return array|bool
 */
function get_terms_ancestory_tree( $term_id, array $meta_keys ) {
	global $wpdb;

	// Build the SQL Query first.
	$sql_query = build_terms_ancestory_tree_sql_query( $meta_keys );

	// Assemble the values, i.e. get them in the right order
	// to insert into the SQL query.
	$values = $meta_keys;
	array_unshift( $values, $term_id );

	// Prepare the values and then insert into the SQL query.
	// We are swapping out the %d/%f/%s placeholders with their value.
	$sql_query = $wpdb->prepare( $sql_query, $values );

	// Run the query to get records from the database.
	$records = $wpdb->get_results( $sql_query );

	// Check if we got records back from the database. If yes,
	// return the records.
	if ( $records && is_array( $records ) ) {
		return $records;
	}

	// Oh poo, we something when wrong.
	return false;
}

/**
 * Build the SQL Query string.
 *
 * @since 1.0.0
 *
 * @param array $meta_keys Array of meta key(s) to retrieve.
 *
 * @return string
 */
function build_terms_ancestory_tree_sql_query( array $meta_keys  ) {
	global $wpdb;

	$number_of_meta_keys = count( $meta_keys );

	$sql_query = "SELECT t.term_id, @parent := t.parent AS parent_id";
	for( $suffix_number = 1; $suffix_number <= $number_of_meta_keys; $suffix_number++ ) {
		$sql_query .= sprintf( ', tm%1$d.meta_value AS metadata%1$d', $suffix_number );
	}

	$sql_query .= "\n" .
"FROM (
	SELECT *
	FROM {$wpdb->term_taxonomy} AS tt
		ORDER BY 
		CASE 
			WHEN tt.term_id > tt.parent THEN tt.term_id 
			ELSE tt.parent 
		END DESC
) AS t
JOIN (
	SELECT @parent := %d
) AS tmp";

	for( $suffix_number = 1; $suffix_number <= $number_of_meta_keys; $suffix_number++ ) {
		$sql_query .= "\n" . sprintf(
				'LEFT JOIN %1$s AS tm%2$d ON tm%2$d.term_id = @parent AND tm%2$d.meta_key = ',
				$wpdb->termmeta,
				$suffix_number
			);

		$sql_query .= '%s';
	}

	$sql_query .= "\n" . "WHERE t.term_id = @parent;";

	return $sql_query;
}

/**
 * Checks if the term has a parent.
 *
 * @since 1.0.0
 *
 * @param WP_Term $term Term object.
 *
 * @return bool
 */
function has_parent_term( WP_Term $term ) {
	return ( $term->parent > 0 );
}