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
        log_message('debug', print_r($checks,true));
        $stats = array();
        $stats['total_time_t'] = $this->calculate_total_time($checks);
        $stats['total_time'] = $this->duration_to_string($stats['total_time_t']) ;
        return $stats;
    }
    
    public function duration_to_string($timestamp) {
        $seconds = $timestamp;
        $minutes = (int) ($seconds / 60);
        $hours = (int) ($minutes / 60);
        $seconds = $seconds - $minutes * 60 ;
        $minutes = $minutes - $hours * 60;
        return $hours.'h '.$minutes.'m '.$seconds.'s';
    }
    
    private function calculate_total_time($checks) {
        log_message('debug', "Début de calcul de total time");
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
        
        log_message('debug', "Fin de calcul de total time");
        
        return $total_time;
    }
}

/* End of file Time_manager.php */
/* Location: ./application/libraries/Time_manager.php */