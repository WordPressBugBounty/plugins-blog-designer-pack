<?php
/**
 * Slider Template 2
 * 
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

// Post Meta Data
$meta_data = array(
				'author'	=> $atts['show_author'],
				'post_date'	=> $atts['show_date'],
				'comments' 	=> $atts['show_comments'],
			);
?>
<div class="bdpp-post-slide <?php echo esc_attr( $atts['wrp_cls'] ); ?> bdpp-clearfix">
	<div class="bdpp-post-slider-content">
		<div class="bdpp-col-left bdpp-col-2 bdpp-columns">
			<a class="bdpp-post-linkoverlay" href="<?php echo esc_url( $atts['post_link'] ); ?>"></a>
			<div class="bdpp-post-img-bg" style="<?php echo esc_attr( $atts['image_style'] ); ?>">
				<?php if( $atts['format'] == 'video' ) { echo bdp_post_format_html( $atts['format'] ); } ?>
			</div>
		</div>

		<div class="bdpp-col-right bdpp-col-2 bdpp-columns">
			<div class="bdpp-featured-meta">
				<?php if( $atts['show_category'] && $atts['cate_name'] ) { ?>
				<div class="bdpp-post-cats"><?php echo wp_kses_post( $atts['cate_name'] ); ?></div>
				<?php } ?>

				<h2 class="bdpp-post-title">
					<a href="<?php echo esc_url( $atts['post_link'] ); ?>"><?php the_title(); ?></a>
				</h2>

				<?php if( $atts['show_date'] || $atts['show_author'] || $atts['show_comments'] ) { ?>
				<div class="bdpp-post-meta-outer">
					<div class="bdpp-post-meta bdpp-post-meta-up">
						<?php echo bdp_post_meta_data( $meta_data, array( 'sharing_trigger' => 'hover' ) ); ?>
					</div>
				</div>
				<?php }

				if( $atts['show_content'] ) { ?>
				<div class="bdpp-post-content">
					<div class="bdpp-post-desc"><?php echo bdp_get_post_excerpt( $post->ID, get_the_content(), $atts['content_words_limit'] ); ?></div>
					<?php if( $atts['show_read_more'] ) { ?>
					<a href="<?php echo esc_url( $atts['post_link'] ); ?>" class="bdpp-rdmr-btn"><?php echo wp_kses_post( $atts['read_more_text'] ); ?></a>
					<?php } ?>
				</div>
				<?php }

				if( $atts['show_tags'] && $atts['tags'] ) { ?>
				<div class="bdpp-post-meta bdpp-post-meta-down"><?php echo wp_kses_post( $atts['tags'] ); ?></div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>