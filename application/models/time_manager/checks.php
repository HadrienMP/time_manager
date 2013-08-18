<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Checks
 *
 *
 * @package	Time_Manager
 * @author	HadrienMP
 */
class Checks extends CI_Model
{	
	function get_checks($user_id, $period = Periods::ALL_TIME) {}
	function update_checks($ids) {}
	function delete_checks($ids) {}
	function create($is_check_in, $user_id) {}
}
	