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

function to_slash($var) {
	return substr($var, 0, 2).'/'.substr($var, 2, 2).'/'.substr($var, 4, 4);
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

/**
 * Transforms a duration (number of seconds) into a well formated time string
 * @param unknown $timestamp the duration to convert
 * @param number $working_time optional the time to be worked for a day, 
 * used to calculate the time string in work days
 * @return string
 */
function duration_to_string($timestamp, $working_time = NULL) {
	
	$prefix = "";
	if ($timestamp < 0) {
		$timestamp *= -1;
		$prefix = "-";
	}

	$days = "";
	if (isset($working_time) && $working_time != 0) {
		$days = (int) ($timestamp / $working_time);
		$days .= ' jours ';
		$timestamp -= $days * $working_time;
	}
	
    $seconds = $timestamp;
    $minutes = (int) ($seconds / 60);
    $hours = str_pad((int) ($minutes / 60), 2, "0", STR_PAD_LEFT);
    $seconds = str_pad($seconds - $minutes * 60 , 2, "0", STR_PAD_LEFT);
    $minutes = str_pad($minutes - $hours * 60, 2, "0", STR_PAD_LEFT);
    return $prefix.$days.$hours.':'.$minutes.':'.$seconds;
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

function string_to_stripped_date($string) {
	return date("Y-m-d",strtotime($string));
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
 * (
 *    [22/10/2013] => Array
 *        (
 *          [0] => Array
 *                (
 *                    [id] => 1
 *                    [user_id] => 1
 *                    [check_in] => 1
 *                    [date] => 2013-10-22 20:03:22
 *                    [hour] => 20
 *                    [minute] => 03
 *                )
 *
 *            [1] => Array
 *                (
 *                    [id] => 2
 *                    [user_id] => 1
 *                    [check_in] => 0
 *                    [date] => 2013-10-22 20:03:42
 *                    [hour] => 20
 *                    [minute] => 03
 *                )
 *
 *        )
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

/**
 * The same as db_to_form_checks but in reverse
 * @param unknown $checks
 */
function form_to_db_checks($checks, $is_to_add=FALSE) {
	
	$rearranged = array();
	
	foreach ($checks as $day_checks) {
		foreach ($day_checks as $check) {
            // If we're preparing the checks to add array the id can't be present
            // (insert_batch won't work otherwise)
            if ($is_to_add) {
                unset($check['id']);
            }
			$rearranged[] = update_time($check);
		}
	}
	
	return $rearranged;
}

/**
 * Prepares the checks to add for the insert in db (removes id and updates time)
 * @param array the checks' array to update (db format)
 */
function prepare_checks_to_add_for_db($checks) {
    log_message('debug', 'prepare_checks_to_add_for_db, checks_to_add : '.print_r($checks, TRUE));
    $checks = form_to_db_checks($checks, TRUE);
    return $checks;
}

/**
 * Updates the time of the checks set in the form in case their hour / minute changed
 * @param $check the check to update
 */
function update_time($check) {
    $date = new DateTime($check['date']);
    $date->setTime($check['hour'], $check['minute']);
    $check['date'] = $date->format("Y-m-d H:i:s");
    unset($check['hour']);
    unset($check['minute']);
    return $check;
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
function calculate_time_spent($checks) {
	
	// Reference dates
	$today = string_to_stripped_date("today");
	$a_week_ago = string_to_stripped_date("-1 week");
	$a_month_ago = string_to_stripped_date("-1 month");
	
	// Times
	$time_today = 0;
	$time_week = 0;
	$time_month = 0;
	
	// Utils variables
	$last_check_out_time = strtotime('now');
	
	// The checks are run in reverse order, we calculate the time spent between a check out and a check in
	$checks = array_reverse($checks);
	foreach ($checks as $index => $check) {
		
		$time = strtotime($check['date']);
		
		if ($check['check_in'])
		{
			$diff = $last_check_out_time - $time;
			$date = string_to_stripped_date($check['date']);
			$last_date = $index > 0 ? string_to_stripped_date($checks[$index -1]['date']) : NULL;
			$last_check_in = $index > 0 ? $checks[$index - 1]['check_in'] : NULL;
			
			// Calculates time spent based on the period
			if ($date === $today 
				|| ($last_date === $today && $last_check_in == FALSE) ) {
				// Time for today or if the last check in was today and a check out (we calculate the time over night)
				$time_today += $diff;
			}
			else if (($date < $today && $date >= $a_week_ago) 
				|| ($last_date >= $a_week_ago && $last_check_in == FALSE)) {
				// Time for the week or if the last check in was this week and a check out (we calculate the time over night)
				$time_week += $diff;
			}
			else if (($date < $a_week_ago && $date >= $a_month_ago) 
				|| ($last_date >= $a_month_ago && $last_check_in == FALSE)) {
				$time_month += $diff;
			}
		} else {
			$last_check_out_time = $time;
		}
	}
	
	$time_week += $time_today;
	$time_month += $time_week;
	
	return array(
		'day' => $time_today,
		'week' => $time_week,
		'month' => $time_month
	);
}

/**
 * Calculates the user's overtime
 * @param number $time_spent the time already spent in seconds
 * @param number number $working_time total time to spend at work for a day
 * @return number the overtime in seconds
 */
function calculate_overtime($time_spent, $working_time, $days) {
	$regular_total_worktime = $working_time * $days;
	$overtime = $time_spent - $regular_total_worktime;
	
	return $overtime;
}

/**
 * Counts the number of days where at least one check occured
 * @param unknown $checks the checks array (db format) to parse
 */
function count_days($checks) {
	$days = 0;
	$last_date = NULL;
	foreach ($checks as $check) {
		$date = explode(" ", $check['date']);
		if (count($date) > 0 && $date[0] != $last_date) {
			$last_date = $date[0];
			$days++;
		}
	}
	
	if (count($checks) > 0) {
	    $last_date = $checks[count($checks) -1]['date'];
	    $last_date = explode(" ", $last_date);
	    if (count($last_date) == 2 && $last_date[0] != date("Y-m-d", strtotime("today"))) {
	        $diff = strtotime("today") - strtotime($last_date[0]);
	        $days += floor($diff/(60*60*24));
	    }
	}
	
	return $days;
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