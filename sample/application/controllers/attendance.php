<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Attendance extends MY_Controller {

    private $month_list;

    function __construct() {
        parent::__construct();
        $this->load->model('employees_model', 'employees_m');
        $this->load->model('attendance_model', 'att_m');
        $this->load->model('shifts_model', 'shifts_m');
        $this->load->model('leaves_model', 'leaves_m');
        $this->load->model('overtime_model', 'ot_m');
        $this->load->model('notifications_model', 'notifications');
        
        $this->load->library('ws', array('tag' => getenv('HTTP_HOST') == '10.120.10.139' ? 'hris' : 'hristest'));
        $this->ws->load('notifs');
    }

    /* Views */

    public function index() {
        $date = new DateTime();
        $tmp_date = new DateTime($date->format("Y-m-d H:i:s"));
        $tmp_date->modify("-7 day");
        $allow_search = false;

        $period_dtl = (object) array();
        $period_dtl->start = $tmp_date->format("Y-m-d");
        $period_dtl->end = $date->format("Y-m-d");

        if (in_array($this->session->userdata("mb_deptno"), array(24)) || in_array($this->session->userdata("mb_no"), array(114, 229))) {
            $allow_search = true;
            $emp_list = $this->employees_m->getAll(true, "*", false, 0, 0, array("mb_status"=>"ASC", "mb_lname" => "ASC"));
            $dept_list = $this->employees_m->getDepts();
        } else {
            if ($this->employees_m->isDeptHead()) {
                $depts = $this->employees_m->getDeptHeads(array("h.employee_id" => $this->session->userdata("user_id")));
                $dept_head_list = array();
                foreach ($depts as $dept) {
                    $dept_head_list[] = $dept->dept_no;
                }
                $emp_list = $this->employees_m->getAll(false, "*", false, 0, 0, array("mb_status"=>"ASC", "mb_lname" => "ASC"), array("mb_deptno IN (" . implode(",", $dept_head_list) . ") AND " => 1));
                $dept_list = $this->employees_m->getDepts("dept_no IN (" . implode(",", $dept_head_list) . ")");
            } else {
                $emp_list = $this->employees_m->getAll(false, "*", false, 0, 0, array("mb_status"=>"ASC", "mb_lname" => "ASC"), array("mb_no" => $this->session->userdata("mb_no")));
                $dept_list = $this->employees_m->getDepts("dept_no = '" . $this->session->userdata("mb_deptno") . "'");
            }
        }

        $this->view_template('attendance/attendance', 'Attendance', array(
            'breadcrumbs' => array('Attendance'),
            'js' => array(
                'jquery.handsontable.full.min.js',
                'date-time/bootstrap-datepicker.min.js',
                'fineupload/jquery.fine-uploader.js',
                'attendance.js'
            ),
            'css' => array(
                'fineupload/fine-uploader-new.css'
            ),
            'cur_period' => $period_dtl,
            'depts' => $dept_list,
            'emp_list' => $emp_list,
            'allow_search' => $allow_search,
            'emp_dept' => $this->session->userdata("mb_deptno"),
            'emp_id' => $this->session->userdata("mb_no")
        ));
    }

    /*
     * This example code demonstrates how to integrate Fine
     * Uploader using an action inside a Lithium controller.
     *
     * Normally this would be called from JavaScript on your page.
     *
     */

    public function fineupload() {
        $this->_render['layout'] = false; // no layout
        $this->_render['type'] = 'json';
        $tempfilepath = tempnam(sys_get_temp_dir(), base_url() . 'uploads/att_/');
        $loc = 'uploads/att_/';
        //if ($this->request->is('ajax')) {
        if ($this->input->server('REQUEST_METHOD') == 'GET') {
            // i.e. do HTML5 streaming upload
            $pathinfo = pathinfo($_GET['qqfile']);
            $filename = $pathinfo['filename'];
            $ext = @$pathinfo['extension'];
            $ext = ($ext == '') ? $ext : '.' . $ext;
            $uploadname = strtotime("now") . rand(10, 99) . "_" . $filename . $ext;
            $input = fopen('php://input', 'r');
            $temp = fopen($tempfilepath, 'w');
            $realsize = stream_copy_to_stream($input, $temp); // write stream to temp file
            @chmod($tempfilepath, 0644);
            fclose($input);
            if ($realsize != (int) $_SERVER['CONTENT_LENGTH']) {
                $results = array('error' => 'Could not save upload file.');
            } else {
                $results = array('success' => true);
            }
        } else {
            //print_r($_FILES['qqfile']);
            // else do regular POST upload (i.e. for old non-HTML5 browsers)
            $size = $_FILES['qqfile']['size'];
            if ($size == 0) {
                return array('error' => 'File is empty.');
            }
            $pathinfo = pathinfo($_FILES['qqfile']['name']);
            $filename = $pathinfo['filename'];
            $ext = @$pathinfo['extension'];
            $ext = ($ext == '') ? $ext : '.' . $ext;
            $uploadname = md5_file($_FILES['qqfile']['tmp_name']) . "_" . $filename . $ext;
            if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], $loc . $uploadname)) {
                $results = array('error' => 'Could not save upload file.');
            } else {
                @chmod($tempfilepath, 0644);
                $results = array('success' => true, "filename" => $uploadname);
            }
        }
        echo json_encode($results); // returns JSON
    }

    public function fineuploaddelete() {
        $results = array('success' => true);
        echo json_encode($results); // returns JSON
    }

    public function summary() {
        // redirect("/timekeeping/summary");
    }

    public function getAllChangeAttendanceForApproval() {
        $post = $this->input->post();
        $search = ($post['status'] !== "") ? 'att_status = ' . $post['status'] : 'att_status >= 0';
        if (isset($post["search"]))
            $search .= " and mb_name like '%" . $post["search"]["value"] . "%'";
        $att = $this->att_m->change_att_info('*', $search, 0, 0, Array());
        $data = Array();
        foreach ($att as $value) {
            $value = (Array) $value;
            $shift = $this->shifts_m->getAll(false, '*', 'shift_id =' . $value["shift_id"]);
            $shift_hrs_frm = "";
            $shift_hrs_to = "";
            if (count($shift) > 0) {
                $shift = (array) $shift[0];
                $shift_hrs_frm = ((strlen($shift["shift_hr_from"]) == 2) ? $shift["shift_hr_from"] : "0" . $shift["shift_hr_from"]) . ":" . ((strlen($shift["shift_min_from"]) == 2) ? $shift["shift_min_from"] : "0" . $shift["shift_min_from"]) . "H";
                $shift_hrs_to = ((strlen($shift["shift_hr_to"]) == 2) ? $shift["shift_hr_to"] : "0" . $shift["shift_hr_to"]) . ":" . ((strlen($shift["shift_min_to"]) == 2) ? $shift["shift_min_to"] : "0" . $shift["shift_min_to"]) . "H";
            }

            $submittedby = (array) $this->employees_m->getMembers($value["submitted_by"])[0];
            $approvedby = $value["approved_by"] ? (array) $this->employees_m->getMembers($value["approved_by"])[0] : Array("mb_nick" => "", "mb_lname" => "");
            $data[] = Array(
                "id" => $value["id"],
                "mb_id" => $value["mb_id"],
                "mb_name" => $value["mb_name"],
                "att_date" => $value["att_date"],
                "shift_id" => $value["shift_id"],
                "shift_code" => isset($shift["shift_code"]) ? $shift["shift_code"] : "",
                "shift_color" => isset($shift["shift_color"]) ? $shift["shift_color"] : "",
                "shift_hrs" => $shift_hrs_frm . " - " . $shift_hrs_to,
                "actual_in" => $value["actual_in"] ? $value["actual_in"] : "",
                "actual_out" => $value["actual_out"] ? $value["actual_out"] : "",
                "new_in" => $value["new_in"] ? $value["new_in"] : "",
                "new_out" => $value["new_out"] ? $value["new_out"] : "",
                "submitted_by" => ($submittedby["mb_nick"] . " " . $submittedby["mb_lname"]),
                "approved_by" => ($approvedby["mb_nick"] . " " . $approvedby["mb_lname"]),
                "att_status" => $value["att_status"],
                "att_id" => $value["att_id"]
            );
        }
        echo json_encode(array("data" => $data, "recordsTotal" => count($att), "recordsFiltered" => count($att)));
    }

    public function logs() {
        $date = new DateTime();
        $tmp_date = new DateTime($date->format("Y-m-d H:i:s"));
        $tmp_date->modify("-7 day");
        $allow_search = false;

        $period_dtl = (object) array();
        $period_dtl->start = $tmp_date->format("Y-m-d");
        $period_dtl->end = $date->format("Y-m-d");
        $emp_list = $this->employees_m->getAll(true, "*", false, 0, 0, array("mb_lname" => "ASC"));

        if (in_array($this->session->userdata("mb_deptno"), array(24)) || in_array($this->session->userdata("mb_no"), array(114, 229))) {
            $allow_search = true;
        }

        $this->view_template('attendance/logs', 'Attendance', array(
            'breadcrumbs' => array('In/Out'),
            'js' => array(
                'jquery.handsontable.full.min.js',
                'date-time/bootstrap-datepicker.min.js',
                'attendance.logs.js'
            ),
            'cur_period' => $period_dtl,
            'emp_list' => $emp_list,
            'allow_search' => $allow_search,
            'emp_id' => $this->session->userdata("mb_no")
        ));
    }

    /* End of Views */

    public function attendance_approval() {

        $this->view_template('attendance/attendance_approval', 'Attendance', array(
            'breadcrumbs' => array('Attendance for Approval'),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'attendance.attendance_approval.js'
            )
        ));
    }

    /* Attendance */

    public function getAllAttendance() {
        $limit = $this->input->post("limit");
        $page = $this->input->post("page");
        $dept_id = $this->input->post("department");
        $mb_no = $this->input->post("emp");
        $type = $this->input->post("type");
        $offset = ($page - 1) * $limit;

        $date_from = $this->input->post("dateFrom");
        $date_from = new DateTime($date_from . " 00:00:00");

        $date_to = $this->input->post("dateTo");
        $date_to = new DateTime($date_to . " 00:00:00");

        $date1 = date_create($date_from->format("Y-m-d"));
        $date2 = date_create($date_to->format("Y-m-d"));
        $diff = date_diff($date1, $date2);

        if ($mb_no) {
            $employees = array(0 => (object) $this->employees_m->get($mb_no));
            $total_count = count($employees);
        } else {

            $where_arr = array();
            if ($type)
                $where_arr = array("mb_3" => $type);

            if (empty($dept_id) && !(in_array($this->session->userdata("mb_deptno"), array(24)) || in_array($this->session->userdata("mb_no"), array(114, 229)))) {
                if ($this->employees_m->isDeptHead()) {
                    $depts = $this->employees_m->getDeptHeads(array("h.employee_id" => $this->session->userdata("user_id")));
                    $dept_head_list = array();
                    foreach ($depts as $dept) {
                        $dept_head_list[] = $dept->dept_no;
                    }
                    $employees = $this->employees_m->getAll(false, "*", false, 0, 0, array("mb_lname" => "ASC"), array("mb_deptno IN (" . implode(",", $dept_head_list) . ") AND " => 1));
                    $total_count = count($employees);

                    //$employees = $this->employees_m->getAll(false, "*", false, $offset, $limit, array("mb_lname" => "ASC"), array("mb_deptno IN (" . implode(",", $dept_head_list) . ") AND " => 1));
                } else {
                    $employees = $this->employees_m->getAll(false, "*", false, 0, 0, array("mb_lname" => "ASC"), array("mb_no" => $this->session->userdata("mb_no")));
                    $total_count = count($employees);

                    //$employees = $this->employees_m->getAll(false, "*", false, $offset, $limit, array("mb_lname" => "ASC"), array("mb_no" => $this->session->userdata("mb_no")));
                }
            } else {
                $employees = $this->employees_m->getAll(false, "*", $dept_id, 0, 0, array("d.dept_name" => "ASC", "mb_lname" => "ASC"), $where_arr);
                $total_count = count($employees);

                //$employees = $this->employees_m->getAll(false, "*", $dept_id, $offset, $limit, array("d.dept_name" => "ASC", "mb_lname" => "ASC"), $where_arr);
            }
        }

        $response_arr = $return_arr = array();

        $response_arr = array("ID", "Name", "Department", "Date", "Shift", "IN", "OUT", "Actual IN", "Actual Out", "UT (Min)", "Tardy (Min)", "Rendered Hours", "Overtime", "NSD", "Remarks", "");
        $width_arr = array(80, 250, 120, 80, 50, 80, 80, 80, 80, 70, 80, 120, 70, 70, 120, 50);
        $cnt = 0;
        foreach ($employees as $employee) {

            $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));

            //print_r($diff->days);
            //while ($tmp_date <= $date_to) {
            if ($cnt == 0)
                $total_count = $total_count * ($diff->days + 1);
            for ($i = 0; $i < ($diff->days + 1); $i++) {
                $cnt++;
                if (((($page * $limit) - $limit) < $cnt and ( $page * $limit) >= $cnt) or $cnt == 0) {
                    //echo $cnt;
                    $default_shifts[0] = "RD";
                    $default_shifts[-1] = "SS";
                    $default_shifts[-2] = "PH";

                    $record = $this->shifts_m->getEmployeeSchedules("tms.*, tsc.shift_code, tsc.shift_color, CONCAT(LPAD(tsc.shift_hr_from,2,'0'),':',LPAD(tsc.shift_min_from,2,'0')) shift_from, CONCAT(LPAD(tsc.shift_hr_to,2,'0'),':',LPAD(tsc.shift_min_to,2,'0')) shift_to", "tms.mb_no = '" . $employee->mb_no . "' AND CONCAT(year,'-',month,'-',day) = '" . $tmp_date->format("Y-n-j") . "'");
                    $day_record = $this->att_m->getAllAttendanceFiltered("a.*", "mb_no ='" . $employee->mb_no . "' AND att_date ='" . $tmp_date->format("Y-n-j") . "'");
                    $awol_record = $this->att_m->getAllAWOL("a.*", "mb_no ='" . $employee->mb_no . "' AND att_date ='" . $tmp_date->format("Y-n-j") . "'");

                    $shift_code = "";
                    $shift_from = "";
                    $shift_to = "";
                    $hr_remarks = "";
                    if (isset($record[0]->leave_id) && $record[0]->leave_id) {
                        $leave_dtl = $this->leaves_m->getLeave($record[0]->leave_id);
                        $shift_code = $leave_dtl[0]->leave_code;
                    } else if (isset($record[0]->shift_id)) {
                        $shift_code = (isset($default_shifts[$record[0]->shift_id]) ? $default_shifts[$record[0]->shift_id] : $record[0]->shift_code);
                        $shift_from = $record[0]->shift_from;
                        $shift_to = $record[0]->shift_to;
                    }

                    if (count($awol_record))
                        if ($awol_record[0]->is_awol)
                            $hr_remarks = "AWoL" . ($awol_record[0]->is_leave_deduct ? " - Leave Deducted" : "") . ($awol_record[0]->awol_reason ? " - " . $awol_record[0]->awol_reason : "");
                        else
                            $hr_remarks = "Not AWoL" . ($awol_record[0]->is_el ? " - EL" : "") . ($awol_record[0]->awol_reason ? " - " . $awol_record[0]->awol_reason : "");

                    $chg_att = "";

                    $chg_att = count($day_record) ? (array) $this->att_m->change_att_info('tac.*', 'att_id = ' . $day_record[0]->att_id, 0, 1, Array("att_status" => "desc")) : "";
                    $att_edit = "";
                    if (count($day_record) and ( $day_record[0]->actual_in == "" or $day_record[0]->actual_out == "" or count($chg_att))) {
                        $att_edit = $day_record[0]->att_id;
                    } elseif (count($record) and $tmp_date->format("Y-m-d") <= date("Y-m-d") and $record[0]->shift_id > 0 and ! $day_record) {
                        $att_edit = 0;
                    } else {
                        $att_edit = "";
                    }
                    $emp_data = array(
                        $employee->mb_id,
                        $employee->mb_lname . ", " . ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname),
                        $employee->dept_name,
                        $tmp_date->format("Y-m-d"),
                        count($record) ? $shift_code : "N/A",
                        count($record) ? $shift_from : "",
                        count($record) ? $shift_to : "",
                        count($day_record) ? $day_record[0]->actual_in : "",
                        count($day_record) ? $day_record[0]->actual_out : "",
                        count($day_record) ? $day_record[0]->undertime : "",
                        count($day_record) ? $day_record[0]->tardy : "",
                        count($day_record) ? number_format($day_record[0]->reg_hrs, 2) : "",
                        count($day_record) ? $day_record[0]->overtime : "",
                        count($day_record) ? $day_record[0]->nsd : "",
                        $hr_remarks,
                        $att_edit
                    );

                    if (!in_array($this->session->userdata("mb_deptno"), array(24)))
                        $emp_data[15] = "";

                    $return_arr[] = $emp_data;
                }
                $tmp_date->modify("+1 day");
            }
        }


        echo json_encode(array("data" => $return_arr, "header" => $response_arr, "width" => $width_arr, "total_count" => $total_count, "page" => $page));
    }

    public function export() {
        $this->load->library('excel');
        $activeSheetInd = -1;
        $file_name = "Attendance Report.xls";
        $response_arr = array("Date", "Shift", "IN", "OUT", "Actual IN", "Actual Out", "UT (Min)", "Tardy (Min)", "Rendered Hours", "Overtime", "Approved OT", "NSD", "Remarks");

        $dept_id = $this->input->post("export-dept");
        $mb_no = $this->input->post("export-emp");
        $type = $this->input->post("export-type");

        $date_from = $this->input->post("export-from");
        $date_from = new DateTime($date_from . " 00:00:00");

        $date_to = $this->input->post("export-to");
        $date_to = new DateTime($date_to . " 00:00:00");

        $headerSchedStyle = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'c5d9f1')
            ),
            'borders' => array('outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                )),
            'alignment' => array('wrap' => true,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'font' => array('bold' => true)
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
        $footerLabelStyle = array('borders' => array('outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                )),
            'alignment' => array('wrap' => true,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'font' => array('bold' => true)
        );
        $footerDefaultSchedStyle = array('borders' => array('outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                )),
            'alignment' => array('wrap' => true,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'font' => array('bold' => true)
        );

        if ($mb_no) {
            $emp_record = $this->employees_m->get($mb_no);
            if (count($emp_record))
                $employees = array(0 => (object) $emp_record);
            else
                $employees = array();
            $total_count = count($employees);
        }
        else {
            $where_arr = array();
            if ($type)
                $where_arr = array("mb_3" => $type);
            if (empty($dept_id) && !(in_array($this->session->userdata("mb_deptno"), array(24)) || in_array($this->session->userdata("mb_no"), array(114, 229)))) {
                if ($this->employees_m->isDeptHead()) {
                    $depts = $this->employees_m->getDeptHeads(array("h.employee_id" => $this->session->userdata("user_id")));
                    $dept_head_list = array();
                    foreach ($depts as $dept) {
                        $dept_head_list[] = $dept->dept_no;
                    }
                    $employees = $this->employees_m->getAll(false, "*", false, 0, 0, array("d.dept_name" => "ASC", "mb_lname" => "ASC"), array("mb_deptno IN (" . implode(",", $dept_head_list) . ") AND " => 1));
                } else {
                    $employees = $this->employees_m->getAll(false, "*", false, 0, 0, array(), array("mb_no" => $this->session->userdata("mb_no")));
                }
            } else {
                $employees = $this->employees_m->getAll(false, "*", $dept_id, 0, 0, array("d.dept_name" => "ASC", "mb_lname" => "ASC"), $where_arr);
            }
        }

        $activeSheet = $this->excel->setActiveSheetIndex(++$activeSheetInd);
        if (count($employees)) {
            $row = 0;

            foreach ($employees as $employee) {

                $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));
                $c = 0;
                $row+=2;
                $column = PHPExcel_Cell::stringFromColumnIndex($c);
                $cell = $column . $row;
                $column2 = PHPExcel_Cell::stringFromColumnIndex($c + 12);
                $cell2 = $column2 . $row;
                $activeSheet->mergeCells($cell . ":" . $cell2);
                $activeSheet->setCellValue($cell, "Attendance Log Sheet");
                $row++;
                $cell = $column . $row;
                $column2 = PHPExcel_Cell::stringFromColumnIndex($c + 12);
                $cell2 = $column2 . $row;
                $activeSheet->mergeCells($cell . ":" . $cell2);
                $activeSheet->setCellValue($cell, "For the Period : " . $date_from->format("Y-m-d") . " to " . $date_to->format("Y-m-d"));
                $row++;
                $cell = $column . $row;
                $column2 = PHPExcel_Cell::stringFromColumnIndex($c + 4);
                $cell2 = $column2 . $row;
                $activeSheet->mergeCells($cell . ":" . $cell2);
                $activeSheet->setCellValue($cell, "Name of Employee : " . $employee->mb_fname . " " . $employee->mb_lname);
                $c = round(12 / 2) - 1;
                $column = PHPExcel_Cell::stringFromColumnIndex($c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $employee->mb_nick);
                $column2 = PHPExcel_Cell::stringFromColumnIndex($c + 7);
                $cell2 = $column2 . $row;
                $activeSheet->mergeCells($cell . ":" . $cell2);
                $c = 0;
                $row++;
                $column = PHPExcel_Cell::stringFromColumnIndex($c);
                $cell = $column . $row;
                $column2 = PHPExcel_Cell::stringFromColumnIndex($c + 12);
                $cell2 = $column2 . $row;
                $activeSheet->mergeCells($cell . ":" . $cell2);
                $activeSheet->setCellValue($cell, "Employee ID : " . $employee->mb_id);
                $row++;
                $cell = $column . $row;
                $column2 = PHPExcel_Cell::stringFromColumnIndex($c + 12);
                $cell2 = $column2 . $row;
                $activeSheet->mergeCells($cell . ":" . $cell2);
                $activeSheet->setCellValue($cell, "Branch/Branch Name : " . $employee->dept_name);

                $row++;
                foreach ($response_arr as $c => $title) {
                    $column = PHPExcel_Cell::stringFromColumnIndex($c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, $title);
                    $activeSheet->getStyle($cell)->applyFromArray($headerSchedStyle);
                }
                $t_undertime = $t_tardy = $t_rendered = $t_overtime = $t_app_ot = $t_nsd = $present_count = 0;

                while ($tmp_date <= $date_to) {
                    $row++;
                    $default_shifts[0] = "RD";
                    $default_shifts[-1] = "SS";
                    $default_shifts[-2] = "PH";

                    $record = $this->shifts_m->getEmployeeSchedules("tms.*, tsc.shift_code, tsc.shift_color, CONCAT(LPAD(tsc.shift_hr_from,2,'0'),':',LPAD(tsc.shift_min_from,2,'0')) shift_from, CONCAT(LPAD(tsc.shift_hr_to,2,'0'),':',LPAD(tsc.shift_min_to,2,'0')) shift_to", "tms.mb_no = '" . $employee->mb_no . "' AND CONCAT(year,'-',month,'-',day) = '" . $tmp_date->format("Y-n-j") . "'");
                    $day_record = $this->att_m->getAllAttendanceFiltered("a.*", "mb_no ='" . $employee->mb_no . "' AND att_date ='" . $tmp_date->format("Y-n-j") . "'");
                    $awol_record = $this->att_m->getAllAWOL("a.*", "mb_no ='" . $employee->mb_no . "' AND att_date ='" . $tmp_date->format("Y-n-j") . "'");
                    $ot_records = $this->ot_m->getEmpOTApplication("tla.*", "tla.mb_no ='" . $employee->mb_no . "' AND `date` ='" . $tmp_date->format("Y-n-j") . "' AND tla.status = 3");

                    $shift_from = "";
                    $shift_to = "";
                    if (isset($record[0]->leave_id) && $record[0]->leave_id) {
                        $leave_dtl = $this->leaves_m->getLeave($record[0]->leave_id);
                        $shift_code = $leave_dtl[0]->leave_code;
                        $shift_from = $leave_dtl[0]->leave_code;
                        $shift_to = $leave_dtl[0]->leave_code;
                    } else if (isset($record[0]->shift_id)) {
                        switch ($record[0]->shift_id) {
                            case 0 : $shift_code = "RD";
                                break;
                            case -1 : $shift_code = "SS";
                                break;
                            case -2 : $shift_code = "PH";
                                break;
                            default : $shift_code = $record[0]->shift_code;
                                ;
                        }

                        $shift_from = $record[0]->shift_from;
                        $shift_to = $record[0]->shift_to;
                    }
                    if (count($day_record) && ($day_record[0]->actual_in && $day_record[0]->actual_out)/* && count($record) && $record[0]->lv_app_id == 0 */) {
                        $present_count++;
                        if (count($record) && $record[0]->lv_app_id <> 0) {
                            $lv_record = $this->leaves_m->getEmpLeaveApplication("tla.*", "lv_app_id ='" . $record[0]->lv_app_id . "'");
                            if (count($lv_record) && ($lv_record[0]->leave_id == 4 || ($lv_record[0]->leave_id == 1 && $lv_record[0]->sub_categ_id == 1))) {
                                $present_count--;
                            }
                        }
                    }

                    $c = 0;

                    $column = PHPExcel_Cell::stringFromColumnIndex($c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, $tmp_date->format("Y-m-d"));
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, count($record) ? $shift_code : "N/A");
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, count($record) ? $shift_from : "N/A");
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, count($record) ? $shift_to : "N/A");
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, count($day_record) ? $day_record[0]->actual_in : "");
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, count($day_record) ? $day_record[0]->actual_out : "");
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, count($day_record) ? $day_record[0]->undertime : "");
                    $t_undertime += count($day_record) ? $day_record[0]->undertime : 0;
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, count($day_record) ? $day_record[0]->tardy : "");
                    $t_tardy += count($day_record) ? $day_record[0]->tardy : 0;
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                    $cell = $column . $row;
                    $hrs = count($day_record) ? number_format($day_record[0]->reg_hrs, 2) : "";
                    $activeSheet->setCellValue($cell, $hrs);

                    $hour = explode(".", $hrs);
                    $minutes = $hrs - $hour[0];
                    $decimalminutes = ($minutes / 60) * 100;
                    $t_rendered += ($hour[0] + $decimalminutes);

                    // $t_rendered += count($day_record)?$day_record[0]->reg_hrs:0;
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, count($day_record) ? number_format($day_record[0]->overtime, 2) : "");
                    $t_overtime += count($day_record) ? $day_record[0]->overtime : 0;
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);

                    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                    $cell = $column . $row;
                    $approved_ot = 0;
                    if (count($ot_records)) {
                        $last_ot_to = "";
                        foreach ($ot_records as $key => $ot_record) {
                            $ot_date = new DateTime($ot_record->date);
                            $ot_from = new DateTime($ot_date->format("Y-m-d ") . $ot_record->time_in . ":00");
                            $ot_to = new DateTime($ot_date->format("Y-m-d ") . $ot_record->time_out . ":00");

                            if ($ot_from > $ot_to)
                                $ot_to->modify("+1 day");

                            $ot_to_basis = new DateTime($ot_to->format("Y-m-d H:i:s"));
                            $ot_from_basis = new DateTime($ot_from->format("Y-m-d H:i:s"));
                            if (count($record) && $record[0]->shift_id < 1) {
                                if (count($day_record)) {
                                    if ($day_record[0]->actual_in)
                                        $time_in = new DateTime($tmp_date->format("Y-m-d") . " " . $day_record[0]->actual_in . ":00");
                                    else
                                        $time_in = new DateTime($ot_from->format("Y-m-d H:i:s"));


                                    if ($day_record[0]->actual_out)
                                        $time_out = new DateTime($tmp_date->format("Y-m-d") . " " . $day_record[0]->actual_out . ":00");
                                    else
                                        $time_out = new DateTime($ot_to->format("Y-m-d H:i:s"));


                                    if ($last_ot_to != "" && $last_ot_to->format('U') == $ot_from->format('U')) {
                                        //IF the LAST_OT_TO for the first OT file is equal to OT_FROM for the second OT file in the same date
                                        //Recompute the approved OT by getting the actual TIME_IN and actual TIME_OUT

                                        $ot_from_basis = new DateTime($time_in->format("Y-m-d H:i:s"));
                                        $ot_to_basis = new DateTime($time_out->format("Y-m-d H:i:s"));
                                        if ($ot_from_basis > $ot_to_basis)
                                            $ot_to_basis->modify("+1 day");
                                        $approved_ot = floor(($ot_to_basis->format("U") - $ot_from_basis->format("U")) / 3600);
                                    }
                                    else {
                                        if ($ot_from < $time_in && $time_in < $ot_to) {
                                            $ot_from_basis = new DateTime($time_in->format("Y-m-d H:i:s"));
                                        }
                                        if ($ot_from < $time_out && $time_out < $ot_to) {
                                            $ot_to_basis = new DateTime($time_out->format("Y-m-d H:i:s"));
                                        }
                                        if ($ot_from_basis > $ot_to_basis)
                                            $ot_to_basis->modify("+1 day");

                                        $approved_ot += floor(($ot_to_basis->format("U") - $ot_from_basis->format("U")) / 3600);
                                    }

                                    //Get the last ot_to being filed
                                    if ($last_ot_to == "")
                                        $last_ot_to = new DateTime($ot_to->format("Y-m-d H:i:s"));
                                    else {
                                        if ($last_ot_to->format('U') == $ot_from->format('U')) {
                                            $last_ot_to = new DateTime($ot_to->format("Y-m-d H:i:s"));
                                        }
                                    }
                                }
                            } else {
                                if (count($day_record)) {
                                    if ($day_record[0]->actual_in)
                                        $time_in = new DateTime($tmp_date->format("Y-m-d") . " " . $day_record[0]->actual_in . ":00");
                                    else
                                        $time_in = new DateTime($ot_from->format("Y-m-d H:i:s"));

                                    if ($day_record[0]->actual_out)
                                        $time_out = new DateTime($tmp_date->format("Y-m-d") . " " . $day_record[0]->actual_out . ":00");
                                    else
                                        $time_out = new DateTime($ot_to->format("Y-m-d H:i:s"));

                                    if ($ot_from < $time_in && $time_in < $ot_to) {
                                        $ot_from_basis = new DateTime($time_in->format("Y-m-d H:i:s"));
                                    }
                                    if ($ot_from < $time_out && $time_out < $ot_to) {
                                        $ot_to_basis = new DateTime($time_out->format("Y-m-d H:i:s"));
                                    }
                                    $approved_ot += floor(($ot_to_basis->format("U") - $ot_from_basis->format("U")) / 3600);
                                } else {
                                    $shift_in = new DateTime($tmp_date->format("Y-m-d") . " " . $record[0]->shift_from . ":00");
                                    $shift_out = new DateTime($tmp_date->format("Y-m-d") . " " . $record[0]->shift_to . ":00");

                                    if ($ot_from < $time_in && $time_in < $ot_to) {
                                        $ot_from_basis = new DateTime($time_in->format("Y-m-d H:i:s"));
                                    }
                                    if ($ot_from < $time_out && $time_out < $ot_to) {
                                        $ot_to_basis = new DateTime($time_out->format("Y-m-d H:i:s"));
                                    }
                                    $approved_ot += floor(($ot_to_basis->format("U") - $ot_from_basis->format("U")) / 3600);
                                }
                            }
                        }

                        $activeSheet->setCellValue($cell, $approved_ot);
                    } else if (count($day_record))
                        $activeSheet->setCellValue($cell, 0);
                    else
                        $activeSheet->setCellValue($cell, "");
                    $t_app_ot += $approved_ot;
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);

                    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, count($day_record) ? number_format($day_record[0]->nsd, 2) : "");
                    $t_nsd += count($day_record) ? $day_record[0]->nsd : 0;
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);


                    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                    $cell = $column . $row;
                    if (count($awol_record))
                        if ($awol_record[0]->is_awol)
                            $activeSheet->setCellValue($cell, "AWoL" . ($awol_record[0]->is_leave_deduct ? " - Leave Deducted" : "") . ($awol_record[0]->awol_reason ? " - " . $awol_record[0]->awol_reason : ""));
                        else
                            $activeSheet->setCellValue($cell, "Not AWoL" . ($awol_record[0]->is_el ? " - EL" : "") . ($awol_record[0]->awol_reason ? " - " . $awol_record[0]->awol_reason : ""));
                    else
                        $activeSheet->setCellValue($cell, "");
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);

                    $tmp_date->modify("+1 day");
                }
                $row++;

                $c = 0;
                $column = PHPExcel_Cell::stringFromColumnIndex($c);
                $cell = $column . $row;
                $column2 = PHPExcel_Cell::stringFromColumnIndex($c + 4);
                $c+=4;
                $cell2 = $column2 . $row;
                $activeSheet->mergeCells($cell . ":" . $cell2);
                $activeSheet->setCellValue($cell, "Total =>");
                $activeSheet->getStyle($cell . ":" . $cell2)->applyFromArray($footerLabelStyle);
                $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $present_count);
                $activeSheet->getStyle($cell)->applyFromArray($footerDefaultSchedStyle);
                $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $t_undertime);
                $activeSheet->getStyle($cell)->applyFromArray($footerDefaultSchedStyle);
                $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $t_tardy);
                $activeSheet->getStyle($cell)->applyFromArray($footerDefaultSchedStyle);
                $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                $cell = $column . $row;

                $hour = explode(".", $t_rendered);
                $minutes = $t_rendered - $hour[0];
                $decimalminutes = ($minutes * 60) / 100;
                $t_rendered = ($hour[0] + $decimalminutes);

                $activeSheet->setCellValue($cell, $t_rendered);
                $activeSheet->getStyle($cell)->applyFromArray($footerDefaultSchedStyle);
                $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $t_overtime);
                $activeSheet->getStyle($cell)->applyFromArray($footerDefaultSchedStyle);

                $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $t_app_ot);
                $activeSheet->getStyle($cell)->applyFromArray($footerDefaultSchedStyle);

                $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $t_nsd);
                $activeSheet->getStyle($cell)->applyFromArray($footerDefaultSchedStyle);

                $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, "");
                $activeSheet->getStyle($cell)->applyFromArray($footerDefaultSchedStyle);
            }
        }
        else {
            $activeSheet = $this->excel->setActiveSheetIndex(++$activeSheetInd);
            $activeSheet->setCellValue("A1", "No Record Found");
        }

        $column_start = 'A';
        $total_columns = 10;
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

    /* End of Attendance */

    public function AttendanceDetails() {
        $post = $this->input->post();
        $attdetails = Array();
        $att = $this->att_m;
        $ifreject = (isset($post['reject']) and $post['reject'] == 1) ? " and att_status = 0 " : "";

        if (isset($post['checkid'])) {
            $chg_att = (array) $this->att_m->change_att_info('tac.*', 'id = ' . $post["checkid"], 0, 1, Array("att_status" => "desc"));
            $emp = (array) $this->employees_m->getmember($chg_att[0]->mb_no);
            if ($chg_att[0]->att_id !== '0')
                $att = (array) $att->getAllAttendanceFiltered('*', 'a.att_id = ' . $chg_att[0]->att_id)[0];
            if ($chg_att[0]->att_id == '0') {
                $attdetails = Array("att_id" => $chg_att[0]->att_id, "att_date" => $chg_att[0]->att_date, "mb_id" => $emp['mb_id']);
            }
        }
        if (!isset($post['checkid'])) {
            if ($post['updateAttendance'] !== "0") {
                $att = (array) $att->getAllAttendanceFiltered('*', 'a.att_id = ' . $post['updateAttendance'])[0];
                $chg_att = (array) $this->att_m->change_att_info('tac.*', 'att_id = ' . $att["att_id"] . $ifreject, 0, isset($post['limit']) ? $post['limit'] : 0, Array("att_status" => "desc"));
            } else {
                $emp = (array) $this->employees_m->getmember("", Array('mb_id' => $post["rowdata"][0]));
                $chg_att = (array) $this->att_m->change_att_info('tac.*', "tac.mb_no = " . $emp['mb_no'] . " and tac.att_date = '" . $post['rowdata'][3] . "'" . $ifreject, 0, isset($post['limit']) ? $post['limit'] : 0, Array("att_status" => "desc"));
            }
        }
        if (isset($post['rowdata']))
            $attdetails = Array("att_id" => $post["updateAttendance"], "att_date" => $post['rowdata'][3], "mb_id" => $post['rowdata'][0]);

        if (isset($post['updateAttendance']) and $post['updateAttendance'] !== "0") {
            $emp = $this->employees_m->getmember($att["mb_no"]);
            $attdetails = Array("att_id" => $post["updateAttendance"]);
        }
        if (count($chg_att) > 0)
            $chg_att = isset($post['limit']) ? (array) $chg_att[0] : (array) $chg_att;
        if (count($att) > 0) {
            echo json_encode(Array("success" => 1, "att" => $att ? (array) $att : Array(), "emp" => (array) $emp, "chg" => $chg_att ? (array) $chg_att : Array(), 'attdetails' => $attdetails));
        } else {
            echo json_encode(Array("success" => 0, "msg" => "No Data"));
        }
    }

    function changeAttInfo($att_id) {
        $selqry = "ta.att_id,
                        ta.mb_no,
                        ta.att_date,
                        ta.actual_in,
                        ta.actual_in_sec,
                        ta.actual_out,
                        ta.actual_out_sec,
                        @act_in := DATE_FORMAT(CONCAT(att_date,' ',actual_in,':',actual_in_sec),'%Y-%m-%d %H:%i:%s') act_in,
                        @shft_in := DATE_FORMAT(CONCAT(att_date,' ',shift_hr_from,':',shift_min_from),'%Y-%m-%d %H:%i:%s') shft_in,
                        @act_out := DATE_FORMAT(CONCAT(IF(actual_out < actual_in,att_date + INTERVAL 1 DAY,att_date),' ',actual_out,':',actual_out_sec),'%Y-%m-%d %H:%i:%s') act_out,   
                        @shft_out := DATE_FORMAT(CONCAT(IF(shift_hr_from > shift_hr_to,att_date + INTERVAL 1 DAY,att_date),' ',shift_hr_to,':',shift_min_to),'%Y-%m-%d %H:%i:%s') shft_out,
                        @act_hr_in := HOUR(TIME(actual_in) + interval (60-MINUTE(actual_in)) minute) act_hr_in,
                        @act_hr_out := if(HOUR(actual_out) < HOUR(actual_in),(24 + HOUR(actual_out)),HOUR(actual_out)) act_hr_out,
                            if(actual_in is null or actual_out is null,0,
                                            TIME_FORMAT(TIMEDIFF(
                                                    if(@shft_out is not null,@shft_out,@act_out),
                                                    if(@shft_in is not null,@shft_in,@act_in)
                                ),'%k.%i')) reg_hrs,
                            if(@act_in is not null and @act_in > @shft_in,MINUTE(timediff(@act_in - interval SECOND(@act_in) second,@shft_in)),0) tardy,
                            if(@act_out is not null and @act_out < @shft_out,FLOOR(TIME_TO_SEC(timediff(@shft_out,@act_out - interval SECOND(@act_out) second))/60),0) undertime,
                            (if(@act_out is not null and @act_out > @shft_out,HOUR(timediff(@act_out,@shft_out)),0) +
                            if(@act_in is not null and @act_in < @shft_in,HOUR(timediff(@act_in,@shft_in)),0)) overtime,
                            if(@act_hr_in >= 22 or @act_hr_out >= 22,if(@act_hr_out > 30,30,@act_hr_out)-22,0)+
                            if(@act_hr_in <= 6,6-@act_hr_in,0)+
                            if(tsc.shift_hr_from = 22 and tsc.shift_hr_to = 7 and hour(actual_out)>=tsc.shift_hr_to,1,0)nsd";

        $attinfo = $this->att_m->getAttinfo($selqry, "ta.att_id = " . $att_id);
        foreach ($attinfo as $att) {
            $param = Array("reg_hrs" => $att->reg_hrs,
                "tardy" => $att->tardy,
                "undertime" => $att->undertime,
                "overtime" => $att->overtime,
                "nsd" => $att->nsd
            );
            $this->att_m->att_update("att_id = " . $att_id, $param);
        }
    }

    public function cancelrequest() {
        $post = $this->input->post();
        $param = Array("id" => $post['rejectid']);
        $chg_att = (array) $this->att_m->change_att_info('tac.*', $param)[0];
        if (count($chg_att)) {
            $chg = $this->att_m->update_chg_att(Array('att_status' => 0), $param);
            echo json_encode(Array("success" => 1, "msg" => "Request Canceled"));
        } else {
            echo json_encode(Array("success" => 0, "msg" => "Cannot find Data"));
        }
    }

    public function AttendanceApprovalChg() {
        $post = $this->input->post();
        $chg_stats = 2;
        if ($post["action"] == "reject")
            $chg_stats = 0;
        $data = Array("reject_remarks" => $post["remarks"], "att_status" => $chg_stats, "approved_by" => $this->session->userdata("mb_no"));
        $param = Array("id" => $post["id"]);
        $chg_att = (array) $this->att_m->change_att_info('tac.*,gm.mb_nick,gm.mb_lname,gm.mb_deptno', $param, 0, 1, Array("att_status" => "desc"))[0];
        $chg = $this->att_m->update_chg_att($data, $param);
        if ($chg_stats == 2 and $chg_att["att_id"] !== 0)
            $att = $this->att_m->update_attendance("tac.id = " . $post["id"]);

        if ($chg_stats == 2 and $chg_att["att_id"] == 0) {
            $checkatt = $this->att_m->getAttinfo('*',Array('mb_no' => $chg_att["mb_no"], 'att_date' => $chg_att["att_date"]));
            if(count($checkatt)>0){
                $att = $checkatt[0]->att_id;
            }else{
            $att = $this->att_m->ins_att(Array('mb_no' => $chg_att["mb_no"], 'att_date' => $chg_att["att_date"], 'shift_id' => $chg_att["shift_id"], 'actual_in' => count($chg_att["new_in"]) ? $chg_att["new_in"] : null, 'actual_out' => count($chg_att["new_out"]) ? $chg_att["new_out"] : null));
            }
            $this->att_m->update_chg_att(Array('att_id' => $att), $param);
        }
        if ($chg_stats == 2 ){
            $att_checking = (array) $this->att_m->change_att_info('tac.*,gm.mb_nick,gm.mb_lname,gm.mb_deptno', $param, 0, 1, Array("att_status" => "desc"))[0];
                if($att_checking["actual_in"] !== null and $att_checking["actual_in"] !== null)$this->changeAttInfo($att_checking['att_id']);
                    
        }

        $this->notifications->create("application", 2, array($chg_att["mb_nick"] . " " . $chg_att["mb_lname"], ($chg_stats == 2) ? "approved" : "rejected"), 0, 24, "attendance/");
        $this->notifications->create("application", 2, array("Your", ($chg_stats == 2) ? "approved" : "rejected"), $chg_att["mb_no"], 0, "attendance/");

        NOTIFS::publish("APPROVAL_114", array(
            'type' => "ATT",
            'count' => -1
        ));


        echo json_encode(Array("success" => $chg));
    }

    public function setAttendanceChg() {
        $post = $this->input->post();
        $results = Array("success" => 0);
        if ($post['att_id'] !== "0") {
            $att = (array) $this->att_m->getAllAttendanceFiltered('*', 'a.att_id = ' . $post['att_id'])[0];
        } else {
            //$emp = (array) $this->employees_m->getmember("",Array('mb_id'=>$post['mb_id']));
            $postdate = new DateTime($post['att_date']);
            $att = (array) $this->att_m->getEmployeeAttendanceDetails("'' att_id, tms.mb_no, concat(tms.year,'-',LPAD(tms.month,2,0),'-',LPAD(tms.day,2,0))att_date, tms.shift_id, null actual_in, null actual_out", Array('gm.mb_id' => $post['mb_id'], 'tms.year' => $postdate->format('Y'), 'tms.month' => $postdate->format('m'), 'tms.day' => $postdate->format('d')))[0];
        }
        if (isset($post["remarks"]) and isset($post["addfile"]) and strlen($post["addfile"]) > 0) {
            $insert = Array(
                "att_id" => $att["att_id"],
                "mb_no" => $att["mb_no"],
                "shift_id" => $att["shift_id"],
                "att_date" => $att["att_date"],
                "actual_in" => $att["actual_in"],
                "actual_out" => $att["actual_out"],
                "new_in" => $post["new_in"],
                "new_out" => $post["new_out"],
                "remarks" => $post["remarks"],
                "image_file" => $post["addfile"],
                "submitted_by" => $this->session->userdata("mb_no"),
                "created_date"=> date('Y-m-d')
            );
            $chg_att = $this->att_m->add_chg_att($insert);
            
            NOTIFS::publish("APPROVAL_114", array(
                'type' => "ATT",
                'count' => 1
            ));
            
            $results = Array("success" => $chg_att);
        }
        echo json_encode($results);
    }

    /* Logs */

    public function getAllLogs() {
        $limit = $this->input->post("limit");
        $page = $this->input->post("page");
        $mb_no = $this->input->post("emp");
        $offset = ($page - 1) * $limit;

        $date_from = $this->input->post("dateFrom");
        $date_from = new DateTime($date_from . " 00:00:00");

        $date_to = $this->input->post("dateTo");
        $date_to = new DateTime($date_to . " 00:00:00");

        $employees = array(0 => (object) $this->employees_m->get($mb_no));
        $total_count = count($employees);

        $response_arr = $return_arr = array();
        $response_arr = array("ID", "Name", "Department", "Date", "Time", "Type");
        $width_arr = array(80, 280, 100, 120, 100, 120);
        $return_arr = array();
        foreach ($employees as $employee) {
            if (!isset($employee->mb_no))
                continue;
            $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));
            while ($tmp_date <= $date_to) {

                $day_record = $this->att_m->getAllLogsFiltered("a.*", "a.enroll_number ='" . $employee->enroll_number . "' AND CONCAT(year_log,'-',month_log,'-',day_log) ='" . $tmp_date->format("Y-n-j") . "'");
                if (count($day_record)) {
                    foreach ($day_record as $day_data) {
                        $emp_data = array(
                            "mb_id" => $employee->mb_id,
                            "mb_nick" => $employee->mb_lname . ", " . ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname),
                            "dept_name" => $employee->dept_name,
                            "day" => $tmp_date->format("Y-m-d"),
                            "time" => str_pad($day_data->hour_log, 2, "0", STR_PAD_LEFT) . ":" . str_pad($day_data->min_log, 2, "0", STR_PAD_LEFT),
                            "type" => $day_data->in_out_mode == 0 ? "In" : "Out"
                        );
                        $return_arr[] = $emp_data;
                    }
                }
                $tmp_date->modify("+1 day");
            }
        }

        echo json_encode(array("data" => $return_arr, "header" => $response_arr, "width" => $width_arr, "total_count" => count($return_arr) == 0 ? 0 : $total_count, "page" => $page));
    }

    public function exportLogs() {
        $this->load->library('excel');
        $activeSheetInd = -1;
        $file_name = "Logs Report.xls";
        $response_arr = array("Date", "Time", "Type");

        $mb_no = $this->input->post("export-emp");

        $date_from = $this->input->post("export-from");
        $date_from = new DateTime($date_from . " 00:00:00");

        $date_to = $this->input->post("export-to");
        $date_to = new DateTime($date_to . " 00:00:00");

        $emp_record = $this->employees_m->get($mb_no);
        if (count($emp_record))
            $employees = array(0 => (object) $emp_record);
        else
            $employees = array();
        $total_count = count($employees);

        $activeSheet = $this->excel->setActiveSheetIndex(++$activeSheetInd);
        if (count($employees)) {
            $row = 0;
            foreach ($employees as $employee) {
                $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));

                $c = 0;
                $row+=2;
                $column = PHPExcel_Cell::stringFromColumnIndex($c);
                $cell = $column . $row;
                $column2 = PHPExcel_Cell::stringFromColumnIndex($c + 5);
                $cell2 = $column2 . $row;
                $activeSheet->mergeCells($cell . ":" . $cell2);
                $activeSheet->setCellValue($cell, "Biometrics Log Sheet");
                $row++;
                $cell = $column . $row;
                $column2 = PHPExcel_Cell::stringFromColumnIndex($c + 5);
                $cell2 = $column2 . $row;
                $activeSheet->mergeCells($cell . ":" . $cell2);
                $activeSheet->setCellValue($cell, "For the Period : " . $date_from->format("Y-m-d") . " to " . $date_to->format("Y-m-d"));
                $row++;
                $cell = $column . $row;
                $column2 = PHPExcel_Cell::stringFromColumnIndex($c + 4);
                $cell2 = $column2 . $row;
                $activeSheet->mergeCells($cell . ":" . $cell2);
                $activeSheet->setCellValue($cell, "Name of Employee : " . $employee->mb_fname . " " . $employee->mb_lname);
                $c = round(12 / 2) - 1;
                $column = PHPExcel_Cell::stringFromColumnIndex($c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $employee->mb_nick);
                $c = 0;
                $row++;
                $column = PHPExcel_Cell::stringFromColumnIndex($c);
                $cell = $column . $row;
                $column2 = PHPExcel_Cell::stringFromColumnIndex($c + 5);
                $cell2 = $column2 . $row;
                $activeSheet->mergeCells($cell . ":" . $cell2);
                $activeSheet->setCellValue($cell, "Employee ID : " . $employee->mb_id);
                $row++;
                $cell = $column . $row;
                $column2 = PHPExcel_Cell::stringFromColumnIndex($c + 5);
                $cell2 = $column2 . $row;
                $activeSheet->mergeCells($cell . ":" . $cell2);
                $activeSheet->setCellValue($cell, "Branch/Branch Name : " . $employee->dept_name);

                $row++;
                foreach ($response_arr as $c => $title) {
                    $column = PHPExcel_Cell::stringFromColumnIndex($c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, $title);
                }

                while ($tmp_date <= $date_to) {
                    $day_record = $this->att_m->getAllLogsFiltered("a.*", "a.enroll_number ='" . $employee->enroll_number . "' AND CONCAT(year_log,'-',month_log,'-',day_log) ='" . $tmp_date->format("Y-n-j") . "'");
                    if (count($day_record)) {
                        foreach ($day_record as $day_data) {
                            $row++;
                            $c = 0;
                            $column = PHPExcel_Cell::stringFromColumnIndex($c);
                            $cell = $column . $row;
                            $activeSheet->setCellValue($cell, $tmp_date->format("Y-m-d"));
                            $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                            $cell = $column . $row;
                            $activeSheet->setCellValue($cell, str_pad($day_data->hour_log, 2, "0", STR_PAD_LEFT) . ":" . str_pad($day_data->min_log, 2, "0", STR_PAD_LEFT));
                            $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                            $cell = $column . $row;
                            $activeSheet->setCellValue($cell, $day_data->in_out_mode == 0 ? "Time In" : "Time Out");
                        }
                    }
                    $tmp_date->modify("+1 day");
                }
            }
        } else {
            $activeSheet = $this->excel->setActiveSheetIndex(++$activeSheetInd);
            $activeSheet->setCellValue("A1", "No Record Found");
        }

        $column_start = 'A';
        $total_columns = 6;
        for ($col = 0; $col < $total_columns; $col++) {
            $column_start = PHPExcel_Cell::stringFromColumnIndex($col);
            $activeSheet->getColumnDimension($column_start)->setAutoSize(true);
            //$column_start++; 
        }

        $activeSheet = $this->excel->setActiveSheetIndex(0);
        $activeSheet->setTitle('Biometrics Report');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    /* End of Logs */
}

?>