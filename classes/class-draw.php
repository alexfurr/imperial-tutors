<?php

class imperialTutorsDraw
{
	
	public static function drawBookingCalendar($month="", $year="", $tutorUsername="")
	{
		
		$str='';
		
		if($month==""){$month = date('n');}
		if($year==""){$year = date('Y');}
		
		if($tutorUsername=="")
		{
			$tutorUsername = $_GET['username'];
		}

		
		
		// Get the next month and year
		$nextYear = $year;
		$nextMonth = $month+1;
		$prevYear = $year;
		$prevMonth = $month-1;
		
		if($month==12)
		{
			$nextMonth=1;
			$nextYear = $year+1;
		}
		if($month==1)
		{
			$prevMonth=12;
			$prevYear = $year-1;
		}
		
		$thisMonth= $month;
		if($month<10){$thisMonth = '0'.$month;}
		
		// End of prev / next year calculations
				
		$availableSlots = imperialTutorQueries::getActiveSlots($tutorUsername);
		$slotArray = array();
		// Go through the slots and put them in an array with the data 
		foreach ($availableSlots as $slotInfo)
		{
			// Remove the time to get the slot date
			$slotDate = date('Y-m-d', strtotime($slotInfo->slotDate) );
			
			$slotArray[$slotDate][] = array
			(
				"slotDate" => $slotInfo->slotDate,
				"slotDuration"	=> $slotInfo->duration,
				"slotLocation"	=> $slotInfo->location,
			);
		}		

		$monthArray = tutorCalendar::generateMonthDayArray($month, $year);		
		$monthName = tutorCalendar::getMonth($month);
		$str.='<div class="monthWrapper">';		
		$str.='<div class="prevMonthLink loadCalMonth" id="prevMonth_'.$prevMonth.'_'.$prevYear.'_'.$tutorUsername.'"><i class="fas fa-chevron-circle-left"></i></div>';
		$str.='<div class="monthName">';
		$str.='<div class="currentYear">'.$year.'</div>';
		$str.='<div class="currentMonth">'.$monthName.'</div>';
		$str.='</div>';
		$str.='<div class="nextMonthLink loadCalMonth" id="prevMonth_'.$nextMonth.'_'.$nextYear.'_'.$tutorUsername.'"><i class="fas fa-chevron-circle-right"></i></div>';
		$str.='</div>';
		
		$dayArray = array("Mon", "Tues", "Weds", "Thurs", "Fri", "Sat", "Sun");
		$str.= '<div class="month">';
		$str.='<div class="week day-name">';
		foreach($dayArray as $dayName)
		{
			$str.='<div class="day dayName">'.$dayName.'</div>';
		}
		$str.='</div>'; // End of month div
		
	
		foreach ($monthArray as $week)
		{
			$str.= '<div class="week">';
			foreach ($week as $thisDay)
			{
				
				$thisDayText = $thisDay;
				if($thisDay<10){$thisDay = '0'.$thisDay;}
				
				$thisFullDate = $year.'-'.$thisMonth.'-'.$thisDay;
				
				$activeClass= '';
				if(array_key_exists($thisFullDate, $slotArray) )
				{
					$activeClass = ' hasSlots ';
				}
				
				$str.='<div class="day"><div class="dayNumber '.$activeClass.'" id="hasSlots_'.$thisFullDate.'_'.$tutorUsername.'">'.$thisDayText.'</div></div>';
			}
			$str.='</div>'; // End of week
		}
		$str.= '</div>'; // End of Month		
		
		return $str;
	
	}
	
	
	public static function drawTutorSlots($username, $pastOrFuture="all")
	{
		$html='';
		
		// Get the list of upcoming timeslots
		$myTimeslots = imperialTutorQueries::getAllSlots($username);
		
		if($pastOrFuture<>"all")
		{
			//$myTimeslots = imperialTutorQueries::getMyBookedSlots($username);
		}
		
		$rowCount=0;

		$slotCount = count($myTimeslots);

		if($slotCount==0)
		{
			
			switch ($pastOrFuture)
			{
				
				case "past":
					$html.= 'You have no previous meetings';				
				break;	
				
				case "all":
				case "future":
					$html.= 'You have no upcoming timeslots';				
				break;
				
			}
			
			
			
		}

		else
		{
			
			
			
			// Create Blank Array for the months
			$myTimeslotsByMonth = array();
			
			
			
			$html.= '<form method="post" action="?view=tutee-timeslots&username='.$username.'&action=bulkDeleteSlots&tab=upcoming">';
			
			foreach($myTimeslots as $slotInfo)
			{
				
				$slotID = $slotInfo->slotID;
				$slotDate = $slotInfo->slotDate;
				$duration = $slotInfo->duration;
				$location = $slotInfo->location;				
				$tuteeUsername = $slotInfo->tuteeUsername;
				$tookPlace = $slotInfo->tookPlace;
				
				
				
				$showRow = false;
				
				$currentDateTime = date('Y-m-d H:i:s');
				$currentDateTime = imperialTutorUtils::getUKdate($currentDateTime);				
				
				switch($pastOrFuture)
				{
					case "future":
						if($slotDate > $currentDateTime && $tuteeUsername<>"")
						{
							$showRow=true;
						}				
					
					break;
					
					case "past":
						if($slotDate < $currentDateTime && $tuteeUsername<>"")
						{
							$showRow=true;
						}							
					
					break;
					
					case "all":
						if($slotDate > $currentDateTime)
						{
							$showRow=true;
						}					
					break;
					
				}
				
				
				// Don't show things if the date is screwed				
				if($slotDate=="0000-00-00 00:00:00")
				{
					$showRow = false; 
				}

				
				
				
				if($showRow==true)
				{
					$rowCount++;
					$endTime = new DateTime($slotDate);
					$endTime->modify('+'.$duration.' minutes');				
					$endDateTime = $endTime->format('g:i a');
					
					
					
					$slotDate = date_create($slotDate);
					
					// Convert to month and year for ordering
					$monthYear = date_format($slotDate, 'Y-m');
					
					$slotDateText = date_format($slotDate, 'l jS F, Y');
					$slotTime = date_format($slotDate, 'g:i a ');
					
					$myTimeslotsByMonth[$monthYear][] = array(
						"slotID" => $slotID,
						"slotDate" => $slotDate,
						"location" => $location,
						"tuteeUsername" => $tuteeUsername,
						"slotDateText" => $slotDateText,
						"slotTime" => $slotTime,
						"endTime" => $endDateTime,
						"tookPlace"	=> $tookPlace,
					);
				}
				

			}

			
			// reverse the array if its PREVIOUS
			$myTimeslotsByMonth = array_reverse($myTimeslotsByMonth);
			
			
			
			$lastMonth = '';

			foreach($myTimeslotsByMonth as $thisMonth => $monthSlotsArray)
			{	
			
				if($lastMonth<>$thisMonth)
				{
					$tempArray = explode("-", $thisMonth);
					$thisYear = $tempArray[0];			
					$thisMonthNumber = $tempArray[1];
					
					$dateObj   = DateTime::createFromFormat('!m', $thisMonthNumber);
					$monthName = $dateObj->format('F'); // March			

					$html.= '<h2>'.$monthName.' '.$thisYear.'</h2>';
				}
			
				$html.= '<table class="imperial-table"><tr>';
				if($pastOrFuture=="future" || $pastOrFuture=="all")
				{
					$html.='<th></th>';
				}
				
				
				
				$html.='<th>Appointment Date</th><th>Time</th><th>Student</th>';
				
				
				
				
				$html.='<th></th></tr>';

				foreach ($monthSlotsArray as $slotInfo)
				{
			
					$slotID = $slotInfo['slotID'];
					$slotDate = $slotInfo['slotDate'];
					$location = $slotInfo['location'];
					$endTime = $slotInfo['endTime'];
					$tookPlace = $slotInfo['tookPlace'];
					
					
					$location = imperialNetworkUtils::convertTextFromDB($location);
					
					$tuteeUsername = $slotInfo['tuteeUsername'];
					$slotDateText = $slotInfo['slotDateText'];
					$slotTime = $slotInfo['slotTime'];		
					
					$html.= '<tr>';
					
					if($pastOrFuture=="future" || $pastOrFuture=="all")
					{						
					
						$html.= '<td width="10px" valign="top"><input type="checkbox" name="check_list[]" value="'.$slotID.'" id="delete_'.$slotID.'"></td>';
					}
					
					$html.= '<td width="400px" valign="top">';
					$html.='<label for="delete_'.$slotID.'">'.$slotDateText.'<br/><span class="smallText">'.$location.'</span></label></td>';
					$html.= '<td width="100px" valign="top">'.$slotTime.' - '.$endTime.'</td>';
					
					$html.= '<td width="100px">';
					if($tuteeUsername)
					{
						$tuteeInfo = imperialQueries::getUserInfo($tuteeUsername);
						$cid = $tuteeInfo['userID'];
						$args = array(			
							"CID"		=> $cid,
						);
						$avatarURL = get_user_avatar_url( $args);						
						
						
						$html.='<div style="display:flex; align-items: center;">';
						$html.='<div class="studentAvatar">';
						$html.='<div class="rounded-image" style="width:50px; height:50px">';
						$html.= '<a href="?username='.$tuteeUsername.'">';
						$html.='<img src="'.$avatarURL.'"> ';
						$html.='</a>';
						$html.='</div>';						
						$html.='</div>';
						$html.='<div style="padding-left:10px">';
						$html.= '<a href="?username='.$tuteeUsername.'">';
						$html.=$tuteeInfo['first_name'].' '.$tuteeInfo['last_name'];
						$html.='</a>';						
						$html.'</div>';
						$html.'</div>';

					}
					else
					{
						$html.= '<span class="greyText">-</span>';
					}
					
					$html.= '</td>';
					
					if($pastOrFuture=="past")
					{	
						$html.= '<td valign="top">';
						$html.='<div id="slotStatus_'.$slotID.'">';
						$html.= imperialTutorsDraw::drawSlotStatus($slotID, $tookPlace);
						$html.='</div>';
						$html.='</td>';				
					}
					
					if($pastOrFuture=="future" || $pastOrFuture=="all")
					{	
						$formAction = "?view=tutee-timeslots&username=".$username.'&tab=upcoming';
						$html.= '<td width="100px" valign="top"><a href="javascript:confirmSlotCancelCheck('.$slotID.', \''.$formAction.'\');" class="imperial-button"><i class="far fa-trash-alt"></i> Delete </a></td>';
			
					}
					
					
					
					$html.= '</tr>';
				}
				
				$lastMonth = $thisMonth;
				
				$html.= '</table>';
				
				
				if($pastOrFuture=="future" || $pastOrFuture=="all")
				{	
					$html.= '<input type="submit" value="Delete Selected Slots" class="imperial-button">';
				}
				
			}
			
			$html.= '</form>';
		}
		
		if($rowCount==0)
		{
			$html = 'No meetings found';
		}
		
		return $html;

		
	}
	

	
	public static function drawSlotStatus($slotID, $status)
	{
		
		$html='';
		switch ($status)
		{
			
			
			case "1":
			
				$html.='<div class="successText">This meeting took place</div>';
				$html.='<span class="smallText"><a href="javascript:confirmSlotHappenned('.$slotID.', 2);">Swap to "Did not take place"</a></span>';
				
				
				
			break;
			
			
			case "2":
			
				$html.='<div class="failText"><i class="fas fa-exclamation-triangle"></i> Did not take place</div>';
				$html.='<span class="smallText"><a href="javascript:confirmSlotHappenned('.$slotID.', 1);">Swap to "Did take place"</a></span>';
			break;			
			
			default:
			
				$html.='<a class="imperial-button" href="javascript:confirmSlotHappenned('.$slotID.', 1);">';
				$html.='<i class="far fa-calendar-check"></i> Took Place</a><br/>';
				$html.='<a class="imperial-button" href="javascript:confirmSlotHappenned('.$slotID.', 2);">';
				$html.='<i class="far fa-calendar-times"></i> Did not take place</a>';

			break;		

			
			
			
			
			
			
		}
		
		return $html;			

		
	}
	
	
}

	
	?>