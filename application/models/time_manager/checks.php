<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once("periods.php");

/**
 * Checks
 *
 *
 * @package	Time_manager
 * @author	HadrienMP
 */
class Checks extends CI_Model
{
	const TABLE_NAME = "checks";	
    
    /**
     * Gets all the checks of a user for a certain period of time.
     * @param $user_id
     * @param $period
     * @return an array of checks
     */
	function get_checks($user_id, $period = Periods::ALL_TIME) {
        $checks = NULL;
        if (!empty($user_id)) {
			$this->db->order_by("date", "desc");
			$query = $this->db->get(Checks::TABLE_NAME,1,0);
            $checks = $query->result_array();
		}
        else {
            log_message('error', "User id vide");
        }
        return $checks;
    }
	
	/**
	 * Returns the last check value
	 * @param $user_id
	 * @return boolean last check_in value (FALSE if none found)
	 */
	function get_last_check($user_id) {
		$result = FALSE;
		if (!empty($user_id)) {
			$this->db->select('check_in');
			$this->db->order_by("date", "desc");
			$query = $this->db->get(Checks::TABLE_NAME,1,0);
			if ($query->num_rows() == 1) $result = $query->row()->check_in;
		}
        else {
            log_message('error', "User id vide");
        }
		return $result;
	}
	function update_checks($ids) {}
	function delete_checks($ids) {}
    
	function create($is_check_in, $user_id) {
        log_message('debug', $user_id);
        log_message('debug', 'Is check in : '.$is_check_in);
        if (!empty($user_id)) {
            $data = array(
               'check_in' => $is_check_in,
               'date' => date("Y-m-d H:i:s"),
               'user_id' => $user_id
            );
            
            $this->db->insert(Checks::TABLE_NAME, $data); 
        }
        else {
            log_message('error', "User id vide");
        }
	}
}
	