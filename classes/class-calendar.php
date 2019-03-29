<?php

class tutorCalendar
{

	
	public static function getMonth ($monthNum)
	{
		$dateObj   = DateTime::createFromFormat('!m', $monthNum);
		$monthName = $dateObj->format('F'); // March
		
		return $monthName;
	}
	
	
	public static function generateMonthDayArray($month, $year)
	{
		// Get the number of days in this month
		$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		
		if($month<10){$month='0'.$month;} // Format the month if missing a zero
		
		
		// Get the first day
		$firstDayDate = $year.'-'.$month.'-01';
				
		$firstDayNumber = date('N', strtotime($firstDayDate));
	
		
		// Calculate how many additional blank divs to add based on the first day
		// e.g. A thursday would have three additional divs
		$startBlankCount = $firstDayNumber-1;
		if($startBlankCount<0){$startBlankCount=0;} // Can't be less than zero!
		
		
		//Create the master array
		$monthDayArray = array();
		
		$thisWeekdayCount=0;
		$currentWeek=1;
		$tempWeekArray = array();
		
		$i=0;
		while($i<$startBlankCount)
		{
			$tempWeekArray[]="";
			$i++;
		}
		
		// Add any blank divs to the start
		
		$thisDay = 1;
		while($thisDay <= $daysInMonth)
		{
			
			// Count the items in the array
			if(count($tempWeekArray)>=7)
			{
				$monthDayArray[] = $tempWeekArray; // Add this week to the array
				$tempWeekArray = array(); // Clear the temp array
			}
			
			$tempWeekArray[] = $thisDay;						
			$thisDay++;
		}
		
		// Check if there are any items left in the temp array and add if not
		if(count($tempWeekArray)>=1)
		{
			$daysLeft = 7 - count($tempWeekArray);
			$i=0;
			while($i<$daysLeft)
			{
				$tempWeekArray[] = "";
				$i++;
			}
			
			$monthDayArray[] = $tempWeekArray; // Add this week to the array
		}	
		
		return $monthDayArray;
	}	

}




function sendIcalEvent($slotID, $eventType, $to_name, $to_address, $messageBody)
{
	
	$slotInfo = imperialTutorQueries::getSlotInfo($slotID);
	$startDate = $slotInfo['slotDate'];	
	$duration = $slotInfo['duration'];
	$location = $slotInfo['location'];
	$UID = $slotInfo['UID'];
	
	$subject = 'New Tutor Booking Invitation';
	$method="REQUEST";
	$status="CONFIRMED";
	
	if($eventType=="cancel")
	{
		$subject = 'Tutor Booking Cancellation';
		$method="CANCEL";
		$status="CANCELLED";
	}
	
	$tuteeUsername = $slotInfo['tuteeUsername'];
	$tuteeInfo = imperialQueries::getUserInfo($tuteeUsername);
	$tuteeName = $tuteeInfo['first_name'].' '.$tuteeInfo['last_name'];	
	$tuteeEmail = $tuteeInfo['email'];
			
	$endDate = strtotime($startDate.' + '.$duration.' minutes');
	$endDate =  date('Y-m-d H:i:s', $endDate);
	
	$from_name = $tuteeName;
	$from_address = $tuteeEmail;
	
	//$from_name = "Alex Furr";
	//$from_address = "afurr@ic.ac.uk";
	//$to_address = "afurr@ic.ac.uk";
	
	//$mail_from_name = "Medlearn Tutor Bookings";
	//$mail_from_address = "donotreply@medlearn.imperial.ac.uk";
		
    //Create Email Headers
    $mime_boundary = "----Meeting Booking----".MD5(TIME());

	$headers='';
    //$headers = "From: ".$from_name." <".$from_address.">\n";
    //$headers .= "Reply-To: ".$tuteeName." <".$tuteeEmail.">\n";
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
    $headers .= "Content-class: urn:content-classes:calendarmessage\n";
    
    //Create Email Body (HTML)
    $message = "--$mime_boundary\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\n";
    $message .= "Content-Transfer-Encoding: 8bit\n\n";
    $message .= "<html>\n";
    $message .= "<body>\n";
    $message .= '<p>Dear '.$to_name.',</p>';
    $message .= '<p>'.$messageBody.'</p>';
    $message .= "</body>\n";
    $message .= "</html>\n";
    $message .= "--$mime_boundary\r\n";
		
	
	// Convert to UTC
	$tz_from = 'Europe/London';
	$tz_to = 'UTC';
	$format = 'Ymd\THis\Z';

	// Create Start Date
	$dt = new DateTime($startDate, new DateTimeZone($tz_from));
	$dt->setTimeZone(new DateTimeZone($tz_to));
	$startDateICS =  $dt->format($format) . "\n";

	// Create End Date
	$dt->add(new DateInterval('PT' . $duration . 'M'));
	$endDateICS =  $dt->format($format) . "\n";	
	
    $ical = 'BEGIN:VCALENDAR' . "\r\n" .
    'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
    'VERSION:2.0' . "\r\n" .
    'METHOD:'.$method. "\r\n" .  
   
	
    'BEGIN:VEVENT' . "\r\n" .
    'ORGANIZER;CN="'.$from_name.'":MAILTO:'.$from_address. "\r\n" .
    'ATTENDEE;CN="'.$to_name.'";ROLE=REQ-PARTICIPANT\r\n"' .	
    'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
    'UID:'.$UID."\r\n" .
    'DTSTAMP:'.date("Ymd\TGis"). "\r\n" .
    'DTSTART:'.$startDateICS. "\r\n" .
    'DTEND:'.$endDateICS. "\r\n" .
	
    'TRANSP:OPAQUE'. "\r\n" .
    'SEQUENCE:1'. "\r\n" .
	'STATUS:'.$status.'' .
    'SUMMARY:' . $subject . "\r\n" .
    'LOCATION:' . $location . "\r\n" .
    'CLASS:PUBLIC'. "\r\n" .
    'PRIORITY:5'. "\r\n" .
    'BEGIN:VALARM' . "\r\n" .
    'TRIGGER:-PT15M' . "\r\n" .
    'ACTION:DISPLAY' . "\r\n" .
    'DESCRIPTION:Reminder' . "\r\n" .
    'END:VALARM' . "\r\n" .
    'END:VEVENT'. "\r\n" .
    'END:VCALENDAR'. "\r\n";
    $message .= 'Content-Type: text/calendar;name="meeting.ics";method=REQUEST'."\n";
    $message .= "Content-Transfer-Encoding: 8bit\n\n";
    $message .= $ical;

    $mailsent = mail($to_address, $subject, $message, $headers);

    return ($mailsent)?(true):(false);
}





?>