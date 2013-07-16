<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->spark('Twiggy/0.8.5');
		$this->load->helper('url');
		$this->load->library('tank_auth');
    }
	
	public function stats()
	{
		if (!$this->tank_auth->is_logged_in()) {
			redirect('/auth/login/');
		} else {
            $this->twiggy->template('stats')->display();
        }
	} 
}