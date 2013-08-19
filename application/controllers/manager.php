<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manager extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->spark('Twiggy/0.8.5');
		$this->load->helper('url');
		$this->load->library('tank_auth');
		$this->load->library('time_manager');
        $this->load->library('session');
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
    
    private function _post_action() {
        // Store the current url to be able to go back when check-in occurs
        $this->session->set_userdata('previous_url');
    }
	
	public function stats()
	{
		$this->_pre_action();
		log_message('debug', 'Affichage du template');
    	$this->twiggy->template('stats')->display();
        // $this->_post_action();
	}
    
    public function punch()
    {
        $this->_pre_action();
        log_message('debug', $this->tank_auth->get_user_id());
        $this->time_manager->check($this->tank_auth->get_user_id());
        log_message('debug', 'checked');
        redirect('');
    }
}