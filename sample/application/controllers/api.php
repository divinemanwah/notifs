<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    function __construct() {
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");
		
		$this->load->library('ws', array('tag' => getenv('HTTP_HOST') == '10.120.10.139' ? 'hris' : 'hristest'));
    }

    function getAllEmployeeInCondo() {
        $this->load->model('employees_model');
        $where_array = $this->input->post();

        $json_array = array();
        $aaData_array = array();
        $aaData_array = $this->employees_model->getAllEmployeeInCondo($where_array);
        $json_array = array("sEcho" => 1,
            "aaData" => $aaData_array
        );
        echo json_encode($json_array);
    }

    function getCondo() {
        $this->load->model('condo_model');
        $where_array = array('c.status' => 1);
        echo json_encode($this->condo_model->Condo_List($where_array));
    }

    function get_shifts() {
        $this->load->model('shifts_model');
        echo json_encode($this->shifts_model->getAll());
    }

    function getDateOfCommencement($username = null) {
        $this->load->model('employees_model');
        $result = $this->employees_model->getByUsername($username);
        if (count($result) > 0) {
            $date_commencement = $result->mb_commencement;
            echo strtotime($date_commencement);
        } else {
            echo "0";
        }
    }

	function setupTest() {
		$WshShell = new COM("WScript.Shell");
        if($WshShell) {
		  if(getenv('HTTP_HOST') == '10.120.10.139') {
		    $phpPath = "C:\\php-5.6.3-Win32-VC11-x64\\php.exe";
			$outputPath = "C:\\Testing.txt";
		  }
		  else {
		    $phpPath = "C:\\xampp\\php\\php.exe";
			$outputPath = "C:\\xampp\\htdocs\\Testing.txt";
		  }
          $oExec = $WshShell->Run("cmd /K ".$phpPath." ".BASEPATH."..\\index.php api setUploadedFile 0 >> ".$outputPath, 9, false);
		}
	}
	
	/* Back-end processing of Attendance - File Upload */
	function setUploadedFile($uploadId) {
	  echo "Upload ID: ".$uploadId."\r\n";
	  echo "URL : ". BASEPATH . "..\\index.php api setUploadedFile " . $uploadId . "\r\n";
	  $date = new DateTime();
	  if(!$this->input->is_cli_request()) {
	    echo "Date: ".$date->format("Y-m-d H:i:s")."\r\nMessage: Cannot run this code. Direct access.\r\n";
		echo str_repeat("=", 100);
	    echo "\n";
	    die();
	  }
	  else if(empty($uploadId)) {
	    echo "Date: ".$date->format("Y-m-d H:i:s")."\r\nMessage: Cannot run this code. No upload id.\r\n";
		echo str_repeat("=", 100);
	    echo "\n";
	    die();
	  }
	  
	  echo "Date: ".$date->format("Y-m-d H:i:s")."\r\nMessage: Testing\r\n";
	  $this->benchmark->mark('total_start');
	  $this->load->model('shifts_model', 'shifts_m');
	  $this->load->model('employees_model', 'employees_m');
	  $this->load->model('leaves_model', 'leaves_m');
	  
	  $month_list = array(
							"jan"=>"01",
							"feb"=>"02",
							"mar"=>"03",
							"apr"=>"04",
							"may"=>"05",
							"jun"=>"06",
							"jul"=>"07",
							"aug"=>"08",
							"sep"=>"09",
							"oct"=>"10",
							"nov"=>"11",
							"dec"=>"12"
						);
	  
	  $shifts_dtl = $this->shifts_m->getAll(false, "*");
	  
	  $shifts_dtl[] = (object) array("shift_id"=>0,"shift_code"=>"RD") ;
	  $shifts_dtl[] = (object) array("shift_id"=>-1,"shift_code"=>"SS") ;
	  $shifts_dtl[] = (object) array("shift_id"=>-2,"shift_code"=>"PH") ;
	  $shifts_list = array();
	  foreach($shifts_dtl as $shift) {
	    $shifts_list[$shift->shift_id] = $shift->shift_code;
	  }
	  
	  $upload_dtl = $this->shifts_m->getAllUploadsFiltered("*","upload_id ='".$uploadId."'");
		
	  $reader = $upload_dtl[0]->file_ext == ".xlsx"?"Excel2007":"Excel5";
	  //load our new PHPExcel library
	  $this->load->library('excel'); 
	  $objReader = PHPExcel_IOFactory::createReader($reader);
	  $objPHPExcel = $objReader->load(dirname(__FILE__)."/../../".$upload_dtl[0]->file_path);
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
	  
	  while($rownumber <= $highestRow){
		$row = $objWorksheet->getRowIterator($rownumber)->current();
		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false);
		  
		// Month Year Checking
		if($rownumber == 1){
		  $year = $month = "";
		  foreach ($cellIterator as $col=>$cell) {
			$cellValue = $cell->getValue();

			if(!empty($year)){
			  $dataArray[$cell->getColumn()]['month'] = $month;
			  $dataArray[$cell->getColumn()]['year'] = $year;
			}
						
			if(empty($cellValue))
			  continue;
			else {
			  $cellValueArr = explode(" ",$cellValue);
			  $cellmonth = strtolower($cellValueArr[0]);
			  $cellyear = strtolower($cellValueArr[1]);
			  if($cellmonth != $month){
			    $dataArray[$cell->getColumn()]['month'] = $month_list[$cellmonth];
			    $dataArray[$cell->getColumn()]['year'] = $cellyear;
			    $month = $month_list[$cellmonth];
			    $year = $cellyear;
			  }			
			}
		  }
		}
		else // Month Year Checking
		if($rownumber == 2){
		  $month_31 = array("01","03","05","07","08","10","12");
		  $month_30 = array("04","06","09","11");
		  foreach ($cellIterator as $col=>$cell) {
			if($col < 2)
			  continue;
			$cellValue = $cell->getValue();
			$dataArray[$cell->getColumn()]['day'] = $cellValue;
		  }
		}
		if($rownumber > 3){
		  $user = "";
		  foreach ($cellIterator as $cell) {
		    $cellValue = $cell->getValue();
		    if($cell->getColumn() == "B")
			  continue;
			if($cell->getColumn() == "A"){
			  $user = $cellValue;
			  if(empty($cellValue))
			    break;
			  $user_dtl = $this->employees_m->getById($user);	
			  if(count($user_dtl)) {
			    $user = $user_dtl->mb_no;
			    /* Update Attendance */
			    $emp_list[] = $user_dtl->mb_no;
			    /* End Update Attendance */
			  }
			  continue;
			}
			/* Update Attendance */
			if($cell->getColumn() == "C"){
			  $date_from = $dataArray[$cell->getColumn()]['year'].(str_pad($dataArray[$cell->getColumn()]['month'],2,"0",STR_PAD_LEFT)).(str_pad($dataArray[$cell->getColumn()]['day'],2,"0",STR_PAD_LEFT));
			}
			else {
			  $date_to = $dataArray[$cell->getColumn()]['year'].(str_pad($dataArray[$cell->getColumn()]['month'],2,"0",STR_PAD_LEFT)).(str_pad(  $dataArray[$cell->getColumn()]['day'],2,"0",STR_PAD_LEFT));
			}
			/* End Update Attendance */
			
			$sched_dtl = $this->shifts_m->getAllMemberScheduleFiltered("*","`mb_no` = '".$user."' AND `year` = '".$dataArray[$cell->getColumn()]['year']."' AND `month` = '".$dataArray[$cell->getColumn()]['month']."' AND `day` = '".$dataArray[$cell->getColumn()]['day']."'");
			
			if($sched_dtl) {
			  $success = $this->shifts_m->updateMemberSchedule(
														array("shift_id"	=> array_search($cellValue,$shifts_list)),
														array("tkms_id"	=> $sched_dtl[0]->tkms_id)
														);
		      if($sched_dtl[0]->lv_app_id) {
				$request_dtl 	= $this->leaves_m->getEmpLeaveApplication("*, tla.status, DATE(tla.date_from) date_from, DATE(tla.date_to) date_to", "tla.lv_app_id = '".$sched_dtl[0]->lv_app_id."'");
				if($request_dtl[0]->allocated>0) {
				  $leave_date_from = new DateTime($request_dtl[0]->date_from);
				  $leave_bal = $this->leaves_m->getEmpLeaveBalances($request_dtl[0]->mb_no,"*",$request_dtl[0]->leave_id,$leave_date_from->format("Y"));
				  if(array_search($cellValue,$shifts_list) > 0) {
				    $this->leaves_m->updateEmpLeaveBalances(array("allocated" => $leave_bal[0]->allocated - 1,"used" => $leave_bal[0]->used + 1), array("leave_id"=>$request_dtl[0]->leave_id,"mb_no"=>$request_dtl[0]->mb_no, "year"=>$leave_date_from->format("Y")));
				    $this->leaves_m->updateLeaveApplication(array("allocated"=>$request_dtl[0]->allocated - 1,"used" => $request_dtl[0]->used + 1), array("lv_app_id"=>$request_dtl[0]->lv_app_id));
				  }
				  else {
				    $this->leaves_m->updateEmpLeaveBalances(array("allocated" => $leave_bal[0]->allocated - 1,"bal" => $leave_bal[0]->bal + 1), array("leave_id"=>$request_dtl[0]->leave_id,"mb_no"=>$request_dtl[0]->mb_no, "year"=>$leave_date_from->format("Y")));
					$this->leaves_m->updateLeaveApplication(array("allocated"=>$request_dtl[0]->allocated - 1), array("lv_app_id"=>$request_dtl[0]->lv_app_id));
					$success = $this->shifts_m->updateMemberSchedule(
														array("leave_id"	=> 0, "lv_app_id"	=> 0 ),
														array("tkms_id"	=> $sched_dtl[0]->tkms_id)
														);
				  }
				}
			  }
			}
			else {
			    $success = $this->shifts_m->insertMemberSchedule(array(
														"mb_no"		=> $user,
														"year"		=> $dataArray[$cell->getColumn()]['year'],
														"month"		=> $dataArray[$cell->getColumn()]['month'],
														"day"		=> $dataArray[$cell->getColumn()]['day'],
														"shift_id"	=> array_search($cellValue,$shifts_list)
													  ));
			}
		  }
		}
		$rownumber++;
	  }
	  // Update Attendance 
	  
	  if(!empty($date_from) && !empty($date_to)) {
		foreach($emp_list as $mb_no) {
		  $this->updateAttendance($mb_no, $date_from, $date_to);
	    }
	  }
	  // End Update Attendance 
	  $this->benchmark->mark('total_end'); 
	  
	  echo "Time Elapsed : ".$this->benchmark->elapsed_time('total_start','total_end')."\r\n";
	  echo str_repeat("=", 100)."\r\n";
	}
	
	function sample() {
	  $this->load->model('employees_model', 'employees_m');
	  $playsms = $this->load->database(array(
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => 'MY34qZZFsGt7Jbc8',
            'database' => 'playsms',
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => false,
            'db_debug' => false,
            'cache_on' => false,
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci'
                ), true);
	  
	  $get = $this->input->get();
	  $sms_datetime = $get['date'];
	  $sms_sender = $get['sndr'];
	  $message = trim($get['msg']);
	  $msg_dtl = explode(" ",$message);
	  if(count($msg_dtl) == 3) {
		$this->ws->load('notifs');
	    $code = strtolower($msg_dtl[0]);
		$mb_id = $msg_dtl[1];
		$pass = $msg_dtl[2];
		$reply_sms_msg = "";
		//Check if valid Code [SL,EL]
		$status = 0;
		$pattern = '/^(s|e)l\s(l|e)[0-9]{2}-[0-9]{3}\s[0-9]{4}$/i';
		if(empty($message) || !preg_match($pattern,$message)) {
		  $reply_sms_msg = "System cannot process your request. Your message format should be EL or SL<space>L12-627<space>PIN Code. Please resend with the correct format. ";
		}
		else {
		  $user_dtl = $this->employees_m->getById($mb_id);
		  
		  if(count($user_dtl) && $user_dtl->mb_status) {
		    if($pass != $user_dtl->sms_passcode) {
			  $reply_sms_msg = "System cannot process your request. Invalid Employee ID or PIN. ";
			  $status = 2;
			}
			else {
		      $reply_sms_msg = "Hi ".$user_dtl->mb_nick.", your text has been received. Please be reminded to submit necessary documents upon return to work. ";
			  $status = 1;
			}
		  }
		  else if(!$user_dtl->mb_status) {
			$reply_sms_msg = "Your account is already disabled. ";
			$status = 3;
		  }
		  else
		    $reply_sms_msg = "System cannot process your request. Invalid Employee ID or PIN. ";
		}
				
	    $sms_receiver = $get['rcvr'];
		if($status != 3) {
			$this->load->model('sms_model', 'sms');
			$this->sms->insertSMS(array("sms_in_datetime"=>$sms_datetime, "sms_in_sender"=>$sms_sender, "sms_in_text"=>$message, "sms_in_receiver"=>$sms_receiver, "mb_id"=>$mb_id, "code"=>$code, "pass"=>$pass, "status"=>$status));
			
			NOTIFS::publish("SMS", array());
		}
		
		$uid = "1";
		$gpid = "0";
		$sms_sender_id = "000";
		$sms_footer = "PSP SMS";
		$sms_type = "text";
		$unicode = 0;
		
		$queue_code = md5(mktime().$uid.$gpid.$reply_sms_msg);
		$db_query = "INSERT INTO playsms_tblsmsoutgoing_queue ";
		$db_query .= "(queue_code,datetime_entry,datetime_scheduled,uid,gpid,sender_id,footer,message,sms_type,unicode) ";
		$db_query .= "VALUES ('$queue_code',NOW(),NOW(),'$uid','$gpid','$sms_sender_id','$sms_footer','$reply_sms_msg','$sms_type','$unicode');";
		
		$result = $playsms->query($db_query);

		// $db_query = "SELECT id FROM playsms_tblsmsoutgoing_queue WHERE queue_code LIKE '%$queue_code%'";
		// $db_result = $playsms->query($db_query);
		// print_r($db_result);
		// echo "<hr/>";
		// $db_row = $db_result->row_array();
		$queue_id = $playsms->insert_id();
		if ($queue_id) {
		  $db_query = "INSERT INTO playsms_tblSMSOutgoing_queue_dst (queue_id,dst) VALUES ('$queue_id','$sms_sender')";
		  $playsms->query($db_query);
		}
	  }
	  else {
		
		// Send error SMS to sender.
	  }
	}

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

								$excess 			= 0;
								$nsd				= 0;
								$regHrs				= 0;
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
										$countedOut 	= new DateTime($shift_out->format("Y-m-d H:i:s"));
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

										$countedIn 	= new DateTime($shift_in->format("Y-m-d H:i:s"));

                                        if ($curLog > $shift_out) {
                                            $excessOut = ($curLog->format("U") - $shift_out->format("U"));
                                            $excess += floor($excessOut / 3600);
										}

										$countedOut 	= new DateTime($shift_out->format("Y-m-d H:i:s"));

                                        $regHrsHr = ($countedOut->format("U") - $countedIn->format("U")) / 3600;
                                        $regHrs = floor($regHrsHr) + ((($regHrsHr - floor($regHrsHr)) * 60) / 100);

                                        if ($shift_in < $nsdDateIn)
											$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:i:s"));
										else {
											if ($shift_in->format("i") != "00") {
												$countedIn 	= new DateTime($shift_in->format("Y-m-d :00:00"));
											}
											else
												$countedIn 	= new DateTime($shift_in->format("Y-m-d H:00:00"));
										}

                                        if ($shift_out > $nsdDateOut)
											$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
										else
											$countedOut 		= new DateTime($shift_out->format("Y-m-d H:i:s"));

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
							$excess 	= 0;
							$nsd		= 0;
							$regHrs	= 0;
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
									$countedOut 	= new DateTime($shift_out->format("Y-m-d H:i:s"));
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

									$countedIn 	= new DateTime($shift_in->format("Y-m-d H:i:s"));

                                    if ($curLog > $shift_out) {
                                        $excessOut = ($curLog->format("U") - $shift_out->format("U"));
                                        $excess += floor($excessOut / 3600);
									}

									$countedOut 	= new DateTime($shift_out->format("Y-m-d H:i:s"));

                                    $regHrsHr = ($countedOut->format("U") - $countedIn->format("U")) / 3600;
                                    $regHrs = floor($regHrsHr) + ((($regHrsHr - floor($regHrsHr)) * 60) / 100);

                                    if ($shift_in < $nsdDateIn)
										$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:i:s"));
									else
										$countedIn 	= new DateTime($shift_in->format("Y-m-d H:i:s"));

                                    if ($shift_out > $nsdDateOut)
										$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
									else
										$countedOut 		= new DateTime($shift_out->format("Y-m-d H:i:s"));

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

											$countedIn 	= new DateTime($shift_in->format("Y-m-d H:i:s"));

                                            if ($curLog > $shift_out) {
                                                $excessOut = ($curLog->format("U") - $shift_out->format("U"));
                                                $excess += floor($excessOut / 3600);
											}

											$countedOut 	= new DateTime($shift_out->format("Y-m-d H:i:s"));

                                            $regHrsHr = ($countedOut->format("U") - $countedIn->format("U")) / 3600;
                                            $regHrs = floor($regHrsHr) + ((($regHrsHr - floor($regHrsHr)) * 60) / 100);

                                            if ($shift_in < $nsdDateIn)
												$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:i:s"));
											else
												$countedIn 	= new DateTime($shift_in->format("Y-m-d H:i:s"));

                                            if ($shift_out > $nsdDateOut)
												$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
											else
												$countedOut 		= new DateTime($shift_out->format("Y-m-d H:i:s"));

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
													$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:00:00"));
												else
													$countedIn 	= new DateTime($tmpDateIn->format("Y-m-d H:00:00"));

                                                if ($curLog > $nsdDateOut)
													$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
												else {
                                                    if ($curLog->format("i") != "00")
														$curLog->modify("+1 hour");
													$countedOut 		= new DateTime($curLog->format("Y-m-d H:00:00"));
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
										$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:i:s"));
									else {
                                        if ($shift_in->format("i") != "00") {
											$countedIn 	= new DateTime($shift_in->format("Y-m-d :00:00"));
										}
										else
											$countedIn 	= new DateTime($shift_in->format("Y-m-d H:00:00"));
									}

                                    if ($shift_out > $nsdDateOut)
										$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
									else
										$countedOut 		= new DateTime($shift_out->format("Y-m-d H:i:s"));
								}
								else {
                                    $regHrsHr = ($curLog->format("U") - $tmpDateIn->format("U")) / 3600;
                                    $regHrs = floor($regHrsHr) + ((($regHrsHr - floor($regHrsHr)) * 60) / 100);
									$excess = floor($regHrs);

                                    if ($tmpDateIn < $nsdDateIn)
										$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:00:00"));
									else
										$countedIn 	= new DateTime($tmpDateIn->format("Y-m-d H:00:00"));

                                    if ($curLog > $nsdDateOut)
										$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:00:00"));
									else {
                                        if ($curLog->format("i") != "00")
											$curLog->modify("+1 hour");
										$countedOut 		= new DateTime($curLog->format("Y-m-d H:00:00"));
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
							$excess 			= 0;
							$nsd				= 0;
							$regHrs				= 0;
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
										$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:00:00"));
									else
										$countedIn 	= new DateTime($tmpDateIn->format("Y-m-d H:00:00"));

                                    if ($curLog > $nsdDateOut)
										$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:00:00"));
									else {
                                        if ($curLog->format("i") != "00")
											$curLog->modify("+1 hour");
										$countedOut 		= new DateTime($curLog->format("Y-m-d H:00:00"));
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
	
	public function updateAttendance($mb_no,$date_str_from,$date_str_to) {
		$this->load->model('fix_model', 'fix_m');
	  /* Update Attendance */
	  $timekeeping_query = "SELECT *
						  FROM intra.g4_member gm 
						WHERE 
						  `gm`.`mb_no` 	= '".$mb_no."'";
	  $prev_schedule_data = $this->fix_m->query($timekeeping_query);
	  $prev_row = $prev_schedule_data->row();
	  if(isset($prev_row->mb_no) && !empty($prev_row->mb_no)) {
	    $this->fix_m->clearAttendance($date_str_from, $date_str_to, $prev_row->mb_no);
	    $biometrics = $this->fix_m->getBio($date_str_from, $date_str_to, $prev_row->enroll_number);
	    $this->calculateToAttendance($biometrics);
	  }
	  /* End Update Attendance */
	}

	
}
