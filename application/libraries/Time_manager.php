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
		log_message('debug', "\tArrivée dans la librairie");
		return $this->ci->checks->get_last_check($user_id);
	}
	
}

/* End of file Time_manager.php */
/* Location: ./application/libraries/Time_manager.php */