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
        
        $this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');

		if ($this->form_validation->run() == FALSE)
		{
            $this->twiggy->template('preferences')->display();
        }
	}
    
    public function punch()
    {
        $this->_pre_action();
        $this->time_manager->check($this->tank_auth->get_user_id());
        redirect('');
    }
}