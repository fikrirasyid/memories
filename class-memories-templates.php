<?php
/**
 * Templating class
 */
class Memories_Templates{
	/**
	 * Display HTML table based output for dashboard and email use
	 * 
	 * @return void
	 */
	function today_posts( $posts ){
		// Get current timestamp
		$current_timestamp = current_time( 'timestamp' );

		// Loop the posts, if there's any
		if( $posts->have_posts() ){
			
			echo '<table><tbody>';

			while ( $posts->have_posts() ) {

				$posts->the_post();

				?>
					<!-- Year -->
					<?php 
						$post_year = get_the_date( 'Y' );
						$post_timestamp = strtotime( get_the_date() );

						if( $posts->year != $post_year ):
					?>
					<tr>
						<td>
							<h1 style="margin-top: 80px; margin-bottom: 0; border-bottom: 1px solid #afafaf; padding-bottom: 10px;"><?php echo $post_year; ?></h1>
							<h3 style="margin-top: 10px; font-style: italic; text-transform: capitalize; color: #afafaf;"><?php echo human_time_diff( $post_timestamp, $current_timestamp ); _e( ' Ago', 'memories' );?></h3>
						</td>
					</tr>
					<?php
						endif;
						$posts->year = $post_year;
					?>

					<!-- Post -->
					<tr>
						<td>
							<!-- post content -->
							<table style="background: white; width: 540px; margin-bottom: 10px;" cellspacing="0">
								
								<?php if( has_post_thumbnail() ) : ?>
								<tr>
									<td style="padding: 15px; border: 1px solid #efefef; border-bottom: none; background: #fafafa;">
										<?php 
											$post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
											$post_thumbnail_src = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );

											if( isset( $post_thumbnail_src[0] ) ){
												echo "<img src='{$post_thumbnail_src[0]}' style='width: 100%;' />";															
											}
										?>
									</td>
								</tr>
								<?php endif; ?>

								<tr>
									<td style="padding: 15px; border: 1px solid #efefef; line-height: 1.4;" width="300">

										<h3 style="margin-top: 0;">
											<a href="<?php the_permalink(); ?>" title="<?php echo the_title(); ?>">
												<?php the_title(); ?>
											</a>
										</h3>

										<h4><?php the_date( 'l, M j, Y H:i'); ?></h4>

										<?php 
											add_filter( 'the_content', array( $this, 'modified_content' ) );
											add_filter( 'img_caption_shortcode', array( $this, 'modified_caption_shortcode' ), 10, 3 );

											the_content(); 
										?>
									</td>
								</tr>
							</table>								
						</td>
					</tr>
				<?php
			}

			echo '</tbody></table>';
						

		}

		return;
	}

	/**
	 * Remove image width & height markup
	 * 
	 * @param string of content
	 * 
	 * @return string of modified content
	 */
	function modified_content( $content ){
        $content = str_replace( ']]>', ']]&gt;', $content );													
		$content = preg_replace( '/(width|height)="\d*"\s/', "", $content );
		$content = str_replace( '" src="', '" style="width:100%;" src="', $content );

		return $content;
	}

	/**
	 * Modifying caption attributes
	 * 
	 * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/img_caption_shortcode
	 * 
	 * @param string empty
	 * @param array attributes of shortcode
	 * @param string shortcode content
	 * 
	 * @return string of captions
	 */
	function modified_caption_shortcode( $empty, $attr, $content ){
		$attr = shortcode_atts( array(
			'id'      => '',
			'align'   => 'alignnone',
			'width'   => '',
			'caption' => ''
		), $attr );

		if ( 1 > (int) $attr['width'] || empty( $attr['caption'] ) ) {
			return '';
		}

		if ( $attr['id'] ) {
			$attr['id'] = 'id="' . esc_attr( $attr['id'] ) . '" ';
		}

		return '<div ' . $attr['id']
		. 'class="wp-caption ' . esc_attr( $attr['align'] ) . '" '
		. 'style="max-width: ' . ( 10 + (int) $attr['width'] ) . 'px;">'
		. do_shortcode( $content )
		. '<p class="wp-caption-text">' . $attr['caption'] . '</p>'
		. '</div>';

	}	
}