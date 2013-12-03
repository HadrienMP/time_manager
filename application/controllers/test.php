<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Test class
 *
 * @author Istvan Pusztai
 * @version 1.0.2 $Id: test.php 7 2009-09-29 06:23:41Z Istvan $
 * @copyright Copyright (C) 2010 Istvan Pusztai (twitter.com/istvanp)
 **/
 
class Test extends CI_Controller {

	private $timings = array();
	private $tests = array();
	
	/**
	 * Constructor function
	 *
	 * @return void
	 **/
	public function __construct()
	{
		parent::__construct();
		
		// Set time marker for the start of the test suite
		$this->benchmark->mark('first');
		
		log_message('debug', 'Test Controller Initialized');
		
		// Load the unit test library
		$this->load->library('unit_test');
		
		// Load syntax highlighting helper
		$this->load->helper('text');
		
		// Set mode to strict
		$this->unit->use_strict(TRUE);
		
		// Disable database debugging so we can test all units without stopping
		// at the first SQL error
		// $this->db->db_debug = FALSE;
		
		// Create list of tests
		$this->_map_tests();
	}
	
	public function time_spent_helper() 
	{
		$this->load->helper('time_manager_helper');
		$this->load->helper('unit_datasource_helper');
		
		/*
		 * All time calculations
		 */
		
		// 2 minutes
		$this->benchmark->mark('start');
		$checks = get_checks_2();
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['day']), duration_to_string(0),'Time spent (2 minutes / day)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		$this->benchmark->mark('start');
		$checks = get_checks_2();
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['week']), duration_to_string(120),'Time spent (2 minutes / week)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		$this->benchmark->mark('start');
		$checks = get_checks_2();
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['month']), duration_to_string(120),'Time spent (2 minutes / month)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		
		// 7h22
		$this->benchmark->mark('start');
		$checks = get_checks_722();
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['month']), duration_to_string(7*3600 + 22*60),'Time spent (7h22)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		
		// Over night
		$this->benchmark->mark('start');
		$checks = get_checks_overnight();
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['month']), duration_to_string((7+24)*3600 + 22*60),'Time spent (overnight)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		$this->benchmark->mark('start');
		$checks = get_checks_overnight();
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['day']), duration_to_string((7+24)*3600 + 22*60 - 3*3600),'Time spent (overnight today)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		
		// 2 days
		$this->benchmark->mark('start');
		$checks = get_checks_2_days();
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['month']), duration_to_string(2*(7*3600 + 22*60)),'Time spent (2 days)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		$this->benchmark->mark('start');
		
		/*
		 * Today time calculations
		 */
		
		// Over nights
		$this->benchmark->mark('start');
		$checks = get_checks_2_days();
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['day']), duration_to_string(7*3600 + 22*60),'Time spent today (2 days)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		
		/*
		 * All periods
		 */
		$this->benchmark->mark('start');
		$checks = get_checks_month();
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['day']), duration_to_string(7*3600 + 22*60),'Time spent today (Month / day)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		$this->benchmark->mark('start');
		$checks = get_checks_month();
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['week']), duration_to_string(2*(7*3600 + 22*60)),'Time spent today (Month / week)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		$this->benchmark->mark('start');
		$checks = get_checks_month();
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['month']), duration_to_string(3*(7*3600 + 22*60)),'Time spent today (Month / month)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
        
		$checks = get_checks_month_no_checkout();
        $spent_today = strtotime('now') - strtotime($checks[count($checks) -1]['date']);
		$this->benchmark->mark('start');
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['day']), 
            duration_to_string($spent_today),
            'Time spent no checkout yesterday (day)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		$this->benchmark->mark('start');
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['week']), 
            duration_to_string(3*3600 + $spent_today),
            'Time spent no checkout yesterday (week)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		$this->benchmark->mark('start');
		$time_spent = calculate_time_spent($checks);
		$this->unit->run(duration_to_string($time_spent['month']), 
            duration_to_string(10*3600 + 22*60 + $spent_today),
            'Time spent no checkout yesterday (month)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
	}
	
	public function duration_to_string_helper() {
		$this->load->helper('time_manager_helper');
		
		// -5 jours 07:21:00
		$this->benchmark->mark('start');
		$this->unit->run(duration_to_string(-159060, 26520), '-5 jours 07:21:00','Duration to string (minus)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		
		// 00:00:00
		$this->benchmark->mark('start');
		$this->unit->run(duration_to_string(0, 26520), '00:00:00','Duration to string (minus)');
		$this->benchmark->mark('end');
		$this->timings[] = $this->benchmark->elapsed_time('start', 'end');
		
	}
	
	
	/**
	 * Remap function
	 * Maps the requested action to a method and performs the tests.
	 * Do not modify/delete this function.
	 *
	 * @author Istvan Pusztai
	 * @return void
	 **/
	public function _remap()
	{	
		$view_data = array();
		$action = $this->uri->rsegment(2);
		$view_data['headings'] = array("types" => array(), "tests" => array());
		
		switch (strtolower($action))
		{
			case 'index':
				$view_data['msg'] = "Please pick a test suite";
			break;
			case 'all':
				$i = 0;
				foreach ($this->tests as $key => $type)
				{
					$view_data['headings']['types'][count($this->timings)] = ucfirst($key);
					foreach($type as $key2 => $method)
					{
						$view_data['headings']['tests'][count($this->timings)] = $method;
						call_user_func(array($this, $method));
					}
				}
			break;
			case 'models':
			case 'views':
			case 'libraries':
			case 'helpers':
				if (array_key_exists($action, $this->tests) && count($this->tests[$action]) > 0)
				{
					foreach ($this->tests[$action] as $method)
					{
						$view_data['headings']['tests'][count($this->timings)] = $method;
						call_user_func(array($this, $method));
					}
				}
				else
				{
					$view_data['msg'] = "There are no test suites for $action";
				}
			break;
			default:			
				if (array_search_recursive($action, $this->tests))
				{
					call_user_func(array($this, $action));
				}
				else
				{
					$view_data['msg'] = "<em>$action</em> is an invalid test suite";
				}
		}
		
		// Prepare report
		$report = $this->unit->result();
		
		// Prepare totals
		$view_data['totals']['all'] = count($report);
		$view_data['totals']['failed'] = 0;
		
		// Count failures
		foreach($report as $key => $test)
		{
			if ($test['Result'] == 'Failed')
			{
				++$view_data['totals']['failed'];
			}
		}
		
		// Count passes
		$view_data['totals']['passed'] = $view_data['totals']['all'] - $view_data['totals']['failed'];
		
		// Calculate the total time taken for the test suite
		$view_data['total_time'] = $this->benchmark->elapsed_time('first', 'end');
		
		// Other useful data
		$view_data['tests']		= $this->tests;
		$view_data['type']		= $action;
		$view_data['report']	= $report;
		$view_data['timings']	= $this->timings;
		
		$this->load->view('unit_test', $view_data);
	}

	/**
	 * Map Tests
	 * Creates a list of tests from the functions defined in this class.
	 * Do not modify/delete this function.
	 *
	 * @author Istvan Pusztai
	 * @return void
	 **/
	public function _map_tests()
	{
		$methods = get_class_methods($this);
		natsort($methods);
		
		foreach ($methods as $method)
		{
			if (strpos($method, '_') !== 0
				AND $method != __CLASS__
				AND $method != "CI_Base"
				AND $method != "Controller"
				AND $method != "get_instance"
			)
			{
				$length = strlen($method);
				
				if (strripos($method, 'model') === $length - 5)
				{
					$this->tests['models'][] = $method;
				}
				else if (strripos($method, 'view')  === $length - 4)
				{
					$this->tests['views'][] = $method;
				}
				else if (strripos($method, 'library')  === $length - 7)
				{
					$this->tests['libraries'][] = $method;
				}
				else if (strripos($method, 'helper')  === $length - 6)
				{
					$this->tests['helpers'][] = $method;
				}
			}
		}
		
		return $this->tests;
	}
}

/**
 * Array Search (Rescursive)
 * Searches through an array for a value recursively
 * >>> Place this code in a helper if you use it elsewhere <<<
 *
 * @author Istvan Pusztai
 * @since 1.0.2
 * @param string $needle The value to look for
 * @param array $haystack The array to search
 * @param bool $strict Use strict comparison
 * @return bool
 **/
function array_search_recursive($needle, $haystack, $strict = FALSE, $path = array())
{
	if ( ! is_array($haystack))
	{
		return FALSE;
	}
 
	foreach ($haystack as $key => $val)
	{
		if (is_array($val) && $subPath = array_search_recursive($needle, $val, $strict, $path))
		{
			$path = array_merge($path, array($key), $subPath);
			return $path;
		}
		else if (( ! $strict && $val == $needle) || ($strict && $val === $needle))
		{
			$path[] = $key;
			return $path;
		}
	}
	
	return FALSE;
}
/* End of file test.php */