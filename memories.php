<?php
/*
 * Plugin Name: Memories
 * Plugin URI: http://wordpress.org/plugins/memories/
 * Description: Rediscover the post(s) you published years ago
 * Author: Fikri Rasyid
 * Version: 0.1
 * Author URI: fikrirasyid.com/wordpress-plugins/memories/
 * License: GPL2+
 * Text Domain: memories
 */

define( 'MEMORIES__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

class Memories_Setup{
	var $templates;
	var $memories;

	function __construct(){
		$this->requiring_files();

		$this->templates = new Memories_Templates;
		$this->memories = new Memories;

		add_action( 'daily_memories', array( $this, 'daily_email' ) );

		register_activation_hook( __FILE__, array( $this, 'activation' ) );

		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
	}

	/**
	 * Requiring external files
	 * 
	 * @return void
	 */
	function requiring_files(){
		require_once( MEMORIES__PLUGIN_DIR . 'class-memories.php' );
		require_once( MEMORIES__PLUGIN_DIR . 'class-memories-templates.php' );
		require_once( MEMORIES__PLUGIN_DIR . 'class-memories-dashboard.php' );
	}

	/**
	 * Activation task. Do this when the plugin is activated
	 * 
	 * @return void
	 */
	function activation(){
		if( !wp_next_scheduled( 'daily_memories' ) ){
			// Register the schedule a minute after now, so user can feel the result rightaway
			$current_time = current_time( 'timestamp' );

			// Get today's 01:00 timestamp
			$today = mktime( 1, 0, 0, date( 'n', $current_time ), date( 'j', $current_time ), date( 'Y', $current_time ) );

			// Applying offset
			$timezone_offset = wp_timezone_override_offset();

			if( $timezone_offset ){
				$today = $today - ( $timezone_offset * 60 * 60 );
			}
			
			wp_schedule_event( $today, 'daily', 'daily_memories' );
		}
	}

	/**
	 * Deactivation task. Do this when the plugin is deactivated
	 * 
	 * @return void
	 */
	function deactivation(){
		wp_clear_scheduled_hook( 'daily_memories' );
	}

	/**
	 * Sending email to administrator
	 * 
	 * @return void
	 */
	function daily_email(){
		// Setup email destination
		$to 		= get_option( 'admin_email' );

		// Get today's posts
		$today_posts = $this->memories->get_today_posts();

		// Setup subject
		$subject 	= sprintf( __( '%s %s Today (%s) in History', 'memories' ), get_bloginfo( 'name' ), ngettext( 'Post', 'Posts', $today_posts->post_count ), date( 'F j, Y', current_time( 'timestamp' ) ));

		$message = '<h4>'. sprintf( __( 'Hi, %s published in this blog today (<cite>%s</cite>) in history. Have a good time with it.', 'memories' ), ngettext( 'this is post that was', 'these are posts that were', $today_posts->post_count ), date( 'F j', current_time( 'timestamp' ) ) ) .'</h4>';

		ob_start();

			// Display today's posts content
			$this->templates->today_posts( $today_posts );

		$message .= ob_get_clean();

		add_filter( 'wp_mail_content_type', array( $this, 'html_content_type' ) );

		$sent = wp_mail( $to, $subject, $message );

		remove_filter( 'wp_mail_content_type', array( $this, 'html_content_type' ) );

		die();
	}

	/**
	 * Set HTML content type
	 * 
	 * @return string
	 */
	function html_content_type(){
		return 'text/html';
	}
}
new Memories_Setup;