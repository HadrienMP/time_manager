<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->spark('Twiggy/0.8.5');
    }
	
	public function index()
	{
		$this->twiggy->display();
	} 
}