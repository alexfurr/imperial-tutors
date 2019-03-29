<?php

$imperialTutorsDB = new imperialTutorsDB();

class imperialTutorsDB
{
	var $DBversion 		= '0.5';	
	
	//~~~~~
	function __construct ()
	{
		add_action( 'init',  array( $this, 'checkCompat' ) );

		global $dbTable_tutorBookings;	
		global $wpdb;
		$dbTable_tutorBookings = $wpdb->base_prefix . 'imperial_tutor_bookings';

		
		
	}

	//~~~~~
	function checkCompat ()
	{
		
		// Get the Current DB and check against this verion
		$currentDBversion = get_option('placementsDB_version');
		$thisDBversion = $this->DBversion;
		
		if($thisDBversion>$currentDBversion)
		{
			$this->createTables();
			update_option('tutorsDB_version', $thisDBversion);			
		}
		//$this->createTables();
		
		
	}
	
	
	
	function createTables ()
	{

		global $wpdb;
		global $dbTable_tutorBookings;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$WPversion = substr( get_bloginfo('version'), 0, 3);
		$charset_collate = ( $WPversion >= 3.5 ) ? $wpdb->get_charset_collate() : $this->getCharsetCollate();

		
		//users table
		$sql = "CREATE TABLE $dbTable_tutorBookings (
			slotID mediumint(9) NOT NULL AUTO_INCREMENT,
			UID varchar(255),
			tutorUsername varchar(50),
			slotDate datetime NOT NULL,		
			duration int NOT NULL,
			location varchar(255),
			tuteeUsername varchar(50),
			tookPlace tinyint,
			INDEX tuteeBookings (tuteeUsername),
			INDEX (tutorUsername),
			INDEX (tutorUsername),
			PRIMARY KEY (slotID)			
			
		) $charset_collate;";
			
		$feedback = dbDelta( $sql );

			
	}

	
	function getCharsetCollate () 
	{
		global $wpdb;
		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) )
		{
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) 
		{
			$charset_collate .= " COLLATE $wpdb->collate";
		}
		return $charset_collate;
	}	

}



?>