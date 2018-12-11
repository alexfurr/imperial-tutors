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
	
}

	
	?>