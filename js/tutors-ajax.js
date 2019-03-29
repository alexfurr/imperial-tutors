// Start listening on page load 
jQuery(document).ready(function() {
	start_cal_listen();
});
// And also listen after ajax calls	
jQuery( document ).ajaxStop(function() {
	start_cal_listen();
});


// Start listening for click events in the calendar
function start_cal_listen()
{
	

	//Listener for nexy / prev button
	jQuery('.loadCalMonth').on( 'click', function (  ) {
		// Get the ID of the button, turn to array with _ as split and second element is the quiz IDa
		var month = this.id.split("_")[1];
		var year = this.id.split("_")[2];	
		var tutorUsername = this.id.split("_")[3];		
		
		//console.log("Clicked month"+month+" and year "+year);
		drawCalendar(month, year, tutorUsername);
	});	
	
//	document.getElementById('availableSlotsDiv').innerHTML = "Loading...";		

	//Listener for nexy / prev button
	jQuery('.hasSlots').on( 'click', function (  ) {
		// Get the ID of the button, turn to array with _ as split and second element is the quiz IDa
		var thisDate = this.id.split("_")[1];
		var tutorUsername = this.id.split("_")[2];		
		
		//console.log("Clicked thisDate = "+thisDate+" and tutorUsername "+tutorUsername);	
		drawDaySlots(thisDate, tutorUsername);
		
	});		
	
	
}

function drawCalendar(month, year, tutorUsername)
{
	
	jQuery.ajax({
		type: 'POST',
		url: tutorBookingParams.ajaxurl,
		data: {			
			"action"		: "drawCalendar",
			"month"			: month,
			"year"			: year,
			"tutorUsername"	: tutorUsername,			
			"security"		: tutorBookingParams.ajax_nonce
		},
		success: function(data)
		{
			
			document.getElementById('calendarWrap').innerHTML = data;

		}
			
	});
	
}


//open Modal and display timeslots for this date
function drawDaySlots(thisDate, tutorUsername)
{
	

	// Show the popup
	document.getElementById('imperial-modal-content').innerHTML = "Loading, please wait...";	
	document.getElementById('imperial-modal').style.display = "block";	
	
	
	jQuery.ajax({
		type: 'POST',
		url: tutorBookingParams.ajaxurl,
		data: {			
			"action"		: "drawDaySlots",
			"thisDate"		: thisDate,
			"tutorUsername"	: tutorUsername,			
			"security"		: tutorBookingParams.ajax_nonce
		},
		success: function(data)
		{
			
			document.getElementById('imperial-modal-content').innerHTML = data;

		}
			
	});
	
}

function confirmSlotBookingCheck(slotID)
{
	

	jQuery.ajax({
		type: 'POST',
		url: tutorBookingParams.ajaxurl,
		data: {			
			"action"		: "confirmSlotBookingCheck",
			"slotID"		: slotID,
			"security"		: tutorBookingParams.ajax_nonce
		},
		success: function(data)
		{			
			document.getElementById('imperial-modal-content').innerHTML = data;
		}
			
	});
	
}


function confirmSlotBooking(slotID)
{
	

	jQuery.ajax({
		type: 'POST',
		url: tutorBookingParams.ajaxurl,
		data: {			
			"action"		: "confirmSlotBooking",
			"slotID"		: slotID,
			"security"		: tutorBookingParams.ajax_nonce
		},
		success: function(data)
		{
			
			document.getElementById('imperial-modal-content').innerHTML = data;

		}
			
	});
	
}




function confirmSlotHappenned(slotID, status)
{
			document.getElementById('slotStatus_'+slotID).innerHTML = "Saving...";
	
	
		jQuery.ajax({
		type: 'POST',
		url: tutorBookingParams.ajaxurl,
		data: {			
			"action"		: "confirmSlotHappenned",
			"slotID"		: slotID,
			"status"		: status,
			"security"		: tutorBookingParams.ajax_nonce
		},
		success: function(data)
		{
			
			document.getElementById('slotStatus_'+slotID).innerHTML = data;

		}
			
	});
}





function confirmSlotCancelCheck(slotID, formAction)
{
	
		
	// Get the ID of the click
	var html = "<h2>Are you sure you want to cancel this booking?</h2>";
	html+="An email will be send to both tutor and tutee with the cancellation information.";
	html+="<form method='post' class='imperial-form' action='"+formAction+"&action=cancelSlot'>";
	html+="<br/><br/><input type='submit' class='imperial-button' value='Yes, Cancel the Booking'>";
	html+="<input type='hidden' name='slotID' value='"+slotID+"'>";

	html+="</form>";
	// Show the popup
	document.getElementById('imperial-modal-content').innerHTML = html;	
	document.getElementById('imperial-modal').style.display = "block";
}


		
