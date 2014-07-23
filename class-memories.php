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
		$posts_grouped = $this->group_by_year( $posts );

		return $posts_grouped;
	}

	/**
	 * Get posts by date and month given
	 * 
	 * @return obj
	 */
	function get_posts( $day = 1, $month = 1 ){
		$posts = get_posts( array(
			'date_query' => array(
				array(
					'day'	=> $day,
					'month' => $month
				)
			)
		) );

		return $posts;
	}

	/**
	 * Group posts by year
	 * 
	 * @return array
	 */
	function group_by_year( $items ){
		// Prepare to catch the items
		$posts = array();

		if( !empty( $items ) ){

			foreach ( $items as $item ) {

				$posts[date( 'Y', strtotime( $item->post_date ) )][$item->ID] = $item;

			}
		}

		return $posts;
	}
}