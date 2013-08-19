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
	function get_checks($user_id, $period = Periods::ALL_TIME) {}
	
	/**
	 * Returns the last check value
	 * @param $user_id
	 * @return boolean last check_in value (FALSE if none found)
	 */
	function get_last_check($user_id) {
		log_message('debug', "\t\tArrivée dans le modèle");
		$result = FALSE;
		if (!empty($user_id)) {
			$this->db->select('check_in');
			$this->db->order_by("date", "desc");
			$query = $this->db->get(Checks::TABLE_NAME,1,0);
			if ($query->num_rows() == 1) $result = $query->row()->check_in;
			log_message('debug', "Requête utilisée : " . print_r($this->db->last_query(), TRUE));
			log_message('debug', "Résultat : " . print_r($query->row(), TRUE));
		}
		log_message('debug', "\t\tRécypération avec succès : " . $result);
		return $result;
	}
	function update_checks($ids) {}
	function delete_checks($ids) {}
	function create($is_check_in, $user_id) {}
}
	