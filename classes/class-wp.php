<?php

$imperialTutors = new imperialTutors();

class imperialTutors
{


	//~~~~~
	function __construct ()
	{
		
		$this->addWPActions();
		
	}
	

	
/*	---------------------------
	PRIMARY HOOKS INTO WP 
	--------------------------- */	
	function addWPActions ()
	{
				
		//Frontend
		add_action( 'wp_footer', array( $this, 'frontendEnqueues' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'adminSettingsEnqueues' ) );
		
		
		//add_action( 'admin_menu', array( $this, 'create_AdminPages' ) );
		
		
		// Add shortcode to display placements
		//add_shortcode( 'imperial-placements', array( 'imperialPlacementsDraw', 'drawPlacementsFrontEnd' ) );
		//add_shortcode( 'my-placements', array( 'imperialPlacementsDraw', 'drawMyPlacements' ) );
		
	
	}
	
	function adminSettingsEnqueues ()
	{
		//WP includes
		wp_enqueue_script('jquery');
	}

	
	function create_AdminPages()
	{
		
		/* Create Admin Pages */

		$page_title="Tutors";
		$menu_title="Tutors";
		$menu_slug="imperial-tutors";
		$function=  array( $this, 'drawTutorsPage' );
		$myCapability = "manage_options";		
		
		add_menu_page( $page_title, $menu_title, $myCapability, $menu_slug, $function, 'dashicons-businessman', 6  );		
			
	}
	
	
	function drawTutorsPage()
	{
		include_once( TUTORS_PATH . '/admin/tutors_admin.php' );
	}	
		
	
	
	function frontendEnqueues ()
	{
		//Scripts
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style( 'imperial-tutors', TUTORS_PLUGIN_URL . '/css/styles.css' );
		
		wp_enqueue_style('jquery-ui-style','//ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',false,"1.9.0",false);
		
		
		// Register Ajax script for front end
		wp_enqueue_script('tutors_booking_ajax', TUTORS_PLUGIN_URL.'/js/tutors-ajax.js', array( 'jquery' ) );
			
		
		//Localise the JS file
		$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'ajax_nonce' => wp_create_nonce('ajaxCalendarNonce'),
		);
		
		
		wp_localize_script( 'tutors_booking_ajax', 'tutorBookingParams', $params );			
		

		
	}	
	

			

	
	
}

?>