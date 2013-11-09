<?php

$DATE_FORMAT = "d/m/Y";
$TIME_FORMAT = "H:i:s";

function has_errors($errors) {
    if ($errors != '') {
        return 'wrong';
    }
    return '';
}

function no_slash($var) {
	return str_replace("/","",$var);
}

/*
 * ---------------------------------------------------------------------------
 * 
 * 									UTILS
 * 
 * ----------------------------------------------------------------------------
 */

function preferences_to_duration($preferences) {
    $hours = $preferences['hours'];
    $minutes = $preferences['minutes'];
    return $hours * 60 + $minutes;
}

function duration_to_preferences($duration) {
    if (isset($duration)) {
        $hours = (int) ($duration / 60);
        $minutes = $duration % 60;
        return array(
            'hours' => $hours,
            'minutes' => $minutes
        );
    }
}

function duration_to_string($timestamp) {
    $seconds = $timestamp;
    $minutes = (int) ($seconds / 60);
    $hours = (int) ($minutes / 60);
    $seconds = $seconds - $minutes * 60 ;
    $minutes = $minutes - $hours * 60;
    return $hours.':'.$minutes.':'.$seconds;
}

function mysql_to_php_date($date) {
	$new = strtotime($date);
	return date("d/m/Y",$new);
}

function mysql_date_to_time_array($date) {
	$new = strtotime($date);
	return array(
		'hour' => date("H",$new),
		'minute' => date("i",$new)
	);
}

/*
 * ---------------------------------------------------------------------------
*
* 							PUNCHES SCREEN
*
* ----------------------------------------------------------------------------
*/

/**
 * Reorders the checks from the db to an array like so : 
 * {
 * 		'07/11/13' : [db_check_array, db_check_array, db_check_array, db_check_array],  
 * 		'06/11/13' : [db_check_array, db_check_array, db_check_array, db_check_array],  
 * 		'05/11/13' : [db_check_array, db_check_array, db_check_array, db_check_array],  
 * }
 * @param unknown $checks
 */
function db_to_form_checks($checks) {
	$rearranged = array();

	foreach ($checks as $check) {
		$check = array_merge($check, mysql_date_to_time_array($check['date']));
		$rearranged[mysql_to_php_date($check['date'])][] = $check;
	}
	
	return $rearranged;
}

/*
 * ---------------------------------------------------------------------------
 * 
 * 								STATS
 * 
 * ----------------------------------------------------------------------------
 */

/**
 * Calculates the time on work this day
 * @param array $checks today's checks for the user
 * @return number the time spent in seconds
 */
function calculate_time_spent_today($checks) {
    $total_time = 0;
    $last_check_in_time = NULL;
    
    foreach ($checks as $check) {
        $time = strtotime($check['date']);
        // If the check is a check in, save the time
        if ($check['check_in']) {
            $last_check_in_time = $time;
        }
        else if ($last_check_in_time != NULL) {
            // The total time is increased with the time difference between check in and check out
            $total_time += $time - $last_check_in_time;
        }
    }

    // If the last check is a check in : calculate the current time
    $number_of_checks = count($checks);
    if ($number_of_checks > 0 && $checks[count($checks) - 1]['check_in']) {
        $total_time += time() - $last_check_in_time;
    }

    return $total_time;
}

/**
 * Calculates the user's overtime
 * @param array $checks all the user's checks
 * @return number the overtime in seconds
 */
function calculate_overtime($checks, $working_time) {
	$total_time = 0;
	$last_check_in_time = NULL;

	foreach ($checks as $check) {
		$time = strtotime($check['date']);
		// If the check is a check in, save the time
		if ($check['check_in']) {
			$last_check_in_time = $time;
		}
		else if ($last_check_in_time != NULL) {
			// The total time is increased with the time difference between check in and check out
			$total_time += $time - $last_check_in_time;
		}
	}

	// If the last check is a check in : calculate the current time
	$number_of_checks = count($checks);
	if ($number_of_checks > 0 && $checks[count($checks) - 1]['check_in']) {
		$total_time += time() - $last_check_in_time;
	}

	return $total_time;
}

function calculate_time_spent() {
	
}

/**
 * Calcultates the time left to work this day
 * @param number $time_spent the time already spent in seconds
 * @param number $working_time total time to spend at work for a day
 * @return number the time left to spend at work
 */
function calculate_time_left($time_spent, $working_time) {
    return $working_time - $time_spent;
}

/**
 * Calculates the time to leave work
 * @param number $time_left the time left to send at work today
 */
function calculate_end_time($time_left) {
    return date("H:i:s", time() + $time_left);
}