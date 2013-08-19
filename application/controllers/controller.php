<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->spark('Twiggy/0.8.5');
		$this->load->helper('url');
		$this->load->library('tank_auth');
		$this->load->library('time_manager');
    }
	
	private function _pre_action() {
		if (!$this->tank_auth->is_logged_in()) {
			redirect('/auth/login/');
		} 
		else {
			log_message('debug', 'DEBUT de la récupération de l\'état de check in de l\'utilisateur');
			$checked_in = $this->time_manager->is_user_checked_in($this->tank_auth->get_user_id());
			log_message('debug', 'FIN de la récupération de l\'état de check in de l\'utilisateur : ' . $checked_in);
			$checked_in = $checked_in ? "checked_in" : "";
			$this->twiggy->set("checked_in", $checked_in, $global = FALSE);
			log_message('debug', 'Valeur de la classe : ' . $checked_in);
		}
	}
	
	public function stats()
	{
		$this->_pre_action();
		log_message('debug', 'Affichage du template');
    	$this->twiggy->template('stats')->display();
	}
}