<?php
/**
 * 'bdp_post' Post Grid Shortcode
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Function to handle the `bdp_post` shortcode
 * 
 * @since 1.0
 */
function bdp_render_post_grid( $atts, $content = null ) {

	// Taking some globals
	global $post, $multipage, $bdpp_layout_id;

	// Shortcode Parameters
	$atts = shortcode_atts(array(
		'limit' 				=> 20,
		'category' 				=> array(),
		'grid' 					=> 3,
		'design' 				=> 'design-1',
		'show_author' 			=> 'true',
		'show_tags'				=> 'true',
		'show_comments'			=> 'true',
		'show_category' 		=> 'true',
		'show_content' 			=> 'true',
		'show_date' 			=> 'true',
		'pagination' 			=> 'true',
		'media_size' 			=> '',
		'content_words_limit' 	=> 20,
		'show_read_more' 		=> 'true',
		'read_more_text'		=> '',
		'order'					=> 'DESC',
		'orderby'				=> 'date',
		'css_class'				=> '',
		'custom_param_1'		=> '',	// Custom Param Passed Just for Developer
		'custom_param_2'		=> '',
	), $atts, 'bdp_post');

	$shortcode_designs 				= bdp_post_designs();
	$atts['shortcode']				= 'bdp_post';
	$atts['layout_id']				= $bdpp_layout_id;
	$atts['limit'] 					= bdp_clean_number( $atts['limit'], 20, 'number' );
	$atts['grid']					= bdp_clean_number( $atts['grid'], 1 );
	$atts['grid']					= ( $atts['grid'] <= 12 ) ? $atts['grid'] : 3;
	$atts['show_author'] 			= bdp_string_to_bool( $atts['show_author']  );
	$atts['show_tags'] 				= bdp_string_to_bool( $atts['show_tags'] );
	$atts['show_comments'] 			= bdp_string_to_bool( $atts['show_comments'] );
	$atts['show_date'] 				= bdp_string_to_bool( $atts['show_date'] );
	$atts['show_category'] 			= bdp_string_to_bool( $atts['show_category'] );
	$atts['show_content'] 			= bdp_string_to_bool( $atts['show_content'] );
	$atts['pagination'] 			= bdp_string_to_bool( $atts['pagination'] );
	$atts['show_read_more'] 		= bdp_string_to_bool( $atts['show_read_more'] );
	$atts['category'] 				= bdp_maybe_explode( $atts['category'] );
	$atts['media_size'] 			= ! empty( $atts['media_size'] )			? $atts['media_size'] 						: '';
	$atts['media_size']				= ( $atts['grid'] > 1 && empty($atts['media_size']) ) ? 'bdpp-medium' 					: $atts['media_size'];
	$atts['content_words_limit'] 	= ! empty( $atts['content_words_limit'] ) 	? $atts['content_words_limit'] 				: 20;
	$atts['read_more_text']			= ! empty( $atts['read_more_text'] )		? $atts['read_more_text']					: __( 'Read More', 'blog-designer-pack' );
	$atts['order'] 					= ( strtolower($atts['order']) == 'asc' ) 	? 'ASC' 									: 'DESC';
	$atts['orderby'] 				= ! empty( $atts['orderby'] )				? $atts['orderby'] 							: 'date';
	$atts['design'] 				= ($atts['design'] && (array_key_exists(trim($atts['design']), $shortcode_designs)))	? trim($atts['design'])		: 'design-1';
	$atts['multi_page']				= ( $multipage || is_single() ) ? 1 : 0;
	$atts['unique'] 				= bdp_get_unique();
	$atts['css_class']				.= ( $atts['layout_id'] ) ? " bdpp-layout-{$atts['layout_id']}"	: '';
	$atts['css_class']				= bdp_sanitize_html_classes( $atts['css_class'] );

	// Pagination parameter
	if( isset( $_GET['bdpp_page'] ) || $atts['multi_page'] ) {
		$atts['paged'] = isset( $_GET['bdpp_page'] ) ? $_GET['bdpp_page'] : 1;
	} elseif ( get_query_var( 'paged' ) ) {
		$atts['paged'] = get_query_var('paged');
	} elseif ( get_query_var( 'page' ) ) {
		$atts['paged'] = get_query_var( 'page' );
	} else {
		$atts['paged'] = 1;
	}

	// Taking some variables
	$count = 0;

	// WP Query Parameters
	$args = array(
		'post_type'				=> BDP_POST_TYPE,
		'post_status'			=> array('publish'),
		'order'					=> $atts['order'],
		'orderby'				=> $atts['orderby'],
		'posts_per_page'		=> $atts['limit'],
		'paged'					=> ( $atts['pagination'] ) ? $atts['paged'] : 1,
		'no_found_rows'			=> ( ! $atts['pagination'] ) ? true : false,
		'ignore_sticky_posts'	=> true,
	);

	// Category Parameter
	if( $atts['category'] ) {

		$args['tax_query'] = array(
								array(
									'taxonomy' 	=> BDP_CAT,
									'terms' 	=> $atts['category'],
									'field' 	=> ( isset($atts['category'][0]) && is_numeric($atts['category'][0]) ) ? 'term_id' : 'slug',
								));
	}

	$args = apply_filters( 'bdpp_post_query_args', $args, $atts );

	// WP Query
	$query 					= new WP_Query( $args );
	$atts['max_num_pages'] 	= $query->max_num_pages;

	ob_start();

	// If post is there
	if ( $query->have_posts() ) {

		include( BDP_DIR . "/templates/grid/loop-start.php" );

		while ( $query->have_posts() ) : $query->the_post();

			$count++;
			$atts['count'] 		= $count;
			$atts['format']		= bdp_get_post_format();
			$atts['feat_img'] 	= bdp_get_post_feat_image( $post->ID, $atts['media_size'] );
			$atts['post_link'] 	= bdp_get_post_link( $post->ID );
			$atts['cate_name'] 	= ( $atts['show_category'] )	? bdp_get_post_terms( $post->ID, BDP_CAT ) : '';
			$atts['tags']  		= ( $atts['show_tags'] ) 		? bdp_post_meta_data( array('tag' => $atts['show_tags']), array('tag_taxonomy' => 'post_tag') ) : '';

			$atts['wrp_cls'] = "bdpp-col-{$atts['grid']} bdpp-columns bdpp-post-{$post->ID} bdpp-post-{$atts['format']}";
			$atts['wrp_cls'] .= ( $count % $atts['grid']  == 1 )	? ' bdpp-first'		: '';
			$atts['wrp_cls'] .= empty( $atts['feat_img'] )			? ' bdpp-no-thumb'	: ' bdpp-has-thumb';

			// Include Design File
			include( BDP_DIR . "/templates/grid/{$atts['design']}.php" );

		endwhile;

		include( BDP_DIR . "/templates/grid/loop-end.php" );
	}

	wp_reset_postdata(); // Reset WP Query

	$content .= ob_get_clean();
	return $content;
}

// Post Grid Shortcode
add_shortcode( 'bdp_post', 'bdp_render_post_grid' );