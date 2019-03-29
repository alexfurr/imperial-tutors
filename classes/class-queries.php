<?php

class imperialTutorQueries
{
	

	public static function getAllSlots($tutorUsername)
	{
		
		global $wpdb;
		global $dbTable_tutorBookings;
		
			
		$sql = "SELECT * FROM $dbTable_tutorBookings WHERE tutorUsername='" . $tutorUsername."' ORDER by slotDate ASC";
		$tutorSlots =  $wpdb->get_results( $sql );

		return $tutorSlots;				
	}		
	
	
	
	
	public static function getAllFutureSlots($tutorUsername)
	{
		
		global $wpdb;
		global $dbTable_tutorBookings;
		
		$today = date('Y-m-d');
			
		$sql = "SELECT * FROM $dbTable_tutorBookings WHERE tutorUsername='" . $tutorUsername."' AND date(slotDate) >='".$today."' ORDER by slotDate ASC";
		
		$tutorSlots =  $wpdb->get_results( $sql );

		return $tutorSlots;				
	}	
	
	public static function getAllPastSlots($tutorUsername)
	{
		
		global $wpdb;
		global $dbTable_tutorBookings;
		
		$today = date('Y-m-d H:i:s');
			
		$sql = "SELECT * FROM $dbTable_tutorBookings WHERE tutorUsername='" . $tutorUsername."' AND date(slotDate) <'".$today."' ORDER by slotDate ASC";
		
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
	public static function getMyUpcomingSlots($tuteeUsername, $tutorUsername)
	{
		
		global $wpdb;
		global $dbTable_tutorBookings;
		
		$today = date('Y-m-d');
			
		$sql = "SELECT * FROM $dbTable_tutorBookings WHERE tuteeUsername='" . $tuteeUsername."' AND tutorUsername = '".$tutorUsername."' AND date(slotDate) >='".$today."' ORDER by slotDate ASC";
		
		$myTutorSlots =  $wpdb->get_results( $sql, ARRAY_A );

		return $myTutorSlots;				
	}	
	
	// Have both usernames in case of tutor switch and don't want to confuse tutors
	public static function getMyBookedSlots($username)
	{
		
		global $wpdb;
		global $dbTable_tutorBookings;
		
		$today = date('Y-m-d');
			
		$sql = "SELECT * FROM $dbTable_tutorBookings WHERE tuteeUsername IS NOT NULL AND tutorUsername = '".$username."' ORDER by slotDate ASC";
		$mySlots =  $wpdb->get_results( $sql );

		return $mySlots;				
	}		
	
	
	static function getAllTutors($academicYear)
	{
		global $wpdb;
		global $imperialNetworkDB;		
		
		$tutorAllocationsTable = $imperialNetworkDB::imperialTableNames()['dbTable_tutorAllocations'];		
		$usersTable = $imperialNetworkDB::imperialTableNames()['dbTable_users'];		
			
		$sql = "SELECT $tutorAllocationsTable.tutorUsername, $usersTable.title, 
		$usersTable.first_name, $usersTable.last_name, $usersTable.email FROM $tutorAllocationsTable
		INNER JOIN $usersTable ON $tutorAllocationsTable.tutorUsername = $usersTable.username 
		WHERE academicYear='$academicYear' GROUP by tutorUsername";
		
		$allTutors =  $wpdb->get_results( $sql, ARRAY_A );

		return $allTutors;			
	}
	
	
	// gets a massive list of ALL tutor tutee allocations
	static function getAllTutorAllocations($academicYear)
	{
		global $wpdb;
		global $imperialNetworkDB;		
		
		$tutorAllocationsTable = $imperialNetworkDB::imperialTableNames()['dbTable_tutorAllocations'];		
			
		$sql = "SELECT * FROM $tutorAllocationsTable WHERE academicYear='$academicYear'";
		
		$allocations =  $wpdb->get_results( $sql, ARRAY_A );

		return $allocations;			
	}	
	
	static function getUnsignedOffSlots($username)
	{
		global $wpdb;
		global $dbTable_tutorBookings;
		
		$today = date('Y-m-d H:i:s');
			
		$sql = "SELECT * FROM $dbTable_tutorBookings WHERE tutorUsername='" . $username."' 
		AND (tuteeUsername IS NOT NULL OR tuteeUsername <>'')		
		AND (tookPlace IS NULL OR tookPlace ='')		
		AND date(slotDate) <'".$today."' ORDER by slotDate ASC";

		$slots =  $wpdb->get_results( $sql );

		
		return $slots;


	}
	


	
	
}

	
	?>