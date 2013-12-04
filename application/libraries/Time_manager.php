<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Time_manager
 *
 * Authentication library for Code Igniter.
 *
 * @package		Time_manager
 * @author		HadrienMP
 */
class Time_manager
{

    function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->database();
        $this->ci->load->model('time_manager/checks');
        $this->ci->load->model('time_manager/parameters');
        $this->ci->load->helper('time_manager_helper');
    }
    
    /**
     * Determines if user checked in
     * @param $user_id
     * @return boolean true if last check is a check in false otherwise
     */
    public function is_user_checked_in($user_id) {
        return $this->ci->checks->get_last_check($user_id);
    }

    /**
     * Checks the user in
     * @param $user_id
     */
    public function check($user_id) {
        // The check type is out if the last check is in and vice versa
        $is_check_in = !$this->is_user_checked_in($user_id);
        $this->ci->checks->create($is_check_in, $user_id);
    }

    /**
     * Calculates the statistics of the user based on his punches
     * @param integer user_id the user's id
     */
    public function calculate_stats($user_id) {
        $checks = $this->ci->checks->get_checks($user_id);
        $working_time = $this->ci->parameters->get_working_time($user_id);
        
        $time_spent = calculate_time_spent($checks);
        $days = count_days($checks);
        
        $stats = array();
        // Today's stats
        $stats['time_spent_t'] = $time_spent['day'];
        $stats['time_spent'] = duration_to_string($stats['time_spent_t']);
        $stats['time_left_t'] = calculate_time_left($stats['time_spent_t'], $working_time);
        $stats['time_left'] = duration_to_string($stats['time_left_t']);
        $stats['end_time'] = calculate_end_time($stats['time_left_t']);
        
        // Overtime stats
        foreach (array_keys($time_spent) as $period) {
	        $stats['periods'][$period]['total_time_spent_t'] = $time_spent[$period];
	        $stats['periods'][$period]['total_time_spent'] = duration_to_string($stats['periods'][$period]['total_time_spent_t']);
	        $stats['periods'][$period]['days_worked'] = $days[$period];
	        $stats['periods'][$period]['overtime_t'] = calculate_overtime($stats['periods'][$period]['total_time_spent_t'], $working_time, $stats['periods'][$period]['days_worked']);
	        $stats['periods'][$period]['overtime'] = duration_to_string($stats['periods'][$period]['overtime_t'], $working_time);
        }
        
        return $stats;
    }
    
    /**
     * Gets all the checks from the db and rearranges them in a manageable way
     * @param number $user_id the user's id
     */
    public function get_all_checks($user_id) {
    	$checks = $this->ci->checks->get_checks($user_id);
    	return db_to_form_checks($checks);
    }
    
    public function update_checks($checks, $checks_to_add, $ids_to_delete, $user_id) {
        log_message('debug', 'Library : update_checks, checks : '.print_r($checks, TRUE));
        log_message('debug', 'Library : update_checks, ids to delete : '.print_r($ids_to_delete, TRUE));
    	$this->ci->checks->update_checks(form_to_db_checks($checks), 
            prepare_checks_to_add_for_db($checks_to_add), $ids_to_delete, $user_id);
    }

    /**
     * Gets the preferences of the user
     * It converts the preferences in the db to a readable format for the app
     * @param integer $user_id the user's id
     * @return array the preferences array of the user
     */
    public function get_preferences($user_id) {
        $parameters = $this->ci->parameters->get_parameters($user_id);
        $preferences =  duration_to_preferences(@$parameters['working_time']);
        $preferences['stats_period'] = @$parameters['stats_period'];
        return $preferences;
    }

    /**
     * Save the preferences of the user
     * Transforms the preferences to a storable format in the db
     * @param array $preferences the preferences array
     * @param integer $user_id the user's id
     */
    public function save_preferences($preferences, $user_id) {
        // Convert the 'working time' array from preferences to a duration (in minutes)
        $duration = preferences_to_duration($preferences);
        $this->ci->parameters->set_parameters($duration, $user_id);
    }
    
    public function get_data_info($user_id) {
    	$today = time("today 00:00:00");
    	$beginning_month = strtotime(date('01-m-Y'));
    	$end_of_month = strtotime(date('01-m-Y', strtotime("+1 month 00:00:00")));
    	$diff = (int) (($today - $beginning_month) / (24*3600)) + 1;
    	$total = (int) (($end_of_month - $beginning_month) / (24*3600));
    	
    	
    	return array(
    			'number_of_checks' => $this->ci->checks->count_checks($user_id),
    			'percent_month' => (int) ( $diff *100 / $total),
    	);
    }
}

/* End of file Time_manager.php */
/* Location: ./application/libraries/Time_manager.php */
