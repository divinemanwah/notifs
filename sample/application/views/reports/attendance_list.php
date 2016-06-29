<style>
.widget-box[class*=widget-color-]>.widget-header div.chosen-drop {
  color: #000000 !important;
}
</style>
<div class="col-12">
	<div class="widget-box widget-color-blue">
		<div class="widget-header">
			<h4 class="widget-title">Attendance</h4>
			<div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-info2" id="tk-search-btn">
				<i class="ace-icon fa fa-search"></i>
				Search
			  </button>
		    </div>
			<div class="widget-toolbar no-border">
				<div class="input-daterange input-group" style="margin: 4px 0 0 0;">
					<input type="text" class="input-sm form-control" id="date-from" name="start" style="background-color: #F7F7F7; width: 90px;" value="<?=$cur_period->start?>">
					<span class="input-group-addon btn-info2">
						<i class="fa fa-exchange"></i>
					</span>
					<input type="text" class="input-sm form-control" id="date-to" name="end" style="background-color: #F7F7F7; width: 90px;" value="<?=$cur_period->end?>">
				</div>
			</div>
			<div class="widget-toolbar no-border">
				<select id="tk-filter-emp" class="chosen-select" data-placeholder="Specific Employee">
				  <option value=""></option>
				  <?php
					foreach($emp_list as $emp)
						echo '<option value="' . $emp->mb_no . '">' . ($emp->mb_3=="Expat"?$emp->mb_nick:$emp->mb_fname)." ".$emp->mb_lname . '</option>';
				  ?>
				</select>
			</div>
			<div class="widget-toolbar no-border">
				<button class="btn btn-xs btn-info2 dropdown-toggle" data-toggle="dropdown" id="tk-filter-status-btn" data-id="0">
					<i class="ace-icon fa fa-filter"></i> Status: All
					<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-status">
				    <li><a href="#" data-id="incomplete">Incomplete Logs</a></li>
					<li><a href="#" data-id="awol">AWOL</a></li>
					<li><a href="#" data-id="tardy-ut">Tardy / Undertime</a></li>
					<li class="divider"></li>
					<li class="active"><a href="#">All</a></li>
				</ul>
			</div>
			<div class="widget-toolbar no-border">
				<button class="btn btn-xs btn-info2 dropdown-toggle" data-toggle="dropdown" id="tk-filter-dept-btn" data-id="0">
					<i class="ace-icon fa fa-filter"></i> Department: All
					<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-dept">
					<?php
						foreach($depts as $dept)
							echo '<li><a href="#" data-id="' . $dept->dept_no . '">' . $dept->dept_name . '</a></li>';
					?>
					<li class="divider"></li>
					<li class="active"><a href="#">All</a></li>
				</ul>
			</div>
			<div class="widget-toolbar no-border">
				<button class="btn btn-xs btn-info2 dropdown-toggle" data-toggle="dropdown" id="tk-filter-emp-stat-btn" data-id="0">
					<i class="ace-icon fa fa-filter"></i> Employment: All
					<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-emp-stat">
					<li><a href="#" data-id="1">Probationary</a></li>
					<li><a href="#" data-id="2">Confirmed</a></li>
					<li class="divider"></li>
					<li class="active"><a href="#">All</a></li>
				</ul>
			</div>
		</div>
		
		<div class="widget-body">
			<div class="widget-main no-padding">
			    <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
				  <div id="tk-attendance-loader" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
				  <div id="tk-attendance-no-record" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
				  <table id="tk-attendance-table" class="table table-striped table-bordered table-hover" width="100%"></table>
				</div>
				<div id="tk-attendance-pager"></div>
				<div class="widget-toolbox no-border">
					<form id="exportForm" action="<?=base_url("reports/exportAttendance")?>" method="POST" style="display:inline" >
					  <input type="hidden" name="export-dept" value=""/>
					  <input type="hidden" name="export-emp" value=""/>
					  <input type="hidden" name="export-from" value=""/>
					  <input type="hidden" name="export-to" value=""/>
					  <input type="hidden" name="export-status" value=""/>
					  <input type="hidden" name="export-emp-stat" value=""/>
					  <button class="btn btn-xs btn-info">Export using Daily Template</button>
					</form>
					<form id="exportForm" action="<?=base_url("reports/exportSummaryAttendance")?>" method="POST" style="display:inline" >
					  <input type="hidden" name="export-dept" value=""/>
					  <input type="hidden" name="export-emp" value=""/>
					  <input type="hidden" name="export-from" value=""/>
					  <input type="hidden" name="export-to" value=""/>
					  <input type="hidden" name="export-status" value=""/>
					  <input type="hidden" name="export-emp-stat" value=""/>
					  <button class="btn btn-xs btn-info">Export using Summary Template</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="request-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Attendance Information</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="header smaller no-margin-top"><small>Schedule Information</small> <small id='header-text' class='text text-warning'></small></h3>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<span class="btn btn-info btn-sm col-sm-1 text-center" >Shift</span>
						<span class="btn btn-info btn-sm col-sm-2 text-center" >IN</span>
						<span class="btn btn-info btn-sm col-sm-2 text-center" >OUT</span>
						<span class="btn btn-info btn-sm col-sm-2 text-center" >Time IN</span>
						<span class="btn btn-info btn-sm col-sm-2 text-center" >Time OUT</span>
						<span class="btn btn-info btn-sm col-sm-1 text-center" >Tardy</span>
						<span class="btn btn-info btn-sm col-sm-2 text-center" >Undertime</span>
					</div>
					<div class="col-sm-12">
						<span class="btn btn-white btn-info btn-sm col-sm-1 text-center" id='shift-code' ></span>
						<span class="btn btn-white btn-info btn-sm col-sm-2 text-center" id='shift-from' ></span>
						<span class="btn btn-white btn-info btn-sm col-sm-2 text-center" id='shift-to' ></span>
						<span class="btn btn-white btn-info btn-sm col-sm-2 text-center" id='time-in' ></span>
						<span class="btn btn-white btn-info btn-sm col-sm-2 text-center" id='time-out' ></span>
						<span class="btn btn-white btn-info btn-sm col-sm-1 text-center" id='tardy' ></span>
						<span class="btn btn-white btn-info btn-sm col-sm-2 text-center" id='undertime' ></span>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-sm-4">
					    <span class="btn btn-success btn-sm col-sm-12 text-center" >Leave Data</span>
					</div>
					<div class="col-sm-4">
					    <span class="btn btn-success btn-sm col-sm-12 text-center" >OBT Data</span>
					</div>
					<div class="col-sm-4">
					    <span class="btn btn-success btn-sm col-sm-12 text-center" >CWS Data</span>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4" id='leave-tbl'>
					    <span class="btn btn-white btn-success btn-sm col-sm-12 text-center" >No Leave</span>
					</div>
					<div class="col-sm-4" id='obt-tbl'>
					    <span class="btn btn-white btn-success btn-sm col-sm-12 text-center" >No OBT</span>
					</div>
					<div class="col-sm-4" id='cws-tbl'>
					    <span class="btn btn-white btn-success btn-sm col-sm-12 text-center" >No CWS</span>
					</div>
				</div>
				<br/>
				<div class="row">
				  <div class="col-sm-6" id='att-settings'>
				    <div class="row">
					  <div class="col-sm-2">&nbsp;</div>
					  <div class="col-sm-8">
				        <span class="btn btn-info btn-sm col-sm-12 text-center" >Attendance</span>
					  </div>
					  <div class="col-sm-2">&nbsp;</div>
				    </div>
				    <div class="row">
					  <div class="col-sm-2">&nbsp;</div>
					  <div class="col-sm-8">
				        <span class="btn btn-white btn-info btn-sm col-sm-6 text-center" ><b>Time</b></span>
					    <span class="btn btn-white btn-info btn-sm col-sm-6 text-center" ><b>Type</b></span>
					  </div>
					  <div class="col-sm-2">&nbsp;</div>
				    </div>
                                    <div class="row">
					  <div class="col-sm-2">&nbsp;</div>
					  <div class="col-sm-8" id='att-tbl'>
					    <span class="btn btn-white btn-info btn-sm col-sm-12 text-center" >No Logs</span>
					  </div>
					  <div class="col-sm-2">&nbsp;</div>
				    </div>
				  
                                    <div id="awolhistorypage" class="hidden">
                                    <br>
                                    <div class="row">
                                            <div class="col-sm-1">&nbsp;</div>
                                                    <div class="col-sm-10"><span class="btn btn-grey btn-info btn-sm col-sm-12 text-center">Tagging History</span></div>
                                            <div class="col-sm-1">&nbsp;</div>
                                    </div>
                                    <div class="row">
                                            <div class="col-sm-1">&nbsp;</div>
                                                    <div class="col-sm-10" id="awol-history">
                                                        <span  class="btn btn-white btn-info btn-sm col-sm-12">
                                                            NO History
                                                        </span>
                                                    </div>
                                            <div class="col-sm-1">&nbsp;</div>
                                    </div>
                                    </div>

                                  </div>
				  <div class="col-sm-5 panel panel-success" id='awol-settings'>
				    <form id="awolForm" class="row">
					<input type='hidden' id='mb_no' value=''/>
					<input type='hidden' id='day' value=''/>
					<!--<h3 class="header smaller btn-grey no-margin-top"><small>AWOL Tagging</small></h3> -->
                                        <span class="btn btn-danger btn-sm col-sm-12 text-center" >AWOL Tagging</span>
				    <div class=" col-sm-12 awol-field">
						<span>Mark : </span>
						<div class="radio">
							<label>
								<input name="awol-tag" type="radio" class="awol-radio ace" value="1">
								<span class="lbl"> AWoL</span>
							</label>
							<label>
								<input name="awol-tag" type="radio" class="awol-radio ace" value="0">
								<span class="lbl"> Not AWoL</span>
							</label>
							<label>
								<input name="awol-tag" type="radio" class="awol-radio ace" value="el">
								<span class="lbl"> EL</span>
							</label>
						</div>
					</div>
					<div class="col-sm-12 awol-field">
					  <span>Remarks : </span>
					  <textarea id='reason' name='awol-reason' class="autosize-transition form-control" rows="3"></textarea>
					</div>
					<div class="row col-sm-12">&nbsp;</div>
					<div class=" col-sm-12">
                                           <button id='awol-revert' class="btn btn-sm btn-save btn-warning modal-action-btn hidden">
						<i class="ace-icon fa fa-rotate-left"></i>
						Revert
                                           </button>
                                            <button id='awol-update' class="btn btn-sm btn-save btn-success modal-action-btn hidden">
						<i class="ace-icon fa fa-pencil"></i>
						Update
                                           </button>
					  <button id='awol-save' class="btn btn-sm btn-save btn-success modal-action-btn">
						<i class="ace-icon fa fa-save"></i>
						Save
					  </button>
					</div>
					<div class="row col-sm-12">&nbsp;</div>
					</form>
				  </div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-sm btn-danger modal-action-btn" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Close
				</button>
			</div>
		</div>
	</div>
</div>