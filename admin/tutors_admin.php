<h1>Imperial Tutors</h1>


<div class="admin-settings-group">
<?php
// Get array of current academic year tutors

// Temp array for now
$tutorArray = array("alexfurr", "mameen");



echo '<table class="imperial-table">';
foreach ($tutorArray as $tutorUsername)
{

	// Get the Tutor User Meta
	
	$tutorMeta = imperialQueries::getUserInfo($tutorUsername);
	$name = $tutorMeta['last_name'].', '.$tutorMeta['first_name'];
	$email = $tutorMeta['email'];
	$preferredEmail = $tutorMeta['preferred_email'];
	
	echo '<tr>';
	echo '<td>'.$name.'</td>';
	echo '<td>'.$tutorUsername.'</td>';
	echo '<td>'.$email.'</td>';
	echo '<td>'.$preferredEmail.'</td>';
	echo '<td><a href="#">Manage Timeslots</a></td>';
	echo '</tr>';
	
}
echo '</table>';


?>
</div>