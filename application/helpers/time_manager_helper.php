<?php

function has_errors($errors) {
    if ($errors != '') {
        return 'wrong';
    }
    return '';
}

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
    return $hours.'h '.$minutes.'min '.$seconds.'s';
}

function calculate_total_time($checks) {
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

function calculate_time_left($time_spent, $working_time) {
    return $working_time - $time_spent;
}

function calculate_end_time($time_left) {
    return date("H\h i", time() + $time_left);
}