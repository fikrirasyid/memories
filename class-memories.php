<?php
/**
 * Get past contents (Memories)
 */
class Memories{

	var $current_time;

	/**
	 * Constructing the class
	 */
	function __construct(){
		$this->current_time = current_time( 'timestamp' );
	}

	/**
	 * Get posts published in current date in history
	 * 
	 * @return obj
	 */
	function get_today_posts(){
		$day 	= date( 'j', $this->current_time );
		$month 	= date( 'n', $this->current_time );

		// Just in case you want to randomise the content for setting purpose
		// $day = array_rand( range( 1, 31 ) );
		// $month = array_rand( range( 1, 12 ) );

		$posts 	= $this->get_posts( $day, $month );

		return $posts;
	}

	/**
	 * Get posts by date and month given
	 * 
	 * @return obj
	 */
	function get_posts( $day = 1, $month = 1 ){
		$posts = new WP_Query( array(
			'ignore_sticky_posts' => true,
			'date_query' => array(
				array(
					'day'	=> $day,
					'month' => $month
				)
			)
		) );

		$posts->year = 0;

		return $posts;
	}
}