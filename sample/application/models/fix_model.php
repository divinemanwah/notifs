<?php

class Fix_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getAllBio($where = "") {

        $this->db->select("*");

        if ($where) {
            $this->db->where($where);
        }

        return $this->db->from("tk_biometric_log")
                        ->order_by("year_log", "ASC")
                        ->order_by("month_log", "ASC")
                        ->order_by("day_log", "ASC")
                        ->order_by("hour_log", "ASC")
                        ->order_by("min_log", "ASC")
                        ->order_by("sec_log", "ASC")
                        ->get()
                        ->result();
    }

    function resetAttendance() {
        $this->db->delete("tk_attendance", array("att_id >" => 0));
    }

    function query($query) {
        return $this->db->query($query);
    }

    function clearAttendance($from, $to, $mb_no) {
        if ($mb_no)
            $this->db->where("REPLACE(att_date,'-','') BETWEEN '" . $from . "' AND '" . $to . "' AND mb_no = '" . $mb_no . "'")->delete("tk_attendance");
        else
            $this->db->where("REPLACE(att_date,'-','') BETWEEN '" . $from . "' AND '" . $to . "'")->delete("tk_attendance");
    }

    function getBio($from, $to, $enroll_no = null) {
        $where = "CONCAT(year_log,LPAD(month_log,2,'0'),LPAD(day_log,2,'0')) BETWEEN '" . $from . "' AND '" . $to . "'";
        if ($enroll_no != null)
            $where .= " AND enroll_number = '" . $enroll_no . "'";

        return $this->db->select("*")
                        ->where($where)
                        ->order_by("year_log", "ASC")
                        ->order_by("month_log", "ASC")
                        ->order_by("day_log", "ASC")
                        ->order_by("hour_log", "ASC")
                        ->order_by("min_log", "ASC")
                        ->order_by("sec_log", "ASC")
                        ->get("tk_biometric_log")
                        ->result();
    }

    function setRegLeaveBalance($mb_no, $race, $dept_no) {
        $today = new DateTime();
        $timekeeping_query = "select * from g4_member where mb_employment_status = 2 AND mb_status = 1 AND mb_no = '" . $mb_no . "'";
        $employee_dtl = $this->db->query($timekeeping_query);

        $employee = $employee_dtl->result();

        if (!count($employee))
            return;

        $leave_query = "select * from tk_leave_code where has_entitlement = 1 and is_manual_entitlement = 0;";
        $leave_entitlement = $this->db->query($leave_query);

        $query = "UPDATE tk_lv_balance SET bal = 0 WHERE mb_no = '" . $employee[0]->mb_no . "' AND leave_id = '2' AND year = '" . $today->format("Y") . "'";
        $this->db->query($query);

        $leaves = $leave_entitlement->result();
        if (count($leaves)) {
            foreach ($leaves as $leave) {
                $total_entitlement = 0;
                if ($race == 'Local' && in_array($dept_no, array(32, 33, 34)) && $leave->leave_code == 'AL') {
                    if ($leave->is_fixed_entitlement) {
                        $total_entitlement = $leave->fixed_entitlement;
                    } else if ($leave->is_computed_entitlement) {
                        if (empty($employee[0]->{"mb_" . $leave->start_date}))
                            continue;
                        $year_end = new DateTime($today->format("Y-12-31 00:00:00"));
                        $date_basis = new DateTime($employee[0]->{"mb_" . $leave->start_date} . " 00:00:00");
                        if ($date_basis->format('Y') != $year_end->format('Y')) {
                            continue;
                        }
                        if ($date_basis->format('j') == 1) {
                            $total_entitlement = floor((12 - ($date_basis->format('n') - 1)) * (5 / 12));
                        } else {
                            $total_month = (12 - ($date_basis->format('n'))) * (5 / 12);
                            $total_days = $date_basis->format('j') > 15 ? 0 : 0.49;
                            $total_entitlement = floor($total_month + $total_days);
                        }
                    } else
                        continue;
                }
                else if ($race == 'Expat' && $leave->leave_code == 'AL') {
                    if ($leave->is_fixed_entitlement) {
                        $total_entitlement = $leave->fixed_entitlement;
                    } else if ($leave->is_computed_entitlement) {
                        if (empty($employee[0]->{"mb_" . $leave->start_date}))
                            continue;
                        $year_end = new DateTime($today->format("Y-12-31 00:00:00"));
                        $date_basis = new DateTime($employee[0]->{"mb_" . $leave->start_date} . " 00:00:00");
                        if ($date_basis->format('Y') != $year_end->format('Y')) {
                            continue;
                        }
                        if ($date_basis->format('j') == 1) {
                            $total_entitlement = (12 - ($date_basis->format('n') - 1)) * 3.5;
                        } else {
                            $total_month = (12 - ($date_basis->format('n'))) * 3.5;
                            $total_days = (($date_basis->format('t') - ($date_basis->format('j') - 1)) / $date_basis->format('t') * 3.5);
                            $total_entitlement = floor($total_month + $total_days);
                        }
                    } else
                        continue;
                }
                else if ($race == 'Local' && !in_array($dept_no, array(32, 33, 34)) && $leave->local_expat == 'l') {
                    if ($leave->is_fixed_entitlement) {
                        $total_entitlement = $leave->fixed_entitlement;
                    } else if ($leave->is_computed_entitlement) {
                        if (empty($employee[0]->{"mb_" . $leave->start_date}))
                            continue;
                        $year_end = new DateTime($today->format("Y-12-31 00:00:00"));
                        $date_basis = new DateTime($employee[0]->{"mb_" . $leave->start_date} . " 00:00:00");
                        if ($date_basis->format('Y') != $year_end->format('Y')) {
                            continue;
                        }
                        if ($date_basis->format('j') == 1) {
                            $total_entitlement = floor((12 - ($date_basis->format('n') - 1)) * (10 / 12));
                        } else {
                            $total_month = (12 - ($date_basis->format('n'))) * (10 / 12);
                            $total_days = $date_basis->format('j') > 15 ? 0 : 1;
                            $total_entitlement = floor($total_month + $total_days);
                        }
                    } else
                        continue;
                } else
                    continue;
                $tmp_leave_query = "SELECT * FROM tk_lv_balance WHERE leave_id = '" . $leave->leave_id . "' AND mb_no = '" . $employee[0]->mb_no . "' AND year = '" . $today->format("Y") . "'";
                $tmp_leave_dtl = $this->db->query($tmp_leave_query);

                $tmp_leave = $tmp_leave_dtl->result();

                if (count($tmp_leave)) {
                    $query = "UPDATE tk_lv_balance SET bal = '" . $total_entitlement . "' WHERE leave_id = '" . $leave->leave_id . "' AND mb_no = '" . $employee[0]->mb_no . "' AND year = '" . $today->format("Y") . "'";
                    $this->db->query($query);
                } else {
                    $query = "INSERT INTO tk_lv_balance VALUE (NULL, '" . $leave->leave_id . "', '" . $employee[0]->mb_no . "', '" . $today->format("Y") . "', '" . $total_entitlement . "', 0, 0, 0, 0, 0)";
                    $this->db->query($query);
                }
            }
        }
    }

    function setRegLeaveBalanceAnnual($mb_no, $race, $dept_no, $year) {
		/* Make this as setup */
		$today = new DateTime();
		$effectivity_date = new DateTime($today->format("Y-10-13 00:00:00"));
		if($today < $effectivity_date || $today->format("n") < $effectivity_date->format("n")) {
			return;
		}
		/* End of effectivity checking */
		
        $timekeeping_query = "select * from g4_member where mb_employment_status = 2 AND mb_status = 1 AND mb_no = '" . $mb_no . "'";
        $employee_dtl = $this->db->query($timekeeping_query);

        $employee = $employee_dtl->result();

        if (!count($employee))
            return;

        $leave_query = "select * from tk_leave_code where has_entitlement = 1 and is_manual_entitlement = 0;";
        $leave_entitlement = $this->db->query($leave_query);

        $query = "UPDATE tk_lv_balance SET bal = 0 WHERE mb_no = '" . $employee[0]->mb_no . "' AND leave_id = '2' AND year = '" . $year . "'";
        $this->db->query($query);

        $leaves = $leave_entitlement->result();
        if (count($leaves)) {
            $today = new DateTime();
            foreach ($leaves as $leave) {
                $total_entitlement = 0;
                if ($race == 'Local' && in_array($dept_no, array(32, 33, 34)) && $leave->leave_code == 'AL') {
                    if ($leave->is_fixed_entitlement) {
                        $total_entitlement = $leave->fixed_entitlement;
                    } else if ($leave->is_computed_entitlement) {
                        if (empty($employee[0]->{"mb_" . $leave->start_date}))
                            continue;
                        $total_entitlement = 5;
                    } else
                        continue;
                }
                else if ($race == 'Expat' && $leave->leave_code == 'AL') {
                    if ($leave->is_fixed_entitlement) {
                        $total_entitlement = $leave->fixed_entitlement;
                    } else if ($leave->is_computed_entitlement) {
                        if (empty($employee[0]->{"mb_" . $leave->start_date}))
                            continue;
                        $total_entitlement = $leave->max_entitlement;
                    } else
                        continue;
                }
                else if ($race == 'Local' && !in_array($dept_no, array(32, 33, 34)) && $leave->local_expat == 'l') {
                    if ($leave->is_fixed_entitlement) {
                        $total_entitlement = $leave->fixed_entitlement;
                    } else if ($leave->is_computed_entitlement) {
                        if (empty($employee[0]->{"mb_" . $leave->start_date}))
                            continue;
                        $total_entitlement = $leave->max_entitlement;
                    } else
                        continue;
                } else
                    continue;
				
				$tmp_leave_query = "SELECT * FROM tk_lv_balance WHERE leave_id = '" . $leave->leave_id . "' AND mb_no = '" . $employee[0]->mb_no . "' AND year = '" . $year . "'";
                $tmp_leave_dtl = $this->db->query($tmp_leave_query);

                $tmp_leave = $tmp_leave_dtl->result();
				
				if (count($tmp_leave)) {
                    $query = "UPDATE tk_lv_balance SET bal = '" . $total_entitlement . "' WHERE leave_id = '" . $leave->leave_id . "' AND mb_no = '" . $employee[0]->mb_no . "' AND year = '" . $year . "'";
                    $this->db->query($query);
                } else {
                    $query = "INSERT INTO tk_lv_balance VALUE (NULL, '" . $leave->leave_id . "', '" . $employee[0]->mb_no . "', '" . $year . "', '" . $total_entitlement . "', 0, 0, 0, 0, 0)";
                    $this->db->query($query);
                }
            }
        }
    }

}
