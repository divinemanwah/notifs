<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MY_Controller {

	function __construct() {
	
		parent::__construct();
		
		$this->load->model('employees_model', 'employees');
		$this->load->model('violations_model', 'violations');
		
		$this->lang->load('main');
	}

	public function index()
	{
	
		$data = array();
		
		$offenses = array();
		
		$cites = $this->cite->getAll('cite', true, null, true);

		foreach($cites as $cite) {
			
			if($cite->status > 0) {
				if(array_key_exists($cite->offense, $offenses)) {
				
					$offenses[$cite->offense][0]++;
					$offenses[$cite->offense][1] = ($offenses[$cite->offense][0] / count($cites)) * 100;
				}
				else
					$offenses[$cite->offense] = array(
							1,
							(1 / count($cites)) * 100
						);
			}
		}
		
		foreach($offenses as $label => $offense)
			$data[] = array(
					'label' => $label,
					'data' => $offense[1]
				);

		$this->view_template('dashboard', lang('dashboard_title'), array(
			'breadcrumbs' => array(lang('dashboard_overview')),
			'js' => array(
					'jquery.easypiechart.min.js',
					'flot/jquery.flot.min.js',
					'flot/jquery.flot.pie.min.js',
					'flot/jquery.flot.resize.min.js',
					'dashboard.js'
				),
			'last_login' => $this->session->flashdata('last_login') ? $this->session->flashdata('last_login') : null,
			'cites_pie_data' => json_encode($data),
			'curr_prev_year' => $this->cite->getCurrentPreviousYearInfo(),
			'offense_ave' => $this->cite->getMonthlyAverage(),
			'violation_ave' => $this->violations->getMonthlyAverage(),
			'logged_in_count' => $this->employees->getLoggedInCount(),
			'active_employees' => $this->employees->getCount(),
			'local_employees' => $this->employees->getCount('Local'),
			'expat_employees' => $this->employees->getCount('Expat'),
			'new_hires' => $this->employees->getNewHires(),
			'inactive_count' => $this->employees->getInactiveCount(),
			'reg_rate' => round($this->employees->getRegularizationRate()),
			'korean_employees' => $this->employees->getCount('Outsource'),
			'new_resigned' => $this->employees->getinfo('*',Array('mb_resign_date <='=>date('Y-m-d')),Array('mb_resign_date'=>'DESC'),0,10),
                        'total_local' => $this->employees->getinfo('count(mb_3) total,mb_3',Array('mb_resign_date between \''.date('Y-01-01').'\' and \''.date('Y-m-d').'\''=>NULL,'day(mb_resign_date) >='=>1,'mb_3'=>'Local'),Array(),0,0,Array('mb_3')),
                        'total_expat' => $this->employees->getinfo('count(mb_3) total,mb_3',Array('mb_resign_date between \''.date('Y-01-01').'\' and \''.date('Y-m-d').'\''=>NULL,'day(mb_resign_date) >='=>1,'mb_3'=>'Expat'),Array(),0,0,Array('mb_3'))
		));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */