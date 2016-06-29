<? if($this->session->userdata("tk_approver")) { ?>
<div class="col-md-12">
  <div class="widget-box widget-color-red3 approval-list-widget">
	<div class="widget-header">
	  <h4 class="widget-title">Schedules for Approval</h4>
	  <div class="widget-toolbar no-border">
		<button class="btn btn-xs btn-danger" id="tk-sched-search-btn">
		  <i class="ace-icon fa fa-search"></i> Search
		</button>
	  </div>
	  <div class="widget-toolbar no-border">
		<button class="btn btn-xs btn-danger dropdown-toggle" data-toggle="dropdown" id="tk-sched-filter-status-btn" data-id="1">
			<i class="ace-icon fa fa-filter"></i> Status: Submitted
			<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
		</button>
		<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-sched-filter-status">
			<li class="active"><a href="#" data-id="1">Submitted</a></li>
			<li><a href="#" data-id="3">Approved</a></li>
			<li><a href="#" data-id="4">Cancelled</a></li>
			<li class="divider"></li>
			<li><a href="#">All</a></li>
		</ul>
	  </div>
	</div>
	<div class="widget-body">
	  <div class="widget-main no-padding">
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
				<span id="approval-list-success-msg"></span>
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
			  <span id="approval-list-err-msg"></span>
			  <br />
			</div>
		  </div>
		</div>
        <div class="">
		  <table id="approval-table" class="table table-striped table-bordered table-hover" width="100%">
			<thead>
			  <tr>
				<th class="center">
				  <label class="position-relative">
					<input type="checkbox" class="ace" />
					<span class="lbl"></span>
				  </label>
				</th>
				<th>Group</th>
				<th>Period</th>
				<th>Uploaded Date</th>
				<th>Uploaded By</th>
				<th>Approved By</th>
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

<div id="approval-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
		<h4 class="blue bigger">Schedules for Approval</h4>
	  </div>
	  <div class="modal-body">
	    <div class="row">
		  <div class="col-xs-12">
		    <div class="well">
			  <h4 class="red smaller lighter">Are you sure you want to <label id='modal-action'>&nbsp;</label> <strong id="modal-target-lbl">&nbsp;</strong>?</h4>
			  <p id="approval-remarks_lbl">Remarks: (Optional)</p>
			  <textarea id="approval-remarks" name="approval-remarks" style="height: 112px; resize: none;" class="form-control limited" maxlength="250" ></textarea>
			  <br/>
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

<div id="history-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
		<h4 class="blue bigger">Schedule Approval History</h4>
	  </div>
	  <div class="modal-body">
	    <div id="approval_header">
		  <h3 class="header smaller no-margin-top"><small>Approval Remarks</small></h3>
		</div>
		<div style="max-height: 150px; overflow: auto;" id="approval_list">None</div>
	  </div>
	  <div class="modal-footer">
		  <button class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>
<? } ?>

<? if($this->session->userdata("cws_approver")) { ?>
<div class="col-md-12">
  <div class="widget-box widget-color-green2 change-sched-list-widget">
	<div class="widget-header">
	  <h4 class="widget-title">Change of Schedules for Approval</h4>
	  <div class="widget-toolbar no-border">
		<button class="btn btn-xs btn-success" id="tk-cws-search-btn">
		  <i class="ace-icon fa fa-search"></i> Search
		</button>
	  </div>
	  <div class="widget-toolbar no-border">
		<button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" id="tk-cws-filter-status-btn" data-id="1">
			<i class="ace-icon fa fa-filter"></i> Status: Submitted
			<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
		</button>
		<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-cws-filter-status">
			<li class="active"><a href="#" data-id="1">Submitted</a></li>
			<li><a href="#" data-id="3">Approved</a></li>
			<li><a href="#" data-id="4">Cancelled</a></li>
			<li class="divider"></li>
			<li><a href="#">All</a></li>
		</ul>
	  </div>
	</div>
	<div class="widget-body">
	  <div class="widget-main no-padding">
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
				<span id="change-sched-list-success-msg"></span>
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
			  <span id="change-sched-list-err-msg"></span>
			  <br />
			</div>
		  </div>
		</div>
        <div class="">
		  <table id="change-sched-table" class="table table-striped table-bordered table-hover" width="100%">
			<thead>
			  <tr>
				<th class="center">
				  <label class="position-relative">
					<input type="checkbox" class="ace" />
					<span class="lbl"></span>
				  </label>
				</th>
				<th>Employee ID</th>
				<th>Date From</th>
				<th>Date To</th>
				<th>Submitted By</th>
				<th>Approved By</th>
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

<div id="change-approval-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
		<h4 class="blue bigger">Change of Schedules for Approval</h4>
	  </div>
	  <div class="modal-body">
	    <div class="row">
		  <div class="col-xs-12">
		    <div class="well">
			  <h4 class="red smaller lighter">Are you sure you want to <label id='change-modal-action'>&nbsp;</label> <strong id="change-modal-target-lbl">&nbsp;</strong>?</h4>
			  <p id="remarks_lbl">Remarks: (Optional)</p>
			  <textarea id="change-remarks" name="change-remarks" style="height: 112px; resize: none;" class="form-control limited" maxlength="250" ></textarea>
			  <br/>
			  <button class="btn btn-sm btn-success change-modal-action-btn">Yes</button>
			  <button class="btn btn-sm btn-danger" data-dismiss="modal">No</button>
			</div>
			<strong>Note:</strong> This cannot be undone
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>

<div id="request-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
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
						<h3 class="header smaller no-margin-top"><small>Change of Shift Information - <b id="cws-requester" class="text text-info"></b></small></h3>
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
								  <select id="new-shift" class="form-control input-sm input-medium" name="new-shift">
									  <? foreach($shifts as $shift_id=>$shift) { ?>
									  <option value="<?=$shift_id?>"><?="[".$shift->stime."] - ".$shift->scode?></option>
									  <? } ?>
								  </select>
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
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<h3 class="header smaller no-margin-top"><small>Approval Remarks</small></h3>
					</div>
				</div>
				<div class="row" >
				    <div class="col-sm-12" style="max-height: 150px; overflow: auto;" id="cws_approval_list">
						None
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm btn-success modal-action-btn" id="cws-approve">
					<i class="ace-icon fa fa-check"></i>
					Approve
				</button>
				<button class="btn btn-sm btn-danger modal-action-btn" id="cws-reject">
					<i class="ace-icon fa fa-times"></i>
					Reject
				</button>
				<button class="btn btn-sm btn-cancel modal-action-btn" data-dismiss="modal">
					<i class="ace-icon fa fa-share-square-o"></i>
					Close
				</button>
			</div>
		</div>
	</div>
</div>
<? } ?>

