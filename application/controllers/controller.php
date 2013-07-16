<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->spark('Twiggy/0.8.5');
    }
	
	public function stats()
	{
        $data = array(
                    'time_spent' => '0h 0m 0s',
                    'time_left' => '0h 0m 0s',
                    'over_time' => '0h 0m 0s',
                    'over_time_today' => '0h 0m 0s',
                    'end_time' => '0h 0m 0s',
                    'end_time_no_over_time' => '0h 0m 0s'
                );
        $this->twiggy->set($data, NULL)->template('stats')->display();
	} 
}