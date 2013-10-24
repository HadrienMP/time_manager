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
    }
	
	private function _pre_action() {
		if (!$this->tank_auth->is_logged_in()) {
			redirect('/auth/login/');
		} 
		else {
		    // Determine whether the user checked in yet			
			$checked_in = $this->time_manager->is_user_checked_in($this->tank_auth->get_user_id());
			$checked_in = $checked_in ? "checked_in" : "";
			$this->twiggy->set("checked_in", $checked_in, $global = FALSE);
		}
	}
	
	public function stats()
	{
		$this->_pre_action();
        
        $stats = $this->time_manager->calculate_stats($this->tank_auth->get_user_id());
        $this->twiggy->set('stats', $stats);
    	$this->twiggy->template('stats')->display();
	}
	
	public function preferences()
	{
		$this->_pre_action();
        
        $preferences = array();
        
        if ($this->input->post()) {
            $this->load->helper(array('form', 'url'));

            if ($this->form_validation->run('preferences') == TRUE)
            {
                // TODO: Save preferences in DB
                $preferences['success']=TRUE;
            } else {
                $preferences['hours']=set_value('hours');
                $preferences['minutes']=set_value('minutes');
                $preferences['seconds']=set_value('seconds');
            }
        } else {
            // TODO: Load preferences from DB
        }
        
        $this->twiggy->set($preferences, NULL);
        $this->twiggy->template('preferences')->display();
        
	}
    
    public function punch()
    {
        $this->_pre_action();
        $this->time_manager->check($this->tank_auth->get_user_id());
        redirect('');
    }
}