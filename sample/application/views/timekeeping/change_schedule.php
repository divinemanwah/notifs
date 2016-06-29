<div class="col-lg-12 well well-sm hidden">
  <span class="red smaller bolder">Note:</span> Please do not forget to click the submit button <i class="green ace-icon fa fa-share-square-o bigger-130"></i>!
</div>
<div class="col-lg-6">
  <div class="widget-box widget-color-red2">
	<div class="widget-header">
		<h5 class="widget-title smaller">Approved Schedule</h5>
		<div class="widget-toolbar no-border">
			<div class="input-daterange input-group" style="margin: 4px 0 0 0;">
				<input type="text" class="input-sm form-control" id="date-from" name="start" style="background-color: #FFDDDD; width: 90px;" value="<?=$cur_period->start?>">
				<span class="input-group-addon btn-danger">
					<i class="fa fa-exchange"></i>
				</span>
				<input type="text" class="input-sm form-control" id="date-to" name="end" style="background-color: #FFDDDD; width: 90px;" value="<?=$cur_period->end?>">
			</div>
		</div>
	</div>

	<div class="widget-body">
		<div class="widget-main no-padding" >
		    <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
			  <div id="tk-table-loader" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
			  <div id="tk-table-no-record" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
			  <table id="tk-schedules-table" class="table table-striped table-bordered table-hover" width="100%"></table>
			</div>
			<div id="tk-schedules-pager"></div>
		</div>
	</div>
  </div>
</div>
<div class="col-lg-6">
  <div class="widget-box widget-color-grey">
	<div class="widget-header">
		<h5 class="widget-title smaller">Change of Schedule Requests</h5>
		<div class="widget-toolbar no-border">
		  <button class="btn btn-xs btn-inverse" id="add-request-btn">
			<i class="ace-icon fa fa-plus"></i>
			Add
		  </button>
		</div>
		<div class="widget-toolbar no-border">
			<div class="input-daterange input-group" style="margin: 4px 0 0 0;">
				<input type="text" class="input-sm form-control" id="req-date-from" name="start" style="background-color: #F1F1F1; width: 90px;" value="<?=$cur_period->start?>">
				<span class="input-group-addon btn-inverse">
					<i class="fa fa-exchange"></i>
				</span>
				<input type="text" class="input-sm form-control" id="req-date-to" name="end" style="background-color: #F1F1F1; width: 90px;" value="<?=$cur_period->end?>">
			</div>
		</div>
	</div>

	<div class="widget-body">
		<div class="widget-main no-padding" >
		    <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
			  <div id="tk-req-loader" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
			  <div id="tk-req-no-record" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
			  <table id="tk-req-table" class="table table-striped table-bordered table-hover" width="100%"></table>
			</div>
			<div id="tk-req-pager"></div>
		</div>
	</div>
  </div>
</div>

<div id="request-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Change Shift</h4>
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
						<form class="form-horizontal" role="form" id="requestForm" novalidate="novalidate">
							<input class="form-control" type="hidden" id="request-id" name="request-id" />
							<h3 class="header smaller no-margin-top"><small>Change of Shift Information</small></h3>
							<div class="row input-daterange">
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="att-date-from">Date From : </label>
									<div class="col-sm-4">
										<input class="form-control input-sm input-date" type="text" id="att-date-from" name="att-date-from" />
									</div>
								</div>
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="att-date-to">Date To : </label>
									<div class="col-sm-4">
										<input class="form-control input-sm input-date" type="text" id="att-date-to" name="att-date-to" />
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="orig-shift">Original Shift : </label>
									<label class="col-sm-8 control-label no-padding-right align-left" id="shift-str">[N/A] - N/A</label>
									<input type="hidden" id="orig-shift-str" name="orig-shift-str" value=""/>
									<input type="hidden" id="orig-shift-ids-str" name="orig-shift-ids-str" value=""/>
								</div>
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="new-shift">New Shift</label>
									<div class="col-sm-8">
									  <div class="input-group">
										<select id="new-shift" name="new-shift">
										  <? foreach($shifts as $shift_id=>$shift) { ?>
										  <option value="<?=$shift_id?>"><?="[".$shift->stime."] - ".$shift->scode?></option>
										  <? } ?>
										</select>
									  </div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-sm-12">
									<label class="col-sm-2 control-label no-padding-right" for="reason">Reason :</label>
									<div class="col-sm-8 control-label no-padding-right">
									  <textarea id="reason" style="height: 112px; resize: none;" class="form-control limited" name="reason" maxlength="250" ></textarea>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="row col-sm-12 hidden" id="approval_header">
				  <h3 class="header smaller no-margin-top"><small>Approval Remarks</small></h3>
				</div>
				<div class="row col-sm-12 hidden" style="max-height: 150px; overflow: auto;" id="approval_list">None</div>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm btn-danger modal-action-btn" data-dismiss="modal">
					<i class="ace-icon fa fa-times"></i>
					Close
				</button>
				<button class="btn btn-sm btn-primary modal-action-btn" id="request-save">
					<i class="ace-icon fa fa-save"></i>
					Confirm
				</button>
			</div>
		</div>
	</div>
</div>

<div id="action-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
	    <button type="button" data-dismiss="modal" class="close">&times;</button>
		<h4 class="blue bigger">Change of Schedule</h4>
	  </div>
	  <div class="modal-body">
	    <div class="row">
		  <div class="col-xs-12">
		    <div class="well">
			  <h4 class="red smaller lighter">Are you sure you want to <label id='request-action'>delete</label> Request <strong id="request_lbl">&nbsp;</strong>?</h4>
			  <button class="btn btn-sm btn-success modal-action-btn">Yes</button>
			  <button class="btn btn-sm btn-danger" data-dismiss="modal">No</button>
			</div>
			<strong>Note:</strong> This cannot be undone
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>

