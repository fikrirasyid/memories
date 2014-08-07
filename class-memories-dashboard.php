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
		?>
		<div class="wrap">
			<h2><?php _e( 'Memories', 'memories' ); ?></h2>
			<p><?php printf( __( 'Hi, this is what is being posted in this blog today (<cite>%s</cite>) in history:', 'memories' ), date( 'l, F j, Y', current_time( 'timestamp' ) ) ); ?></p>

			<?php 
				// Get today's posts
				$today_posts = $this->memories->get_today_posts();

				// Display today's posts content
				$this->templates->today_posts( $today_posts );
			?>

		</div>
		<?php
	}
}
new Memories_Dashboard;