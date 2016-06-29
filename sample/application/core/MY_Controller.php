<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    protected $_mode;
    protected $_version;
    protected $base_dept_score;
    protected $base_hr_score;
    protected $base_hrmis_score;
    protected $_doc;

    public function __construct() {
        parent::__construct();

        // if(array_key_exists('ci_session', $_COOKIE))
        // $this->session->set_userdata(array_merge(unserialize($_COOKIE['ci_session']), $this->session->all_userdata()));
        // else
        // redirect('http://' . $this->input->server('HTTP_HOST') . '/intranet/', 'refresh');

        if (!($this->uri->segment(1) == 'notifications' && $this->uri->segment(2) == 'scheduler') &&
            !($this->uri->segment(1) == 'employees' && $this->uri->segment(2) == 'updateHRScorePoint')
            ) {

            // if(!$this->session->userdata('mb_no'))
            // redirect('auth/login', 'refresh');

            $this->load->library('user_agent');
            $this->load->library('encrypt');

            $_u = $this->input->get('u', true);
            $_h = $this->input->get('h', true);

            if (!$this->ion_auth->logged_in() || !$this->session->userdata('mb_no')) {

                if ($this->agent->is_referral() && parse_url($this->agent->referrer(), PHP_URL_HOST) == '10.120.10.125' && $_u && $_h && $this->encrypt->decode($_h) == $_u) {

                    $_tables = $this->config->item('tables', 'ion_auth');

                    $query = $this->db
                            ->select('username, email, id, active, last_login')
                            ->where('username', $this->db->escape_str($_u))
                            ->limit(1)
                            ->get($_tables['users']);

                    if ($query->num_rows() === 1) {
                        $user = $query->row();

                        $this->ion_auth->set_session($user);

                        $this->ion_auth->update_last_login($user->id);

                        $this->ion_auth->clear_login_attempts($user->{'username'});

                        $this->ion_auth->remember_user($user->id);

                        $this->session->sess_update(true);

                        redirect('/', 'refresh');
                    }
                } else
                    redirect('auth/login', 'refresh');
            }
            elseif ($_u && $_h)
                redirect('/', 'refresh');

            // if($this->session->userdata('mb_deptno') != 24)
            // redirect('http://' . $this->input->server('HTTP_HOST') . '/intranet/dashboard/', 'refresh');
            
            $this->lang->load('common');

            $this->_mode = $this->input->get('mode', true) || '';

            $this->load->model('cite_model', 'cite');

            $this->load->model('employees_model', 'employees');

            // $this->config->load('kpi', true);
            // $this->base_dept_score = $this->config->item('base_dept_score', 'kpi');
            // $this->base_hr_score = $this->config->item('base_hr_score', 'kpi');
            // $this->base_hrmis_score = $this->config->item('base_hrmis_score', 'kpi');

            $this->load->model('kpi_model', 'kpi');

            $this->base_dept_score = $this->kpi->getBaseScore(2)[0]->score;
            $this->base_hr_score = $this->kpi->getBaseScore(3)[0]->score;
            $this->base_hrmis_score = $this->kpi->getBaseScore(1)[0]->score;

            $this->_doc = $this->employees->getDOC($this->session->userdata('mb_no'));

            // $this->load->library('monsterid/monsterid', array('seed' => $this->session->userdata('mb_no'), 'size' => 100));
            // $this->checkRegularables();
        }
    }

    private function load_view($template, $vars, $return = false) {

        if ($return)
            return $this->load->view($template, $vars, $return);
        else
            $this->load->view($template, $vars, $return);
    }

    public function view_template($template, $title, $vars = array()) {

        $breadcrumbs = array_key_exists('breadcrumbs', $vars) ? $vars['breadcrumbs'] : array();
        $js = array_key_exists('js', $vars) ? $vars['js'] : array();
        $css = array_key_exists('css', $vars) ? $vars['css'] : array();

        if ($this->_mode == 'fragment')
            print json_encode(array(
                'title' => $title,
                'breadcrumbs' => $breadcrumbs,
                'content' => $this->load->view($template, $vars, true),
                'js' => $js,
                'css' => $css
            ));
        else {

            $this->_version = 0;

            try {

                $sqldb = new SQLite3(dirname(__FILE__) . '\..\..\.svn\wc.db', SQLITE3_OPEN_READONLY);

                $result = $sqldb->query('SELECT revision FROM NODES');

                if ($result) {

                    $row = $result->fetchArray(SQLITE3_ASSOC);

                    $this->_version = $row['revision'];
                }
            } catch (Exception $e) {
                
            }

            $this->config->load('notifs', true);

            $subordinates = $this->employees->getSubordinates(0, 'u2.*, k.score, m.mb_no');

            $subords_pending = 0;

            foreach ($subordinates as $subordinate)
                if (is_null($subordinate->score) || $subordinate->score == 0)
                    $subords_pending++;

            $curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));

            $curr_month = intval($curr_date->format('n'), 10);

            $quadrant = floor(($curr_month - 1) / 6);

            $start = array(12, 6);

            $end = array(5, 11);

            $cal_info = cal_info(0);

// 			$from_to = '
// 					<select>
// 						<option>
// 						' . implode('</option><option>', array_slice($cal_info['months'], $curr_month)) . '
// 						</option>
// 					</select>
// 				';

            $this->load->view('header', array(
                'page_title' => $title,
                'breadcrumbs' => $breadcrumbs,
                'pending_count' => $this->cite->getPendingCount(),
                'rev' => $this->_version,
                'subordinates' => $subordinates,
                'subords_pending' => $subords_pending
            ));
            $this->load->view($template, array_merge($vars, array('page_title' => $title)));
            $this->load->view('footer', array(
                'js' => $js,
                'css' => $css,
                'rev' => $this->_version,
                'notif_icons' => json_encode($this->config->item('icons', 'notifs')),
                'subordinates' => $subordinates,
                'from_to' => $cal_info['months'][$start[$quadrant]] . ' to ' . $cal_info['months'][$end[$quadrant]],
//                 'from_to' => $cal_info['months'][$curr_month],
                'base_dept_score' => $this->base_dept_score,
                'base_hr_score' => $this->base_hr_score,
                'base_hrmis_score' => $this->base_hrmis_score,
                '_doc' => $this->_doc['mb_commencement']
            ));
        }
    }

    // private function checkRegularables() {
    // $file = sys_get_temp_dir() . '\checkRegularables_checked';
    // $mtime = @filemtime($file);
    // if(!file_exists($file) || ($mtime && date('Y-m-d', $mtime) != date('Y-m-d'))) {
    // $this->load->model('notifications_model', 'notifs');
    // $this->load->model('employees_model', 'employees');
    // $this->notifs->create('system', 1, array($this->employees->getRegularablesCount()), 0, 24, 'employees', 'getRegularables');
    // file_put_contents($file, $mtime);
    // }
    // }
    // public function _output($output) {
    // require('GoogleTranslate.php');
    // require('simple_html_dom.php');
    // $html = str_get_html($output);
    // if($html) {
    // $translates = $html->find('.translate');
    // foreach($translates as $translate)
    // $translate->innertext = GoogleTranslate::staticTranslate($translate->innertext, 'en', 'tl');
    // echo $html;
    // unset($html);
    // }
    // else
    // echo $output;
    // }

    public function calculateToAttendance($biometrics) {
        $this->load->model('fix_model', 'fix_m');
        foreach ($biometrics as $key => $bio) {
            if ($bio->in_out_mode != -1) {
                $dateStr = ($bio->year_log . "-" . str_pad($bio->month_log, 2, "0", STR_PAD_LEFT) . "-" . str_pad($bio->day_log, 2, "0", STR_PAD_LEFT));
                $timeStr = (str_pad($bio->hour_log, 2, "0", STR_PAD_LEFT) . ":" . str_pad($bio->min_log, 2, "0", STR_PAD_LEFT));

                $curDate = new DateTime($dateStr . " 00:00:00");
                $timekeeping_query = "SELECT tms.*,tsc.shift_hr_from,tsc.shift_min_from,tsc.shift_hr_to,tsc.shift_min_to, gm.mb_id, gm.mb_lname, gm.mb_3
									FROM tk_member_schedule tms
									INNER JOIN g4_member gm 
									  ON tms.mb_no = gm.mb_no
									LEFT JOIN tk_shift_code tsc 
									  ON tms.shift_id = tsc.shift_id
								  WHERE 
									`gm`.`enroll_number` 	= " . $bio->enroll_number . " AND 
									`tms`.`year` 			= " . $bio->year_log . " AND 
									`tms`.`month` 			= " . $bio->month_log . " AND 
									`tms`.`day` 			= " . $bio->day_log;

                $cur_schedule_data = $this->fix_m->query($timekeeping_query);
                $cur_row = $cur_schedule_data->row();
                $has_current = count($cur_row);

                $prevDate = new DateTime($dateStr . " 00:00:00");
                $prevDate->modify("-1 day");
                $prev_timekeeping_query = "SELECT tms.*,tsc.shift_hr_from,tsc.shift_min_from,tsc.shift_hr_to,tsc.shift_min_to, gm.mb_id, gm.mb_lname, gm.mb_3
									FROM tk_member_schedule tms
									INNER JOIN g4_member gm 
									  ON tms.mb_no = gm.mb_no
									LEFT JOIN tk_shift_code tsc 
									  ON tms.shift_id = tsc.shift_id
								  WHERE 
									`gm`.`enroll_number` 	= " . $bio->enroll_number . " AND 
									`tms`.`year` 			= " . $prevDate->format("Y") . " AND 
									`tms`.`month` 			= " . $prevDate->format("n") . " AND 
									`tms`.`day` 			= " . $prevDate->format("j");

                $prev_schedule_data = $this->fix_m->query($prev_timekeeping_query);
                $prev_row = $prev_schedule_data->row();
                $has_previous = count($prev_row);

                $nextDate = new DateTime($dateStr . " 00:00:00");
                $nextDate->modify("+1 day");
                $next_timekeeping_query = "SELECT tms.*,tsc.shift_hr_from,tsc.shift_min_from,tsc.shift_hr_to,tsc.shift_min_to, gm.mb_id, gm.mb_lname, gm.mb_3
									FROM tk_member_schedule tms
									INNER JOIN g4_member gm 
									  ON tms.mb_no = gm.mb_no
									LEFT JOIN tk_shift_code tsc 
									  ON tms.shift_id = tsc.shift_id
								  WHERE 
									`gm`.`enroll_number` 	= " . $bio->enroll_number . " AND 
									`tms`.`year` 			= " . $nextDate->format("Y") . " AND 
									`tms`.`month` 			= " . $nextDate->format("n") . " AND 
									`tms`.`day` 			= " . $nextDate->format("j");

                $next_schedule_data = $this->fix_m->query($next_timekeeping_query);
                $next_row = $next_schedule_data->row();
                $has_next = count($next_row);

                $valid = false;
                $curLog = new DateTime($dateStr . " " . $timeStr . ":00");

                if ($has_previous) {
                    if ($prev_row->shift_id > 0 && !$prev_row->leave_id) {

                        $shift_in = new DateTime($prevDate->format("Y-m-d") . " " . $prev_row->shift_hr_from . ":" . $prev_row->shift_min_from . ":00");
                        $shift_out = new DateTime($prevDate->format("Y-m-d") . " " . $prev_row->shift_hr_to . ":" . $prev_row->shift_min_to . ":00");

                        if ($shift_in > $shift_out)
                            $shift_out->modify("+1 day");

                        if ($bio->in_out_mode == 0) {
                            $tmp_shift_out = new DateTime($shift_out->format("Y-m-d H:i:s"));
                            $tmp_shift_out->modify("+3 hours");
                            if ($curLog <= $tmp_shift_out) {

                                $timekeeping_query = "SELECT * 
								FROM tk_attendance ta
							  WHERE 
								`ta`.`mb_no` 	    = '" . $prev_row->mb_no . "' AND 
								`ta`.`att_date` 	= '" . $shift_in->format("Y-m-d") . "'";
                                $attendance_res = $this->fix_m->query($timekeeping_query);
                                $attendance_data = $attendance_res->row();
                                if (count($attendance_data) == 0) {
                                    $tardy = ($curLog->format("U") - $shift_in->format("U")) / 60;
                                    if ($tardy < 0)
                                        $tardy = 0;

                                    $insertQuery = "INSERT INTO tk_attendance
											(mb_no,att_date,shift_id,actual_in,actual_in_sec,tardy)
										  VALUES
											('" . $prev_row->mb_no . "',
														'".$prevDate->format("Y-m-d")."',
											'" . $prev_row->shift_id . "',
											'" . $timeStr . "',
											'" . $bio->sec_log . "',
											'" . $tardy . "');";
                                    $attendance_res = $this->fix_m->query($insertQuery);
									$valid = true;
                                }
                                else if (empty($attendance_data->actual_in)) {
                                    $tardy = ($curLog->format("U") - $shift_in->format("U")) / 60;
                                    if ($tardy < 0)
                                        $tardy = 0;

                                    $insertQuery = "UPDATE tk_attendance
								  SET
								   actual_out = NULL,
								   actual_out_sec = '0',
								   undertime = '0',
								   overtime = '0',
								   reg_hrs = '0',
								   nsd = '0',
								   actual_in = '" . $timeStr . "',
								   actual_in_sec = '" . $bio->sec_log . "',
								   tardy = '" . $tardy . "'
								  WHERE
									mb_no = '" . $prev_row->mb_no . "' AND
												att_date = '".$prevDate->format("Y-m-d")."'";
                                    $attendance_res = $this->fix_m->query($insertQuery);
								  $valid = true;
                                }
                                else if ($has_current) {
                                    if ($cur_row->shift_id > 0) {
                                        $shift_out = new DateTime($prevDate->format("Y-m-d") . " " . $prev_row->shift_hr_to . ":" . $prev_row->shift_min_to . ":00");
                                        $shift_in = new DateTime($curDate->format("Y-m-d") . " " . $cur_row->shift_hr_from . ":" . $cur_row->shift_min_from . ":00");
										// $valid = true;
                                        if ($shift_out->format("His") == $shift_in->format("His")) {
                                            $shift_in->modify("-1 hours");
											$valid = true;
                                            if ($shift_in <= $curLog)
                                                $valid = false;
                                        }
                                    }
                                }
                            }
                        }
                        else if ($bio->in_out_mode == 1) {
                            if ($curLog <= $shift_out) {
                                $valid = true;
                            }
							else if ($has_current) {
                                if ($cur_row->shift_id > 0) {
                                    $cur_shift_in = new DateTime($curDate->format("Y-m-d") . " " . $cur_row->shift_hr_from . ":" . $cur_row->shift_min_from . ":00");
                                    $cur_shift_out = new DateTime($curDate->format("Y-m-d") . " " . $cur_row->shift_hr_to . ":" . $cur_row->shift_min_to . ":00");

                                    if ($cur_shift_in > $cur_shift_out)
                                        $cur_shift_out->modify("+1 day");
                                    $cur_shift_in->modify("-1 hour");
                                    if ($curLog < $cur_shift_in) {
                                        $timekeeping_query = "SELECT * 
								FROM tk_attendance ta
							  WHERE 
								`ta`.`mb_no` 	    = '" . $prev_row->mb_no . "' AND 
								`ta`.`att_date` 	= '" . $curLog->format("Y-m-d") . "'";
                                        $attendance_res = $this->fix_m->query($timekeeping_query);
                                        $attendance_data = $attendance_res->row();
                                        if (count($attendance_data)) {
                                            $timekeeping_query = "DELETE
							  FROM
								tk_attendance
							  WHERE 
								`att_id` 	    = '" . $attendance_data->att_id . "'";
                                            $attendance_res = $this->fix_m->query($timekeeping_query);
                                        }
                                        $cur_shift_in->modify("-3 hour");
                                        if ($curLog < $cur_shift_in)
                                            $valid = true;
                                    }
                                    else {
                                        $cur_shift_in = new DateTime($curDate->format("Y-m-d") . " " . $cur_row->shift_hr_from . ":" . $cur_row->shift_min_from . ":00");
                                        $cur_shift2_in = new DateTime($curDate->format("Y-m-d") . " " . $cur_row->shift_hr_from . ":" . $cur_row->shift_min_from . ":00");
                                        $cur_shift2_in->modify("+2 hours");
                                        if ($cur_shift_in == $shift_out && $cur_shift2_in > $curLog) {
                                            $valid = true;
                                        }
                                    }
								}
								else {
                                    if ($curLog > $shift_out) {
                                        $timekeeping_query = "SELECT * 
								FROM tk_attendance ta
							  WHERE 
								`ta`.`mb_no` 	    = '" . $prev_row->mb_no . "' AND 
								`ta`.`att_date` 	= '" . $curLog->format("Y-m-d") . "'";
                                        $attendance_res = $this->fix_m->query($timekeeping_query);
                                        $attendance_data = $attendance_res->row();

                                        if (count($attendance_data) == 0) {
                                            $timekeeping_query = "SELECT * 
								FROM tk_attendance ta
							  WHERE 
								`ta`.`mb_no` 	    = '" . $prev_row->mb_no . "' AND 
								`ta`.`att_date` 	= '" . $shift_in->format("Y-m-d") . "'";
                                            $attendance_res = $this->fix_m->query($timekeeping_query);
                                            $attendance_data = $attendance_res->row();
                                            if (count($attendance_data) == 0)
                                                $valid = true;
                                            else if ($attendance_data->actual_out) {
                                                $previous_shift_in = new DateTime($attendance_data->att_date . " " . $attendance_data->actual_in);
                                                $previous_shift_out = new DateTime($attendance_data->att_date . " " . $attendance_data->actual_out);
                                                if ($previous_shift_out < $previous_shift_in)
                                                    $previous_shift_out->modify("+1 day");
                                                $previous_out = new DateTime($previous_shift_out->format("Y-m-d ") . $attendance_data->actual_out);
                                                if ($curLog > $previous_out && $curLog->format("Y-m-d") == $previous_out->format("Y-m-d"))
                                                    $valid = true;
											}
											else
                                                $valid = true;
                                        }
                                    }
                                }
                            }
                            if ($valid) {
                                $timekeeping_query = "SELECT * 
								FROM tk_attendance ta
							  WHERE 
								`ta`.`mb_no` 	    = '" . $prev_row->mb_no . "' AND 
								`ta`.`att_date` 	= '" . $shift_in->format("Y-m-d") . "'";
                                $attendance_res = $this->fix_m->query($timekeeping_query);
                                $attendance_data = $attendance_res->row();

                                $excess = 0;
                                $nsd = 0;
                                $regHrs = 0;
                                if (count($attendance_data) == 0) {
                                    $undertime = ($shift_out->format("U") - $curLog->format("U")) / 60;
									if($undertime < 0) $undertime = 0;

                                    if ($curLog->format("U") < $shift_out->format("U")) {
                                        $obt_query = "SELECT 
									  *,
									  CONCAT(`date`,' ',TIME(time_in)) full_timein,
                                      IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
									FROM tk_obt_application `toa`
									WHERE
									  `toa`.`mb_no` = '" . $cur_row->mb_no . "' AND 
									  `toa`.`date` 	= '" . $shift_in->format("Y-m-d") . "' AND
									  TIME('" . $timeStr . "') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
									  AND `toa`.`status` = 3
									ORDER BY time_out DESC";
                                        $obt_res = $this->fix_m->query($obt_query);
										$obt_data = $obt_res->result();
										if(count($obt_data)) {
											foreach($obt_data as $obt_row) {
												$obt_from = new DateTime($obt_row->full_timein);
												$obt_to = new DateTime($obt_row->full_timeout);
												if($obt_to->format("U") == $shift_out->format("U")) {
												  $shift_out = $obt_from;
												}
												else
												  break;
											}

											if($curLog->format("U") < $shift_out->format("U")) {
												$undertime = ($shift_out->format("U")-$curLog->format("U"))/60;
											}
											else {
                                                $undertime = 0;
                                            }
                                        }
                                    }

                                    if ($curLog > $shift_out) {
                                        $excessOut = ($curLog->format("U") - $shift_out->format("U"));
                                        $excess += floor($excessOut / 3600);
                                        $countedOut = new DateTime($shift_out->format("Y-m-d H:i:s"));
                                    }
									else {
                                        $countedOut = new DateTime($curLog->format("Y-m-d H:i:s"));
                                    }

                                    $insertQuery = "INSERT INTO tk_attendance
											(mb_no,att_date,shift_id,actual_out,actual_out_sec,undertime,overtime)
										  VALUES
											('" . $prev_row->mb_no . "',
											'" . $shift_in->format("Y-m-d") . "',
											'" . $prev_row->shift_id . "',
											'" . $timeStr . "',
											'" . $bio->sec_log . "',
											'" . $undertime . "',
											'" . $excess . "');";
                                    $attendance_res = $this->fix_m->query($insertQuery);
                                }
								else {
                                    if ($attendance_data->actual_in != "") {
                                        $nsdDateIn = new DateTime($shift_in->format("Y-m-d 22:00:00"));
                                        $nsdDateOut = new DateTime($shift_in->format("Y-m-d 22:00:00"));
                                        // Custom by HR
                                        if ($shift_out->format("Hi") == "0700")
                                            $nsdDateOut->modify("+9 hour");
                                        else
                                            $nsdDateOut->modify("+8 hour");

                                        $tmpDateIn = new DateTime($attendance_data->att_date . " " . $attendance_data->actual_in . ":00");
										if($tmpDateIn < $shift_in) {
											$tmpDateIn->modify("+1 day");
										}
						
                                        if ($tmpDateIn > $curLog)
                                            $tmpDateIn->modify("-1 day");

                                        if ($tmpDateIn < $shift_in) {
                                            $excessIn = ($shift_in->format("U") - $tmpDateIn->format("U"));
                                            $excess += floor($excessIn / 3600);
                                        }

                                        $countedIn = new DateTime($shift_in->format("Y-m-d H:i:s"));

                                        if ($curLog > $shift_out) {
                                            $excessOut = ($curLog->format("U") - $shift_out->format("U"));
                                            $excess += floor($excessOut / 3600);
                                        }

                                        $countedOut = new DateTime($shift_out->format("Y-m-d H:i:s"));

                                        $regHrsHr = ($countedOut->format("U") - $countedIn->format("U")) / 3600;
                                        $regHrs = floor($regHrsHr) + ((($regHrsHr - floor($regHrsHr)) * 60) / 100);

                                        if ($shift_in < $nsdDateIn)
                                            $countedIn = new DateTime($nsdDateIn->format("Y-m-d H:i:s"));
                                        else {
                                            if ($shift_in->format("i") != "00") {
                                                $countedIn = new DateTime($shift_in->format("Y-m-d :00:00"));
											}
											else
                                                $countedIn = new DateTime($shift_in->format("Y-m-d H:00:00"));
                                        }

                                        if ($shift_out > $nsdDateOut)
                                            $countedOut = new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
                                        else
                                            $countedOut = new DateTime($shift_out->format("Y-m-d H:i:s"));

                                        $nsd = floor(($countedOut->format("U") - $countedIn->format("U")) / 3600);
										if($nsd < 0) $nsd = 0;
                                    }
                                    $undertime = ($shift_out->format("U") - $curLog->format("U")) / 60;
									if($undertime < 0) $undertime = 0;

                                    if ($curLog->format("U") < $shift_out->format("U")) {
                                        $obt_query = "SELECT 
									  *,
									  CONCAT(`date`,' ',TIME(time_in)) full_timein,
                                      IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
									FROM tk_obt_application `toa`
									WHERE
									  `toa`.`mb_no` = '" . $cur_row->mb_no . "' AND 
									  `toa`.`date` 	= '" . $shift_in->format("Y-m-d") . "' AND
									  TIME('" . $timeStr . "') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
									  AND `toa`.`status` = 3
									ORDER BY time_out DESC";
                                        $obt_res = $this->fix_m->query($obt_query);

										$obt_data = $obt_res->result();
										if(count($obt_data)) {
											foreach($obt_data as $obt_row) {
												$obt_from = new DateTime($obt_row->full_timein);
												$obt_to = new DateTime($obt_row->full_timeout);
												if($obt_to->format("U") == $shift_out->format("U")) {
													$shift_out = $obt_from;
												}
												else
													break;
											}
						
											if($curLog->format("U") < $shift_out->format("U")) {
												$undertime = ($shift_out->format("U")-$curLog->format("U"))/60;
											}
											else {
                                                $undertime = 0;
                                            }
                                        }
                                    }

                                    $insertQuery = "UPDATE tk_attendance
								  SET
								   actual_out = '" . $timeStr . "',
								   actual_out_sec = '" . $bio->sec_log . "',
								   undertime = '" . $undertime . "',
								   overtime = '" . $excess . "',
								   reg_hrs = '" . $regHrs . "',
								   nsd = '" . $nsd . "'
								  WHERE
									mb_no = '" . $prev_row->mb_no . "' AND
									att_date = '" . $shift_in->format("Y-m-d") . "'";
                                    $attendance_res = $this->fix_m->query($insertQuery);
                                }
                            }
                        }
                    }
                }

                if ($has_current && !$valid) {
                    if ($cur_row->shift_id > 0 && !$cur_row->leave_id) {

                        $shift_in = new DateTime($curDate->format("Y-m-d") . " " . $cur_row->shift_hr_from . ":" . $cur_row->shift_min_from . ":00");
                        $shift_out = new DateTime($curDate->format("Y-m-d") . " " . $cur_row->shift_hr_to . ":" . $cur_row->shift_min_to . ":00");

                        if ($shift_in > $shift_out)
                            $shift_out->modify("+1 day");

                        if ($bio->in_out_mode == 0) {
                            $valid = true;

                            $timekeeping_query = "SELECT * 
								FROM tk_attendance ta
							  WHERE 
								`ta`.`mb_no` 	    = '" . $cur_row->mb_no . "' AND 
								`ta`.`att_date` 	= '" . $shift_in->format("Y-m-d") . "'";
                            $attendance_res = $this->fix_m->query($timekeeping_query);
                            $attendance_data = $attendance_res->row();

                            $tardy = ($curLog->format("U") - $shift_in->format("U")) / 60;
                            if ($tardy < 0)
                                $tardy = 0;

                            if (count($attendance_data) == 0) {
                                $insertQuery = "INSERT INTO tk_attendance
										(mb_no,att_date,shift_id,actual_in,actual_in_sec,tardy)
									  VALUES
										('" . $cur_row->mb_no . "',
										'" . $shift_in->format("Y-m-d") . "',
										'" . $cur_row->shift_id . "',
										'" . $timeStr . "',
										'" . $bio->sec_log . "',
										'" . $tardy . "');";
                                $attendance_res = $this->fix_m->query($insertQuery);
                            }
							else if (empty($attendance_data->actual_in) || $timeStr == $attendance_data->actual_in) {
                                $insertQuery = "UPDATE tk_attendance
								  SET
								   actual_out = NULL,
								   actual_out_sec = '0',
								   undertime = '0',
								   overtime = '0',
								   reg_hrs = '0',
								   nsd = '0',
								   actual_in = '" . $timeStr . "',
								   actual_in_sec = '" . $bio->sec_log . "',
								   tardy = '" . $tardy . "'
								  WHERE
									mb_no = '" . $cur_row->mb_no . "' AND
									att_date = '" . $shift_in->format("Y-m-d") . "'";
                                $attendance_res = $this->fix_m->query($insertQuery);
                            }
							else if ($has_next) {
                                // print_r($next_row);
                                $tmpCurDate = new DateTime($curDate->format("Y-m-d") . " 00:00:00");
                                $tmpCurDate->modify("+1 day");

                                if ($next_row->shift_hr_from) {
                                    $shift_in = new DateTime($tmpCurDate->format("Y-m-d") . " " . $next_row->shift_hr_from . ":" . $next_row->shift_min_from . ":00");
                                    $shift_out = new DateTime($tmpCurDate->format("Y-m-d") . " " . $next_row->shift_hr_to . ":" . $next_row->shift_min_to . ":00");

                                    if ($shift_in > $shift_out)
                                        $shift_out->modify("+1 day");

                                    $shift_in->modify("-4 hours");

                                    // echo $shift_in->format("Y-m-d H:i:s")." < ".$curLog->format("Y-m-d H:i:s");
                                    if ($shift_in < $curLog) {
                                        $shift_in->modify("+4 hours");
                                        $timekeeping_query = "SELECT * 
										FROM tk_attendance ta
									  WHERE 
										`ta`.`mb_no` 	    = '" . $next_row->mb_no . "' AND 
										`ta`.`att_date` 	= '" . $shift_in->format("Y-m-d") . "'";
                                        $attendance_res = $this->fix_m->query($timekeeping_query);
                                        $attendance_data = $attendance_res->row();

                                        $tardy = ($curLog->format("U") - $shift_in->format("U")) / 60;
                                        if ($tardy < 0)
                                            $tardy = 0;

                                        if (count($attendance_data) == 0) {
                                            $insertQuery = "INSERT INTO tk_attendance
												(mb_no,att_date,shift_id,actual_in,actual_in_sec,tardy)
											  VALUES
												('" . $next_row->mb_no . "',
												'" . $shift_in->format("Y-m-d") . "',
												'" . $next_row->shift_id . "',
												'" . $timeStr . "',
												'" . $bio->sec_log . "',
												'" . $tardy . "');";
                                            $attendance_res = $this->fix_m->query($insertQuery);
                                        }
                                    }
                                }
                            }
                        }
						else if ($bio->in_out_mode == 1 && $curLog >= $shift_in) {
                            $valid = true;

                            $timekeeping_query = "SELECT * 
								FROM tk_attendance ta
							  WHERE 
								`ta`.`mb_no` 	    = '" . $cur_row->mb_no . "' AND 
								`ta`.`att_date` 	= '" . $shift_in->format("Y-m-d") . "'";
                            $attendance_res = $this->fix_m->query($timekeeping_query);
                            $attendance_data = $attendance_res->row();
                            $excess = 0;
                            $nsd = 0;
                            $regHrs = 0;
                            if (count($attendance_data) == 0) {
                                $undertime = ($shift_out->format("U") - $curLog->format("U")) / 60;
								if($undertime < 0) $undertime = 0;

                                if ($curLog->format("U") < $shift_out->format("U")) {
                                    $obt_query = "SELECT 
									  *,
									  CONCAT(`date`,' ',TIME(time_in)) full_timein,
                                      IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
									FROM tk_obt_application `toa`
									WHERE
									  `toa`.`mb_no` = '" . $cur_row->mb_no . "' AND 
									  `toa`.`date` 	= '" . $shift_in->format("Y-m-d") . "' AND
									  TIME('" . $timeStr . "') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
									  AND `toa`.`status` = 3
									ORDER BY time_out DESC";
                                    $obt_res = $this->fix_m->query($obt_query);

									$obt_data = $obt_res->result();
									if(count($obt_data)) {
										foreach($obt_data as $obt_row) {
											$obt_from = new DateTime($obt_row->full_timein);
											$obt_to = new DateTime($obt_row->full_timeout);
											if($obt_to->format("U") == $shift_out->format("U")) {
												$shift_out = $obt_from;
											}
											else
												break;
										}
						
										if($curLog->format("U") < $shift_out->format("U")) {
											$undertime = ($shift_out->format("U")-$curLog->format("U"))/60;
										}
										else {
                                            $undertime = 0;
                                        }
                                    }
                                }

                                if ($curLog > $shift_out) {
                                    $excessOut = ($curLog->format("U") - $shift_out->format("U"));
                                    $excess += floor($excessOut / 3600);
                                    $countedOut = new DateTime($shift_out->format("Y-m-d H:i:s"));
                                }
								else {
                                    $countedOut = new DateTime($curLog->format("Y-m-d H:i:s"));
                                }

                                $insertQuery = "INSERT INTO tk_attendance
											(mb_no,att_date,shift_id,actual_out,actual_out_sec,undertime,overtime)
										  VALUES
											('" . $cur_row->mb_no . "',
											'" . $shift_in->format("Y-m-d") . "',
											'" . $cur_row->shift_id . "',
											'" . $timeStr . "',
											'" . $bio->sec_log . "',
											'" . $undertime . "',
											'" . $excess . "');";
                                $attendance_res = $this->fix_m->query($insertQuery);
                            }
							else {
                                if ($attendance_data->actual_in != "") {
                                    $nsdDateIn = new DateTime($shift_in->format("Y-m-d 22:00:00"));
                                    $nsdDateOut = new DateTime($shift_in->format("Y-m-d 22:00:00"));
                                    // Custom by HR
                                    if ($shift_out->format("Hi") == "0700")
                                        $nsdDateOut->modify("+9 hour");
                                    else
                                        $nsdDateOut->modify("+8 hour");

                                    $tmpDateIn = new DateTime($attendance_data->att_date . " " . $attendance_data->actual_in . ":00");

                                    if ($tmpDateIn > $curLog)
                                        $tmpDateIn->modify("-1 day");

                                    if ($tmpDateIn < $shift_in) {
                                        $excessIn = ($shift_in->format("U") - $tmpDateIn->format("U"));
                                        $excess += floor($excessIn / 3600);
                                    }

                                    $countedIn = new DateTime($shift_in->format("Y-m-d H:i:s"));

                                    if ($curLog > $shift_out) {
                                        $excessOut = ($curLog->format("U") - $shift_out->format("U"));
                                        $excess += floor($excessOut / 3600);
                                    }

                                    $countedOut = new DateTime($shift_out->format("Y-m-d H:i:s"));

                                    $regHrsHr = ($countedOut->format("U") - $countedIn->format("U")) / 3600;
                                    $regHrs = floor($regHrsHr) + ((($regHrsHr - floor($regHrsHr)) * 60) / 100);

                                    if ($shift_in < $nsdDateIn)
                                        $countedIn = new DateTime($nsdDateIn->format("Y-m-d H:i:s"));
                                    else
                                        $countedIn = new DateTime($shift_in->format("Y-m-d H:i:s"));

                                    if ($shift_out > $nsdDateOut)
                                        $countedOut = new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
                                    else
                                        $countedOut = new DateTime($shift_out->format("Y-m-d H:i:s"));

                                    $nsd = floor(($countedOut->format("U") - $countedIn->format("U")) / 3600);
									if($nsd < 0) $nsd = 0;
                                }

                                $undertime = 0;
                                $undertime = ($shift_out->format("U") - $curLog->format("U")) / 60;
								if($undertime < 0) $undertime = 0;

                                if ($curLog->format("U") < $shift_out->format("U")) {
                                    $obt_query = "SELECT 
									  *,
									  CONCAT(`date`,' ',TIME(time_in)) full_timein,
                                      IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
									FROM tk_obt_application `toa`
									WHERE
									  `toa`.`mb_no` = '" . $cur_row->mb_no . "' AND 
									  `toa`.`date` 	= '" . $shift_in->format("Y-m-d") . "' AND
									  TIME('" . $timeStr . "') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
									  AND `toa`.`status` = 3
									ORDER BY time_out DESC";
                                    $obt_res = $this->fix_m->query($obt_query);
									$obt_data = $obt_res->result();
									if(count($obt_data)) {
										foreach($obt_data as $obt_row) {
											$obt_from = new DateTime($obt_row->full_timein);
											$obt_to = new DateTime($obt_row->full_timeout);
											if($obt_to->format("U") == $shift_out->format("U")) {
												$shift_out = $obt_from;
											}
											else
												break;
										}

										if($curLog->format("U") < $shift_out->format("U")) {
											$undertime = ($shift_out->format("U")-$curLog->format("U"))/60;
										}
										else {
                                            $undertime = 0;
                                        }
                                    }
                                }

                                $insertQuery = "UPDATE tk_attendance
								  SET
								   actual_out = '" . $timeStr . "',
								   actual_out_sec = '" . $bio->sec_log . "',
								   undertime = '" . $undertime . "',
								   overtime = '" . $excess . "',
								   reg_hrs = '" . $regHrs . "',
								   nsd = '" . $nsd . "'
								  WHERE
									mb_no = '" . $cur_row->mb_no . "' AND
									att_date = '" . $shift_in->format("Y-m-d") . "'";
                                $attendance_res = $this->fix_m->query($insertQuery);
                            }
                        }
						else if ($bio->in_out_mode == 1) {
                            $shift_in_tmp = new DateTime($curDate->format("Y-m-d") . " " . $cur_row->shift_hr_from . ":" . $cur_row->shift_min_from . ":00");
                            $shift_in_tmp->modify("-3 hours");
                            if ($curLog < $shift_in_tmp) {
                                $timekeeping_query = "SELECT * 
								FROM tk_attendance ta
								INNER JOIN g4_member gm ON ta.mb_no = gm.mb_no
							  WHERE 
								`gm`.`enroll_number` 	    = '" . $bio->enroll_number . "' AND 
								`ta`.`att_date` 	= '" . $prevDate->format("Y-m-d") . "'";
                                $attendance_res = $this->fix_m->query($timekeeping_query);
                                $attendance_data = $attendance_res->row();

                                if (count($attendance_data) == 0) {
                                    $undertime = 0;
									if($has_previous && $prev_row->shift_id > 0) {
										$shift_in 	= new DateTime($prevDate->format("Y-m-d")." ".$prev_row->shift_hr_from.":".$prev_row->shift_min_from.":00");
										$shift_out 	= new DateTime($prevDate->format("Y-m-d")." ".$prev_row->shift_hr_to.":".$prev_row->shift_min_to.":00");

										$undertime = ($shift_out->format("U")-$curLog->format("U"))/60;
										if($undertime < 0) $undertime = 0;
                                    }


                                    $insertQuery = "INSERT INTO tk_attendance
											(mb_no,att_date,actual_out,actual_out_sec, undertime)
										  VALUES
											('" . $cur_row->mb_no . "',
											'" . $prevDate->format("Y-m-d") . "',
											'" . $timeStr . "',
											'" . $bio->sec_log . "',
											'" . $undertime . "');";
                                    $attendance_res = $this->fix_m->query($insertQuery);
                                }
                                else {
                                    $undertime = 0;
                                    $excess = 0;
                                    $regHrs = 0;
                                    $nsd = 0;
                                    if ($attendance_data->actual_in != "") {
                                        if ($has_previous && $prev_row->shift_id > 0) {
                                            $nsdDateIn = new DateTime($shift_in->format("Y-m-d 22:00:00"));
                                            $nsdDateOut = new DateTime($shift_in->format("Y-m-d 22:00:00"));
                                            // Custom by HR
                                            if ($shift_out->format("Hi") == "0700")
                                                $nsdDateOut->modify("+9 hour");
                                            else
                                                $nsdDateOut->modify("+8 hour");

                                            $tmpDateIn = new DateTime($attendance_data->att_date . " " . $attendance_data->actual_in . ":00");
                                            if ($tmpDateIn > $curLog)
                                                $tmpDateIn->modify("-1 day");

                                            if ($tmpDateIn < $shift_in) {
                                                $excessIn = ($shift_in->format("U") - $tmpDateIn->format("U"));
                                                $excess += floor($excessIn / 3600);
                                            }

                                            $countedIn = new DateTime($shift_in->format("Y-m-d H:i:s"));

                                            if ($curLog > $shift_out) {
                                                $excessOut = ($curLog->format("U") - $shift_out->format("U"));
                                                $excess += floor($excessOut / 3600);
                                            }

                                            $countedOut = new DateTime($shift_out->format("Y-m-d H:i:s"));

                                            $regHrsHr = ($countedOut->format("U") - $countedIn->format("U")) / 3600;
                                            $regHrs = floor($regHrsHr) + ((($regHrsHr - floor($regHrsHr)) * 60) / 100);

                                            if ($shift_in < $nsdDateIn)
                                                $countedIn = new DateTime($nsdDateIn->format("Y-m-d H:i:s"));
                                            else
                                                $countedIn = new DateTime($shift_in->format("Y-m-d H:i:s"));

                                            if ($shift_out > $nsdDateOut)
                                                $countedOut = new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
                                            else
                                                $countedOut = new DateTime($shift_out->format("Y-m-d H:i:s"));

                                            $nsd = floor(($countedOut->format("U") - $countedIn->format("U")) / 3600);
											if($nsd < 0) $nsd = 0;

                                            $undertime = ($shift_out->format("U") - $curLog->format("U")) / 60;
											if($undertime < 0) $undertime = 0;
                                            if ($curLog->format("U") < $shift_out->format("U")) {
                                                $obt_query = "SELECT 
										  *,
										  CONCAT(`date`,' ',TIME(time_in)) full_timein,
										  IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
										FROM tk_obt_application `toa`
										WHERE
										  `toa`.`mb_no` = '" . $cur_row->mb_no . "' AND 
										  `toa`.`date` 	= '" . $shift_in->format("Y-m-d") . "' AND
										  TIME('" . $timeStr . "') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
										  AND `toa`.`status` = 3
										ORDER BY time_out DESC";
                                                $obt_res = $this->fix_m->query($obt_query);

												$obt_data = $obt_res->result();
                                                if (count($obt_data)) {
													foreach($obt_data as $obt_row) {
														$obt_from = new DateTime($obt_row->full_timein);
														$obt_to = new DateTime($obt_row->full_timeout);
														if($obt_to->format("U") == $shift_out->format("U")) {
															$shift_out = $obt_from;
														}
														else
															break;
													}
								
													if($curLog->format("U") < $shift_out->format("U")) {
														$undertime = ($shift_out->format("U")-$curLog->format("U"))/60;
													}
													else {
                                                        $undertime = 0;
                                                    }
                                                }
                                            }
										}
                                        else {
                                            if ($attendance_data->actual_in != "") {
                                                $tmpDateIn = new DateTime($attendance_data->att_date . " " . $attendance_data->actual_in . ":00");

                                                $nsdDateIn = new DateTime($tmpDateIn->format("Y-m-d 22:00:00"));
                                                $nsdDateOut = new DateTime($tmpDateIn->format("Y-m-d 22:00:00"));
                                                $nsdDateOut->modify("+8 hour");

                                                $regHrsHr = ($curLog->format("U") - $tmpDateIn->format("U")) / 3600;
                                                $regHrs = floor($regHrsHr) + ((($regHrsHr - floor($regHrsHr)) * 60) / 100);
                                                $excess = floor($regHrs);

                                                if ($tmpDateIn < $nsdDateIn)
                                                    $countedIn = new DateTime($nsdDateIn->format("Y-m-d H:00:00"));
                                                else
                                                    $countedIn = new DateTime($tmpDateIn->format("Y-m-d H:00:00"));

                                                if ($curLog > $nsdDateOut)
                                                    $countedOut = new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
                                                else {
                                                    if ($curLog->format("i") != "00")
                                                        $curLog->modify("+1 hour");
                                                    $countedOut = new DateTime($curLog->format("Y-m-d H:00:00"));
                                                    if ($curLog->format("i") != "00")
                                                        $curLog->modify("-1 hour");
                                                }

                                                $nsd = floor(($countedOut->format("U") - $countedIn->format("U")) / 3600);
												if($nsd < 0) $nsd = 0;
							
                                            }
                                        }
                                    }

                                    $insertQuery = "UPDATE tk_attendance
								  SET
								   actual_out = '" . $timeStr . "',
								   actual_out_sec = '" . $bio->sec_log . "',
								   undertime = '" . $undertime . "',
								   overtime = '" . $excess . "',
								   reg_hrs = '" . $regHrs . "',
								   nsd = '" . $nsd . "'
								  WHERE
									mb_no = '" . $cur_row->mb_no . "' AND
									att_date = '" . $prevDate->format("Y-m-d") . "'";
                                    $attendance_res = $this->fix_m->query($insertQuery);
                                }
                                $valid = true;
                            }
                        }
                    }
                }

                if (!$valid) {
                    $timekeeping_query = "SELECT * 
								FROM tk_attendance ta
								INNER JOIN g4_member gm ON ta.mb_no = gm.mb_no
							  WHERE 
								`gm`.`enroll_number` 	    = '" . $bio->enroll_number . "' AND 
								`ta`.`att_date` 	= '" . $curLog->format("Y-m-d") . "'";
                    $attendance_res = $this->fix_m->query($timekeeping_query);
                    $attendance_data = $attendance_res->row();

                    $prev_timekeeping_query = "SELECT * 
								FROM tk_attendance ta
								INNER JOIN g4_member gm ON ta.mb_no = gm.mb_no
							  WHERE 
								`gm`.`enroll_number` 	    = '" . $bio->enroll_number . "' AND 
								`ta`.`att_date` 	= '" . $prevDate->format("Y-m-d") . "'";
                    $prev_attendance_res = $this->fix_m->query($prev_timekeeping_query);
                    $prev_attendance_data = $prev_attendance_res->row();

                    $emp_query = "SELECT * 
								FROM g4_member gm 
							  WHERE 
								`gm`.`enroll_number` 	    = '" . $bio->enroll_number . "'";
                    $emp_res = $this->fix_m->query($emp_query);
                    $emp_data = $emp_res->result();

                    if ($bio->in_out_mode == 0) {
                        if (count($attendance_data) == 0) {
                            $insertQuery = "INSERT INTO tk_attendance
										(mb_no,att_date,actual_in,actual_in_sec)
									  VALUES
										('" . (count($emp_data) ? $emp_data[0]->mb_no : 0) . "',
										'" . $curLog->format("Y-m-d") . "',
										'" . $timeStr . "',
										'" . $bio->sec_log . "');";
                            $attendance_res = $this->fix_m->query($insertQuery);
						}
						else if(empty($attendance_data->actual_in)) {
                            $insertQuery = "UPDATE tk_attendance
							  SET
							   actual_out = NULL,
							   actual_out_sec = '0',
							   undertime = '0',
							   overtime = '0',
							   reg_hrs = '0',
							   nsd = '0',
							   actual_in = '" . $timeStr . "',
							   actual_in_sec = '" . $bio->sec_log . "',
							   tardy = '0'
							  WHERE
								mb_no = '" . (count($emp_data) ? $emp_data[0]->mb_no : 0) . "' AND
								att_date = '" . $curLog->format("Y-m-d") . "'";
                            $attendance_res = $this->fix_m->query($insertQuery);
                        }
					}
					else {
                        if (count($prev_attendance_data) && !count($attendance_data)) {
                            $regHrs = $nsd = $excess = 0;
                            if ($prev_attendance_data->actual_in != "") {
                                $tmpDateIn = new DateTime($prev_attendance_data->att_date . " " . $prev_attendance_data->actual_in . ":00");
                                $nsdDateIn = new DateTime($tmpDateIn->format("Y-m-d 22:00:00"));
                                $nsdDateOut = new DateTime($tmpDateIn->format("Y-m-d 22:00:00"));
                                $nsdDateOut->modify("+8 hour");

								if($has_previous && $prev_row->shift_id > 0) {
									$shift_in 	= new DateTime($prevDate->format("Y-m-d")." ".$prev_row->shift_hr_from.":".$prev_row->shift_min_from.":00");
									$shift_out 	= new DateTime($prevDate->format("Y-m-d")." ".$prev_row->shift_hr_to.":".$prev_row->shift_min_to.":00");

									if($shift_out < $shift_in) {
                                        $shift_out->modify("+1 day");
                                    }

                                    // Custom by HR
                                    if ($shift_out->format("Hi") == "0700")
                                        $nsdDateOut->modify("+9 hour");
                                    else
                                        $nsdDateOut->modify("+8 hour");

                                    $regHrsHr = ($shift_out->format("U") - $shift_in->format("U")) / 3600;
                                    $regHrs = floor($regHrsHr) + ((($regHrsHr - floor($regHrsHr)) * 60) / 100);

                                    if ($curLog > $shift_out) {
                                        $excessOut = ($curLog->format("U") - $shift_out->format("U"));
                                        $excess += floor($excessOut / 3600);
                                    }

                                    if ($shift_in < $nsdDateIn)
                                        $countedIn = new DateTime($nsdDateIn->format("Y-m-d H:i:s"));
                                    else {
                                        if ($shift_in->format("i") != "00") {
                                            $countedIn = new DateTime($shift_in->format("Y-m-d :00:00"));
										}
										else
                                            $countedIn = new DateTime($shift_in->format("Y-m-d H:00:00"));
                                    }

                                    if ($shift_out > $nsdDateOut)
                                        $countedOut = new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
                                    else
                                        $countedOut = new DateTime($shift_out->format("Y-m-d H:i:s"));
                                }
                                else {
                                    $regHrsHr = ($curLog->format("U") - $tmpDateIn->format("U")) / 3600;
                                    $regHrs = floor($regHrsHr) + ((($regHrsHr - floor($regHrsHr)) * 60) / 100);
                                    $excess = floor($regHrs);

                                    if ($tmpDateIn < $nsdDateIn)
                                        $countedIn = new DateTime($nsdDateIn->format("Y-m-d H:00:00"));
                                    else
                                        $countedIn = new DateTime($tmpDateIn->format("Y-m-d H:00:00"));

                                    if ($curLog > $nsdDateOut)
                                        $countedOut = new DateTime($nsdDateOut->format("Y-m-d H:00:00"));
                                    else {
                                        if ($curLog->format("i") != "00")
                                            $curLog->modify("+1 hour");
                                        $countedOut = new DateTime($curLog->format("Y-m-d H:00:00"));
                                        if ($curLog->format("i") != "00")
                                            $curLog->modify("-1 hour");
                                    }
                                }

                                $nsd = floor(($countedOut->format("U") - $countedIn->format("U")) / 3600);
								if($nsd < 0) $nsd = 0;
					
                            }
                            $insertQuery = "UPDATE tk_attendance
								  SET
								   actual_out 		= '" . $timeStr . "',
								   actual_out_sec 	= '" . $bio->sec_log . "',
								   overtime 		= '" . $excess . "',
								   reg_hrs 			= '" . $regHrs . "',
								   nsd 				= '" . $nsd . "'
								  WHERE
									mb_no = '" . (count($emp_data) ? $emp_data[0]->mb_no : 0) . "' AND
									att_date = '" . $prevDate->format("Y-m-d") . "'";
                            $attendance_res = $this->fix_m->query($insertQuery);
                            $valid = true;
                        }
                        if (!$valid) {
                            $excess = 0;
                            $nsd = 0;
                            $regHrs = 0;
                            if (count($attendance_data) == 0) {
                                $insertQuery = "INSERT INTO tk_attendance
												(mb_no,att_date,actual_out,actual_out_sec)
											  VALUES
												('" . (count($emp_data) ? $emp_data[0]->mb_no : 0) . "',
												'" . $curLog->format("Y-m-d") . "',
												'" . $timeStr . "',
												'" . $bio->sec_log . "');";
                                $attendance_res = $this->fix_m->query($insertQuery);
							}
							else {
                                if ($attendance_data->actual_in != "") {
                                    $tmpDateIn = new DateTime($attendance_data->att_date . " " . $attendance_data->actual_in . ":00");

                                    $nsdDateIn = new DateTime($tmpDateIn->format("Y-m-d 22:00:00"));
                                    $nsdDateOut = new DateTime($tmpDateIn->format("Y-m-d 22:00:00"));
                                    $nsdDateOut->modify("+8 hour");

                                    $regHrsHr = ($curLog->format("U") - $tmpDateIn->format("U")) / 3600;
                                    $regHrs = floor($regHrsHr) + ((($regHrsHr - floor($regHrsHr)) * 60) / 100);
                                    $excess = floor($regHrs);

                                    if ($tmpDateIn < $nsdDateIn)
                                        $countedIn = new DateTime($nsdDateIn->format("Y-m-d H:00:00"));
                                    else
                                        $countedIn = new DateTime($tmpDateIn->format("Y-m-d H:00:00"));

                                    if ($curLog > $nsdDateOut)
                                        $countedOut = new DateTime($nsdDateOut->format("Y-m-d H:00:00"));
                                    else {
                                        if ($curLog->format("i") != "00")
                                            $curLog->modify("+1 hour");
                                        $countedOut = new DateTime($curLog->format("Y-m-d H:00:00"));
                                        if ($curLog->format("i") != "00")
                                            $curLog->modify("-1 hour");
                                    }
                                    $nsd = floor(($countedOut->format("U") - $countedIn->format("U")) / 3600);
									if($nsd < 0) $nsd = 0;
						
                                }
                                $insertQuery = "UPDATE tk_attendance
									  SET
									   actual_out 		= '" . $timeStr . "',
									   actual_out_sec 	= '" . $bio->sec_log . "',
									   overtime 		= '" . $excess . "',
									   reg_hrs 			= '" . $regHrs . "',
									   nsd 				= '" . $nsd . "'
									  WHERE
										mb_no = '" . (count($emp_data) ? $emp_data[0]->mb_no : 0) . "' AND
										att_date = '" . $curLog->format("Y-m-d") . "'";
                                $attendance_res = $this->fix_m->query($insertQuery);
                            }
                        }
                    }
                }
            }
        }
    }

    public function updateAttendance($mb_no, $date_str_from, $date_str_to) {
        $this->load->model('fix_model', 'fix_m');
        /* Update Attendance */
        $timekeeping_query = "SELECT *
						  FROM g4_member gm 
						WHERE 
						  `gm`.`mb_no` 	= '" . $mb_no . "'";
        $prev_schedule_data = $this->fix_m->query($timekeeping_query);
        $prev_row = $prev_schedule_data->row();
        if (isset($prev_row->mb_no) && !empty($prev_row->mb_no)) {
            $this->fix_m->clearAttendance($date_str_from, $date_str_to, $prev_row->mb_no);
            $biometrics = $this->fix_m->getBio($date_str_from, $date_str_to, $prev_row->enroll_number);
            $this->calculateToAttendance($biometrics);
        }
        /* End Update Attendance */
    }

}

/* End of file My_Controller.php */
/* Location: ./application/core/My_Controller.php */