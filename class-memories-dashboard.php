<?php
class Memories_Dashboard{

	var $capability;
	var $templates;
	var $memories;

	/**
	 * Constructing the class
	 */
	function __construct(){
		$this->capability = 'import';
		$this->templates = new Memories_Templates;
		$this->memories = new Memories;

		add_action( 'admin_menu', array( $this, 'add_pages' ) );
	}	

	/**
	 * Adding page
	 * 
	 * @return void
	 */
	function add_pages(){
		add_submenu_page( 'tools.php', __( 'Memories', 'memories' ), __( 'Memories', 'memories' ), $this->capability, 'memories', array( $this, 'page' ) );
	}

	/**
	 * Render settings page
	 * 
	 * @return void
	 */
	function page(){
		// Get today's posts
		$today_posts = $this->memories->get_today_posts();

		?>
		<div class="wrap">
			<h2><?php _e( 'Memories', 'memories' ); ?></h2>
			<h4><?php printf( __( 'Hi, %s published in this blog today (<cite>%s</cite>) in history. We will send you an email to <code>%s</code> every morning.', 'memories' ), ngettext( 'this is post that was', 'these are posts that were', $today_posts->post_count ), date( 'l, F j, Y', current_time( 'timestamp' ) ), get_bloginfo( 'admin_email' ) ); ?></h4>
			<p><?php _e( 'Note: We are sending the email that is used on <code>Dashboard &rarr; Settings &rarr; General &rarr; E-mail Address</code>.', 'memories' ); ?></p>

			<?php 
				// Display today's posts content
				$this->templates->today_posts( $today_posts );
			?>

		</div>
		<?php
	}
}
new Memories_Dashboard;