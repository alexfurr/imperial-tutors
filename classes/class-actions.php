<?php

class imperialTutorActions
{
	public static function cancelSlot($slotID)
	{
		
		// Check if they are admin OR logged in user is ONE of the above
		$slotInfo = imperialTutorQueries::getSlotInfo($slotID);
		$tutorUsername = $slotInfo['tutorUsername'];
		$tuteeUsername = $slotInfo['tuteeUsername'];
		$slotDate = $slotInfo['slotDate'];
		$slotDateText =date('l, jS F Y, g:i a', strtotime($slotDate) );
		$eventType='cancel';		

		
		// Only send the cancelaltion email if there is a booking for it
		if($tuteeUsername<>"")
		{
			$tutorInfo = imperialQueries::getUserInfo($tutorUsername);
			$tutorName = $tutorInfo['first_name'].' '.$tutorInfo['last_name'];
			$tutorEmail = $tutorInfo['email'];
			if($tutorInfo['preferred_email']<>"")
			{
				$tutorEmail = $tutorInfo['preferred_email'];
			}
			
			if($tuteeUsername<>"")
			{

				$tuteeInfo = imperialQueries::getUserInfo($tuteeUsername);
				$tuteeName = $tuteeInfo['first_name'].' '.$tuteeInfo['last_name'];
				$tuteeEmail = $tuteeInfo['preferred_email'];
				
			}				
			// Send cancelleation to tutor
			$messageBody ='This is your cancellation for your tutor meeting with '.$tuteeName.' on '.$slotDateText;
			sendIcalEvent($slotID, $eventType, $tutorName, $tutorEmail, $messageBody);
			
			// Send to tutee as well
			$messageBody ='New meeting request from one of your tutees ('.$tuteeName.')  on '.$slotDateText;
			sendIcalEvent($slotID, $eventType, $tuteeName, $tuteeEmail, $messageBody);				
		
		
		}		
		

		global $wpdb;
		global $dbTable_tutorBookings;
				
		$SQL = "DELETE FROM  ".$dbTable_tutorBookings." WHERE slotID = ".$slotID;
		$wpdb->query( $SQL );
		
		
		

	}	
	

	
	
	
	public static function createSlots($tutorUsername)
	{
		
		// Create array blank dates and blank times
		$slotDateArray = array();
		$startTimesArray = array();
		foreach ($_POST as $KEY => $VALUE)	
		{
			$$KEY = $VALUE;
		}
		
		
		if($creationType=="single")
		{
			$slotDateArray[]=$singleDatepicker;
			
			
			/*
			if($singleMin_AMPM=="PM")
			{
				$singleHour = $singleHour+12;
			}
			*/
			$startTime = $singleHour.':'.$singleMin;
			$startTimesArray[]=$startTime;
			$whichTutee = $_POST['whichTutee'];

		}
		else
		{
		
		
			$startDate = strtotime($multiDatepickerStart);
			$endDate = strtotime($multiDatepickerEnd);

			$whichTutee = '';
			
			// Get the date array
			switch ($frequency)
			{
				case "day":
					$timeFrequency = '+1 day';
				break;
				
				case "weekly":			
					$timeFrequency = '+1 week';
				break;
				
				case "fortnightly":			
					$timeFrequency = '+2 weeks';
				break;	

				case "monthly":			
					$timeFrequency = '+1 month';
				break;				
				
				
			}
	
			$i = $startDate;
			
			while ($i<=$endDate)
			{		
				$thisDate = date('Y-m-d', $i);			
				$slotDateArray[] = $thisDate;
				$i = strtotime($timeFrequency, $i);						
			}			
			
			
			
			/*
			if($multiMin_AMPM=="PM")
			{
				$multiHour = $multiHour+12;
			}
			*/
			$startTime = $multiHour.':'.$multiMin;
		
			$startTimesArray[] = $startTime;
			
			if($slotCount>1)
			{
				$i=1;
				$lastStartTime = $startTime;
				
				$interval = $duration + $timeBetween;
				while ($i<$slotCount)
				{
					$nextStartTime = strtotime("+".$interval." minutes", strtotime($lastStartTime));
					$lastStartTime =  date('H:i', $nextStartTime);
					
					$startTimesArray[] = $lastStartTime;

					$i++;
					
				}
			}
		}
		
		
		// Now go through all the dates and times and add to the DB		
		global $wpdb;
		global $dbTable_tutorBookings;
	
		foreach ($slotDateArray as $myDate)
		{
			foreach ($startTimesArray as $myTime)
			{
				$thisDateTime = $myDate.' '.$myTime;
				
				// Create unique ID for this item
				$UID = md5(uniqid(mt_rand(), true)) . "@medlearn.imperial.ac.uk\r\n";
									
				$wpdb->query( $wpdb->prepare(
				"INSERT INTO ".$dbTable_tutorBookings." (tutorUsername, tuteeUsername, slotDate, duration, location, UID) 
				VALUES ( %s, %s, %s, %d, %s, %s )",
				array(
					$tutorUsername,
					$whichTutee,
					$thisDateTime,
					$duration,
					$location,
					$UID,
					)
				));

			}					
		}
	
	}
	
} // End Class
?>