<?php
$tutorsAjax = new tutorsAjax();
class tutorsAjax
{
	
	//~~~~~
	public function __construct ()
	{
		$this->addWPActions();
	}	
	
	
	function addWPActions()
	{	
	
		// Show calendar when clicked next month etc
		add_action( 'wp_ajax_drawCalendar', array($this, 'drawCalendar' ));
		
		// Show available slots for a date click
		add_action( 'wp_ajax_drawDaySlots', array($this, 'drawDaySlots' ));
		
		// Confirms slot booking prompt
		add_action( 'wp_ajax_confirmSlotBookingCheck', array($this, 'confirmSlotBookingCheck' ));
		
		// Adds booking slot to the DB
		add_action( 'wp_ajax_confirmSlotBooking', array($this, 'confirmSlotBooking' ));

		

	}
	public function drawCalendar()
	{
		
		
		// Check the AJAX nonce				
		check_ajax_referer( 'ajaxCalendarNonce', 'security' );
		
		$month = $_POST['month'];
		$year = $_POST['year'];
		$tutorUsername = $_POST['tutorUsername'];
		
		$cal = imperialTutorsDraw::drawBookingCalendar($month, $year, $tutorUsername);
		echo $cal;

		die();
	}	
	
	
	
	public static function  drawDaySlots()
	{
		
		
		$slotDate = $_POST['thisDate'];	
		$slotDateText =date('l, jS F Y', strtotime($slotDate) );
		
		$tutorUsername = $_POST['tutorUsername'];			
		$activeSlots = imperialTutorQueries::getActiveSlots($tutorUsername, $slotDate);
		
		
		echo '<div class="availableSlotsDiv">';		
		echo '<h2>'.$slotDateText.'</h2>';
		echo '<table class="imperial-table">';
		foreach ($activeSlots as $slotInfo)
		{
			$slotID = $slotInfo->slotID;
			$duration = $slotInfo->duration;
			$location = $slotInfo->location;
			$thisSlotDate = $slotInfo->slotDate;
			$thisSlotDateTime = new DateTime($thisSlotDate);			
			
			$slotTime =  $thisSlotDateTime->format('g:i a');			
		
			echo '<tr>';
			echo '<td>'.$slotTime.'</td>';
			echo '<td>'.$duration.' mins</td>';
			echo '<td><a href="javascript:confirmSlotBookingCheck('.$slotID.')" class="imperial-button">Book this slot</a></td>';
			echo '</tr>';	
		}		
		echo '</table>';	
		echo '</div>';

		die();		
	}
	
	public static function confirmSlotBookingCheck()
	{
		
		$slotID = $_POST['slotID'];
		
		
		$slotInfo = imperialTutorQueries::getSlotInfo($slotID);
		$slotDate = $slotInfo['slotDate'];	
		
		$slotDateText =date('l, jS F Y, g:i a', strtotime($slotDate) );
				
		echo '<div class="availableSlotsDiv">';						
		echo '<h2>Please confirm your booking</h2>';		
		echo 'Are you sure you want to book this slot?';
		
		
		echo '<h3>'.$slotDateText.'</h3>';
		//echo '<textarea placeholder="Optional Message to your tutor" id="></textarea>';
		echo '<a href="javascript:confirmSlotBooking('.$slotID.')" class="imperial-button">Book this slot</a>';
		echo '</div>';
		
		die();		

	}
	
	
	public static function confirmSlotBooking()
	{
		
		$slotID = $_POST['slotID'];
		
		$slotInfo = imperialTutorQueries::getSlotInfo($slotID);
		$slotDate = $slotInfo['slotDate'];	
		$UID = $slotInfo['UID'];
		$tutorUsername = $slotInfo['tutorUsername'];
				
		$tutorInfo = imperialQueries::getUserInfo($tutorUsername);	
		$tutorEmail = $tutorInfo['email'];
		$tutorName = $tutorInfo['first_name'].' '.$tutorInfo['last_name'];
		if($tutorInfo['preferred_email']<>"")
		{
			$tutorEmail = $tutorInfo['preferred_email'];
		}
		
		$slotDateText =date('l, jS F Y, g:i a', strtotime($slotDate) );
		
		// Update into the DB
		global  $wpdb;
		global $dbTable_tutorBookings;
		
		$table_name = $imperialTutorsDB->dbTable_tutorBookings;
		
		$currentUsername = $_SESSION['username'];
		$currentUserEmail = $_SESSION['email'];
		$currentFullname = $_SESSION['fullname'];

		// Do the update			
		$wpdb->query( $wpdb->prepare(
			"UPDATE  $dbTable_tutorBookings SET tuteeUsername=%s WHERE slotID = %d",	$currentUsername, $slotID
		));  
		
				
		echo '<div class="availableSlotsDiv">';				
		echo '<h2>Thank you - your slot has been booked</h2>';		
		
		echo '<h3>'.$slotDateText.'</h3>';
				
		// Email The Student			
		echo 'An email has been sent to your email address ('.$currentUserEmail.') with a calendar item';	
		echo '</div>';
		
		
		
		// Get the slot Info and send iCal Invites
		$eventType='invite';
		$messageBody ='This is your calendar invite for your tutor meeting with '.$tutorName.' on '.$slotDateText;
		sendIcalEvent($slotID, $eventType, $currentFullname, $currentUserEmail, $messageBody);
		
		// Send to Tutor as Well
		$eventType='invite';
		$messageBody ='New meeting request from one of your tutees ('.$currentFullname.')  on '.$slotDateText;
		sendIcalEvent($slotID, $eventType, $tutorName, $tutorEmail, $messageBody);
		die();


	}	
	
	
} // End Class
?>