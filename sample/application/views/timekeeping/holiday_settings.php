<div class="col-xs-12 col-sm-4">
	<div class="widget-box widget-color-green2">
		<div class="widget-header">
			<h4 class="widget-title">Add Holiday</h4>
		</div>
		
		<div class="widget-body" id="holiday-setting">
			<div class="widget-main">
                            <div class="row">
                                <div class="col-xs-12">
                                    <span class="label label-info">*This data will affect the schedule of employees</span>
                                </div>
                            </div>
			  <div class="row">
				<div class="col-xs-12">
				  <div class="alert alert-success hidden">


					<strong>
					  <i class="ace-icon fa fa-check"></i>
					  Success!
					</strong>
					<br />
					<span class="success-msg">The record has been saved.</span>
					<br />
				  </div>
                                  <div class="alert alert-danger hidden">

					<strong>
					  <i class="ace-icon fa fa-times"></i>
					  Error!
					</strong>
					<br />
					<span class="err-msg"></span>
					<br />
				  </div>
				</div>
			  </div>               
                            <br>
                            <div class="col-lg-12"></div>
                            
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-2 control-label" for="holidayname">Name:</label>
                                    <div class="col-sm-10">
                                    <input class="form-control" id="holidayname" >
                                    </div>
                                </div>
                              
                             <div class="col-lg-12"></div>
                              
                                <div class="col-sm-12 form-group input-daterange">
                                    <label class="col-sm-2 control-label no-padding-right" for="holidaydate">Date:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control input-date" id="holidaydate"  readonly="" value="" type="text">
                                    </div>
                                </div>
                              
                             <div class="col-lg-12"></div>  
                              
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="holidaytype">Type:</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="holidaytype">
					<? foreach($h_settings as $key => $val){ ?>
                                            <option value="<?=$key?>"><?=$val?></option>
					<? } ?>
                                        </select>
                                    </div>
                                </div>
                            
                             <div class="col-lg-12"></div>
                              
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="holidaystatus">Status:</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="holidaystatus">
                                            <option value=0>Not Fixed</option>
                                            <option value=1>Fixed</option>
                                        </select>
                                    </div>
                                </div>
                            
                            
				<!-- <p class="text-muted" id="schedule-pattern">e.g. March <span id="sched-day-from">1</span> to <span id="sched-month-to">March</span> <span id="sched-day-to">31</span></p> -->
	
				<div class="center">
					<button type="button" class="btn btn-sm btn-success" id="holiday-set-save">
						<i class="ace-icon fa fa-save bigger-110"></i>
						Save
					</button>
				</div>

			</div>
		</div>
	</div>
</div>

<div class="col-xs-12 col-sm-8">
	<div class="widget-box widget-color-blue3">
		<div class="widget-header">
			<h4 class="widget-title">Holiday</h4>
			<!--
                        <div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-purple" id="add-shift-btn">
				<i class="ace-icon fa fa-plus"></i>
				Add
			  </button>
                        </div>
                        
			<div class="widget-toolbar no-border">
			  <label class="small">Show inactive
				<input id="shift-display-inactive" class="ace ace-switch ace-switch-3" type="checkbox" />
				<span class="lbl middle"></span>
			  </label>
                        </div>
                        -->
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
				<div class="">
					<table id="holiday-table" class="table table-striped table-bordered table-hover" width="100%">
						<thead>
							<tr>
								<th>Holiday name</th>
								<th>
									<i class="ace-icon fa fa-calendar bigger-110 hidden-480"></i>
									Date
								</th>
								<th>
									<i class="ace-icon fa fa-settings bigger-110 hidden-480"></i>
									Type
								</th>
								<th class="hidden-480">Status</th>
                                                                <th class="hidden-480"></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


<div id="add-holiday-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-md ">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Holiday Information</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="alert alert-success hidden">
							<button type="button" class="close" data-dismiss="alert">
								<i class="ace-icon fa fa-times"></i>
							</button>

							<strong>
								<i class="ace-icon fa fa-check"></i>
								Success!
							</strong>
							<br />
							<span class="success-msg"></span>
							<br />
						</div>
						<div class="alert alert-danger hidden">
							<button type="button" class="close" data-dismiss="alert">
								<i class="ace-icon fa fa-times"></i>
							</button>

							<strong>
								<i class="ace-icon fa fa-times"></i>
								Error!
							</strong>
							<br />
							<span class="err-msg"></span>
							<br />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<form class="form-horizontal" role="form" id="add-holiday">
							<input type="hidden" name="holiday_id" id="holiday_id" />
							<h3 class="header smaller no-margin-top"><small>Holiday Information</small></h3>
							<div class="row">
								<div class="col-sm-12 col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-xs-12">
                                                                            <span class="label label-info">*This data will affect the schedule of employees</span>
                                                                        </div>
                                                                    </div>
                                                                    <br>
									<div class="form-holiday">
                                                                            <div class="col-md-6">
										<label class="col-sm-3 control-label no-padding-right" for="add-group-code">Name</label>
										<div class="col-sm-9">
                                                                                    <input class="form-control" type="text" id="add-holiday-code"  />
										</div>
                                                                                
                                                                                <br><br>
                                                                                
                                                                                <label class="col-sm-3 control-label no-padding-right" for="add-holiday-date">Date</label>
                                                                                <div class="col-sm-9 input-daterange">
                                                                                    <input class="form-control input-date" id="add-holiday-date"  readonly="" value="" type="text">
                                                                                </div>
                                                                                
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                
                                                                                <label class="col-sm-3 control-label no-padding-right" for="add-holiday-type">Type</label>
                                                                                <div class="col-sm-9">
                                                                                    <select class="form-control" id="add-holiday-type">
                                                                                    <? foreach($h_settings as $key => $val){ ?>
                                                                                        <option value="<?=$key?>"><?=$val?></option>
                                                                                    <? } ?>
                                                                                    </select>
                                                                                </div>
                                                                                
                                                                                <br><br>
                                                                                
                                                                                <label class="col-sm-3 control-label no-padding-right" for="add-holiday-status">Status</label>
                                                                                <div class="col-sm-9">
                                                                                    <select class="form-control" id="add-holiday-status">
                                                                                        <option value=0>Not Fixed</option>
                                                                                        <option value=1>Fixed</option>
                                                                                    </select>
                                                                                </div>
                                                                                
                                                                                <br><br>
                                                                                
                                                                                <i><label class="col-sm-12 text-success control-label no-padding-right" > Created By: 
                                                                                        <span id="add-holiday-create">Admin</span></label></i>

                                                                            </div>
                                                                            
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
                            <br>
                            <br>
                            <div id="history-panel" class="row">

                                <div class="row">
                                              <div class="col-sm-2">&nbsp;</div>
                                              <div class="col-sm-8">
                                                <span class="btn btn-success btn-sm col-sm-12 text-center">History</span>
                                              </div>
                                              <div class="col-sm-2">&nbsp;</div>
                                </div>
                                <div class="row">
                                              <div class="col-sm-2">&nbsp;</div>
                                              <div class="col-sm-8" id="holiday-history">
                                                  <span class="btn btn-white btn-info btn-sm col-sm-12 text-center">No Data</span>
                                              </div>
                                              <div class="col-sm-2">&nbsp;</div>
                                </div>                                
                            </div>

                                            
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm btn-danger modal-action-btn" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Cancel
				</button>
				<button class="btn btn-sm btn-primary modal-action-btn" id="add-holiday-save">
					<i class="ace-icon fa fa-save"></i>
					Confirm
				</button>
			</div>
		</div>
	</div>
</div>
