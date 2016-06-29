<?php

class Employees_model extends CI_Model {

    protected $base_hr_score;

    function __construct() {
        parent::__construct();

        //$this->config->load('kpi', true);
        //$this->base_hr_score = $this->config->item('base_hr_score', 'kpi');
    }

    public function employee_exists($id) {

        return $this->db->get_where('g4_member', array('mb_no' => $id))->num_rows() == 1;
    }

    public function get($id) {

        return $this->db
                        ->select("m.*, d.dept_name, i.nick_name as supervisor")
                        ->join('dept d', 'm.mb_deptno = d.dept_no')
                        ->join('hr_expat e', 'm.mb_no = e.employee_id', 'left')
                        ->join('hr_dept_heads h', 'h.dept_id = m.mb_deptno', 'left')
                        ->join('users_info i', 'i.hr_users_id = h.employee_id', 'left')
                        ->get_where('g4_member m', array('m.mb_no' => $id))
                        ->row_array();
    }

    public function get2($id) {

        return $this->db
                        ->join('dept d', 'm.mb_deptno = d.dept_no')
                        ->join('hr_expat e', 'm.mb_no = e.employee_id', 'left')
                        ->where_in('m.mb_no', $id)
                        ->get('g4_member m')
                        ->result_array();
    }

    public function get3($id) {

        return $this->db
                        ->select('m.mb_no, m.mb_nick, d.dept_name')
                        ->join('dept d', 'm.mb_deptno = d.dept_no')
                        ->get_where('g4_member m', array('m.mb_no' => $id))
                        ->row_array();
    }

    public function getDOC($id) {

        return $this->db
                        ->select('mb_commencement')
                        ->get_where('g4_member', array('mb_no' => $id))
                        ->row_array();
    }

    public function getmember($mb_no = "", $where = Array()) {
        return $this->db->get_where('g4_member', ($mb_no) ? array('mb_no' => $mb_no) : $where)->row();
    }
    
    public function getinfo($select = '*',$where = Array(),$order_arr = Array(),$start=0,$limit=0,$group = Array()){
        
        $this->db->select($select,false)
                 ->from('g4_member gm')
                 ->join('dept d', 'gm.mb_deptno = d.dept_no')
                ;
        
        if($where)
            $this->db->where($where);
        
        if ($limit > 0)
            $this->db->limit($limit, $start);
        
        if (!empty($groups)) 
            foreach ($groups as $group)
                $this->db->group_by($group);
                
        if (!empty($order_arr)) 
            foreach ($order_arr as $field => $dir)
                $this->db->order_by($field, $dir);
        
        return $this->db->get()->result();
    }

    public function getById($id) {

        return $this->db
                        ->join('dept d', 'm.mb_deptno = d.dept_no')
                        ->get_where('g4_member m', array('m.mb_id' => $id))
                        ->row();
    }

    public function _getById($id) {

        return $this->db
                        ->get_where('users_info', array('employee_id' => $id))
                        ->row();
    }

    public function _getById2($id) {

        return $this->db
                        ->where_in('employee_id', $id)
                        ->get('users_info')
                        ->result();
    }

    public function getByUsername($username) {

        return $this->db
                        ->join('dept d', 'm.mb_deptno = d.dept_no')
                        ->get_where('g4_member m', array('m.mb_username' => $username))
                        ->row();
    }

    public function holidaysettings() {
        return $this->db
                        ->select('COLUMN_COMMENT')
                        ->get_where('information_schema.columns', Array('table_name' => 'hr_holidays', 'COLUMN_NAME' => 'h_status'))
                        ->row();
    }

    public function insertholidays($data) {
        return $this->db->insert('hr_holidays', $data);
    }

    public function updateholidays($data, $param) {
        return $this->db->update("hr_holidays", $data, $param);
    }

    public function getholidays($select = '*') {
        return $this->db
                        ->select($select, false)
                        ->from('hr_holidays');
    }

    public function getholidayhistory($select = '*') {
        return $this->db
                        ->select($select, false)
                        ->from('hr_holiday_history');
    }

    public function holiday_validation($record, $mbno, $hstatus) {
        $hday = $this->db->from('hr_holiday_mem_res')->where(Array('mb_no' => $mbno))->get()->result();
        if ($record == 'insert') {
            if (count($hday) > 0) {
                $this->db->update('hr_holiday_mem_res', Array('h_status' => $hstatus), Array('mb_no' => $mbno));
            } else {
                $data = Array('h_status' => $hstatus, 'mb_no' => $mbno);
                $this->db->insert('hr_holiday_mem_res', $data);
            }
            return $this->db->affected_rows();
        } else {
            return $hday ? $hday[0] : Array("h_status" => 0);
        }
    }

    public function getMembers($id) {
        // return $this->db->get_where('g4_member', ($mb_no) ? array('mb_no' => $mb_no) : $where)->row();
        return $this->db
                        ->get_where('g4_member', array('mb_no' => $id))
                        ->result();
    }

    public function getAll($show_inactive = false, $select = '*', $dept = "", $start = 0, $limit = 0, $order_arr = array(), $where_arr = array(), $include_group = false, $return_obj = false) {

        $this->db->select($select, false)
                ->from('g4_member m')
                ->join('dept d', 'm.mb_deptno = d.dept_no', 'inner')
                ->where_not_in('m.mb_no', array(229, 382, 348));

        if ($include_group)
            $this->db->join('dept_group_mem_assign gma', 'gma.mb_no = m.mb_no', 'left')
                    ->join('dept_group dept', 'dept.id = gma.group_id and dept.dept_status = 1', 'left');
        /*
          $this->db->join('users_info hui', 'hui.employee_id = m.mb_id', 'inner')
          ->join('hr_users_groups hug', 'hui.hr_users_id = hug.user_id  AND `hug`.`group_id` > 2', 'left')
          ->join('hr_groups hg', 'hg.id = hug.group_id', 'left');
         * 
         */


        if (!$show_inactive)
            $this->db->where('m.mb_status', 1);

        if ($dept && is_numeric($dept))
            $this->db->where('m.mb_deptno', $dept);

        if (!empty($where_arr)) {
            foreach ($where_arr as $field => $val) {
                $this->db->where($field, $val);
            }
        }

        if ($limit > 0)
            $this->db->limit($limit, $start);

        if (!empty($order_arr)) {
            foreach ($order_arr as $field => $dir)
                $this->db->order_by($field, $dir);
        } else {
            $this->db->order_by('m.mb_id', 'desc');
        }

        if ($return_obj) {
            return $this->db;
        }
        return $this->db->get()->result();
    }

    public function getAll2($show_inactive = false, $select = '*', $dept = "", $start = 0, $limit = 0, $order_arr = array(), $where_arr = array()) {

        $this->db->select($select)
                ->from('g4_member m')
                ->join('dept d', 'm.mb_deptno = d.dept_no')
                ->join('users_info u', 'u.employee_id = m.mb_id')
                ->where_not_in('m.mb_no', array(229, 382, 348));

        if (!$show_inactive)
            $this->db->where('m.mb_status', 1);

        if ($dept && is_numeric($dept))
            $this->db->where('m.mb_deptno', $dept);

        if (!empty($where_arr)) {
            foreach ($where_arr as $field => $val) {
                $this->db->where($field, $val);
            }
        }

        if ($limit > 0)
            $this->db->limit($limit, $start);

        if (!empty($order_arr)) {
            foreach ($order_arr as $field => $dir)
                $this->db->order_by($field, $dir);
        } else {
            $this->db->order_by('m.mb_id', 'desc');
        }
        return $this->db->get()->result();
    }

    public function getAllLocals($show_inactive = false, $select = '*', $dept) {

        $this->db->select($select)
                ->from('g4_member m')
                ->join('dept d', 'm.mb_deptno = d.dept_no')
                ->where('mb_3', 'Local')
                ->where_not_in('mb_no', array(229, 382, 348))
                ->order_by('m.mb_id', 'desc');

        if (!$show_inactive)
            $this->db->where('m.mb_status', 1);

        if ($dept && is_numeric($dept))
            $this->db->where('m.mb_deptno', $dept);

        return $this->db->get()->result();
    }

    public function getAllExpats($show_inactive = false, $select = '*', $dept) {

        $this->db->select($select)
                ->from('g4_member m')
                ->join('dept d', 'm.mb_deptno = d.dept_no')
                ->where('mb_3', 'Expat')
                ->where_not_in('mb_no', array(229, 382, 348))
                ->order_by('m.mb_id', 'desc');

        if (!$show_inactive)
            $this->db->where('m.mb_status', 1);

        if ($dept && is_numeric($dept))
            $this->db->where('m.mb_deptno', $dept);

        return $this->db->get()->result();
    }

    public function getAllExpats_extended($show_inactive, $dept, $page, $per_page, $order_by, $expired = false) {
        if ($expired)
            $docs = $this->db->get('hr_documents_notification')->result_array();

        $this->db->start_cache();

        $this->db->select('
							m.mb_no,
							m.mb_id,
							m.mb_lname,
							m.mb_fname,
							m.mb_mname,
							m.mb_nick,
							m.mb_sex,
							m.mb_civil,
							e.nationality,
							e.passport_no,
                                                        e.passport_issued,
							e.passport_validity,
							m.mb_birth,
							m.mb_2,
							d.dept_name,
							e.lec_designation,
							m.mb_commencement,
							m.mb_resign_date,
							m.mb_employment_status,
							m.mb_email,
							e.personal_email,
							e.blood_type,
							e.height,
							e.weight,
							e.phil_add,
							e.hometown_add,
							e.hometown_no,
							e.tin_no,
							e.aep_no,
                                                        e.aep_issued,
							e.aep_validity,                                                       
                                                        e.i_card,
                                                        e.cwv_no,
                                                        e.cwv_issued,
                                                        e.cwv_validity,
                                                        e.total_cwv,
							e.remarks,
							m.mb_status
						')
                ->from('g4_member m')
                ->join('dept d', 'm.mb_deptno = d.dept_no')
                ->join('hr_expat e', 'm.mb_no = e.employee_id', 'left')
                ->where('m.mb_3', 'Expat')
                ->where_not_in('m.mb_no', array(229, 382, 348))
                ->order_by($order_by);

        if (!$show_inactive)
            $this->db->where('m.mb_status', 1);

        if ($dept && is_numeric($dept))
            $this->db->where('m.mb_deptno', $dept);

        if ($expired) {

            $docinfo = (array) $docs[0];

            $dateform = Array();
            foreach (json_decode('[' . $docinfo['passport'] . ']', true) as $val)
                $dateform[] = "DATE(e.passport_validity - INTERVAL " . $val . " DAY)";

            foreach (json_decode('[' . $docinfo['aep'] . ']', true) as $val)
                $dateform[] = "DATE(e.aep_validity - INTERVAL " . $val . " DAY)";

            foreach (json_decode('[' . $docinfo['cwv'] . ']', true) as $val)
                $dateform[] = "DATE(e.cwv_validity - INTERVAL " . $val . " DAY)";

            if (count($dateform) > 0)
                $this->db->where("DATE(NOW()) IN ( " . implode(", ", $dateform) . " )", NULL, FALSE);
        }

        $this->db->stop_cache();

        $count = $this->db->count_all_results();

        $this->db->limit($per_page, $page ? ($page - 1) * $per_page : 0);

        $filtered = $this->db->get()->result();
        $this->db->flush_cache();

        return array(
            'count' => $count,
            'data' => $filtered
        );
    }

    public function updateExpat($data) {

        $fields = array(
            'm.mb_id',
            'm.mb_lname',
            'm.mb_fname',
            'm.mb_mname',
            'm.mb_nick',
            'm.mb_sex',
            'm.mb_civil',
            'e.nationality',
            'e.passport_no',
            'e.passport_issued',
            'e.passport_validity',
            'm.mb_birth',
            'm.mb_2',
            'd.dept_name',
            'e.lec_designation',
            'm.mb_commencement',
            'm.mb_resign_date',
            'm.mb_employment_status',
            'm.mb_email',
            'e.personal_email',
            'e.blood_type',
            'e.height',
            'e.weight',
            'e.phil_add',
            'e.hometown_add',
            'e.hometown_no',
            'e.tin_no',
            'e.aep_no',
            'e.aep_issued',
            'e.aep_validity',
            'e.i_card',
            'e.cwv_no',
            'e.cwv_validity',
            'e.cwv_expired',
            'e.total_cwv',
            'e.remarks'
        );

        $curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));

        foreach ($data as $d) {

            if ($d[2] == "50") {
                $this->db->insert('hr_cws_info', Array('mb_no' => $d[0], 'cwv_no' => $d[4]));
                $cwscount = $this->db
                        ->from('hr_cws_info')
                        ->where(Array('mb_no' => $d[0]));
                $cwstotal = $cwscount->count_all_results();
                $this->db->where(Array('employee_id' => $d[0]))
                        ->update('hr_expat', array('cwv_no' => $d[4],
                            'cwv_validity' => null,
                            'cwv_expired' => null,
                            'total_cwv' => $cwstotal
                ));
            }

            if (!isset($fields[$d[2]]))
                continue;

            $f = explode('.', $fields[$d[2]]);

            if ($f[0] == 'm') {

                if ($f[1] == 'mb_civil')
                    switch ($d[4]) {
                        case 'Single':

                            $d[4] = 1;

                            break;
                        case 'Married':

                            $d[4] = 2;

                            break;
                        case 'Widowed':

                            $d[4] = 3;

                            break;
                        case 'Separated':

                            $d[4] = 4;

                            break;
                        case 'Divorced':

                            $d[4] = 5;

                            break;
                        default:

                            $d[4] = 1;
                    }

                if ($f[1] == 'mb_employment_status')
                    switch ($d[4]) {
                        case 'Probational':

                            $d[4] = 1;

                            break;
                        case 'Regular':

                            $d[4] = 2;

                            break;
                        default:

                            $d[4] = 1;
                    }

                $this->db->where('mb_no', $d[0])->update('g4_member', array($f[1] => $d[4]));
            } elseif ($f[0] == 'e') {

                $emp = $this->get($d[0]);

                if ($emp) {


                    $expathr = $this->db
                            ->from('hr_expat')
                            ->where('employee_id', $emp['mb_no']);
                    $expatdata = clone $expathr;
                    $expatdata = $expatdata->get()->result();


                    if ($expathr->count_all_results()) {
                        $cws = Array();
                        $expat_update = array(
                            $f[1] => $d[4],
                            'updated_by' => $this->session->userdata('mb_no'),
                            'updated_date' => $curr_date->format('Y-m-d H:i:s')
                        );

                        if ($d[2] == "31" and $d[4] !== "") {
                            if (count($expatdata[0]->cwv_no) > 0)
                                $this->db->where(Array('mb_no' => $emp['mb_no'], 'cwv_no' => $expatdata[0]->cwv_no))
                                        ->update('hr_cws_info', Array($f[1] => $d[4]));
                            else
                                $this->db->insert('hr_cws_info', Array($f[1] => $d[4], 'mb_no' => $emp['mb_no']));

                            $cws_info = $this->db
                                    ->from('hr_cws_info')
                                    ->where(Array('mb_no' => $emp['mb_no']))
                                    ->get()
                                    ->result();

                            if (count($cws_info))
                                $cws = Array_merge(Array("total_cwv" => count($cws_info)), $expat_update);
                        }elseif (in_array($d[2], Array("32", "33")) and $d[4] !== "") {

                            $this->db->where(Array('mb_no' => $emp['mb_no'], 'cwv_no' => $expatdata[0]->cwv_no))
                                    ->update('hr_cws_info', Array($f[1] => $d[4]));
                        }

                        $this->db
                                ->where('employee_id', $emp['mb_no'])
                                ->update('hr_expat', $expat_update);
                    } else {
                        $this->db->insert('hr_expat', array(
                            'employee_id' => $emp['mb_no'],
                            $f[1] => $d[4],
                            'total_cwv' => 1,
                            'created_by' => $this->session->userdata('mb_no'),
                            'created_date' => $curr_date->format('Y-m-d H:i:s')
                        ));
                        if ($d[2] == "31")
                            $this->db->insert('hr_cws_info', Array('mb_no' => $emp['mb_no'], $f[1] => $d[4]));
                    }
                }
            }
            elseif ($f[0] == 'd') {

                $depts = $this->getDepts();

                $match = false;

                foreach ($depts as $dept)
                    if ($dept->dept_name == $d[4]) {

                        $d[4] = $dept->dept_no;

                        $match = true;

                        break;
                    }

                if ($match)
                    $this->db->where('mb_no', $d[0])->update('g4_member', array('mb_deptno' => $d[4]));
            }
        }

        return time();
    }

    public function getCount($type = '', $show_inactive = false) {

        $this->db
                ->where_not_in('mb_no', array(229, 382, 348))
                ->from('g4_member');

        if (!$show_inactive)
            $this->db->where('mb_status', 1);

        if (in_array($type, array('Local', 'Expat')))
            $this->db->where(array(
                'mb_3' => $type,
                'mb_deptno !=' => 35
            ));

        if ($type == 'Outsource')
            $this->db->where('mb_deptno', 35);

        return $this->db->count_all_results();
    }

    public function getLastLogin($id) {

        return @$this->db->order_by('login_id', 'desc')->get_where('member_login', array('mb_no' => $id))->row(1)->date_login;
    }

    public function getNewHires() {

        $curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));

        return $this->db
                        ->select("m.mb_name, m.mb_nick, d.dept_name, date_format(m.mb_commencement, '%b %e') as hire_date", false)
                        ->join('dept d', 'm.mb_deptno = d.dept_no')
                        ->order_by('m.mb_commencement', 'desc')
                        ->get_where('g4_member m', array(
                            'm.mb_status' => 1,
                            'extract(YEAR_MONTH from m.mb_commencement) =' => $curr_date->format('Ym')
                        ))
                        ->result();
    }

    public function getInactiveCount() {

        return $this->db->where(array('mb_status !=' => 1))->from('g4_member')->count_all_results();
    }

    public function getLoggedInCount() {

        // $curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));
        // return $this->db->where(array('yearweek(date_login, 1) =' => $curr_date->format('YW'), 'date_logout' => null))->from('member_login')->count_all_results();

        $users = $this->db->select('user_data')->get('hr_sessions')->result();

        $_users = array();

        foreach ($users as $user)
            if ($data = @unserialize($user->user_data))
                if (isset($data['identity']))
                    $_users[] = $data['identity'];

        return count(array_unique($_users));
    }

    public function getRegularizationRate() {

        $curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));

        $count = $this->db->where_not_in('mb_no', array(229, 382, 348))->where(array('period_diff(extract(year_month from now()), extract(year_month from mb_commencement)) >=' => 6))->where("(year(mb_commencement) = '" . $curr_date->format('Y') . "' or year(mb_commencement) = '" . (intval($curr_date->format('Y')) - 1) . "')", null, false)->from('g4_member')->count_all_results();

        return $count ? (($this->db->where_not_in('mb_no', array(229, 382, 348))->where(array('period_diff(extract(year_month from now()), extract(year_month from mb_commencement)) >=' => 6, 'mb_employment_status' => 2))->where("(year(mb_commencement) = '" . $curr_date->format('Y') . "' or year(mb_commencement) = '" . (intval($curr_date->format('Y')) - 1) . "')", null, false)->from('g4_member')->count_all_results() / $count) * 100) : $count;
    }

    public function getKPI($type, $id = null) {

        switch ($type) {
            case 'employee':

                if ($id && is_numeric($id)) {

                    $q = $this->db
                            ->select('kpi_score')
                            ->get_where('hr_kpi_scores', array(
                        'employee_id' => $id
                    ));

                    $n = $q->num_rows();

                    if ($n) {

                        $score = intval($q->row()->kpi_score, 10);

                        if ($score < 0)
                            $score = 0;
                        elseif ($score > $this->base_hr_score)
                            $score = $this->base_hr_score;
                    }

                    return $n > 0 ? $score : false;
                } else
                    return false;

                break;
            case 'department':

                if ($id && is_numeric($id))
                    return '';
                else
                    return $this->db
                                    ->select('*')
                                    ->join('dept d', 'm.mb_deptno = d.dept_no', 'inner')
                                    ->get_where('g4_member m', array('mb_status' => 1))
                                    ->result();

                break;
            default:

                return false;
        }
    }

    public function setKPI($id, $score) {


        return $this->employee_exists($id) &&
                is_numeric($id) && ($score >= 0 || $score <= $this->base_hr_score) &&
                $this->db->insert('hr_kpi_scores', array(
                    'employee_id' => $id,
                    'kpi_score' => $score,
                    'last_updated' => time()
                )) &&
                $this->db->affected_rows() > 0;
    }

    public function resetKPI($id) {

        $this->db->delete('hr_kpi_scores', array('employee_id' => $id));
    }

    public function check_valid($id, $password) {

        $this->db->db_debug = false;

        return $this->db
                        ->where('mb_password', 'password(' . $this->db->escape($password) . ')', false)
                        ->where('mb_no', $id)
                        ->from('g4_member')
                        ->count_all_results() > 0;
    }

    public function save_session() {

        $this->db
                ->where('id', $this->session->userdata('user_id'))
                ->update('hr_users', array('last_session_id' => $this->session->userdata('session_id')));
    }

    public function create($id, $data) {

        if (getenv('HTTP_HOST') == '10.120.10.139')
            $intra_db = $this->load->database(array(
                'hostname' => '10.120.10.125',
                'username' => 'root',
                'password' => 'Rlqja1004',
                'database' => 'intra',
                'dbdriver' => 'mysqli',
                'dbprefix' => '',
                'pconnect' => false,
                'db_debug' => false,
                'cache_on' => false,
                'char_set' => 'utf8',
                'dbcollat' => 'utf8_general_ci'
                    ), true);

        if (isset($data['mb_password'])) {

            $this->db->set('mb_password', "password('{$data['mb_password']}')", false);

            if (getenv('HTTP_HOST') == '10.120.10.139')
                $intra_db->set('mb_password', "password('{$data['mb_password']}')", false);

            unset($data['mb_password']);
        }

        $curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));

        $date_time = $curr_date->format('Y-m-d H:i:s');

        $this->db->set('mb_id', strtoupper(substr($data['mb_id'], 0, 7)));
        $this->db->set('mb_name', "{$data['mb_fname']} {$data['mb_lname']}");
        $this->db->set('mb_sex', $data['mb_sex'] == '2' ? 'F' : 'M');
        $this->db->set('mb_3', $data['mb_3'] == '2' ? 'Expat' : 'Local');
        $this->db->set('mb_datetime', $date_time);
        $this->db->set('enroll_number', intval(str_ireplace('L', '1', str_ireplace('E', '2', str_ireplace('-', '0', $data['mb_id'])))));

        if (intval($data['mb_employment_status'], 10) == 2)
            $data['mb_confirmation'] = $curr_date->format('Y-m-d');

        if (getenv('HTTP_HOST') == '10.120.10.139') {

            $intra_db->set('mb_id', substr($data['mb_id'], 0, 7));
            $intra_db->set('mb_name', "{$data['mb_fname']} {$data['mb_lname']}");
            $intra_db->set('mb_level', 2);
            $intra_db->set('mb_sex', $data['mb_sex'] == '2' ? 'F' : 'M');
            $intra_db->set('mb_3', $data['mb_3'] == '2' ? 'Expat' : 'Local');
            $intra_db->set('mb_datetime', $date_time);
            $intra_db->set('enroll_number', intval(str_ireplace('L', '1', str_ireplace('E', '2', str_ireplace('-', '0', $data['mb_id'])))));
        }

        $_data = array(
            'hr_users_id' => $id,
            'employee_id' => strtoupper(substr($data['mb_id'], 0, 7)),
            'enroll_number' => intval(str_ireplace('L', '1', str_ireplace('E', '2', str_ireplace('-', '0', $data['mb_id'])))),
            'first_name' => $data['mb_fname'],
            'last_name' => $data['mb_lname'],
            'middle_name' => $data['mb_mname'],
            'nick_name' => $data['mb_nick'],
            'gender' => intval($data['mb_sex']),
            'dept_id' => intval($data['mb_deptno']),
            'job_title' => $data['mb_2'],
            'ethnicity' => intval($data['mb_3']),
            'created_by' => intval($this->session->userdata('user_id'))
        );

        unset($data['mb_id'], $data['mb_name'], $data['mb_sex'], $data['mb_3']);

        $ret1 = $this->db->insert('g4_member', $data) &&
                $this->db->affected_rows() > 0;

        $ret2 = $this->db->insert('users_info', $_data) &&
                $this->db->affected_rows() > 0;

        if (intval($data['mb_employment_status'], 10) == 2) {

            $CI = & get_instance();

            $CI->load->model('fix_model', 'fix', true);

            $CI->fix->setRegLeaveBalance($id, $data['mb_3'] == '2' ? 'Expat' : 'Local', intval($data['mb_deptno'], 10));
        }

        if (getenv('HTTP_HOST') == '10.120.10.139') {

            unset(
                    $data['mb_lv_app_grp_id'], $data['mb_ot_app_grp_id'], $data['mb_obt_app_grp_id'], $data['mb_cws_app_grp_id'], $data['mb_sched_grp_id'], $data['mb_commencement'], $data['mb_confirmation'], $data['mb_resign_date'], $data['mb_civil'], $data['mb_employment_status'], $data['condo_id']
            );

            $intra_db->insert('g4_member', $data);

            $intra_db->close();
        }


        return $ret1 && $ret2;
    }

    public function update($id, $data, $chage_pass_only = false) {

        if (!is_numeric($id) && !is_array($id))
            return false;

        // $this->db->db_debug = false;

        if (getenv('HTTP_HOST') == '10.120.10.139')
            $intra_db = $this->load->database(array(
                'hostname' => '10.120.10.125',
                'username' => 'root',
                'password' => 'Rlqja1004',
                'database' => 'intra',
                'dbdriver' => 'mysqli',
                'dbprefix' => '',
                'pconnect' => false,
                'db_debug' => false,
                'cache_on' => false,
                'char_set' => 'utf8',
                'dbcollat' => 'utf8_general_ci'
                    ), true);

        $_password = null;

        if (isset($data['mb_password'])) {

            $this->db->set('mb_password', "password('{$data['mb_password']}')", false);

            if (getenv('HTTP_HOST') == '10.120.10.139')
                $intra_db->set('mb_password', "password('{$data['mb_password']}')", false);

            $_password = $data['mb_password'];

            unset($data['mb_password']);
        }

        $_user = is_array($id) ? $this->get2($id) : $this->get($id);

        if (is_array($id))
            $multi_id = array_map(function ($a) {
                return $a['mb_id'];
            }, $_user);

        $_user2 = is_array($id) ? $this->_getById2($multi_id) : $this->_getById($_user['mb_id']);

        if (isset($data['mb_fname']) && isset($data['mb_lname']))
            $data['mb_name'] = "{$data['mb_fname']} {$data['mb_lname']}";

        if (isset($data['mb_id'])) {

            $data['mb_id'] = strtoupper(substr($data['mb_id'], 0, 7));
            $data['enroll_number'] = intval(str_ireplace('L', '1', str_ireplace('E', '2', str_ireplace('-', '0', $data['mb_id']))));
        }

        if (isset($data['mb_3']))
            $data['mb_3'] = intval($data['mb_3'], 10) == 2 ? 'Expat' : 'Local';
        
        if (isset($data['mb_resign_date']) and $data['mb_resign_date'] == '')
            $data['mb_resign_date'] = NULL;
        
        /**
          if (array_key_exists('mb_employment_status', $data) && intval($data['mb_employment_status'], 10) == 2) {

          $curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));

          $data['mb_confirmation'] = $curr_date->format('Y-m-d');
          }
         */
        $ret1 = (
                is_array($id) ?
                        $this->db
                                ->where_in('mb_no', $id)
                                ->update('g4_member', $data) :
                        $this->db
                                ->where('mb_no', $id)
                                ->update('g4_member', $data)
                ) &&
                // $expat_affected || $this->db->affected_rows() > 0;
                $this->db->affected_rows() > 0;

        if (array_key_exists('mb_employment_status', $data) && intval($data['mb_employment_status'], 10) == 2) {

            $CI = & get_instance();

            $CI->load->model('fix_model', 'fix', true);

            $CI->fix->setRegLeaveBalance($id, $data['mb_3'], intval($data['mb_deptno'], 10));

            $year = (new DateTime())->modify("+1 year")->format("Y");
            $CI->fix->setRegLeaveBalanceAnnual($id, $data['mb_3'], intval($data['mb_deptno'], 10), $year);
        }

        if (getenv('HTTP_HOST') == '10.120.10.139') {

            unset(
                    $data['mb_lv_app_grp_id'], $data['mb_ot_app_grp_id'], $data['mb_obt_app_grp_id'], $data['mb_cws_app_grp_id'], $data['mb_sched_grp_id'], $data['mb_commencement'], $data['mb_confirmation'], $data['mb_resign_date'], $data['mb_civil'], $data['mb_employment_status'], $data['condo_id']
            );

            if (is_array($id))
                $intra_db->where_in('mb_id', $multi_id);
            else
                $intra_db->where('mb_id', $_user['mb_id']);

            $intra_db->update('g4_member', $data);

            $intra_db->close();
        }

        $_data = array(
            'updated_by' => intval($this->session->userdata('user_id')),
            'updated_on' => time()
        );

        if (isset($data['mb_fname']))
            $_data['first_name'] = $data['mb_fname'];

        if (isset($data['mb_lname']))
            $_data['last_name'] = $data['mb_lname'];

        if (isset($data['mb_mname']))
            $_data['middle_name'] = $data['mb_mname'];

        if (isset($data['mb_nick']))
            $_data['nick_name'] = $data['mb_nick'];

        if (isset($data['mb_sex']))
            $_data['gender'] = intval($data['mb_sex']);

        if (isset($data['mb_deptno']))
            $_data['dept_id'] = intval($data['mb_deptno']);

        if (isset($data['mb_2']))
            $_data['job_title'] = $data['mb_2'];

        if (isset($data['mb_3']))
            $_data['ethnicity'] = intval($data['mb_3']);

        if (isset($data['mb_id'])) {

            $_data['employee_id'] = strtoupper(substr($data['mb_id'], 0, 7));
            $_data['enroll_number'] = intval(str_ireplace('L', '1', str_ireplace('E', '2', str_ireplace('-', '0', $data['mb_id']))));
        }

        $ret2 = isset($data['mb_status']) ?
                (
                (
                is_array($id) ?
                        $this->update_multi(array_map(function ($a) {
                                    return $a->hr_users_id;
                                }, $_user2), array('active' => intval($data['mb_status']))) :
                        $this->ion_auth->update($_user2->hr_users_id, array('active' => intval($data['mb_status'])))
                ) &&
                (
                is_array($id) ?
                        $this->db
                                ->where_in('employee_id', $multi_id)
                                ->update('users_info', array(
                                    'updated_by' => intval($this->session->userdata('user_id')),
                                    'updated_on' => time()
                                )) :
                        $this->db
                                ->where('employee_id', $_user['mb_id'])
                                ->update('users_info', array(
                                    'updated_by' => intval($this->session->userdata('user_id')),
                                    'updated_on' => time()
                                ))
                ) &&
                $this->db->affected_rows() > 0
                ) :
                (
                $chage_pass_only ?
                        $this->update_multi(array_map(function ($a) {
                                    return $a->hr_users_id;
                                }, $_user2), array('password' => $_password)) :
                        (
                        (
                        is_array($id) ?
                                $this->db
                                        ->where_in('employee_id', $multi_id)
                                        ->update('users_info', $_data) :
                                $this->db
                                        ->where('employee_id', $_user['mb_id'])
                                        ->update('users_info', $_data)
                        ) &&
                        $this->db->affected_rows() > 0 &&
                        (
                        isset($data['mb_username']) ?
                                (
                                is_array($id) ?
                                        $this->update_multi(array_map(function ($a) {
                                                    return $a->hr_users_id;
                                                }, $_user2), array('username' => $data['mb_username'])) :
                                        $this->ion_auth->update($_user2->hr_users_id, array('username' => $data['mb_username']))
                                ) :
                                true
                        )
                        )
                );

        $ret3 = true;

        if ($_password != null) {

            $_id = 0;

            if (is_array($id)) {

                $emp = $this->db->where_in('employee_id', $multi_id)->get('users_info')->result();

                if (count($emp))
                    $_id = array_map(function ($a) {
                        return $a->hr_users_id;
                    }, $emp);

                $ret3 = $this->update_multi($_id, array('password' => $_password));
            } else {

                $emp = $this->db->get_where('users_info', array('employee_id' => $_user['mb_id']))->row();

                if ($emp)
                    $_id = $emp->hr_users_id;

                $ret3 = $this->ion_auth->update($_id, array('password' => $_password));
            }
        }

        // return $ret1 && $ret2 && $ret3;
        return $ret2 && $ret3; // temporarily remove $ret1
    }

    private function update_multi($ids, $data) {

        foreach ($ids as $id)
            $this->ion_auth->update($id, $data);

        return true;
    }

    public function getGroups($show_all = false) {

        $groups = $this->ion_auth->groups()->result();

        if (!$show_all)
            return array_slice($groups, 1);

        return $groups;
    }

    public function getDepts($where = array()) {

        if (count($where) > 0)
            $this->db->where($where);
        return $this->db->order_by('dept_name')->get('dept')->result();
    }

    public function getDeptHeads($where = array()) {
        if (is_array($where) && count($where)) {
            foreach ($where as $k => $v) {
                $this->db->where($k, $v);
            }
        }

        return $this->db
                        ->select('h.id, d.dept_no, d.dept_name, i.hr_users_id, i.first_name, i.last_name, h.updated_by, h.updated_on')
                        ->join('hr_dept_heads h', 'h.dept_id = d.dept_no', 'left')
                        ->join('hr_users u', 'u.id = h.employee_id', 'left')
                        ->join('users_info i', 'i.hr_users_id = u.id', 'left')
                        ->order_by('d.dept_name')
                        ->get('dept d')
                        ->result();
    }

    public function getDeptHeadId($dept_id) {
        $this->db->select('employee_id')
                ->from('hr_dept_heads')
                ->where(array(
                    "dept_id" => $dept_id
        ));

        return $this->db->get()->result();
    }

    public function isDeptHead($id = 0) {

        $id = $id ? $id : $this->session->userdata('user_id');

        return $this->db->where('employee_id', $id)->get('hr_dept_heads')->num_rows() > 0;
    }

    public function getSubordinates($id = 0, $select = 'u2.*, k.score') {

        $id = intval($id ? $id : $this->session->userdata('user_id'), 10);

        $curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));

        $curr_month = intval($curr_date->format('n'), 10);

        $quadrant = floor(($curr_month - 1) / 3);

        $start = array(1, 4, 7, 10);

        $end = array(3, 6, 9, 12);

        return $this->db
                        ->select($select)
                        ->join('hr_dept_heads h', 'h.employee_id = u.hr_users_id', 'left')
                        ->join('users_info u2', 'u2.dept_id = h.dept_id', 'inner')
                        //->join('hr_dept_kpi k', "k.employee_id = u2.hr_users_id and k.head_id = $id and k.added_date between " . mktime(0, 0, 0, $start[$quadrant], 1, $curr_date->format('Y')) . ' and ' . mktime(23, 59, 59, $end[$quadrant] + 1, 0, $curr_date->format('Y')), 'left')
                        ->join('g4_member m', 'm.mb_id = u2.employee_id')
                        // ->join('hr_dept_kpi k', " k.employee_id = m.mb_no and k.added_date between " . mktime(0, 0, 0, $curr_month, 1, $curr_date->format('Y')) . ' and ' . mktime(23, 59, 59, $curr_month + 1, 0, $curr_date->format('Y')), 'left')
                        // ->join('hr_dept_kpi k', "k.employee_id = u2.hr_users_id and k.head_id = $id and k.added_date between FROM_UNIXTIME(" . mktime(0, 0, 0, $curr_month, 1, $curr_date->format('Y')) . ') and FROM_UNIXTIME(' . mktime(23, 59, 59, $curr_month + 1, 0, $curr_date->format('Y')) . ')', 'left')
                        ->join('hr_dept_kpi k', "k.employee_id = u2.hr_users_id and k.head_id = $id and k.added_date between FROM_UNIXTIME(" . mktime(0, 0, 0, $curr_month, 1, $curr_date->format('Y')) . ') and FROM_UNIXTIME(' . mktime(23, 59, 59, $curr_month + 1, 0, $curr_date->format('Y')) . ')', 'left')
                        ->where(array(
                            'h.employee_id' => $id,
                            'm.mb_status' => 1
                        ))
                        ->where_not_in('m.mb_no', array(229, 382, 348))
                        ->order_by('u2.last_name')
                        ->get('users_info u')
                        ->result();
    }

    public function getCites($id, $status = 0) {

        $this->db->from('hr_cites');

        if ($status && is_numeric($status))
            $this->db->where('status', $status);
        else
            $this->db->where(array(
                'status >' => 0,
                'status <' => 4
            ));

        return $this->db->get()->result();
    }

    public function getViolations($id, $this_year_only = false) {

        $this->db
                // ->select('mv.id, mv.violation_id, mv.commission_date, vr.repetition, vr.type, vr.minus, vr.subsequent_minus')
                ->select('mv.id, mv.commission_date, v.id as vio_id, c.title as cat_title, v.description as vio_desc, v.kpi_matrix, v.prescriptive_period, v.no_dismissal, p.description as pen_desc, mv.initial')
                ->from('hr_member_violations mv')
                ->join('hr_violations v', 'mv.violation_id = v.id', 'inner')
                ->join('hr_violation_categories c', 'v.cat_id = c.id', 'inner')
                ->join('hr_cites_assoc a', 'mv.id = a.member_violation_id', 'left')
                ->join('hr_cites c2', 'a.cite_id = c2.id', 'left')
                ->join('hr_penalties p', 'c2.penalty_id = p.id', 'left');

        if ($this_year_only) {

            $curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));

            $this->db->where('year(mv.commission_date)', $curr_date->format('Y'));
        }

        return $this->db
                        ->where(array(
                            'mv.employee_id' => is_numeric($id) ? $id : 0,
                            'mv.status' => 1,
                            'v.status' => 1
                        ))
                        ->order_by('vio_id, mv.commission_date')
                        ->get()
                        ->result();
    }

    public function logged_in($id = 0) {

        $user = $this->ion_auth->user($id ? $id : $this->session->userdata('user_id'))->row();

        return $user ? count($this->db->get_where('hr_sessions', array('session_id' => $user->last_session_id))->result()) > 0 : false;
    }

    public function last_online($id = 0) {

        $user = $this->ion_auth->user($id ? $id : $this->session->userdata('user_id'))->row();

        if ($user) {

            if (count($sess = $this->db->get_where('hr_sessions', array('session_id' => $user->last_session_id))->result()) > 0)
                return array(1, $sess[0]->last_activity);
            else
                return array(2, $user->last_login);
        } else
            return false;
    }

    public function getExpatNationalities($q) {

        return array_map(function ($a) {
            return $a->nationality;
        }, $this->db->distinct()->select('nationality')->like('nationality', $q)->order_by('nationality')->get('hr_expat')->result());
    }

    public function getJobTitles($q) {

        return array_map(function ($a) {
            return $a->mb_2;
        }, $this->db->distinct()->select('mb_2')->like('mb_2', $q)->order_by('mb_2')->get('g4_member')->result());
    }

    public function getRegularables() {

        // $curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));

        return $this->db
                        ->select('m.mb_id, m.mb_lname, m.mb_fname, d.dept_name, m.mb_status, m.mb_no, m.mb_3, m.mb_nick')
                        ->from('g4_member m')
                        ->join('dept d', 'm.mb_deptno = d.dept_no')
                        ->where_not_in('m.mb_no', array(229, 382, 348))
                        ->where(array('period_diff(extract(year_month from now()), extract(year_month from m.mb_commencement)) >=' => 6, 'm.mb_employment_status' => 1, 'm.mb_status' => 1))
                        ->order_by('m.mb_id', 'desc')
                        ->get()->result();
    }

    public function getRegularablesCount() {

        // $curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));

        return $this->db->where(array('period_diff(extract(year_month from now()), extract(year_month from mb_commencement)) >=' => 6, 'mb_employment_status' => 1, 'mb_status' => 1))->from('g4_member')->count_all_results();
    }

    public function updateDeptHead($dept_id, $emp_id) {

        $this->db->where('dept_id', $dept_id);
        $q = $this->db->get('hr_dept_heads');

        $data = array(
            'dept_id' => $dept_id,
            'employee_id' => $emp_id,
            'updated_by' => $this->session->userdata('user_id'),
            'updated_on' => time()
        );

        return is_numeric($dept_id) && is_numeric($emp_id) && ($q->num_rows() > 0 ? $this->db->where('dept_id', $dept_id)->update('hr_dept_heads', $data) > 0 : $this->db->insert('hr_dept_heads', $data)) && $this->db->affected_rows();
    }

    private function mysql_password($password) {

        return '*' . strtoupper(sha1(sha1($password, true)));
    }

    // Temporary login check
    public function check_credentials($user, $pass) {
        
    }

    public function get_report_modules($where_arr = array()) {
        $this->db->select("a.*,  b.dept_name,  (CASE WHEN a.status = '1' THEN 'Active' ELSE 'Inactive' END) status_name");
        $this->db->from('tk_access_report a');
        $this->db->join('dept AS b', 'a.dept_no=b.dept_no', 'left');
        if (count($where_arr) > 0)
            $this->db->where($where_arr);
        $result = $this->db->get();
        return $result->result();
        /* if($result->num_rows() > 0){
          if($result->num_rows() == 1){
          return $result->row();
          }
          else{
          return $result->result();
          }
          }
          else {
          return array();
          } */
    }

    public function delete_access_report($table = "tk_access_report", $data, $param) {
        return $this->db->update($table, $data, $param);
    }

    public function batch_insert($table, $data) {
        $this->db->insert_batch($table, $data);
        return $this->db->affected_rows();
    }

// public function import($id, $id2) {
    // return false;
    // }


    public function getCondo() {

        $this->db->where("is_active = 1 And status = 1");
        //$this->db->where("is_active", 1);  
        return $this->db->order_by('condo_name')->get('condo_list')->result();
    }

    public function getAllEmployeeInCondo($where) {
        return $this->db->select('mem.*,CONCAT(mem.mb_nick," ",mem.mb_lname) as mb_name,con.*,lv.*', false)
                        ->from('g4_member mem')
                        ->join('condo_list con', 'mem.condo_id=con.condo_id')
                        ->join('tk_member_schedule sched', 'sched.mb_no=mem.mb_no')
                        ->join('tk_leave_code lv', 'sched.leave_id=lv.leave_id', 'left')
                        ->where($where)
                        ->group_by('mem.mb_no')
                        ->get()
                        ->result();
    }

}
