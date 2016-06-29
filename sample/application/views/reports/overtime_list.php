<style>
.widget-box[class*=widget-color-]>.widget-header div.chosen-drop {
  color: #000000 !important;
}
</style>
<div class="col-12">
	<div class="widget-box widget-color-green">
		<div class="widget-header">
			<h4 class="widget-title">Overtime Filings</h4>
			<div class="widget-toolbar no-border">
			  <button class="btn btn-xs btn-success" id="tk-search-btn">
				<i class="ace-icon fa fa-search"></i>
				Search
			  </button>
		    </div>
			<div class="widget-toolbar no-border">
				<div class="input-daterange input-group" style="margin: 4px 0 0 0;">
					<input type="text" class="input-sm form-control" id="date-from" name="start" style="background-color: #F7F7F7; width: 90px;" value="<?=$cur_period->start?>">
					<span class="input-group-addon btn-success">
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
				<button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" id="tk-filter-status-btn" data-id="0">
					<i class="ace-icon fa fa-filter"></i> Status: All
					<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu" id="tk-filter-status">
				    <li><a href="#" data-id="-1">Pending</a></li>
					<li><a href="#" data-id="1">Submitted</a></li>
					<li><a href="#" data-id="2">Rejected</a></li>
					<li><a href="#" data-id="3">Approved</a></li>
					<li><a href="#" data-id="4">Cancelled</a></li>
					<li class="divider"></li>
					<li class="active"><a href="#">All</a></li>
				</ul>
			</div>
			<div class="widget-toolbar no-border">
				<button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" id="tk-filter-dept-btn" data-id="0">
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
		</div>
		
		<div class="widget-body">
			<div class="widget-main no-padding">
			    <div style="overflow: auto; height: 400px; position: relative; background-color: rgb(216, 216, 216);">
				  <div id="tk-leave-loader" class="hidden" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-refresh fa-spin"></i> Loading...</div>
				  <div id="tk-leave-no-record" style="width: 20%; position: absolute; top: 42%; left: 40%; font-size: 30px; text-align: center;"><i class="ace-icon fa fa-warning"></i> No Record Found</div>
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
				<h4 class="blue bigger">Filed Overtime Information</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<form class="form-horizontal" role="form" id="requestForm" novalidate="novalidate">
							<input class="form-control" type="hidden" id="request-id" name="request-id" />
							<h3 class="header smaller no-margin-top"><small>Overtime Information</small></h3>
							<div class="row input-daterange">
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="ot-date-from">Date : </label>
									<div class="col-sm-4">
										<input class="form-control input-sm input-date" readonly type="text" id="ot-date-from" name="ot-date-from" />
									</div>
								</div>
							</div>
							<div class="row">
							    <div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="ot-time-from">Time From : </label>
									<div class="col-sm-4">
									  <div class="input-group bootstrap-timepicker">
										<input class="form-control timepicker" readonly type="text" id="ot-time-from" name="ot-time-from" />
									  </div>
									</div>
								</div>
								<div class="form-group col-sm-6">
									<label class="col-sm-4 control-label no-padding-right" for="ot-time-to">Time To : </label>
									<div class="col-sm-4">
									  <div class="input-group bootstrap-timepicker">
										<input class="form-control timepicker" readonly type="text" id="ot-time-to" name="ot-time-to" />
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