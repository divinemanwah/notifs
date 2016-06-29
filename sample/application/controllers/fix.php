<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fix extends MY_Controller {

	private $month_list;

	function __construct() {
		parent::__construct();
		$this->load->model('fix_model', 'fix_m');
		$this->load->model('ion_auth_model', 'ion_m');
		$this->month_list = array(
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
	}
	
	/* Views */
	
	public function index() {
	  redirect("fix/init");
	}
	
	public function init() {
		/*
		$this->fix_m->resetAttendance();
		$biometrics = $this->fix_m->getAllBio();
		$this->calculateToAttendance($biometrics);
		*/
		echo "Test";
	}
	
	public function updateAttendanceFix($from,$to,$enroll_number=null) {
	  $prev_row = (object) array("mb_no"=> "");
	  if($enroll_number != null) {
	    $timekeeping_query = "SELECT *
								FROM g4_member gm 
							  WHERE 
								`gm`.`enroll_number` 	= ".$enroll_number;
	    $prev_schedule_data = $this->fix_m->query($timekeeping_query);
        $prev_row = $prev_schedule_data->row();
	  }
	  
	  if($enroll_number==null || (isset($prev_row->mb_no) && !empty($prev_row->mb_no))) {
	    $this->fix_m->clearAttendance($from,$to,$prev_row->mb_no);
	    $biometrics = $this->fix_m->getBio($from,$to,$enroll_number);//print_r($biometrics);
		$this->calculateToAttendanceFix($biometrics);
	  }
	}
	
	private function calculateToAttendanceFix($biometrics) {
		foreach($biometrics as $key=>$bio) {
			if($bio->in_out_mode != -1) {
				if($bio->in_out_mode == 0)
					$type = "In";
				else
					$type = "Out";
				$dateStr = ($bio->year_log."-".str_pad($bio->month_log,2,"0",STR_PAD_LEFT)."-".str_pad($bio->day_log,2,"0",STR_PAD_LEFT));
				$timeStr = (str_pad($bio->hour_log,2,"0",STR_PAD_LEFT).":".str_pad($bio->min_log,2,"0",STR_PAD_LEFT));
				
				$curDate = new DateTime($dateStr." 00:00:00");
				$timekeeping_query = "SELECT tms.*,tsc.shift_hr_from,tsc.shift_min_from,tsc.shift_hr_to,tsc.shift_min_to, gm.mb_id, gm.mb_lname, gm.mb_3
										FROM tk_member_schedule tms
										INNER JOIN g4_member gm 
										  ON tms.mb_no = gm.mb_no
										LEFT JOIN tk_shift_code tsc 
										  ON tms.shift_id = tsc.shift_id
									  WHERE 
										`gm`.`enroll_number` 	= ".$bio->enroll_number." AND 
										`tms`.`year` 			= ".$bio->year_log." AND 
										`tms`.`month` 			= ".$bio->month_log." AND 
										`tms`.`day` 			= ".$bio->day_log;
				
				$cur_schedule_data = $this->fix_m->query($timekeeping_query);
				$cur_row = $cur_schedule_data->row();
				$has_current = count($cur_row);
				
				$prevDate = new DateTime($dateStr." 00:00:00");
				$prevDate->modify("-1 day");
				$prev_timekeeping_query = "SELECT tms.*,tsc.shift_hr_from,tsc.shift_min_from,tsc.shift_hr_to,tsc.shift_min_to, gm.mb_id, gm.mb_lname, gm.mb_3
										FROM tk_member_schedule tms
										INNER JOIN g4_member gm 
										  ON tms.mb_no = gm.mb_no
										LEFT JOIN tk_shift_code tsc 
										  ON tms.shift_id = tsc.shift_id
									  WHERE 
										`gm`.`enroll_number` 	= ".$bio->enroll_number." AND 
										`tms`.`year` 			= ".$prevDate->format("Y")." AND 
										`tms`.`month` 			= ".$prevDate->format("n")." AND 
										`tms`.`day` 			= ".$prevDate->format("j");
				
				$prev_schedule_data = $this->fix_m->query($prev_timekeeping_query);
				$prev_row = $prev_schedule_data->row();
				$has_previous = count($prev_row);
				
				$nextDate = new DateTime($dateStr." 00:00:00");
				$nextDate->modify("+1 day");
				$next_timekeeping_query = "SELECT tms.*,tsc.shift_hr_from,tsc.shift_min_from,tsc.shift_hr_to,tsc.shift_min_to, gm.mb_id, gm.mb_lname, gm.mb_3
										FROM tk_member_schedule tms
										INNER JOIN g4_member gm 
										  ON tms.mb_no = gm.mb_no
										LEFT JOIN tk_shift_code tsc 
										  ON tms.shift_id = tsc.shift_id
									  WHERE 
										`gm`.`enroll_number` 	= ".$bio->enroll_number." AND 
										`tms`.`year` 			= ".$nextDate->format("Y")." AND 
										`tms`.`month` 			= ".$nextDate->format("n")." AND 
										`tms`.`day` 			= ".$nextDate->format("j");
				
				$next_schedule_data = $this->fix_m->query($next_timekeeping_query);
				$next_row = $next_schedule_data->row();
				$has_next = count($next_row);
				
				echo "<table border='1' cellspacing ='0' width='100%'>";
				echo "<tr>";
				echo "<td>Enroll Number : ".$bio->enroll_number."</td>";
				echo "<td>Date : ".$dateStr."</td>";
				echo "<td>Time ".$type." : ".$timeStr."</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan='2'>Previous Date : ".$prevDate->format("Y-n-j")."</td>";
				echo "<td>Has Previous : ".count($prev_row)."</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan='2'>Current Date : ".$bio->year_log."-".$bio->month_log."-".$bio->day_log."</td>";
				echo "<td>Has Current : ".count($cur_row)."</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan='3'>&nbsp;</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan='3'>";
				$valid = false;
				$curLog		= new DateTime($dateStr." ".$timeStr.":00");
				
				if($has_previous) {
					if($prev_row->shift_id > 0 && !$prev_row->leave_id) {
					
						$shift_in 	= new DateTime($prevDate->format("Y-m-d")." ".$prev_row->shift_hr_from.":".$prev_row->shift_min_from.":00");
						$shift_out 	= new DateTime($prevDate->format("Y-m-d")." ".$prev_row->shift_hr_to.":".$prev_row->shift_min_to.":00");

						if($shift_in > $shift_out)
							$shift_out->modify("+1 day");
						if($bio->in_out_mode == 0) {
							$tmp_shift_out = new DateTime($shift_out->format("Y-m-d H:i:s"));
							$tmp_shift_out->modify("+3 hours");
							if($curLog <= $tmp_shift_out ) {
								$timekeeping_query = "SELECT * 
														FROM tk_attendance ta
													  WHERE 
														`ta`.`mb_no` 	    = '".$prev_row->mb_no."' AND 
														`ta`.`att_date` 	= '".$shift_in->format("Y-m-d")."'";
								$attendance_res = $this->fix_m->query($timekeeping_query);
								$attendance_data = $attendance_res->row();

								if(count($attendance_data)==0) {
									echo "Previous Schedule : (".$prev_row->shift_id.") ".$prev_row->shift_hr_from.":".$prev_row->shift_min_from." - ".$prev_row->shift_hr_to.":".$prev_row->shift_min_to;
									echo "<br/>";
									echo "Time : ".$curLog->format("Y-m-d H:i:s");
									echo "<br/>";
									echo "Schedule In : ".$shift_in->format("Y-m-d H:i:s");
									echo "<br/>";
									echo "Schedule Out : ".$shift_out->format("Y-m-d H:i:s");
									echo "<br/>";
									echo "Remarks: User logged in before the previous shift ends - Inserted";
									echo "<br/>";
						  
									$tardy = ($curLog->format("U")-$shift_in->format("U"))/60;
									if($tardy < 0) $tardy = 0;
							
									$obt_query = "SELECT 
												  *,
												  CONCAT(`date`,' ',TIME(time_in)) full_timein,
												  IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
												FROM tk_obt_application `toa`
												WHERE
												  `toa`.`mb_no` = '".$prev_row->mb_no."' AND 
												  `toa`.`date` 	= '".$shift_in->format("Y-m-d")."' AND
												  TIME('".$timeStr."') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
												  AND `toa`.`status` = 3
												ORDER BY time_in ASC";
									$obt_res = $this->fix_m->query($obt_query);
									$obt_data = $obt_res->result();
									if(count($obt_data)) {
										foreach($obt_data as $obt_row) {
											$obt_from = new DateTime($obt_row->full_timein);
											$obt_to = new DateTime($obt_row->full_timeout);
											if($obt_from->format("U") <= $shift_in->format("U")) {
												$shift_in = $obt_to;
											}
											else
												break;
										}

										if($curLog->format("U") > $shift_in->format("U")) {
											$tardy = ($curLog->format("U")-$shift_in->format("U"))/60;
										}
										else {
											$tardy = 0;
										}
									}
									
									$insertQuery = "INSERT INTO tk_attendance
														(mb_no,att_date,shift_id,actual_in,actual_in_sec,tardy)
													  VALUES
														('".$prev_row->mb_no."',
														'".$prevDate->format("Y-m-d")."',
														'".$prev_row->shift_id."',
														'".$timeStr."',
														'".$bio->sec_log."',
														'".$tardy."');";
									$attendance_res = $this->fix_m->query($insertQuery);
									$valid = true;
								}
								else if(empty($attendance_data->actual_in)) {
									$tardy = ($curLog->format("U")-$shift_in->format("U"))/60;
									if($tardy < 0) $tardy = 0;
							
									$obt_query = "SELECT 
												  *,
												  CONCAT(`date`,' ',TIME(time_in)) full_timein,
												  IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
												FROM tk_obt_application `toa`
												WHERE
												  `toa`.`mb_no` = '".$prev_row->mb_no."' AND 
												  `toa`.`date` 	= '".$shift_in->format("Y-m-d")."' AND
												  TIME('".$timeStr."') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
												  AND `toa`.`status` = 3
												ORDER BY time_in ASC";
									$obt_res = $this->fix_m->query($obt_query);
									$obt_data = $obt_res->result();
									if(count($obt_data)) {
										foreach($obt_data as $obt_row) {
											$obt_from = new DateTime($obt_row->full_timein);
											$obt_to = new DateTime($obt_row->full_timeout);
											if($obt_from->format("U") <= $shift_in->format("U")) {
												$shift_in = $obt_to;
											}
											else
												break;
										}

										if($curLog->format("U") > $shift_in->format("U")) {
											$tardy = ($curLog->format("U")-$shift_in->format("U"))/60;
										}
										else {
											$tardy = 0;
										}
									}
							
									$insertQuery = "UPDATE tk_attendance
											  SET
											   actual_out = NULL,
											   actual_out_sec = '0',
											   undertime = '0',
											   overtime = '0',
											   reg_hrs = '0',
											   nsd = '0',
											   actual_in = '".$timeStr."',
											   actual_in_sec = '".$bio->sec_log."',
											   tardy = '".$tardy."'
											  WHERE
												mb_no = '".$prev_row->mb_no."' AND
												att_date = '".$prevDate->format("Y-m-d")."'";
									$attendance_res = $this->fix_m->query($insertQuery);
									$valid = true;
								}
								else if($has_current) {
									if($cur_row->shift_id > 0) {
										$shift_out 	= new DateTime($prevDate->format("Y-m-d")." ".$prev_row->shift_hr_to.":".$prev_row->shift_min_to.":00");
										$shift_in 	= new DateTime($curDate->format("Y-m-d")." ".$cur_row->shift_hr_from.":".$cur_row->shift_min_from.":00");
										// $valid = true;
										if($shift_out->format("His") == $shift_in->format("His")) {
											$shift_in->modify("-1 hours");
											$valid = true;
											if($shift_in <= $curLog)
												$valid = false;
										}
									}
								}
							}
						}
						else if($bio->in_out_mode == 1) {
							if($curLog <= $shift_out) {
								echo "Previous Schedule : (".$prev_row->shift_id.") ".$prev_row->shift_hr_from.":".$prev_row->shift_min_from." - ".$prev_row->shift_hr_to.":".$prev_row->shift_min_to;
								echo "<br/>";
								echo "Time : ".$curLog->format("Y-m-d H:i:s");
								echo "<br/>";
								echo "Schedule In : ".$shift_in->format("Y-m-d H:i:s");
								echo "<br/>";
								echo "Schedule Out : ".$shift_out->format("Y-m-d H:i:s");
								echo "<br/>";
								echo "Remarks: User logged out before the previous shift ends";
								$valid = true;
							}
							else if($has_current) {
								if($cur_row->shift_id > 0) {
									$cur_shift_in 	= new DateTime($curDate->format("Y-m-d")." ".$cur_row->shift_hr_from.":".$cur_row->shift_min_from.":00");
									$cur_shift_out 	= new DateTime($curDate->format("Y-m-d")." ".$cur_row->shift_hr_to.":".$cur_row->shift_min_to.":00");

									if($cur_shift_in > $cur_shift_out)
										$cur_shift_out->modify("+1 day");
									$cur_shift_in->modify("-1 hour");
									if($curLog < $cur_shift_in) {
										echo "Previous Schedule : (".$prev_row->shift_id.") ".$prev_row->shift_hr_from.":".$prev_row->shift_min_from." - ".$prev_row->shift_hr_to.":".$prev_row->shift_min_to;
										echo "<br/>";
										echo "Time : ".$curLog->format("Y-m-d H:i:s");
										echo "<br/>";
										echo "Schedule In : ".$shift_in->format("Y-m-d H:i:s");
										echo "<br/>";
										echo "Schedule Out : ".$shift_out->format("Y-m-d H:i:s");
										echo "<br/>";
										echo "Remarks: User logged out before the current shift starts";
							
										$timekeeping_query = "SELECT * 
																FROM tk_attendance ta
															  WHERE 
																`ta`.`mb_no` 	    = '".$prev_row->mb_no."' AND 
																`ta`.`att_date` 	= '".$curLog->format("Y-m-d")."'";
										$attendance_res = $this->fix_m->query($timekeeping_query);
										$attendance_data = $attendance_res->row();
										if(count($attendance_data)) {
											$timekeeping_query = "DELETE
																	  FROM
																		tk_attendance
																	  WHERE 
																		`att_id` 	    = '".$attendance_data->att_id."'";
											$attendance_res = $this->fix_m->query($timekeeping_query);
										}
										$cur_shift_in->modify("-3 hour");
										if($curLog < $cur_shift_in)
											$valid = true;
									}
									else {
										$cur_shift_in 	= new DateTime($curDate->format("Y-m-d")." ".$cur_row->shift_hr_from.":".$cur_row->shift_min_from.":00");
										$cur_shift2_in 	= new DateTime($curDate->format("Y-m-d")." ".$cur_row->shift_hr_from.":".$cur_row->shift_min_from.":00");
										$cur_shift2_in->modify("+2 hours");
										if($cur_shift_in == $shift_out && $cur_shift2_in > $curLog) {
											echo "Previous Schedule : (".$prev_row->shift_id.") ".$prev_row->shift_hr_from.":".$prev_row->shift_min_from." - ".$prev_row->shift_hr_to.":".$prev_row->shift_min_to;
											echo "<br/>";
											echo "Time : ".$curLog->format("Y-m-d H:i:s");
											echo "<br/>";
											echo "Schedule In : ".$shift_in->format("Y-m-d H:i:s");
											echo "<br/>";
											echo "Schedule Out : ".$shift_out->format("Y-m-d H:i:s");
											echo "<br/>";
											echo "Remarks: User logged out before the current shift starts 2";
											$valid = true;
										}
									}
								}
								else {
									if($curLog > $shift_out) {
										$timekeeping_query = "SELECT * 
												FROM tk_attendance ta
											  WHERE 
												`ta`.`mb_no` 	    = '".$prev_row->mb_no."' AND 
												`ta`.`att_date` 	= '".$curLog->format("Y-m-d")."'";
										$attendance_res = $this->fix_m->query($timekeeping_query);
										$attendance_data = $attendance_res->row();
							
										if(count($attendance_data) == 0) {
											echo "Previous Schedule : (".$prev_row->shift_id.") ".$prev_row->shift_hr_from.":".$prev_row->shift_min_from." - ".$prev_row->shift_hr_to.":".$prev_row->shift_min_to;
											echo "<br/>";
											echo "Time : ".$curLog->format("Y-m-d H:i:s");
											echo "<br/>";
											echo "Schedule In : ".$shift_in->format("Y-m-d H:i:s");
											echo "<br/>";
											echo "Schedule Out : ".$shift_out->format("Y-m-d H:i:s");
											echo "<br/>";
											echo "Remarks: User logged out. Current N/A Shift";
											$timekeeping_query = "SELECT * 
																	FROM tk_attendance ta
																  WHERE 
																	`ta`.`mb_no` 	    = '".$prev_row->mb_no."' AND 
																	`ta`.`att_date` 	= '".$shift_in->format("Y-m-d")."'";
											$attendance_res = $this->fix_m->query($timekeeping_query);
											$attendance_data = $attendance_res->row();
											if(count($attendance_data) == 0)
												$valid = true;
											else if($attendance_data->actual_out) {
												$previous_shift_in = new DateTime($attendance_data->att_date." ".$attendance_data->actual_in);
												$previous_shift_out = new DateTime($attendance_data->att_date." ".$attendance_data->actual_out);
												if($previous_shift_out<$previous_shift_in)
													$previous_shift_out->modify("+1 day");
												$previous_out = new DateTime($previous_shift_out->format("Y-m-d ").$attendance_data->actual_out);
												if($curLog > $previous_out && $curLog->format("Y-m-d") == $previous_out->format("Y-m-d"))
													$valid = true;
												else
													echo "<br/><br/>";
											}
											else
												$valid = true;
										}
									}
								}
							}
							if($valid) {
								$timekeeping_query = "SELECT * 
														FROM tk_attendance ta
													  WHERE 
														`ta`.`mb_no` 	    = '".$prev_row->mb_no."' AND 
														`ta`.`att_date` 	= '".$shift_in->format("Y-m-d")."'";
								$attendance_res = $this->fix_m->query($timekeeping_query);
								$attendance_data = $attendance_res->row();
						
								$excess 			= 0;
								$nsd				= 0;
								$regHrs				= 0;
								if(count($attendance_data)==0) {
									$undertime = ($shift_out->format("U")-$curLog->format("U"))/60;
									if($undertime < 0) $undertime = 0;
						  
									if($curLog->format("U") < $shift_out->format("U")) {
										$obt_query = "SELECT 
												  *,
												  CONCAT(`date`,' ',TIME(time_in)) full_timein,
												  IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
												FROM tk_obt_application `toa`
												WHERE
												  `toa`.`mb_no` = '".$cur_row->mb_no."' AND 
												  `toa`.`date` 	= '".$shift_in->format("Y-m-d")."' AND
												  TIME('".$timeStr."') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
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
												echo "<br/>Undertime Orig: ".$undertime;
												$undertime = 0;
												echo "<br/>Undertime OBT: ".$undertime;
											}
										}
									}
							
									if($curLog > $shift_out) {
										$excessOut 	= ($curLog->format("U")-$shift_out->format("U"));
										$excess		+= floor($excessOut/3600);
										$countedOut 	= new DateTime($shift_out->format("Y-m-d H:i:s"));
									}
									else {
										$countedOut 		= new DateTime($curLog->format("Y-m-d H:i:s"));
									}
						  
									$insertQuery = "INSERT INTO tk_attendance
														(mb_no,att_date,shift_id,actual_out,actual_out_sec,undertime,overtime)
													  VALUES
														('".$prev_row->mb_no."',
														'".$shift_in->format("Y-m-d")."',
														'".$prev_row->shift_id."',
														'".$timeStr."',
														'".$bio->sec_log."',
														'".$undertime."',
														'".$excess."');";
									$attendance_res = $this->fix_m->query($insertQuery);
								}
								else {
									if($attendance_data->actual_in != "") {
										$nsdDateIn = new DateTime($shift_in->format("Y-m-d 22:00:00"));
										$nsdDateOut = new DateTime($shift_in->format("Y-m-d 22:00:00"));
										// Custom by HR
										if($shift_out->format("Hi") == "0700")
											$nsdDateOut->modify("+9 hour");
										else
											$nsdDateOut->modify("+8 hour");
									  
										$tmpDateIn = new DateTime($attendance_data->att_date." ".$attendance_data->actual_in.":00");
										if($tmpDateIn < $shift_in) {
											$tmpDateIn->modify("+1 day");
										}
										
										if($tmpDateIn > $curLog)
											$tmpDateIn->modify("-1 day");
										
										if($tmpDateIn < $shift_in) {
											$excessIn 	= ($shift_in->format("U")-$tmpDateIn->format("U"));
											$excess		+= floor($excessIn/3600);
										}
							
										$countedIn 	= new DateTime($shift_in->format("Y-m-d H:i:s"));
						  
										if($curLog > $shift_out) {
											$excessOut 	= ($curLog->format("U")-$shift_out->format("U"));
											$excess		+= floor($excessOut/3600);
										}
							
										$countedOut 	= new DateTime($shift_out->format("Y-m-d H:i:s"));
							
										$regHrsHr = ($countedOut->format("U")-$countedIn->format("U"))/3600;
										$regHrs = floor($regHrsHr)+((($regHrsHr-floor($regHrsHr))*60)/100);
							
										if($shift_in < $nsdDateIn)
											$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:i:s"));
										else {
											if($shift_in->format("i") != "00"){
												$countedIn 	= new DateTime($shift_in->format("Y-m-d :00:00"));
											}
											else
												$countedIn 	= new DateTime($shift_in->format("Y-m-d H:00:00"));
										}
						  
										if($shift_out > $nsdDateOut)
											$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
										else
											$countedOut 		= new DateTime($shift_out->format("Y-m-d H:i:s"));
										  
										$nsd = floor(($countedOut->format("U")-$countedIn->format("U"))/3600);
										if($nsd < 0) $nsd = 0;
									}
									$undertime = ($shift_out->format("U")-$curLog->format("U"))/60;
									if($undertime < 0) $undertime = 0;
						  
									if($curLog->format("U") < $shift_out->format("U")) {
										$obt_query = "SELECT 
													  *,
													  CONCAT(`date`,' ',TIME(time_in)) full_timein,
													  IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
													FROM tk_obt_application `toa`
													WHERE
													  `toa`.`mb_no` = '".$cur_row->mb_no."' AND 
													  `toa`.`date` 	= '".$shift_in->format("Y-m-d")."' AND
													  TIME('".$timeStr."') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
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
												echo "<br/>Undertime Orig: ".$undertime;
												$undertime = 0;
												echo "<br/>Undertime OBT: ".$undertime;
											}
										}
									}
						
									$insertQuery = "UPDATE tk_attendance
											  SET
											   actual_out = '".$timeStr."',
											   actual_out_sec = '".$bio->sec_log."',
											   undertime = '".$undertime."',
											   overtime = '".$excess."',
											   reg_hrs = '".$regHrs."',
											   nsd = '".$nsd."'
											  WHERE
												mb_no = '".$prev_row->mb_no."' AND
												att_date = '".$shift_in->format("Y-m-d")."'";
									$attendance_res = $this->fix_m->query($insertQuery);
								}
							}
						}
					}
				}

				if($has_current && !$valid) {
					if($cur_row->shift_id > 0 && !$cur_row->leave_id) {
					
						$shift_in 	= new DateTime($curDate->format("Y-m-d")." ".$cur_row->shift_hr_from.":".$cur_row->shift_min_from.":00");
						$shift_out 	= new DateTime($curDate->format("Y-m-d")." ".$cur_row->shift_hr_to.":".$cur_row->shift_min_to.":00");
					
						if($shift_in > $shift_out)
							$shift_out->modify("+1 day");
					
						if($bio->in_out_mode == 0) {
							echo "Current Schedule : (".$cur_row->shift_id.") ".$cur_row->shift_hr_from.":".$cur_row->shift_min_from." - ".$cur_row->shift_hr_to.":".$cur_row->	shift_min_to;
							echo "<br/>";
							echo "Time : ".$curLog->format("Y-m-d H:i:s");
							echo "<br/>";
							echo "Schedule In : ".$shift_in->format("Y-m-d H:i:s");
							echo "<br/>";
							echo "Schedule Out : ".$shift_out->format("Y-m-d H:i:s");
							echo "<br/>";
							echo "Remarks: User logged in - Invalid Prev Sched";
							$valid = true;
					  
							$timekeeping_query = "SELECT * 
													FROM tk_attendance ta
												  WHERE 
													`ta`.`mb_no` 	    = '".$cur_row->mb_no."' AND 
													`ta`.`att_date` 	= '".$shift_in->format("Y-m-d")."'";
							$attendance_res = $this->fix_m->query($timekeeping_query);
							$attendance_data = $attendance_res->row();
					  
							$tardy = ($curLog->format("U")-$shift_in->format("U"))/60;
							if($tardy < 0) $tardy = 0;
					  
							$obt_query = "SELECT 
										  *,
										  CONCAT(`date`,' ',TIME(time_in)) full_timein,
										  IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
										FROM tk_obt_application `toa`
										WHERE
										  `toa`.`mb_no` = '".$cur_row->mb_no."' AND 
										  `toa`.`date` 	= '".$shift_in->format("Y-m-d")."' AND
										  TIME('".$timeStr."') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
										  AND `toa`.`status` = 3
										ORDER BY time_in ASC";
							$obt_res = $this->fix_m->query($obt_query);
							$obt_data = $obt_res->result();
							if(count($obt_data)) {
								foreach($obt_data as $obt_row) {
									$obt_from = new DateTime($obt_row->full_timein);
									$obt_to = new DateTime($obt_row->full_timeout);
									if($obt_from->format("U") <= $shift_in->format("U")) {
										$shift_in = $obt_to;
									}
									else
										break;
								}

								if($curLog->format("U") > $shift_in->format("U")) {
									$tardy = ($curLog->format("U")-$shift_in->format("U"))/60;
								}
								else {
									$tardy = 0;
								}
							}
					  
							if(count($attendance_data)==0) {
								$insertQuery = "INSERT INTO tk_attendance
											(mb_no,att_date,shift_id,actual_in,actual_in_sec,tardy)
										  VALUES
											('".$cur_row->mb_no."',
											'".$shift_in->format("Y-m-d")."',
											'".$cur_row->shift_id."',
											'".$timeStr."',
											'".$bio->sec_log."',
											'".$tardy."');";
								$attendance_res = $this->fix_m->query($insertQuery);
							}
							else if(empty($attendance_data->actual_in) || $timeStr == $attendance_data->actual_in) {
								$insertQuery = "UPDATE tk_attendance
											  SET
											   actual_out = NULL,
											   actual_out_sec = '0',
											   undertime = '0',
											   overtime = '0',
											   reg_hrs = '0',
											   nsd = '0',
											   actual_in = '".$timeStr."',
											   actual_in_sec = '".$bio->sec_log."',
											   tardy = '".$tardy."'
											  WHERE
												mb_no = '".$cur_row->mb_no."' AND
												att_date = '".$shift_in->format("Y-m-d")."'";
								$attendance_res = $this->fix_m->query($insertQuery);
							}
							else if($has_next) {
								// print_r($next_row);
								$tmpCurDate = new DateTime($curDate->format("Y-m-d")." 00:00:00");
								$tmpCurDate->modify("+1 day");
						
								if($next_row->shift_hr_from) {
									$shift_in 	= new DateTime($tmpCurDate->format("Y-m-d")." ".$next_row->shift_hr_from.":".$next_row->shift_min_from.":00");
									$shift_out 	= new DateTime($tmpCurDate->format("Y-m-d")." ".$next_row->shift_hr_to.":".$next_row->shift_min_to.":00");
						
									if($shift_in > $shift_out)
										$shift_out->modify("+1 day");
						  
									$shift_in->modify("-4 hours");
						
									// echo $shift_in->format("Y-m-d H:i:s")." < ".$curLog->format("Y-m-d H:i:s");
									if($shift_in < $curLog) {
										echo "<br/>";
										echo "Next Schedule : (".$next_row->shift_id.") ".$next_row->shift_hr_from.":".$next_row->shift_min_from." - ".$next_row->shift_hr_to.":".$next_row->shift_min_to;
										echo "<br/>";
										echo "Time : ".$curLog->format("Y-m-d H:i:s");
										echo "<br/>";
										echo "Schedule In : ".$shift_in->format("Y-m-d H:i:s");
										echo "<br/>";
										echo "Schedule Out : ".$shift_out->format("Y-m-d H:i:s");
										echo "<br/>";
										echo "Remarks: User logged in 4 hours before next shift - Invalid Prev Sched";
							
										$shift_in->modify("+4 hours");
										$timekeeping_query = "SELECT * 
														FROM tk_attendance ta
													  WHERE 
														`ta`.`mb_no` 	    = '".$next_row->mb_no."' AND 
														`ta`.`att_date` 	= '".$shift_in->format("Y-m-d")."'";
										$attendance_res = $this->fix_m->query($timekeeping_query);
										$attendance_data = $attendance_res->row();
							
										if(count($attendance_data)==0) {
											$tardy = ($curLog->format("U")-$shift_in->format("U"))/60;
											if($tardy < 0)
											  $tardy = 0;
											
											$obt_query = "SELECT 
														  *,
														  CONCAT(`date`,' ',TIME(time_in)) full_timein,
														  IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
														FROM tk_obt_application `toa`
														WHERE
														  `toa`.`mb_no` = '".$cur_row->mb_no."' AND 
														  `toa`.`date` 	= '".$shift_in->format("Y-m-d")."' AND
														  TIME('".$timeStr."') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
														  AND `toa`.`status` = 3
														ORDER BY time_in ASC";
											$obt_res = $this->fix_m->query($obt_query);
											$obt_data = $obt_res->result();
											if(count($obt_data)) {
												foreach($obt_data as $obt_row) {
													$obt_from = new DateTime($obt_row->full_timein);
													$obt_to = new DateTime($obt_row->full_timeout);
													if($obt_from->format("U") <= $shift_in->format("U")) {
													  $shift_in = $obt_to;
													}
													else
													  break;
												}

												if($curLog->format("U") > $shift_in->format("U")) {
													$tardy = ($curLog->format("U")-$shift_in->format("U"))/60;
												}
												else {
													$tardy = 0;
												}
											}
							
											$insertQuery = "INSERT INTO tk_attendance
																(mb_no,att_date,shift_id,actual_in,actual_in_sec,tardy)
															  VALUES
																('".$next_row->mb_no."',
																'".$shift_in->format("Y-m-d")."',
																'".$next_row->shift_id."',
																'".$timeStr."',
																'".$bio->sec_log."',
																'".$tardy."');";
											$attendance_res = $this->fix_m->query($insertQuery);
										}
									}
								}
							}
						}
						else if($bio->in_out_mode == 1 && $curLog >= $shift_in) {
							echo "Current Schedule : (".$cur_row->shift_id.") ".$cur_row->shift_hr_from.":".$cur_row->shift_min_from." - ".$cur_row->shift_hr_to.":".$cur_row->shift_min_to;
							echo "<br/>";
							echo "Time : ".$curLog->format("Y-m-d H:i:s");
							echo "<br/>";
							echo "Schedule In : ".$shift_in->format("Y-m-d H:i:s");
							echo "<br/>";
							echo "Schedule Out : ".$shift_out->format("Y-m-d H:i:s");
							echo "<br/>";
							echo "Remarks: User logged out after the current shift start - Invalid Prev Sched";
							$valid = true;
					  
							$timekeeping_query = "SELECT * 
										FROM tk_attendance ta
									  WHERE 
										`ta`.`mb_no` 	    = '".$cur_row->mb_no."' AND 
										`ta`.`att_date` 	= '".$shift_in->format("Y-m-d")."'";
							$attendance_res = $this->fix_m->query($timekeeping_query);
							$attendance_data = $attendance_res->row();
							$excess 			= 0;
							$nsd				= 0;
							$regHrs				= 0;
							if(count($attendance_data)==0) {
								$undertime = ($shift_out->format("U")-$curLog->format("U"))/60;
								if($undertime < 0) $undertime = 0;
						
								if($curLog->format("U") < $shift_out->format("U")) {
									$obt_query = "SELECT 
												  *,
												  CONCAT(`date`,' ',TIME(time_in)) full_timein,
												  IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
												FROM tk_obt_application `toa`
												WHERE
												  `toa`.`mb_no` = '".$cur_row->mb_no."' AND 
												  `toa`.`date` 	= '".$shift_in->format("Y-m-d")."' AND
												  TIME('".$timeStr."') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
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
											echo "<br/>Undertime Orig: ".$undertime;
											$undertime = 0;
											echo "<br/>Undertime OBT: ".$undertime;
										}
									}
								}
							
								if($curLog > $shift_out) {
									$excessOut 	= ($curLog->format("U")-$shift_out->format("U"));
									$excess		+= floor($excessOut/3600);
									$countedOut 	= new DateTime($shift_out->format("Y-m-d H:i:s"));
								}
								else {
									$countedOut 		= new DateTime($curLog->format("Y-m-d H:i:s"));
								}
					   
								$insertQuery = "INSERT INTO tk_attendance
														(mb_no,att_date,shift_id,actual_out,actual_out_sec,undertime,overtime)
													  VALUES
														('".$cur_row->mb_no."',
														'".$shift_in->format("Y-m-d")."',
														'".$cur_row->shift_id."',
														'".$timeStr."',
														'".$bio->sec_log."',
														'".$undertime."',
														'".$excess."');";
								$attendance_res = $this->fix_m->query($insertQuery);
							}
							else {
								if($attendance_data->actual_in != "") {
									$nsdDateIn = new DateTime($shift_in->format("Y-m-d 22:00:00"));
									$nsdDateOut = new DateTime($shift_in->format("Y-m-d 22:00:00"));
									// Custom by HR
									if($shift_out->format("Hi") == "0700")
										$nsdDateOut->modify("+9 hour");
									else
										$nsdDateOut->modify("+8 hour");
								  
									$tmpDateIn = new DateTime($attendance_data->att_date." ".$attendance_data->actual_in.":00");
								  
									if($tmpDateIn > $curLog)
										$tmpDateIn->modify("-1 day");
								  
									if($tmpDateIn < $shift_in) {
										$excessIn 	= ($shift_in->format("U")-$tmpDateIn->format("U"));
										$excess		+= floor($excessIn/3600);
									}
								  
									$countedIn 	= new DateTime($shift_in->format("Y-m-d H:i:s"));
								  
									if($curLog > $shift_out) {
										$excessOut 	= ($curLog->format("U")-$shift_out->format("U"));
										$excess		+= floor($excessOut/3600);
									}
								  
									$countedOut 	= new DateTime($shift_out->format("Y-m-d H:i:s"));
								  
									$regHrsHr = ($countedOut->format("U")-$countedIn->format("U"))/3600;
									$regHrs = floor($regHrsHr)+((($regHrsHr-floor($regHrsHr))*60)/100);
								  
									if($shift_in < $nsdDateIn)
										$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:i:s"));
									else
										$countedIn 	= new DateTime($shift_in->format("Y-m-d H:i:s"));
								  
									if($shift_out > $nsdDateOut)
										$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
									else
										$countedOut 		= new DateTime($shift_out->format("Y-m-d H:i:s"));
									  
									$nsd = floor(($countedOut->format("U")-$countedIn->format("U"))/3600);
									if($nsd < 0) $nsd = 0;
								}
						
								$undertime = 0;
								$undertime = ($shift_out->format("U")-$curLog->format("U"))/60;
								if($undertime < 0) $undertime = 0;
						
								if($curLog->format("U") < $shift_out->format("U")) {
									$obt_query = "SELECT 
												  *,
												  CONCAT(`date`,' ',TIME(time_in)) full_timein,
												  IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
												FROM tk_obt_application `toa`
												WHERE
												  `toa`.`mb_no` = '".$cur_row->mb_no."' AND 
												  `toa`.`date` 	= '".$shift_in->format("Y-m-d")."' AND
												  TIME('".$timeStr."') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
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
											echo "<br/>Undertime Orig: ".$undertime;
											$undertime = 0;
											echo "<br/>Undertime OBT: ".$undertime;
										}
									}
								}
						
								$insertQuery = "UPDATE tk_attendance
											  SET
											   actual_out = '".$timeStr."',
											   actual_out_sec = '".$bio->sec_log."',
											   undertime = '".$undertime."',
											   overtime = '".$excess."',
											   reg_hrs = '".$regHrs."',
											   nsd = '".$nsd."'
											  WHERE
												mb_no = '".$cur_row->mb_no."' AND
												att_date = '".$shift_in->format("Y-m-d")."'";
								$attendance_res = $this->fix_m->query($insertQuery);
							}
						}
						else if($bio->in_out_mode == 1) {
							$shift_in_tmp = new DateTime($curDate->format("Y-m-d")." ".$cur_row->shift_hr_from.":".$cur_row->shift_min_from.":00");
							$shift_in_tmp->modify("-3 hours");
							if($curLog < $shift_in_tmp) {
								$timekeeping_query = "SELECT * 
														FROM tk_attendance ta
														INNER JOIN g4_member gm ON ta.mb_no = gm.mb_no
													  WHERE 
														`gm`.`enroll_number` 	    = '".$bio->enroll_number."' AND 
														`ta`.`att_date` 	= '".$prevDate->format("Y-m-d")."'";
								$attendance_res = $this->fix_m->query($timekeeping_query);
								$attendance_data = $attendance_res->row();
						
								if(count($attendance_data) == 0) {
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
														('".$cur_row->mb_no."',
														'".$prevDate->format("Y-m-d")."',
														'".$timeStr."',
														'".$bio->sec_log."',
														'".$undertime."');";
									echo $insertQuery;
									$attendance_res = $this->fix_m->query($insertQuery);
									echo "<br/>";
									echo "Remarks: No Previous Attendance. Out 3 hours before current shift starts";
								}
								else {
									$undertime = 0;
									$excess = 0;
									$regHrs = 0;
									$nsd = 0;
									if($attendance_data->actual_in != "") {
										if($has_previous && $prev_row->shift_id > 0) {
											$nsdDateIn = new DateTime($shift_in->format("Y-m-d 22:00:00"));
											$nsdDateOut = new DateTime($shift_in->format("Y-m-d 22:00:00"));
											// Custom by HR
											if($shift_out->format("Hi") == "0700")
												$nsdDateOut->modify("+9 hour");
											else
												$nsdDateOut->modify("+8 hour");
							  
											$tmpDateIn = new DateTime($attendance_data->att_date." ".$attendance_data->actual_in.":00");
											if($tmpDateIn > $curLog)
												$tmpDateIn->modify("-1 day");
								
											if($tmpDateIn < $shift_in) {
												$excessIn 	= ($shift_in->format("U")-$tmpDateIn->format("U"));
												$excess		+= floor($excessIn/3600);
											}
								
											$countedIn 	= new DateTime($shift_in->format("Y-m-d H:i:s"));
							  
											if($curLog > $shift_out) {
												$excessOut 	= ($curLog->format("U")-$shift_out->format("U"));
												$excess		+= floor($excessOut/3600);
											}
								
											$countedOut 	= new DateTime($shift_out->format("Y-m-d H:i:s"));
								
											$regHrsHr = ($countedOut->format("U")-$countedIn->format("U"))/3600;
											$regHrs = floor($regHrsHr)+((($regHrsHr-floor($regHrsHr))*60)/100);
								
											if($shift_in < $nsdDateIn)
												$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:i:s"));
											else
												$countedIn 	= new DateTime($shift_in->format("Y-m-d H:i:s"));
							  
											if($shift_out > $nsdDateOut)
												$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
											else
												$countedOut 		= new DateTime($shift_out->format("Y-m-d H:i:s"));
								  
											$nsd = floor(($countedOut->format("U")-$countedIn->format("U"))/3600);
											if($nsd < 0) $nsd = 0;
							  
											$undertime = ($shift_out->format("U")-$curLog->format("U"))/60;
											if($undertime < 0) $undertime = 0;
											echo "<br/>";
											echo $undertime;
											if($curLog->format("U") < $shift_out->format("U")) {
												$obt_query = "SELECT 
														  *,
														  CONCAT(`date`,' ',TIME(time_in)) full_timein,
														  IF(TIME(time_in) > TIME(time_out), DATE_ADD(CONCAT(`date`,' ',TIME(time_out)), INTERVAL 1 DAY), CONCAT(`date`,' ',TIME(time_out))) full_timeout
														FROM tk_obt_application `toa`
														WHERE
														  `toa`.`mb_no` = '".$cur_row->mb_no."' AND 
														  `toa`.`date` 	= '".$shift_in->format("Y-m-d")."' AND
														  TIME('".$timeStr."') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
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
														echo "<br/>Undertime Orig: ".$undertime;
														$undertime = 0;
														echo "<br/>Undertime OBT: ".$undertime;
													}
												}
											}
										}
										else {
											if($attendance_data->actual_in != "") {
												$tmpDateIn = new DateTime($attendance_data->att_date." ".$attendance_data->actual_in.":00");
											
												$nsdDateIn = new DateTime($tmpDateIn->format("Y-m-d 22:00:00"));
												$nsdDateOut = new DateTime($tmpDateIn->format("Y-m-d 22:00:00"));
												$nsdDateOut->modify("+8 hour");
											
												$regHrsHr = ($curLog->format("U")-$tmpDateIn->format("U"))/3600;
												$regHrs = floor($regHrsHr)+((($regHrsHr-floor($regHrsHr))*60)/100);
												$excess = floor($regHrs);
											
												if($tmpDateIn < $nsdDateIn)
													$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:00:00"));
												else
													$countedIn 	= new DateTime($tmpDateIn->format("Y-m-d H:00:00"));
										  
												if($curLog > $nsdDateOut)
													$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
												else {
													if($curLog->format("i") != "00")
														$curLog->modify("+1 hour");
													$countedOut 		= new DateTime($curLog->format("Y-m-d H:00:00"));
													if($curLog->format("i") != "00")
														$curLog->modify("-1 hour");
												}
											
												$nsd = floor(($countedOut->format("U")-$countedIn->format("U"))/3600);
												if($nsd < 0) $nsd = 0;
											
											}
										}
									}
						  
									$insertQuery = "UPDATE tk_attendance
											  SET
											   actual_out = '".$timeStr."',
											   actual_out_sec = '".$bio->sec_log."',
											   undertime = '".$undertime."',
											   overtime = '".$excess."',
											   reg_hrs = '".$regHrs."',
											   nsd = '".$nsd."'
											  WHERE
												mb_no = '".$cur_row->mb_no."' AND
												att_date = '".$prevDate->format("Y-m-d")."'";
									echo $insertQuery;
									$attendance_res = $this->fix_m->query($insertQuery);
									echo "<br/>";
									echo "Remarks: Have Previous Attendance. Out 3 hours before current shift starts";
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
											`gm`.`enroll_number` 	    = '".$bio->enroll_number."' AND 
											`ta`.`att_date` 	= '".$curLog->format("Y-m-d")."'";
					$attendance_res = $this->fix_m->query($timekeeping_query);
					$attendance_data = $attendance_res->row();
				  
					$prev_timekeeping_query = "SELECT * 
									FROM tk_attendance ta
									INNER JOIN g4_member gm ON ta.mb_no = gm.mb_no
								  WHERE 
									`gm`.`enroll_number` 	    = '".$bio->enroll_number."' AND 
									`ta`.`att_date` 	= '".$prevDate->format("Y-m-d")."'";
					$prev_attendance_res = $this->fix_m->query($prev_timekeeping_query);
					$prev_attendance_data = $prev_attendance_res->row();
				  
					$emp_query = "SELECT * 
									FROM g4_member gm 
								  WHERE 
									`gm`.`enroll_number` 	    = '".$bio->enroll_number."'";
					$emp_res = $this->fix_m->query($emp_query);
					$emp_data = $emp_res->result();
				  
					if($bio->in_out_mode == 0) {
						echo "Current Schedule : (N/A)";
						echo "<br/>";
						echo "Time : ".$curLog->format("Y-m-d H:i:s");
						echo "<br/>";
						echo "Schedule In : N/A";
						echo "<br/>";
						echo "Schedule Out : N/A";
						echo "<br/>";
						echo "Remarks: User logged in. No Schedule";
					
						if(count($attendance_data)==0) {
							$insertQuery = "INSERT INTO tk_attendance
											(mb_no,att_date,actual_in,actual_in_sec)
										  VALUES
											('".(count($emp_data)?$emp_data[0]->mb_no:0)."',
											'".$curLog->format("Y-m-d")."',
											'".$timeStr."',
											'".$bio->sec_log."');";
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
										   actual_in = '".$timeStr."',
										   actual_in_sec = '".$bio->sec_log."',
										   tardy = '0'
										  WHERE
											mb_no = '".(count($emp_data)?$emp_data[0]->mb_no:0)."' AND
											att_date = '".$curLog->format("Y-m-d")."'";
							$attendance_res = $this->fix_m->query($insertQuery);
						}
					}
					else {
						if(count($prev_attendance_data) && !count($attendance_data)) {
							echo "Current Schedule : (N/A)";
							echo "<br/>";
							echo "Time : ".$curLog->format("Y-m-d H:i:s");
							echo "<br/>";
							echo "Schedule In : N/A";
							echo "<br/>";
							echo "Schedule Out : N/A";
							echo "<br/>";
							echo "Remarks: User logged out. No Schedule. Has Previous Attendance. No Current";
							$regHrs = $nsd = $excess = 0;
							if($prev_attendance_data->actual_in != "") {
								$tmpDateIn = new DateTime($prev_attendance_data->att_date." ".$prev_attendance_data->actual_in.":00");
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
									if($shift_out->format("Hi") == "0700")
										$nsdDateOut->modify("+9 hour");
									else
										$nsdDateOut->modify("+8 hour");
						  
									$regHrsHr = ($shift_out->format("U")-$shift_in->format("U"))/3600;
									$regHrs = floor($regHrsHr)+((($regHrsHr-floor($regHrsHr))*60)/100);
						  
									if($curLog > $shift_out) {
										$excessOut 	= ($curLog->format("U")-$shift_out->format("U"));
										$excess		+= floor($excessOut/3600);
									}
						  
									if($shift_in < $nsdDateIn)
										$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:i:s"));
									else {
										if($shift_in->format("i") != "00"){
											$countedIn 	= new DateTime($shift_in->format("Y-m-d :00:00"));
										}
										else
											$countedIn 	= new DateTime($shift_in->format("Y-m-d H:00:00"));
									}
						  
									if($shift_out > $nsdDateOut)
										$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:i:s"));
									else
										$countedOut 		= new DateTime($shift_out->format("Y-m-d H:i:s"));
								}
								else {
									$regHrsHr = ($curLog->format("U")-$tmpDateIn->format("U"))/3600;
									$regHrs = floor($regHrsHr)+((($regHrsHr-floor($regHrsHr))*60)/100);
									$excess = floor($regHrs);
						
									if($tmpDateIn < $nsdDateIn)
										$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:00:00"));
									else
										$countedIn 	= new DateTime($tmpDateIn->format("Y-m-d H:00:00"));
					  
									if($curLog > $nsdDateOut)
										$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:00:00"));
									else {
										if($curLog->format("i") != "00")
											$curLog->modify("+1 hour");
										$countedOut 		= new DateTime($curLog->format("Y-m-d H:00:00"));
										if($curLog->format("i") != "00")
											$curLog->modify("-1 hour");
									}
								}
						
								$nsd = floor(($countedOut->format("U")-$countedIn->format("U"))/3600);
								if($nsd < 0) $nsd = 0;
						
							}
							$insertQuery = "UPDATE tk_attendance
										  SET
										   actual_out 		= '".$timeStr."',
										   actual_out_sec 	= '".$bio->sec_log."',
										   overtime 		= '".$excess."',
										   reg_hrs 			= '".$regHrs."',
										   nsd 				= '".$nsd."'
										  WHERE
											mb_no = '".(count($emp_data)?$emp_data[0]->mb_no:0)."' AND
											att_date = '".$prevDate->format("Y-m-d")."'";
							$attendance_res = $this->fix_m->query($insertQuery);
							$valid = true;
						}
						if(!$valid) {
						
							echo "Current Schedule : (N/A)";
							echo "<br/>";
							echo "Time : ".$curLog->format("Y-m-d H:i:s");
							echo "<br/>";
							echo "Schedule In : N/A";
							echo "<br/>";
							echo "Schedule Out : N/A";
							echo "<br/>";
							echo "Remarks: User logged out. No Schedule";
							
							$excess 			= 0;
							$nsd				= 0;
							$regHrs				= 0;
							if(count($attendance_data)==0) {
								$insertQuery = "INSERT INTO tk_attendance
														(mb_no,att_date,actual_out,actual_out_sec)
													  VALUES
														('".(count($emp_data)?$emp_data[0]->mb_no:0)."',
														'".$curLog->format("Y-m-d")."',
														'".$timeStr."',
														'".$bio->sec_log."');";
								$attendance_res = $this->fix_m->query($insertQuery);
							}
							else {
								if($attendance_data->actual_in != "") {
									$tmpDateIn = new DateTime($attendance_data->att_date." ".$attendance_data->actual_in.":00");
								
									$nsdDateIn = new DateTime($tmpDateIn->format("Y-m-d 22:00:00"));
									$nsdDateOut = new DateTime($tmpDateIn->format("Y-m-d 22:00:00"));
									$nsdDateOut->modify("+8 hour");
								
									$regHrsHr = ($curLog->format("U")-$tmpDateIn->format("U"))/3600;
									$regHrs = floor($regHrsHr)+((($regHrsHr-floor($regHrsHr))*60)/100);
									$excess = floor($regHrs);
								
									if($tmpDateIn < $nsdDateIn)
										$countedIn 	= new DateTime($nsdDateIn->format("Y-m-d H:00:00"));
									else
										$countedIn 	= new DateTime($tmpDateIn->format("Y-m-d H:00:00"));
							  
									if($curLog > $nsdDateOut)
										$countedOut 	= new DateTime($nsdDateOut->format("Y-m-d H:00:00"));
									else {
										if($curLog->format("i") != "00")
											$curLog->modify("+1 hour");
										$countedOut 		= new DateTime($curLog->format("Y-m-d H:00:00"));
										if($curLog->format("i") != "00")
											$curLog->modify("-1 hour");
									}
									$nsd = floor(($countedOut->format("U")-$countedIn->format("U"))/3600);
									if($nsd < 0) $nsd = 0;
								
								}
								  $insertQuery = "UPDATE tk_attendance
												  SET
												   actual_out 		= '".$timeStr."',
												   actual_out_sec 	= '".$bio->sec_log."',
												   overtime 		= '".$excess."',
												   reg_hrs 			= '".$regHrs."',
												   nsd 				= '".$nsd."'
												  WHERE
													mb_no = '".(count($emp_data)?$emp_data[0]->mb_no:0)."' AND
													att_date = '".$curLog->format("Y-m-d")."'";
								  $attendance_res = $this->fix_m->query($insertQuery);
							}
						}
					}
				}
				
				echo "</td>";
				echo "</tr>";
				echo "</table>";
				echo "<br/><br/><br/>";
			}
		}
	}
	
	public function leaveAccrual() {
	  $timekeeping_query 	= "select * from g4_member where mb_employment_status = 2 AND mb_status = 1";
	  $all_regular 			= $this->fix_m->query($timekeeping_query);
	  $regulars 			= $all_regular->result();
	  $year =  (new DateTime())->modify("+1 year")->format("Y");
	  foreach($regulars as $regular) {
	    $this->fix_m->setRegLeaveBalanceAnnual($regular->mb_no,$regular->mb_3,$regular->mb_deptno, $year);
	  }
	}
	
}
?>