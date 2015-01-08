<?php

/**
 * Get download categories
 *
 * @return array
 */
function render_edd_get_categories() {
	$terms = get_terms( 'download_category', 'hide_empty=false' );

	$output = array();
	foreach ( $terms as $term ) {
		$output[ $term->term_id ] = $term->name;
	}
	return $output;
}

/**
 * Get download tags
 *
 * @return array
 */
function render_edd_get_tags() {
	$terms = get_terms( 'download_tag', 'hide_empty=false' );

	$output = array();
	foreach ( $terms as $term ) {
		$output[ $term->term_id ] = $term->name;
	}
	return $output;
}

/**
 * Get all downloads
 *
 * @return array
 */
function render_edd_get_downloads() {
	$args = array(
		'post_type' => 'download'
	);
	$posts = get_posts( $args );

	$output = array();
	foreach ( $posts as $post ) {
		$output[ $post->ID ] = $post->post_title;
	}
	return $output;
}