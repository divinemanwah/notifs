<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Timekeeping extends MY_Controller {

    private $month_list;

    function __construct() {
        parent::__construct();
        $this->load->model('employees_model', 'employees_m');
        $this->load->model('shifts_model', 'shifts_m');
        $this->load->model('leaves_model', 'leaves_m');
        $this->load->model('fix_model', 'fix_m');

        $this->load->library('ws', array('tag' => getenv('HTTP_HOST') == '10.120.10.139' ? 'hris' : 'hristest'));

        $this->month_list = array(
            "jan" => "01",
            "feb" => "02",
            "mar" => "03",
            "apr" => "04",
            "may" => "05",
            "jun" => "06",
            "jul" => "07",
            "aug" => "08",
            "sep" => "09",
            "oct" => "10",
            "nov" => "11",
            "dec" => "12"
        );
    }

    /* Views */

    public function index() {
        redirect("/timekeeping/view_schedules");
    }

    public function summary() {
        $this->view_template('timekeeping/timekeep_summary', 'Timekeeping', array(
            'breadcrumbs' => array('Schedules Summary'),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'timekeeping.js'
            ),
            'depts' => $this->employees_m->getDepts()
        ));
    }

    public function settings() {
        $emp_list = $this->employees_m->getAll(false, "*", false, 0, 0, array("mb_lname" => "ASC"));

        $this->view_template('timekeeping/timekeep_settings', 'Timekeeping', array(
            'breadcrumbs' => array('Manage', 'General Settings'),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'date-time/bootstrap-timepicker.min.js',
                'timekeeping.settings.js'
            ),
            'css' => array(
                'bootstrap-timepicker.css'
            ),
            'depts' => $this->employees_m->getDepts(),
            'data' => $this->shifts_m->getGeneralSettings(),
            'emp_list' => $emp_list
        ));
    }

    public function approval_settings() {
        $this->view_template('timekeeping/approval_settings', 'Timekeeping', array(
            'breadcrumbs' => array('Manage', 'Approval Settings'),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'timekeeping.approval_settings.js'
            )
        ));
    }

    public function holidays() {

        $h_settings = $this->employees_m->holidaysettings();
        $this->view_template('timekeeping/holiday_settings', 'Timekeeping', array(
            'breadcrumbs' => array('Holiday', 'Setup'),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'date-time/bootstrap-datepicker.min.js',
                'holiday.settings.js'
            ),
            'css' => array(
                'bootstrap-timepicker.css'
            ),
            'h_settings' => (Array) json_decode($h_settings->COLUMN_COMMENT)
        ));
    }

    public function getholidaydata() {
        $post = $this->input->post();
        $select = "h_name,
                        REPLACE(h_date,'0000',YEAR(now())) h_date,
                        h_status,
                        if(YEAR(h_date) = 0000,'Fixed','Not Fixed') h_type,id,
                        (select mb_fname from g4_member where mb_no = created_by) created_by
                        ";
        $hdays = $this->employees_m->getholidays($select)
                ->where("(year(h_date) >= " . date("Y", strtotime('now')) . " or year(h_date) = 0000)");

        if (isset($post['id']))
            $hdays = $hdays->where(Array('id' => $post['id']));

        $hdays = $hdays
                ->get()
                ->result();
        $history = $this->employees_m;
        if ($post['getform']) {
            $historysel = "h_created,h_name,h_date,(select mb_fname from g4_member where mb_no = created_by) created_by";
            $history = $history->getholidayhistory($historysel)
                    ->where(Array('id' => $post['id']))
                    ->order_by('h_created', 'desc');
            $history = (array) $history->get()->result();
        }
        echo json_encode(array("data" => $hdays, "history" => $history));
    }

    public function holidaymem($record, $mbno, $hstatus = "") {
        $hdays = $this->employees_m->holiday_validation($record, $mbno, $hstatus);
        if ($record !== 'insert') {
            echo json_encode(array("data" => $hdays, "success" => 1, "msg" => "Holiday has been updated"));
        } else {
            if ($hdays) {
                echo json_encode(array("success" => 1, "msg" => "Holiday has been updated"));
            } else {
                echo json_encode(array("success" => 0, "msg" => "Record not updated"));
            }
        }
    }

    public function updateholiday() {
        $post = $this->input->post();
        $dateval = strtotime($post['hdate']);
        $hdays = $this->employees_m->getholidays()
                ->where(Array('id' => $post['hid']));
        $hdays = $hdays->get()->result();
        if (count($hdays) == 0) {
            echo json_encode(array("success" => 0, "msg" => "Record not Found"));
        } else {
            $h_date = $post['hdate'];
            if ($post['hstatus'] !== "0")
                $h_date = str_replace(date("Y", $dateval), '0000', $post['hdate']);

            $this->employees_m->updateholidays(Array(
                'h_name' => $post['hname'],
                'h_status' => $post['htype'],
                'h_date' => $h_date,
                'created_by' => $this->session->userdata('mb_no')
                    ), Array('id' => $post['hid']));

            if ($h_date !== $hdays[0]->h_date)
                $this->changeshiftcodetoph('update', date('Y-m-d', $dateval), $hdays[0]->h_date);

            echo json_encode(array("success" => 1, "msg" => "Holiday has been updated"));
        }
    }

    public function h_insert() {
        $post = $this->input->post();
        $dateval = strtotime($post['holidaydate']);
        if (date("Y", strtotime("now")) > date("Y", $dateval)) {
            echo json_encode(array("success" => 0, "msg" => "Please update your Date"));
            return false;
        }

        $hdays = $this->employees_m->getholidays()
                ->where(Array("REPLACE(h_date,'0000',YEAR(now())) >= " => date("Y-m-d", $dateval)))
                ->where(Array('h_name' => $post['holidayname']));

        if (count($hdays->get()->result()) > 0) {
            echo json_encode(array("success" => 0, "msg" => "Holiday is already exist"));
        } else {
            $h_date = "";
            if ($post['holidaystatus'] == "0") {
                $h_date = $post['holidaydate'];
            } else {
                $h_date = str_replace(date("Y", $dateval), '0000', $post['holidaydate']);
            }

            $this->employees_m->insertholidays(Array(
                'h_name' => $post['holidayname'],
                'h_status' => $post['holidaytype'],
                'h_date' => $h_date,
                'created_by' => $this->session->userdata('mb_no')
            ));



            $this->changeshiftcodetoph('insert', date('Y-m-d', $dateval));

            echo json_encode(array("success" => 1, "msg" => "Holiday has been added"));
        }
    }

    public function changeshiftcodetoph($info, $newdate = "", $olddate = "") {

        $schedSel = 'gm.mb_no,gm.mb_id,gm.mb_nick,gm.mb_name,
                         tms.tkms_id,tms.mb_no,tms.year,tms.month,tms.day,tms.shift_id,tms.leave_id,tms.lv_app_id,
                         hhmr.mb_no,hhmr.h_status,
                         tla.date_from,tla.date_to,tla.reason,tla.status,tla.allocated,tla.used,
                         tlb.lv_bal_id,tlb.bal,tlb.used,tlb.allocated,
                         tcsr.cs_req_id,tcsr.att_date_from,tcsr.att_date_to,tcsr.reason,tcsr.status';

        $schedWhere = "date_format(concat(tms.year,'-',tms.month,'-',tms.day),'%Y-%m-%d') = '" . $newdate . "'
                        and gm.mb_status = 1
                        and hhmr.h_status = 1 ";

        $withoutleave = " and tms.shift_id > 0
                              and tla.status is null
                              and tcsr.status is null";
        $withleave = " and tla.status = 3";

        $sched = $this->shifts_m->getphshift('gm.mb_no', $schedWhere . $withoutleave);
        $schedwithlv = $this->shifts_m->getphshift($schedSel, $schedWhere . $withleave);
        $mbnowithlv = implode(',', array_map(function ($a) {
                    if ($a->shift_id !== null)
                        return intval($a->mb_no);
                }, $schedwithlv));
        $noshiftlv = implode(',', array_map(function ($a) {
                    if ($a->shift_id == null)
                        return intval($a->mb_no);
                }, $schedwithlv));
        if ($newdate >= date('Y-m-d', strtotime('now'))) {
            if (count($sched) > 0) {
                $mbno = implode(',', array_map(function ($a) {
                            return intval($a->mb_no);
                        }, $sched));
                $updatesched = $this->shifts_m->updateMemberSchedule(Array('shift_id' => -2), "date_format(concat(year,'-',month,'-',day),'%Y-%m-%d') = '" . $newdate . "' and mb_no in(" . $mbno . ")");
            }
            if ($info == 'update') {
                /*
                  $hdays = $this->employees_m->getholidays("*")
                  ->where("REPLACE(h_date,'0000',YEAR(NOW())) = '".str_replace('0000',date('Y',strtotime('now')),$olddate)."'");
                  $hdays = $hdays->get()->result();
                 * 
                 */

                $oldleavedata = $this->shifts_m->checkoldmemberschedule("tmshh.*,tlb.lv_bal_id", "date_format(concat(tms.year,'-',tms.month,'-',tms.day),'%Y-%m-%d') = '" . str_replace('0000', date('Y', strtotime('now')), $olddate) . "' and tmshh.leave_id > 0");
                if (count($oldleavedata) > 0) {
                    $lvbalid = implode(',', array_map(function ($a) {
                                if ($a->shift_id !== null)
                                    return intval($a->lv_bal_id);
                            }, $oldleavedata));
                    $lvappid = implode(',', array_map(function ($a) {
                                if ($a->shift_id !== null)
                                    return intval($a->lv_app_id);
                            }, $oldleavedata));
                    $lvbalidns = implode(',', array_map(function ($a) {
                                if ($a->shift_id == null)
                                    return intval($a->lv_bal_id);
                            }, $oldleavedata));
                    $lvappidns = implode(',', array_map(function ($a) {
                                if ($a->shift_id == null)
                                    return intval($a->lv_app_id);
                            }, $oldleavedata));
                    if (strlen(str_replace(',', '', $lvappid)) > 0) {
                        $appwhere = "lv_app_id in(" . $lvappid . ")";
                        $balwhere = "lv_bal_id in(" . $lvbalid . ")";
                        $used = Array("used" => "used+1");
                        $bal = Array("bal" => "bal-1");
                        $leaveapp = $this->leaves_m->setupdateLeaveApplication($used, $appwhere);
                        $leavebal = $this->leaves_m->setupdateEmpLeaveBalances(array_merge($used, $bal), $balwhere);
                    }
                    if (strlen(str_replace(',', '', $lvappidns)) > 0) {
                        $appwherens = "lv_app_id in(" . $lvappidns . ")";
                        $balwherens = "lv_bal_id in(" . $lvbalidns . ")";
                        $usedns = Array("allocated" => "allocated+1");
                        $balns = Array("bal" => "bal-1");
                        $leaveappns = $this->leaves_m->setupdateLeaveApplication($usedns, $appwherens);
                        $leavebalns = $this->leaves_m->setupdateEmpLeaveBalances(array_merge($usedns, $balns), $balwherens);
                    }
                }

                $rollbck = $this->shifts_m->rollbackmemberschedule(" date_format(concat(tms.year,'-',tms.month,'-',tms.day),'%Y-%m-%d') = '" . str_replace('0000', date('Y', strtotime('now')), $olddate) . "'");
                $deletehistory = $this->shifts_m->deleteoldhistorysched(" date_format(concat(year,'-',month,'-',day),'%Y-%m-%d') = '" . str_replace('0000', date('Y', strtotime('now')), $olddate) . "'");
            }

            // leave
            if (count($schedwithlv) > 0) {
                $lvbalid = implode(',', array_map(function ($a) {
                            if ($a->shift_id !== null)
                                return intval($a->lv_bal_id);
                        }, $schedwithlv));
                $lvappid = implode(',', array_map(function ($a) {
                            if ($a->shift_id !== null)
                                return intval($a->lv_app_id);
                        }, $schedwithlv));
                $lvbalidns = implode(',', array_map(function ($a) {
                            if ($a->shift_id == null)
                                return intval($a->lv_bal_id);
                        }, $schedwithlv));
                $lvappidns = implode(',', array_map(function ($a) {
                            if ($a->shift_id == null)
                                return intval($a->lv_app_id);
                        }, $schedwithlv));
                if (strlen(str_replace(',', '', $lvappid)) > 0) {
                    $appwhere = "lv_app_id in(" . $lvappid . ")";
                    $balwhere = "lv_bal_id in(" . $lvbalid . ")";
                    $used = Array("used" => "used-1");
                    $bal = Array("bal" => "bal+1");
                    $leaveapp = $this->leaves_m->setupdateLeaveApplication($used, $appwhere);
                    $leavebal = $this->leaves_m->setupdateEmpLeaveBalances(array_merge($used, $bal), $balwhere);
                }
                if (strlen(str_replace(',', '', $lvappidns)) > 0) {
                    $appwherens = "lv_app_id in(" . $lvappidns . ")";
                    $balwherens = "lv_bal_id in(" . $lvbalidns . ")";
                    $usedns = Array("allocated" => "allocated-1");
                    $balns = Array("bal" => "bal+1");
                    $leaveappns = $this->leaves_m->setupdateLeaveApplication($usedns, $appwherens);
                    $leavebalns = $this->leaves_m->setupdateEmpLeaveBalances(array_merge($usedns, $balns), $balwherens);
                }
                if (strlen($mbnowithlv) > 0)
                    $lvupdatesched = $this->shifts_m->updateMemberSchedule(Array('shift_id' => -2), "date_format(concat(year,'-',month,'-',day),'%Y-%m-%d') = '" . $newdate . "' and mb_no in(" . $mbnowithlv . ")");
                if (strlen($noshiftlv) > 0)
                    $noshiftupdate = $this->shifts_m->noshiftlvhistory("date_format(concat(year,'-',month,'-',day),'%Y-%m-%d') = '" . $newdate . "' and mb_no in(" . $noshiftlv . ")");
            }
        }
    }

    public function upload_sched() {
        $settings = $this->shifts_m->getGeneralSettings();

        $apprv_group = $this->shifts_m->getUploaderGroup($this->session->userdata("mb_no"), "*");

        $default_sched_day = $settings[0]->default_sched_day;
        $default_period = $settings[0]->default_period;

        $date = new DateTime();

        // $date->modify("+1 month");
        $tmp_date = new DateTime($date->format("Y-m-" . $default_period . " 00:00:00"));
        if ($date->format("d") > $default_sched_day) {
            $tmp_date->modify("+1 month");
        }

        $period_dtl = (object) array("deadline" => $default_sched_day);
        $period_dtl->start = $tmp_date->format("Y-m-d");
        if ($default_period == 1) {
            $period_dtl->end = $tmp_date->format("Y-m-t");
        } else {
            $tmp_date->modify("+1 month")->modify("-1 day");
            $period_dtl->end = $tmp_date->format("Y-m-d");
        }

        $this->view_template('timekeeping/upload_schedule', 'Timekeeping', array(
            'breadcrumbs' => array('Upload Schedule'),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'jquery.form.js',
                'timekeeping.upload_schedule.js'
            ),
            'cur_period' => $period_dtl,
            'apprv_group' => $apprv_group
        ));
    }

    public function manage_approval() {
        $settings = $this->shifts_m->getGeneralSettings();

        $default_sched_day = $settings[0]->default_sched_day;
        $default_period = $settings[0]->default_period;

        $date = new DateTime();
        $tmp_date = new DateTime($date->format("Y-m-" . $default_period . " 00:00:00"));
        if ($date->format("d") > $default_sched_day) {
            $tmp_date->modify("+1 month");
        }

        $period_dtl = (object) array("deadline" => $default_sched_day);
        $period_dtl->start = $tmp_date->format("Y-m-d");
        if ($default_period == 1) {
            $period_dtl->end = $tmp_date->format("Y-m-t");
        } else {
            $tmp_date->modify("+1 month")->modify("-1 day");
            $period_dtl->end = $tmp_date->format("Y-m-d");
        }

        $shifts_dtl = $this->shifts_m->getAll(false, "*, CONCAT(LPAD(shift_hr_from,2,'0'),':',LPAD(shift_min_from,2,'0'),'-',LPAD(shift_hr_to,2,'0'),':',LPAD(shift_min_to,2,'0')) shift_sched, shift_color");

        $shifts_dtl[] = (object) array("shift_id" => 0, "shift_code" => "RD", "shift_sched" => "Rest Day", "shift_color" => "1B7935");
        $shifts_dtl[] = (object) array("shift_id" => -1, "shift_code" => "SS", "shift_sched" => "Suspension", "shift_color" => "FA4747");
        $shifts_dtl[] = (object) array("shift_id" => -2, "shift_code" => "PH", "shift_sched" => "Holiday", "shift_color" => "D87947");
        $shifts_list = array();
        foreach ($shifts_dtl as $shift) {
            $shifts_list[$shift->shift_id] = (object) array();
            $shifts_list[$shift->shift_id]->scode = $shift->shift_code;
            $shifts_list[$shift->shift_id]->stime = $shift->shift_sched;
        }

        $this->view_template('timekeeping/manage_approval', 'Timekeeping', array(
            'breadcrumbs' => array('Schedules for Approval'),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'timekeeping.manage_approval.js'
            ),
            'cur_period' => $period_dtl,
            'shifts' => $shifts_list
        ));
    }

    public function view_schedules() {
        $settings = $this->shifts_m->getGeneralSettings();
        $allow_search = false;
        $default_sched_day = $settings[0]->default_sched_day;
        $default_period = $settings[0]->default_period;

        $date = new DateTime();
        $tmp_date = new DateTime($date->format("Y-m-" . $default_period . " 00:00:00"));
        if ($date < $tmp_date) {
            $tmp_date->modify("-1 month");
        }

        $period_dtl = (object) array("deadline" => $default_sched_day);
        $period_dtl->start = $tmp_date->format("Y-m-d");
        if ($default_period == 1) {
            $period_dtl->end = $tmp_date->format("Y-m-t");
        } else {
            $tmp_date->modify("+1 month")->modify("-1 day");
            $period_dtl->end = $tmp_date->format("Y-m-d");
        }

        if (in_array($this->session->userdata("mb_deptno"), array(24)) || in_array($this->session->userdata("mb_no"), array(114, 229, 46))) {
            $allow_search = true;
        }

        if ($allow_search) {
            $emp_list = $this->employees_m->getAll(false, "*", false, 0, 0, array("mb_lname" => "ASC"));
        } else {
            $emp_list = $this->employees_m->getAll(false, "*", $this->session->userdata("mb_deptno"), 0, 0, array("mb_lname" => "ASC"));
        }

        $this->view_template('timekeeping/view_schedules', 'Timekeeping', array(
            'breadcrumbs' => array('View Schedules'),
            'js' => array(
                'jquery.handsontable.full.min.js',
                'date-time/bootstrap-datepicker.min.js',
                'timekeeping.view_schedules.js'
            ),
            'cur_period' => $period_dtl,
            'depts' => $this->employees_m->getDepts(),
            'allow_search' => $allow_search,
            'emp_list' => $emp_list,
            'emp_dept' => $this->session->userdata("mb_deptno")
        ));
    }

    public function change_schedule() {
        $settings = $this->shifts_m->getGeneralSettings();
        $allow_search = false;
        $default_sched_day = $settings[0]->default_sched_day;
        $default_period = $settings[0]->default_period;

        $date = new DateTime();
        $tmp_date = new DateTime($date->format("Y-m-" . $default_period . " 00:00:00"));
        if ($date < $tmp_date) {
            $tmp_date->modify("-1 month");
        }

        $period_dtl = (object) array("deadline" => $default_sched_day);
        $period_dtl->start = $tmp_date->format("Y-m-d");
        if ($default_period == 1) {
            $period_dtl->end = $tmp_date->format("Y-m-t");
        } else {
            $tmp_date->modify("+1 month")->modify("-1 day");
            $period_dtl->end = $tmp_date->format("Y-m-d");
        }

        if (in_array($this->session->userdata("mb_deptno"), array(24)) || in_array($this->session->userdata("mb_no"), array(114, 229))) {
            $allow_search = true;
        }

        $shifts_dtl = $this->shifts_m->getAll(false, "*, CONCAT(LPAD(shift_hr_from,2,'0'),':',LPAD(shift_min_from,2,'0'),'-',LPAD(shift_hr_to,2,'0'),':',LPAD(shift_min_to,2,'0')) shift_sched, shift_color", "(FIND_IN_SET('" . $this->session->userdata("mb_deptno") . "', cws_depts) OR FIND_IN_SET('" . $this->session->userdata("mb_no") . "', sched_users))");
        $shifts_dtl[] = (object) array("shift_id" => 0, "shift_code" => "RD", "shift_sched" => "Rest Day", "shift_color" => "1B7935");
        $shifts_dtl[] = (object) array("shift_id" => -1, "shift_code" => "SS", "shift_sched" => "Suspension", "shift_color" => "FA4747");
        $shifts_dtl[] = (object) array("shift_id" => -2, "shift_code" => "PH", "shift_sched" => "Holiday", "shift_color" => "D87947");
        $shifts_list = array();
        foreach ($shifts_dtl as $shift) {
            $shifts_list[$shift->shift_id] = (object) array();
            $shifts_list[$shift->shift_id]->scode = $shift->shift_code;
            $shifts_list[$shift->shift_id]->stime = $shift->shift_sched;
        }

        $this->view_template('timekeeping/change_schedule', 'Timekeeping', array(
            'breadcrumbs' => array('Change Schedule'),
            'js' => array(
                'jquery.handsontable.full.min.js',
                'jquery.inputlimiter.1.3.1.min.js',
                'jquery.validate.min.js',
                'date-time/bootstrap-datepicker.min.js',
                'timekeeping.change_schedule.js'
            ),
            'cur_period' => $period_dtl,
            'allow_search' => $allow_search,
            'emp_id' => $this->session->userdata("mb_no"),
            'shifts' => $shifts_list
        ));
    }

    public function special_upload() {
        $settings = $this->shifts_m->getGeneralSettings();

        $default_sched_day = $settings[0]->default_sched_day;
        $default_period = $settings[0]->default_period;

        $date = new DateTime();
        // $date->modify("+1 month");
        $tmp_date = new DateTime($date->format("Y-m-" . $default_period . " 00:00:00"));
        if ($date->format("d") > $default_sched_day) {
            $tmp_date->modify("+1 month");
        }

        $period_dtl = (object) array("deadline" => $default_sched_day);
        $period_dtl->start = $tmp_date->format("Y-m-d");
        if ($default_period == 1) {
            $period_dtl->end = $tmp_date->format("Y-m-t");
        } else {
            $tmp_date->modify("+1 month")->modify("-1 day");
            $period_dtl->end = $tmp_date->format("Y-m-d");
        }

        $this->view_template('timekeeping/special_upload', 'Timekeeping', array(
            'breadcrumbs' => array('Special Upload'),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'jquery.form.js',
                'timekeeping.special_upload.js',
                'date-time/bootstrap-datepicker.min.js'
            ),
            'cur_period' => $period_dtl
        ));
    }

    /* End of Views */

    /* Upload Schedule */

    public function uploadSchedule() {
        $date = new DateTime();
        $post = $this->input->post();
        $files = $_FILES;

        $apprv_grp_id = $post['group-id'];
        if (empty($apprv_grp_id)) {
            echo json_encode(array("success" => 0, "msg" => "<br/>Select group."));
            return;
        }

        $having_str = "tsu.apprv_grp_id = '" . $apprv_grp_id . "' AND tsu.status IN (1,3) AND tsu.period_from = '" . $post['period-start'] . "' AND tsu.period_to = '" . $post['period-end'] . "'";

        $data = $this->shifts_m->getAllUploadsFiltered("*", $having_str);
        $all_upload_count = count($data);
        if ($all_upload_count) {
            echo json_encode(array("success" => 0, "msg" => "<br/>Duplicate schedule."));
            return;
        }

        $config = array();
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xls';
        $config['encrypt_name'] = true;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload("schedule-file")) {
            echo json_encode(array("success" => 0, "msg" => $this->upload->display_errors("", "")));
        } else {
            $file_info = $this->upload->data();
            $file_info['apprv_grp_id'] = $apprv_grp_id;
            $file_info['file_path'] = "uploads/" . $file_info['raw_name'] . $file_info['file_ext'];
            $file_info['org_file'] = $file_info['client_name'];
            $file_info['period_from'] = $post['period-start'];
            $file_info['period_to'] = $post['period-end'];
            $file_info['created_datetime'] = $date->format("Y-m-d H:i:s");
            $file_info['created_by'] = $this->session->userdata("mb_no");
            $file_info['updated_datetime'] = $date->format("Y-m-d H:i:s");
            $file_info['updated_by'] = $this->session->userdata("mb_no");
            unset($file_info['full_path']);
            unset($file_info['raw_name']);
            unset($file_info['orig_name']);
            unset($file_info['client_name']);
            unset($file_info['is_image']);
            unset($file_info['image_width']);
            unset($file_info['image_height']);
            unset($file_info['image_type']);
            unset($file_info['image_size_str']);

            $shifts_dtl = $this->shifts_m->getAll(false, "*, CONCAT(LPAD(shift_hr_from,2,'0'),':',LPAD(shift_min_from,2,'0'),'-',LPAD(shift_hr_to,2,'0'),':',LPAD(shift_min_to,2,'0')) shift_sched, shift_color");

            $shifts_dtl[] = (object) array("shift_id" => 0, "shift_code" => "RD", "shift_sched" => "Rest Day", "shift_color" => "1B7935", "sched_depts" => "all");
            $shifts_dtl[] = (object) array("shift_id" => -1, "shift_code" => "SS", "shift_sched" => "Suspension", "shift_color" => "FA4747", "sched_depts" => "all");
            $shifts_dtl[] = (object) array("shift_id" => -2, "shift_code" => "PH", "shift_sched" => "Holiday", "shift_color" => "D87947", "sched_depts" => "all");
            $shifts_list = array();
            $shifts_dept = array();
            foreach ($shifts_dtl as $shift) {
                $shifts_list[$shift->shift_id] = $shift->shift_code;
                $shifts_dept[$shift->shift_code] = $shift->sched_depts;
                // $shifts_dept[$shift->shift_id] = $shift->sched_depts;
            }

            $reader = $file_info["file_ext"] == ".xlsx" ? "Excel2007" : "Excel5";
            //load our new PHPExcel library
            $this->load->library('excel');
            $objReader = PHPExcel_IOFactory::createReader($reader);
            $objPHPExcel = $objReader->load(dirname(__FILE__) . "/../../" . $file_info['file_path']);
            $objPHPExcel->setActiveSheetIndex();
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnInd = PHPExcel_Cell::columnIndexFromString($highestColumn);

            $period_from = new DateTime($post['period-start'] . " 00:00:00");
            $period_to = new DateTime($post['period-end'] . " 23:59:59");

            $dataArray = array();
            //$mapArray = array();
            $rownumber = 1;
            while ($rownumber <= $highestRow) {
                $row = $objWorksheet->getRowIterator($rownumber)->current();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                // Month Year Checking
                if ($rownumber == 1) {
                    $year = $month = "";
                    foreach ($cellIterator as $col => $cell) {
                        $cellValue = $cell->getValue();

                        if (!empty($year)) {
                            $dataArray[$cell->getColumn()]['month'] = $month;
                            $dataArray[$cell->getColumn()]['year'] = $year;
                        }

                        if (empty($cellValue))
                            continue;
                        else {
                            $cellValueArr = explode(" ", $cellValue);
                            if (count($cellValueArr) != 2) {
                                echo json_encode(array(
                                    "success" => 0,
                                    "msg" => "<br/>Invalid Format. Must be \"Month Year\" Only (ex. \"" . $date->format("F Y") . "\". <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                ));
                                die();
                            }
                            $cellmonth = strtolower($cellValueArr[0]);
                            $cellyear = strtolower($cellValueArr[1]);

                            if (!isset($this->month_list[$cellmonth])) {
                                echo json_encode(array(
                                    "success" => 0,
                                    "msg" => "<br/>Invalid Month specified. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                ));
                                die();
                            }

                            if (!is_numeric($cellyear) || $cellyear < 2014 || $cellyear > (($date->format("Y") * 1) + 1)) {
                                echo json_encode(array(
                                    "success" => 0,
                                    "msg" => "<br/>Invalid Year specified. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                ));
                                die();
                            }

                            if ($cellmonth != $month) {
                                $dataArray[$cell->getColumn()]['month'] = $this->month_list[$cellmonth];
                                $dataArray[$cell->getColumn()]['year'] = $cellyear;
                                $month = $this->month_list[$cellmonth];
                                $year = $cellyear;
                            }
                        }
                    }
                } else // Month Year Checking
                if ($rownumber == 2) {
                    $month_31 = array("01", "03", "05", "07", "08", "10", "12");
                    $month_30 = array("04", "06", "09", "11");
                    foreach ($cellIterator as $col => $cell) {
                        if ($col < 2)
                            continue;
                        $cellValue = $cell->getValue();
                        if (
                                !is_numeric($cellValue) ||
                                $cellValue < 1 ||
                                (in_array($dataArray[$cell->getColumn()]['month'], $month_31) && $cellValue > 31) ||
                                (in_array($dataArray[$cell->getColumn()]['month'], $month_30) && $cellValue > 30) ||
                                (
                                $dataArray[$cell->getColumn()]['month'] == "02" &&
                                (
                                ($cellValue > 28 && $dataArray[$cell->getColumn()]['year'] % 4 == 0) ||
                                ($cellValue > 29 && $dataArray[$cell->getColumn()]['year'] % 4 > 0)
                                )
                                )
                        ) {
                            echo json_encode(array(
                                "success" => 0,
                                "msg" => "<br/>Invalid Day specified. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                            ));
                            die();
                        } else {
                            $date = new DateTime($dataArray[$cell->getColumn()]['year'] . "-" . $dataArray[$cell->getColumn()]['month'] . "-" . $cellValue . " 00:00:00");
                            if ($date > $period_to || $date < $period_from) {
                                echo json_encode(array(
                                    "success" => 0,
                                    "msg" => "<br/>Dates should be between " . $period_from->format("Y-m-d") . " AND " . $period_to->format("Y-m-d") . ". <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                ));
                                die();
                            }
                        }
                        $dataArray[$cell->getColumn()]['day'] = $cellValue;
                    }
                } else
                if ($rownumber > 3) {
                    $user = "";
                    $sched_user = array();
                    foreach ($cellIterator as $cell) {
                        $cellValue = $cell->getValue();
                        if ($cell->getColumn() == "B")
                            continue;
                        if ($cell->getColumn() == "A") {
                            $user = $cellValue;
                            if (empty($cellValue))
                                break;
                            $user_dtl = $this->employees_m->getById($user);
                            if (count($user_dtl)) {
                                $user = $user_dtl->mb_no;
                                $shifts_dtl = $this->shifts_m->getAll(false, "*, CONCAT(LPAD(shift_hr_from,2,'0'),':',LPAD(shift_min_from,2,'0'),'-',LPAD(shift_hr_to,2,'0'),':',LPAD(shift_min_to,2,'0')) shift_sched, shift_color", "FIND_IN_SET('" . $user_dtl->mb_no . "', sched_users)");
                                if ($user_dtl->mb_sched_grp_id != $apprv_grp_id) {
                                    echo json_encode(array(
                                        "success" => 0,
                                        "msg" => "<br/>Employee is not assigned to this Group. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                    ));
                                    die();
                                }
                            } else {
                                echo json_encode(array(
                                    "success" => 0,
                                    "msg" => "<br/>Employee ID does not exists. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                ));
                                die();
                            }
                            continue;
                        }
                        if (!in_array($cellValue, $shifts_list)) {
                            echo json_encode(array(
                                "success" => 0,
                                "msg" => "<br/>Invalid Shift Code. <br/>Valid values [" . implode(", ", $shifts_list) . "]. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                            ));
                            die();
                        } else {
                            $shifts_dept_arr = explode(",", $shifts_dept[$cellValue]);
                            if (count($shifts_dtl)) {
                                $valid = false;
                                foreach ($shifts_dtl as $shift) {
                                    if ($shift->shift_code == $cellValue) {
                                        $valid = true;
                                        break;
                                    }
                                }
                                if ($valid == false && array_search($cellValue, $shifts_list) > 0) {
                                    echo json_encode(array(
                                        "success" => 0,
                                        "msg" => "<br/>Shift Code '$cellValue' not allowed for employee. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                    ));
                                    die();
                                }
                            } else if (!in_array($user_dtl->mb_deptno, $shifts_dept_arr) && $shifts_dept[$cellValue] != "all") {
                                echo json_encode(array(
                                    "success" => 0,
                                    "msg" => "<br/>Shift Code '$cellValue' not allowed for employee. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                ));
                                die();
                            }
                        }
                        $dataArray[$cell->getColumn()]['users'][$user] = array_search($cellValue, $shifts_list);
                    }
                }
                $rownumber++;
            }

            $data = $this->shifts_m->getAllUploadsFiltered("tsu.*", array(
                "tsu.apprv_grp_id" => $file_info['apprv_grp_id'],
                "tsu.period_from" => $file_info['period_from'],
                "tsu.period_to" => $file_info['period_to'],
                "tsu.status <>" => 3
            ));
            if (count($data)) {
                $param = array("upload_id" => $data[0]->upload_id, "apprv_grp_id" => $file_info['apprv_grp_id'], "period_from" => $file_info['period_from'], "period_to" => $file_info['period_to']);
                unset($file_info['apprv_grp_id']);
                unset($file_info['period_from']);
                unset($file_info['period_to']);
                unset($file_info['created_datetime']);
                unset($file_info['created_by']);
                $success = $this->shifts_m->updateSchedUpload($file_info, $param);
                if ($success) {
                    echo json_encode(array("success" => 1, "msg" => "Schedule has been updated"));
                } else {
                    echo json_encode(array("success" => 0, "msg" => "<br/>A database error occured. <br/>Please contact system administrator."));
                }
            } else {
                $success = $this->shifts_m->insertSchedUpload($file_info);
                if ($success) {
                    echo json_encode(array("success" => 1, "msg" => "Schedule has been uploaded"));
                } else {
                    echo json_encode(array("success" => 0, "msg" => "<br/>A database error occured. <br/>Please contact system administrator."));
                }
            }
        }
    }

    public function submitSchedule() {
        $mb_no = $this->session->userdata("mb_no");
        if ($mb_no) {
            $date = new DateTime();
            $upload_id = $this->input->post("upload_id");
            $param = array("upload_id" => $upload_id);
            $upload_info = array("status" => 1);
            $success = $this->shifts_m->updateSchedUpload($upload_info, $param);

            if ($success) {
                $upload_dtl = $this->shifts_m->getAllUploadsFiltered("tsu.*", array("tsu.upload_id" => $upload_id));

                $approver2_dtl = $this->shifts_m->getApprovalGroupApprover($upload_dtl[0]->apprv_grp_id, "*", array("taga.mb_id" => $mb_no));

                if (count($approver2_dtl)) {
                    $approver_dtl = $this->shifts_m->getApprovalGroupApprover($approver2_dtl[0]->apprv_grp_id, "MIN(level) level", array("level >" => $approver2_dtl[0]->level));
                } else {
                    $approver_dtl = $this->shifts_m->getApprovalGroupApprover($upload_dtl[0]->apprv_grp_id, "MIN(level) level");
                }


                if (count($approver_dtl) && $approver_dtl[0]->level) {
                    $data = array("upload_id" => $upload_dtl[0]->upload_id,
                        "apprv_grp_id" => $upload_dtl[0]->apprv_grp_id,
                        "period_from" => $upload_dtl[0]->period_from,
                        "period_to" => $upload_dtl[0]->period_to,
                        "approved_level" => $approver_dtl[0]->level,
                        "submitted_by" => $mb_no,
                        "status" => 1,
                        "created_by" => $mb_no,
                        "created_datetime" => $date->format("Y-m-d H:i:s"),
                        "updated_by" => $mb_no,
                        "updated_datetime" => $date->format("Y-m-d H:i:s"));
                    $success = $this->shifts_m->insertForApproval($data);
                    $data = array("upload_id" => $upload_dtl[0]->upload_id,
                        "status" => 1,
                        "remarks" => "Submitted for approval",
                        "created_by" => $mb_no,
                        "created_datetime" => $date->format("Y-m-d H:i:s"));
                    $success = $this->shifts_m->insertForApprovalSchedHist($data);
                    echo json_encode(array("success" => 1, "msg" => "Schedule Submitted!"));
                } else {
                    $this->shifts_m->updateSchedUpload(array("status" => 3), array("upload_id" => $upload_dtl[0]->upload_id));
                    // To be continued...
                }
            } else {
                echo json_encode(array("success" => 0, "msg" => "A database error occured. Please contact system administrator."));
            }
        } else {
            echo json_encode(array("success" => 0, "msg" => "You have been logged out. Please reload the page."));
        }
    }

    public function deleteSchedule() {
        $upload_id = $this->input->post("upload_id");
        $param = array("upload_id" => $upload_id);
        $upload_dtl = $this->shifts_m->getAllUploadsFiltered("tsu.*", array("tsu.upload_id" => $upload_id));
        if ($upload_dtl[0]->dirty_bit_ind)
            $success = $this->shifts_m->updateSchedUpload(array("status" => 4), $param);
        else
            $success = $this->shifts_m->deleteSchedUpload($param);

        if ($success) {
            $this->shifts_m->deleteForApproval($param);
            if (!$upload_dtl[0]->dirty_bit_ind)
                unlink(dirname(__FILE__) . "/../../" . $upload_dtl[0]->file_path);
            echo json_encode(array("success" => 1, "msg" => "Schedule Deleted!"));
        }
        else {
            echo json_encode(array("success" => 0, "msg" => "A database error occured. Please contact system administrator."));
        }
    }

    public function getAllUploads() {
        $post = $this->input->post();
        $order_arr = array();
        if (is_array($post['order'])) {
            foreach ($post['order'] as $orderDtl) {
                $order_arr[$post['columns'][$orderDtl['column']]['data']] = $orderDtl['dir'];
            }
        }

        $uploader_depts = $this->shifts_m->getUploaderDepartment($this->session->userdata("mb_no"), "*");
        $apprv_grp_id = 0;
        $approval_group_arr = array();
        ;
        if (count($uploader_depts)) {
            foreach ($uploader_depts as $uploader_dept) {
                $apprv_grp_id = $uploader_dept->apprv_grp_id;
                $approval_group_arr[] = $apprv_grp_id;
            }
        }
        $apprv_str = "";
        $having_str = "";
        $apprv_str .= "tsu.apprv_grp_id IN ('" . implode("','", $approval_group_arr) . "')";

        // $having_str .= $apprv_str;
        $search_str = "";
        // $having_str = "tsu.apprv_grp_id = '".$apprv_grp_id."'";
        if (!empty($post['search']['value'])) {
            foreach ($post['columns'] as $column) {
                if ($column['searchable'] == "true") {
                    $search_str .= (empty($search_str) ? "" : " OR ") . $column['data'] . " LIKE '%" . $post['search']['value'] . "%' ";
                }
            }
        }

        if (!empty($search_str))
            $having_str .= $apprv_str . " AND (" . $search_str . ")";
        else
            $having_str .= $apprv_str;

        $select_str = "tsu.*, tag.group_code, gm.mb_nick, CASE tsu.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END status_lbl, CONCAT(tsu.period_from,' ~ ',tsu.period_to) period ";

        $data = $this->shifts_m->getAllUploadsFiltered($select_str, $apprv_str);
        $all_upload_count = count($data);

        $data_all = $this->shifts_m->getAllUploadsFiltered($select_str, $having_str);
        $all_filtered_count = count($data_all);

        $data = $this->shifts_m->getAllUploadsFiltered($select_str, $having_str, $post['start'], $post['length'], $order_arr);

        echo json_encode(array("data" => $data,
            "draw" => (int) $post["draw"],
            "recordsTotal" => $all_upload_count,
            "recordsFiltered" => $all_filtered_count));
    }

    public function getUploadSchedHistory() {
        $upload_id = $this->input->post("id");
        $remarks = $this->shifts_m->getUploadSchedRemarks("tsah.*,gm.mb_nick", "tsah.upload_id = '" . $upload_id . "'");
        echo json_encode(array("success" => 1, "remarks" => $remarks));
    }

    /* End of Schedule */

    /* Approval */

    public function getAllForApproval() {
        $post = $this->input->post();
        $order_arr = array();
        if (is_array($post['order'])) {
            foreach ($post['order'] as $orderDtl) {
                $order_arr[$post['columns'][$orderDtl['column']]['data']] = $orderDtl['dir'];
            }
        }

        $approver_depts = $this->shifts_m->getApproverGroup($this->session->userdata("mb_no"), "DISTINCT taga.apprv_grp_id, taga.level");
        $app_level = $apprv_grp_id = 0;
        $apprv_grp = "";
        $apprv_level = "";
        $search_str = "";
        if (count($approver_depts)) {
            foreach ($approver_depts as $groups) {
                $search_str .= (empty($search_str) ? "" : " OR ") . "(tsa.apprv_grp_id = '" . $groups->apprv_grp_id . "' AND tsa.approved_level >= '" . $groups->level . "')";
            }
        } else {
            $search_str .= "tsa.approval_id < 0";
        }

        if (!empty($post["status"])) {
            $search_str = "(" . $search_str . ") AND tsa.status = '" . $post["status"] . "'";
        }

        $having_str = "";
        if (!empty($post['search']['value'])) {
            foreach ($post['columns'] as $column) {
                if ($column['searchable'] == "true") {
                    $having_str .= (empty($having_str) ? "" : " OR ") . $column['data'] . " LIKE '%" . $post['search']['value'] . "%' ";
                }
            }
        }
        $having_str = empty($having_str) ? $search_str : "(" . $search_str . ") AND (" . $having_str . ")";

        $select_str = " tsa.*, tsu.file_path, tsu.org_file, tag.group_code, sub.mb_nick sender, apprv.mb_nick approver, CASE tsa.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' END status_lbl, CONCAT(tsa.period_from,' ~ ',tsa.period_to) period, taga.level user_level, tsa.created_datetime ";

        $data = $this->shifts_m->getAllForApprovalFiltered($select_str, $search_str);
        $all_approval_count = count($data);

        $data_all = $this->shifts_m->getAllForApprovalFiltered($select_str, $having_str);
        $all_filtered_count = count($data_all);

        $data = $this->shifts_m->getAllForApprovalFiltered($select_str, $having_str, $post['start'], $post['length'], $order_arr);

        echo json_encode(array("data" => $data,
            "draw" => (int) $post["draw"],
            "recordsTotal" => $all_approval_count,
            "recordsFiltered" => $all_filtered_count));
    }

    public function approveSchedule() {
        $date = new DateTime();
        $post = $this->input->post();
        $approver_dtl = $this->shifts_m->getApprovalGroupApprover($post['grp_id'], "MIN(level) level", array("level >" => $post['approval_level']));
        if (count($approver_dtl) && $approver_dtl[0]->level) {
            $this->shifts_m->updateForApproval(array("approved_level" => $approver_dtl[0]->level, "approved_by" => $this->session->userdata("mb_no")), array("approval_id" => $post['approval_id']));
            $data = array("upload_id" => $post['upload_id'],
                "status" => 3,
                "remarks" => "Approved",
                "created_by" => $this->session->userdata("mb_no"),
                "created_datetime" => $date->format("Y-m-d H:i:s"));
            $success = $this->shifts_m->insertForApprovalSchedHist($data);
        } else {
            $this->shifts_m->updateForApproval(array("status" => 3, "approved_by" => $this->session->userdata("mb_no")), array("approval_id" => $post['approval_id']));
            $this->shifts_m->updateSchedUpload(array("status" => 3, "dirty_bit_ind" => 1), array("upload_id" => $post['upload_id']));

            $data = array("upload_id" => $post['upload_id'],
                "status" => 3,
                "remarks" => "Approved",
                "created_by" => $this->session->userdata("mb_no"),
                "created_datetime" => $date->format("Y-m-d H:i:s"));
            $success = $this->shifts_m->insertForApprovalSchedHist($data);

            $WshShell = new COM("WScript.Shell");
            if ($WshShell) {
                if (getenv('HTTP_HOST') == '10.120.10.139') {
                    $phpPath = "C:\\php-5.6.3-Win32-VC11-x64\\php.exe";
                    $outputPath = "C:\\Testing.txt";
                } else {
                    $phpPath = "C:\\xampp\\php\\php.exe";
                    $outputPath = "C:\\xampp\\htdocs\\Testing.txt";
                }
                $oExec = $WshShell->Run("cmd /K " . $phpPath . " " . BASEPATH . "..\\index.php api setUploadedFile " . $post['upload_id'] . " >> " . $outputPath, 0, false);
            } else {
                $shifts_dtl = $this->shifts_m->getAll(false, "*");

                $shifts_dtl[] = (object) array("shift_id" => 0, "shift_code" => "RD");
                $shifts_dtl[] = (object) array("shift_id" => -1, "shift_code" => "SS");
                $shifts_dtl[] = (object) array("shift_id" => -2, "shift_code" => "PH");
                $shifts_list = array();
                foreach ($shifts_dtl as $shift) {
                    $shifts_list[$shift->shift_id] = $shift->shift_code;
                }

                $upload_dtl = $this->shifts_m->getAllUploadsFiltered("*", "upload_id ='" . $post['upload_id'] . "'");

                $reader = $upload_dtl[0]->file_ext == ".xlsx" ? "Excel2007" : "Excel5";
                //load our new PHPExcel library
                $this->load->library('excel');
                $objReader = PHPExcel_IOFactory::createReader($reader);
                $objPHPExcel = $objReader->load(dirname(__FILE__) . "/../../" . $upload_dtl[0]->file_path);
                $objPHPExcel->setActiveSheetIndex();
                $objWorksheet = $objPHPExcel->getActiveSheet();
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnInd = PHPExcel_Cell::columnIndexFromString($highestColumn);

                $dataArray = array();
                //$mapArray = array();
                $rownumber = 1;

                /* Update Attendance */
                $emp_list = array();
                $date_from = "";
                $date_to = "";
                /* End Update Attendance */

                while ($rownumber <= $highestRow) {
                    $row = $objWorksheet->getRowIterator($rownumber)->current();
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);

                    // Month Year Checking
                    if ($rownumber == 1) {
                        $year = $month = "";
                        foreach ($cellIterator as $col => $cell) {
                            $cellValue = $cell->getValue();

                            if (!empty($year)) {
                                $dataArray[$cell->getColumn()]['month'] = $month;
                                $dataArray[$cell->getColumn()]['year'] = $year;
                            }

                            if (empty($cellValue))
                                continue;
                            else {
                                $cellValueArr = explode(" ", $cellValue);
                                $cellmonth = strtolower($cellValueArr[0]);
                                $cellyear = strtolower($cellValueArr[1]);
                                if ($cellmonth != $month) {
                                    $dataArray[$cell->getColumn()]['month'] = $this->month_list[$cellmonth];
                                    $dataArray[$cell->getColumn()]['year'] = $cellyear;
                                    $month = $this->month_list[$cellmonth];
                                    $year = $cellyear;
                                }
                            }
                        }
                    } else // Month Year Checking
                    if ($rownumber == 2) {
                        $month_31 = array("01", "03", "05", "07", "08", "10", "12");
                        $month_30 = array("04", "06", "09", "11");
                        foreach ($cellIterator as $col => $cell) {
                            if ($col < 2)
                                continue;
                            $cellValue = $cell->getValue();
                            $dataArray[$cell->getColumn()]['day'] = $cellValue;
                        }
                    }
                    else
                    if ($rownumber > 3) {
                        $user = "";
                        foreach ($cellIterator as $cell) {
                            $cellValue = $cell->getValue();
                            if ($cell->getColumn() == "B")
                                continue;
                            if ($cell->getColumn() == "A") {
                                $user = $cellValue;
                                if (empty($cellValue))
                                    break;
                                $user_dtl = $this->employees_m->getById($user);
                                if (count($user_dtl)) {
                                    $user = $user_dtl->mb_no;
                                    /* Update Attendance */
                                    $emp_list[] = $user_dtl->mb_no;
                                    /* End Update Attendance */
                                }
                                continue;
                            }
                            /* Update Attendance */
                            if ($cell->getColumn() == "C") {
                                $date_from = $dataArray[$cell->getColumn()]['year'] . (str_pad($dataArray[$cell->getColumn()]['month'], 2, "0", STR_PAD_LEFT)) . (str_pad($dataArray[$cell->getColumn()]['day'], 2, "0", STR_PAD_LEFT));
                            } else {
                                $date_to = $dataArray[$cell->getColumn()]['year'] . (str_pad($dataArray[$cell->getColumn()]['month'], 2, "0", STR_PAD_LEFT)) . (str_pad($dataArray[$cell->getColumn()]['day'], 2, "0", STR_PAD_LEFT));
                            }
                            /* End Update Attendance */
                            $sched_dtl = $this->shifts_m->getAllMemberScheduleFiltered("*", "`mb_no` = '" . $user . "' AND `year` = '" . $dataArray[$cell->getColumn()]['year'] . "' AND `month` = '" . $dataArray[$cell->getColumn()]['month'] . "' AND `day` = '" . $dataArray[$cell->getColumn()]['day'] . "'");

                            if ($sched_dtl) {
                                $success = $this->shifts_m->updateMemberSchedule(
                                        array("shift_id" => array_search($cellValue, $shifts_list)), array("tkms_id" => $sched_dtl[0]->tkms_id)
                                );
                                if ($sched_dtl[0]->lv_app_id) {
                                    $request_dtl = $this->leaves_m->getEmpLeaveApplication("*, tla.status, DATE(tla.date_from) date_from, DATE(tla.date_to) date_to", "tla.lv_app_id = '" . $sched_dtl[0]->lv_app_id . "'");
                                    if ($request_dtl[0]->allocated > 0) {
										$date_from = new DateTime($request_dtl[0]->date_from);
                                        $leave_bal = $this->leaves_m->getEmpLeaveBalances($request_dtl[0]->mb_no, "*", $request_dtl[0]->leave_id,$date_from->format("Y"));
                                        if (array_search($cellValue, $shifts_list) > 0) {
                                            $this->leaves_m->updateEmpLeaveBalances(array("allocated" => $leave_bal[0]->allocated - 1, "used" => $leave_bal[0]->used + 1), array("leave_id" => $request_dtl[0]->leave_id, "mb_no" => $request_dtl[0]->mb_no, "year"=>$date_from->format("Y")));
                                            $this->leaves_m->updateLeaveApplication(array("allocated" => $request_dtl[0]->allocated - 1, "used" => $request_dtl[0]->used + 1), array("lv_app_id" => $request_dtl[0]->lv_app_id));
                                        } else {
                                            $this->leaves_m->updateEmpLeaveBalances(array("allocated" => $leave_bal[0]->allocated - 1, "bal" => $leave_bal[0]->bal + 1), array("leave_id" => $request_dtl[0]->leave_id, "mb_no" => $request_dtl[0]->mb_no, "year"=>$date_from->format("Y")));
                                            $this->leaves_m->updateLeaveApplication(array("allocated" => $request_dtl[0]->allocated - 1), array("lv_app_id" => $request_dtl[0]->lv_app_id));
                                            $success = $this->shifts_m->updateMemberSchedule(
                                                    array("leave_id" => 0, "lv_app_id" => 0,), array("tkms_id" => $sched_dtl[0]->tkms_id)
                                            );
                                        }
                                    }
                                }
                            } else {
                                $success = $this->shifts_m->insertMemberSchedule(array(
                                    "mb_no" => $user,
                                    "year" => $dataArray[$cell->getColumn()]['year'],
                                    "month" => $dataArray[$cell->getColumn()]['month'],
                                    "day" => $dataArray[$cell->getColumn()]['day'],
                                    "shift_id" => array_search($cellValue, $shifts_list)
                                ));
                            }
                        }
                    }
                    $rownumber++;
                }
                /* Update Attendance */
                if (!empty($date_from) && !empty($date_to)) {
                    foreach ($emp_list as $mb_no) {
                        $this->updateAttendance($mb_no, $date_from, $date_to);
                    }
                }
            }
            /* End Update Attendance */
        }
        echo json_encode(array("success" => 1, "msg" => "Schedule Approved!"));
    }

    public function rejectSchedule() {
        $date = new DateTime();
        $post = $this->input->post();
        $this->shifts_m->updateSchedUpload(array("status" => 2, "dirty_bit_ind" => 1), array("upload_id" => $post['upload_id']));
        $data = array("upload_id" => $post['upload_id'],
            "status" => 2,
            "remarks" => empty($post['remarks']) ? "Rejected" : $post['remarks'],
            "created_by" => $this->session->userdata("mb_no"),
            "created_datetime" => $date->format("Y-m-d H:i:s"));
        $success = $this->shifts_m->insertForApprovalSchedHist($data);
        $this->shifts_m->deleteForApproval(array("upload_id" => $post['upload_id']));
        echo json_encode(array("success" => 1, "msg" => "Schedule Rejected!"));
    }

    public function getAllChangeScheduleForApproval() {
        $post = $this->input->post();
        $order_arr = array();
        if (is_array($post['order'])) {
            foreach ($post['order'] as $orderDtl) {
                $order_arr[$post['columns'][$orderDtl['column']]['data']] = $orderDtl['dir'];
            }
        }

        $approver_depts = $this->shifts_m->getCWSApproverGroup($this->session->userdata("mb_no"), "DISTINCT taga.cws_apprv_grp_id, taga.level");
        $app_level = $apprv_grp_id = 0;
        $apprv_grp = "";
        $apprv_level = "";
        $search_str = "";
        if (count($approver_depts)) {
            foreach ($approver_depts as $groups) {
                $search_str .= (empty($search_str) ? "" : " OR ") . "(tcsa.apprv_grp_id = '" . $groups->cws_apprv_grp_id . "' AND tcsa.approved_level >= '" . $groups->level . "')";
            }
        } else {
            $search_str .= "tcsa.approval_id < 0";
        }

        if (!empty($post["status"])) {
            $search_str = "(" . $search_str . ") AND tcsa.status = '" . $post["status"] . "'";
        }

        $having_str = "";
        if (!empty($post['search']['value'])) {
            foreach ($post['columns'] as $column) {
                if ($column['searchable'] == "true") {
                    $having_str .= (empty($having_str) ? "" : " OR ") . $column['data'] . " LIKE '%" . $post['search']['value'] . "%' ";
                }
            }
        }
        $having_str = empty($having_str) ? $search_str : "(" . $search_str . ") AND (" . $having_str . ")";

        $select_str = " tcsa.*, tag.group_code, sub.mb_id, CONCAT(IF(sub.mb_3='Local',sub.mb_fname,sub.mb_nick),' ',sub.mb_lname) sender, CONCAT(IF(apprv.mb_3='Local',apprv.mb_fname,apprv.mb_nick),' ',apprv.mb_lname) approver, CASE tcsa.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' END status_lbl, CONCAT(tcsa.date_from,' ~ ',tcsa.date_to) period, taga.level user_level ";

        $data = $this->shifts_m->getAllForApprovalChangeShiftFiltered($select_str, $search_str);
        $all_approval_count = count($data);

        $data_all = $this->shifts_m->getAllForApprovalChangeShiftFiltered($select_str, $having_str);
        $all_filtered_count = count($data_all);

        $data = $this->shifts_m->getAllForApprovalChangeShiftFiltered($select_str, $having_str, $post['start'], $post['length'], $order_arr);

        echo json_encode(array("data" => $data,
            "draw" => (int) $post["draw"],
            "recordsTotal" => $all_approval_count,
            "recordsFiltered" => $all_filtered_count));
    }

    public function approveChangeSchedule() {
        $date = new DateTime();
        $post = $this->input->post();
        $approver_dtl = $this->shifts_m->getCWSApprovalGroupApprover($post['grp_id'], "MIN(level) level", array("level >" => $post['approval_level']));
        if (count($approver_dtl) && $approver_dtl[0]->level) {
            $success = $this->shifts_m->updateForApprovalChangeShift(array("approved_level" => $approver_dtl[0]->level, "approved_by" => $this->session->userdata("mb_no")), array("approval_id" => $post['approval_id']));

            if ($success) {
                $request_dtl = $this->shifts_m->getEmployeeChangeSchedules("tcsq.*", array("tcsq.cs_req_id" => $post['request_id']));

                // Notification - General
                $this->load->model('notifications_model', 'notifications');
                $this->notifications->create("application", 1, array("CWS", "approved"), $request_dtl[0]->mb_no, 0, "timekeeping/change_schedule");

                // Notification
                // $this->ws->load('notifs');
                $approver_dtl = $this->shifts_m->getCWSApprovalGroupApprover($post['grp_id'], "taga.*", array("level" => $approver_dtl[0]->level));
                foreach ($approver_dtl as $approver) {
                    $recipient = $approver->mb_id;
                    $date = new DateTime();
                    NOTIFS::publish("APPROVAL_$recipient", array(
                        'type' => "CWS",
                        'count' => 1
                    ));
                }
                $approver_dtl = $this->shifts_m->getCWSApprovalGroupApprover($post['grp_id'], "taga.*", array("level" => $post['approval_level']));
                foreach ($approver_dtl as $approver) {
                    $recipient = $approver->mb_id;
                    $date = new DateTime();
                    NOTIFS::publish("APPROVAL_$recipient", array(
                        'type' => "CWS",
                        'count' => -1
                    ));
                }
                //

                $this->shifts_m->updateChangeShift(array("dirty_bit_ind" => 1), array("cs_req_id" => $post['request_id']));
                $data = array("cs_req_id" => $post['request_id'],
                    "status" => 3,
                    "remarks" => "Approved",
                    "created_by" => $this->session->userdata("mb_no"),
                    "created_datetime" => $date->format("Y-m-d H:i:s"));
                $success = $this->shifts_m->insertForApprovalChangeShiftHist($data);
            }
        } else {
            $this->shifts_m->updateForApprovalChangeShift(array("status" => 3, "approved_by" => $this->session->userdata("mb_no")), array("approval_id" => $post['approval_id']));
            $this->shifts_m->updateChangeShift(array("status" => 3, "dirty_bit_ind" => 1), array("cs_req_id" => $post['request_id']));

            $change_data = $this->shifts_m->getEmployeeChangeSchedules("*", "cs_req_id = '" . $post['request_id'] . "'");

            if (count($change_data)) {
                if (!empty($change_data[0]->att_date_from)) {
                    $date_from = new DateTime($change_data[0]->att_date_from . " 00:00:00");
                    $date_to = new DateTime($change_data[0]->att_date_to . " 00:00:00");
                    $tmp_date = new DateTime($change_data[0]->att_date_from . " 00:00:00");

                    /* Update Attendance */
                    $date_str_from = $date_from->format("Ymd");
                    $date_str_to = $date_to->format("Ymd");
                    /* End Update Attendance */

                    while ($tmp_date <= $date_to) {
                        $shift_rec = $this->shifts_m->getEmployeeSchedules("*", "tms.`mb_no` = '" . $change_data[0]->mb_no . "' AND `year` = '" . $tmp_date->format("Y") . "' AND `month` = '" . $tmp_date->format("m") . "' AND `day` = '" . $tmp_date->format("d") . "' ");
                        if (count($shift_rec)) {
                            $this->shifts_m->updateMemberSchedule(
                                    array(
                                "shift_id" => $change_data[0]->proposed_shift_id
                                    ), array(
                                "year" => $tmp_date->format("Y"),
                                "month" => $tmp_date->format("m"),
                                "day" => $tmp_date->format("d"),
                                "mb_no" => $change_data[0]->mb_no
                                    )
                            );
                            if ($shift_rec[0]->lv_app_id) {
                                $request_dtl = $this->leaves_m->getEmpLeaveApplication("*, tla.status, DATE(tla.date_from) date_from, DATE(tla.date_to) date_to", "tla.lv_app_id = '" . $shift_rec[0]->lv_app_id . "'");
                                if ($request_dtl[0]->allocated > 0) {
									$date_from = new DateTime($request_dtl[0]->date_from);
                                    $leave_bal = $this->leaves_m->getEmpLeaveBalances($request_dtl[0]->mb_no, "*", $request_dtl[0]->leave_id,$date_from->format("Y"));
                                    if ($change_data[0]->proposed_shift_id > 0) {
                                        $this->leaves_m->updateEmpLeaveBalances(array("allocated" => $leave_bal[0]->allocated - 1, "used" => $leave_bal[0]->used + 1), array("leave_id" => $request_dtl[0]->leave_id, "mb_no" => $request_dtl[0]->mb_no, "year"=>$date_from->format("Y")));
                                        $this->leaves_m->updateLeaveApplication(array("allocated" => $request_dtl[0]->allocated - 1, "used" => $request_dtl[0]->used + 1), array("lv_app_id" => $request_dtl[0]->lv_app_id));
                                    } else {
                                        $this->leaves_m->updateEmpLeaveBalances(array("allocated" => $leave_bal[0]->allocated - 1, "bal" => $leave_bal[0]->bal + 1), array("leave_id" => $request_dtl[0]->leave_id, "mb_no" => $request_dtl[0]->mb_no, "year"=>$date_from->format("Y")));
                                        $this->leaves_m->updateLeaveApplication(array("allocated" => $request_dtl[0]->allocated - 1), array("lv_app_id" => $request_dtl[0]->lv_app_id));
                                        $success = $this->shifts_m->updateMemberSchedule(
                                                array("leave_id" => 0, "lv_app_id" => 0,), array("tkms_id" => $shift_rec[0]->tkms_id)
                                        );
                                    }
                                }
                            }
                        } else {
                            $this->shifts_m->insertMemberSchedule(
                                    array(
                                        "shift_id" => $change_data[0]->proposed_shift_id,
                                        "year" => $tmp_date->format("Y"),
                                        "month" => $tmp_date->format("m"),
                                        "day" => $tmp_date->format("d"),
                                        "mb_no" => $change_data[0]->mb_no
                                    )
                            );
                        }
                        $tmp_date->modify("+1 day");
                    }
                    $data = array("cs_req_id" => $post['request_id'],
                        "status" => 3,
                        "remarks" => "Approved",
                        "created_by" => $this->session->userdata("mb_no"),
                        "created_datetime" => $date->format("Y-m-d H:i:s"));

                    /* Update Attendance */
                    $this->updateAttendance($change_data[0]->mb_no, $date_str_from, $date_str_to);
                    /* End Update Attendance */

                    $success = $this->shifts_m->insertForApprovalChangeShiftHist($data);

                    $request_dtl = $this->shifts_m->getEmployeeChangeSchedules("tcsq.*", array("tcsq.cs_req_id" => $post['request_id']));

                    // Notification - General
                    $this->load->model('notifications_model', 'notifications');
                    $this->notifications->create("application", 1, array("CWS", "approved"), $request_dtl[0]->mb_no, 0, "timekeeping/change_schedule");


                    // Notification - Badge
                    $approver_dtl = $this->shifts_m->getCWSApprovalGroupApprover($post['grp_id'], "taga.*", array("level" => $post['approval_level']));
                    foreach ($approver_dtl as $approver) {
                        $recipient = $approver->mb_id;
                        $date = new DateTime();
                        NOTIFS::publish("APPROVAL_$recipient", array(
                            'type' => "CWS",
                            'count' => -1
                        ));
                    }
                    //
                }
            }
        }
        echo json_encode(array("success" => 1, "msg" => "Schedule Approved!"));
    }

    public function rejectChangeSchedule() {
        $mb_no = $this->session->userdata("mb_no");
        if ($mb_no) {
            $date = new DateTime();
            $post = $this->input->post();

            $approver_dtl = $this->shifts_m->getAllForApprovalChangeShiftFiltered("tcsa.*", "tcsa.cs_req_id = '" . $post['request_id'] . "'");
            if (count($approver_dtl)) {
                $this->shifts_m->updateChangeShift(array("status" => 2, "dirty_bit_ind" => 1), array("cs_req_id" => $post['request_id']));
                $success = $this->shifts_m->deleteForApprovalChangeShift(array("cs_req_id" => $post['request_id']));
                if ($success) {

                    $data = array("cs_req_id" => $post['request_id'],
                        "status" => 2,
                        "remarks" => empty($post['remarks']) ? "Rejected" : $post['remarks'],
                        "created_by" => $this->session->userdata("mb_no"),
                        "created_datetime" => $date->format("Y-m-d H:i:s"));
                    $success = $this->shifts_m->insertForApprovalChangeShiftHist($data);

                    $request_dtl = $this->shifts_m->getEmployeeChangeSchedules("tcsq.*", array("tcsq.cs_req_id" => $post['request_id']));

                    // Notification - General
                    $this->load->model('notifications_model', 'notifications');
                    $this->notifications->create("application", 1, array("CWS", "rejected"), $request_dtl[0]->mb_no, 0, "timekeeping/change_schedule");


                    // Notification - Badge
                    $approver_dtl = $this->shifts_m->getCWSApprovalGroupApprover($approver_dtl[0]->apprv_grp_id, "taga.*", array("level" => $approver_dtl[0]->approved_level));
                    foreach ($approver_dtl as $approver) {
                        $recipient = $approver->mb_id;
                        $date = new DateTime();
                        NOTIFS::publish("APPROVAL_$recipient", array(
                            'type' => "CWS",
                            'count' => -1
                        ));
                    }
                    //
                }
                echo json_encode(array("success" => 1, "msg" => "Change Schedule Rejected!"));
            } else {
                echo json_encode(array("success" => 0, "msg" => "Leave application does not exists."));
            }
        } else {
            echo json_encode(array("success" => 0, "msg" => "You have been logged out. Please login again."));
        }
    }

    /* End of Approval */

    /* Schedules */

    public function getAllSchedules() {
        $limit = $this->input->post("limit");
        $page = $this->input->post("page");
        $dept_id = $this->input->post("department");
        $mb_no = $this->input->post("emp");
        $offset = ($page - 1) * $limit;

        $date_from = $this->input->post("dateFrom");
        $date_from = new DateTime($date_from . " 00:00:00");

        $date_to = $this->input->post("dateTo");
        $date_to = new DateTime($date_to . " 00:00:00");
        if (!empty($mb_no)) {
            $employees = array((object) $this->employees_m->get($mb_no));
            $total_count = count($employees);
        } else {
            $employees = $this->employees_m->getAll(false, "m.mb_id", $dept_id);
            $total_count = count($employees);
            $employees = $this->employees_m->getAll(false, "m.*,d.dept_name", $dept_id, $offset, $limit, array("d.dept_name" => "ASC", "mb_lname" => "ASC"));
        }
        $response_arr = $return_arr = array();
        $response_arr = array("ID", "Name", "Department");
        foreach ($employees as $employee) {
            $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));
            $emp_data = array(
                "mb_id" => $employee->mb_id,
                "mb_nick" => $employee->mb_lname . ", " . ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname),
                "dept_name" => $employee->dept_name
            );
            while ($tmp_date <= $date_to) {
                $dateLabel = $tmp_date->format("Y-n-j");
                if (!in_array($dateLabel, $response_arr))
                    $response_arr[] = $dateLabel;
                $record = $this->shifts_m->getEmployeeSchedules("tms.year, tms.month, tms.day, tms.leave_id, tms.shift_id, tms.mb_no, tsc.shift_code, tsc.shift_color, CONCAT(TIME_FORMAT(CONCAT(tsc.shift_hr_from,':',tsc.shift_min_from),'%H:%i'),' - ',TIME_FORMAT(CONCAT(tsc.shift_hr_to,':',tsc.shift_min_to),'%H:%i'))sched", "tms.mb_no = '" . $employee->mb_no . "' AND year = '" . $tmp_date->format("Y") . "' AND month = '" . $tmp_date->format("n") . "' AND day = '" . $tmp_date->format("j") . "'");
                if (count($record)) {
                    if ($record[0]->leave_id) {
                        $leave_dtl = $this->leaves_m->getLeave($record[0]->leave_id);
                        $emp_data[$dateLabel] = $leave_dtl[0]->leave_code . "#C2C2C2#";
                    } else {
                        switch ($record[0]->shift_id) {
                            case "0":
                                $emp_data[$dateLabel] = "RD#1B7935#";
                                break;
                            case "-1":
                                $emp_data[$dateLabel] = "SS#FA4747#";
                                break;
                            case "-2":
                                $emp_data[$dateLabel] = "PH#D87947#";
                                break;
                            default:
                                $emp_data[$dateLabel] = $record[0]->shift_code.$record[0]->shift_color.(($record[0]->sched)?"#".$record[0]->sched:"");
                        }
                    }
                } else {
                    $emp_data[$dateLabel] = "";
                }
                $tmp_date->modify("+1 day");
            }
            $return_arr[] = $emp_data;
        }
        echo json_encode(array("data" => $return_arr, "header" => $response_arr, "total_count" => $total_count, "page" => $page));
    }

    public function schedules_export() {
        $this->load->library('excel');
        $activeSheetInd = -1;
        $file_name = "HRIS Schedules.xls";

        $dept_id = $this->input->post("export-dept");
        $mb_no = $this->input->post("export-emp");

        $date_from = $this->input->post("export-from");
        $date_from = new DateTime($date_from . " 00:00:00");

        $date_to = $this->input->post("export-to");
        $date_to = new DateTime($date_to . " 00:00:00");

        if (!empty($mb_no))
            $employees = array((object) $this->employees_m->get($mb_no));
        else
            $employees = $this->employees_m->getAll(false, "*", $dept_id, 0, 0, array("d.dept_name" => "ASC", "mb_3" => "DESC", "mb_lname" => "ASC")); {// Styles
            $headerStyle = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c9c9c9'),
                    'font' => array('bold' => true)),
                'borders' => array('outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '000000'),
                    )),
                'alignment' => array('wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
            $headerStyleDays = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c5d9f1')
                ),
                'borders' => array('outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '000000'),
                    )),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'font' => array('size' => "8")
            );
            $defaultSchedStyle = array('borders' => array('outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '000000'),
                    )),
                'alignment' => array('wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
            $nameSchedStyle = array('borders' => array('outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '000000'),
                    )),
                'alignment' => array('wrap' => true,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );

            $labelStyle = array('font' => array('bold' => true));
        }//End of Styles

        $activeSheet = $this->excel->setActiveSheetIndex( ++$activeSheetInd);

        $row = 1;
        //BLANK CELLS
        $merge_cells = 0;
        $start_cell = "A" . $row;
        $column_start = "A";
        for ($i = 1; $i <= 3; $i++) {
            $row_cel = $column_start . $row;
            $activeSheet->setCellValue($row_cel, "");
            $activeSheet->getStyle($row_cel)->applyFromArray($labelStyle);
            $merge_cells++;
            $column_start++;
        }//end for 

        if ($merge_cells > 1) {
            $activeSheet->mergeCells($start_cell . ":" . $row_cel);
            $activeSheet->getStyle($start_cell . ":" . $row_cel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }

        //MONTHS, YEAR
        // $dt_from = new DateTime($start_date." 00:00:00");
        // $dt_to = new DateTime($end_date." 00:00:00");
        $column_start_num = 3;
        $color_ctr = 1;
        $cnt_date = new DateTime($date_from->format("Y-m-d H:i:s"));
        while ($cnt_date <= $date_to) {
            $tmp_date = new DateTime($cnt_date->format("Y-m-t"));
            if ($tmp_date > $date_to)
                $tmp_date = new DateTime($date_to->format("Y-m-d"));

            $diff = $cnt_date->diff($tmp_date);

            if ($diff->format("%a") != 6015) {
                $total_merge = ($diff->format("%a") * 1) + 1;
            } else {
                // else let's use our own method

                $y1 = $cnt_date->format('Y');
                $y2 = $tmp_date->format('Y');
                $z1 = $cnt_date->format('z');
                $z2 = $tmp_date->format('z');

                $total_merge = abs(floor($y1 * 365.2425 + $z1) - floor($y2 * 365.2425 + $z2)) + 1;
            }

            $row_cell1 = $column_start . $row;
            $col_num = $total_merge + $column_start_num;
            //echo $col_num;
            $column_start = PHPExcel_Cell::stringFromColumnIndex($col_num - 1);
            $row_cell2 = $column_start . $row;

            $activeSheet->mergeCells($row_cell1 . ":" . $row_cell2);
            $activeSheet->getCell($row_cell1)->setValueExplicit($cnt_date->format("M Y"), PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->getStyle($row_cell1 . ":" . $row_cell2)->applyFromArray($this->headStyleRandom($color_ctr));

            $column_start = PHPExcel_Cell::stringFromColumnIndex($col_num);
            $tmp_date->modify("+1 day");
            $cnt_date = $tmp_date;
            $column_start_num += $total_merge;
            $color_ctr++;
        }
        //echo "asdasdasd";die();
        //END MONTHS, YEAR
        //Headers
        $c = 0;
        $row++;
        $header_list = array("ID", "Name", "Department");
        foreach ($header_list as $val) {
            $column = PHPExcel_Cell::stringFromColumnIndex($c);
            $row_cel = $column . $row;
            $row_cel2 = $column . ($row + 1);
            $activeSheet->mergeCells($row_cel . ":" . $row_cel2);
            //$activeSheet->setCellValue($row_cel,$val);
            $activeSheet->getCell($row_cel)->setValueExplicit($val, PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->getStyle($row_cel . ":" . $row_cel2)->applyFromArray($headerStyle);
            $c++;
        }

        $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));
        while ($tmp_date <= $date_to) {
            $column = PHPExcel_Cell::stringFromColumnIndex($c);
            $row_cel = $column . $row;
            $row_cel2 = $column . ($row + 1);
            $activeSheet->getCell($row_cel)->setValueExplicit($tmp_date->format("d"), PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->getCell($row_cel2)->setValueExplicit($tmp_date->format("D"), PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->getStyle($row_cel)->applyFromArray($headerStyle);
            $activeSheet->getStyle($row_cel2)->applyFromArray($headerStyleDays);
            $c++;
            $tmp_date->modify("+1 day");
        }
        $row++;
        if (count($employees)) {
            //$date_from = new DateTime($date_from." 00:00:00");
            foreach ($employees as $employee) {
                $c = 0;
                $row+=1;
                $column = PHPExcel_Cell::stringFromColumnIndex($c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $employee->mb_id);
                $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                $c+=1;
                $column = PHPExcel_Cell::stringFromColumnIndex($c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $employee->mb_lname . ", " . ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname));
                $activeSheet->getStyle($cell)->applyFromArray($nameSchedStyle);
                $c+=1;
                $column = PHPExcel_Cell::stringFromColumnIndex($c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $employee->dept_name);
                $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);

                $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));
                while ($tmp_date <= $date_to) {
                    $record = $this->shifts_m->getEmployeeSchedules("tms.*, tsc.shift_code, tsc.shift_color", "tms.mb_no = '" . $employee->mb_no . "' AND year = '" . $tmp_date->format("Y") . "' AND month = '" . $tmp_date->format("n") . "' AND day = '" . $tmp_date->format("j") . "'");
                    $c+=1;
                    $column = PHPExcel_Cell::stringFromColumnIndex($c);
                    $cell = $column . $row;

                    $color = "FFFFFF";
                    if (count($record)) {
                        if ($record[0]->leave_id) {
                            $leave_dtl = $this->leaves_m->getLeave($record[0]->leave_id);
                            $activeSheet->setCellValue($cell, $leave_dtl[0]->leave_code);
                            $color = "C2C2C2";
                        } else {
                            switch ($record[0]->shift_id) {
                                case "0":
                                    $activeSheet->setCellValue($cell, "RD");
                                    $color = "1B7935";
                                    break;
                                case "-1":
                                    $activeSheet->setCellValue($cell, "SS");
                                    $color = "FA4747";
                                    break;
                                case "-2":
                                    $activeSheet->setCellValue($cell, "PH");
                                    $color = "D87947";
                                    break;
                                default:
                                    $activeSheet->setCellValue($cell, $record[0]->shift_code);
                                    $color = str_replace("#", "", $record[0]->shift_color);
                            }
                        }
                    } else {
                        $activeSheet->setCellValue($cell, "");
                    }
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle)->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => $color)
                                )
                            )
                    );
                    $tmp_date->modify("+1 day");
                }
            }
        } else {
            $activeSheet = $this->excel->setActiveSheetIndex( ++$activeSheetInd);
            $activeSheet->setCellValue("A1", "No Record Found");
        }

        $column_start = 'A';
        $total_columns = $c + 1;
        for ($col = 0; $col < $total_columns; $col++) {
            $column_start = PHPExcel_Cell::stringFromColumnIndex($col);
            $activeSheet->getColumnDimension($column_start)->setAutoSize(true);
            //$column_start++; 
        }

        $activeSheet = $this->excel->setActiveSheetIndex(0);
        $activeSheet->setTitle('Attendance Report');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    /* End of Schedules */

    /* Shifts */

    public function getAllShifts($inactive = 0) {
        $post = $this->input->post();
        $order_arr = array();
        if (is_array($post['order'])) {
            foreach ($post['order'] as $orderDtl) {
                $order_arr[$post['columns'][$orderDtl['column']]['data']] = $orderDtl['dir'];
            }
        }

        $having_str = "";
        if (!empty($post['search']['value'])) {
            foreach ($post['columns'] as $column) {
                if ($column['searchable'] == "true") {
                    $having_str .= (empty($having_str) ? "" : " OR ") . $column['data'] . " LIKE '%" . $post['search']['value'] . "%' ";
                }
            }
        }

        $select_str = "s.*, CONCAT(LPAD(s.shift_hr_from,2,'0'),':',LPAD(s.shift_min_from,2,'0')) shift_from, " .
                "CONCAT(LPAD(s.shift_hr_to,2,'0'),':',LPAD(s.shift_min_to,2,'0')) shift_to, " .
                "IF(enabled,'Enabled','Disabled') enabled_lbl";


        $data = $this->shifts_m->getAll($inactive);
        $all_shifts_count = count($data);

        $data_all = $this->shifts_m->getAllFiltered($inactive, $select_str, $having_str);
        $all_filtered_count = count($data_all);

        $data = $this->shifts_m->getAllFiltered($inactive, $select_str, $having_str, $post['start'], $post['length'], $order_arr);

        echo json_encode(array("data" => $data,
            "draw" => (int) $post["draw"],
            "recordsTotal" => $all_shifts_count,
            "recordsFiltered" => $all_filtered_count));
    }

    public function getShift($shift_id) {
        $select_str = "tsc.*, CONCAT(LPAD(tsc.shift_hr_from,2,'0'),':',LPAD(tsc.shift_min_from,2,'0')) shift_from,
					CONCAT(LPAD(tsc.shift_hr_to,2,'0'),':',LPAD(tsc.shift_min_to,2,'0')) shift_to ";
        $data = $this->shifts_m->getShift($shift_id, $select_str);
        echo json_encode(array("success" => 1, "data" => $data));
    }

    public function updateShift() {
        $date = new DateTime();
        $post = $this->input->post();
        $shift_id = $post['shift-id'];
        $shift_code = $post['shift-code'];
        $enabled = $post['shift-status'];
        $color = $post['shift-color'];
        $shift_start = explode(":", $post['shift-start']);
        $shift_end = explode(":", $post['shift-end']);
        $sched_depts = isset($post['sched-dept']) ? $post['sched-dept'] : array();
        $cws_depts = isset($post['cws-dept']) ? $post['cws-dept'] : array();
        $sched_users = isset($post['sched-user']) ? $post['sched-user'] : array();
        $sched_depts_str = implode(",", $sched_depts);
        $cws_depts_str = implode(",", $cws_depts);
        $sched_users_str = implode(",", $sched_users);

        $success = $this->shifts_m->updateShift(
                array("shift_code" => $shift_code,
            "enabled" => $enabled,
            "shift_hr_from" => $shift_start[0],
            "shift_min_from" => $shift_start[1],
            "shift_hr_to" => $shift_end[0],
            "shift_min_to" => $shift_end[1],
            "shift_color" => $color,
            "updated_datetime" => $date->format("Y-m-d H:i:s"),
            "updated_by" => $this->session->userdata("mb_no"),
            "sched_depts" => $sched_depts_str,
            "cws_depts" => $cws_depts_str,
            "sched_users" => $sched_users_str
                ), array("shift_id" => $shift_id));

        echo json_encode(array("success" => 1, "msg" => "Record updated!"));
    }

    public function insertShift() {
        $date = new DateTime();
        $post = $this->input->post();
        $shift_code = $post['add-shift-code'];
        $enabled = $post['add-shift-status'];
        $color = $post['add-shift-color'];
        $start = explode(":", $post['add-shift-start']);
        $end = explode(":", $post['add-shift-end']);
        $sched_depts = isset($post['add-sched-dept']) ? $post['add-sched-dept'] : array();
        $cws_depts = isset($post['add-cws-dept']) ? $post['add-cws-dept'] : array();
        $sched_users = isset($post['add-sched-user']) ? $post['add-sched-user'] : array();
        $sched_depts_str = implode(",", $sched_depts);
        $cws_depts_str = implode(",", $cws_depts);
        $sched_users_str = implode(",", $sched_users);

        if (empty($shift_code)) {
            echo json_encode(array("success" => 0, "msg" => "Shift Code is required!"));
            return;
        }

        $shift_dtl = $this->shifts_m->getAllFiltered(true, "s.shift_code, s.shift_id", array("s.shift_code" => $shift_code));

        if (count($shift_dtl)) {
            echo json_encode(array("success" => 0, "msg" => "Shift Code already exists!"));
            return;
        } else {
            $success = $this->shifts_m->insertShift(array("shift_code" => $shift_code,
                "enabled" => $enabled,
                "shift_hr_from" => $start[0],
                "shift_min_from" => $start[1],
                "shift_hr_to" => $end[0],
                "shift_min_to" => $end[1],
                "shift_color" => $color,
                "created_datetime" => $date->format("Y-m-d H:i:s"),
                "created_by" => $this->session->userdata("mb_no"),
                "updated_datetime" => $date->format("Y-m-d H:i:s"),
                "updated_by" => $this->session->userdata("mb_no"),
                "sched_depts" => $sched_depts_str,
                "cws_depts" => $cws_depts_str,
                "sched_users" => $sched_users_str
            ));
            if ($success) {
                echo json_encode(array("success" => 1, "msg" => "Record Saved!"));
            } else {
                echo json_encode(array("success" => 0, "msg" => "A database error occured. Please contact system administrator."));
            }
        }
    }

    /* End of Shifts */

    /* Approval Groups */

    public function getApprovalGroup($apprv_id) {
        $select_str = "tag.*, " .
                "IF(enabled,'Enabled','Disabled') enabled_lbl";
        $data = $this->shifts_m->getApprovalGroup($apprv_id, $select_str);
        $data[0]->uploaders = $this->shifts_m->getApprovalGroupUploader($apprv_id, "mb_3, tagu.mb_id, gm.mb_nick, gm.mb_fname, gm.mb_lname");
        $data[0]->approvers = $this->shifts_m->getApprovalGroupApprover($apprv_id, "mb_3, taga.level, taga.mb_id, gm.mb_nick, gm.mb_fname, gm.mb_lname");
        $emp = $this->employees_m->getAll(false, "*", false);
        echo json_encode(array("success" => 1, "data" => $data, "emp" => $emp));
    }

    public function getAllApprovalGroups($inactive = 0) {
        $post = $this->input->post();
        $order_arr = array();
        if (is_array($post['order'])) {
            foreach ($post['order'] as $orderDtl) {
                $order_arr[$post['columns'][$orderDtl['column']]['data']] = $orderDtl['dir'];
            }
        }

        $having_str = "";
        if (!empty($post['search']['value'])) {
            foreach ($post['columns'] as $column) {
                if ($column['searchable'] == "true") {
                    $having_str .= (empty($having_str) ? "" : " OR ") . $column['data'] . " LIKE '%" . $post['search']['value'] . "%' ";
                }
            }
        }

        $select_str = "tag.*, " .
                "IF(tag.enabled,'Enabled','Disabled') enabled_lbl";

        $data = $this->shifts_m->getAllApprovalGroups($inactive, $select_str);
        $all_approvers_count = count($data);

        $data_all = $this->shifts_m->getAllApprovalGroupsFiltered($inactive, $select_str, $having_str);
        $all_filtered_count = count($data_all);

        $data = $this->shifts_m->getAllApprovalGroupsFiltered($inactive, $select_str, $having_str, $post['start'], $post['length'], $order_arr);

        echo json_encode(array("data" => $data,
            "draw" => (int) $post["draw"],
            "recordsTotal" => $all_approvers_count,
            "recordsFiltered" => $all_filtered_count));
    }

    public function insertApprovalGroup() {
        $date = new DateTime();
        $post = $this->input->post();
        $grp_code = $post['add-apprv-grp-code'];
        $enabled = $post['add-apprv-grp-status'];
        $uploader_list = isset($post['add-uploader']) ? $post['add-uploader'] : array();
        $approver_list = isset($post['add-approver']) ? $post['add-approver'] : array();
        $approver_lvl_list = isset($post['add-approver_lvl']) ? $post['add-approver_lvl'] : array();

        $app_group_dtl = $this->shifts_m->getAllApprovalGroupsFiltered(true, "tag.apprv_grp_id", array("group_code" => $grp_code));

        if (count($app_group_dtl)) {
            echo json_encode(array("success" => 0, "msg" => "Group Code already exists!"));
            return;
        } else {
            $success = $this->shifts_m->insertApprovalGroup(array("group_code" => $grp_code, "enabled" => $enabled, "created_datetime" => $date->format("Y-m-d H:i:s"), "created_by" => $this->session->userdata("mb_no"), "updated_datetime" => $date->format("Y-m-d H:i:s"), "updated_by" => $this->session->userdata("mb_no")));
            if ($success) {
                $app_group_dtl = $this->shifts_m->getAllApprovalGroupsFiltered(true, "tag.apprv_grp_id", array("group_code" => $grp_code));
                $grp_id = $app_group_dtl[0]->apprv_grp_id;
                foreach ($uploader_list as $uploader) {
                    $this->shifts_m->insertApprovalGroupUploaders(array("apprv_grp_id" => $grp_id, "mb_id" => $uploader));
                }
                foreach ($approver_list as $key => $approver) {
                    $this->shifts_m->insertApprovalGroupApprovers(array("apprv_grp_id" => $grp_id, "mb_id" => $approver, "level" => $approver_lvl_list[$key]));
                }
                echo json_encode(array("success" => 1, "msg" => "Record Saved!"));
            } else {
                echo json_encode(array("success" => 0, "msg" => "A database error occured. Please contact system administrator."));
            }
        }
    }

    public function updateApprovalGroup() {
        $date = new DateTime();
        $post = $this->input->post();
        $grp_id = $post['apprv-grp-id'];
        $grp_code = $post['apprv-grp-code'];
        $enabled = $post['apprv-grp-status'];
        $uploader_list = isset($post['uploader']) ? $post['uploader'] : array();
        $uploader_del_list = isset($post['uploader_del_arr']) ? $post['uploader_del_arr'] : array();
        $approver_list = isset($post['approver']) ? $post['approver'] : array();
        $approver_lvl_list = isset($post['approver_lvl']) ? $post['approver_lvl'] : array();
        $approver_del_list = isset($post['approver_del_arr']) ? $post['approver_del_arr'] : array();

        $app_group_dtl = $this->shifts_m->getApprovalGroup($grp_id, "*");
        $uploader_dtl = $this->shifts_m->getApprovalGroupUploader($grp_id, "*, GROUP_CONCAT(tagu.mb_id) uploaders");
        $approver_dtl = $this->shifts_m->getApprovalGroupApprover($grp_id, "*, GROUP_CONCAT(taga.mb_id) approvers");

        $org_uploaders = explode(",", $uploader_dtl[0]->uploaders);
        $for_deletion_uploaders = array_diff($uploader_del_list, $uploader_list);
        $for_insert_uploaders = array_diff($uploader_list, $org_uploaders);

        $org_approvers = explode(",", $approver_dtl[0]->approvers);
        $for_deletion_approvers = array_diff($approver_del_list, $approver_list);
        $for_insert_approvers = array_diff($approver_list, $org_approvers);

        $app_group_dtl = $this->shifts_m->getAllApprovalGroupsFiltered(true, "tag.apprv_grp_id", array("group_code" => $grp_code));

        if (count($app_group_dtl) && $app_group_dtl[0]->apprv_grp_id != $grp_id) {
            echo json_encode(array("success" => 0, "msg" => "Group Code already exists!"));
            return;
        }

        $success = $this->shifts_m->updateApprovalGroup(array("group_code" => $grp_code, "enabled" => $enabled, "updated_datetime" => $date->format("Y-m-d H:i:s"), "updated_by" => $this->session->userdata("mb_no")), array("apprv_grp_id" => $grp_id));

        foreach ($for_insert_uploaders as $uploader) {
            $this->shifts_m->insertApprovalGroupUploaders(array("apprv_grp_id" => $grp_id, "mb_id" => $uploader));
        }

        foreach ($for_deletion_uploaders as $uploader) {
            $this->shifts_m->deleteApprovalGroupUploaders(array("apprv_grp_id" => $grp_id, "mb_id" => $uploader));
        }

        foreach ($for_insert_approvers as $key => $approver) {
            $this->shifts_m->insertApprovalGroupApprovers(array("apprv_grp_id" => $grp_id, "mb_id" => $approver, "level" => $approver_lvl_list[$key]));
        }

        foreach ($for_deletion_approvers as $approver) {
            $this->shifts_m->deleteApprovalGroupApprovers(array("apprv_grp_id" => $grp_id, "mb_id" => $approver));
        }
        if ($success)
            echo json_encode(array("success" => 1, "msg" => "Record updated!"));
        else
            echo json_encode(array("success" => 0, "msg" => "A database error occured. Please contact system administrator."));
    }

    public function deleteApprovalGroup() {
        $post = $this->input->post();
        $grp_id = $post['apprv_id'];

        $this->shifts_m->deleteApprovalGroup(array("apprv_grp_id" => $grp_id));
        $this->shifts_m->deleteApprovalGroupUploaders(array("apprv_grp_id" => $grp_id));
        $this->shifts_m->deleteApprovalGroupApprovers(array("apprv_grp_id" => $grp_id));

        echo json_encode(array("success" => 1));
    }

    public function getApprovalGroupFields() {
        $emp = $this->employees_m->getAll(false, "*", false);
        echo json_encode(array("success" => 1, "emp" => $emp));
    }

    /* End of Approval Groups */

    /* CWS Approval Groups */

    public function getCWSApprovalGroup($apprv_id) {
        $select_str = "tag.*, " .
                "IF(enabled,'Enabled','Disabled') enabled_lbl";
        $data = $this->shifts_m->getCWSApprovalGroup($apprv_id, $select_str);
        $data[0]->approvers = $this->shifts_m->getCWSApprovalGroupApprover($apprv_id, "mb_3, taga.level, taga.mb_id, gm.mb_nick, gm.mb_fname, gm.mb_lname");
        $emp = $this->employees_m->getAll(false, "*", false);
        echo json_encode(array("success" => 1, "data" => $data, "emp" => $emp));
    }

    public function getAllCWSApprovalGroups($inactive = 0) {
        $post = $this->input->post();
        $order_arr = array();
        if (is_array($post['order'])) {
            foreach ($post['order'] as $orderDtl) {
                $order_arr[$post['columns'][$orderDtl['column']]['data']] = $orderDtl['dir'];
            }
        }

        $having_str = "";
        if (!empty($post['search']['value'])) {
            foreach ($post['columns'] as $column) {
                if ($column['searchable'] == "true") {
                    $having_str .= (empty($having_str) ? "" : " OR ") . $column['data'] . " LIKE '%" . $post['search']['value'] . "%' ";
                }
            }
        }

        $select_str = "tag.*, " .
                "IF(tag.enabled,'Enabled','Disabled') enabled_lbl";

        $data = $this->shifts_m->getAllCWSApprovalGroups($inactive, $select_str);
        $all_approvers_count = count($data);

        $data_all = $this->shifts_m->getAllCWSApprovalGroupsFiltered($inactive, $select_str, $having_str);
        $all_filtered_count = count($data_all);

        $data = $this->shifts_m->getAllCWSApprovalGroupsFiltered($inactive, $select_str, $having_str, $post['start'], $post['length'], $order_arr);

        echo json_encode(array("data" => $data,
            "draw" => (int) $post["draw"],
            "recordsTotal" => $all_approvers_count,
            "recordsFiltered" => $all_filtered_count));
    }

    public function insertCWSApprovalGroup() {
        $date = new DateTime();
        $post = $this->input->post();
        $grp_code = $post['add-cws-apprv-grp-code'];
        $enabled = $post['add-cws-apprv-grp-status'];
        $approver_list = isset($post['add_cws_approver']) ? $post['add_cws_approver'] : array();
        $approver_lvl_list = isset($post['add_cws_approver_lvl']) ? $post['add_cws_approver_lvl'] : array();

        $app_group_dtl = $this->shifts_m->getAllCWSApprovalGroupsFiltered(true, "tag.cws_apprv_grp_id", array("group_code" => $grp_code));

        if (count($app_group_dtl)) {
            echo json_encode(array("success" => 0, "msg" => "CWS Group Code already exists!"));
            return;
        } else {
            $success = $this->shifts_m->insertCWSApprovalGroup(array("group_code" => $grp_code, "enabled" => $enabled, "created_datetime" => $date->format("Y-m-d H:i:s"), "created_by" => $this->session->userdata("mb_no"), "updated_datetime" => $date->format("Y-m-d H:i:s"), "updated_by" => $this->session->userdata("mb_no")));
            if ($success) {
                $app_group_dtl = $this->shifts_m->getAllCWSApprovalGroupsFiltered(true, "tag.cws_apprv_grp_id", array("group_code" => $grp_code));
                $grp_id = $app_group_dtl[0]->cws_apprv_grp_id;
                foreach ($approver_list as $key => $approver) {
                    $this->shifts_m->insertCWSApprovalGroupApprovers(array("cws_apprv_grp_id" => $grp_id, "mb_id" => $approver, "level" => $approver_lvl_list[$key]));
                }
                echo json_encode(array("success" => 1, "msg" => "Record Saved!"));
            } else {
                echo json_encode(array("success" => 0, "msg" => "A database error occured. Please contact system administrator."));
            }
        }
    }

    public function updateCWSApprovalGroup() {
        $date = new DateTime();
        $post = $this->input->post();
        $grp_id = $post['cws-apprv-grp-id'];
        $grp_code = $post['cws-apprv-grp-code'];
        $enabled = $post['cws-apprv-grp-status'];
        $approver_list = isset($post['cws_approver']) ? $post['cws_approver'] : array();
        $approver_lvl_list = isset($post['cws_approver_lvl']) ? $post['cws_approver_lvl'] : array();
        $approver_del_list = isset($post['cws_approver_del_arr']) ? $post['cws_approver_del_arr'] : array();
        $success = true;

        $app_group_dtl = $this->shifts_m->getCWSApprovalGroup($grp_id, "*");
        $approver_dtl = $this->shifts_m->getCWSApprovalGroupApprover($grp_id, "*, GROUP_CONCAT(taga.mb_id) approvers");

        $org_approvers = explode(",", $approver_dtl[0]->approvers);
        $for_deletion_approvers = array_diff($approver_del_list, $approver_list);
        $for_insert_approvers = array_diff($approver_list, $org_approvers);

        $app_group_dtl = $this->shifts_m->getAllCWSApprovalGroupsFiltered(true, "tag.cws_apprv_grp_id", array("group_code" => $grp_code));

        if (count($app_group_dtl) && $app_group_dtl[0]->cws_apprv_grp_id != $grp_id) {
            echo json_encode(array("success" => 0, "msg" => "CWS Group Code already exists!"));
            return;
        }

        $success = $this->shifts_m->updateCWSApprovalGroup(array("group_code" => $grp_code, "enabled" => $enabled, "updated_datetime" => $date->format("Y-m-d H:i:s"), "updated_by" => $this->session->userdata("mb_no")), array("cws_apprv_grp_id" => $grp_id));

        foreach ($for_deletion_approvers as $approver) {
            $this->shifts_m->deleteCWSApprovalGroupApprovers(array("cws_apprv_grp_id" => $grp_id, "mb_id" => $approver));
        }

        foreach ($for_insert_approvers as $key => $approver) {
            $this->shifts_m->insertCWSApprovalGroupApprovers(array("cws_apprv_grp_id" => $grp_id, "mb_id" => $approver, "level" => $approver_lvl_list[$key]));
        }

        if ($success)
            echo json_encode(array("success" => 1, "msg" => "Record updated!"));
        else
            echo json_encode(array("success" => 0, "msg" => "A database error occured. Please contact system administrator."));
    }

    public function deleteCWSApprovalGroup() {
        $post = $this->input->post();
        $grp_id = $post['apprv_id'];

        $this->shifts_m->deleteCWSApprovalGroup(array("cws_apprv_grp_id" => $grp_id));
        $this->shifts_m->deleteCWSApprovalGroupApprovers(array("cws_apprv_grp_id" => $grp_id));

        echo json_encode(array("success" => 1));
    }

    public function getCWSApprovalGroupFields() {
        $emp = $this->employees_m->getAll(false, "*", false);
        echo json_encode(array("success" => 1, "emp" => $emp));
    }

    /* End of Approval Groups */

    /* Change of Schedule */

    public function getEmpSchedule() {
        $limit = $this->input->post("limit");
        $page = $this->input->post("page");
        $mb_no = $this->session->userdata("mb_no");
        $offset = ($page - 1) * $limit;

        $date_from = $this->input->post("dateFrom");
        $date_from = new DateTime($date_from . " 00:00:00");

        $date_to = $this->input->post("dateTo");
        $date_to = new DateTime($date_to . " 00:00:00");

        $employees = array(0 => (object) $this->employees_m->get($mb_no));
        $total_count = count($employees);
        $response_arr = $return_arr = array();
        $response_arr = array("ID", "Name", "Department", "Date", "Shift", "IN", "OUT");
        $width_arr = array(80, 200, 80, 80, 50, 60, 60);

        foreach ($employees as $employee) {
            $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));
            while ($tmp_date <= $date_to) {
                $default_shifts[0] = "RD";
                $default_shifts[-1] = "SS";
                $default_shifts[-2] = "PH";

                $record = $this->shifts_m->getEmployeeSchedules("tms.*, tsc.shift_code, tsc.shift_color, CONCAT(LPAD(tsc.shift_hr_from,2,'0'),':',LPAD(tsc.shift_min_from,2,'0')) shift_from, CONCAT(LPAD(tsc.shift_hr_to,2,'0'),':',LPAD(tsc.shift_min_to,2,'0')) shift_to", "tms.mb_no = '" . $employee->mb_no . "' AND CONCAT(year,'-',month,'-',day) = '" . $tmp_date->format("Y-n-j") . "'");

                $emp_data = array(
                    "mb_id" => $employee->mb_id,
                    "mb_nick" => ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname) . " " . $employee->mb_lname,
                    "dept_name" => $employee->dept_name,
                    "day" => $tmp_date->format("Y-m-d"),
                    "shift" => "N/A",
                    "time_in" => "-",
                    "time_out" => "-"
                );

                if (count($record)) {
                    $emp_data["shift"] = ($record[0]->shift_id < 1) ? (isset($default_shifts[$record[0]->shift_id]) ? $default_shifts[$record[0]->shift_id] : "N/A") : $record[0]->shift_code;
                    $emp_data["time_in"] = ($record[0]->shift_id < 1) ? "-" : $record[0]->shift_from;
                    $emp_data["time_out"] = ($record[0]->shift_id < 1) ? "-" : $record[0]->shift_to;
                }
                $tmp_date->modify("+1 day");
                $return_arr[] = $emp_data;
            }
        }
        echo json_encode(array("data" => $return_arr, "header" => $response_arr, "width" => $width_arr, "total_count" => $total_count, "page" => $page));
    }

    public function getEmpChangeSchedule() {
        $limit = $this->input->post("limit");
        $page = $this->input->post("page");
        $mb_no = $this->session->userdata("mb_no");
        $offset = ($page - 1) * $limit;

        $date_from = $this->input->post("dateFrom");
        $date_from = new DateTime($date_from . " 00:00:00");

        $date_to = $this->input->post("dateTo");
        $date_to = new DateTime($date_to . " 00:00:00");

        $employees = array(0 => (object) $this->employees_m->get($mb_no));
        $total_count = count($employees);
        $response_arr = $return_arr = array();
        $response_arr = array("Request ID", "Date From", "Date To", "Original Shift", "Requested Shift", "IN", "OUT", "Status", "Action");

        $date = new DateTime();
        foreach ($employees as $employee) {
            $default_shifts[0] = "RD";
            $default_shifts[-1] = "SS";
            $default_shifts[-2] = "PH";

            $record = $this->shifts_m->getEmployeeChangeSchedules("tcsq.*, gm.*, tcsq.orig_shift orig_shift, tsc2.shift_code new_shift_code, tsc2.shift_color new_shift_color, CONCAT(LPAD(tsc2.shift_hr_from,2,'0'),':',LPAD(tsc2.shift_min_from,2,'0')) new_shift_from , CONCAT(LPAD(tsc2.shift_hr_to,2,'0'),':',LPAD(tsc2.shift_min_to,2,'0')) new_shift_to, CASE tcsq.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END status_lbl", "gm.mb_no = '" . $employee->mb_no . "' AND (att_date_from BETWEEN '" . $date_from->format("Y-m-d") . "' AND '" . $date_to->format("Y-m-d") . "' OR att_date_to BETWEEN '" . $date_from->format("Y-m-d") . "' AND '" . $date_to->format("Y-m-d") . "')");


            foreach ($record as $data) {

                $allow_view = false;
                $allow_edit = false;
                $allow_submit = false;
                $allow_delete = false;
                $allow_cancel = false;

                switch ($data->status) {
                    case 0 :
                        $allow_edit = true;
                        $allow_submit = true;
                        $allow_delete = true;
                        break;
                    case 1 :
                        $allow_view = true;
                        $allow_delete = true;
                        break;
                    case 2 :
                        // $allow_edit = true;
                        // $allow_submit = true;
                        // $allow_cancel = true;
                        $allow_view = true;
                        break;
                    case 3 :
                        $allow_view = true;
                        $allow_cancel = false;
                        break;
                    case 4 :
                        $allow_view = true;
                        break;
                }

                $tmp_date = new DateTime($data->att_date_from);
                $emp_data = array(
                    "req_id" => $data->cs_req_id,
                    "day_from" => $data->att_date_from,
                    "day_to" => $data->att_date_to,
                    "org_shift" => $data->orig_shift, // str_replace("<br/>","\n",$data->orig_shift),
                    "new_shift" => ($data->proposed_shift_id < 1) ? (isset($default_shifts[$data->proposed_shift_id]) ? $default_shifts[$data->proposed_shift_id] : "N/A") : $data->new_shift_code,
                    "new_time_in" => ($data->proposed_shift_id < 1) ? "-" : $data->new_shift_from,
                    "new_time_out" => ($data->proposed_shift_id < 1) ? "-" : $data->new_shift_to,
                    "status" => $data->status_lbl,
                    "action" => '<div class="action-buttons">' .
                    ($allow_view ? '<a class="green request-view" href="#" data-id="' . $data->cs_req_id . '" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>' : '') .
                    /* ($allow_edit?'<a class="blue request-edit" href="#" data-id="'.$data->cs_req_id.'" title="Edit"><i class="ace-icon fa fa-edit bigger-130"></i></a>':'').
                      ($allow_submit?'<a class="green request-submit" href="#" data-id="'.$data->cs_req_id.'" title="Submit"><i class="ace-icon fa fa-share-square-o bigger-130"></i></a>':''). */
                    ($allow_delete ? '<a class="red request-remove" href="#" data-id="' . $data->cs_req_id . '" title="Delete"><i class="ace-icon fa fa-trash-o bigger-130"></i></a>' : '') .
                    (($allow_cancel && $data->dirty_bit_ind == 1 && $date->format("Ymd") <= $tmp_date->format("Ymd")) ? '<a class="red request-cancel" href="#" data-id="' . $data->cs_req_id . '" title="Cancel"><i class="ace-icon fa fa-close bigger-130"></i></a>' : '') .
                    '</div>'
                );
                $return_arr[] = $emp_data;
            }
        }
        echo json_encode(array("data" => $return_arr, "header" => $response_arr, "total_count" => count($return_arr) == 0 ? 0 : $total_count, "page" => $page));
    }

    public function getChangeShift() {
        $cs_req_id = $this->input->post("request_id");
        $data = $this->shifts_m->getEmployeeChangeSchedules("tcsq.*, gm.*, tcsq.orig_shift orig_shift, tsc2.shift_code new_shift_code, tsc2.shift_color new_shift_color,  CONCAT(LPAD(tsc2.shift_hr_from,2,'0'),':',LPAD(tsc2.shift_min_from,2,'0')) new_shift_from , CONCAT(LPAD(tsc2.shift_hr_to,2,'0'),':',LPAD(tsc2.shift_min_to,2,'0')) new_shift_to, CASE tcsq.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' END status_lbl", "tcsq.cs_req_id = '" . $cs_req_id . "'");
        $remarks = $this->shifts_m->getEmployeeChangeSchedulesRemarks("tcsh.*,CONCAT(IF(gm.mb_3='Local',gm.mb_fname,gm.mb_nick),' ',gm.mb_lname) mb_nick", "tcsh.cs_req_id = '" . $cs_req_id . "'");
        echo json_encode(array("success" => 1, "data" => $data, "remarks" => $remarks));
    }

    public function getEmpShift() {
        $date_from = $this->input->post("dateFrom");
        $date_from = new DateTime($date_from . " 00:00:00");
        $mb_no = $this->session->userdata("mb_no");

        $date_to = $this->input->post("dateTo");
        $date_to = new DateTime($date_to . " 00:00:00");

        $return_arr = array();
        $default_shifts[0] = array("shift_id" => 0, "shift_code" => "RD", "shift_sched" => "Rest Day", "shift_color" => "1B7935");
        $default_shifts[-1] = array("shift_id" => -1, "shift_code" => "SS", "shift_sched" => "Suspension", "shift_color" => "FA4747");
        $default_shifts[-2] = array("shift_id" => -2, "shift_code" => "PH", "shift_sched" => "Holiday", "shift_color" => "D87947");

        $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));
        while ($tmp_date <= $date_to) {
            $record = $this->shifts_m->getEmployeeSchedules("tms.*, tsc.shift_code, tsc.shift_color, CONCAT(LPAD(tsc.shift_hr_from,2,'0'),':',LPAD(tsc.shift_min_from,2,'0')) shift_from, CONCAT(LPAD(tsc.shift_hr_to,2,'0'),':',LPAD(tsc.shift_min_to,2,'0')) shift_to", "tms.mb_no = '" . $mb_no . "' AND CONCAT(year,'-',month,'-',day) = '" . $tmp_date->format("Y-n-j") . "'");
            if (count($record)) {
                foreach ($record as $shift) {
                    if (isset($default_shifts[$shift->shift_id])) {
                        $return_arr[$shift->shift_id] = array("shift_id" => $shift->shift_id, "shift_code" => $default_shifts[$shift->shift_id]["shift_code"], "shift_time" => "[" . $default_shifts[$shift->shift_id]["shift_sched"] . "]");
                    } else {
                        $return_arr[$shift->shift_id] = array("shift_id" => $shift->shift_id, "shift_code" => $shift->shift_code, "shift_time" => "[" . $shift->shift_from . "-" . $shift->shift_to . "]");
                    }
                }
            } else {
                $return_arr["-10"] = array("shift_id" => "-10", "shift_code" => "N/A", "shift_time" => "[N/A]");
            }
            $tmp_date->modify("+1 day");
        }
        echo json_encode(array("data" => $return_arr));
    }

    public function saveChangeShift() {
        $date = new DateTime();
        $mb_no = $this->session->userdata("mb_no");
        if ($mb_no) {
            $post = $this->input->post();
            $request_id = $post['request-id'];
            $date_from = $post['att-date-from'];
            $date_to = $post['att-date-to'];
            $org_shift_id = $post['orig-shift-ids-str'];
            $org_shift_str = $post['orig-shift-str'];
            $new_shift_id = $post['new-shift'];
            $reason = $post['reason'];

            $emp_dtl = $this->employees_m->get($mb_no);
            $apprv_grp_id = 0;
            if (count($emp_dtl)) {
                $apprv_grp_id = $emp_dtl['mb_cws_app_grp_id'];
            }
            $date_from = new DateTime($date_from . " 00:00:00");
            $date_to = new DateTime($date_to . " 00:00:00");
            if ($date_to < $date_from) {
                echo json_encode(array("success" => 0, "msg" => "Invalid date range. Please review."));
                return false;
            }

            $record = $this->shifts_m->getEmployeeChangeSchedules("*", "gm.mb_no = '" . $mb_no . "' AND (att_date_from BETWEEN '" . $date_from->format("Y-m-d") . "' AND '" . $date_to->format("Y-m-d") . "' OR att_date_to BETWEEN '" . $date_from->format("Y-m-d") . "' AND '" . $date_to->format("Y-m-d") . "') AND tcsq.status NOT IN(2,3,4) AND tcsq.cs_req_id <> '" . $request_id . "'");

            if (count($record)) {
                echo json_encode(array("success" => 0, "msg" => "Duplicate request found for dates. Please review."));
                return false;
            } else {
                $approver2_dtl = $this->shifts_m->getCWSApprovalGroupApprover($apprv_grp_id, "*", array("taga.mb_id" => $mb_no));
                if (count($approver2_dtl))
                    $approver_dtl = $this->shifts_m->getCWSApprovalGroupApprover($approver2_dtl[0]->cws_apprv_grp_id, "MIN(level) level", array("level >" => $approver2_dtl[0]->level));
                else
                    $approver_dtl = $this->shifts_m->getCWSApprovalGroupApprover($apprv_grp_id, "MIN(level) level");

                // Insert
                $success = $this->shifts_m->insertChangeShift(array(
                    "mb_no" => $mb_no,
                    "apprv_grp_id" => $apprv_grp_id,
                    "att_date_from" => $date_from->format("Y-m-d"),
                    "att_date_to" => $date_to->format("Y-m-d"),
                    "orig_shift_ids" => $org_shift_id,
                    "orig_shift" => $org_shift_str,
                    "proposed_shift_id" => $new_shift_id,
                    "reason" => $reason,
                    "status" => 1,
                    "created_datetime" => $date->format("Y-m-d H:i:s"),
                    "submitted_datetime" => $date->format("Y-m-d H:i:s")
                ));
                $cws_app_id = $this->shifts_m->lastID();
            }

            if ($success) {
                if (count($approver_dtl) && $approver_dtl[0]->level) {
                    $data = array("cs_req_id" => $cws_app_id,
                        "apprv_grp_id" => $apprv_grp_id,
                        "date_from" => $date_from->format("Y-m-d"),
                        "date_to" => $date_to->format("Y-m-d"),
                        "approved_level" => $approver_dtl[0]->level,
                        "submitted_by" => $mb_no,
                        "status" => 1,
                        "created_by" => $mb_no,
                        "created_datetime" => $date->format("Y-m-d H:i:s"),
                        "updated_by" => $mb_no,
                        "updated_datetime" => $date->format("Y-m-d H:i:s"));
                    $success = $this->shifts_m->insertForApprovalChangeShift($data);
                    if ($success) {
                        // Notification
                        $this->ws->load('notifs');
                        $approver_dtl = $this->shifts_m->getCWSApprovalGroupApprover($apprv_grp_id, "taga.*", array("level" => $approver_dtl[0]->level));
                        foreach ($approver_dtl as $approver) {
                            $recipient = $approver->mb_id;
                            $date = new DateTime();
                            NOTIFS::publish("APPROVAL_$recipient", array(
                                'type' => "CWS",
                                'count' => 1
                            ));
                        }
                        //
                        $data = array("cs_req_id" => $cws_app_id,
                            "status" => 1,
                            "remarks" => "Submitted for approval",
                            "created_by" => $mb_no,
                            "created_datetime" => $date->format("Y-m-d H:i:s"));
                        $success = $this->shifts_m->insertForApprovalChangeShiftHist($data);
                        echo json_encode(array("success" => 1, "msg" => "Schedule Submitted!"));
                    } else
                        echo json_encode(array("success" => 0, "msg" => "A database error occured. Please contact system administrator."));
                }
                else {
                    $this->shifts_m->updateChangeShift(array("status" => 3, "dirty_bit_ind" => 1), array("cs_req_id" => $cws_app_id));

                    $date_str_from = $date_from->format("Ymd");
                    $date_str_to = $date_to->format("Ymd");
                    $tmp_date = new DateTime($date_from->format("Y-m-d 00:00:00"));

                    while ($tmp_date <= $date_to) {
                        $shift_rec = $this->shifts_m->getEmployeeSchedules("*", "tms.`mb_no` = '" . $mb_no . "' AND `year` = '" . $tmp_date->format("Y") . "' AND `month` = '" . $tmp_date->format("m") . "' AND `day` = '" . $tmp_date->format("d") . "' ");
                        if (count($shift_rec)) {
                            $this->shifts_m->updateMemberSchedule(
                                    array(
                                "shift_id" => $new_shift_id
                                    ), array(
                                "year" => $tmp_date->format("Y"),
                                "month" => $tmp_date->format("m"),
                                "day" => $tmp_date->format("d"),
                                "mb_no" => $mb_no
                                    )
                            );
                            if ($shift_rec[0]->lv_app_id) {
                                $request_dtl = $this->leaves_m->getEmpLeaveApplication("*, tla.status, DATE(tla.date_from) date_from, DATE(tla.date_to) date_to", "tla.lv_app_id = '" . $shift_rec[0]->lv_app_id . "'");
                                if ($request_dtl[0]->allocated > 0) {
									$date_from = new DateTime($request_dtl[0]->date_from);
                                    $leave_bal = $this->leaves_m->getEmpLeaveBalances($request_dtl[0]->mb_no, "*", $request_dtl[0]->leave_id,$date_from->format("Y"));
                                    if ($new_shift_id > 0) {
                                        $this->leaves_m->updateEmpLeaveBalances(array("allocated" => $leave_bal[0]->allocated - 1, "used" => $leave_bal[0]->used + 1), array("leave_id" => $request_dtl[0]->leave_id, "mb_no" => $request_dtl[0]->mb_no, "year"=>$date_from->format("Y")));
                                        $this->leaves_m->updateLeaveApplication(array("allocated" => $request_dtl[0]->allocated - 1, "used" => $request_dtl[0]->used + 1), array("lv_app_id" => $request_dtl[0]->lv_app_id));
                                    } else {
                                        $this->leaves_m->updateEmpLeaveBalances(array("allocated" => $leave_bal[0]->allocated - 1, "bal" => $leave_bal[0]->bal + 1), array("leave_id" => $request_dtl[0]->leave_id, "mb_no" => $request_dtl[0]->mb_no, "year"=>$date_from->format("Y")));
                                        $this->leaves_m->updateLeaveApplication(array("allocated" => $request_dtl[0]->allocated - 1), array("lv_app_id" => $request_dtl[0]->lv_app_id));
                                        $success = $this->shifts_m->updateMemberSchedule(
                                                array("leave_id" => 0, "lv_app_id" => 0,), array("tkms_id" => $shift_rec[0]->tkms_id)
                                        );
                                    }
                                }
                            }
                        } else {
                            $this->shifts_m->insertMemberSchedule(
                                    array(
                                        "shift_id" => $new_shift_id,
                                        "year" => $tmp_date->format("Y"),
                                        "month" => $tmp_date->format("m"),
                                        "day" => $tmp_date->format("d"),
                                        "mb_no" => $mb_no
                                    )
                            );
                        }
                        $tmp_date->modify("+1 day");
                    }

                    $data = array("cs_req_id" => $request_id,
                        "status" => 3,
                        "remarks" => "Approved",
                        "created_by" => $mb_no,
                        "created_datetime" => $date->format("Y-m-d H:i:s"));
                    $success = $this->shifts_m->insertForApprovalChangeShiftHist($data);

                    /* Update Attendance */
                    $this->updateAttendance($mb_no, $date_str_from, $date_str_to);
                    /* End Update Attendance */

                    echo json_encode(array("success" => 1, "msg" => "Schedule Submitted!"));
                }
            } else {
                echo json_encode(array("success" => 0, "msg" => "A database error occured. Please contact system administrator."));
            }
        } else {
            echo json_encode(array("success" => 0, "msg" => "You have been logged out. Please reload the page."));
        }
    }

    public function deleteChangeShift() {
        $date = new DateTime();
        $request_id = $this->input->post("request_id");
        $param = array("cs_req_id" => $request_id);
        $request_dtl = $this->shifts_m->getEmployeeChangeSchedules("tcsq.*", array("tcsq.cs_req_id" => $request_id));
        if ($request_dtl[0]->dirty_bit_ind) {
            $success = $this->shifts_m->updateChangeShift(array("status" => 4), $param);
            $data = array("cs_req_id" => $request_id,
                "status" => 4,
                "remarks" => "Cancelled",
                "created_by" => $this->session->userdata("mb_no"),
                "created_datetime" => $date->format("Y-m-d H:i:s"));
            $success = $this->shifts_m->insertForApprovalChangeShiftHist($data);
        } else
            $success = $this->shifts_m->deleteChangeShift($param);

        if ($success) {
            $approver_dtl = $this->shifts_m->getAllForApprovalChangeShiftFiltered("tcsa.*", "tcsa.cs_req_id = '" . $request_id . "'");
            $success = $this->shifts_m->deleteForApprovalChangeShift($param);
            if ($success && count($approver_dtl)) {
                // Notification
                $this->ws->load('notifs');
                $approver_dtl = $this->shifts_m->getApprovalGroupApprover($request_dtl[0]->apprv_grp_id, "taga.*", array("level" => $approver_dtl[0]->approved_level));
                foreach ($approver_dtl as $approver) {
                    $recipient = $approver->mb_id;
                    $date = new DateTime();
                    NOTIFS::publish("APPROVAL_$recipient", array(
                        'type' => "CWS",
                        'count' => -1
                    ));
                }
                //
            }
            echo json_encode(array("success" => 1, "msg" => "Schedule Deleted!"));
        } else {
            echo json_encode(array("success" => 0, "msg" => "A database error occured. Please contact system administrator."));
        }
    }

    public function cancelChangeShift() {
        $date = new DateTime();
        $request_id = $this->input->post("request_id");
        $param = array("cs_req_id" => $request_id);
        $request_dtl = $this->shifts_m->getEmployeeChangeSchedules("tcsq.*", array("tcsq.cs_req_id" => $request_id));
        $success = $this->shifts_m->updateChangeShift(array("status" => 4), $param);
        $data = array("cs_req_id" => $request_id,
            "status" => 4,
            "remarks" => "Cancelled",
            "created_by" => $this->session->userdata("mb_no"),
            "created_datetime" => $date->format("Y-m-d H:i:s"));
        $success = $this->shifts_m->insertForApprovalChangeShiftHist($data);

        if ($success) {
            $approver_dtl = $this->shifts_m->getAllForApprovalChangeShiftFiltered("tcsa.*", "tcsa.cs_req_id = '" . $request_id . "'");
            $success = $this->shifts_m->deleteForApprovalChangeShift($param);
            if ($success && count($approver_dtl)) {
                // Notification
                $this->ws->load('notifs');
                $approver_dtl = $this->shifts_m->getCWSApprovalGroupApprover($request_dtl[0]->apprv_grp_id, "taga.*", array("level" => $approver_dtl[0]->approved_level));
                foreach ($approver_dtl as $approver) {
                    $recipient = $approver->mb_id;
                    $date = new DateTime();
                    NOTIFS::publish("APPROVAL_$recipient", array(
                        'type' => "CWS",
                        'count' => -1
                    ));
                }
                //
            }
            if ($success) {
                /* Update Attendance */
                $this->updateAttendance($this->session->userdata("mb_no"), $date_str_from, $date_str_to);
                /* End Update Attendance */
            }
            echo json_encode(array("success" => 1, "msg" => "Change of Schedule Cancelled!"));
        } else {
            echo json_encode(array("success" => 0, "msg" => "A database error occured. Please contact system administrator."));
        }
    }

    /* End of Change of Schedule */

    /* Settings */

    public function updateGeneralSettings() {
        $post = $this->input->post();
        $resp = $this->shifts_m->updateGeneralSettings(array("default_period" => $post['default_period'], "default_sched_day" => $post['default_sched_day']), array());
        if ($resp) {
            echo json_encode(array("success" => 1, "msg" => "Record updated"));
        } else {
            echo json_encode(array("success" => 0, "msg" => "A database error occured"));
        }
    }

    /* End of Settings */


    /* Upload Special Schedule */

    public function uploadSpecialSchedule() {
        $date = new DateTime();
        $post = $this->input->post();
        $files = $_FILES;

        $config = array();
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xls';
        $config['encrypt_name'] = true;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload("schedule-file")) {
            echo json_encode(array("success" => 0, "message" => $this->upload->display_errors("", "")));
        } else {
            $file_info = $this->upload->data();
            $file_info['file_path'] = "uploads/" . $file_info['raw_name'] . $file_info['file_ext'];
            $file_info['org_file'] = $file_info['client_name'];
            $file_info['period_from'] = $post['period-start'];
            $file_info['period_to'] = $post['period-end'];
            $file_info['created_datetime'] = $date->format("Y-m-d H:i:s");
            $file_info['created_by'] = $this->session->userdata("mb_no");
            $file_info['updated_datetime'] = $date->format("Y-m-d H:i:s");
            $file_info['updated_by'] = $this->session->userdata("mb_no");
            unset($file_info['full_path']);
            unset($file_info['raw_name']);
            unset($file_info['orig_name']);
            unset($file_info['client_name']);
            unset($file_info['is_image']);
            unset($file_info['image_width']);
            unset($file_info['image_height']);
            unset($file_info['image_type']);
            unset($file_info['image_size_str']);

            $shifts_dtl = $this->shifts_m->getAll(false, "*, CONCAT(LPAD(shift_hr_from,2,'0'),':',LPAD(shift_min_from,2,'0'),'-',LPAD(shift_hr_to,2,'0'),':',LPAD(shift_min_to,2,'0')) shift_sched, shift_color");

            $shifts_dtl[] = (object) array("shift_id" => 0, "shift_code" => "RD", "shift_sched" => "Rest Day", "shift_color" => "1B7935", "sched_depts" => "all");
            $shifts_dtl[] = (object) array("shift_id" => -1, "shift_code" => "SS", "shift_sched" => "Suspension", "shift_color" => "FA4747", "sched_depts" => "all");
            $shifts_dtl[] = (object) array("shift_id" => -2, "shift_code" => "PH", "shift_sched" => "Holiday", "shift_color" => "D87947", "sched_depts" => "all");
            $shifts_list = array();
            $shifts_dept = array();
            foreach ($shifts_dtl as $shift) {
                $shifts_list[$shift->shift_id] = $shift->shift_code;
                $shifts_dept[$shift->shift_code] = $shift->sched_depts;
            }

            $reader = $file_info["file_ext"] == ".xlsx" ? "Excel2007" : "Excel5";
            //load our new PHPExcel library
            $this->load->library('excel');
            $objReader = PHPExcel_IOFactory::createReader($reader);
            $objPHPExcel = $objReader->load(dirname(__FILE__) . "/../../" . $file_info['file_path']);
            $objPHPExcel->setActiveSheetIndex();
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnInd = PHPExcel_Cell::columnIndexFromString($highestColumn);

            $dataArray = array();
            //$mapArray = array();
            $rownumber = 1;
            while ($rownumber <= $highestRow) {
                $row = $objWorksheet->getRowIterator($rownumber)->current();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                // Month Year Checking
                if ($rownumber == 1) {
                    $year = $month = "";
                    foreach ($cellIterator as $col => $cell) {
                        $cellValue = $cell->getValue();

                        if (!empty($year)) {
                            $dataArray[$cell->getColumn()]['month'] = $month;
                            $dataArray[$cell->getColumn()]['year'] = $year;
                        }

                        if (empty($cellValue))
                            continue;
                        else {
                            $cellValueArr = explode(" ", $cellValue);
                            if (count($cellValueArr) != 2) {
                                echo json_encode(array(
                                    "success" => 0,
                                    "msg" => "<br/>Invalid Format. Must be \"Month Year\" Only (ex. \"" . $date->format("F Y") . "\". <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                ));
                                die();
                            }
                            $cellmonth = strtolower($cellValueArr[0]);
                            $cellyear = strtolower($cellValueArr[1]);

                            if (!isset($this->month_list[$cellmonth])) {
                                echo json_encode(array(
                                    "success" => 0,
                                    "msg" => "<br/>Invalid Month specified. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                ));
                                die();
                            }

                            if (!is_numeric($cellyear) || $cellyear < 2014 || $cellyear > (($date->format("Y") * 1) + 1)) {
                                echo json_encode(array(
                                    "success" => 0,
                                    "msg" => "<br/>Invalid Year specified. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                ));
                                die();
                            }

                            if ($cellmonth != $month) {
                                $dataArray[$cell->getColumn()]['month'] = $this->month_list[$cellmonth];
                                $dataArray[$cell->getColumn()]['year'] = $cellyear;
                                $month = $this->month_list[$cellmonth];
                                $year = $cellyear;
                            }
                        }
                    }
                } else // Month Year Checking
                if ($rownumber == 2) {
                    $month_31 = array("01", "03", "05", "07", "08", "10", "12");
                    $month_30 = array("04", "06", "09", "11");
                    foreach ($cellIterator as $col => $cell) {
                        if ($col < 2)
                            continue;
                        $cellValue = $cell->getValue();
                        if (
                                !is_numeric($cellValue) ||
                                $cellValue < 1 ||
                                (in_array($dataArray[$cell->getColumn()]['month'], $month_31) && $cellValue > 31) ||
                                (in_array($dataArray[$cell->getColumn()]['month'], $month_30) && $cellValue > 30) ||
                                (
                                $dataArray[$cell->getColumn()]['month'] == "02" &&
                                (
                                ($cellValue > 28 && $dataArray[$cell->getColumn()]['year'] % 4 == 0) ||
                                ($cellValue > 29 && $dataArray[$cell->getColumn()]['year'] % 4 > 0)
                                )
                                )
                        ) {
                            echo json_encode(array(
                                "success" => 0,
                                "msg" => "<br/>Invalid Day specified. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                            ));
                            die();
                        }
                        $dataArray[$cell->getColumn()]['day'] = $cellValue;
                    }
                } else
                if ($rownumber > 3) {
                    $user = "";
                    $sched_user = array();
                    foreach ($cellIterator as $cell) {
                        $cellValue = $cell->getValue();
                        if ($cell->getColumn() == "B")
                            continue;
                        if ($cell->getColumn() == "A") {
                            $user = $cellValue;
                            if (empty($cellValue))
                                break;
                            $user_dtl = $this->employees_m->getById($user);

                            if (count($user_dtl)) {
                                $user = $user_dtl->mb_no;
                                $shifts_dtl = $this->shifts_m->getAll(false, "*, CONCAT(LPAD(shift_hr_from,2,'0'),':',LPAD(shift_min_from,2,'0'),'-',LPAD(shift_hr_to,2,'0'),':',LPAD(shift_min_to,2,'0')) shift_sched, shift_color", "FIND_IN_SET('" . $user_dtl->mb_no . "', sched_users)");
                            } else {
                                echo json_encode(array(
                                    "success" => 0,
                                    "msg" => "<br/>Employee ID does not exists. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                ));
                                die();
                            }
                            continue;
                        }
                        if (!in_array($cellValue, $shifts_list) && $cellValue != "-") {
                            echo json_encode(array(
                                "success" => 0,
                                "msg" => "<br/>Invalid Shift Code. <br/>Valid values [" . implode(", ", $shifts_list) . "]. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                            ));
                            die();
                        } else {
                            $shifts_dept_arr = explode(",", $shifts_dept[$cellValue]);
                            if (count($shifts_dtl)) {
                                $valid = false;
                                foreach ($shifts_dtl as $shift) {
                                    if ($shift->shift_code == $cellValue) {
                                        $valid = true;
                                        break;
                                    }
                                }
                                if ($valid == false && array_search($cellValue, $shifts_list) > 0) {
                                    echo json_encode(array(
                                        "success" => 0,
                                        "msg" => "<br/>Shift Code '$cellValue' not allowed for employee. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                    ));
                                    die();
                                }
                            } else if (!in_array($user_dtl->mb_deptno, $shifts_dept_arr) && $shifts_dept[$cellValue] != "all" && $cellValue != "-") {
                                echo json_encode(array(
                                    "success" => 0,
                                    "msg" => "<br/>Shift Code '$cellValue' not allowed for employee. <br/><strong>Cell : " . $cell->getCoordinate() . "</strong>"
                                ));
                                die();
                            }
                        }
                        if ($cellValue == "-")
                            $dataArray[$cell->getColumn()]['users'][$user] = null;
                        else
                            $dataArray[$cell->getColumn()]['users'][$user] = array_search($cellValue, $shifts_list);
                    }
                }
                $rownumber++;
            }

            $success = $this->shifts_m->insertSpecialSchedUpload($file_info);

            if ($success) {
                echo json_encode(array("success" => 1, "msg" => "Schedule has been uploaded"));
            } else {
                echo json_encode(array("success" => 0, "msg" => "<br/>A database error occured. <br/>Please contact system administrator."));
            }
        }
    }

    public function submitSpecialSchedule() {
        $date = new DateTime();
        $post = $this->input->post();
        $this->shifts_m->updateSpecialSchedUpload(array("status" => 3, "dirty_bit_ind" => 1), array("upload_id" => $post['upload_id']));

        $shifts_dtl = $this->shifts_m->getAll(false, "*");

        $shifts_dtl[] = (object) array("shift_id" => 0, "shift_code" => "RD");
        $shifts_dtl[] = (object) array("shift_id" => -1, "shift_code" => "SS");
        $shifts_dtl[] = (object) array("shift_id" => -2, "shift_code" => "PH");
        $shifts_list = array();
        foreach ($shifts_dtl as $shift) {
            $shifts_list[$shift->shift_id] = $shift->shift_code;
        }

        $upload_dtl = $this->shifts_m->getAllSpecialUploadsFiltered("*", "upload_id ='" . $post['upload_id'] . "'");

        $reader = $upload_dtl[0]->file_ext == ".xlsx" ? "Excel2007" : "Excel5";
        //load our new PHPExcel library
        $this->load->library('excel');
        $objReader = PHPExcel_IOFactory::createReader($reader);
        $objPHPExcel = $objReader->load(dirname(__FILE__) . "/../../" . $upload_dtl[0]->file_path);
        $objPHPExcel->setActiveSheetIndex();
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnInd = PHPExcel_Cell::columnIndexFromString($highestColumn);

        $dataArray = array();
        //$mapArray = array();
        $rownumber = 1;
        $emp_list = array();
        $date_from = "";
        $date_to = "";
        while ($rownumber <= $highestRow) {
            $row = $objWorksheet->getRowIterator($rownumber)->current();
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            // Month Year Checking
            if ($rownumber == 1) {
                $year = $month = "";
                foreach ($cellIterator as $col => $cell) {
                    $cellValue = $cell->getValue();

                    if (!empty($year)) {
                        $dataArray[$cell->getColumn()]['month'] = $month;
                        $dataArray[$cell->getColumn()]['year'] = $year;
                    }

                    if (empty($cellValue))
                        continue;
                    else {
                        $cellValueArr = explode(" ", $cellValue);
                        $cellmonth = strtolower($cellValueArr[0]);
                        $cellyear = strtolower($cellValueArr[1]);
                        if ($cellmonth != $month) {
                            $dataArray[$cell->getColumn()]['month'] = $this->month_list[$cellmonth];
                            $dataArray[$cell->getColumn()]['year'] = $cellyear;
                            $month = $this->month_list[$cellmonth];
                            $year = $cellyear;
                        }
                    }
                }
            } else // Month Year Checking
            if ($rownumber == 2) {
                $month_31 = array("01", "03", "05", "07", "08", "10", "12");
                $month_30 = array("04", "06", "09", "11");
                foreach ($cellIterator as $col => $cell) {
                    if ($col < 2)
                        continue;
                    $cellValue = $cell->getValue();
                    $dataArray[$cell->getColumn()]['day'] = $cellValue;
                }
            }
            else
            if ($rownumber > 3) {
                $user = "";
                foreach ($cellIterator as $cell) {
                    $cellValue = $cell->getValue();
                    if ($cell->getColumn() == "B")
                        continue;
                    if ($cell->getColumn() == "A") {
                        $user = $cellValue;
                        if (empty($cellValue))
                            break;
                        $user_dtl = $this->employees_m->getById($user);
                        if (count($user_dtl)) {
                            $user = $user_dtl->mb_no;
                            $emp_list[] = $user_dtl->mb_no;
                        }
                        continue;
                    }
                    if ($cell->getColumn() == "C") {
                        $date_from = $dataArray[$cell->getColumn()]['year'] . (str_pad($dataArray[$cell->getColumn()]['month'], 2, "0", STR_PAD_LEFT)) . (str_pad($dataArray[$cell->getColumn()]['day'], 2, "0", STR_PAD_LEFT));
                        $date_to = $dataArray[$cell->getColumn()]['year'] . (str_pad($dataArray[$cell->getColumn()]['month'], 2, "0", STR_PAD_LEFT)) . (str_pad($dataArray[$cell->getColumn()]['day'], 2, "0", STR_PAD_LEFT));
                    } else {
                        $date_to = $dataArray[$cell->getColumn()]['year'] . (str_pad($dataArray[$cell->getColumn()]['month'], 2, "0", STR_PAD_LEFT)) . (str_pad($dataArray[$cell->getColumn()]['day'], 2, "0", STR_PAD_LEFT));
                    }
                    $sched_dtl = $this->shifts_m->getAllMemberScheduleFiltered("*", "`mb_no` = '" . $user . "' AND `year` = '" . $dataArray[$cell->getColumn()]['year'] . "' AND `month` = '" . $dataArray[$cell->getColumn()]['month'] . "' AND `day` = '" . $dataArray[$cell->getColumn()]['day'] . "'");
                    $shift_id = $cellValue == "-" ? null : array_search($cellValue, $shifts_list);
                    if ($sched_dtl) {
                        $success = $this->shifts_m->updateMemberSchedule(
                                array("shift_id" => $shift_id), array("tkms_id" => $sched_dtl[0]->tkms_id)
                        );
                        if ($sched_dtl[0]->lv_app_id) {
                            $request_dtl = $this->leaves_m->getEmpLeaveApplication("*, tla.status, DATE(tla.date_from) date_from, DATE(tla.date_to) date_to", "tla.lv_app_id = '" . $sched_dtl[0]->lv_app_id . "'");
                            if ($request_dtl[0]->allocated > 0) {
								$date_from = new DateTime($request_dtl[0]->date_from);
								$leave_bal = $this->leaves_m->getEmpLeaveBalances($request_dtl[0]->mb_no, "*", $request_dtl[0]->leave_id,$date_from->format("Y"));
                                if (array_search($cellValue, $shifts_list) > 0) {
                                    $this->leaves_m->updateEmpLeaveBalances(array("allocated" => $leave_bal[0]->allocated - 1, "used" => $leave_bal[0]->used + 1), array("leave_id" => $request_dtl[0]->leave_id, "mb_no" => $request_dtl[0]->mb_no, "year"=>$date_from->format("Y")));
                                    $this->leaves_m->updateLeaveApplication(array("allocated" => $request_dtl[0]->allocated - 1, "used" => $request_dtl[0]->used + 1), array("lv_app_id" => $request_dtl[0]->lv_app_id));
                                } else {
                                    $this->leaves_m->updateEmpLeaveBalances(array("allocated" => $leave_bal[0]->allocated - 1, "bal" => $leave_bal[0]->bal + 1), array("leave_id" => $request_dtl[0]->leave_id, "mb_no" => $request_dtl[0]->mb_no, "year"=>$date_from->format("Y")));
                                    $this->leaves_m->updateLeaveApplication(array("allocated" => $request_dtl[0]->allocated - 1), array("lv_app_id" => $request_dtl[0]->lv_app_id));
                                    $success = $this->shifts_m->updateMemberSchedule(
                                            array("leave_id" => 0, "lv_app_id" => 0), array("tkms_id" => $sched_dtl[0]->tkms_id)
                                    );
                                }
                            } else if ($request_dtl[0]->used > 0) {
								$date_from = new DateTime($request_dtl[0]->date_from);
								$leave_bal = $this->leaves_m->getEmpLeaveBalances($request_dtl[0]->mb_no, "*", $request_dtl[0]->leave_id,$date_from->format("Y"));
                                if (array_search($cellValue, $shifts_list) <= 0) {
                                    $this->leaves_m->updateEmpLeaveBalances(array("used" => $leave_bal[0]->used - 1, "bal" => $leave_bal[0]->bal + 1), array("leave_id" => $request_dtl[0]->leave_id, "mb_no" => $request_dtl[0]->mb_no, "year"=>$date_from->format("Y")));
                                    $this->leaves_m->updateLeaveApplication(array("used" => $request_dtl[0]->used - 1), array("lv_app_id" => $request_dtl[0]->lv_app_id));
                                    $success = $this->shifts_m->updateMemberSchedule(
                                            array("leave_id" => 0, "lv_app_id" => 0), array("tkms_id" => $sched_dtl[0]->tkms_id)
                                    );
                                }
                            }
                        }
                    } else {
                        $success = $this->shifts_m->insertMemberSchedule(array(
                            "mb_no" => $user,
                            "year" => $dataArray[$cell->getColumn()]['year'],
                            "month" => $dataArray[$cell->getColumn()]['month'],
                            "day" => $dataArray[$cell->getColumn()]['day'],
                            "shift_id" => $shift_id
                        ));
                    }
                }
            }
            $rownumber++;
        }


        if (!empty($date_from) && !empty($date_to)) {
            foreach ($emp_list as $mb_no) {
                $this->updateAttendance($mb_no, $date_from, $date_to);
            }
        }
        echo json_encode(array("success" => 1, "msg" => "Schedule Uploaded!"));
    }

    public function deleteSpecialSchedule() {
        $upload_id = $this->input->post("upload_id");
        $param = array("upload_id" => $upload_id);
        $upload_dtl = $this->shifts_m->getAllSpecialUploadsFiltered("tsu.*", array("tsu.upload_id" => $upload_id));
        if ($upload_dtl[0]->dirty_bit_ind)
            $success = $this->shifts_m->updateSpecialSchedUpload(array("status" => 4), $param);
        else
            $success = $this->shifts_m->deleteSpecialSchedUpload($param);

        if ($success) {
            unlink(dirname(__FILE__) . "/../../" . $upload_dtl[0]->file_path);
            echo json_encode(array("success" => 1, "msg" => "Schedule Deleted!"));
        } else {
            echo json_encode(array("success" => 0, "msg" => "A database error occured. Please contact system administrator."));
        }
    }

    public function getAllSpecialUploads() {
        $post = $this->input->post();
        $order_arr = array();
        if (is_array($post['order'])) {
            foreach ($post['order'] as $orderDtl) {
                $order_arr[$post['columns'][$orderDtl['column']]['data']] = $orderDtl['dir'];
            }
        }

        $having_str = "";
        if (!empty($post['search']['value'])) {
            foreach ($post['columns'] as $column) {
                if ($column['searchable'] == "true") {
                    $having_str .= (empty($having_str) ? "" : " OR ") . $column['data'] . " LIKE '%" . $post['search']['value'] . "%' ";
                }
            }
        }

        $select_str = "tsu.*, gm.mb_nick, CASE tsu.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END status_lbl, CONCAT(tsu.period_from,' ~ ',tsu.period_to) period ";

        $data = $this->shifts_m->getAllSpecialUploadsFiltered($select_str);
        $all_upload_count = count($data);

        $data_all = $this->shifts_m->getAllSpecialUploadsFiltered($select_str, $having_str);
        $all_filtered_count = count($data_all);

        $data = $this->shifts_m->getAllSpecialUploadsFiltered($select_str, $having_str, $post['start'], $post['length'], $order_arr);

        echo json_encode(array("data" => $data,
            "draw" => (int) $post["draw"],
            "recordsTotal" => $all_upload_count,
            "recordsFiltered" => $all_filtered_count));
    }

    /* End of Schedule */

    /* General Public Functions */

    public function download_sp_template() {
        $start_date = $this->input->post("period-start");
        $end_date = $this->input->post("period-end");
        $this->generateExportExcel($start_date, $end_date, false);
    }

    public function download_template($start_date = null, $end_date = null, $with_names = false) {
        $post = $this->input->post();
        $group_id = 0;
        if (isset($post['period-start']))
            $start_date = $post['period-start'];
        if (isset($post['period-end']))
            $end_date = $post['period-end'];
        if (isset($post['dl-group-id']))
            $group_id = $post['dl-group-id'];
        $this->generateExportExcel($start_date, $end_date, $group_id);
    }

    /* End of General Public Functions */

    /* Private Functions */

    private function headStyleRandom($ctr = 0) {
        //$colors = array("daeef3", "fde9d9", "6398f9", "7af97d", "f5754e", "f969c1"); 
        //$i= rand(0,count($colors));
        $color = ($ctr % 2 == 0) ? "fde9d9" : "daeef3";
        $style = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => $color),
                'font' => array('bold' => true)),
            'borders' => array('allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                )),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        return $style;
    }

    private function generateExportExcel($start_date, $end_date, $group_id = 0) {
        $this->load->library('excel');
        $activeSheetInd = -1;

        $column_start = 'A';
        $row_start = 1;
        $file_name = $start_date . " - " . $end_date . " Work Schedule.xls";
        $header_list = array();

        $activeSheet = $this->excel->setActiveSheetIndex( ++$activeSheetInd);
        $activeSheet->setTitle($start_date . " - " . $end_date); {// Styles
            $headerStyle = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c9c9c9'),
                    'font' => array('bold' => true)),
                'borders' => array('outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '000000'),
                    )),
                'alignment' => array('wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
            $headerStyleDays = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'c5d9f1')
                ),
                'borders' => array('outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '000000'),
                    )),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'font' => array('size' => "8")
            );
            $defaultSchedStyle = array('borders' => array('outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '000000'),
                    )),
                'alignment' => array('wrap' => true,
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
            $nameSchedStyle = array('borders' => array('outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '000000'),
                    )),
                'alignment' => array('wrap' => true,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );

            $labelStyle = array('font' => array('bold' => true));
        }//End of Styles
        //BLANK CELLS
        $merge_cells = 0;
        $start_cell = "A" . $row_start;
        $column_start = "A";
        for ($i = 1; $i <= 2; $i++) {
            $row_cel = $column_start . $row_start;
            $activeSheet->setCellValue($row_cel, "");
            $activeSheet->getStyle($row_cel)->applyFromArray($labelStyle);
            $merge_cells++;
            $column_start++;
        }//end for 

        if ($merge_cells > 1) {
            $activeSheet->mergeCells($start_cell . ":" . $row_cel);
            $activeSheet->getStyle($start_cell . ":" . $row_cel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }

        //MONTHS, YEAR
        $dt_from = new DateTime($start_date . " 00:00:00");
        $dt_to = new DateTime($end_date . " 00:00:00");
        $column_start_num = 2;
        $color_ctr = 1;
        while ($dt_from <= $dt_to) {
            $tmp_date = new DateTime($dt_from->format("Y") . "-" . $dt_from->format("m") . "-" . $dt_from->format("t"));
            if ($tmp_date > $dt_to)
                $tmp_date = new DateTime($dt_to->format("Y-m-d"));

            $diff = $dt_from->diff($tmp_date);

            if ($diff->format("%a") != 6015) {
                $total_merge = ($diff->format("%a") * 1) + 1;
            } else {
                // else let's use our own method

                $y1 = $dt_from->format('Y');
                $y2 = $tmp_date->format('Y');
                $z1 = $dt_from->format('z');
                $z2 = $tmp_date->format('z');

                $total_merge = abs(floor($y1 * 365.2425 + $z1) - floor($y2 * 365.2425 + $z2)) + 1;
            }

            $row_cell1 = $column_start . $row_start;
            $col_num = $total_merge + $column_start_num;
            // echo $col_num;
            $column_start = PHPExcel_Cell::stringFromColumnIndex($col_num - 1);
            $row_cell2 = $column_start . $row_start;

            $activeSheet->mergeCells($row_cell1 . ":" . $row_cell2);
            $activeSheet->getCell($row_cell1)->setValueExplicit($dt_from->format("M Y"), PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->getStyle($row_cell1 . ":" . $row_cell2)->applyFromArray($this->headStyleRandom($color_ctr));

            $column_start = PHPExcel_Cell::stringFromColumnIndex($col_num);
            $tmp_date->modify("+1 day");
            $dt_from = $tmp_date;
            $column_start_num += $total_merge;
            $color_ctr++;
        }
        //echo "asdasdasd";die();
        //END MONTHS, YEAR
        //Headers
        $column_start = "A";
        $row_start++;
        $header_list = array("ID", "Name");
        foreach ($header_list as $row => $val) {
            $row_cel = $column_start . $row_start;
            $row_cel2 = $column_start . ($row_start + 1);
            $activeSheet->mergeCells($row_cel . ":" . $row_cel2);
            //$activeSheet->setCellValue($row_cel,$val);
            $activeSheet->getCell($row_cel)->setValueExplicit($val, PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->getStyle($row_cel . ":" . $row_cel2)->applyFromArray($headerStyle);
            $column_start++;
        }

        $dt_from = new DateTime($start_date);
        while ($dt_from <= $dt_to) {
            $row_cel = $column_start . $row_start;
            $row_cel2 = $column_start . ($row_start + 1);
            $activeSheet->getCell($row_cel)->setValueExplicit($dt_from->format("d"), PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->getCell($row_cel2)->setValueExplicit($dt_from->format("D"), PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->getStyle($row_cel)->applyFromArray($headerStyle);
            $activeSheet->getStyle($row_cel2)->applyFromArray($headerStyleDays);
            $column_start++;
            $dt_from->modify("+1 day");
        }
        $row_start++;

        $dept_list = array();
        $employees = array();
        if ($group_id) {
            $employees = $this->employees_m->getAll(false, "*", "", 0, 0, array("mb_nick" => "ASC"), array("mb_sched_grp_id" => $group_id));
            foreach ($employees as $user) {
                $dt_from = new DateTime($start_date);

                $ph = array("shift_code" => "PH", "shift_sched" => "Holiday", "shift_color" => "D87947");
                $holidays = $this->employees_m->getholidays("group_concat(REPLACE(h_date,'0000',YEAR(now()))) hdate")
                        ->where("(year(h_date) >= year(now()) or year(h_date) = 0000 ) and REPLACE(h_date,'0000',YEAR(now())) between '" . $dt_from->format('Y-m-d') . "' and '" . $dt_to->format('Y-m-d') . "'")
                        ->get()
                        ->result();
                $holidays = (Array) $holidays;
                $validph = (array) $this->employees_m->holiday_validation('select', $user->mb_no, '');

                if (!in_array($user->mb_deptno, $dept_list))
                    $dept_list[] = $user->mb_deptno;
                //print_r($user);
                $column_start = "A";
                $row_start++;
                $row_cel = $column_start . $row_start;
                $activeSheet->setCellValue($row_cel, $user->mb_id);
                $activeSheet->getStyle($row_cel)->applyFromArray($defaultSchedStyle);

                $column_start++;
                $row_cel = $column_start . $row_start;
                $activeSheet->setCellValue($row_cel, ($user->mb_3 == "Local" ? $user->mb_fname : $user->mb_nick) . " " . $user->mb_lname);
                $activeSheet->getStyle($row_cel)->applyFromArray($nameSchedStyle);

                $column_start++;

                while ($dt_from <= $dt_to) {
                    $row_cel = $column_start . $row_start;
                    if ($validph['h_status'] == 1 and in_array($dt_from->format('Y-m-d'),explode(",",$holidays[0]->hdate))) {
                        $activeSheet->setCellValue($row_cel, "PH");
                        $activeSheet->getStyle($row_cel)->applyFromArray($defaultSchedStyle)
                                ->applyFromArray(
                                        array(
                                            'fill' => array(
                                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                'color' => array('rgb' => str_replace("#", "", $ph['shift_color']))
                                            )
                                        )
                        );
                    } else {
                        $activeSheet->setCellValue($row_cel, "-");
                        $activeSheet->getStyle($row_cel)->applyFromArray($defaultSchedStyle);
                    }

                    $column_start++;
                    $dt_from->modify("+1 day");
                }
            }
        }

        $row_start+=3;
        $column_start = "A";
        $merge_cells = 0;
        $start_cell = "A" . $row_start;
        for ($i = 1; $i <= 2; $i++) {
            $row_cel = $column_start . $row_start;
            $activeSheet->setCellValue($row_cel, "");
            $activeSheet->getStyle($row_cel)->applyFromArray($labelStyle);
            $merge_cells++;
            $column_start++;
        }//end for 

        $activeSheet->mergeCells($start_cell . ":" . $row_cel);
        $activeSheet->getStyle($start_cell . ":" . $row_cel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $colstart = "C";
        $row_cel = ($column_start++) . $row_start;
        $activeSheet->setCellValue($row_cel, "Legend:");
        $activeSheet->mergeCells($row_cel . ":E" . $row_start);

        $dept_where = "";
        if (count($dept_list)) {
            foreach ($dept_list as $dept) {
                if ($dept_where)
                    $dept_where .= " OR ";
                $dept_where .= "FIND_IN_SET('" . $dept . "', sched_depts)";
            }
            $dept_where = "(" . $dept_where . ")";
        }

        $emp_where = "";
        if (count($employees)) {
            foreach ($employees as $emp) {
                if ($emp_where)
                    $emp_where .= " OR ";
                $emp_where .= "FIND_IN_SET('" . $emp->mb_no . "', sched_users)";
            }
            $emp_where = "(" . $emp_where . ")";
        }
        $extra_where = "";
        if (!empty($dept_where) && !empty($emp_where))
            $extra_where = "(" . $dept_where . " OR " . $emp_where . ")";
        else if (!empty($dept_where))
            $extra_where = $dept_where;
        else if (!empty($emp_where))
            $extra_where = $emp_where;

        $shifts_dtl = $this->shifts_m->getAll(false, "*, CONCAT(LPAD(shift_hr_from,2,'0'),':',LPAD(shift_min_from,2,'0'),'-',LPAD(shift_hr_to,2,'0'),':',LPAD(shift_min_to,2,'0')) shift_sched, shift_color", $extra_where);

        $shifts_dtl[] = (object) array("shift_code" => "RD", "shift_sched" => "Rest Day", "shift_color" => "1B7935");
        $shifts_dtl[] = (object) array("shift_code" => "SS", "shift_sched" => "Suspension", "shift_color" => "FA4747");
        $shifts_dtl[] = (object) array("shift_code" => "PH", "shift_sched" => "Holiday", "shift_color" => "D87947");
        $cntr = -1;
        ob_start();
        foreach ($shifts_dtl as $key => $shift) {
            $cntr++;
            if (($cntr) % 4 == 0) {
                $row_start++;
                $colstart = "C";
            } else {
                $colstart = $colend;
                $colstart++;
                $colstart++;
            }
            $activeSheet->setCellValue($colstart . $row_start, $shift->shift_code);
            $activeSheet->getStyle($colstart . $row_start)->applyFromArray($defaultSchedStyle)
                    ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => str_replace("#", "", $shift->shift_color))
                                )
                            )
            );
            $activeSheet->setCellValue(( ++$colstart) . $row_start, $shift->shift_sched);
            $colend = $colstart;
            $colend++;
            $colend++;
            $colend++;
            $activeSheet->mergeCells($colstart . $row_start . ":" . $colend . $row_start);
            $activeSheet->getStyle($colstart . $row_start . ":" . $colend . $row_start)->applyFromArray($nameSchedStyle);
        }
        $row_start++;
        $row_start++;

        ob_clean();

        $column_start = 'A';
        $total_columns = $column_start_num;
        for ($col = 0; $col < $total_columns; $col++) {
            $column_start = PHPExcel_Cell::stringFromColumnIndex($col);
            $activeSheet->getColumnDimension($column_start)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    /* End of Private Functions */
}

?>