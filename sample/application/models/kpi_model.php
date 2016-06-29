<?php

class Kpi_model extends CI_Model {

    function __construct() {
        parent::__construct();

        $this->load->model('employees_model', 'employees');
    }

    public function getBaseScore($type = 0) {
        $this->db->select('*')
                ->from('hr_kpi_base_scores');

        if($type !== 0){
            $this->db->where(array('type' => $type));
        }

        return $this->db->get()->result();
    }

    public function getScoresByEmployee($id) {
        $this->db->select('*')
                ->from('hrmis_kpi_scores')
                ->where(array('employee_id' => $id));

        return $this->db->get()->result();
    }

    public function getScoresByEmployee2($id, $fromDate, $toDate) {
    	
    	$this->db->select('*')
    	->from('hrmis_kpi_scores')
    	->where(array(
    			'employee_id' => $id,
    			'extract(YEAR_MONTH from create_ymd) <=' => $toDate,
    			'extract(YEAR_MONTH from create_ymd) >=' => $fromDate
    	))
    	->order_by('create_ymd', 'desc');
    
    	return $this->db->get()->result();
    }

    /**
     * Get score by id and month score
     * @param type $id
     * @param type $date_ym
     * @return type
     */
    public function getScore($id, $date_ym) {

        $this->db->select('score, month_point')
                ->from('hrmis_kpi_scores')
                ->where(array(
                    "employee_id" => $id,
                    "extract(YEAR_MONTH from create_ymd) = " => $date_ym
        ));

        return $this->db->get()->result();
    }

    public function getScore2($id, $date_ym) {
    
    	$this->db->select('*')
    	->from('hrmis_kpi_scores')
    	->where(array(
    			"employee_id" => $id,
    			"extract(YEAR_MONTH from create_ymd) = " => $date_ym
    	));
    
    	return $this->db->get()->result();
    }
    
    public function getHrmisScoreAvgOnCutOff($id, $base, $fromDate, $toDate){
    	$this->db->select('avg((score * ' . intval($base, 10) . ') / base_score) as avgScore')
    	->from('hrmis_kpi_scores')
    	->where(array(
    			"employee_id" => $id,
    			"extract(YEAR_MONTH from create_ymd) >= " => intval($fromDate, 10),
    			"extract(YEAR_MONTH from create_ymd) <= " => intval($toDate, 10)
    	));
    	 
    	return $this->db->get()->result();
    }

    public function getDeptScore($id, $month, $year) {

        $this->db->select('score, added_date')
                ->from('hr_dept_kpi')
                ->where(array(
                    "employee_id" => $id,
                	"kpi_month" => $month,
                	"kpi_year" => $year
        ));

        return $this->db->get()->result();
    }
    
    public function getDeptScoreAvgOnCutOff($id, $fromDate, $toDate){
    	$this->db->select('avg(score) as avgScore')
    	->from('hr_dept_kpi')
    	->where(array(
    			"employee_id" => $id,
    			"extract(YEAR_MONTH from added_date) >= " => intval($fromDate, 10),
    			"extract(YEAR_MONTH from added_date) <= " => intval($toDate, 10)
    	));
    	 
    	return $this->db->get()->result();
    }

    public function getHrScore($id, $date_ym) {

        $this->db->select('score, added_date')
                ->from('hr_records_kpi')
                ->where(array(
                    "employee_id" => $id,
                    "extract(YEAR_MONTH from added_date) = " => $date_ym
        ));

        return $this->db->get()->result();
    }
    
    public function getHrScoreAvgOnCutOff($id, $fromDate, $toDate){
    	
    	$emp = $this->employees->get($id);
    	$commenceDate = $emp['mb_commencement'];

    	$this->db->select('avg(score) as avgScore')
    			->from('hr_records_kpi')
    			->where(array(
    					"employee_id" => $id,
    					"extract(YEAR_MONTH from added_date) >= " => intval($fromDate, 10),
    					"extract(YEAR_MONTH from added_date) <= " => intval($toDate, 10),
    					// Starting the getting of average from commence date
    					"extract(YEAR_MONTH from added_date) >=" => intval(date("Y-m-1", strtotime($commenceDate)), 10)
    			));
    	
    	return $this->db->get()->result();
    }

    /**
     * Updating HRMIS Scores
     * @param type $data
     * @return type
     */
    public function updateScores($data) {

        // $data[0][1] Accessing received data from JavaScript
        // [0][1] = MB_NO

        foreach ($data as $d) {

            $postdate = $d[0];
            $arrdate = explode("-", $postdate); //YYYY-MM-DD

            $this->insertScores($d[1], $postdate . " 00:00:00", $d[6], '1', $arrdate[0], $arrdate[1]);

            $monthPoint = new DateTime($postdate . " 00:00:00");

            for ($i = $d[2]; $i > 0; $i--) {
                $monthPoint->modify("-1 month");
                $this->insertScores($d[1], $monthPoint->format('Y-m-d') . " 00:00:00", $d[6], '0', $monthPoint->format('Y'), $monthPoint->format('m'));
            }
        }

        return $d[1];
    }

    public function updateDeptKpiScore($data) {
        foreach ($data as $d) {
        	$date = explode("-", $d[4]);
            $this->insertKPIScore($d[0], $date[1] , $date[0] , $d[8], $d[1], $d[2]);
        }

        return $d[1];
    }

    private function checkPOintExistence($where_arr) {
        $this->db->select('*');
        $this->db->from('hrmis_kpi_scores');

        if (count($where_arr) > 0)
            $this->db->where($where_arr);

        $result = $this->db->get();
        return $result->result();
    }

    private function hasKPIMonthScore($where_arr) {
        $this->db->select('*');
        $this->db->from('hr_dept_kpi');

        if (count($where_arr) > 0)
            $this->db->where($where_arr);

        $result = $this->db->get();
        return $result->result();
    }

    private function hasHrKPIMonthScore($where_arr) {
        $this->db->select('*');
        $this->db->from('hr_records_kpi');

        if (count($where_arr) > 0)
            $this->db->where($where_arr);

        $result = $this->db->get();
        return $result->result();
    }

    private function insertScores($emp_id, $create_ymd, $score, $match_point, $year, $month, $base_score = 100) {

        $primary_data = array('employee_id' => $emp_id, 'create_ymd' => $create_ymd, 'year' => $year, 'month' => $month);

        if ($this->checkPOintExistence($primary_data)) {
            $this->db->where($primary_data)->update('hrmis_kpi_scores', array('score' => $score));
        } else {
            $this->db->insert('hrmis_kpi_scores', array(
                'employee_id' => $emp_id,
                'score' => $score,
                'base_score' => $base_score,
                'create_ymd' => $create_ymd,
                'month_point' => $match_point,
                'month' => $month,
                'year' => $year
            ));
        }
    }

    public function insertKPIScore($emp_id, $month, $year, $score, $dept_id, $head_id) {

        $base_score = 60;
        $primary_data = array('employee_id' => $emp_id, 'kpi_month' => $month, 'kpi_year' => $year);

        if ($this->hasKPIMonthScore($primary_data)) {
            $this->db->where($primary_data)->update('hr_dept_kpi', array('score' => $score, 'updated_date' => date("Y-m-d H:i:s")));
        } else {
            $this->db->insert('hr_dept_kpi', array(
                'employee_id' => $emp_id,
                'score' => $score,
                'base_score' => $base_score,
                'dept_id' => $dept_id,
                'head_id' => $head_id,
                'added_date' => date("Y-m-d H:i:s"),
            	'kpi_month' => $month,
            	'kpi_year' => $year
            ));
        }

    }

    /**
     * Getting score using
     * @param type $id
     * @return type
     */
    public function getDeptScoreByEmployee($id) {
        $this->db->select('*')
                ->from('hr_dept_kpi')
                ->where(array('employee_id' => $id));

        return $this->db->get()->result();
    }

    public function getDeptScoreByEmployee2($id, $fromDate, $toDate) {
    	$this->db->select('*')
    	->from('hr_dept_kpi')
    	->where(array(
    			"employee_id" => $id,
    			"concat(kpi_year, lpad(kpi_month, 2, '0')) >=" => substr($fromDate, 0, -2) . substr($fromDate, -2),
    			"concat(kpi_year, lpad(kpi_month, 2, '0')) <=" => substr($toDate, 0, -2) . substr($toDate, -2)
    	))
    	->order_by('added_date', 'desc');
    
    	return $this->db->get()->result();
    }

    public function insertDeptScoreByEmployee($emp_id, $dept_id, $head_id, $score, $base_score, $create_ymd) {
        $this->db->insert('hr_dept_kpi', array(
            'employee_id' => $emp_id,
            'dept_id' => $dept_id,
            'head_id' => $head_id,
            'score' => $score,
            'base_score' => $base_score,
            'added_date' => $create_ymd
        ));
    }

    public function updateHrKpiScore($data) {
        foreach ($data as $d) {
            $this->insertHrKPIScore($d[0], $d[4], $d[8], $d[1], $d[2]);
        }

        return $d[1];
    }

    public function insertHrKPIScore($emp_id, $month, $score, $dept_id, $head_id, $base_score = 20) {

        $primary_data = array('employee_id' => $emp_id, 'added_date' => $month);

        if ($this->hasHrKPIMonthScore($primary_data)) {
            $this->db->where($primary_data)->update('hr_records_kpi', array('score' => $score, 'updated_date' => date("Y-m-d H:i:s")));
        } else {
            $this->db->insert('hr_records_kpi', array(
                'employee_id' => $emp_id,
                'score' => $score,
                'base_score' => $base_score,
                'dept_id' => $dept_id,
                'head_id' => $head_id,
                'added_date' => $month
            ));
        }
    }

    public function getHrScoreByEmployee($id) {
        $this->db->select('*')
                ->from('hr_records_kpi')
                ->where(array('employee_id' => $id));

        return $this->db->get()->result();
    }
    
    public function getHrScoreByEmployee2($id, $fromDate, $toDate) {
    	$this->db->select('*')
    	->from('hr_records_kpi')
    	->where(array(
    			'employee_id' => $id,
    			'extract(YEAR_MONTH from added_date) <=' => $toDate,
    			'extract(YEAR_MONTH from added_date) >=' => $fromDate
    	))
    	->order_by('added_date', 'desc');
    
    	return $this->db->get()->result();
    }

    public function insertHrScoreByEmployee($emp_id, $dept_id, $head_id, $score, $base_score, $create_ymd) {
        $this->db->insert('hr_records_kpi', array(
            'employee_id' => $emp_id,
            'dept_id' => $dept_id,
            'head_id' => $head_id,
            'score' => $score,
            'base_score' => $base_score,
            'added_date' => $create_ymd
        ));
    }

    private function diffPoint($type, $score) {
        $this->db->select('*');
        $this->db->from('hr_kpi_base_scores');
        $this->db->where(array('type' => $type, 'score' => $score));

        $result = $this->db->get();
        return $result->result();
    }

    public function updatePercentage($hrmisKpi, $departmentKpi, $hrRecordKpi) {
        $kpiPoints = array($hrmisKpi, $departmentKpi, $hrRecordKpi);

        $i = 1;
        foreach ($kpiPoints as $point) {
            if (!$this->diffPoint($i, $point)) {
                $this->db->where(array('type' => $i))->update('hr_kpi_base_scores', array('score' => $point, 'updated_ymd' => date("Y-m-d H:i:s")));
            }
            $i++;
        }
    }

}
