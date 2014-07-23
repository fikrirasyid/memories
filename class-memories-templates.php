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
	function today_posts( $today_posts ){
		// Loop the posts, if there's any
		if( !empty( $today_posts ) ){
			?>

			<table>
				<tbody>

				<?php
				// Display the year first
				foreach ( $today_posts as $year => $posts ) :
				?>
					<tr>
						<td>
							<h2 style="font-weight: bold; padding-top: 50px; padding-bottom: 15px;"><?php echo $year; ?></h2>									
						</td>
					</tr>

					<?php
					if( !empty( $posts ) ) :
					?>
						
						<?php
						// Loop the posts
						foreach ( $posts as $key => $post ) :
						?>
						
						<tr>
							<td>
								<!-- post content -->
								<table style="background: white; width: 540px; margin-bottom: 10px;">
									
									<?php if( has_post_thumbnail( $post->ID ) ) : ?>
									<tr>
										<td style="padding: 15px; border-bottom: 1px solid #efefef;">
											<?php 
												$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
												$post_thumbnail_src = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );

												if( isset( $post_thumbnail_src[0] ) ){
													echo "<img src='{$post_thumbnail_src[0]}' style='width: 100%;' />";															
												}
											?>
										</td>
									</tr>
									<?php endif; ?>

									<tr>
										<td style="padding: 15px;" width="300">

											<h3 style="margin-top: 0;">
												<a href="<?php echo get_permalink( $post->ID ); ?>" title="<?php echo $post->post_title; ?>">
													<?php echo $post->post_title; ?>
												</a>
											</h3>

											<?php
												$content = $post->post_content;
										        // $content = apply_filters( 'the_content', $post->post_content );
										        $content = str_replace( ']]>', ']]&gt;', $content );													
												$content = preg_replace( '/(width|height)="\d*"\s/', "", $content );
												$content = str_replace( '" src="', '" style="width:100%;" src="', $content );
												echo wpautop( $content );
											?>
										</td>
									</tr>
								</table>	

							</td>
						</tr>

						<?php
						endforeach;
						?>

					<?php
					endif;
					?>

				<?php
				endforeach;
				?>

				</tbody>
			</table>

			<?php
		}
	}
}