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
     * @param $time_period
     * @return an array of checks
     */
    public function get_checks($user_id, $time_period = Periods::ALL_TIME) {
        $checks = NULL;
        if (!empty($user_id)) {
            $this->db->order_by("date", "asc");
            // TODO: Handle time period
//             $this->db->where("date >=", $this->today());
            $this->db->where("user_id", $user_id);
            $query = $this->db->get(Checks::TABLE_NAME);
            $checks = $query->result_array();
        }
        else {
            log_message('error', "User id vide");
        }
        return $checks;
    }

    /**
     * Gets all the checks of a user for the present day (including today if last punch was a check in) 
     * @param $user_id
     * @return an array of checks
     */
    public function get_todays_checks($user_id) {
        $checks = NULL;
        if (!empty($user_id)) {
            $this->db->order_by("date", "asc");
            $this->db->where("date >=", $this->today());
            $this->db->where("user_id", $user_id);
            $query = $this->db->get(Checks::TABLE_NAME);
            $checks = $query->result_array();
            
            // Handle the cas where the first check of the day is a check out
            $where_date = $this->today();
            if (count($checks) != 0) {
            	$where_date = $checks[0]['check_in'];
            }
            
            if (count($checks) == 0 || $checks[0]['check_in'] == 0) {            	
           		$this->db->order_by("date", "desc");
            	$this->db->where("date <", $where_date);
            	$query = $this->db->get(Checks::TABLE_NAME,1,0);
            	array_unshift($checks,  $query->row_array());
            
            	if ($checks[0]['check_in'] == 0) {
            		log_message('error', "Les checks de l'utilisateur $user_id sont erronés");
            		// TODO: Handle the null return upper in the chain
            		return NULL;
            	}
            }
        }
        else {
            log_message('error', "User id vide");
        }
        log_message('debug',"Checks d'aujourd'hui : ".print_r($checks, TRUE));
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
            $this->db->where("user_id", $user_id);
            $query = $this->db->get(Checks::TABLE_NAME,1,0);
            if ($query->num_rows() == 1) $result = $query->row()->check_in;
        }
        else {
            log_message('error', "User id vide");
        }
        return $result;
    }
    
    function update_checks($checks_to_update, $checks_to_add, $ids_to_delete, $user_id) {
        log_message('debug', 'update_checks, checks_to_update : '.print_r($checks_to_update, TRUE));
        log_message('debug', 'update_checks, checks_to_add : '.print_r($checks_to_add, TRUE));
        log_message('debug', 'update_checks, ids to delete : '.print_r($ids_to_delete, TRUE));
        
        // Delete
        if (isset($ids_to_delete) and count($ids_to_delete) > 0) {
            $this->db->where_in('id', $ids_to_delete);
            $this->db->delete(Checks::TABLE_NAME);
        }
        
        // Insert
        if (isset($checks_to_add) and count($checks_to_add) > 0) {
            $this->db->insert_batch(Checks::TABLE_NAME, $checks_to_add);
        }
        
        // Update
    	$this->db->update_batch(Checks::TABLE_NAME, $checks_to_update, 'id');
    }
    function delete_checks($ids) {}

    /**
     * Creates a check in for the user specified
     * @param boolean $is_check_in true for in false for out
     * @param unknown $user_id the user's id
     */
    function create($is_check_in, $user_id) {
    
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
    
    /** 
     * @return a mysql date for today at midnight
     */
    private function today() {
    	return date("Y-m-d H:i:s" ,strtotime('today midnight'));
    }
}

