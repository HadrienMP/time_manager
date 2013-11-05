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
     */
    public function calculate_stats($user_id) {
        $checks = $this->ci->checks->get_checks($user_id);
        $working_time = $this->ci->parameters->get_working_time($user_id);
        log_message('debug', print_r($checks,true));
        
        $stats = array();
        $stats['total_time_t'] = calculate_total_time($checks);
        $stats['total_time'] = duration_to_string($stats['total_time_t']);
        $stats['time_left_t'] = calculate_time_left($stats['total_time_t'], $working_time);
        $stats['time_left'] = duration_to_string($stats['time_left_t']);
        $stats['end_time'] = calculate_end_time($stats['time_left_t']);
        return $stats;
    }

    public function get_preferences($user_id) {
        $parameters = $this->ci->parameters->get_parameters($user_id);
        $preferences =  duration_to_preferences(@$parameters['working_time']);
        $preferences['stats_period'] = @$parameters['stats_period'];
        return $preferences;
    }

    public function save_preferences($preferences, $user_id) {
        // Convert the 'working time' array from preferences to a duration (in minutes)
        $duration = preferences_to_duration($preferences);
        $this->ci->parameters->set_parameters($duration, $user_id);
    }
}

/* End of file Time_manager.php */
/* Location: ./application/libraries/Time_manager.php */
