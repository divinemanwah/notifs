<?php
	
	$rec = $_POST['recs'];
	
	//Get IP Address of Client's
	$ip_add = get_client_ip();
	
	//Database Information
	$servername = "localhost";
	$username = "intra_user";
	$password = "JPyQ4cX8m6XpFmh5";
	$dbname = "intra";
	
	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	
	// Check connection
	if (!$conn) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$split_rec = "";
	$cnt_save_log = 0;
	
	//file_put_contents('C:\\biometrics\\Logs\\biometric_logs_to_save.txt', "------------------------------------------ " . Date("Y-m-d h:i:s A \(F d, Y | l\)") . " ------------------------------------------\n", FILE_APPEND);
	
	foreach($rec as $r)
	{
		$splt_data 		= explode("|", $r);
		$sEnrollNumber 	= $splt_data[0];
		$iAttState		= $splt_data[1];
		$iYear			= $splt_data[2];
		$iMonth			= $splt_data[3];
		$iDay			= $splt_data[4];
		$iHour			= $splt_data[5];
		$iMinute		= $splt_data[6];
		$iSecond		= $splt_data[7];
		$valid_data		= 1;
		$sDate 			= $iYear . '-' . $iMonth . '-' . $iDay . ' ' . $iHour . ':' . $iMinute . ':' . $iSecond;
		
		$rec_dup_query = "	SELECT
								*
							FROM tk_biometric_log BL
							WHERE	BL.enroll_number	= ". $sEnrollNumber ."
								AND	BL.in_out_mode		= ". $iAttState ."
								AND	BL.dt_log_int		= ". strtotime($iYear . '-' . $iMonth . '-' . $iDay . ' ' . $iHour . ':' . $iMinute . ':00') ."
							";
		
		IF($rec_dup_data = mysqli_query($conn, $rec_dup_query))
		{
			IF($rec_dup_data->num_rows == 0)
			{
		
		IF($cnt_save_log == 0) {
			$cnt_save_log = $cnt_save_log + 1;
			file_put_contents('C:\\biometrics\\Logs\\biometric_logs_to_save.txt', "------------------------------------------ " . Date("Y-m-d h:i:s A \(F d, Y | l\)") . " ------------------------------------------\n", FILE_APPEND);
		}
		ELSE {
			$cnt_save_log = $cnt_save_log + 1;
		}
		
		//BEGIN: ------------------------------------ Saving Records for tk_biometric_log and tk_attendance ------------------------------------//
		
		//Create SQL to INSERT data
		$InsertData	= "	INSERT INTO tk_biometric_log
						(
							enroll_number,
							in_out_mode,
							year_log,
							month_log,
							day_log,
							hour_log,
							min_log,
							sec_log,
							dt_log,
							dt_log_int,
							client_ip,
							is_valid,
							is_late_save
						)
						VALUES
						(
							" . $sEnrollNumber . ",
							" . $iAttState . ",
							" . $iYear . ",
							" . $iMonth . ",
							" . $iDay . ",
							" . $iHour . ",
							" . $iMinute . ",
							" . $iSecond . ",
							'" . $sDate . "',
							" . strtotime($iYear . '-' . $iMonth . '-' . $iDay . ' ' . $iHour . ':' . $iMinute . ':00') . ",
							'" . $ip_add . "',
							" . $valid_data . ", 1
						)
						
						/*
						On Duplicate Key Update
							sec_log	= (Case in_out_mode When 0 Then sec_log Else ". $iSecond ."  End),
							dt_log	= (Case in_out_mode When 0 Then dt_log 	Else '". $sDate ."'  End);
						*/
						";
		
		
		//Save the data to database
		if (mysqli_query($conn, $InsertData)) {
			echo "New record created successfully";
			
			$split_rec = $sEnrollNumber . "\t" . $iAttState . "\t" . $iYear . "\t" . $iMonth . "\t" . $iDay . "\t" . $iHour . "\t" . $iMinute . "\t" . $iSecond . "\t" . $sDate;
			
			file_put_contents('C:\\biometrics\\Logs\\biometric_logs_to_save.txt', "$split_rec \n", FILE_APPEND);
			if($iAttState != -1) {
				$member_query = "SELECT gm.*
										FROM g4_member gm 
									  WHERE 
										`gm`.`enroll_number` 	= ".$sEnrollNumber;
				$member_data = mysqli_query($conn, $member_query);
				$member_row = $member_data->fetch_object();
				$has_member = count($member_row);
				
				if($has_member) {
				$dateStr = ($iYear."-".str_pad($iMonth,2,"0",STR_PAD_LEFT)."-".str_pad($iDay,2,"0",STR_PAD_LEFT));
				$timeStr = (str_pad($iHour,2,"0",STR_PAD_LEFT).":".str_pad($iMinute,2,"0",STR_PAD_LEFT));
				
				$curDate = new DateTime($dateStr." 00:00:00");
				$timekeeping_query = "SELECT tms.*,tsc.shift_hr_from,tsc.shift_min_from,tsc.shift_hr_to,tsc.shift_min_to, gm.mb_id, gm.mb_lname, gm.mb_3
										FROM tk_member_schedule tms
										INNER JOIN g4_member gm 
										  ON tms.mb_no = gm.mb_no
										LEFT JOIN tk_shift_code tsc 
										  ON tms.shift_id = tsc.shift_id
									  WHERE 
										`gm`.`enroll_number` 	= ".$sEnrollNumber." AND 
										`tms`.`year` 			= ".$iYear." AND 
										`tms`.`month` 			= ".$iMonth." AND 
										`tms`.`day` 			= ".$iDay;
				
				$cur_schedule_data = mysqli_query($conn, $timekeeping_query);
				$cur_row = $cur_schedule_data->fetch_object();
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
										`gm`.`enroll_number` 	= ".$sEnrollNumber." AND 
										`tms`.`year` 			= ".$prevDate->format("Y")." AND 
										`tms`.`month` 			= ".$prevDate->format("n")." AND 
										`tms`.`day` 			= ".$prevDate->format("j");
				
				$prev_schedule_data = mysqli_query($conn, $prev_timekeeping_query);
				$prev_row = $prev_schedule_data->fetch_object();
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
										`gm`.`enroll_number` 	= ".$sEnrollNumber." AND 
										`tms`.`year` 			= ".$nextDate->format("Y")." AND 
										`tms`.`month` 			= ".$nextDate->format("n")." AND 
										`tms`.`day` 			= ".$nextDate->format("j");
				
				$next_schedule_data = mysqli_query($conn, $next_timekeeping_query);
				$next_row = $next_schedule_data->fetch_object();
				$has_next = count($next_row);
				
				$valid = false;
				$curLog		= new DateTime($dateStr." ".$timeStr.":00");
				if($has_previous) {
					if($prev_row->shift_id > 0 && !$prev_row->leave_id) {
					
						$shift_in 	= new DateTime($prevDate->format("Y-m-d")." ".$prev_row->shift_hr_from.":".$prev_row->shift_min_from.":00");
						$shift_out 	= new DateTime($prevDate->format("Y-m-d")." ".$prev_row->shift_hr_to.":".$prev_row->shift_min_to.":00");

						if($shift_in > $shift_out)
							$shift_out->modify("+1 day");
					
						if($iAttState == 0) {
							$tmp_shift_out = new DateTime($shift_out->format("Y-m-d H:i:s"));
							$tmp_shift_out->modify("+3 hours");
							if($curLog <= $tmp_shift_out ) {
						
								$timekeeping_query = "SELECT * 
														FROM tk_attendance ta
													  WHERE 
														`ta`.`mb_no` 	    = '".$prev_row->mb_no."' AND 
														`ta`.`att_date` 	= '".$shift_in->format("Y-m-d")."'";
								$attendance_res = mysqli_query($conn, $timekeeping_query);
								$attendance_data = $attendance_res->fetch_object();
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
												  `toa`.`mb_no` = '".$prev_row->mb_no."' AND 
												  `toa`.`date` 	= '".$shift_in->format("Y-m-d")."' AND
												  TIME('".$timeStr."') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
												  AND `toa`.`status` = 3
												ORDER BY time_in ASC";
									$obt_res = mysqli_query($conn, $obt_query);
									
									if($obt_res === TRUE) {
										if($obt_res->num_rows > 0) {
											while($obt_row = $obt_res->fetch_object()) {
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
									}
									
									$insertQuery = "INSERT INTO tk_attendance
												(mb_no,att_date,shift_id,actual_in,actual_in_sec,tardy)
											  VALUES
												('".$prev_row->mb_no."',
														'".$prevDate->format("Y-m-d")."',
												'".$prev_row->shift_id."',
												'".$timeStr."',
												'".$iSecond."',
												'".$tardy."');";
									mysqli_query($conn, $insertQuery);
									$valid = true;
								}
								else if(empty($attendance_data->actual_in)) {
									$tardy = ($curLog->format("U")-$shift_in->format("U"))/60;
									if($tardy < 0)
										$tardy = 0;
							
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
									$obt_res = mysqli_query($conn, $obt_query);
									
									if($obt_res === TRUE) {
										if($obt_res->num_rows > 0) {
											while($obt_row = $obt_res->fetch_object()) {
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
											   actual_in_sec = '".$iSecond."',
											   tardy = '".$tardy."'
											  WHERE
												mb_no = '".$prev_row->mb_no."' AND
												att_date = '".$prevDate->format("Y-m-d")."'";
									mysqli_query($conn, $insertQuery);
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
						else if($iAttState == 1) {
							if($curLog <= $shift_out) {
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
										$timekeeping_query = "SELECT * 
												FROM tk_attendance ta
											  WHERE 
												`ta`.`mb_no` 	    = '".$prev_row->mb_no."' AND 
												`ta`.`att_date` 	= '".$curLog->format("Y-m-d")."'";
										$attendance_res = mysqli_query($conn, $timekeeping_query);
										$attendance_data = $attendance_res->fetch_object();
										if(count($attendance_data)) {
											$timekeeping_query = "DELETE
																  FROM
																	tk_attendance
																  WHERE 
																	`att_id` 	    = '".$attendance_data->att_id."'";
											mysqli_query($conn, $timekeeping_query);
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
										$attendance_res = mysqli_query($conn, $timekeeping_query);
										$attendance_data = $attendance_res->fetch_object();
							
										if(count($attendance_data) == 0) {
											$timekeeping_query = "SELECT * 
												FROM tk_attendance ta
											  WHERE 
												`ta`.`mb_no` 	    = '".$prev_row->mb_no."' AND 
												`ta`.`att_date` 	= '".$shift_in->format("Y-m-d")."'";
											$attendance_res = mysqli_query($conn, $timekeeping_query);
											$attendance_data = $attendance_res->fetch_object();
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
								$attendance_res = mysqli_query($conn, $timekeeping_query);
								$attendance_data = $attendance_res->fetch_object();
							
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
										$obt_res = mysqli_query($conn, $obt_query);
								
										if($obt_res === TRUE) {
											if($obt_res->num_rows > 0) {
												while($obt_row = $obt_res->fetch_object()) {
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
														'".$iSecond."',
														'".$undertime."',
														'".$excess."');";
									mysqli_query($conn, $insertQuery);
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
										$obt_res = mysqli_query($conn, $obt_query);
										if($obt_res === TRUE) {
											if($obt_res->num_rows > 0) {
												while($obt_row = $obt_res->fetch_object()) {
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

									$insertQuery = "UPDATE tk_attendance
											  SET
											   actual_out = '".$timeStr."',
											   actual_out_sec = '".$iSecond."',
											   undertime = '".$undertime."',
											   overtime = '".$excess."',
											   reg_hrs = '".$regHrs."',
											   nsd = '".$nsd."'
											  WHERE
												mb_no = '".$prev_row->mb_no."' AND
												att_date = '".$shift_in->format("Y-m-d")."'";
									mysqli_query($conn, $insertQuery);
								}
								// file_put_contents('C:\\biometrics\\Logs\\biometric_logs_to_save.txt', "For Testing: $insertQuery \n", FILE_APPEND);
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
				  
						if($iAttState == 0) {
							$valid = true;
					  
							$timekeeping_query = "SELECT * 
										FROM tk_attendance ta
									  WHERE 
										`ta`.`mb_no` 	    = '".$cur_row->mb_no."' AND 
										`ta`.`att_date` 	= '".$shift_in->format("Y-m-d")."'";
							$attendance_res = mysqli_query($conn, $timekeeping_query);
							$attendance_data = $attendance_res->fetch_object();
						  
							$tardy = ($curLog->format("U")-$shift_in->format("U"))/60;
							if($tardy < 0)
								$tardy = 0;
						  
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
							$obt_res = mysqli_query($conn, $obt_query);
							
							if($obt_res === TRUE) {
								if($obt_res->num_rows > 0) {
									while($obt_row = $obt_res->fetch_object()) {
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
							}
									
							if(count($attendance_data)==0) {
								$insertQuery = "INSERT INTO tk_attendance
												(mb_no,att_date,shift_id,actual_in,actual_in_sec,tardy)
											  VALUES
												('".$cur_row->mb_no."',
												'".$shift_in->format("Y-m-d")."',
												'".$cur_row->shift_id."',
												'".$timeStr."',
												'".$iSecond."',
												'".$tardy."');";
								mysqli_query($conn, $insertQuery);
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
										   actual_in_sec = '".$iSecond."',
										   tardy = '".$tardy."'
										  WHERE
											mb_no = '".$cur_row->mb_no."' AND
											att_date = '".$shift_in->format("Y-m-d")."'";
								mysqli_query($conn, $insertQuery);
							}
							else if($has_next) {
								$tmpCurDate = new DateTime($curDate->format("Y-m-d")." 00:00:00");
								$tmpCurDate->modify("+1 day");
							
								if($next_row->shift_hr_from) {
									$shift_in 	= new DateTime($tmpCurDate->format("Y-m-d")." ".$next_row->shift_hr_from.":".$next_row->shift_min_from.":00");
									$shift_out 	= new DateTime($tmpCurDate->format("Y-m-d")." ".$next_row->shift_hr_to.":".$next_row->shift_min_to.":00");
							
									if($shift_in > $shift_out)
										$shift_out->modify("+1 day");
								  
									$shift_in->modify("-4 hours");
								
									if($shift_in < $curLog) {
										$shift_in->modify("+4 hours");
										$timekeeping_query = "SELECT * 
														FROM tk_attendance ta
													  WHERE 
														`ta`.`mb_no` 	    = '".$next_row->mb_no."' AND 
														`ta`.`att_date` 	= '".$shift_in->format("Y-m-d")."'";
										$attendance_res = mysqli_query($conn, $timekeeping_query);
										$attendance_data = $attendance_res->fetch_object();
									  
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
														  `toa`.`mb_no` = '".$prev_row->mb_no."' AND 
														  `toa`.`date` 	= '".$shift_in->format("Y-m-d")."' AND
														  TIME('".$timeStr."') BETWEEN TIME(`toa`.`time_in`) AND TIME(`toa`.`time_out`)
														  AND `toa`.`status` = 3
														ORDER BY time_in ASC";
											$obt_res = mysqli_query($conn, $obt_query);
											
											if($obt_res === TRUE) {
												if($obt_res->num_rows > 0) {
													while($obt_row = $obt_res->fetch_object()) {
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
											}
											
											$insertQuery = "INSERT INTO tk_attendance
															(mb_no,att_date,shift_id,actual_in,actual_in_sec,tardy)
														  VALUES
															('".$next_row->mb_no."',
															'".$shift_in->format("Y-m-d")."',
															'".$next_row->shift_id."',
															'".$timeStr."',
															'".$iSecond."',
															'".$tardy."');";
											mysqli_query($conn, $insertQuery);
										}
									}
								}
							}
						}
						else if($iAttState == 1 && $curLog >= $shift_in) {
							$valid = true;
					  
							$timekeeping_query = "SELECT * 
									FROM tk_attendance ta
								  WHERE 
									`ta`.`mb_no` 	    = '".$cur_row->mb_no."' AND 
									`ta`.`att_date` 	= '".$shift_in->format("Y-m-d")."'";
							$attendance_res = mysqli_query($conn, $timekeeping_query);
							$attendance_data = $attendance_res->fetch_object();
							$excess 			= 0;
							$nsd				= 0;
							$regHrs			= 0;

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
									$obt_res = mysqli_query($conn, $obt_query);
									if($obt_res === TRUE) {
										if($obt_res->num_rows > 0) {
											while($obt_row = $obt_res->fetch_object()) {
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
														'".$iSecond."',
														'".$undertime."',
														'".$excess."');";
								mysqli_query($conn, $insertQuery);
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
									$obt_res = mysqli_query($conn, $obt_query);
									if($obt_res === TRUE) {
										if($obt_res->num_rows > 0) {
											while($obt_row = $obt_res->fetch_object()) {
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
					
								$insertQuery = "UPDATE tk_attendance
											  SET
											   actual_out = '".$timeStr."',
											   actual_out_sec = '".$iSecond."',
											   undertime = '".$undertime."',
											   overtime = '".$excess."',
											   reg_hrs = '".$regHrs."',
											   nsd = '".$nsd."'
											  WHERE
												mb_no = '".$cur_row->mb_no."' AND
												att_date = '".$shift_in->format("Y-m-d")."'";
								mysqli_query($conn, $insertQuery);
							}
						}
						else if($iAttState == 1) {
							$shift_in_tmp = new DateTime($curDate->format("Y-m-d")." ".$cur_row->shift_hr_from.":".$cur_row->shift_min_from.":00");
							$shift_in_tmp->modify("-3 hours");
							if($curLog < $shift_in_tmp) {
								$timekeeping_query = "SELECT * 
									FROM tk_attendance ta
									INNER JOIN g4_member gm ON ta.mb_no = gm.mb_no
								  WHERE 
									`gm`.`enroll_number` 	    = '".$sEnrollNumber."' AND 
									`ta`.`att_date` 	= '".$prevDate->format("Y-m-d")."'";
								$attendance_res = mysqli_query($conn, $timekeeping_query);
								$attendance_data = $attendance_res->fetch_object();

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
												'".$iSecond."',
												'".$undertime."');";
						 
									$attendance_res = mysqli_query($conn, $insertQuery);
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
												$obt_res = mysqli_query($conn, $obt_query);
												if($obt_res === TRUE) {
													if($obt_res->num_rows > 0) {
														while($obt_row = $obt_res->fetch_object()) {
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
											   actual_out_sec = '".$iSecond."',
											   undertime = '".$undertime."',
											   overtime = '".$excess."',
											   reg_hrs = '".$regHrs."',
											   nsd = '".$nsd."'
											  WHERE
												mb_no = '".$cur_row->mb_no."' AND
												att_date = '".$prevDate->format("Y-m-d")."'";
									$attendance_res = mysqli_query($conn, $insertQuery);
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
									`gm`.`enroll_number` 	    = '".$sEnrollNumber."' AND 
									`ta`.`att_date` 	= '".$curLog->format("Y-m-d")."'";
					$attendance_res = mysqli_query($conn, $timekeeping_query);
					$attendance_data = $attendance_res->fetch_object();
			  
					$prev_timekeeping_query = "SELECT * 
									FROM tk_attendance ta
									INNER JOIN g4_member gm ON ta.mb_no = gm.mb_no
								  WHERE 
									`gm`.`enroll_number` 	    = '".$sEnrollNumber."' AND 
									`ta`.`att_date` 	= '".$prevDate->format("Y-m-d")."'";
					$prev_attendance_res = mysqli_query($conn, $prev_timekeeping_query);
					$prev_attendance_data = $prev_attendance_res->fetch_object();
				  
					$emp_query = "SELECT * 
									FROM g4_member gm 
								  WHERE 
									`gm`.`enroll_number` 	    = '".$sEnrollNumber."'";
					$emp_res = mysqli_query($conn, $emp_query);
					$emp_data = $emp_res->fetch_object();
				  
					if($iAttState == 0) {
						if(count($attendance_data)==0) {
							$insertQuery = "INSERT INTO tk_attendance
											(mb_no,att_date,actual_in,actual_in_sec)
										  VALUES
											('".(count($emp_data)?$emp_data->mb_no:0)."',
											'".$curLog->format("Y-m-d")."',
											'".$timeStr."',
											'".$iSecond."');";
							mysqli_query($conn, $insertQuery);
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
								   actual_in_sec = '".$iSecond."',
								   tardy = '0'
								  WHERE
									mb_no = '".(count($emp_data)?$emp_data->mb_no:0)."' AND
									att_date = '".$curLog->format("Y-m-d")."'";
							mysqli_query($conn, $insertQuery);
						}
					}
					else {
						if(count($prev_attendance_data) && !count($attendance_data)) {
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
										   actual_out_sec 	= '".$iSecond."',
										   overtime 		= '".$excess."',
										   reg_hrs 			= '".$regHrs."',
										   nsd 				= '".$nsd."'
										  WHERE
											mb_no = '".(count($emp_data)?$emp_data->mb_no:0)."' AND
											att_date = '".$prevDate->format("Y-m-d")."'";
							$attendance_res = mysqli_query($conn, $insertQuery);
							$valid = true;
						}
						if(!$valid) {
						
							$excess 			= 0;
							$nsd				= 0;
							$regHrs				= 0;
						
							if(count($attendance_data)==0) {
								$insertQuery = "INSERT INTO tk_attendance
														(mb_no,att_date,actual_out,actual_out_sec)
													  VALUES
														('".(count($emp_data)?$emp_data->mb_no:0)."',
														'".$curLog->format("Y-m-d")."',
														'".$timeStr."',
														'".$iSecond."');";
								mysqli_query($conn, $insertQuery);
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
											   actual_out_sec 	= '".$iSecond."',
											   overtime 		= '".$excess."',
											   reg_hrs 			= '".$regHrs."',
											   nsd 				= '".$nsd."'
											  WHERE
												mb_no = '".(count($emp_data)?$emp_data->mb_no:0)."' AND
												att_date = '".$curLog->format("Y-m-d")."'";
								mysqli_query($conn, $insertQuery);
							}
						}
					}
			    }
				
				}
				// end of checking employee record
			}
			//clear result
			@mysqli_free_result($schedule_data);
			@mysqli_free_result($attendance_data);
			@mysqli_free_result($result);
		}
		else {
			
			//$split_rec = $sEnrollNumber . "\t" . $iAttState . "\t" . $iYear . "\t" . $iMonth . "\t" . $iDay . "\t" . $iHour . "\t" . $iMinute . "\t" . $iSecond . "\t" . $sDate . "!!!!!!!!!!!!!!!!!";
			
			//file_put_contents('C:\\biometrics\\Logs\\biometric_logs_to_save.txt', "$split_rec\n", FILE_APPEND);
			
			echo "Error: " . $InsertData . "<br>" . mysqli_error($conn);
		}
		
		//END: ------------------------------------ Saving Records for tk_biometric_log and tk_attendance ------------------------------------//
		
		
			}
		}
		
		@mysqli_free_result($rec_dup_data);
	}
	
	//Close Connection
	mysqli_close($conn);


	// Function to get the client IP address
	function get_client_ip()
	{
		$ipaddress = '';
		
		if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		
		else if(isset($_SERVER['HTTP_X_FORWARDED']) && !empty($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']) && !empty($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		
		else if(isset($_SERVER['HTTP_FORWARDED']) && !empty($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		
		else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		
		else
			$ipaddress = 'UNKNOWN';
		
		return $ipaddress;
	}
	
	IF($cnt_save_log > 0) {
		file_put_contents('C:\\biometrics\\Logs\\biometric_logs_to_save.txt', "------------------------------------------  Total Records Save : " . $cnt_save_log . "  ------------------------------------------------------------------------ \n\n\n", FILE_APPEND);
	}
?>

