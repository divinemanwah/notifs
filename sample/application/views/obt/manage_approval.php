<div class="col-md-12">
  <div class="widget-box widget-color-red3 approval-list-widget">
	<div class="widget-header">
	  <h4 class="widget-title">OBT for Approval</h4>
	  <div class="widget-toolbar no-border">
		<button class="btn btn-xs btn-danger" id="tk-search-btn">
		  <i class="ace-icon fa fa-search"></i> Search
		</button>
	  </div>
	  <div class="widget-toolbar no-border">
		<button class="btn btn-xs btn-danger dropdown-toggle" data-toggle="dropdown" id="tk-filter-status-btn" data-id="1">
			<i class="ace-icon fa fa-filter"></i> Status: Submitted
			<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
		</button>
		<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-status">
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
				<th>Date</th>
				<th>Time From</th>
				<th>Time To</th>
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

<div id="approval-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	  <div class="modal-header">
	    <button type="button" data-dismiss="modal" class="close">&times;</button>
		<h4 class="blue bigger">OBT for Approval</h4>
	  </div>
	  <div class="modal-body">
	    <div class="row">
		  <div class="col-xs-12">
		    <div class="well">
			  <h4 class="red smaller lighter">Are you sure you want to <label id='modal-action'>&nbsp;</label> <strong id="modal-target-lbl">&nbsp;</strong>?</h4>
			  <p id="remarks_lbl">Remarks: (Optional)</p>
			  <textarea id="obt-remarks" name="obt-remarks" style="height: 112px; resize: none;" class="form-control limited" maxlength="250" ></textarea>
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

<div id="request-modal" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="blue bigger">Official Business Trip</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="header smaller no-margin-top"><small>OBT Information - <b id="obt-requester" class="text text-info"></b></small></h3>
						<div class="row input-daterange">
							<div class="form-group col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="obt-date">Date : </label>
								<div class="col-sm-4">
									<input class="form-control input-sm input-date" disabled type="text" id="obt-date" name="obt-date" />
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="obt-time-from">Time From : </label>
								<div class="col-sm-4">
								  <div class="input-group bootstrap-timepicker">
									<input class="form-control timepicker" readonly type="text" id="obt-time-from" name="obt-time-from" />
								  </div>
								</div>
							</div>
							<div class="form-group col-sm-6">
								<label class="col-sm-4 control-label no-padding-right" for="obt-time-to">Time To : </label>
								<div class="col-sm-4">
								  <div class="input-group bootstrap-timepicker">
									<input class="form-control timepicker" readonly type="text" id="obt-time-to" name="obt-time-to" />
								  </div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-sm-12">
								<label class="col-sm-2 control-label no-padding-right" for="reason">Reason :</label>
								<div class="col-sm-8 control-label no-padding-right">
								  <textarea id="reason" style="height: 112px; resize: none;" disabled class="form-control limited" name="reason" maxlength="250" ></textarea>
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
				    <div class="col-sm-12" style="max-height: 150px; overflow: auto;" id="approval_list">
						None
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn btn-sm btn-success modal-action-btn" id="obt-approve">
					<i class="ace-icon fa fa-check"></i>
					Approve
				</button>
				<button class="btn btn-sm btn-danger modal-action-btn" id="obt-reject">
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
