<?php

class imperialTutorQueries
{
	
	
	public static function getAllFutureSlots($tutorUsername)
	{
		
		global $wpdb;
		global $dbTable_tutorBookings;
		
		$today = date('Y-m-d');
			
		$sql = "SELECT * FROM $dbTable_tutorBookings WHERE tutorUsername='" . $tutorUsername."' AND date(slotDate) >='".$today."' ORDER by slotDate ASC";

		echo $sql;
		
		$tutorSlots =  $wpdb->get_results( $sql );

		return $tutorSlots;				
	}	
	
	
	
	public static function getActiveSlots($tutorUsername, $slotDate="")
	{
		
		global $wpdb;
		global $dbTable_tutorBookings;
			
		$slotDateQuery = '';
		if($slotDate<>"")
		{
			$slotDateQuery = "AND date(slotDate) = '".$slotDate."'";
		}
		else
		{
			$slotDate = date('Y-m-d');
			$slotDateQuery = "AND date(slotDate) > '".$slotDate."'";
		}

		$sql = "SELECT * FROM $dbTable_tutorBookings WHERE tutorUsername='" . $tutorUsername."' and (tuteeUsername IS NULL OR tuteeUsername='') ".$slotDateQuery."  ORDER by slotDate ASC";
		
		$tutorSlots =  $wpdb->get_results( $sql );

		return $tutorSlots;
				
	}
	
	public static function getSlotInfo($slotID)
	{
		
		global $wpdb;
		global $dbTable_tutorBookings;
		
		

		$sql = "SELECT * FROM $dbTable_tutorBookings WHERE slotID=" . $slotID;
		$rs = $wpdb->get_row( $sql, ARRAY_A );

		return $rs;
				
	}
	
	public static function getMyTutees($tutorUsername)
	{
		
		global $wpdb;
		
		global $imperialNetworkDB;		
		$tutorAllocationsTable = $imperialNetworkDB::imperialTableNames()['dbTable_tutorAllocations'];		
		$usersTable = $imperialNetworkDB::imperialTableNames()['dbTable_users'];		
		
		

		$sql = "SELECT DISTINCT $tutorAllocationsTable.username, $usersTable.first_name, $usersTable.last_name,
		$usersTable.yos, $usersTable.userID, $usersTable.email FROM $tutorAllocationsTable
		INNER JOIN $usersTable ON $tutorAllocationsTable.username = $usersTable.username 
		WHERE $tutorAllocationsTable.tutorUsername='" . $tutorUsername."' ORDER by $usersTable.yos ASC, $usersTable.last_name ASC";
		
		$tuteeArray = $wpdb->get_results( $sql, ARRAY_A );
		
		$yearTuteeArray = array();
		
		
		
		foreach ($tuteeArray as $tuteeInfo)
		{
			$yos = $tuteeInfo['yos'];			
			$yearTuteeArray[$yos][] = $tuteeInfo;
		}
			

		return $yearTuteeArray;
				
	}		
	
	public static function getMyTutor($username, $academcYear="")
	{
		
		global $wpdb;
		
		global $imperialNetworkDB;		
		$tutorAllocationsTable = $imperialNetworkDB::imperialTableNames()['dbTable_tutorAllocations'];		
		$usersTable = $imperialNetworkDB::imperialTableNames()['dbTable_users'];		
		
		if($academcYear=="")
		{
			$academcYear = get_site_option("current_academic_year");
		}

		$sql = "SELECT $tutorAllocationsTable.tutorUsername, $usersTable.first_name, $usersTable.last_name,
		$usersTable.username, $usersTable.email FROM $tutorAllocationsTable
		INNER JOIN $usersTable ON $tutorAllocationsTable.tutorUsername = $usersTable.username 
		WHERE $tutorAllocationsTable.username='" . $username."' AND $tutorAllocationsTable.academicYear='" . $academcYear."'";
		
		
		$tutorInfo = $wpdb->get_row( $sql, ARRAY_A );
		
		return $tutorInfo;
				
	}	


	
	// Have both usernames in case of tutor switch and don't want to confuse tutors
	public static function getMyUpcomingSlots($username, $tutorUsername)
	{
		
		global $wpdb;
		global $dbTable_tutorBookings;
		
		$today = date('Y-m-d');
			
		$sql = "SELECT * FROM $dbTable_tutorBookings WHERE tuteeUsername='" . $username."' AND tutorUsername = '".$tutorUsername."' AND date(slotDate) >='".$today."' ORDER by slotDate ASC";
		
		$myTutorSlots =  $wpdb->get_results( $sql, ARRAY_A );

		return $myTutorSlots;				
	}	
	

	
	
}

	
	?>