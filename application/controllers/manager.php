<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manager extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->spark('Twiggy/0.8.5');
        $this->load->helper('url');
        $this->load->helper('time_manager_helper');
        $this->load->library('tank_auth');
        $this->load->library('time_manager');
        $this->load->library('session');
        $this->load->library('form_validation');

        // Register available functions
        $this->twiggy->register_function('form_error');
        $this->twiggy->register_function('validation_errors');
        $this->twiggy->register_function('set_value');
        $this->twiggy->register_function('has_errors');
        $this->twiggy->register_function('no_slash');
    }

    /**
     * Pre controller hook that checks if the user is logged in
     */
    private function _pre_action($page = null) {
        if (!$this->tank_auth->is_logged_in()) {
            redirect('/auth/login/');
        } 
        else {
            // Determine whether the user checked in yet			
            $checked_in = $this->time_manager->is_user_checked_in($this->tank_auth->get_user_id());
            $checked_in = $checked_in ? "checked_in" : "";
            $this->twiggy->set("checked_in", $checked_in, $global = FALSE);
            $this->twiggy->set("active", $page);
        }
    }

    /**
     * Stats screen
     */
    public function stats()
    {
        $this->_pre_action(__FUNCTION__);

        $stats = $this->time_manager->calculate_stats($this->tank_auth->get_user_id());
        $this->twiggy->set('stats', $stats);
        $this->twiggy->template('stats')->display();
    }

    /**
     * Preferences screen
     */
    public function preferences()
    {
        $this->_pre_action(__FUNCTION__);

        if ($this->input->post()) {
            $this->load->helper(array('form', 'url'));

            if ($this->form_validation->run('preferences') == TRUE)
            {
                $preferences = $this->fill_preferences_array();
                $this->time_manager->save_preferences($preferences, $this->tank_auth->get_user_id());
                $preferences['success']=TRUE;
            }
            else {
                $preferences = $this->fill_preferences_array();
            }
        } else {
            $preferences = $this->time_manager->get_preferences($this->tank_auth->get_user_id());
        }

        $this->twiggy->set($preferences, NULL);
        $this->twiggy->template('preferences')->display();

    }
    
    /**
     * Punches screen
     */
    public function punches() {
        $this->_pre_action(__FUNCTION__);
        
        $checks = $this->time_manager->get_all_checks($this->tank_auth->get_user_id());
        
        if ($this->input->post()) {
        	
        	log_message('debug', print_r($this->input->post(), TRUE));
        	
        	$prevalidation = TRUE;
        	$ids_to_delete = array();
            
        	/*
        	 * Foreach field, set code igniter validation rules and update the checks array
        	 */
        	foreach($this->input->post() as $field_name => $field_value) {
            
                /*
                 * Prevalidation and preformatting of the array in case of added punches
                 */
                // The field name should look like this : 25102013_minute_25 (mmddyyyy)_hour_key
                $parts = explode("_", $field_name);
                if (!$this->is_check_field_name_ok($parts)) 
                {
                    $prevalidation = FALSE;
                }
                else if ($this->is_new_check($parts, $checks)
                    && isset($checks[to_slash($parts[0])][$parts[2] - 1]))
                {
                    $checks[to_slash($parts[0])][$parts[2]]= $checks[to_slash($parts[0])][$parts[2] - 1];
                }
                
                /*
                 * Validation rules and checks' array modifs
                 */
        		if (preg_match("/minute/", $field_name)) 
                {
        			// Form Validation
        			$this->form_validation->set_rules($field_name, "Minutes", "less_than[60]|greater_than[-1]");
        			
					if ($prevalidation) {
						$checks[to_slash($parts[0])][$parts[2]]['minute'] = $field_value;
					}
        		} 
                else if (preg_match("/hour/", $field_name)) 
                {	
        			$this->form_validation->set_rules($field_name, "Heures", "less_than[24]|greater_than[0]");
        			
					if ($prevalidation) {
						$checks[to_slash($parts[0])][$parts[2]]['hour'] = $field_value; 
        			}
        		}
                else if (preg_match("/delete/", $field_name)) 
                {	                    
					if ($prevalidation) 
                    {
                        // If the field is new, then the deletion has no effect on the checks' array
                        // and the prevalidation passes
                        if (!$this->is_new_check($parts, $checks)) 
                        {
                            log_message('debug', print_r($ids_to_delete, TRUE));
                            $ids_to_delete[] = $checks[to_slash($parts[0])][$parts[2]]['id'];
                        }
                        unset($checks[to_slash($parts[0])][$parts[2]]);
        			}
        		}
        	}
        	
            /*
             * Validate and save the punches
             */
        	if ($this->form_validation->run() == TRUE && $prevalidation == TRUE) {
                $this->time_manager->update_checks($checks, $ids_to_delete, $this->tank_auth->get_user_id());
                $this->twiggy->set('success', TRUE);
        		
        	}
        }

        $this->twiggy->set('checks', $checks, NULL);
        $this->twiggy->template('punches')->display();
    }
    
    /**
     * Checks if the result of an explode on check's field name is well formed
     * @param unknown $parts the result of an explode on check's field name
     * @return boolean true is parts are something like 24102013, anything, 33
     */
    private function is_check_field_name_ok($parts) {
    	return count($parts) == 3 
    		&& strlen($parts[0]) == 8 && is_numeric($parts[0])
    		&& is_numeric($parts[2]) && $parts[2] >= 0;
    }
    
    /**
     * Checks if the check which field name is given exists in the checks' array
     * or if it was added through the UI
     * @param array $parts the result of an explode on check's field name
     * @param array $checks the checks known
     * @return boolean true or false
     */
    private function is_new_check($parts, $checks) {
        return isset($checks[to_slash($parts[0])]) && isset($checks[to_slash($parts[0])][$parts[2]]);
    }
	
    /**
     * Utility function to fill the preferences array based on the data of the form
     * @return array the preferences array to be stored in the db
     */
    private function fill_preferences_array() {
        return array(
                'hours' => set_value('hours'),
                'minutes' => set_value('minutes'),
                );
    }

    /**
     * The punch function, supposed to redirect to the last screen
     */
    public function punch()
    {
        $this->_pre_action();
        $this->time_manager->check($this->tank_auth->get_user_id());
        log_message('debug', print_r($_SERVER, true));
        redirect($_SERVER['HTTP_REFERER']);
    }
}
