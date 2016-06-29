<input type="hidden" id="max_leave" value="<?=$settings[0]->max_pending_leave?>"/>
<input type="hidden" id="total_pending" value="0"/>
<div class="col-lg-12 well well-sm" id="leave-warning">
<h4 class="red smaller lighter">Warning</h4>
  No available leave credits. Cannot file leave. Please ask HR.
</div>
<div class="col-lg-12 well well-sm hidden">
  <span class="red smaller bolder">Note:</span> Please do not forget to click the submit button <i class="green ace-icon fa fa-share-square-o bigger-130"></i>!
</div>
<div class="col-xs-12 col-sm-6">

	<div class="widget-box widget-color-red">
		<div class="widget-header">
			<h4 class="widget-title">General Info</h4>
		</div>
		
		<div class="widget-body">
			<div class="widget-main no-padding">
			  <div style="overflow: auto; height: 75px; position: relative; background-color: rgb(216, 216, 216);">
			    <table id="tk-gen-table" class="table table-striped table-bordered table-hover" width="100%">
				  <thead><tr><th style="width:50%; text-align: center;">AWoL Count (Deducted on VL/AL)</th><th style="width:50%; text-align: center;">EL Count (Deducted on VL/AL)</th></tr></thead>
				  <tbody><tr><td id="awol_cnt" style="width:50%; text-align: center;">0</td><td id="el_cnt" style="width:50%; text-align: center;">0</td></tr></tbody>
				</table>
			  </div>
			</div>
		</div>
	</div>
  <br/>
  <input type="hidden" id="emp_id" value="<?=$emp_id?>"/>
	<div class="widget-box widget-color-green2">
		<div class="widget-header">
			<h4 class="widget-title">Leave Balances</h4>
		</div>
		
		<div class="widget-body">
			<div class="widget-main no-padding">
			  <div style="overflow: auto; height: 260px; position: relative; background-color: rgb(216, 216, 216);">
			    <div id="tk-table-loader" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
			    <div id="tk-table-no-record" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
			    <table id="tk-balances-table" class="table table-striped table-bordered table-hover" width="100%"></table>
			  </div>
			  <div id="tk-balances-pager"></div>
			</div>
		</div>
	</div>
</div>

<div class="col-xs-12 col-sm-6">
	<div class="widget-box widget-color-dark">
		<div class="widget-header">
			<h4 class="widget-title">Leave Filings</h4>
			<div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-grey" id="add-leave-btn">
				<i class="ace-icon fa fa-plus"></i>
				Add
			  </button>
		    </div>
			<div class="widget-toolbar no-border">
				<select id="tk-year" >
					<?php
					for(;$start_year<=$end_year+1;$start_year++)
						echo '<option value="' . $start_year . '" ' . ($start_year==$end_year?"selected='selected'":"") . ' >' . $start_year . '</option>';
					?>
				</select>
			</div>
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
			    <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
				  <div id="tk-leave-loader" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
				  <div id="tk-leave-no-record" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
				  <table id="tk-leave-table" class="table table-striped table-bordered table-hover" width="100%"></table>
				</div>
				<div id="tk-leave-pager"></div>
				
			</div>
		</div>
	</div>
</div>

<div id="request-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">File Leave</h4>
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
							<h3 class="header smaller no-margin-top"><small>Leave Information</small></h3>
							<div class="row">
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="lv-type">Leave Type : </label>
									<div class="col-sm-8">
										<select class="form-control input-sm" type="text" id="lv-type" name="lv-type" >
										</select>
										<input type="hidden" id="lv-sub-type" name="lv-sub-type" />
									</div>
								</div>
							</div>
							<div class="row">
							  <div class="form-group col-sm-1">&nbsp;</div>
							  <div class="form-group col-sm-10">
							    <p id="lv-desc" class="well well-sm"></p>
							  </div>
							  <div class="form-group col-sm-1">&nbsp;</div>
							</div>
							<div class="row input-daterange">
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="lv-date-from">Date From : </label>
									<div class="col-sm-4">
										<input class="form-control input-sm input-date" type="text" id="lv-date-from" name="lv-date-from" />
									</div>
								</div>
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="lv-date-to">Date To : </label>
									<div class="col-sm-4">
										<input class="form-control input-sm input-date" type="text" id="lv-date-to" name="lv-date-to" />
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
							<div class="row" id="mc-row">
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="lv-mc">Med Cert Number : </label>
									<div class="col-sm-8">
										<input type="text" id="lv-mc" name="lv-mc" class="form-control input-sm"/>
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
		<h4 class="blue bigger">Leave Application</h4>
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
		  <div class="col-xs-12">
		    <div class="well">
			  <h4 class="red smaller lighter">Are you sure you want to <label id='request-action'>delete</label> Request <strong id="request_lbl">&nbsp;</strong>?</h4>
			  <div id="remarks-div">
				<p id="remarks-lbl">Remarks: (Required)</p>
				<textarea id="leave-remarks" name="leave-remarks" style="height: 112px; resize: none;" class="form-control limited" maxlength="250" ></textarea>
			  </div>
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

<div id="messsage-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
	    <button type="button" data-dismiss="modal" class="close">&times;</button>
		<h4 class="blue bigger">Alert</h4>
	  </div>
	  <div class="modal-body">
	    <div class="row">
		  <div class="col-xs-12">
		    <div class="well">
			  <h4 class="red smaller lighter" id="message"></h4>
			  <button class="btn btn-sm btn-danger" data-dismiss="modal">Ok</button>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>
