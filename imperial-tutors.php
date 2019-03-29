<?php
/*
Plugin Name: Imperial Tutors
Description: Imperial Tutors - booking forms and dashboard views
Version: 0.1
Author: Alex Furr
*/

// Global defines
define( 'TUTORS_PLUGIN_URL', plugins_url('imperial-tutors' , dirname( __FILE__ )) );
define( 'TUTORS_PATH', plugin_dir_path(__FILE__) );


// Create the table for placement allocations
include_once( TUTORS_PATH . '/classes/class-wp.php');
include_once( TUTORS_PATH . '/classes/class-db.php');
include_once( TUTORS_PATH . '/classes/class-draw.php');
include_once( TUTORS_PATH . '/classes/class-queries.php');
include_once( TUTORS_PATH . '/classes/class-calendar.php');
include_once( TUTORS_PATH . '/classes/class-ajax.php');
include_once( TUTORS_PATH . '/classes/class-actions.php');
include_once( TUTORS_PATH . '/classes/class-utils.php');



?>