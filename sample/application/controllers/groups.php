<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Groups extends MY_Controller {

    private $kr_shifts;

    function __construct() {

        parent::__construct();

        $this->load->model('ion_auth_model', 'ion_m');
        $this->load->model('employees_model', 'emp_m');
        $this->load->model('shifts_model', 'shifts_m');
        $this->load->model('leaves_model', 'leaves_m');
        $this->load->model('department_model', 'dept_m');

        $this->kr_shifts = array("H8", "H12", "A8", "J12", "E12", "M8", "L", "N", "D1");
    }

    /* Group Settings */

    public function settings() {

        $this->view_template('groups/groups_settings', 'Others', array(
            'breadcrumbs' => array('Group', ' Settings'),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'groups.settings.js'
            )
        ));
    }

    public function getGroup($group_id) {
        $data = $this->dept_m->getdepartment()->where(Array('dept.id' => $group_id))->get()->row();
        echo json_encode(array("success" => 1, "data" => $data));
    }

    public function getGroupid($dept) {
        $groups = $this->dept_m->getdepartment()->where(Array("dept.dept_id " => $dept))->where(Array('dept.dept_status' => 1))->get()->result();
        $data = Array();
        foreach ($groups as $group)
            $data[$group->id] = $group->group_name;
        echo json_encode($data);
    }

    public function saveDept() {
        $post = $this->input->post();
        $tblname = 'dept_allowed_group';
        $where = Array('mb_no' => $post['mb_no'],
            'allowed_status' => $post['allowed_status']);

        $chk_id = $this->dept_m->set_record($tblname, array_slice($where, 0, -1)); // check if exist
        $chk_id_mb = clone $chk_id;
        if (count($chk_id_mb->get()->result()) > 0) {
            $chk_id = $chk_id->where(array_slice($where, 1, 1)); //check if exist again
            if (count($chk_id->get()->result()) < 1)
                $updaterecord = $this->dept_m->update_data($tblname, Array(array_slice($where, 0, -1)), array_slice($where, 1, 1));
        }
        else {
            $chk_id_mb = $this->dept_m->create_data($tblname, $where);
        }
    }

    public function getAllGroups() {
        $post = $this->input->post();
        $dept = $this->session->userdata('mb_deptno');
        $_grp = $this->dept_m->getdepartment()->where(Array("dept.dept_id " => $dept));


        $deptstats = 1;
        if (!empty($post['showactive']) && $post['showactive'] == 1) {
            $deptstats = 0;
        }
        $_grp->where(Array("dept.dept_status >=" => $deptstats));
        $_grp_filter = clone $_grp;

        $groups = $_grp->get()->result();
        $all_count = count($groups);


        if (!empty($post['search']['value'])) {
            foreach ($post['columns'] as $column) {
                if ($column['searchable'] == "true") {
                    $_grp_filter->like("group_name", $post['search']['value']);
                }
            }
        }

        $_grp_limit = clone $_grp_filter;
        $groups = $_grp_filter->get()->result();

        $all_filtered_count = count($groups);
        if (is_array($post['order'])) {
            foreach ($post['order'] as $orderDtl) {
                $_grp_limit->order_by($post['columns'][$orderDtl['column']]['data'], $orderDtl['dir']);
            }
        }

        //$_grp_limit->limit($post['length'])->offset($post['start']);
        $data = $_grp_limit->get()->result();
        //print_r($_grp_limit); return false;




        echo json_encode(array("data" => $data,
            "draw" => (int) $post["draw"],
            "recordsTotal" => $all_count,
            "recordsFiltered" => $all_filtered_count));
    }

    public function updateGroup() {
        $post = $this->input->post();
        $data = array();
        $group_id = $post['group_id'];
        $group_name = $post['add-group-code'];
        //$grp = $this->ion_m;
        //$group_success = $grp->update_group($group_id, $group_name);
        $group_success = $this->db->where(Array('id' => $group_id))
                ->update('dept_group', Array('group_name' => $group_name, 'mb_no' => $this->session->userdata('mb_no'), 'update_date' => strtotime('now'), 'dept_status' => 1));

        if ($group_success) {
            echo json_encode(array("success" => 1, "msg" => "Group has been updated"));
        } else {
            echo json_encode(array("success" => 0, "msg" => "Group already exists"));
        }
    }

    public function insertGroup() {
        $post = $this->input->post();
        $data = array();
        $dept_table = Array('dept_group');

        $userid = $this->session->userdata('mb_no');
        $dept = $this->session->userdata('mb_deptno');


        //check if group_name exists in Database
        $getdept = $this->dept_m->getdepartment();
        $getdept = $getdept->where(Array('dept.dept_id' => $dept))
                ->where(Array('lower(dept.group_name)' => strtolower($post['add-group-code'])));

        $deptexist = $getdept->get()->result();
        if (count($deptexist) > 0) {
            if ($deptexist[0]->dept_status == 0) {
                $this->db->where(Array('id' => intval($deptexist[0]->id)))
                        ->update($dept_table[0], Array('dept_status' => 1, 'update_date' => strtotime('now'), 'mb_no' => $userid));
                echo json_encode(array("success" => 1, "msg" => "group name has already updated"));
                exit();
            } else {
                echo json_encode(array("success" => 0, "msg" => "group name has already created"));
                exit();
            }
        }

        foreach ($dept_table as $tblname) {
            if (!$this->db->table_exists($tblname)) {
                echo json_encode(array("success" => 0, "msg" => log_message('error', 'Data was not Saved Error on Database.')));
                exit();
            }
        }
        if (!$userid || !$dept) {
            echo json_encode(array("success" => 0, "msg" => log_message('error', 'Some variable did not contain a value.')));
            exit();
        }

        $group_name = Array('group_name' => $post['add-group-code'],
            'mb_no' => $userid,
            'update_date' => 'unix_timestamp()',
            'dept_status' => 1,
            'dept_id' => $dept);
        $grp = $this->dept_m;
        $group_success = $grp->create_data($dept_table[0], $group_name);


        if ($group_success) {
            echo json_encode(array("success" => 1, "msg" => "Group has been created"));
        } else {
            echo json_encode(array("success" => 0, "msg" => "Group already exists"));
        }
    }

    public function deleteGroup() {
        $post = $this->input->post();
        $group_id = $post['group_id'];

        $group_success = $this->dept_m->update_data('dept_group', Array(0 => Array('id' => $group_id)), Array('dept_status' => 0, 'update_date' => strtotime('now'), 'mb_no' => $this->session->userdata('mb_no')));

        if ($group_success) {
            echo json_encode(array("success" => 1, "msg" => "Group has been deleted"));
        } else {
            echo json_encode(array("success" => 0, "msg" => "Error deleting group"));
        }
    }

    /* End of Group Settings */

    /* Members */

    public function members() {
        $dept = $this->session->userdata('mb_deptno');
        $_grp = $this->dept_m->getdepartment()
                ->where(Array("dept.dept_id " => $dept))
                ->where(Array('dept.dept_status' => 1));
        $_grp = $_grp->get()->result();
        $this->view_template('groups/members', 'Others', array(
            'breadcrumbs' => array('Group', 'Group Members'),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'groups.members.js'
            ),
            'groups' => $_grp
        ));
    }

    public function getAllGroupMembers() {
        $post = $this->input->post();
        $dept = $this->session->userdata('mb_deptno');
        $group_id = $post["group"];
        $grp_sel = 'dept.id,gm.mb_id,gm.mb_fname,gm.mb_lname,gm.mb_nick,dept.group_name,gm.mb_no';
        $_grp = $this->dept_m->getmembers($grp_sel);
        $_grp = $_grp->where(Array("gm.mb_status " => 1))
                ->where(Array("gm.mb_deptno " => $dept));


        if ($group_id)
            $employees = $_grp->where(Array('dept.id' => $group_id));
        else
            $employees = $_grp;

        $_emp_filter = clone $employees;

        $emp_data = $employees->get()->result();
        // echo $employees->last_query();
        $all_count = count($emp_data);

        $search_str = "";
        if (!empty($post['search']['value'])) {
            foreach ($post['columns'] as $column) {
                if ($column['searchable'] == "true") {
                    $search_str .= (empty($search_str) ? "" : " OR ") . " " . (($column['data'] == "group_name") ? "dept." : "gm.") . $column['data'] . " LIKE '%" . $_emp_filter->escape_like_str($post['search']['value']) . "%' ";
                }
            }
        }

        if ($search_str) {
            $_emp_filter->where("(" . $search_str . ")", null, false);
        }

        $_emp_limit = clone $_emp_filter;

        $emp_data = $_emp_filter->get()->result();
        $all_filtered_count = count($emp_data);

        if (is_array($post['order'])) {
            foreach ($post['order'] as $orderDtl) {
                $_emp_limit->order_by($post['columns'][$orderDtl['column']]['data'], $orderDtl['dir']);
            }
        }

        $_emp_limit->limit($post['length'])->offset($post['start']);
        $data = $_emp_limit->get()->result();

        echo json_encode(array("data" => $data,
            "draw" => (int) $post["draw"],
            "recordsTotal" => $all_count,
            "recordsFiltered" => $all_filtered_count));
    }

    public function setMemberGroup() {
        $post = $this->input->post();
        $group_success = '';
        $grouptbl = 'dept_group_mem_assign';
        $data = array();
        $dept = $this->session->userdata('mb_deptno');
        $group_id = $post['add-group-member-code'];
        $mb_no = $post['hr_users_id'];

        if (!empty($mb_no)) {
            $grp = $this->dept_m;
            $grp = $grp->set_record($grouptbl, Array('mb_no' => $mb_no));
            $grp = $grp->get()->result();

            if (count($grp) > 0)
                $group_success = $this->dept_m->update_data($grouptbl, [['mb_no' => $mb_no]], Array('group_id' => $group_id));

            if (!$group_success)
                $group_success = $this->dept_m->create_data($grouptbl, Array('mb_no' => $mb_no, 'group_id' => $group_id));


            if ($group_success) {
                echo json_encode(array("success" => 1, "msg" => "Member is already added to group"));
            } else {
                echo json_encode(array("success" => 0, "msg" => "An error has occured"));
            }
        } else {
            echo json_encode(array("success" => 0, "msg" => "Employee not found"));
        }
    }

    /* End of Members */

    /* Export Schedule By Group */

    public function schedule() {
        $settings = $this->shifts_m->getGeneralSettings();
        $dept = $this->session->userdata('mb_deptno');
        $shifts_dtl = $this->shifts_m->getAll(false, "*, CONCAT(LPAD(shift_hr_from,2,'0'),':',LPAD(shift_min_from,2,'0'),'-',LPAD(shift_hr_to,2,'0'),':',LPAD(shift_min_to,2,'0')) shift_sched, shift_color");
        $shifts_list = array();
        foreach ($shifts_dtl as $shift) {
            /*
              // A1 Shift for Steve
              if($shift->shift_code == "A1" && $this->session->userdata("mb_no") != 126)
              continue;
              else if(in_array($shift->shift_code,$this->kr_shifts) && (!in_array($this->session->userdata("mb_deptno"),array(35,24)))) {
              continue;
              }
             * 
             */
            $shifts_list[$shift->shift_id] = (object) array();
            $shifts_list[$shift->shift_id]->scode = $shift->shift_code;
            $shifts_list[$shift->shift_id]->stime = $shift->shift_sched;
        }

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

        if ($allow_search) {
            $emp_list = $this->emp_m->getAll(false, "*", false, 0, 0, array("mb_lname" => "ASC"));
        } else {
            $emp_list = $this->emp_m->getAll(false, "*", $this->session->userdata("mb_deptno"), 0, 0, array("mb_lname" => "ASC"));
        }
        $groups = $this->dept_m->getdepartment()->where(Array("dept.dept_id " => $dept))->where(Array('dept.dept_status' => 1))->get()->result();
        $this->view_template('groups/schedule', 'Others', array(
            'breadcrumbs' => array('Group', 'Group Members Schedules'),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'groups.schedule.js'
            ),
            'groups' => $groups,
            'cur_period' => $period_dtl,
            'depts' => $this->emp_m->getDepts(),
            'allow_search' => $allow_search,
            'emp_list' => $emp_list,
            'emp_dept' => $this->session->userdata("mb_deptno"),
            'shifts' => $shifts_list
        ));
    }

    public function schedules_export_detail() {
        $this->load->library('excel');
        $activeSheetInd = -1;
        $file_name = "HRIS Detailed Schedules.xls";

        $dept_id = $this->input->post("export-dept");
        $mb_no = $this->input->post("export-emp");
        $group_id = $this->input->post("export-group");
        if ($group_id)
            $group_id = explode(",", $group_id);
        else
            $group_id = array();
        $shift_id = $this->input->post("export-shift");

        $date_from = $this->input->post("export-from");
        $date_from = new DateTime($date_from . " 00:00:00");

        $date_to = $this->input->post("export-to");
        $date_to = new DateTime($date_to . " 00:00:00");

        if (!empty($mb_no)) {
            $employees = $this->emp_m->getAll(false, "m.*,d.*,gma.group_id,dept.group_name", $dept_id, 0, 0, array(), array("m.mb_no" => $mb_no), true);
            $total_count = count($employees);
        } else if ($shift_id) {
            $params = array("tms.shift_id" => $shift_id, "(tms.lv_app_id = '0' OR tms.lv_app_id IS NULL) AND " => true, "tms.year" => $date_from->format("Y"), "tms.month" => $date_from->format("n"), "tms.day" => $date_from->format("j"));
            if ($group_id) {
                $group_str = "gma.group_id IN (";
                foreach ($group_id as $key => $group)
                    $group_str .= ($key == 0) ? "'" . $group . "'" : ",'" . $group . "'";
                $params[$group_str . ") AND "] = true;
            }

            $employees_db = $this->shifts_m->getEmployeeSchedulesDept(false, "gm.*,d.*,gma.group_id,dept.group_name", $dept_id, 0, 0, array("tsc.shift_code" => "ASC", "gma.group_id" => "ASC", "d.dept_name" => "ASC", "gm.mb_3" => "DESC", "mb_lname" => "ASC"), $params);
            $_emp_filter = clone $employees_db;
            $employees = $employees_db->get()->result();
            $total_count = count($employees);
            $employees = $_emp_filter->get()->result();
            $date_to = $this->input->post("export-from");
            $date_to = new DateTime($date_to . " 00:00:00");
        } else if (count($group_id)) {
            $group_str = "gma.group_id IN (";
            foreach ($group_id as $key => $group)
                $group_str .= ($key == 0) ? "'" . $group . "'" : ",'" . $group . "'";
            $params[$group_str . ") AND "] = true;
            $employees = $this->emp_m->getAll(false, "m.mb_id", $dept_id, 0, 0, array(), $params, true);
            $total_count = count($employees);
            $employees = $this->emp_m->getAll(false, "m.*,d.*,gma.group_id,dept.group_name", $dept_id, 0, 0, array("d.dept_name" => "ASC", "gma.group_id" => "ASC", "mb_3" => "DESC", "mb_lname" => "ASC"), $params, true);
        } else {
            $employees = $this->emp_m->getAll(false, "m.mb_id", $dept_id);
            $total_count = count($employees);
            $employees = $this->emp_m->getAll(false, "m.*,d.*,gma.group_id,dept.group_name", $dept_id, 0, 0, array("d.dept_name" => "ASC", "gma.group_id" => "ASC", "mb_3" => "DESC", "mb_lname" => "ASC"), array(), true);
        } {// Styles
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
            $groupStyle = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'E8E8E8')
                ),
                'font' => array('bold' => true, 'color' => array('rgb' => 'D20000')),
                'borders' => array('outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '000000'),
                    )),
                'alignment' => array('wrap' => true,
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

        $activeSheet = $this->excel->setActiveSheetIndex(++$activeSheetInd);

        $row = 1;
        //BLANK CELLS
        $merge_cells = 0;
        $start_cell = "A" . $row;
        $column_start = "A";
        for ($i = 1; $i <= 4; $i++) {
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
        $column_start_num = 4;
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
            echo $col_num;

            $column_start = PHPExcel_Cell::stringFromColumnIndex($col_num - 1);
            $row_cell2 = $column_start . $row;

            $activeSheet->mergeCells($row_cell1 . ":" . $row_cell2);
            $activeSheet->getCell($row_cell1)->setValueExplicit($cnt_date->format("M Y"), PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->getStyle($row_cell1 . ":" . $row_cell2)->applyFromArray($this->headStyleRandom($color_ctr));

            $tmp_date->modify("+1 day");
            $cnt_date = $tmp_date;
            $column_start_num += $total_merge;
            $column_start++;
            $color_ctr++;
        }
        ob_clean();
        //echo "asdasdasd";die();
        //END MONTHS, YEAR
        //Headers
        $c = 0;
        $row++;
        $header_list = array("ID", "Name", "Nickname", "Department");
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

        $column = PHPExcel_Cell::stringFromColumnIndex($c);
        $row_cel = $column . $row;
        $row_cel2 = $column . ($row + 1);
        $activeSheet->mergeCells($row_cel . ":" . $row_cel2);
        $activeSheet->getCell($row_cel)->setValueExplicit("RD", PHPExcel_Cell_DataType::TYPE_STRING);
        $activeSheet->getStyle($row_cel . ":" . $row_cel2)->applyFromArray($headerStyle);

        $row++;
        $total_columns = $c;
        if (count($employees)) {
            //$date_from = new DateTime($date_from." 00:00:00");
            $group = "";
            $total_shifts_count = $total_shifts = $shifts = array();
            foreach ($employees as $employee) {
                if (!in_array($this->session->userdata("mb_deptno"), array(24)) && $group !== $employee->group_id) {
                    if (count($shifts)) {
                        $c = 3;
                        $row+=2;
                        $column = PHPExcel_Cell::stringFromColumnIndex($c);
                        $cell = $column . $row;
                        $activeSheet->setCellValue($cell, "Total Count");
                        $activeSheet->getStyle($cell)->applyFromArray($headerStyle);
                        $row++;
                        ksort($shifts);
                        foreach ($shifts as $shift_id => $shift_code) {
                            $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));
                            $c = 3;
                            $column = PHPExcel_Cell::stringFromColumnIndex($c);
                            $cell = $column . $row;
                            $activeSheet->setCellValue($cell, $shift_code);
                            $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                            while ($tmp_date <= $date_to) {
                                $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                                $cell = $column . $row;
                                $activeSheet->setCellValue($cell, isset($shifts_count[$tmp_date->format("Ymd")][$shift_code]) ? $shifts_count[$tmp_date->format("Ymd")][$shift_code] : 0);
                                $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                                $tmp_date->modify("+1 day");
                            }
                            $row++;
                        }
                    }

                    $shifts = $shifts_count = array();
                    $group = $employee->group_id;
                    $c = 0;
                    $row+=1;
                    $column = PHPExcel_Cell::stringFromColumnIndex($c);
                    $cell = $column . $row;
                    $c = $total_columns;
                    $column = PHPExcel_Cell::stringFromColumnIndex($c);
                    $cell2 = $column . $row;
                    $activeSheet->mergeCells($cell . ":" . $cell2);
                    $activeSheet->setCellValue($cell, empty($employee->group_name) ? "General" : $employee->group_name);
                    $activeSheet->getStyle($cell . ":" . $cell2)->applyFromArray($groupStyle);
                }
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
                $activeSheet->setCellValue($cell, $employee->mb_nick);
                $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);

                $c+=1;
                $column = PHPExcel_Cell::stringFromColumnIndex($c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $employee->dept_name);
                $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);

                $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));

                $rd_count = 0;
                while ($tmp_date <= $date_to) {
                    if (!isset($shifts_count[$tmp_date->format("Ymd")]))
                        $shifts_count[$tmp_date->format("Ymd")] = array();
                    if (!isset($total_shifts_count[$tmp_date->format("Ymd")]))
                        $total_shifts_count[$tmp_date->format("Ymd")] = array();
                    $record = $this->shifts_m->getEmployeeSchedules("tms.*, tsc.shift_code, tsc.shift_color", "tms.mb_no = '" . $employee->mb_no . "' AND year = '" . $tmp_date->format("Y") . "' AND month = '" . $tmp_date->format("n") . "' AND day = '" . $tmp_date->format("j") . "'");
                    $c+=1;
                    $column = PHPExcel_Cell::stringFromColumnIndex($c);
                    $cell = $column . $row;

                    $color = "777777";
                    if (count($record)) {
                        if ($record[0]->leave_id) {
                            $leave_dtl = $this->leaves_m->getLeave($record[0]->leave_id);
                            $tmp_leave_code = $leave_dtl[0]->leave_code;
                            $activeSheet->setCellValue($cell, $tmp_leave_code);
                            $color = "C2C2C2";
                            if (!isset($shifts_count[$tmp_date->format("Ymd")][$tmp_leave_code]))
                                $shifts_count[$tmp_date->format("Ymd")][$tmp_leave_code] = 1;
                            else
                                $shifts_count[$tmp_date->format("Ymd")][$tmp_leave_code] += 1;
                            if (!isset($total_shifts_count[$tmp_date->format("Ymd")][$tmp_leave_code]))
                                $total_shifts_count[$tmp_date->format("Ymd")][$tmp_leave_code] = 1;
                            else
                                $total_shifts_count[$tmp_date->format("Ymd")][$tmp_leave_code] += 1;
                            if (!in_array($tmp_leave_code, $shifts))
                                $shifts[$tmp_leave_code] = $tmp_leave_code;
                            if (!in_array($tmp_leave_code, $total_shifts))
                                $total_shifts[$tmp_leave_code] = $tmp_leave_code;
                        }
                        else {
                            switch ($record[0]->shift_id) {
                                case "0":
                                    $activeSheet->setCellValue($cell, "RD");
                                    $color = "1B7935";
                                    $rd_count += 1;
                                    if (!isset($total_shifts_count[$tmp_date->format("Ymd")]["RD"]))
                                        $total_shifts_count[$tmp_date->format("Ymd")]["RD"] = 1;
                                    else
                                        $total_shifts_count[$tmp_date->format("Ymd")]["RD"] += 1;
                                    if (!in_array("RD", $total_shifts))
                                        $total_shifts[$record[0]->shift_id] = "RD";
                                    break;
                                case "-1":
                                    $activeSheet->setCellValue($cell, "SS");
                                    if (!isset($shifts_count[$tmp_date->format("Ymd")]["SS"]))
                                        $shifts_count[$tmp_date->format("Ymd")]["SS"] = 1;
                                    else
                                        $shifts_count[$tmp_date->format("Ymd")]["SS"] += 1;
                                    if (!isset($total_shifts_count[$tmp_date->format("Ymd")]["SS"]))
                                        $total_shifts_count[$tmp_date->format("Ymd")]["SS"] = 1;
                                    else
                                        $total_shifts_count[$tmp_date->format("Ymd")]["SS"] += 1;
                                    $color = "FA4747";
                                    if (!in_array("SS", $shifts))
                                        $shifts[$record[0]->shift_id] = "SS";
                                    if (!in_array("SS", $total_shifts))
                                        $total_shifts[$record[0]->shift_id] = "SS";
                                    break;
                                case "-2":
                                    $activeSheet->setCellValue($cell, "PH");
                                    if (!isset($shifts_count[$tmp_date->format("Ymd")]["PH"]))
                                        $shifts_count[$tmp_date->format("Ymd")]["PH"] = 1;
                                    else
                                        $shifts_count[$tmp_date->format("Ymd")]["PH"] += 1;
                                    if (!isset($total_shifts_count[$tmp_date->format("Ymd")]["PH"]))
                                        $total_shifts_count[$tmp_date->format("Ymd")]["PH"] = 1;
                                    else
                                        $total_shifts_count[$tmp_date->format("Ymd")]["PH"] += 1;
                                    $color = "D87947";
                                    if (!in_array("PH", $shifts))
                                        $shifts[$record[0]->shift_id] = "PH";
                                    if (!in_array("PH", $total_shifts))
                                        $total_shifts[$record[0]->shift_id] = "PH";
                                    break;
                                default:
                                    $tmp_shift_code = $record[0]->shift_code;
                                    $activeSheet->setCellValue($cell, $tmp_shift_code);
                                    if (!isset($shifts_count[$tmp_date->format("Ymd")][$tmp_shift_code]))
                                        $shifts_count[$tmp_date->format("Ymd")][$tmp_shift_code] = 1;
                                    else
                                        $shifts_count[$tmp_date->format("Ymd")][$tmp_shift_code] += 1;
                                    if (!isset($total_shifts_count[$tmp_date->format("Ymd")][$tmp_shift_code]))
                                        $total_shifts_count[$tmp_date->format("Ymd")][$tmp_shift_code] = 1;
                                    else
                                        $total_shifts_count[$tmp_date->format("Ymd")][$tmp_shift_code] += 1;
                                    $color = str_replace("#", "", $record[0]->shift_color);
                                    if (!in_array($tmp_shift_code, $shifts))
                                        $shifts[$record[0]->shift_id] = $tmp_shift_code;
                                    if (!in_array($tmp_shift_code, $total_shifts))
                                        $total_shifts[$record[0]->shift_id] = $tmp_shift_code;
                            }
                        }
                    }
                    else {
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

                $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, $rd_count);
                $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
            }
            if (count($shifts) && !in_array($this->session->userdata("mb_deptno"), array(24))) {
                $c = 3;
                $row+=2;
                $column = PHPExcel_Cell::stringFromColumnIndex($c);
                $cell = $column . $row;
                $activeSheet->setCellValue($cell, "Total Count");
                $activeSheet->getStyle($cell)->applyFromArray($headerStyle);
                ksort($shifts);
                foreach ($shifts as $shift_id => $shift_code) {
                    $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));
                    $c = 3;
                    $row+=1;
                    $column = PHPExcel_Cell::stringFromColumnIndex($c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, $shift_code);
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                    while ($tmp_date <= $date_to) {
                        $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                        $cell = $column . $row;
                        $activeSheet->setCellValue($cell, isset($shifts_count[$tmp_date->format("Ymd")][$shift_code]) ? $shifts_count[$tmp_date->format("Ymd")][$shift_code] : 0);
                        $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                        $tmp_date->modify("+1 day");
                    }
                }
            }

            $c = 3;
            $row+=2;
            $column = PHPExcel_Cell::stringFromColumnIndex($c);
            $cell = $column . $row;
            $activeSheet->setCellValue($cell, "Total Employee Count");
            $activeSheet->getStyle($cell)->applyFromArray($headerStyle);

            $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
            $cell = $column . $row;
            $activeSheet->setCellValue($cell, count($employees));
            $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);

            if (count($total_shifts)) {
                ksort($total_shifts);
                foreach ($total_shifts as $shift_id => $shift_code) {
                    $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));
                    $c = 3;
                    $row+=1;
                    $column = PHPExcel_Cell::stringFromColumnIndex($c);
                    $cell = $column . $row;
                    $activeSheet->setCellValue($cell, $shift_code);
                    $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                    while ($tmp_date <= $date_to) {
                        $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
                        $cell = $column . $row;
                        $activeSheet->setCellValue($cell, isset($total_shifts_count[$tmp_date->format("Ymd")][$shift_code]) ? $total_shifts_count[$tmp_date->format("Ymd")][$shift_code] : 0);
                        $activeSheet->getStyle($cell)->applyFromArray($defaultSchedStyle);
                        $tmp_date->modify("+1 day");
                    }
                }
            }
        } else {
            // $activeSheet = $this->excel->setActiveSheetIndex(++$activeSheetInd);
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

    public function getAllSchedules() {
        $limit = $this->input->post("limit");
        $page = $this->input->post("page");
        $dept_id = $this->input->post("department");
        $mb_no = $this->input->post("emp");
        $group_id = $this->input->post("grp");
        $shift_id = $this->input->post("shift");
        $offset = ($page - 1) * $limit;

        $date_from = $this->input->post("dateFrom");
        $date_from = new DateTime($date_from . " 00:00:00");

        $date_to = $this->input->post("dateTo");
        $date_to = new DateTime($date_to . " 00:00:00");

        if (!empty($mb_no)) {
            $employees = array((object) $this->emp_m->get($mb_no));
            $total_count = count($employees);
        } else if ($shift_id) {
            $params = array("tms.shift_id" => $shift_id, "(tms.lv_app_id = '0' OR tms.lv_app_id IS NULL) AND " => true, "tms.year" => $date_from->format("Y"), "tms.month" => $date_from->format("n"), "tms.day" => $date_from->format("j"));
            if ($group_id) {
                $group_str = "gma.group_id IN (";
                foreach ($group_id as $key => $group)
                    $group_str .= ($key == 0) ? "'" . $group . "'" : ",'" . $group . "'";
                $params[$group_str . ") AND "] = true;
            }

            $employees_db = $this->shifts_m->getEmployeeSchedulesDept(false, "gm.*,d.*,gma.group_id,dept.group_name", $dept_id, 0, 0, array("tsc.shift_code" => "ASC", "d.dept_name" => "ASC", "gma.group_id" => "ASC", "gm.mb_3" => "DESC", "mb_lname" => "ASC"), $params);
            $_emp_filter = clone $employees_db;
            $employees = $employees_db->get()->result();
            $total_count = count($employees);
            $employees = $_emp_filter->limit($limit, $offset)->get()->result();
            $date_to = $this->input->post("dateFrom");
            $date_to = new DateTime($date_to . " 00:00:00");
        } else if ($group_id) {
            $group_str = "gma.group_id IN (";
            foreach ($group_id as $key => $group)
                $group_str .= ($key == 0) ? "'" . $group . "'" : ",'" . $group . "'";
            $params[$group_str . ") AND "] = true;
            $employees = $this->emp_m->getAll(false, "m.mb_id", $dept_id, 0, 0, array(), $params, true);
            $total_count = count($employees);
            $employees = $this->emp_m->getAll(false, "m.*,d.*,gma.group_id,dept.group_name", $dept_id, $offset, $limit, array("d.dept_name" => "ASC", "gma.group_id" => "ASC", "mb_3" => "DESC", "mb_lname" => "ASC"), $params, true);
        } else {
            $employees = $this->emp_m->getAll(false, "m.mb_id", $dept_id);
            $total_count = count($employees);
            $employees = $this->emp_m->getAll(false, "m.*,d.*,gma.group_id,dept.group_name", $dept_id, $offset, $limit, array("d.dept_name" => "ASC", "gma.group_id" => "ASC", "mb_3" => "DESC", "mb_lname" => "ASC"), array(), true);
        }
        $response_arr = $return_arr = array();
        if (in_array($this->session->userdata("mb_deptno"), array(23)))
            $response_arr = array("ID", "Name", "Nickname", "Group", "Department");
        else
            $response_arr = array("ID", "Name", "Nickname", "Group", "Department");
        foreach ($employees as $employee) {
            $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));
            if (in_array($this->session->userdata("mb_deptno"), array(23)))
                $emp_data = array(
                    "mb_id" => $employee->mb_id,
                    "mb_nick" => $employee->mb_lname . ", " . ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname),
                    "mb_nickname" => $employee->mb_nick,
                    "group_name" => empty($employee->group_name) ? "" : $employee->group_name,
                    "dept_name" => $employee->dept_name
                );
            else
                $emp_data = array(
                    "mb_id" => $employee->mb_id,
                    "mb_nick" => $employee->mb_lname . ", " . ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname),
                    "mb_nickname" => $employee->mb_nick,
                    "group_name" => empty($employee->group_name) ? "" : $employee->group_name,
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

    /* End of Export Schedule By Group */

    public function getAllowedGroup($mb_no) {
        $grp = $this->dept_m->setAllowedGroup($mb_no);
        echo!isset($grp[0]->allowed_status) ? 0 : $grp[0]->allowed_status;
    }

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

}
