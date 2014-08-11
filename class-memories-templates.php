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
		$current_timestamp = current_time( 'timestamp', wp_timezone_override_offset() );

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
								
								<?php if( has_post_thumbnail() && 'video' != get_post_format( get_the_ID() ) ){ ?>
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
								<?php 
								} elseif( 'link' == get_post_format( get_the_ID() ) ) { // Link

									$entry_custom_meta = get_post_custom( get_the_ID() ); 
									
									if( isset( $entry_custom_meta['_format_link_url'] ) ){
										?>
										<tr>
											<td style="padding: 15px; border: 1px solid #efefef; border-bottom: none; background: #fafafa;">
												<h3 style="margin: 0;">
													<?php printf( __( '<a href="%1$s" rel="bookmark">link to %2$s</a>', 'memories' ), $entry_custom_meta['_format_link_url'][0], $this->get_domain_name( $entry_custom_meta['_format_link_url'][0] ) )?>
												</h3>
											</td>
										</tr>
										<?php
									}			

								} elseif( 'video' == get_post_format( get_the_ID() ) ){ // Video 

									$video = get_post_meta( get_the_ID(), '_format_video_embed', true );
									
									if( $video ){
										?>
										<tr>
											<td style="padding: 15px; border: 1px solid #efefef; border-bottom: none; background: #fafafa;">
												<?php $this->get_video_embed_code( $video ); ?>
											</td>
										</tr>
										<?php										
									
									}
								}
								?>

								<tr>
									<td style="padding: 15px; border: 1px solid #efefef; border-bottom: none;">
										<h3 style="margin-top: 0; margin-bottom: 0;">
											<a href="<?php the_permalink(); ?>" title="<?php echo the_title(); ?>" style="text-decoration: none;">
												<?php the_title(); ?>
											</a>
										</h3>
									</td>
								</tr>								

								<tr>
									<td style="padding: 15px; border: 1px solid #efefef; line-height: 1.4;" width="300">

										<h4><?php the_time( 'l, M j, Y H:i'); ?></h4>

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

	/**
	 * Get embed code based on string (path to file, embed code, oEmbed supported video link)
	 * 
	 * @param string path to file || embed code || oEmbed-supported video link
	 * 
	 * @return void
	 */
	function get_video_embed_code( $video ){
		$video_extensions = array( 'mp4', 'ogg' );
		$video_info = pathinfo( $video );

		// Check if this should be displayed using video tag
		if( isset( $video_info['extension'] ) && in_array( $video_info['extension'], $video_extensions) ){

			echo "<video controls><source src='$video'></source></video>";

		} elseif( strpos( $video, '<iframe' ) !== false ){
			// If this is embed code
			echo $video;
		} else {
			// Otherwise, assume that this is oEmbed link and get the content using built-in oEmbed mechanism
			echo wp_oembed_get( $video );
		}
	}

	/**
	 * Returns domain name of given URL
	 * 
	 * @param string url
	 * 
	 * @return string domain name
	 */
	function get_domain_name( $url ){
		$parsed_url = parse_url( $url );

		if( !isset( $parsed_url['scheme'] ) ){
			$url = 'http://' . $url;
		}

		return parse_url( $url , PHP_URL_HOST );	
	}	
}