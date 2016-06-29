<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notifications extends MY_Controller {

    function __construct() {

        parent::__construct();

        $this->load->model('notifications_model', 'notifications');
    }

    public function getAll($user_id = 0, $page = 1, $per_page = 8, $unread_only = false) {

        echo json_encode($this->notifications->getAll($user_id, $page, $per_page, $unread_only));
    }

    public function create($type, $message_id, $extra_data = null, $recipient = 0, $recipient_group = 0, $page = null, $action = null, $args = null) {

        echo json_encode(array('success' => $this->notifications->create($type, $message_id, $extra_data, $recipient, $recipient_group, $page, $action, $args)));
    }

    public function read() {

        if ($id = $this->input->post('id', true))
            $this->notifications->read($id);
    }

    public function readAll() {

        if (count($ids = $this->notifications->getUnreadIDs()))
            $this->notifications->read($ids);
    }

    public function scheduler($action) {
        $this->load->model('notifications_model', 'notifs');
        $this->load->model('employees_model', 'employees');
        switch ($action) {
            case 'checkRegularables':

                $file = sys_get_temp_dir() . '\checkRegularables_checked';

                $mtime = @filemtime($file);

                if (!file_exists($file) || ($mtime && date('Y-m-d', $mtime) != date('Y-m-d'))) {


                    $count = $this->employees->getRegularablesCount();

                    if ($count)
                        $this->notifs->create('system', 1, $count, 0, 24, 'employees', 'getRegularables');

                    file_put_contents($file, $mtime);
                }

                break;
            case 'autoCite':

                // $this->load->model('shifts_model', 'tk_m');
$this->notifs->create('system', 2, 999, 101, 0);
                break;
            case 'checkExpiredCwv':
                $docs = $this->db->get('hr_documents_notification')->result();

                foreach ($docs as $docinfo) {
                    $docinfo = (array) $docinfo;
                    $dateform = Array();
                    foreach (json_decode('[' . $docinfo['passport'] . ']', true) as $val)
                        $dateform[] = "DATE(hh.passport_validity - INTERVAL " . $val . " DAY)";

                    foreach (json_decode('[' . $docinfo['aep'] . ']', true) as $val)
                        $dateform[] = "DATE(hh.aep_validity - INTERVAL " . $val . " DAY)";

                    foreach (json_decode('[' . $docinfo['cwv'] . ']', true) as $val)
                        $dateform[] = "DATE(hh.cwv_validity - INTERVAL " . $val . " DAY)";

                    $expatinfo = $this->db->from("hr_expat hh")
                            ->join("g4_member gm", "gm.mb_no = hh.employee_id", "inner")
                            ->where("DATE(NOW()) IN ( " . implode(", ", $dateform) . " )", NULL, FALSE)
                            ->get()
                            ->result();

                    if (count($expatinfo))
                        $this->notifs->create('system', 2, count($expatinfo), $docinfo['mb_no'], 0, 'employees', 'setExpatexpiration');
                }
                break;
        }
    }

    public function getAllForApproval() {
        /* CWS */
        $this->load->model('shifts_model', 'tk_m');
        $approver_depts = $this->tk_m->getCWSApproverGroup($this->session->userdata("mb_no"), "DISTINCT taga.cws_apprv_grp_id, taga.level");
        $search_str = "";
        if (count($approver_depts)) {
            foreach ($approver_depts as $groups) {
                $search_str .= (empty($search_str) ? "" : " OR ") . "(tcsa.apprv_grp_id = '" . $groups->cws_apprv_grp_id . "' AND tcsa.approved_level = '" . $groups->level . "')";
            }
        } else {
            $search_str .= "tcsa.approval_id < 0";
        }
        $search_str = "(" . $search_str . ") AND tcsa.status = '1'";
        $select_str = "tcsa.*";
        $cws_data = $this->tk_m->getAllForApprovalChangeShiftFiltered($select_str, $search_str);
        // $cws_data = array();
        /* End of CWS */

        /* LV */
        $this->load->model('leaves_model', 'lv_m');
        $approver_depts = $this->lv_m->getApproverGroup($this->session->userdata("mb_no"), "DISTINCT taga.lv_apprv_grp_id, taga.level");
        $search_str = "";
        if (count($approver_depts)) {
            foreach ($approver_depts as $groups) {
                $search_str .= (empty($search_str) ? "" : " OR ") . "(tlaa.lv_apprv_grp_id = '" . $groups->lv_apprv_grp_id . "' AND tlaa.approved_level = '" . $groups->level . "')";
            }
        } else {
            $search_str .= "tlaa.approval_id < 0";
        }
        $search_str = "(" . $search_str . ") AND tlaa.status = '1'";
        $select_str = "tlaa.*";
        $lv_data = $this->lv_m->getAllForApprovalFiltered($select_str, $search_str);
        /* End of LV */

        /* OT */
        $this->load->model('overtime_model', 'ot_m');
        $approver_depts = $this->ot_m->getApproverGroup($this->session->userdata("mb_no"), "DISTINCT taga.ot_apprv_grp_id, taga.level");
        $search_str = "";
        if (count($approver_depts)) {
            foreach ($approver_depts as $groups) {
                $search_str .= (empty($search_str) ? "" : " OR ") . "(tlaa.ot_apprv_grp_id = '" . $groups->ot_apprv_grp_id . "' AND tlaa.approved_level = '" . $groups->level . "')";
            }
        } else {
            $search_str .= "tlaa.approval_id < 0";
        }
        $search_str = "(" . $search_str . ") AND tlaa.status = '1'";
        $select_str = "tlaa.*";
        $ot_data = $this->ot_m->getAllForApprovalFiltered($select_str, $search_str);
        /* End of OT */

        /* OBT */
        $this->load->model('obt_model', 'obt_m');
        $approver_depts = $this->obt_m->getApproverGroup($this->session->userdata("mb_no"), "DISTINCT taga.obt_apprv_grp_id, taga.level");
        $search_str = "";
        if (count($approver_depts)) {
            foreach ($approver_depts as $groups) {
                $search_str .= (empty($search_str) ? "" : " OR ") . "(tlaa.obt_apprv_grp_id = '" . $groups->obt_apprv_grp_id . "' AND tlaa.approved_level = '" . $groups->level . "')";
            }
        } else {
            $search_str .= "tlaa.approval_id < 0";
        }
        $search_str = "(" . $search_str . ") AND tlaa.status = '1'";
        $select_str = "tlaa.*";
        $obt_data = $this->obt_m->getAllForApprovalFiltered($select_str, $search_str);
        /* End of OBT */

        /* Att */
        $this->load->model('attendance_model', 'att_m');
        $chg_att = $this->att_m->change_att_info('tac.*', 'tac.att_status = 1');
        $attcount = ($this->session->userdata("mb_no") == 114) ? count($chg_att) : 0;
        /* End of Att */


        echo json_encode(array("sched_count" => 0, "cws_count" => count($cws_data), "lv_count" => count($lv_data), "ot_count" => count($ot_data), "obt_count" => count($obt_data), "att_count" => $attcount));
    }

}

/* End of file notifications.php */
/* Location: ./application/controllers/notifications.php */